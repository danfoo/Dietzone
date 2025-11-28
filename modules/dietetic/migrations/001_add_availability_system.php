<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add Availability System
 *
 * Creates tables and data for dietitian availability management
 * - tbldietic_dietitian_availability: Weekly recurring schedules
 * - tbldietic_consultation_types: Consultation types with durations
 */

$CI = &get_instance();

echo "<h2>Running Availability System Migration...</h2>";

// Load and execute the SQL file
$sql_file = DIETETIC_MODULE_PATH . 'install/availability_system.sql';

if (!file_exists($sql_file)) {
    die("Error: SQL file not found at " . $sql_file);
}

$sql_content = file_get_contents($sql_file);

// Replace table prefix placeholders
$sql_content = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sql_content);

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $CI->db->query($statement);
            $success_count++;

            // Log successful statements (truncated for readability)
            $short_statement = substr($statement, 0, 100);
            echo "<p style='color: green;'>✓ " . htmlspecialchars($short_statement) . "...</p>";

        } catch (Exception $e) {
            $error_count++;
            $error_msg = $e->getMessage();

            // Check if error is acceptable (table/column already exists)
            if (
                stripos($error_msg, 'already exists') !== false ||
                stripos($error_msg, 'Duplicate column') !== false ||
                stripos($error_msg, 'Duplicate key') !== false
            ) {
                echo "<p style='color: orange;'>⚠ Skipped (already exists): " . htmlspecialchars(substr($statement, 0, 80)) . "...</p>";
            } else {
                // Real error
                echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($error_msg) . "</p>";
                echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 11px;'>" . htmlspecialchars($statement) . "</pre>";
            }

            log_activity('Availability migration error: ' . substr($error_msg, 0, 200));
        }
    }
}

echo "<hr>";
echo "<h3>Migration Summary</h3>";
echo "<p>✓ Successful statements: <strong>{$success_count}</strong></p>";
echo "<p>✗ Errors/Skipped: <strong>{$error_count}</strong></p>";

// Verify tables were created
$tables_to_check = [
    db_prefix() . 'dietic_dietitian_availability',
    db_prefix() . 'dietic_consultation_types'
];

echo "<h3>Verification</h3>";
foreach ($tables_to_check as $table) {
    $exists = $CI->db->table_exists($table);
    $status = $exists ? '✓' : '✗';
    $color = $exists ? 'green' : 'red';
    echo "<p style='color: {$color};'>{$status} Table <code>{$table}</code> " . ($exists ? 'exists' : 'NOT FOUND') . "</p>";
}

// Count consultation types
$types_count = $CI->db->count_all(db_prefix() . 'dietic_consultation_types');
echo "<p>📋 Consultation types created: <strong>{$types_count}</strong></p>";

log_activity('Dietetic Module: Availability system migration completed');

echo "<hr>";
echo "<p><a href='" . admin_url('dietetic') . "' class='btn btn-primary'>← Back to Dietetic Module</a></p>";
