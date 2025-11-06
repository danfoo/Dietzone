# Fix pour l'Erreur 404 sur /admin/clients/client/

## Problème Signalé

**Erreur** : HTTP 404 Not Found sur `https://app.dietsenegal.net/admin/clients/client/`
**Condition** : L'erreur se produit **uniquement** quand le module diététique est activé
**Impact** : Empêche la création de nouveaux clients dans Perfex CRM

## Analyse du Problème

Cette erreur 404 est causée par un conflit entre le module diététique et le système de routing/création de clients de Perfex. Les causes identifiées :

### 1. **Contraintes de Clé Étrangère (Foreign Keys)**
Les contraintes FK sur la table `dietic_patients` peuvent créer des verrous de base de données ou des conflits de transaction pendant la création de clients, causant des erreurs silencieuses transformées en 404.

### 2. **Hooks sans Protection**
Les hooks `customer_profile_tabs` et `customers_navigation_start` s'exécutaient même sur les pages de création, tentant d'accéder à des clients qui n'existent pas encore.

### 3. **Chargement de Modèles**
Le chargement du modèle `dietetic_patients_model` dans certains hooks pouvait échouer et causer des erreurs PHP fatales transformées en 404 par Perfex.

## Solutions Appliquées

### ✅ Solution 1 : Amélioration des Hooks (dietetic.php)

**Hook `dietetic_add_customer_profile_tab`** :
```php
function dietetic_add_customer_profile_tab($client_id)
{
    // Only show tab if viewing an existing client (not on create page)
    if (has_permission('dietetic', '', 'view') && !empty($client_id) && is_numeric($client_id)) {
        try {
            echo '<li role="presentation">
                    <a href="' . admin_url('dietetic/patients/client_view/' . $client_id) . '" data-group="dietetic">
                        <i class="fa fa-heartbeat"></i> ' . _l('dietetic_follow_up') . '
                    </a>
                  </li>';
        } catch (Exception $e) {
            // Silently fail if error - don't break client pages
            log_activity('Dietetic tab error: ' . $e->getMessage());
        }
    }
}
```

**Améliorations** :
- ✅ Vérification que `$client_id` n'est pas vide et est numérique
- ✅ Try-catch pour éviter les erreurs fatales
- ✅ Logging des erreurs pour le débogage

**Hook `dietetic_add_portal_menu`** :
```php
function dietetic_add_portal_menu()
{
    if (!is_client_logged_in()) {
        return;
    }

    $CI = &get_instance();

    try {
        // Check if dietetic_patients_model exists before loading
        if (!file_exists(APPPATH . 'models/dietetic/Dietetic_patients_model.php') &&
            !file_exists(module_dir_path(DIETETIC_MODULE_NAME, 'models/Dietetic_patients_model.php'))) {
            return;
        }

        $CI->load->model('dietetic/dietetic_patients_model');
        $patient = $CI->dietetic_patients_model->get_by_client(get_client_user_id());

        if ($patient) {
            echo '<li class="customers-nav-item-dietetic">
                    <a href="' . site_url('dietetic/portal') . '">
                        <i class="fa fa-heartbeat"></i> My Program
                    </a>
                  </li>';
        }
    } catch (Exception $e) {
        // Silently fail if error - don't break portal
        log_activity('Dietetic portal menu error: ' . $e->getMessage());
    }
}
```

**Améliorations** :
- ✅ Vérification de l'existence du fichier modèle avant chargement
- ✅ Try-catch pour éviter les erreurs fatales
- ✅ Logging des erreurs

### ✅ Solution 2 : Script de Réparation Avancé (fix_client_404.php)

Un script plus robuste qui :
1. ✅ Désactive temporairement les vérifications FK
2. ✅ Supprime les contraintes problématiques
3. ✅ Vérifie l'intégrité des données (records orphelins)
4. ✅ Recrée les contraintes avec des paramètres optimaux
5. ✅ Réactive les vérifications FK
6. ✅ Fournit un rapport détaillé

**Différences avec fix_client_conflict.php** :
- Plus sûr : désactive FK temporairement
- Plus complet : vérifie l'intégrité des données
- Plus détaillé : rapport complet d'exécution
- Mieux adapté : spécifiquement pour les erreurs 404

## Comment Appliquer le Fix

### Option A - Script Automatique (Recommandé)

```bash
cd /path/to/perfex
php -r "define('BASEPATH', true); \$CI =& get_instance(); require_once('modules/dietetic/fix_client_404.php');"
```

Ou créez un fichier temporaire `run_404_fix.php` à la racine de Perfex :

```php
<?php
define('BASEPATH', 1);
require_once('index.php');
require_once(__DIR__ . '/modules/dietetic/fix_client_404.php');
```

Puis exécutez : `php run_404_fix.php` et supprimez le fichier après.

### Option B - Via phpMyAdmin

