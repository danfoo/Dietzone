-- Migration: Add payment settings for Wave and PayPal
-- Date: 2025-12-13

INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('payment_wave_enabled', '0', 'boolean', 'Enable Wave payment gateway'),
('payment_wave_api_key', '', 'text', 'Wave API key'),
('payment_wave_secret_key', '', 'text', 'Wave secret key'),
('payment_wave_merchant_id', '', 'text', 'Wave merchant ID'),
('payment_wave_currency', 'XOF', 'text', 'Wave payment currency (XOF for FCFA)'),
('payment_paypal_enabled', '0', 'boolean', 'Enable PayPal payment gateway'),
('payment_paypal_client_id', '', 'text', 'PayPal client ID'),
('payment_paypal_secret_key', '', 'text', 'PayPal secret key'),
('payment_paypal_mode', 'sandbox', 'text', 'PayPal mode (sandbox or live)'),
('payment_paypal_currency', 'USD', 'text', 'PayPal payment currency')
ON DUPLICATE KEY UPDATE
  `setting_key` = VALUES(`setting_key`);
