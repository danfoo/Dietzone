# 📰 Système Blog & Conseils - Dietzone

**Version** : 1.0.0
**Date** : 30 Novembre 2025
**Auteur** : Claude AI - Super Lead Dev

---

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Installation](#installation)
3. [Architecture](#architecture)
4. [Utilisation Admin](#utilisation-admin)
5. [Utilisation Portal Patient](#utilisation-portal-patient)
6. [API & Endpoints](#api--endpoints)
7. [Personnalisation](#personnalisation)
8. [Troubleshooting](#troubleshooting)

---

## 🌟 Vue d'ensemble

Le système Blog & Conseils permet aux diététiciens de :
- ✅ Publier des articles nutritionnels et des conseils santé
- ✅ Organiser le contenu par catégories colorées
- ✅ Gérer des brouillons avant publication
- ✅ Suivre les vues et statistiques
- ✅ Offrir un contenu éducatif aux patients via le portail

### Fonctionnalités principales

**Pour les Admins/Staff :**
- Création et édition d'articles avec éditeur WYSIWYG
- Gestion des catégories (couleurs, icônes, ordre)
- Upload d'images à la une
- Système de tags
- Statuts : Brouillon, Publié, Archivé
- Planification de publication

**Pour les Patients :**
- Navigation par catégories
- Recherche d'articles
- Vue des articles publiés
- Articles connexes
- Compteur de vues

---

## 🚀 Installation

### Étape 1 : Exécuter la migration

La migration se trouve dans : `modules/dietetic/migrations/010_add_blog_system.php`

**Via l'interface web :**
1. Aller dans : `/admin/dietetic/migrations` (si disponible)
2. Exécuter la migration `010_add_blog_system`

**Via le système Perfex :**
```php
// La migration s'exécute automatiquement lors de l'activation du module
// OU manuellement via :
$CI->load->library('migration');
$CI->migration->version(10); // Version 010
```

### Étape 2 : Vérifier les tables créées

4 tables sont créées :
```sql
tbldietic_blog_articles
tbldietic_blog_categories
tbldietic_blog_comments (pour futur usage)
tbldietic_blog_article_views
```

5 catégories par défaut sont insérées :
- 🍎 Nutrition (vert #4CAF50)
- 🍴 Recettes Santé (orange #FF9800)
- ❤️ Perte de Poids (rose #E91E63)
- 🍃 Bien-être (violet #9C27B0)
- 💓 Sport & Activité (bleu #2196F3)

### Étape 3 : Charger les articles de démonstration (optionnel)

```sql
-- Se connecter à la base de données
mysql -u username -p database_name

-- Charger le fichier
source modules/dietetic/sample_blog_data.sql;

-- OU copier-coller le contenu directement
-- Note : Remplacer author_id par un staff_id valide de votre système
```

### Étape 4 : Vérifier le menu

Le menu "Blog & Conseils" devrait apparaître dans :
- **Admin** : Sidebar Diététique → Blog & Conseils
- **Portal Patient** : Menu latéral → Conseils & Blog

---

## 🏗️ Architecture

### Base de données

#### Table `tbldietic_blog_articles`

| Colonne | Type | Description |
|---------|------|-------------|
| id | INT(11) | ID unique |
| title | VARCHAR(255) | Titre de l'article |
| slug | VARCHAR(255) | URL-friendly (unique) |
| excerpt | TEXT | Résumé court |
| content | LONGTEXT | Contenu HTML complet |
| featured_image | VARCHAR(255) | Nom du fichier image |
| category | VARCHAR(100) | Slug de la catégorie |
| tags | TEXT | Tags séparés par virgules |
| author_id | INT(11) | ID du staff (diététicien) |
| status | ENUM | draft/published/archived |
| views_count | INT(11) | Nombre de vues |
| published_at | DATETIME | Date de publication |
| created_at | DATETIME | Date de création |
| updated_at | DATETIME | Dernière modification |

**Index :**
- Unique sur `slug`
- Index sur `author_id`, `status`, `category`, `published_at`

#### Table `tbldietic_blog_categories`

| Colonne | Type | Description |
|---------|------|-------------|
| id | INT(11) | ID unique |
| name | VARCHAR(100) | Nom affiché |
| slug | VARCHAR(100) | URL-friendly (unique) |
| description | TEXT | Description |
| color | VARCHAR(7) | Code couleur hex (#01807B) |
| icon | VARCHAR(50) | Classe Font Awesome (fa-apple) |
| order | INT(11) | Ordre d'affichage |
| created_at | DATETIME | Date de création |

### Fichiers du système

```
modules/dietetic/
├── controllers/
│   ├── Blog.php                    # Contrôleur admin
│   └── Portal.php                  # Méthodes blog() et blog_article()
├── models/
│   └── Dietetic_blog_model.php     # Modèle de données
├── views/
│   ├── admin/blog/
│   │   ├── manage.php              # Liste des articles
│   │   ├── form.php                # Formulaire création/édition
│   │   └── categories.php          # Gestion des catégories
│   └── portal/blog/
│       ├── index.php               # Grille d'articles
│       └── article.php             # Vue détaillée
├── migrations/
│   └── 010_add_blog_system.php     # Migration des tables
├── uploads/blog/                   # Images uploadées
├── sample_blog_data.sql            # Articles de démo
└── BLOG_SYSTEM_README.md           # Ce fichier
```

---

## 👨‍💼 Utilisation Admin

### Créer un article

1. **Accéder à la liste :**
   Menu Diététique → Blog & Conseils

2. **Nouveau article :**
   Cliquer sur "Nouvel Article"

3. **Remplir le formulaire :**
   - **Titre** * (requis)
   - **Slug** : Généré automatiquement si vide
   - **Extrait** : Résumé court pour les cards
   - **Contenu** * : Éditeur TinyMCE avec formatage HTML
   - **Catégorie** : Sélectionner dans la liste
   - **Tags** : Séparés par virgules
   - **Image à la une** : Upload (max 5MB)
   - **Statut** : Draft/Publié/Archivé
   - **Date de publication** : Planifier ou NOW()

4. **Sauvegarder :**
   - Draft → Enregistré mais non visible patients
   - Publié → Visible immédiatement
   - Archivé → Masqué mais conservé

### Gérer les catégories

1. **Accéder :**
   Blog & Conseils → Bouton "Catégories de Blog"

2. **Ajouter une catégorie :**
   - Cliquer "Nouvelle Catégorie"
   - Remplir le formulaire modal :
     - Nom (ex: "Végétarisme")
     - Slug (auto-généré)
     - Description
     - Couleur (picker)
     - Icône Font Awesome (ex: fa-leaf)
     - Ordre (numérique)

3. **Modifier/Supprimer :**
   - Icône crayon → Éditer
   - Icône poubelle → Supprimer (si aucun article lié)

### Astuce : Slug automatique

Le slug est généré automatiquement à partir du titre :
```
"10 Aliments Riches en Protéines"
→ "10-aliments-riches-en-proteines"
```

Caractéristiques :
- Lettres accentuées converties (é→e, à→a)
- Espaces remplacés par tirets
- Minuscules uniquement
- Pas de caractères spéciaux

---

## 👥 Utilisation Portal Patient

### Accéder au blog

**Menu latéral** → Conseils & Blog

### Navigation

1. **Filtrer par catégorie :**
   - Cliquer sur un badge de catégorie coloré
   - "Toutes les Catégories" pour réinitialiser

2. **Lire un article :**
   - Cliquer sur une card d'article
   - L'article s'ouvre en pleine page
   - Les vues sont incrémentées automatiquement

3. **Articles connexes :**
   - En bas de chaque article
   - Affiche 3 articles de la même catégorie
   - Exclut l'article actuel

### Affichage des cards

Chaque card affiche :
- Image à la une (ou gradient si absente)
- Badge catégorie (avec couleur et icône)
- Titre
- Extrait (tronqué à 150 caractères)
- Date de publication
- Nombre de vues

---

## 🔌 API & Endpoints

### Routes Admin

| Route | Méthode | Description |
|-------|---------|-------------|
| `/admin/dietetic/blog` | GET | Liste articles |
| `/admin/dietetic/blog/create` | GET/POST | Créer article |
| `/admin/dietetic/blog/edit/{id}` | GET/POST | Éditer article |
| `/admin/dietetic/blog/delete/{id}` | GET | Supprimer article |
| `/admin/dietetic/blog/change_status/{id}` | POST | Changer statut (AJAX) |
| `/admin/dietetic/blog/delete_image/{id}` | POST | Supprimer image (AJAX) |
| `/admin/dietetic/blog/categories` | GET/POST | Gérer catégories |
| `/admin/dietetic/blog/delete_category/{id}` | GET | Supprimer catégorie |

### Routes Portal

| Route | Méthode | Description |
|-------|---------|-------------|
| `/dietetic/portal/blog` | GET | Liste articles publiés |
| `/dietetic/portal/blog?category={slug}` | GET | Filtrer par catégorie |
| `/dietetic/portal/blog?page={n}` | GET | Pagination |
| `/dietetic/portal/blog_article/{slug}` | GET | Vue détaillée |
| `/dietetic/portal/blog_search` | GET | Recherche (futur) |

### Modèle - Méthodes principales

```php
// Récupérer un article
$article = $this->dietetic_blog_model->get($id_or_slug);

// Liste tous les articles
$articles = $this->dietetic_blog_model->get_all(['status' => 'published']);

// Articles du portail (avec pagination)
$articles = $this->dietetic_blog_model->get_published_articles($limit, $offset, $category_slug);

// Compter les articles
$count = $this->dietetic_blog_model->count_articles(['status' => 'published']);

// Articles connexes
$related = $this->dietetic_blog_model->get_related_articles($article_id, $limit);

// Incrémenter les vues
$this->dietetic_blog_model->increment_views($article_id, $patient_id);

// Catégories
$categories = $this->dietetic_blog_model->get_all_categories();
$category = $this->dietetic_blog_model->get_category($id_or_slug);
```

---

## 🎨 Personnalisation

### Modifier les couleurs

**Fichier** : `modules/dietetic/views/portal/blog/index.php`

```css
.blog-card-image {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    /* Remplacer par vos couleurs */
}

.category-filter-btn:hover,
.category-filter-btn.active {
    border-color: #01807B;
    background: #01807B;
    /* Couleur principale */
}
```

### Ajouter un champ personnalisé

1. **Modifier la migration** :
```php
ALTER TABLE tbldietic_blog_articles
ADD COLUMN author_bio TEXT AFTER author_id;
```

2. **Modifier le formulaire** (`form.php`) :
```html
<div class="form-group">
    <label>Biographie auteur</label>
    <textarea name="author_bio" class="form-control"></textarea>
</div>
```

3. **Modifier la vue article** (`article.php`) :
```php
<?php if ($article->author_bio) { ?>
    <div class="author-bio"><?php echo $article->author_bio; ?></div>
<?php } ?>
```

### Modifier la pagination

**Fichier** : `modules/dietetic/controllers/Portal.php`

```php
// Ligne ~7120
$per_page = 12; // Modifier le nombre d'articles par page
```

### Désactiver le compteur de vues

**Fichier** : `modules/dietetic/controllers/Portal.php`

```php
// Commenter la ligne ~7200
// $this->dietetic_blog_model->increment_views($article->id, $patient_id);
```

---

## 🐛 Troubleshooting

### Le menu Blog n'apparaît pas

**Solution 1 : Vérifier les permissions**
```php
// L'utilisateur doit avoir la permission 'dietetic' / 'view'
Setup → Roles → Sélectionner le rôle → Dietetic → Cocher "View"
```

**Solution 2 : Clear cache**
```
/admin/dietetic/clear_cache
```

**Solution 3 : Vérifier la migration**
```sql
-- Vérifier que les tables existent
SHOW TABLES LIKE '%dietic_blog%';

-- Résultat attendu : 4 tables
```

### Les images ne s'affichent pas

**Vérifier les permissions du dossier :**
```bash
chmod 755 uploads/blog/
```

**Chemin d'accès :**
```php
// URL correcte :
base_url('uploads/blog/' . $filename)

// Chemin physique :
FCPATH . 'uploads/blog/' . $filename
```

**Note importante :** Les images sont stockées dans le dossier `uploads/blog/` de Perfex (et non dans le module) pour éviter de les perdre lors des mises à jour du module.

### Les catégories n'apparaissent pas

**Réinsérer les catégories par défaut :**
```sql
-- Voir le contenu de la migration 010_add_blog_system.php
-- Copier la section INSERT INTO tbldietic_blog_categories
```

### Erreur 500 sur la page articles

**Vérifier les logs :**
```
application/logs/ → Derniers fichiers de log
```

**Vérifications courantes :**
- TinyMCE chargé ?
- jQuery disponible ?
- Permissions du contrôleur ?

### Le slug génère une erreur "duplicate"

**Le slug doit être unique :**
```php
// Modifier manuellement ou laisser le système auto-incrémenter :
"10-aliments-riches-en-proteines" → existe
"10-aliments-riches-en-proteines-2" → créé automatiquement
```

### Les articles ne s'affichent pas dans le portail

**Vérifier le statut :**
```sql
SELECT id, title, status, published_at
FROM tbldietic_blog_articles
WHERE status = 'published'
AND published_at <= NOW();
```

Seuls les articles `published` avec une date passée s'affichent.

---

## 📊 Statistiques & Métriques

### Vues d'articles

**Table** : `tbldietic_blog_article_views`

```sql
-- Articles les plus vus
SELECT a.title, COUNT(v.id) as total_views
FROM tbldietic_blog_articles a
LEFT JOIN tbldietic_blog_article_views v ON v.article_id = a.id
WHERE a.status = 'published'
GROUP BY a.id
ORDER BY total_views DESC
LIMIT 10;
```

### Statistiques patients

```sql
-- Patient le plus actif (lecteur)
SELECT patient_id, COUNT(*) as articles_lus
FROM tbldietic_blog_article_views
WHERE patient_id IS NOT NULL
GROUP BY patient_id
ORDER BY articles_lus DESC
LIMIT 10;
```

---

## 🚀 Améliorations futures

### Court terme
- [ ] Système de commentaires (table déjà créée)
- [ ] Recherche d'articles
- [ ] Export PDF des articles
- [ ] Partage sur réseaux sociaux

### Long terme
- [ ] Articles en plusieurs langues
- [ ] Système de likes/favoris
- [ ] Newsletter automatique
- [ ] Suggestions d'articles basées sur l'IA
- [ ] Vidéos/Podcasts intégrés

---

## 📞 Support

**Problèmes courants :**
1. Vérifier les logs : `application/logs/`
2. Clear cache : `/admin/dietetic/clear_cache`
3. Vérifier permissions fichiers : `chmod 755`
4. Consulter ce README

**Contact Développeur :**
- Session : `claude/continue-dietzone-project-01UNPsJBHd7EErfSgfTodaQ7`
- Date : 30 Novembre 2025

---

## 📜 Changelog

### Version 1.0.0 (30 Nov 2025)
- ✅ Migration 010 créée avec 4 tables
- ✅ Contrôleur Blog.php complet
- ✅ Modèle Dietetic_blog_model.php
- ✅ Vues admin : manage, form, categories
- ✅ Vues portal : index, article
- ✅ 56 traductions françaises
- ✅ 6 articles de démonstration
- ✅ Documentation complète

---

**Développé avec ❤️ par Claude AI - Super Lead Dev**
