# Intégration LAM WhatsApp API

## Vue d'ensemble

Ce document explique comment configurer et utiliser l'API WhatsApp de L'Afrique Mobile (LAM) pour envoyer des notifications WhatsApp aux patients dans le module Dietetic.

## Configuration requise

### 1. Compte LAM WhatsApp

Vous devez avoir un compte actif chez L'Afrique Mobile avec le service WhatsApp activé.

**Comment obtenir un compte :**
1. Visitez https://lafricamobile.com
2. Créez un compte ou connectez-vous
3. Souscrivez au service WhatsApp Business
4. Récupérez vos credentials :
   - Account ID
   - Password
   - Numéro WhatsApp Business (optionnel)

### 2. Configuration dans DietSénégal

Accédez à la page de configuration des notifications :
```
https://app.dietsenegal.net/admin/dietetic/notifications/settings
```

#### Section "WhatsApp (LAM API)"

Remplissez les champs suivants :

| Champ | Description | Requis | Exemple |
|-------|-------------|--------|---------|
| **Provider** | Sélectionnez "LAM" | ✅ Oui | `lam` |
| **Account ID** | Votre identifiant de compte LAM | ✅ Oui | `123456` |
| **Password** | Votre mot de passe LAM WhatsApp | ✅ Oui | `votre_password` |
| **Sender Number** | Votre numéro WhatsApp Business | ⚠️ Optionnel | `+221771234567` |
| **Return URL** | URL de callback pour les statuts | ⚠️ Optionnel | `https://app.dietsenegal.net/dietetic/whatsapp_callback` |

## Endpoints API

### API LAM WhatsApp

**URL :** `https://lamwhatsapp.lafricamobile.com/api`

**Méthode :** `POST`

**Headers :**
```
Content-Type: application/json
Accept: application/json
```

**Corps de la requête :**
```json
{
  "accountid": "votre_account_id",
  "password": "votre_password",
  "sender": "+221771234567",
  "ret_id": "dietetic_wa_1234567890",
  "ret_url": "https://app.dietsenegal.net/dietetic/whatsapp_callback",
  "text": "Votre message WhatsApp",
  "to": [
    {
      "ret_id_1": "+221771234567"
    }
  ]
}
```

**Réponse success (200/201) :**
```json
{
  "success": true,
  "message": "Message sent successfully",
  "message_id": "wa_msg_123456"
}
```

**Réponse erreur :**
```json
{
  "success": false,
  "error": "Invalid credentials"
}
```

## Format des numéros de téléphone

Les numéros de téléphone doivent être au format international avec le préfixe `+`.

**Exemples valides :**
- `+221771234567` (Sénégal)
- `+33612345678` (France)
- `+237699123456` (Cameroun)

**Conversion automatique :**
Le système convertit automatiquement les numéros sénégalais :
- `771234567` → `+221771234567`
- `221771234567` → `+221771234567`

## Activation automatique

### Nouveaux patients

Lorsque vous configurez LAM WhatsApp avec Account ID et Password valides :
- ✅ Les nouveaux patients auront WhatsApp activé automatiquement
- ✅ Ils recevront des notifications WhatsApp pour tous les événements

### Patients existants

Pour activer WhatsApp pour tous les patients existants :

**Option 1 : Via API (recommandé)**

Dans la console du navigateur (F12) :
```javascript
fetch('/admin/dietetic/notifications/enable_whatsapp_all', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

**Option 2 : Via SQL**

```sql
UPDATE tbldietic_notification_preferences
SET channel_whatsapp = 1
WHERE channel_whatsapp = 0;
```

## Notifications envoyées via WhatsApp

Une fois configuré, les patients recevront des notifications WhatsApp pour :

| Événement | Type de notification | Exemple de message |
|-----------|---------------------|-------------------|
| 👋 **Création de compte** | `welcome` | "Bienvenue dans votre espace DietSénégal ! 🎉" |
| 📋 **Programme assigné** | `program_assigned` | "Votre diététicien vous a assigné un nouveau programme" |
| 📅 **Consultation planifiée** | `consultation_scheduled` | "Votre consultation est prévue le 15/12/2024 à 10h00" |
| 📝 **Enquête alimentaire** | `food_survey_assigned` | "Nouvelle enquête alimentaire : Analyse hebdomadaire" |
| ⚖️ **Rappel pesée** | `weight_reminder` | "C'est l'heure de votre pesée hebdomadaire !" |
| 💧 **Rappel hydratation** | `water_reminder` | "N'oubliez pas de boire de l'eau ! 💧" |
| 🎉 **Jalon atteint** | `milestone` | "Félicitations ! Vous avez perdu 5 kg ! 🎉" |
| 💬 **Nouveau message** | `new_message` | "Vous avez reçu un nouveau message de votre diététicien" |

## Vérification et tests

### 1. Vérifier la configuration

```sql
SELECT * FROM tbldietic_notification_settings
WHERE setting_key LIKE 'whatsapp_lam%';
```

Résultat attendu :
```
whatsapp_lam_account_id    | 123456
whatsapp_lam_password      | ********
whatsapp_lam_sender_number | +221771234567
whatsapp_lam_ret_url       | https://...
```

### 2. Vérifier l'activation pour un patient

```sql
SELECT
    p.id,
    c.company as patient_name,
    prefs.channel_whatsapp,
    cont.phonenumber
