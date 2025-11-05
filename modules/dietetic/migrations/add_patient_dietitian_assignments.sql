-- Table de liaison pour relation many-to-many entre patients et diététiciens
-- Un patient peut être suivi par plusieurs diététiciens
-- Un diététicien peut suivre plusieurs patients

CREATE TABLE IF NOT EXISTS `tbldietic_patient_dietitians` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL COMMENT 'Reference to dietic_patients.id',
  `dietitian_id` int(11) NOT NULL COMMENT 'Reference to staff.staffid',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Diététicien principal pour ce patient',
  `assigned_date` datetime NOT NULL COMMENT 'Date d\'assignation',
  `assigned_by` int(11) DEFAULT NULL COMMENT 'Staff ID who assigned',
  `notes` text DEFAULT NULL COMMENT 'Notes sur cette assignation',
  `status` varchar(20) DEFAULT 'active' COMMENT 'active, inactive',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_patient_dietitian` (`patient_id`, `dietitian_id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_dietitian` (`dietitian_id`),
  KEY `idx_status` (`status`),
  KEY `idx_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Ajouter les contraintes de clés étrangères
ALTER TABLE `tbldietic_patient_dietitians`
  ADD CONSTRAINT `fk_patient_diet_patient`
  FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients`(`id`) ON DELETE CASCADE;

ALTER TABLE `tbldietic_patient_dietitians`
  ADD CONSTRAINT `fk_patient_diet_staff`
  FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff`(`staffid`) ON DELETE CASCADE;

-- Migrer les données existantes de dietic_patients.dietitian_id vers la nouvelle table
INSERT INTO `tbldietic_patient_dietitians` (patient_id, dietitian_id, is_primary, assigned_date, created_at)
SELECT
    id as patient_id,
    dietitian_id,
    1 as is_primary,
    created_at as assigned_date,
    created_at
FROM `tbldietic_patients`
WHERE dietitian_id IS NOT NULL
ON DUPLICATE KEY UPDATE is_primary = 1;

-- Note: On garde la colonne dietitian_id dans dietic_patients pour compatibilité
-- Elle servira de "diététicien principal" par défaut
