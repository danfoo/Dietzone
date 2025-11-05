-- Migration: Add Dietitian Ratings Table
-- Allows patients to rate their dietitians on multiple criteria

CREATE TABLE IF NOT EXISTS `tbldietic_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL COMMENT 'Reference to dietic_patients.id',
  `dietitian_id` int(11) NOT NULL COMMENT 'Reference to staff.staffid',
  `overall_rating` decimal(2,1) NOT NULL DEFAULT 0.0 COMMENT 'Overall rating 0.0 to 5.0',

  -- Multiple rating criteria (each rated 1-5)
  `professionalism_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
  `listening_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
  `advice_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
  `results_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',
  `availability_rating` int(1) DEFAULT NULL COMMENT 'Rating 1-5',

  `comment` text DEFAULT NULL COMMENT 'Written review/comment',
  `is_public` tinyint(1) DEFAULT 1 COMMENT 'Whether rating is publicly visible',
  `is_verified` tinyint(1) DEFAULT 1 COMMENT 'Whether patient actually worked with dietitian',

  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_patient_dietitian` (`patient_id`, `dietitian_id`),
  KEY `dietitian_id` (`dietitian_id`),
  KEY `overall_rating` (`overall_rating`),
  KEY `is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
