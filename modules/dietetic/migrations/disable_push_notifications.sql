-- Script pour désactiver les notifications PUSH Firebase
-- Jusqu'à configuration complète de Firebase
-- Date: 1er Décembre 2025

-- Désactiver les push notifications pour tous les patients existants
UPDATE `tbldietic_notification_preferences`
SET `channel_push` = 0,
    `updated_at` = NOW()
WHERE `channel_push` = 1;

-- Désactiver Firebase dans les paramètres globaux
UPDATE `tbldietic_notification_settings`
SET `setting_value` = '0',
    `updated_at` = NOW()
WHERE `setting_key` = 'push_enabled';

-- Afficher le résultat
SELECT
    'Patients avec push désactivé' as Info,
    COUNT(*) as Total
FROM `tbldietic_notification_preferences`
WHERE `channel_push` = 0;

SELECT
    setting_key,
    setting_value,
    description
FROM `tbldietic_notification_settings`
WHERE setting_key = 'push_enabled';
