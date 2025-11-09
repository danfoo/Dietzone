# 🔐 Système de Permissions Granulaires - Module Diététique

## 📋 Vue d'ensemble

Le système de permissions granulaires permet aux **administrateurs** de contrôler précisément quelles fonctionnalités chaque diététicien peut utiliser.

### Fonctionnalités contrôlées :

| Permission | Description | Par défaut | Réservé Admin |
|------------|-------------|------------|---------------|
| **food_surveys** | Accès aux Enquêtes Alimentaires | ❌ Non | Non |
| **notifications_manage** | Gestion des paramètres de notifications | ❌ Non | ✅ Oui |
| **reports_advanced** | Rapports et statistiques avancés | ❌ Non | Non |
| **settings_module** | Paramètres généraux du module | ❌ Non | ✅ Oui |

---

## 🚀 Installation

### Étape 1 : Exécuter la migration SQL

1. Connectez-vous en tant qu'**administrateur**
2. Allez dans **Diététique > Notifications > Migrations**
3. Localisez la carte **"Système de Permissions Granulaires"**
4. Cliquez sur **"Vérifier"** pour voir si déjà installé
5. Cliquez sur **"Installer"** pour créer la table `tbldietic_staff_permissions`

Ou via SQL directement :
```sql
-- Exécutez le fichier
modules/dietetic/migrations/add_staff_permissions.sql
```

### Étape 2 : Configurer les permissions

1. Allez dans **Diététique > Permissions Diététiciens**
2. Vous verrez tous les membres du staff avec leurs permissions actuelles
3. Utilisez les **toggles** pour activer/désactiver les permissions
4. Les changements sont **appliqués instantanément**

---

## 🎯 Utilisation

### Interface de Gestion des Permissions

**Chemin** : `/admin/dietetic/staff_permissions`

