<?php

require_once ROOT_PATH . '/app/models/Submission.php';
require_once ROOT_PATH . '/app/models/Challenge.php';
require_once ROOT_PATH . '/app/models/Comment.php';
require_once ROOT_PATH . '/app/models/Vote.php';

// CONTRÔLEUR SUBMISSION
// Il gère les projets envoyés, les votes et les commentaires
class SubmissionController extends Controller {

    private $submissionModel;
    private $challengeModel;
    private $commentModel;
    private $voteModel;

    public function __construct() {
        $this->submissionModel = new Submission();
        $this->challengeModel  = new Challenge();
        $this->commentModel    = new Comment();
        $this->voteModel       = new Vote();
    }

    // Affiche un projet précis
    public function show() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $submission = $this->submissionModel->findById('submissions', $id);

        if (!$submission) {
            $this->setFlash('error', 'Participation introuvable.');
            $this->redirect('index.php?page=challenges');
        }

        $comments = $this->commentModel->getBySubmission($id);
        
        $hasVoted = false;
        if ($this->isLoggedIn()) {
            $hasVoted = $this->voteModel->hasVoted($this->currentUserId(), $id);
        }

        $this->render('submission/show', [
            'pageTitle'  => 'Participation de ' . $submission['username'],
            'submission' => $submission,
            'comments'   => $comments,
            'hasVoted'   => $hasVoted,
            'csrf'       => $this->generateCsrfToken()
        ]);
    }

    // Envoyer un projet (POST)
    public function create() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $challengeId = (int)$_POST['challenge_id'];
        
        // On évite qu'un gars participe deux fois au même truc
        if ($this->submissionModel->existsByUserAndChallenge($this->currentUserId(), $challengeId)) {
            $this->setFlash('error', 'Vous avez déjà participé à ce défi.');
            $this->redirect('index.php?page=challenge-show&id=' . $challengeId);
        }

        $image = $this->handleUpload('image');
        
        $data = [
            'challenge_id' => $challengeId,
            'user_id'      => $this->currentUserId(),
            'description'  => $this->sanitize($_POST['description'] ?? ''),
            'link'         => $this->sanitize($_POST['link'] ?? ''),
            'image'        => $image
        ];

        $newId = $this->submissionModel->create($data);

        if ($newId) {
            $this->setFlash('success', 'Votre participation a été publiée !');
            $this->redirect('index.php?page=submission-show&id=' . $newId);
        } else {
            $this->setFlash('error', 'Une erreur est survenue.');
            $this->redirect('index.php?page=challenge-show&id=' . $challengeId);
        }
    }

    // Gère les VOTES (AJAX pour pas que la page recharge)
    public function vote() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $submissionId = (int)$_POST['submission_id'];
        
        // La méthode cast() gère l'ajout ou la suppression du vote (Basculement)
        $result = $this->voteModel->cast($submissionId, $this->currentUserId());
        $count  = $this->voteModel->countBySubmission($submissionId);

        // On répond au format JSON pour que le JavaScript puisse mettre à jour la page
        header('Content-Type: application/json');
        echo json_encode([
            'action' => $result['action'], // 'added' ou 'removed'
            'count'  => $count
        ]);
        exit();
    }

    /**
     * Gère l'ajout de COMMENTAIRES via AJAX
     */
    public function addComment() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $submissionId = (int)$_POST['submission_id'];
        $content = $this->sanitize($_POST['content'] ?? '');

        if (strlen($content) < 2) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Le commentaire est trop court.']);
            exit();
        }

        $id = $this->commentModel->create([
            'submission_id' => $submissionId,
            'user_id'       => $this->currentUserId(),
            'content'       => $content
        ]);

        // On renvoie les infos au format JSON pour l'affichage immédiat en JS
        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'username' => $_SESSION['username'],
            'avatar'   => $_SESSION['user']['avatar'] ?? null,
            'content'  => $content,
            'date'     => date('d/m/Y H:i')
        ]);
        exit();
    }

    /**
     * Liste des meilleures participations (Leaderboard)
     */
    public function leaderboard() {
        $topSubmissions = $this->submissionModel->getTopSubmissions(20);

        $this->render('submission/leaderboard', [
            'pageTitle'      => 'Classement des meilleurs projets',
            'topSubmissions' => $topSubmissions
        ]);
    }

    /**
     * Suppression d'une participation
     */
    public function delete() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id = (int)$_POST['id'];
        $submission = $this->submissionModel->findById('submissions', $id);

        if ($submission && (int)$submission['user_id'] === $this->currentUserId()) {
            $this->submissionModel->delete($id);
            $this->setFlash('success', 'Participation supprimée.');
        }

        $this->redirect('index.php?page=challenges');
    }
}