```sql
-- Remplacez 'tbl' par votre préfixe si différent
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `tbldietic_patients` DROP FOREIGN KEY `fk_diet_patients_client`;
ALTER TABLE `tbldietic_patients` DROP FOREIGN KEY `fk_diet_patients_staff`;

ALTER TABLE `tbldietic_patients`
    ADD CONSTRAINT `fk_diet_patients_client`
    FOREIGN KEY (`client_id`)
    REFERENCES `tblclients`(`userid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

ALTER TABLE `tbldietic_patients`
    ADD CONSTRAINT `fk_diet_patients_staff`
    FOREIGN KEY (`dietitian_id`)
    REFERENCES `tblstaff`(`staffid`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
```

### Option C - Réinstallation du Module

⚠️ **ATTENTION** : Supprime toutes les données !

1. Désactivez le module (Setup > Modules > Dietetic > Deactivate)
2. Désinstallez le module (Uninstall)
3. Pullez les dernières modifications du dépôt
4. Réinstallez le module

## Vérification du Fix

### 1. Test de Création de Client

1. Allez dans **Setup > Clients > New Client**
2. L'URL doit être `/admin/clients/client/`
3. La page doit se charger correctement (pas de 404)
4. Remplissez le formulaire et créez un client
5. Le client doit être créé avec succès

### 2. Test de Création de Contact

1. Éditez un client existant
2. Allez dans l'onglet **Contacts**
3. Cliquez sur **Add New Contact**
4. Créez le contact
5. Le contact doit être créé sans erreur

### 3. Vérification des Logs

Allez dans **Admin > Utilities > Activity Log** et recherchez :
- "Dietetic Module: Advanced client/contact conflict fix applied successfully"
- Aucune erreur "Dietetic tab error" ou "Dietetic portal menu error"

## Diagnostic Supplémentaire

Si le problème persiste après l'application du fix :

### 1. Vérifier les Logs PHP

```bash
# Emplacement typique des logs
tail -f /var/log/apache2/error.log
# ou
tail -f /var/log/nginx/error.log
```

Recherchez des erreurs PHP fatales au moment de la tentative d'accès à `/admin/clients/client/`.

### 2. Vérifier les Permissions

```sql
SELECT * FROM tblpermissions WHERE name LIKE '%client%';
```

Assurez-vous que les permissions sur les clients ne sont pas affectées.

### 3. Test avec le Module Désactivé

1. Désactivez temporairement le module diététique
2. Essayez d'accéder à `/admin/clients/client/`
3. Si ça marche, le problème vient bien du module
4. Réactivez le module et vérifiez les logs

### 4. Vérifier le Routing CodeIgniter

Vérifiez qu'il n'y a pas de fichier `routes.php` dans le module :

```bash
find modules/dietetic -name "routes.php"
```

Résultat attendu : aucun fichier trouvé.

### 5. Vérifier la Structure de la Base de Données

```sql
SHOW CREATE TABLE tbldietic_patients;
```

Vérifiez que les contraintes FK sont bien présentes et correctement configurées.

## Fichiers Modifiés

### modules/dietetic/dietetic.php
- **Ligne 159-174** : Amélioration de `dietetic_add_customer_profile_tab()`
- **Ligne 211-240** : Amélioration de `dietetic_add_portal_menu()`

### modules/dietetic/fix_client_404.php (nouveau)
- Script de réparation avancé pour les erreurs 404
- 6 étapes de vérification et correction
- Rapport détaillé d'exécution

### modules/dietetic/CLIENT_404_FIX.md (nouveau)
- Documentation complète du problème et des solutions
- Guide d'application pas à pas
- Procédures de diagnostic

## Prévention

Pour éviter ce problème à l'avenir :

1. ✅ Toujours vérifier que `$client_id` est valide dans les hooks
2. ✅ Utiliser try-catch dans les hooks pour éviter les erreurs fatales
3. ✅ Logger les erreurs au lieu de les ignorer silencieusement
4. ✅ Tester la création de clients après chaque modification du module
5. ✅ Utiliser `ON UPDATE CASCADE` sur les FK pour éviter les blocages

## Support

Si vous avez toujours l'erreur 404 après avoir appliqué toutes les solutions :

1. **Logs à fournir** :
   - PHP error log complet
   - Perfex Activity Log (dernières 50 entrées)
   - Sortie du script `fix_client_404.php`

2. **Informations système** :
   - Version de Perfex CRM
   - Version de PHP
   - Version de MySQL/MariaDB
   - Configuration du serveur (Apache/Nginx)

3. **Tests effectués** :
   - Résultat de chaque option de fix
   - Résultat des tests de vérification
   - Comportement avec le module désactivé

---

**Date de création** : 2025-11-06
**Dernière mise à jour** : 2025-11-06
**Version du module** : 1.0+
**Compatible avec** : Perfex CRM 2.x, 3.x
