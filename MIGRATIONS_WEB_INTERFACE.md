# 🚀 Interface Web de Migrations SQL - DietZone

## 📋 Vue d'ensemble

Interface web admin pour gérer facilement toutes les migrations SQL du système de notifications sans utiliser la ligne de commande.

## 🌐 Accès à l'interface

### URL directe:
```
https://app.dietsenegal.net/admin/dietetic/notifications/migrations
```

### Depuis le menu:
```
Admin > Diététique > Notifications > (En haut à droite)
```

---

## 🎯 Fonctionnalités

### ✅ 3 Migrations disponibles

1. **Système de Notifications de Base**
   - Tables : preferences, logs, milestones, settings
   - Fichier : `add_notifications_system.sql`
   - Requis : Oui (base du système)

2. **Notifications Push Firebase**
   - Tables : fcm_tokens
   - Modifications : channel_push dans preferences
   - Fichier : `add_firebase_push_notifications.sql`
   - Requis : Non (optionnel)

3. **Optimisations Performance**
   - Index composites pour requêtes rapides
   - Fichier : `optimize_notifications_performance.sql`
   - Requis : Non (recommandé)

---

## 🔧 Utilisation

### Vérifier le statut

**Bouton "Vérifier tout"** :
- Scanne toutes les migrations
- Affiche le statut : ✅ Installé ou ⏳ En attente
- Aucun risque, lecture seule

### Installer une migration

**Bouton "Installer"** sur une carte :
1. Cliquez sur "Installer"
2. Confirmez l'action
3. Attendez le résultat (3-10 secondes)
4. Statut mis à jour automatiquement

### Installer toutes les migrations

**Bouton "Tout installer"** :
1. Installe toutes les migrations manquantes
2. Ordre automatique : Base → Firebase → Optimisations
3. Barre de progression visible
4. Rechargement auto après succès

---

## 🎨 Interface

### Cartes de migration

Chaque migration affiche :
- **En-tête** : Nom + Icône + Statut
- **Description** : Explication simple
- **Tables créées** : Liste des modifications
- **Footer** : Nom du fichier SQL + Boutons d'action

### Statuts possibles

| Statut | Couleur | Signification |
|--------|---------|---------------|
| ⏳ En attente | Orange | Migration pas encore exécutée |
| ✅ Installé | Vert | Migration déjà appliquée |
| ❌ Erreur | Rouge | Erreur lors de l'installation |

### Alertes

- **Succès** (Vert) : Migration réussie
- **Erreur** (Rouge) : Échec de migration
- **Info** (Bleu) : Migration déjà installée

---

## 🔒 Sécurité

### Protection

✅ **Accès admin uniquement** : Requiert droits administrateur
✅ **Confirmation** : Dialogue de confirmation avant installation
✅ **Détection doublons** : Ignore les tables/colonnes déjà existantes
✅ **Logs activité** : Toutes les actions sont loggées
✅ **Rollback-safe** : Les erreurs n'interrompent pas le processus

### Gestion des erreurs

- **Duplicate key** : Ignorée automatiquement
- **Table exists** : Ignorée automatiquement
- **Duplicate column** : Ignorée automatiquement
- **Autres erreurs** : Affichées à l'utilisateur

---

## 📊 Détection intelligente

### Vérification "Notifications de Base"

Vérifie l'existence de :
```
✓ tbldietic_notification_preferences
✓ tbldietic_notification_logs
✓ tbldietic_milestones
✓ tbldietic_notification_settings
```

### Vérification "Firebase Push"

Vérifie :
```
✓ tbldietic_fcm_tokens
✓ Colonne channel_push dans preferences
```

### Vérification "Optimisations"

Toujours marqué comme "Indexes actifs" car :
- Les index ne cassent rien s'ils existent déjà
- MySQL les ignore automatiquement
- Pas de moyen fiable de vérifier tous les index

---

## 🛠️ Fonctionnement technique

### Architecture

```
Vue: migrations.php
   ↓ (AJAX)
Contrôleur: Notifications.php
   ├── check_migration()  → Vérifie statut
   └── execute_migration() → Exécute SQL
       ↓
Fichiers SQL: migrations/*.sql
```

