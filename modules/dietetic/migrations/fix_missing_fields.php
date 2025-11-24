<?php
/**
 * Script de correction des 6 champs manquants
 * URL: https://votredomaine.com/admin/dietetic/fix_missing_anamnesis_fields
 */

defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();
$CI->load->database();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Correction Champs Manquants</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #01807B; border-bottom: 3px solid #01807B; padding-bottom: 10px; }
.alert { padding: 15px; border-radius: 5px; margin: 20px 0; }
.alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
.alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
.alert-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
.log-entry { padding: 10px; margin: 5px 0; border-radius: 5px; font-family: monospace; font-size: 13px; }
.log-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
.log-error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
.btn { display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; font-weight: bold; }
.btn:hover { background: #015a57; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🔧 Correction des Champs Manquants</h2>";

// Vérifier si admin
if (!is_admin()) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>❌ ERREUR:</strong> Vous devez être administrateur pour exécuter ce script.";
    echo "</div>";
    echo "<a href='" . admin_url('dietetic/patients') . "' class='btn'>Retour</a>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='alert alert-info'>";
echo "<strong>ℹ️ Information:</strong> Ce script va ajouter les 6 champs manquants identifiés lors de la vérification.";
echo "</div>";

// Les 6 champs à ajouter
$missing_fields = [
    [
        'name' => 'title',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `title` VARCHAR(10) DEFAULT NULL COMMENT 'Civilité: M., Mme, Mlle'",
        'description' => 'Civilité (M./Mme/Mlle)'
    ],
    [
        'name' => 'is_pregnant',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `is_pregnant` VARCHAR(3) DEFAULT 'no' COMMENT 'yes or no'",
        'description' => 'Grossesse en cours'
    ],
    [
        'name' => 'waist_circumference',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `waist_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de taille en cm'",
        'description' => 'Tour de taille'
    ],
    [
        'name' => 'supplements',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `supplements` TEXT DEFAULT NULL COMMENT 'Compléments alimentaires & vitamines'",
        'description' => 'Compléments alimentaires & vitamines'
    ],
    [
        'name' => 'digestive_issues',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `digestive_issues` VARCHAR(50) DEFAULT 'none' COMMENT 'none, constipation, diarrhea, bloating, reflux, ibs, multiple'",
        'description' => 'Problèmes digestifs'
    ],
    [
        'name' => 'meals_per_day',
        'sql' => "ALTER TABLE `" . db_prefix() . "dietic_patients` ADD COLUMN `meals_per_day` INT(2) DEFAULT NULL COMMENT 'Nombre de repas par jour'",
        'description' => 'Nombre de repas/jour'
    ],
];

// Vérifier quels champs existent déjà
$query = $CI->db->query("SHOW COLUMNS FROM " . db_prefix() . "dietic_patients");
$existing_columns = [];
foreach ($query->result() as $column) {
    $existing_columns[] = $column->Field;
}

$success_count = 0;
$error_count = 0;
$skip_count = 0;

echo "<h3>📝 Journal d'exécution</h3>";

foreach ($missing_fields as $field) {
    // Vérifier si la colonne existe déjà
    if (in_array($field['name'], $existing_columns)) {
        $skip_count++;
        echo "<div class='log-entry' style='background: #fff3cd; border-left: 4px solid #ffc107; color: #856404;'>";
        echo "⚠️ SKIPPED: La colonne '{$field['name']}' ({$field['description']}) existe déjà";
        echo "</div>";
        continue;
    }

    // Essayer d'ajouter la colonne
    try {
        $CI->db->query($field['sql']);
        $success_count++;
        echo "<div class='log-success'>";
        echo "✅ SUCCESS: Colonne '{$field['name']}' ({$field['description']}) ajoutée avec succès";
        echo "</div>";
    } catch (Exception $e) {
        $error_count++;
        $error_message = substr($e->getMessage(), 0, 150);
        echo "<div class='log-error'>";
        echo "❌ ERROR: Échec pour '{$field['name']}' - {$error_message}";
        echo "</div>";
    }
}

// Résumé
echo "<h3>📊 Résumé</h3>";
echo "<div class='alert " . ($error_count > 0 ? 'alert-danger' : 'alert-success') . "'>";

if ($error_count > 0) {
    echo "<strong>⚠️ ATTENTION:</strong> {$error_count} erreur(s) rencontrée(s). ";
} elseif ($success_count > 0) {
    echo "<strong>✅ SUCCÈS:</strong> {$success_count} colonne(s) ajoutée(s) avec succès ! ";
} else {
    echo "<strong>ℹ️ INFO:</strong> Aucune modification nécessaire. ";
}

echo "Total: " . count($missing_fields) . " | ";
echo "Succès: {$success_count} | ";
echo "Erreurs: {$error_count} | ";
echo "Ignorés: {$skip_count}";
echo "</div>";

// Log activity
if ($success_count > 0) {
    log_activity('Dietetic Module: Correction champs manquants - ' . $success_count . ' champs ajoutés');
}

// Boutons
echo "<h3>🛠️ Actions</h3>";
echo "<a href='" . admin_url('dietetic/check_anamnesis_fields') . "' class='btn'>🔍 Vérifier à nouveau</a>";
echo "<a href='" . admin_url('dietetic/patients') . "' class='btn'>📋 Aller aux Patients</a>";

echo "</div></body></html>";
