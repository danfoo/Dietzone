<?php
/**
 * Portal Upgrade Page - Freemium Model
 * Shows comparison between Free and Premium tiers
 */

$active_page = 'upgrade';
$page_title = 'Passer à Premium';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* ============================================
   MODERN UPGRADE PAGE - DIETZONE BRANDING
   ============================================ */

:root {
    --dietzone-primary: #01807B;
    --dietzone-dark: #01655f;
    --dietzone-light: #019690;
    --gold: #FFD700;
    --gold-dark: #FFA500;
}

.upgrade-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px 0;
}

/* Hero Section */
.upgrade-hero {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, var(--dietzone-primary) 0%, var(--dietzone-dark) 100%);
    color: white;
    border-radius: 20px;
    margin-bottom: 40px;
    box-shadow: 0 10px 30px rgba(1, 128, 123, 0.3);
    position: relative;
    overflow: hidden;
}

.upgrade-hero::before {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    top: -150px;
    right: -100px;
}

.upgrade-hero h1 {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 15px 0;
    position: relative;
    z-index: 1;
}

.upgrade-hero .subtitle {
    font-size: 18px;
    opacity: 0.95;
    margin: 0;
    position: relative;
    z-index: 1;
}

.upgrade-hero .crown-icon {
    font-size: 64px;
    margin-bottom: 20px;
    display: inline-block;
    animation: floatCrown 3s ease-in-out infinite;
}

@keyframes floatCrown {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

/* Pricing Cards */
.pricing-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
    margin-bottom: 50px;
}

@media (min-width: 768px) {
    .pricing-cards {
        grid-template-columns: 1fr 1fr;
    }
}

.pricing-card {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.pricing-card.premium {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: #000;
    border: 3px solid var(--gold-dark);
}

.pricing-card.premium::before {
    content: 'RECOMMANDÉ';
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.2);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
}

