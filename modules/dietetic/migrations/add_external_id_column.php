<?php
/**
 * Migration: Add external_id column to notification_logs table
 * This column stores the external notification ID from providers like OneSignal, Firebase, etc.
 */

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

try {
    // Check if column already exists
    $table_name = db_prefix() . 'dietic_notification_logs';
    $column_check = $CI->db->query("SHOW COLUMNS FROM `{$table_name}` LIKE 'external_id'")->row();

    if (!$column_check) {
        // Add external_id column
        $sql = "ALTER TABLE `{$table_name}`
                ADD COLUMN `external_id` VARCHAR(255) NULL DEFAULT NULL
                COMMENT 'External notification ID from OneSignal/Firebase'
                AFTER `error_message`";

        $CI->db->query($sql);
        log_activity('Migration: external_id column added to ' . $table_name);

        // Add index for faster lookups
        $sql_index = "ALTER TABLE `{$table_name}` ADD KEY `idx_external_id` (`external_id`)";
        $CI->db->query($sql_index);
        log_activity('Migration: idx_external_id index added to ' . $table_name);

        echo json_encode([
            'success' => true,
            'message' => 'Colonne external_id ajoutée avec succès'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'already_exists' => true,
            'message' => 'Colonne external_id existe déjà'
        ]);
    }
} catch (Exception $e) {
    log_activity('Migration ERROR: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
