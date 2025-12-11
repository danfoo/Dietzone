-- Table pour stocker les codes OTP temporaires
-- Utilisée pour la réinitialisation de mot de passe

CREATE TABLE IF NOT EXISTS `tbldietic_otp_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(50) NOT NULL COMMENT 'Numéro de téléphone du destinataire',
  `code` varchar(6) NOT NULL COMMENT 'Code OTP à 6 chiffres',
  `type` varchar(20) NOT NULL DEFAULT 'password_reset' COMMENT 'password_reset, login, etc.',
  `used` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=non utilisé, 1=utilisé',
  `created_at` datetime NOT NULL COMMENT 'Date de création',
  `expires_at` datetime NOT NULL COMMENT 'Date d expiration (5 minutes)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Adresse IP de la demande',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`),
  KEY `code` (`code`),
  KEY `type` (`type`),
  KEY `expires_at` (`expires_at`),
  KEY `used` (`used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
