-- Dietetic Module Installation SQL
-- Creates all necessary tables for the dietetic management system

-- Table: diet_patients
-- Links Perfex clients to dietetic patient profiles
CREATE TABLE IF NOT EXISTS `tbldietic_patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `dietitian_id` int(11) NOT NULL COMMENT 'Staff member ID',
  `status` varchar(20) DEFAULT 'active' COMMENT 'active, inactive, archived',
  `gender` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(50) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `lifestyle_notes` text DEFAULT NULL,
  `dietary_preferences` text DEFAULT NULL COMMENT 'vegetarian, vegan, halal, etc.',
  `activity_level` varchar(20) DEFAULT 'moderate' COMMENT 'sedentary, light, moderate, active, very_active',
  `initial_weight` decimal(5,2) DEFAULT NULL,
  `target_weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `objective` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`),
  KEY `dietitian_id` (`dietitian_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_measurements
-- Tracks patient measurements over time
CREATE TABLE IF NOT EXISTS `tbldietic_measurements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `measurement_date` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(4,2) DEFAULT NULL,
  `body_fat` decimal(4,2) DEFAULT NULL COMMENT 'percentage',
  `muscle_mass` decimal(4,2) DEFAULT NULL COMMENT 'percentage',
  `waist` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `hips` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `neck` decimal(5,2) DEFAULT NULL COMMENT 'Tour de cou in cm',
  `chest` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `arms` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `thighs` decimal(5,2) DEFAULT NULL COMMENT 'in cm',
  `calf` decimal(5,2) DEFAULT NULL COMMENT 'Tour de mollets in cm',
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL COMMENT 'staff or client ID',
  `added_by_type` varchar(10) DEFAULT 'staff' COMMENT 'staff or client',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `measurement_date` (`measurement_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_consultations
-- Records all consultation appointments
CREATE TABLE IF NOT EXISTS `tbldietic_consultations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `dietitian_id` int(11) NOT NULL,
  `consultation_date` datetime NOT NULL,
  `consultation_type` varchar(50) DEFAULT 'follow_up' COMMENT 'initial, follow_up, emergency, online, in_person',
  `status` varchar(20) DEFAULT 'scheduled' COMMENT 'scheduled, completed, cancelled, no_show',
  `duration` int(11) DEFAULT 60 COMMENT 'in minutes',
  `location` varchar(100) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `weight_at_visit` decimal(5,2) DEFAULT NULL,
  `bmi_at_visit` decimal(4,2) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `next_consultation_date` datetime DEFAULT NULL,
  `documents` text DEFAULT NULL COMMENT 'JSON array of document paths',
  `satisfaction_score` int(1) DEFAULT NULL COMMENT '1-5 rating',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `dietitian_id` (`dietitian_id`),
  KEY `consultation_date` (`consultation_date`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_programs
-- Defines dietetic programs assigned to patients
CREATE TABLE IF NOT EXISTS `tbldietic_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `dietitian_id` int(11) NOT NULL,
  `program_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active' COMMENT 'active, completed, cancelled',
  `objective` text DEFAULT NULL,
  `daily_calories` int(11) DEFAULT NULL,
  `daily_protein` decimal(6,2) DEFAULT NULL COMMENT 'in grams',
  `daily_carbs` decimal(6,2) DEFAULT NULL COMMENT 'in grams',
  `daily_fats` decimal(6,2) DEFAULT NULL COMMENT 'in grams',
  `daily_fiber` decimal(6,2) DEFAULT NULL COMMENT 'in grams',
  `meal_count` int(2) DEFAULT 3 COMMENT 'meals per day',
  `instructions` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `dietitian_id` (`dietitian_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_meal_plans
-- Weekly meal plans within programs
CREATE TABLE IF NOT EXISTS `tbldietic_meal_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `week_number` int(3) NOT NULL DEFAULT 1,
  `plan_name` varchar(200) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `program_id` (`program_id`),
  KEY `week_number` (`week_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_meals
-- Individual meals within meal plans
CREATE TABLE IF NOT EXISTS `tbldietic_meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meal_plan_id` int(11) NOT NULL,
  `day_of_week` int(1) NOT NULL COMMENT '1=Monday, 7=Sunday',
  `meal_type` varchar(20) NOT NULL COMMENT 'breakfast, snack_am, lunch, snack_pm, dinner, snack_evening',
  `meal_name` varchar(200) DEFAULT NULL,
  `meal_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `calories` decimal(7,2) DEFAULT NULL,
  `protein` decimal(6,2) DEFAULT NULL,
  `carbs` decimal(6,2) DEFAULT NULL,
  `fats` decimal(6,2) DEFAULT NULL,
  `fiber` decimal(6,2) DEFAULT NULL,
  `display_order` int(3) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `meal_plan_id` (`meal_plan_id`),
  KEY `day_of_week` (`day_of_week`),
  KEY `meal_type` (`meal_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_meal_foods
