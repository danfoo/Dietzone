<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_recipes_display extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Test Recipes Display</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:4px;overflow:auto;}</style>';
        echo '</head><body>';

        echo '<h1>🔍 Test Recipes Display</h1>';

        if (!is_client_logged_in()) {
            echo '<p class="error">❌ Non connecté</p>';
            echo '</body></html>';
            return;
        }

        $client_id = get_client_user_id();
        echo '<p class="success">✅ Client ID: ' . $client_id . '</p>';

        $this->load->model('dietetic/dietetic_recipes_model');

        // Find patient
        $this->db->where('client_id', $client_id);
        $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

        if (!$patient) {
            echo '<p class="error">❌ Aucun patient trouvé</p>';
            echo '</body></html>';
            return;
        }

        echo '<p class="success">✅ Patient ID: ' . $patient->id . '</p>';

        // Get recipes
        $recipes = $this->dietetic_recipes_model->get_patient_recipes($patient->id);

        echo '<h2>Recettes récupérées:</h2>';
        echo '<p><strong>Nombre:</strong> ' . count($recipes) . '</p>';

        if (count($recipes) > 0) {
            echo '<pre>' . print_r($recipes, true) . '</pre>';
        }

        echo '<h2>Test de la vue:</h2>';
        
        $view_path = FCPATH . 'modules/dietetic/views/portal/recipes/list.php';
        
        if (file_exists($view_path)) {
            echo '<p class="success">✅ Vue list.php existe</p>';
            echo '<p>Chemin: ' . $view_path . '</p>';
            
            echo '<h3>Essai de chargement de la vue:</h3>';
            
            $data['recipes'] = $recipes;
            $data['patient'] = $patient;
            $data['title'] = 'Mes Recettes';
            
            try {
                echo '<div style="border:2px solid blue; padding:20px; margin:20px 0;">';
                echo '<h4>Contenu de la vue:</h4>';
                $this->load->view('portal/recipes/list', $data);
                echo '</div>';
            } catch (Exception $e) {
                echo '<p class="error">❌ Erreur lors du chargement de la vue:</p>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }
            
        } else {
            echo '<p class="error">❌ Vue list.php N\'EXISTE PAS!</p>';
        }

        echo '</body></html>';
    }
}
