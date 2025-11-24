<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debug_patient_create extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load Dietetic models
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!is_admin()) {
            access_denied('Debug - Admin only');
        }
    }

    /**
     * Test patient creation with full error reporting
     */
    public function index()
    {
        // Enable error display
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        echo "<!DOCTYPE html><html><head><title>Debug Patient Create</title>";
        echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;}";
        echo ".container{max-width:1200px;margin:0 auto;background:white;padding:30px;border-radius:8px;}";
        echo ".success{color:#28a745;background:#d4edda;padding:10px;margin:10px 0;border-radius:4px;}";
        echo ".error{color:#dc3545;background:#f8d7da;padding:10px;margin:10px 0;border-radius:4px;}";
        echo ".info{color:#0056b3;background:#cce5ff;padding:10px;margin:10px 0;border-radius:4px;}";
        echo "pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;font-size:12px;}";
        echo "</style></head><body><div class='container'>";
        echo "<h1>🔍 Debug: Test Création Patient</h1>";

        // Handle form submission
        if ($this->input->post('test_create')) {
            echo "<h2>📝 Étape 1: Récupération des données POST</h2>";

            $raw_data = $this->input->post();
            echo "<div class='info'>";
            echo "<strong>Données POST reçues:</strong><br>";
            echo "<pre>" . print_r($raw_data, true) . "</pre>";
            echo "</div>";

            echo "<h2>🔍 Étape 2: Filtrage des données</h2>";

            // Get valid columns
            $table_name = db_prefix() . 'dietic_patients';
            $query = $this->db->query("DESCRIBE `{$table_name}`");
            $valid_columns = [];
            foreach ($query->result_array() as $row) {
                $valid_columns[] = $row['Field'];
            }

            echo "<div class='info'>";
            echo "<strong>Colonnes valides dans la DB (" . count($valid_columns) . "):</strong><br>";
            echo "<pre>" . implode(', ', $valid_columns) . "</pre>";
            echo "</div>";

            // Filter data
            $filtered_data = [];
            $skipped_fields = [];
            foreach ($raw_data as $key => $value) {
                if ($key == 'test_create') continue; // Skip submit button

                if (in_array($key, $valid_columns)) {
                    $filtered_data[$key] = $value;
                } else {
                    $skipped_fields[] = $key;
                }
            }

            echo "<div class='success'>";
            echo "<strong>Données filtrées (" . count($filtered_data) . " champs):</strong><br>";
            echo "<pre>" . print_r($filtered_data, true) . "</pre>";
            echo "</div>";

            if (!empty($skipped_fields)) {
                echo "<div class='error'>";
                echo "<strong>Champs ignorés (" . count($skipped_fields) . "):</strong><br>";
                echo "<pre>" . implode(', ', $skipped_fields) . "</pre>";
                echo "</div>";
            }

            echo "<h2>💾 Étape 3: Test d'insertion dans la DB</h2>";

            try {
                // Prepare data
                $insert_data = $filtered_data;

                // Add timestamps
                $insert_data['created_at'] = date('Y-m-d H:i:s');

                // Calculate BMI if possible
                if (!empty($insert_data['initial_weight']) && !empty($insert_data['height'])) {
                    $weight = floatval($insert_data['initial_weight']);
                    $height = floatval($insert_data['height']);
                    if ($height > 0) {
                        $heightInMeters = $height / 100;
                        $insert_data['bmi'] = round($weight / ($heightInMeters * $heightInMeters), 1);
                    }
                }

                echo "<div class='info'>";
                echo "<strong>Données préparées pour l'insertion:</strong><br>";
                echo "<pre>" . print_r($insert_data, true) . "</pre>";
                echo "</div>";

                // Check if client already has patient
                if (!empty($insert_data['client_id'])) {
                    $this->db->where('client_id', $insert_data['client_id']);
                    $existing = $this->db->get(db_prefix() . 'dietic_patients')->row();

                    if ($existing) {
                        echo "<div class='error'>";
                        echo "<strong>❌ ERREUR:</strong> Ce client a déjà un dossier patient (ID: {$existing->id})";
                        echo "</div>";
                    } else {
                        echo "<div class='success'>✅ Client disponible pour création de patient</div>";
                    }
                } else {
                    echo "<div class='error'>❌ Aucun client_id fourni</div>";
                }

                // Test insert
                echo "<h3>Test d'insertion SQL...</h3>";

                $this->db->db_debug = false; // Disable automatic error display
                $result = $this->db->insert(db_prefix() . 'dietic_patients', $insert_data);

                if ($result) {
                    $patient_id = $this->db->insert_id();
                    echo "<div class='success'>";
                    echo "<h3>✅ SUCCÈS !</h3>";
                    echo "Patient créé avec l'ID: <strong>{$patient_id}</strong><br>";
                    echo "<a href='" . admin_url('dietetic/patients/view/' . $patient_id) . "'>Voir le patient</a>";
                    echo "</div>";
                } else {
                    $error = $this->db->error();
                    echo "<div class='error'>";
                    echo "<h3>❌ ÉCHEC de l'insertion</h3>";
                    echo "<strong>Code erreur:</strong> " . $error['code'] . "<br>";
                    echo "<strong>Message:</strong> " . $error['message'] . "<br>";
                    echo "</div>";

                    // Show last query
                    echo "<div class='error'>";
                    echo "<strong>Dernière requête SQL:</strong><br>";
                    echo "<pre>" . $this->db->last_query() . "</pre>";
                    echo "</div>";
                }

            } catch (Exception $e) {
                echo "<div class='error'>";
                echo "<h3>❌ EXCEPTION CAPTURÉE</h3>";
                echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
                echo "<strong>Fichier:</strong> " . $e->getFile() . "<br>";
                echo "<strong>Ligne:</strong> " . $e->getLine() . "<br>";
                echo "<strong>Stack trace:</strong><br>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                echo "</div>";
            }

            echo "<hr><a href='" . admin_url('dietetic/debug_patient_create') . "'>← Retour au formulaire</a>";

        } else {
            // Show test form
            echo "<p>Ce formulaire de test va créer un patient avec des données minimales et afficher toutes les étapes et erreurs.</p>";

            // Get first available client
            $clients = $this->clients_model->get();
            $available_client = null;

            foreach ($clients as $client) {
                $this->db->where('client_id', $client['userid']);
                $existing = $this->db->get(db_prefix() . 'dietic_patients')->row();
                if (!$existing) {
                    $available_client = $client;
                    break;
                }
            }

            if (!$available_client) {
                echo "<div class='error'>⚠️ Aucun client disponible (tous les clients ont déjà un dossier patient)</div>";
                echo "</div></body></html>";
                return;
            }

            $staff_id = get_staff_user_id();

            echo "<form method='post' style='background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;'>";
            echo "<h3>Formulaire de test avec données minimales</h3>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Client:</strong></label><br>";
            echo "<select name='client_id' style='padding:8px;width:100%;max-width:400px;'>";
            echo "<option value='{$available_client['userid']}'>{$available_client['company']} (ID: {$available_client['userid']})</option>";
            echo "</select>";
            echo "</div>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Diététicien:</strong></label><br>";
            echo "<input type='text' name='dietitian_id' value='{$staff_id}' readonly style='padding:8px;width:100%;max-width:400px;'>";
            echo "</div>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Statut:</strong></label><br>";
            echo "<select name='status' style='padding:8px;width:100%;max-width:400px;'>";
            echo "<option value='active'>Actif</option>";
            echo "<option value='inactive'>Inactif</option>";
            echo "</select>";
            echo "</div>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Genre:</strong></label><br>";
            echo "<select name='gender' style='padding:8px;width:100%;max-width:400px;'>";
            echo "<option value=''>-- Optionnel --</option>";
            echo "<option value='male'>Homme</option>";
            echo "<option value='female'>Femme</option>";
            echo "</select>";
            echo "</div>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Poids initial (kg):</strong></label><br>";
            echo "<input type='number' name='initial_weight' value='70' step='0.1' style='padding:8px;width:100%;max-width:400px;'>";
            echo "</div>";

            echo "<div style='margin:10px 0;'>";
            echo "<label><strong>Taille (cm):</strong></label><br>";
            echo "<input type='number' name='height' value='170' step='0.1' style='padding:8px;width:100%;max-width:400px;'>";
            echo "</div>";

            echo "<div style='margin:20px 0;'>";
            echo "<button type='submit' name='test_create' value='1' style='background:#28a745;color:white;padding:12px 24px;border:none;border-radius:4px;cursor:pointer;font-size:16px;'>🧪 Tester la création</button>";
            echo "</div>";

            echo "</form>";

            echo "<div class='info'>";
            echo "<strong>ℹ️ Note:</strong> Ce formulaire teste uniquement les champs de base. Si ça fonctionne, le problème vient d'un champ spécifique dans le formulaire complet.";
            echo "</div>";
        }

        echo "</div></body></html>";
    }
}
