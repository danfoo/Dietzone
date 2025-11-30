# 🚀 Installation Rapide du Système Blog - 5 Minutes

**Date :** 30 Novembre 2025
**Version :** 1.0.0

---

## ⚡ INSTALLATION EN 3 ÉTAPES

### Étape 1️⃣ : Créer les tables (2 minutes)

**Via phpMyAdmin :**

1. Ouvrez phpMyAdmin
2. Sélectionnez votre base de données (ex: `trpuftja_wqa`)
3. Cliquez sur l'onglet **"SQL"**
4. **Copiez-collez** le contenu du fichier :
   ```
   modules/dietetic/install_blog_system.sql
   ```
5. Cliquez sur **"Exécuter"**

✅ **Résultat attendu :**
```
4 tables créées avec succès
5 catégories insérées
```

**Vérification :**
```sql
SHOW TABLES LIKE '%dietic_blog%';
```

Vous devriez voir :
- `tbldietic_blog_articles`
- `tbldietic_blog_categories`
- `tbldietic_blog_comments`
- `tbldietic_blog_article_views`

---

### Étape 2️⃣ : Trouver votre Staff ID (1 minute)

**Via phpMyAdmin :**

1. Onglet **"SQL"**
2. Exécutez cette requête :
   ```sql
   SELECT staffid, CONCAT(firstname, ' ', lastname) as nom
   FROM tblstaff;
   ```
3. **Notez votre `staffid`** (ex: 5)

---

### Étape 3️⃣ : Charger les articles de démo (2 minutes)

1. **Ouvrez le fichier :**
   ```
   modules/dietetic/sample_blog_data.sql
   ```

2. **Modifiez la ligne 9 :**
   ```sql
   SET @author_id = 1;  -- ← CHANGEZ avec votre staffid !
   ```

   **Exemple :**
   ```sql
   SET @author_id = 5;  -- Si votre staffid est 5
   ```

3. **Dans phpMyAdmin :**
   - Onglet **"SQL"**
   - **Copiez-collez tout le contenu** du fichier
   - Cliquez **"Exécuter"**

✅ **Résultat attendu :**
```
6 articles insérés avec succès
```

**Vérification :**
```sql
SELECT id, title, status FROM tbldietic_blog_articles;
```

---

## ✅ VÉRIFICATION FINALE

### 1. Vider le cache

**Via navigateur :**
- Chrome/Edge : `Ctrl + Shift + Delete` → Vider cache
- Firefox : `Ctrl + Shift + Delete` → Vider cache

**Via Perfex (si disponible) :**
```
/admin/utilities/clear_cache
OU
/admin/dietetic/clear_cache
```

### 2. Se déconnecter et reconnecter

1. Cliquez sur votre profil → Déconnexion
2. Reconnectez-vous

### 3. Vérifier le menu

**Dans l'admin :**
```
Menu de gauche → Diététique → Blog & Conseils
```

Vous devriez voir :
- 📋 Liste des articles (6 articles)
- ➕ Bouton "Nouvel Article"
- 📁 Bouton "Catégories de Blog"

**Dans le portail patient :**
```
Menu latéral → Conseils & Blog
```

---

## 🐛 PROBLÈMES FRÉQUENTS

### ❌ Le menu "Blog & Conseils" n'apparaît pas

**Solution 1 : Vérifier les tables**
```sql
SHOW TABLES LIKE '%dietic_blog%';
-- Doit retourner 4 tables
```

**Solution 2 : Vider le cache**
- Navigateur : Ctrl + Shift + Delete
- Perfex : `/admin/utilities/clear_cache`
- Se déconnecter et reconnecter

**Solution 3 : Vérifier les permissions**
```
Setup → Staff → Sélectionnez votre utilisateur
→ Onglet Permissions → Dietetic → Cocher "View"
```

**Solution 4 : Force refresh**
```
Dans la page admin : Ctrl + F5 (Windows) ou Cmd + Shift + R (Mac)
```

