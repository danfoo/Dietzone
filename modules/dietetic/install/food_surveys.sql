-- Food Surveys Table
CREATE TABLE IF NOT EXISTS `tbldietic_food_surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `dietitian_id` int(11) NOT NULL,
  `survey_name` varchar(255) NOT NULL,
  `objective` text NOT NULL,
  `duration_days` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `program_id` (`program_id`),
  KEY `dietitian_id` (`dietitian_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily Food Entries Table
CREATE TABLE IF NOT EXISTS `tbldietic_food_survey_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `breakfast_photo` varchar(255) DEFAULT NULL,
  `breakfast_time` time DEFAULT NULL,
  `breakfast_notes` text DEFAULT NULL,
  `lunch_photo` varchar(255) DEFAULT NULL,
  `lunch_time` time DEFAULT NULL,
  `lunch_notes` text DEFAULT NULL,
  `dinner_photo` varchar(255) DEFAULT NULL,
  `dinner_time` time DEFAULT NULL,
  `dinner_notes` text DEFAULT NULL,
  `snack_photo` varchar(255) DEFAULT NULL,
  `snack_time` time DEFAULT NULL,
  `snack_notes` text DEFAULT NULL,
  `water_quantity_ml` int(11) DEFAULT NULL,
  `has_recommendation` tinyint(1) DEFAULT 0,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `survey_date` (`survey_id`, `entry_date`),
  KEY `survey_id` (`survey_id`),
  KEY `entry_date` (`entry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Beverages Table (drinks consumed during the day)
CREATE TABLE IF NOT EXISTS `tbldietic_food_survey_beverages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `beverage_name` varchar(255) NOT NULL,
  `quantity_ml` int(11) NOT NULL,
  `consumption_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dietitian Recommendations Table
CREATE TABLE IF NOT EXISTS `tbldietic_food_survey_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `dietitian_id` int(11) NOT NULL,
  `recommendation_text` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`),
  KEY `dietitian_id` (`dietitian_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Patient Comments on Recommendations Table
CREATE TABLE IF NOT EXISTS `tbldietic_food_survey_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recommendation_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recommendation_id` (`recommendation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign keys
ALTER TABLE `tbldietic_food_surveys`
  ADD CONSTRAINT `fk_food_surveys_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_food_surveys_program` FOREIGN KEY (`program_id`) REFERENCES `tbldietic_programs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_food_surveys_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE CASCADE;

ALTER TABLE `tbldietic_food_survey_entries`
  ADD CONSTRAINT `fk_survey_entries_survey` FOREIGN KEY (`survey_id`) REFERENCES `tbldietic_food_surveys` (`id`) ON DELETE CASCADE;

ALTER TABLE `tbldietic_food_survey_beverages`
  ADD CONSTRAINT `fk_survey_beverages_entry` FOREIGN KEY (`entry_id`) REFERENCES `tbldietic_food_survey_entries` (`id`) ON DELETE CASCADE;

ALTER TABLE `tbldietic_food_survey_recommendations`
  ADD CONSTRAINT `fk_survey_recommendations_entry` FOREIGN KEY (`entry_id`) REFERENCES `tbldietic_food_survey_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_survey_recommendations_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE CASCADE;

ALTER TABLE `tbldietic_food_survey_comments`
  ADD CONSTRAINT `fk_survey_comments_recommendation` FOREIGN KEY (`recommendation_id`) REFERENCES `tbldietic_food_survey_recommendations` (`id`) ON DELETE CASCADE;
