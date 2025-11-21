<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Photo Upload - Debug
 */
class Test_photo_upload extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<h1>Test Photo Upload - Debug</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e74c3c;color:white;} .success{color:green;font-weight:bold;} .error{color:red;font-weight:bold;}</style>';

        // Test 1: Check if table exists
        echo '<h2>Test 1: Vérifier si la table existe</h2>';
        $table_name = db_prefix() . 'dietic_recipe_photos';

        if ($this->db->table_exists($table_name)) {
            echo '<p class="success">✅ Table existe: ' . $table_name . '</p>';

            // Get table structure
            echo '<h2>Test 2: Structure de la table</h2>';
            $fields = $this->db->field_data($table_name);

            echo '<table>';
            echo '<thead><tr><th>Champ</th><th>Type</th><th>Max Length</th><th>Null</th><th>Default</th></tr></thead><tbody>';
            foreach ($fields as $field) {
                echo '<tr>';
                echo '<td>' . $field->name . '</td>';
                echo '<td>' . $field->type . '</td>';
                echo '<td>' . (isset($field->max_length) ? $field->max_length : 'N/A') . '</td>';
                echo '<td>' . ($field->null ? 'YES' : 'NO') . '</td>';
                echo '<td>' . (isset($field->default) ? $field->default : 'NULL') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            // Test 3: Check specific columns
            echo '<h2>Test 3: Vérifier les colonnes nécessaires</h2>';
            $required_columns = ['id', 'recipe_id', 'photo_url', 'uploaded_at', 'is_main', 'display_order'];
            echo '<table>';
            echo '<thead><tr><th>Colonne</th><th>Existe?</th></tr></thead><tbody>';
            foreach ($required_columns as $col) {
                $exists = $this->db->field_exists($col, $table_name);
                echo '<tr>';
                echo '<td>' . $col . '</td>';
                echo '<td style="color:' . ($exists ? 'green' : 'red') . ';">' . ($exists ? '✅ OUI' : '❌ NON') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            // Test 4: Check upload directory
            echo '<h2>Test 4: Vérifier le dossier d\'upload</h2>';
            $upload_path = FCPATH . 'uploads/dietetic/recipes/';
            echo '<p>Chemin: <code>' . $upload_path . '</code></p>';

            if (is_dir($upload_path)) {
                echo '<p class="success">✅ Dossier existe</p>';
                if (is_writable($upload_path)) {
                    echo '<p class="success">✅ Dossier accessible en écriture</p>';
                } else {
                    echo '<p class="error">❌ Dossier NON accessible en écriture</p>';
                    echo '<p>Permissions actuelles: ' . substr(sprintf('%o', fileperms($upload_path)), -4) . '</p>';
                }
            } else {
                echo '<p class="error">❌ Dossier n\'existe pas</p>';
                echo '<p>Tentative de création...</p>';
                if (mkdir($upload_path, 0755, true)) {
                    echo '<p class="success">✅ Dossier créé avec succès</p>';
                } else {
                    echo '<p class="error">❌ Impossible de créer le dossier</p>';
                }
            }

            // Test 5: Show sample data
            echo '<h2>Test 5: Exemple de données photos (si présentes)</h2>';
            $query = $this->db->get($table_name, 5);
            if ($query->num_rows() > 0) {
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
                echo '<p><em>Aucune donnée dans la table</em></p>';
            }

        } else {
            echo '<p class="error">❌ Table n\'existe PAS: ' . $table_name . '</p>';
        }
    }
}
