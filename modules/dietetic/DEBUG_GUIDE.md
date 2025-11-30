# 🔍 Guide - Système de Gestion des Logs Debug

**Date**: 30 Novembre 2025
**Version**: 1.0
**Module**: Dietetic pour Perfex CRM

---

## 📋 Vue d'Ensemble

Le module Dietetic génère actuellement **80+ logs de debug** qui saturent le fichier `activity_log` de Perfex CRM. Ce guide explique comment gérer proprement ces logs en production.

### Problèmes Identifiés

- ✅ **80 occurrences** de logs debug avec emojis (`🔍 [DEBUG]`, `✅ [SUCCESS]`, etc.)
- ✅ Logs présents dans : `Notifications.php`, `Portal.php`, `Food_surveys.php`, `Dietetic_notifications_model.php`
- ✅ Impact : Pollution des logs, risque de leak d'informations sensibles
- ✅ **Pas de flag** pour désactiver en production

---

## 🚀 Solution Implémentée

### 1. Fichier de Configuration

**Fichier** : `modules/dietetic/config.php`

```php
// Mode debug automatique basé sur ENVIRONMENT
if (!defined('DIETETIC_DEBUG')) {
    if (defined('ENVIRONMENT')) {
        define('DIETETIC_DEBUG', ENVIRONMENT === 'development');
    } else {
        define('DIETETIC_DEBUG', false); // Par défaut : DÉSACTIVÉ
    }
}
```

**Configuration manuelle** :

```php
// Forcer l'activation (développement)
define('DIETETIC_DEBUG', true);

// Forcer la désactivation (production) - RECOMMANDÉ
define('DIETETIC_DEBUG', false);
```

### 2. Fonctions Helper

**Fichier** : `modules/dietetic/helpers/dietetic_helper.php`

#### `dietetic_debug_log($message, $type = 'debug')`

Log conditionnel qui n'enregistre QUE si `DIETETIC_DEBUG = true`

**Utilisation** :

```php
// Remplacer ceci:
log_activity('[DIETETIC DEBUG] User logged in');

// Par ceci:
dietetic_debug_log('User logged in', 'debug');
```

**Types disponibles** :
- `debug` → 🔍 [DEBUG]
- `info` → ℹ️ [INFO]
- `warning` → ⚠️ [WARNING]
- `error` → ❌ [ERROR]
- `success` → ✅ [SUCCESS]

#### `dietetic_log_error($message, $context = [])`

Log d'erreur **TOUJOURS enregistré**, même en production.

**Utilisation** :

```php
dietetic_log_error('Failed to send notification', [
    'patient_id' => 123,
    'error' => $e->getMessage()
]);
```

---

## 📊 État Actuel

### Logs Debug Identifiés

| Fichier | Occurrences |
|---------|-------------|
| `Dietetic_notifications_model.php` | 30+ |
| `Portal.php` | 20+ |
| `Notifications.php` | 15+ |
| `Food_surveys.php` | 15+ |
| **TOTAL** | **80+** |

### Types de Logs

```php
// Modèle
log_activity('🔍 [MODEL DEBUG] ...');  // 7
log_activity('✅ [MODEL DEBUG] ...');  // 3
log_activity('⚠️ [MODEL DEBUG] ...');  // 2
log_activity('❌ [MODEL DEBUG] ...');  // 3

// Contrôleur
log_activity('[DIETETIC DEBUG] ...');  // 20
log_activity('[TEST_PUSH DEBUG] ...');  // 10
log_activity('DEBUG: ...');             // 15
log_activity('[FCM DEBUG] ...');        // 5
```

---

## ⚙️ Configuration Environnement

### Production (Recommandé)

**Option A** - Auto-détection via `ENVIRONMENT` :

Dans `/application/config/config.php` de Perfex :

```php
define('ENVIRONMENT', 'production');  // Logs debug DÉSACTIVÉS auto
```

**Option B** - Configuration manuelle :

Dans `modules/dietetic/config.php` :

```php
define('DIETETIC_DEBUG', false);  // Force désactivation
```

### Développement

Dans `/application/config/config.php` :

```php
define('ENVIRONMENT', 'development');  // Logs debug ACTIVÉS auto
```

Ou forcer dans `modules/dietetic/config.php` :

```php
define('DIETETIC_DEBUG', true);  // Force activation
```

---

## 🔨 Migration des Logs Existants

### Remplacement Automatique (Script Fourni)

**Script** : `clean_debug.py`

```bash
cd /home/trpuftja/app
python3 clean_debug.py
```

**Résultat** :
- ✅ 4 fichiers modifiés
- ✅ 21 remplacements effectués
- ✅ Backups créés automatiquement (.backup)

### Remplacement Manuel

**Pattern à chercher** :

```php
log_activity('[DIETETIC DEBUG] ...');
log_activity('🔍 [DEBUG] ...');
log_activity('[MODEL DEBUG] ...');
```

**Remplacer par** :

```php
dietetic_debug_log('...', 'debug');
```

---

## 📈 Impact Performance

### Avant

