# 📬 Guide Complet des Notifications - Dietetic Module

## 🎯 Votre Situation

**Serveur :** app.dietsenegal.net
**Configuration cron actuelle :**
```bash
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

**Problème rencontré :** Notification de dîner à 18h23 non reçue

---

## 🔍 Diagnostic Rapide

### Option 1 : Script de Test Automatique (Recommandé)

Sur votre serveur de production :

```bash
cd /home/trpuftja/app
bash modules/dietetic/test_dinner_notification.sh
```

Ce script va automatiquement :
- ✅ Vérifier que PHP est accessible
- ✅ Vérifier que Perfex CRM fonctionne
- ✅ Vérifier la configuration du cron
- ✅ Tester l'exécution manuelle
- ✅ Vous dire exactement quoi faire ensuite

### Option 2 : Test Manuel

```bash
cd /home/trpuftja/app
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

Si ça fonctionne sans erreur, le système est opérationnel.

---

## 📋 Checklist de Vérification

### 1. Vérifier la Fréquence du Cron

Le cron doit s'exécuter **toutes les 5 minutes**.

**Via SSH :**
```bash
crontab -l | grep cron/index
```

**Vous devez voir :**
```bash
*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index
```

**❌ Si différent, corriger :**
```bash
crontab -e
# Modifier pour avoir: */5 * * * *
```

### 2. Vérifier dans Perfex

1. **Aller sur :** https://app.dietsenegal.net/admin/settings?group=cron_job
2. **Vérifier :** "Last Cron Run" doit être récent (< 5 minutes)
3. **Si ancien :** Le cron ne s'exécute pas → Retour à l'étape 1

### 3. Vérifier vos Préférences

1. **Aller sur :** https://app.dietsenegal.net/dietetic/portal/notification_preferences
2. **Section "Rappels de Repas"**
3. **Vérifier :**
   - ☑️ Rappel Dîner : **COCHÉ**
   - ⏰ Heure du dîner : **18:23**
   - 📧 Au moins 1 canal activé : **Email / SMS / WhatsApp**

### 4. Vérifier vos Coordonnées

1. Vous devez avoir un **email valide**
2. Vous devez avoir un **téléphone valide** (pour SMS/WhatsApp)

### 5. Vérifier la Configuration des Canaux

**Admin > Dietetic > Configuration > Notifications**

- **Email :** Configuration SMTP dans Setup > Settings > Email
- **SMS :** Account ID et Password LAM renseignés
- **WhatsApp :** API Key renseignée

---

## ⏰ Comprendre le Timing

Le cron s'exécute toutes les **5 minutes** :
- 18:00, 18:05, 18:10, 18:15, 18:20, 18:25, 18:30...

**Si vous configurez 18:23 :**
- Le cron de 18:20 → Trop tôt, pas d'envoi
- Le cron de 18:25 → Il est passé 18:23 → **ENVOI !**

**Délai maximal :** ± 5 minutes après l'heure configurée

---

## 🧪 Test Complet du Système

Pour tester que tout fonctionne :

```bash
cd /home/trpuftja/app
php modules/dietetic/test_cron.php
```

Ce script teste tous les types de notifications et vous montre :
- Quels patients sont éligibles
- Quelles notifications sont envoyées
- Les erreurs éventuelles

---

## 📊 Vérifier les Logs

### A. Activity Log de Perfex

1. **Aller sur :** https://app.dietsenegal.net/admin/staff/activity_log
2. **Rechercher :** "Dietetic Cron"
3. **Vous devriez voir :**
   ```
   Dietetic Cron: 3 notifications envoyées, 0 échecs
   ```

### B. Logs des Notifications Dietetic

1. **Aller sur :** https://app.dietsenegal.net/admin/dietetic (menu à trouver)
2. **Section :** Notifications > Logs
3. **Voir :** L'historique de toutes les notifications

---

## 🔧 Solutions aux Problèmes Courants

### Problème 1 : "Last Cron Run" est ancien

**Cause :** Le cron système ne s'exécute pas

**Solutions :**
1. Vérifier la fréquence : `crontab -l`
2. Doit être `*/5 * * * *`
3. Tester manuellement : `/usr/bin/php /home/trpuftja/app/index.php cron/index`
4. Vérifier les logs : `tail -f /var/log/cron`

