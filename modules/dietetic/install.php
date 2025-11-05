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

// Replace table prefix placeholders
$install_sql = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $install_sql);

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $install_sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $CI->db->query($statement);
        } catch (Exception $e) {
            // Log but continue - table might exist already
            log_activity('Dietetic install: ' . substr($e->getMessage(), 0, 200));
        }
    }
}

// Add foreign key constraints with proper db_prefix()
$foreign_keys = [
    "ALTER TABLE `" . db_prefix() . "dietic_patients`
     ADD CONSTRAINT `fk_diet_patients_client`
     FOREIGN KEY (`client_id`) REFERENCES `" . db_prefix() . "clients`(`userid`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_patients`
     ADD CONSTRAINT `fk_diet_patients_staff`
     FOREIGN KEY (`dietitian_id`) REFERENCES `" . db_prefix() . "staff`(`staffid`) ON DELETE RESTRICT",

    "ALTER TABLE `" . db_prefix() . "dietic_measurements`
     ADD CONSTRAINT `fk_diet_measurements_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_consultations`
     ADD CONSTRAINT `fk_diet_consultations_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_consultations`
     ADD CONSTRAINT `fk_diet_consultations_staff`
     FOREIGN KEY (`dietitian_id`) REFERENCES `" . db_prefix() . "staff`(`staffid`) ON DELETE RESTRICT",

    "ALTER TABLE `" . db_prefix() . "dietic_programs`
     ADD CONSTRAINT `fk_diet_programs_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_programs`
     ADD CONSTRAINT `fk_diet_programs_staff`
     FOREIGN KEY (`dietitian_id`) REFERENCES `" . db_prefix() . "staff`(`staffid`) ON DELETE RESTRICT",

    "ALTER TABLE `" . db_prefix() . "dietic_meal_plans`
     ADD CONSTRAINT `fk_diet_meal_plans_program`
     FOREIGN KEY (`program_id`) REFERENCES `" . db_prefix() . "dietic_programs`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_meals`
     ADD CONSTRAINT `fk_diet_meals_plan`
     FOREIGN KEY (`meal_plan_id`) REFERENCES `" . db_prefix() . "dietic_meal_plans`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_meal_foods`
     ADD CONSTRAINT `fk_diet_meal_foods_meal`
     FOREIGN KEY (`meal_id`) REFERENCES `" . db_prefix() . "dietic_meals`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_meal_foods`
     ADD CONSTRAINT `fk_diet_meal_foods_food`
     FOREIGN KEY (`food_id`) REFERENCES `" . db_prefix() . "dietic_foods`(`id`) ON DELETE RESTRICT",

    "ALTER TABLE `" . db_prefix() . "dietic_reminders`
     ADD CONSTRAINT `fk_diet_reminders_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",

    "ALTER TABLE `" . db_prefix() . "dietic_documents`
     ADD CONSTRAINT `fk_diet_documents_patient`
     FOREIGN KEY (`patient_id`) REFERENCES `" . db_prefix() . "dietic_patients`(`id`) ON DELETE CASCADE",
];

// Add foreign keys silently
foreach ($foreign_keys as $fk_sql) {
    try {
        $CI->db->query($fk_sql);
    } catch (Exception $e) {
        // FK might exist, that's OK
    }
}

// Load sample data only if foods table is empty
$sample_data_file = __DIR__ . '/sample_data.sql';
if (file_exists($sample_data_file)) {
    // Check if we already have foods in the database
    $existing_foods_count = $CI->db->count_all(db_prefix() . 'dietic_foods');

    // Only load sample data if the foods table is empty
    if ($existing_foods_count == 0) {
        $sample_sql = file_get_contents($sample_data_file);

        // Replace table prefix
        $sample_sql = str_replace('`tbldietic_', '`' . db_prefix() . 'dietic_', $sample_sql);

        $statements = array_filter(array_map('trim', explode(';', $sample_sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $CI->db->query($statement);
                } catch (Exception $e) {
                    // Sample data might already exist, that's OK
                    log_activity('Dietetic sample data: ' . substr($e->getMessage(), 0, 200));
                }
            }
        }

        log_activity('Dietetic Module: Sample food data loaded');
    }
}

// Log installation
log_activity('Dietetic Module Installed Successfully');
