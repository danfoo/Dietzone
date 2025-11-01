<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietetic Module Installation Script
 */

$CI = &get_instance();

// Create uploads directory
if (!is_dir(DIETETIC_MODULE_UPLOAD_FOLDER)) {
    mkdir(DIETETIC_MODULE_UPLOAD_FOLDER, 0755, true);
    // Create subdirectories
    mkdir(DIETETIC_MODULE_UPLOAD_FOLDER . '/documents', 0755, true);
    mkdir(DIETETIC_MODULE_UPLOAD_FOLDER . '/meal_plans', 0755, true);
    mkdir(DIETETIC_MODULE_UPLOAD_FOLDER . '/consultations', 0755, true);

    // Create .htaccess to protect uploads
    $htaccess = 'Options -Indexes' . PHP_EOL;
    file_put_contents(DIETETIC_MODULE_UPLOAD_FOLDER . '/.htaccess', $htaccess);
}

// Load and execute the installation SQL
$install_sql = file_get_contents(__DIR__ . '/install.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $install_sql)));

foreach ($statements as $statement) {
    if (!empty($statement) && stripos($statement, 'CREATE TABLE') !== false) {
        // Execute CREATE TABLE statements
        $CI->db->query($statement);
    } elseif (!empty($statement) && stripos($statement, 'INSERT INTO') !== false) {
        // Execute INSERT statements
        $CI->db->query($statement);
    } elseif (!empty($statement) && stripos($statement, 'ALTER TABLE') !== false) {
        // Try to execute ALTER TABLE (foreign keys), but don't fail if they already exist
        try {
            $CI->db->query($statement);
        } catch (Exception $e) {
            // Foreign key might already exist, continue
            log_activity('Dietetic module: ' . $e->getMessage());
        }
    }
}

// Load sample data
$sample_data_file = __DIR__ . '/sample_data.sql';
if (file_exists($sample_data_file)) {
    $sample_sql = file_get_contents($sample_data_file);
    $statements = array_filter(array_map('trim', explode(';', $sample_sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $CI->db->query($statement);
            } catch (Exception $e) {
                // Sample data might already exist
                log_activity('Dietetic module sample data: ' . $e->getMessage());
            }
        }
    }
}

// Log installation
log_activity('Dietetic Module Installed');
