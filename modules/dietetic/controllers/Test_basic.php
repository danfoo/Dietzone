<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Basic - Ultra basique sans dépendances
 */
class Test_basic extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Ne rien charger du tout
    }

    public function index()
    {
        echo '<h1>Test Basic - Étape par étape</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;}</style>';

        echo '<h2>Étape 1: Contrôleur charge</h2>';
        echo '<p class="success">✅ Le contrôleur fonctionne</p>';

        echo '<h2>Étape 2: Tester chargement du helper</h2>';
        try {
            $this->load->helper('dietetic/dietetic');
            echo '<p class="success">✅ Helper dietetic chargé</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Helper ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
            return;
        }

        echo '<h2>Étape 3: Tester fonction dietetic_has_permission()</h2>';
        try {
            if (function_exists('dietetic_has_permission')) {
                $result = dietetic_has_permission('view');
                echo '<p class="success">✅ dietetic_has_permission() existe et retourne: ' . ($result ? 'true' : 'false') . '</p>';
            } else {
                echo '<p class="error">❌ dietetic_has_permission() n\'existe pas</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ dietetic_has_permission() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 4: Tester chargement du model</h2>';
        try {
            $this->load->model('dietetic/dietetic_patients_model');
            echo '<p class="success">✅ Model dietetic_patients_model chargé</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Model ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
            return;
        }

        echo '<h2>Étape 5: Tester get_all()</h2>';
        try {
            $patients = $this->dietetic_patients_model->get_all();
            echo '<p class="success">✅ get_all() retourne ' . count($patients) . ' patients</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ get_all() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 6: Vérifier fichier helper</h2>';
        $helper_file = FCPATH . 'modules/dietetic/helpers/dietetic_helper.php';
        if (file_exists($helper_file)) {
            echo '<p class="success">✅ Fichier helper existe: ' . $helper_file . '</p>';
        } else {
            echo '<p class="error">❌ Fichier helper MANQUANT: ' . $helper_file . '</p>';
        }

        echo '<h2>Étape 7: Lister toutes les fonctions du helper</h2>';
        if (file_exists($helper_file)) {
            $content = file_get_contents($helper_file);
            preg_match_all('/function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $content, $matches);
            echo '<p>Fonctions trouvées dans le helper:</p><ul>';
            foreach ($matches[1] as $func) {
                echo '<li>' . htmlspecialchars($func) . '</li>';
            }
            echo '</ul>';
        }

        echo '<hr>';
        echo '<p><strong>Si vous voyez ce message, le problème est ailleurs.</strong></p>';
        echo '<p>Essayez maintenant d\'accéder à <a href="' . admin_url('dietetic/patients') . '">/admin/dietetic/patients</a></p>';
    }
}
