-- Script SQL pour réinitialiser les templates de notifications de refus
-- À exécuter si les SMS de refus affichent "confirmation" au lieu de "refus"

-- Supprimer les anciens templates de refus (s'ils existent)
DELETE FROM tbldietic_notification_settings
WHERE setting_name IN (
    'template_appointment_rejected_subject',
    'template_appointment_rejected_body',
    'template_appointment_rejected_sms_body',
    'template_appointment_rejected_whatsapp_body'
);

-- Réinsérer les templates corrects
INSERT INTO tbldietic_notification_settings (setting_name, setting_value, created_at) VALUES
('template_appointment_rejected_subject', 'Demande de rendez-vous non acceptée', NOW()),
('template_appointment_rejected_body', 'Bonjour {patient_name},\n\n❌ Votre demande de rendez-vous n\'a pas pu être acceptée.\n\n📆 Date demandée : {consultation_date}\n👨‍⚕️ Diététicien : {dietitian_name}\n\nRaison : {reason}\n\nVeuillez contacter votre diététicien ou proposer une autre date.\n📞 Nous restons à votre disposition.', NOW()),
('template_appointment_rejected_sms_body', '{first_name}, RDV {date} REFUSE. Proposer nouvelle date', NOW()),
('template_appointment_rejected_whatsapp_body', '{first_name}, RDV {date} REFUSE. Proposer nouvelle date', NOW());

-- Vérifier les templates actuels
SELECT setting_name, setting_value
FROM tbldietic_notification_settings
WHERE setting_name LIKE '%appointment_%'
ORDER BY setting_name;
