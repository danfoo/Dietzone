-- Migration script to update LAM SMS configuration from old format to new format
-- This script handles existing installations that have old LAM configuration keys

-- Remove old LAM SMS configuration keys (if they exist)
DELETE FROM `tbldietic_notification_settings` WHERE `setting_key` IN ('lam_api_url', 'lam_api_key', 'lam_api_sender', 'sms_lam_api_key');

-- Insert new LAM SMS configuration keys (only if they don't exist)
INSERT IGNORE INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
('sms_lam_account_id', '', 'LAM SMS Account ID', NOW(), NOW()),
('sms_lam_password', '', 'LAM SMS Password', NOW(), NOW()),
('sms_lam_sender_id', 'API_LAMSMS', 'LAM SMS Sender ID', NOW(), NOW()),
('sms_lam_ret_url', '', 'LAM SMS Callback URL', NOW(), NOW()),
('sms_lam_priority', '2', 'LAM SMS Priority (1-3)', NOW(), NOW());

-- Also ensure sms_provider exists
INSERT IGNORE INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
('sms_provider', 'lam', 'SMS Provider: lam or custom', NOW(), NOW());
