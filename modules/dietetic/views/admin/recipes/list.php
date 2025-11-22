<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<style>
/* Modern Recipes Design */
:root {
    --recipe-red: #e74c3c;
    --recipe-red-dark: #c0392b;
}

.recipes-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.recipes-header h4 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 600;
}

.recipes-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.btn-new-recipe {
    background: white;
    color: var(--recipe-red);
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-new-recipe:hover {
    background: #f8f9fa;
    color: var(--recipe-red-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s;
    text-align: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stat-card .stat-icon {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.3;
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    margin: 5px 0;
}

.stat-card .stat-label {
    font-size: 13px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.stat-card.recipe-total {
    border-left: 4px solid #e74c3c;
}

.stat-card.recipe-total .stat-icon,
.stat-card.recipe-total .stat-value {
    color: #e74c3c;
}

.stat-card.recipe-pending {
    border-left: 4px solid #f39c12;
}

.stat-card.recipe-pending .stat-icon,
.stat-card.recipe-pending .stat-value {
    color: #f39c12;
}

.stat-card.recipe-approved {
    border-left: 4px solid #27ae60;
}

.stat-card.recipe-approved .stat-icon,
.stat-card.recipe-approved .stat-value {
    color: #27ae60;
}

.stat-card.recipe-rejected {
    border-left: 4px solid #95a5a6;
}

.stat-card.recipe-rejected .stat-icon,
.stat-card.recipe-rejected .stat-value {
    color: #95a5a6;
}

/* Filter Tabs */
.filter-tabs {
    background: white;
    border-radius: 10px;
    padding: 15px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.filter-tabs .nav-tabs {
    border-bottom: 2px solid #ecf0f1;
    margin-bottom: 0;
}

.filter-tabs .nav-tabs li {
    margin-bottom: -2px;
}

.filter-tabs .nav-tabs li a {
    color: #7f8c8d;
    border: none;
    padding: 12px 24px;
    transition: all 0.3s;
    border-bottom: 3px solid transparent;
    background: transparent !important;
}

.filter-tabs .nav-tabs li a:hover {
    background: #f8f9fa !important;
    border-bottom-color: #e74c3c;
}

.filter-tabs .nav-tabs li.active a {
    color: #e74c3c;
    border-bottom: 3px solid #e74c3c;
    background: transparent !important;
}

/* Recipe Cards */
.recipe-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    display: flex;
    gap: 20px;
}

.recipe-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.recipe-photo {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}

.recipe-photo-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #95a5a6;
    flex-shrink: 0;
}

.recipe-info {
    flex: 1;
}

.recipe-info h3 {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 600;
}

.recipe-info h3 a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s;
}

.recipe-info h3 a:hover {
    color: #e74c3c;
}

.recipe-status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 10px;
}

.recipe-status-badge.status-pending {
    background: #fef5e7;
    color: #f39c12;
}

.recipe-status-badge.status-approved {
    background: #eafaf1;
    color: #27ae60;
}

.recipe-status-badge.status-rejected {
    background: #fadbd8;
    color: #e74c3c;
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
    gap: 5px;
    color: #7f8c8d;
    font-size: 13px;
}

.recipe-meta-item i {
    color: #95a5a6;
}

.recipe-description {
    color: #7f8c8d;
    margin-bottom: 12px;
    line-height: 1.5;
}

.recipe-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* Search Box */
.search-box {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

#recipe-search {
    width: 100%;
    padding: 15px 50px 15px 20px;
    font-size: 16px;
    border: 2px solid #e74c3c;
    border-radius: 25px;
    box-shadow: 0 2px 8px rgba(231, 76, 60, 0.2);
    transition: all 0.3s;
}

#recipe-search:focus {
    border-color: #c0392b;
    box-shadow: 0 0 12px rgba(231, 76, 60, 0.4);
    outline: none;
}

.search-icon {
    position: absolute;
    right: 35px;
    top: 50%;
    transform: translateY(-50%);
    color: #e74c3c;
    font-size: 18px;
    pointer-events: none;
}

.search-results-count {
    margin-top: 10px;
    color: #7f8c8d;
    font-size: 14px;
}

#no-results {
    background: white;
    border-radius: 10px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

#no-results i {
    font-size: 64px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

#no-results h4 {
    color: #7f8c8d;
    margin-bottom: 10px;
}

