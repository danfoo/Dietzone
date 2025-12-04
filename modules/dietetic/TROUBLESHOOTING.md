# 🔧 Guide de Dépannage - Notifications Dietetic

## 🚨 Problèmes Courants et Solutions

### 1. Les notifications ne sont pas envoyées

#### ✅ Vérifications à faire

**A. Vérifier que le cron est configuré**

```bash
# Voir les crons actifs
crontab -l

# Vous devriez voir:
# */5 * * * * php /home/user/Dietzone/index.php cron/index
```

**Solution :** Si absent, exécutez :
```bash
bash modules/dietetic/setup_cron.sh
```

---

**B. Vérifier que le cron s'exécute**

1. Allez dans **Setup > Settings > Cron Job**
2. Regardez la date "Last Cron Run"
3. Doit être < 5 minutes

**Solution :** Si non exécuté :
```bash
# Tester manuellement
php index.php cron/index

# Vérifier les logs serveur
tail -f /var/log/cron
```

---

**C. Vérifier les préférences du patient**

1. Admin > Dietetic > Patients > [Sélectionner patient]
2. Onglet "Notifications"
3. Vérifier que les rappels sont activés

**Exemple de configuration correcte :**
- ✅ Rappel pesée : Activé
- ✅ Rappel eau : Activé
- ✅ Canal Email : Activé
- ✅ Canal SMS : Activé (si configuré)

---

**D. Vérifier la configuration des canaux**

**Email :**
1. Setup > Settings > Email
2. SMTP doit être configuré

**SMS :**
1. Admin > Dietetic > Configuration > Notifications
2. Vérifier Account ID et Password LAM
3. Tester l'envoi

**WhatsApp :**
1. Admin > Dietetic > Configuration > Notifications
2. Vérifier API Key
3. Tester l'envoi

---

### 2. Le cron s'exécute mais aucune notification envoyée

#### Diagnostic

```bash
# Test manuel complet avec logs détaillés
php modules/dietetic/test_cron.php
```

#### Causes possibles :

**A. Aucun patient éligible à l'heure actuelle**

Les notifications sont envoyées selon les horaires configurés :
- Pesée : Jour et heure spécifiques (ex: Vendredi 09:00)
- Eau : 10:00, 14:00, 18:00
- Repas : 08:00 (breakfast), 12:30 (lunch), 19:00 (dinner)

**Solution :** Attendez l'heure de notification ou modifiez les heures de test

---

**B. Préférences désactivées**

```sql
-- Vérifier les préférences d'un patient
SELECT * FROM tbldietic_notification_preferences WHERE patient_id = 123;

-- Résultat attendu:
-- reminder_weight = 1
-- reminder_water = 1
-- channel_email = 1
```

**Solution :** Réactiver dans Admin > Dietetic > Patient > Notifications

---

**C. Pas de contact configuré**

Vérifier que le patient a :
- Email valide
- Numéro de téléphone (pour SMS/WhatsApp)

```sql
-- Vérifier les contacts
SELECT email, phonenumber FROM tblcontacts WHERE id = 123;
```

---

### 3. Notifications envoyées en double

#### Causes :

1. Deux crons configurés (système + auto-cron)
2. Cron qui s'exécute trop souvent

#### Solution :

```bash
# 1. Vérifier les crons actifs
crontab -l

# 2. Supprimer les doublons
crontab -e
# Garder uniquement:
# */5 * * * * php /home/user/Dietzone/index.php cron/index

# 3. Désactiver l'auto-cron
# Setup > Settings > Cron Job > Auto Cron : OFF
```

---

### 4. Erreurs dans les logs

#### Consulter les logs

**Logs Perfex :**
```
Admin > Activity Log
Rechercher: "Dietetic Cron"
```

**Logs système :**
```bash
# Logs cron
tail -f /var/log/cron

# Logs PHP
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx
```

#### Erreurs courantes :

**"PHP Fatal error: Class 'CI_Controller' not found"**
```bash
# Vérifier le chemin du projet dans le cron
crontab -l

# Chemin correct:
*/5 * * * * php /home/user/Dietzone/index.php cron/index
```

**"Unable to connect to database"**
```bash
# Vérifier la configuration de la base de données
cat application/config/app-config.php | grep DB_

# Tester la connexion
php -r "new mysqli('localhost', 'user', 'pass', 'db') or die('Error');"
```

**"SMTP Error: Could not authenticate"**
```
# Vérifier la configuration SMTP
Setup > Settings > Email

# Tester l'envoi d'email
Admin > Utilities > Email Tests
```

---

### 5. SMS/WhatsApp ne fonctionne pas

#### Vérifier la configuration

**SMS (LAM):**
```bash
# Tester l'API manuellement
curl -X POST https://api.lamsms.com/send \
  -d "account_id=YOUR_ID" \
  -d "password=YOUR_PASS" \
  -d "to=221777123456" \
  -d "message=Test"
```

**WhatsApp:**
```bash
# Tester l'API
curl -X POST YOUR_WHATSAPP_API_URL \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -d "phone=221777123456" \
  -d "message=Test"
```

#### Logs des notifications

