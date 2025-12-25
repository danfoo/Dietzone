-- Add external_id column to notification_logs table
-- This column stores the external notification ID from providers like OneSignal, Firebase, etc.
-- for tracking and debugging purposes

ALTER TABLE `tbldietic_notification_logs`
ADD COLUMN `external_id` VARCHAR(255) NULL DEFAULT NULL
COMMENT 'External notification ID from OneSignal/Firebase'
AFTER `error_message`;

-- Add index for faster lookups by external_id
ALTER TABLE `tbldietic_notification_logs`
ADD KEY `idx_external_id` (`external_id`);
