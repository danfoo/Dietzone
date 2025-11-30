-- ================================================================
-- INSTALLATION COMPLETE DU SYSTÈME BLOG/CONSEILS - DIETZONE
-- ================================================================
-- Date: 30 Novembre 2025
-- Version: 1.0.0
--
-- Ce script crée toutes les tables nécessaires + catégories par défaut
-- Exécutez ce fichier dans phpMyAdmin ou via ligne de commande
-- ================================================================

-- ----------------------------------------------------------------
-- 1. CRÉATION DE LA TABLE DES ARTICLES
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbldietic_blog_articles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `excerpt` text,
    `content` longtext NOT NULL,
    `featured_image` varchar(255) DEFAULT NULL,
    `category` varchar(100) DEFAULT NULL,
    `tags` text,
    `author_id` int(11) NOT NULL COMMENT 'Staff member ID',
    `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
    `views_count` int(11) NOT NULL DEFAULT 0,
    `published_at` datetime DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `author_id` (`author_id`),
    KEY `status` (`status`),
    KEY `category` (`category`),
    KEY `published_at` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 2. CRÉATION DE LA TABLE DES CATÉGORIES
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbldietic_blog_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text,
    `color` varchar(7) DEFAULT '#01807B',
    `icon` varchar(50) DEFAULT 'fa-newspaper-o',
    `order` int(11) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 3. CRÉATION DE LA TABLE DES COMMENTAIRES (pour usage futur)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbldietic_blog_comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `article_id` int(11) NOT NULL,
    `patient_id` int(11) DEFAULT NULL,
    `author_name` varchar(100) NOT NULL,
    `author_email` varchar(100) DEFAULT NULL,
    `content` text NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `article_id` (`article_id`),
    KEY `patient_id` (`patient_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 4. CRÉATION DE LA TABLE DE SUIVI DES VUES
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbldietic_blog_article_views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `article_id` int(11) NOT NULL,
    `patient_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` varchar(255) DEFAULT NULL,
    `viewed_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `article_id` (`article_id`),
    KEY `patient_id` (`patient_id`),
    KEY `viewed_at` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 5. INSERTION DES CATÉGORIES PAR DÉFAUT
-- ----------------------------------------------------------------
INSERT INTO `tbldietic_blog_categories` (`name`, `slug`, `description`, `color`, `icon`, `order`, `created_at`) VALUES
('Nutrition', 'nutrition', 'Conseils et informations sur la nutrition', '#4CAF50', 'fa-apple', 1, NOW()),
('Recettes Santé', 'recettes-sante', 'Recettes saines et équilibrées', '#FF9800', 'fa-cutlery', 2, NOW()),
('Perte de Poids', 'perte-de-poids', 'Conseils pour une perte de poids saine', '#E91E63', 'fa-heart', 3, NOW()),
('Bien-être', 'bien-etre', 'Conseils pour un mode de vie sain', '#9C27B0', 'fa-leaf', 4, NOW()),
('Sport & Activité', 'sport-activite', 'Conseils sur l\'activité physique', '#2196F3', 'fa-heartbeat', 5, NOW());

-- ================================================================
-- INSTALLATION TERMINÉE !
-- ================================================================
-- Tables créées : 4
-- Catégories créées : 5
--
-- PROCHAINES ÉTAPES :
-- 1. Vérifiez que les tables existent : SHOW TABLES LIKE '%dietic_blog%';
-- 2. Chargez les articles de démo : modules/dietetic/sample_blog_data.sql
--    (N'oubliez pas de remplacer author_id par un staff_id valide)
-- 3. Accédez au menu : /admin/dietetic/blog
-- ================================================================
