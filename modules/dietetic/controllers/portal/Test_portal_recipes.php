<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_portal_recipes extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Test Portal Recipes</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:4px;overflow:auto;}</style>';
        echo '</head><body>';

        echo '<h1>🔍 Test Portal Recipes Error</h1>';

        echo '<h2>Test 1: Client Login Status</h2>';

        if (is_client_logged_in()) {
            $client_id = get_client_user_id();
            echo '<p class="success">✅ Client connecté - ID: ' . $client_id . '</p>';
        } else {
            echo '<p class="error">❌ Client NON connecté</p>';
            echo '<p>Vous devez être connecté en tant que client pour accéder au portail.</p>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 2: Charger le modèle</h2>';

        try {
            $this->load->model('dietetic/dietetic_recipes_model');
            echo '<p class="success">✅ Modèle dietetic_recipes_model chargé</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement du modèle:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 3: Vérifier la méthode get_patient_recipes()</h2>';

        if (method_exists($this->dietetic_recipes_model, 'get_patient_recipes')) {
            echo '<p class="success">✅ Méthode get_patient_recipes() existe</p>';
        } else {
            echo '<p class="error">❌ Méthode get_patient_recipes() N\'EXISTE PAS!</p>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 4: Trouver le patient lié au client</h2>';

        try {
            $this->load->model('dietetic/dietetic_patients_model');

            $this->db->where('client_id', $client_id);
            $patient = $this->db->get(db_prefix() . 'dietic_patients')->row();

            if ($patient) {
                echo '<p class="success">✅ Patient trouvé - ID: ' . $patient->id . '</p>';

                echo '<h2>Test 5: Charger les recettes du patient</h2>';

                try {
                    $recipes = $this->dietetic_recipes_model->get_patient_recipes($patient->id);
                    echo '<p class="success">✅ Recettes chargées: ' . count($recipes) . '</p>';

                    if (count($recipes) > 0) {
                        echo '<pre>' . print_r($recipes, true) . '</pre>';
                    } else {
                        echo '<p>Aucune recette assignée à ce patient.</p>';
                    }

                } catch (Exception $e) {
                    echo '<p class="error">❌ EXCEPTION lors du chargement des recettes:</p>';
                    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';

                    // Afficher l'erreur SQL
                    $error = $this->db->error();
                    if (!empty($error['message'])) {
                        echo '<p class="error"><strong>Erreur SQL:</strong></p>';
                        echo '<pre>' . htmlspecialchars(print_r($error, true)) . '</pre>';
                    }

                    // Afficher la dernière requête
                    echo '<p><strong>Dernière requête SQL:</strong></p>';
                    echo '<pre>' . htmlspecialchars($this->db->last_query()) . '</pre>';
                }

            } else {
                echo '<p class="error">❌ Aucun patient trouvé pour ce client</p>';
                echo '<p>Client ID: ' . $client_id . '</p>';
                echo '<p>Le client doit être lié à un patient dans la table dietic_patients.</p>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors de la recherche du patient:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }

        echo '<h2>Test 6: Vérifier le contrôleur Portal Recipes</h2>';

        $controller_path = FCPATH . 'modules/dietetic/controllers/portal/Recipes.php';

        if (file_exists($controller_path)) {
            echo '<p class="success">✅ Contrôleur portal/Recipes.php existe</p>';
            echo '<p>Chemin: ' . $controller_path . '</p>';
        } else {
            echo '<p class="error">❌ Contrôleur portal/Recipes.php N\'EXISTE PAS!</p>';
        }

        echo '</body></html>';
    }
}
