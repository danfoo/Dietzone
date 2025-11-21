<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_assign extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recipes_model');
        $this->load->helper('dietetic/dietetic');
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Test Assignation</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:4px;overflow:auto;} table{border-collapse:collapse;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;} th{background:#f0f0f0;}</style>';
        echo '</head><body>';

        echo '<h1>🧪 Test Assignation Recette → Patient</h1>';

        echo '<h2>Test 1: Permissions</h2>';
        echo '<table>';
        echo '<tr><th>Permission</th><th>Status</th></tr>';
        
        $has_edit = function_exists('dietetic_has_permission') ? dietetic_has_permission('edit') : false;
        echo '<tr><td>dietetic_has_permission(\'edit\')</td><td>' . ($has_edit ? '<span class="success">✅ TRUE</span>' : '<span class="error">❌ FALSE - BLOQUÉ!</span>') . '</td></tr>';
        echo '</table>';

        if (!$has_edit) {
            echo '<p class="error">⚠️ PROBLÈME: Pas de permission EDIT! L\'assignation sera bloquée.</p>';
        }

        echo '<h2>Test 2: Vérifier la méthode assign_to_patient()</h2>';
        
        if (method_exists($this->dietetic_recipes_model, 'assign_to_patient')) {
            echo '<p class="success">✅ Méthode assign_to_patient() existe</p>';
        } else {
            echo '<p class="error">❌ Méthode assign_to_patient() N\'EXISTE PAS!</p>';
            echo '</body></html>';
            return;
        }

        echo '<h2>Test 3: Test réel d\'assignation</h2>';
        echo '<p>Tentative d\'assigner la recette #7 (Yassa) au patient #1 (Eric)</p>';

        try {
            $recipe_id = 7;
            $patient_id = 1;
            $notes = 'Test d\'assignation automatique - ' . date('Y-m-d H:i:s');

            echo '<p><strong>Paramètres:</strong></p>';
            echo '<ul>';
            echo '<li>Recipe ID: ' . $recipe_id . '</li>';
            echo '<li>Patient ID: ' . $patient_id . '</li>';
            echo '<li>Notes: ' . htmlspecialchars($notes) . '</li>';
            echo '</ul>';

            $assignment_id = $this->dietetic_recipes_model->assign_to_patient($recipe_id, $patient_id, null, $notes);

            if ($assignment_id) {
                echo '<p class="success">✅ SUCCÈS! Assignment ID: ' . $assignment_id . '</p>';
                
                // Vérifier dans la base
                $this->db->where('id', $assignment_id);
                $assignment = $this->db->get(db_prefix() . 'dietic_recipe_assignments')->row();
                
                if ($assignment) {
                    echo '<p class="success">✅ Vérification en base: Assignation trouvée</p>';
                    echo '<pre>' . print_r($assignment, true) . '</pre>';
                } else {
                    echo '<p class="error">❌ Assignation introuvable en base!</p>';
                }
            } else {
                echo '<p class="error">❌ ÉCHEC! assign_to_patient() a retourné FALSE</p>';
                
                // Afficher la dernière erreur SQL
                echo '<p><strong>Dernière erreur SQL:</strong></p>';
                $error = $this->db->error();
                echo '<pre>' . print_r($error, true) . '</pre>';
            }

        } catch (Exception $e) {
            echo '<p class="error">❌ EXCEPTION: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }

        echo '<h2>Test 4: Structure de la table</h2>';
        
        $table = db_prefix() . 'dietic_recipe_assignments';
        
        if ($this->db->table_exists($table)) {
            echo '<p class="success">✅ Table ' . $table . ' existe</p>';
            
            $fields = $this->db->field_data($table);
            echo '<table>';
            echo '<tr><th>Colonne</th><th>Type</th><th>Null</th></tr>';
            foreach ($fields as $field) {
                echo '<tr>';
                echo '<td>' . $field->name . '</td>';
                echo '<td>' . $field->type . '</td>';
                echo '<td>' . ($field->null ? 'YES' : 'NO') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="error">❌ Table ' . $table . ' N\'EXISTE PAS!</p>';
        }

        echo '</body></html>';
    }
}
