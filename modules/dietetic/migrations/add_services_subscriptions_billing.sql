-- =====================================================
-- Migration: Services, Subscriptions & Billing System
-- Description: Add service plans, subscriptions, invoices,
--              payments, commissions and revenue sharing
-- Date: 2025-01-27
-- =====================================================

-- =====================================================
-- 1. SERVICE PLANS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_service_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Plan name (English)',
  `name_fr` varchar(255) DEFAULT NULL COMMENT 'Plan name (French)',
  `description` text DEFAULT NULL COMMENT 'Plan description (English)',
  `description_fr` text DEFAULT NULL COMMENT 'Plan description (French)',
  `features` text DEFAULT NULL COMMENT 'JSON array of features',
  `duration_value` int(11) NOT NULL DEFAULT 1 COMMENT 'Duration number',
  `duration_unit` enum('day','week','month','year') NOT NULL DEFAULT 'month' COMMENT 'Duration unit',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base price',
  `billing_cycle` enum('monthly','yearly') NOT NULL DEFAULT 'monthly' COMMENT 'Billing frequency',
  `trial_days` int(11) DEFAULT 0 COMMENT 'Free trial period in days',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Is plan active',
  `created_by` int(11) NOT NULL COMMENT 'Admin who created',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. SUBSCRIPTIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL COMMENT 'Patient ID',
  `dietitian_id` int(11) NOT NULL COMMENT 'Assigned dietitian',
  `service_plan_id` int(11) NOT NULL COMMENT 'Selected service plan',
  `referral_source` enum('platform','dietitian') NOT NULL DEFAULT 'platform' COMMENT 'Who brought the patient',
  `start_date` date NOT NULL COMMENT 'Subscription start date',
  `end_date` date DEFAULT NULL COMMENT 'Subscription end date',
  `trial_end_date` date DEFAULT NULL COMMENT 'Trial period end date',
  `status` enum('trial','active','expired','cancelled','pending') NOT NULL DEFAULT 'pending' COMMENT 'Subscription status',
  `billing_cycle` enum('monthly','yearly') NOT NULL DEFAULT 'monthly' COMMENT 'Billing frequency',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subscription amount',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax rate (percentage)',
  `next_billing_date` date DEFAULT NULL COMMENT 'Next billing date',
  `cancelled_at` datetime DEFAULT NULL COMMENT 'Cancellation date',
  `cancelled_by` int(11) DEFAULT NULL COMMENT 'Who cancelled (staff_id)',
  `cancellation_reason` text DEFAULT NULL COMMENT 'Cancellation reason',
  `notes` text DEFAULT NULL COMMENT 'Additional notes',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_dietitian_id` (`dietitian_id`),
  KEY `idx_service_plan_id` (`service_plan_id`),
  KEY `idx_status` (`status`),
  KEY `idx_referral_source` (`referral_source`),
  KEY `idx_next_billing_date` (`next_billing_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. INVOICES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) NOT NULL COMMENT 'Related subscription',
  `patient_id` int(11) NOT NULL COMMENT 'Patient ID',
  `dietitian_id` int(11) NOT NULL COMMENT 'Dietitian ID',
  `invoice_number` varchar(50) NOT NULL COMMENT 'Auto-generated invoice number',
  `issue_date` date NOT NULL COMMENT 'Invoice issue date',
  `due_date` date NOT NULL COMMENT 'Payment due date',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal amount (HT)',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax rate (percentage)',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax amount',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount (TTC)',
  `status` enum('draft','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft' COMMENT 'Invoice status',
  `paid_date` datetime DEFAULT NULL COMMENT 'Payment date',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Payment method used',
  `notes` text DEFAULT NULL COMMENT 'Invoice notes',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_invoice_number` (`invoice_number`),
  KEY `idx_subscription_id` (`subscription_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_dietitian_id` (`dietitian_id`),
  KEY `idx_status` (`status`),
  KEY `idx_issue_date` (`issue_date`),
  KEY `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. PAYMENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL COMMENT 'Related invoice',
  `subscription_id` int(11) NOT NULL COMMENT 'Related subscription',
  `patient_id` int(11) NOT NULL COMMENT 'Patient ID',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Payment amount',
  `payment_method` enum('card','bank_transfer','cash','mobile_money','paypal','wave','orange_money') NOT NULL DEFAULT 'cash' COMMENT 'Payment method',
  `payment_date` datetime NOT NULL COMMENT 'Payment date',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT 'External transaction ID',
  `transaction_data` text DEFAULT NULL COMMENT 'JSON data from payment gateway',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `notes` text DEFAULT NULL COMMENT 'Payment notes',
  `recorded_by` int(11) DEFAULT NULL COMMENT 'Staff who recorded payment',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_subscription_id` (`subscription_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. COMMISSION SETTINGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_commission_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referral_source` enum('platform','dietitian') NOT NULL COMMENT 'Who brought the patient',
  `dietitian_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Dietitian commission percentage',
  `platform_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Platform commission percentage',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Is this setting active',
  `effective_from` date NOT NULL COMMENT 'Effective start date',
  `effective_to` date DEFAULT NULL COMMENT 'Effective end date',
  `notes` text DEFAULT NULL COMMENT 'Notes about this setting',
  `created_by` int(11) NOT NULL COMMENT 'Admin who created',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_referral_source` (`referral_source`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_effective_dates` (`effective_from`, `effective_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. REVENUE SHARES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_revenue_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL COMMENT 'Related invoice',
  `payment_id` int(11) DEFAULT NULL COMMENT 'Related payment',
  `subscription_id` int(11) NOT NULL COMMENT 'Related subscription',
  `dietitian_id` int(11) NOT NULL COMMENT 'Dietitian ID',
  `patient_id` int(11) NOT NULL COMMENT 'Patient ID',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total invoice amount (TTC)',
  `dietitian_share` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Dietitian share amount',
  `platform_share` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Platform share amount',
  `dietitian_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Dietitian percentage used',
  `platform_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Platform percentage used',
  `referral_source` enum('platform','dietitian') NOT NULL COMMENT 'Who brought the patient',
  `commission_setting_id` int(11) DEFAULT NULL COMMENT 'Commission setting used',
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Payment status to dietitian',
  `paid_date` datetime DEFAULT NULL COMMENT 'Date when paid to dietitian',
  `payment_reference` varchar(255) DEFAULT NULL COMMENT 'Payment reference to dietitian',
  `notes` text DEFAULT NULL COMMENT 'Notes',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_subscription_id` (`subscription_id`),
  KEY `idx_dietitian_id` (`dietitian_id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_status` (`status`),
  KEY `idx_referral_source` (`referral_source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. COMMISSION PAYMENTS TABLE (Payments to Dietitians)
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_commission_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dietitian_id` int(11) NOT NULL COMMENT 'Dietitian receiving payment',
  `period_start` date NOT NULL COMMENT 'Payment period start',
  `period_end` date NOT NULL COMMENT 'Payment period end',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount paid',
  `revenue_share_ids` text DEFAULT NULL COMMENT 'JSON array of revenue_share IDs included',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Payment method',
  `payment_date` datetime NOT NULL COMMENT 'Payment date',
  `transaction_reference` varchar(255) DEFAULT NULL COMMENT 'Transaction reference',
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `notes` text DEFAULT NULL COMMENT 'Payment notes',
  `processed_by` int(11) DEFAULT NULL COMMENT 'Admin who processed',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dietitian_id` (`dietitian_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_period` (`period_start`, `period_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. INSERT DEFAULT COMMISSION SETTINGS
-- =====================================================
INSERT INTO `tbldietic_commission_settings`
  (`referral_source`, `dietitian_percentage`, `platform_percentage`, `is_active`, `effective_from`, `created_by`, `notes`)
VALUES
  ('platform', 60.00, 40.00, 1, CURDATE(), 1, 'Default: Platform brings patient - 60% dietitian, 40% platform'),
  ('dietitian', 80.00, 20.00, 1, CURDATE(), 1, 'Default: Dietitian brings patient - 80% dietitian, 20% platform')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- =====================================================
-- 9. CREATE INVOICE NUMBER SEQUENCE TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `tbldietic_invoice_sequence` (
  `year` int(4) NOT NULL COMMENT 'Invoice year',
  `last_number` int(11) NOT NULL DEFAULT 0 COMMENT 'Last invoice number for this year',
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. ADD FOREIGN KEY CONSTRAINTS
-- =====================================================

-- Subscriptions foreign keys
ALTER TABLE `tbldietic_subscriptions`
  ADD CONSTRAINT `fk_subscription_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_subscription_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_subscription_plan` FOREIGN KEY (`service_plan_id`) REFERENCES `tbldietic_service_plans` (`id`) ON DELETE RESTRICT;

-- Invoices foreign keys
ALTER TABLE `tbldietic_invoices`
  ADD CONSTRAINT `fk_invoice_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `tbldietic_subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invoice_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invoice_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE RESTRICT;

-- Payments foreign keys
ALTER TABLE `tbldietic_payments`
  ADD CONSTRAINT `fk_payment_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `tbldietic_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payment_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `tbldietic_subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payment_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE;

-- Revenue shares foreign keys
ALTER TABLE `tbldietic_revenue_shares`
  ADD CONSTRAINT `fk_revenue_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `tbldietic_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_revenue_payment` FOREIGN KEY (`payment_id`) REFERENCES `tbldietic_payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_revenue_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `tbldietic_subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_revenue_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_revenue_patient` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_revenue_commission_setting` FOREIGN KEY (`commission_setting_id`) REFERENCES `tbldietic_commission_settings` (`id`) ON DELETE SET NULL;

-- Commission payments foreign keys
ALTER TABLE `tbldietic_commission_payments`
  ADD CONSTRAINT `fk_commission_payment_dietitian` FOREIGN KEY (`dietitian_id`) REFERENCES `tblstaff` (`staffid`) ON DELETE RESTRICT;

-- =====================================================
-- Migration completed successfully
-- =====================================================
