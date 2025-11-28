-- Phase 9: Recurring Payments & Refunds Migration
-- Created: 28 November 2025

-- ========================================
-- RECURRING PAYMENTS TABLES
-- ========================================

-- Table for recurring payment schedules
CREATE TABLE IF NOT EXISTS `tbldietic_recurring_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `dietitian_id` INT(11) NOT NULL,
  `service_plan_id` INT(11) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'XOF',
  `frequency` ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly') NOT NULL DEFAULT 'monthly',
  `payment_method` VARCHAR(50) NOT NULL COMMENT 'paypal, wave, orange_money, etc.',
  `payment_method_token` TEXT DEFAULT NULL COMMENT 'Token for automatic payment (if applicable)',
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL COMMENT 'NULL = indefinite',
  `next_payment_date` DATE NOT NULL,
  `last_payment_date` DATE DEFAULT NULL,
  `status` ENUM('active', 'paused', 'cancelled', 'expired', 'failed') NOT NULL DEFAULT 'active',
  `retry_count` INT(11) DEFAULT 0,
  `max_retries` INT(11) DEFAULT 3,
  `failure_reason` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `cancelled_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subscription` (`subscription_id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_dietitian` (`dietitian_id`),
  KEY `idx_next_payment` (`next_payment_date`, `status`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for recurring payment history/transactions
CREATE TABLE IF NOT EXISTS `tbldietic_recurring_payment_transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `recurring_payment_id` INT(11) NOT NULL,
  `invoice_id` INT(11) DEFAULT NULL,
  `payment_id` INT(11) DEFAULT NULL,
  `scheduled_date` DATE NOT NULL,
  `processed_date` DATETIME DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'processing', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `attempt_count` INT(11) DEFAULT 0,
  `transaction_id` VARCHAR(255) DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_recurring_payment` (`recurring_payment_id`),
  KEY `idx_invoice` (`invoice_id`),
  KEY `idx_payment` (`payment_id`),
  KEY `idx_scheduled_date` (`scheduled_date`, `status`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- REFUNDS TABLES
-- ========================================

-- Table for refunds
CREATE TABLE IF NOT EXISTS `tbldietic_refunds` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `payment_id` INT(11) NOT NULL,
  `invoice_id` INT(11) NOT NULL,
  `patient_id` INT(11) NOT NULL,
  `dietitian_id` INT(11) NOT NULL,
  `refund_type` ENUM('full', 'partial') NOT NULL,
  `original_amount` DECIMAL(10,2) NOT NULL COMMENT 'Original payment amount',
  `refund_amount` DECIMAL(10,2) NOT NULL COMMENT 'Amount to refund',
  `remaining_amount` DECIMAL(10,2) NOT NULL COMMENT 'Amount remaining after refund',
  `currency` VARCHAR(10) DEFAULT 'XOF',
  `reason` TEXT NOT NULL,
  `status` ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` VARCHAR(50) NOT NULL COMMENT 'Same as original payment',
  `refund_reference` VARCHAR(255) DEFAULT NULL COMMENT 'Reference from payment gateway',
  `transaction_id` VARCHAR(255) DEFAULT NULL COMMENT 'Transaction ID from gateway',
  `initiated_by` INT(11) NOT NULL COMMENT 'Staff ID who initiated refund',
  `approved_by` INT(11) DEFAULT NULL COMMENT 'Staff ID who approved (if applicable)',
  `processed_date` DATETIME DEFAULT NULL,
  `completed_date` DATETIME DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL COMMENT 'Admin notes',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payment` (`payment_id`),
  KEY `idx_invoice` (`invoice_id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_dietitian` (`dietitian_id`),
  KEY `idx_status` (`status`),
  KEY `idx_initiated_by` (`initiated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- SETTINGS FOR RECURRING PAYMENTS
-- ========================================

INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`)
VALUES
    ('recurring_payments_enabled', '1', 'boolean', 'Activer les paiements récurrents'),
    ('recurring_retry_max_attempts', '3', 'number', 'Nombre maximum de tentatives de retry'),
    ('recurring_retry_interval_days', '3', 'number', 'Intervalle en jours entre chaque retry'),
    ('recurring_send_reminder_days', '3', 'number', 'Jours avant paiement pour envoyer un rappel'),
    ('recurring_send_failure_notification', '1', 'boolean', 'Envoyer notification en cas d\'échec'),
    ('refunds_enabled', '1', 'boolean', 'Activer les remboursements'),
    ('refunds_require_approval', '0', 'boolean', 'Les remboursements nécessitent une approbation'),
    ('refunds_auto_update_invoice', '1', 'boolean', 'Mettre à jour automatiquement les factures')
ON DUPLICATE KEY UPDATE
    `setting_value` = VALUES(`setting_value`);

-- ========================================
-- ALTER EXISTING TABLES
-- ========================================

-- Add recurring flag to subscriptions
-- Note: IF NOT EXISTS not supported in MariaDB, errors will be ignored by migration script
ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `is_recurring` TINYINT(1) DEFAULT 0 AFTER `status`;

ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `recurring_payment_id` INT(11) DEFAULT NULL AFTER `is_recurring`;

ALTER TABLE `tbldietic_subscriptions`
ADD INDEX `idx_recurring` (`is_recurring`);

-- Add refunded flag to payments
ALTER TABLE `tbldietic_payments`
ADD COLUMN `refunded_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `status`;

ALTER TABLE `tbldietic_payments`
ADD COLUMN `is_refunded` TINYINT(1) DEFAULT 0 AFTER `refunded_amount`;

ALTER TABLE `tbldietic_payments`
ADD COLUMN `refund_id` INT(11) DEFAULT NULL AFTER `is_refunded`;

ALTER TABLE `tbldietic_payments`
ADD INDEX `idx_refunded` (`is_refunded`);

-- Add refunded amount to invoices
ALTER TABLE `tbldietic_invoices`
ADD COLUMN `refunded_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_amount`;

ALTER TABLE `tbldietic_invoices`
ADD COLUMN `net_amount` DECIMAL(10,2) DEFAULT NULL AFTER `refunded_amount`;

-- Update net_amount for existing invoices
UPDATE `tbldietic_invoices`
SET `net_amount` = `total_amount` - IFNULL(`refunded_amount`, 0)
WHERE `net_amount` IS NULL;

-- ========================================
-- INDEXES FOR PERFORMANCE
-- ========================================

-- Optimize queries for dashboard
-- Note: IF NOT EXISTS not supported in MariaDB, duplicate key errors will be ignored
CREATE INDEX `idx_recurring_active` ON `tbldietic_recurring_payments` (`status`, `next_payment_date`);
CREATE INDEX `idx_refunds_pending` ON `tbldietic_refunds` (`status`, `created_at`);

-- ========================================
-- FOREIGN KEY CONSTRAINTS (Optional but recommended)
-- ========================================

-- Note: Enable foreign keys only if your database supports it and all referenced records exist
-- Uncomment if needed:

-- ALTER TABLE `tbldietic_recurring_payments`
--   ADD CONSTRAINT `fk_recurring_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `tbldietic_subscriptions` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_recurring_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `tbldietic_recurring_payment_transactions`
--   ADD CONSTRAINT `fk_transaction_recurring` FOREIGN KEY (`recurring_payment_id`) REFERENCES `tbldietic_recurring_payments` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `tbldietic_refunds`
--   ADD CONSTRAINT `fk_refund_payment` FOREIGN KEY (`payment_id`) REFERENCES `tbldietic_payments` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_refund_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `tbldietic_invoices` (`id`) ON DELETE CASCADE;
