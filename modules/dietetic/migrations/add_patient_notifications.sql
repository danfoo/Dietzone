-- Migration to add patient notifications table for frontend
-- This table stores notifications that appear in the patient portal

CREATE TABLE IF NOT EXISTS `tbldietic_patient_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `notification_type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-bell',
  `url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `tbldietic_patient_notifications_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `tbldietic_patients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add index for better performance
CREATE INDEX `idx_patient_unread` ON `tbldietic_patient_notifications` (`patient_id`, `is_read`, `created_at`);
