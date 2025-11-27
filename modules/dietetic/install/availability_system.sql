-- Table for dietitian recurring weekly schedules
CREATE TABLE IF NOT EXISTS `tbldietic_dietitian_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dietitian_id` int(11) NOT NULL COMMENT 'FK to tblstaff.staffid',
  `day_of_week` tinyint(1) NOT NULL COMMENT '0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday',
  `start_time` time NOT NULL COMMENT 'Start time of availability slot',
  `end_time` time NOT NULL COMMENT 'End time of availability slot',
  `slot_duration` int(11) DEFAULT 60 COMMENT 'Default slot duration in minutes for this time block',
  `location` varchar(100) DEFAULT NULL COMMENT 'Office location or Online',
  `consultation_types` varchar(255) DEFAULT NULL COMMENT 'Comma-separated list of allowed consultation type IDs, NULL = all types allowed',
  `max_patients_per_slot` tinyint(2) DEFAULT 1 COMMENT 'Number of patients that can book same slot (usually 1)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=active, 0=inactive',
  `notes` text DEFAULT NULL COMMENT 'Internal notes about this availability slot',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dietitian_day` (`dietitian_id`, `day_of_week`),
  KEY `idx_dietitian_active` (`dietitian_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for consultation types with specific durations and settings
CREATE TABLE IF NOT EXISTS `tbldietic_consultation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Type name (e.g., Initial Consultation, Follow-up)',
  `slug` varchar(50) NOT NULL COMMENT 'URL-friendly identifier',
  `duration` int(11) NOT NULL DEFAULT 60 COMMENT 'Default duration in minutes',
  `description` text DEFAULT NULL COMMENT 'Description of this consultation type',
  `color` varchar(7) DEFAULT '#01807B' COMMENT 'Hex color for calendar display',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Price for this consultation type',
  `requires_preparation` tinyint(1) DEFAULT 0 COMMENT 'Does patient need to prepare (fasting, etc.)?',
  `preparation_instructions` text DEFAULT NULL COMMENT 'Instructions for patient preparation',
  `is_online_available` tinyint(1) DEFAULT 1 COMMENT 'Can this be done online?',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=active, 0=inactive',
  `display_order` int(11) DEFAULT 0 COMMENT 'Order for display in lists',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug` (`slug`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default consultation types
INSERT INTO `tbldietic_consultation_types` (`name`, `slug`, `duration`, `description`, `color`, `requires_preparation`, `is_online_available`, `display_order`, `created_at`) VALUES
('Consultation Initiale', 'initial', 90, 'Première consultation complète avec évaluation nutritionnelle détaillée', '#01807B', 0, 1, 1, NOW()),
('Suivi Régulier', 'follow_up', 60, 'Consultation de suivi pour ajustement du plan alimentaire', '#F3911D', 0, 1, 2, NOW()),
('Consultation Urgente', 'emergency', 30, 'Consultation rapide pour problème urgent', '#e74c3c', 0, 1, 3, NOW()),
('Consultation en Ligne', 'online', 45, 'Consultation vidéo à distance', '#3498db', 0, 1, 4, NOW()),
('Bilan Nutritionnel', 'assessment', 120, 'Bilan nutritionnel complet avec mesures et analyses', '#9b59b6', 1, 0, 5, NOW()),
('Consultation de Groupe', 'group', 90, 'Session collective (max 10 personnes)', '#2ecc71', 0, 0, 6, NOW());

-- Add index to existing consultations table for performance
ALTER TABLE `tbldietic_consultations`
ADD INDEX `idx_dietitian_date_status` (`dietitian_id`, `consultation_date`, `status`);

-- Add consultation_type_id column to link with consultation_types table
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `consultation_type_id` int(11) DEFAULT NULL COMMENT 'FK to tbldietic_consultation_types.id' AFTER `consultation_type`;

-- Migration: Map existing consultation_type values to consultation_type_id
UPDATE `tbldietic_consultations` c
LEFT JOIN `tbldietic_consultation_types` ct ON (
  (c.consultation_type = 'initial' AND ct.slug = 'initial') OR
  (c.consultation_type = 'follow_up' AND ct.slug = 'follow_up') OR
  (c.consultation_type = 'emergency' AND ct.slug = 'emergency') OR
  (c.consultation_type = 'online' AND ct.slug = 'online')
)
SET c.consultation_type_id = ct.id
WHERE c.consultation_type_id IS NULL AND ct.id IS NOT NULL;
