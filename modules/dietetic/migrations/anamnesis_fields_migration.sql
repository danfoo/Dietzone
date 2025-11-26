-- ============================================
-- MIGRATION: Ajout des champs d'anamnèse complète
-- Date: 2025-01-24
-- Description: Ajout de tous les nouveaux champs pour l'anamnèse détaillée du patient
-- Version: 2.0 (sans dépendances AFTER)
-- ============================================

-- Section 1: Informations Personnelles (ajouts)
ALTER TABLE `tbldietic_patients` ADD COLUMN `title` VARCHAR(10) DEFAULT NULL COMMENT 'Civilité: M., Mme, Mlle';
ALTER TABLE `tbldietic_patients` ADD COLUMN `occupation` VARCHAR(200) DEFAULT NULL COMMENT 'Profession du patient';
ALTER TABLE `tbldietic_patients` ADD COLUMN `work_type` VARCHAR(50) DEFAULT NULL COMMENT 'sedentary, light, moderate, physical, very_physical';
ALTER TABLE `tbldietic_patients` ADD COLUMN `address` TEXT DEFAULT NULL COMMENT 'Adresse complète';

-- Section 1.5: Informations Spécifiques Femmes
ALTER TABLE `tbldietic_patients` ADD COLUMN `is_pregnant` VARCHAR(3) DEFAULT 'no' COMMENT 'yes or no';
ALTER TABLE `tbldietic_patients` ADD COLUMN `pregnancy_months` INT(2) DEFAULT NULL COMMENT '1-9 mois de grossesse';
ALTER TABLE `tbldietic_patients` ADD COLUMN `breastfeeding` VARCHAR(3) DEFAULT 'no' COMMENT 'yes or no';
ALTER TABLE `tbldietic_patients` ADD COLUMN `menstrual_cycle` VARCHAR(20) DEFAULT NULL COMMENT 'regular, irregular, absent, menopause';

-- Section 2: Données Physiques & Mensurations (ajouts)
ALTER TABLE `tbldietic_patients` ADD COLUMN `waist_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de taille en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `hip_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de hanches en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `neck_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de cou en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `chest_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de poitrine en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `arm_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de bras en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `thigh_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de cuisse en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `calf_circumference` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tour de mollet en cm';
ALTER TABLE `tbldietic_patients` ADD COLUMN `physical_activity_details` TEXT DEFAULT NULL COMMENT 'Détails activité physique';

-- Section 3: Antécédents & Historique Médical (ajouts)
ALTER TABLE `tbldietic_patients` ADD COLUMN `supplements` TEXT DEFAULT NULL COMMENT 'Compléments alimentaires & vitamines';
ALTER TABLE `tbldietic_patients` ADD COLUMN `recent_blood_work` TEXT DEFAULT NULL COMMENT 'Dernières analyses sanguines';
ALTER TABLE `tbldietic_patients` ADD COLUMN `weight_history` TEXT DEFAULT NULL COMMENT 'Historique de poids';
ALTER TABLE `tbldietic_patients` ADD COLUMN `previous_diets` TEXT DEFAULT NULL COMMENT 'Régimes précédents et résultats';
ALTER TABLE `tbldietic_patients` ADD COLUMN `weight_gain_triggers` TEXT DEFAULT NULL COMMENT 'Facteurs de prise de poids';
ALTER TABLE `tbldietic_patients` ADD COLUMN `family_history` TEXT DEFAULT NULL COMMENT 'Maladies familiales';
ALTER TABLE `tbldietic_patients` ADD COLUMN `surgeries` TEXT DEFAULT NULL COMMENT 'Chirurgies & hospitalisations';
ALTER TABLE `tbldietic_patients` ADD COLUMN `sleep_hours` DECIMAL(3,1) DEFAULT NULL COMMENT 'Heures de sommeil par nuit';
ALTER TABLE `tbldietic_patients` ADD COLUMN `sleep_quality` VARCHAR(20) DEFAULT NULL COMMENT 'very_good, good, average, poor, very_poor';
ALTER TABLE `tbldietic_patients` ADD COLUMN `stress_level` INT(2) DEFAULT NULL COMMENT 'Niveau de stress 1-10';
ALTER TABLE `tbldietic_patients` ADD COLUMN `mental_health` VARCHAR(20) DEFAULT NULL COMMENT 'excellent, good, moderate, anxiety, depression';
ALTER TABLE `tbldietic_patients` ADD COLUMN `stress_eating` VARCHAR(20) DEFAULT 'no' COMMENT 'no, sometimes, often, always';
ALTER TABLE `tbldietic_patients` ADD COLUMN `eating_disorders_history` TEXT DEFAULT NULL COMMENT 'Historique de troubles alimentaires';

