-- Migration: Enable SMS and WhatsApp for existing patients (if configured)
-- This should be run AFTER configuring LAM SMS and/or WhatsApp API credentials

-- Enable SMS for all existing patients
-- Run this only if you have configured LAM SMS credentials in notification settings
UPDATE tbldietic_notification_preferences
SET channel_sms = 1
WHERE channel_sms = 0;

-- Enable WhatsApp for all existing patients
-- Run this only if you have configured WhatsApp API credentials in notification settings
UPDATE tbldietic_notification_preferences
SET channel_whatsapp = 1
WHERE channel_whatsapp = 0;

-- Check results
SELECT
    p.id as patient_id,
    c.company as patient_name,
    prefs.channel_email,
    prefs.channel_sms,
    prefs.channel_whatsapp,
    prefs.channel_push
FROM tbldietic_patients p
JOIN tblclients c ON c.userid = p.client_id
LEFT JOIN tbldietic_notification_preferences prefs ON prefs.patient_id = p.id
ORDER BY p.id DESC
LIMIT 20;
