<?php
$active_page = 'recipes';
$page_title = $recipe->name;
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === DESIGN MOBILE FIRST - PAGE DÉTAIL RECETTE === */

/* Variables de couleurs - Charte graphique officielle */
:root {
    --primary-color: #01807B;
    --primary-dark: #026660;
    --secondary-color: #dc3545;
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

/* === HERO IMAGE AVEC OVERLAY === */
.recipe-hero {
    position: relative;
    width: 100%;
    height: 250px;
    margin: 0 0 20px 0;
    overflow: hidden;
    border-radius: var(--border-radius-lg);
}

.recipe-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recipe-hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    color: #D1D5DB;
}

/* Overlay noir en bas */
.recipe-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.4) 70%, transparent 100%);
    padding: 40px 20px 20px;
    color: white;
}

.recipe-hero-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 16px 0;
    color: white;
    text-align: center;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

.recipe-hero-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.recipe-hero-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
}

.recipe-hero-meta-item i {
    font-size: 16px;
}

.recipe-hero-rating {
    display: flex;
    align-items: center;
    gap: 4px;
}

.recipe-hero-rating i {
    color: #FFC107;
    font-size: 16px;
}

.recipe-hero-rating-value {
    font-size: 18px;
    font-weight: 800;
}

.recipe-hero-rating-count {
    font-size: 12px;
    opacity: 0.9;
}

/* === NAVIGATION BUTTONS === */
.recipe-actions {
    padding: 0;
    display: flex;
    gap: 12px;
    margin: 0 0 20px 0;
}

