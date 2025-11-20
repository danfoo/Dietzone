<?php
$active_page = 'recipes';
$page_title = $recipe->name;
$this->load->view('portal/includes/portal_header');
?>

<style>
.recipe-detail-header {
    background: linear-gradient(135deg, #01807B 0%, #016660 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
}

.recipe-detail-header h1 {
    margin: 0 0 15px 0;
    font-size: 32px;
    font-weight: 700;
}

.recipe-detail-meta {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.recipe-detail-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
}

.recipe-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.recipe-gallery img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
}

.recipe-main-image {
    grid-column: 1 / 3;
    grid-row: 1 / 3;
}

.recipe-main-image img {
    height: 415px;
}

.section-card {
    background: white;
    border-radius: 12px;
    border: 2px solid #f1f3f5;
    padding: 24px;
    margin-bottom: 24px;
}

.section-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-card h3 i {
    color: #01807B;
}

.ingredients-list {
    list-style: none;
    padding: 0;
}

.ingredients-list li {
    padding: 12px;
    border-bottom: 1px solid #f1f3f5;
    display: flex;
    justify-content: space-between;
}

.ingredients-list li:last-child {
    border-bottom: none;
}

.instructions-list {
    list-style: none;
    padding: 0;
    counter-reset: step-counter;
}

.instructions-list li {
    padding: 20px;
    padding-left: 70px;
    border-left: 4px solid #01807B;
    margin-bottom: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    position: relative;
    counter-increment: step-counter;
}

.instructions-list li:before {
    content: counter(step-counter);
    position: absolute;
    left: 20px;
    top: 20px;
    background: #01807B;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}

.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
}

.nutrition-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    border: 2px solid #e9ecef;
}

.nutrition-item .value {
    font-size: 28px;
    font-weight: bold;
    color: #01807B;
}

