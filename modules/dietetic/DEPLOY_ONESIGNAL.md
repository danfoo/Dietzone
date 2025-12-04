# 🚀 Déploiement OneSignal - Guide Rapide

## Option 1 : Via Ligne de Commande (Recommandé)

```bash
# Aller dans le dossier de l'application
cd /home/trpuftja/app

# Exécuter le script de déploiement
php modules/dietetic/deploy_onesignal.php
```

**Avantages** :
- ✅ Utilise automatiquement les credentials Perfex CRM
- ✅ Pas besoin du mot de passe root MySQL
- ✅ Vérification automatique de l'état
- ✅ Rapport détaillé

**Sortie attendue** :

```
╔════════════════════════════════════════════════════════════════╗
║        DIETZONE - OneSignal Migration Deployment              ║
║  Migration: Firebase → OneSignal                              ║
╚════════════════════════════════════════════════════════════════╝

📋 Étape 1/6 : Chargement de la configuration...
✅ Configuration chargée

📋 Étape 2/6 : Connexion à la base de données...
   Host: localhost
   User: your_db_user
   Database: perfexcrm
✅ Connexion établie

📋 Étape 3/6 : Vérification de l'état actuel...
⚠️  Colonne onesignal_player_id manquante
⚠️  Settings OneSignal manquants

🚀 Migration nécessaire, démarrage...

📋 Étape 4/6 : Exécution de la migration SQL...
   Nombre de commandes SQL à exécuter: 8

   Exécution commande 1... ✅
   Exécution commande 2... ✅
   Exécution commande 3... ✅
   [...]

   Résultat: 8 succès, 0 erreurs
✅ Migration SQL terminée

📋 Étape 5/6 : Vérification de la migration...
   ✅ Colonne onesignal_player_id créée
   ✅ Index idx_player_id créé
   ✅ Settings OneSignal créés (4 entrées)
   ✅ Table de migration créée

   Résultat: 4/4 vérifications passées
✅ Vérification terminée

📋 Étape 6/6 : Résumé final...
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 État de la base de données:
   Total appareils      : 15
   Appareils actifs     : 12
   OneSignal Players    : 0
   Firebase Tokens      : 12

⚙️  Settings OneSignal:
   onesignal_app_id              : (vide)
   onesignal_rest_api_key        : (vide)
   onesignal_user_auth_key       : (vide)
   onesignal_web_enabled         : 1

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ MIGRATION TERMINÉE AVEC SUCCÈS !

📝 Prochaines étapes:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
1️⃣  Configurer OneSignal dans l'admin
2️⃣  Uploader OneSignalSDKWorker.js à la racine
3️⃣  Mettre à jour les vues frontend
4️⃣  Configurer Median Dashboard
5️⃣  Rebuild l'APK Median
```

---

## Option 2 : Via Navigateur Web

Si vous ne pouvez pas utiliser SSH/CLI :

```
1. Ouvrez votre navigateur
2. Allez sur: https://app.dietsenegal.net/modules/dietetic/deploy_onesignal.php
3. Connectez-vous en tant qu'admin
4. Le script s'exécutera automatiquement
```

**⚠️ Important** : Cette URL doit être accessible uniquement aux admins pour des raisons de sécurité.

---

## Option 3 : MySQL Direct (Si les 2 premières échouent)

Si vous avez les credentials MySQL de Perfex CRM :

```bash
# Trouver les credentials dans la configuration
cat application/config/database.php

# Exécuter la migration avec ces credentials
mysql -u VOTRE_USER -p VOTRE_DATABASE < modules/dietetic/migrations/migrate_to_onesignal.sql
```

Exemple :

```bash
# Si votre config montre:
# 'username' => 'trpuftja_crm'
# 'password' => 'VotreMotDePasse'
# 'database' => 'trpuftja_perfex'

mysql -u trpuftja_crm -p trpuftja_perfex < modules/dietetic/migrations/migrate_to_onesignal.sql
```

---

## Vérification Post-Déploiement

Après avoir exécuté le script, vérifiez que tout est OK :

