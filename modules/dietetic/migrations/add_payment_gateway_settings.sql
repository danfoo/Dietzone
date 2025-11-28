-- Payment Gateway Settings Migration
-- Phase 8 - Online Payment Integrations

-- PayPal Settings
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`)
VALUES
    ('paypal_enabled', '0', NOW(), NOW()),
    ('paypal_mode', 'sandbox', NOW(), NOW()),
    ('paypal_client_id', '', NOW(), NOW()),
    ('paypal_secret', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `updated_at` = NOW();

-- Wave Settings
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`)
VALUES
    ('wave_enabled', '0', NOW(), NOW()),
    ('wave_api_key', '', NOW(), NOW()),
    ('wave_merchant_id', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `updated_at` = NOW();

-- Orange Money Settings
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`)
VALUES
    ('orange_money_enabled', '0', NOW(), NOW()),
    ('orange_money_merchant_key', '', NOW(), NOW()),
    ('orange_money_api_url', 'https://api.orange.com/orange-money-webpay/dev/v1', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `updated_at` = NOW();

-- Add transaction_id column to payments table if not exists
ALTER TABLE `tbldietic_payments`
ADD COLUMN IF NOT EXISTS `transaction_id` VARCHAR(255) DEFAULT NULL AFTER `payment_reference`,
ADD INDEX IF NOT EXISTS `idx_transaction_id` (`transaction_id`);

-- Add payment_reference column if not exists
ALTER TABLE `tbldietic_payments`
ADD COLUMN IF NOT EXISTS `payment_reference` VARCHAR(255) DEFAULT NULL AFTER `notes`;
