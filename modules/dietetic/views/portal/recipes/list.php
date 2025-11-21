<?php
$active_page = 'recipes';
$page_title = 'Bibliothèque de Recettes';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === DESIGN INSPIRÉ DE L'IMAGE DE RÉFÉRENCE === */
/* Style moderne, coloré, friendly avec aesthetic iOS */

/* Variables de couleurs */
:root {
    --primary-color: #5B5EF4;
    --primary-dark: #4547D8;
    --secondary-color: #FF6B6B;
    --background: #F8F9FC;
    --card-bg: #FFFFFF;
    --text-primary: #1E1E1E;
    --text-secondary: #6B7280;
    --border-radius-lg: 20px;
    --border-radius-md: 16px;
    --border-radius-sm: 12px;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
}

body {
    background: var(--background);
}

/* === MOBILE FIRST DESIGN === */

/* Base Container */
.container {
    padding: 16px;
    max-width: 1400px;
}

/* Filter Section */
.filter-section {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.filter-section h4 {
    font-size: 18px;
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
    font-size: 16px;
}

.filter-section hr {
    margin: 20px 0;
    border: none;
    border-top: 2px solid #F1F3F5;
}

/* Search Input - Pill shaped */
.filter-section .input-group {
    margin-bottom: 16px;
}

.filter-section .form-control {
    border-radius: 50px;
    border: none;
    background: #F3F4F6;
    padding: 14px 20px;
    font-size: 15px;
    color: var(--text-primary);
    font-weight: 500;
}

.filter-section .form-control::placeholder {
    color: #9CA3AF;
}

.filter-section .form-control:focus {
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(91, 94, 244, 0.1);
    outline: none;
}

.filter-section .input-group-btn .btn {
    border-radius: 50px;
    padding: 14px 28px;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: -0.2px;
}

.filter-section .btn-primary {
    background: var(--primary-color);
    border: none;
    box-shadow: 0 4px 12px rgba(91, 94, 244, 0.3);
}

.filter-section .btn-primary:hover,
.filter-section .btn-primary:focus {
    background: var(--primary-dark);
    box-shadow: 0 6px 16px rgba(91, 94, 244, 0.4);
}

.filter-section .btn-danger {
    width: 100%;
    border-radius: 50px;
    padding: 14px;
    font-weight: 700;
    font-size: 15px;
    background: var(--secondary-color);
    border: none;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    letter-spacing: -0.2px;
}

.filter-section .btn-danger:hover {
    background: #FF5252;
    box-shadow: 0 6px 16px rgba(255, 107, 107, 0.4);
}

/* Category Filters - Pills */
.category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.category-filter {
    padding: 12px 20px;
    border-radius: 50px;
    background: #F3F4F6;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -0.2px;
}

.category-filter i {
    font-size: 13px;
}

.category-filter:hover,
.category-filter.active {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(91, 94, 244, 0.3);
}

/* Tabs */
.recipes-tabs {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 0;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
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
    padding: 18px 16px;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.2s;
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

/* Recipe Grid - Mobile First */
.recipes-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Recipe Card - Style moderne iOS */
.recipe-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.recipe-card:active {
    transform: scale(0.97);
}

/* Recipe Photo - Grande et arrondie */
.recipe-photo {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.recipe-photo-placeholder {
    width: 100%;
    height: 220px;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 56px;
    color: #D1D5DB;
}

/* Recipe Content - Spacing généreux */
.recipe-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.recipe-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 12px;
    line-height: 1.3;
    letter-spacing: -0.4px;
}

.recipe-title a {
    color: var(--text-primary);
    text-decoration: none;
}

.recipe-title a:active {
    color: var(--primary-color);
}

/* Recipe Meta - Avec icônes */
.recipe-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 600;
}

.recipe-meta-item i {
    color: var(--primary-color);
    font-size: 13px;
}

/* Recipe Description */
.recipe-description {
    color: var(--text-secondary);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Recipe Tags */
.recipe-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.recipe-tag {
    background: #F3F4F6;
    color: var(--text-primary);
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: -0.1px;
}

/* Recipe Actions - Boutons arrondis style iOS */
.recipe-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.recipe-actions .btn {
    flex: 1;
    border-radius: 50px;
    padding: 13px;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.2s;
    border: none;
    letter-spacing: -0.2px;
}

.recipe-actions .btn-primary {
    background: var(--primary-color);
    color: white;
    box-shadow: 0 4px 12px rgba(91, 94, 244, 0.3);
}

.recipe-actions .btn-primary:hover {
    background: var(--primary-dark);
    box-shadow: 0 6px 16px rgba(91, 94, 244, 0.4);
}

.recipe-actions .btn-primary:active {
    transform: scale(0.95);
}

/* Favorite Button */
.btn-favorite {
    background: #F3F4F6;
    color: var(--text-secondary);
    border: none;
    transition: all 0.2s;
}

.btn-favorite:hover,
.btn-favorite.active {
    background: var(--secondary-color);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.btn-favorite:active {
    transform: scale(0.95);
}

.btn-favorite i.fa-heart {
    display: none;
}

.btn-favorite.active i.fa-heart {
    display: inline;
}

.btn-favorite i.fa-heart-o {
    display: inline;
}

.btn-favorite.active i.fa-heart-o {
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

/* === TABLET (576px+) === */
@media (min-width: 576px) {
    .container {
        padding: 20px;
    }

    .filter-section {
        padding: 24px;
        margin-bottom: 24px;
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
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        height: 240px;
    }

    .recipe-title {
        font-size: 19px;
    }

    .recipe-description {
        font-size: 15px;
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
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .recipe-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        height: 260px;
    }

    .recipe-photo-placeholder {
        font-size: 72px;
    }

    .recipe-content {
        padding: 24px;
    }

    .recipe-title {
        font-size: 20px;
        margin-bottom: 14px;
    }

    .recipe-title a:hover {
        color: var(--primary-color);
    }

    .recipe-meta {
        gap: 18px;
        margin-bottom: 16px;
    }

    .recipe-meta-item {
        font-size: 14px;
    }

    .recipe-description {
        -webkit-line-clamp: 3;
        font-size: 15px;
    }

    .recipe-tag {
        font-size: 13px;
    }

    .recipe-actions .btn {
        font-size: 15px;
        padding: 14px;
    }

    .category-filter:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(91, 94, 244, 0.3);
    }
}

/* === LARGE DESKTOP (1200px+) === */
@media (min-width: 1200px) {
    .recipes-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        height: 280px;
    }
}

/* === EXTRA LARGE (1400px+) === */
@media (min-width: 1400px) {
    .recipes-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
</style>

<div class="container">
    <!-- Search and Filters -->
    <div class="filter-section">
        <form method="get" action="<?php echo site_url('dietetic/portal/recipes'); ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search"
                               placeholder="Rechercher une recette..."
                               value="<?php echo $this->input->get('search'); ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-search"></i> Rechercher
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <a href="<?php echo site_url('dietetic/portal/recipes_favorites'); ?>"
                       class="btn btn-danger btn-block">
                        <i class="fa fa-heart"></i> Mes Favoris (<?php echo count($favorites); ?>)
                    </a>
                </div>
            </div>
        </form>

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
                    <i class="fa fa-book"></i> Toutes les Recettes (<?php echo count($all_recipes); ?>)
                </a>
            </li>
            <li>
                <a href="#assigned" data-toggle="tab">
                    <i class="fa fa-star"></i> Mes Recettes Assignées (<?php echo count($assigned_recipes); ?>)
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
                            <div class="recipe-card">
                                <?php if ($recipe->main_photo) : ?>
                                    <img src="<?php echo base_url($recipe->main_photo->photo_url); ?>"
                                         alt="<?php echo htmlspecialchars($recipe->name); ?>"
                                         class="recipe-photo">
                                <?php else : ?>
                                    <div class="recipe-photo-placeholder">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="recipe-content">
                                    <h3 class="recipe-title">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>">
                                            <?php echo htmlspecialchars($recipe->name); ?>
                                        </a>
                                    </h3>

                                    <div class="recipe-meta">
                                        <?php if ($recipe->preparation_time) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo $recipe->preparation_time; ?> min
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->average_rating > 0) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-star" style="color: #f39c12;"></i>
                                                <?php echo number_format($recipe->average_rating, 1); ?> (<?php echo $recipe->ratings_count; ?>)
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($recipe->description) : ?>
                                        <p class="recipe-description">
                                            <?php echo nl2br(htmlspecialchars(substr($recipe->description, 0, 120))); ?>
                                            <?php if (strlen($recipe->description) > 120) echo '...'; ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($recipe->tags)) : ?>
                                        <div class="recipe-tags">
                                            <?php foreach (array_slice($recipe->tags, 0, 3) as $tag) : ?>
                                                <span class="recipe-tag"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="recipe-actions">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> Voir
                                        </a>
                                        <button type="button"
                                                class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                onclick="toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                            <i class="fa fa-heart"></i>
                                            <i class="fa fa-heart-o"></i>
                                            Favori
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                            <div class="recipe-card">
                                <?php if ($recipe->main_photo) : ?>
                                    <img src="<?php echo base_url($recipe->main_photo->photo_url); ?>"
                                         alt="<?php echo htmlspecialchars($recipe->name); ?>"
                                         class="recipe-photo">
                                <?php else : ?>
                                    <div class="recipe-photo-placeholder">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="recipe-content">
                                    <h3 class="recipe-title">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>">
                                            <?php echo htmlspecialchars($recipe->name); ?>
                                        </a>
                                    </h3>

                                    <div class="recipe-meta">
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-user"></i>
                                            Assigné par <?php echo htmlspecialchars($recipe->dietitian_name); ?>
                                        </div>
                                        <?php if ($recipe->preparation_time) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo $recipe->preparation_time; ?> min
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($recipe->notes) : ?>
                                        <div class="alert alert-info" style="margin-top: 10px;">
                                            <strong>Note de votre diététicien :</strong><br>
                                            <?php echo nl2br(htmlspecialchars($recipe->notes)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($recipe->tags)) : ?>
                                        <div class="recipe-tags">
                                            <?php foreach (array_slice($recipe->tags, 0, 3) as $tag) : ?>
                                                <span class="recipe-tag"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="recipe-actions">
                                        <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> Voir
                                        </a>
                                        <button type="button"
                                                class="btn btn-favorite btn-sm <?php echo $is_favorite ? 'active' : ''; ?>"
                                                onclick="toggleFavorite(<?php echo $recipe->id; ?>, this); return false;">
                                            <i class="fa fa-heart"></i>
                                            <i class="fa fa-heart-o"></i>
                                            Favori
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
