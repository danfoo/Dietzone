<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * DIAGNOSTIC BLOG PORTAL - Trouver la source de l'erreur NS_ERROR_NET_ERROR_RESPONSE
 *
 * Accès: /dietetic/portal/test_blog_diagnostic
 *
 * Ce script teste chaque étape de la méthode blog() pour trouver où l'erreur se produit
 */
class Blog_diagnostic extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        // Forcer l'affichage des erreurs
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        echo '<html><head><title>Blog Diagnostic</title>';
        echo '<style>
            body { font-family: Arial; padding: 20px; background: #f5f5f5; }
            .test { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ccc; }
            .success { border-left-color: #28a745; }
            .error { border-left-color: #dc3545; background: #fff5f5; }
            .warning { border-left-color: #ffc107; background: #fffef5; }
            h1 { color: #01807B; }
            h2 { color: #333; margin-top: 30px; }
            code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
            pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
        </style></head><body>';

        echo '<h1>🔍 DIAGNOSTIC SYSTÈME BLOG - PORTAL PATIENT</h1>';
        echo '<p>Ce script teste chaque étape pour trouver l\'erreur...</p>';
        echo '<hr>';

        // TEST 1: Vérifier les tables
        echo '<h2>📋 TEST 1 : Vérification des tables</h2>';

        $tables_to_check = [
            'dietic_blog_articles',
            'dietic_blog_categories',
            'dietic_blog_comments',
            'dietic_blog_article_views'
        ];

        $all_tables_exist = true;
        foreach ($tables_to_check as $table) {
            $full_table_name = db_prefix() . $table;
            $exists = $this->db->table_exists($full_table_name);

            echo '<div class="test ' . ($exists ? 'success' : 'error') . '">';
            echo '<strong>' . ($exists ? '✅' : '❌') . ' Table:</strong> <code>' . $full_table_name . '</code>';

            if ($exists) {
                $count = $this->db->count_all($full_table_name);
                echo ' - <em>' . $count . ' ligne(s)</em>';
            } else {
                echo ' - <strong>TABLE MANQUANTE!</strong>';
                $all_tables_exist = false;
            }
            echo '</div>';
        }

        if (!$all_tables_exist) {
            echo '<div class="test error">';
            echo '<strong>❌ PROBLÈME DÉTECTÉ:</strong> Des tables sont manquantes!<br>';
            echo 'Solution: Exécutez le script <code>modules/dietetic/install_blog_system.sql</code>';
            echo '</div>';
            echo '</body></html>';
            return;
        }

        // TEST 2: Charger le modèle
        echo '<h2>📦 TEST 2 : Chargement du modèle</h2>';

        try {
            $this->load->model('dietetic/dietetic_blog_model');
            echo '<div class="test success">✅ Modèle <code>Dietetic_blog_model</code> chargé avec succès</div>';
        } catch (Exception $e) {
            echo '<div class="test error">';
            echo '❌ <strong>ERREUR lors du chargement du modèle:</strong><br>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</div>';
            echo '</body></html>';
            return;
        }

        // TEST 3: Tester les méthodes du modèle
        echo '<h2>🔧 TEST 3 : Méthodes du modèle</h2>';

        $methods_to_test = [
            'get_published' => ['limit' => 12, 'offset' => 0, 'category' => null],
            'get_count' => [['status' => 'published']],
            'get_all_categories' => [],
            'get_featured' => [3]
        ];

        foreach ($methods_to_test as $method => $params) {
            echo '<div class="test">';
            echo '<strong>Méthode:</strong> <code>' . $method . '()</code><br>';

            try {
                $result = call_user_func_array([$this->dietetic_blog_model, $method], $params);
                echo '✅ <strong>Succès!</strong> ';

                if (is_array($result)) {
                    echo 'Retourne ' . count($result) . ' résultat(s)';
                } elseif (is_numeric($result)) {
                    echo 'Retourne: ' . $result;
                } else {
                    echo 'Type: ' . gettype($result);
                }

                if ($method === 'get_published' && is_array($result) && !empty($result)) {
                    echo '<br><em>Premier article: "' . htmlspecialchars($result[0]->title) . '"</em>';
                }

            } catch (Exception $e) {
                echo '❌ <strong>ERREUR:</strong><br>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }

            echo '</div>';
        }

        // TEST 4: Vérifier les vues
        echo '<h2>🎨 TEST 4 : Vérification des vues</h2>';

        $views_to_check = [
            'portal/blog/index.php',
            'portal/blog/article.php',
            'portal/blog/search.php',
            'portal/includes/portal_header.php',
            'portal/includes/portal_footer.php'
        ];

        foreach ($views_to_check as $view) {
            $view_path = module_dir_path('dietetic', 'views/' . $view);
            $exists = file_exists($view_path);

            echo '<div class="test ' . ($exists ? 'success' : 'error') . '">';
            echo ($exists ? '✅' : '❌') . ' <code>' . $view . '</code>';
            if ($exists) {
                echo ' - ' . filesize($view_path) . ' bytes';
            }
            echo '</div>';
        }

        // TEST 5: Simuler la méthode blog()
        echo '<h2>🚀 TEST 5 : Simulation de la méthode blog()</h2>';

        echo '<div class="test">';
        echo '<strong>Étape 1:</strong> Vérifier patient connecté...<br>';

        // Vérifier si on peut charger le modèle patients
        try {
            $this->load->model('dietetic/dietetic_patients_model');
            echo '✅ Modèle patients chargé<br>';
        } catch (Exception $e) {
            echo '❌ Erreur modèle patients: ' . $e->getMessage() . '<br>';
        }

        echo '<br><strong>Étape 2:</strong> Récupération des articles...<br>';
        try {
            $per_page = 12;
            $articles = $this->dietetic_blog_model->get_published($per_page, 0, null);
            echo '✅ ' . count($articles) . ' article(s) récupéré(s)<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }

        echo '<br><strong>Étape 3:</strong> Récupération des catégories...<br>';
        try {
            $categories = $this->dietetic_blog_model->get_all_categories();
            echo '✅ ' . count($categories) . ' catégorie(s) récupérée(s)<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }

        echo '<br><strong>Étape 4:</strong> Comptage total...<br>';
        try {
            $total = $this->dietetic_blog_model->get_count([
                'status' => 'published',
                'published_at <=' => date('Y-m-d H:i:s')
            ]);
            echo '✅ Total: ' . $total . ' article(s) publié(s)<br>';
        } catch (Exception $e) {
            echo '❌ Erreur: ' . $e->getMessage() . '<br>';
        }

        echo '</div>';

        // TEST 6: Tester le routing
        echo '<h2>🛣️ TEST 6 : Routes et méthodes valides</h2>';

        echo '<div class="test">';
        echo '<strong>Vérifier Portal.php...</strong><br>';

        $portal_path = module_dir_path('dietetic', 'controllers/Portal.php');
        if (file_exists($portal_path)) {
            $content = file_get_contents($portal_path);

            // Vérifier si les méthodes blog sont dans valid_methods
            if (strpos($content, "'blog'") !== false) {
                echo "✅ Méthode 'blog' trouvée dans valid_methods<br>";
            } else {
                echo "❌ Méthode 'blog' MANQUANTE dans valid_methods<br>";
            }

            if (strpos($content, "'blog_article'") !== false) {
                echo "✅ Méthode 'blog_article' trouvée dans valid_methods<br>";
            } else {
                echo "❌ Méthode 'blog_article' MANQUANTE dans valid_methods<br>";
            }
        } else {
            echo '❌ Fichier Portal.php non trouvé<br>';
        }

        echo '</div>';

        // TEST 7: Logs d'erreurs PHP
        echo '<h2>📝 TEST 7 : Logs d\'erreurs récents</h2>';

        $log_path = FCPATH . 'application/logs/';
        if (is_dir($log_path)) {
            $log_files = glob($log_path . 'log-*.php');
            if (!empty($log_files)) {
                rsort($log_files); // Plus récent en premier
                $latest_log = $log_files[0];

                echo '<div class="test">';
                echo '<strong>Dernier fichier de log:</strong> ' . basename($latest_log) . '<br>';

                $log_content = file_get_contents($latest_log);
                $lines = explode("\n", $log_content);
                $error_lines = array_filter($lines, function($line) {
                    return stripos($line, 'error') !== false ||
                           stripos($line, 'blog') !== false ||
                           stripos($line, 'portal') !== false;
                });

                if (!empty($error_lines)) {
                    echo '<strong>Erreurs trouvées:</strong><br>';
                    echo '<pre>' . htmlspecialchars(implode("\n", array_slice($error_lines, -10))) . '</pre>';
                } else {
                    echo '✅ Aucune erreur récente trouvée dans les logs';
                }

                echo '</div>';
            } else {
                echo '<div class="test warning">⚠️ Aucun fichier de log trouvé</div>';
            }
        } else {
            echo '<div class="test warning">⚠️ Dossier logs non accessible</div>';
        }

        // RÉSUMÉ FINAL
        echo '<h2>📊 RÉSUMÉ & RECOMMANDATIONS</h2>';

        echo '<div class="test">';
        echo '<strong>Si toutes les vérifications sont ✅:</strong><br>';
        echo '1. Le problème vient peut-être du patient non connecté<br>';
        echo '2. Ou d\'un problème de session<br>';
        echo '3. Essayez d\'accéder directement à: <code>/dietetic/portal/blog</code> en étant connecté<br><br>';

        echo '<strong>Prochaines étapes:</strong><br>';
        echo '1. Vérifiez les résultats ci-dessus<br>';
        echo '2. Notez les ❌ (erreurs)<br>';
        echo '3. Corrigez dans l\'ordre des priorités<br>';
        echo '</div>';

        echo '<hr>';
        echo '<p><strong>Diagnostic terminé!</strong> Copiez ces résultats et partagez-les pour analyse.</p>';

        echo '</body></html>';
    }
}
