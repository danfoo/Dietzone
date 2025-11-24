-- ============================================
-- CORRECTION: Ajout des 6 champs manquants
-- Date: 2025-01-24
-- ============================================

-- Section 1: title (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `title` VARCHAR(10) DEFAULT NULL COMMENT 'Civilité: M., Mme, Mlle';

-- Section 1.5: is_pregnant (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `is_pregnant` VARCHAR(3) DEFAULT 'no' COMMENT 'yes or no';

-- Section 2: waist_circumference (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `waist_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de taille en cm';

-- Section 3: supplements (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `supplements` TEXT DEFAULT NULL COMMENT 'Compléments alimentaires & vitamines';

-- Section 4: digestive_issues (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `digestive_issues` VARCHAR(50) DEFAULT 'none' COMMENT 'none, constipation, diarrhea, bloating, reflux, ibs, multiple';

-- Section 5: meals_per_day (manquant)
ALTER TABLE `tbldietic_patients` ADD COLUMN `meals_per_day` INT(2) DEFAULT NULL COMMENT 'Nombre de repas par jour';

-- Note: Ce script corrige les 6 champs qui n'ont pas été ajoutés lors de la migration initiale
