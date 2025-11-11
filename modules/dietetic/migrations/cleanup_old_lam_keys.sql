-- ============================================================================
-- Script de nettoyage manuel pour les anciennes clés LAM SMS
-- ============================================================================
-- Ce script supprime les anciennes clés de configuration LAM qui causent
-- l'erreur "Duplicate entry 'lam_api_url' for key 'setting_key'"
--
-- INSTRUCTIONS :
-- 1. Copiez tout le contenu de ce fichier
-- 2. Connectez-vous à phpMyAdmin ou votre client MySQL
-- 3. Sélectionnez votre base de données
-- 4. Collez et exécutez ces requêtes
-- ============================================================================

-- Étape 1 : Supprimer toutes les anciennes clés LAM
DELETE FROM `tbldietic_notification_settings`
WHERE `setting_key` IN (
    'lam_api_url',
    'lam_api_key',
    'lam_api_sender',
    'sms_lam_api_key'
);

-- Étape 2 : Vérifier que les suppressions ont réussi
SELECT COUNT(*) as 'Anciennes clés restantes (devrait être 0)'
FROM `tbldietic_notification_settings`
WHERE `setting_key` IN ('lam_api_url', 'lam_api_key', 'lam_api_sender', 'sms_lam_api_key');

-- Étape 3 : Vérifier les nouvelles clés existantes
SELECT `setting_key`, `setting_value`
FROM `tbldietic_notification_settings`
WHERE `setting_key` LIKE 'sms_lam_%' OR `setting_key` = 'sms_provider'
ORDER BY `setting_key`;

-- ============================================================================
-- RÉSULTAT ATTENDU :
-- - "Anciennes clés restantes" devrait afficher : 0
-- - Vous devriez voir les nouvelles clés :
--   sms_lam_account_id, sms_lam_password, sms_lam_sender_id,
--   sms_lam_ret_url, sms_lam_priority
-- ============================================================================