-- Section 4: Système Digestif & Intolérances
ALTER TABLE `tbldietic_patients` ADD COLUMN `digestive_issues` VARCHAR(50) DEFAULT 'none' COMMENT 'none, constipation, diarrhea, bloating, reflux, ibs, multiple';
ALTER TABLE `tbldietic_patients` ADD COLUMN `bowel_frequency` DECIMAL(3,1) DEFAULT NULL COMMENT 'Fréquence des selles par jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `water_intake` DECIMAL(3,1) DEFAULT NULL COMMENT 'Consommation d\'eau en L/jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `digestive_details` TEXT DEFAULT NULL COMMENT 'Détails des problèmes digestifs';
ALTER TABLE `tbldietic_patients` ADD COLUMN `lactose_intolerance` TINYINT(1) DEFAULT 0 COMMENT 'Intolérance au lactose';
ALTER TABLE `tbldietic_patients` ADD COLUMN `gluten_intolerance` TINYINT(1) DEFAULT 0 COMMENT 'Intolérance au gluten/cœliaque';
ALTER TABLE `tbldietic_patients` ADD COLUMN `fructose_intolerance` TINYINT(1) DEFAULT 0 COMMENT 'Intolérance au fructose';
ALTER TABLE `tbldietic_patients` ADD COLUMN `fodmap_sensitivity` TINYINT(1) DEFAULT 0 COMMENT 'Sensibilité aux FODMAPs';
ALTER TABLE `tbldietic_patients` ADD COLUMN `histamine_intolerance` TINYINT(1) DEFAULT 0 COMMENT 'Intolérance à l\'histamine';
ALTER TABLE `tbldietic_patients` ADD COLUMN `caffeine_sensitivity` TINYINT(1) DEFAULT 0 COMMENT 'Sensibilité à la caféine';
ALTER TABLE `tbldietic_patients` ADD COLUMN `food_dislikes` TEXT DEFAULT NULL COMMENT 'Aliments non tolérés/aversions';
ALTER TABLE `tbldietic_patients` ADD COLUMN `favorite_foods` TEXT DEFAULT NULL COMMENT 'Aliments favoris';
ALTER TABLE `tbldietic_patients` ADD COLUMN `cultural_food_preferences` TEXT DEFAULT NULL COMMENT 'Préférences culturelles alimentaires';
ALTER TABLE `tbldietic_patients` ADD COLUMN `cooking_skills` VARCHAR(20) DEFAULT NULL COMMENT 'beginner, basic, intermediate, advanced, professional';

-- Section 5: Mode de Vie Détaillé
ALTER TABLE `tbldietic_patients` ADD COLUMN `meals_per_day` INT(2) DEFAULT NULL COMMENT 'Nombre de repas par jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `snacks_per_day` INT(2) DEFAULT NULL COMMENT 'Collations par jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `breakfast_time` TIME DEFAULT NULL COMMENT 'Heure du petit-déjeuner';
ALTER TABLE `tbldietic_patients` ADD COLUMN `dinner_time` TIME DEFAULT NULL COMMENT 'Heure du dîner';
ALTER TABLE `tbldietic_patients` ADD COLUMN `eating_speed` VARCHAR(20) DEFAULT NULL COMMENT 'very_slow, slow, normal, fast, very_fast';
ALTER TABLE `tbldietic_patients` ADD COLUMN `eating_environment` VARCHAR(30) DEFAULT NULL COMMENT 'table_calm, table_distracted, standing, working';
ALTER TABLE `tbldietic_patients` ADD COLUMN `meal_preparation` VARCHAR(30) DEFAULT NULL COMMENT 'home_fresh, home_batch, mixed, mostly_out, processed';
ALTER TABLE `tbldietic_patients` ADD COLUMN `typical_day_diet` TEXT DEFAULT NULL COMMENT 'Description journée alimentaire typique';
ALTER TABLE `tbldietic_patients` ADD COLUMN `coffee_per_day` INT(2) DEFAULT NULL COMMENT 'Nombre de cafés par jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `tea_per_day` INT(2) DEFAULT NULL COMMENT 'Nombre de thés par jour';
ALTER TABLE `tbldietic_patients` ADD COLUMN `soda_per_week` INT(2) DEFAULT NULL COMMENT 'Nombre de sodas par semaine';
ALTER TABLE `tbldietic_patients` ADD COLUMN `alcohol_per_week` INT(2) DEFAULT NULL COMMENT 'Verres d\'alcool par semaine';
ALTER TABLE `tbldietic_patients` ADD COLUMN `sweet_cravings` VARCHAR(20) DEFAULT NULL COMMENT 'never, rarely, sometimes, often, daily';
ALTER TABLE `tbldietic_patients` ADD COLUMN `salt_preference` VARCHAR(20) DEFAULT NULL COMMENT 'low, normal, high';
ALTER TABLE `tbldietic_patients` ADD COLUMN `smoking` VARCHAR(20) DEFAULT 'no' COMMENT 'no, former, occasional, light, moderate, heavy';
ALTER TABLE `tbldietic_patients` ADD COLUMN `time_for_cooking` VARCHAR(20) DEFAULT NULL COMMENT 'none, 15_30min, 30_60min, 1_2hours, flexible';
ALTER TABLE `tbldietic_patients` ADD COLUMN `budget_level` VARCHAR(20) DEFAULT NULL COMMENT 'tight, moderate, comfortable, high';
ALTER TABLE `tbldietic_patients` ADD COLUMN `barriers_to_change` TEXT DEFAULT NULL COMMENT 'Obstacles au changement alimentaire';
ALTER TABLE `tbldietic_patients` ADD COLUMN `motivation_level` INT(2) DEFAULT 5 COMMENT 'Niveau de motivation 1-10';

-- Note: Cette migration ajoute 60 nouveaux champs pour un suivi d'anamnèse complet et professionnel
-- Le script PHP vérifie automatiquement si les colonnes existent déjà avant de les ajouter
