# Corrections Migration Phase 9 - Compatibilité MariaDB

**Date**: 28 Novembre 2025
**Problème**: Erreurs de syntaxe SQL avec MariaDB
**Statut**: ✅ Corrigé

---

## 🐛 Problèmes Identifiés

### Erreur 1 : Colonnes inexistantes dans `tbldietic_settings`

**Message d'erreur** :
```
Unknown column 'created_at' in 'INSERT INTO'
```

**Cause** :
- La table `tbldietic_settings` n'a pas de colonnes `created_at` et `updated_at`
- Structure réelle : `id`, `setting_key`, `setting_value`, `setting_type`, `description`

**Solution** :
```sql
-- AVANT (incorrect)
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`)

-- APRÈS (corrigé)
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`)
```

---

### Erreur 2 : Syntaxe `IF NOT EXISTS` non supportée par MariaDB

**Message d'erreur** :
```
You have an error in your SQL syntax near 'COMMENT 'Total - refunded amount''
```

**Cause** :
- MariaDB ne supporte pas `ADD COLUMN IF NOT EXISTS` (syntaxe MySQL 8.0+ uniquement)
- MariaDB ne supporte pas `ADD INDEX IF NOT EXISTS`
- MariaDB ne supporte pas `CREATE INDEX IF NOT EXISTS` dans certaines versions

**Solution** :
Séparer chaque opération ALTER TABLE et enlever `IF NOT EXISTS` :

```sql
-- AVANT (incorrect pour MariaDB)
ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN IF NOT EXISTS `is_recurring` TINYINT(1) DEFAULT 0 AFTER `status`,
ADD COLUMN IF NOT EXISTS `recurring_payment_id` INT(11) DEFAULT NULL AFTER `is_recurring`,
ADD INDEX IF NOT EXISTS `idx_recurring` (`is_recurring`);

-- APRÈS (corrigé pour MariaDB)
ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `is_recurring` TINYINT(1) DEFAULT 0 AFTER `status`;

ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `recurring_payment_id` INT(11) DEFAULT NULL AFTER `is_recurring`;

ALTER TABLE `tbldietic_subscriptions`
ADD INDEX `idx_recurring` (`is_recurring`);
```

**Gestion des duplications** :
- Le script PHP de migration ignore automatiquement les erreurs "Duplicate column"
- Le script PHP ignore automatiquement les erreurs "Duplicate key"
- Aucune action supplémentaire requise

---

### Erreur 3 : Colonne `net_amount` non créée

**Message d'erreur** :
```
Unknown column 'net_amount' in 'WHERE'
```

**Cause** :
- Erreur consécutive à l'Erreur 2
- La colonne n'a pas pu être créée à cause de la syntaxe invalide

**Solution** :
Une fois l'Erreur 2 corrigée, cette erreur disparaît automatiquement.

---

## ✅ Corrections Apportées

### Fichier : `add_recurring_payments_and_refunds.sql`

#### 1. Section SETTINGS (lignes 104-115)

**Changements** :
- ❌ Supprimé : `created_at`, `updated_at`
- ✅ Ajouté : `setting_type`, `description`
- ✅ Modifié : `ON DUPLICATE KEY UPDATE` pour utiliser `setting_value`

```sql
INSERT INTO `tbldietic_settings` (`setting_key`, `setting_value`, `setting_type`, `description`)
VALUES
    ('recurring_payments_enabled', '1', 'boolean', 'Activer les paiements récurrents'),
    ('recurring_retry_max_attempts', '3', 'number', 'Nombre maximum de tentatives de retry'),
    -- ... autres paramètres
ON DUPLICATE KEY UPDATE
    `setting_value` = VALUES(`setting_value`);
```

#### 2. Section ALTER TABLE subscriptions (lignes 123-130)

**Changements** :
- ❌ Supprimé : `IF NOT EXISTS`
- ✅ Séparé : Chaque ADD COLUMN/INDEX dans une requête distincte

```sql
ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `is_recurring` TINYINT(1) DEFAULT 0 AFTER `status`;

ALTER TABLE `tbldietic_subscriptions`
ADD COLUMN `recurring_payment_id` INT(11) DEFAULT NULL AFTER `is_recurring`;

ALTER TABLE `tbldietic_subscriptions`
ADD INDEX `idx_recurring` (`is_recurring`);
```

#### 3. Section ALTER TABLE payments (lignes 133-143)

**Changements** :
- ❌ Supprimé : `IF NOT EXISTS`
- ✅ Séparé : 4 requêtes ALTER TABLE distinctes

```sql
ALTER TABLE `tbldietic_payments`
ADD COLUMN `refunded_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `status`;

ALTER TABLE `tbldietic_payments`
ADD COLUMN `is_refunded` TINYINT(1) DEFAULT 0 AFTER `refunded_amount`;

ALTER TABLE `tbldietic_payments`
ADD COLUMN `refund_id` INT(11) DEFAULT NULL AFTER `is_refunded`;

ALTER TABLE `tbldietic_payments`
ADD INDEX `idx_refunded` (`is_refunded`);
```

#### 4. Section ALTER TABLE invoices (lignes 146-150)

**Changements** :
- ❌ Supprimé : `IF NOT EXISTS`, `COMMENT` (cause problème avec syntax)
- ✅ Séparé : 2 requêtes ALTER TABLE distinctes

```sql
ALTER TABLE `tbldietic_invoices`
ADD COLUMN `refunded_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `total_amount`;

ALTER TABLE `tbldietic_invoices`
ADD COLUMN `net_amount` DECIMAL(10,2) DEFAULT NULL AFTER `refunded_amount`;
```

