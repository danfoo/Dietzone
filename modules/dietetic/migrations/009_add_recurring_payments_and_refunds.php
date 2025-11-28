<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add Recurring Payments and Refunds System
 *
 * Phase 9 - Creates tables and settings for:
 * - tbldietic_recurring_payments: Recurring payment schedules
 * - tbldietic_recurring_payment_transactions: Transaction history
 * - tbldietic_refunds: Refund management
 * - Updates to existing tables (subscriptions, payments, invoices)
 * - Settings for recurring payments and refunds
 *
 * Created: 28 November 2025
 * Phase: 9
 */

$CI = &get_instance();

echo "<div class='panel panel-primary'>";
echo "<div class='panel-heading'><h3 class='panel-title'><i class='fa fa-database'></i> Phase 9: Recurring Payments & Refunds Migration</h3></div>";
echo "<div class='panel-body'>";

echo "<div class='alert alert-info'>";
echo "<i class='fa fa-info-circle'></i> ";
echo "Cette migration ajoute le système de paiements récurrents et de remboursements.<br>";
echo "<strong>Composants :</strong> 3 nouvelles tables, modifications de 3 tables existantes, 8 nouveaux paramètres.";
echo "</div>";

// Load and execute the SQL file
$sql_file = dirname(__FILE__) . '/add_recurring_payments_and_refunds.sql';

if (!file_exists($sql_file)) {
    echo "<div class='alert alert-danger'>";
    echo "<i class='fa fa-times'></i> Erreur: Fichier SQL non trouvé à " . htmlspecialchars($sql_file);
    echo "</div>";
    die();
}

$sql_content = file_get_contents($sql_file);

// Replace table prefix placeholders
$sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

// Remove comments
$sql_content = preg_replace('/^--.*$/m', '', $sql_content); // Single-line comments
$sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content); // Multi-line comments

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;
$skipped_count = 0;
$errors = [];

echo "<h4><i class='fa fa-cogs'></i> Exécution des requêtes SQL</h4>";
echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;'>";

foreach ($statements as $index => $statement) {
    if (empty($statement) || strlen($statement) < 10) {
        continue;
    }

    try {
        $CI->db->query($statement);
        $success_count++;

        // Extract operation type for better logging
        if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
            echo "<p style='color: #28a745; margin: 5px 0;'><i class='fa fa-check-circle'></i> Table créée: <code>{$matches[1]}</code></p>";
        } elseif (preg_match('/ALTER TABLE.*?`([^`]+)`/i', $statement, $matches)) {
            echo "<p style='color: #17a2b8; margin: 5px 0;'><i class='fa fa-wrench'></i> Table modifiée: <code>{$matches[1]}</code></p>";
        } elseif (preg_match('/INSERT INTO.*?`([^`]+)`/i', $statement, $matches)) {
            echo "<p style='color: #007bff; margin: 5px 0;'><i class='fa fa-plus-circle'></i> Données insérées dans: <code>{$matches[1]}</code></p>";
        } elseif (preg_match('/CREATE INDEX.*?`([^`]+)`/i', $statement, $matches)) {
            echo "<p style='color: #6c757d; margin: 5px 0;'><i class='fa fa-bolt'></i> Index créé: <code>{$matches[1]}</code></p>";
        } elseif (preg_match('/UPDATE.*?`([^`]+)`/i', $statement, $matches)) {
            echo "<p style='color: #ffc107; margin: 5px 0;'><i class='fa fa-edit'></i> Données mises à jour dans: <code>{$matches[1]}</code></p>";
        } else {
            echo "<p style='color: #28a745; margin: 5px 0;'><i class='fa fa-check'></i> Requête " . ($index + 1) . " exécutée avec succès</p>";
        }

    } catch (Exception $e) {
        $error_msg = $e->getMessage();

        // Check if error is acceptable (table/column already exists)
        if (
            stripos($error_msg, 'already exists') !== false ||
            stripos($error_msg, 'Duplicate column') !== false ||
            stripos($error_msg, 'Duplicate key') !== false ||
            stripos($error_msg, 'Duplicate entry') !== false ||
            stripos($error_msg, 'check that column') !== false
        ) {
            $skipped_count++;
            echo "<p style='color: #ff9800; margin: 5px 0;'><i class='fa fa-info-circle'></i> Ignoré (déjà existant): " . htmlspecialchars(substr($statement, 0, 80)) . "...</p>";
        } else {
            // Real error
            $error_count++;
            $errors[] = [
                'statement' => substr($statement, 0, 150),
                'error' => $error_msg
            ];
            echo "<p style='color: #dc3545; margin: 5px 0;'><i class='fa fa-exclamation-triangle'></i> <strong>Erreur:</strong> " . htmlspecialchars(substr($error_msg, 0, 100)) . "</p>";
            echo "<pre style='background: #ffe6e6; padding: 5px; font-size: 11px; margin: 5px 0;'>" . htmlspecialchars(substr($statement, 0, 200)) . "...</pre>";
        }

        log_activity('Recurring Payments migration error: ' . substr($error_msg, 0, 200));
    }
}

echo "</div>";

