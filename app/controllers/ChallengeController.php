<?php

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/models/Challenge.php';

class ChallengeController extends Controller {

    private Challenge $challengeModel;

    public function __construct() {
        $this->challengeModel = new Challenge();
    }

    // ── GET /challenges ───────────────────────────────────────
    public function index(): void {
        $keyword  = $this->get('keyword');
        $category = $this->get('category');
        $sort     = $this->get('sort', 'newest');
        $page     = max(1, (int) $this->get('page', '1'));

        $filters     = compact('keyword', 'category', 'sort');
        $challenges  = $this->challengeModel->getAll($filters, $page);
        $total       = $this->challengeModel->countAll($filters);
        $totalPages  = (int) ceil($total / PER_PAGE);
        $categories  = $this->challengeModel->getCategories();

        $this->render('challenge.index', [
            'pageTitle'  => 'Tous les défis',
            'challenges' => $challenges,
            'categories' => $categories,
            'filters'    => $filters,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
        ]);
    }

    // ── GET /challenges/show?id=X ─────────────────────────────
    public function show(): void {
        $id        = (int) ($_GET['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge) {
            $this->setFlash('error', 'Défi introuvable.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        require_once ROOT_PATH . '/app/models/Submission.php';
        require_once ROOT_PATH . '/app/models/Vote.php';

        $submissionModel = new Submission();
        $voteModel       = new Vote();

        $sort        = $this->get('sort', 'newest');
        $submissions = $submissionModel->getByChallenge($id, $sort);
        $userVotes   = [];

        if ($this->isLoggedIn()) {
            foreach ($submissions as $sub) {
                $userVotes[$sub['id']] = $voteModel->hasVoted($this->currentUserId(), $sub['id']);
            }
        }

        $alreadySubmitted = $this->isLoggedIn()
            ? $submissionModel->existsByUserAndChallenge($this->currentUserId(), $id)
            : false;

        $this->render('challenge.show', [
            'pageTitle'        => htmlspecialchars($challenge['title']),
            'challenge'        => $challenge,
            'submissions'      => $submissions,
            'userVotes'        => $userVotes,
            'alreadySubmitted' => $alreadySubmitted,
            'sort'             => $sort,
            'csrf'             => $this->generateCsrfToken(),
        ]);
    }

    // ── GET /challenges/create ────────────────────────────────
    public function showCreate(): void {
        $this->requireLogin();
        $csrf = $this->generateCsrfToken();
        $this->render('challenge.create', ['pageTitle' => 'Créer un défi', 'csrf' => $csrf]);
    }

    // ── POST /challenges/create ───────────────────────────────
    public function create(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $title       = $this->post('title');
        $description = $this->post('description');
        $category    = $this->post('category');
        $deadline    = $this->post('deadline');

        $errors = [];
        if (strlen($title) < 5)       $errors[] = 'Le titre doit comporter au moins 5 caractères.';
        if (strlen($description) < 20) $errors[] = 'La description doit comporter au moins 20 caractères.';
        if (empty($category))          $errors[] = 'Veuillez choisir une catégorie.';
        if (empty($deadline))          $errors[] = 'Veuillez définir une date limite.';

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $this->redirect(BASE_URL . '/index.php?page=challenge-create');
            return;
        }

        $image = $this->handleUpload('image', 'challenges');

        $id = $this->challengeModel->create([
            'user_id'     => $this->currentUserId(),
            'title'       => $title,
            'description' => $description,
            'category'    => $category,
            'deadline'    => $deadline,
            'image'       => $image,
        ]);

        if ($id) {
            $this->setFlash('success', 'Défi créé avec succès !');
            $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $id);
        } else {
            $this->setFlash('error', 'Erreur lors de la création du défi.');
            $this->redirect(BASE_URL . '/index.php?page=challenge-create');
        }
    }

    // ── GET /challenges/edit?id=X ─────────────────────────────
    public function showEdit(): void {
        $this->requireLogin();
        $id        = (int) ($_GET['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge || (int)$challenge['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $csrf = $this->generateCsrfToken();
        $this->render('challenge.edit', [
            'pageTitle' => 'Modifier le défi',
            'challenge' => $challenge,
            'csrf'      => $csrf,
        ]);
    }

    // ── POST /challenges/edit ─────────────────────────────────
    public function update(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id        = (int) ($_POST['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge || (int)$challenge['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $data = [
            'title'       => $this->post('title'),
            'description' => $this->post('description'),
            'category'    => $this->post('category'),
            'deadline'    => $this->post('deadline'),
        ];

        $image = $this->handleUpload('image', 'challenges');
        if ($image) $data['image'] = $image;

        $this->challengeModel->update($id, $data);

        $this->setFlash('success', 'Défi modifié avec succès !');
        $this->redirect(BASE_URL . '/index.php?page=challenge-show&id=' . $id);
    }

    // ── POST /challenges/delete ───────────────────────────────
    public function delete(): void {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id        = (int) ($_POST['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge || (int)$challenge['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect(BASE_URL . '/index.php?page=challenges');
            return;
        }

        $this->challengeModel->delete($id);
        $this->setFlash('success', 'Défi supprimé.');
        $this->redirect(BASE_URL . '/index.php?page=challenges');
    }
}
