<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_statistics_notes extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Create table for patient statistics notes
        if (!$CI->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'dietic_statistics_notes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `patient_id` int(11) NOT NULL,
                `note_date` date NOT NULL COMMENT "Date à laquelle la note s\'applique",
                `note_text` text NOT NULL COMMENT "Contenu de la note",
                `note_type` varchar(50) DEFAULT "general" COMMENT "Type de note: general, milestone, event, etc.",
                `icon` varchar(50) DEFAULT NULL COMMENT "Icône Font Awesome à afficher",
                `color` varchar(20) DEFAULT NULL COMMENT "Couleur du marqueur (#hex)",
                `created_by` int(11) NOT NULL COMMENT "ID du créateur (staff ou contact)",
                `created_by_type` enum("staff","patient") NOT NULL DEFAULT "patient",
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `patient_id` (`patient_id`),
                KEY `note_date` (`note_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
        }
    }

    public function down()
    {
        $CI = &get_instance();

        if ($CI->db->table_exists(db_prefix() . 'dietic_statistics_notes')) {
            $CI->db->query('DROP TABLE `' . db_prefix() . 'dietic_statistics_notes`');
        }
    }
}
