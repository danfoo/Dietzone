# Test et Résolution Erreur 404 - view_meal_plan

## 🔴 Problème
Erreur 404 persistante lors de l'accès à `/dietetic/portal/view_meal_plan/2`

## 🧪 Tests à Effectuer (DANS L'ORDRE)

### Test 1 : Vérifier que le module est déployé

```bash
cd /path/to/perfex/modules/dietetic
git pull origin claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ
```

### Test 2 : Page de Diagnostic Web

Accédez à cette URL (remplacez par votre domaine) :
```
https://app.dietsenegal.net/modules/dietetic/diagnostic_web.php
```

**Ce que vous devriez voir :**
- ✓ Tous les fichiers présents
- ✓ Toutes les méthodes trouvées
- ✓ Toutes les tables existent

**Si vous voyez des ✗ :**
- Notez QUELS fichiers/éléments sont manquants
- Cela indique où se situe le problème

---

### Test 3 : Test de Routing Basic

**Connectez-vous en tant que PATIENT** (pas admin !), puis accédez à :
```
https://app.dietsenegal.net/dietetic/portal/test
```

**Résultat Attendu :**
```
✓ Routing Test Successful!
If you see this message, the Portal controller is accessible.
```

**Si vous obtenez 404 ici :**
→ Le problème est que Perfex ne route PAS du tout vers le contrôleur Portal
→ Solution : Vérifier que le module est activé dans Perfex

**Si ça fonctionne :**
→ Le contrôleur Portal est accessible ✓
→ Passez au test suivant

---

### Test 4 : Test de Routing avec Paramètre

Depuis la page de test, cliquez sur "Test with parameter" OU accédez directement à :
```
https://app.dietsenegal.net/dietetic/portal/test_with_param/123
```

**Résultat Attendu :**
```
✓ Routing with Parameter Test Successful!
Received parameter: 123
```

**Si vous obtenez 404 ici :**
→ Le problème est avec le passage de paramètres dans les URLs
→ Possible problème de configuration CodeIgniter

**Si ça fonctionne :**
→ Le routing avec paramètres fonctionne ✓
→ Passez au test suivant

---

### Test 5 : Test de l'URL Alternative (sans underscore)

Depuis la page de test, cliquez sur "viewmealplan/2 (no underscore)" OU accédez directement à :
```
https://app.dietsenegal.net/dietetic/portal/viewmealplan/2
```

**Résultat Attendu :**
- Soit la page du plan de repas s'affiche correctement ✓
- Soit un message d'erreur explicite (pas 404)

**Si vous obtenez 404 ici MAIS que le test avec paramètres fonctionnait :**
→ Le problème est lié au mot-clé "mealplan" ou "viewmealplan"
→ Très bizarre, possible conflit de noms

**Si vous obtenez un message d'erreur (pas 404) :**
→ EXCELLENT ! Le routing fonctionne !
→ Le message d'erreur vous dira quel est le vrai problème :
   - "Meal plan not found" = Le plan ID 2 n'existe pas
   - "Program not found" = Le programme associé n'existe pas
   - "Access denied" = Le plan appartient à un autre patient
   - "No patient found" = Votre compte client n'est pas lié à un patient

**Si la page s'affiche correctement :**
→ PARFAIT ! L'URL sans underscore fonctionne !
→ Solution : Utiliser cette URL désormais

---

### Test 6 : Test de l'URL Originale (avec underscore)

Depuis la page de test, cliquez sur "view_meal_plan/2 (original)" OU accédez directement à :
```
https://app.dietsenegal.net/dietetic/portal/view_meal_plan/2
```

**Si ça fonctionne :**
→ Problème résolu ! 🎉

**Si 404 MAIS que viewmealplan fonctionne :**
→ CodeIgniter a un problème avec les underscores dans les URLs
→ Solution : Modifier tous les liens pour utiliser `viewmealplan` au lieu de `view_meal_plan`

---

### Test 7 : Consulter les Logs de Débogage

Dans Perfex CRM :
1. Allez dans **Setup → Activity Log**
2. Recherchez : `[DIETETIC DEBUG]`
3. Notez les messages

**Logs possibles :**

| Message de Log | Signification | Action |
|----------------|---------------|--------|
| Aucun log `[DIETETIC DEBUG]` | Méthode jamais appelée | Problème de routing - voir tests 3-4 |
| `view_meal_plan called with ID: 2` | ✓ Méthode appelée | Continuer à lire les logs |
| `Invalid meal plan ID` | ID mal formaté | Vérifier l'URL |
| `Meal plan not found: ID = 2` | Plan ID 2 n'existe pas | Voir Test 8 |
| `Program not found` | Programme manquant | Données corrompues |
| `Access denied: Program patient_id (...) != Patient ID (...)` | Plan pas pour ce patient | Utiliser un autre ID de plan |
| `Access granted, loading view` | ✓ Tout OK | Si 404 après, problème avec la vue |

