-- ========================================
-- RÉINITIALISATION MIGRATION 009
-- À EXÉCUTER VIA phpMyAdmin ou interface SQL
-- ========================================

-- Étape 1 : Supprimer l'enregistrement de la migration
-- Ceci permettra de ré-afficher le bouton "Appliquer"
DELETE FROM `tbldietic_migrations` WHERE migration_name = '009_add_recurring_payments_and_refunds';

-- Message de confirmation
SELECT '✓ Migration 009 réinitialisée. Retournez sur /admin/dietetic/migrations et cliquez sur Appliquer.' AS message;