---

### ❌ Erreur : "La table n'existe pas"

**Cause :** Les tables n'ont pas été créées

**Solution :**
1. Retournez à l'Étape 1
2. Exécutez `install_blog_system.sql`
3. Vérifiez avec `SHOW TABLES LIKE '%dietic_blog%';`

---

### ❌ Erreur lors du chargement des articles

**Erreur :** `Column 'author_id' cannot be null`

**Cause :** Vous n'avez pas défini `@author_id`

**Solution :**
1. Ouvrez `sample_blog_data.sql`
2. Ligne 9 : `SET @author_id = VOTRE_ID;`
3. Trouvez votre ID avec :
   ```sql
   SELECT staffid FROM tblstaff WHERE email = 'votre@email.com';
   ```

---

### ❌ Les articles ne s'affichent pas dans le portail patient

**Vérifications :**

1. **Les articles sont publiés ?**
   ```sql
   SELECT id, title, status, published_at
   FROM tbldietic_blog_articles
   WHERE status = 'published';
   ```

2. **La date de publication est passée ?**
   ```sql
   UPDATE tbldietic_blog_articles
   SET published_at = NOW()
   WHERE status = 'published' AND published_at > NOW();
   ```

3. **Le patient est connecté ?**
   - Connectez-vous au portail patient
   - Menu latéral → Conseils & Blog

---

## 📊 VÉRIFICATION COMPLÈTE

Exécutez ces requêtes pour tout vérifier :

```sql
-- 1. Tables créées ?
SHOW TABLES LIKE '%dietic_blog%';
-- Résultat attendu : 4 lignes

-- 2. Catégories créées ?
SELECT COUNT(*) as total FROM tbldietic_blog_categories;
-- Résultat attendu : 5

-- 3. Articles créés ?
SELECT COUNT(*) as total FROM tbldietic_blog_articles;
-- Résultat attendu : 6

-- 4. Articles publiés ?
SELECT COUNT(*) as total FROM tbldietic_blog_articles WHERE status = 'published';
-- Résultat attendu : 5

-- 5. Détails articles
SELECT id, title, status, category, author_id
FROM tbldietic_blog_articles
ORDER BY id;
```

---

## 🎯 RÉSULTAT FINAL ATTENDU

Après l'installation complète, vous devriez avoir :

✅ **4 tables** créées
✅ **5 catégories** colorées (Nutrition, Recettes, Perte de Poids, Bien-être, Sport)
✅ **6 articles** de démonstration
✅ **Menu "Blog & Conseils"** visible dans admin
✅ **Menu "Conseils & Blog"** visible dans portail patient
✅ Articles lisibles et fonctionnels

---

## 🆘 BESOIN D'AIDE ?

Si le problème persiste :

1. **Vérifiez les logs d'erreur :**
   ```
   application/logs/log-YYYY-MM-DD.php
   ```

2. **Console navigateur :**
   - F12 → Onglet Console
   - Regardez les erreurs JavaScript

3. **Requête de diagnostic :**
   ```sql
   SELECT
       (SELECT COUNT(*) FROM tbldietic_blog_articles) as articles,
       (SELECT COUNT(*) FROM tbldietic_blog_categories) as categories,
       (SELECT COUNT(*) FROM tbldietic_blog_articles WHERE status='published') as published;
   ```

4. **Contactez le support** avec les informations ci-dessus

---

## 📝 NOTES IMPORTANTES

- ⚠️ **Toujours faire une sauvegarde** avant d'exécuter des scripts SQL
- 💡 Les articles avec `status = 'draft'` ne sont pas visibles pour les patients
- 🔒 Seuls les staff avec permission `dietetic/view` voient le menu
- 🎨 Vous pouvez personnaliser les catégories (couleurs, icônes) après installation

---

**Installation terminée ! Profitez de votre système Blog/Conseils !** 🎉
