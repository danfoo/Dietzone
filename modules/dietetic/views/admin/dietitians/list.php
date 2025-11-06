<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* ===== Page Container ===== */
.dietitians-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 30px 0;
}

/* ===== Header Section ===== */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.header-content {
    position: relative;
    z-index: 1;
}

.page-title {
    margin: 0 0 10px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-subtitle {
    margin: 0;
    opacity: 0.95;
    font-size: 16px;
    font-weight: 400;
}

/* ===== Stats Overview ===== */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-left: 4px solid;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
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

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card.primary {
    border-color: #667eea;
}
.stat-card.primary::before { content: '\f0c0'; color: #667eea; }

.stat-card.success {
    border-color: #06d6a0;
}
.stat-card.success::before { content: '\f007'; color: #06d6a0; }

.stat-card.warning {
    border-color: #ffd60a;
}
.stat-card.warning::before { content: '\f005'; color: #ffd60a; }

.stat-card.info {
    border-color: #118ab2;
}
.stat-card.info::before { content: '\f073'; color: #118ab2; }

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-bottom: 15px;
}

.stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #06d6a0, #1b9aaa); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #ffd60a, #ffc300); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #118ab2, #0582ca); }

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    color: #7f8c8d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===== Filters & Search Section ===== */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filters-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.filter-group {
    position: relative;
}

.filter-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #7f8c8d;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-group input,
.filter-group select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter-group input:focus,
.filter-group select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-group i {
    position: absolute;
    right: 15px;
    top: 43px;
    color: #95a5a6;
    pointer-events: none;
}

.view-toggle {
    display: flex;
    gap: 10px;
}

.view-toggle-btn {
    padding: 10px 15px;
    border: 2px solid #ecf0f1;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #7f8c8d;
}

.view-toggle-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.view-toggle-btn.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

/* ===== Dietitians Grid ===== */
.dietitians-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 25px;
}

.dietitian-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.dietitian-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 30px 25px 25px;
    text-align: center;
    position: relative;
}

.card-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-top {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #8b6914;
}

.badge-excellent {
    background: linear-gradient(135deg, #06d6a0, #1b9aaa);
    color: white;
}

.badge-good {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.dietitian-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 15px;
    border: 4px solid white;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    position: relative;
}

.dietitian-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 40px;
    font-weight: 700;
}

.dietitian-name {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 8px 0;
}

.dietitian-email {
    font-size: 13px;
    color: #7f8c8d;
    margin: 0;
}

.dietitian-email i {
    margin-right: 5px;
}

.card-body {
    padding: 25px;
}

.rating-section {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 20px;
}

.rating-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 10px;
}

