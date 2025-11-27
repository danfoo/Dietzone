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

        // Insert default activities with kcal/min values (base scientifique pour 70kg)
        $default_activities = [
            // Cardio - Marche et course
            ['name' => 'Marche lente (3 km/h)', 'kcal_per_minute' => 2.5, 'category' => 'Cardio', 'description' => 'Marche très lente, promenade'],
            ['name' => 'Marche normale (4-5 km/h)', 'kcal_per_minute' => 3.5, 'category' => 'Cardio', 'description' => 'Marche à allure normale'],
            ['name' => 'Marche rapide (6 km/h)', 'kcal_per_minute' => 6.5, 'category' => 'Cardio', 'description' => 'Marche à allure rapide'],
            ['name' => 'Marche nordique', 'kcal_per_minute' => 7.0, 'category' => 'Cardio', 'description' => 'Marche avec bâtons'],
            ['name' => 'Course à pied (jogging 8 km/h)', 'kcal_per_minute' => 11.4, 'category' => 'Cardio', 'description' => 'Course à pied à rythme modéré'],
            ['name' => 'Course rapide (10-12 km/h)', 'kcal_per_minute' => 13.5, 'category' => 'Cardio', 'description' => 'Course à rythme élevé'],
            ['name' => 'Sprint / Fractionné', 'kcal_per_minute' => 16.0, 'category' => 'Cardio', 'description' => 'Course à haute intensité'],
            ['name' => 'Montée d\'escaliers', 'kcal_per_minute' => 9.0, 'category' => 'Cardio', 'description' => 'Montée active d\'escaliers'],

            // Cardio - Vélo
            ['name' => 'Vélo d\'appartement (léger)', 'kcal_per_minute' => 5.5, 'category' => 'Cardio', 'description' => 'Vélo stationnaire intensité faible'],
            ['name' => 'Vélo (vitesse modérée 15-20 km/h)', 'kcal_per_minute' => 8.0, 'category' => 'Cardio', 'description' => 'Vélo à vitesse moyenne'],
            ['name' => 'Vélo (vitesse élevée 25-30 km/h)', 'kcal_per_minute' => 12.0, 'category' => 'Cardio', 'description' => 'Vélo à vitesse élevée'],
            ['name' => 'VTT', 'kcal_per_minute' => 10.5, 'category' => 'Cardio', 'description' => 'Vélo tout terrain'],
            ['name' => 'Spinning / RPM', 'kcal_per_minute' => 11.0, 'category' => 'Cardio', 'description' => 'Cours de vélo collectif'],

            // Cardio - Natation
            ['name' => 'Natation (crawl modéré)', 'kcal_per_minute' => 10.0, 'category' => 'Cardio', 'description' => 'Natation crawl à rythme modéré'],
            ['name' => 'Natation (crawl intensif)', 'kcal_per_minute' => 13.0, 'category' => 'Cardio', 'description' => 'Natation crawl rapide'],
            ['name' => 'Natation (brasse)', 'kcal_per_minute' => 8.5, 'category' => 'Cardio', 'description' => 'Natation brasse'],
            ['name' => 'Natation (dos crawlé)', 'kcal_per_minute' => 9.0, 'category' => 'Cardio', 'description' => 'Natation sur le dos'],
            ['name' => 'Aquagym', 'kcal_per_minute' => 6.0, 'category' => 'Cardio', 'description' => 'Gymnastique aquatique'],

            // Cardio - Autres
            ['name' => 'Corde à sauter', 'kcal_per_minute' => 13.0, 'category' => 'Cardio', 'description' => 'Saut à la corde'],
            ['name' => 'Elliptique', 'kcal_per_minute' => 9.0, 'category' => 'Cardio', 'description' => 'Vélo elliptique'],
            ['name' => 'Rameur', 'kcal_per_minute' => 10.5, 'category' => 'Cardio', 'description' => 'Aviron sur rameur'],
            ['name' => 'Stepper', 'kcal_per_minute' => 8.5, 'category' => 'Cardio', 'description' => 'Machine de step'],

            // Musculation et renforcement
            ['name' => 'Musculation intensive', 'kcal_per_minute' => 8.0, 'category' => 'Musculation', 'description' => 'Musculation avec charges lourdes'],
            ['name' => 'Musculation modérée', 'kcal_per_minute' => 5.0, 'category' => 'Musculation', 'description' => 'Musculation légère à modérée'],
            ['name' => 'Pompes', 'kcal_per_minute' => 7.0, 'category' => 'Musculation', 'description' => 'Exercice de pompes'],
            ['name' => 'Tractions', 'kcal_per_minute' => 8.0, 'category' => 'Musculation', 'description' => 'Exercice de tractions'],
            ['name' => 'Squats', 'kcal_per_minute' => 6.5, 'category' => 'Musculation', 'description' => 'Flexions de jambes'],
            ['name' => 'Burpees', 'kcal_per_minute' => 10.0, 'category' => 'Musculation', 'description' => 'Exercice complet burpees'],
            ['name' => 'Abdominaux / Gainage', 'kcal_per_minute' => 5.5, 'category' => 'Musculation', 'description' => 'Exercices abdominaux et gainage'],
            ['name' => 'CrossFit / HIIT', 'kcal_per_minute' => 12.5, 'category' => 'Musculation', 'description' => 'Entraînement haute intensité'],
            ['name' => 'Circuit training', 'kcal_per_minute' => 9.0, 'category' => 'Musculation', 'description' => 'Entraînement en circuit'],
            ['name' => 'Fitness / Gymnastique', 'kcal_per_minute' => 6.0, 'category' => 'Musculation', 'description' => 'Gymnastique et fitness général'],
            ['name' => 'Calisthenics', 'kcal_per_minute' => 7.5, 'category' => 'Musculation', 'description' => 'Exercices au poids du corps'],

            // Sports collectifs
            ['name' => 'Football', 'kcal_per_minute' => 10.0, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de football'],
            ['name' => 'Basketball', 'kcal_per_minute' => 9.5, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de basketball'],
            ['name' => 'Volleyball', 'kcal_per_minute' => 4.0, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de volleyball'],
            ['name' => 'Handball', 'kcal_per_minute' => 10.5, 'category' => 'Sports collectifs', 'description' => 'Match de handball'],
            ['name' => 'Rugby', 'kcal_per_minute' => 11.0, 'category' => 'Sports collectifs', 'description' => 'Match ou entraînement de rugby'],
            ['name' => 'Tennis', 'kcal_per_minute' => 8.0, 'category' => 'Sports collectifs', 'description' => 'Match de tennis'],
            ['name' => 'Tennis de table', 'kcal_per_minute' => 5.0, 'category' => 'Sports collectifs', 'description' => 'Ping-pong'],
            ['name' => 'Badminton', 'kcal_per_minute' => 7.0, 'category' => 'Sports collectifs', 'description' => 'Match de badminton'],
            ['name' => 'Squash', 'kcal_per_minute' => 12.0, 'category' => 'Sports collectifs', 'description' => 'Match de squash'],

            // Arts martiaux et sports de combat
            ['name' => 'Boxe (entraînement)', 'kcal_per_minute' => 11.0, 'category' => 'Arts martiaux', 'description' => 'Entraînement de boxe'],
            ['name' => 'Boxe (sac de frappe)', 'kcal_per_minute' => 9.0, 'category' => 'Arts martiaux', 'description' => 'Frappe sur sac'],
            ['name' => 'Kickboxing', 'kcal_per_minute' => 12.0, 'category' => 'Arts martiaux', 'description' => 'Entraînement de kickboxing'],
            ['name' => 'MMA / Combat libre', 'kcal_per_minute' => 13.0, 'category' => 'Arts martiaux', 'description' => 'Arts martiaux mixtes'],
            ['name' => 'Karaté', 'kcal_per_minute' => 10.0, 'category' => 'Arts martiaux', 'description' => 'Entraînement de karaté'],
            ['name' => 'Judo', 'kcal_per_minute' => 10.5, 'category' => 'Arts martiaux', 'description' => 'Entraînement de judo'],
            ['name' => 'Taekwondo', 'kcal_per_minute' => 10.0, 'category' => 'Arts martiaux', 'description' => 'Entraînement de taekwondo'],
            ['name' => 'Kung-fu / Wushu', 'kcal_per_minute' => 9.5, 'category' => 'Arts martiaux', 'description' => 'Arts martiaux chinois'],
            ['name' => 'Capoeira', 'kcal_per_minute' => 8.5, 'category' => 'Arts martiaux', 'description' => 'Art martial brésilien'],
            ['name' => 'Tai Chi', 'kcal_per_minute' => 3.5, 'category' => 'Arts martiaux', 'description' => 'Art martial doux'],

            // Yoga et étirements
            ['name' => 'Yoga doux / Hatha', 'kcal_per_minute' => 2.5, 'category' => 'Yoga/Étirements', 'description' => 'Yoga léger et étirements'],
            ['name' => 'Yoga dynamique (Vinyasa)', 'kcal_per_minute' => 4.5, 'category' => 'Yoga/Étirements', 'description' => 'Yoga dynamique Vinyasa'],
            ['name' => 'Yoga Power / Ashtanga', 'kcal_per_minute' => 5.5, 'category' => 'Yoga/Étirements', 'description' => 'Yoga intense'],
            ['name' => 'Pilates', 'kcal_per_minute' => 4.0, 'category' => 'Yoga/Étirements', 'description' => 'Séance de Pilates'],
            ['name' => 'Stretching / Étirements', 'kcal_per_minute' => 2.0, 'category' => 'Yoga/Étirements', 'description' => 'Séance d\'étirements'],

            // Danse
            ['name' => 'Danse de salon', 'kcal_per_minute' => 5.0, 'category' => 'Danse', 'description' => 'Danse de salon (valse, tango)'],
            ['name' => 'Salsa / Bachata', 'kcal_per_minute' => 6.5, 'category' => 'Danse', 'description' => 'Danses latines'],
            ['name' => 'Zumba', 'kcal_per_minute' => 8.0, 'category' => 'Danse', 'description' => 'Cours de zumba'],
            ['name' => 'Hip-hop / Street dance', 'kcal_per_minute' => 7.5, 'category' => 'Danse', 'description' => 'Danse hip-hop'],
            ['name' => 'Danse classique / Ballet', 'kcal_per_minute' => 6.0, 'category' => 'Danse', 'description' => 'Ballet classique'],
            ['name' => 'Danse africaine', 'kcal_per_minute' => 7.0, 'category' => 'Danse', 'description' => 'Danses africaines'],

            // Autres activités
            ['name' => 'Escalade (salle)', 'kcal_per_minute' => 11.0, 'category' => 'Autres', 'description' => 'Escalade en salle'],
            ['name' => 'Escalade (extérieur)', 'kcal_per_minute' => 10.0, 'category' => 'Autres', 'description' => 'Escalade en extérieur'],
            ['name' => 'Golf (marche)', 'kcal_per_minute' => 4.5, 'category' => 'Autres', 'description' => 'Golf avec marche'],
            ['name' => 'Roller / Patin à roulettes', 'kcal_per_minute' => 7.0, 'category' => 'Autres', 'description' => 'Roller ou patins'],
            ['name' => 'Ski de fond', 'kcal_per_minute' => 12.0, 'category' => 'Autres', 'description' => 'Ski de fond'],
            ['name' => 'Ski alpin', 'kcal_per_minute' => 8.0, 'category' => 'Autres', 'description' => 'Ski de descente'],
            ['name' => 'Randonnée (montagne)', 'kcal_per_minute' => 7.5, 'category' => 'Autres', 'description' => 'Randonnée en montagne'],
            ['name' => 'Équitation', 'kcal_per_minute' => 5.5, 'category' => 'Autres', 'description' => 'Équitation / Monte'],
            ['name' => 'Jardinage', 'kcal_per_minute' => 4.5, 'category' => 'Autres', 'description' => 'Travaux de jardinage'],
            ['name' => 'Ménage actif', 'kcal_per_minute' => 3.8, 'category' => 'Autres', 'description' => 'Ménage intensif'],
            ['name' => 'Bricolage', 'kcal_per_minute' => 4.0, 'category' => 'Autres', 'description' => 'Travaux de bricolage'],
        ];

        $now = date('Y-m-d H:i:s');
        // Check if activities already exist to prevent duplicates
        $existing_count = $CI->db->count_all(db_prefix() . 'dietic_activities');

        // Only insert if table is empty
        if ($existing_count == 0) {
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
