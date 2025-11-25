<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_audio_notes_to_recommendations extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $table_name = db_prefix() . 'dietic_recommendation_audio_notes';

        if (!$CI->db->table_exists($table_name)) {
            $CI->db->query("
                CREATE TABLE `{$table_name}` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `recommendation_id` int(11) NOT NULL COMMENT 'Reference to tbldietic_food_survey_recommendations',
                    `sender_type` enum('dietitian','patient') NOT NULL COMMENT 'Who sent the audio',
                    `sender_id` int(11) NOT NULL COMMENT 'Staff ID or Contact ID',
                    `audio_file` varchar(255) NOT NULL,
                    `duration` int(11) DEFAULT NULL COMMENT 'Duration in seconds',
                    `file_size` int(11) DEFAULT NULL COMMENT 'File size in bytes',
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `recommendation_id` (`recommendation_id`),
                    KEY `sender_type` (`sender_type`),
                    CONSTRAINT `{$table_name}_ibfk_1` FOREIGN KEY (`recommendation_id`)
                        REFERENCES `" . db_prefix() . "dietic_food_survey_recommendations` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ";
            ");
        }

        $audio_dir = FCPATH . 'uploads/dietetic/recommendation_audio/';
        if (!is_dir($audio_dir)) {
            mkdir($audio_dir, DIR_READ_MODE, true);
        }
    }

    public function down()
    {
        $CI = &get_instance();
        $table_name = db_prefix() . 'dietic_recommendation_audio_notes';

        if ($CI->db->table_exists($table_name)) {
            $CI->db->query("DROP TABLE `{$table_name}`");
        }
    }
}
