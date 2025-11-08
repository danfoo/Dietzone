# Guide de Configuration des Notifications - Module Diététique

## 🔍 Diagnostic: Pourquoi vous ne recevez pas de notifications

Le système de notifications est **entièrement codé** mais **pas activé**. Voici pourquoi:

### ❌ Problèmes identifiés:

1. **Tables de base de données non créées**
   - Les tables `tbldietic_notification_preferences`, `tbldietic_notification_logs`, `tbldietic_milestones` n'existent pas
   - Sans ces tables, aucune notification ne peut être envoyée

2. **Cron job non configuré**
   - Le fichier `cron_notifications.php` existe mais n'est pas exécuté automatiquement
   - Les rappels automatiques (pesée, eau, consultations) ne s'exécutent jamais

3. **Configuration email potentiellement manquante**
   - Perfex CRM doit avoir une configuration email valide

---

## ✅ Solution: Installation en 4 étapes

### Étape 1: Installer les tables de notifications

```bash
cd /home/user/Dietzone
bash install_notifications.sh
```

OU manuellement:

```bash
mysql -u root -proot perfexcrm < modules/dietetic/migrations/add_notifications_system.sql
```

### Étape 2: Configurer le CRON job

Ajoutez cette ligne à votre crontab (exécute toutes les heures):

```bash
crontab -e
```

Ajoutez:
```
0 * * * * cd /home/user/Dietzone && php modules/dietetic/cron_notifications.php >> /var/log/dietetic_notifications.log 2>&1
```

### Étape 3: Configurer les emails dans Perfex

1. Connectez-vous en admin: https://app.dietsenegal.net/admin
2. Allez dans **Setup > Settings > Email**
3. Configurez:
   - **Email Protocol**: SMTP
   - **SMTP Host**: votre serveur SMTP
   - **SMTP Port**: 587 ou 465
   - **SMTP Username**: votre email
   - **SMTP Password**: votre mot de passe
   - **Email From**: noreply@dietsenegal.net
   - **Email From Name**: DietSenegal

### Étape 4: Tester le système

```bash
cd /home/user/Dietzone
php modules/dietetic/cron_notifications.php
```

Vous devriez voir:
```
========================================
Dietetic Notifications Cron Job
Started at: 2025-01-XX XX:XX:XX
========================================

Checking weight reminders...
Checking water reminders...
...
```

---

## 📧 Types de notifications disponibles

### 1. **Notifications instantanées** (déclenchées automatiquement):

✅ **Nouvelle recommandation**
- Quand: Le diététicien ajoute une recommandation sur une entrée alimentaire
- Destinataire: Patient concerné
- Canal: Email (si activé dans préférences)

✅ **Consultation programmée**
- Quand: Une nouvelle consultation est créée ou modifiée
- Destinataire: Patient concerné
- Canal: Email (si activé)

✅ **Commentaire ajouté**
- Quand: Le diététicien commente une recommandation
- Destinataire: Patient concerné
- Canal: Email

### 2. **Rappels automatiques** (via CRON):

⏰ **Rappel pesée hebdomadaire**
- Fréquence: 1x/semaine (jour et heure configurables par patient)
- Par défaut: Vendredi à 9h00

⏰ **Rappels hydratation**
- Fréquence: 3x/jour par défaut
- Heures: 10h00, 14h00, 18h00 (configurables)

⏰ **Rappel consultation (J-1)**
- Quand: 24h avant la consultation
- Exécuté: Toutes les heures par le CRON

⏰ **Rappel consultation (H-1)**
- Quand: 1h avant la consultation
- Exécuté: Toutes les heures par le CRON

⏰ **Rappel soumission repas**
- Quand: 18h si le patient n'a pas soumis ses repas du jour
- Uniquement pour les enquêtes actives

### 3. **Milestones** (jalons):

🎉 **Perte de poids**
- 5kg perdus, 10kg perdus, etc.