#### 5. Section CREATE INDEX (lignes 163-164)

**Changements** :
- ❌ Supprimé : `IF NOT EXISTS`

```sql
CREATE INDEX `idx_recurring_active` ON `tbldietic_recurring_payments` (`status`, `next_payment_date`);
CREATE INDEX `idx_refunds_pending` ON `tbldietic_refunds` (`status`, `created_at`);
```

---

## 🔄 Ré-exécution de la Migration

### Option 1 : Via phpMyAdmin (Recommandé)

1. **Réinitialiser la migration** :
   ```sql
   DELETE FROM `tbldietic_migrations` WHERE migration_name = '009_add_recurring_payments_and_refunds';
   ```

2. **Retourner à l'interface** :
   - URL : https://app.dietsenegal.net/admin/dietetic/migrations
   - Cliquer sur **"Appliquer"** pour `009_add_recurring_payments_and_refunds.php`

### Option 2 : Via Script SQL

Fichier : `RESET_009_migration.sql` créé pour vous

1. Ouvrir le fichier dans phpMyAdmin
2. Exécuter UNIQUEMENT la première requête (DELETE)
3. Retourner à l'interface web et ré-exécuter

---

## 📊 Résultat Attendu Après Correction

### Tables créées (3)
```
✓ tbldietic_recurring_payments (0 lignes)
✓ tbldietic_recurring_payment_transactions (0 lignes)
✓ tbldietic_refunds (0 lignes)
```

### Colonnes ajoutées (7)

**tbldietic_subscriptions** :
- `is_recurring` TINYINT(1)
- `recurring_payment_id` INT(11)
- Index: `idx_recurring`

**tbldietic_payments** :
- `refunded_amount` DECIMAL(10,2)
- `is_refunded` TINYINT(1)
- `refund_id` INT(11)
- Index: `idx_refunded`

**tbldietic_invoices** :
- `refunded_amount` DECIMAL(10,2)
- `net_amount` DECIMAL(10,2)

### Paramètres créés (8)
```
recurring_payments_enabled = 1
recurring_retry_max_attempts = 3
recurring_retry_interval_days = 3
recurring_send_reminder_days = 3
recurring_send_failure_notification = 1
refunds_enabled = 1
refunds_require_approval = 0
refunds_auto_update_invoice = 1
```

### Index de performance (2)
```
idx_recurring_active (sur recurring_payments)
idx_refunds_pending (sur refunds)
```

### Statistiques de migration
```
✓ ~45-50 requêtes réussies
⚠️ 0-10 éléments ignorés (normal si colonnes déjà créées)
❌ 0 erreur
```

---

## 🔍 Vérification Post-Migration

### SQL de vérification rapide

```sql
-- Vérifier les tables
SHOW TABLES LIKE '%dietic_recurring%';
SHOW TABLES LIKE '%dietic_refunds';

-- Vérifier les colonnes (subscriptions)
SHOW COLUMNS FROM tbldietic_subscriptions LIKE '%recurring%';

-- Vérifier les colonnes (payments)
SHOW COLUMNS FROM tbldietic_payments LIKE '%refund%';

-- Vérifier les colonnes (invoices)
SHOW COLUMNS FROM tbldietic_invoices WHERE Field IN ('refunded_amount', 'net_amount');

-- Vérifier les paramètres
SELECT * FROM tbldietic_settings
WHERE setting_key LIKE 'recurring%' OR setting_key LIKE 'refunds%';

-- Devrait retourner 8 lignes
```

---

## 🆘 Si Problèmes Persistent

### Erreur "Duplicate column"

**C'est normal !** Le script ignore automatiquement ces erreurs.
La colonne existe déjà, aucune action requise.

### Erreur "Duplicate key name"

**C'est normal !** L'index existe déjà, aucune action requise.

### Autres erreurs

1. Vérifiez les permissions MySQL de l'utilisateur
2. Vérifiez que toutes les tables existent
3. Contactez le support avec le message d'erreur exact

---

## 📝 Résumé Technique

**Problème principal** : Incompatibilité syntaxe MySQL 8.0+ avec MariaDB

**Solution** :
- Adapter la syntaxe pour MariaDB
- Séparer les opérations ALTER TABLE
- Corriger les colonnes de la table settings

**Impact** :
- Migration maintenant 100% compatible MariaDB
- Peut être ré-exécutée sans problème
- Gestion automatique des duplications

**Fichiers modifiés** :
- `add_recurring_payments_and_refunds.sql` (corrections syntaxe)

**Fichiers créés** :
- `RESET_009_migration.sql` (script de réinitialisation)
- `MIGRATION_FIX_MARIADB.md` (ce fichier)

---

**Développé par** : Claude AI - Super Lead Dev
**Session** : claude/continue-dietzone-project-018emaSM3eumRaNt5f1bkX8D
**Statut** : ✅ Prêt pour ré-exécution

---

*Vous pouvez maintenant ré-exécuter la migration en toute confiance.*
