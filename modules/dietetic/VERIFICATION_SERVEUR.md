# 🔍 Vérification du Cron - app.dietsenegal.net

Votre configuration cron détectée :
```bash
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

## ✅ Le cron est configuré, mais vérifions pourquoi la notification n'a pas été envoyée

---

## 🔍 Étape 1 : Vérifier la fréquence du cron

Le cron doit s'exécuter **toutes les 5 minutes** pour que les notifications fonctionnent correctement.

### Via SSH :

```bash
# Se connecter au serveur
ssh votre_user@app.dietsenegal.net

# Voir la configuration complète du cron
crontab -l
```

**Vous devriez voir quelque chose comme :**
```bash
*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index
```

### Via cPanel :

1. Allez dans **Cron Jobs**
2. Vérifiez la ligne du cron Perfex
3. La fréquence doit être : `*/5 * * * *` (toutes les 5 minutes)

**❌ Si vous voyez :**
- `0 * * * *` → S'exécute 1 fois par heure (trop long !)
- `0 0 * * *` → S'exécute 1 fois par jour (trop long !)
- Autre → Vérifiez la fréquence

**✅ Vous devez avoir :**
- `*/5 * * * *` → Toutes les 5 minutes ✓

---

## 🧪 Étape 2 : Tester le cron manuellement

### Via SSH :

```bash
# Se connecter au serveur
ssh votre_user@app.dietsenegal.net

# Exécuter le cron manuellement
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

**Que devez-vous voir ?**
- Des messages sur les tâches exécutées
- Si des notifications doivent être envoyées, vous verrez "Sending notification..."
- Aucune erreur PHP

**❌ Si vous voyez des erreurs :**
- Notez-les et consultez TROUBLESHOOTING.md
- Ou partagez-les pour diagnostic

---

## 📊 Étape 3 : Vérifier dans Perfex CRM

### A. Vérifier que le cron s'exécute

1. Allez sur https://app.dietsenegal.net/admin/settings?group=cron_job
2. Regardez **"Last Cron Run"**

**✅ Si récent (< 5 minutes) :**
- Le cron fonctionne correctement
- Passez à l'étape 4

**❌ Si ancien (> 10 minutes) :**
- Le cron ne s'exécute pas
- Vérifiez la fréquence (Étape 1)
- Ou le chemin est incorrect

### B. Vérifier les logs

1. Allez sur https://app.dietsenegal.net/admin/staff/activity_log
2. Recherchez : **"Dietetic Cron"**

**✅ Si vous voyez des logs :**
```
Dietetic Cron: 3 notifications envoyées, 0 échecs
```
Le cron s'exécute et envoie des notifications !

**❌ Si aucun log :**
- Le hook Dietetic ne s'exécute pas
- Vérifiez que le module est activé

---

## 👤 Étape 4 : Vérifier vos préférences de notification

### A. Vérifier les paramètres du rappel

1. Allez sur https://app.dietsenegal.net/dietetic/portal/notification_preferences
2. Section **"Rappels de Repas"**

**Vérifiez :**
- ☑️ **Rappel Dîner** : COCHÉ
- ⏰ **Heure du dîner** : **18:23**
- 📧 Au moins un canal activé (Email / SMS / WhatsApp)

### B. Vérifier vos coordonnées

1. Allez sur votre profil patient
2. Vérifiez que vous avez :
   - ✅ **Email valide** (pour recevoir par email)
   - ✅ **Téléphone valide** (pour SMS/WhatsApp)

---

## 🕐 Étape 5 : Comprendre le délai

**Important :** Le cron s'exécute toutes les **5 minutes**.

Si vous avez configuré 18:23 :
- Le cron peut s'exécuter à : 18:20, 18:25, 18:30, etc.
- Votre notification sera envoyée au **prochain passage** après 18:23
- Donc entre **18:23 et 18:28** (± 5 minutes max)

**Exemple concret :**
```
18:20 → Cron s'exécute → Pas encore 18:23 → Pas d'envoi
18:23 → (Vous configurez l'heure)
18:25 → Cron s'exécute → Il est 18:23 passé → ENVOI !
```

---

## 🔧 Actions de Dépannage

### Problème : Le cron ne s'exécute pas (Last Cron Run ancien)

