<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Nutrition Table Structure
 */
class Test_nutrition_table extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<h1>Test Nutrition Table Structure</h1>';
        echo '<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e74c3c;color:white;}</style>';

        // Test 1: Check if table exists
        echo '<h2>Test 1: Vérifier si la table existe</h2>';
        $table_name = db_prefix() . 'dietic_recipe_nutrition';

        if ($this->db->table_exists($table_name)) {
            echo '<p style="color:green;">✅ Table existe: ' . $table_name . '</p>';

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
            $required_columns = ['calories', 'protein', 'carbs', 'fat', 'fiber', 'sodium', 'sugar'];
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

            // Test 4: Show sample data
            echo '<h2>Test 4: Exemple de données (si présentes)</h2>';
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
            echo '<p style="color:red;">❌ Table n\'existe PAS: ' . $table_name . '</p>';
        }
    }
}
