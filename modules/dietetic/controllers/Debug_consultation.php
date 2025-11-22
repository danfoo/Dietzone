<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Debug Controller for Consultation Creation
 * URL: /admin/dietetic/debug_consultation
 */
class Debug_consultation extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_patients_model');
    }

    /**
     * Debug consultation creation with detailed error reporting
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('Debug');
        }

        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<title>Debug Consultation</title>";
        echo "<style>
            body { font-family: 'Courier New', monospace; max-width: 1200px; margin: 20px auto; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
            h1, h2, h3 { color: #4ec9b0; }
            .success { color: #4ec9b0; }
            .error { color: #f48771; }
            .warning { color: #dcdcaa; }
            .info { color: #9cdcfe; }
            pre { background: #252526; padding: 15px; border-radius: 5px; overflow-x: auto; border-left: 3px solid #007acc; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th, td { padding: 10px; text-align: left; border: 1px solid #3c3c3c; }
            th { background: #2d2d30; color: #4ec9b0; }
            tr:nth-child(even) { background: #252526; }
            .highlight { background: #264f78; }
            .btn { display: inline-block; padding: 10px 20px; background: #007acc; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
            .btn:hover { background: #005a9e; }
            .section { background: #252526; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007acc; }
            .code { background: #1e1e1e; padding: 2px 6px; border-radius: 3px; color: #ce9178; }
        </style>";
        echo "</head><body>";

        echo "<h1>🔍 Diagnostic Consultation - Debug Complet</h1>";
        echo "<hr>";

        // Check if this is a POST request (form submission)
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->debug_post_request();
        } else {
            $this->show_diagnostic_info();
        }

        echo "</body></html>";
    }

    /**
     * Show diagnostic information
     */
    private function show_diagnostic_info()
    {
        echo "<div class='section'>";
        echo "<h2>📊 Étape 1: Vérification Structure BDD</h2>";

        $table_name = db_prefix() . 'dietic_consultations';

        // Check table existence
        $table_exists = $this->db->table_exists(db_prefix() . 'dietic_consultations');

        if (!$table_exists) {
            echo "<p class='error'>✗ ERREUR CRITIQUE: La table {$table_name} n'existe pas!</p>";
            return;
        } else {
            echo "<p class='success'>✓ Table {$table_name} existe</p>";
        }

        // Get columns
        $query = $this->db->query("SHOW COLUMNS FROM {$table_name}");
        $columns = $query->result();

        $required_columns = [
            'consultation_mode' => 'VARCHAR(20)',
            'online_platform' => 'VARCHAR(50)',
            'meeting_link' => 'VARCHAR(500)'
        ];

        $existing_columns = [];
        foreach ($columns as $column) {
            $existing_columns[$column->Field] = $column->Type;
        }

        $missing_columns = [];
        foreach ($required_columns as $col => $type) {
            if (!isset($existing_columns[$col])) {
                $missing_columns[] = $col;
            }
        }

        if (!empty($missing_columns)) {
            echo "<p class='error'>✗ Colonnes manquantes détectées:</p>";
            echo "<ul class='error'>";
            foreach ($missing_columns as $col) {
                echo "<li>{$col}</li>";
            }
            echo "</ul>";
            echo "<p class='warning'>⚠ Vous devez d'abord appliquer la migration!</p>";
            echo "<p><a href='" . admin_url('dietetic/migrate_consultations') . "' class='btn'>Exécuter la Migration</a></p>";
        } else {
            echo "<p class='success'>✓ Toutes les colonnes requises existent</p>";
        }

        echo "<h3>Structure Complète de la Table:</h3>";
        echo "<table>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Défaut</th></tr>";
        foreach ($columns as $column) {
            $highlight = isset($required_columns[$column->Field]) ? "class='highlight'" : "";
            echo "<tr {$highlight}>";
            echo "<td><code>{$column->Field}</code></td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";

        // Check patients
        echo "<div class='section'>";
        echo "<h2>👥 Étape 2: Vérification Patients</h2>";

        $patients = $this->dietetic_patients_model->get();

        if (empty($patients)) {
            echo "<p class='error'>✗ Aucun patient trouvé dans la base de données</p>";
            echo "<p class='warning'>⚠ Vous devez créer au moins un patient avant de créer une consultation</p>";
        } else {
            echo "<p class='success'>✓ {count($patients)} patient(s) trouvé(s)</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Nom</th><th>Client ID</th></tr>";
            foreach (array_slice($patients, 0, 5) as $patient) {
                echo "<tr>";
                echo "<td>{$patient->id}</td>";
                echo "<td>{$patient->client_name}</td>";
                echo "<td>{$patient->client_id}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        echo "</div>";

        // Check staff
        echo "<div class='section'>";
        echo "<h2>👨‍⚕️ Étape 3: Vérification Staff</h2>";

        $this->load->model('staff_model');
        $staff = $this->staff_model->get();

        if (empty($staff)) {
            echo "<p class='error'>✗ Aucun membre du staff trouvé</p>";
        } else {
            echo "<p class='success'>✓ {count($staff)} membre(s) du staff trouvé(s)</p>";
        }
        echo "</div>";

        // PHP errors check
        echo "<div class='section'>";
        echo "<h2>🐛 Étape 4: Test de Création de Consultation</h2>";
        echo "<p class='info'>Nous allons maintenant tester la création d'une consultation avec des données fictives.</p>";

        if (!empty($patients) && !empty($staff) && empty($missing_columns)) {
            echo "<form method='POST' action='" . admin_url('dietetic/debug_consultation') . "'>";
            echo "<input type='hidden' name='test_create' value='1'>";
            echo "<button type='submit' class='btn'>🧪 Tester la Création</button>";
            echo "</form>";
        } else {
            echo "<p class='error'>✗ Impossible de tester: corrigez d'abord les erreurs ci-dessus</p>";
        }
        echo "</div>";

        // Show error logs location
        echo "<div class='section'>";
        echo "<h2>📝 Étape 5: Vérifier les Logs PHP</h2>";
        echo "<p>Les erreurs 500 sont généralement loggées dans:</p>";
        echo "<ul>";
        echo "<li><code>/var/log/apache2/error.log</code> (Apache)</li>";
        echo "<li><code>/var/log/nginx/error.log</code> (Nginx)</li>";
        echo "<li><code>" . FCPATH . "application/logs/</code> (CodeIgniter)</li>";
        echo "</ul>";
        echo "<p class='info'>💡 Astuce: Regardez la dernière ligne de ces fichiers pour voir l'erreur exacte</p>";
        echo "</div>";
    }

    /**
     * Debug POST request
     */
    private function debug_post_request()
    {
        echo "<div class='section'>";
        echo "<h2>🧪 Test de Création en Cours...</h2>";

        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo "<h3>Données POST Reçues:</h3>";
        echo "<pre>";
        print_r($this->input->post());
        echo "</pre>";

        // Prepare test data
        $patients = $this->dietetic_patients_model->get();
        $this->load->model('staff_model');
        $staff = $this->staff_model->get();

        if (empty($patients) || empty($staff)) {
            echo "<p class='error'>✗ Impossible de tester: patients ou staff manquants</p>";
            echo "</div>";
            return;
        }

        $data = [
            'patient_id' => $patients[0]->id,
            'dietitian_id' => $staff[0]['staffid'],
            'consultation_date' => date('Y-m-d H:i:s'),
            'consultation_type' => 'follow_up',
            'status' => 'scheduled',
            'duration' => 60,
            'consultation_mode' => 'in_person',
            'location' => 'Cabinet Test',
            'reason' => 'Test diagnostic',
            'notes' => 'Notes de test',
            'observations' => 'Observations de test',
            'recommendations' => 'Recommandations de test'
        ];

        echo "<h3>Données de Test Préparées:</h3>";
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        try {
            echo "<h3>Tentative d'Insertion...</h3>";

            // Try to insert
            $this->db->insert(db_prefix() . 'dietic_consultations', $data);

            if ($this->db->affected_rows() > 0) {
                $insert_id = $this->db->insert_id();
                echo "<p class='success'>✅ SUCCÈS! Consultation créée avec ID: {$insert_id}</p>";

                // Retrieve it
                $created = $this->db->get_where(db_prefix() . 'dietic_consultations', ['id' => $insert_id])->row();
                echo "<h3>Consultation Créée:</h3>";
                echo "<pre>";
                print_r($created);
                echo "</pre>";

                // Clean up test data
                $this->db->delete(db_prefix() . 'dietic_consultations', ['id' => $insert_id]);
                echo "<p class='info'>✓ Données de test supprimées</p>";

                echo "<p class='success' style='font-size: 18px; padding: 20px; background: #0d3320; border-radius: 8px;'>";
                echo "🎉 Aucune erreur détectée! Le formulaire devrait fonctionner correctement.<br>";
                echo "Si vous avez toujours l'erreur 500, vérifiez:<br>";
                echo "1. Les logs PHP du serveur<br>";
                echo "2. Les permissions de fichiers<br>";
                echo "3. La configuration PHP (memory_limit, max_input_vars)";
                echo "</p>";

            } else {
                echo "<p class='error'>✗ Échec de l'insertion</p>";
                $error = $this->db->error();
                echo "<pre>";
                print_r($error);
                echo "</pre>";
            }

        } catch (Exception $e) {
            echo "<p class='error'>✗ EXCEPTION CAPTURÉE:</p>";
            echo "<pre class='error'>";
            echo "Message: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Trace:\n" . $e->getTraceAsString();
            echo "</pre>";
        }

        echo "</div>";

        // Show database last error
        echo "<div class='section'>";
        echo "<h3>Dernière Erreur BDD:</h3>";
        $db_error = $this->db->error();
        if (!empty($db_error['message'])) {
            echo "<pre class='error'>";
            print_r($db_error);
            echo "</pre>";
        } else {
            echo "<p class='success'>Aucune erreur BDD</p>";
        }
        echo "</div>";
    }
}