### Problème 2 : Le cron s'exécute mais pas de "Dietetic Cron" dans les logs

**Cause :** Le hook Dietetic ne s'exécute pas

**Solutions :**
1. Vérifier que le module Dietetic est activé : Admin > Modules
2. Vérifier le fichier `/home/trpuftja/app/modules/dietetic/dietetic.php`
3. Doit contenir : `hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');`

### Problème 3 : Notification envoyée mais pas reçue

**Causes possibles :**

**Email :**
- Vérifier les spams/indésirables
- Tester la config SMTP : Setup > Settings > Email

**SMS :**
- Vérifier la configuration LAM
- Tester l'envoi depuis Admin > Dietetic

**WhatsApp :**
- Vérifier l'API Key
- Tester l'envoi depuis Admin > Dietetic

### Problème 4 : Erreur lors de l'exécution du cron

**Voir les logs PHP :**
```bash
tail -f /home/trpuftja/app/application/logs/$(date +%Y-%m-%d).php
```

**Erreurs courantes :**
- Base de données inaccessible → Vérifier app-config.php
- Permission denied → Vérifier les permissions des fichiers
- Memory limit → Augmenter dans php.ini

---

## 📚 Documentation Disponible

| Fichier | Description |
|---------|-------------|
| **INSTALLATION_RAPIDE.md** | Guide condensé pour installer le cron |
| **VERIFICATION_SERVEUR.md** | Vérifications spécifiques pour votre serveur |
| **CRON_SETUP.md** | Documentation complète de configuration |
| **TROUBLESHOOTING.md** | Guide de dépannage détaillé |
| **test_cron.php** | Script de test complet |
| **test_dinner_notification.sh** | Script de test pour notification dîner |

---

## 🎯 Actions à Faire Maintenant

### Étape 1 : Vérifier la Fréquence

```bash
ssh votre_user@app.dietsenegal.net
crontab -l | grep cron/index
```

**Doit afficher :**
```
*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index
```

**Si ce n'est pas `*/5`, corriger :**
```bash
crontab -e
# Changer en: */5 * * * *
```

### Étape 2 : Tester

```bash
cd /home/trpuftja/app
bash modules/dietetic/test_dinner_notification.sh
```

### Étape 3 : Vérifier dans Perfex

1. https://app.dietsenegal.net/admin/settings?group=cron_job
2. "Last Cron Run" récent ?
3. Activity Log → "Dietetic Cron" présent ?

### Étape 4 : Vérifier vos Préférences

1. https://app.dietsenegal.net/dietetic/portal/notification_preferences
2. Rappel dîner coché ?
3. Heure = 18:23 ?
4. Canal activé ?

### Étape 5 : Test en Conditions Réelles

**Option A :** Attendre demain 18:23

**Option B :** Changer l'heure pour **maintenant + 10 minutes**
- Sauvegarder
- Attendre 10-15 minutes
- Vérifier la réception

---

## ✅ Résumé

**Votre configuration cron :** ✓ Existante
```bash
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

**À vérifier :**
1. ☐ Fréquence = `*/5 * * * *` (toutes les 5 minutes)
2. ☐ "Last Cron Run" récent dans Perfex
3. ☐ "Dietetic Cron" dans Activity Log
4. ☐ Préférences : Rappel dîner coché + heure 18:23
5. ☐ Au moins 1 canal activé + coordonnées renseignées

**Si tout est ✓, vous recevrez la notification au prochain passage du cron après 18:23 ! 🎉**

---

## 📞 Support

Si le problème persiste après ces vérifications :

1. Exécutez et sauvegardez la sortie de :
   ```bash
   bash modules/dietetic/test_dinner_notification.sh > diagnostic.txt 2>&1
   ```

2. Collectez également :
   - Screenshot de "Last Cron Run" dans Perfex
   - Screenshot de vos préférences de notification
   - Logs d'erreur si disponibles

3. Partagez ces informations pour un diagnostic approfondi

---

**🎯 L'essentiel : Le cron doit s'exécuter toutes les 5 minutes (`*/5 * * * *`)**

Une fois corrigé, attendez le prochain passage après 18:23 et vous recevrez votre notification ! 📬✨
