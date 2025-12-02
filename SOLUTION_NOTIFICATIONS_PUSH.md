# 🚀 Solution Rapide - Notifications Push Non Fonctionnelles

## ⚡ Solution en 3 Étapes

### 1️⃣ Diagnostic
Ouvrez ce lien pour vérifier la configuration :
```
https://app.dietsenegal.net/check_firebase_config.php
```

### 2️⃣ Configuration Firebase
Si des champs manquent, configurez-les ici :
```
https://app.dietsenegal.net/admin/dietetic/notifications/settings
```

**Valeurs requises** (depuis Firebase Console) :
- **API Key** : `AIza...` (Firebase Console > Paramètres du projet > Config Web)
- **Project ID** : ex. `dietsenegal-app`
- **Messaging Sender ID** : ex. `123456789`
- **App ID** : `1:123...`
- **VAPID Key** : `BN3g...` (Cloud Messaging > Configuration Web > Générer une paire de clés)

### 3️⃣ Test
Envoyez une notification de test :
```
https://app.dietsenegal.net/test_push_notification.php?password=dietsenegal2025
```

---

## 📋 Checklist Rapide

- [ ] Accéder au diagnostic
- [ ] Noter les champs manquants
- [ ] Aller sur [Firebase Console](https://console.firebase.google.com)
- [ ] Récupérer les valeurs de configuration
- [ ] Les entrer dans Admin > Dietetic > Notifications > Paramètres
- [ ] Activer "Push Notifications" (toggle)
- [ ] Sauvegarder
- [ ] Tester avec le script de test
- [ ] Demander à un patient de tester sur le portail

---

## 🔥 Obtenir la Configuration Firebase

### Étapes Firebase Console

1. **Accéder au projet**
   - https://console.firebase.google.com
   - Sélectionnez votre projet (ou créez-en un)

2. **Paramètres du projet** (icône engrenage ⚙️)
   - Onglet **Général**
   - Sous "Vos applications", trouvez votre app Web
   - Si aucune app : cliquez sur `</>` pour en ajouter une
   - Copiez les valeurs :
     ```javascript
     apiKey: "AIza..."
     projectId: "dietsenegal-app"
     messagingSenderId: "123456..."
     appId: "1:123..."
     ```

3. **Cloud Messaging**
   - Onglet **Cloud Messaging**
   - Section **Configuration Web**
   - **"Générer une paire de clés"** si pas encore fait
   - Copiez la **clé VAPID** : `BN3g...`

4. **Domaines autorisés**
   - Onglet **Authentication**
   - Sous-menu **Settings**
   - **Authorized domains**
   - Ajoutez : `app.dietsenegal.net`

---

## 🧪 Test Patient

1. Connectez-vous en tant que patient
   ```
   https://app.dietsenegal.net/clients/login
   ```

2. Allez sur Préférences
   ```
   https://app.dietsenegal.net/dietetic/portal/notification_preferences
   ```

3. Cliquez sur **"Activer les Notifications"**

4. Autorisez dans le navigateur

5. Vérifiez le message de succès

---

## ❌ Problèmes Courants

### "Firebase push notifications not configured"
➡️ Complétez la configuration dans Admin > Notifications > Paramètres

### "Permission denied"
➡️ Cliquez sur 🔒 dans la barre d'adresse > Notifications > Autoriser

### "No registration token available"
➡️ Vérifiez que la VAPID Key est correcte dans les paramètres

### Service Worker 404
➡️ Le fichier `firebase-messaging-sw.js` doit être à la racine du site

---

## 📖 Documentation Complète

Pour plus de détails, consultez :
```
GUIDE_NOTIFICATIONS_PUSH.md
```

---

## 🆘 Support

Si le problème persiste :

1. **Activez le mode debug**
   ```php
   // modules/dietetic/config.php
   define('DIETETIC_DEBUG', true);
   ```

2. **Vérifiez les logs**
   - Admin > Utilities > Activity Log
   - Filtrez par "FCM" ou "notification"

3. **Console navigateur (F12)**
   - Onglet Console : erreurs
   - Onglet Network : requêtes
   - Onglet Application > Service Workers

---

**Temps estimé** : 10-15 minutes ⏱️

**Difficulté** : Facile 🟢