.nutrition-item .label {
    font-size: 13px;
    color: #6c757d;
    margin-top: 8px;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-badge {
    background: #01807B;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.rating-section {
    background: #fff3cd;
    border: 2px solid #ffc107;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.stars-rating {
    font-size: 32px;
    color: #ddd;
    cursor: pointer;
}

.stars-rating i {
    transition: color 0.2s;
}

.stars-rating i.active,
.stars-rating i:hover,
.stars-rating i:hover ~ i {
    color: #ffc107;
}

.rating-item {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}

.rating-stars {
    color: #ffc107;
}
</style>

<div class="container">
    <div class="recipe-detail-header">
        <h1><?php echo htmlspecialchars($recipe->name); ?></h1>

        <?php if ($recipe->description) : ?>
            <p style="font-size: 16px; opacity: 0.95; margin: 0;">
                <?php echo nl2br(htmlspecialchars($recipe->description)); ?>
            </p>
        <?php endif; ?>

        <div class="recipe-detail-meta">
            <?php if ($recipe->category) : ?>
                <div class="recipe-detail-meta-item">
                    <i class="fa fa-tag"></i>
                    <span>
                        <?php
                        $categories = [
                            'breakfast' => 'Petit-déjeuner',
                            'lunch' => 'Déjeuner',
                            'dinner' => 'Dîner',
                            'snack' => 'Collation'
                        ];
                        echo $categories[$recipe->category] ?? $recipe->category;
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($recipe->preparation_time) : ?>
                <div class="recipe-detail-meta-item">
                    <i class="fa fa-clock-o"></i>
                    <span><?php echo $recipe->preparation_time; ?> minutes</span>
                </div>
            <?php endif; ?>
            <?php if ($recipe->average_rating > 0) : ?>
                <div class="recipe-detail-meta-item">
                    <i class="fa fa-star" style="color: #ffc107;"></i>
                    <span><?php echo number_format($recipe->average_rating, 1); ?> / 5 (<?php echo $recipe->ratings_count; ?> avis)</span>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 25px;">
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Retour aux recettes
            </a>
            <button type="button" class="btn btn-danger btn-favorite <?php echo $is_favorite ? 'active' : ''; ?>"
                    data-recipe-id="<?php echo $recipe->id; ?>">
                <i class="fa fa-heart"></i> <?php echo $is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>
            </button>
        </div>
    </div>

    <?php if (!empty($recipe->photos)) : ?>
        <div class="recipe-gallery">
            <?php foreach ($recipe->photos as $index => $photo) : ?>
                <div class="<?php echo $photo->is_main ? 'recipe-main-image' : ''; ?>">
                    <img src="<?php echo base_url($photo->photo_url); ?>"
                         alt="<?php echo htmlspecialchars($recipe->name); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="section-card">
                <h3><i class="fa fa-list"></i> Ingrédients</h3>
                <ul class="ingredients-list">
                    <?php foreach ($recipe->ingredients as $ingredient) : ?>
                        <li>
                            <span><?php echo htmlspecialchars($ingredient->ingredient_name); ?></span>
                            <?php if ($ingredient->quantity) : ?>
                                <span class="text-muted">
                                    <?php echo number_format($ingredient->quantity, 2); ?>
                                    <?php echo htmlspecialchars($ingredient->unit); ?>
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if ($recipe->nutrition) : ?>
            <div class="col-md-6">
                <div class="section-card">
                    <h3><i class="fa fa-heartbeat"></i> Valeurs Nutritionnelles</h3>
                    <div class="nutrition-grid">
                        <?php if ($recipe->nutrition->calories) : ?>
                            <div class="nutrition-item">
                                <div class="value"><?php echo number_format($recipe->nutrition->calories, 0); ?></div>
                                <div class="label">Calories</div>
                            </div>
                        <?php endif; ?>
                        <?php if ($recipe->nutrition->protein) : ?>
                            <div class="nutrition-item">
                                <div class="value"><?php echo number_format($recipe->nutrition->protein, 1); ?>g</div>
                                <div class="label">Protéines</div>
                            </div>
                        <?php endif; ?>
                        <?php if ($recipe->nutrition->carbs) : ?>
                            <div class="nutrition-item">
                                <div class="value"><?php echo number_format($recipe->nutrition->carbs, 1); ?>g</div>
                                <div class="label">Glucides</div>
                            </div>
                        <?php endif; ?>
                        <?php if ($recipe->nutrition->fat) : ?>
                            <div class="nutrition-item">
                                <div class="value"><?php echo number_format($recipe->nutrition->fat, 1); ?>g</div>
                                <div class="label">Lipides</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-12">
            <div class="section-card">
                <h3><i class="fa fa-tasks"></i> Instructions</h3>
                <ul class="instructions-list">
                    <?php foreach ($recipe->instructions as $instruction) : ?>
                        <li><?php echo nl2br(htmlspecialchars($instruction->instruction)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php if (!empty($recipe->tags)) : ?>
            <div class="col-md-12">
                <div class="section-card">
                    <h3><i class="fa fa-tags"></i> Tags</h3>
                    <div class="tags-list">
                        <?php foreach ($recipe->tags as $tag) : ?>
                            <span class="tag-badge"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-12">
            <div class="section-card">
                <h3><i class="fa fa-star"></i> Noter cette recette</h3>

                <?php if ($my_rating) : ?>
                    <div class="alert alert-success">
                        <strong>Vous avez déjà noté cette recette :</strong> <?php echo $my_rating->rating; ?> étoiles
                        <?php if ($my_rating->comment) : ?>
                            <br><em><?php echo htmlspecialchars($my_rating->comment); ?></em>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="rating-section">
                    <h4>Votre note</h4>
                    <div class="stars-rating" id="stars-rating">
                        <i class="fa fa-star" data-rating="1"></i>
                        <i class="fa fa-star" data-rating="2"></i>
                        <i class="fa fa-star" data-rating="3"></i>
                        <i class="fa fa-star" data-rating="4"></i>
                        <i class="fa fa-star" data-rating="5"></i>
                    </div>
                    <textarea class="form-control" id="rating-comment" placeholder="Votre commentaire (optionnel)" style="margin-top: 15px;" rows="3"></textarea>
                    <button type="button" class="btn btn-primary" id="submit-rating" style="margin-top: 10px;">
                        <i class="fa fa-check"></i> Enregistrer ma note
                    </button>
                </div>

                <?php if (!empty($ratings)) : ?>
                    <h4 style="margin-top: 30px;">Tous les avis (<?php echo count($ratings); ?>)</h4>
                    <?php foreach ($ratings as $rating) : ?>
                        <div class="rating-item">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <i class="fa fa-star<?php echo $i <= $rating->rating ? '' : '-o'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p><strong><?php echo htmlspecialchars($rating->patient_name); ?></strong></p>
                            <?php if ($rating->comment) : ?>
                                <p><?php echo nl2br(htmlspecialchars($rating->comment)); ?></p>
                            <?php endif; ?>
                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($rating->created_at)); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedRating = <?php echo $my_rating ? $my_rating->rating : 0; ?>;

    // Initialize stars if already rated
    if (selectedRating > 0) {
        updateStars(selectedRating);
    }

    // Star rating
    $('#stars-rating i').click(function() {
        selectedRating = $(this).data('rating');
        updateStars(selectedRating);
    });

    function updateStars(rating) {
        $('#stars-rating i').each(function() {
            if ($(this).data('rating') <= rating) {
                $(this).removeClass('fa-star-o').addClass('fa-star active');
            } else {
                $(this).removeClass('fa-star active').addClass('fa-star-o');
            }
        });
    }

    // Submit rating
    $('#submit-rating').click(function() {
        if (selectedRating === 0) {
            alert('Veuillez sélectionner une note');
            return;
        }

        const comment = $('#rating-comment').val();

        $.post('<?php echo site_url('dietetic/portal/recipe_rate'); ?>', {
            recipe_id: <?php echo $recipe->id; ?>,
            rating: selectedRating,
            comment: comment
        }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                alert('Merci pour votre avis !');
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    // Toggle favorite
    $('.btn-favorite').click(function() {
        const btn = $(this);
        const recipeId = btn.data('recipe-id');
        const isFavorite = btn.hasClass('active');

        const action = isFavorite ? 'remove_from_favorites' : 'add_to_favorites';

        $.post('<?php echo site_url('dietetic/portal/'); ?>' + action, {
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