### Flux d'exécution

1. **Chargement page** :
   - Auto-vérification de toutes les migrations
   - Affichage des statuts

2. **Clic "Installer"** :
   - Confirmation utilisateur
   - POST AJAX vers `execute_migration`
   - Lecture du fichier SQL
   - Remplacement du préfixe `tbldietic_` → `tblperfex_dietic_`
   - Exécution statement par statement
   - Gestion des erreurs
   - Réponse JSON

3. **Affichage résultat** :
   - Mise à jour du statut
   - Alert-box (succès/erreur)
   - Rechargement si tout OK

---

## 📝 Ordre recommandé

### Installation séquentielle

```
1. Système de Notifications (REQUIS)
   ↓
2. Firebase Push (OPTIONNEL)
   ↓
3. Optimisations Performance (RECOMMANDÉ)
```

### Ou en un clic

Utilisez **"Tout installer"** qui respecte automatiquement cet ordre.

---

## 🚨 Dépannage

### Migration déjà installée

**Symptôme** : Bouton "Installer" grisé
**Cause** : Les tables existent déjà
**Solution** : Aucune action nécessaire ✅

### Erreur "Fichier introuvable"

**Cause** : Fichier SQL manquant
**Vérifiez** :
```bash
ls modules/dietetic/migrations/
```

**Fichiers requis** :
- add_notifications_system.sql
- add_firebase_push_notifications.sql
- optimize_notifications_performance.sql

### Erreur de permissions

**Cause** : Utilisateur MySQL n'a pas CREATE/ALTER
**Solution** :
```sql
GRANT ALL PRIVILEGES ON perfexcrm.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Migration partielle

**Symptôme** : "5 requêtes réussies, 2 échouées"
**Cause** : Certaines tables existent déjà
**Solution** : Normal, ignorez si les tables importantes existent

---

## 🔍 Vérifications manuelles

### Via SQL

```sql
-- Vérifier les tables
SHOW TABLES LIKE 'tbldietic_notification%';
SHOW TABLES LIKE 'tbldietic_fcm%';
SHOW TABLES LIKE 'tbldietic_milestones';

-- Vérifier la colonne channel_push
DESCRIBE tbldietic_notification_preferences;

-- Vérifier les index
SHOW INDEX FROM tbldietic_notification_logs;
```

### Via interface

```
Admin > Diététique > Notifications > Settings
```

Si la page charge correctement = Système installé ✅

---

## 💡 Conseils

### Avant de commencer

1. ✅ **Sauvegarde BDD** : Exportez la base
2. ✅ **Testez en dev** : D'abord sur environnement de test
3. ✅ **Vérifiez permissions** : User MySQL a CREATE/ALTER

### Pendant l'installation

1. 🚫 **Ne fermez pas la page** pendant l'installation
2. 🚫 **N'interrompez pas** le processus
3. ✅ **Attendez** la fin complète

### Après l'installation

1. ✅ **Vérifiez** : Cliquez "Vérifier tout"
2. ✅ **Testez** : Allez dans Settings > Notifications
3. ✅ **Configurez** : Activez les canaux (Email/SMS/Push)

---

## 📞 Support

### Logs de debug

Consultez :
```
Admin > Utilities > Activity Log
```

Recherchez : "Notifications Migration"

### Fichiers sources

```
Vue:        modules/dietetic/views/admin/notifications/migrations.php
Contrôleur: modules/dietetic/controllers/Notifications.php (lignes 353-573)
Migrations: modules/dietetic/migrations/*.sql
```

---

## 🎉 Résultat final

Après installation complète, vous aurez :

✅ **4 tables** de notifications de base
✅ **1 table** FCM pour push notifications
✅ **10+ index** pour performances optimales
✅ **Menu admin** fonctionnel
✅ **Interface settings** accessible
✅ **Système prêt** à envoyer des notifications

---

**Page accessible à** : https://app.dietsenegal.net/admin/dietetic/notifications/migrations

**Temps d'installation** : ~10-15 secondes pour tout

**Facilité** : 🟢🟢🟢🟢🟢 (Très facile)
