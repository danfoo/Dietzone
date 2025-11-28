-- ========================================
-- CRÉATION TABLE DE MIGRATIONS + RESET
-- À EXÉCUTER VIA phpMyAdmin
-- ========================================

-- Étape 1 : Créer la table de migrations si elle n'existe pas
CREATE TABLE IF NOT EXISTS `tbldietic_migrations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `migration_name` varchar(255) NOT NULL,
    `applied_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `migration_name` (`migration_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Étape 2 : Supprimer l'enregistrement de la migration 009 (si existe)
DELETE FROM `tbldietic_migrations` WHERE migration_name = '009_add_recurring_payments_and_refunds';

-- Message de confirmation
SELECT 'Table tbldietic_migrations créée et migration 009 réinitialisée' AS message;
