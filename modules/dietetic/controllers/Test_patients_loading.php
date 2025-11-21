<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Patients Loading - Debug
 */
class Test_patients_loading extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_patients_model');
    }

    public function index()
    {
        echo '<h1>Test Patients Loading - Debug</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e74c3c;color:white;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;} pre{background:#f5f5f5;padding:10px;border-radius:4px;}</style>';

        // Test 1: Check if table exists
        echo '<h2>Test 1: Vérifier si la table patients existe</h2>';
        $table_name = db_prefix() . 'dietic_patients';

        if ($this->db->table_exists($table_name)) {
            echo '<p class="success">✅ Table existe: ' . $table_name . '</p>';

            // Get table structure
            echo '<h2>Test 2: Structure de la table</h2>';
            $fields = $this->db->field_data($table_name);

            echo '<table>';
            echo '<thead><tr><th>Champ</th><th>Type</th><th>Null</th></tr></thead><tbody>';
            foreach ($fields as $field) {
                echo '<tr>';
                echo '<td>' . $field->name . '</td>';
                echo '<td>' . $field->type . '</td>';
                echo '<td>' . ($field->null ? 'YES' : 'NO') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            // Test 3: Count total records
            echo '<h2>Test 3: Nombre total de patients dans la table</h2>';
            $total = $this->db->count_all($table_name);
            echo '<p>Total de lignes dans la table: <strong>' . $total . '</strong></p>';

            if ($total > 0) {
                echo '<p class="success">✅ Il y a des données dans la table</p>';

                // Show sample data
                echo '<h2>Test 4: Exemple de données (5 premières lignes)</h2>';
                $query = $this->db->get($table_name, 5);
                $rows = $query->result_array();

                echo '<table>';
                echo '<thead><tr>';
                foreach (array_keys($rows[0]) as $key) {
                    echo '<th>' . $key . '</th>';
                }
                echo '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td>' . htmlspecialchars($value ?? 'NULL') . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p class="error">❌ La table est vide</p>';
            }

            // Test 5: Check what get_all() returns
            echo '<h2>Test 5: Tester dietetic_patients_model->get_all()</h2>';

            try {
                $patients = $this->dietetic_patients_model->get_all();
                echo '<p>Nombre de patients retournés par get_all(): <strong>' . count($patients) . '</strong></p>';

                if (count($patients) > 0) {
                    echo '<p class="success">✅ get_all() retourne des données</p>';
                    echo '<h3>Détails des patients:</h3>';
                    echo '<table>';
                    echo '<thead><tr>';
                    echo '<th>ID</th><th>Client Name</th><th>Email</th><th>Status</th>';
                    echo '</tr></thead><tbody>';

                    foreach (array_slice($patients, 0, 10) as $patient) {
                        echo '<tr>';
                        echo '<td>' . (isset($patient->id) ? $patient->id : 'N/A') . '</td>';
                        echo '<td>' . (isset($patient->client_name) ? htmlspecialchars($patient->client_name) : 'N/A') . '</td>';
                        echo '<td>' . (isset($patient->email) ? htmlspecialchars($patient->email) : 'N/A') . '</td>';
                        echo '<td>' . (isset($patient->status) ? $patient->status : 'N/A') . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';

                    if (count($patients) > 10) {
                        echo '<p><em>... et ' . (count($patients) - 10) . ' autres patients</em></p>';
                    }

                    echo '<h3>Structure du premier patient:</h3>';
                    echo '<pre>' . print_r($patients[0], true) . '</pre>';
                } else {
                    echo '<p class="error">❌ get_all() retourne un tableau vide</p>';

                    // Show the SQL query being executed
                    echo '<h3>Debug: Dernière requête SQL</h3>';
                    echo '<pre>' . $this->db->last_query() . '</pre>';
                }

            } catch (Exception $e) {
                echo '<p class="error">❌ ERREUR: ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }

            // Test 6: Check if clients table exists (Perfex CRM standard)
            echo '<h2>Test 6: Vérifier la table clients de Perfex</h2>';
            $clients_table = db_prefix() . 'clients';

            if ($this->db->table_exists($clients_table)) {
                echo '<p class="success">✅ Table clients existe: ' . $clients_table . '</p>';
                $total_clients = $this->db->count_all($clients_table);
                echo '<p>Total de clients dans Perfex: <strong>' . $total_clients . '</strong></p>';
            } else {
                echo '<p class="error">❌ Table clients n\'existe pas</p>';
            }

            // Test 7: Check current user
            echo '<h2>Test 7: Utilisateur actuel</h2>';
            echo '<p>User ID: <strong>' . get_staff_user_id() . '</strong></p>';
            echo '<p>Is Admin: <strong>' . (is_admin() ? 'OUI' : 'NON') . '</strong></p>';

        } else {
            echo '<p class="error">❌ Table n\'existe PAS: ' . $table_name . '</p>';
        }
    }
}
