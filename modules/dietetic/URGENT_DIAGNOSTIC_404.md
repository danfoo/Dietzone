# 🚨 DIAGNOSTIC URGENT - Erreur 404 Persistante sur view_meal_plan

## Statut Actuel
- ❌ URL `/dietetic/portal/view_meal_plan/2` → 404
- ❌ URL `/dietetic/portal/viewmealplan/2` → 404
- ❌ URL `/dietetic/portal/mealplan/2` → 404
- ❌ URL `/dietetic/portal/meal_plan_view?id=2` → 404
- ✅ URL `/dietetic/portal/meal_plans` → Fonctionne
- ✅ URL `/dietetic/portal` (dashboard) → Fonctionne

## 🔍 Tests de Diagnostic à Faire

### Test 1 : Vérifier que le contrôleur est accessible
**URL à tester :**
```
https://app.dietsenegal.net/dietetic/portal/test
```

**Résultat attendu :**
- Une page avec "✅ Portal Controller Test OK"

**Si ça fonctionne :**
- Le contrôleur Portal est accessible
- Le problème est spécifique à view_meal_plan

**Si ça ne fonctionne pas :**
- Le contrôleur Portal a un problème de routing global
- Vérifier que le module est activé

---

### Test 2 : Tester un appel direct hardcodé
**URL à tester :**
```
https://app.dietsenegal.net/dietetic/portal/test_view_2
```

**Résultat attendu :**
- Affichage du plan alimentaire ID 2
- OU redirection vers login si non connecté
- OU redirection vers meal_plans si plan n'existe pas

**Si ça fonctionne :**
- La méthode view_meal_plan() fonctionne quand appelée directement
- Le problème est dans le passage du paramètre

**Si ça ne fonctionne pas :**
- Problème dans la méthode view_meal_plan() elle-même
- Vérifier les logs Activity Log

---

### Test 3 : Vérifier les logs
**Emplacement :**
Perfex CRM → Setup → Activity Log

**Chercher :**
`[DIETETIC DEBUG]`

**Questions :**
1. Voyez-vous des logs quand vous testez les URLs ?
2. Quel est le dernier log visible ?
3. Y a-t-il des erreurs PHP dans les logs ?

---

## 🛠️ Vérifications à Faire sur le Serveur

### 1. Vérifier que les changements sont déployés

Connectez-vous au serveur et vérifiez :

```bash
cd /chemin/vers/perfex/modules/dietetic/controllers
cat Portal.php | grep -A 5 "public function test()"
```

**Résultat attendu :**
Vous devriez voir la méthode `test()` que j'ai créée.

**Si la méthode n'existe pas :**
Les changements ne sont pas déployés. Exécutez :
```bash
cd /chemin/vers/perfex/modules/dietetic
git fetch origin
git checkout claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
git pull origin claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
```

---

### 2. Vérifier que le module est activé

**Dans Perfex CRM :**
1. Setup → Modules
2. Cherchez "Dietetic Management"
3. Vérifiez qu'il est **Activé** (bouton vert)

**Si désactivé :**
Activez-le et réessayez.

---

### 3. Vérifier les permissions du fichier

```bash
ls -la /chemin/vers/perfex/modules/dietetic/controllers/Portal.php
```

**Permission attendue :**
`-rw-r--r--` (644) ou équivalent

**Si mauvaises permissions :**
```bash
chmod 644 /chemin/vers/perfex/modules/dietetic/controllers/Portal.php
```

---

### 4. Vérifier les erreurs PHP

**Logs Apache/Nginx :**
```bash
# Pour Apache
tail -f /var/log/apache2/error.log

# Pour Nginx
tail -f /var/log/nginx/error.log
```

**Puis testez l'URL et regardez les erreurs en temps réel.**

---

## 🔧 Solutions Possibles

### Solution A : Problème de cache
**Action :**
Videz le cache de Perfex et du navigateur

**Dans Perfex :**
Setup → Clear Cache (si disponible)

**Dans le navigateur :**
Ctrl+Shift+R (force reload)

---

### Solution B : Problème de .htaccess
**Vérifiez que le fichier .htaccess existe :**
```bash
ls -la /chemin/vers/perfex/.htaccess
```

**Contenu minimum requis :**
```apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

---

### Solution C : Créer une page de redirection standalone

Si tout échoue, je peux créer un fichier PHP standalone qui fait la redirection :

**Fichier à créer :** `modules/dietetic/view_plan.php`
```php
<?php
// Standalone entry point
require_once('../../application/bootstrap.php');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
redirect('dietetic/portal/meal_plans');
```

**URL d'accès :**
```
https://app.dietsenegal.net/modules/dietetic/view_plan.php?id=2
```

---

## 📋 Checklist de Diagnostic

Cochez au fur et à mesure :

- [ ] Test 1 : `/dietetic/portal/test` fonctionne ?
- [ ] Test 2 : `/dietetic/portal/test_view_2` fonctionne ?
- [ ] Test 3 : Des logs `[DIETETIC DEBUG]` apparaissent ?
- [ ] Changements déployés (méthode `test()` existe) ?
- [ ] Module activé dans Setup → Modules ?
- [ ] Permissions du fichier correctes (644) ?
- [ ] Erreurs PHP dans les logs Apache/Nginx ?
- [ ] Cache vidé ?
- [ ] Fichier .htaccess présent et correct ?

---

## 🆘 Prochaines Étapes

1. **Déployez** les derniers changements (avec méthodes test)
2. **Testez** les 2 URLs de diagnostic
3. **Consultez** les Activity Logs
4. **Rapportez** les résultats :
   - Quelle URL fonctionne ?
   - Que disent les logs ?
   - Y a-t-il des erreurs PHP ?

---

**Date :** 2025-11-06
**Commits :** c666373, 9fc7377
**Branche :** claude/incomplete-request-011CUrrfkZGfQkdXijx3fAVD
