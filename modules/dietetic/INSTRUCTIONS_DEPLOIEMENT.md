# 🚀 INSTRUCTIONS DE DÉPLOIEMENT - URGENT

## 📌 Contexte

Des corrections ont été apportées pour résoudre l'erreur 404 sur la page de visualisation des plans alimentaires.
Ces changements sont sur GitHub mais **doivent être déployés sur le serveur** `app.dietsenegal.net`.

---

## ✅ Changements à Déployer

**Branche GitHub :** `claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD`

**Derniers commits :**
- `209cc78` - Fix: Contournement du problème de routing
- `fb96328` - Add: Outils de diagnostic avancés
- `c666373` - Fix: Contournement avec paramètres GET
- `9fc7377` - Fix: Améliore la gestion des erreurs 404

**Fichiers modifiés :**
- `modules/dietetic/controllers/Portal.php`
- `modules/dietetic/views/portal_meal_plans.php`

---

## 🛠️ Étapes de Déploiement

### Méthode 1 : Via Git (Recommandé)

**1. Connectez-vous au serveur via SSH**

```bash
ssh utilisateur@app.dietsenegal.net
```

**2. Naviguez vers le dossier du module**

```bash
cd /chemin/vers/perfex/modules/dietetic
# Exemples possibles :
# cd /var/www/html/perfex/modules/dietetic
# cd /home/utilisateur/public_html/modules/dietetic
```

**3. Vérifiez l'état actuel**

```bash
git status
git branch
```

**4. Récupérez les changements**

```bash
git fetch origin
git checkout claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
git pull origin claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
```

**5. Vérifiez que les changements sont appliqués**

```bash
grep -n "Check if we're viewing a specific meal plan" controllers/Portal.php
```

**Résultat attendu :**
```
331:        // Check if we're viewing a specific meal plan
```

Si cette ligne apparaît, le déploiement est réussi ✅

---

### Méthode 2 : Via FTP/SFTP (Alternative)

Si Git n'est pas disponible sur le serveur :

**1. Téléchargez les fichiers depuis GitHub**
   - Allez sur : https://github.com/danfoo/Dietzone/tree/claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
   - Téléchargez : `modules/dietetic/controllers/Portal.php`
   - Téléchargez : `modules/dietetic/views/portal_meal_plans.php`

**2. Uploadez via FTP/SFTP**
   - Remplacez `modules/dietetic/controllers/Portal.php`
   - Remplacez `modules/dietetic/views/portal_meal_plans.php`

**3. Vérifiez les permissions**
   ```bash
   chmod 644 modules/dietetic/controllers/Portal.php
   chmod 644 modules/dietetic/views/portal_meal_plans.php
   ```

---

### Méthode 3 : Via cPanel File Manager

**1. Connectez-vous à cPanel**

**2. Ouvrez File Manager**

**3. Naviguez vers :**
   ```
   public_html/modules/dietetic/controllers/
   ```

**4. Remplacez Portal.php**
   - Téléchargez depuis GitHub
   - Uploadez le nouveau fichier

**5. Naviguez vers :**
   ```
   public_html/modules/dietetic/views/
   ```

**6. Remplacez portal_meal_plans.php**
   - Téléchargez depuis GitHub
   - Uploadez le nouveau fichier

---

## ✅ Vérification du Déploiement

### Test 1 : Vérifier que le fichier est à jour

Sur le serveur, exécutez :
```bash
grep "Check if we're viewing a specific meal plan" modules/dietetic/controllers/Portal.php
```

Si cette ligne existe, le fichier est à jour ✅

### Test 2 : Tester l'URL dans le navigateur

**Liste des plans (devrait fonctionner comme avant) :**
```
https://app.dietsenegal.net/dietetic/portal/meal_plans
```

**Détails d'un plan (nouvelle fonctionnalité) :**
```
https://app.dietsenegal.net/dietetic/portal/meal_plans?view=2
```

**Résultat attendu :** Les détails du plan alimentaire s'affichent ✅

---

## 🐛 En cas de Problème

### Problème : Toujours 404 après déploiement

**Solution 1 : Videz les caches**

```bash
# Dans Perfex
Setup → Clear Cache

# Via PHP (si accessible)
rm -rf application/cache/*

# Redémarrez PHP-FPM (si applicable)
sudo systemctl restart php7.4-fpm
# OU
sudo systemctl restart php8.0-fpm
```

**Solution 2 : Vérifiez les permissions**

```bash
chmod -R 755 modules/dietetic/controllers/
chmod -R 755 modules/dietetic/views/
chmod 644 modules/dietetic/controllers/Portal.php
chmod 644 modules/dietetic/views/portal_meal_plans.php
```

**Solution 3 : Consultez les logs**

```bash
# Logs Apache
tail -100 /var/log/apache2/error.log

# Logs Nginx
tail -100 /var/log/nginx/error.log

# Logs PHP
tail -100 /var/log/php-fpm/error.log
```

### Problème : Erreur PHP après déploiement

Si vous voyez une erreur PHP (page blanche ou erreur 500) :

**1. Vérifiez la syntaxe PHP**
```bash
php -l modules/dietetic/controllers/Portal.php
```

**2. Consultez les logs d'erreur PHP**

**3. Restaurez l'ancienne version temporairement**
```bash
git checkout HEAD~1 modules/dietetic/controllers/Portal.php
```

---

## 📊 Résumé des Changements

### Ce qui a été modifié dans Portal.php :

La méthode `meal_plans()` accepte maintenant un paramètre GET `view` :

```php
// Avant (ligne ~322)
public function meal_plans()
{
    // ... code existant
}

// Après (ligne ~324)
public function meal_plans()
{
    // Check if we're viewing a specific meal plan
    $view_plan_id = $this->input->get('view');

    if (!empty($view_plan_id) && is_numeric($view_plan_id)) {
        return $this->view_meal_plan($view_plan_id);
    }

    // ... code existant
}
```

### Ce qui a été modifié dans portal_meal_plans.php :

Le lien vers les détails utilise maintenant le paramètre `view` :

```php
// Avant (ligne ~553)
<a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>">

// Après (ligne ~554)
<a href="<?php echo site_url('dietetic/portal/meal_plans?view=' . $plan->id); ?>">
```

---

## 🆘 Support

Si vous rencontrez des problèmes lors du déploiement :

1. Vérifiez que vous avez les bonnes permissions sur le serveur
2. Consultez les logs d'erreur
3. Testez les URLs de vérification ci-dessus
4. Contactez l'équipe de développement avec les messages d'erreur exacts

---

**Date :** 2025-11-06
**Branche :** `claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD`
**Commits :** 209cc78, fb96328, c666373, 9fc7377
