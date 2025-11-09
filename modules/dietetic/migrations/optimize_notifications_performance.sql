-- Performance Optimization for Notifications System
-- Date: 2025-11-09
-- Description: Add composite indexes for common queries

-- Optimize notification_logs for frequent queries
-- Most common query: Get recent notifications for a patient
ALTER TABLE `tbldietic_notification_logs`
ADD INDEX `idx_patient_created` (`patient_id`, `created_at` DESC);

-- Query: Get failed notifications for retry
ALTER TABLE `tbldietic_notification_logs`
ADD INDEX `idx_status_created` (`status`, `created_at` DESC);

-- Query: Get notifications by type and patient
ALTER TABLE `tbldietic_notification_logs`
ADD INDEX `idx_patient_type` (`patient_id`, `notification_type`);

-- Optimize preferences lookups by reminder settings
ALTER TABLE `tbldietic_notification_preferences`
ADD INDEX `idx_reminder_weight` (`reminder_weight`, `reminder_weight_day`);

ALTER TABLE `tbldietic_notification_preferences`
ADD INDEX `idx_reminder_water` (`reminder_water`);

-- Optimize FCM tokens for common queries
-- Query: Get active tokens for a patient
ALTER TABLE `tbldietic_fcm_tokens`
ADD INDEX `idx_patient_active` (`patient_id`, `is_active`);

-- Query: Clean up old inactive tokens
ALTER TABLE `tbldietic_fcm_tokens`
ADD INDEX `idx_active_updated` (`is_active`, `updated_at`);

-- Optimize milestones queries
ALTER TABLE `tbldietic_milestones`
ADD INDEX `idx_patient_achieved` (`patient_id`, `achieved_at` DESC);

ALTER TABLE `tbldietic_milestones`
ADD INDEX `idx_patient_notified` (`patient_id`, `notified`);
