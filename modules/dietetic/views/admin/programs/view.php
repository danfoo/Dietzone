<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Professional Design for Program View */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Hero Header */
.program-hero {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 20px;
    padding: 50px;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(17, 153, 142, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.program-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.program-hero-content {
    position: relative;
    z-index: 1;
}

.program-hero-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.program-hero-subtitle {
    font-size: 18px;
    opacity: 0.95;
    margin-bottom: 10px;
}

.program-hero-subtitle a {
    color: white;
    text-decoration: underline;
    font-weight: 600;
}

.program-hero-subtitle a:hover {
    opacity: 0.8;
}

.program-status-badge-hero {
    display: inline-block;
    padding: 8px 20px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    margin-top: 10px;
}

/* Action Bar */
.action-bar-modern {
    background: white;
    border-radius: 12px;
    padding: 20px 30px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.action-bar-modern .btn-modern {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-back-modern {
    background: #f5f5f5;
    color: #666;
}

.btn-back-modern:hover {
    background: #e0e0e0;
    color: #666;
}

.btn-edit-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-edit-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Main Grid Layout */
.program-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

@media (max-width: 992px) {
    .program-grid {
        grid-template-columns: 1fr;
    }
}

/* Info Cards */
.info-card-modern {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.info-card-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.icon-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.icon-nutrition {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.icon-description {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.icon-objective {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.icon-instructions {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.info-card-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.info-table-modern {
    width: 100%;
}

.info-table-modern tr {
    border-bottom: 1px solid #f5f5f5;
}

.info-table-modern tr:last-child {
    border-bottom: none;
}

.info-table-modern td {
    padding: 15px 0;
    font-size: 15px;
}

.info-table-modern td:first-child {
    color: #7f8c8d;
    font-weight: 600;
    width: 40%;
}

.info-table-modern td:last-child {
    color: #2c3e50;
    font-weight: 500;
}

/* Nutrition Cards */
.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

@media (max-width: 768px) {
    .nutrition-grid {
        grid-template-columns: 1fr;
    }
}

.nutrition-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 16px;
    padding: 25px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.nutrition-card:hover {
    transform: translateY(-5px);
}

.nutrition-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    transform: rotate(45deg);
}

.nutrition-card.calories {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.nutrition-card.protein {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.nutrition-card.carbs {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.nutrition-card.fats {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.nutrition-card.fiber {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.nutrition-icon {
    font-size: 32px;
    margin-bottom: 10px;
    opacity: 0.9;
}

.nutrition-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.nutrition-value {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
}

.nutrition-unit {
    font-size: 14px;
    opacity: 0.9;
}

/* Sidebar Cards */
.sidebar-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
}

.sidebar-card-title {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-card-title i {
    color: #11998e;
}

.sidebar-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f5f5f5;
}

.sidebar-stat:last-child {
    border-bottom: none;
}

.sidebar-stat-label {
    color: #7f8c8d;
    font-size: 14px;
    font-weight: 600;
}

.sidebar-stat-value {
    color: #2c3e50;
    font-size: 18px;
    font-weight: 700;
}

/* Date Badge */
.date-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

/* Content Section */
.content-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.content-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.content-section-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.content-text {
    color: #555;
    font-size: 15px;
    line-height: 1.8;
    white-space: pre-wrap;
}

/* Meal Plans Section */
.meal-plans-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

.meal-plans-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.meal-plans-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 12px;
}

.meal-plans-title i {
    color: #11998e;
}

.btn-add-meal-plan {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-add-meal-plan:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
    color: white;
}

/* Meal Plan Cards */
.meal-plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.meal-plan-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s ease;
    border-left: 5px solid #11998e;
    position: relative;
    overflow: hidden;
}

.meal-plan-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
}

.meal-plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.meal-plan-week {
    display: inline-block;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meal-plan-name {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
}

.meal-plan-date {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.meal-plan-date i {
    color: #11998e;
}

.meal-plan-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-meal-action {
    flex: 1;
    min-width: 100px;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 13px;
}

.btn-meal-view {
    background: white;
    color: #2c3e50;
    border: 2px solid #e0e0e0;
}

.btn-meal-view:hover {
    background: #f5f5f5;
    color: #2c3e50;
    border-color: #11998e;
}

.btn-meal-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-meal-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-meal-pdf {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-meal-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 64px;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state-title {
    font-size: 20px;
    font-weight: 600;
    color: #7f8c8d;
    margin-bottom: 10px;
}

.empty-state-text {
    color: #bdc3c7;
    font-size: 14px;
}

/* Animations */
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

.animate-in {
    animation: fadeInUp 0.6s ease-out forwards;
}

.delay-1 { animation-delay: 0.1s; opacity: 0; }
.delay-2 { animation-delay: 0.2s; opacity: 0; }
.delay-3 { animation-delay: 0.3s; opacity: 0; }
.delay-4 { animation-delay: 0.4s; opacity: 0; }

/* Status Badges Custom */
.status-badge-active {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
}

.status-badge-completed {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
}

.status-badge-paused {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    display: inline-block;
}

/* Responsive */
@media (max-width: 768px) {
    .program-hero {
        padding: 30px 20px;
    }

    .program-hero-title {
        font-size: 24px;
    }

    .action-bar-modern {
        flex-direction: column;
        gap: 15px;
    }

    .action-bar-modern .btn-modern {
        width: 100%;
        justify-content: center;
    }

    .nutrition-grid {
        grid-template-columns: 1fr;
    }

    .meal-plans-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .btn-add-meal-plan {
        width: 100%;
        justify-content: center;
    }

    .meal-plans-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Hero Header -->
                <div class="program-hero animate-in">
                    <div class="program-hero-content">
                        <div class="program-hero-title">
                            <?php echo htmlspecialchars($program->program_name); ?>
                        </div>
                        <div class="program-hero-subtitle">
                            <i class="fa fa-user"></i> <?php echo _l('dietetic_patient'); ?>:
                            <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>">
                                <?php echo htmlspecialchars($patient->client->company); ?>
                            </a>
                        </div>
                        <div class="program-status-badge-hero">
                            <?php echo dietetic_program_status_badge($program->status); ?>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="action-bar-modern animate-in delay-1">
                    <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn-modern btn-back-modern">
                        <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                    </a>
                    <?php if (dietetic_has_permission('edit')) { ?>
                        <a href="<?php echo admin_url('dietetic/programs/edit/' . $program->id); ?>" class="btn-modern btn-edit-modern">
                            <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                        </a>
                    <?php } ?>
                </div>

                <!-- Main Grid -->
                <div class="program-grid">

                    <!-- Left Column -->
                    <div>

                        <!-- Program Details Card -->
                        <div class="info-card-modern animate-in delay-2">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-info">
                                    <i class="fa fa-info"></i>
                                </div>
                                <h3 class="info-card-title"><?php echo _l('details'); ?></h3>
                            </div>
                            <table class="info-table-modern">
                                <tr>
                                    <td><?php echo _l('dietetic_status'); ?></td>
                                    <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo _l('dietetic_dietitian'); ?></td>
                                    <td><?php echo htmlspecialchars($program->dietitian_name); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo _l('dietetic_start_date'); ?></td>
                                    <td>
                                        <span class="date-badge">
                                            <i class="fa fa-calendar"></i>
                                            <?php echo _d($program->start_date); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($program->end_date) { ?>
                                    <tr>
                                        <td><?php echo _l('dietetic_end_date'); ?></td>
                                        <td>
                                            <span class="date-badge">
                                                <i class="fa fa-calendar"></i>
                                                <?php echo _d($program->end_date); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td><?php echo _l('dietetic_meal_count'); ?></td>
                                    <td><strong><?php echo $program->meal_count; ?></strong> repas par jour</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Nutrition Card (Manual Values from Program) -->
                        <?php if ($program->daily_calories || $program->daily_protein || $program->daily_carbs || $program->daily_fats || $program->daily_fiber) { ?>
                        <div class="info-card-modern animate-in delay-3">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-nutrition">
                                    <i class="fa fa-apple"></i>
                                </div>
                                <h3 class="info-card-title">
                                    <?php echo _l('dietetic_daily_targets'); ?>
                                    <span style="font-size: 12px; font-weight: 500; color: #999; display: block; margin-top: 5px;">
                                        <i class="fa fa-pencil"></i> Valeurs saisies manuellement
                                    </span>
                                </h3>
                            </div>

                            <div class="nutrition-grid">
                                <?php if ($program->daily_calories) { ?>
                                    <div class="nutrition-card calories">
                                        <div class="nutrition-icon">🔥</div>
                                        <div class="nutrition-label">Calories</div>
                                        <div class="nutrition-value"><?php echo number_format($program->daily_calories, 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">kcal / jour</div>
                                    </div>
                                <?php } ?>

                                <?php if ($program->daily_protein) { ?>
                                    <div class="nutrition-card protein">
                                        <div class="nutrition-icon">💪</div>
                                        <div class="nutrition-label">Protéines</div>
                                        <div class="nutrition-value"><?php echo number_format($program->daily_protein, 1, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour
                                            <?php if ($program->daily_calories) {
                                                $protein_cal = $program->daily_protein * 4;
                                                $protein_pct = round(($protein_cal / $program->daily_calories) * 100, 1);
                                                echo ' • ' . $protein_pct . '%';
                                            } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($program->daily_carbs) { ?>
                                    <div class="nutrition-card carbs">
                                        <div class="nutrition-icon">🌾</div>
                                        <div class="nutrition-label">Glucides</div>
                                        <div class="nutrition-value"><?php echo number_format($program->daily_carbs, 1, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour
                                            <?php if ($program->daily_calories) {
                                                $carbs_cal = $program->daily_carbs * 4;
                                                $carbs_pct = round(($carbs_cal / $program->daily_calories) * 100, 1);
                                                echo ' • ' . $carbs_pct . '%';
                                            } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($program->daily_fats) { ?>
                                    <div class="nutrition-card fats">
                                        <div class="nutrition-icon">🥑</div>
                                        <div class="nutrition-label">Lipides</div>
                                        <div class="nutrition-value"><?php echo number_format($program->daily_fats, 1, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour
                                            <?php if ($program->daily_calories) {
                                                $fats_cal = $program->daily_fats * 9;
                                                $fats_pct = round(($fats_cal / $program->daily_calories) * 100, 1);
                                                echo ' • ' . $fats_pct . '%';
                                            } ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($program->daily_fiber) { ?>
                                    <div class="nutrition-card fiber">
                                        <div class="nutrition-icon">🌿</div>
                                        <div class="nutrition-label">Fibres</div>
                                        <div class="nutrition-value"><?php echo number_format($program->daily_fiber, 1, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour</div>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php if ($program->daily_calories && $program->daily_protein && $program->daily_carbs && $program->daily_fats) { ?>
                                <!-- Macro Distribution Chart -->
                                <div style="margin-top: 30px; padding: 25px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                    <h4 style="font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 20px; text-align: center;">
                                        <i class="fa fa-pie-chart" style="color: #11998e;"></i> Distribution des Macronutriments
                                    </h4>
                                    <canvas id="macroDistributionChart" style="max-height: 250px;"></canvas>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <!-- Calculated Objectives from Anamnesis -->
                        <?php if ($calculated_objectives) { ?>
                            <div class="info-card-modern animate-in delay-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #01807B;">
                                <div class="info-card-header">
                                    <div class="info-card-icon" style="background: linear-gradient(135deg, #01807B 0%, #01655f 100%);">
                                        <i class="fa fa-calculator"></i>
                                    </div>
                                    <h3 class="info-card-title">
                                        Objectifs Calculés depuis l'Anamnèse
                                        <span style="font-size: 12px; font-weight: 500; color: #01807B; display: block; margin-top: 5px;">
                                            <i class="fa fa-info-circle"></i> Basés sur les données patient (âge, taille, poids, activité, objectif)
                                        </span>
                                    </h3>
                                </div>

                                <!-- Metabolic Summary -->
                                <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                    <h4 style="font-size: 15px; font-weight: 700; color: #2c3e50; margin-bottom: 15px;">
                                        <i class="fa fa-heartbeat" style="color: #01807B;"></i> Métabolisme
                                    </h4>
                                    <div class="nutrition-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                        <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white;">
                                            <div style="font-size: 11px; opacity: 0.9; margin-bottom: 5px;">MB (Mifflin)</div>
                                            <div style="font-size: 24px; font-weight: 700;"><?php echo number_format($calculated_objectives['bmr']['value'], 0); ?></div>
                                            <div style="font-size: 11px; opacity: 0.9;">kcal/jour</div>
                                        </div>
                                        <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; color: white;">
                                            <div style="font-size: 11px; opacity: 0.9; margin-bottom: 5px;">TDEE</div>
                                            <div style="font-size: 24px; font-weight: 700;"><?php echo number_format($calculated_objectives['tdee']['tdee'], 0); ?></div>
                                            <div style="font-size: 11px; opacity: 0.9;"><?php echo $calculated_objectives['tdee']['activity_description']; ?></div>
                                        </div>
                                        <?php if ($calculated_objectives['calorie_needs']['deficit_surplus'] != 0) { ?>
                                            <div style="text-align: center; padding: 15px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 10px; color: white;">
                                                <div style="font-size: 11px; opacity: 0.9; margin-bottom: 5px;">Ajustement</div>
                                                <div style="font-size: 24px; font-weight: 700;">
                                                    <?php echo ($calculated_objectives['calorie_needs']['deficit_surplus'] > 0 ? '+' : '') . $calculated_objectives['calorie_needs']['deficit_surplus']; ?>
                                                </div>
                                                <div style="font-size: 11px; opacity: 0.9;">kcal/jour</div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Calculated Macros -->
                                <div class="nutrition-grid">
                                    <div class="nutrition-card calories">
                                        <div class="nutrition-icon">🔥</div>
                                        <div class="nutrition-label">Calories Cible</div>
                                        <div class="nutrition-value"><?php echo number_format($calculated_objectives['macros']['calories'], 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">kcal / jour</div>
                                    </div>

                                    <div class="nutrition-card protein">
                                        <div class="nutrition-icon">💪</div>
                                        <div class="nutrition-label">Protéines</div>
                                        <div class="nutrition-value"><?php echo number_format($calculated_objectives['macros']['protein']['grams'], 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour • <?php echo $calculated_objectives['macros']['protein']['percentage']; ?>%</div>
                                    </div>

                                    <div class="nutrition-card carbs">
                                        <div class="nutrition-icon">🌾</div>
                                        <div class="nutrition-label">Glucides</div>
                                        <div class="nutrition-value"><?php echo number_format($calculated_objectives['macros']['carbs']['grams'], 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour • <?php echo $calculated_objectives['macros']['carbs']['percentage']; ?>%</div>
                                    </div>

                                    <div class="nutrition-card fats">
                                        <div class="nutrition-icon">🥑</div>
                                        <div class="nutrition-label">Lipides</div>
                                        <div class="nutrition-value"><?php echo number_format($calculated_objectives['macros']['fats']['grams'], 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour • <?php echo $calculated_objectives['macros']['fats']['percentage']; ?>%</div>
                                    </div>

                                    <div class="nutrition-card fiber">
                                        <div class="nutrition-icon">🌿</div>
                                        <div class="nutrition-label">Fibres</div>
                                        <div class="nutrition-value"><?php echo number_format($calculated_objectives['macros']['fiber'], 0, ',', ' '); ?></div>
                                        <div class="nutrition-unit">g / jour</div>
                                    </div>

                                    <?php if ($calculated_objectives['water_needs']) { ?>
                                        <div class="nutrition-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                            <div class="nutrition-icon">💧</div>
                                            <div class="nutrition-label">Eau</div>
                                            <div class="nutrition-value"><?php echo number_format($calculated_objectives['water_needs']['daily_liters'], 1, ',', ' '); ?></div>
                                            <div class="nutrition-unit">L / jour • <?php echo $calculated_objectives['water_needs']['glasses_250ml']; ?> verres</div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <?php if ($calculated_objectives['body_composition']) { ?>
                                    <!-- Body Composition -->
                                    <div style="background: white; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        <h4 style="font-size: 15px; font-weight: 700; color: #2c3e50; margin-bottom: 15px;">
                                            <i class="fa fa-pie-chart" style="color: #01807B;"></i> Composition Corporelle (US Navy)
                                        </h4>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px;">
                                            <div style="text-align: center; padding: 10px;">
                                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 3px;">Masse Grasse</div>
                                                <div style="font-size: 20px; font-weight: 700; color: #f093fb;"><?php echo $calculated_objectives['body_composition']['body_fat_percentage']; ?>%</div>
                                                <div style="font-size: 10px; color: #7f8c8d;"><?php echo $calculated_objectives['body_composition']['fat_mass']; ?> kg</div>
                                            </div>
                                            <div style="text-align: center; padding: 10px;">
                                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 3px;">Masse Maigre</div>
                                                <div style="font-size: 20px; font-weight: 700; color: #43e97b;"><?php echo $calculated_objectives['body_composition']['lean_body_mass']; ?> kg</div>
                                            </div>
                                            <div style="text-align: center; padding: 10px;">
                                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 3px;">Masse Musculaire</div>
                                                <div style="font-size: 20px; font-weight: 700; color: #667eea;"><?php echo $calculated_objectives['body_composition']['muscle_mass']; ?> kg</div>
                                            </div>
                                            <div style="text-align: center; padding: 10px;">
                                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 3px;">Eau Corporelle</div>
                                                <div style="font-size: 20px; font-weight: 700; color: #4facfe;"><?php echo $calculated_objectives['body_composition']['body_water']; ?> kg</div>
                                            </div>
                                        </div>
                                        <div style="margin-top: 15px; text-align: center; padding: 10px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px;">
                                            <span style="font-size: 13px; font-weight: 600; color: #2c3e50;">Catégorie:</span>
                                            <span style="font-size: 13px; font-weight: 700; color: #01807B; margin-left: 8px;"><?php echo $calculated_objectives['body_composition']['category']; ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <!-- Description -->
                        <?php if ($program->description) { ?>
                            <div class="content-section animate-in delay-4">
                                <div class="content-section-header">
                                    <div class="info-card-icon icon-description">
                                        <i class="fa fa-file-text"></i>
                                    </div>
                                    <h3 class="content-section-title"><?php echo _l('dietetic_description'); ?></h3>
                                </div>
                                <div class="content-text"><?php echo nl2br(htmlspecialchars($program->description)); ?></div>
                            </div>
                        <?php } ?>

                    </div>

                    <!-- Right Sidebar -->
                    <div>

                        <!-- Objective Card -->
                        <?php if ($program->objective) { ?>
                            <div class="sidebar-card animate-in delay-2">
                                <div class="sidebar-card-title">
                                    <i class="fa fa-bullseye"></i>
                                    <?php echo _l('dietetic_objective'); ?>
                                </div>
                                <div class="content-text"><?php echo nl2br(htmlspecialchars($program->objective)); ?></div>
                            </div>
                        <?php } ?>

                        <!-- Instructions Card -->
                        <?php if ($program->instructions) { ?>
                            <div class="sidebar-card animate-in delay-3">
                                <div class="sidebar-card-title">
                                    <i class="fa fa-list-ol"></i>
                                    <?php echo _l('dietetic_instructions'); ?>
                                </div>
                                <div class="content-text"><?php echo nl2br(htmlspecialchars($program->instructions)); ?></div>
                            </div>
                        <?php } ?>

                        <!-- Quick Stats Card -->
                        <div class="sidebar-card animate-in delay-4">
                            <div class="sidebar-card-title">
                                <i class="fa fa-bar-chart"></i>
                                Statistiques Rapides
                            </div>
                            <div class="sidebar-stat">
                                <span class="sidebar-stat-label">Plans de repas</span>
                                <span class="sidebar-stat-value"><?php echo count($meal_plans); ?></span>
                            </div>
                            <div class="sidebar-stat">
                                <span class="sidebar-stat-label">Repas / jour</span>
                                <span class="sidebar-stat-value"><?php echo $program->meal_count; ?></span>
                            </div>
                            <?php if ($program->daily_calories) { ?>
                                <div class="sidebar-stat">
                                    <span class="sidebar-stat-label">Calories / jour</span>
                                    <span class="sidebar-stat-value"><?php echo number_format($program->daily_calories, 0, ',', ' '); ?></span>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>

                <!-- Meal Plans Section -->
                <div class="meal-plans-section animate-in delay-4">
                    <div class="meal-plans-header">
                        <h3 class="meal-plans-title">
                            <i class="fa fa-cutlery"></i>
                            <?php echo _l('dietetic_meal_plans'); ?>
                        </h3>
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/programs/create_meal_plan/' . $program->id); ?>" class="btn-add-meal-plan">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_meal_plan'); ?>
                            </a>
                        <?php } ?>
                    </div>

                    <?php if (!empty($meal_plans)) { ?>
                        <div class="meal-plans-grid">
                            <?php foreach ($meal_plans as $plan) { ?>
                                <div class="meal-plan-card">
                                    <div class="meal-plan-week">
                                        <i class="fa fa-calendar-o"></i> Semaine <?php echo $plan->week_number; ?>
                                    </div>
                                    <div class="meal-plan-name">
                                        <?php echo htmlspecialchars($plan->plan_name); ?>
                                    </div>
                                    <div class="meal-plan-date">
                                        <i class="fa fa-clock-o"></i>
                                        <?php echo $plan->start_date ? _d($plan->start_date) : 'Non défini'; ?>
                                    </div>
                                    <div class="meal-plan-actions">
                                        <a href="<?php echo admin_url('dietetic/programs/meal_plan/' . $plan->id); ?>" class="btn-meal-action btn-meal-view">
                                            <i class="fa fa-eye"></i> Voir
                                        </a>
                                        <?php if (dietetic_has_permission('edit')) { ?>
                                            <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $plan->id); ?>" class="btn-meal-action btn-meal-edit">
                                                <i class="fa fa-pencil"></i> Éditer
                                            </a>
                                        <?php } ?>
                                        <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $plan->id); ?>" class="btn-meal-action btn-meal-pdf">
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fa fa-cutlery"></i>
                            </div>
                            <div class="empty-state-title">Aucun plan de repas</div>
                            <div class="empty-state-text">
                                Commencez par créer un plan de repas pour ce programme
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Invoices Section -->
                <div class="meal-plans-section animate-in delay-5" style="margin-top: 30px;">
                    <div class="meal-plans-header">
                        <h3 class="meal-plans-title">
                            <i class="fa fa-file-text-o"></i>
                            Factures liées au programme
                        </h3>
                    </div>

                    <?php if (!empty($invoices)) { ?>
                        <div class="table-responsive">
                            <table class="table table-hover" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Date</th>
                                        <th>Date d'échéance</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice) { ?>
                                        <tr>
                                            <td>
                                                <strong style="color: #01807B;">
                                                    <i class="fa fa-file-text-o"></i>
                                                    <?php echo format_invoice_number($invoice->id); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <i class="fa fa-calendar" style="color: #999;"></i>
                                                <?php echo _d($invoice->date); ?>
                                            </td>
                                            <td>
                                                <i class="fa fa-clock-o" style="color: #999;"></i>
                                                <?php echo _d($invoice->duedate); ?>
                                            </td>
                                            <td>
                                                <strong style="font-size: 15px; color: #2c3e50;">
                                                    <?php echo app_format_money($invoice->total, $invoice->currency_name); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = 'default';
                                                if ($invoice->status == 2) {
                                                    $status_class = 'success'; // Paid
                                                } elseif ($invoice->status == 1) {
                                                    $status_class = 'warning'; // Unpaid
                                                } elseif ($invoice->status == 5) {
                                                    $status_class = 'danger'; // Overdue
                                                } elseif ($invoice->status == 3) {
                                                    $status_class = 'info'; // Partially Paid
                                                }
                                                ?>
                                                <span class="label label-<?php echo $status_class; ?>" style="padding: 6px 12px; font-size: 12px; border-radius: 4px;">
                                                    <?php echo format_invoice_status($invoice->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo admin_url('invoices/list_invoices/' . $invoice->id); ?>"
                                                   class="btn btn-sm btn-default"
                                                   style="border-radius: 6px;"
                                                   target="_blank">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                            <div class="empty-state-title">Aucune facture</div>
                            <div class="empty-state-text">
                                Aucune facture n'est associée à ce programme pour le moment
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Activity History Section -->
                <div class="meal-plans-section animate-in delay-6" style="margin-top: 30px;">
                    <div class="meal-plans-header">
                        <h3 class="meal-plans-title">
                            <i class="fa fa-history"></i>
                            Historique d'activité
                        </h3>
                    </div>

                    <?php if (!empty($history)) { ?>
                        <div class="timeline-container" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <div class="timeline">
                                <?php foreach ($history as $activity) { ?>
                                    <div class="timeline-item" style="position: relative; padding-left: 40px; padding-bottom: 30px; border-left: 2px solid #e9ecef;">
                                        <div class="timeline-marker" style="position: absolute; left: -8px; top: 0; width: 14px; height: 14px; border-radius: 50%; background: #01807B; border: 3px solid white; box-shadow: 0 0 0 2px #e9ecef;"></div>
                                        <div class="timeline-content">
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                                <div>
                                                    <strong style="color: #2c3e50; font-size: 14px;">
                                                        <?php
                                                        if (!empty($activity->staffid)) {
                                                            $staff = $this->staff_model->get($activity->staffid);
                                                            echo $staff ? get_staff_full_name($activity->staffid) : 'Système';
                                                        } else {
                                                            echo 'Système';
                                                        }
                                                        ?>
                                                    </strong>
                                                </div>
                                                <small style="color: #999; font-size: 12px;">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo time_ago($activity->date); ?>
                                                </small>
                                            </div>
                                            <div style="color: #555; font-size: 13px; line-height: 1.6;">
                                                <?php echo $activity->description; ?>
                                            </div>
                                            <?php if (!empty($activity->additional_data)) { ?>
                                                <div style="margin-top: 8px; padding: 10px; background: #f8f9fa; border-radius: 6px; font-size: 12px; color: #666;">
                                                    <i class="fa fa-info-circle"></i> Détails supplémentaires
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fa fa-history"></i>
                            </div>
                            <div class="empty-state-title">Aucun historique</div>
                            <div class="empty-state-text">
                                Aucune activité n'a été enregistrée pour ce programme
                            </div>
                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<?php if ($program->daily_calories && $program->daily_protein && $program->daily_carbs && $program->daily_fats) { ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function() {
    'use strict';

    // Calculate macro percentages
    const protein = <?php echo $program->daily_protein; ?>;
    const carbs = <?php echo $program->daily_carbs; ?>;
    const fats = <?php echo $program->daily_fats; ?>;
    const calories = <?php echo $program->daily_calories; ?>;

    const proteinCal = protein * 4;
    const carbsCal = carbs * 4;
    const fatsCal = fats * 9;

    const proteinPct = Math.round((proteinCal / calories) * 100 * 10) / 10;
    const carbsPct = Math.round((carbsCal / calories) * 100 * 10) / 10;
    const fatsPct = Math.round((fatsCal / calories) * 100 * 10) / 10;

    // Create doughnut chart
    const ctx = document.getElementById('macroDistributionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Protéines (' + proteinPct + '%)', 'Glucides (' + carbsPct + '%)', 'Lipides (' + fatsPct + '%)'],
                datasets: [{
                    data: [proteinPct, carbsPct, fatsPct],
                    backgroundColor: [
                        'rgba(240, 147, 251, 0.8)',  // Protein (pink/purple)
                        'rgba(79, 172, 254, 0.8)',   // Carbs (blue)
                        'rgba(67, 233, 123, 0.8)'    // Fats (green)
                    ],
                    borderColor: [
                        'rgba(240, 147, 251, 1)',
                        'rgba(79, 172, 254, 1)',
                        'rgba(67, 233, 123, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            color: '#2c3e50',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;

                                let grams = 0;
                                if (context.dataIndex === 0) {
                                    grams = protein + 'g';
                                } else if (context.dataIndex === 1) {
                                    grams = carbs + 'g';
                                } else if (context.dataIndex === 2) {
                                    grams = fats + 'g';
                                }

                                return label + ' • ' + grams;
                            }
                        }
                    }
                },
                cutout: '65%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });
    }
})();
</script>
<?php } ?>
