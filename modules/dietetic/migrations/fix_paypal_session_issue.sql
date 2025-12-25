-- ================================================================
-- Migration: Fix PayPal Session Issue
-- Date: 2025-12-13
-- Description: Créer une table pour stocker les tokens de paiement
--              au lieu de compter sur la session qui est perdue
--              lors des redirections PayPal
-- ================================================================

-- Table pour stocker les transactions de paiement en cours
CREATE TABLE IF NOT EXISTS `tbldietic_payment_tokens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL COMMENT 'ID de la facture Perfex',
  `client_id` INT(11) NOT NULL COMMENT 'ID du client pour validation',
  `gateway` VARCHAR(50) NOT NULL COMMENT 'paypal, wave, orange_money',
  `order_id` VARCHAR(255) DEFAULT NULL COMMENT 'ID de commande externe (PayPal order_id, etc)',
  `token` VARCHAR(255) DEFAULT NULL COMMENT 'Token de paiement (pour validation)',
  `amount` DECIMAL(10,2) NOT NULL COMMENT 'Montant du paiement',
  `currency` VARCHAR(10) DEFAULT 'XOF' COMMENT 'Devise',
  `status` ENUM('pending', 'processing', 'completed', 'cancelled', 'expired') NOT NULL DEFAULT 'pending',
  `metadata` TEXT DEFAULT NULL COMMENT 'Données JSON supplémentaires',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` DATETIME NOT NULL COMMENT 'Expiration du token (1h par défaut)',
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_invoice_gateway` (`invoice_id`, `gateway`, `status`),
  KEY `idx_invoice` (`invoice_id`),
  KEY `idx_token` (`token`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_status` (`status`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Index pour nettoyage automatique des tokens expirés
CREATE INDEX `idx_cleanup` ON `tbldietic_payment_tokens` (`status`, `expires_at`);

-- ================================================================
-- Commentaire de migration
-- ================================================================
-- Cette table permet de :
-- 1. Stocker l'order_id PayPal en BD au lieu de la session
-- 2. Récupérer les infos lors du callback via invoice_id ou token
-- 3. Éviter les problèmes de session perdue (SameSite cookies)
-- 4. Auto-nettoyage des tokens expirés (cron job)
-- ================================================================
