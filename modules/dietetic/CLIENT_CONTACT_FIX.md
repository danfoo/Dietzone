# Fix pour le Conflit de Création Client/Contact

## Problème Identifié

Le module diététique empêchait la création de contacts lors de la création de clients Perfex. Ce problème est causé par une contrainte de clé étrangère (foreign key) sur la table `dietic_patients` qui peut créer des conflits de transaction ou des verrous de base de données lors de la création de clients.

## Cause Technique

La contrainte de clé étrangère suivante pouvait causer des problèmes :

```sql
FOREIGN KEY (`client_id`) REFERENCES `tblclients`(`userid`) ON DELETE CASCADE
```

Bien que cette contrainte soit correctement définie, elle peut :
- Créer des verrous de base de données pendant les transactions
- Causer des échecs de transaction si MySQL est en mode strict
- Interférer avec le processus de création de clients de Perfex

## Solution Appliquée

### 1. Script de Réparation (`fix_client_conflict.php`)

Un script a été créé pour réparer les installations existantes. Ce script :
- Supprime la contrainte de clé étrangère problématique
- Vérifie et corrige les index de la base de données
- Recrée la contrainte avec des paramètres optimaux (`ON UPDATE CASCADE`)
- Valide la structure de la base de données

### 2. Installation Améliorée (`install.php`)

Le script d'installation a été amélioré pour :
- Vérifier si les contraintes existent déjà avant de les créer
- Gérer les erreurs de manière plus robuste
- Logger les problèmes sans bloquer l'installation
- Permettre au module de fonctionner même si certaines contraintes échouent

## Comment Appliquer le Fix

### Option 1 : Exécuter le Script de Réparation (Recommandé)

Si vous avez déjà installé le module et rencontrez le problème :

1. Accédez au backend de Perfex en tant qu'administrateur
2. Allez dans Setup > Modules
3. Trouvez le module "Dietetic"
4. Exécutez ce script PHP sur votre serveur :

```bash
cd /path/to/perfex
php -r "define('BASEPATH', true); require_once('modules/dietetic/fix_client_conflict.php');"
```

Ou créez un fichier temporaire dans la racine de Perfex :

```php
<?php
// run_fix.php
require_once(__DIR__ . '/application/config/config.php');
require_once(__DIR__ . '/application/config/database.php');
$CI =& get_instance();
require_once(__DIR__ . '/modules/dietetic/fix_client_conflict.php');
```

Puis exécutez : `php run_fix.php`

### Option 2 : Réinstaller le Module

1. Désinstallez le module diététique (Setup > Modules > Dietetic > Uninstall)
2. Tirez (pull) les dernières modifications du dépôt git
3. Réinstallez le module

**⚠️ Attention** : La désinstallation supprimera toutes les données diététiques !

### Option 3 : Correction Manuelle via phpMyAdmin

1. Connectez-vous à phpMyAdmin
2. Sélectionnez votre base de données Perfex
3. Exécutez cette requête SQL :

```sql
-- Remplacez 'tbl' par votre préfixe de table si différent
ALTER TABLE `tbldietic_patients` DROP FOREIGN KEY `fk_diet_patients_client`;

ALTER TABLE `tbldietic_patients`
    ADD CONSTRAINT `fk_diet_patients_client`
    FOREIGN KEY (`client_id`)
    REFERENCES `tblclients`(`userid`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
```

## Vérification

Après avoir appliqué le fix :

1. Essayez de créer un nouveau client dans Perfex (Setup > Clients > New Client)
2. Ajoutez un contact au client
3. Sauvegardez le client
4. Vérifiez que le client et le contact sont créés correctement

## Impact sur les Fonctionnalités

Ce fix :
- ✅ Résout le problème de création de clients/contacts
- ✅ Maintient l'intégrité référentielle de la base de données
- ✅ Ajoute `ON UPDATE CASCADE` pour une meilleure synchronisation
- ✅ Améliore la robustesse de l'installation
- ✅ N'affecte pas les données existantes
- ✅ N'affecte pas les autres fonctionnalités du module

## Détails Techniques

### Changements dans `install.php`

- Ajout de vérification d'existence avant création de contraintes
- Meilleur logging des erreurs
- Gestion gracieuse des échecs de contraintes
- Le module fonctionne maintenant même si certaines contraintes FK échouent

### Nouveau fichier `fix_client_conflict.php`

- Script de réparation autonome
- Peut être exécuté à tout moment
- Vérifie et corrige la structure de la base de données
- Fournit des retours détaillés sur les opérations

## Support

Si vous rencontrez toujours des problèmes après avoir appliqué ce fix :

1. Vérifiez les logs de Perfex (Admin > Utilities > Activity Log)
2. Vérifiez les logs d'erreur PHP de votre serveur
3. Vérifiez que MySQL/MariaDB est à jour
4. Assurez-vous que l'utilisateur de base de données a les permissions ALTER TABLE

## Logs à Vérifier

Après l'application du fix, vérifiez les logs suivants dans Perfex :
- "Dietetic Module: Client/contact conflict fix applied"
- "Dietetic Module: Added foreign key fk_diet_patients_client"
- Tout message contenant "FK constraint"

---

**Date de création** : 2025-11-06
**Version du module** : 1.0+
**Compatible avec** : Perfex CRM 2.x, 3.x
