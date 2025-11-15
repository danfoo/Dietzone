-- Migration: Add Firebase Cloud Messaging API v1 Support
-- Date: 2025-11-14
-- Description: Add settings for Firebase HTTP v1 API with Service Account authentication

-- Insert Firebase v1 API settings into notification settings
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`, `updated_at`) VALUES
('firebase_use_v1_api', '0', 'Use Firebase Cloud Messaging API v1 (recommended) instead of Legacy API', NOW()),
('firebase_service_account_json', '', 'Firebase Service Account JSON for API v1 authentication', NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Note: The existing firebase_server_key will continue to work for Legacy API
-- Users can choose between Legacy API (firebase_server_key) or v1 API (firebase_service_account_json)
