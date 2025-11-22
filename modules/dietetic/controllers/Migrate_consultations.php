<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration Controller for Consultations Communication Channels
 * URL: /admin/dietetic/migrate_consultations
 */
class Migrate_consultations extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Check and apply migration for communication channels
     */
    public function index()
    {
        // Only allow admin access
        if (!is_admin()) {
            access_denied('Migration');
        }

        $table_name = db_prefix() . 'dietic_consultations';

        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<title>Migration - Canaux de Communication</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
            h1 { color: #333; }
            .success { color: green; font-weight: bold; }
            .error { color: red; font-weight: bold; }
            .info { color: blue; }
            pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background: #4CAF50; color: white; }
            .highlight { background: #d4edda; }
            .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            .btn:hover { background: #45a049; }
        </style>";
        echo "</head><body>";

        echo "<h1>🔄 Migration: Canaux de Communication pour Consultations</h1>";
        echo "<hr>";

        // Check existing columns
        $query = $this->db->query("SHOW COLUMNS FROM {$table_name}");
        $columns = $query->result();

        $existing_columns = [];
        foreach ($columns as $column) {
            $existing_columns[] = $column->Field;
        }

        $required_columns = [
            'consultation_mode' => [
                'type' => "VARCHAR(20) DEFAULT 'in_person'",
                'comment' => "in_person or online",
                'after' => 'consultation_type'
            ],
            'online_platform' => [
                'type' => "VARCHAR(50) DEFAULT NULL",
                'comment' => "zoom, google_meet, teams, whatsapp, skype, other",
                'after' => 'consultation_mode'
            ],
            'meeting_link' => [
                'type' => "VARCHAR(500) DEFAULT NULL",
                'comment' => "URL for online meetings",
                'after' => 'online_platform'
            ]
        ];

        $missing_columns = [];
        foreach ($required_columns as $col => $details) {
            if (!in_array($col, $existing_columns)) {
                $missing_columns[$col] = $details;
            }
        }

        if (empty($missing_columns)) {
            echo "<div class='success'>";
            echo "<h2>✓ Migration Déjà Appliquée</h2>";
            echo "<p>Toutes les colonnes requises existent déjà dans la table.</p>";
            echo "</div>";
        } else {
            echo "<div class='info'>";
            echo "<h2>📋 Colonnes Manquantes Détectées</h2>";
            echo "<p>Les colonnes suivantes vont être ajoutées:</p>";
            echo "<ul>";
            foreach ($missing_columns as $col => $details) {
                echo "<li><strong>{$col}</strong>: {$details['type']}</li>";
            }
            echo "</ul>";
            echo "</div>";

            // Apply migration
            echo "<h2>🚀 Application de la Migration...</h2>";

            $success = true;
            $errors = [];

            foreach ($missing_columns as $col => $details) {
                try {
                    $sql = "ALTER TABLE `{$table_name}`
                            ADD COLUMN `{$col}` {$details['type']}
                            COMMENT '{$details['comment']}'
                            AFTER `{$details['after']}`";

                    echo "<p>Ajout de la colonne <strong>{$col}</strong>...</p>";

                    if ($this->db->query($sql)) {
                        echo "<p class='success'>✓ Colonne {$col} ajoutée avec succès</p>";
                    } else {
                        $success = false;
                        $error = $this->db->error();
                        $errors[] = "Erreur pour {$col}: " . $error['message'];
                        echo "<p class='error'>✗ Échec pour {$col}: {$error['message']}</p>";
                    }
                } catch (Exception $e) {
                    $success = false;
                    $errors[] = "Exception pour {$col}: " . $e->getMessage();
                    echo "<p class='error'>✗ Exception pour {$col}: {$e->getMessage()}</p>";
                }
            }

            // Add indexes
            echo "<h3>Ajout des Index...</h3>";

            $indexes_to_add = [];
            if (isset($missing_columns['consultation_mode'])) {
                $indexes_to_add[] = 'consultation_mode';
            }
            if (isset($missing_columns['online_platform'])) {
                $indexes_to_add[] = 'online_platform';
            }

            foreach ($indexes_to_add as $index_col) {
                try {
                    // Check if index already exists
                    $index_query = $this->db->query("SHOW INDEX FROM {$table_name} WHERE Key_name = '{$index_col}'");
                    if ($index_query->num_rows() == 0) {
                        $sql = "ALTER TABLE `{$table_name}` ADD INDEX `{$index_col}` (`{$index_col}`)";

                        if ($this->db->query($sql)) {
                            echo "<p class='success'>✓ Index {$index_col} ajouté</p>";
                        }
                    } else {
                        echo "<p class='info'>ℹ Index {$index_col} existe déjà</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>⚠ Avertissement pour index {$index_col}: {$e->getMessage()}</p>";
                }
            }

            if ($success) {
                echo "<div class='success'>";
                echo "<h2>✅ Migration Terminée avec Succès!</h2>";
                echo "<p>Toutes les colonnes ont été ajoutées correctement.</p>";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "<h2>❌ Migration Échouée</h2>";
                echo "<p>Certaines erreurs se sont produites:</p>";
                echo "<ul>";
                foreach ($errors as $error) {
                    echo "<li>{$error}</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
        }

        // Show final table structure
        echo "<h2>📊 Structure Actuelle de la Table</h2>";
        $query = $this->db->query("SHOW COLUMNS FROM {$table_name}");
        $columns = $query->result();

        echo "<table>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Défaut</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            $highlight = in_array($column->Field, array_keys($required_columns)) ? "class='highlight'" : "";
            echo "<tr {$highlight}>";
            echo "<td><strong>{$column->Field}</strong></td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
            echo "<td>{$column->Extra}</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<p><a href='" . admin_url('dietetic/consultations/create') . "' class='btn'>🏥 Créer une Consultation</a></p>";
        echo "<p><a href='" . admin_url('dietetic/consultations') . "' class='btn'>📋 Liste des Consultations</a></p>";

        echo "</body></html>";
    }
}
