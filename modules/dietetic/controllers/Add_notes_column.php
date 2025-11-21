<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration script to add 'notes' column to dietic_recipe_assignments table
 *
 * URL: /dietetic/add_notes_column
 */
class Add_notes_column extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo '<!DOCTYPE html><html><head><title>Add Notes Column</title>';
        echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:15px;border-radius:4px;}</style>';
        echo '</head><body>';

        echo '<h1>🔧 Ajout de la colonne notes</h1>';

        $table_name = db_prefix() . 'dietic_recipe_assignments';

        // Check if table exists
        if (!$this->db->table_exists($table_name)) {
            echo '<p class="error">❌ La table ' . $table_name . ' n\'existe pas!</p>';
            echo '</body></html>';
            return;
        }

        echo '<p class="success">✅ Table trouvée: ' . $table_name . '</p>';

        // Check if column already exists
        $fields = $this->db->field_data($table_name);
        $column_exists = false;

        foreach ($fields as $field) {
            if ($field->name === 'notes') {
                $column_exists = true;
                break;
            }
        }

        if ($column_exists) {
            echo '<p class="error">⚠️ La colonne "notes" existe déjà dans la table!</p>';
            echo '</body></html>';
            return;
        }

        echo '<p>➡️ La colonne "notes" n\'existe pas encore. Ajout en cours...</p>';

        // Add the column
        $sql = "ALTER TABLE `{$table_name}`
                ADD COLUMN `notes` TEXT NULL
                COMMENT 'Notes du diététicien pour le patient concernant cette recette'
                AFTER `assigned_by_dietitian_id`";

        try {
            $this->db->query($sql);
            echo '<p class="success">✅ Colonne "notes" ajoutée avec succès!</p>';

            echo '<h2>Vérification:</h2>';
            $fields = $this->db->field_data($table_name);
            echo '<pre>';
            foreach ($fields as $field) {
                echo $field->name . ' (' . $field->type . ')<br>';
            }
            echo '</pre>';

            echo '<hr>';
            echo '<p><strong>✅ Migration terminée avec succès!</strong></p>';
            echo '<p><a href="' . site_url('dietetic/portal/recipes') . '">Tester la page recipes</a></p>';

        } catch (Exception $e) {
            echo '<p class="error">❌ ERREUR lors de l\'ajout de la colonne:</p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';

            $error = $this->db->error();
            if (!empty($error['message'])) {
                echo '<p class="error"><strong>Erreur SQL:</strong></p>';
                echo '<pre>' . htmlspecialchars(print_r($error, true)) . '</pre>';
            }
        }

        echo '</body></html>';
    }
}