---

### Test 8 : Vérifier si le Plan de Repas Existe

Exécutez cette requête SQL dans phpMyAdmin ou autre :

```sql
SELECT
    mp.id as meal_plan_id,
    mp.plan_name,
    mp.program_id,
    p.id as program_id_check,
    p.patient_id,
    pat.id as patient_id_check,
    c.userid as client_id
FROM tbldietic_meal_plans mp
LEFT JOIN tbldietic_programs p ON p.id = mp.program_id
LEFT JOIN tbldietic_patients pat ON pat.id = p.patient_id
LEFT JOIN tblclients c ON c.userid = pat.client_id
WHERE mp.id = 2;
```

**Résultats à vérifier :**
- ✓ `meal_plan_id = 2` existe ?
- ✓ `program_id_check` n'est pas NULL ?
- ✓ `patient_id_check` n'est pas NULL ?
- ✓ `client_id` correspond à votre compte ?

**Si tout est NULL après program_id :**
→ Le plan de repas existe mais son programme n'existe pas
→ Données corrompues - besoin de restaurer le programme

**Si patient_id ne correspond pas à votre patient :**
→ Normal ! Ce plan appartient à un autre patient
→ Utilisez un plan qui vous appartient

**Si aucune ligne retournée :**
→ Le plan ID 2 n'existe tout simplement pas
→ Utilisez un ID de plan qui existe

---

## 🎯 Diagnostic Rapide

Résumez vos résultats :

1. **Diagnostic web page** : ✓ ou ✗
2. **Test routing basic** (`/test`) : ✓ ou ✗
3. **Test routing avec param** (`/test_with_param/123`) : ✓ ou ✗
4. **URL sans underscore** (`/viewmealplan/2`) : ✓ ou ✗ ou message d'erreur
5. **URL avec underscore** (`/view_meal_plan/2`) : ✓ ou ✗
6. **Logs [DIETETIC DEBUG]** : présents ou absents
7. **Requête SQL plan ID 2** : existe ou pas

---

## 🔧 Solutions selon le Diagnostic

### Cas A : Tests 1-4 ✓, Test 5 fonctionne, Test 6 échoue (404)
**Problème :** CodeIgniter bloque les underscores dans les noms de méthodes

**Solution Permanente :**
1. Modifier tous les liens qui utilisent `view_meal_plan` pour utiliser `viewmealplan`
2. Fichier à modifier : `modules/dietetic/views/portal_meal_plans.php`

### Cas B : Tests 1-3 ✓, Tests 4-6 échouent (404)
**Problème :** Routing avec paramètres ne fonctionne pas

**Solution :**
1. Vérifier config CodeIgniter : `application/config/config.php`
2. Chercher `$config['uri_protocol']` et essayer différentes valeurs

### Cas C : Test 5-6 affichent "Meal plan not found"
**Problème :** Le plan ID 2 n'existe pas en base

**Solution :**
1. Trouver un ID existant avec SQL :
   ```sql
   SELECT id, plan_name FROM tbldietic_meal_plans ORDER BY id DESC LIMIT 10;
   ```
2. Utiliser un ID qui existe

### Cas D : Test 5-6 affichent "Access denied"
**Problème :** Le plan appartient à un autre patient

**Solution :**
1. Trouver VOS plans avec SQL :
   ```sql
   SELECT mp.id, mp.plan_name
   FROM tbldietic_meal_plans mp
   JOIN tbldietic_programs p ON p.id = mp.program_id
   JOIN tbldietic_patients pat ON pat.id = p.patient_id
   JOIN tblclients c ON c.userid = pat.client_id
   WHERE c.userid = VOTRE_CLIENT_ID;
   ```
2. Utiliser un de ces IDs

### Cas E : Aucun log [DIETETIC DEBUG]
**Problème :** La méthode n'est jamais appelée

**Solution :**
1. Vérifier que le module est activé : Setup → Modules
2. Re-déployer le code
3. Vider le cache Perfex si existant

---

## 📞 Rapporter les Résultats

Après avoir effectué ces tests, rapportez-moi :

1. ✅ ou ❌ pour chaque test (1-8)
2. Les messages d'erreur exacts si pas 404
3. Les logs `[DIETETIC DEBUG]` trouvés
4. Le résultat de la requête SQL

Avec ces informations, je pourrai identifier la cause exacte et fournir le fix approprié !

---

**Date :** 2025-11-06
**Branche :** `claude/build-dietetic-crm-module-011CUg57kDmPcHyfwwy6HAYZ`
