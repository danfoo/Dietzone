# Guide de Migration - Paiements Récurrents & Remboursements

**Date**: 28 Novembre 2025
**Version**: 1.6.0
**Phase**: 9

---

## 📋 Vue d'ensemble

Ce guide explique comment exécuter la migration qui ajoute les fonctionnalités de **Paiements Récurrents** et **Remboursements** à votre système Dietetic.

---

## ⚠️ Avertissements Importants

### Avant de Commencer

1. **Sauvegarde obligatoire** : Faites une sauvegarde complète de votre base de données avant d'exécuter cette migration.
2. **Accès administrateur requis** : Seuls les administrateurs peuvent exécuter des migrations.
3. **Opération irréversible** : Cette migration modifie la structure de la base de données de manière permanente.
4. **Temps d'exécution** : La migration peut prendre 30-60 secondes selon la taille de votre base de données.

---

## 🔒 Sécurité - Tokens CSRF

### Protection CSRF Intégrée

La page de migrations est **automatiquement protégée contre les attaques CSRF** :

✅ **Token CSRF automatique** : Chaque formulaire de migration contient un token CSRF unique
✅ **Vérification côté serveur** : CodeIgniter vérifie automatiquement le token avant d'exécuter la migration
✅ **Session sécurisée** : Seuls les administrateurs connectés peuvent accéder à la page

### Vérification du Token

Le token est généré et vérifié automatiquement via :

```php
<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
```

**Aucune action supplémentaire n'est requise de votre part.**

---

## 📦 Ce que Cette Migration Ajoute

### Nouvelles Tables (3)

1. **`tbldietic_recurring_payments`**
   - Gestion des planifications de paiements récurrents
   - Champs : ID, subscription_id, patient_id, amount, frequency, status, etc.
   - **Indexée** pour des performances optimales

2. **`tbldietic_recurring_payment_transactions`**
   - Historique complet de toutes les transactions
   - Champs : ID, recurring_payment_id, invoice_id, scheduled_date, status, etc.
   - **Indexée** sur les dates et statuts

3. **`tbldietic_refunds`**
   - Gestion des demandes de remboursement
   - Champs : ID, payment_id, refund_amount, refund_type, status, approved_by, etc.
   - **Workflow d'approbation** intégré

### Tables Modifiées (3)

1. **`tbldietic_subscriptions`**
   - Ajout : `is_recurring` (flag)
   - Ajout : `recurring_payment_id` (lien vers paiement récurrent)

2. **`tbldietic_payments`**
   - Ajout : `refunded_amount` (montant remboursé)
   - Ajout : `is_refunded` (flag)
   - Ajout : `refund_id` (lien vers remboursement)

3. **`tbldietic_invoices`**
   - Ajout : `refunded_amount` (montant remboursé)
   - Ajout : `net_amount` (montant total - remboursements)

### Nouveaux Paramètres (8)

Configuration automatique dans `tbldietic_settings` :

| Paramètre | Valeur par défaut | Description |
|-----------|-------------------|-------------|
| `recurring_payments_enabled` | 1 | Activer les paiements récurrents |
| `recurring_retry_max_attempts` | 3 | Nombre max de tentatives |
| `recurring_retry_interval_days` | 3 | Jours entre chaque tentative |
| `recurring_send_reminder_days` | 3 | Jours avant paiement pour rappel |
| `recurring_send_failure_notification` | 1 | Notifier en cas d'échec |
| `refunds_enabled` | 1 | Activer les remboursements |
| `refunds_require_approval` | 0 | Nécessite approbation admin (0=non) |
| `refunds_auto_update_invoice` | 1 | Mettre à jour facture automatiquement |

---

## 🚀 Procédure d'Exécution

### Étape 1 : Accéder à la Page de Migrations

1. Connectez-vous en tant qu'**administrateur**
2. Naviguez vers : **Diététique → Migrations**
3. URL directe : `https://app.dietsenegal.net/admin/dietetic/migrations`

### Étape 2 : Localiser la Migration

