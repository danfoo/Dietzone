-- Migration: Add Dietitian Profile System
-- Description: Add referral codes, specialties, and professional info for dietitians
-- Date: 2025-12-14

-- 1. Add columns to staff table for dietitian profile
ALTER TABLE `tblstaff`
ADD COLUMN `dietitian_referral_code` VARCHAR(20) NULL UNIQUE COMMENT 'Unique referral code for patient assignment' AFTER `email`,
ADD COLUMN `dietitian_specialties` TEXT NULL COMMENT 'JSON array of specialties/skills' AFTER `dietitian_referral_code`,
ADD COLUMN `dietitian_years_experience` INT(3) DEFAULT 0 COMMENT 'Years of professional experience' AFTER `dietitian_specialties`,
ADD COLUMN `dietitian_bio` TEXT NULL COMMENT 'Professional biography' AFTER `dietitian_years_experience`,
ADD COLUMN `dietitian_languages` VARCHAR(255) NULL COMMENT 'Languages spoken (comma-separated)' AFTER `dietitian_bio`,
ADD COLUMN `dietitian_certifications` TEXT NULL COMMENT 'Professional certifications' AFTER `dietitian_languages`,
ADD COLUMN `dietitian_profile_updated_at` DATETIME NULL COMMENT 'Last profile update timestamp' AFTER `dietitian_certifications`;

-- 2. Create referrals tracking table
CREATE TABLE IF NOT EXISTS `tbldietic_referrals` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dietitian_staff_id` INT(11) NOT NULL COMMENT 'Staff member (dietitian) who referred',
  `patient_id` INT(11) NOT NULL COMMENT 'Patient who was referred',
  `referral_code` VARCHAR(20) NOT NULL COMMENT 'Code used for referral',
  `referred_at` DATETIME NOT NULL COMMENT 'When the referral was made',
  `source` VARCHAR(50) DEFAULT 'registration' COMMENT 'Source of referral (registration, manual, etc)',
  PRIMARY KEY (`id`),
  INDEX `idx_dietitian` (`dietitian_staff_id`),
  INDEX `idx_patient` (`patient_id`),
  INDEX `idx_code` (`referral_code`),
  INDEX `idx_date` (`referred_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 3. Create index on referral code for fast lookups
CREATE INDEX `idx_dietitian_referral_code` ON `tblstaff` (`dietitian_referral_code`);

-- 4. Create specialties predefined list table (optional, for autocomplete)
CREATE TABLE IF NOT EXISTS `tbldietic_specialties` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name_fr` VARCHAR(100) NOT NULL COMMENT 'Specialty name in French',
  `name_en` VARCHAR(100) NULL COMMENT 'Specialty name in English',
  `icon` VARCHAR(50) NULL COMMENT 'FontAwesome icon class',
  `color` VARCHAR(20) DEFAULT '#01807B' COMMENT 'Badge color',
  `is_active` TINYINT(1) DEFAULT 1,
  `display_order` INT(3) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 5. Insert default specialties
INSERT INTO `tbldietic_specialties` (`name_fr`, `name_en`, `icon`, `color`, `display_order`) VALUES
('Perte de poids', 'Weight Loss', 'fa-balance-scale', '#e74c3c', 1),
('Nutrition sportive', 'Sports Nutrition', 'fa-heartbeat', '#3498db', 2),
('Diabète', 'Diabetes', 'fa-medkit', '#e67e22', 3),
('Nutrition pédiatrique', 'Pediatric Nutrition', 'fa-child', '#9b59b6', 4),
('Grossesse', 'Pregnancy Nutrition', 'fa-female', '#e91e63', 5),
('Troubles du comportement alimentaire (TCA)', 'Eating Disorders', 'fa-user-md', '#f39c12', 6),
('Végétarisme/Véganisme', 'Vegetarian/Vegan', 'fa-leaf', '#27ae60', 7),
('Maladies cardiovasculaires', 'Cardiovascular Disease', 'fa-heart', '#c0392b', 8),
('Allergies alimentaires', 'Food Allergies', 'fa-warning', '#d35400', 9),
('Nutrition gériatrique', 'Geriatric Nutrition', 'fa-wheelchair', '#7f8c8d', 10),
('Nutrition clinique', 'Clinical Nutrition', 'fa-hospital-o', '#16a085', 11),
('Bien-être général', 'General Wellness', 'fa-smile-o', '#01807B', 12),
('Nutrition de la femme', 'Women\'s Nutrition', 'fa-venus', '#e91e8f', 13),
('Nutrition santé publique & collective', 'Public & Community Health Nutrition', 'fa-users', '#2980b9', 14),
('Nutrition fonctionnelle & préventive', 'Functional & Preventive Nutrition', 'fa-shield', '#16a085', 15);

-- 6. Add referral_code column to patient registration tracking (if needed)
-- This allows us to track which code was used even if the assignment changes later
ALTER TABLE `tbldietic_patients`
ADD COLUMN `registration_referral_code` VARCHAR(20) NULL COMMENT 'Referral code used during registration' AFTER `notes`;

-- 7. Create view for dietitian statistics
CREATE OR REPLACE VIEW `view_dietitian_stats` AS
SELECT
    s.staffid,
    s.firstname,
    s.lastname,
    s.email,
    s.dietitian_referral_code,
    s.dietitian_specialties,
    s.dietitian_years_experience,
    COUNT(DISTINCT pda.patient_id) as total_patients,
    COUNT(DISTINCT CASE WHEN r.referred_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN r.patient_id END) as referrals_this_month,
    COUNT(DISTINCT CASE WHEN c.status = 'completed' THEN c.id END) as completed_consultations,
    AVG(CASE WHEN rat.rating > 0 THEN rat.rating END) as avg_rating,
    COUNT(DISTINCT rat.id) as total_ratings
FROM tblstaff s
LEFT JOIN tbldietic_patient_dietitians pda ON s.staffid = pda.dietitian_id
LEFT JOIN tbldietic_referrals r ON s.staffid = r.dietitian_staff_id
LEFT JOIN tbldietic_consultations c ON pda.patient_id = c.patient_id AND c.dietitian_id = s.staffid
LEFT JOIN tbldietic_ratings rat ON s.staffid = rat.dietitian_id
WHERE s.active = 1
GROUP BY s.staffid;

-- Success message
SELECT 'Dietitian Profile System migration completed successfully!' as status;
