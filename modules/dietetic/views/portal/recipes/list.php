<?php
$active_page = 'recipes';
$page_title = 'Bibliothèque de Recettes';
$this->load->view('portal/includes/portal_header');
?>

<style>
.recipes-tabs {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    border: 2px solid #f1f3f5;
}

.recipes-tabs .nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

.recipes-tabs .nav-tabs li a {
    color: #6c757d;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s;
}

.recipes-tabs .nav-tabs li.active a {
    color: #01807B;
    border-bottom: 3px solid #01807B;
    background: transparent;
}

.recipe-card {
    background: white;
    border-radius: 12px;
    border: 2px solid #f1f3f5;
    margin-bottom: 20px;
    overflow: hidden;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
}

.recipe-card:hover {
    border-color: #01807B;
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.15);
    transform: translateY(-4px);
}

.recipe-photo {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.recipe-photo-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #f1f3f5 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    color: #adb5bd;
}

.recipe-content {
    padding: 20px;
    flex: 1;
}

.recipe-title {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 10px;
}

.recipe-title a {
    color: #212529;
    text-decoration: none;
    transition: color 0.3s;
}

.recipe-title a:hover {
    color: #01807B;
}

.recipe-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6c757d;
    font-size: 13px;
}

.recipe-meta-item i {
    color: #01807B;
}

.recipe-description {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.recipe-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}

.recipe-tag {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.recipe-actions {
    display: flex;
    gap: 10px;
}

.btn-favorite {
    border: 2px solid #e9ecef;
    background: white;
    color: #6c757d;
    transition: all 0.3s;
}

.btn-favorite:hover,
.btn-favorite.active {
    border-color: #dc3545;
    background: #dc3545;
    color: white;
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

.filter-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    border: 2px solid #f1f3f5;
}

.filter-section h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #212529;
}

.category-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.category-filter {
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #e9ecef;
    background: white;
    color: #495057;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
}

.category-filter:hover,
.category-filter.active {
    border-color: #01807B;
    background: #01807B;
    color: white;
    text-decoration: none;
}

.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

@media (max-width: 768px) {
    .recipes-grid {
        grid-template-columns: 1fr;
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
                                                data-recipe-id="<?php echo $recipe->id; ?>">
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
                                                data-recipe-id="<?php echo $recipe->id; ?>">
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
$(document).ready(function() {
    // Toggle favorite
    $('.btn-favorite').click(function() {
        const btn = $(this);
        const recipeId = btn.data('recipe-id');
        const isFavorite = btn.hasClass('active');

        const action = isFavorite ? 'remove_from_favorites' : 'add_to_favorites';

        $.post('<?php echo site_url('dietetic/portal/'); ?>' + action, {
            recipe_id: recipeId,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                btn.toggleClass('active');
                // Show toast notification
                if (window.alert_float) {
                    alert_float('success', data.message);
                }
            } else {
                alert(data.message);
            }
        });
    });
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
