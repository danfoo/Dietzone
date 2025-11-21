<?php
$active_page = 'recipes';
$page_title = 'Bibliothèque de Recettes';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === MOBILE FIRST DESIGN === */

/* Base Container */
.container {
    padding: 12px;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.filter-section h4 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-section h4 i {
    color: #01807B;
    font-size: 14px;
}

.filter-section hr {
    margin: 16px 0;
    border-color: #f1f3f5;
}

/* Search Input Mobile */
.filter-section .input-group {
    margin-bottom: 12px;
}

.filter-section .form-control {
    border-radius: 12px;
    border: none;
    background: #f8f9fa;
    padding: 12px 16px;
    font-size: 14px;
}

.filter-section .form-control:focus {
    background: #fff;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.filter-section .input-group-btn .btn {
    border-radius: 12px;
    padding: 12px 20px;
    font-weight: 600;
    font-size: 14px;
}

.filter-section .btn-danger {
    width: 100%;
    border-radius: 12px;
    padding: 12px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
}

/* Category Filters */
.category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.category-filter {
    padding: 10px 16px;
    border-radius: 24px;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.category-filter i {
    font-size: 12px;
}

.category-filter:hover,
.category-filter.active {
    background: #01807B;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
}

/* Tabs */
.recipes-tabs {
    background: white;
    border-radius: 16px;
    padding: 0;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.recipes-tabs .nav-tabs {
    border-bottom: 1px solid #f1f3f5;
    margin: 0;
    display: flex;
}

.recipes-tabs .nav-tabs li {
    flex: 1;
}

.recipes-tabs .nav-tabs li a {
    color: #6c757d;
    padding: 16px 12px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s;
    text-align: center;
    border: none;
    border-radius: 0;
    background: transparent;
}

.recipes-tabs .nav-tabs li.active a {
    color: #01807B;
    background: rgba(1, 128, 123, 0.05);
    border-bottom: 3px solid #01807B;
}

.recipes-tabs .tab-content {
    padding: 16px;
}

/* Recipe Grid - Mobile First */
.recipes-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Recipe Card */
.recipe-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.recipe-card:active {
    transform: scale(0.98);
}

/* Recipe Photo */
.recipe-photo {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.recipe-photo-placeholder {
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #dee2e6;
}

/* Recipe Content */
.recipe-content {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.recipe-title {
    font-size: 16px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 8px;
    line-height: 1.4;
}

.recipe-title a {
    color: #212529;
    text-decoration: none;
}

.recipe-title a:active {
    color: #01807B;
}

/* Recipe Meta */
.recipe-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #6c757d;
    font-size: 12px;
}

.recipe-meta-item i {
    color: #01807B;
    font-size: 11px;
}

/* Recipe Description */
.recipe-description {
    color: #6c757d;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Recipe Tags */
.recipe-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.recipe-tag {
    background: #f8f9fa;
    color: #495057;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Recipe Actions */
.recipe-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.recipe-actions .btn {
    flex: 1;
    border-radius: 12px;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
}

.recipe-actions .btn-primary {
    background: #01807B;
    border-color: #01807B;
    box-shadow: 0 2px 8px rgba(1, 128, 123, 0.2);
}

.recipe-actions .btn-primary:active {
    transform: scale(0.95);
}

/* Favorite Button */
.btn-favorite {
    background: #f8f9fa;
    color: #6c757d;
    border: none;
    transition: all 0.2s;
}

.btn-favorite:hover,
.btn-favorite.active {
    background: #dc3545;
    color: white;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
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
    border-radius: 12px;
    border: none;
    padding: 16px;
    font-size: 14px;
}

/* === TABLET (576px+) === */
@media (min-width: 576px) {
    .container {
        padding: 16px;
    }

    .filter-section {
        padding: 20px;
        margin-bottom: 20px;
    }

    .recipes-tabs {
        margin-bottom: 20px;
    }

    .recipes-tabs .tab-content {
        padding: 20px;
    }

    .recipes-tabs .nav-tabs li a {
        padding: 16px 20px;
        font-size: 14px;
    }

    .recipes-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        height: 200px;
    }

    .recipe-title {
        font-size: 17px;
    }

    .recipe-description {
        font-size: 14px;
    }

    .category-filter {
        padding: 10px 18px;
        font-size: 14px;
    }
}

/* === DESKTOP (992px+) === */
@media (min-width: 992px) {
    .container {
        padding: 24px;
    }

    .filter-section {
        padding: 24px;
        margin-bottom: 24px;
    }

    .filter-section .input-group {
        margin-bottom: 0;
    }

    .recipes-tabs {
        margin-bottom: 24px;
        padding: 20px;
    }

    .recipes-tabs .tab-content {
        padding: 24px 0 0 0;
    }

    .recipes-tabs .nav-tabs li a {
        padding: 16px 24px;
        font-size: 15px;
    }

    .recipes-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .recipe-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .recipe-photo,
    .recipe-photo-placeholder {
        height: 220px;
    }

    .recipe-photo-placeholder {
        font-size: 64px;
    }

    .recipe-content {
        padding: 20px;
    }

    .recipe-title {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .recipe-title a:hover {
        color: #01807B;
    }

    .recipe-meta {
        gap: 15px;
        margin-bottom: 12px;
    }

    .recipe-meta-item {
        font-size: 13px;
    }

    .recipe-meta-item i {
        font-size: 12px;
    }

    .recipe-description {
        -webkit-line-clamp: 3;
    }

    .recipe-tag {
        font-size: 12px;
    }

    .recipe-actions .btn {
        font-size: 14px;
    }

    .category-filter:hover {
        transform: translateY(-2px);
    }
}

/* === LARGE DESKTOP (1200px+) === */
@media (min-width: 1200px) {
    .recipes-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
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
