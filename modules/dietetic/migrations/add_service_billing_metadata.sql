-- =====================================================
-- Migration: Add Service Billing Metadata to Items
-- Description: Add fields for service duration, discounts,
--              and program association
-- Date: 2025-01-XX
-- =====================================================

-- Add service billing metadata to items table
ALTER TABLE `tblitems`
ADD COLUMN `service_duration_months` INT DEFAULT 1 COMMENT 'Durée du service en mois (par défaut)',
ADD COLUMN `service_type` ENUM('consultation', 'program', 'addon') DEFAULT 'program' COMMENT 'Type de service',
ADD COLUMN `service_discount_6_months` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Réduction pour 6 mois (pourcentage)',
ADD COLUMN `service_discount_12_months` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Réduction pour 1 an (pourcentage)',
ADD COLUMN `service_required_specialty` VARCHAR(100) DEFAULT NULL COMMENT 'Spécialité requise du diététicien',
ADD COLUMN `service_is_recurring` TINYINT(1) DEFAULT 1 COMMENT 'Service avec facturation récurrente (1=oui, 0=non)';

-- Add billing fields to programs table
ALTER TABLE `tbldietic_programs`
ADD COLUMN `service_id` INT DEFAULT NULL COMMENT 'Service lié (depuis tblitems)',
ADD COLUMN `duration_months` INT DEFAULT NULL COMMENT 'Durée choisie du programme (mois)',
ADD COLUMN `monthly_price` DECIMAL(15,2) DEFAULT NULL COMMENT 'Prix mensuel',
ADD COLUMN `total_price` DECIMAL(15,2) DEFAULT NULL COMMENT 'Prix total avec réductions',
ADD COLUMN `discount_applied` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Réduction appliquée (pourcentage)',
ADD COLUMN `billing_status` ENUM('pending', 'active', 'suspended', 'cancelled') DEFAULT 'pending' COMMENT 'Statut de facturation',
ADD COLUMN `next_billing_date` DATE DEFAULT NULL COMMENT 'Prochaine date de facturation',
ADD COLUMN `last_billing_date` DATE DEFAULT NULL COMMENT 'Dernière date de facturation',
ADD COLUMN `grace_period_end` DATE DEFAULT NULL COMMENT 'Fin de la période de grâce (7 jours après expiration)',
ADD COLUMN `total_invoices_expected` INT DEFAULT NULL COMMENT 'Nombre total de factures attendues',
ADD COLUMN `total_invoices_paid` INT DEFAULT 0 COMMENT 'Nombre de factures payées';

-- Add index for billing queries
ALTER TABLE `tbldietic_programs`
ADD INDEX `idx_billing_status` (`billing_status`),
ADD INDEX `idx_next_billing_date` (`next_billing_date`),
ADD INDEX `idx_service_id` (`service_id`);

-- =====================================================
-- Migration completed successfully
-- L'admin créera les services directement dans Perfex
-- via Setup → Items avec les réductions configurées
-- =====================================================
