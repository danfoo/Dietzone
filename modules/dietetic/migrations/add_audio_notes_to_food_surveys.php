<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Migration: Add audio notes table for food surveys
 *
 * Allows patients to record voice notes for their meals instead of typing
 */
class Migration_Add_audio_notes_to_food_surveys extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Create audio notes table
        $table_name = db_prefix() . 'dietic_food_survey_audio_notes';

        if (!$CI->db->table_exists($table_name)) {
            $CI->db->query("
                CREATE TABLE `{$table_name}` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `entry_id` int(11) NOT NULL COMMENT 'Reference to tbldietic_food_survey_entries',
                    `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL COMMENT 'Type of meal',
                    `audio_file` varchar(255) NOT NULL COMMENT 'Filename of the audio file',
                    `duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
                    `file_size` int(11) DEFAULT NULL COMMENT 'File size in bytes',
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `entry_id` (`entry_id`),
                    KEY `meal_type` (`meal_type`),
                    CONSTRAINT `{$table_name}_ibfk_1` FOREIGN KEY (`entry_id`) REFERENCES `" . db_prefix() . "dietic_food_survey_entries` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
            ");
        }

        // Create uploads directory if it doesn't exist
        $audio_dir = FCPATH . 'uploads/dietetic/audio_notes/';
        if (!is_dir($audio_dir)) {
            mkdir($audio_dir, DIR_READ_MODE, true);
        }
    }

    public function down()
    {
        $CI = &get_instance();
        $table_name = db_prefix() . 'dietic_food_survey_audio_notes';

        if ($CI->db->table_exists($table_name)) {
            $CI->db->query("DROP TABLE `{$table_name}`");
        }
    }
}
