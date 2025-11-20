<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_recipes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Test');
        }

        echo "<!DOCTYPE html><html><head><title>Test Recipes</title>";
        echo "<style>body{font-family:Arial;padding:20px;background:#1e1e1e;color:#d4d4d4;} .success{color:#4ec9b0;} .error{color:#f48771;} .info{color:#569cd6;} pre{background:#252526;padding:15px;border:1px solid #3e3e42;overflow-x:auto;} .section{background:#252526;padding:20px;margin:20px 0;border-radius:8px;border:1px solid #3e3e42;} h2{color:#4ec9b0;}</style>";
        echo "</head><body>";

        echo "<h1>🧪 Test du contrôleur Recipes</h1>";

        $CI = &get_instance();

        // TEST 1: Charger le helper
        echo "<div class='section'>";
        echo "<h2>TEST 1: Charger le helper</h2>";
        try {
            $CI->load->helper('dietetic/dietetic');
            echo "<p class='success'>✅ Helper chargé</p>";

            if (function_exists('dietetic_has_permission')) {
                echo "<p class='success'>✅ Fonction dietetic_has_permission existe</p>";

                $result = dietetic_has_permission('view');
                echo "<p class='success'>✅ dietetic_has_permission('view') = " . ($result ? 'TRUE' : 'FALSE') . "</p>";
            } else {
                echo "<p class='error'>❌ Fonction dietetic_has_permission n'existe PAS</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
        }
        echo "</div>";

        // TEST 2: Charger le modèle
        echo "<div class='section'>";
        echo "<h2>TEST 2: Charger le modèle Recipes</h2>";
        try {
            $CI->load->model('dietetic/dietetic_recipes_model');
            echo "<p class='success'>✅ Modèle chargé</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        // TEST 3: Appeler get_all()
        echo "<div class='section'>";
        echo "<h2>TEST 3: Appeler get_all()</h2>";
        try {
            $recipes = $CI->dietetic_recipes_model->get_all([], 'all');
            echo "<p class='success'>✅ get_all() exécuté</p>";
            echo "<p class='info'>Nombre de recettes: " . count($recipes) . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur dans get_all(): " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        // TEST 4: Appeler get_statistics()
        echo "<div class='section'>";
        echo "<h2>TEST 4: Appeler get_statistics()</h2>";
        try {
            $stats = $CI->dietetic_recipes_model->get_statistics();
            echo "<p class='success'>✅ get_statistics() exécuté</p>";
            echo "<pre>" . print_r($stats, true) . "</pre>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur dans get_statistics(): " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        // TEST 5: Charger la vue list.php
        echo "<div class='section'>";
        echo "<h2>TEST 5: Vérifier la vue</h2>";
        $view_path = FCPATH . 'modules/dietetic/views/admin/recipes/list.php';
        if (file_exists($view_path)) {
            echo "<p class='success'>✅ Vue list.php existe</p>";
            echo "<p class='info'>Chemin: " . $view_path . "</p>";
        } else {
            echo "<p class='error'>❌ Vue list.php n'existe PAS</p>";
        }
        echo "</div>";

        // TEST 6: Test complet du contrôleur
        echo "<div class='section'>";
        echo "<h2>TEST 6: Simuler index() du contrôleur</h2>";
        try {
            $status = 'all';
            $data = [];
            $data['title'] = 'Bibliothèque de Recettes';
            $data['recipes'] = $CI->dietetic_recipes_model->get_all([], $status);
            $data['current_status'] = $status;
            $data['stats'] = $CI->dietetic_recipes_model->get_statistics();

            echo "<p class='success'>✅ Toutes les données préparées sans erreur</p>";
            echo "<p class='info'>Nombre de recettes: " . count($data['recipes']) . "</p>";
            echo "<p class='info'>Stats:</p>";
            echo "<pre>" . print_r($data['stats'], true) . "</pre>";

            // Essayer de charger la vue
            echo "<p class='info'>Tentative de chargement de la vue...</p>";
            ob_start();
            $CI->load->view('dietetic/admin/recipes/list', $data);
            $view_output = ob_get_clean();

            echo "<p class='success'>✅ Vue chargée sans erreur!</p>";
            echo "<p class='info'>Taille de la sortie: " . strlen($view_output) . " octets</p>";

        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "</div>";

        echo "<div class='section' style='background:#3a2a1a;border-color:#f39c12;'>";
        echo "<h2>🔧 Conclusion</h2>";
        echo "<p>Si tous les tests passent ici mais que /admin/dietetic/recipes donne erreur 500,</p>";
        echo "<p>le problème est probablement:</p>";
        echo "<ul>";
        echo "<li>Le constructeur du contrôleur Recipes qui plante</li>";
        echo "<li>La vérification de permissions qui échoue</li>";
        echo "<li>Un problème dans la vue elle-même</li>";
        echo "</ul>";
        echo "<hr>";
        echo "<a href='" . admin_url('dietetic/recipes') . "' style='color:#3498db;'>Tester /admin/dietetic/recipes maintenant</a>";
        echo "</div>";

        echo "</body></html>";
    }
}
