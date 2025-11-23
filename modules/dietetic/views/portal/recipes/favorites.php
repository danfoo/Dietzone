<?php
$active_page = 'recipes';
$page_title = 'Mes Recettes Favorites';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Variables de couleurs - Charte graphique officielle */
:root {
    --primary-color: #01807B;
    --primary-dark: #015a57;
    --primary-light: #019B95;
    --secondary-color: #F3911D;
    --secondary-dark: #e07d0f;
    --secondary-light: #FFA74D;
    --tertiary-color: #FFFFFF;
    --card-bg: #FFFFFF;
    --text-primary: #1E1E1E;
    --text-secondary: #6B7280;
    --text-dark: #1a202c;
    --text-medium: #2d3748;
    --text-light: #718096;
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

/* Page Header */
.page-header-fav {
    background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-dark) 100%);
    color: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    margin-bottom: 30px;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.5s ease-out;
}

.page-header-fav::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.05) 100%);
    pointer-events: none;
}

.page-header-fav h1 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.page-header-fav h1 i {
    background: rgba(255, 255, 255, 0.2);
    padding: 10px;
    border-radius: 10px;
    font-size: 20px;
}

.page-header-fav p {
    margin: 10px 0 0 0;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.page-header-fav .btn-default {
    margin-top: 20px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    padding: 10px 20px;
    font-weight: 700;
    transition: var(--transition);
    position: relative;
    z-index: 1;
}

.page-header-fav .btn-default:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
}

/* Recipe Grid - Layout liste vertical */
.recipes-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Recipe Card - Layout horizontal */
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

.recipe-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateX(4px);
}

.recipe-card:active {
    transform: scale(0.99);
}

/* Recipe Photo - À gauche */
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

/* Recipe Content - À droite */
.recipe-content {
    padding: 12px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

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
    transition: var(--transition-fast);
}

.recipe-title a:hover {
    color: var(--primary-color);
}

.recipe-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 8px;
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

.recipe-meta-item i.fa-star {
    color: #FFC107;
}

.recipe-meta-item i.fa-heart {
    color: var(--secondary-color);
}

/* Tags */
.recipe-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}

.recipe-tag {
    background: #F3F4F6;
    color: var(--text-primary);
    padding: 3px 10px;
    border-radius: 50px;
    font-size: 10px;
    font-weight: 700;
}

/* Actions */
.recipe-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.recipe-actions .btn {
    border-radius: 50px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    transition: var(--transition);
    border: none;
}

.recipe-actions .btn-primary {
    background: var(--primary-color);
    color: white;
}

.recipe-actions .btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.recipe-actions .btn-danger {
    background: var(--secondary-color);
    color: white;
}

.recipe-actions .btn-danger:hover {
    background: var(--secondary-dark);
    transform: translateY(-2px);
}

/* Alert */
.alert {
    border-radius: var(--border-radius-md);
    border: none;
    padding: 18px;
    font-size: 15px;
    font-weight: 600;
    animation: fadeInUp 0.5s ease-out;
}

/* Responsive */
@media (min-width: 576px) {
    .recipe-photo,
    .recipe-photo-placeholder {
        width: 140px;
        min-width: 140px;
        height: 140px;
    }

    .recipe-title {
        font-size: 17px;
    }
}

@media (min-width: 992px) {
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

    .recipe-meta-item {
        font-size: 13px;
    }
}
</style>

<div class="container">
    <div class="page-header-fav">
        <h1>
            <i class="fa fa-heart"></i> Mes Recettes Favorites
        </h1>
        <p>
            Retrouvez ici toutes vos recettes préférées
        </p>
        <div>
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Retour aux recettes
            </a>
        </div>
    </div>

    <?php if (empty($favorites)) : ?>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            Vous n'avez pas encore de recettes favorites. Explorez la bibliothèque pour en ajouter !
        </div>
    <?php else : ?>
        <div class="recipes-grid">
            <?php foreach ($favorites as $recipe) : ?>
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
                                    <i class="fa fa-star"></i>
                                    <?php echo number_format($recipe->average_rating, 1); ?>
                                </div>
                            <?php endif; ?>
                            <div class="recipe-meta-item">
                                <i class="fa fa-heart"></i>
                                Ajouté le <?php echo date('d/m/Y', strtotime($recipe->added_at)); ?>
                            </div>
                        </div>

                        <?php if (!empty($recipe->tags)) : ?>
                            <div class="recipe-tags">
                                <?php foreach (array_slice($recipe->tags, 0, 3) as $tag) : ?>
                                    <span class="recipe-tag">
                                        <?php echo htmlspecialchars($tag); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="recipe-actions">
                            <a href="<?php echo site_url('dietetic/portal/recipe_view/' . $recipe->id); ?>"
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> Voir
                            </a>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-favorite"
                                    data-recipe-id="<?php echo $recipe->id; ?>">
                                <i class="fa fa-trash"></i> Retirer
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('.btn-remove-favorite').click(function() {
        if (!confirm('Retirer cette recette de vos favoris ?')) {
            return;
        }

        const btn = $(this);
        const recipeId = btn.data('recipe-id');

        $.post('<?php echo site_url('dietetic/portal/remove_from_favorites'); ?>', {
            recipe_id: recipeId
        }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
