-- Script de réinitialisation pour la migration 009
-- À exécuter avant de ré-exécuter la migration si elle a échoué

-- 1. Supprimer l'enregistrement de la migration (pour pouvoir la ré-exécuter)
DELETE FROM `tbldietic_migrations` WHERE migration_name = '009_add_recurring_payments_and_refunds';

-- 2. OPTIONNEL : Supprimer les tables créées (si vous voulez repartir de zéro)
-- Décommentez ces lignes SEULEMENT si vous voulez tout supprimer et recommencer

-- DROP TABLE IF EXISTS `tbldietic_recurring_payment_transactions`;
-- DROP TABLE IF EXISTS `tbldietic_recurring_payments`;
-- DROP TABLE IF EXISTS `tbldietic_refunds`;

-- 3. OPTIONNEL : Supprimer les colonnes ajoutées (si vous voulez repartir de zéro)
-- Décommentez ces lignes SEULEMENT si vous voulez tout supprimer et recommencer

-- ALTER TABLE `tbldietic_subscriptions` DROP COLUMN IF EXISTS `is_recurring`;
-- ALTER TABLE `tbldietic_subscriptions` DROP COLUMN IF EXISTS `recurring_payment_id`;
-- ALTER TABLE `tbldietic_subscriptions` DROP INDEX IF EXISTS `idx_recurring`;

-- ALTER TABLE `tbldietic_payments` DROP COLUMN IF EXISTS `refunded_amount`;
-- ALTER TABLE `tbldietic_payments` DROP COLUMN IF EXISTS `is_refunded`;
-- ALTER TABLE `tbldietic_payments` DROP COLUMN IF EXISTS `refund_id`;
-- ALTER TABLE `tbldietic_payments` DROP INDEX IF EXISTS `idx_refunded`;

-- ALTER TABLE `tbldietic_invoices` DROP COLUMN IF EXISTS `refunded_amount`;
-- ALTER TABLE `tbldietic_invoices` DROP COLUMN IF EXISTS `net_amount`;

-- 4. OPTIONNEL : Supprimer les paramètres (si vous voulez repartir de zéro)
-- Décommentez ces lignes SEULEMENT si vous voulez tout supprimer et recommencer

-- DELETE FROM `tbldietic_settings` WHERE setting_key IN (
--     'recurring_payments_enabled',
--     'recurring_retry_max_attempts',
--     'recurring_retry_interval_days',
--     'recurring_send_reminder_days',
--     'recurring_send_failure_notification',
--     'refunds_enabled',
--     'refunds_require_approval',
--     'refunds_auto_update_invoice'
-- );

-- Message de confirmation
SELECT 'Migration 009 réinitialisée. Vous pouvez maintenant la ré-exécuter depuis l\'interface.' AS message;
