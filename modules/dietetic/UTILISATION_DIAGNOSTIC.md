# 🔍 Comment Utiliser le Diagnostic des Notifications

Vous avez configuré votre rappel de dîner à 18h23 mais n'avez pas reçu de notification ?
Voici comment diagnostiquer rapidement le problème.

---

## ✅ Solution Simple : Script de Diagnostic

### Méthode 1 : Via SSH (Recommandé)

Connectez-vous à votre serveur et exécutez :

```bash
cd /home/trpuftja/app
php modules/dietetic/diagnose_notifications.php
```

**Ce que fait ce script :**
- ✅ Vérifie si le module Dietetic est activé
- ✅ Vérifie le hook after_cron_run
- ✅ Vérifie vos préférences de notification
- ✅ Vérifie les canaux (Email, SMS, WhatsApp)
- ✅ Affiche les patients avec rappel dîner
- ✅ Vérifie les dernières notifications envoyées
- ✅ **Vous dit EXACTEMENT quoi faire**

---

### Méthode 2 : Via Navigateur Web (Plus Simple)

Accédez directement à :
```
https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php
```

Une page web s'affiche avec toutes les informations !

---

## 🔴 Problème Principal Identifié

D'après votre test précédent :
```
✓ Cron exécuté avec succès
ℹ️  Aucune activité Dietetic visible  ← PROBLÈME ICI
```

**Cela signifie :** Le cron Perfex fonctionne, mais le module Dietetic ne se déclenche PAS.

---

## 🎯 Solution Probable #1 : Module Non Activé

**C'est la cause la plus fréquente !**

### Étapes :

1. **Allez sur :** https://app.dietsenegal.net/admin/modules

2. **Trouvez** le module "Dietetic" dans la liste

3. **Cliquez sur** "Activate" (ou "Activer")

4. **Testez à nouveau :**
   ```bash
   cd /home/trpuftja/app
   /usr/bin/php /home/trpuftja/app/index.php cron/index
   ```

5. **Vous devriez maintenant voir** des messages Dietetic !

---

## 🎯 Solution Probable #2 : Préférences Non Configurées

1. **Allez sur :** https://app.dietsenegal.net/dietetic/portal/notification_preferences

2. **Vérifiez :**
   - ☑️ **Rappel Dîner** : COCHÉ
   - ⏰ **Heure** : 19:00 (ou l'heure souhaitée)
   - 📧 **Au moins 1 canal activé** : Email ☑️

3. **Sauvegardez**

---

## 🎯 Solution Probable #3 : Aucun Canal Activé

**Vérifiez que vous avez activé au moins un canal :**

Dans vos préférences, cochez :
- ☑️ Email
- ☑️ SMS (si configuré par l'admin)
- ☑️ WhatsApp (si configuré par l'admin)

**Important :** Il faut au moins 1 canal coché !

---

## 📋 Checklist Rapide

Cochez au fur et à mesure :

### Système
- [ ] Module Dietetic **activé** dans Admin > Modules
- [ ] Cron s'exécute (Last Cron Run < 5 min)
- [ ] "Dietetic Cron" visible dans Activity Log

### Vos Préférences
- [ ] Rappel dîner **coché**
- [ ] Heure configurée (ex: 19:00)
- [ ] **Au moins 1 canal activé** (Email/SMS/WhatsApp)

### Vos Coordonnées
- [ ] Email renseigné (pour canal Email)
- [ ] Téléphone renseigné (pour SMS/WhatsApp)

### Configuration Admin
- [ ] SMTP configuré (pour Email)
- [ ] LAM configuré (pour SMS)
- [ ] WhatsApp configuré (pour WhatsApp)

---

## 🧪 Test Rapide

Pour tester que tout fonctionne :

1. **Configurez une heure proche** (ex: maintenant + 10 minutes)
2. **Sauvegardez** vos préférences
3. **Attendez** 10-15 minutes
4. **Vous recevrez** la notification !

**Rappel :** Le cron s'exécute toutes les 5 minutes, donc ± 5 min de délai.

---

## 📊 Vérifications dans Perfex

### 1. Vérifier le Cron
https://app.dietsenegal.net/admin/settings?group=cron_job
- "Last Cron Run" doit être récent (< 5 min)

### 2. Vérifier les Logs
https://app.dietsenegal.net/admin/staff/activity_log
- Recherchez "Dietetic Cron"
- Vous devriez voir : `Dietetic Cron: X notifications envoyées`

### 3. Vérifier vos Préférences
https://app.dietsenegal.net/dietetic/portal/notification_preferences
- Rappel dîner coché ?
- Heure correcte ?
- Canal activé ?

---

## 🆘 Si le Problème Persiste

Exécutez le diagnostic complet et envoyez-moi la sortie :

```bash
cd /home/trpuftja/app
php modules/dietetic/diagnose_notifications.php > diagnostic.txt 2>&1
cat diagnostic.txt
```

Ou accédez directement via navigateur :
https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php

---

## 🎯 Résumé Ultra-Rapide

**Problème :** Notification non reçue
**Cause probable :** Module Dietetic non activé
**Solution :** Admin > Modules > Activer "Dietetic"

**Ensuite :**
1. Configurez vos préférences
2. Activez au moins 1 canal
3. Testez avec une heure proche
4. Attendez 5-15 minutes
5. Vous recevrez la notification ! 🎉

---

## 📞 Support

Si après avoir :
- ✅ Activé le module
- ✅ Configuré les préférences
- ✅ Activé au moins 1 canal
- ✅ Vérifié vos coordonnées

...vous ne recevez toujours rien, exécutez le diagnostic et partagez-moi le résultat.

---

**🎉 Dans 99% des cas, le problème est : Module non activé OU Aucun canal activé !**
