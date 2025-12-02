# Configuration du Cron pour les Notifications Dietetic

## 📋 Vue d'ensemble

Le module Dietetic utilise le système de cron intégré de Perfex CRM pour envoyer automatiquement les rappels de notifications aux patients.

**Types de notifications automatiques :**
- ⚖️ Rappels de pesée (hebdomadaire)
- 💧 Rappels d'hydratation (3x/jour)
- 🍽️ Rappels de repas (petit-déjeuner, déjeuner, dîner)
- 📅 Rappels de consultation (J-1 et H-1)
- 📝 Rappels de saisie des repas (18h00)

---

## ✅ Étape 1 : Vérifier l'installation

1. Connectez-vous en tant qu'administrateur
2. Allez dans **Admin > Dietetic > Configuration > Vérifier les Notifications**
3. URL : `https://app.dietsenegal.net/admin/dietetic/setup/check_notifications`

Cette page vous montrera :
- ✓ État des tables de base de données
- ✓ Statistiques des notifications
- ✓ Configuration des canaux (Email, SMS, WhatsApp)

---

## ⚙️ Étape 2 : Configurer le Cron Perfex CRM

Le module Dietetic s'intègre automatiquement au cron de Perfex via des hooks. Vous devez simplement vous assurer que le cron Perfex est actif.

### Option A : Cron Système (Recommandé pour Production)

Ajoutez cette ligne à votre crontab serveur :

```bash
# Éditer le crontab
crontab -e

# Ajouter cette ligne (exécution toutes les 5 minutes)
*/5 * * * * php /home/user/Dietzone/index.php cron/index
```

**Vérification :**
```bash
# Voir les crons actifs
crontab -l

# Tester manuellement le cron
php /home/user/Dietzone/index.php cron/index
```

### Option B : Auto Cron (Alternatif)

Si vous ne pouvez pas accéder au crontab système :

1. Allez dans **Setup > Settings > Cron Job**
2. Activez **"Auto Cron"**
3. Le cron s'exécutera automatiquement lors des visites du site

⚠️ **Note :** Cette option est moins fiable que le cron système.

---

## 🔍 Étape 3 : Vérifier que le cron fonctionne

### 3.1 Vérification dans Perfex

1. **Setup > Settings > Cron Job**
2. Vérifiez la **"Last Cron Run"** (dernière exécution)
3. Doit être récent (< 5 minutes si configuré toutes les 5 min)

### 3.2 Vérification dans les logs

**Logs Activity de Perfex :**
1. Allez dans **Admin > Activity Log**
2. Recherchez "Dietetic Cron"
3. Vous devriez voir des entrées comme :
   ```
   Dietetic Cron: 5 notifications envoyées, 0 échecs
   ```

**Logs des Notifications Dietetic :**
1. Allez dans **Admin > Dietetic > Notifications > Logs**
2. Vérifiez les notifications récentes envoyées

---

## 🧪 Étape 4 : Test manuel

Pour tester le système sans attendre le cron :

### Test via ligne de commande

```bash
# Aller dans le répertoire du projet
cd /home/user/Dietzone

# Exécuter le cron manuellement
php index.php cron/index

# Ou tester directement le script des notifications
php modules/dietetic/cron_notifications.php
```

### Test via l'interface web

Vous pouvez déclencher manuellement le cron en visitant :
```
https://app.dietsenegal.net/admin/utilities/cron
```

---

## 📊 Étape 5 : Surveillance et maintenance

### Vérifications régulières

**Hebdomadaire :**
- ✓ Vérifier que le cron s'exécute (Settings > Cron Job)
- ✓ Consulter les logs des notifications
- ✓ Vérifier les préférences des patients

**Mensuel :**
- ✓ Analyser les statistiques d'envoi
- ✓ Vérifier les échecs de notifications
- ✓ Optimiser les heures d'envoi si nécessaire

### Logs disponibles

1. **Activity Log Perfex**
   - Admin > Activity Log
   - Rechercher "Dietetic Cron"

2. **Logs Notifications**
   - Admin > Dietetic > Notifications > Logs
   - Détails par patient et par canal

3. **Logs Système**
   - `/var/log/cron` (logs serveur)
   - Vérifie l'exécution du cron système

---

## 🛠️ Dépannage

### Le cron ne s'exécute pas

**Vérifier :**
1. Le cron système est-il configuré ?
   ```bash
   crontab -l
   ```

2. Le fichier index.php est-il accessible ?
   ```bash
   php /home/user/Dietzone/index.php cron/index
   ```

3. Les permissions sont-elles correctes ?
   ```bash
   ls -la /home/user/Dietzone/index.php
   ```

### Les notifications ne sont pas envoyées

**Vérifier :**
1. Les préférences du patient
   - Admin > Dietetic > Patient > Notifications

2. La configuration des canaux
   - Admin > Dietetic > Configuration > Notifications
   - SMS : Account ID et Password LAM
   - WhatsApp : API Key
   - Email : Configuration SMTP de Perfex

3. Les logs d'erreur
   - Admin > Activity Log (rechercher "error")
   - Logs notifications Dietetic

### Notifications en double

**Causes possibles :**
- Deux crons configurés (système + auto)
- Cron qui s'exécute trop souvent

**Solution :**
- Garder uniquement le cron système
- Désactiver l'auto cron dans Setup > Settings

---

## 📝 Configuration des horaires

Les horaires par défaut sont définis dans les préférences de chaque patient :

| Type | Horaire par défaut |
|------|-------------------|
| Pesée | Vendredi 09:00 |
| Eau | 10:00, 14:00, 18:00 |
| Petit-déjeuner | 08:00 |
| Déjeuner | 12:30 |
| Dîner | 19:00 |
| Saisie repas | 18:00 (si rien saisi) |
| Consultation J-1 | 24h avant |
| Consultation H-1 | 1h avant |

**Personnalisation :**
Les patients peuvent modifier leurs horaires dans :
- Portail Patient > Mes Préférences > Notifications

---

## 🔐 Sécurité et bonnes pratiques

1. **Utilisez le cron système** plutôt que l'auto-cron
2. **Exécutez toutes les 5 minutes** pour une réactivité optimale
3. **Surveillez les logs** régulièrement
4. **Sauvegardez** la configuration des notifications
5. **Testez** après chaque modification

---

## 📞 Support

En cas de problème :

1. Vérifiez les logs dans Activity Log
2. Testez manuellement le cron : `php index.php cron/index`
3. Consultez la page de diagnostic : `/admin/dietetic/setup/check_notifications`
4. Contactez le support technique avec :
   - Version de Perfex CRM
   - Logs d'erreur
   - Configuration du cron

---

## ✨ Résumé rapide

```bash
# 1. Configurer le cron système
crontab -e
*/5 * * * * php /home/user/Dietzone/index.php cron/index

# 2. Vérifier l'installation
https://app.dietsenegal.net/admin/dietetic/setup/check_notifications

# 3. Tester manuellement
php /home/user/Dietzone/index.php cron/index

# 4. Vérifier les logs
Admin > Activity Log (rechercher "Dietetic Cron")
```

**C'est tout ! Les notifications devraient maintenant être envoyées automatiquement. 🎉**