// Summary
echo "<hr>";
echo "<h4><i class='fa fa-chart-pie'></i> Résumé de la migration</h4>";
echo "<div class='row'>";
echo "<div class='col-md-4'>";
echo "<div class='panel panel-success'><div class='panel-body text-center'>";
echo "<h3 style='color: #28a745; margin: 0;'>{$success_count}</h3>";
echo "<p style='margin: 0;'>Requêtes réussies</p>";
echo "</div></div>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<div class='panel panel-warning'><div class='panel-body text-center'>";
echo "<h3 style='color: #ff9800; margin: 0;'>{$skipped_count}</h3>";
echo "<p style='margin: 0;'>Éléments ignorés (déjà existants)</p>";
echo "</div></div>";
echo "</div>";
echo "<div class='col-md-4'>";
echo "<div class='panel panel-danger'><div class='panel-body text-center'>";
echo "<h3 style='color: #dc3545; margin: 0;'>{$error_count}</h3>";
echo "<p style='margin: 0;'>Erreurs</p>";
echo "</div></div>";
echo "</div>";
echo "</div>";

// Verify tables were created
echo "<h4><i class='fa fa-check-square'></i> Vérification des tables</h4>";

$tables_to_check = [
    db_prefix() . 'dietic_recurring_payments' => 'Paiements récurrents',
    db_prefix() . 'dietic_recurring_payment_transactions' => 'Historique des transactions',
    db_prefix() . 'dietic_refunds' => 'Remboursements'
];

echo "<div class='table-responsive'>";
echo "<table class='table table-bordered table-striped'>";
echo "<thead><tr><th width='40%'>Table</th><th width='30%'>Description</th><th width='15%'>Statut</th><th width='15%'>Lignes</th></tr></thead>";
echo "<tbody>";

foreach ($tables_to_check as $table => $description) {
    $exists = $CI->db->table_exists($table);
    $count = $exists ? $CI->db->count_all($table) : 0;

    $status_icon = $exists ? '<i class="fa fa-check-circle" style="color: #28a745;"></i> Existe' : '<i class="fa fa-times-circle" style="color: #dc3545;"></i> Manquante';

    echo "<tr>";
    echo "<td><code>{$table}</code></td>";
    echo "<td>{$description}</td>";
    echo "<td>{$status_icon}</td>";
    echo "<td>" . ($exists ? $count : 'N/A') . "</td>";
    echo "</tr>";
}

echo "</tbody></table>";
echo "</div>";

// Check settings
echo "<h4><i class='fa fa-cog'></i> Vérification des paramètres</h4>";

$settings_to_check = [
    'recurring_payments_enabled',
    'recurring_retry_max_attempts',
    'recurring_retry_interval_days',
    'recurring_send_reminder_days',
    'recurring_send_failure_notification',
    'refunds_enabled',
    'refunds_require_approval',
    'refunds_auto_update_invoice'
];

$settings_table = db_prefix() . 'dietic_settings';
$settings_found = 0;

if ($CI->db->table_exists($settings_table)) {
    foreach ($settings_to_check as $setting_key) {
        $query = $CI->db->where('setting_key', $setting_key)->get($settings_table);
        if ($query->num_rows() > 0) {
            $settings_found++;
        }
    }
}

$settings_percentage = count($settings_to_check) > 0 ? round(($settings_found / count($settings_to_check)) * 100) : 0;
echo "<div class='progress'>";
echo "<div class='progress-bar progress-bar-success' style='width: {$settings_percentage}%;'>{$settings_found} / " . count($settings_to_check) . " paramètres</div>";
echo "</div>";

// Final status
echo "<hr>";

if ($error_count > 0) {
    echo "<div class='alert alert-danger'>";
    echo "<i class='fa fa-exclamation-triangle'></i> ";
    echo "<strong>Migration terminée avec {$error_count} erreur(s).</strong> Vérifiez les messages ci-dessus.";
    echo "</div>";

    if (count($errors) > 0) {
        echo "<h4>Détails des erreurs :</h4>";
        echo "<div class='panel panel-danger'><div class='panel-body'>";
        foreach ($errors as $i => $error) {
            echo "<p><strong>Erreur " . ($i + 1) . ":</strong></p>";
            echo "<pre style='background: #f8d7da; padding: 10px;'>" . htmlspecialchars($error['error']) . "</pre>";
            echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 11px;'>" . htmlspecialchars($error['statement']) . "...</pre>";
            if ($i < count($errors) - 1) echo "<hr>";
        }
        echo "</div></div>";
    }
} else {
    echo "<div class='alert alert-success'>";
    echo "<i class='fa fa-check-circle'></i> ";
    echo "<strong>Migration terminée avec succès !</strong> Toutes les tables et paramètres ont été créés.";
    echo "</div>";
}

// Log activity
log_activity('Dietetic Module: Phase 9 - Recurring Payments & Refunds migration completed');

echo "<p class='text-center'>";
echo "<a href='" . admin_url('dietetic/migrations') . "' class='btn btn-default'><i class='fa fa-arrow-left'></i> Retour aux migrations</a> ";
echo "<a href='" . admin_url('dietetic/recurring_payments') . "' class='btn btn-primary'><i class='fa fa-refresh'></i> Paiements Récurrents</a> ";
echo "<a href='" . admin_url('dietetic/refunds') . "' class='btn btn-info'><i class='fa fa-undo'></i> Remboursements</a>";
echo "</p>";

echo "</div>"; // panel-body
echo "</div>"; // panel
