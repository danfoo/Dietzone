<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add Patient Booking Fields
 *
 * Adds support for patient self-service booking:
 * - booked_by_patient field to track booking source
 * - Extended status values to include 'pending' and 'confirmed'
 * - Indexes for better query performance
 */

$CI = &get_instance();

echo "<h2>Running Patient Booking Migration...</h2>";

// Load and execute the SQL file
$sql_file = DIETETIC_MODULE_PATH . 'migrations/add_patient_booking_fields.sql';

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
        // Skip comments
        if (str_starts_with(trim($statement), '--')) {
            continue;
        }

        try {
            $CI->db->query($statement);
            $success_count++;

            // Log successful statements (truncated for readability)
            $short_statement = substr($statement, 0, 100);
            echo "<p style='color: green;'>✓ " . htmlspecialchars($short_statement) . "...</p>";

        } catch (Exception $e) {
            $error_count++;
            $error_msg = $e->getMessage();

            // Check if error is acceptable (column/index already exists)
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

            log_activity('Patient booking migration error: ' . substr($error_msg, 0, 200));
        }
    }
}

echo "<hr>";
echo "<h3>Migration Summary</h3>";
echo "<p>✓ Successful statements: <strong>{$success_count}</strong></p>";
echo "<p>✗ Errors/Skipped: <strong>{$error_count}</strong></p>";

// Verify column was added
$table_name = db_prefix() . 'dietic_consultations';
$columns = $CI->db->list_fields($table_name);

echo "<h3>Verification</h3>";

$has_booked_by_patient = in_array('booked_by_patient', $columns);
$color = $has_booked_by_patient ? 'green' : 'red';
$status = $has_booked_by_patient ? '✓' : '✗';

echo "<p style='color: {$color};'>{$status} Column <code>booked_by_patient</code> " . ($has_booked_by_patient ? 'exists' : 'NOT FOUND') . "</p>";

log_activity('Dietetic Module: Patient booking migration completed');

echo "<hr>";
echo "<p><a href='" . admin_url('dietetic') . "' class='btn btn-primary'>← Back to Dietetic Module</a></p>";
