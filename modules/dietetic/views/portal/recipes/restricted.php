<?php
$active_page = 'recipes';
$page_title = 'Recette non disponible';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === DESIGN MOBILE FIRST - PAGE RESTRICTION RECETTE === */

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

.container {
    padding: 15px;
    max-width: 100%;
    padding-bottom: 80px;
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
    filter: blur(4px) brightness(0.7);
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
    filter: blur(4px) brightness(0.7);
}

/* Overlay avec icône de verrouillage */
.recipe-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: white;
    padding: 20px;
}

.lock-icon {
    font-size: 64px;
    color: white;
    margin-bottom: 16px;
    opacity: 0.9;
}

.recipe-hero-title {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: white;
    text-align: center;
    line-height: 1.3;
}

/* === CARTE DE MESSAGE === */
.restriction-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-md);
    padding: 32px 24px;
    margin: 0 0 20px 0;
    box-shadow: var(--shadow-md);
    text-align: center;
}

.restriction-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #FFF4E6 0%, #FFE8CC 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}

.restriction-icon i {
    font-size: 40px;
    color: #F59E0B;
}

.restriction-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 12px 0;
    line-height: 1.3;
}

.restriction-message {
    font-size: 16px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0 0 24px 0;
}

.restriction-info {
    background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%);
    border-left: 4px solid var(--primary-color);
    padding: 16px 20px;
    border-radius: var(--border-radius-sm);
    margin: 24px 0;
    text-align: left;
}

.restriction-info h4 {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-dark);
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.restriction-info p {
    font-size: 14px;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.5;
}

/* === BOUTONS D'ACTION === */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 24px;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 14px 24px;
    border-radius: var(--border-radius-sm);
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary:hover {
    background: var(--primary-dark);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: white;
    color: var(--primary-color);
    padding: 14px 24px;
    border-radius: var(--border-radius-sm);
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 2px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--background);
    color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

/* === RESPONSIVE === */
@media (min-width: 576px) {
    .container {
        padding: 20px;
    }

    .recipe-hero {
        height: 300px;
    }

    .lock-icon {
        font-size: 80px;
    }

    .recipe-hero-title {
        font-size: 26px;
    }

    .restriction-title {
        font-size: 28px;
    }

    .action-buttons {
        flex-direction: row;
        justify-content: center;
    }

    .btn-primary,
    .btn-secondary {
        width: auto;
        min-width: 200px;
    }
}

@media (min-width: 992px) {
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .recipe-hero {
        height: 350px;
    }
}
</style>

<div class="container">
    <!-- Hero Image avec overlay verrouillé -->
    <div class="recipe-hero">
        <?php if (!empty($recipe->photos) && !empty($recipe->photos[0]->photo_url)): ?>
            <img src="<?php echo base_url($recipe->photos[0]->photo_url); ?>" alt="<?php echo htmlspecialchars($recipe->name); ?>" class="recipe-hero-image">
        <?php else: ?>
            <div class="recipe-hero-placeholder">
                <i class="fa fa-cutlery"></i>
            </div>
        <?php endif; ?>

        <div class="recipe-hero-overlay">
            <div class="lock-icon">
                <i class="fa fa-lock"></i>
            </div>
            <h1 class="recipe-hero-title"><?php echo htmlspecialchars($recipe->name); ?></h1>
        </div>
    </div>

    <!-- Carte de message de restriction -->
    <div class="restriction-card">
        <div class="restriction-icon">
            <i class="fa fa-info-circle"></i>
        </div>

        <h2 class="restriction-title">Recette disponible sur recommandation</h2>

        <p class="restriction-message">
            Cette recette n'est pas encore disponible dans votre programme alimentaire actuel.
            Elle est accessible uniquement sur recommandation de votre diététicien.
        </p>

        <div class="restriction-info">
            <h4>
                <i class="fa fa-lightbulb-o"></i>
                Comment accéder à cette recette ?
            </h4>
            <p>
                Contactez votre diététicien pour discuter de l'ajout de cette recette à votre plan alimentaire personnalisé.
                Votre diététicien pourra évaluer si cette recette correspond à vos objectifs nutritionnels.
            </p>
        </div>

        <div class="action-buttons">
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="btn-primary">
                <i class="fa fa-cutlery"></i>
                Voir mes recettes
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="btn-secondary">
                <i class="fa fa-user-md"></i>
                Contacter mon diététicien
            </a>
        </div>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
