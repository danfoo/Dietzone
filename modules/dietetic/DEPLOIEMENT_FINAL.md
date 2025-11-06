# 🚀 DÉPLOIEMENT FINAL - Correction Erreur 404

## ✅ Tous les Changements Sont Sur GitHub

**Branche :** `claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD`

**Derniers commits :**
- `5675b88` - Fichier de diagnostic
- `45296e1` - Instructions de déploiement
- `209cc78` - **FIX PRINCIPAL : Contournement routing**
- `fb96328` - Outils de diagnostic
- `c666373` - Contournement GET params
- `9fc7377` - Amélioration gestion erreurs

---

## 🎯 SOLUTION APPLIQUÉE

### Problème Initial
URL qui ne fonctionnait pas :
```
https://app.dietsenegal.net/dietetic/portal/view_meal_plan/2
```
→ Erreur 404

### Solution Implémentée

**1. Modification de `controllers/Portal.php` (ligne ~331)**

Ajout d'une vérification dans la méthode `meal_plans()` :

```php
// Check if we're viewing a specific meal plan
$view_plan_id = $this->input->get('view');

if (!empty($view_plan_id) && is_numeric($view_plan_id)) {
    // Redirect to the view_meal_plan method
    log_activity('[DIETETIC DEBUG] meal_plans() redirecting to view_meal_plan(' . $view_plan_id . ')');
    return $this->view_meal_plan($view_plan_id);
}
```

**2. Modification de `views/portal_meal_plans.php` (ligne ~554)**

Changement du lien :

```php
<!-- AVANT (causait la 404) -->
<a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>">

<!-- APRÈS (fonctionne) -->
<a href="<?php echo site_url('dietetic/portal/meal_plans?view=' . $plan->id); ?>">
```

### Nouvelle URL Fonctionnelle

```
https://app.dietsenegal.net/dietetic/portal/meal_plans?view=2
```

---

## 📋 DÉPLOIEMENT SUR LE SERVEUR

### Méthode 1 : Script Automatique (Recommandé)

```bash
# 1. Connectez-vous au serveur
ssh utilisateur@app.dietsenegal.net

# 2. Allez dans le dossier du module
cd /home/trpuftja/app/modules/dietetic

# 3. Rendez le script exécutable et lancez-le
chmod +x deploy.sh
./deploy.sh
```

Le script fait automatiquement :
- ✅ Vérification du dossier
- ✅ Checkout de la bonne branche
- ✅ Pull des changements
- ✅ Vérification que les fichiers sont à jour

---

### Méthode 2 : Commandes Manuelles

```bash
# 1. Connectez-vous au serveur
ssh utilisateur@app.dietsenegal.net

# 2. Allez dans le dossier du module
cd /home/trpuftja/app/modules/dietetic

# 3. Récupérez les changements
git fetch origin
git checkout claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
git pull origin claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD

# 4. Vérifiez que les changements sont appliqués
grep "meal_plans?view=" views/portal_meal_plans.php
```

**Si la dernière commande affiche une ligne contenant `meal_plans?view=`, c'est bon ✅**

---

### Méthode 3 : Via PlanetHoster NOC (Sans Git)

Si Git ne fonctionne pas, modifiez manuellement :

**Fichier à modifier :** `modules/dietetic/views/portal_meal_plans.php`

**Ligne à trouver (vers ligne 554) :**
```php
<a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>" class="btn-view-meal">
```

**Remplacer par :**
```php
<a href="<?php echo site_url('dietetic/portal/meal_plans?view=' . $plan->id); ?>" class="btn-view-meal">
```

---

## ✅ VÉRIFICATION APRÈS DÉPLOIEMENT

### Test 1 : Vérifier les fichiers sur le serveur

```bash
# Vérifier Portal.php
grep -n "Check if we're viewing a specific meal plan" controllers/Portal.php

# Résultat attendu : Une ligne trouvée (vers ligne 331)
```

```bash
# Vérifier portal_meal_plans.php
grep -n "meal_plans?view=" views/portal_meal_plans.php

# Résultat attendu : Une ligne trouvée (vers ligne 554)
```

### Test 2 : Tester dans le navigateur

**A. Liste des plans (devrait fonctionner) :**
```
https://app.dietsenegal.net/dietetic/portal/meal_plans
```

**B. Détails d'un plan (nouvelle fonctionnalité) :**
```
https://app.dietsenegal.net/dietetic/portal/meal_plans?view=2
```

**C. Cliquer sur "Voir les Repas" dans la liste**
→ Devrait afficher les détails du plan au lieu d'une 404

---

## 🔧 EN CAS DE PROBLÈME

### Problème : Toujours 404 après déploiement

**Solution : Vider le cache**

```bash
# Via SSH
rm -rf /home/trpuftja/app/application/cache/*

# OU via Perfex
Setup → Clear Cache (si disponible)
```

**Puis vider le cache du navigateur :**
- Chrome/Edge : Ctrl + Shift + Delete
- Firefox : Ctrl + Shift + Delete
- Safari : Cmd + Option + E

### Problème : Git pull échoue

**Erreur possible :** "Your local changes would be overwritten"

**Solution :**
```bash
# Sauvegarder vos modifications locales
git stash

# Puis tirer les changements
git pull origin claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD

# Réappliquer vos modifications (si nécessaire)
git stash pop
```

### Problème : Le fichier n'est toujours pas à jour

**Vérification :**
```bash
# Voir le contenu exact de la ligne 554
sed -n '554p' views/portal_meal_plans.php
```

**Si ça affiche toujours l'ancien lien :**
→ Le pull n'a pas fonctionné
→ Utilisez la Méthode 3 (modification manuelle via NOC)

---

## 📊 RÉSUMÉ

| Élément | Statut |
|---------|--------|
| Code sur GitHub | ✅ Poussé (commit 209cc78) |
| Script de déploiement | ✅ Créé (deploy.sh) |
| Documentation | ✅ Complète |
| Fichiers modifiés | 2 fichiers |
| Tests nécessaires | 2 URLs à tester |

---

## 🆘 SUPPORT

Si après le déploiement ça ne fonctionne toujours pas :

1. **Vérifiez les logs Activity Log dans Perfex**
   - Setup → Activity Log
   - Cherchez `[DIETETIC DEBUG]`

2. **Exécutez le diagnostic**
   ```
   https://app.dietsenegal.net/modules/dietetic/diagnostic.php
   ```

3. **Contactez avec ces informations :**
   - Capture d'écran du diagnostic
   - Logs `[DIETETIC DEBUG]` trouvés
   - Résultat de la commande : `git log -1 --oneline`

---

**Date :** 2025-11-06
**Version :** Finale
**Branche :** claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
**Commit principal :** 209cc78
