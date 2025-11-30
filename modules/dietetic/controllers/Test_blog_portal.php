<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * TEST BLOG PORTAL - Simuler exactement l'accès /dietetic/portal/blog
 * Accès: /dietetic/portal/test_blog_simple
 */
class Test_blog_portal extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        // Forcer affichage erreurs
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }

    public function index()
    {
        echo '<html><head><title>Test Blog Portal</title>';
        echo '<style>
            body { font-family: Arial; padding: 20px; background: #f5f5f5; }
            .test { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ccc; }
            .success { border-left-color: #28a745; }
            .error { border-left-color: #dc3545; background: #fff5f5; }
            .warning { border-left-color: #ffc107; }
            h1 { color: #01807B; }
            pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow: auto; }
            code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        </style></head><body>';

        echo '<h1>🔬 TEST BLOG PORTAL - Simulation Exacte</h1>';
        echo '<p>Test de la route <code>/dietetic/portal/blog</code></p>';
        echo '<hr>';

        // TEST 1: Vérifier session client
        echo '<h2>1️⃣ Vérification Session Client</h2>';
        echo '<div class="test">';

        if (is_client_logged_in()) {
            echo '✅ <strong>Client connecté!</strong><br>';
            echo 'Contact ID: ' . get_contact_user_id() . '<br>';

            // Récupérer infos contact
            $this->load->model('clients_model');
            $contact = $this->clients_model->get_contact(get_contact_user_id());

            if ($contact) {
                echo 'Email: ' . $contact->email . '<br>';
                echo 'Client ID: ' . $contact->userid . '<br>';
            }
        } else {
            echo '❌ <strong>Aucun client connecté!</strong><br>';
            echo '<em>Connectez-vous d\'abord via /clients/login</em><br>';
            echo '</div></body></html>';
            return;
        }
        echo '</div>';

        // TEST 2: Charger helper et vérifier patient
        echo '<h2>2️⃣ Vérification Patient Diététique</h2>';
        echo '<div class="test">';

        try {
            $this->load->helper('dietetic/dietetic');
            echo '✅ Helper dietetic chargé<br>';

            // Vérifier méthode get_logged_in_patient
            if (function_exists('get_logged_in_patient_from_contact')) {
                echo '✅ Fonction get_logged_in_patient_from_contact existe<br>';
            } else {
                echo '⚠️ Fonction get_logged_in_patient_from_contact n\'existe pas<br>';
            }

        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }
        echo '</div>';

        // TEST 3: Charger modèle blog
        echo '<h2>3️⃣ Chargement Modèle Blog</h2>';
        echo '<div class="test">';

        try {
            $this->load->model('dietetic/dietetic_blog_model');
            echo '✅ Modèle dietetic_blog_model chargé<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
            echo '</div></body></html>';
            return;
        }
        echo '</div>';

        // TEST 4: Récupérer articles
        echo '<h2>4️⃣ Récupération Articles</h2>';
        echo '<div class="test">';

        try {
            $articles = $this->dietetic_blog_model->get_published(12, 0, null);
            echo '✅ Articles récupérés: ' . count($articles) . '<br>';

            if (!empty($articles)) {
                echo '<strong>Premier article:</strong> ' . htmlspecialchars($articles[0]->title) . '<br>';
            }
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        }
        echo '</div>';

        // TEST 5: Récupérer catégories
        echo '<h2>5️⃣ Récupération Catégories</h2>';
        echo '<div class="test">';

        try {
            $categories = $this->dietetic_blog_model->get_all_categories();
            echo '✅ Catégories récupérées: ' . count($categories) . '<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }
        echo '</div>';

        // TEST 6: Préparer données pour la vue
        echo '<h2>6️⃣ Préparation Données Vue</h2>';
        echo '<div class="test">';

        try {
            $data = [];
            $data['articles'] = $this->dietetic_blog_model->get_published(12, 0, null);
            $data['categories'] = $this->dietetic_blog_model->get_all_categories();
            $data['current_category'] = null;
            $data['total_pages'] = 1;
            $data['current_page'] = 1;
            $data['title'] = 'Conseils & Blog';
            $data['active_page'] = 'blog';

            echo '✅ Données préparées:<br>';
            echo '- Articles: ' . count($data['articles']) . '<br>';
            echo '- Catégories: ' . count($data['categories']) . '<br>';
            echo '- Titre: ' . $data['title'] . '<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }
        echo '</div>';

        // TEST 7: Vérifier fichier vue
        echo '<h2>7️⃣ Vérification Fichier Vue</h2>';
        echo '<div class="test">';

        $view_path = module_dir_path('dietetic', 'views/portal/blog/index.php');
        if (file_exists($view_path)) {
            echo '✅ Vue existe: <code>portal/blog/index.php</code><br>';
            echo 'Taille: ' . filesize($view_path) . ' bytes<br>';

            // Vérifier si la vue a des erreurs de syntaxe
            echo '<br><strong>Test de syntaxe PHP:</strong><br>';
            exec('php -l ' . escapeshellarg($view_path) . ' 2>&1', $output, $return_code);
            if ($return_code === 0) {
                echo '✅ Syntaxe PHP correcte<br>';
            } else {
                echo '❌ Erreur de syntaxe PHP:<br>';
                echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
            }
        } else {
            echo '❌ Vue n\'existe pas!<br>';
        }
        echo '</div>';

        // TEST 8: Vérifier header et footer
        echo '<h2>8️⃣ Vérification Header/Footer Portal</h2>';
        echo '<div class="test">';

        $header_path = module_dir_path('dietetic', 'views/portal/includes/portal_header.php');
        $footer_path = module_dir_path('dietetic', 'views/portal/includes/portal_footer.php');

        if (file_exists($header_path)) {
            echo '✅ Header existe (' . filesize($header_path) . ' bytes)<br>';
        } else {
            echo '❌ Header manquant<br>';
        }

        if (file_exists($footer_path)) {
            echo '✅ Footer existe (' . filesize($footer_path) . ' bytes)<br>';
        } else {
            echo '❌ Footer manquant<br>';
        }
        echo '</div>';

        // TEST 9: Tenter de charger la vue (RISQUÉ - peut causer erreur)
        echo '<h2>9️⃣ Test Chargement Vue (Simulation)</h2>';
        echo '<div class="test warning">';
        echo '⚠️ <strong>Ce test peut révéler l\'erreur fatale!</strong><br><br>';

        echo '<form method="post">';
        echo '<button type="submit" name="load_view" style="padding: 10px 20px; background: #ffc107; border: none; cursor: pointer; border-radius: 5px;">';
        echo '⚠️ CHARGER LA VUE (peut causer erreur 500)';
        echo '</button>';
        echo '</form>';

        if (isset($_POST['load_view'])) {
            echo '<br><strong>Tentative de chargement...</strong><br>';

            try {
                ob_start();

                // Simuler les données
                $articles = $this->dietetic_blog_model->get_published(12, 0, null);
                $categories = $this->dietetic_blog_model->get_all_categories();
                $current_category = null;
                $total_pages = 1;
                $current_page = 1;

                // Charger la vue
                $this->load->view('dietetic/portal/blog/index', [
                    'articles' => $articles,
                    'categories' => $categories,
                    'current_category' => $current_category,
                    'total_pages' => $total_pages,
                    'current_page' => $current_page
                ]);

                $output = ob_get_clean();

                echo '✅ Vue chargée sans erreur! (' . strlen($output) . ' caractères)<br>';
                echo '<details><summary>Voir HTML généré (premiers 500 caractères)</summary>';
                echo '<pre>' . htmlspecialchars(substr($output, 0, 500)) . '...</pre>';
                echo '</details>';

            } catch (Exception $e) {
                ob_end_clean();
                echo '❌ <strong>ERREUR TROUVÉE!</strong><br>';
                echo 'Message: <code>' . $e->getMessage() . '</code><br>';
                echo 'Fichier: ' . $e->getFile() . ':' . $e->getLine() . '<br>';
                echo '<details><summary>Stack Trace</summary>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
                echo '</details>';
            }
        }

        echo '</div>';

        // RÉSUMÉ
        echo '<h2>📊 RÉSUMÉ</h2>';
        echo '<div class="test">';
        echo '<strong>Si tous les tests sont ✅ sauf le test 9:</strong><br>';
        echo '→ L\'erreur vient probablement de la vue elle-même<br>';
        echo '→ Cliquez sur le bouton jaune ci-dessus pour identifier l\'erreur exacte<br><br>';

        echo '<strong>Accès direct:</strong><br>';
        echo '<a href="/dietetic/portal/blog" style="padding: 10px 20px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">Tester /dietetic/portal/blog</a>';
        echo '</div>';

        echo '</body></html>';
    }
}
