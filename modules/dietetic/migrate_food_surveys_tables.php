<?php
/**
 * Script de migration pour corriger la structure des tables food surveys
 *
 * Ce script corrige la colonne 'breakfast_photo' qui était mal nommée dans la version précédente.
 *
 * IMPORTANT: Supprimer ce fichier après utilisation!
 *
 * Pour exécuter: Visiter https://votre-site.com/modules/dietetic/migrate_food_surveys_tables.php
 */

// Bootstrap Perfex CRM
define('ENVIRONMENT', 'production');
chdir(__DIR__ . '/../../');

// Define BASEPATH before including files
if (!defined('BASEPATH')) {
    define('BASEPATH', realpath(__DIR__ . '/../../application') . '/');
}

// Load the CodeIgniter bootstrap
require_once(BASEPATH . '../index.php');

$CI = &get_instance();

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Migration Tables Food Surveys</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;max-width:900px;margin:0 auto;}";
echo ".success{color:green;background:#e7f9e7;padding:10px;margin:10px 0;border-left:4px solid green;}";
echo ".error{color:red;background:#ffe7e7;padding:10px;margin:10px 0;border-left:4px solid red;}";
echo ".warning{color:orange;background:#fff3e7;padding:10px;margin:10px 0;border-left:4px solid orange;}";
echo ".info{color:blue;background:#e7f0ff;padding:10px;margin:10px 0;border-left:4px solid blue;}";
echo "pre{background:#f8f8f8;padding:10px;border-radius:4px;overflow:auto;}";
echo "</style></head><body>";

echo "<h1>🔧 Migration Tables Food Surveys</h1>";
echo "<p>Ce script va corriger la structure des tables food surveys.</p>";

