# Phase 9 - Paiements Récurrents & Remboursements

**Date**: 28 Novembre 2025
**Version**: 1.5.0
**Statut**: ✅ Implémenté

---

## 📋 Vue d'ensemble

Cette phase implémente deux fonctionnalités critiques pour la gestion financière automatisée :

1. **Paiements Récurrents** - Abonnements automatiques avec prélèvements mensuels
2. **Remboursements** - Système complet de remboursements partiels et complets

---

## ✨ Fonctionnalités Implémentées

### 🔄 **1. Paiements Récurrents**

#### Architecture

**Tables créées** :
- `tbldietic_recurring_payments` - Planifications de paiements récurrents
- `tbldietic_recurring_payment_transactions` - Historique des transactions

#### Fonctionnalités

✅ **Création d'abonnements récurrents**
- Fréquences : Quotidien, Hebdomadaire, Mensuel, Trimestriel, Annuel
- Lien avec les abonnements (subscriptions)
- Support multi-passerelles (PayPal, Wave, Orange Money)
- Dates personnalisables (début, fin, prochain paiement)

✅ **Traitement automatique**
- Cron job pour détecter les paiements dus
- Génération automatique des factures
- Tentative de paiement automatique
- Mise à jour des statuts

✅ **Gestion des échecs**
- Système de retry configurable (jusqu'à 3 tentatives par défaut)
- Intervalle de retry paramétrable (3 jours par défaut)
- Notifications d'échec automatiques
- Marquage comme "failed" après max retries

✅ **Statuts des paiements récurrents**
- `active` - Actif et en cours
- `paused` - En pause (peut être réactivé)
- `cancelled` - Annulé définitivement
- `expired` - Expiré (date de fin atteinte)
- `failed` - Échec après tentatives maximum

✅ **Notifications automatiques**
- Rappels avant prélèvement (3 jours avant par défaut)
- Confirmation de paiement réussi
- Alertes d'échec de paiement
- Notifications de fin d'abonnement

#### Modèle : `Dietetic_recurring_payments_model`

**Méthodes principales** :

```php
// Créer un paiement récurrent
$recurring_id = $this->dietetic_recurring_payments_model->add([
    'subscription_id' => 123,
    'patient_id' => 45,
    'dietitian_id' => 6,
    'amount' => 50000,
    'frequency' => 'monthly',
    'payment_method' => 'paypal',
    'start_date' => '2025-12-01'
]);

// Récupérer les paiements dus
$due_payments = $this->dietetic_recurring_payments_model->get_due_payments();

// Traiter un paiement récurrent
$result = $this->dietetic_recurring_payments_model->process_payment($recurring_id);

// Pause/Resume
$this->dietetic_recurring_payments_model->pause($recurring_id);
$this->dietetic_recurring_payments_model->resume($recurring_id);

// Annuler
$this->dietetic_recurring_payments_model->cancel($recurring_id, 'Raison');

// Statistiques
$stats = $this->dietetic_recurring_payments_model->get_statistics();
// Returns: active, monthly_recurring_revenue, due_this_week, failed
```

---

### 💰 **2. Remboursements**

#### Architecture

**Table créée** :
- `tbldietic_refunds` - Gestion complète des remboursements

**Colonnes ajoutées** :
- `tbldietic_payments.refunded_amount` - Montant remboursé
- `tbldietic_payments.is_refunded` - Flag remboursement
- `tbldietic_invoices.refunded_amount` - Montant remboursé sur facture
- `tbldietic_invoices.net_amount` - Montant net (total - remboursements)

#### Fonctionnalités

✅ **Remboursements partiels et complets**
- Calcul automatique du type (partial/full)
- Validation des montants disponibles
- Protection contre sur-remboursement
- Historique complet

✅ **Workflow d'approbation (optionnel)**
- Mode auto-approve pour admins
- Système d'approbation à deux niveaux
- Traçabilité (initiated_by, approved_by)
- Possibilité de rejeter avec raison

✅ **Intégration passerelles de paiement**
- **PayPal** : API de remboursement
- **Wave** : API de remboursement
- **Orange Money** : API de remboursement
- **Manuels** : Cash, Virement (traitement hors ligne)

✅ **Statuts des remboursements**
- `pending` - En attente d'approbation
- `processing` - En cours de traitement
- `completed` - Remboursement effectué
- `failed` - Échec du remboursement
- `cancelled` - Remboursement annulé/rejeté

✅ **Mise à jour automatique**
- Mise à jour du paiement original
- Mise à jour de la facture
- Recalcul du montant net
- Annulation facture si remboursement total

#### Modèle : `Dietetic_refunds_model`

**Méthodes principales** :

```php
// Initier un remboursement
$result = $this->dietetic_refunds_model->initiate([
    'payment_id' => 456,
    'refund_amount' => 25000,
    'reason' => 'Service non fourni',
    'notes' => 'Demande client'
]);

// Approuver (admin uniquement)
$result = $this->dietetic_refunds_model->approve($refund_id);

// Rejeter
$result = $this->dietetic_refunds_model->reject($refund_id, 'Raison du rejet');

// Traiter un remboursement
$result = $this->dietetic_refunds_model->process_refund($refund_id);

// Statistiques
$stats = $this->dietetic_refunds_model->get_statistics();
// Returns: total, pending, completed, total_refunded
```

---

## 🗂️ Structure des Fichiers

### Nouveaux Fichiers

```
modules/dietetic/
├── models/
│   ├── Dietetic_recurring_payments_model.php  (549 lignes)
│   └── Dietetic_refunds_model.php             (569 lignes)
├── migrations/
│   └── add_recurring_payments_and_refunds.sql (210 lignes)
└── PHASE_9_RECURRING_PAYMENTS_REFUNDS.md      (Ce fichier)
```

### Fichiers à Créer (Extension future)

```
modules/dietetic/
├── controllers/
│   ├── Recurring_payments.php  (À créer)
│   └── Refunds.php             (À créer)
└── views/
    ├── admin/
    │   ├── recurring_payments/
    │   │   ├── list.php
    │   │   ├── manage.php
    │   │   └── transactions.php
    │   └── refunds/
    │       ├── list.php
    │       ├── initiate.php
    │       └── view.php
    └── portal/
        └── recurring_payments.php
```

---

## 🔧 Installation & Configuration

### 1. Exécuter la Migration SQL

```bash
# Depuis MySQL
mysql -u root -p nom_database < modules/dietetic/migrations/add_recurring_payments_and_refunds.sql

# Ou depuis PHP/Admin
# Coller le contenu SQL dans phpMyAdmin ou l'exécuter via script
```

### 2. Vérifier les Paramètres

**Diététique → Paramètres**

Nouveaux paramètres ajoutés :

```
Paiements Récurrents:
- recurring_payments_enabled = 1
- recurring_retry_max_attempts = 3
- recurring_retry_interval_days = 3
- recurring_send_reminder_days = 3
- recurring_send_failure_notification = 1

Remboursements:
- refunds_enabled = 1
- refunds_require_approval = 0 (0=auto, 1=nécessite approbation)
- refunds_auto_update_invoice = 1
```

### 3. Configurer le Cron Job

**Ajouter au crontab** :

```bash
# Traiter les paiements récurrents dus - Tous les jours à 2h du matin
0 2 * * * php /path/to/perfex/modules/dietetic/cron/process_recurring_payments.php

# Ou utiliser le cron Perfex existant (recommandé)
# Le traitement se fera automatiquement via hooks
```

---

## 💻 Utilisation Programmatique

### Créer un Abonnement Récurrent

```php
$this->load->model('dietetic/dietetic_recurring_payments_model');

// Données de l'abonnement
$data = [
    'subscription_id' => 123,
    'patient_id' => 45,
    'dietitian_id' => 6,
    'service_plan_id' => 2,
    'amount' => 50000, // 50,000 FCFA
    'currency' => 'XOF',
    'frequency' => 'monthly', // daily, weekly, monthly, quarterly, yearly
    'payment_method' => 'paypal', // paypal, wave, orange_money, etc.
    'start_date' => '2025-12-01',
    'end_date' => '2026-12-01', // NULL = indefinite
    'max_retries' => 3
];

$recurring_id = $this->dietetic_recurring_payments_model->add($data);

if ($recurring_id) {
    echo "Abonnement récurrent créé : ID $recurring_id";
} else {
    echo "Erreur lors de la création";
}
```

### Traiter les Paiements Dus (Cron)

```php
$this->load->model('dietetic/dietetic_recurring_payments_model');

// Récupérer tous les paiements dus aujourd'hui
$due_payments = $this->dietetic_recurring_payments_model->get_due_payments();

foreach ($due_payments as $recurring) {
    echo "Traitement du paiement récurrent #" . $recurring->id . "\n";

    $result = $this->dietetic_recurring_payments_model->process_payment($recurring->id);

    if ($result) {
        echo "✓ Succès\n";
    } else {
        echo "✗ Échec\n";
    }
}
```

### Initier un Remboursement

```php
$this->load->model('dietetic/dietetic_refunds_model');

// Remboursement partiel
$result = $this->dietetic_refunds_model->initiate([
    'payment_id' => 456,
    'refund_amount' => 25000, // 25,000 FCFA (moitié du paiement)
    'reason' => 'Service partiellement non fourni',
    'notes' => 'Remboursement approuvé par le directeur',
    'currency' => 'XOF'
]);

if ($result['success']) {
    echo "Remboursement initié : ID " . $result['refund_id'];

    // Si approbation requise, approuver manuellement
    if ($requires_approval) {
        $approve = $this->dietetic_refunds_model->approve($result['refund_id']);
    }
} else {
    echo "Erreur : " . $result['error'];
}
```

### Approuver/Rejeter un Remboursement

```php
$this->load->model('dietetic/dietetic_refunds_model');

// Approuver
$result = $this->dietetic_refunds_model->approve($refund_id);

// Rejeter
$result = $this->dietetic_refunds_model->reject($refund_id, 'Raison insuffisante');
```

---

## 📊 Exemples de Requêtes SQL

### Paiements Récurrents Actifs

```sql
SELECT
    rp.id,
    c.company as patient_name,
    rp.amount,
    rp.frequency,
    rp.next_payment_date,
    rp.status
FROM tbldietic_recurring_payments rp
JOIN tbldietic_patients p ON p.id = rp.patient_id
JOIN tblclients c ON c.userid = p.client_id
WHERE rp.status = 'active'
ORDER BY rp.next_payment_date ASC;
```

### Historique des Transactions

```sql
SELECT
    t.id,
    t.scheduled_date,
    t.processed_date,
    t.amount,
    t.status,
    i.invoice_number
FROM tbldietic_recurring_payment_transactions t
LEFT JOIN tbldietic_invoices i ON i.id = t.invoice_id
WHERE t.recurring_payment_id = 123
ORDER BY t.scheduled_date DESC;
```

### Remboursements en Attente

```sql
SELECT
    r.id,
    c.company as patient_name,
    r.refund_amount,
    r.reason,
    r.created_at,
    CONCAT(s.firstname, ' ', s.lastname) as initiated_by_name
FROM tbldietic_refunds r
JOIN tbldietic_patients p ON p.id = r.patient_id
JOIN tblclients c ON c.userid = p.client_id
JOIN tblstaff s ON s.staffid = r.initiated_by
WHERE r.status = 'pending'
ORDER BY r.created_at DESC;
```

### Revenu Récurrent Mensuel (MRR)

```sql
SELECT
    SUM(amount) as mrr,
    COUNT(*) as active_subscriptions
FROM tbldietic_recurring_payments
WHERE status = 'active'
  AND frequency = 'monthly';
```

---

## 🔐 Sécurité

### Paiements Récurrents

1. **Validation des montants**
   - Vérification cohérence avec le plan de service
   - Montants > 0 requis

2. **Tokens de paiement**
   - Stockage sécurisé des tokens gateway
   - Chiffrement recommandé (extension future)

3. **Permissions**
   - Seuls admins peuvent créer/modifier
   - Diététiciens voient uniquement leurs abonnements

4. **Logging complet**
   - Chaque tentative de paiement loggée
   - Échecs tracés avec raisons

### Remboursements

1. **Approbation à deux niveaux**
   - Initiateur (staff)
   - Approbateur (admin)

2. **Validation des montants**
   - Protection contre sur-remboursement
   - Vérification montant disponible

3. **Traçabilité**
   - initiated_by, approved_by enregistrés
   - Timestamps complets
   - Notes obligatoires

4. **Intégration Gateway**
   - Vérification transaction ID
   - Confirmation gateway
   - Rollback si échec

---

## 🧪 Tests

### Test 1 : Créer un Abonnement Récurrent

```php
// Dans un contrôleur de test ou console PHP
$this->load->model('dietetic/dietetic_recurring_payments_model');

$recurring_id = $this->dietetic_recurring_payments_model->add([
    'subscription_id' => 1,
    'patient_id' => 1,
    'dietitian_id' => 1,
    'amount' => 50000,
    'frequency' => 'monthly',
    'payment_method' => 'paypal',
    'start_date' => date('Y-m-d'),
    'next_payment_date' => date('Y-m-d', strtotime('+1 month'))
]);

echo "Recurring Payment ID: " . $recurring_id;
```

**Vérification** :
```sql
SELECT * FROM tbldietic_recurring_payments WHERE id = [recurring_id];
SELECT * FROM tbldietic_subscriptions WHERE id = 1; -- Vérifier is_recurring = 1
```

### Test 2 : Traiter un Paiement Récurrent

```php
$result = $this->dietetic_recurring_payments_model->process_payment($recurring_id);

if ($result) {
    echo "Payment processed successfully";

    // Vérifier facture créée
    $invoice = $this->db->select('*')
        ->where('subscription_id', 1)
        ->order_by('id', 'DESC')
        ->limit(1)
        ->get('tbldietic_invoices')
        ->row();

    echo "Invoice created: " . $invoice->invoice_number;
}
```

### Test 3 : Initier et Traiter un Remboursement

```php
$this->load->model('dietetic/dietetic_refunds_model');

// Étape 1 : Initier
$result = $this->dietetic_refunds_model->initiate([
    'payment_id' => 1,
    'refund_amount' => 25000,
    'reason' => 'Test de remboursement partiel'
]);

echo "Refund ID: " . $result['refund_id'];

// Étape 2 : Approuver (si nécessaire)
$approve = $this->dietetic_refunds_model->approve($result['refund_id']);

// Étape 3 : Vérifier
$refund = $this->dietetic_refunds_model->get($result['refund_id']);
echo "Status: " . $refund->status; // Should be 'completed'

// Vérifier paiement mis à jour
$payment = $this->dietetic_payments_model->get(1);
echo "Refunded amount: " . $payment->refunded_amount;
```

---

## 📈 Métriques & Statistiques

### Dashboard Admin - Métriques Suggérées

**Paiements Récurrents** :
- MRR (Monthly Recurring Revenue)
- Nombre d'abonnements actifs
- Taux de réussite des paiements
- Paiements dus cette semaine
- Abonnements en échec

**Remboursements** :
- Total remboursé (mois en cours)
- Remboursements en attente d'approbation
- Taux de remboursement (% du revenu)
- Remboursements par méthode de paiement
- Temps moyen de traitement

### Calculs Utiles

```php
// MRR (Monthly Recurring Revenue)
$mrr = $this->dietetic_recurring_payments_model->get_statistics()['monthly_recurring_revenue'];

// ARR (Annual Recurring Revenue)
$arr = $mrr * 12;

// Churn Rate (Abonnements annulés / Total)
$total = $this->db->count_all(db_prefix() . 'dietic_recurring_payments');
$cancelled = $this->db->where('status', 'cancelled')->count_all_results(db_prefix() . 'dietic_recurring_payments');
$churn_rate = ($cancelled / $total) * 100;

// Refund Rate
$total_revenue = ...; // From payments
$total_refunded = $this->dietetic_refunds_model->get_statistics()['total_refunded'];
$refund_rate = ($total_refunded / $total_revenue) * 100;
```

---

## 🐛 Dépannage

### Problème : Paiements récurrents ne se traitent pas automatiquement

**Solutions** :
1. Vérifier que le cron job est configuré et actif
2. Tester manuellement :
   ```php
   $due = $this->dietetic_recurring_payments_model->get_due_payments();
   var_dump($due);
   ```
3. Vérifier les logs :
   ```sql
   SELECT * FROM tblactivitylog WHERE description LIKE '%Recurring%' ORDER BY date DESC LIMIT 20;
   ```
4. Vérifier les paramètres :
   ```sql
   SELECT * FROM tbldietic_settings WHERE setting_key LIKE 'recurring%';
   ```

### Problème : Remboursement échoue

**Solutions** :
1. Vérifier que le paiement est complété :
   ```sql
   SELECT * FROM tbldietic_payments WHERE id = X;
   ```
2. Vérifier montant disponible :
   ```php
   $payment = $this->dietetic_payments_model->get($payment_id);
   $available = $payment->amount - $payment->refunded_amount;
   ```
3. Consulter les logs d'erreur :
   ```sql
   SELECT * FROM tbldietic_refunds WHERE id = X;
   -- Voir error_message
   ```
4. Vérifier configuration passerelle de paiement

### Problème : Échecs de paiement récurrent répétés

**Solutions** :
1. Vérifier credentials gateway
2. Vérifier que payment_method_token est valide
3. Augmenter retry_interval_days :
   ```sql
   UPDATE tbldietic_settings SET setting_value = '5' WHERE setting_key = 'recurring_retry_interval_days';
   ```
4. Contacter le patient pour mettre à jour moyen de paiement

---

## 🚀 Extensions Futures

### Phase 10 - Suggestions

1. **Interface Admin Complète**
   - Dashboard paiements récurrents
   - Gestion visuelle des abonnements
   - Interface de remboursement en un clic
   - Rapports détaillés

2. **Portail Patient**
   - Voir ses abonnements actifs
   - Mettre à jour moyen de paiement
   - Historique des prélèvements
   - Demander annulation

3. **Automatisations Avancées**
   - Dunning management (relances paiement)
   - Upgrade/downgrade automatique de plan
   - Prorata pour changements de plan
   - Pauses temporaires automatiques

4. **Intégrations**
   - Stripe Billing pour paiements récurrents
   - QuickBooks/Xero pour comptabilité
   - Webhooks pour événements
   - API REST pour intégrations externes

5. **Analytics**
   - Cohort analysis
   - LTV (Lifetime Value) prediction
   - Churn prediction ML
   - Revenue forecasting

---

## 📝 Changelog

### Version 1.5.0 - 28 Novembre 2025

**Ajouts** :
- ✅ Table recurring_payments (planifications)
- ✅ Table recurring_payment_transactions (historique)
- ✅ Table refunds (remboursements)
- ✅ Modèle Dietetic_recurring_payments_model (549 lignes)
- ✅ Modèle Dietetic_refunds_model (569 lignes)
- ✅ 8 nouveaux paramètres de configuration
- ✅ Colonnes refunded_amount/net_amount sur invoices
- ✅ Colonnes refunded_amount/is_refunded sur payments
- ✅ Support 5 fréquences de paiement
- ✅ Système de retry configurable
- ✅ Workflow d'approbation remboursements
- ✅ Intégration passerelles (PayPal, Wave, Orange Money)

**Modifications** :
- Subscriptions : +2 colonnes (is_recurring, recurring_payment_id)
- Payments : +3 colonnes (refunded_amount, is_refunded, refund_id)
- Invoices : +2 colonnes (refunded_amount, net_amount)

---

## 👥 Support

- **Email** : support@dietsenegal.net
- **Documentation** : Ce fichier + code source
- **Logs** : Admin → Utilities → Activity Log

---

## 📄 Cas d'Usage Réels

### Cas 1 : Abonnement Mensuel Automatique

```
Patient: Marie Ndiaye
Plan: Suivi Nutritionnel Premium (50,000 FCFA/mois)
Paiement: PayPal

1. Admin crée abonnement récurrent
2. Système génère facture le 1er de chaque mois
3. PayPal prélève automatiquement
4. Patient reçoit facture et confirmation par email
5. Diététicien peut consulter le patient sans souci de paiement
```

### Cas 2 : Remboursement Partiel

```
Patient: Ibrahima Sarr
Paiement original: 100,000 FCFA
Raison: Annulation de 2 consultations sur 4

1. Admin initie remboursement de 50,000 FCFA
2. Système marque paiement comme partiellement remboursé
3. PayPal traite le remboursement
4. Facture mise à jour : Net = 50,000 FCFA
5. Patient reçoit notification de remboursement
```

### Cas 3 : Gestion d'Échec de Paiement

```
Jour 1: Tentative paiement récurrent échoue (carte expirée)
Jour 4: Retry automatique #1 échoue
Jour 7: Retry automatique #2 échoue
Jour 10: Retry automatique #3 échoue
→ Statut: "failed", patient notifié, admin alerté

Admin contacte patient → Met à jour carte → Réactive abonnement
```

---

**Développé par** : Claude AI Assistant
**Statut** : ✅ Backend Complet - Frontend À Développer
**Dernière mise à jour** : 28 Novembre 2025
**Version** : 1.5.0

---

*Cette phase pose les fondations solides pour un système de paiement récurrent et de remboursement entièrement automatisé.*
