# Fix PayPal Session Loss Issue

**Date**: 13 Décembre 2025
**Statut**: ✅ Implémenté
**Priorité**: 🔴 Critique

---

## 🐛 **Problème Identifié**

### Symptômes
Après avoir effectué un paiement PayPal avec succès, l'utilisateur rencontre une erreur lors de la redirection vers le callback :

```
NS_ERROR_NET_ERROR_RESPONSE
GET https://app.dietsenegal.net/dietetic/portal/paypal_callback/success/7?token=...&PayerID=...
```

### Cause Racine

Le système stockait l'`order_id` PayPal dans la **session PHP** :

```php
// Ancien code (ligne 13479)
$this->session->set_userdata('paypal_order_' . $invoice->id, [
    'order_id' => $result['id'],
    'amount' => $amount_usd,
    'invoice_id' => $invoice->id
]);
```

**Problème** : Lors de la redirection depuis `paypal.com` vers `app.dietsenegal.net`, la session est perdue à cause de :

1. **SameSite cookie policy** - Navigateurs modernes bloquent les cookies tiers
2. **Redirection cross-domain** - PayPal.com → app.dietsenegal.net
3. **Session regeneration** - CodeIgniter peut régénérer l'ID de session

Le callback ne trouvait donc pas l'`order_id` et renvoyait une erreur 400.

---

## ✅ **Solution Implémentée**

### Architecture

Au lieu de stocker dans la session, on utilise maintenant la **base de données** avec une table dédiée : `tbldietic_payment_tokens`

### Modifications Apportées

#### 1. **Nouvelle Table** (`modules/dietetic/migrations/fix_paypal_session_issue.sql`)

```sql
CREATE TABLE IF NOT EXISTS `tbldietic_payment_tokens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `client_id` INT(11) NOT NULL,
  `gateway` VARCHAR(50) NOT NULL,
  `order_id` VARCHAR(255) DEFAULT NULL,
  `token` VARCHAR(255) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'XOF',
  `status` ENUM('pending', 'processing', 'completed', 'cancelled', 'expired'),
  `metadata` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_invoice_gateway` (`invoice_id`, `gateway`, `status`)
);
```

#### 2. **Helper Functions** (`modules/dietetic/helpers/dietetic_helper.php`)

Nouvelles fonctions ajoutées (lignes 1789-1921) :

- `dietetic_create_payment_token()` - Créer un token de paiement
- `dietetic_get_payment_token()` - Récupérer un token
- `dietetic_update_payment_token_status()` - Mettre à jour le statut
- `dietetic_cleanup_expired_payment_tokens()` - Nettoyage automatique

#### 3. **Initiate Payment** (ligne 13478)

**Avant** :
```php
$this->session->set_userdata('paypal_order_' . $invoice->id, [...]);
```

**Après** :
```php
dietetic_create_payment_token(
    $invoice->id,
    $client_id,
    'paypal',
    $result['id'], // PayPal order_id
    $amount_usd,
    'USD'
);
```

#### 4. **Callback Handler** (ligne 13617)

**Avant** :
```php
if (!$this->session->userdata('client_logged_in')) {
    redirect(site_url('dietetic/portal'));
}
$order_data = $this->session->userdata('paypal_order_' . $invoice_id);
```

**Après** :
```php
// Utilise is_client_logged_in() qui restaure depuis cookies si besoin
if (!is_client_logged_in()) {
    redirect(site_url('dietetic/portal'));
}

// Récupère depuis la BD au lieu de la session
$payment_token = dietetic_get_payment_token($invoice_id, 'paypal');
$order_id = $payment_token->order_id;
```

---

## 📦 **Installation**

### Étape 1 : Appliquer la Migration SQL

```bash
mysql -u [user] -p [database] < modules/dietetic/migrations/fix_paypal_session_issue.sql
```

Ou via l'interface admin (à créer) :
```
/admin/dietetic/migrations
```

### Étape 2 : Vérifier la Table

```sql
SHOW TABLES LIKE 'tbldietic_payment_tokens';
DESC tbldietic_payment_tokens;
```

### Étape 3 : Tester le Flux PayPal

1. Se connecter comme patient
2. Aller sur une facture impayée
3. Cliquer "Payer avec PayPal"
4. Compléter le paiement sur PayPal
5. Vérifier que la redirection fonctionne correctement
6. Vérifier que la facture est marquée comme payée

---

## 🔍 **Comment Ça Fonctionne**

### Flux Normal

