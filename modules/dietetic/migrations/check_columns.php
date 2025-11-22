<?php
/**
 * Script to check if consultation communication columns exist
 * Run this from: https://app.dietsenegal.net/admin/dietetic/check_columns
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Get database instance
$CI =& get_instance();
$CI->load->database();

echo "<h2>Vérification des Colonnes de Communication dans la Table Consultations</h2>";
echo "<hr>";

// Check if columns exist
$query = $CI->db->query("SHOW COLUMNS FROM " . db_prefix() . "dietic_consultations");
$columns = $query->result();

$required_columns = ['consultation_mode', 'online_platform', 'meeting_link'];
$existing_columns = [];
$missing_columns = [];

foreach ($columns as $column) {
    $existing_columns[] = $column->Field;
}

foreach ($required_columns as $req_col) {
    if (!in_array($req_col, $existing_columns)) {
        $missing_columns[] = $req_col;
    }
}

echo "<h3>Statut:</h3>";
if (empty($missing_columns)) {
    echo "<p style='color: green; font-weight: bold;'>✓ Toutes les colonnes requises existent!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Colonnes manquantes trouvées!</p>";
    echo "<ul>";
    foreach ($missing_columns as $col) {
        echo "<li style='color: red;'>" . $col . "</li>";
    }
    echo "</ul>";

    echo "<h3>Action Requise:</h3>";
    echo "<p>Vous devez appliquer la migration SQL. Voici les commandes à exécuter:</p>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "ALTER TABLE `" . db_prefix() . "dietic_consultations`\n";
    echo "ADD COLUMN `consultation_mode` VARCHAR(20) DEFAULT 'in_person' COMMENT 'in_person or online' AFTER `consultation_type`;\n\n";
    echo "ALTER TABLE `" . db_prefix() . "dietic_consultations`\n";
    echo "ADD COLUMN `online_platform` VARCHAR(50) DEFAULT NULL COMMENT 'zoom, google_meet, teams, whatsapp, skype, other' AFTER `consultation_mode`;\n\n";
    echo "ALTER TABLE `" . db_prefix() . "dietic_consultations`\n";
    echo "ADD COLUMN `meeting_link` VARCHAR(500) DEFAULT NULL COMMENT 'URL for online meetings' AFTER `online_platform`;\n\n";
    echo "ALTER TABLE `" . db_prefix() . "dietic_consultations`\n";
    echo "ADD INDEX `consultation_mode` (`consultation_mode`);\n\n";
    echo "ALTER TABLE `" . db_prefix() . "dietic_consultations`\n";
    echo "ADD INDEX `online_platform` (`online_platform`);\n";
    echo "</pre>";
}

echo "<h3>Toutes les Colonnes de la Table:</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Nom</th><th>Type</th><th>Null</th><th>Default</th></tr>";
foreach ($columns as $column) {
    $highlight = in_array($column->Field, $required_columns) ? "style='background: #d4edda;'" : "";
    echo "<tr $highlight>";
    echo "<td>" . $column->Field . "</td>";
    echo "<td>" . $column->Type . "</td>";
    echo "<td>" . $column->Null . "</td>";
    echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";