.rating-number {
    font-size: 48px;
    font-weight: 700;
    background: linear-gradient(135deg, #f39c12, #f1c40f);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.rating-stars {
    display: flex;
    gap: 4px;
}

.rating-stars i {
    font-size: 20px;
    color: #f39c12;
}

.rating-stars i.empty {
    color: #d5d8dc;
}

.rating-count {
    font-size: 13px;
    color: #95a5a6;
    font-weight: 500;
}

.criteria-breakdown {
    margin-top: 15px;
}

.criterion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.criterion-label {
    font-size: 12px;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.criterion-value {
    display: flex;
    align-items: center;
    gap: 8px;
}

.criterion-bar {
    width: 100px;
    height: 6px;
    background: #ecf0f1;
    border-radius: 3px;
    overflow: hidden;
}

.criterion-fill {
    height: 100%;
    background: linear-gradient(90deg, #06d6a0, #1b9aaa);
    border-radius: 3px;
    transition: width 0.5s ease;
}

.criterion-number {
    font-size: 12px;
    font-weight: 700;
    color: #2c3e50;
    min-width: 30px;
    text-align: right;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: #ecf0f1;
    transform: scale(1.05);
}

.stat-item i {
    font-size: 24px;
    color: #667eea;
    margin-bottom: 8px;
    display: block;
}

.stat-item-value {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 5px;
}

.stat-item-label {
    font-size: 11px;
    color: #7f8c8d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-footer {
    padding: 20px 25px;
    background: #f8f9fa;
    display: flex;
    gap: 10px;
}

.btn-view-profile {
    flex: 1;
    padding: 14px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-view-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-contact {
    padding: 14px 20px;
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-contact:hover {
    background: #667eea;
    color: white;
}

/* ===== Empty State ===== */
.empty-state {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 30px;
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #95a5a6;
}

.empty-state-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 15px 0;
}

.empty-state-text {
    font-size: 16px;
    color: #7f8c8d;
    margin: 0;
}

/* ===== Loading State ===== */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 8px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .dietitians-grid {
        grid-template-columns: 1fr;
    }

    .stats-overview {
        grid-template-columns: 1fr;
    }

    .page-title {
        font-size: 24px;
    }

    .filters-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
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

.dietitian-card {
    animation: fadeInUp 0.5s ease forwards;
}

.dietitian-card:nth-child(1) { animation-delay: 0.1s; }
.dietitian-card:nth-child(2) { animation-delay: 0.2s; }
.dietitian-card:nth-child(3) { animation-delay: 0.3s; }
.dietitian-card:nth-child(4) { animation-delay: 0.4s; }
.dietitian-card:nth-child(5) { animation-delay: 0.5s; }
.dietitian-card:nth-child(6) { animation-delay: 0.6s; }
</style>

<div id="wrapper">
    <div class="content dietitians-page">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fa fa-user-md"></i>
                    Nos Diététiciens
                </h1>
                <p class="page-subtitle">Découvrez notre équipe d'experts en nutrition et diététique</p>
            </div>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-warning" style="padding: 25px; border-radius: 12px; margin-bottom: 30px; border-left: 4px solid #ffc107; background: #fff3cd;">
                <i class="fa fa-exclamation-triangle" style="font-size: 20px; margin-right: 10px;"></i>
                <strong>Information:</strong> <?php echo $error; ?>
            </div>
        <?php } ?>

        <?php if (!empty($dietitians)) { ?>
            <?php
            // Calculate global stats
            $total_dietitians = count($dietitians);
            $total_patients = array_sum(array_column($dietitians, 'total_patients'));
            $total_ratings_count = array_sum(array_column($dietitians, 'total_ratings'));
            $avg_overall_rating = $total_ratings_count > 0 ?
                array_sum(array_map(function($d) { return $d->avg_rating * $d->total_ratings; }, $dietitians)) / $total_ratings_count : 0;
            ?>

            <!-- Stats Overview -->
            <div class="stats-overview">
                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_dietitians; ?></div>
                    <div class="stat-label">Diététiciens</div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_patients; ?></div>
                    <div class="stat-label">Patients Total</div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fa fa-star"></i>
                    </div>
                    <div class="stat-value"><?php echo number_format($avg_overall_rating, 1); ?></div>
                    <div class="stat-label">Note Moyenne</div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_ratings_count; ?></div>
                    <div class="stat-label">Avis Total</div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <h3 class="filters-title"><i class="fa fa-filter"></i> Filtres & Recherche</h3>
                    <div class="view-toggle">
                        <button class="view-toggle-btn active" id="gridViewBtn" onclick="switchView('grid')">
                            <i class="fa fa-th"></i>
                        </button>
                        <button class="view-toggle-btn" id="listViewBtn" onclick="switchView('list')">
                            <i class="fa fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Rechercher</label>
                        <input type="text" id="searchInput" placeholder="Nom, email..." onkeyup="filterDietitians()">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class="filter-group">
                        <label>Note Minimum</label>
                        <select id="ratingFilter" onchange="filterDietitians()">
                            <option value="0">Toutes les notes</option>
                            <option value="4.5">4.5★ et plus</option>
                            <option value="4.0">4.0★ et plus</option>
                            <option value="3.5">3.5★ et plus</option>
                            <option value="3.0">3.0★ et plus</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Trier Par</label>
                        <select id="sortFilter" onchange="sortDietitians()">
                            <option value="rating">Note (Haut → Bas)</option>
                            <option value="patients">Patients (Plus → Moins)</option>
                            <option value="consultations">Consultations</option>
                            <option value="name">Nom (A → Z)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dietitians Grid -->
            <div class="dietitians-grid" id="dietitiansContainer">
                <?php foreach ($dietitians as $dietitian) {
                    $badge_class = '';
                    $badge_text = '';
                    if ($dietitian->avg_rating >= 4.8) {
                        $badge_class = 'badge-top';
                        $badge_text = '⭐ Top Expert';
                    } elseif ($dietitian->avg_rating >= 4.5) {
                        $badge_class = 'badge-excellent';
                        $badge_text = 'Excellent';
                    } elseif ($dietitian->avg_rating >= 4.0) {
                        $badge_class = 'badge-good';
                        $badge_text = 'Très Bien';
                    }
                ?>
                    <div class="dietitian-card"
                         data-name="<?php echo strtolower($dietitian->firstname . ' ' . $dietitian->lastname); ?>"
                         data-email="<?php echo strtolower($dietitian->email); ?>"
                         data-rating="<?php echo $dietitian->avg_rating; ?>"
                         data-patients="<?php echo $dietitian->total_patients; ?>"
                         data-consultations="<?php echo $dietitian->total_consultations; ?>">

                        <div class="card-header">
                            <?php if ($badge_text) { ?>
                                <div class="card-badge <?php echo $badge_class; ?>">
                                    <?php echo $badge_text; ?>
                                </div>
                            <?php } ?>

                            <div class="dietitian-avatar">
                                <?php if (!empty($dietitian->profile_image)) { ?>
                                    <img src="<?php echo staff_profile_image_url($dietitian->staffid, 'small'); ?>"
                                         alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                                <?php } else { ?>
                                    <div class="avatar-placeholder">
                                        <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <h3 class="dietitian-name">
                                <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                            </h3>
                            <p class="dietitian-email">
                                <i class="fa fa-envelope"></i>
                                <?php echo htmlspecialchars($dietitian->email); ?>
                            </p>
                        </div>

                        <div class="card-body">
                            <!-- Rating Section -->
                            <div class="rating-section">
                                <?php if ($dietitian->total_ratings > 0) { ?>
                                    <div class="rating-display">
                                        <div class="rating-number"><?php echo number_format($dietitian->avg_rating, 1); ?></div>
                                        <div>
                                            <div class="rating-stars">
                                                <?php
                                                $full_stars = floor($dietitian->avg_rating);
                                                $half_star = ($dietitian->avg_rating - $full_stars) >= 0.5;

                                                for ($i = 0; $i < $full_stars; $i++) {
                                                    echo '<i class="fa fa-star"></i>';
                                                }
                                                if ($half_star) {
                                                    echo '<i class="fa fa-star-half-o"></i>';
                                                    $full_stars++;
                                                }
                                                for ($i = $full_stars; $i < 5; $i++) {
                                                    echo '<i class="fa fa-star empty"></i>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rating-count"><?php echo $dietitian->total_ratings; ?> avis clients</div>

                                    <!-- Criteria Breakdown -->
                                    <div class="criteria-breakdown">
                                        <?php
                                        $criteria = [
                                            ['label' => 'Professionnalisme', 'value' => $dietitian->avg_professionalism ?? 0],
                                            ['label' => 'Écoute', 'value' => $dietitian->avg_listening ?? 0],
                                            ['label' => 'Conseils', 'value' => $dietitian->avg_advice ?? 0],
                                            ['label' => 'Résultats', 'value' => $dietitian->avg_results ?? 0],
                                            ['label' => 'Disponibilité', 'value' => $dietitian->avg_availability ?? 0],
                                        ];
                                        foreach ($criteria as $criterion) {
                                            $percentage = ($criterion['value'] / 5) * 100;
                                        ?>
                                            <div class="criterion">
                                                <span class="criterion-label"><?php echo $criterion['label']; ?></span>
                                                <div class="criterion-value">
                                                    <div class="criterion-bar">
                                                        <div class="criterion-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                    </div>
                                                    <span class="criterion-number"><?php echo number_format($criterion['value'], 1); ?>/5</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="rating-display">
                                        <div class="rating-stars">
                                            <i class="fa fa-star empty"></i>
                                            <i class="fa fa-star empty"></i>
                                            <i class="fa fa-star empty"></i>
                                            <i class="fa fa-star empty"></i>
                                            <i class="fa fa-star empty"></i>
                                        </div>
                                    </div>
                                    <div class="rating-count">Aucun avis pour le moment</div>
                                <?php } ?>
                            </div>

                            <!-- Stats Grid -->
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <i class="fa fa-users"></i>
                                    <div class="stat-item-value"><?php echo $dietitian->total_patients; ?></div>
                                    <div class="stat-item-label">Patients</div>
                                </div>
                                <div class="stat-item">
                                    <i class="fa fa-calendar-check-o"></i>
                                    <div class="stat-item-value"><?php echo $dietitian->total_consultations; ?></div>
                                    <div class="stat-item-label">Consultations</div>
                                </div>
                                <div class="stat-item">
                                    <i class="fa fa-list-alt"></i>
                                    <div class="stat-item-value"><?php echo $dietitian->total_programs; ?></div>
                                    <div class="stat-item-label">Programmes</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="<?php echo admin_url('dietetic/dietitians/view/' . $dietitian->staffid); ?>"
                               class="btn-view-profile">
                                <i class="fa fa-eye"></i>
                                Voir le Profil Complet
                            </a>
                            <button class="btn-contact" title="Contacter">
                                <i class="fa fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <h3 class="empty-state-title">Aucun diététicien trouvé</h3>
                <p class="empty-state-text">
                    Il n'y a actuellement aucun diététicien dans le système.<br>
                    Les diététiciens apparaîtront ici une fois qu'ils seront ajoutés et auront des patients assignés.
                </p>
            </div>
        <?php } ?>
    </div>
</div>

<script>
// Filter dietitians
function filterDietitians() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const minRating = parseFloat(document.getElementById('ratingFilter').value);
    const cards = document.querySelectorAll('.dietitian-card');

    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        const email = card.getAttribute('data-email');
        const rating = parseFloat(card.getAttribute('data-rating'));

        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRating = rating >= minRating;

        card.style.display = (matchesSearch && matchesRating) ? 'block' : 'none';
    });
}

// Sort dietitians
function sortDietitians() {
    const container = document.getElementById('dietitiansContainer');
    const cards = Array.from(container.querySelectorAll('.dietitian-card'));
    const sortBy = document.getElementById('sortFilter').value;

    cards.sort((a, b) => {
        let aValue, bValue;

        switch (sortBy) {
            case 'rating':
                aValue = parseFloat(a.getAttribute('data-rating'));
                bValue = parseFloat(b.getAttribute('data-rating'));
                return bValue - aValue; // Descending
            case 'patients':
                aValue = parseInt(a.getAttribute('data-patients'));
                bValue = parseInt(b.getAttribute('data-patients'));
                return bValue - aValue; // Descending
            case 'consultations':
                aValue = parseInt(a.getAttribute('data-consultations'));
                bValue = parseInt(b.getAttribute('data-consultations'));
                return bValue - aValue; // Descending
            case 'name':
                aValue = a.getAttribute('data-name');
                bValue = b.getAttribute('data-name');
                return aValue.localeCompare(bValue); // Ascending
            default:
                return 0;
        }
    });

    // Re-append sorted cards
    cards.forEach(card => container.appendChild(card));
}

// Switch between grid and list view
function switchView(view) {
    const container = document.getElementById('dietitiansContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    if (view === 'grid') {
        container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(380px, 1fr))';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        container.style.gridTemplateColumns = '1fr';
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const fills = document.querySelectorAll('.criterion-fill');
    fills.forEach(fill => {
        const width = fill.style.width;
        fill.style.width = '0';
        setTimeout(() => {
            fill.style.width = width;
        }, 100);
    });
});
</script>

<?php init_tail(); ?>