Dans la liste des migrations, recherchez :

```
Nom : 009 Add Recurring Payments And Refunds
Fichier : 009_add_recurring_payments_and_refunds.php
Statut : En attente (label gris)
```

### Étape 3 : Exécuter la Migration

1. Cliquez sur le bouton **"Appliquer"** (bleu)
2. **Confirmez** dans la boîte de dialogue :
   ```
   Êtes-vous sûr de vouloir appliquer cette migration ?

   009_add_recurring_payments_and_refunds.php

   Cette action est irréversible.
   ```
3. Cliquez sur **"OK"**

### Étape 4 : Vérifier l'Exécution

La page affichera un rapport détaillé :

#### Rapport de Migration - Sections

**Section 1 : Exécution des requêtes SQL**
- Liste de toutes les requêtes exécutées
- Icônes colorées :
  - ✅ Vert : Table créée
  - 🔧 Bleu : Table modifiée
  - ➕ Bleu : Données insérées
  - ⚡ Gris : Index créé
  - ⚠️ Orange : Élément ignoré (déjà existant)
  - ❌ Rouge : Erreur

**Section 2 : Résumé de la migration**
- Requêtes réussies
- Éléments ignorés (normal si migration déjà exécutée partiellement)
- Erreurs (devrait être 0)

**Section 3 : Vérification des tables**
- Tableau listant les 3 nouvelles tables
- Statut de chaque table (Existe / Manquante)
- Nombre de lignes dans chaque table

**Section 4 : Vérification des paramètres**
- Barre de progression : X / 8 paramètres créés
- Devrait afficher **8 / 8 paramètres** ✅

**Section 5 : Statut final**
- Alerte verte : "Migration terminée avec succès !"
- Ou alerte rouge : "Migration terminée avec X erreur(s)" (nécessite investigation)

---

## ✅ Résultat Attendu

### Après Migration Réussie

**Tables créées** :
```sql
✓ tbldietic_recurring_payments (0 lignes)
✓ tbldietic_recurring_payment_transactions (0 lignes)
✓ tbldietic_refunds (0 lignes)
```

**Paramètres créés** :
```
✓ 8 / 8 paramètres
```

**Requêtes exécutées** :
```
✓ ~35-40 requêtes réussies
⚠️ 0-5 éléments ignorés (normal)
❌ 0 erreur
```

**Statut** :
```
✓ Migration terminée avec succès !
```

---

## 🐛 Résolution de Problèmes

### Erreur : "Table already exists"

**Cause** : Migration déjà exécutée partiellement
**Solution** : Normal, l'élément est ignoré automatiquement
**Action** : Aucune, continuez

### Erreur : "Access denied for user"

**Cause** : Permissions insuffisantes sur la base de données
**Solution** : Vérifier les permissions MySQL de l'utilisateur
**Action** : Accordez les permissions CREATE, ALTER, INSERT, INDEX

### Erreur : "Duplicate column name"

**Cause** : Colonne déjà ajoutée lors d'une tentative précédente
**Solution** : Normal, l'élément est ignoré automatiquement
**Action** : Aucune, continuez

### Erreur : "Migration file not found"

**Cause** : Fichier SQL manquant
**Solution** : Vérifier que `add_recurring_payments_and_refunds.sql` existe
**Action** : Re-déployer le module ou récupérer le fichier manquant

### La Migration Ne S'affiche Pas

**Cause** : Fichier PHP non présent
**Solution** : Vérifier que `009_add_recurring_payments_and_refunds.php` existe dans `/modules/dietetic/migrations/`
**Action** : Re-déployer le fichier

---

## 📊 Vérification Post-Migration

### 1. Vérifier les Tables

```sql
SHOW TABLES LIKE '%dietic_recurring%';
SHOW TABLES LIKE '%dietic_refunds%';
```

**Résultat attendu** :
```
tbldietic_recurring_payments
tbldietic_recurring_payment_transactions
tbldietic_refunds
```

### 2. Vérifier les Colonnes Ajoutées

