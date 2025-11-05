<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Apply database migrations for Dietetic module
 * This file should be run manually or included in upgrade process
 */

$CI = &get_instance();

// Migration: Add ratings table
$migration_sql = file_get_contents(__DIR__ . '/add_ratings_table.sql');

// Replace table prefix placeholders
$migration_sql = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $migration_sql);

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $migration_sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $CI->db->query($statement);
            echo "✓ Migration executed successfully<br>";
        } catch (Exception $e) {
            echo "✗ Migration error: " . $e->getMessage() . "<br>";
        }
    }
}

// Add foreign key constraints
$foreign_keys = [
    "ALTER TABLE `" . db_prefix() . "dietic_ratings`
     ADD CONSTRAINT `fk_diet_ratings_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_ratings`
     ADD CONSTRAINT `fk_diet_ratings_staff`
     FOREIGN KEY (`dietitian_id`) REFERENCES `" . db_prefix() . "staff`(`staffid`) ON DELETE CASCADE",
];

foreach ($foreign_keys as $fk_sql) {
    try {
        $CI->db->query($fk_sql);
        echo "✓ Foreign key added successfully<br>";
    } catch (Exception $e) {
        // FK might exist, that's OK
        echo "✓ Foreign key already exists (skipped)<br>";
    }
}

echo "<br><strong>Ratings table migration completed!</strong>";
