# 🗑️ GUIDE DE SUPPRESSION DU SYSTÈME DE FACTURATION CUSTOM

## 📋 Vue d'ensemble

Ce guide vous permet de supprimer complètement le système de facturation custom pour utiliser le système Perfex natif à la place.

---

## 📊 Statistiques de Suppression

### Fichiers à supprimer :
- **9 Contrôleurs** (Commissions, Invoices, Payments, Refunds, etc.)
- **8 Modèles** (Service Plans, Subscriptions, Invoices, etc.)
- **45+ Vues** (Admin + Portal + Payment Gateways)
- **10 Tables de base de données**
- **4 Fichiers de debug/test**
- **1 Migration**

### Total : ~350 fichiers et 10 tables = **Environ 15,000 lignes de code**

---

## ⚠️ AVANT DE COMMENCER

### ✅ Checklist de Sécurité :

1. [ ] **Sauvegarde complète de la base de données**
   ```bash
   mysqldump -u${APP_DB_USERNAME} -p${APP_DB_PASSWORD} ${APP_DB_NAME} > backup_avant_suppression.sql
   ```

2. [ ] **Commit Git de l'état actuel**
   ```bash
   git add -A
   git commit -m "Sauvegarde avant suppression système facturation"
   ```

3. [ ] **Vérifier qu'aucune donnée importante** n'existe dans les tables
   ```sql
   SELECT COUNT(*) FROM tbldietic_invoices;
   SELECT COUNT(*) FROM tbldietic_payments;
   SELECT COUNT(*) FROM tbldietic_subscriptions;
   ```

4. [ ] **Confirmer l'utilisation de Perfex natif** à la place

---

## 🚀 PROCÉDURE DE SUPPRESSION

### Étape 1 : Suppression des Fichiers

```bash
cd /home/user/Dietzone/modules/dietetic
chmod +x CLEANUP_BILLING_SYSTEM.sh
./CLEANUP_BILLING_SYSTEM.sh
```

**Résultat attendu :**
```
Fichiers supprimés: 67
Dossiers supprimés: 11
```

### Étape 2 : Suppression des Tables

```bash
mysql -u${APP_DB_USERNAME} -p${APP_DB_PASSWORD} ${APP_DB_NAME} < CLEANUP_BILLING_TABLES.sql
```

### Étape 3 : Nettoyage des Menus (dietetic.php)

Supprimer les entrées de menu liées à la facturation :

```php
// À SUPPRIMER dans dietetic.php - fonction dietetic_module_init_menu_items()

// Facturation
'slug'     => 'dietetic-invoices',
'slug'     => 'dietetic-subscriptions',
'slug'     => 'dietetic-service-plans',
'slug'     => 'dietetic-payments',
'slug'     => 'dietetic-recurring-payments',
'slug'     => 'dietetic-refunds',
'slug'     => 'dietetic-revenue',
'slug'     => 'dietetic-commissions',
```

### Étape 4 : Nettoyage des Traductions (dietetic_lang.php)

Supprimer environ 200 traductions liées à :
- Service Plans
- Subscriptions
- Invoices
- Payments
- Recurring Payments
- Refunds
- Commissions
- Revenue Dashboard

### Étape 5 : Commit Final

```bash
git add -A
git commit -m "refactor: Remove custom billing system - migrate to Perfex native

BREAKING CHANGE: Complete removal of custom billing system

Removed:
- 9 controllers (67 files total)
- 8 models
- 11 view directories
- 10 database tables
- All billing-related menus and translations

Reason: Migration to Perfex CRM native billing system
- Reduces maintenance burden
- Eliminates bugs (AJAX, DataTables, NULL errors)
- Leverages battle-tested Perfex invoicing
- 15,000 lines of code removed

Next steps:
- Use Perfex tblinvoices instead of dietic_invoices
- Use Perfex tblsubscriptions instead of dietic_subscriptions
- Use Perfex tblinvoice_items for service plans
- Link dietetic data to Perfex invoices via invoice_id"

git push
```

---

## 📁 FICHIERS SUPPRIMÉS - DÉTAIL

### Contrôleurs (9)
```
✗ Commissions.php (5.7 KB)
✗ Invoices.php (8.9 KB)
✗ Payment_gateways.php (20.2 KB)
✗ Payments.php (4.6 KB)
✗ Recurring_payments.php (14.2 KB)
✗ Refunds.php (12.5 KB)
✗ Revenue_dashboard.php (14.2 KB)
✗ Service_plans.php (6.9 KB)
✗ Subscriptions.php (10.7 KB)
```

