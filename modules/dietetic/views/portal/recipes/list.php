<?php
$active_page = 'recipes';
$page_title = 'Bibliothèque de Recettes';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === DESIGN INSPIRÉ DE L'IMAGE DE RÉFÉRENCE === */
/* Style moderne, coloré, friendly avec aesthetic iOS */

/* Variables de couleurs - Charte graphique officielle */
:root {
    --primary-color: #01807B;
    --primary-dark: #015a57;
    --primary-light: #019B95;
    --secondary-color: #F3911D;
    --secondary-dark: #e07d0f;
    --secondary-light: #FFA74D;
    --tertiary-color: #FFFFFF;
    --background: #F8F9FC;
    --card-bg: #FFFFFF;
    --text-primary: #1E1E1E;
    --text-secondary: #6B7280;
    --text-dark: #1a202c;
    --text-medium: #2d3748;
    --text-light: #718096;
    --border-color: #e2e8f0;
    --border-light: #edf2f7;
    --success-color: #48bb78;
    --success-light: #9ae6b4;
    --danger-color: #f56565;
    --warning-color: #ed8936;
    --info-color: #4299e1;
    --border-radius-lg: 20px;
    --border-radius-md: 16px;
    --border-radius-sm: 12px;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 8px 20px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 12px 28px rgba(0, 0, 0, 0.12);
    --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-bounce: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #f0f4f8 0%, #e8eff5 50%, #f5f7fa 100%);
    background-attachment: fixed;
    padding-top: 50px;
    padding-bottom: 80px;
    min-height: 100vh;
    color: var(--text-medium);
    line-height: 1.6;
}

/* === MOBILE FIRST DESIGN === */

/* Override content-container padding for this page */
.content-container {
    padding: 0 !important;
}

/* Base Container */
.container {
    padding: 15px;
    max-width: calc(100% - 30px);
    margin: 0 auto;
}

@media (min-width: 1400px) {
    .container {
        max-width: 1800px;
    }
}

/* Filter Section */
.filter-section {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    animation: fadeInUp 0.5s ease-out;
}

.filter-section h4 {
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 16px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.3px;
}

.filter-section h4 i {
    color: var(--primary-color);
    font-size: 14px;
}

.filter-section hr {
    margin: 20px 0;
    border: none;
    border-top: 2px solid #F1F3F5;
}

/* Search Input - Pill shaped avec icône intégrée */
.filter-section .search-wrapper {
    position: relative;
    margin-bottom: 16px;
}

.filter-section .search-wrapper .search-icon {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    font-size: 16px;
    pointer-events: none;
    z-index: 2;
}

.filter-section .input-group {
    margin-bottom: 16px;
}

.filter-section .form-control {
    border-radius: 50px;
    border: none;
    background: #F3F4F6;
    padding: 12px 45px 12px 18px;
    font-size: 14px;
    color: var(--text-primary);
    font-weight: 500;
    width: 100%;
    transition: var(--transition);
}

.filter-section .form-control::placeholder {
    color: #9CA3AF;
}

.filter-section .form-control:focus {
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(1, 128, 123, 0.1);
    outline: none;
}

.filter-section .input-group-btn {
    display: none;
}

.filter-section .btn-danger {
    width: 100%;
    border-radius: 50px;
    padding: 12px;
    font-weight: 700;
    font-size: 14px;
    background: var(--secondary-color);
    border: none;
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.3);
    letter-spacing: -0.2px;
    transition: var(--transition);
}

.filter-section .btn-danger:hover {
    background: var(--secondary-light);
    box-shadow: 0 6px 16px rgba(243, 145, 29, 0.4);
    transform: translateY(-2px);
}

/* Category Filters - Pills */
.category-filters {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 10px;
    margin-bottom: -10px;
}

.category-filters::-webkit-scrollbar {
    height: 6px;
}

.category-filters::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 10px;
}

.category-filters::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 10px;
}

.category-filters::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

.category-filter {
    padding: 10px 16px;
    border-radius: 50px;
    background: #F3F4F6;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: -0.2px;
    white-space: nowrap;
    flex-shrink: 0;
}

.category-filter i {
    font-size: 12px;
}