-- Foods included in each meal
CREATE TABLE IF NOT EXISTS `tbldietic_meal_foods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meal_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit` varchar(20) DEFAULT 'g' COMMENT 'g, ml, piece, cup, tbsp, etc.',
  `display_order` int(3) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `meal_id` (`meal_id`),
  KEY `food_id` (`food_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_foods
-- Food database with nutritional values
CREATE TABLE IF NOT EXISTS `tbldietic_foods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `food_name` varchar(200) NOT NULL,
  `food_name_fr` varchar(200) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL COMMENT 'vegetables, fruits, proteins, grains, dairy, fats, beverages, etc.',
  `serving_size` decimal(8,2) DEFAULT 100 COMMENT 'reference serving size',
  `serving_unit` varchar(20) DEFAULT 'g',
  `calories` decimal(7,2) NOT NULL COMMENT 'per serving size',
  `protein` decimal(6,2) DEFAULT 0,
  `carbs` decimal(6,2) DEFAULT 0,
  `fats` decimal(6,2) DEFAULT 0,
  `fiber` decimal(6,2) DEFAULT 0,
  `sugar` decimal(6,2) DEFAULT 0,
  `sodium` decimal(7,2) DEFAULT 0 COMMENT 'in mg',
  `vitamins` text DEFAULT NULL COMMENT 'JSON or text description',
  `minerals` text DEFAULT NULL COMMENT 'JSON or text description',
  `allergens` varchar(255) DEFAULT NULL COMMENT 'gluten, dairy, nuts, etc.',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `food_name` (`food_name`),
  KEY `category` (`category`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_reminders
-- Automated reminders for appointments, meal times, and renewals
CREATE TABLE IF NOT EXISTS `tbldietic_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `reminder_type` varchar(50) NOT NULL COMMENT 'appointment, meal, measurement, renewal, custom',
  `related_id` int(11) DEFAULT NULL COMMENT 'ID of related record (consultation, program, etc.)',
  `send_via` varchar(20) DEFAULT 'email' COMMENT 'email, sms, both',
  `recipient` varchar(255) NOT NULL COMMENT 'email or phone',
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `scheduled_date` datetime NOT NULL,
  `sent_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, sent, failed, cancelled',
  `error_message` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `scheduled_date` (`scheduled_date`),
  KEY `status` (`status`),
  KEY `reminder_type` (`reminder_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_documents
-- Stores documents related to patients/consultations
CREATE TABLE IF NOT EXISTS `tbldietic_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `related_type` varchar(50) DEFAULT NULL COMMENT 'consultation, program, measurement, general',
  `related_id` int(11) DEFAULT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(50) DEFAULT NULL COMMENT 'pdf, image, lab_result, prescription, etc.',
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL COMMENT 'in bytes',
  `uploaded_by` int(11) NOT NULL COMMENT 'staff ID',
  `description` text DEFAULT NULL,
  `is_visible_to_client` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `related_type` (`related_type`),
  KEY `is_visible_to_client` (`is_visible_to_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: diet_settings
-- Module-specific settings
CREATE TABLE IF NOT EXISTS `tbldietic_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'text' COMMENT 'text, number, boolean, json',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Insert default settings
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('lam_api_url', '', 'text', 'LAM SMS API endpoint URL'),
('lam_api_key', '', 'text', 'LAM SMS API key'),
('lam_api_sender', '', 'text', 'LAM SMS sender name'),
('reminder_before_appointment_hours', '24', 'number', 'Hours before appointment to send reminder'),
('reminder_meal_enabled', '0', 'boolean', 'Enable meal time reminders'),
('reminder_measurement_enabled', '1', 'boolean', 'Enable measurement reminders'),
('reminder_renewal_days', '7', 'number', 'Days before program end to send renewal reminder'),
('default_consultation_duration', '60', 'number', 'Default consultation duration in minutes'),
('pdf_logo_path', '', 'text', 'Path to logo for PDF generation'),
('enable_client_booking', '1', 'boolean', 'Allow clients to book appointments from portal'),
('enable_client_measurements', '1', 'boolean', 'Allow clients to add measurements from portal');

-- Foreign key relationships will be added by install.php with proper db_prefix()
