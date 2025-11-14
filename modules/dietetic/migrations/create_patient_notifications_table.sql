-- Migration: Création table des notifications in-app pour patients
-- Cette table stocke les notifications affichées dans l'interface patient
-- Différente de dietic_notification_logs qui est l'historique d'envoi

CREATE TABLE IF NOT EXISTS `tbldietic_patient_notifications` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_id` INT(11) UNSIGNED NOT NULL,
    `notification_type` VARCHAR(50) NOT NULL DEFAULT 'info',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-bell',
    `url` VARCHAR(500) DEFAULT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `read_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_patient_id` (`patient_id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index composé pour les requêtes fréquentes
CREATE INDEX `idx_patient_unread` ON `tbldietic_patient_notifications` (`patient_id`, `is_read`, `created_at`);

-- Commentaire sur la table
ALTER TABLE `tbldietic_patient_notifications`
COMMENT = 'Notifications in-app affichées dans le panneau de notifications du portail patient';
