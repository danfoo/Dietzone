-- Script SQL pour supprimer les tables du système de facturation custom
-- À exécuter UNIQUEMENT après avoir confirmé la suppression des fichiers

-- ⚠️  ATTENTION: Cette action est IRRÉVERSIBLE
-- Assurez-vous d'avoir une sauvegarde complète avant d'exécuter ce script

SET FOREIGN_KEY_CHECKS = 0;

-- Tables de facturation à supprimer
DROP TABLE IF EXISTS `tbldietic_service_plans`;
DROP TABLE IF EXISTS `tbldietic_subscriptions`;
DROP TABLE IF EXISTS `tbldietic_invoices`;
DROP TABLE IF EXISTS `tbldietic_invoice_items`;
DROP TABLE IF EXISTS `tbldietic_payments`;
DROP TABLE IF EXISTS `tbldietic_recurring_payments`;
DROP TABLE IF EXISTS `tbldietic_recurring_payment_transactions`;
DROP TABLE IF EXISTS `tbldietic_refunds`;
DROP TABLE IF EXISTS `tbldietic_commission_settings`;
DROP TABLE IF EXISTS `tbldietic_revenue_shares`;

SET FOREIGN_KEY_CHECKS = 1;

-- Vérification
SELECT 'Tables supprimées avec succès !' as Status;
SELECT 'Vérifiez qu\'aucune table dietic_* de facturation n\'existe :' as Message;
SHOW TABLES LIKE 'tbldietic_%';
