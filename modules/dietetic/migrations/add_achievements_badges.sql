-- ================================================================
-- GAMIFICATION SYSTEM - Badges & Achievements
-- Migration SQL pour ajouter le système de badges
-- Date: 2025-01-30
-- ================================================================

-- Table des définitions de badges (master list)
CREATE TABLE IF NOT EXISTS `tbldietic_badge_definitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_key` varchar(50) NOT NULL COMMENT 'Identifiant unique du badge',
  `name` varchar(100) NOT NULL COMMENT 'Nom du badge',
  `description` text COMMENT 'Description du badge',
  `icon` varchar(50) DEFAULT 'fa-trophy' COMMENT 'Icône FontAwesome',
  `color` varchar(20) DEFAULT '#01807B' COMMENT 'Couleur du badge',
  `category` varchar(30) NOT NULL COMMENT 'Catégorie: streak, weight, nutrition, hydration, activity, goal',
  `requirement_type` varchar(30) NOT NULL COMMENT 'Type de condition: days_streak, weight_loss, meals_logged, water_logged, activities, goal_reached',
  `requirement_value` int(11) NOT NULL COMMENT 'Valeur requise pour débloquer',
  `points` int(11) DEFAULT 0 COMMENT 'Points gagnés en débloquant',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Badge actif',
  `display_order` int(11) DEFAULT 0 COMMENT 'Ordre d affichage',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_key` (`badge_key`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des badges débloqués par les patients
CREATE TABLE IF NOT EXISTS `tbldietic_patient_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL COMMENT 'Référence à badge_definitions',
  `unlocked_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de déverrouillage',
  `progress_value` int(11) DEFAULT NULL COMMENT 'Valeur de progression au moment du déblocage',
  `notification_sent` tinyint(1) DEFAULT 0 COMMENT 'Notification envoyée',
  `seen` tinyint(1) DEFAULT 0 COMMENT 'Badge vu par le patient',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_patient_badge` (`patient_id`, `badge_id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_badge` (`badge_id`),
  KEY `idx_unlocked` (`unlocked_at`),
  KEY `idx_seen` (`seen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des points des patients
CREATE TABLE IF NOT EXISTS `tbldietic_patient_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `total_points` int(11) DEFAULT 0 COMMENT 'Total des points accumulés',
  `current_level` varchar(20) DEFAULT 'debutant' COMMENT 'Niveau actuel: debutant, bronze, argent, or, platine, diamant',
  `level_progress` int(11) DEFAULT 0 COMMENT 'Progression dans le niveau actuel',
  `points_today` int(11) DEFAULT 0 COMMENT 'Points gagnés aujourd hui',
  `points_this_week` int(11) DEFAULT 0 COMMENT 'Points gagnés cette semaine',
  `points_this_month` int(11) DEFAULT 0 COMMENT 'Points gagnés ce mois',
  `last_activity_date` date DEFAULT NULL COMMENT 'Dernière activité',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_patient` (`patient_id`),
  KEY `idx_level` (`current_level`),
  KEY `idx_total_points` (`total_points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de l'historique des points
CREATE TABLE IF NOT EXISTS `tbldietic_points_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `points` int(11) NOT NULL COMMENT 'Points gagnés (peut être négatif)',
  `action_type` varchar(50) NOT NULL COMMENT 'Type d action: daily_checkin, meal_logged, water_logged, weigh_in, activity, consultation, badge_unlocked, streak_bonus',
  `action_description` varchar(255) DEFAULT NULL COMMENT 'Description de l action',
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID de référence (badge_id, activity_id, etc.)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_action` (`action_type`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des badges par défaut
INSERT INTO `tbldietic_badge_definitions` (`badge_key`, `name`, `description`, `icon`, `color`, `category`, `requirement_type`, `requirement_value`, `points`, `display_order`) VALUES
-- Badges de Streak (série de jours)
('first_day', 'Premier Pas', 'Bienvenue ! Vous avez commencé votre parcours santé', 'fa-star', '#FFD700', 'streak', 'days_streak', 1, 10, 1),
('week_warrior', 'Guerrier Hebdo', '7 jours consécutifs de suivi - Quelle régularité !', 'fa-fire', '#FF6B6B', 'streak', 'days_streak', 7, 50, 2),
('month_master', 'Maître du Mois', '30 jours consécutifs - Vous êtes sur la bonne voie !', 'fa-calendar-check-o', '#FF8C42', 'streak', 'days_streak', 30, 200, 3),
('unstoppable', 'Inarrêtable', '100 jours consécutifs - Rien ne vous arrête !', 'fa-bolt', '#9B59B6', 'streak', 'days_streak', 100, 500, 4),
('legend', 'Légende', '365 jours consécutifs - Vous êtes une inspiration !', 'fa-crown', '#E74C3C', 'streak', 'days_streak', 365, 1000, 5),

-- Badges de Perte de Poids
('weight_5kg', 'Première Victoire', '5 kg perdus - Excellent début !', 'fa-trophy', '#3498DB', 'weight', 'weight_loss', 5, 100, 10),
('weight_10kg', 'Double Victoire', '10 kg perdus - Vous êtes incroyable !', 'fa-trophy', '#2ECC71', 'weight', 'weight_loss', 10, 200, 11),
('weight_15kg', 'Triple Champion', '15 kg perdus - Quelle transformation !', 'fa-trophy', '#F39C12', 'weight', 'weight_loss', 15, 300, 12),
('weight_20kg', 'Super Champion', '20 kg perdus - Vous êtes un modèle !', 'fa-trophy', '#E67E22', 'weight', 'weight_loss', 20, 400, 13),
('weight_25kg', 'Ultra Champion', '25 kg perdus - Performance exceptionnelle !', 'fa-trophy', '#C0392B', 'weight', 'weight_loss', 25, 500, 14),

-- Badges de Nutrition
('meal_beginner', 'Apprenti Nutrition', '7 jours de repas validés', 'fa-cutlery', '#27AE60', 'nutrition', 'meals_logged', 7, 30, 20),
('meal_expert', 'Expert Nutrition', '30 jours de repas validés', 'fa-leaf', '#16A085', 'nutrition', 'meals_logged', 30, 150, 21),
('meal_master', 'Maître Nutrition', '90 jours de repas validés', 'fa-apple', '#1ABC9C', 'nutrition', 'meals_logged', 90, 400, 22),

-- Badges d'Hydratation
('water_warrior', 'Guerrier de l Eau', '7 jours d hydratation validée', 'fa-tint', '#3498DB', 'hydration', 'water_logged', 7, 30, 30),
('hydration_hero', 'Héros Hydratation', '30 jours d hydratation validée', 'fa-tint', '#2980B9', 'hydration', 'water_logged', 30, 150, 31),
('water_master', 'Maître de l Hydratation', '90 jours d hydratation validée', 'fa-tint', '#1F618D', 'hydration', 'water_logged', 90, 400, 32),

-- Badges d'Activité Physique
('active_start', 'Début Actif', '5 activités physiques enregistrées', 'fa-heartbeat', '#E74C3C', 'activity', 'activities', 5, 50, 40),
('fitness_fan', 'Fan de Fitness', '20 activités physiques enregistrées', 'fa-bicycle', '#C0392B', 'activity', 'activities', 20, 150, 41),
('sport_champion', 'Champion Sportif', '50 activités physiques enregistrées', 'fa-trophy', '#8E44AD', 'activity', 'activities', 50, 300, 42),

-- Badges d'Objectif
('goal_reached', 'Objectif Atteint', 'Vous avez atteint votre poids cible !', 'fa-bullseye', '#2ECC71', 'goal', 'goal_reached', 1, 1000, 50),
('goal_maintained', 'Objectif Maintenu', 'Poids cible maintenu pendant 30 jours', 'fa-check-circle', '#27AE60', 'goal', 'goal_maintained', 30, 500, 51);

-- Insertion des paramètres de niveaux
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('level_debutant_min', '0', NOW()),
('level_bronze_min', '100', NOW()),
('level_argent_min', '300', NOW()),
('level_or_min', '600', NOW()),
('level_platine_min', '1000', NOW()),
('level_diamant_min', '2000', NOW())
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = NOW();

-- Insertion des valeurs de points par action
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('points_daily_checkin', '10', NOW()),
('points_meal_logged', '5', NOW()),
('points_water_logged', '3', NOW()),
('points_weigh_in', '20', NOW()),
('points_activity', '15', NOW()),
('points_consultation', '25', NOW()),
('points_streak_7', '50', NOW()),
('points_streak_30', '200', NOW()),
('points_streak_100', '500', NOW())
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = NOW();
