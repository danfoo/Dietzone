-- Migration: Add Communication Channels to Consultations Table
-- Date: 2025-01-22
-- Description: Adds fields for online consultation platforms (Zoom, Google Meet, Teams, WhatsApp, etc.)

-- Add consultation mode column
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `consultation_mode` VARCHAR(20) DEFAULT 'in_person' COMMENT 'in_person or online' AFTER `consultation_type`;

-- Add online platform column
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `online_platform` VARCHAR(50) DEFAULT NULL COMMENT 'zoom, google_meet, teams, whatsapp, skype, other' AFTER `consultation_mode`;

-- Add meeting link column
ALTER TABLE `tbldietic_consultations`
ADD COLUMN `meeting_link` VARCHAR(500) DEFAULT NULL COMMENT 'URL for online meetings' AFTER `online_platform`;

-- Add index for faster queries
ALTER TABLE `tbldietic_consultations`
ADD INDEX `consultation_mode` (`consultation_mode`);

ALTER TABLE `tbldietic_consultations`
ADD INDEX `online_platform` (`online_platform`);
