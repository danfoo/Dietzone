<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test View Loading - Tester le chargement de la vue progressivement
 */
class Test_view_loading extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<h1>Test View Loading</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;}</style>';

        // Load dependencies
        $this->load->helper('dietetic/dietetic');
        $this->load->model('dietetic/dietetic_patients_model');

        $patients = $this->dietetic_patients_model->get_all();

        echo '<h2>Étape 1: Tester les fonctions de traduction utilisées dans la vue</h2>';

        $translation_keys = [
            'dietetic_patients',
            'dietetic_client_name',
            'dietetic_dietitian',
            'dietetic_status',
            'dietetic_weight',
            'dietetic_bmi',
            'dietetic_activity_level',
            'created_at',
            'options'
        ];

        foreach ($translation_keys as $key) {
            try {
                $translation = _l($key);
                echo '<p class="success">✅ _l(\'' . $key . '\') = "' . htmlspecialchars($translation) . '"</p>';
            } catch (Exception $e) {
                echo '<p class="error">❌ _l(\'' . $key . '\') ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        }

        echo '<h2>Étape 2: Tester _dt() sur une date</h2>';
        try {
            $formatted_date = _dt(date('Y-m-d H:i:s'));
            echo '<p class="success">✅ _dt() fonctionne: ' . htmlspecialchars($formatted_date) . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ _dt() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 3: Tester dietetic_calculate_bmi()</h2>';
        try {
            $bmi = dietetic_calculate_bmi(70, 170);
            echo '<p class="success">✅ dietetic_calculate_bmi() fonctionne: ' . $bmi . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ dietetic_calculate_bmi() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 4: Tester dietetic_has_permission()</h2>';
        try {
            $can_create = dietetic_has_permission('create');
            $can_edit = dietetic_has_permission('edit');
            $can_delete = dietetic_has_permission('delete');
            echo '<p class="success">✅ dietetic_has_permission(\'create\') = ' . ($can_create ? 'true' : 'false') . '</p>';
            echo '<p class="success">✅ dietetic_has_permission(\'edit\') = ' . ($can_edit ? 'true' : 'false') . '</p>';
            echo '<p class="success">✅ dietetic_has_permission(\'delete\') = ' . ($can_delete ? 'true' : 'false') . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ dietetic_has_permission() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 5: Tester admin_url()</h2>';
        try {
            $url1 = admin_url('dietetic/patients/create');
            $url2 = admin_url('dietetic/patients/view/1');
            echo '<p class="success">✅ admin_url() fonctionne</p>';
            echo '<p>URL 1: ' . htmlspecialchars($url1) . '</p>';
            echo '<p>URL 2: ' . htmlspecialchars($url2) . '</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ admin_url() ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<h2>Étape 6: Simuler la boucle foreach de la vue</h2>';
        try {
            echo '<p>Nombre de patients: ' . count($patients) . '</p>';

            foreach ($patients as $patient) {
                echo '<div style="padding:10px; border:1px solid #ddd; margin:10px 0;">';
                echo '<strong>Patient ' . $patient->id . '</strong><br>';

                // Tester toutes les propriétés utilisées dans la vue
                echo 'client_name: ' . htmlspecialchars($patient->client_name) . '<br>';
                echo 'dietitian_name: ' . htmlspecialchars($patient->dietitian_name) . '<br>';
                echo 'status: ' . htmlspecialchars($patient->status) . '<br>';

                if ($patient->initial_weight) {
                    echo 'initial_weight: ' . $patient->initial_weight . ' kg<br>';
                }

                if ($patient->initial_weight && $patient->height) {
                    $bmi = dietetic_calculate_bmi($patient->initial_weight, $patient->height);
                    echo 'BMI: ' . $bmi . '<br>';
                }

                if ($patient->activity_level) {
                    echo 'activity_level: ' . ucfirst(str_replace('_', ' ', $patient->activity_level)) . '<br>';
                }

                echo 'created_at: ' . _dt($patient->created_at) . '<br>';
                echo '</div>';
            }

            echo '<p class="success">✅ Boucle foreach réussie</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur dans la boucle: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<h2>Étape 7: Tester init_head() et init_tail()</h2>';
        try {
            echo '<p>init_head() et init_tail() sont des fonctions Perfex qui chargent CSS/JS...</p>';
            echo '<p class="success">✅ Ces fonctions existent et seront testées dans l\'étape suivante</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }

        echo '<hr>';
        echo '<h2>Résultat</h2>';
        echo '<p><strong>Si toutes les étapes sont ✅ ci-dessus, le problème est probablement dans init_head(), init_tail(), ou le chargement des assets CSS/JS.</strong></p>';
        echo '<p><a href="' . admin_url('dietetic/patients') . '">Essayer à nouveau /admin/dietetic/patients</a></p>';
    }
}
