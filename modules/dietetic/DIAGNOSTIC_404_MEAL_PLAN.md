# Diagnostic 404 Error - View Meal Plan

## Problème Signalé
Erreur 404 lors de l'accès à : https://app.dietsenegal.net/dietetic/portal/view_meal_plan/2

## Actions Effectuées

### 1. Logs de Débogage Ajoutés
Des logs détaillés ont été ajoutés à la méthode `view_meal_plan()` pour identifier exactement où l'erreur se produit.

Les logs incluent :
- ✅ Appel de la méthode avec l'ID du plan de repas
- ✅ Statut d'authentification de l'utilisateur
- ✅ Recherche du patient
- ✅ Recherche du plan de repas
- ✅ Recherche du programme
- ✅ Vérification des droits d'accès

### 2. Méthode Alternative Créée
Une méthode alias `viewmealplan()` (sans underscore) a été créée pour tester si le problème vient du routing avec underscore.

## Comment Tester

### Étape 1 : Déployer le Code
```bash
# Sur le serveur app.dietsenegal.net
cd /path/to/perfex/modules/dietetic
git pull origin claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ
```

### Étape 2 : Tester les URLs

Essayez ces 3 URLs dans l'ordre :

1. **URL originale avec underscore :**
   ```
   https://app.dietsenegal.net/dietetic/portal/view_meal_plan/2
   ```

2. **URL alternative sans underscore :**
   ```
   https://app.dietsenegal.net/dietetic/portal/viewmealplan/2
   ```

3. **URL avec ID différent (pour tester si c'est spécifique à l'ID 2) :**
   ```
   https://app.dietsenegal.net/dietetic/portal/view_meal_plan/1
   ```

### Étape 3 : Consulter les Logs

Dans Perfex CRM, allez dans :
**Setup → Activity Log** (ou **Configuration → Journal d'Activité**)

Recherchez les entrées contenant : `[DIETETIC DEBUG]`

Les logs vous diront exactement où le problème se situe.

## Scénarios Possibles

### Scénario A : Aucun Log `[DIETETIC DEBUG]`
**Signification :** La méthode n'est JAMAIS appelée - problème de routing

**Solution :**
- Vérifier que le module est bien activé
- Vérifier que les fichiers sont bien déployés
- Problème possible avec CodeIgniter routing dans Perfex

### Scénario B : Log "Meal plan not found: ID = 2"
**Signification :** Le plan de repas ID 2 n'existe pas dans la base de données

**Solution :**
1. Vérifier en base de données :
   ```sql
   SELECT * FROM tbldietic_meal_plans WHERE id = 2;
   ```
2. Si vide, utiliser un ID existant ou créer un plan de repas

### Scénario C : Log "Program not found"
**Signification :** Le plan de repas existe mais son programme associé n'existe pas

**Solution :**
1. Vérifier l'intégrité des données :
   ```sql
   SELECT mp.*, p.id as program_exists
   FROM tbldietic_meal_plans mp
   LEFT JOIN tbldietic_programs p ON p.id = mp.program_id
   WHERE mp.id = 2;
   ```
2. Corriger les données ou restaurer le programme manquant

### Scénario D : Log "Access denied: Program patient_id (...) != Patient ID (...)"
**Signification :** Le plan de repas existe mais n'appartient pas au patient connecté

**Solution :**
- C'est normal ! Le patient ne peut voir que SES propres plans
- Se connecter avec le bon compte patient
- Ou utiliser l'ID d'un plan appartenant au patient connecté

### Scénario E : Log "Access granted, loading view" MAIS toujours 404
**Signification :** La vue `portal_meal_plan_view.php` est introuvable ou a une erreur

**Solution :**
1. Vérifier que le fichier existe :
   ```bash
   ls -la modules/dietetic/views/portal_meal_plan_view.php
   ```
2. Vérifier les erreurs PHP dans le fichier vue

## Si l'URL Alternative Fonctionne

Si `/dietetic/portal/viewmealplan/2` fonctionne mais pas `/dietetic/portal/view_meal_plan/2`, alors le problème est lié au routing CodeIgniter avec les underscores.

**Solution :** Modifier tous les liens pour utiliser `viewmealplan` au lieu de `view_meal_plan`.

## Contact

Après avoir testé, rapportez les résultats avec :
1. Quelle(s) URL(s) testée(s)
2. Ce qui s'affiche (404 ? autre erreur ? page correcte ?)
3. Les logs `[DIETETIC DEBUG]` trouvés dans Activity Log

---

**Date :** 2025-11-06
**Commit :** 2c1d6a4
**Branche :** claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ
