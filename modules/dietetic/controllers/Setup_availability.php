<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Setup Availability System
 * Simple controller to run the availability system migration
 */
class Setup_availability extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied('Setup Availability');
        }
    }

    /**
     * Run the migration
     */
    public function index()
    {
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Availability System</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #01807B; border-bottom: 3px solid #01807B; padding-bottom: 10px; }
        h3 { color: #2c3e50; margin-top: 25px; }
        .success { color: green; padding: 8px; margin: 5px 0; }
        .error { color: red; padding: 8px; margin: 5px 0; background: #ffebee; border-left: 4px solid red; }
        .warning { color: orange; padding: 8px; margin: 5px 0; background: #fff3e0; border-left: 4px solid orange; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #026660; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        hr { margin: 30px 0; border: none; border-top: 2px solid #e0e0e0; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>🚀 Installation du Système de Disponibilités</h2>";

        // Load and execute the SQL file
        $sql_file = DIETETIC_MODULE_PATH . 'install/availability_system.sql';

        if (!file_exists($sql_file)) {
            echo "<p class='error'>❌ Erreur: Fichier SQL introuvable à " . htmlspecialchars($sql_file) . "</p>";
            echo "<p><a href='" . admin_url('dietetic') . "' class='btn'>← Retour</a></p>";
            echo "</div></body></html>";
            return;
        }

        $sql_content = file_get_contents($sql_file);

        // Replace table prefix placeholders
        $sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql_content)));

        $success_count = 0;
        $error_count = 0;
        $skip_count = 0;

        echo "<p>📦 Exécution de <strong>" . count($statements) . "</strong> requêtes SQL...</p>";
        echo "<hr>";

        foreach ($statements as $index => $statement) {
            if (!empty($statement)) {
                try {
                    $this->db->query($statement);
                    $success_count++;

                    // Determine what was executed
                    $short_statement = substr($statement, 0, 80);
                    if (stripos($statement, 'CREATE TABLE') !== false) {
                        preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches);
                        $table_name = $matches[1] ?? 'unknown';
                        echo "<p class='success'>✓ Table créée: <code>{$table_name}</code></p>";
                    } elseif (stripos($statement, 'INSERT INTO') !== false) {
                        preg_match('/INSERT INTO.*?`([^`]+)`/i', $statement, $matches);
                        $table_name = $matches[1] ?? 'unknown';
                        echo "<p class='success'>✓ Données insérées dans: <code>{$table_name}</code></p>";
                    } elseif (stripos($statement, 'ALTER TABLE') !== false) {
                        preg_match('/ALTER TABLE.*?`([^`]+)`/i', $statement, $matches);
                        $table_name = $matches[1] ?? 'unknown';
                        echo "<p class='success'>✓ Table modifiée: <code>{$table_name}</code></p>";
                    } else {
                        echo "<p class='success'>✓ Requête exécutée: " . htmlspecialchars($short_statement) . "...</p>";
                    }

                } catch (Exception $e) {
                    $error_msg = $e->getMessage();

                    // Check if error is acceptable (table/column already exists)
                    if (
                        stripos($error_msg, 'already exists') !== false ||
                        stripos($error_msg, 'Duplicate column') !== false ||
                        stripos($error_msg, 'Duplicate key') !== false ||
                        stripos($error_msg, 'Duplicate entry') !== false
                    ) {
                        $skip_count++;
                        $short_statement = substr($statement, 0, 60);
                        echo "<p class='warning'>⚠ Ignoré (existe déjà): " . htmlspecialchars($short_statement) . "...</p>";
                    } else {
                        // Real error
                        $error_count++;
                        echo "<p class='error'>✗ Erreur: " . htmlspecialchars($error_msg) . "</p>";
                        echo "<pre>" . htmlspecialchars(substr($statement, 0, 300)) . "...</pre>";
                    }

                    log_activity('Availability migration: ' . substr($error_msg, 0, 200));
                }
            }
        }

        echo "<hr>";
        echo "<h3>📊 Résumé de l'installation</h3>";
        echo "<p>✓ Requêtes réussies: <strong style='color: green;'>{$success_count}</strong></p>";
        echo "<p>⚠ Requêtes ignorées (déjà existantes): <strong style='color: orange;'>{$skip_count}</strong></p>";
        echo "<p>✗ Erreurs: <strong style='color: red;'>{$error_count}</strong></p>";

        // Verify tables were created
        echo "<hr>";
        echo "<h3>🔍 Vérification des tables</h3>";

        $tables_to_check = [
            db_prefix() . 'dietic_dietitian_availability' => 'Disponibilités des diététiciens',
            db_prefix() . 'dietic_consultation_types' => 'Types de consultations'
        ];

        foreach ($tables_to_check as $table => $description) {
            $exists = $this->db->table_exists($table);
            if ($exists) {
                $count = $this->db->count_all($table);
                echo "<p class='success'>✓ Table <code>{$table}</code> existe ({$description}) - <strong>{$count}</strong> enregistrements</p>";
            } else {
                echo "<p class='error'>✗ Table <code>{$table}</code> INTROUVABLE</p>";
            }
        }

        // Check if consultation_type_id column was added
        $consult_table = db_prefix() . 'dietic_consultations';
        $columns = $this->db->list_fields($consult_table);
        if (in_array('consultation_type_id', $columns)) {
            echo "<p class='success'>✓ Colonne <code>consultation_type_id</code> ajoutée à la table consultations</p>";
        } else {
            echo "<p class='warning'>⚠ Colonne <code>consultation_type_id</code> non trouvée (peut-être déjà existante)</p>";
        }

        // Count consultation types
        if ($this->db->table_exists(db_prefix() . 'dietic_consultation_types')) {
            $types_count = $this->db->count_all(db_prefix() . 'dietic_consultation_types');

            echo "<hr>";
            echo "<h3>📋 Types de consultations créés ({$types_count})</h3>";

            $types = $this->db->get(db_prefix() . 'dietic_consultation_types')->result();
            echo "<table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                    <thead>
                        <tr style='background: #f5f5f5; border-bottom: 2px solid #ddd;'>
                            <th style='padding: 10px; text-align: left;'>Nom</th>
                            <th style='padding: 10px; text-align: center;'>Durée</th>
                            <th style='padding: 10px; text-align: center;'>En ligne</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($types as $type) {
                $online = $type->is_online_available ? '✓ Oui' : '✗ Non';
                echo "<tr style='border-bottom: 1px solid #eee;'>
                        <td style='padding: 8px;'><strong>{$type->name}</strong><br><small style='color: #666;'>{$type->description}</small></td>
                        <td style='padding: 8px; text-align: center;'>{$type->duration} min</td>
                        <td style='padding: 8px; text-align: center;'>{$online}</td>
                      </tr>";
            }

            echo "</tbody></table>";
        }

        echo "<hr>";

        if ($error_count == 0) {
            echo "<div style='background: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 20px 0;'>
                    <h3 style='color: #2e7d32; margin-top: 0;'>✅ Installation terminée avec succès !</h3>
                    <p>Le système de disponibilités est maintenant installé. Vous pouvez :</p>
                    <ul>
                        <li>Configurer les horaires des diététiciens dans <strong>Diététique > Disponibilités</strong></li>
                        <li>Créer des consultations avec vérification automatique des créneaux</li>
                    </ul>
                  </div>";
        } else {
            echo "<div style='background: #fff3e0; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0;'>
                    <h3 style='color: #e65100; margin-top: 0;'>⚠ Installation partiellement réussie</h3>
                    <p>Certaines erreurs se sont produites. Veuillez vérifier les messages ci-dessus.</p>
                  </div>";
        }

        log_activity('Dietetic Module: Availability system installation completed');

        echo "<p style='margin-top: 30px;'>
                <a href='" . admin_url('dietetic') . "' class='btn'>← Retour au Module Diététique</a>
                <a href='" . admin_url('dietetic/availability') . "' class='btn' style='margin-left: 10px; background: #F3911D;'>Gérer les Disponibilités →</a>
              </p>";

        echo "</div></body></html>";
    }
}