.pricing-card .card-header {
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.pricing-card .plan-name {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.pricing-card .plan-icon {
    font-size: 32px;
}

.pricing-card .plan-price {
    font-size: 16px;
    opacity: 0.8;
    margin: 0;
}

.pricing-card .features-list {
    list-style: none;
    padding: 0;
    margin: 0 0 30px 0;
}

.pricing-card .features-list li {
    padding: 12px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    line-height: 1.5;
}

.pricing-card .features-list li i {
    font-size: 18px;
    flex-shrink: 0;
}

.pricing-card .features-list li.available i {
    color: #10b981;
}

.pricing-card .features-list li.limited i {
    color: #f59e0b;
}

.pricing-card .features-list li.unavailable {
    opacity: 0.5;
}

.pricing-card .features-list li.unavailable i {
    color: #ef4444;
}

.pricing-card .cta-button {
    display: block;
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.pricing-card.free .cta-button {
    background: #e5e7eb;
    color: #6b7280;
}

.pricing-card.premium .cta-button {
    background: rgba(0, 0, 0, 0.2);
    color: white;
}

.pricing-card.premium .cta-button:hover {
    background: rgba(0, 0, 0, 0.3);
    transform: scale(1.02);
}

/* Current Plan Badge */
.current-plan-badge {
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--dietzone-primary);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.4);
}

/* Comparison Table */
.comparison-table-wrapper {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 50px;
}

.comparison-table-wrapper h2 {
    text-align: center;
    font-size: 28px;
    margin: 0 0 30px 0;
    color: #333;
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
}

.comparison-table thead {
    background: linear-gradient(135deg, var(--dietzone-primary) 0%, var(--dietzone-dark) 100%);
    color: white;
}

.comparison-table th {
    padding: 20px 15px;
    font-size: 18px;
    font-weight: 600;
}

.comparison-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.comparison-table tbody tr:hover {
    background: #f9fafb;
}

.comparison-table td {
    padding: 20px 15px;
    text-align: center;
}

.comparison-table td:first-child {
    text-align: left;
    font-weight: 500;
}

.comparison-table .feature-icon {
    margin-right: 10px;
    color: var(--dietzone-primary);
}

.comparison-table .check-icon {
    color: #10b981;
    font-size: 20px;
}

.comparison-table .cross-icon {
    color: #ef4444;
    font-size: 20px;
}

/* FAQ Section */
.faq-section {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.faq-section h2 {
    text-align: center;
    font-size: 28px;
    margin: 0 0 30px 0;
    color: #333;
}

.faq-item {
    margin-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 20px;
}

.faq-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.faq-question {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.faq-question i {
    color: var(--dietzone-primary);
}

.faq-answer {
    font-size: 15px;
    color: #666;
    line-height: 1.7;
    margin: 0;
}

/* Mobile Responsiveness */
@media (max-width: 767px) {
    .upgrade-hero h1 {
        font-size: 24px;
    }

    .upgrade-hero .subtitle {
        font-size: 16px;
    }

    .pricing-card {
        padding: 30px 20px;
    }

    .comparison-table {
        font-size: 14px;
    }

    .comparison-table th,
    .comparison-table td {
        padding: 15px 10px;
    }
}
</style>

<div class="upgrade-page-wrapper">
    <div class="container" style="max-width: 1100px;">

        <!-- Hero Section -->
        <div class="upgrade-hero">
            <div class="crown-icon">
                <i class="fa fa-crown"></i>
            </div>
            <h1>Passez à Dietzone Premium</h1>
            <p class="subtitle">
                Débloquez toutes les fonctionnalités pour un suivi nutritionnel complet
            </p>
        </div>

        <!-- Pricing Cards -->
        <div class="pricing-cards">
            <!-- Free Plan -->
            <div class="pricing-card free">
                <?php if (!$is_premium): ?>
                    <div class="current-plan-badge">Votre plan actuel</div>
                <?php endif; ?>

                <div class="card-header">
                    <h3 class="plan-name">
                        <span class="plan-icon"><i class="fa fa-user"></i></span>
                        Gratuit
                    </h3>
                    <p class="plan-price">Programme expiré ou sans programme actif</p>
                </div>

                <ul class="features-list">
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span>Historique complet (pesées, repas, photos)</span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span>Gestion du profil</span>
                    </li>
                    <li class="limited">
                        <i class="fa fa-exclamation-circle"></i>
                        <span>1 pesée par semaine</span>
                    </li>
                    <li class="limited">
                        <i class="fa fa-exclamation-circle"></i>
                        <span>Journal alimentaire basique</span>
                    </li>
                    <li class="limited">
                        <i class="fa fa-exclamation-circle"></i>
                        <span>Consultation photos uniquement</span>
                    </li>
                    <li class="unavailable">
                        <i class="fa fa-times-circle"></i>
                        <span>Messagerie diététicien</span>
                    </li>
                    <li class="unavailable">
                        <i class="fa fa-times-circle"></i>
                        <span>Plans de repas</span>
                    </li>
                    <li class="unavailable">
                        <i class="fa fa-times-circle"></i>
                        <span>Rappels automatiques</span>
                    </li>
                </ul>

                <?php if (!$is_premium): ?>
                    <button class="cta-button" disabled>Votre plan actuel</button>
                <?php endif; ?>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card premium">
                <?php if ($is_premium): ?>
                    <div class="current-plan-badge">Votre plan actuel</div>
                <?php endif; ?>

                <div class="card-header">
                    <h3 class="plan-name">
                        <span class="plan-icon"><i class="fa fa-crown"></i></span>
                        Premium
                    </h3>
                    <p class="plan-price">Programme actif requis</p>
                </div>

                <ul class="features-list">
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Tout du plan Gratuit +</strong></span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span>Pesées <strong>illimitées</strong></span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span>Journal alimentaire avec <strong>analyse nutritionnelle</strong></span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span>Ajout de photos <strong>illimité</strong></span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Messagerie</strong> avec votre diététicien</span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Plans de repas</strong> personnalisés</span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Rappels automatiques</strong> (repas, hydratation, pesée)</span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Prise de rendez-vous</strong> en ligne</span>
                    </li>
                    <li class="available">
                        <i class="fa fa-check-circle"></i>
                        <span><strong>Bibliothèque complète</strong> de recettes</span>
                    </li>
                </ul>

                <?php if ($is_premium): ?>
                    <button class="cta-button" disabled>Votre plan actuel</button>
                <?php else: ?>
                    <a href="<?php echo site_url('dietetic/portal'); ?>" class="cta-button">
                        <i class="fa fa-arrow-left"></i> Retour au portail
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="comparison-table-wrapper">
            <h2>Comparaison Détaillée</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Fonctionnalité</th>
                        <th>Gratuit</th>
                        <th>Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($features as $key => $feature): ?>
                    <tr>
                        <td>
                            <i class="fa <?php echo $feature['icon']; ?> feature-icon"></i>
                            <strong><?php echo $feature['name']; ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo $feature['description']; ?></small>
                        </td>
                        <td>
                            <?php if ($feature['free'] === true): ?>
                                <i class="fa fa-check-circle check-icon"></i>
                            <?php elseif ($feature['free'] === false): ?>
                                <i class="fa fa-times-circle cross-icon"></i>
                            <?php else: ?>
                                <span style="color: #f59e0b; font-weight: 500;"><?php echo $feature['free']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($feature['premium'] === true): ?>
                                <i class="fa fa-check-circle check-icon"></i>
                            <?php elseif ($feature['premium'] === false): ?>
                                <i class="fa fa-times-circle cross-icon"></i>
                            <?php else: ?>
                                <span style="color: #10b981; font-weight: 500;"><?php echo $feature['premium']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>Questions Fréquentes</h2>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fa fa-question-circle"></i>
                    Comment passer à Premium ?
                </div>
                <p class="faq-answer">
                    Contactez votre diététicien pour souscrire à un nouveau programme nutritionnel.
                    Une fois votre programme actif, vous aurez automatiquement accès à toutes les fonctionnalités Premium.
                </p>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fa fa-question-circle"></i>
                    Que se passe-t-il si mon programme expire ?
                </div>
                <p class="faq-answer">
                    Vous passez automatiquement au plan Gratuit. Vous gardez l'accès à tout votre historique (pesées, repas, photos),
                    mais les fonctionnalités Premium (messagerie, plans de repas, rappels, etc.) sont désactivées.
                    Vous pouvez les réactiver en renouvelant votre programme.
                </p>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fa fa-question-circle"></i>
                    Mes données sont-elles conservées ?
                </div>
                <p class="faq-answer">
                    Absolument ! Toutes vos données (historique des pesées, repas enregistrés, photos de progression, badges gagnés)
                    sont conservées à vie et restent accessibles même avec un plan Gratuit. Vos données vous appartiennent.
                </p>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <i class="fa fa-question-circle"></i>
                    Puis-je passer de Gratuit à Premium quand je veux ?
                </div>
                <p class="faq-answer">
                    Oui ! Il suffit de souscrire à un programme avec votre diététicien. L'activation est instantanée
                    et vous retrouvez immédiatement l'accès à toutes les fonctionnalités Premium.
                </p>
            </div>
        </div>

        <!-- Contact CTA -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo site_url('dietetic/portal'); ?>"
               class="btn btn-lg"
               style="
                   background: linear-gradient(135deg, var(--dietzone-primary) 0%, var(--dietzone-dark) 100%);
                   color: white;
                   padding: 15px 40px;
                   border-radius: 30px;
                   font-size: 18px;
                   font-weight: 600;
                   text-decoration: none;
                   display: inline-flex;
                   align-items: center;
                   gap: 10px;
                   box-shadow: 0 4px 15px rgba(1, 128, 123, 0.3);
                   transition: transform 0.2s;
               "
               onmouseover="this.style.transform='translateY(-2px)'"
               onmouseout="this.style.transform='translateY(0)'">
                <i class="fa fa-arrow-left"></i>
                Retour au Portail
            </a>
        </div>

    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
