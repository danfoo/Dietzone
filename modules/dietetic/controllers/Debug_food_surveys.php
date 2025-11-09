<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Diagnostic for Food Surveys Error 500
 * Access via: /admin/dietetic/debug_food_surveys
 * DELETE THIS FILE after diagnosis
 */
class Debug_food_surveys extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "<h1>🔍 Diagnostic Enquêtes Alimentaires (Erreur 500)</h1>";
        echo "<hr>";

        // 1. Current user
        echo "<h2>1. Utilisateur Actuel</h2>";
        $current_staff_id = get_staff_user_id();
        echo "<p><strong>Staff ID:</strong> {$current_staff_id}</p>";
        echo "<p><strong>is_admin():</strong> " . (is_admin() ? '✅ TRUE' : '❌ FALSE') . "</p>";

        $this->db->select('staffid, firstname, lastname, email, admin');
        $this->db->where('staffid', $current_staff_id);
        $user = $this->db->get(db_prefix() . 'staff')->row();
        if ($user) {
            echo "<p><strong>Nom:</strong> {$user->firstname} {$user->lastname}</p>";
            echo "<p><strong>Email:</strong> {$user->email}</p>";
            echo "<p><strong>Admin (DB):</strong> " . ($user->admin ? '✅ 1' : '❌ 0') . "</p>";
        }
        echo "<hr>";

        // 2. Check helper loading
        echo "<h2>2. Chargement du Helper</h2>";
        try {
            $this->load->helper('dietetic/dietetic');
            echo "<p>✅ Helper dietetic/dietetic chargé avec succès</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur lors du chargement du helper: " . $e->getMessage() . "</p>";
        }
        echo "<hr>";

        // 3. Check basic permission
        echo "<h2>3. Permission de Base (dietetic_has_permission)</h2>";
        try {
            $has_view = dietetic_has_permission('view');
            echo "<p><strong>dietetic_has_permission('view'):</strong> " . ($has_view ? '✅ TRUE' : '❌ FALSE') . "</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
        }
        echo "<hr>";

        // 4. Check feature permission
        echo "<h2>4. Permission Enquêtes Alimentaires</h2>";
        try {
            $has_food_surveys = dietetic_has_feature_permission('food_surveys');
            echo "<p><strong>dietetic_has_feature_permission('food_surveys'):</strong> " . ($has_food_surveys ? '✅ TRUE' : '❌ FALSE') . "</p>";

            if (!$has_food_surveys) {
                echo "<div style='background: #f44336; color: white; padding: 15px;'>";
                echo "<h3>❌ PERMISSION REFUSÉE</h3>";
                echo "<p>L'utilisateur actuel n'a pas la permission 'food_surveys'.</p>";
                echo "<p>Le contrôleur Food_surveys redirigera vers /dietetic/patients</p>";
                echo "</div>";
                echo "<hr>";
                return;
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
        }
        echo "<hr>";

        // 5. Check table existence
        echo "<h2>5. Vérification des Tables</h2>";
        $tables_to_check = [
            'dietic_food_surveys',
            'dietic_food_survey_entries',
            'dietic_food_survey_beverages',
            'dietic_food_survey_recommendations',
            'dietic_food_survey_comments'
        ];

        $all_tables_exist = true;
        foreach ($tables_to_check as $table) {
            $exists = $this->db->table_exists(db_prefix() . $table);
            echo "<p><strong>" . db_prefix() . $table . ":</strong> " . ($exists ? '✅ Existe' : '❌ N\'existe pas') . "</p>";
            if (!$exists) {
                $all_tables_exist = false;
            }
        }

        if (!$all_tables_exist) {
            echo "<div style='background: #f44336; color: white; padding: 15px;'>";
            echo "<h3>❌ TABLES MANQUANTES</h3>";
            echo "<p>Certaines tables n'existent pas. Le contrôleur devrait rediriger vers install_tables()</p>";
            echo "</div>";
        }
        echo "<hr>";

        // 6. Try to load models
        echo "<h2>6. Chargement des Modèles</h2>";

        try {
            $this->load->model('dietetic/dietetic_patients_model');
            echo "<p>✅ dietetic_patients_model chargé</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur dietetic_patients_model: " . $e->getMessage() . "</p>";
        }

        try {
            $this->load->model('dietetic/dietetic_programs_model');
            echo "<p>✅ dietetic_programs_model chargé</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur dietetic_programs_model: " . $e->getMessage() . "</p>";
        }

        try {
            $this->load->model('staff_model');
            echo "<p>✅ staff_model chargé</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Erreur staff_model: " . $e->getMessage() . "</p>";
        }

        if ($all_tables_exist) {
            try {
                $this->load->model('dietetic/dietetic_food_surveys_model');
                echo "<p>✅ dietetic_food_surveys_model chargé</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Erreur dietetic_food_surveys_model: " . $e->getMessage() . "</p>";
            }
        }
        echo "<hr>";

        // 7. Try to call get_all() on the model
        if ($all_tables_exist) {
            echo "<h2>7. Test de get_all() sur le Modèle</h2>";
            try {
                if (isset($this->dietetic_food_surveys_model)) {
                    $surveys = $this->dietetic_food_surveys_model->get_all();
                    echo "<p>✅ get_all() exécuté avec succès</p>";
                    echo "<p><strong>Nombre d'enquêtes:</strong> " . count($surveys) . "</p>";

                    if (count($surveys) > 0) {
                        echo "<table border='1' cellpadding='5'>";
                        echo "<tr><th>ID</th><th>Patient ID</th><th>Créée le</th></tr>";
                        foreach (array_slice($surveys, 0, 5) as $survey) {
                            echo "<tr>";
                            echo "<td>{$survey->id}</td>";
                            echo "<td>{$survey->patient_id}</td>";
                            echo "<td>{$survey->created_at}</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠️ Le modèle dietetic_food_surveys_model n'est pas chargé</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ ERREUR lors de get_all(): " . $e->getMessage() . "</p>";
                echo "<p><strong>Stack trace:</strong></p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        }
        echo "<hr>";

        // 8. Check PHP errors
        echo "<h2>8. Configuration PHP</h2>";
        echo "<p><strong>display_errors:</strong> " . ini_get('display_errors') . "</p>";
        echo "<p><strong>error_reporting:</strong> " . error_reporting() . "</p>";
        echo "<p><strong>log_errors:</strong> " . ini_get('log_errors') . "</p>";
        echo "<p><strong>error_log:</strong> " . ini_get('error_log') . "</p>";
        echo "<hr>";

        // 9. Try to simulate the full constructor
        echo "<h2>9. Simulation du Constructeur Food_surveys</h2>";
        echo "<p>Exécution du même code que le constructeur...</p>";
        try {
            // Same as Food_surveys constructor
            $this->load->helper('dietetic/dietetic');

            if (!dietetic_has_permission('view')) {
                echo "<p style='color: red;'>❌ dietetic_has_permission('view') a échoué</p>";
            } else {
                echo "<p>✅ dietetic_has_permission('view') OK</p>";
            }

            if (!dietetic_has_feature_permission('food_surveys')) {
                echo "<p style='color: red;'>❌ dietetic_has_feature_permission('food_surveys') a échoué</p>";
            } else {
                echo "<p>✅ dietetic_has_feature_permission('food_surveys') OK</p>";
            }

            $this->load->model('dietetic/dietetic_patients_model');
            $this->load->model('dietetic/dietetic_programs_model');
            $this->load->model('staff_model');

            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
                $this->load->model('dietetic/dietetic_food_surveys_model');
                echo "<p>✅ Tous les modèles chargés</p>";
            }

            echo "<p style='color: green;'><strong>✅ CONSTRUCTEUR SIMULÉ AVEC SUCCÈS</strong></p>";
            echo "<p>Le problème n'est PAS dans le constructeur.</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ ERREUR dans le constructeur: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        echo "<hr>";

        echo "<h2>10. 💡 Conclusion</h2>";
        echo "<p><strong>Si tout est ✅ ci-dessus, le problème est dans la méthode index() du contrôleur Food_surveys</strong></p>";
        echo "<p>Consultez les logs PHP pour voir l'erreur exacte :</p>";
        echo "<ul>";
        echo "<li>Logs Apache: /var/log/apache2/error.log</li>";
        echo "<li>Logs PHP: " . ini_get('error_log') . "</li>";
        echo "<li>Logs Perfex CRM: uploads/logs/</li>";
        echo "</ul>";

        echo "<p><em>⚠️ Copiez TOUT le contenu de cette page</em></p>";
    }
}
