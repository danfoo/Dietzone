-- Migration: Add Notifications and Milestones System
-- Date: 2025-11-08
-- Description: Tables for notification preferences, logs, and milestones tracking

-- Table for notification preferences per patient
CREATE TABLE IF NOT EXISTS `tbldietic_notification_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `reminder_weight` tinyint(1) DEFAULT 1 COMMENT 'Weekly weight reminder',
  `reminder_weight_day` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT 'friday',
  `reminder_weight_time` time DEFAULT '09:00:00',
  `reminder_water` tinyint(1) DEFAULT 1 COMMENT 'Daily water reminders',
  `reminder_water_times` varchar(50) DEFAULT '10:00,14:00,18:00' COMMENT 'Comma-separated times',
  `notify_recommendation` tinyint(1) DEFAULT 1 COMMENT 'Notify when dietitian adds recommendation',
  `notify_consultation` tinyint(1) DEFAULT 1 COMMENT 'Notify for new consultation',
  `notify_milestone` tinyint(1) DEFAULT 1 COMMENT 'Celebrate milestones',
  `notify_program` tinyint(1) DEFAULT 1 COMMENT 'Notify for program assignments and updates',
  `notify_food_entry` tinyint(1) DEFAULT 1 COMMENT 'Remind to submit daily food entry',
  `channel_email` tinyint(1) DEFAULT 1,
  `channel_sms` tinyint(1) DEFAULT 0,
  `channel_whatsapp` tinyint(1) DEFAULT 0,
  `channel_push` tinyint(1) DEFAULT 1 COMMENT 'Firebase push notifications',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_id` (`patient_id`),
  KEY `idx_patient` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for notification logs
CREATE TABLE IF NOT EXISTS `tbldietic_notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `notification_type` enum('reminder_weight','reminder_water','milestone','recommendation_added','comment_added','consultation_scheduled','consultation_reminder_day','consultation_reminder_hour','consultation_cancelled','program_assigned','program_updated','program_ending','food_entry_reminder','test') NOT NULL,
  `channel` enum('email','sms','whatsapp') NOT NULL,
  `recipient` varchar(255) NOT NULL COMMENT 'Email, phone number, or WhatsApp ID',
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `sent_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL COMMENT 'Related entity ID (consultation_id, recommendation_id, etc.)',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_type` (`notification_type`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for milestones achieved
CREATE TABLE IF NOT EXISTS `tbldietic_milestones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `milestone_type` enum('weight_loss_5kg','weight_loss_10kg','weight_loss_15kg','weight_loss_20kg','weight_loss_25kg','weight_goal_reached','program_completed','consistent_7days','consistent_30days') NOT NULL,
  `achieved_at` datetime NOT NULL,
  `starting_weight` decimal(5,2) DEFAULT NULL,
  `current_weight` decimal(5,2) DEFAULT NULL,
  `weight_lost` decimal(5,2) DEFAULT NULL,
  `notified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_patient` (`patient_id`),
  KEY `idx_type` (`milestone_type`),
  KEY `idx_notified` (`notified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for SMS/WhatsApp provider settings
CREATE TABLE IF NOT EXISTS `tbldietic_notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default SMS/WhatsApp settings
INSERT INTO `tbldietic_notification_settings` (`setting_key`, `setting_value`, `description`, `updated_at`) VALUES
('sms_provider', 'lam', 'SMS Provider: lam or custom', NOW()),
('sms_lam_account_id', '', 'LAM SMS Account ID', NOW()),
('sms_lam_password', '', 'LAM SMS Password', NOW()),
('sms_lam_sender_id', 'API_LAMSMS', 'LAM SMS Sender ID', NOW()),
('sms_lam_ret_url', '', 'LAM SMS Callback URL', NOW()),
('sms_lam_priority', '2', 'LAM SMS Priority (1-3)', NOW()),
('whatsapp_provider', 'twilio', 'WhatsApp Provider: twilio, meta, or custom', NOW()),
('whatsapp_api_key', '', 'WhatsApp API Key', NOW()),
('whatsapp_phone_number', '', 'WhatsApp Business Phone Number', NOW()),
('notifications_enabled', '1', 'Master switch for all notifications', NOW());
