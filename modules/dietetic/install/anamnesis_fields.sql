-- =====================================================
-- Migration: Ajout des champs d'anamnèse complète
-- Date: 2025-11-24
-- Description: Ajout de tous les champs pour une anamnèse professionnelle
-- =====================================================

-- Informations personnelles complémentaires
ALTER TABLE `tbldietic_patients`
ADD COLUMN `title` VARCHAR(10) NULL COMMENT 'Civilité: M., Mme, Mlle' AFTER `gender`,
ADD COLUMN `occupation` VARCHAR(100) NULL COMMENT 'Profession' AFTER `title`,
ADD COLUMN `address` TEXT NULL COMMENT 'Adresse complète' AFTER `occupation`;

-- Informations spécifiques femmes
ALTER TABLE `tbldietic_patients`
ADD COLUMN `is_pregnant` ENUM('no','yes') DEFAULT 'no' COMMENT 'État de grossesse' AFTER `gender`,
ADD COLUMN `pregnancy_months` TINYINT NULL COMMENT 'Mois de grossesse (1-9)' AFTER `is_pregnant`,
ADD COLUMN `breastfeeding` ENUM('no','yes') DEFAULT 'no' COMMENT 'Allaitement en cours' AFTER `pregnancy_months`,
ADD COLUMN `menstrual_cycle` VARCHAR(50) NULL COMMENT 'Régularité du cycle menstruel' AFTER `breastfeeding`;

-- Antécédents et historique médical
ALTER TABLE `tbldietic_patients`
ADD COLUMN `family_history` TEXT NULL COMMENT 'Antécédents familiaux (diabète, HTA, obésité, etc.)' AFTER `medical_conditions`,
ADD COLUMN `supplements` TEXT NULL COMMENT 'Compléments alimentaires et dosages' AFTER `medications`,
ADD COLUMN `recent_exams` TEXT NULL COMMENT 'Examens médicaux récents et résultats' AFTER `supplements`,
ADD COLUMN `last_blood_test_date` DATE NULL COMMENT 'Date du dernier bilan sanguin' AFTER `recent_exams`,
ADD COLUMN `blood_test_results` TEXT NULL COMMENT 'Résultats analyses (glycémie, cholestérol, etc.)' AFTER `last_blood_test_date`;

-- Système digestif
ALTER TABLE `tbldietic_patients`
ADD COLUMN `digestive_symptoms` TEXT NULL COMMENT 'Symptômes: ballonnements, constipation, diarrhée, reflux' AFTER `allergies`,
ADD COLUMN `bowel_frequency` VARCHAR(50) NULL COMMENT 'Fréquence des selles (X fois/jour ou /semaine)' AFTER `digestive_symptoms`,
ADD COLUMN `water_intake` DECIMAL(4,2) NULL COMMENT 'Consommation eau en litres par jour' AFTER `bowel_frequency`,
ADD COLUMN `food_intolerances` TEXT NULL COMMENT 'Intolérances: lactose, gluten, fructose, etc.' AFTER `water_intake`;

-- Mode de vie détaillé
ALTER TABLE `tbldietic_patients`
ADD COLUMN `sleep_hours` DECIMAL(3,1) NULL COMMENT 'Heures de sommeil par nuit' AFTER `activity_level`,
ADD COLUMN `sleep_quality` ENUM('poor','fair','good','excellent') NULL COMMENT 'Qualité du sommeil' AFTER `sleep_hours`,
ADD COLUMN `stress_level` ENUM('low','moderate','high','very_high') NULL COMMENT 'Niveau de stress perçu' AFTER `sleep_quality`,
ADD COLUMN `smoking` ENUM('no','yes','former') NULL COMMENT 'Statut tabagique' AFTER `stress_level`,
ADD COLUMN `smoking_details` VARCHAR(100) NULL COMMENT 'Détails tabagisme (nb cigarettes/jour, depuis quand)' AFTER `smoking`,
ADD COLUMN `alcohol_consumption` ENUM('never','occasional','regular','frequent') NULL COMMENT 'Fréquence consommation alcool' AFTER `smoking_details`,
ADD COLUMN `alcohol_details` VARCHAR(100) NULL COMMENT 'Type et quantité d\'alcool' AFTER `alcohol_consumption`,
ADD COLUMN `physical_activity_details` TEXT NULL COMMENT 'Type, fréquence et durée activités physiques' AFTER `alcohol_details`,
ADD COLUMN `work_type` ENUM('sedentary','light','moderate','physical','very_physical') NULL COMMENT 'Type de travail' AFTER `physical_activity_details`;

-- Mensurations complémentaires
ALTER TABLE `tbldietic_patients`
ADD COLUMN `waist_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de taille en cm (à l\'ombilic)' AFTER `height`,
ADD COLUMN `hip_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de hanches en cm (point le plus large)' AFTER `waist_circumference`,
ADD COLUMN `neck_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de cou en cm' AFTER `hip_circumference`,
ADD COLUMN `chest_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de poitrine en cm' AFTER `neck_circumference`,
ADD COLUMN `arm_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de bras en cm (biceps)' AFTER `chest_circumference`,
ADD COLUMN `thigh_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de cuisse en cm' AFTER `arm_circumference`,
ADD COLUMN `calf_circumference` DECIMAL(5,2) NULL COMMENT 'Tour de mollet en cm' AFTER `thigh_circumference`;

-- Indices calculés automatiquement
ALTER TABLE `tbldietic_patients`
ADD COLUMN `waist_hip_ratio` DECIMAL(4,3) NULL COMMENT 'Rapport taille/hanches (calculé auto)' AFTER `bmi`,
ADD COLUMN `body_shape` VARCHAR(20) NULL COMMENT 'Morphologie: pomme/poire (calculé auto)' AFTER `waist_hip_ratio`;

-- Métadonnées anamnèse
ALTER TABLE `tbldietic_patients`
ADD COLUMN `anamnesis_completed` TINYINT(1) DEFAULT 0 COMMENT 'Anamnèse complète (1) ou partielle (0)' AFTER `status`,
ADD COLUMN `anamnesis_completion_percentage` TINYINT NULL COMMENT 'Pourcentage de complétion 0-100' AFTER `anamnesis_completed`,
ADD COLUMN `last_anamnesis_update` DATETIME NULL COMMENT 'Date dernière mise à jour anamnèse' AFTER `anamnesis_completion_percentage`;