.category-filter:hover,
.category-filter.active {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

/* Tabs */
.recipes-tabs {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 0;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

.recipes-tabs .nav-tabs {
    border: none;
    margin: 0;
    display: flex;
    background: #F8F9FA;
}

.recipes-tabs .nav-tabs li {
    flex: 1;
}

.recipes-tabs .nav-tabs li a {
    color: var(--text-secondary);
    padding: 14px 12px;
    font-weight: 700;
    font-size: 13px;
    transition: var(--transition);
    text-align: center;
    border: none;
    border-radius: 0;
    background: transparent;
    letter-spacing: -0.2px;
}

.recipes-tabs .nav-tabs li.active a {
    color: var(--primary-color);
    background: white;
}

.recipes-tabs .tab-content {
    padding: 20px;
}

/* Recipe List - Mobile First */
.recipes-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Recipe Card - Layout horizontal (image à gauche) */
.recipe-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    display: flex;
    flex-direction: row;
    cursor: pointer;
    animation: scaleIn 0.4s ease-out;
}

.recipe-card:active {
    transform: scale(0.99);
}

/* Recipe Photo - À gauche, carrée */
.recipe-photo {
    width: 120px;
    min-width: 120px;
    height: 120px;
    object-fit: cover;
    flex-shrink: 0;
}

.recipe-photo-placeholder {
    width: 120px;
    min-width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: #D1D5DB;
    flex-shrink: 0;
}

/* Recipe Content - À droite de l'image */
.recipe-content {
    padding: 12px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Recipe Header - Temps et catégorie en haut */
.recipe-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.recipe-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: var(--text-secondary);
    font-size: 11px;
    font-weight: 600;
}

.recipe-meta-item i {
    color: var(--primary-color);
    font-size: 11px;
}

.recipe-category-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: -0.1px;
}

/* Recipe Title - Cliquable */
.recipe-title {
    font-size: 15px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 8px;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

.recipe-title a {
    color: var(--text-primary);
    text-decoration: none;
}

.recipe-title a:hover {
    color: var(--primary-color);
}

.recipe-title a:active {
    color: var(--primary-color);
}

/* Recipe Rating - En dessous du titre */
.recipe-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: auto;
    justify-content: space-between;
}

.recipe-rating-info {
    display: flex;
    align-items: center;
    gap: 4px;
}

.recipe-rating i {
    color: #FFC107;
    font-size: 13px;
}

.recipe-rating-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
}

.recipe-rating-count {
    font-size: 11px;
    color: var(--text-secondary);
}

/* Masquer les éléments non utilisés dans le layout liste */
.recipe-description,
.recipe-tags,
.recipe-actions {
    display: none;
}

