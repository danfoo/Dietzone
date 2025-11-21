<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Patients Display - Debug without DataTables
 */
class Test_patients_display extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_patients_model');
    }

    public function index()
    {
        $patients = $this->dietetic_patients_model->get_all();

        echo '<h1>Test Patients Display - Sans DataTables</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e74c3c;color:white;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;}</style>';

        echo '<p>Nombre de patients: <strong>' . count($patients) . '</strong></p>';

        if (count($patients) > 0) {
            echo '<table>';
            echo '<thead><tr>';
            echo '<th>ID</th>';
            echo '<th>Client Name</th>';
            echo '<th>Dietitian Name</th>';
            echo '<th>Status</th>';
            echo '<th>Email</th>';
            echo '<th>Weight</th>';
            echo '<th>Created At</th>';
            echo '</tr></thead><tbody>';

            foreach ($patients as $patient) {
                echo '<tr>';
                echo '<td>' . $patient->id . '</td>';
                echo '<td>' . htmlspecialchars($patient->client_name) . '</td>';
                echo '<td>' . htmlspecialchars($patient->dietitian_name) . '</td>';
                echo '<td>' . $patient->status . '</td>';
                echo '<td>' . htmlspecialchars($patient->email) . '</td>';
                echo '<td>' . ($patient->initial_weight ? $patient->initial_weight . ' kg' : '-') . '</td>';
                echo '<td>' . $patient->created_at . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';

            echo '<h2>Test: Affichage HTML brut (comme dans la vraie page)</h2>';
            echo '<div>';
            foreach ($patients as $patient) {
                echo '<div style="padding:10px; border:1px solid #ccc; margin:10px 0; background:#f9f9f9;">';
                echo '<strong>Patient ' . $patient->id . ':</strong> ';
                echo htmlspecialchars($patient->client_name);
                echo ' - ' . htmlspecialchars($patient->dietitian_name);
                echo ' (' . $patient->status . ')';
                echo '</div>';
            }
            echo '</div>';

        } else {
            echo '<p class="error">❌ Aucun patient trouvé</p>';
        }

        echo '<hr>';
        echo '<h2>Actions suggérées:</h2>';
        echo '<ol>';
        echo '<li>Si vous voyez les patients ci-dessus mais pas sur /admin/dietetic/patients, le problème est DataTables</li>';
        echo '<li>Vérifiez la console JavaScript sur /admin/dietetic/patients (F12 → Console)</li>';
        echo '<li>Cherchez des erreurs JavaScript comme "DataTables not loaded"</li>';
        echo '</ol>';

        echo '<p><a href="' . admin_url('dietetic/patients') . '" class="btn btn-primary">← Retour à la liste des patients</a></p>';
    }
}