```bash
# Test 1: Vérifier la colonne OneSignal
mysql -u VOTRE_USER -p VOTRE_DATABASE -e "DESCRIBE tbldietic_fcm_tokens;"

# Doit afficher une colonne "onesignal_player_id"

# Test 2: Vérifier les settings
mysql -u VOTRE_USER -p VOTRE_DATABASE -e "SELECT * FROM tbldietic_notification_settings WHERE setting_key LIKE 'onesignal%';"

# Doit afficher 4 lignes:
# - onesignal_app_id
# - onesignal_rest_api_key
# - onesignal_user_auth_key
# - onesignal_web_enabled
```

---

## Rollback (En cas de problème)

Si vous devez annuler la migration :

```bash
php modules/dietetic/rollback_onesignal.php
```

Ou manuellement :

```sql
-- Supprimer la colonne OneSignal
ALTER TABLE tbldietic_fcm_tokens DROP COLUMN onesignal_player_id;

-- Supprimer les settings
DELETE FROM tbldietic_notification_settings WHERE setting_key LIKE 'onesignal%';

-- Supprimer la table de migration (optionnelle)
DROP TABLE IF EXISTS tbldietic_onesignal_migration;
```

---

## Dépannage

### Erreur : "Access denied for user 'root'@'localhost'"

**Solution** : Utilisez `deploy_onesignal.php` (Option 1) qui utilise automatiquement les bons credentials.

### Erreur : "Configuration de base de données invalide"

**Cause** : Le fichier `application/config/database.php` est manquant ou corrompu.

**Solution** :

```bash
# Vérifier que le fichier existe
ls -la application/config/database.php

# Vérifier les permissions
chmod 644 application/config/database.php
```

### Erreur : "Duplicate column name 'onesignal_player_id'"

**Cause** : La migration a déjà été exécutée.

**Solution** : C'est normal ! La colonne existe déjà. Vous pouvez continuer avec la configuration OneSignal.

### Le script ne fait rien

**Cause** : La migration est déjà faite.

**Solution** : Vérifiez l'output du script, il doit dire "Migration déjà effectuée !". Passez à la configuration OneSignal.

---

## Prochaines Étapes Après Déploiement

Une fois la migration BDD terminée :

### 1. Configuration OneSignal (30 min)

```
→ Créer un compte: https://onesignal.com
→ Créer l'app "Dietzone"
→ Configurer Google Android (FCM)
→ Configurer Web Push
→ Récupérer: App ID + REST API Key
```

### 2. Configuration Admin Dietzone (5 min)

```
→ Admin → Diététique → Notifications → Settings
→ Onglet "OneSignal Push Notifications"
→ Remplir: App ID + REST API Key
→ Sauvegarder
```

### 3. Upload OneSignalSDKWorker.js (2 min)

```bash
# Télécharger depuis OneSignal Dashboard
# Placer à la racine du site
cp OneSignalSDKWorker.js /home/trpuftja/app/
chmod 644 /home/trpuftja/app/OneSignalSDKWorker.js
```

### 4. Configuration Median (15 min)

```
→ Median Dashboard: https://median.co/dashboard
→ App Settings → Push Notifications
→ Sélectionner "OneSignal"
→ Entrer votre OneSignal App ID
→ Save & Rebuild APK
```

### 5. Tests (30 min)

```
→ Test Web: Activer notifications sur le portail
→ Test Mobile: Installer APK et vérifier
→ Test Backend: Envoyer notification depuis OneSignal Dashboard
```

---

## Documentation Complète

Pour le guide complet de migration :

```bash
cat ONESIGNAL_MIGRATION.md
```

Ou en ligne :

```
https://github.com/danfoo/Dietzone/blob/claude/continue-dietzone-project-01AwUVDfrM2xTmgMuSeMTCpE/ONESIGNAL_MIGRATION.md
```

---

## Support

En cas de problème :

1. **Logs Perfex CRM** : `application/logs/`
2. **Logs Activity** : Admin → Setup → Activity Log
3. **Issue GitHub** : https://github.com/danfoo/Dietzone/issues
4. **Documentation OneSignal** : https://documentation.onesignal.com

---

**Bon déploiement ! 🚀**
