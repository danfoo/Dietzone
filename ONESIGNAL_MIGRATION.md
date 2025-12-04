# 🔄 Migration Firebase → OneSignal

## 📋 Vue d'ensemble

Ce guide explique comment migrer complètement de Firebase Cloud Messaging vers OneSignal pour les push notifications, afin d'obtenir une compatibilité parfaite avec Median (génération d'APK).

### Pourquoi migrer vers OneSignal ?

| Critère | Firebase Direct | OneSignal |
|---------|-----------------|-----------|
| **Web Push** | ✅ Oui | ✅ Oui |
| **Median APK** | ❌ Non supporté | ✅ **Supporté nativement** |
| **iOS Push** | ✅ Oui | ✅ Oui |
| **Segmentation** | ⚠️ Manuelle | ✅ Automatique |
| **Analytics** | ⚠️ Basique | ✅ Avancé (CTR, conversion) |
| **A/B Testing** | ❌ Non | ✅ Oui |
| **Prix (10k users)** | Gratuit | Gratuit |

---

## 🚀 Phase 1 : Configuration OneSignal (30 min)

### Étape 1.1 : Créer un compte OneSignal

1. Allez sur [https://onesignal.com](https://onesignal.com)
2. Cliquez sur **"Get Started Free"**
3. Inscrivez-vous avec votre email pro
4. Vérifiez votre email

### Étape 1.2 : Créer une application

1. Dans le dashboard OneSignal → **"New App/Website"**
2. Nom de l'app : **"Dietzone"**
3. Sélectionnez les plateformes :
   - ✅ **Web Push** (pour le portail web)
   - ✅ **Google Android** (pour Median APK)
   - ✅ **Apple iOS** (pour Median APK - optionnel)

### Étape 1.3 : Configurer Google Android (FCM)

1. Dans OneSignal → **Settings** → **Platforms** → **Google Android (FCM)**
2. Cliquez sur **"Configure"**
3. Méthode recommandée : **Upload Firebase Server JSON**

**Obtenir le fichier JSON depuis Firebase :**

```bash
# Aller sur Firebase Console
https://console.firebase.google.com

# Sélectionner votre projet → Settings → Service Accounts
# Cliquer sur "Generate new private key"
# Télécharger le fichier JSON
```

4. Uploadez le fichier JSON sur OneSignal
5. Cliquez sur **"Save & Continue"**

### Étape 1.4 : Configurer Web Push

1. Dans OneSignal → **Settings** → **Platforms** → **Web Push**
2. Sélectionnez **"Typical Site"** (site standard)
3. Entrez votre domaine : `https://app.dietsenegal.net`
4. Configuration :
   - **Auto Resubscribe** : ✅ Activé
   - **Notification Persistence** : ✅ Activé
   - **Default Icon** : Uploadez le logo de Dietzone
5. Téléchargez le fichier **OneSignalSDKWorker.js**
6. Cliquez sur **"Save"**

### Étape 1.5 : Récupérer les clés

📝 **Notez ces informations (vous en aurez besoin) :**

```
OneSignal App ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
REST API Key: OS-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

Pour les trouver : **Settings** → **Keys & IDs**

---

## 📦 Phase 2 : Migration de la base de données (5 min)

### Étape 2.1 : Exécuter la migration SQL

```bash
# Sur votre serveur de production
mysql -u root -p perfexcrm < modules/dietetic/migrations/migrate_to_onesignal.sql
```

### Étape 2.2 : Vérifier la migration

```sql
-- Vérifier que la colonne onesignal_player_id a été ajoutée
DESCRIBE tbldietic_fcm_tokens;

-- Vérifier les nouveaux settings
SELECT * FROM tbldietic_notification_settings
WHERE setting_key LIKE 'onesignal%';
```

Résultat attendu :

```
+---------------------------+
| setting_key               |
+---------------------------+
| onesignal_app_id          |
| onesignal_rest_api_key    |
| onesignal_user_auth_key   |
| onesignal_web_enabled     |
+---------------------------+
```

---

## ⚙️ Phase 3 : Configuration Backend (10 min)

### Étape 3.1 : Configurer OneSignal dans l'admin

1. Connectez-vous en tant qu'admin : `https://app.dietsenegal.net/admin`
2. Menu : **Diététique** → **Notifications** → **Settings**
3. Cherchez la section **"OneSignal Push Notifications"**
4. Remplissez :

| Champ | Valeur |
|-------|--------|
| **OneSignal App ID** | `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |
| **REST API Key** | `OS-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` |
| **Web Enabled** | ✅ Oui |

5. Cliquez sur **"Save Settings"**

### Étape 3.2 : Uploader OneSignalSDKWorker.js

```bash
# Sur le serveur
cd /home/trpuftja/app

# Uploader le fichier téléchargé depuis OneSignal
# Le placer à la racine du site (même niveau que index.php)
# Vérifier les permissions
chmod 644 OneSignalSDKWorker.js

# Vérifier qu'il est accessible
curl https://app.dietsenegal.net/OneSignalSDKWorker.js
```

---

## 💻 Phase 4 : Frontend Web (15 min)

### Étape 4.1 : Remplacer firebase_push.js par onesignal_push.js

Éditez le fichier qui charge les scripts JavaScript (probablement dans un template ou view) :

**AVANT :**

```php
<!-- Firebase Push -->
<script src="<?= module_dir_url('dietetic', 'assets/js/firebase_push.js') ?>"></script>
<script>
// Initialize Firebase
DietzonePushNotifications.init(<?= json_encode($firebase_config) ?>);
</script>
```

**APRÈS :**

```php
<!-- OneSignal Push -->
<script src="<?= module_dir_url('dietetic', 'assets/js/onesignal_push.js') ?>"></script>
<script>
// Initialize OneSignal
DietzonePushNotifications.init(<?= json_encode($onesignal_config) ?>);
</script>
```

### Étape 4.2 : Mettre à jour le controller pour fournir la config

Éditez `modules/dietetic/controllers/Portal.php` dans la méthode qui charge la page principale :

**AVANT :**

```php
// Load Firebase config
$this->load->library('dietetic/firebase_cloud_messaging');
$firebase_config = $this->firebase_cloud_messaging->get_web_config();
$data['firebase_config'] = $firebase_config;
```

**APRÈS :**

```php
// Load OneSignal config
$this->load->library('dietetic/onesignal_cloud_messaging');
$onesignal_config = $this->onesignal_cloud_messaging->get_web_config();
$data['onesignal_config'] = $onesignal_config;
```

### Étape 4.3 : Mettre à jour les vues

Cherchez dans vos vues où Firebase est initialisé :

```bash
cd modules/dietetic/views
grep -r "firebase_config" .
```

Remplacez `firebase_config` par `onesignal_config`.

---

## 📱 Phase 5 : Configuration Median (20 min)

### Étape 5.1 : Configurer Median Dashboard

1. Connectez-vous à [https://median.co/dashboard](https://median.co/dashboard)
2. Sélectionnez votre app **Dietzone**
3. Allez dans **App Settings** → **Push Notifications**
4. Sélectionnez **OneSignal**
5. Entrez votre **OneSignal App ID** : `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`
6. Cliquez sur **"Save"**

### Étape 5.2 : Ajouter le script Median dans votre site

Éditez le template HTML principal pour inclure le script Median OneSignal :

```html
<!-- Dans <head> ou avant </body> -->
<?php if ($is_median_app): ?>
<script src="<?= module_dir_url('dietetic', 'assets/js/median_onesignal.js') ?>"></script>
<?php endif; ?>
```

**Comment détecter Median ?**

```php
// Dans votre controller
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_median_app = (
    strpos($user_agent, 'GoNativeIOS') !== false ||
    strpos($user_agent, 'GoNativeAndroid') !== false
);
$data['is_median_app'] = $is_median_app;
```

### Étape 5.3 : Rebuild l'APK dans Median

1. Dans Median Dashboard → **App Configuration**
2. Cliquez sur **"Build"** → **"Rebuild App"**
3. Attendez que le build soit terminé (15-30 min)
4. Téléchargez le nouveau APK

---

## 🧪 Phase 6 : Tests (30 min)

### Test 1 : Vérifier la configuration

```bash
# Test 1: Vérifier l'endpoint OneSignal config
curl https://app.dietsenegal.net/dietetic/portal/get_onesignal_config

# Résultat attendu:
{
  "success": true,
  "config": {
    "appId": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
    ...
  }
}
```

### Test 2 : Test manuel depuis OneSignal Dashboard

1. Allez sur OneSignal Dashboard → **Messages** → **New Push**
2. Créez un message de test :
   - **Title** : "Test Dietzone"
   - **Message** : "Ceci est un test de notification"
   - **Audience** : "Test Users" ou "All Subscribed Users"
3. Cliquez sur **"Send Message"**
4. Vérifiez que vous recevez la notification

### Test 3 : Test depuis le backend PHP

Créez un fichier de test : `modules/dietetic/dev_tools/test/test_onesignal.php`

```php
<?php
require_once('application/config/database.php');
require_once('application/config/app-config.php');

// Initialize CI
$CI = &get_instance();
$CI->load->database();
$CI->load->library('dietetic/onesignal_cloud_messaging');

// Test: Envoyer à un player ID spécifique
$result = $CI->onesignal_cloud_messaging->send_to_player(
    'PLAYER_ID_DE_TEST', // Remplacer par un vrai player ID
    'Test depuis PHP',
    'Cette notification est envoyée depuis le backend Dietzone',
    ['test' => true],
    ['click_action' => site_url('dietetic/portal')]
);

echo json_encode($result, JSON_PRETTY_PRINT);
```

Exécutez :

```bash
php modules/dietetic/dev_tools/test/test_onesignal.php
```

### Test 4 : Test dans l'app Median

1. Installez l'APK généré sur un appareil Android de test
2. Ouvrez l'app
3. Connectez-vous avec un compte patient
4. Vérifiez dans les logs du serveur que le Player ID a été enregistré :

```bash
tail -f application/logs/activity-log-$(date +%Y-%m-%d).log | grep OneSignal
```

5. Envoyez une notification depuis OneSignal Dashboard
6. Vérifiez qu'elle arrive sur l'appareil

---

## 📊 Phase 7 : Monitoring (10 min)

### Vérifier les Player IDs enregistrés

```sql
SELECT
    ft.id,
    ft.patient_id,
    CONCAT(c.firstname, ' ', c.lastname) as patient,
    ft.device_type,
    ft.device_name,
    ft.onesignal_player_id,
    ft.is_active,
    ft.last_used_at,
    ft.created_at
FROM tbldietic_fcm_tokens ft
JOIN tbldietic_patients p ON p.id = ft.patient_id
JOIN tblcontacts c ON c.id = p.client_id
WHERE ft.onesignal_player_id IS NOT NULL
AND ft.is_active = 1
ORDER BY ft.last_used_at DESC
LIMIT 20;
```

### Vérifier les notifications envoyées

```sql
SELECT
    id,
    patient_id,
    notification_type,
    channel,
    subject,
    status,
    recipient,
    external_id, -- OneSignal Notification ID
    sent_at,
    error_message
FROM tbldietic_notification_logs
WHERE channel = 'push'
AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY created_at DESC
LIMIT 20;
```

### OneSignal Dashboard Analytics

1. Allez sur OneSignal Dashboard → **Delivery**
2. Consultez les métriques :
   - **Sent** : Notifications envoyées
   - **Delivered** : Reçues par les appareils
   - **Clicked** : Taux de clic (CTR)
   - **Errors** : Erreurs d'envoi

---

## 🔍 Dépannage

### Problème 1 : "OneSignal is not configured"

**Cause** : Les settings OneSignal ne sont pas dans la BDD ou sont vides

**Solution** :

```sql
-- Vérifier les settings
SELECT * FROM tbldietic_notification_settings
WHERE setting_key LIKE 'onesignal%';

-- Si vide, ajouter manuellement :
INSERT INTO tbldietic_notification_settings (setting_key, setting_value)
VALUES
('onesignal_app_id', 'VOTRE_APP_ID'),
('onesignal_rest_api_key', 'VOTRE_REST_API_KEY'),
('onesignal_web_enabled', '1');
```

### Problème 2 : OneSignalSDKWorker.js not found (404)

**Cause** : Le fichier n'est pas à la racine du site

**Solution** :

```bash
# Vérifier l'emplacement
ls -la /home/trpuftja/app/OneSignalSDKWorker.js

# Si absent, télécharger depuis OneSignal :
# https://documentation.onesignal.com/docs/web-push-quickstart
wget https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js
mv OneSignalSDKWorker.js /home/trpuftja/app/
chmod 644 /home/trpuftja/app/OneSignalSDKWorker.js
```

### Problème 3 : Notifications pas reçues dans Median

**Cause** : Le Player ID n'est pas enregistré

**Solution** :

1. Ouvrir l'app Median
2. Ouvrir la console de debug (si disponible)
3. Chercher les logs `[Median]`
4. Vérifier que le Player ID a été récupéré et envoyé au serveur

Si pas de Player ID :

- Vérifier que OneSignal est bien configuré dans Median Dashboard
- Vérifier que l'APK a été rebuild après configuration
- Vérifier le fichier `median_onesignal.js` est bien chargé

### Problème 4 : Erreur "Invalid Player ID"

**Cause** : Le Player ID n'existe pas ou a été supprimé dans OneSignal

**Solution** :

```sql
-- Désactiver le player ID invalide
UPDATE tbldietic_fcm_tokens
SET is_active = 0
WHERE onesignal_player_id = 'PLAYER_ID_INVALIDE';
```

OneSignal nettoiera automatiquement les players invalides la prochaine fois.

---

## 🔄 Rollback (si nécessaire)

Si vous devez revenir à Firebase :

### Étape 1 : Restaurer le code PHP

```bash
git checkout HEAD -- modules/dietetic/libraries/
git checkout HEAD -- modules/dietetic/models/Dietetic_notifications_model.php
git checkout HEAD -- modules/dietetic/controllers/Portal.php
```

### Étape 2 : Restaurer le code JavaScript

```bash
# Rechargez firebase_push.js au lieu de onesignal_push.js
# Mettez à jour vos vues pour utiliser firebase_config
```

### Étape 3 : Supprimer les colonnes OneSignal (optionnel)

```sql
ALTER TABLE tbldietic_fcm_tokens DROP COLUMN onesignal_player_id;
DELETE FROM tbldietic_notification_settings WHERE setting_key LIKE 'onesignal%';
```

---

## 📈 Avantages Post-Migration

### Pour les utilisateurs (patients)

✅ **App mobile native** via Median (meilleure UX)
✅ **Notifications plus fiables** (moins de blocages navigateur)
✅ **Support iOS + Android** unifié
✅ **Notifications même app fermée**

### Pour les développeurs

✅ **Une seule plateforme** (Web + Mobile)
✅ **Analytics avancés** (CTR, conversion, etc.)
✅ **Segmentation automatique** (langue, timezone, etc.)
✅ **A/B Testing** des notifications
✅ **API plus simple** que Firebase v1

### Pour l'admin

✅ **Dashboard unifié** OneSignal pour tout
✅ **Statistiques en temps réel**
✅ **Envoi manuel** depuis le dashboard
✅ **Scheduling** des notifications
✅ **Templates** réutilisables

---

## 📚 Ressources

### Documentation

- [OneSignal Web Push Setup](https://documentation.onesignal.com/docs/web-push-quickstart)
- [OneSignal REST API](https://documentation.onesignal.com/reference/rest-api-overview)
- [Median OneSignal Integration](https://median.co/docs/onesignal)
- [OneSignal PHP SDK](https://github.com/OneSignal/onesignal-php-api)

### Support

- **OneSignal Support** : [https://onesignal.com/support](https://onesignal.com/support)
- **Median Support** : [https://median.co/support](https://median.co/support)
- **DietZone Issues** : GitHub Issues de ce projet

---

## ✅ Checklist Finale

Avant de déployer en production :

- [ ] OneSignal app créée et configurée
- [ ] Firebase JSON uploadé sur OneSignal
- [ ] Clés OneSignal (App ID + REST API Key) récupérées
- [ ] Migration SQL exécutée
- [ ] Settings OneSignal configurés dans l'admin
- [ ] OneSignalSDKWorker.js uploadé à la racine du site
- [ ] Code JavaScript mis à jour (onesignal_push.js)
- [ ] Code PHP backend mis à jour
- [ ] Median configuré avec OneSignal App ID
- [ ] APK Median rebuild et téléchargé
- [ ] Tests manuels réussis (Web + Mobile)
- [ ] Monitoring en place (logs + SQL queries)
- [ ] Documentation lue et comprise

---

**Migration réussie ?** 🎉

Vos push notifications sont maintenant unifiées entre Web et Mobile, avec une meilleure fiabilité et des analytics avancés !

**Besoin d'aide ?** Consultez la section Dépannage ou ouvrez une issue GitHub.