/* Favorite Button - Inline avec les notes */
.btn-favorite {
    background: transparent;
    color: var(--text-secondary);
    border: none;
    transition: var(--transition-fast);
    padding: 4px;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.recipe-card {
    position: relative;
}

.btn-favorite:hover {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.btn-favorite.active {
    color: var(--secondary-color);
}

.btn-favorite:active {
    transform: scale(0.95);
}

.btn-favorite i.fa-heart {
    display: none;
    font-size: 16px;
}

.btn-favorite.active i.fa-heart {
    display: inline;
}

.btn-favorite i.fa-heart-o {
    display: inline;
    font-size: 16px;
}

.btn-favorite.active i.fa-heart-o {
    display: none;
}

.btn-favorite span {
    display: none;
}

/* Alerts */
.alert {
    border-radius: var(--border-radius-md);
    border: none;
    padding: 18px;
    font-size: 15px;
    font-weight: 600;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.pagination a,
.pagination span {
    padding: 10px 16px;
    border-radius: 50px;
    background: #F3F4F6;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
}

.pagination a:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.pagination .active {
    background: var(--primary-color);
    color: white;
}

.pagination .disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* === TABLET (576px+) === */
@media (min-width: 576px) {
    .container {
        padding: 20px;
    }

    .filter-section {
        padding: 24px;
        margin-bottom: 24px;
    }

    .filter-section .btn-danger {
        width: auto;
        min-width: 200px;
        float: right;
    }

    .recipes-tabs {
        margin-bottom: 24px;
    }

    .recipes-tabs .tab-content {
        padding: 24px;
    }

    .recipes-tabs .nav-tabs li a {
        padding: 18px 24px;
        font-size: 15px;
    }

    .recipes-grid {
        gap: 18px;
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        width: 140px;
        min-width: 140px;
        height: 140px;
    }

    .recipe-title {
        font-size: 17px;
    }

    .category-filter {
        padding: 12px 22px;
        font-size: 15px;
    }
}

/* === DESKTOP (992px+) === */
@media (min-width: 992px) {
    .container {
        padding: 32px;
    }

    .filter-section {
        padding: 28px;
        margin-bottom: 28px;
    }

    .filter-section .input-group {
        margin-bottom: 0;
    }

    .recipes-tabs {
        margin-bottom: 28px;
    }

    .recipes-tabs .tab-content {
        padding: 28px;
    }

    .recipes-tabs .nav-tabs li a {
        padding: 20px 28px;
        font-size: 16px;
    }

    .recipes-grid {
        gap: 20px;
    }

    .recipe-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        width: 160px;
        min-width: 160px;
        height: 160px;
    }

    .recipe-photo-placeholder {
        font-size: 48px;
    }

    .recipe-content {
        padding: 16px 20px;
    }

    .recipe-title {
        font-size: 18px;
    }

    .recipe-title a:hover {
        color: var(--primary-color);
    }

    .recipe-meta-item {
        font-size: 13px;
    }

    .recipe-rating-value {
        font-size: 15px;
    }

    .category-filter:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(1, 128, 123, 0.3);
    }
}
</style>

<div class="container">
    <!-- Search and Filters -->
    <div class="filter-section">
        <form method="get" action="<?php echo site_url('dietetic/portal/recipes'); ?>">
            <div class="row">
                <div class="col-md-12">
                    <div class="search-wrapper">
                        <input type="text" class="form-control" name="search"
                               placeholder="Rechercher une recette..."
                               value="<?php echo $this->input->get('search'); ?>"
                               onkeypress="if(event.keyCode==13) this.form.submit();">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                </div>
            </div>
        </form>

        <div class="row" style="margin-top: 16px;">
            <div class="col-md-12">
                <a href="<?php echo site_url('dietetic/portal/recipes_favorites'); ?>"
                   class="btn btn-danger btn-block">
                    <i class="fa fa-heart"></i> Mes Favoris (<?php echo count($favorites); ?>)
                </a>
            </div>
        </div>

        <hr>

        <h4><i class="fa fa-filter"></i> Catégories</h4>
        <div class="category-filters">
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>"
               class="category-filter <?php echo !$this->input->get('category') ? 'active' : ''; ?>">
                Toutes
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=breakfast'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'breakfast' ? 'active' : ''; ?>">
                <i class="fa fa-coffee"></i> Petit-déjeuner
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=lunch'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'lunch' ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i> Déjeuner
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=dinner'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'dinner' ? 'active' : ''; ?>">
                <i class="fa fa-moon-o"></i> Dîner
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=snack'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'snack' ? 'active' : ''; ?>">
                <i class="fa fa-apple"></i> Collation
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=smoothie'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'smoothie' ? 'active' : ''; ?>">
                <i class="fa fa-glass"></i> Smoothie
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=juice'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'juice' ? 'active' : ''; ?>">
                <i class="fa fa-tint"></i> Jus naturel
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=beverage'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'beverage' ? 'active' : ''; ?>">
                <i class="fa fa-coffee"></i> Boisson
            </a>
            <a href="<?php echo site_url('dietetic/portal/recipes?category=dessert'); ?>"
               class="category-filter <?php echo $this->input->get('category') == 'dessert' ? 'active' : ''; ?>">
                <i class="fa fa-birthday-cake"></i> Dessert
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="recipes-tabs">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#all" data-toggle="tab">
                    <i class="fa fa-book"></i> Les recettes
                </a>
            </li>
            <li>
                <a href="#assigned" data-toggle="tab">
                    <i class="fa fa-star"></i> Recommandées
                </a>
            </li>
        </ul>

        <div class="tab-content" style="padding-top: 20px;">
            <!-- All Recipes Tab -->
            <div class="tab-pane active" id="all">
                <?php if (empty($all_recipes)) : ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Aucune recette trouvée.
                    </div>
                <?php else : ?>
                    <div class="recipes-grid">
                        <?php foreach ($all_recipes as $recipe) : ?>
                            <?php
                            $is_favorite = false;
                            foreach ($favorites as $fav) {
                                if ($fav->id == $recipe->id) {
                                    $is_favorite = true;
                                    break;
                                }
                            }
                            ?>
                            <div class="recipe-card" onclick="window.location.href='<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>'">
                                <!-- Image à gauche -->
                                <?php if ($recipe->main_photo) : ?>
                                    <img src="<?php echo base_url($recipe->main_photo->photo_url); ?>"
                                         alt="<?php echo htmlspecialchars($recipe->name); ?>"
                                         class="recipe-photo">
                                <?php else : ?>
                                    <div class="recipe-photo-placeholder">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Contenu à droite -->
                                <div class="recipe-content">
                                    <!-- Header: Temps + Catégorie -->
                                    <div class="recipe-header">
                                        <div class="recipe-meta">
                                            <?php if ($recipe->preparation_time) : ?>
                                                <div class="recipe-meta-item">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo $recipe->preparation_time; ?> min
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($recipe->category) : ?>
                                            <span class="recipe-category-badge">
                                                <?php
                                                $category_labels = [
                                                    'breakfast' => 'Petit-déjeuner',
                                                    'lunch' => 'Déjeuner',
                                                    'dinner' => 'Dîner',
                                                    'snack' => 'Collation',
                                                    'smoothie' => 'Smoothie',
                                                    'juice' => 'Jus naturel',
                                                    'beverage' => 'Boisson',
                                                    'dessert' => 'Dessert'
                                                ];
                                                echo $category_labels[$recipe->category] ?? ucfirst($recipe->category);
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Titre cliquable -->
                                    <h3 class="recipe-title">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>" onclick="event.stopPropagation();">
                                            <?php echo htmlspecialchars($recipe->name); ?>
                                        </a>
                                    </h3>

                                    <!-- Rating sous le titre -->
                                    <?php if ($recipe->average_rating > 0) : ?>
                                        <div class="recipe-rating">
                                            <div class="recipe-rating-info">
                                                <i class="fa fa-star"></i>
                                                <span class="recipe-rating-value"><?php echo number_format($recipe->average_rating, 1); ?></span>
                                                <span class="recipe-rating-count">(<?php echo $recipe->ratings_count; ?> avis)</span>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                    onclick="event.stopPropagation(); toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                                <i class="fa fa-heart"></i>
                                                <i class="fa fa-heart-o"></i>
                                                <span>Favori</span>
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <div class="recipe-rating">
                                            <div class="recipe-rating-info">
                                                <i class="fa fa-star-o"></i>
                                                <span class="recipe-rating-count">Pas encore d'avis</span>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                    onclick="event.stopPropagation(); toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                                <i class="fa fa-heart"></i>
                                                <i class="fa fa-heart-o"></i>
                                                <span>Favori</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination (seulement si > 12 recettes) -->
                    <?php if (count($all_recipes) > 12) : ?>
                        <div class="pagination-wrapper">
                            <div class="pagination">
                                <span class="disabled"><i class="fa fa-chevron-left"></i></span>
                                <span class="active">1</span>
                                <a href="#">2</a>
                                <a href="#">3</a>
                                <a href="#">4</a>
                                <span>...</span>
                                <a href="#"><?php echo ceil(count($all_recipes) / 12); ?></a>
                                <a href="#"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Assigned Recipes Tab -->
            <div class="tab-pane" id="assigned">
                <?php if (empty($assigned_recipes)) : ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Votre diététicien ne vous a pas encore assigné de recettes.
                    </div>
                <?php else : ?>
                    <div class="recipes-grid">
                        <?php foreach ($assigned_recipes as $recipe) : ?>
                            <?php
                            $is_favorite = false;
                            foreach ($favorites as $fav) {
                                if ($fav->id == $recipe->id) {
                                    $is_favorite = true;
                                    break;
                                }
                            }
                            ?>
                            <div class="recipe-card" onclick="window.location.href='<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>'">
                                <!-- Image à gauche -->
                                <?php if ($recipe->main_photo) : ?>
                                    <img src="<?php echo base_url($recipe->main_photo->photo_url); ?>"
                                         alt="<?php echo htmlspecialchars($recipe->name); ?>"
                                         class="recipe-photo">
                                <?php else : ?>
                                    <div class="recipe-photo-placeholder">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Contenu à droite -->
                                <div class="recipe-content">
                                    <!-- Header: Temps + Catégorie -->
                                    <div class="recipe-header">
                                        <div class="recipe-meta">
                                            <?php if ($recipe->preparation_time) : ?>
                                                <div class="recipe-meta-item">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo $recipe->preparation_time; ?> min
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($recipe->category) : ?>
                                            <span class="recipe-category-badge">
                                                <?php
                                                $category_labels = [
                                                    'breakfast' => 'Petit-déjeuner',
                                                    'lunch' => 'Déjeuner',
                                                    'dinner' => 'Dîner',
                                                    'snack' => 'Collation',
                                                    'smoothie' => 'Smoothie',
                                                    'juice' => 'Jus naturel',
                                                    'beverage' => 'Boisson',
                                                    'dessert' => 'Dessert'
                                                ];
                                                echo $category_labels[$recipe->category] ?? ucfirst($recipe->category);
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Titre cliquable -->
                                    <h3 class="recipe-title">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>" onclick="event.stopPropagation();">
                                            <?php echo htmlspecialchars($recipe->name); ?>
                                        </a>
                                    </h3>

                                    <!-- Rating sous le titre -->
                                    <?php if ($recipe->average_rating > 0) : ?>
                                        <div class="recipe-rating">
                                            <div class="recipe-rating-info">
                                                <i class="fa fa-star"></i>
                                                <span class="recipe-rating-value"><?php echo number_format($recipe->average_rating, 1); ?></span>
                                                <span class="recipe-rating-count">(<?php echo $recipe->ratings_count; ?> avis)</span>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                    onclick="event.stopPropagation(); toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                                <i class="fa fa-heart"></i>
                                                <i class="fa fa-heart-o"></i>
                                                <span>Favori</span>
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <div class="recipe-rating">
                                            <div class="recipe-rating-info">
                                                <i class="fa fa-star-o"></i>
                                                <span class="recipe-rating-count">Pas encore d'avis</span>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                    onclick="event.stopPropagation(); toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                                <i class="fa fa-heart"></i>
                                                <i class="fa fa-heart-o"></i>
                                                <span>Favori</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination (seulement si > 12 recettes) -->
                    <?php if (count($assigned_recipes) > 12) : ?>
                        <div class="pagination-wrapper">
                            <div class="pagination">
                                <span class="disabled"><i class="fa fa-chevron-left"></i></span>
                                <span class="active">1</span>
                                <a href="#">2</a>
                                <a href="#">3</a>
                                <a href="#">4</a>
                                <span>...</span>
                                <a href="#"><?php echo ceil(count($assigned_recipes) / 12); ?></a>
                                <a href="#"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction globale pour gérer les favoris (appelée via onclick)
function toggleFavorite(recipeId, btnElement) {
    const btn = $(btnElement);
    const isFavorite = btn.hasClass('active');
    const action = isFavorite ? 'remove_from_favorites' : 'add_to_favorites';
    const url = '<?php echo site_url('dietetic/portal/'); ?>' + action;

    $.post(url, {
        recipe_id: recipeId,
        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function(response) {
        try {
            const data = JSON.parse(response);

            if (data.success) {
                btn.toggleClass('active');
                if (window.alert_float) {
                    alert_float('success', data.message);
                }
            } else {
                if (window.alert_float) {
                    alert_float('danger', data.message);
                } else {
                    alert('Erreur: ' + data.message);
                }
            }
        } catch(e) {
            if (window.alert_float) {
                alert_float('danger', 'Une erreur est survenue');
            } else {
                alert('Une erreur est survenue');
            }
        }
    }).fail(function(xhr, status, error) {
        if (window.alert_float) {
            alert_float('danger', 'Erreur de connexion');
        } else {
            alert('Erreur de connexion');
        }
    });
}
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
