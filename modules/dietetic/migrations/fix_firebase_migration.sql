-- Fix Firebase Migration - Add missing channel_push column
-- This fixes the incomplete Firebase migration

-- Add channel_push column to preferences table
ALTER TABLE `tbldietic_notification_preferences`
ADD COLUMN `channel_push` tinyint(1) DEFAULT 1 AFTER `channel_whatsapp`;

-- Update the channel enum in logs table to include 'push'
ALTER TABLE `tbldietic_notification_logs`
MODIFY COLUMN `channel` enum('email','sms','whatsapp','push') NOT NULL;

-- Success message
SELECT 'Migration Firebase corrigée - colonne channel_push ajoutée' as message;
