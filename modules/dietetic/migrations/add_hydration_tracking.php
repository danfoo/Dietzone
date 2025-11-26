<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_hydration_tracking extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Create hydration tracking table
        $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'dietic_hydration_tracking` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `patient_id` int(11) NOT NULL,
            `quantity_ml` int(11) NOT NULL,
            `tracking_date` date NOT NULL,
            `tracking_time` time NOT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `patient_id` (`patient_id`),
            KEY `tracking_date` (`tracking_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

        // Create hydration goals table
        $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'dietic_hydration_goals` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `patient_id` int(11) NOT NULL,
            `daily_goal_ml` int(11) NOT NULL DEFAULT 2000,
            `created_at` datetime NOT NULL,
            `updated_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `patient_id` (`patient_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }

    public function down()
    {
        $CI = &get_instance();

        $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'dietic_hydration_tracking`;');
        $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'dietic_hydration_goals`;');
    }
}
