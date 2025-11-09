-- Migration: Add Firebase Push Notifications Support
-- Date: 2025-11-09
-- Description: Tables for Firebase Cloud Messaging (FCM) tokens and push notification delivery

-- Table for storing device FCM tokens
CREATE TABLE IF NOT EXISTS `tbldietic_fcm_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL COMMENT 'FCM device token',
  `device_type` enum('web','android','ios') DEFAULT 'web',
  `device_name` varchar(100) DEFAULT NULL COMMENT 'Browser or device name',
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`token`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_device_type` (`device_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add push notification channel to existing notification_logs table
-- This is safe to run multiple times (IF NOT EXISTS pattern)
ALTER TABLE `tbldietic_notification_logs`
MODIFY COLUMN `channel` enum('email','sms','whatsapp','push') NOT NULL;

-- Insert Firebase settings into notification settings
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`, `updated_at`) VALUES
('push_enabled', '0', 'Enable Firebase Push Notifications', NOW()),
('firebase_api_key', '', 'Firebase Web API Key', NOW()),
('firebase_auth_domain', '', 'Firebase Auth Domain', NOW()),
('firebase_project_id', '', 'Firebase Project ID', NOW()),
('firebase_storage_bucket', '', 'Firebase Storage Bucket', NOW()),
('firebase_messaging_sender_id', '', 'Firebase Messaging Sender ID', NOW()),
('firebase_app_id', '', 'Firebase App ID', NOW()),
('firebase_vapid_key', '', 'Firebase VAPID Key for Web Push', NOW()),
('firebase_server_key', '', 'Firebase Server Key (Legacy) for FCM API', NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
