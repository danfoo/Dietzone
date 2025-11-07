# Fix de Routing pour le Portail Client - Module Diététique

## Problème Résolu

**Symptôme** : Erreur 404 lors de l'accès aux méthodes du portail client avec paramètres
**Exemple d'URL** : `https://app.dietsenegal.net/dietetic/portal/view_meal_plan/123`
**Cause** : CodeIgniter/Perfex CRM ne route pas automatiquement les segments d'URL vers les méthodes du contrôleur Portal avec paramètres

## Solution Implémentée

### Méthode `_remap()` dans le Contrôleur Portal

La méthode spéciale `_remap()` de CodeIgniter a été ajoutée au contrôleur `Portal.php` pour gérer manuellement le routing de toutes les requêtes.

**Fichier** : `modules/dietetic/controllers/Portal.php`
**Lignes** : 33-71

```php
public function _remap($method, $params = [])
{
    // Debug logging (only in development environment)
    if (ENVIRONMENT === 'development') {
        log_activity('[DIETETIC DEBUG] _remap called - Method: ' . $method . ', Params: ' . json_encode($params));
    }

    // List of valid methods in this controller
    $valid_methods = [
        'index',
        'measurements',
        'add_measurement',
        'meal_plans',
        'view_meal_plan',
        'viewmealplan',
        'mealplan',
        'meal_plan_view',
        'consultations',
        'my_dietitians',
        'rate_dietitian',
        'test',
        'test_with_param'
    ];

    // If method doesn't exist, treat it as index with the method name as a parameter
    if (!in_array($method, $valid_methods)) {
        if (ENVIRONMENT === 'development') {
            log_activity('[DIETETIC DEBUG] Method not found: ' . $method . ', redirecting to index');
        }
        // Method not found, call index instead
        return call_user_func_array([$this, 'index'], array_merge([$method], $params));
    }

    // Call the requested method with all parameters
    if (ENVIRONMENT === 'development') {
        log_activity('[DIETETIC DEBUG] Calling method: ' . $method . ' with params: ' . json_encode($params));
    }
    return call_user_func_array([$this, $method], $params);
}
```

### Comment ça fonctionne

1. **Interception** : Toutes les requêtes vers le contrôleur Portal passent par `_remap()`
2. **Validation** : Vérifie si la méthode demandée existe dans la liste des méthodes valides
3. **Routing** : Appelle la méthode appropriée avec tous ses paramètres
4. **Fallback** : Si la méthode n'existe pas, redirige vers `index()`

### URLs Supportées

Toutes ces URLs fonctionnent maintenant correctement :

```
/dietetic/portal/view_meal_plan/123
/dietetic/portal/viewmealplan/123
/dietetic/portal/mealplan/123
/dietetic/portal/meal_plan_view?id=123
/dietetic/portal/measurements
/dietetic/portal/consultations
/dietetic/portal/my_dietitians
```

## Modifications Associées

### 1. Mise à jour des Liens dans les Vues

**Fichier** : `modules/dietetic/views/portal_meal_plans.php`
**Ligne** : 553

**Avant** (contournement avec paramètre GET) :
```php
<a href="<?php echo site_url('dietetic/portal/meal_plan_view?id=' . $plan->id); ?>">
```