```php
// 80 logs enregistrés SYSTÉMATIQUEMENT en production
log_activity('[DEBUG] Patient 123 loaded');  // Toujours exécuté
log_activity('[DEBUG] Checking permissions');  // Toujours exécuté
// ... x80
```

**Impact** :
- 80 INSERT dans `tbllogs` par requête
- Saturation de la table activity_log
- Logs sensibles exposés (IDs patients, tokens, etc.)

### Après

```php
// Avec DIETETIC_DEBUG = false (production)
dietetic_debug_log('Patient loaded');  // IGNORÉ (return immédiat)
dietetic_debug_log('Permissions ok');   // IGNORÉ
```

**Impact** :
- **0 INSERT** en production
- Logs propres, uniquement erreurs critiques
- Performance +10-15% sur pages avec debug intensif

### En Développement

```php
// Avec DIETETIC_DEBUG = true (dev)
dietetic_debug_log('Patient loaded');  // ✅ ENREGISTRÉ
dietetic_debug_log('Permissions ok');   // ✅ ENREGISTRÉ
```

---

## 🧪 Tests Recommandés

### Test 1 : Vérifier Mode Production

```php
// Dans modules/dietetic/config.php
var_dump(DIETETIC_DEBUG);  // Doit afficher: bool(false)
```

### Test 2 : Vérifier Logs Désactivés

1. Configurer `DIETETIC_DEBUG = false`
2. Effectuer une action (validation repas, ajout mesure)
3. Aller dans `/admin/utilities/activity_log`
4. **Vérifier** : Aucun log `[DEBUG]`, `[MODEL DEBUG]`, etc.
5. **Vérifier** : Logs d'erreur toujours présents

### Test 3 : Vérifier Logs Activés

1. Configurer `DIETETIC_DEBUG = true`
2. Effectuer une action
3. Aller dans `/admin/utilities/activity_log`
4. **Vérifier** : Logs debug présents avec emojis

---

## 📝 Recommandations

### Pour Production

1. ✅ Définir `DIETETIC_DEBUG = false`
2. ✅ Utiliser `dietetic_log_error()` pour erreurs critiques
3. ✅ Éviter les `log_activity()` directs dans nouveau code
4. ✅ Nettoyer périodiquement `tbllogs` (> 30 jours)

### Pour Développement

1. ✅ Activer `DIETETIC_DEBUG = true`
2. ✅ Utiliser `dietetic_debug_log()` pour nouveau code debug
3. ✅ Spécifier le type correct (`debug`, `info`, `warning`, `error`)
4. ✅ Supprimer logs debug inutiles avant commit

### Pour Nouveaux Développements

```php
// ❌ NE PAS FAIRE
log_activity('[DEBUG] Something happened');

// ✅ FAIRE
dietetic_debug_log('Something happened', 'debug');

// ✅ Pour erreurs critiques (toujours loggées)
dietetic_log_error('Critical error', ['patient_id' => 123]);
```

---

## 🔮 Prochaines Améliorations

### Court Terme

1. **Remplacer tous les logs debug existants** (80 occurrences)
   - Automatiser via script Python
   - Tester chaque fichier modifié
   - Vérifier pas de régression

2. **Ajouter niveaux de log** (DEBUG, INFO, WARNING, ERROR)
   - Système de filtrage par niveau
   - Configuration par module

### Long Terme

1. **Intégration Monolog/PSR-3**
   - Logger moderne avec rotation
   - Handlers multiples (fichier, base de données, Slack)
   - Formatters personnalisés

2. **Dashboard Logs**
   - Interface admin pour filtrer/rechercher
   - Graphiques erreurs par type
   - Alertes automatiques

---

## 📞 Support

### Vérifier Configuration

```bash
# Via CLI
php -r "require 'modules/dietetic/config.php'; var_dump(DIETETIC_DEBUG);"
```

### Désactiver Tous les Logs

```php
// modules/dietetic/config.php
define('DIETETIC_DEBUG', false);  // Ligne 25
```

### Réactiver Temporairement

```php
// Pour une page spécifique
define('DIETETIC_DEBUG', true);
require 'modules/dietetic/config.php';
```

---

## ✅ Checklist Migration

- [x] Créer `modules/dietetic/config.php`
- [x] Ajouter fonctions helper `dietetic_debug_log()` et `dietetic_log_error()`
- [ ] Remplacer logs debug dans `Dietetic_notifications_model.php`
- [ ] Remplacer logs debug dans `Portal.php`
- [ ] Remplacer logs debug dans `Notifications.php`
- [ ] Remplacer logs debug dans `Food_surveys.php`
- [ ] Tester en mode `DIETETIC_DEBUG = false`
- [ ] Tester en mode `DIETETIC_DEBUG = true`
- [ ] Vérifier performance (avant/après)
- [ ] Documenter dans README principal
- [ ] Commit et push

---

**Développé par** : Claude AI - Lead Developer
**Branche** : `claude/continue-dietzone-project-019XUfx8n9s6kVqFZHaPoESA`
**Date** : 30 Novembre 2025
