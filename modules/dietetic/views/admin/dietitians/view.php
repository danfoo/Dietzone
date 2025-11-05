<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* ===== Page Container ===== */
.dietitian-profile-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 30px 0;
}

/* ===== Hero Header ===== */
.profile-hero {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 50px;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    overflow: hidden;
}

.profile-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.hero-content {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 40px;
    align-items: center;
}

.profile-avatar-large {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    border: 6px solid white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    position: relative;
}

.profile-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder-large {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    font-weight: 700;
    color: #8b6914;
}

.profile-info-main {
    flex: 1;
}

.profile-name {
    font-size: 42px;
    font-weight: 700;
    margin: 0 0 15px 0;
    line-height: 1.2;
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    opacity: 0.95;
}

.meta-item i {
    font-size: 20px;
}

.profile-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.badge-custom {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.badge-top {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #8b6914;
}

.badge-verified {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
}

.rating-hero-box {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    min-width: 200px;
}

.rating-hero-number {
    font-size: 72px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 10px;
    text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.rating-hero-stars {
    font-size: 24px;
    margin-bottom: 10px;
}

.rating-hero-count {
    font-size: 14px;
    opacity: 0.9;
}

/* ===== Action Bar ===== */
.action-bar {
    background: white;
    border-radius: 16px;
    padding: 20px 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 15px;
}

.btn-action {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-outline {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

/* ===== Main Grid Layout ===== */
.profile-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

/* ===== Stats Cards ===== */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-modern {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    opacity: 0.05;
    font-family: 'FontAwesome';
    font-size: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-card-modern.patients::before { content: '\f0c0'; }
.stat-card-modern.consultations::before { content: '\f073'; }
.stat-card-modern.programs::before { content: '\f0cb'; }

.stat-icon-modern {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    margin-bottom: 15px;
}

.stat-card-modern.patients .stat-icon-modern {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.stat-card-modern.consultations .stat-icon-modern {
    background: linear-gradient(135deg, #f093fb, #f5576c);
}

.stat-card-modern.programs .stat-icon-modern {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
}

.stat-number-modern {
    font-size: 36px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 8px;
}

.stat-label-modern {
    font-size: 14px;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-sublabel {
    font-size: 13px;
    color: #95a5a6;
}

.stat-sublabel strong {
    color: #667eea;
    font-weight: 700;
}

/* ===== Criteria Section ===== */
.criteria-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.card-header-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f1f3f5;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.card-title {
    font-size: 22px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.criterion-modern {
    margin-bottom: 25px;
}

.criterion-modern:last-child {
    margin-bottom: 0;
}

.criterion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.criterion-name {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
}

.criterion-score {
    display: flex;
    align-items: center;
    gap: 8px;
}

.criterion-stars-mini {
    color: #f39c12;
    font-size: 14px;
}

.criterion-number {
    font-size: 18px;
    font-weight: 700;
    color: #667eea;
}

.progress-bar-modern {
    height: 12px;
    background: #ecf0f1;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}

.progress-fill-modern {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 6px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.progress-fill-modern::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* ===== Contact Card ===== */
.contact-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.contact-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.contact-info {
    flex: 1;
}

.contact-label {
    font-size: 12px;
    color: #95a5a6;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.contact-value {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 600;
}

/* ===== Performance Card ===== */
.performance-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 30px;
}

.performance-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.performance-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.performance-label {
    font-size: 14px;
    opacity: 0.9;
}

.performance-value {
    font-size: 24px;
    font-weight: 700;
}

/* ===== Reviews Section ===== */
.reviews-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.review-modern {
    background: #f8f9fa;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 20px;
    position: relative;
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.review-modern:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.review-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.reviewer-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}

.reviewer-details {
    flex: 1;
}

.reviewer-name {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 4px 0;
}

.review-date-small {
    font-size: 13px;
    color: #95a5a6;
}

.review-rating-box {
    text-align: right;
}

.review-score-large {
    font-size: 32px;
    font-weight: 700;
    color: #f39c12;
    line-height: 1;
    margin-bottom: 5px;
}

.review-stars-modern {
    color: #f39c12;
    font-size: 16px;
}

.review-comment-modern {
    font-size: 15px;
    line-height: 1.8;
    color: #34495e;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    position: relative;
}

.review-comment-modern::before {
    content: '"';
    font-size: 48px;
    color: #e0e0e0;
    position: absolute;
    top: -10px;
    left: 10px;
    font-family: Georgia, serif;
}

.review-criteria-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 15px;
}

.review-criterion-pill {
    background: white;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 13px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.review-criterion-pill strong {
    color: #2c3e50;
}

.review-criterion-pill span {
    color: #667eea;
    font-weight: 700;
}

.review-actions-modern {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-review-action {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
    border: none;
}

.btn-review-action.warning {
    background: #fff3cd;
    color: #856404;
}

.btn-review-action.warning:hover {
    background: #ffc107;
}

.btn-review-action.danger {
    background: #f8d7da;
    color: #721c24;
}

.btn-review-action.danger:hover {
    background: #dc3545;
    color: white;
}

/* ===== Empty State ===== */
.empty-reviews {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #95a5a6;
}

.empty-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
}

.empty-text {
    font-size: 15px;
    color: #7f8c8d;
}

/* ===== Responsive ===== */
@media (max-width: 1200px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .profile-meta {
        justify-content: center;
    }

    .stats-row {
        grid-template-columns: 1fr;
    }

    .action-bar {
        flex-direction: column;
        gap: 15px;
    }

    .profile-name {
        font-size: 32px;
    }

    .rating-hero-box {
        margin: 0 auto;
    }
}

/* ===== Animations ===== */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card-modern,
.criteria-card,
.contact-card,
.performance-card,
.reviews-card {
    animation: fadeInUp 0.6s ease forwards;
}

.stat-card-modern:nth-child(1) { animation-delay: 0.1s; }
.stat-card-modern:nth-child(2) { animation-delay: 0.2s; }
.stat-card-modern:nth-child(3) { animation-delay: 0.3s; }
</style>

<div id="wrapper">
    <div class="content dietitian-profile-page">
        <!-- Action Bar with Back Button -->
        <div class="action-bar">
            <a href="<?php echo admin_url('dietetic/dietitians'); ?>" class="btn-action btn-outline">
                <i class="fa fa-arrow-left"></i>
                Retour à la liste
            </a>
            <div class="action-buttons">
                <button class="btn-action btn-primary-gradient">
                    <i class="fa fa-envelope"></i>
                    Contacter
                </button>
                <button class="btn-action btn-outline">
                    <i class="fa fa-print"></i>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Hero Profile Header -->
        <div class="profile-hero">
            <div class="hero-content">
                <div class="profile-avatar-large">
                    <?php if (!empty($dietitian->profile_image)) { ?>
                        <img src="<?php echo base_url('uploads/staff_profile_images/' . $dietitian->staffid . '/' . $dietitian->profile_image); ?>"
                             alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                    <?php } else { ?>
                        <div class="avatar-placeholder-large">
                            <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="profile-info-main">
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                    </h1>

                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fa fa-envelope"></i>
                            <?php echo htmlspecialchars($dietitian->email); ?>
                        </div>
                        <?php if (!empty($dietitian->phonenumber)) { ?>
                            <div class="meta-item">
                                <i class="fa fa-phone"></i>
                                <?php echo htmlspecialchars($dietitian->phonenumber); ?>
                            </div>
                        <?php } ?>
                        <div class="meta-item">
                            <i class="fa fa-user-md"></i>
                            Diététicien Nutritionniste
                        </div>
                    </div>

                    <div class="profile-badges">
                        <?php if ($average_ratings && $average_ratings->avg_overall >= 4.8) { ?>
                            <span class="badge-custom badge-top">
                                <i class="fa fa-star"></i>
                                Top Expert
                            </span>
                        <?php } ?>
                        <span class="badge-custom badge-verified">
                            <i class="fa fa-check-circle"></i>
                            Vérifié
                        </span>
                    </div>
                </div>

                <?php if ($average_ratings && $average_ratings->total_ratings > 0) { ?>
                    <div class="rating-hero-box">
                        <div class="rating-hero-number"><?php echo number_format($average_ratings->avg_overall, 1); ?></div>
                        <div class="rating-hero-stars">
                            <?php
                            $full_stars = floor($average_ratings->avg_overall);
                            $half_star = ($average_ratings->avg_overall - $full_stars) >= 0.5;
                            for ($i = 0; $i < $full_stars; $i++) {
                                echo '<i class="fa fa-star"></i>';
                            }
                            if ($half_star) {
                                echo '<i class="fa fa-star-half-o"></i>';
                                $full_stars++;
                            }
                            for ($i = $full_stars; $i < 5; $i++) {
                                echo '<i class="fa fa-star-o"></i>';
                            }
                            ?>
                        </div>
                        <div class="rating-hero-count"><?php echo $average_ratings->total_ratings; ?> avis</div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Statistics Row -->
        <div class="stats-row">
            <div class="stat-card-modern patients">
                <div class="stat-icon-modern">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-number-modern"><?php echo $stats->total_patients; ?></div>
                <div class="stat-label-modern">Total Patients</div>
                <div class="stat-sublabel">
                    <strong><?php echo $stats->active_patients; ?></strong> patients actifs
                </div>
            </div>

            <div class="stat-card-modern consultations">
                <div class="stat-icon-modern">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <div class="stat-number-modern"><?php echo $stats->total_consultations; ?></div>
                <div class="stat-label-modern">Consultations</div>
                <div class="stat-sublabel">
                    <strong><?php echo $stats->completed_consultations; ?></strong> complétées
                </div>
            </div>

            <div class="stat-card-modern programs">
                <div class="stat-icon-modern">
                    <i class="fa fa-list-alt"></i>
                </div>
                <div class="stat-number-modern"><?php echo $stats->total_programs; ?></div>
                <div class="stat-label-modern">Programmes</div>
                <div class="stat-sublabel">
                    <strong><?php echo $stats->active_programs; ?></strong> programmes actifs
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="profile-grid">
            <!-- Left Column -->
            <div>
                <!-- Criteria Ratings -->
                <?php if ($average_ratings && $average_ratings->total_ratings > 0) { ?>
                    <div class="criteria-card">
                        <div class="card-header-modern">
                            <div class="card-icon">
                                <i class="fa fa-bar-chart"></i>
                            </div>
                            <h3 class="card-title">Évaluations Détaillées</h3>
                        </div>

                        <?php
                        $criteria = [
                            ['name' => 'Professionnalisme', 'value' => $average_ratings->avg_professionalism],
                            ['name' => 'Qualité d\'Écoute', 'value' => $average_ratings->avg_listening],
                            ['name' => 'Conseils Pratiques', 'value' => $average_ratings->avg_advice],
                            ['name' => 'Résultats Obtenus', 'value' => $average_ratings->avg_results],
                            ['name' => 'Disponibilité', 'value' => $average_ratings->avg_availability],
                        ];

                        foreach ($criteria as $criterion) {
                            $percentage = ($criterion['value'] / 5) * 100;
                            $full_stars = floor($criterion['value']);
                            $half_star = ($criterion['value'] - $full_stars) >= 0.5;
                        ?>
                            <div class="criterion-modern">
                                <div class="criterion-header">
                                    <span class="criterion-name"><?php echo $criterion['name']; ?></span>
                                    <div class="criterion-score">
                                        <div class="criterion-stars-mini">
                                            <?php
                                            for ($i = 0; $i < $full_stars; $i++) {
                                                echo '<i class="fa fa-star"></i>';
                                            }
                                            if ($half_star) {
                                                echo '<i class="fa fa-star-half-o"></i>';
                                                $full_stars++;
                                            }
                                            for ($i = $full_stars; $i < 5; $i++) {
                                                echo '<i class="fa fa-star-o"></i>';
                                            }
                                            ?>
                                        </div>
                                        <span class="criterion-number"><?php echo number_format($criterion['value'], 1); ?></span>
                                    </div>
                                </div>
                                <div class="progress-bar-modern">
                                    <div class="progress-fill-modern" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- Reviews Section -->
                <div class="reviews-card">
                    <div class="card-header-modern">
                        <div class="card-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                        <h3 class="card-title">Avis des Patients <span style="color: #95a5a6; font-weight: 400;">(<?php echo count($ratings); ?>)</span></h3>
                    </div>

                    <?php if (!empty($ratings)) { ?>
                        <?php foreach ($ratings as $rating) { ?>
                            <div class="review-modern">
                                <div class="review-top">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?php echo strtoupper(substr($rating->patient_name, 0, 1)); ?>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">
                                                <?php echo htmlspecialchars($rating->patient_name); ?>
                                                <?php if (!$rating->is_public) { ?>
                                                    <span class="label label-warning" style="font-size: 11px; margin-left: 8px;">Masqué</span>
                                                <?php } ?>
                                            </h4>
                                            <div class="review-date-small">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo _dt($rating->created_at); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-rating-box">
                                        <div class="review-score-large"><?php echo number_format($rating->overall_rating, 1); ?></div>
                                        <div class="review-stars-modern">
                                            <?php
                                            $full_stars = floor($rating->overall_rating);
                                            $half_star = ($rating->overall_rating - $full_stars) >= 0.5;
                                            for ($i = 0; $i < $full_stars; $i++) {
                                                echo '<i class="fa fa-star"></i>';
                                            }
                                            if ($half_star) {
                                                echo '<i class="fa fa-star-half-o"></i>';
                                                $full_stars++;
                                            }
                                            for ($i = $full_stars; $i < 5; $i++) {
                                                echo '<i class="fa fa-star-o"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($rating->comment) { ?>
                                    <div class="review-comment-modern">
                                        <?php echo nl2br(htmlspecialchars($rating->comment)); ?>
                                    </div>
                                <?php } ?>

                                <div class="review-criteria-grid">
                                    <?php if ($rating->professionalism_rating) { ?>
                                        <div class="review-criterion-pill">
                                            <strong>Professionnalisme</strong>
                                            <span><?php echo $rating->professionalism_rating; ?>/5</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($rating->listening_rating) { ?>
                                        <div class="review-criterion-pill">
                                            <strong>Écoute</strong>
                                            <span><?php echo $rating->listening_rating; ?>/5</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($rating->advice_rating) { ?>
                                        <div class="review-criterion-pill">
                                            <strong>Conseils</strong>
                                            <span><?php echo $rating->advice_rating; ?>/5</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($rating->results_rating) { ?>
                                        <div class="review-criterion-pill">
                                            <strong>Résultats</strong>
                                            <span><?php echo $rating->results_rating; ?>/5</span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($rating->availability_rating) { ?>
                                        <div class="review-criterion-pill">
                                            <strong>Disponibilité</strong>
                                            <span><?php echo $rating->availability_rating; ?>/5</span>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php if (is_admin()) { ?>
                                    <div class="review-actions-modern">
                                        <button class="btn-review-action warning toggle-visibility"
                                                data-id="<?php echo $rating->id; ?>"
                                                data-public="<?php echo $rating->is_public; ?>">
                                            <i class="fa fa-eye<?php echo $rating->is_public ? '-slash' : ''; ?>"></i>
                                            <?php echo $rating->is_public ? 'Masquer' : 'Publier'; ?>
                                        </button>
                                        <button class="btn-review-action danger delete-rating"
                                                data-id="<?php echo $rating->id; ?>">
                                            <i class="fa fa-trash"></i>
                                            Supprimer
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="empty-reviews">
                            <div class="empty-icon">
                                <i class="fa fa-comment-o"></i>
                            </div>
                            <h4 class="empty-title">Aucun avis pour le moment</h4>
                            <p class="empty-text">Les avis des patients apparaîtront ici une fois qu'ils auront évalué ce diététicien.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div>
                <!-- Contact Information -->
                <div class="contact-card">
                    <div class="card-header-modern">
                        <div class="card-icon">
                            <i class="fa fa-address-card"></i>
                        </div>
                        <h3 class="card-title">Contact</h3>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="contact-info">
                            <div class="contact-label">Email</div>
                            <div class="contact-value"><?php echo htmlspecialchars($dietitian->email); ?></div>
                        </div>
                    </div>

                    <?php if (!empty($dietitian->phonenumber)) { ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">Téléphone</div>
                                <div class="contact-value"><?php echo htmlspecialchars($dietitian->phonenumber); ?></div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="contact-info">
                            <div class="contact-label">Staff ID</div>
                            <div class="contact-value">#<?php echo $dietitian->staffid; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="performance-card">
                    <div class="card-header-modern" style="border-bottom: 2px solid rgba(255,255,255,0.2); padding-bottom: 20px; margin-bottom: 20px;">
                        <h3 class="card-title" style="color: white; margin: 0; font-size: 20px;">
                            <i class="fa fa-line-chart" style="margin-right: 10px;"></i>
                            Performance
                        </h3>
                    </div>

                    <div class="performance-item">
                        <span class="performance-label">Taux de Satisfaction</span>
                        <span class="performance-value">
                            <?php
                            $satisfaction_rate = $average_ratings && $average_ratings->total_ratings > 0 ?
                                ($average_ratings->avg_overall / 5) * 100 : 0;
                            echo number_format($satisfaction_rate, 0);
                            ?>%
                        </span>
                    </div>

                    <div class="performance-item">
                        <span class="performance-label">Patients Actifs</span>
                        <span class="performance-value"><?php echo $stats->active_patients; ?></span>
                    </div>

                    <div class="performance-item">
                        <span class="performance-label">Programmes Actifs</span>
                        <span class="performance-value"><?php echo $stats->active_programs; ?></span>
                    </div>

                    <div class="performance-item">
                        <span class="performance-label">Consultations Réalisées</span>
                        <span class="performance-value"><?php echo $stats->completed_consultations; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Animate progress bars on load
    setTimeout(function() {
        $('.progress-fill-modern').each(function() {
            var width = $(this).css('width');
            $(this).css('width', '0');
            setTimeout(() => {
                $(this).css('width', width);
            }, 100);
        });
    }, 300);

    // Toggle visibility
    $('.toggle-visibility').on('click', function() {
        var btn = $(this);
        var ratingId = btn.data('id');

        $.ajax({
            url: '<?php echo admin_url('dietetic/dietitians/toggle_rating_visibility/'); ?>' + ratingId,
            type: 'POST',
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert_float('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert_float('danger', data.message);
                }
            }
        });
    });

    // Delete rating
    $('.delete-rating').on('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis définitivement ?')) {
            return;
        }

        var btn = $(this);
        var ratingId = btn.data('id');

        $.ajax({
            url: '<?php echo admin_url('dietetic/dietitians/delete_rating/'); ?>' + ratingId,
            type: 'POST',
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert_float('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert_float('danger', data.message);
                }
            }
        });
    });
});
</script>

<?php init_tail(); ?>
