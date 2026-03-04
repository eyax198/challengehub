<?php

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/models/Submission.php';
require_once ROOT_PATH . '/app/models/Challenge.php';
require_once ROOT_PATH . '/app/models/Comment.php';
require_once ROOT_PATH . '/app/models/Vote.php';

class SubmissionController extends Controller {

    private Submission $submissionModel;
    private Challenge  $challengeModel;
    private Comment    $commentModel;
    private Vote       $voteModel;

    public function __construct() {
        $this->submissionModel = new Submission();
        $this->challengeModel  = new Challenge();
        $this->commentModel    = new Comment();
        $this->voteModel       = new Vote();
    }

    // ── GET /submission/show?id=X ─────────────────────────────
    public function show(): void {
        $id         = (int) ($_GET['id'] ?? 0);
        $submission = $this->submissionModel->findById('submissions', $id);

        if (!$submission) {
            $this->setFlash('error', 'Participation introuvable.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $comments = $this->commentModel->getBySubmission($id);
        $hasVoted = $this->isLoggedIn()
            ? $this->voteModel->hasVoted($this->currentUserId(), $id)
            : false;

        $this->render('submission.show', [
            'pageTitle'  => 'Participation de ' . htmlspecialchars($submission['username']),
            'submission' => $submission,
            'comments'   => $comments,
            'hasVoted'   => $hasVoted,
            'csrf'       => $this->generateCsrfToken(),
        ]);
    }

    // ── POST /submission/create ───────────────────────────────
    public function create(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $challengeId = (int) ($_POST['challenge_id'] ?? 0);
        $challenge   = $this->challengeModel->findById('challenges', $challengeId);

        if (!$challenge) {
            $this->setFlash('error', 'Défi introuvable.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        if ($this->submissionModel->existsByUserAndChallenge($this->currentUserId(), $challengeId)) {
            $this->setFlash('error', 'Vous avez déjà soumis une participation à ce défi.');
            $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $challengeId);
            return;
        }

        $description = $this->post('description');
        $link        = $this->post('link');

        if (strlen($description) < 10) {
            $this->setFlash('error', 'La description est trop courte.');
            $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $challengeId);
            return;
        }

        $image = $this->handleUpload('image', 'submissions');

        $id = $this->submissionModel->create([
            'challenge_id' => $challengeId,
            'user_id'      => $this->currentUserId(),
            'description'  => $description,
            'image'        => $image,
            'link'         => !empty($link) ? $link : null,
        ]);

        if ($id) {
            $this->setFlash('success', 'Participation soumise avec succès !');
            $this->redirect(BASE_URL . '/index.php?page=submission-show&id=' . $id);
        } else {
            $this->setFlash('error', 'Erreur lors de la soumission.');
            $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $challengeId);
        }
    }

    // ── GET /submission/edit?id=X ─────────────────────────────
    public function showEdit(): void {
        $this->requireLogin();
        $id         = (int) ($_GET['id'] ?? 0);
        $submission = $this->submissionModel->findById('submissions', $id);

        if (!$submission || (int)$submission['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $this->render('submission.edit', [
            'pageTitle'  => 'Modifier la participation',
            'submission' => $submission,
            'csrf'       => $this->generateCsrfToken(),
        ]);
    }

    // ── POST /submission/update ───────────────────────────────
    public function update(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id         = (int) ($_POST['id'] ?? 0);
        $submission = $this->submissionModel->findById('submissions', $id);

        if (!$submission || (int)$submission['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $data = [
            'description' => $this->post('description'),
            'link'        => $this->post('link'),
        ];

        $image = $this->handleUpload('image', 'submissions');
        if ($image) $data['image'] = $image;

        $this->submissionModel->update($id, $data);

        $this->setFlash('success', 'Participation modifiée.');
        $this->redirect(BASE_URL . '/index.php?page=submission-show&id=' . $id);
    }

    // ── POST /submission/delete ───────────────────────────────
    public function delete(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id         = (int) ($_POST['id'] ?? 0);
        $submission = $this->submissionModel->findById('submissions', $id);

        if (!$submission || (int)$submission['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $challengeId = $submission['challenge_id'];
        $this->submissionModel->delete($id);
        $this->setFlash('success', 'Participation supprimée.');
        $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $challengeId);
    }

    // ── POST /comment/create (AJAX) ────────────────────────────
    public function addComment(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $content      = $this->post('content');

        if (strlen($content) < 2) {
            $this->json(['error' => 'Commentaire trop court.'], 400);
            return;
        }

        $id = $this->commentModel->create([
            'submission_id' => $submissionId,
            'user_id'       => $this->currentUserId(),
            'content'       => $content,
        ]);

        $user = $this->currentUser();

        $this->json([
            'success'  => true,
            'id'       => $id,
            'content'  => htmlspecialchars($content),
            'username' => htmlspecialchars($user['username']),
            'avatar'   => $user['avatar'] ?? null,
            'date'     => date('d/m/Y H:i'),
        ]);
    }

    // ── POST /vote (AJAX) ─────────────────────────────────────
    public function vote(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $result       = $this->voteModel->cast($submissionId, $this->currentUserId());
        $count        = $this->voteModel->countBySubmission($submissionId);

        $this->json([
            'action' => $result['action'],
            'count'  => $count,
        ]);
    }

    // ── GET /leaderboard ─────────────────────────────────────
    public function leaderboard(): void {
        $topSubmissions = $this->submissionModel->getTopSubmissions(20);

        $this->render('submission.leaderboard', [
            'pageTitle'      => 'Classement des meilleures participations',
            'topSubmissions' => $topSubmissions,
        ]);
    }
}
