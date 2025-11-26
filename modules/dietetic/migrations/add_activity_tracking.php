<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_activity_tracking extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        // Create activities table (database of sports activities)
        $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'dietic_activities` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `kcal_per_minute` decimal(6,2) NOT NULL COMMENT "Calories burned per minute",
            `description` text DEFAULT NULL,
            `category` varchar(100) DEFAULT NULL COMMENT "Sport category (cardio, musculation, etc.)",
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` datetime NOT NULL,
            `updated_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `is_active` (`is_active`),
            KEY `category` (`category`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

        // Create patient activities tracking table
        $CI->db->query('CREATE TABLE IF NOT EXISTS `' . db_prefix() . 'dietic_patient_activities` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `patient_id` int(11) NOT NULL,
            `activity_id` int(11) NOT NULL,
            `duration_minutes` int(11) NOT NULL COMMENT "Duration in minutes",
            `kcal_burned` decimal(8,2) NOT NULL COMMENT "Total calories burned (calculated)",
            `activity_date` date NOT NULL,
            `activity_time` time DEFAULT NULL,
            `notes` text DEFAULT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `patient_id` (`patient_id`),
            KEY `activity_id` (`activity_id`),
            KEY `activity_date` (`activity_date`),
            CONSTRAINT `fk_patient_activities_activity` FOREIGN KEY (`activity_id`) REFERENCES `' . db_prefix() . 'dietic_activities` (`id`) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

        // Insert default activities with kcal/min values
        $default_activities = [
            // Cardio activities
            ['name' => 'Course à pied (jogging)', 'kcal_per_minute' => 11.4, 'category' => 'Cardio', 'description' => 'Course à pied à rythme modéré'],
            ['name' => 'Marche rapide', 'kcal_per_minute' => 6.5, 'category' => 'Cardio', 'description' => 'Marche à allure rapide'],
            ['name' => 'Marche normale', 'kcal_per_minute' => 3.5, 'category' => 'Cardio', 'description' => 'Marche à allure normale'],
            ['name' => 'Vélo (vitesse modérée)', 'kcal_per_minute' => 8.0, 'category' => 'Cardio', 'description' => 'Vélo à vitesse moyenne'],
            ['name' => 'Vélo (vitesse élevée)', 'kcal_per_minute' => 12.0, 'category' => 'Cardio', 'description' => 'Vélo à vitesse élevée'],
            ['name' => 'Natation (crawl modéré)', 'kcal_per_minute' => 10.0, 'category' => 'Cardio', 'description' => 'Natation crawl à rythme modéré'],
            ['name' => 'Natation (brasse)', 'kcal_per_minute' => 8.5, 'category' => 'Cardio', 'description' => 'Natation brasse'],
            ['name' => 'Corde à sauter', 'kcal_per_minute' => 13.0, 'category' => 'Cardio', 'description' => 'Saut à la corde'],
            ['name' => 'Elliptique', 'kcal_per_minute' => 9.0, 'category' => 'Cardio', 'description' => 'Vélo elliptique'],
            ['name' => 'Rameur', 'kcal_per_minute' => 10.5, 'category' => 'Cardio', 'description' => 'Aviron sur rameur'],

            // Musculation
            ['name' => 'Musculation intensive', 'kcal_per_minute' => 8.0, 'category' => 'Musculation', 'description' => 'Musculation avec charges lourdes'],
            ['name' => 'Musculation modérée', 'kcal_per_minute' => 5.0, 'category' => 'Musculation', 'description' => 'Musculation légère à modérée'],
            ['name' => 'CrossFit / HIIT', 'kcal_per_minute' => 12.5, 'category' => 'Musculation', 'description' => 'Entraînement haute intensité'],
            ['name' => 'Fitness / Gymnastique', 'kcal_per_minute' => 6.0, 'category' => 'Musculation', 'description' => 'Gymnastique et fitness général'],

            // Sports collectifs
            ['name' => 'Football', 'kcal_per_minute' => 10.0, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de football'],
            ['name' => 'Basketball', 'kcal_per_minute' => 9.5, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de basketball'],
            ['name' => 'Volleyball', 'kcal_per_minute' => 4.0, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de volleyball'],
            ['name' => 'Tennis', 'kcal_per_minute' => 8.0, 'category' => 'Sports collectifs', 'description' => 'Match de tennis'],
            ['name' => 'Badminton', 'kcal_per_minute' => 7.0, 'category' => 'Sports collectifs', 'description' => 'Match de badminton'],

            // Yoga et étirements
            ['name' => 'Yoga doux', 'kcal_per_minute' => 2.5, 'category' => 'Yoga/Étirements', 'description' => 'Yoga léger et étirements'],
            ['name' => 'Yoga dynamique', 'kcal_per_minute' => 4.5, 'category' => 'Yoga/Étirements', 'description' => 'Yoga dynamique (Vinyasa, Ashtanga)'],
            ['name' => 'Pilates', 'kcal_per_minute' => 4.0, 'category' => 'Yoga/Étirements', 'description' => 'Séance de Pilates'],

            // Autres activités
            ['name' => 'Danse', 'kcal_per_minute' => 7.0, 'category' => 'Autres', 'description' => 'Danse (salsa, zumba, etc.)'],
            ['name' => 'Escalade', 'kcal_per_minute' => 11.0, 'category' => 'Autres', 'description' => 'Escalade en salle ou extérieur'],
            ['name' => 'Jardinage', 'kcal_per_minute' => 4.5, 'category' => 'Autres', 'description' => 'Travaux de jardinage'],
            ['name' => 'Ménage actif', 'kcal_per_minute' => 3.8, 'category' => 'Autres', 'description' => 'Ménage intensif'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($default_activities as $activity) {
            $CI->db->insert(db_prefix() . 'dietic_activities', [
                'name' => $activity['name'],
                'kcal_per_minute' => $activity['kcal_per_minute'],
                'category' => $activity['category'],
                'description' => $activity['description'],
                'is_active' => 1,
                'created_at' => $now
            ]);
        }
    }

    public function down()
    {
        $CI = &get_instance();

        // Drop patient activities table first (has foreign key)
        $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'dietic_patient_activities`;');

        // Then drop activities table
        $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'dietic_activities`;');
    }
}
