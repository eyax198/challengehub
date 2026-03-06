<?php

require_once ROOT_PATH . '/app/models/Challenge.php';

// CONTRÔLEUR CHALLENGE
// On gère les défis : les voir, les créer, les modifier...
class ChallengeController extends Controller {

    private $challengeModel;

    public function __construct() {
        // Initialisation du modèle pour les défis
        $this->challengeModel = new Challenge();
    }

    // Page avec la liste de tous les défis
    public function index() {
        // Récupération des filtres depuis l'URL (?keyword=...&category=...)
        $filters = [
            'keyword'  => $this->sanitize($_GET['keyword'] ?? ''),
            'category' => $this->sanitize($_GET['category'] ?? ''),
            'sort'     => $this->sanitize($_GET['sort'] ?? 'newest')
        ];

        // Gestion de la pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Appel au modèle pour récupérer les données
        $challenges = $this->challengeModel->getAll($filters, $page);
        $total      = $this->challengeModel->countAll($filters);
        
        // On calcule combien il y a de pages
        $totalPages = ceil($total / PER_PAGE);
        
        // Récupération de la liste des catégories pour le menu déroulant
        $categories = $this->challengeModel->getCategories();

        $this->render('challenge/index', [
            'pageTitle'  => 'Tous les défis',
            'challenges' => $challenges,
            'categories' => $categories,
            'filters'    => $filters,
            'page'       => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Voir un défi en détail
    public function show() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // On récupère le défi
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge) {
            $this->setFlash('error', 'Défi introuvable.');
            $this->redirect('index.php?page=challenges');
        }

        // On a besoin d'autres modèles ici pour les votes et commentaires
        require_once ROOT_PATH . '/app/models/Submission.php';
        require_once ROOT_PATH . '/app/models/Vote.php';

        $submissionModel = new Submission();
        $voteModel       = new Vote();

        $sort = $_GET['sort'] ?? 'newest';
        $submissions = $submissionModel->getByChallenge($id, $sort);
        
        // On vérifie si l'utilisateur a déjà voté pour chaque participation
        $userVotes = [];
        if ($this->isLoggedIn()) {
            foreach ($submissions as $sub) {
                $userVotes[$sub['id']] = $voteModel->hasVoted($this->currentUserId(), $sub['id']);
            }
        }

        // On vérifie si l'utilisateur a déjà participé à ce défi
        $alreadySubmitted = false;
        if ($this->isLoggedIn()) {
            $alreadySubmitted = $submissionModel->existsByUserAndChallenge($this->currentUserId(), $id);
        }

        $this->render('challenge/show', [
            'pageTitle'        => $challenge['title'],
            'challenge'        => $challenge,
            'submissions'      => $submissions,
            'userVotes'        => $userVotes,
            'alreadySubmitted' => $alreadySubmitted,
            'sort'             => $sort,
            'csrf'             => $this->generateCsrfToken() // Pour le vote
        ]);
    }

    /**
     * Affiche le formulaire de création
     */
    public function showCreate() {
        $this->requireLogin();
        $this->render('challenge/create', [
            'pageTitle' => 'Créer un défi',
            'csrf'      => $this->generateCsrfToken()
        ]);
    }

    /**
     * Traite la création d'un défi (POST)
     */
    public function create() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        // Récupération des données
        $data = [
            'user_id'     => $this->currentUserId(),
            'title'       => $this->sanitize($_POST['title'] ?? ''),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'category'    => $this->sanitize($_POST['category'] ?? ''),
            'deadline'    => $this->sanitize($_POST['deadline'] ?? '')
        ];

        // Validation simple
        if (empty($data['title']) || empty($data['description'])) {
            $this->setFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            $this->redirect('index.php?page=challenge-create');
        }

        // Gestion de l'image
        $data['image'] = $this->handleUpload('image');

        // Enregistrement en BDD
        $newId = $this->challengeModel->create($data);

        if ($newId) {
            $this->setFlash('success', 'Votre défi a été publié !');
            $this->redirect('index.php?page=challenge-show&id=' . $newId);
        } else {
            $this->setFlash('error', 'Erreur lors de la création.');
            $this->redirect('index.php?page=challenge-create');
        }
    }

    /**
     * Affiche le formulaire de modification
     */
    public function showEdit() {
        $this->requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        // Vérification du propriétaire
        if (!$challenge || (int)$challenge['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Action non autorisée.');
            $this->redirect('index.php?page=challenges');
        }

        $this->render('challenge/edit', [
            'pageTitle' => 'Modifier le défi',
            'challenge' => $challenge,
            'csrf'      => $this->generateCsrfToken()
        ]);
    }

    /**
     * Traite la modification (POST)
     */
    public function update() {
        $this->requireLogin();
        $this->verifyCsrfToken();

        $id = (int)($_POST['id'] ?? 0);
        $challenge = $this->challengeModel->findById('challenges', $id);

        if (!$challenge || (int)$challenge['user_id'] !== $this->currentUserId()) {
            $this->setFlash('error', 'Action non autorisée.');
            $this->redirect('index.php?page=challenges');
        }

        $data = [
            'title'       => $this->sanitize($_POST['title'] ?? ''),
            'description' => $this->sanitize($_POST['description'] ?? ''),
            'category'    => $this->sanitize($_POST['category'] ?? ''),
            'deadline'    => $this->sanitize($_POST['deadline'] ?? '')
        ];

        // On ne change l'image que si une nouvelle est envoyée
        $newImage = $this->handleUpload('image');
        if ($newImage) {
            $data['image'] = $newImage;
        }

        if ($this->challengeModel->update($id, $data)) {
            $this->setFlash('success', 'Défi mis à jour.');
            $this->redirect('index.php?page=challenge-show&id=' . $id);
        } else {
            $this->setFlash('error', 'Aucun changement effectué.');
            $this->redirect('index.php?page=challenge-show&id=' . $id);
        }
    }

    // Pour supprimer un défi
    public function delete() {
        $this->requireLogin();
        
        // Pour la suppression, on utilise généralement POST pour plus de sécurité
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfToken();
            $id = (int)($_POST['id'] ?? 0);
            
            $challenge = $this->challengeModel->findById('challenges', $id);
            if ($challenge && (int)$challenge['user_id'] === $this->currentUserId()) {
                $this->challengeModel->delete($id);
                $this->setFlash('success', 'Défi supprimé avec succès.');
            } else {
                $this->setFlash('error', 'Action impossible.');
            }
        }
        
        $this->redirect('index.php?page=challenges');
    }
}