### Modèles (8)
```
✗ Dietetic_commission_settings_model.php (7.2 KB)
✗ Dietetic_invoices_model.php (18.5 KB)
✗ Dietetic_payments_model.php (18.6 KB)
✗ Dietetic_recurring_payments_model.php (15.8 KB)
✗ Dietetic_refunds_model.php (17.6 KB)
✗ Dietetic_revenue_shares_model.php (6.3 KB)
✗ Dietetic_service_plans_model.php (6.3 KB)
✗ Dietetic_subscriptions_model.php (10.2 KB)
```

### Vues (11 dossiers, ~45 fichiers)
```
✗ views/admin/commissions/
✗ views/admin/invoices/
✗ views/admin/payments/
✗ views/admin/recurring_payments/
✗ views/admin/refunds/
✗ views/admin/revenue_dashboard/
✗ views/admin/service_plans/
✗ views/admin/subscriptions/
✗ views/payment_gateways/
✗ views/recurring_payments/
✗ views/refunds/
✗ views/portal_subscriptions.php
✗ views/portal_subscription_view.php
✗ views/portal/invoices.php
✗ views/portal/invoice.php
```

### Tables (10)
```sql
✗ tbldietic_service_plans
✗ tbldietic_subscriptions
✗ tbldietic_invoices
✗ tbldietic_invoice_items
✗ tbldietic_payments
✗ tbldietic_recurring_payments
✗ tbldietic_recurring_payment_transactions
✗ tbldietic_refunds
✗ tbldietic_commission_settings
✗ tbldietic_revenue_shares
```

---

## ✅ CE QUI RESTE (Fonctionnalités Diététiques)

Les tables/fichiers suivants sont **CONSERVÉS** car spécifiques à la diététique :

### Tables Conservées ✅
```sql
✓ tbldietic_patients
✓ tbldietic_consultations
✓ tbldietic_programs
✓ tbldietic_meal_plans
✓ tbldietic_recipes
✓ tbldietic_anthropometric_data
✓ tbldietic_food_diary
✓ tbldietic_preferences
✓ tbldietic_medical_history
```

### Contrôleurs Conservés ✅
```
✓ Patients.php
✓ Consultations.php
✓ Programs.php
✓ Meal_plans.php
✓ Recipes.php
✓ Dashboard.php
✓ Portal.php
```

---

## 🎯 PROCHAINES ÉTAPES (Migration vers Perfex)

### 1. Créer des Services Perfex
```php
// Au lieu de dietic_service_plans
$this->load->model('invoice_items_model');
$item = [
    'description' => 'Consultation Diététique Initiale',
    'long_description' => 'Analyse complète...',
    'rate' => 50000, // 50,000 XOF
    'tax' => 1, // ID de la taxe
    'unit' => 'consultation'
];
$this->invoice_items_model->add($item);
```

### 2. Créer des Factures Perfex
```php
// Au lieu de dietic_invoices
$this->load->model('invoices_model');
$invoice_data = [
    'clientid' => $patient->client_id,
    'date' => date('Y-m-d'),
    'duedate' => date('Y-m-d', strtotime('+30 days')),
    'currency' => 1, // XOF
    'newitems' => [
        [
            'description' => 'Consultation',
            'qty' => 1,
            'rate' => 50000
        ]
    ]
];
$invoice_id = $this->invoices_model->add($invoice_data);

// Lier à la consultation diététique
$this->db->update('tbldietic_consultations',
    ['invoice_id' => $invoice_id],
    ['id' => $consultation_id]
);
```

### 3. Gérer les Abonnements
```php
// Au lieu de dietic_subscriptions
$this->load->model('subscriptions_model');
$subscription = [
    'name' => 'Suivi Diététique Mensuel',
    'clientid' => $patient->client_id,
    'date' => date('Y-m-d'),
    'terms' => 'Mensuel',
    'currency' => 1,
    // ...
];
$sub_id = $this->subscriptions_model->add($subscription);
```

---

## 📞 SUPPORT

Si vous avez des questions ou rencontrez des problèmes :

1. Vérifier le backup existe
2. Vérifier le commit Git
3. Tester la création d'une facture Perfex manuellement
4. Demander de l'aide si nécessaire

---

## ⏱️ TEMPS ESTIMÉ

- **Suppression fichiers** : 2 minutes
- **Suppression tables** : 1 minute
- **Nettoyage menus/traductions** : 10-15 minutes
- **Tests** : 5 minutes

**Total : ~20 minutes**

---

## 🎉 BÉNÉFICES APRÈS SUPPRESSION

✅ **15,000 lignes de code en moins**
✅ **Plus de bugs DataTables/AJAX**
✅ **Plus de maintenance de facturation**
✅ **Système Perfex robuste et testé**
✅ **Mises à jour automatiques avec Perfex**
✅ **Meilleure intégration native**
✅ **Focus sur les fonctionnalités diététiques**

---

**Prêt à nettoyer ? Exécutez les scripts !** 🚀