#no-results p {
    color: #95a5a6;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="recipes-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                <i class="fa fa-book"></i> Bibliothèque de Recettes
                            </h4>
                            <p>
                                <i class="fa fa-info-circle"></i> Gérez votre collection de recettes diététiques
                            </p>
                        </div>
                        <div class="col-md-4 text-right" style="padding-top: 10px;">
                            <?php if (dietetic_has_permission('create')) : ?>
                                <a href="<?php echo admin_url('dietetic/recipes/create'); ?>" class="btn btn-new-recipe">
                                    <i class="fa fa-plus-circle"></i> Nouvelle Recette
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-3">
                        <div class="stat-card recipe-total">
                            <div class="stat-icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats->total; ?></div>
                            <div class="stat-label">Total Recettes</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card recipe-pending">
                            <div class="stat-icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats->pending; ?></div>
                            <div class="stat-label">En Attente</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card recipe-approved">
                            <div class="stat-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats->approved; ?></div>
                            <div class="stat-label">Approuvées</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card recipe-rejected">
                            <div class="stat-icon">
                                <i class="fa fa-times-circle"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats->rejected; ?></div>
                            <div class="stat-label">Rejetées</div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <ul class="nav nav-tabs">
                        <li class="<?php echo $current_status == 'all' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=all'); ?>">
                                <i class="fa fa-th-list"></i> Toutes (<?php echo $stats->total; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'pending' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=pending'); ?>">
                                <i class="fa fa-clock-o"></i> En Attente (<?php echo $stats->pending; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'approved' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=approved'); ?>">
                                <i class="fa fa-check-circle"></i> Approuvées (<?php echo $stats->approved; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'rejected' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=rejected'); ?>">
                                <i class="fa fa-times-circle"></i> Rejetées (<?php echo $stats->rejected; ?>)
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Search Bar -->
                <div class="search-box">
                    <div style="position: relative;">
                        <input type="text" id="recipe-search" placeholder="🔍 Rechercher une recette par nom, catégorie, diététicien...">
                        <i class="fa fa-search search-icon"></i>
                    </div>
                    <div class="search-results-count">
                        <i class="fa fa-info-circle"></i> <span id="visible-count"><?php echo count($recipes); ?></span> recette(s) affichée(s)
                    </div>
                </div>

                <!-- Recipes List -->
                <div id="recipes-container">
                    <?php if (empty($recipes)) : ?>
                        <div class="alert alert-info" style="border-radius: 10px;">
                            <i class="fa fa-info-circle"></i>
                            Aucune recette trouvée.
                        </div>
                    <?php else : ?>
                        <?php foreach ($recipes as $recipe) : ?>
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

                                <div class="recipe-info">
                                    <h3>
                                        <a href="<?php echo admin_url('dietetic/recipes/view/' . $recipe->id); ?>">
                                            <?php echo htmlspecialchars($recipe->name); ?>
                                        </a>
                                        <span class="recipe-status-badge status-<?php echo $recipe->status; ?>">
                                            <?php
                                            $status_labels = [
                                                'pending' => 'En attente',
                                                'approved' => 'Approuvée',
                                                'rejected' => 'Rejetée'
                                            ];
                                            echo $status_labels[$recipe->status] ?? $recipe->status;
                                            ?>
                                        </span>
                                    </h3>

                                    <div class="recipe-meta">
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-user"></i>
                                            <?php echo htmlspecialchars($recipe->dietitian_name); ?>
                                        </div>
                                        <?php if ($recipe->category) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-tag"></i>
                                                <?php
                                                $categories = [
                                                    'breakfast' => 'Petit-déjeuner',
                                                    'lunch' => 'Déjeuner',
                                                    'dinner' => 'Dîner',
                                                    'snack' => 'Collation'
                                                ];
                                                echo $categories[$recipe->category] ?? $recipe->category;
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->preparation_time) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo $recipe->preparation_time; ?> min
                                            </div>
                                        <?php endif; ?>
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-list"></i>
                                            <?php echo $recipe->ingredients_count; ?> ingrédients
                                        </div>
                                        <?php if ($recipe->ratings_count > 0) : ?>
                                            <div class="recipe-meta-item">
                                                <i class="fa fa-star" style="color: #f39c12;"></i>
                                                <?php echo number_format($recipe->average_rating, 1); ?>/5 (<?php echo $recipe->ratings_count; ?>)
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($recipe->description) : ?>
                                        <div class="recipe-description">
                                            <?php echo nl2br(htmlspecialchars(substr($recipe->description, 0, 200))); ?>
                                            <?php if (strlen($recipe->description) > 200) echo '...'; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="recipe-actions">
                                        <a href="<?php echo admin_url('dietetic/recipes/view/' . $recipe->id); ?>"
                                           class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> Voir
                                        </a>
                                        <?php if (dietetic_can_edit_recipe($recipe)) : ?>
                                            <a href="<?php echo admin_url('dietetic/recipes/edit/' . $recipe->id); ?>"
                                               class="btn btn-default btn-sm">
                                                <i class="fa fa-pencil"></i> Modifier
                                            </a>
                                        <?php endif; ?>
                                        <?php if (is_admin() && $recipe->status == 'pending') : ?>
                                            <a href="<?php echo admin_url('dietetic/recipes/approve/' . $recipe->id); ?>"
                                               class="btn btn-success btn-sm">
                                                <i class="fa fa-check"></i> Approuver
                                            </a>
                                        <?php endif; ?>
                                        <?php if (dietetic_can_delete_recipe($recipe)) : ?>
                                            <a href="<?php echo admin_url('dietetic/recipes/delete/' . $recipe->id); ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                                                <i class="fa fa-trash"></i> Supprimer
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="no-results" style="display: none;">
                    <i class="fa fa-search"></i>
                    <h4>Aucune recette trouvée</h4>
                    <p>Essayez de modifier votre recherche</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search functionality
    var $searchInput = $('#recipe-search');
    var $recipeCards = $('.recipe-card');
    var $container = $('#recipes-container');
    var $noResults = $('#no-results');

    $searchInput.on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm === '') {
            $recipeCards.show();
            $container.show();
            $noResults.hide();
        } else {
            var visibleCount = 0;
            $recipeCards.each(function() {
                var $card = $(this);
                var cardText = $card.text().toLowerCase();

                if (cardText.indexOf(searchTerm) > -1) {
                    $card.show();
                    visibleCount++;
                } else {
                    $card.hide();
                }
            });

            if (visibleCount === 0) {
                $container.hide();
                $noResults.show();
            } else {
                $container.show();
                $noResults.hide();
            }
        }

        updateResultsCount();
    });

    function updateResultsCount() {
        var visibleCount = $recipeCards.filter(':visible').length;
        $('#visible-count').text(visibleCount);
    }

    // Fix menu toggle
    setTimeout(function() {
        $('#side-menu a[href*="dietetic"]').first().parent().find('> a').on('click', function(e) {
            var $submenu = $(this).next('ul');
            if ($submenu.length > 0) {
                e.preventDefault();
                $submenu.toggleClass('in');
                $(this).parent().toggleClass('active');
            }
        });
    }, 500);
});
</script>

<?php init_tail(); ?>