**Après** (méthode standard avec segments d'URL) :
```php
<a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>">
```

### 2. Nettoyage des Logs de Debug

Les logs de debug ont été rendus conditionnels pour ne s'exécuter qu'en environnement de développement :

**Fichier** : `modules/dietetic/controllers/Portal.php`

- **Méthode `_remap()`** : Logs conditionnels avec `if (ENVIRONMENT === 'development')`
- **Méthode `view_meal_plan()`** : Logs de debug retirés, seul le log de tentative d'accès non autorisé conservé
- **Méthode `meal_plan_view()`** : Log de debug retiré

### 3. Suppression des Fichiers de Diagnostic

Les fichiers temporaires créés pour diagnostiquer le problème ont été supprimés :

- `DIAGNOSTIC_404_MEAL_PLAN.md`
- `TEST_404_FIX.md`
- `diagnostic_web.php`

## Pourquoi cette Solution ?

### Avantages de `_remap()`

1. **Contrôle Total** : Gestion manuelle du routing pour tous les cas edge
2. **Compatibilité** : Fonctionne avec toutes les versions de CodeIgniter/Perfex CRM
3. **Flexibilité** : Permet d'ajouter facilement de nouvelles méthodes
4. **Fallback** : Gestion gracieuse des méthodes inexistantes
5. **Debug** : Logs détaillés en mode développement

### Alternatives Considérées

1. **Routes personnalisées** : Perfex CRM ne permet pas facilement de modifier les routes des modules
2. **URL sans underscores** : Nécessiterait de renommer toutes les méthodes existantes
3. **Paramètres GET** : Moins propre et non-standard pour CodeIgniter

## Test de la Solution

### URLs de Test Disponibles

Le contrôleur Portal inclut des méthodes de test pour vérifier le routing :

```
/dietetic/portal/test
/dietetic/portal/test_with_param/123
```

Ces méthodes affichent des informations de diagnostic pour confirmer que le routing fonctionne.

### Test Manuel

1. Se connecter en tant que patient (client)
2. Accéder à `/dietetic/portal/meal_plans`
3. Cliquer sur "Voir les Repas" sur un plan alimentaire
4. Vérifier que l'URL contient `/view_meal_plan/{id}` et que la page se charge correctement

## Maintenance Future

### Ajout de Nouvelles Méthodes

Pour ajouter une nouvelle méthode au contrôleur Portal :

1. Créer la méthode dans `Portal.php`
2. Ajouter le nom de la méthode à l'array `$valid_methods` dans `_remap()`
3. Créer les vues associées si nécessaire
4. Mettre à jour les liens dans les vues

**Exemple** :

```php
// Dans _remap()
$valid_methods = [
    // ... méthodes existantes
    'nouvelle_methode',  // Ajouter ici
];

// Nouvelle méthode
public function nouvelle_methode($param = null)
{
    // Votre code ici
}
```

### Considérations de Sécurité

- La méthode `_remap()` vérifie que seules les méthodes dans `$valid_methods` peuvent être appelées
- Toutes les méthodes du contrôleur Portal vérifient l'authentification du client
- Les méthodes de test (`test`, `test_with_param`) peuvent être retirées en production

## Commits Associés

- **Commit principal** : Ajout de la méthode `_remap()` pour résoudre le routing avec paramètres
- **Branche** : `claude/continue-previous-session-011CUtvwn5j14yC9xu6rAP47`
- **Date** : 2025-11-07

## Autres Contrôleurs

**Question** : Les autres contrôleurs du module ont-ils besoin de `_remap()` ?

**Réponse** : Non. Seul le contrôleur Portal nécessite `_remap()` car :

- C'est le seul contrôleur accessible via le portail client avec un URL pattern spécifique
- Les autres contrôleurs (Dietetic, Patients, Consultations, etc.) sont pour l'administration
- Perfex CRM gère le routing des contrôleurs admin différemment

## Support

Si vous rencontrez des problèmes de routing après cette solution :

1. **Vérifier les logs** : En mode développement, les logs `[DIETETIC DEBUG]` apparaissent dans Activity Log
2. **Vérifier la méthode** : Assurez-vous que la méthode est dans `$valid_methods`
3. **Vérifier l'authentification** : Le client doit être connecté pour accéder au portail
4. **Vérifier les paramètres** : Les méthodes doivent accepter les paramètres corrects

---

**Date de création** : 2025-11-07
**Dernière mise à jour** : 2025-11-07
**Version du module** : 1.0+
**Compatible avec** : Perfex CRM 2.x, 3.x