```sql
-- Voir les tentatives d'envoi
SELECT * FROM tbldietic_patient_notifications
WHERE patient_id = 123
ORDER BY created_at DESC
LIMIT 10;

-- Analyser les échecs
SELECT channel, COUNT(*) as failed_count
FROM tbldietic_patient_notifications
WHERE status = 'failed'
GROUP BY channel;
```

---

### 6. Performances lentes

#### Symptômes :
- Cron prend > 5 minutes
- Timeout errors
- Site lent après l'exécution du cron

#### Solutions :

**A. Optimiser la base de données**
```sql
-- Analyser les tables
ANALYZE TABLE tbldietic_notification_preferences;
ANALYZE TABLE tbldietic_patient_notifications;

-- Optimiser les tables
OPTIMIZE TABLE tbldietic_notification_preferences;
OPTIMIZE TABLE tbldietic_patient_notifications;
```

**B. Augmenter les limites PHP**
```php
// Dans application/config/app-config.php
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '300');
```

**C. Activer le cache**
Le système utilise déjà le cache Perfex pour les préférences.

---

### 7. Tester un type de notification spécifique

```bash
# Test uniquement des rappels de pesée
php -r "
require 'index.php';
\$CI = &get_instance();
\$CI->load->model('dietetic/dietetic_notifications_model');
\$patients = \$CI->dietetic_notifications_model->get_patients_for_weight_reminder();
print_r(\$patients);
"
```

---

## 🔍 Checklist Complète de Diagnostic

Cochez chaque élément :

### Infrastructure
- [ ] PHP installé et accessible
- [ ] Cron système configuré
- [ ] Base de données accessible
- [ ] Permissions fichiers correctes

### Configuration Perfex
- [ ] Cron Perfex activé
- [ ] Last Cron Run récent (< 5 min)
- [ ] Auto-cron désactivé (si cron système actif)

### Configuration Dietetic
- [ ] Tables installées
- [ ] Canaux configurés (Email, SMS, WhatsApp)
- [ ] Préférences patients activées
- [ ] Horaires de notifications configurés

### Contacts patients
- [ ] Email valide
- [ ] Téléphone valide (pour SMS/WhatsApp)
- [ ] Contact principal défini

### Logs et monitoring
- [ ] Activity Log consulté
- [ ] Logs notifications consultés
- [ ] Aucune erreur PHP
- [ ] Aucune erreur SMTP

---

## 📊 Commandes Utiles

```bash
# Vérifier l'état du cron
crontab -l

# Tester le cron manuellement
php index.php cron/index

# Test complet avec détails
php modules/dietetic/test_cron.php

# Voir les logs en temps réel
tail -f /var/log/cron

# Vérifier les processus PHP
ps aux | grep php

# Tester la connexion à la base
mysql -u root -p dietezone -e "SELECT COUNT(*) FROM tbldietic_notification_preferences;"

# Vérifier les permissions
ls -la index.php
ls -la modules/dietetic/

# Voir les crons de tous les utilisateurs
sudo cat /var/spool/cron/crontabs/* 2>/dev/null
```

---

## 🆘 Si rien ne fonctionne

### Réinitialisation complète

```bash
# 1. Supprimer le cron existant
crontab -e
# (Supprimer les lignes Perfex/Dietetic)

# 2. Réinstaller le cron
bash modules/dietetic/setup_cron.sh

# 3. Tester immédiatement
php modules/dietetic/test_cron.php

# 4. Vérifier les logs
tail -f /var/log/cron &
php index.php cron/index

# 5. Consulter Activity Log dans Perfex
# Admin > Activity Log (rechercher "Dietetic Cron")
```

---

## 📞 Obtenir de l'aide

Avant de contacter le support, collectez ces informations :

```bash
# 1. Version PHP
php -v

# 2. Configuration cron
crontab -l

# 3. Last Cron Run
# Screenshot de Setup > Settings > Cron Job

# 4. Logs d'erreur
tail -n 50 /var/log/apache2/error.log  # ou nginx

# 5. Test manuel
php modules/dietetic/test_cron.php > test_output.txt 2>&1

# 6. Configuration base de données
grep DB_ application/config/app-config.php | grep -v PASSWORD
```

Envoyez ces informations avec votre demande d'assistance.

---

## ✅ Validation Finale

Après avoir résolu le problème, vérifiez :

1. **Le cron s'exécute automatiquement**
   ```bash
   # Attendre 5-10 minutes, puis vérifier
   grep "Dietetic Cron" /var/log/syslog | tail -n 5
   ```

2. **Les notifications sont reçues**
   - Email reçu ✅
   - SMS reçu ✅ (si configuré)
   - WhatsApp reçu ✅ (si configuré)

3. **Les logs sont propres**
   - Admin > Activity Log : Pas d'erreur
   - Admin > Dietetic > Notifications > Logs : Notifications envoyées

4. **Les patients reçoivent bien les rappels**
   - Demander confirmation à 2-3 patients

---

**Si tout fonctionne, félicitations ! 🎉**

Le système est maintenant opérationnel et les patients recevront automatiquement leurs rappels de notifications.
