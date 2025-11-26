<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_direct_insert extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('clients_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!is_admin()) {
            access_denied('Test - Admin only');
        }
    }

    /**
     * Test direct database insert bypassing all forms
     */
    public function index()
    {
        // Enable full error reporting
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        header('Content-Type: text/html; charset=utf-8');

        echo "<!DOCTYPE html><html><head><title>Test Direct Insert</title>";
        echo "<style>
            body{font-family:Arial;margin:20px;background:#f5f5f5;}
            .container{max-width:900px;margin:0 auto;background:white;padding:30px;border-radius:8px;}
            .success{background:#d4edda;color:#155724;padding:15px;margin:10px 0;border-radius:4px;border-left:4px solid #28a745;}
            .error{background:#f8d7da;color:#721c24;padding:15px;margin:10px 0;border-radius:4px;border-left:4px solid #dc3545;}
            .info{background:#cce5ff;color:#004085;padding:15px;margin:10px 0;border-radius:4px;border-left:4px solid #0056b3;}
            pre{background:#f8f9fa;padding:15px;border-radius:5px;overflow-x:auto;font-size:12px;white-space:pre-wrap;}
            h2{color:#333;border-bottom:2px solid #007bff;padding-bottom:10px;margin-top:30px;}
        </style></head><body><div class='container'>";

        echo "<h1>🧪 Test Direct Insert - Patient DB</h1>";
        echo "<p>Ce test insère directement un patient dans la DB avec des données minimales (sans formulaire, sans POST).</p>";

        // Find first available client
        echo "<h2>1️⃣ Recherche d'un client disponible</h2>";
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
            echo "<div class='error'>❌ Aucun client disponible pour le test (tous ont déjà un dossier patient)</div>";
            echo "<p><strong>Solution:</strong> Créez d'abord un nouveau client dans Perfex CRM.</p>";
            echo "</div></body></html>";
            return;
        }

        echo "<div class='success'>";
        echo "✅ Client trouvé: <strong>{$available_client['company']}</strong> (ID: {$available_client['userid']})<br>";
        echo "Email: {$available_client['email']}";
        echo "</div>";

        // Prepare minimal data
        echo "<h2>2️⃣ Préparation des données minimales</h2>";

        $staff_id = get_staff_user_id();

        $test_data = [
            'client_id' => $available_client['userid'],
            'dietitian_id' => $staff_id,
            'status' => 'active'
        ];

        echo "<div class='info'>";
        echo "<strong>Données à insérer:</strong><br>";
        echo "<pre>" . print_r($test_data, true) . "</pre>";
        echo "</div>";

        // Test 1: Direct DB insert (bypass model)
        echo "<h2>3️⃣ Test #1: Insertion SQL directe (bypass model)</h2>";

        try {
            $insert_data = $test_data;
            $insert_data['created_at'] = date('Y-m-d H:i:s');

            $this->db->db_debug = false; // Disable auto error display

            $result = $this->db->insert(db_prefix() . 'dietic_patients', $insert_data);

            if ($result) {
                $patient_id = $this->db->insert_id();
                echo "<div class='success'>";
                echo "✅ <strong>SUCCÈS!</strong> Insertion SQL directe réussie<br>";
                echo "Patient créé avec l'ID: <strong>{$patient_id}</strong><br>";
                echo "<a href='" . admin_url('dietetic/patients/view/' . $patient_id) . "' target='_blank'>→ Voir le patient</a>";
                echo "</div>";

                // Clean up - delete test patient
                echo "<div class='info'>";
                echo "🧹 Nettoyage: Suppression du patient de test...";
                $this->db->where('id', $patient_id);
                $this->db->delete(db_prefix() . 'dietic_patients');
                echo " ✅ Fait";
                echo "</div>";

            } else {
                $error = $this->db->error();
                echo "<div class='error'>";
                echo "❌ <strong>ÉCHEC</strong> de l'insertion SQL<br>";
                echo "<strong>Code erreur MySQL:</strong> {$error['code']}<br>";
                echo "<strong>Message:</strong> {$error['message']}<br>";
                echo "</div>";

                echo "<div class='error'>";
                echo "<strong>Requête SQL:</strong><br>";
                echo "<pre>" . $this->db->last_query() . "</pre>";
                echo "</div>";
            }

        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "❌ <strong>EXCEPTION:</strong> {$e->getMessage()}<br>";
            echo "<strong>Fichier:</strong> {$e->getFile()} ligne {$e->getLine()}";
            echo "</div>";
        }

        // Test 2: Using model add() method
        echo "<h2>4️⃣ Test #2: Via le modèle (add method)</h2>";

        try {
            $patient_id = $this->dietetic_patients_model->add($test_data);

            if ($patient_id) {
                echo "<div class='success'>";
                echo "✅ <strong>SUCCÈS!</strong> Création via le modèle réussie<br>";
                echo "Patient créé avec l'ID: <strong>{$patient_id}</strong><br>";
                echo "<a href='" . admin_url('dietetic/patients/view/' . $patient_id) . "' target='_blank'>→ Voir le patient</a>";
                echo "</div>";

                // Clean up
                echo "<div class='info'>";
                echo "🧹 Nettoyage: Suppression du patient de test...";
                $this->db->where('id', $patient_id);
                $this->db->delete(db_prefix() . 'dietic_patients');
                echo " ✅ Fait";
                echo "</div>";

            } else {
                echo "<div class='error'>";
                echo "❌ <strong>ÉCHEC:</strong> Le modèle a retourné FALSE<br>";
                echo "Cela peut signifier que le client a déjà un dossier patient.";
                echo "</div>";
            }

        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "❌ <strong>EXCEPTION dans le modèle:</strong><br>";
            echo "<strong>Message:</strong> {$e->getMessage()}<br>";
            echo "<strong>Fichier:</strong> {$e->getFile()} ligne {$e->getLine()}<br>";
            echo "<strong>Stack trace:</strong><br>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }

        // Summary
        echo "<h2>📊 Résumé</h2>";
        echo "<div class='info'>";
        echo "<p>Ce test permet d'identifier où se situe le problème:</p>";
        echo "<ul>";
        echo "<li>✅ <strong>Test #1 réussi + Test #2 réussi</strong> → Le problème vient du contrôleur ou du formulaire</li>";
        echo "<li>✅ <strong>Test #1 réussi + Test #2 échoué</strong> → Le problème est dans le modèle (Dietetic_patients_model)</li>";
        echo "<li>❌ <strong>Test #1 échoué</strong> → Problème de base de données (contraintes, colonnes manquantes, etc.)</li>";
        echo "</ul>";
        echo "</div>";

        echo "</div></body></html>";
    }
}