🎯 **Objectif atteint**
- Poids cible atteint

📅 **Programme complété**
- Fin du programme avec succès

---

## ⚙️ Préférences patients

Chaque patient peut configurer ses préférences depuis:
**Portail Patient > Mon Profil > Préférences de notifications**

Options disponibles:
- ✅ Activer/désactiver chaque type de notification
- ⏰ Choisir le jour et l'heure pour rappel pesée
- ⏰ Choisir les heures pour rappels eau
- 📧 Activer/désactiver email
- 📱 Activer/désactiver SMS (si configuré)
- 💬 Activer/désactiver WhatsApp (si configuré)

---

## 🔧 Vérifications admin

### Voir les logs de notifications:

**Admin > Diététique > Notifications > Logs**

Vous y verrez:
- Toutes les notifications envoyées
- Statut (envoyé/échoué)
- Canal utilisé (email/SMS/WhatsApp)
- Date et heure

### Voir les préférences patients:

**Admin > Diététique > Notifications > Préférences**

### Tester une notification:

**Admin > Diététique > Notifications > Test**

---

## 📊 Monitoring

### Vérifier que le CRON s'exécute:

```bash
tail -f /var/log/dietetic_notifications.log
```

### Vérifier les notifications envoyées:

```sql
SELECT * FROM tbldietic_notification_logs
ORDER BY created_at DESC
LIMIT 10;
```

### Vérifier les préférences d'un patient:

```sql
SELECT * FROM tbldietic_notification_preferences
WHERE patient_id = 1;
```

---

## 🚨 Dépannage

### Aucune notification n'est envoyée:

1. ✅ Vérifier que les tables existent:
   ```sql
   SHOW TABLES LIKE 'tbldietic_notification%';
   ```

2. ✅ Vérifier que le CRON s'exécute:
   ```bash
   grep dietetic /var/log/cron
   ```

3. ✅ Vérifier la config email Perfex:
   - Admin > Setup > Settings > Email
   - Tester avec "Send Test Email"

4. ✅ Vérifier les préférences patient:
   - Le patient a-t-il activé les notifications?
   - L'email du patient est-il valide?

### Les emails ne partent pas:

1. ✅ Vérifier la configuration SMTP dans Perfex
2. ✅ Vérifier les logs Perfex: `application/logs/`
3. ✅ Vérifier que l'email "From" est autorisé par votre serveur SMTP

### Le CRON ne s'exécute pas:

1. ✅ Vérifier que la ligne est dans le crontab: `crontab -l`
2. ✅ Vérifier les permissions: `chmod +x modules/dietetic/cron_notifications.php`
3. ✅ Vérifier les logs: `tail -f /var/log/dietetic_notifications.log`

---

## 📝 Fichiers importants

- **Modèle**: `modules/dietetic/models/Dietetic_notifications_model.php`
- **CRON**: `modules/dietetic/cron_notifications.php`
- **Migration SQL**: `modules/dietetic/migrations/add_notifications_system.sql`
- **Contrôleur admin**: `modules/dietetic/controllers/Notifications.php`
- **Préférences patient**: `modules/dietetic/views/portal/notifications/preferences.php`

---

## ✨ Après installation

Une fois tout configuré, vous recevrez:

1. ✉️ **Email instantané** quand le diététicien:
   - Ajoute une recommandation sur vos repas
   - Programme une consultation
   - Commente une de vos entrées

2. ⏰ **Rappels automatiques**:
   - Pesée hebdomadaire (vendredi 9h par défaut)
   - Hydratation (3x/jour)
   - Consultation à venir (J-1 et H-1)
   - Soumission repas (18h si non fait)

3. 🎉 **Célébrations**:
   - Chaque palier de poids atteint
   - Objectif atteint
   - Programme complété

---

**Besoin d'aide?** Consultez les logs dans **Admin > Diététique > Notifications > Logs**
