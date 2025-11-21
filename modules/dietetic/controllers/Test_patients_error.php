<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Patients Error - Debug 500 error
 */
class Test_patients_error extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<h1>Test Patients Error 500 - Diagnostic</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} .warning{color:orange;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:4px;}</style>';

        $patients = $this->dietetic_patients_model->get_all();

        echo '<h2>Test 1: Fonctions de traduction</h2>';
        try {
            $test1 = _l('dietetic_patients');
            echo '<p class="success">✅ _l() fonctionne: ' . htmlspecialchars($test1) . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ _l() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Test 2: Fonction dietetic_has_permission()</h2>';
        try {
            $test2 = dietetic_has_permission('view');
            echo '<p class="success">✅ dietetic_has_permission() fonctionne: ' . ($test2 ? 'true' : 'false') . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ dietetic_has_permission() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Test 3: Fonction dietetic_calculate_bmi()</h2>';
        try {
            if (function_exists('dietetic_calculate_bmi')) {
                $test3 = dietetic_calculate_bmi(70, 170);
                echo '<p class="success">✅ dietetic_calculate_bmi() fonctionne: ' . $test3 . '</p>';
            } else {
                echo '<p class="error">❌ dietetic_calculate_bmi() n\'existe pas!</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ dietetic_calculate_bmi() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Test 4: Fonction _dt()</h2>';
        try {
            $test4 = _dt(date('Y-m-d H:i:s'));
            echo '<p class="success">✅ _dt() fonctionne: ' . htmlspecialchars($test4) . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ _dt() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Test 5: Tester une boucle complète comme dans la vue</h2>';
        echo '<p>Nombre de patients: ' . count($patients) . '</p>';

        try {
            foreach ($patients as $patient) {
                echo '<div style="padding:10px; border:1px solid #ccc; margin:10px 0;">';
                echo '<strong>Patient ID ' . $patient->id . '</strong><br>';
                echo 'Nom: ' . htmlspecialchars($patient->client_name) . '<br>';
                echo 'Diététicien: ' . htmlspecialchars($patient->dietitian_name) . '<br>';
                echo 'Status: ' . htmlspecialchars($patient->status) . '<br>';

                // Test BMI calculation
                if ($patient->initial_weight && $patient->height) {
                    if (function_exists('dietetic_calculate_bmi')) {
                        $bmi = dietetic_calculate_bmi($patient->initial_weight, $patient->height);
                        echo 'BMI: ' . $bmi . '<br>';
                    } else {
                        echo '<span class="error">BMI: fonction manquante</span><br>';
                    }
                }

                // Test date formatting
                echo 'Créé le: ' . _dt($patient->created_at) . '<br>';

                echo '</div>';
            }
            echo '<p class="success">✅ Boucle complète réussie</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur dans la boucle: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<h2>Test 6: Charger la vraie vue avec capture d'erreur</h2>';
        echo '<p>Tentative de chargement de la vue list.php...</p>';

        try {
            ob_start();
            $data['title'] = _l('dietetic_patients');
            $data['patients'] = $patients;
            $this->load->view('admin/patients/list', $data);
            $output = ob_get_clean();

            echo '<p class="success">✅ Vue chargée sans erreur PHP!</p>';
            echo '<p class="warning">⚠️ Si vous voyez ce message, l\'erreur 500 est probablement causée par un problème de chargement de ressources (CSS/JS) ou de configuration serveur, pas par le code PHP.</p>';

            // Afficher les 100 premiers caractères de la sortie
            echo '<h3>Aperçu de la sortie (100 premiers caractères):</h3>';
            echo '<pre>' . htmlspecialchars(substr($output, 0, 100)) . '...</pre>';

        } catch (Exception $e) {
            ob_end_clean();
            echo '<p class="error">❌ ERREUR lors du chargement de la vue!</p>';
            echo '<p>Message: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>Fichier: ' . htmlspecialchars($e->getFile()) . '</p>';
            echo '<p>Ligne: ' . $e->getLine() . '</p>';
            echo '<h3>Stack Trace:</h3>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
    }
}
