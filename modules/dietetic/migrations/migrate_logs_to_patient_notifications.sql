-- =====================================================
-- Migration: Copier notifications_logs vers patient_notifications
-- =====================================================
-- Date: 2025-12-02
-- Objectif: Remplir la table patient_notifications avec les logs existants
--
-- Problème identifié:
--   - 62 notifications dans tbldietic_notification_logs
--   - 0 notifications dans tbldietic_patient_notifications
--   - L'API utilise patient_notifications quand elle existe
--   - Résultat: Tableau vide affiché au patient
--
-- Solution:
--   Copier les notifications des logs vers patient_notifications
--   avec les champs appropriés pour le portail patient
-- =====================================================

-- Étape 1: Insérer les notifications des logs dans patient_notifications
-- On exclut les notifications avec patient_id = 0 (notifications système)
-- et on garde uniquement celles avec status = 'sent'
INSERT INTO `tbldietic_patient_notifications` (
    `patient_id`,
    `notification_type`,
    `title`,
    `message`,
    `icon`,
    `url`,
    `is_read`,
    `created_at`,
    `updated_at`
)
SELECT
    nl.`patient_id`,
    nl.`notification_type`,
    -- Générer un titre basé sur le type
    CASE nl.`notification_type`
        WHEN 'weight_reminder' THEN 'Rappel de Pesée'
        WHEN 'water_reminder' THEN 'Rappel d\'Hydratation'
        WHEN 'recommendation' THEN 'Nouvelle Recommandation'
        WHEN 'consultation' THEN 'Consultation'
        WHEN 'milestone' THEN 'Jalon Atteint'
        WHEN 'program' THEN 'Programme Diététique'
        WHEN 'food_entry' THEN 'Saisie Alimentaire'
        WHEN 'test' THEN 'Test Notification'
        ELSE 'Notification'
    END as `title`,
    nl.`message`,
    -- Générer une icône basée sur le type
    CASE nl.`notification_type`
        WHEN 'weight_reminder' THEN 'fa-balance-scale'
        WHEN 'water_reminder' THEN 'fa-tint'
        WHEN 'recommendation' THEN 'fa-comments'
        WHEN 'consultation' THEN 'fa-calendar'
        WHEN 'milestone' THEN 'fa-trophy'
        WHEN 'program' THEN 'fa-leaf'
        WHEN 'food_entry' THEN 'fa-cutlery'
        WHEN 'test' THEN 'fa-flask'
        ELSE 'fa-bell'
    END as `icon`,
    NULL as `url`, -- Pas d'URL pour les notifications historiques
    0 as `is_read`, -- Toutes marquées comme non lues
    nl.`created_at`,
    NOW() as `updated_at`
FROM `tbldietic_notification_logs` nl
WHERE nl.`patient_id` > 0  -- Exclure les notifications système (patient_id = 0)
  AND nl.`status` = 'sent'  -- Uniquement les notifications envoyées avec succès
  AND NOT EXISTS (
      -- Éviter les doublons si la migration est exécutée plusieurs fois
      SELECT 1
      FROM `tbldietic_patient_notifications` pn
      WHERE pn.`patient_id` = nl.`patient_id`
        AND pn.`notification_type` = nl.`notification_type`
        AND pn.`message` = nl.`message`
        AND pn.`created_at` = nl.`created_at`
  );

-- Étape 2: Afficher le résultat
SELECT
    COUNT(*) as notifications_migrated,
    'Notifications successfully migrated from logs to patient_notifications' as status
FROM `tbldietic_patient_notifications`;

-- Étape 3: Vérifier par patient
SELECT
    patient_id,
    COUNT(*) as notification_count,
    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count
FROM `tbldietic_patient_notifications`
GROUP BY patient_id
ORDER BY patient_id;
