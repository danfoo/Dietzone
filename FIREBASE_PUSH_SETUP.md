# 🔥 Guide Firebase Push Notifications - DietZone

## 📋 Vue d'ensemble

Ce guide explique comment activer et configurer les notifications push Firebase dans DietZone.

### ✅ Fonctionnalités ajoutées

- **Notifications push web** via Firebase Cloud Messaging (FCM)
- **Support multi-appareils** : web, Android, iOS
- **Notifications en temps réel** même quand l'application est fermée
- **Notifications in-app** avec bannière élégante
- **Gestion des tokens** par appareil
- **Canal supplémentaire** en plus de Email/SMS/WhatsApp

---

## 🚀 Installation Firebase

### Étape 1 : Créer un projet Firebase

1. Allez sur [Firebase Console](https://console.firebase.google.com/)
2. Cliquez sur **"Ajouter un projet"**
3. Nommez votre projet (ex: "DietZone")
4. Désactivez Google Analytics si non nécessaire
5. Cliquez sur **"Créer le projet"**

### Étape 2 : Ajouter une application Web

1. Dans votre projet Firebase, cliquez sur l'icône **Web** (</>)
2. Enregistrez votre application :
   - Nom: `DietZone Web`
   - Cochez "Configurer aussi Firebase Hosting" (optionnel)
3. **Copiez la configuration** qui apparaît :

```javascript
const firebaseConfig = {
  apiKey: "AIzaSy...",
  authDomain: "your-project.firebaseapp.com",
  projectId: "your-project-id",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abc123"
};
```

### Étape 3 : Activer Firebase Cloud Messaging

1. Dans Firebase Console, allez dans **"Build" > "Cloud Messaging"**
2. Si demandé, activez l'**API Cloud Messaging**
3. Notez le **Server Key** et **Sender ID** (dans Project Settings > Cloud Messaging)

### Étape 4 : Générer les clés VAPID

1. Allez dans **Project Settings** (⚙️)
2. Onglet **"Cloud Messaging"**
3. Section **"Web Push certificates"**
4. Cliquez sur **"Generate key pair"**
5. **Copiez la clé VAPID** générée

---

## ⚙️ Configuration dans DietZone

### Étape 1 : Installer les tables

Exécutez la migration SQL :

```bash
mysql -u root -proot perfexcrm < modules/dietetic/migrations/add_firebase_push_notifications.sql
```

### Étape 2 : Configurer Firebase dans l'admin

1. Connectez-vous en admin : `https://app.dietsenegal.net/admin`
2. Allez dans **Diététique > Notifications > Settings** (nouvel onglet)
3. Section **"Firebase Push Notifications"**
4. Remplissez les champs avec votre configuration Firebase :

| Champ | Valeur |
|-------|--------|
| **Push Enabled** | ✅ Activé |
| **Firebase API Key** | Votre `apiKey` |
| **Firebase Auth Domain** | Votre `authDomain` |
| **Firebase Project ID** | Votre `projectId` |
| **Firebase Storage Bucket** | Votre `storageBucket` |
| **Messaging Sender ID** | Votre `messagingSenderId` |
| **Firebase App ID** | Votre `appId` |
| **VAPID Key** | La clé générée à l'étape 3 |
| **Server Key (Legacy)** | Le Server Key pour FCM API |

5. Cliquez sur **"Save Settings"**

### Étape 3 : Vérifier le Service Worker

Le fichier `firebase-messaging-sw.js` doit être à la **racine** de votre site :

```
/home/user/Dietzone/firebase-messaging-sw.js
```

Vérifiez qu'il est accessible via :
```
https://app.dietsenegal.net/firebase-messaging-sw.js
```

---

## 📱 Utilisation dans le Portail Patient

### Activation des notifications

1. Le patient se connecte au portail
2. Va dans **Notifications > Préférences**
3. Active **"Notifications Push"** ✅
4. Le navigateur demandera la permission :
   - Cliquez sur **"Autoriser"**
5. Le token FCM est automatiquement enregistré

### Test rapide

Dans la console navigateur (F12) :

```javascript
// Vérifier si Firebase est initialisé
DietzonePushNotifications.isEnabled()
// true ou false

// Demander la permission
DietzonePushNotifications.requestPermission((success, token) => {
    console.log('Success:', success);
    console.log('Token:', token);
});
```

---

## 🔧 Fichiers ajoutés/modifiés

### Nouveaux fichiers créés

```
📁 Dietzone/
├── firebase-messaging-sw.js                     # Service Worker Firebase
├── FIREBASE_PUSH_SETUP.md                       # Ce guide
├── modules/dietetic/
│   ├── migrations/
│   │   └── add_firebase_push_notifications.sql  # Migration Firebase
│   ├── libraries/
│   │   └── Firebase_cloud_messaging.php         # Bibliothèque FCM
│   ├── assets/
│   │   ├── js/
│   │   │   └── firebase_push.js                 # Client Firebase
│   │   └── css/
│   │       └── dietetic_portal.css              # Styles notifications in-app (modifié)
```

### Fichiers modifiés

```
✏️ modules/dietetic/dietetic.php                 # Menu admin + notifications
✏️ modules/dietetic/controllers/Portal.php       # Routes FCM
✏️ modules/dietetic/models/Dietetic_notifications_model.php  # Support push
✏️ modules/dietetic/migrations/add_notifications_system.sql  # Colonne channel_push
```

---

## 📊 Tables de base de données

### `tbldietic_fcm_tokens`

Stocke les tokens FCM par patient et par appareil :

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | int(11) | ID unique |
| `patient_id` | int(11) | ID du patient |
| `token` | varchar(255) | Token FCM unique |
| `device_type` | enum | web, android, ios |
| `device_name` | varchar(100) | Nom de l'appareil/navigateur |
| `is_active` | tinyint(1) | Token actif ? |
| `last_used_at` | datetime | Dernière utilisation |

### Modification `tbldietic_notification_logs`

Le champ `channel` accepte maintenant `push` :

```sql
enum('email','sms','whatsapp','push')
```

### Modification `tbldietic_notification_preferences`

Nouvelle colonne ajoutée :

```sql
`channel_push` tinyint(1) DEFAULT 1
```

---

## 🎯 Envoi de notifications push

### Depuis le code PHP

```php
// Charger le modèle
$this->load->model('dietetic/dietetic_notifications_model');

// Envoyer une notification
$this->dietetic_notifications_model->send_notification([
    'patient_id' => 1,
    'type' => 'recommendation_added',
    'subject' => 'Nouvelle recommandation',
    'message' => 'Votre diététicien a ajouté une recommandation sur vos repas.',
    'email' => 'patient@example.com',
    'phone' => '+221770000000',
    'channels' => [
        'email' => 1,
        'sms' => 0,
        'whatsapp' => 0,
        'push' => 1  // ✅ Notification push activée
    ],
    'push_data' => [
        'click_action' => site_url('dietetic/portal/view_recommendations/123'),
        'icon' => base_url('uploads/company/logo.png')
    ]
]);
```

### Automatiquement (CRON)

Le script `cron_notifications.php` envoie automatiquement des push si activés :

```bash
cd /home/user/Dietzone
php modules/dietetic/cron_notifications.php
```

---

## 🔍 Monitoring & Debug

### Logs de notifications

**Admin > Diététique > Notifications > Logs**

Filtrez par canal : `push`

### Vérifier les tokens actifs

```sql
SELECT
    ft.id,
    ft.patient_id,
    CONCAT(c.firstname, ' ', c.lastname) as patient,
    ft.device_type,
    ft.device_name,
    ft.is_active,
    ft.last_used_at,
    ft.created_at
FROM tbldietic_fcm_tokens ft
JOIN tbldietic_patients p ON p.id = ft.patient_id
JOIN tblclients c ON c.userid = p.client_id
WHERE ft.is_active = 1
ORDER BY ft.last_used_at DESC;
```

### Tester l'envoi manuel

Dans la console Firebase :

1. Allez dans **Cloud Messaging**
2. Cliquez sur **"Send your first message"**
3. Testez avec un token copié depuis la BDD

---

## ⚡ Optimisations de performance

### Index ajoutés automatiquement

```sql
-- Sur tbldietic_fcm_tokens
KEY `idx_patient` (`patient_id`)
KEY `idx_active` (`is_active`)
KEY `idx_device_type` (`device_type`)

-- Sur tbldietic_notification_logs
KEY `idx_patient` (`patient_id`)
KEY `idx_type` (`notification_type`)
KEY `idx_status` (`status`)
```

### Nettoyage des tokens inactifs

Firebase désactive automatiquement les tokens invalides.
Pour nettoyer manuellement :

```sql
DELETE FROM tbldietic_fcm_tokens
WHERE is_active = 0
AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## 🚨 Dépannage

### Les notifications push ne fonctionnent pas

1. ✅ Vérifier que Firebase est configuré dans **Admin > Notifications > Settings**
2. ✅ Vérifier que `push_enabled = 1` dans `tbldietic_notification_settings`
3. ✅ Vérifier que le patient a activé `channel_push` dans ses préférences
4. ✅ Vérifier que le service worker est accessible : `/firebase-messaging-sw.js`
5. ✅ Vérifier la console navigateur pour les erreurs JavaScript
6. ✅ Tester avec un message de test depuis Firebase Console

### Le navigateur ne demande pas la permission

- Vérifier que le site est en **HTTPS** (obligatoire pour push)
- Réinitialiser les permissions du site dans les paramètres du navigateur
- Tester avec un autre navigateur (Chrome recommandé)

### Les notifications s'affichent plusieurs fois

- Un patient peut avoir plusieurs tokens (plusieurs appareils)
- C'est normal : chaque appareil reçoit la notification

---

## 📈 Statistiques

Consultez les statistiques dans **Admin > Diététique > Notifications > Logs**

- Total de push envoyés
- Taux de succès/échec
- Appareils actifs par type

---

## 🎉 Avantages des Push Notifications

✅ **Instantanées** : Arrivent en temps réel
✅ **Visibles** : Même si l'application est fermée
✅ **Engageantes** : Taux de clic élevé
✅ **Gratuites** : Pas de coût SMS
✅ **Multi-appareils** : Web + Mobile

---

## 🔐 Sécurité

- ✅ Tokens chiffrés par Firebase
- ✅ HTTPS obligatoire
- ✅ Validation des permissions
- ✅ Tokens révocables
- ✅ Expiration automatique des tokens inactifs

---

**Besoin d'aide ?** Consultez la [documentation Firebase](https://firebase.google.com/docs/cloud-messaging)
