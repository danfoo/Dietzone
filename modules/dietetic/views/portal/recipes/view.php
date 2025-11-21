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
    height: 50vh;
    min-height: 300px;
    margin: -20px -15px 20px -15px; /* Compense le padding du container pour full width */
    overflow: hidden;
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
    padding: 16px 15px;
    display: flex;
    gap: 12px;
    background: white;
    border-bottom: 1px solid #F1F3F5;
    margin: -20px -15px 20px -15px;
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
    padding: 0 15px;
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
    padding: 16px 16px 16px 60px;
    border-left: 3px solid var(--primary-color);
    margin-bottom: 12px;
    background: #F8F9FA;
    border-radius: var(--border-radius-sm);
    position: relative;
    counter-increment: step-counter;
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
}

.instructions-list li:before {
    content: counter(step-counter);
    position: absolute;
    left: 16px;
    top: 16px;
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

/* === RATING SECTION - NON FONCTIONNELLE === */
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

.rating-disabled-notice {
    background: #F8D7DA;
    border: 1px solid #F5C2C7;
    padding: 12px;
    border-radius: var(--border-radius-sm);
    color: #842029;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 16px;
}

.rating-disabled-notice i {
    margin-right: 6px;
}

.stars-rating {
    font-size: 28px;
    color: #ddd;
    text-align: center;
    margin: 12px 0;
    pointer-events: none;
    opacity: 0.5;
}

.rating-comment-disabled {
    opacity: 0.5;
    pointer-events: none;
}

.btn-submit-disabled {
    opacity: 0.5;
    pointer-events: none;
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
        height: 60vh;
        min-height: 400px;
        margin: -20px -20px 24px -20px;
    }

    .recipe-hero-title {
        font-size: 32px;
    }

    .recipe-hero-meta-item {
        font-size: 16px;
    }

    .recipe-actions {
        padding: 20px;
        margin: -20px -20px 24px -20px;
    }

    .recipe-container {
        padding: 0 20px;
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
        height: 70vh;
        min-height: 500px;
        margin: -32px -32px 28px -32px;
    }

    .recipe-hero-overlay {
        padding: 60px 40px 30px;
    }

    .recipe-hero-title {
        font-size: 42px;
    }

    .recipe-actions {
        padding: 24px 32px;
        margin: -32px -32px 28px -32px;
    }

    .recipe-container {
        padding: 0 32px;
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

    <!-- Notation et Commentaires -->
    <div class="section-card">
        <h3><i class="fa fa-star"></i> Notes et Avis</h3>

        <!-- Notice: Fonctionnalité non disponible -->
        <div class="rating-disabled-notice">
            <i class="fa fa-exclamation-triangle"></i>
            La notation et les commentaires ne sont pas disponibles pour le moment.
        </div>

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
            <div class="stars-rating" id="stars-rating">
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
            </div>
            <div class="rating-comment-disabled">
                <textarea class="form-control" placeholder="Votre commentaire (fonctionnalité bientôt disponible)" rows="3" disabled></textarea>
            </div>
            <button type="button" class="btn btn-primary btn-submit-disabled" style="margin-top: 10px;" disabled>
                <i class="fa fa-check"></i> Enregistrer ma note
            </button>
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

// Note: Les fonctionnalités de notation sont désactivées pour le moment
// Le code ci-dessous est conservé mais non fonctionnel
$(document).ready(function() {
    // Désactivé pour le moment
    // let selectedRating = 0;
    // $('#stars-rating i').click(function() { ... });
    // $('#submit-rating').click(function() { ... });
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
