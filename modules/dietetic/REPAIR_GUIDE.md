# Guide de Réparation des Meal Plans Orphelins

## 🔴 Problème Identifié

Lors des tests, nous avons découvert que certains **meal plans** référencent des **programmes inexistants** dans la base de données. Cela provoque des erreurs 404 lorsqu'on tente d'accéder à ces meal plans.

### Diagnostic des Logs

```
[DIETETIC DEBUG] Meal plan found: ID = 2, program_id = 2
[DIETETIC DEBUG] Program not found: ID = 2  ← PROBLÈME
```

Le meal plan #2 existe mais référence le programme #2 qui n'existe pas.

---

## 🔧 Solution : Script de Réparation Automatique

Un script de réparation a été créé pour détecter et corriger automatiquement tous les meal plans orphelins.

### Localisation

```
modules/dietetic/repair_orphan_meal_plans.php
```

---

## 📋 Instructions d'Utilisation

### Étape 1 : Diagnostic

Accédez à l'URL suivante dans votre navigateur :

```
https://app.dietsenegal.net/modules/dietetic/repair_orphan_meal_plans.php
```

**Ce que vous verrez :**
- 📊 Statistiques globales (total meal plans, orphelins, valides)
- 📋 Liste détaillée de tous les meal plans orphelins
- ℹ️ Informations sur le patient cible pour la réparation

**Mode Diagnostic :** Aucune modification n'est effectuée à ce stade.

---

### Étape 2 : Réparation

Une fois le diagnostic effectué, cliquez sur le bouton **"🔧 Lancer la Réparation"**.

**Ce que le script va faire pour chaque meal plan orphelin :**

1. ✅ Créer un programme de remplacement avec le même ID que celui manquant
2. ✅ Associer ce programme au patient ID 1
3. ✅ Utiliser le diététicien assigné au patient
4. ✅ Définir des valeurs par défaut appropriées
5. ✅ Le meal plan référencera automatiquement le nouveau programme

**Sécurité :** La réparation utilise une **transaction SQL** - si une erreur survient, **toutes les modifications sont annulées**.

---

### Étape 3 : Vérification

Après la réparation, testez les URLs suivantes :

```
✅ /dietetic/portal/view_meal_plan/2
✅ /dietetic/portal/viewmealplan/2
✅ /dietetic/portal/mealplan/2
```

**Résultat attendu :** Les pages devraient maintenant s'afficher correctement au lieu de retourner une erreur 404.

---

## 🔍 Vérification SQL Manuelle (Optionnel)

Pour vérifier manuellement les meal plans orphelins, exécutez cette requête :

```sql
SELECT
    mp.id as meal_plan_id,
    mp.program_id as missing_program_id,
    mp.plan_name,
    p.id as program_exists
FROM tbldietic_meal_plans mp
LEFT JOIN tbldietic_programs p ON p.id = mp.program_id
WHERE p.id IS NULL;
```

**Si `program_exists` est NULL**, le meal plan est orphelin.

---

## 🚨 Après la Réparation

### Désactiver le Script (Important pour la Sécurité)

Une fois la réparation terminée, **désactivez le script** pour éviter tout accès non autorisé :

1. Ouvrez le fichier : `modules/dietetic/repair_orphan_meal_plans.php`
2. Changez la ligne 12 :
   ```php
   define('REPAIR_ENABLED', true);
   ```
   en
   ```php
   define('REPAIR_ENABLED', false);
   ```

3. Ou supprimez simplement le fichier :
   ```bash
   rm modules/dietetic/repair_orphan_meal_plans.php
   ```

---

## 📊 Exemple de Résultat

### Avant Réparation
```
✅ Total Meal Plans: 10
❌ Meal Plans Orphelins: 3
✅ Meal Plans Valides: 7
```

### Après Réparation
```
✅ Total Meal Plans: 10
❌ Meal Plans Orphelins: 0
✅ Meal Plans Valides: 10

✅ Réparation terminée avec succès !
3 meal plan(s) réparé(s)
0 erreur(s)
```

---

## 🛠️ Détails Techniques

### Structure du Programme Créé

Pour chaque meal plan orphelin, un programme est créé avec :

| Champ | Valeur |
|-------|--------|
| `id` | L'ID manquant (ex: 2) |
| `patient_id` | 1 (patient cible) |
| `dietitian_id` | ID du diététicien du patient |
| `program_name` | "Programme pour {nom du plan}" |
| `description` | "Programme créé automatiquement pour réparer le meal plan #{ID}" |
| `start_date` | Date de début du meal plan ou date actuelle |
| `end_date` | Date de fin du meal plan ou NULL |
| `status` | 'active' |
| `created_at` | Date de création du meal plan orphelin |

### Logs Générés

Après la réparation, vous verrez dans les **Activity Logs** :

```
✅ Meal Plan #2: Programme #2 créé avec succès
   → Nom: Programme pour Plan Semaine 1
   → Patient: Client Name
   → Diététicien: ID #5
```

---

## 🔗 URLs de Test

Après la réparation, testez ces URLs :

| URL | Description | Résultat Attendu |
|-----|-------------|------------------|
| `/dietetic/portal/test` | Test basique | ✅ Page de test |
| `/dietetic/portal/test_with_param/123` | Test avec paramètre | ✅ "Received parameter: 123" |
| `/dietetic/portal/view_meal_plan/2` | Meal plan avec underscore | ✅ Affichage du plan |
| `/dietetic/portal/viewmealplan/2` | Meal plan sans underscore | ✅ Affichage du plan |
| `/dietetic/portal/mealplan/2` | Meal plan format court | ✅ Affichage du plan |

---

## 📞 En Cas de Problème

Si le script échoue ou si vous rencontrez des erreurs :

1. **Consultez les logs d'activité** : Setup → Activity Log → Rechercher `[DIETETIC DEBUG]`
2. **Vérifiez les permissions** : Le script doit pouvoir se connecter à la base de données
3. **Contactez le support** avec :
   - Capture d'écran de l'erreur
   - Logs d'activité complets
   - Résultat de la requête SQL de diagnostic

---

**Date :** 2025-11-07
**Branche :** `claude/continue-previous-session-011CUsze68Nvj7kAXJpRUMSF`
**Commit :** Fix des meal plans orphelins