```
1. Patient clique "Payer avec PayPal"
   ↓
2. Création Order PayPal (API)
   ↓
3. ✅ Stockage order_id en BD (+ client_id, invoice_id)
   ↓
4. Redirection vers PayPal.com
   ↓
5. Patient paie sur PayPal
   ↓
6. Redirection callback : app.dietsenegal.net/dietetic/portal/paypal_callback/success/{invoice_id}
   ↓
7. ✅ Récupération order_id depuis BD (via invoice_id)
   ↓
8. Vérification client_id correspond
   ↓
9. Capture du paiement PayPal (API)
   ↓
10. Mise à jour facture + enregistrement paiement
   ↓
11. ✅ Marquage token comme 'completed'
```

### Sécurité

- **Validation client_id** : Le callback vérifie que le `client_id` du token correspond au client connecté
- **Expiration tokens** : Les tokens expirent après 1 heure
- **Statuts** : `pending` → `processing` → `completed`/`cancelled`
- **Cleanup automatique** : Tokens expirés nettoyés par cron

---

## 🧪 **Tests Recommandés**

### Test 1 : Paiement Normal
- [ ] Paiement PayPal se complète correctement
- [ ] Callback ne renvoie pas d'erreur
- [ ] Facture marquée comme payée
- [ ] Token marqué comme 'completed'

### Test 2 : Annulation
- [ ] Cliquer "Annuler" sur PayPal
- [ ] Redirection correcte vers liste factures
- [ ] Token marqué comme 'cancelled'

### Test 3 : Session Perdue
- [ ] Supprimer les cookies avant le callback
- [ ] Vérifier que `is_client_logged_in()` restaure depuis cookies
- [ ] Paiement se complète quand même

### Test 4 : Token Expiré
- [ ] Modifier `expires_at` dans la BD (passé)
- [ ] Tenter d'accéder au callback
- [ ] Vérifier message d'erreur approprié

### Test 5 : Concurrent Payments
- [ ] Initier 2 paiements pour la même facture
- [ ] Vérifier qu'un seul token actif existe (`UNIQUE KEY`)

---

## 📊 **Monitoring**

### Logs à Surveiller

```sql
-- Tokens en cours
SELECT * FROM tbldietic_payment_tokens
WHERE status = 'pending'
ORDER BY created_at DESC;

-- Tokens bloqués en processing (> 10 min)
SELECT * FROM tbldietic_payment_tokens
WHERE status = 'processing'
AND updated_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE);

-- Taux de succès
SELECT
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM tbldietic_payment_tokens
GROUP BY status;
```

### Activity Logs

```sql
SELECT * FROM tbllogs
WHERE description LIKE '%PAYMENT TOKEN%'
ORDER BY date DESC
LIMIT 50;
```

---

## 🔄 **Maintenance**

### Nettoyage Manuel

```sql
-- Nettoyer les tokens expirés
DELETE FROM tbldietic_payment_tokens
WHERE expires_at < NOW()
OR (status IN ('completed', 'cancelled') AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY));
```

### Cron Job Automatique (Optionnel)

Ajouter au cron Perfex (à implémenter) :

```php
// Dans modules/dietetic/controllers/Cron.php
public function cleanup_payment_tokens()
{
    $deleted = dietetic_cleanup_expired_payment_tokens();
    log_activity('Cron: Cleaned up ' . $deleted . ' expired payment tokens');
}
```

---

## 🚀 **Améliorations Futures**

1. **Webhook PayPal** : Au lieu de capturer dans le callback, utiliser un webhook serveur (plus fiable)
2. **Retry automatique** : Si capture échoue, retry avec exponentiel backoff
3. **Logs détaillés** : Sauvegarder réponses PayPal complètes dans `metadata`
4. **Admin UI** : Interface pour voir/gérer les tokens manuellement
5. **Analytics** : Dashboard temps moyen paiement, taux abandon, etc.

---

## ✅ **Checklist Déploiement**

- [x] Migration SQL créée
- [x] Helper functions ajoutées
- [x] initiate_paypal_payment modifié
- [x] paypal_callback modifié
- [x] Tests unitaires (optionnel)
- [ ] Migration SQL appliquée en prod
- [ ] Tests end-to-end
- [ ] Documentation mise à jour
- [ ] Commit + Push

---

## 📞 **Support**

**Problème** : Token introuvable dans callback
- Vérifier table existe : `SHOW TABLES LIKE 'tbldietic_payment_tokens'`
- Vérifier token créé : `SELECT * FROM tbldietic_payment_tokens WHERE invoice_id = X`
- Vérifier pas expiré : `WHERE expires_at > NOW()`

**Problème** : Erreur "Client ID mismatch"
- Vérifier session restaurée correctement
- Checker `is_client_logged_in()` fonctionne
- Vérifier cookies `dietetic_client_id` et `dietetic_auth_token`

---

**Version**: 1.0.0
**Auteur**: Claude AI - Expert Perfex CRM & Diététique
**Date**: 13 Décembre 2025