FROM tbldietic_patients p
JOIN tblclients c ON c.userid = p.client_id
LEFT JOIN tbldietic_notification_preferences prefs ON prefs.patient_id = p.id
LEFT JOIN tblcontacts cont ON cont.userid = c.userid AND cont.is_primary = 1
WHERE p.id = 1;
```

### 3. Tester l'envoi

**Via l'interface admin :**
1. Allez sur https://app.dietsenegal.net/admin/dietetic/notifications/settings
2. Utilisez le formulaire "Tester l'envoi de notifications"
3. Sélectionnez "WhatsApp"
4. Entrez votre numéro : `+221XXXXXXXXX`
5. Message : "Test de notification WhatsApp"
6. Cliquez sur "Envoyer le test"

### 4. Consulter les logs

```sql
SELECT
    created_at,
    patient_id,
    notification_type,
    channel,
    status,
    recipient,
    error_message
FROM tbldietic_notification_logs
WHERE channel = 'whatsapp'
ORDER BY created_at DESC
LIMIT 10;
```

**Statuts possibles :**
- `sent` ✅ : WhatsApp envoyé avec succès
- `failed` ❌ : Échec d'envoi (voir error_message)
- `pending` ⏳ : En attente

## Dépannage

### Erreur : "LAM WhatsApp credentials not configured"

**Cause :** Account ID ou Password manquants

**Solution :**
1. Vérifiez que vous avez bien rempli les champs dans les paramètres
2. Vérifiez dans la base de données :
   ```sql
   SELECT * FROM tbldietic_notification_settings
   WHERE setting_key IN ('whatsapp_lam_account_id', 'whatsapp_lam_password');
   ```

### Erreur : HTTP 401 ou 403

**Cause :** Credentials incorrects

**Solution :**
1. Vérifiez vos credentials sur https://lafricamobile.com
2. Assurez-vous que le service WhatsApp est actif
3. Vérifiez que vous avez suffisamment de crédit

### Erreur : "Invalid phone number"

**Cause :** Format de numéro incorrect

**Solution :**
1. Vérifiez que le numéro commence par `+` et le code pays
2. Exemples valides : `+221771234567`, `+33612345678`
3. Vérifiez dans la table contacts :
   ```sql
   SELECT userid, phonenumber FROM tblcontacts
   WHERE userid IN (SELECT client_id FROM tbldietic_patients);
   ```

### Messages non reçus malgré status "sent"

**Causes possibles :**
1. Le numéro n'a pas WhatsApp installé
2. Le numéro a bloqué votre numéro business
3. Délai de livraison (peut prendre quelques minutes)

**Solution :**
1. Vérifiez que le destinataire a WhatsApp
2. Demandez au destinataire de vérifier s'il a bloqué le numéro
3. Consultez le tableau de bord LAM pour voir les statuts de livraison

## Codes d'erreur LAM

| Code | Description | Action |
|------|-------------|--------|
| 401 | Authentication failed | Vérifier credentials |
| 402 | Insufficient balance | Recharger le compte |
| 403 | Forbidden | Vérifier les permissions |
| 404 | Phone number not found | Vérifier le numéro |
| 500 | Server error | Réessayer plus tard |

## Limites et quotas

- **Rate limit** : Vérifier avec LAM pour votre plan
- **Messages par jour** : Dépend de votre abonnement
- **Longueur du message** : Maximum 4096 caractères
- **Médias** : Supportés (à implémenter)

## Support

Pour toute question technique :
- **LAM Support** : https://lafricamobile.com/support
- **Documentation LAM** : https://developers.lafricamobile.com/docs/whatsapp
- **Email LAM** : support@lafricamobile.com

## Architecture technique

### Fichiers modifiés

1. **Dietetic_notifications_model.php**
   - `send_whatsapp_notification()` : Route vers LAM ou autre provider
   - `send_lam_whatsapp()` : Implémentation API LAM WhatsApp
   - `is_whatsapp_configured()` : Vérification configuration LAM

2. **Notifications.php (controller)**
   - Ajout des champs LAM WhatsApp dans settings
   - Masquage des passwords dans les logs
   - Endpoint `enable_whatsapp_all()`

### Flow d'envoi

```
1. Événement déclenché (création patient, programme, etc.)
2. → Notification model : send_notification()
3. → send_whatsapp_notification()
4. → Vérification provider (lam, twilio, meta)
5. → Si LAM : send_lam_whatsapp()
6. → Format du numéro (conversion +221)
7. → Requête CURL vers API LAM
8. → Log du résultat dans tbldietic_notification_logs
9. → Return success/error
```

## Sécurité

✅ **Bonnes pratiques :**
- Les passwords sont masqués dans les logs
- HTTPS obligatoire pour les callbacks
- Validation des numéros de téléphone
- Logs détaillés pour audit

⚠️ **Attention :**
- Ne partagez jamais vos credentials LAM
- Changez le password régulièrement
- Surveillez les logs pour détecter les abus
- Limitez l'accès admin aux paramètres de notifications

## Changelog

**Version 1.0 (2024-11-25)**
- Intégration initiale LAM WhatsApp API
- Support des notifications multi-canaux
- Activation automatique basée sur la configuration
- Logs détaillés et debugging

---

**Dernière mise à jour** : 25 novembre 2024
**Auteur** : Claude (Anthropic)
**Version du module** : Dietetic 2.0
