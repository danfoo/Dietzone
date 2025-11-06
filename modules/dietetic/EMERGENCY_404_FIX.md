# Fix d'Urgence pour Erreur 404 Persistante

## Situation Actuelle

Erreur 404 persistante sur : `https://app.dietsenegal.net/admin/clients/client/8?group=contacts&new_contact=true`

Cette erreur se produit **uniquement** quand le module diététique est activé.

## Fix Urgent en 3 Étapes

### Étape 1 : Diagnostic Rapide

Exécutez le script de diagnostic pour identifier la cause exacte :

```bash
cd /path/to/perfex
php -r "define('BASEPATH', true); \$CI =& get_instance(); require_once('modules/dietetic/diagnose_404.php');"
```

Ou créez un fichier `run_diagnose.php` à la racine de Perfex :

```php
<?php
define('BASEPATH', 1);
require_once('index.php');
require_once(__DIR__ . '/modules/dietetic/diagnose_404.php');
```

Exécutez : `php run_diagnose.php`

Le script vous dira exactement quel composant cause le problème.

### Étape 2 : Test avec Hooks Désactivés

**But** : Confirmer si le problème vient des hooks du module

1. Ouvrez le fichier `modules/dietetic/dietetic.php`

2. Ajoutez cette ligne **juste après** `<?php` (ligne 2) :
   ```php
   require_once(__DIR__ . '/disable_hooks_temporarily.php');
   ```

3. Testez l'accès à `/admin/clients/client/8`

4. Si ça fonctionne maintenant :
   - ✅ Le problème vient des hooks
   - Passez à l'Étape 3

5. Si ça ne fonctionne toujours pas :
   - ❌ Le problème vient d'ailleurs (probablement FK ou DB)
   - Passez directement à l'Option B de l'Étape 3

6. **N'oubliez pas** de supprimer la ligne ajoutée après le test

### Étape 3 : Application du Fix

#### Option A : Si les Hooks sont la Cause

Modifiez `modules/dietetic/dietetic.php` ligne 157 :

**REMPLACEZ** :
```php
hooks()->add_action('customer_profile_tabs', 'dietetic_add_customer_profile_tab');
```

**PAR** :
```php
// Temporarily disabled to fix 404 errors
// hooks()->add_action('customer_profile_tabs', 'dietetic_add_customer_profile_tab');
```

Ceci désactive complètement le hook qui ajoute l'onglet diététique sur les profils clients.

**Impact** : L'onglet "Dietetic Follow-up" ne s'affichera plus sur les profils clients, mais tout le reste fonctionne.

**Pour accéder aux patients** : Utilisez le menu Dietetic > Patients

#### Option B : Fix de la Base de Données (si FK sont la cause)

Exécutez dans phpMyAdmin ou MySQL CLI :

```sql
-- Désactiver les vérifications FK
SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer toutes les contraintes FK de la table patients
ALTER TABLE `tbldietic_patients` DROP FOREIGN KEY `fk_diet_patients_client`;
ALTER TABLE `tbldietic_patients` DROP FOREIGN KEY `fk_diet_patients_staff`;

-- Réactiver les vérifications FK
SET FOREIGN_KEY_CHECKS = 1;
```

**⚠️ ATTENTION** : Ceci supprime les contraintes d'intégrité référentielle. Les données ne seront plus protégées automatiquement.

**Impact** :
- ✅ Résout les blocages de transaction
- ⚠️ Possibilité de records orphelins si vous supprimez des clients
- ⚠️ Responsabilité manuelle de maintenir l'intégrité

#### Option C : Désinstallation Temporaire

Si ni A ni B ne fonctionnent :

1. **Sauvegardez vos données** :
   ```sql
   -- Backup des tables importantes
   CREATE TABLE backup_dietic_patients AS SELECT * FROM tbldietic_patients;
   CREATE TABLE backup_dietic_measurements AS SELECT * FROM tbldietic_measurements;
   CREATE TABLE backup_dietic_programs AS SELECT * FROM tbldietic_programs;
   ```

2. Désactivez le module dans Setup > Modules

3. Testez `/admin/clients/client/8`

4. Si ça marche, le problème est confirmé dans le module

5. Réactivez le module et procédez avec une investigation approfondie

## Vérification PHP Error Logs

Le plus important est de **voir l'erreur PHP réelle**. Suivez ces logs en temps réel pendant que vous essayez d'accéder à la page :

### Sur Apache :
```bash
tail -f /var/log/apache2/error.log
```

### Sur Nginx :
```bash
tail -f /var/log/nginx/error.log
```

### Log PHP directement :
```bash
tail -f /var/log/php-fpm/error.log
# ou
tail -f /var/log/php/error.log
```

**Pendant que le log tourne** : Accédez à `https://app.dietsenegal.net/admin/clients/client/8`

Vous verrez l'erreur PHP exacte qui cause le 404.

## Erreurs Courantes et Solutions

### 1. "Call to undefined function _l()"
**Cause** : La fonction de traduction n'est pas chargée
**Solution** : Le hook s'exécute trop tôt. Ajoutez un check :
```php
if (!function_exists('_l')) return;
```

### 2. "Call to a member function on null"
**Cause** : Le $CI n'est pas initialisé ou un modèle retourne null
**Solution** : Ajoutez des vérifications null partout

### 3. "Cannot use object of type CI_DB_mysqli_result as array"
**Cause** : Erreur de type dans une requête DB
**Solution** : Vérifiez les appels `->row()` vs `->result()`

### 4. "Headers already sent"
**Cause** : Le hook echo quelque chose avant les headers HTTP
**Solution** : Utilisez output buffering (déjà fait dans le dernier commit)

### 5. "Trying to get property of non-object"
**Cause** : Accès à une propriété d'un objet null
**Solution** : Ajoutez `if ($object)` avant d'accéder aux propriétés

## Commandes Utiles de Débogage

### Vérifier si le module est actif :
```sql
SELECT * FROM tblmodules WHERE module_name = 'dietetic';
```

### Vérifier les derniers logs d'activité :
```sql
SELECT * FROM tblactivity_log
WHERE description LIKE '%Dietetic%'
ORDER BY date DESC
LIMIT 20;
```

### Vérifier les contraintes FK actuelles :
```sql
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME,
    UPDATE_RULE,
    DELETE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE()
AND TABLE_NAME LIKE '%dietic%';
```

### Clear le cache Perfex :
```bash
cd /path/to/perfex
rm -rf application/cache/*
# Ne supprimez PAS le fichier .htaccess dans cache/
```

## Contact Support

Si aucune solution ne fonctionne, fournissez ces informations :

1. **Output du script de diagnostic** (`diagnose_404.php`)
2. **Extrait du PHP error log** (les 20 dernières lignes au moment de l'erreur)
3. **Output de** :
   ```sql
   SHOW CREATE TABLE tbldietic_patients;
   ```
4. **Version de** :
   - PHP : `php -v`
   - MySQL : `mysql --version`
   - Perfex CRM : visible dans Setup > About

---

**Dernière mise à jour** : 2025-11-06
**Status** : En investigation active
**Priorité** : URGENTE