```sql
DESCRIBE tbldietic_subscriptions;
DESCRIBE tbldietic_payments;
DESCRIBE tbldietic_invoices;
```

**Nouvelles colonnes attendues** :
- `subscriptions` : `is_recurring`, `recurring_payment_id`
- `payments` : `refunded_amount`, `is_refunded`, `refund_id`
- `invoices` : `refunded_amount`, `net_amount`

### 3. Vérifier les Paramètres

```sql
SELECT * FROM tbldietic_settings WHERE setting_key LIKE 'recurring%' OR setting_key LIKE 'refunds%';
```

**Résultat attendu** : 8 lignes

### 4. Tester les Menus

Vérifier que ces menus sont accessibles :

1. **Diététique → Paiements Récurrents**
   - URL : `/admin/dietetic/recurring_payments`
   - Devrait afficher : Dashboard avec statistiques (MRR, actifs, etc.)

2. **Diététique → Remboursements**
   - URL : `/admin/dietetic/refunds`
   - Devrait afficher : Liste des remboursements avec filtres

---

## 🔄 Re-exécution de la Migration

### Si Vous Devez Re-exécuter

La migration est **idempotente** : elle peut être exécutée plusieurs fois sans problème.

**Comportement** :
- Tables déjà existantes → Ignorées ⚠️
- Colonnes déjà ajoutées → Ignorées ⚠️
- Paramètres déjà créés → Mise à jour (ON DUPLICATE KEY UPDATE)
- Index déjà créés → Ignorés ⚠️

**Marquage de la migration** :
- Si vous voulez re-exécuter après un échec partiel, supprimez d'abord l'entrée :
  ```sql
  DELETE FROM tbldietic_migrations WHERE migration_name = '009_add_recurring_payments_and_refunds';
  ```
- Puis re-exécutez via l'interface

---

## 📚 Prochaines Étapes

### Après Migration Réussie

1. **Configurer les paramètres** (optionnel)
   - Diététique → Paramètres
   - Ajuster les valeurs par défaut selon vos besoins

2. **Configurer les passerelles de paiement**
   - PayPal, Wave, Orange Money
   - Voir : `PHASE_8_PAYMENT_GATEWAYS.md`

3. **Créer un paiement récurrent de test**
   - Diététique → Paiements Récurrents → Nouveau
   - Ou via un abonnement

4. **Tester un remboursement**
   - Diététique → Remboursements → Nouveau
   - Sélectionner un paiement existant

---

## 🆘 Support

### En Cas de Problème

1. **Vérifiez les logs**
   - Admin → Utilities → Activity Log
   - Recherchez "migration" ou "recurring"

2. **Erreur persistante**
   - Capturez le message d'erreur complet
   - Vérifiez les permissions base de données
   - Contactez le support technique

3. **Restauration**
   - Si la migration échoue gravement, restaurez votre sauvegarde
   - Contactez le support avant de ré-essayer

---

## ✅ Checklist de Migration

Avant d'exécuter :
- [ ] Sauvegarde de la base de données effectuée
- [ ] Connecté en tant qu'administrateur
- [ ] Navigation vers `/admin/dietetic/migrations` réussie

Pendant l'exécution :
- [ ] Bouton "Appliquer" cliqué
- [ ] Confirmation validée
- [ ] Page de résultats chargée

Après l'exécution :
- [ ] Message "Migration terminée avec succès" affiché
- [ ] 3 tables créées vérifiées
- [ ] 8 paramètres créés vérifiés
- [ ] 0 erreur dans le rapport
- [ ] Menus "Paiements Récurrents" et "Remboursements" accessibles

---

**Développé par** : Claude AI - Super Lead Dev
**Session** : claude/continue-dietzone-project-018emaSM3eumRaNt5f1bkX8D
**Documentation** : Phase 9 - Paiements Récurrents & Remboursements

---

*Cette migration finalise le backend de la Phase 9. L'interface admin est déjà complète (Étape 1 Phase 10).*
