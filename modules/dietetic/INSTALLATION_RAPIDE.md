# ⚡ Installation Rapide du Cron - Dietetic Notifications

## 🚨 Problème Identifié

**Vous avez configuré un rappel à 18h23 mais n'avez pas reçu de notification.**

**Cause :** Le cron système n'est pas configuré sur le serveur `app.dietsenegal.net`.
Sans cron, les notifications automatiques **ne peuvent pas être envoyées**.

---

## ✅ Solution Rapide (5 minutes)

### Étape 1 : Se connecter au serveur

```bash
# Se connecter en SSH au serveur de production
ssh votre_user@app.dietsenegal.net

# Ou via votre panel d'hébergement (cPanel, Plesk, etc.)
```

---

### Étape 2 : Configurer le Cron

#### Option A : Via ligne de commande (Recommandé)

```bash
# 1. Ouvrir l'éditeur de crontab
crontab -e

# 2. Ajouter cette ligne à la fin du fichier :
*/5 * * * * php /chemin/vers/dietzone/index.php cron/index

# Exemple concret :
*/5 * * * * php /home/dietsenegal/public_html/index.php cron/index

# 3. Sauvegarder et quitter (Ctrl+X puis Y sur nano, :wq sur vim)
```

#### Option B : Via cPanel/Plesk

**cPanel :**
1. Allez dans **Cron Jobs** (ou **Tâches Cron**)
2. Dans "Add New Cron Job" :
   - **Common Settings** : Every 5 Minutes (*/5 * * * *)
   - **Command** : `php /home/dietsenegal/public_html/index.php cron/index`
3. Cliquez sur **Add New Cron Job**

**Plesk :**
1. Allez dans **Scheduled Tasks** (ou **Tâches Planifiées**)
2. Cliquez sur **Add Task**
3. Configurez :
   - **Task Type** : Run a PHP script
   - **Command** : `php /var/www/vhosts/dietsenegal.net/httpdocs/index.php cron/index`
   - **Schedule** : Every 5 minutes
4. Cliquez sur **OK**

---

### Étape 3 : Vérifier que ça fonctionne

**A. Test immédiat (ligne de commande) :**

```bash
# Sur le serveur, exécuter manuellement le cron
php /chemin/vers/dietzone/index.php cron/index

# Si tout fonctionne, vous verrez des messages sur les tâches exécutées
```

**B. Vérification dans Perfex (après 5-10 minutes) :**

1. **Setup > Settings > Cron Job**
   - Vérifiez "Last Cron Run" → Doit être récent (< 5 min)

2. **Admin > Activity Log**
   - Recherchez "Dietetic Cron"
   - Vous verrez : `Dietetic Cron: X notifications envoyées`

3. **Admin > Dietetic > Notifications > Logs**
   - Consultez les notifications envoyées

---

## 📱 Tester avec votre notification de dîner

Une fois le cron configuré :

1. **Modifier l'heure de test** (si nécessaire)
   - Allez sur : https://app.dietsenegal.net/dietetic/portal/notification_preferences
   - Changez l'heure du dîner pour dans **5-10 minutes**
   - Sauvegardez

2. **Attendre l'heure configurée**
   - Le cron s'exécute toutes les 5 minutes
   - La notification sera envoyée à l'heure prévue (± 5 min max)

3. **Vérifier la réception**
   - Email ✅
   - SMS ✅ (si configuré)
   - WhatsApp ✅ (si configuré)

---

## 🔍 Trouver le bon chemin du projet

Si vous ne connaissez pas le chemin exact :

```bash
# Sur le serveur
pwd  # Affiche le répertoire actuel

# Exemple de chemins courants :
# cPanel : /home/username/public_html
# Plesk : /var/www/vhosts/domain.com/httpdocs
# VPS : /var/www/html ou /var/www/dietezone
```

---

## ⚙️ Configuration Complète

Le cron que vous ajoutez exécute **toutes** ces tâches automatiquement :

| Notification | Fréquence | Horaire |
|-------------|-----------|---------|
| 📏 Pesée | Hebdomadaire | Configurable (ex: Vendredi 09:00) |
| 💧 Eau | 3x/jour | 10:00, 14:00, 18:00 |
| 🥐 Petit-déjeuner | Quotidien | 08:00 |
| 🍽️ Déjeuner | Quotidien | 12:30 |
| 🍝 Dîner | Quotidien | **18:23** (votre config) |
| 📅 Consultation J-1 | Unique | 24h avant |
| 📅 Consultation H-1 | Unique | 1h avant |
| 📝 Saisie repas | Quotidien | 18:00 (si rien saisi) |

---

## 🆘 En cas de problème

### Problème : "crontab: command not found"

**Solution :** Utilisez l'interface de votre hébergeur (cPanel/Plesk)

---

### Problème : "Permission denied"

**Solution :**
```bash
# Vérifier les permissions
ls -la /chemin/vers/dietzone/index.php

# Si besoin, ajuster
chmod 755 /chemin/vers/dietzone/index.php
```

---

### Problème : Le cron s'exécute mais pas de notification

**Vérifications :**

1. **Canaux activés ?**
   - Admin > Dietetic > Configuration > Notifications
   - Email/SMS/WhatsApp configurés ?

2. **Préférences patient activées ?**
   - Portal > Préférences > Notifications
   - Rappel dîner coché ?
   - Au moins un canal activé ?

3. **Contact valide ?**
   - Email renseigné ?
   - Téléphone renseigné (pour SMS/WhatsApp) ?

4. **Logs d'erreur ?**
   - Admin > Activity Log (rechercher "error")

---

## 📖 Documentation Complète

Pour plus de détails, consultez :

- **[CRON_SETUP.md](CRON_SETUP.md)** - Configuration détaillée
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Dépannage complet

---

## ✅ Checklist Finale

Cochez quand c'est fait :

- [ ] Connecté au serveur de production
- [ ] Cron ajouté au système (crontab ou panel)
- [ ] Test manuel effectué avec succès
- [ ] "Last Cron Run" récent dans Perfex
- [ ] "Dietetic Cron" visible dans Activity Log
- [ ] Notification de test reçue

**Une fois ces étapes validées, vous recevrez automatiquement vos rappels ! 🎉**

---

## 🚀 Résumé Ultra-Rapide

```bash
# 1. Se connecter au serveur
ssh user@app.dietsenegal.net

# 2. Configurer le cron
crontab -e
# Ajouter : */5 * * * * php /chemin/vers/dietzone/index.php cron/index

# 3. Tester
php /chemin/vers/dietzone/index.php cron/index

# 4. Vérifier dans Perfex
# Setup > Settings > Cron Job → "Last Cron Run"
```

**C'est tout ! ⚡**
