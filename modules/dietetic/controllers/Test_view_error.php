<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_view_error extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recipes_model');
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<h1>Test View Error</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:4px;overflow:auto;}</style>';

        echo '<h2>Test 1: Charger la recette</h2>';
        
        try {
            $recipe = $this->dietetic_recipes_model->get(7);
            
            if ($recipe) {
                echo '<p class="success">✅ Recette chargée: ' . htmlspecialchars($recipe->name) . '</p>';
            } else {
                echo '<p class="error">❌ Recette non trouvée</p>';
                return;
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement de la recette:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            return;
        }

        echo '<h2>Test 2: Charger les patients assignés</h2>';
        
        try {
            $assigned_patients = $this->dietetic_recipes_model->get_assigned_patients(7);
            
            echo '<p class="success">✅ Méthode get_assigned_patients() exécutée</p>';
            echo '<p>Nombre de patients assignés: <strong>' . count($assigned_patients) . '</strong></p>';
            
            if (count($assigned_patients) > 0) {
                echo '<pre>' . print_r($assigned_patients, true) . '</pre>';
            }
            
        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des patients assignés:</p>';
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
            
            return;
        }

        echo '<h2>Test 3: Charger tous les patients (dropdown)</h2>';
        
        try {
            $this->load->model('dietetic/dietetic_patients_model');
            $patients = $this->dietetic_patients_model->get_all();
            
            echo '<p class="success">✅ Patients chargés: ' . count($patients) . '</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION lors du chargement des patients:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            return;
        }

        echo '<h2>✅ Tous les tests passent!</h2>';
        echo '<p>Le problème ne vient pas de la logique PHP. Vérifiez:</p>';
        echo '<ul>';
        echo '<li>Erreurs dans le HTML/PHP de la vue</li>';
        echo '<li>Fonctions manquantes (dietetic_has_permission, etc.)</li>';
        echo '<li>Logs du serveur</li>';
        echo '</ul>';
    }
}
