# 🔔 Guide de Configuration des Notifications Push - Dietzone

## 📋 Table des Matières
1. [Problème](#problème)
2. [Diagnostic](#diagnostic)
3. [Configuration Firebase](#configuration-firebase)
4. [Étapes de Résolution](#étapes-de-résolution)
5. [Vérification](#vérification)
6. [Dépannage](#dépannage)

---

## ❓ Problème

**Symptôme** : Les patients ne reçoivent plus les notifications push sur https://app.dietsenegal.net/dietetic/portal/notification_preferences

**Causes possibles** :
- ✗ Configuration Firebase incomplète ou manquante
- ✗ VAPID Key non configurée
- ✗ Service Worker non accessible
- ✗ Notifications désactivées dans les paramètres
- ✗ Permissions bloquées par le navigateur

---

## 🔍 Diagnostic

### Étape 1 : Vérifier la Configuration Firebase

Accédez au script de diagnostic :
```
https://app.dietsenegal.net/check_firebase_config.php
```

Ce script vérifie :
- ✓ Existence des tables de notifications
- ✓ Configuration Firebase actuelle
- ✓ Champs manquants
- ✓ Tokens FCM enregistrés
- ✓ Accessibilité du Service Worker

### Étape 2 : Vérifier les Tables

Si les tables n'existent pas, exécutez les migrations :
```
https://app.dietsenegal.net/admin/dietetic/notifications/run_migration
```

---

## 🔥 Configuration Firebase

### Prérequis Firebase
Vous devez avoir un projet Firebase configuré. Si ce n'est pas le cas :

1. **Créer un Projet Firebase**
   - Allez sur https://console.firebase.google.com
   - Cliquez sur "Ajouter un projet"
   - Suivez les étapes (nom du projet, activation de Google Analytics optionnel)

2. **Configurer Cloud Messaging**
   - Dans votre projet Firebase, allez dans **Paramètres du projet** (icône engrenage ⚙️)
   - Sélectionnez l'onglet **Cloud Messaging**
   - Notez le **Sender ID** (ID d'expéditeur)

3. **Créer une Application Web**
   - Dans **Paramètres du projet** > **Général**
   - Sous "Vos applications", cliquez sur l'icône Web (`</>`)
   - Donnez un nom à l'application (ex: "DietSenegal Web")
   - **Ne cochez PAS** "Configurer Firebase Hosting"
   - Cliquez sur "Enregistrer l'application"
   - Firebase vous donnera un objet de configuration JavaScript :
     ```javascript
     const firebaseConfig = {
       apiKey: "AIza...",
       authDomain: "votre-projet.firebaseapp.com",
       projectId: "votre-projet",
       storageBucket: "votre-projet.appspot.com",
       messagingSenderId: "123456789",
       appId: "1:123456789:web:abc123..."
     };
     ```
   - **Notez ces valeurs** pour les utiliser plus tard

4. **Générer la VAPID Key (Web Push Certificate)**
   - Toujours dans **Cloud Messaging**
   - Descendez jusqu'à **Configuration Web**
   - Sous "Certificats push Web", cliquez sur **Générer une paire de clés**
   - Notez la **clé VAPID** générée (commence par `B...`)

5. **Obtenir le Server Key (Legacy) - Optionnel**
   - Dans **Cloud Messaging**, vous verrez **Server key (legacy)**
   - Notez cette clé si vous voulez utiliser l'API Legacy au lieu de l'API v1

### Configuration dans Dietzone

1. **Accéder aux Paramètres**
   ```
   Admin > Dietetic > Notifications > Paramètres
   ```
   Ou directement :
   ```
   https://app.dietsenegal.net/admin/dietetic/notifications/settings
   ```

2. **Activer les Notifications Push**
   - Activez le toggle **"Push Notifications activées"**

3. **Remplir les Champs Firebase**

   #### Configuration de Base (Obligatoire)
   | Champ | Description | Exemple |
   |-------|-------------|---------|
   | **Firebase API Key** | Clé API de votre projet | `AIzaSyC...` |
   | **Firebase Project ID** | ID du projet | `dietsenegal-app` |
   | **Firebase Messaging Sender ID** | ID d'expéditeur | `123456789` |
   | **Firebase App ID** | ID de l'application | `1:123...` |
   | **Firebase VAPID Key** | Clé push web | `BN3g...` |

   #### Champs Optionnels (Auto-remplis si vides)
   | Champ | Valeur par Défaut |
   |-------|-------------------|
   | **Auth Domain** | `{projectId}.firebaseapp.com` |
   | **Storage Bucket** | `{projectId}.appspot.com` |

   #### API Legacy (Optionnel)
   | Champ | Description |
   |-------|-------------|
   | **Firebase Server Key** | Clé serveur legacy (si vous n'utilisez pas l'API v1) |
   | **Use Firebase v1 API** | Décoché = Legacy, Coché = Modern v1 API |

4. **Enregistrer**
   - Cliquez sur **"Enregistrer les paramètres"**
   - Vérifiez le message de succès

---

## 🛠️ Étapes de Résolution

### Solution Complète (Étape par Étape)

#### 1️⃣ Vérifier et Configurer Firebase

```bash
# 1. Accédez au diagnostic
https://app.dietsenegal.net/check_firebase_config.php

# 2. Vérifiez les champs manquants
# 3. Allez sur Firebase Console et récupérez les valeurs
# 4. Configurez dans Admin > Dietetic > Notifications > Paramètres
```

#### 2️⃣ Vérifier le Service Worker

Le Service Worker doit être accessible à la racine :
```bash
https://app.dietsenegal.net/firebase-messaging-sw.js
```

**Vérification** :
- Ouvrez cette URL dans votre navigateur
- Vous devriez voir du code JavaScript (pas d'erreur 404)
- Le fichier doit être à la racine, **PAS** dans `/modules/dietetic/`

#### 3️⃣ Autoriser les Notifications dans Firebase Console

1. Allez dans **Firebase Console** > Votre Projet
2. **Authentication** > **Settings** > **Authorized domains**
3. Ajoutez votre domaine : `app.dietsenegal.net`
4. Sauvegardez

#### 4️⃣ Tester sur le Portail Patient

1. Connectez-vous en tant que patient
   ```
   https://app.dietsenegal.net/clients/login
   ```

2. Allez sur **Préférences de Notification**
   ```
   https://app.dietsenegal.net/dietetic/portal/notification_preferences
   ```

3. Cliquez sur **"Activer les Notifications"**
   - Le navigateur doit demander la permission
   - Cliquez sur **"Autoriser"**

4. **Si bloqué** :
   - Cliquez sur l'icône 🔒 à gauche de l'URL
   - Trouvez "Notifications" et changez en "Autoriser"
   - Rechargez la page et réessayez

#### 5️⃣ Vérifier l'Enregistrement du Token

Après avoir autorisé :
1. Ouvrez la Console du Navigateur (F12)
2. Onglet **Console**
3. Recherchez `[FCM] Token received`
4. Vous devriez voir un message de succès avec le token

Ou vérifiez dans la base de données :
```sql
SELECT * FROM tbldietic_fcm_tokens
WHERE patient_id = {VOTRE_PATIENT_ID}
ORDER BY created_at DESC;
```

#### 6️⃣ Tester l'Envoi d'une Notification

**Option A - Via l'Interface Admin**
```
Admin > Dietetic > Notifications > Test Notification
```

**Option B - Via Firebase Console**
1. Firebase Console > **Cloud Messaging** > **Send your first message**
2. Titre et message
3. Cible : **Jeton d'appareil unique** (FCM Token)
4. Envoyez

---

## ✅ Vérification

### Checklist Complète

- [ ] Tables de notifications créées
- [ ] Firebase configuré avec toutes les clés requises
- [ ] Push notifications activées (`push_enabled` = 1)
- [ ] Service Worker accessible à `/firebase-messaging-sw.js`
- [ ] Domaine autorisé dans Firebase Console
- [ ] Permission du navigateur accordée
- [ ] Token FCM enregistré en base de données
- [ ] Test de notification réussi

### Tests Rapides

**1. Configuration Firebase**
```bash
curl https://app.dietsenegal.net/dietetic/portal/get_firebase_config

# Doit retourner:
{
  "success": true,
  "config": {
    "apiKey": "...",
    "projectId": "...",
    ...
  }
}
```

**2. Service Worker**
```bash
curl -I https://app.dietsenegal.net/firebase-messaging-sw.js

# Doit retourner:
HTTP/1.1 200 OK
Content-Type: application/javascript
```

**3. Token Enregistré**
```bash
https://app.dietsenegal.net/check_firebase_config.php
# Section "Tokens FCM enregistrés" > devrait afficher des tokens actifs
```

---

## 🐛 Dépannage

### Problème : "Firebase push notifications not configured"

**Cause** : Configuration incomplète

**Solution** :
1. Vérifiez que tous les champs obligatoires sont remplis
2. Allez sur https://app.dietsenegal.net/check_firebase_config.php
3. Notez les champs manquants
4. Configurez-les dans Admin > Notifications > Paramètres

---

### Problème : "Service Worker registration failed"

**Cause** : Service Worker non accessible ou erreur dans le fichier

**Solution** :
```bash
# 1. Vérifier l'existence du fichier
ls -la /home/user/Dietzone/firebase-messaging-sw.js

# 2. Vérifier qu'il est accessible
curl https://app.dietsenegal.net/firebase-messaging-sw.js

# 3. Vérifier les erreurs JavaScript dans la console navigateur (F12)
```

---

### Problème : "Permission denied"

**Cause** : Utilisateur a bloqué les notifications

**Solution (Chrome/Edge)** :
1. Cliquez sur l'icône 🔒 à gauche de l'URL
2. Trouvez "Notifications"
3. Changez de "Bloquer" à "Autoriser"
4. Rechargez la page

**Solution (Firefox)** :
1. Cliquez sur l'icône 🛡️ à gauche de l'URL
2. Flèche à côté de "Notifications bloquées"
3. Sélectionnez "Autoriser"

---

### Problème : "No registration token available"

**Causes possibles** :
1. ❌ VAPID Key manquante ou invalide
2. ❌ Service Worker pas encore actif
3. ❌ Permission pas accordée

**Solution** :
```javascript
// Console du navigateur (F12)
console.log('Permission:', Notification.permission);
console.log('SW Registration:', navigator.serviceWorker.controller);

// Doit afficher:
// Permission: "granted"
// SW Registration: ServiceWorkerContainer {...}
```

Si `Notification.permission` = `"default"` ou `"denied"` :
- Réinitialisez les permissions du site dans le navigateur
- Réessayez d'autoriser

---

### Problème : "Token registered but no notifications received"

**Vérifications** :
1. **Vérifier que le token est actif**
   ```sql
   SELECT * FROM tbldietic_fcm_tokens WHERE is_active = 1;
   ```

2. **Tester l'envoi via Firebase Console**
   - Firebase Console > Cloud Messaging > Send test message
   - Utilisez le token FCM de la base de données

3. **Vérifier les logs Activity**
   ```
   Admin > Utilities > Activity Log
   # Filtrer par "FCM" ou "notification"
   ```

4. **Vérifier que le service cron est actif**
   ```bash
   # Cron doit tourner toutes les 15 minutes
   */15 * * * * php /path/to/perfex/index.php cron/index
   ```

---

### Problème : "Notifications work in browser but not in app"

**Cause** : Service Worker scope ou configuration différente

**Solution** :
1. Vérifiez que le Service Worker est au même emplacement (racine)
2. Vérifiez que la configuration Firebase est identique
3. Désinstallez et réinstallez le Service Worker :
   ```javascript
   // Console navigateur
   navigator.serviceWorker.getRegistrations().then(registrations => {
     registrations.forEach(registration => {
       registration.unregister();
     });
   });
   // Rechargez la page et réactivez les notifications
   ```

---

## 📞 Support

Si le problème persiste après avoir suivi ce guide :

1. **Vérifiez les logs d'activité Perfex**
   ```
   Admin > Utilities > Activity Log
   ```

2. **Vérifiez les logs du serveur**
   ```bash
   tail -f /var/log/apache2/error.log
   # ou
   tail -f /var/log/nginx/error.log
   ```

3. **Activez le mode debug**
   ```php
   // modules/dietetic/config.php
   define('DIETETIC_DEBUG', true);
   ```

4. **Console navigateur (F12)**
   - Onglet Console : erreurs JavaScript
   - Onglet Network : requêtes réseau
   - Onglet Application > Service Workers : état du SW

---

## 📚 Ressources

- [Firebase Cloud Messaging Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Web Push Notifications Guide](https://web.dev/push-notifications-overview/)
- [Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)

---

**Version** : 1.0
**Dernière mise à jour** : 2 Décembre 2025
**Auteur** : DietSenegal Technical Team