try {
    $db_prefix = db_prefix();
    $table_name = $db_prefix . 'dietic_food_survey_entries';

    echo "<h2>Étape 1: Vérification de la table</h2>";

    // Check if table exists
    $table_exists = $CI->db->query("SHOW TABLES LIKE '{$table_name}'")->row_array();

    if (!$table_exists) {
        echo "<div class='error'>❌ La table {$table_name} n'existe pas. Utilisez le script d'installation d'abord.</div>";
        echo "<p><a href='install_food_surveys_tables.php'>→ Aller au script d'installation</a></p>";
        exit;
    }

    echo "<div class='success'>✅ Table trouvée: {$table_name}</div>";

    // Get current columns
    $columns = $CI->db->query("SHOW COLUMNS FROM {$table_name}")->result_array();

    echo "<h3>Colonnes actuelles:</h3><pre>";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "</pre>";

    // Check for the broken column name
    $has_broken_column = false;
    $has_correct_column = false;

    foreach ($columns as $col) {
        if (strpos($col['Field'], '_photo') !== false && $col['Field'] != 'breakfast_photo' && $col['Field'] != 'lunch_photo' && $col['Field'] != 'dinner_photo') {
            $has_broken_column = true;
            $broken_column_name = $col['Field'];
        }
        if ($col['Field'] == 'breakfast_photo') {
            $has_correct_column = true;
        }
    }

    echo "<h2>Étape 2: Analyse de la structure</h2>";

    if ($has_correct_column) {
        echo "<div class='success'>✅ La colonne 'breakfast_photo' existe déjà. Structure correcte!</div>";
        echo "<div class='info'>ℹ️ Aucune migration nécessaire.</div>";
    } elseif ($has_broken_column) {
        echo "<div class='warning'>⚠️ Colonne cassée détectée: '{$broken_column_name}'</div>";
        echo "<div class='info'>🔧 Migration nécessaire...</div>";

        echo "<h2>Étape 3: Migration de la structure</h2>";

        // Rename the broken column
        $rename_sql = "ALTER TABLE `{$table_name}` CHANGE `{$broken_column_name}` `breakfast_photo` VARCHAR(255) DEFAULT NULL";

        try {
            $CI->db->query($rename_sql);
            echo "<div class='success'>✅ Colonne renommée: '{$broken_column_name}' → 'breakfast_photo'</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Erreur lors du renommage: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ La colonne 'breakfast_photo' n'existe pas.</div>";
        echo "<div class='info'>🔧 Ajout de la colonne...</div>";

        echo "<h2>Étape 3: Ajout de la colonne manquante</h2>";

        // Add the missing column
        $add_sql = "ALTER TABLE `{$table_name}` ADD COLUMN `breakfast_photo` VARCHAR(255) DEFAULT NULL AFTER `entry_date`";

        try {
            $CI->db->query($add_sql);
            echo "<div class='success'>✅ Colonne 'breakfast_photo' ajoutée avec succès</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Erreur lors de l'ajout: " . $e->getMessage() . "</div>";
        }
    }

    // Verify all photo columns exist
    echo "<h2>Étape 4: Vérification des autres colonnes photo</h2>";

    $required_columns = ['breakfast_photo', 'lunch_photo', 'dinner_photo'];
    $columns = $CI->db->query("SHOW COLUMNS FROM {$table_name}")->result_array();
    $existing_column_names = array_column($columns, 'Field');

    $missing_columns = [];
    foreach ($required_columns as $col) {
        if (!in_array($col, $existing_column_names)) {
            $missing_columns[] = $col;
        }
    }

    if (empty($missing_columns)) {
        echo "<div class='success'>✅ Toutes les colonnes photo sont présentes</div>";
    } else {
        echo "<div class='warning'>⚠️ Colonnes manquantes: " . implode(', ', $missing_columns) . "</div>";

        // Add missing columns
        foreach ($missing_columns as $col) {
            $position = 'AFTER entry_date';
            if ($col == 'lunch_photo') $position = 'AFTER breakfast_notes';
            if ($col == 'dinner_photo') $position = 'AFTER lunch_notes';

            $add_sql = "ALTER TABLE `{$table_name}` ADD COLUMN `{$col}` VARCHAR(255) DEFAULT NULL {$position}";

            try {
                $CI->db->query($add_sql);
                echo "<div class='success'>✅ Colonne '{$col}' ajoutée</div>";
            } catch (Exception $e) {
                echo "<div class='error'>❌ Erreur ajout '{$col}': " . $e->getMessage() . "</div>";
            }
        }
    }

    // Final verification
    echo "<h2>Étape 5: Vérification finale</h2>";

    $final_columns = $CI->db->query("SHOW COLUMNS FROM {$table_name}")->result_array();

    echo "<h3>Structure finale:</h3><pre>";
    foreach ($final_columns as $col) {
        $marker = in_array($col['Field'], $required_columns) ? '✅ ' : '   ';
        echo $marker . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    echo "</pre>";

    // Check all required columns are present
    $final_column_names = array_column($final_columns, 'Field');
    $all_present = true;
    foreach ($required_columns as $col) {
        if (!in_array($col, $final_column_names)) {
            $all_present = false;
            break;
        }
    }

    if ($all_present) {
        echo "<div class='success'>";
        echo "<h3>✅ Migration terminée avec succès!</h3>";
        echo "<p>La table est maintenant correctement structurée.</p>";
        echo "<p><strong>Vous pouvez maintenant:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Uploader des photos de repas</li>";
        echo "<li>✅ Soumettre des entrées quotidiennes</li>";
        echo "<li>✅ Utiliser toutes les fonctionnalités des enquêtes alimentaires</li>";
        echo "</ul>";
        echo "<p style='color:red;font-weight:bold;'>⚠️ IMPORTANT: Supprimez ce fichier pour des raisons de sécurité!</p>";
        echo "<code>rm " . __FILE__ . "</code>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Migration incomplète</h3>";
        echo "<p>Certaines colonnes sont toujours manquantes. Vérifiez les messages d'erreur ci-dessus.</p>";
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div class='error'>❌ Erreur fatale: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
