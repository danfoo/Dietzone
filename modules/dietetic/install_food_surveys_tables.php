<?php
/**
 * Script pour installer manuellement les tables food surveys
 *
 * IMPORTANT: Supprimer ce fichier après utilisation pour des raisons de sécurité!
 *
 * Pour exécuter: Visiter https://votre-site.com/modules/dietetic/install_food_surveys_tables.php
 */

// Load Perfex CRM
if (!defined('BASEPATH')) {
    require_once(__DIR__ . '/../../application/config/database.php');
    require_once(__DIR__ . '/../../application/libraries/App_db.php');
}

$CI = &get_instance();

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Installation Tables Food Surveys</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f5f5f5;}";
echo ".success{color:green;background:#e7f9e7;padding:10px;margin:10px 0;border-left:4px solid green;}";
echo ".error{color:red;background:#ffe7e7;padding:10px;margin:10px 0;border-left:4px solid red;}";
echo ".info{color:blue;background:#e7f0ff;padding:10px;margin:10px 0;border-left:4px solid blue;}";
echo "</style></head><body>";

echo "<h1>Installation Tables Food Surveys</h1>";

try {
    // Load SQL file
    $sql_file = __DIR__ . '/install/food_surveys.sql';

    if (!file_exists($sql_file)) {
        echo "<div class='error'>❌ Fichier SQL non trouvé: {$sql_file}</div>";
        exit;
    }

    $sql = file_get_contents($sql_file);

    // Replace table prefix
    $sql = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql);

    // Get all CREATE TABLE statements
    preg_match_all('/CREATE TABLE.*?;/is', $sql, $create_statements);

    echo "<h2>Création des tables...</h2>";

    $created = 0;
    $errors = 0;

    foreach ($create_statements[0] as $statement) {
        // Extract table name
        preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $table_match);
        $table_name = $table_match[1] ?? 'unknown';

        // Check if table exists
        $exists = $CI->db->query("SHOW TABLES LIKE '{$table_name}'")->row_array();

        if ($exists) {
            echo "<div class='info'>ℹ️ Table existe déjà: {$table_name}</div>";
            continue;
        }

        try {
            $CI->db->query($statement);
            echo "<div class='success'>✅ Table créée: {$table_name}</div>";
            $created++;
        } catch (Exception $e) {
            echo "<div class='error'>❌ Erreur création {$table_name}: " . $e->getMessage() . "</div>";
            $errors++;
        }
    }

    echo "<h2>Résumé</h2>";
    echo "<div class='success'>{$created} tables créées avec succès</div>";

    if ($errors > 0) {
        echo "<div class='error'>{$errors} erreurs rencontrées</div>";
    }

    // Try to add foreign keys (they might fail if data conflicts)
    echo "<h2>Ajout des contraintes de clés étrangères...</h2>";

    $fk_statements = [
        "ALTER TABLE `" . db_prefix() . "dietic_food_surveys`
         ADD CONSTRAINT `fk_food_surveys_patient` FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients` (`id`) ON DELETE CASCADE",

        "ALTER TABLE `" . db_prefix() . "dietic_food_survey_entries`
         ADD CONSTRAINT `fk_survey_entries_survey` FOREIGN KEY (`survey_id`) REFERENCES `" . db_prefix() . "dietic_food_surveys` (`id`) ON DELETE CASCADE",

        "ALTER TABLE `" . db_prefix() . "dietic_food_survey_beverages`
         ADD CONSTRAINT `fk_survey_beverages_entry` FOREIGN KEY (`entry_id`) REFERENCES `" . db_prefix() . "dietic_food_survey_entries` (`id`) ON DELETE CASCADE",

        "ALTER TABLE `" . db_prefix() . "dietic_food_survey_recommendations`
         ADD CONSTRAINT `fk_survey_recommendations_entry` FOREIGN KEY (`entry_id`) REFERENCES `" . db_prefix() . "dietic_food_survey_entries` (`id`) ON DELETE CASCADE",

        "ALTER TABLE `" . db_prefix() . "dietic_food_survey_comments`
         ADD CONSTRAINT `fk_survey_comments_recommendation` FOREIGN KEY (`recommendation_id`) REFERENCES `" . db_prefix() . "dietic_food_survey_recommendations` (`id`) ON DELETE CASCADE"
    ];

    $fk_added = 0;
    $fk_skipped = 0;

    foreach ($fk_statements as $fk_sql) {
        preg_match('/CONSTRAINT `([^`]+)`/', $fk_sql, $matches);
        $constraint_name = $matches[1] ?? 'unknown';

        // Check if constraint exists
        $check_sql = "SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND CONSTRAINT_NAME = '{$constraint_name}'";

        $exists = $CI->db->query($check_sql)->row();

        if ($exists) {
            echo "<div class='info'>ℹ️ Contrainte existe déjà: {$constraint_name}</div>";
            continue;
        }

        try {
            $CI->db->query($fk_sql);
            echo "<div class='success'>✅ Contrainte ajoutée: {$constraint_name}</div>";
            $fk_added++;
        } catch (Exception $e) {
            echo "<div class='error'>⚠️ Contrainte ignorée {$constraint_name}: " . substr($e->getMessage(), 0, 100) . "</div>";
            $fk_skipped++;
        }
    }

    echo "<h2>Résultat final</h2>";
    echo "<div class='success'>";
    echo "✅ {$created} tables créées<br>";
    echo "✅ {$fk_added} contraintes ajoutées<br>";
    if ($fk_skipped > 0) {
        echo "⚠️ {$fk_skipped} contraintes ignorées (non critiques)<br>";
    }
    echo "</div>";

    echo "<div class='info'>";
    echo "<strong>✅ Installation terminée!</strong><br><br>";
    echo "Vous pouvez maintenant utiliser les enquêtes alimentaires.<br>";
    echo "<strong style='color:red'>IMPORTANT: Supprimez ce fichier pour des raisons de sécurité!</strong><br>";
    echo "<code>rm " . __FILE__ . "</code>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>❌ Erreur fatale: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