**Solutions :**

1. **Vérifier que le cron est activé**
```bash
# Sur le serveur
crontab -l | grep cron/index
```

2. **Tester manuellement**
```bash
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

3. **Vérifier les logs système**
```bash
tail -f /var/log/cron
# ou
grep CRON /var/log/syslog
```

4. **Vérifier les permissions**
```bash
ls -la /home/trpuftja/app/index.php
# Doit être accessible en lecture
```

---

### Problème : Le cron s'exécute mais pas de notification Dietetic

**Vérifications :**

1. **Module Dietetic activé ?**
   - Admin > Modules
   - Dietetic doit être "Activated"

2. **Hook enregistré ?**
   - Vérifier dans `/home/trpuftja/app/modules/dietetic/dietetic.php`
   - Doit contenir : `hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');`

3. **Erreurs PHP ?**
```bash
# Sur le serveur
tail -f /home/trpuftja/app/application/logs/*.php
```

---

### Problème : Notification envoyée mais pas reçue

**Vérifications :**

1. **Email :**
   - Vérifier les spams/indésirables
   - Admin > Settings > Email → Configuration SMTP correcte ?

2. **SMS :**
   - Admin > Dietetic > Configuration > Notifications
   - Account ID et Password LAM corrects ?

3. **WhatsApp :**
   - Admin > Dietetic > Configuration > Notifications
   - API Key correcte ?

4. **Logs des notifications :**
   - Admin > Dietetic > Notifications > Logs
   - Vérifier le statut : "sent" ou "failed"

---

## 🧪 Test Complet

Pour tester tout le système, exécutez ce script de test :

```bash
# Sur le serveur
cd /home/trpuftja/app
php modules/dietetic/test_cron.php
```

Ce script va :
- ✅ Vérifier la connexion à la base de données
- ✅ Lister les patients éligibles pour chaque type de rappel
- ✅ Tester l'envoi des notifications
- ✅ Afficher les résultats détaillés

---

## 📋 Checklist Rapide

Cochez au fur et à mesure :

- [ ] **Fréquence du cron vérifiée** (`*/5 * * * *`)
- [ ] **Test manuel réussi** (pas d'erreur)
- [ ] **"Last Cron Run" récent** (< 5 min)
- [ ] **"Dietetic Cron" dans Activity Log**
- [ ] **Rappel dîner COCHÉ**
- [ ] **Heure 18:23 configurée**
- [ ] **Au moins 1 canal activé**
- [ ] **Email/Téléphone renseigné**
- [ ] **Configuration Email/SMS/WhatsApp OK**

---

## ✅ Une fois tout vérifié

**Scénario 1 : Tout est OK**
- Attendez le prochain passage du cron après 18:23
- Vous recevrez la notification dans les 5 minutes

**Scénario 2 : Test rapide**
- Changez l'heure à : **maintenant + 10 minutes**
- Attendez 10-15 minutes
- Vous devriez recevoir la notification

---

## 📞 Besoin d'aide supplémentaire ?

Si après toutes ces vérifications ça ne fonctionne toujours pas, collectez ces informations :

```bash
# Sur le serveur
echo "=== Cron Configuration ==="
crontab -l | grep cron/index

echo "=== Test Manual ==="
/usr/bin/php /home/trpuftja/app/index.php cron/index

echo "=== PHP Version ==="
/usr/bin/php -v

echo "=== Permissions ==="
ls -la /home/trpuftja/app/index.php

echo "=== Logs Recent ==="
tail -20 /home/trpuftja/app/application/logs/$(date +%Y-%m-%d).php
```

Partagez ces informations pour un diagnostic approfondi.

---

## 🎯 Résumé

**Votre configuration :**
```bash
/usr/bin/php /home/trpuftja/app/index.php cron/index
```

**Actions à faire :**
1. Vérifier que la fréquence est `*/5 * * * *`
2. Tester manuellement pour vérifier que ça fonctionne
3. Vérifier "Last Cron Run" dans Perfex
4. Vérifier vos préférences (rappel dîner coché, heure 18:23)
5. Attendre le prochain passage du cron (± 5 min)

**Si tout est OK, vous recevrez la notification ! 🎉**
