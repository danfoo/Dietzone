-- =========================================================
-- Migration: Firebase → OneSignal Push Notifications
-- Date: 2025-12-04
-- Description: Ajoute le support OneSignal pour Median
-- =========================================================

-- Step 1: Ajouter la colonne onesignal_player_id à la table des tokens
-- Cette colonne stocke le Player ID OneSignal pour chaque appareil
ALTER TABLE `tbldietic_fcm_tokens`
ADD COLUMN `onesignal_player_id` VARCHAR(255) NULL AFTER `token`,
ADD INDEX `idx_player_id` (`onesignal_player_id`);

-- Step 2: Ajouter les settings OneSignal
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`) VALUES
('onesignal_app_id', '', 'OneSignal App ID (xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)'),
('onesignal_rest_api_key', '', 'OneSignal REST API Key (commence par OS-...)'),
('onesignal_user_auth_key', '', 'OneSignal User Auth Key (optionnel, pour admin API)'),
('onesignal_web_enabled', '1', 'Enable OneSignal for Web Push (1=enabled, 0=disabled)')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- Step 3: Créer une table pour mapper Firebase tokens → OneSignal Player IDs (optionnel)
CREATE TABLE IF NOT EXISTS `tbldietic_onesignal_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firebase_token` varchar(255) NOT NULL,
  `onesignal_player_id` varchar(255) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `migrated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`firebase_token`),
  KEY `idx_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Mettre à jour la description de push_enabled pour indiquer OneSignal
UPDATE `tbldietic_notification_settings`
SET `description` = 'Enable Push Notifications via OneSignal (1=enabled, 0=disabled)'
WHERE `setting_key` = 'push_enabled';

-- Step 5: Ajouter un commentaire sur la table pour tracking
ALTER TABLE `tbldietic_fcm_tokens`
COMMENT = 'Stores device tokens for push notifications (Firebase legacy + OneSignal)';

-- Step 6: Nettoyer les tokens Firebase inactifs (optionnel - décommenter si nécessaire)
-- DELETE FROM `tbldietic_fcm_tokens`
-- WHERE `is_active` = 0
-- AND `updated_at` < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- =========================================================
-- Notes de migration:
--
-- 1. Cette migration est NON-DESTRUCTIVE
--    - Conserve tous les tokens Firebase existants
--    - Ajoute simplement une colonne pour OneSignal
--    - Permet de fonctionner en mode hybride pendant la transition
--
-- 2. Après migration, vous pouvez:
--    - Option A: Garder Firebase pour Web + OneSignal pour Mobile
--    - Option B: Migrer complètement vers OneSignal (Web + Mobile)
--
-- 3. Pour Option B (migration complète):
--    - Configurer OneSignal Web SDK dans le frontend
--    - Demander aux patients de réactiver les notifications
--    - Les nouveaux Player IDs seront automatiquement enregistrés
--
-- 4. Rollback possible:
--    - ALTER TABLE `tbldietic_fcm_tokens` DROP COLUMN `onesignal_player_id`;
--    - DELETE FROM `tbldietic_notification_settings` WHERE setting_key LIKE 'onesignal%';
--    - DROP TABLE `tbldietic_onesignal_migration`;
-- =========================================================

-- Afficher un résumé après migration
SELECT
    'Migration terminée' AS status,
    COUNT(*) AS total_devices,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_devices,
    SUM(CASE WHEN onesignal_player_id IS NOT NULL THEN 1 ELSE 0 END) AS onesignal_registered
FROM `tbldietic_fcm_tokens`;
