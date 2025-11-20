<?php
$active_page = 'recipes';
$page_title = 'Mes Recettes Favorites';
$this->load->view('portal/includes/portal_header');
?>

<style>
.recipes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.recipe-card {
    background: white;
    border-radius: 12px;
    border: 2px solid #f1f3f5;
    margin-bottom: 20px;
    overflow: hidden;
    transition: all 0.3s;
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
}

.recipe-title a:hover {
    color: #01807B;
}

.recipe-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
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

.recipe-actions {
    display: flex;
    gap: 10px;
}
</style>

<div class="container">
    <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 28px; font-weight: 700;">
            <i class="fa fa-heart"></i> Mes Recettes Favorites
        </h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">
            Retrouvez ici toutes vos recettes préférées
        </p>
        <div style="margin-top: 20px;">
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
                                    <i class="fa fa-star" style="color: #f39c12;"></i>
                                    <?php echo number_format($recipe->average_rating, 1); ?>
                                </div>
                            <?php endif; ?>
                            <div class="recipe-meta-item">
                                <i class="fa fa-heart" style="color: #dc3545;"></i>
                                Ajouté le <?php echo date('d/m/Y', strtotime($recipe->added_at)); ?>
                            </div>
                        </div>

                        <?php if (!empty($recipe->tags)) : ?>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px;">
                                <?php foreach (array_slice($recipe->tags, 0, 3) as $tag) : ?>
                                    <span style="background: #e9ecef; color: #495057; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
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