![Interface permissions](https://via.placeholder.com/800x400?text=Interface+Permissions)

#### Fonctionnalités :

- ✅ **Vue tableau** : Toutes les permissions de tous les diététiciens en un coup d'œil
- ✅ **Toggle switches** : Activation/désactivation instantanée
- ✅ **Badges Admin** : Les admins ont automatiquement tous les accès
- ✅ **Tooltips** : Descriptions des permissions au survol
- ✅ **Actions groupées** : Réinitialiser toutes les permissions d'un utilisateur
- ✅ **Légende** : Description détaillée de chaque permission

#### Exemples d'utilisation :

**Scénario 1 : Donner accès aux Enquêtes Alimentaires**
1. Trouvez le diététicien dans la liste
2. Localisez la colonne "Enquêtes Alimentaires"
3. Activez le toggle (devient vert)
4. ✅ Le diététicien voit maintenant le menu "Enquêtes Alimentaires"

**Scénario 2 : Retirer l'accès**
1. Trouvez le diététicien
2. Désactivez le toggle (devient gris)
3. ❌ Le menu disparaît immédiatement pour ce diététicien

**Scénario 3 : Réinitialiser toutes les permissions**
1. Cliquez sur **"Réinitialiser"** dans la colonne Actions
2. Confirmez l'action
3. ✅ Toutes les permissions sont supprimées (revient aux valeurs par défaut)

---

## 🔒 Sécurité

### Admins

Les **administrateurs** (staffid=1 ou is_admin=1) :
- ✅ Ont **toujours** accès à toutes les fonctionnalités
- ✅ Voient **toutes** les entrées du menu
- ✅ Peuvent gérer les permissions des autres
- ❌ Ne peuvent PAS se retirer leurs propres permissions

### Diététiciens (Non-admin)

Les **diététiciens non-admins** :
- ❌ Voient **seulement** les menus pour lesquels ils ont une permission
- ❌ Sont **redirigés** s'ils tentent d'accéder à une fonctionnalité interdite
- ✅ Reçoivent un **message clair** : "Vous n'avez pas accès à..."
- ✅ Toutes les tentatives non autorisées sont **loguées**

### Vérifications de Sécurité

Le système vérifie les permissions à **3 niveaux** :

```
┌─────────────────────────────────────┐
│  1. MENU (dietetic.php)             │
│  ✓ Les items sont cachés si pas     │
│    de permission                     │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│  2. CONTRÔLEUR (__construct)        │
│  ✓ Vérifie dietetic_has_feature_    │
│    permission()                      │
│  ✓ Redirige si accès refusé          │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│  3. MODÈLE (si applicable)          │
│  ✓ Sécurité supplémentaire au       │
│    niveau données                    │
└─────────────────────────────────────┘
```

---

## 📊 Impact sur les Fonctionnalités

### Enquêtes Alimentaires (`food_surveys`)

**Avec permission** :
- ✅ Menu "Enquêtes Alimentaires" visible
- ✅ Peut créer/modifier/supprimer des enquêtes
- ✅ Peut voir les résultats des enquêtes de ses patients

**Sans permission** :
- ❌ Menu caché
- ❌ URL `/dietetic/food_surveys` redirige vers `/dietetic/patients`
- ⚠️ Message : "Vous n'avez pas accès au module Enquêtes Alimentaires"

### Gestion Notifications (`notifications_manage`)

**⚠️ Réservé aux admins seulement**

**Avec permission (Admin)** :
- ✅ Menu "Notifications" visible
- ✅ Accès aux paramètres SMS/WhatsApp/Firebase
- ✅ Peut gérer les templates de notifications
- ✅ Voit les logs de notifications

**Sans permission** :
- ❌ Menu caché
- ❌ URL `/dietetic/notifications` redirige vers `/dietetic/patients`
- ⚠️ Message : "Fonctionnalité réservée aux administrateurs"

---

## 🛠️ Configuration Avancée

### Valeurs par Défaut

Modifiez les valeurs par défaut pour les nouveaux diététiciens :

```sql
-- Dans la table tbldietic_settings
UPDATE tbldietic_settings
SET setting_value = '1'  -- 1 = activé, 0 = désactivé
WHERE setting_key = 'permissions_default_food_surveys';
```

### Copier les Permissions

Vous pouvez copier toutes les permissions d'un diététicien à un autre :

1. Dans l'interface, utilisez le formulaire "Copier permissions"
2. Sélectionnez le diététicien source
3. Sélectionnez le diététicien destination
4. ✅ Toutes les permissions sont copiées instantanément

### Permissions en Masse via SQL

```sql
-- Donner food_surveys à tous les diététiciens actifs
INSERT INTO tbldietic_staff_permissions (staff_id, permission_key, permission_value, granted_by)
SELECT
    staffid,
    'food_surveys',
    1,
    1  -- Granted by admin
FROM tblstaff
WHERE active = 1 AND admin = 0
ON DUPLICATE KEY UPDATE permission_value = 1;
```

---

## 🔍 Vérification et Débogage

### Vérifier si un diététicien a une permission

**Via Interface** :
1. Allez dans **Diététique > Permissions Diététiciens**
2. Cherchez le diététicien
3. Regardez les toggles verts/gris

**Via Code PHP** :
```php
// Vérifier si le diététicien actuel a accès aux enquêtes
if (dietetic_has_feature_permission('food_surveys')) {
    echo "Accès autorisé";
} else {
    echo "Accès refusé";
}

// Vérifier pour un diététicien spécifique
$staff_id = 5;
if (dietetic_has_feature_permission('food_surveys', $staff_id)) {
    echo "Le staff #5 a accès";
}
```

**Via SQL** :
```sql
-- Voir toutes les permissions d'un diététicien
SELECT
    sp.permission_key,
    sp.permission_value,
    sp.granted_at,
    CONCAT(s.firstname, ' ', s.lastname) as granted_by_name
FROM tbldietic_staff_permissions sp
LEFT JOIN tblstaff s ON s.staffid = sp.granted_by
WHERE sp.staff_id = 5;  -- Remplacer 5 par le staffid
```

### Logs d'Accès Non Autorisé

```sql
-- Voir les tentatives d'accès refusées
SELECT * FROM tblactivitylog
WHERE description LIKE '%Unauthorized%'
ORDER BY date DESC
LIMIT 50;
```

---

## 📚 Fonctions Helper Disponibles

### `dietetic_has_feature_permission($key, $staff_id = null)`

Vérifie si un utilisateur a une permission spécifique.

**Paramètres** :
- `$key` : Clé de permission ('food_surveys', 'notifications_manage', etc.)
- `$staff_id` : ID du staff (null = utilisateur actuel)

**Retourne** : `true` si autorisé, `false` sinon

**Exemples** :
```php
// Utilisateur actuel
if (dietetic_has_feature_permission('food_surveys')) {
    // Afficher le bouton "Nouvelle enquête"
}

// Utilisateur spécifique
if (dietetic_has_feature_permission('reports_advanced', 12)) {
    // Le staff #12 peut voir les rapports avancés
}
```

### `dietetic_grant_permission($staff_id, $key, $enabled, $notes = null)`

Attribue ou retire une permission (admin seulement).

**Paramètres** :
- `$staff_id` : ID du diététicien
- `$key` : Clé de permission
- `$enabled` : `true` pour activer, `false` pour désactiver
- `$notes` : Notes optionnelles

**Retourne** : `true` si succès, `false` sinon

**Exemples** :
```php
// Donner accès aux enquêtes
dietetic_grant_permission(5, 'food_surveys', true, 'Formé aux enquêtes alimentaires');

// Retirer l'accès
dietetic_grant_permission(5, 'food_surveys', false);
```

### `dietetic_get_staff_permissions($staff_id = null)`

Récupère toutes les permissions d'un utilisateur.

**Paramètres** :
- `$staff_id` : ID du staff (null = utilisateur actuel)

**Retourne** : Array associatif `['permission_key' => bool]`

**Exemple** :
```php
$permissions = dietetic_get_staff_permissions(5);
// ['food_surveys' => true, 'notifications_manage' => false, ...]

foreach ($permissions as $key => $enabled) {
    echo "$key: " . ($enabled ? 'OUI' : 'NON') . "\n";
}
```

### `dietetic_get_available_permissions()`

Liste toutes les permissions disponibles avec leurs descriptions.

**Retourne** : Array de permissions avec métadonnées

**Exemple** :
```php
$available = dietetic_get_available_permissions();

foreach ($available as $key => $info) {
    echo "{$info['label']}: {$info['description']}\n";
    if (isset($info['admin_only'])) {
        echo "  (Réservé aux admins)\n";
    }
}
```

---

## ❓ FAQ

### Q1 : Comment activer les Enquêtes Alimentaires pour tous les diététiciens ?

**R** : Deux options :

**Option A (Interface)** :
1. Allez dans **Diététique > Permissions Diététiciens**
2. Activez le toggle "Enquêtes Alimentaires" pour chaque diététicien

**Option B (SQL - plus rapide)** :
```sql
INSERT INTO tbldietic_staff_permissions (staff_id, permission_key, permission_value, granted_by)
SELECT staffid, 'food_surveys', 1, 1
FROM tblstaff WHERE active = 1 AND admin = 0
ON DUPLICATE KEY UPDATE permission_value = 1;
```

### Q2 : Pourquoi un diététicien ne voit-il pas le menu Enquêtes ?

**Vérifications** :
1. ✅ La migration `add_staff_permissions.sql` est installée ?
2. ✅ Le diététicien a la permission `food_surveys` activée ?
3. ✅ Le diététicien n'est pas en mode "Admin Preview" ?
4. ✅ Le cache du navigateur est vidé ?

### Q3 : Un admin peut-il perdre ses permissions ?

**R** : **NON**. Les admins (is_admin=1) contournent **toutes** les vérifications de permissions. Ils ont toujours accès à tout.

### Q4 : Que se passe-t-il si la table des permissions n'existe pas ?

**R** : Le système est conçu pour être **sécurisé par défaut** :
- Si la table n'existe pas → Accès **REFUSÉ**
- Si la permission n'existe pas → Accès **REFUSÉ**
- Seuls les admins contournent cette règle

### Q5 : Comment supprimer complètement le système de permissions ?

**⚠️ Non recommandé**, mais si nécessaire :

```sql
-- 1. Supprimer la table
DROP TABLE IF EXISTS tbldietic_staff_permissions;

-- 2. Supprimer les paramètres
DELETE FROM tbldietic_settings
WHERE setting_key LIKE 'permissions_default_%';

-- 3. Redémarrer le serveur web pour vider le cache
```

Ensuite, commentez les vérifications dans les contrôleurs.

---

## 🎓 Bonnes Pratiques

### ✅ À FAIRE

- ✅ Toujours tester avec un compte non-admin
- ✅ Documenter pourquoi chaque permission est accordée
- ✅ Utiliser l'interface web (plus sûr que SQL direct)
- ✅ Faire des sauvegardes régulières de `tbldietic_staff_permissions`
- ✅ Vérifier les logs d'accès non autorisé régulièrement

### ❌ À NE PAS FAIRE

- ❌ Ne jamais modifier manuellement les permissions d'un admin
- ❌ Ne pas donner `notifications_manage` aux non-admins (réservé admins)
- ❌ Ne pas supprimer la table sans désactiver le code
- ❌ Ne pas oublier d'informer les diététiciens des changements
- ❌ Ne pas donner toutes les permissions par défaut (principe du moindre privilège)

---

## 📞 Support

**Problème avec les permissions ?**

1. Vérifiez les logs : `Admin > Utilities > Activity Log` → Recherche "Unauthorized"
2. Testez en mode admin pour confirmer que la fonctionnalité marche
3. Vérifiez la table SQL : `SELECT * FROM tbldietic_staff_permissions WHERE staff_id = X`
4. Contactez le développeur avec les logs

**Demande de nouvelle permission ?**

Modifiez `dietetic_get_available_permissions()` dans `dietetic_helper.php` :
```php
'ma_nouvelle_permission' => [
    'label' => 'Ma Fonctionnalité',
    'description' => 'Description de ce que ça fait',
    'default' => false,
    'admin_only' => false,  // true si réservé admins
],
```

---

**Version** : 1.0.0
**Dernière mise à jour** : 2025-11-09
**Auteur** : Module Diététique - Perfex CRM