.btn-back {
    flex: 1;
    padding: 12px 16px;
    border-radius: 50px;
    background: #F3F4F6;
    color: var(--text-primary);
    border: none;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-back:hover {
    background: #E5E7EB;
    text-decoration: none;
}

.btn-favorite-detail {
    flex: 1;
    padding: 12px 16px;
    border-radius: 50px;
    background: var(--secondary-color);
    color: white;
    border: none;
    font-size: 14px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-favorite-detail:hover {
    background: #FF5252;
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
}

.btn-favorite-detail.active {
    background: var(--primary-color);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

/* === CONTAINER === */
.recipe-container {
    padding: 0;
    max-width: 100%;
}

/* === SECTION CARD === */
.section-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.section-card h3 {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 16px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.3px;
}

.section-card h3 i {
    color: var(--primary-color);
    font-size: 18px;
}

/* === DESCRIPTION === */
.recipe-description {
    font-size: 15px;
    line-height: 1.7;
    color: var(--text-secondary);
}

/* === INGREDIENTS === */
.ingredients-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.ingredients-list li {
    padding: 14px 0;
    border-bottom: 1px solid #F1F3F5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

.ingredients-list li:last-child {
    border-bottom: none;
}

.ingredient-name {
    color: var(--text-primary);
    font-weight: 600;
    flex: 1;
}

.ingredient-quantity {
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    margin-left: 12px;
}

/* === NUTRITION - 2 COLONNES SUR MOBILE === */
.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.nutrition-item {
    background: #F8F9FA;
    padding: 16px;
    border-radius: var(--border-radius-md);
    text-align: center;
    border: 2px solid #F1F3F5;
}

.nutrition-value {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
    letter-spacing: -0.5px;
}

.nutrition-label {
    font-size: 12px;
    color: var(--text-secondary);
    margin-top: 6px;
    font-weight: 600;
}

/* === INSTRUCTIONS === */
.instructions-list {
    list-style: none;
    padding: 0;
    margin: 0;
    counter-reset: step-counter;
}

.instructions-list li {
    padding: 12px 12px 12px 50px;
    margin-bottom: 8px;
    position: relative;
    counter-increment: step-counter;
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
}

.instructions-list li:before {
    content: counter(step-counter);
    position: absolute;
    left: 0;
    top: 12px;
    background: var(--primary-color);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
}

/* === TAGS DISCRETS === */
.tags-wrapper {
    text-align: center;
    padding: 12px 0;
}

.tags-list {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.tag-badge {
    background: #F3F4F6;
    color: var(--text-secondary);
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: -0.1px;
}

/* === RATING SECTION === */
.rating-section {
    background: #FFF3CD;
    border: 2px solid #FFC107;
    padding: 16px;
    border-radius: var(--border-radius-md);
    margin-bottom: 16px;
}

.rating-section h4 {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 12px 0;
    color: var(--text-primary);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.interactive-stars {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin: 12px 0;
}

.interactive-stars i {
    font-size: 36px;
    color: #dee2e6;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 44px;
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.interactive-stars i:hover,
.interactive-stars i.hover {
    color: #FFC107;
    transform: scale(1.15);
}

.interactive-stars i.selected {
    color: #FFC107;
}

.interactive-stars i.fa-star {
    color: #FFC107;
}

.interactive-stars i.fa-star-o {
    color: #dee2e6;
}

.rating-item {
    padding: 16px;
    border-bottom: 1px solid #F1F3F5;
    margin-bottom: 12px;
}

.rating-item:last-child {
    border-bottom: none;
}

.rating-stars {
    color: #FFC107;
    font-size: 14px;
    margin-bottom: 8px;
}

.rating-author {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 14px;
    margin-bottom: 6px;
}

.rating-comment-text {
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 8px 0;
}

.rating-date {
    font-size: 11px;
    color: var(--text-secondary);
}

/* === TABLET (576px+) === */
@media (min-width: 576px) {
    .recipe-hero {
        height: 250px;
        margin: 0 0 24px 0;
    }

    .recipe-hero-title {
        font-size: 32px;
    }

    .recipe-hero-meta-item {
        font-size: 16px;
    }

    .recipe-actions {
        margin: 0 0 24px 0;
    }

    .recipe-container {
        padding: 0;
    }

    .section-card {
        padding: 24px;
        margin-bottom: 24px;
    }

    .nutrition-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* === DESKTOP (992px+) === */
@media (min-width: 992px) {
    .recipe-hero {
        height: 250px;
        margin: 0 0 28px 0;
    }

    .recipe-hero-overlay {
        padding: 60px 40px 30px;
    }

    .recipe-hero-title {
        font-size: 42px;
    }

    .recipe-actions {
        margin: 0 0 28px 0;
    }

    .recipe-container {
        padding: 0;
    }

    .section-card {
        padding: 28px;
        margin-bottom: 28px;
    }

    .section-card h3 {
        font-size: 20px;
    }

    .recipe-description {
        font-size: 16px;
    }

    .ingredients-list li {
        font-size: 15px;
    }

    .instructions-list li {
        font-size: 15px;
    }
}

/* === AUTEUR DE LA RECETTE === */
.recipe-author {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 16px;
}

.recipe-author-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.recipe-author-photo-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    font-weight: 800;
    border: 3px solid var(--primary-color);
}

.recipe-author-info {
    flex: 1;
}

.recipe-author-label {
    font-size: 11px;
    text-transform: uppercase;
    color: var(--text-secondary);
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.recipe-author-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

@media (min-width: 576px) {
    .recipe-author {
        padding: 24px;
    }

    .recipe-author-photo,
    .recipe-author-photo-placeholder {
        width: 70px;
        height: 70px;
    }

    .recipe-author-name {
        font-size: 18px;
    }
}
</style>

<!-- Hero Image avec Overlay -->
<div class="recipe-hero">
    <?php if (!empty($recipe->photos)) : ?>
        <?php
        $main_photo = null;
        foreach ($recipe->photos as $photo) {
            if ($photo->is_main) {
                $main_photo = $photo;
                break;
            }
        }
        if (!$main_photo && count($recipe->photos) > 0) {
            $main_photo = $recipe->photos[0];
        }
        ?>
        <?php if ($main_photo) : ?>
            <img src="<?php echo base_url($main_photo->photo_url); ?>"
                 alt="<?php echo htmlspecialchars($recipe->name); ?>"
                 class="recipe-hero-image">
        <?php else : ?>
            <div class="recipe-hero-placeholder">
                <i class="fa fa-cutlery"></i>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="recipe-hero-placeholder">
            <i class="fa fa-cutlery"></i>
        </div>
    <?php endif; ?>

    <!-- Overlay avec infos -->
    <div class="recipe-hero-overlay">
        <h1 class="recipe-hero-title"><?php echo htmlspecialchars($recipe->name); ?></h1>
        <div class="recipe-hero-meta">
            <!-- Temps de cuisson à gauche -->
            <div class="recipe-hero-meta-item">
                <?php if ($recipe->preparation_time) : ?>
                    <i class="fa fa-clock-o"></i>
                    <span><?php echo $recipe->preparation_time; ?> min</span>
                <?php else : ?>
                    <i class="fa fa-clock-o"></i>
                    <span>N/A</span>
                <?php endif; ?>
            </div>

            <!-- Note à droite -->
            <div class="recipe-hero-rating">
                <?php if ($recipe->average_rating > 0) : ?>
                    <i class="fa fa-star"></i>
                    <span class="recipe-hero-rating-value"><?php echo number_format($recipe->average_rating, 1); ?></span>
                    <span class="recipe-hero-rating-count">(<?php echo $recipe->ratings_count; ?>)</span>
                <?php else : ?>
                    <i class="fa fa-star-o"></i>
                    <span class="recipe-hero-rating-count">Pas d'avis</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Actions (Retour et Favori) -->
<div class="recipe-actions">
    <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="btn-back">
        <i class="fa fa-arrow-left"></i> Retour
    </a>
    <button type="button"
            class="btn-favorite-detail <?php echo $is_favorite ? 'active' : ''; ?>"
            onclick="toggleFavorite(<?php echo $recipe->id; ?>, this);">
        <i class="fa fa-heart"></i>
        <span><?php echo $is_favorite ? 'Favori' : 'Ajouter'; ?></span>
    </button>
</div>

<div class="recipe-container">
    <!-- Description -->
    <?php if ($recipe->description) : ?>
        <div class="section-card">
            <h3><i class="fa fa-align-left"></i> Description</h3>
            <div class="recipe-description">
                <?php echo nl2br(htmlspecialchars($recipe->description)); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ingrédients -->
    <?php if (!empty($recipe->ingredients)) : ?>
        <div class="section-card">
            <h3><i class="fa fa-list"></i> Ingrédients</h3>
            <ul class="ingredients-list">
                <?php foreach ($recipe->ingredients as $ingredient) : ?>
                    <li>
                        <span class="ingredient-name"><?php echo htmlspecialchars($ingredient->ingredient_name); ?></span>
                        <?php if ($ingredient->quantity) : ?>
                            <span class="ingredient-quantity">
                                <?php echo number_format($ingredient->quantity, 2); ?>
                                <?php echo htmlspecialchars($ingredient->unit); ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Valeurs Nutritionnelles - 2 blocs sur mobile -->
    <?php if ($recipe->nutrition) : ?>
        <div class="section-card">
            <h3><i class="fa fa-heartbeat"></i> Valeurs Nutritionnelles</h3>
            <div class="nutrition-grid">
                <?php if ($recipe->nutrition->calories) : ?>
                    <div class="nutrition-item">
                        <div class="nutrition-value"><?php echo number_format($recipe->nutrition->calories, 0); ?></div>
                        <div class="nutrition-label">Calories</div>
                    </div>
                <?php endif; ?>
                <?php if ($recipe->nutrition->protein) : ?>
                    <div class="nutrition-item">
                        <div class="nutrition-value"><?php echo number_format($recipe->nutrition->protein, 1); ?>g</div>
                        <div class="nutrition-label">Protéines</div>
                    </div>
                <?php endif; ?>
                <?php if ($recipe->nutrition->carbs) : ?>
                    <div class="nutrition-item">
                        <div class="nutrition-value"><?php echo number_format($recipe->nutrition->carbs, 1); ?>g</div>
                        <div class="nutrition-label">Glucides</div>
                    </div>
                <?php endif; ?>
                <?php if ($recipe->nutrition->fat) : ?>
                    <div class="nutrition-item">
                        <div class="nutrition-value"><?php echo number_format($recipe->nutrition->fat, 1); ?>g</div>
                        <div class="nutrition-label">Lipides</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Instructions -->
    <?php if (!empty($recipe->instructions)) : ?>
        <div class="section-card">
            <h3><i class="fa fa-tasks"></i> Instructions</h3>
            <ul class="instructions-list">
                <?php foreach ($recipe->instructions as $instruction) : ?>
                    <li><?php echo nl2br(htmlspecialchars($instruction->instruction)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Tags - Discrets -->
    <?php if (!empty($recipe->tags)) : ?>
        <div class="tags-wrapper">
            <div class="tags-list">
                <?php foreach ($recipe->tags as $tag) : ?>
                    <span class="tag-badge"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Auteur de la recette -->
    <div class="recipe-author">
        <?php
        // Récupérer la photo du diététicien
        $profile_image_url = staff_profile_image_url($recipe->dietitian_staff_id, 'small');
        $initials = '';
        if ($recipe->dietitian_name) {
            $names = explode(' ', $recipe->dietitian_name);
            $initials = strtoupper(substr($names[0], 0, 1));
            if (count($names) > 1) {
                $initials .= strtoupper(substr($names[count($names) - 1], 0, 1));
            }
        }
        ?>
        <?php if ($profile_image_url && file_exists(FCPATH . $profile_image_url)) : ?>
            <img src="<?php echo base_url($profile_image_url); ?>"
                 alt="<?php echo htmlspecialchars($recipe->dietitian_name); ?>"
                 class="recipe-author-photo">
        <?php else : ?>
            <div class="recipe-author-photo-placeholder">
                <?php echo $initials; ?>
            </div>
        <?php endif; ?>
        <div class="recipe-author-info">
            <div class="recipe-author-label">Créé par</div>
            <div class="recipe-author-name"><?php echo htmlspecialchars($recipe->dietitian_name); ?></div>
        </div>
    </div>

    <!-- Notation et Commentaires -->
    <div class="section-card">
        <h3><i class="fa fa-star"></i> Notes et Avis</h3>

        <?php if ($my_rating) : ?>
            <div class="alert alert-success">
                <strong>Votre note :</strong> <?php echo $my_rating->rating; ?> étoiles
                <?php if ($my_rating->comment) : ?>
                    <br><em><?php echo htmlspecialchars($my_rating->comment); ?></em>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="rating-section">
            <h4>Votre note</h4>
            <form id="ratingForm">
                <div class="interactive-stars" id="stars-rating">
                    <i class="fa fa-star-o" data-rating="1"></i>
                    <i class="fa fa-star-o" data-rating="2"></i>
                    <i class="fa fa-star-o" data-rating="3"></i>
                    <i class="fa fa-star-o" data-rating="4"></i>
                    <i class="fa fa-star-o" data-rating="5"></i>
                </div>
                <textarea class="form-control" id="rating-comment" name="comment" placeholder="Votre commentaire (optionnel)" style="margin-top: 15px;" rows="3"><?php echo $my_rating && $my_rating->comment ? htmlspecialchars($my_rating->comment) : ''; ?></textarea>
                <button type="submit" class="btn btn-primary" id="submit-rating" style="margin-top: 10px; width: 100%;" disabled>
                    <i class="fa fa-check"></i> Enregistrer ma note
                </button>
            </form>
        </div>

        <!-- Affichage des avis existants -->
        <?php if (!empty($ratings)) : ?>
            <h4 style="margin-top: 24px; font-size: 16px; font-weight: 700;">Tous les avis (<?php echo count($ratings); ?>)</h4>
            <?php foreach ($ratings as $rating) : ?>
                <div class="rating-item">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <i class="fa fa-star<?php echo $i <= $rating->rating ? '' : '-o'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <div class="rating-author"><?php echo htmlspecialchars($rating->patient_name); ?></div>
                    <?php if ($rating->comment) : ?>
                        <div class="rating-comment-text"><?php echo nl2br(htmlspecialchars($rating->comment)); ?></div>
                    <?php endif; ?>
                    <div class="rating-date"><?php echo date('d/m/Y', strtotime($rating->created_at)); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Fonction pour gérer les favoris
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
                const span = btn.find('span');
                span.text(btn.hasClass('active') ? 'Favori' : 'Ajouter');
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

// Rating functionality - Interactive stars system
$(document).ready(function() {
    var selectedRating = <?php echo $my_rating ? $my_rating->rating : 0; ?>;

    console.log('=== RATING SYSTEM DEBUG ===');
    console.log('jQuery loaded:', typeof jQuery !== 'undefined');
    console.log('Initial rating:', selectedRating);
    console.log('Star group element:', $('#stars-rating').length);
    console.log('Stars count:', $('#stars-rating i').length);
    console.log('Submit button:', $('#submit-rating').length);
    console.log('Form:', $('#ratingForm').length);

    // Helper functions
    function highlightStars(rating) {
        console.log('highlightStars called with:', rating);
        $('#stars-rating i').each(function(index) {
            if (index < rating) {
                $(this).removeClass('fa-star-o').addClass('fa-star');
            } else {
                $(this).removeClass('fa-star').addClass('fa-star-o');
            }
        });
    }

    function markSelected(rating) {
        console.log('markSelected called with:', rating);
        $('#stars-rating i').each(function(index) {
            if (index < rating) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
        highlightStars(rating);
    }

    // Initialize current rating
    if (selectedRating > 0) {
        console.log('Initializing with existing rating:', selectedRating);
        highlightStars(selectedRating);
        markSelected(selectedRating);
        $('#submit-rating').prop('disabled', false);
    }

    // Star hover effect
    $('#stars-rating i').on('mouseenter', function() {
        var rating = parseInt($(this).attr('data-rating'));
        console.log('Mouse enter star:', rating);
        highlightStars(rating);
    });

    // Star click/touch to select
    $('#stars-rating i').on('click', function() {
        var rating = parseInt($(this).attr('data-rating'));
        console.log('★ STAR CLICKED! Rating:', rating);
        selectedRating = rating;
        markSelected(rating);
        $('#submit-rating').prop('disabled', false);
        console.log('Button enabled, selectedRating:', selectedRating);

        // Haptic feedback on mobile
        if ('vibrate' in navigator) {
            navigator.vibrate(10);
        }
    });

    // Reset hover effect
    $('#stars-rating').on('mouseleave', function() {
        console.log('Mouse leave, reset to:', selectedRating);
        highlightStars(selectedRating);
    });

    // Form submission
    $('#ratingForm').on('submit', function(e) {
        e.preventDefault();
        console.log('=== FORM SUBMITTED ===');
        console.log('Selected rating:', selectedRating);

        if (selectedRating === 0) {
            console.log('No rating selected, showing alert');
            if (window.alert_float) {
                alert_float('warning', 'Veuillez sélectionner une note');
            } else {
                alert('Veuillez sélectionner une note');
            }
            return false;
        }

        var comment = $('#rating-comment').val();
        var $submitBtn = $('#submit-rating');
        var originalBtnText = $submitBtn.html();

        console.log('Comment:', comment);
        console.log('Sending AJAX request...');

        // Disable button during submission
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Envoi en cours...');

        $.ajax({
            url: '<?php echo site_url('dietetic/portal/recipe_rate'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                recipe_id: <?php echo $recipe->id; ?>,
                rating: selectedRating,
                comment: comment,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(data) {
                console.log('AJAX Success! Response:', data);
                if (data.success) {
                    if (window.alert_float) {
                        alert_float('success', data.message || 'Merci pour votre avis !');
                    } else {
                        alert(data.message || 'Merci pour votre avis !');
                    }
                    // Reload after 1.5 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    console.error('Rating failed:', data.message);
                    if (window.alert_float) {
                        alert_float('danger', data.message || 'Erreur lors de l\'enregistrement');
                    } else {
                        alert(data.message || 'Erreur lors de l\'enregistrement');
                    }
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error!');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                if (window.alert_float) {
                    alert_float('danger', 'Erreur de connexion. Veuillez réessayer.');
                } else {
                    alert('Erreur de connexion. Veuillez réessayer.');
                }
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalBtnText);
            }
        });

        return false;
    });

    console.log('=== RATING SYSTEM READY ===');
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
