<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<!-- MOBILE HEADER & NAVIGATION v2.0 -->
<style>
/* IMPORTANT: Force display pour debug */
.mobile-portal-header,
.mobile-bottom-nav {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Reset pour mobile */
@media (max-width: 768px) {
    body {
        padding-top: 60px !important;
        padding-bottom: 65px !important;
        margin: 0 !important;
    }
}

@media (min-width: 769px) {
    body {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
}

/* Header Mobile Fixe */
.mobile-portal-header {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%) !important;
    color: white !important;
    padding: 12px 15px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    z-index: 99999 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;
    margin: 0 !important;
}

.mobile-portal-header .logo {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: white !important;
}

.mobile-portal-header .hamburger {
    width: 30px !important;
    height: 25px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    cursor: pointer !important;
}

.mobile-portal-header .hamburger span {
    display: block !important;
    height: 3px !important;
    width: 100% !important;
    background: white !important;
    border-radius: 2px !important;
    transition: 0.3s !important;
}

/* Menu Mobile Slide */
.mobile-menu-slide {
    position: fixed !important;
    top: 0 !important;
    right: -100% !important;
    width: 280px !important;
    height: 100vh !important;
    background: white !important;
    z-index: 100000 !important;
    transition: right 0.3s ease !important;
    overflow-y: auto !important;
    padding-top: 60px !important;
}

.mobile-menu-slide.open {
    right: 0 !important;
}

.mobile-menu-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0,0,0,0.5) !important;
    z-index: 99998 !important;
    display: none !important;
}

.mobile-menu-overlay.show {
    display: block !important;
}

.mobile-menu-slide a {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 15px 20px !important;
    color: #333 !important;
    text-decoration: none !important;
    border-bottom: 1px solid #eee !important;
}

.mobile-menu-slide a.active {
    background: #e8f5f4 !important;
    color: #01807B !important;
    border-left: 4px solid #01807B !important;
}

.mobile-menu-slide a i {
    font-size: 20px !important;
    width: 25px !important;
}

/* Bottom Nav Fixe */
.mobile-bottom-nav {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    background: white !important;
    display: flex !important;
    justify-content: space-around !important;
    padding: 8px 0 !important;
    border-top: 1px solid #ddd !important;
    z-index: 99999 !important;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1) !important;
    margin: 0 !important;
}

.mobile-bottom-nav a {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 4px !important;
    color: #666 !important;
    text-decoration: none !important;
    font-size: 11px !important;
}

.mobile-bottom-nav a.active {
    color: #01807B !important;
}

.mobile-bottom-nav a i {
    font-size: 22px !important;
}

/* Cache sur desktop */
@media (min-width: 769px) {
    .mobile-portal-header,
    .mobile-bottom-nav,
    .mobile-menu-slide,
    .mobile-menu-overlay {
        display: none !important;
    }
}
</style>

<!-- Header Mobile -->
<div class="mobile-portal-header">
    <div class="logo">
        <i class="fa fa-heartbeat"></i> DietSenegal
    </div>
    <div class="hamburger" onclick="toggleMobileMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<!-- Overlay -->
<div class="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>

<!-- Menu Slide -->
<div class="mobile-menu-slide" id="mobileMenu">
    <a href="<?php echo site_url('dietetic/portal'); ?>" class="active">
        <i class="fa fa-home"></i>
        <span>Accueil</span>
    </a>
    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
        <i class="fa fa-cutlery"></i>
        <span>Plans de Repas</span>
    </a>
    <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>">
        <i class="fa fa-clipboard-list"></i>
        <span>Enquêtes Alimentaires</span>
    </a>
    <?php } ?>
    <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>">
        <i class="fa fa-heartbeat"></i>
        <span>Mes Mesures</span>
    </a>
    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
        <i class="fa fa-user-md"></i>
        <span>Mon Diététicien</span>
    </a>
    <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
    <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>">
        <i class="fa fa-bell"></i>
        <span>Notifications</span>
    </a>
    <?php } ?>
    <a href="<?php echo site_url('clients/profile'); ?>">
        <i class="fa fa-user"></i>
        <span>Mon Profil</span>
    </a>
    <a href="<?php echo site_url('authentication/logout'); ?>">
        <i class="fa fa-sign-out"></i>
        <span>Déconnexion</span>
    </a>
</div>

<!-- Bottom Navigation -->
<div class="mobile-bottom-nav">
    <a href="<?php echo site_url('dietetic/portal'); ?>" class="active">
        <i class="fa fa-home"></i>
        <span>Accueil</span>
    </a>
    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
        <i class="fa fa-cutlery"></i>
        <span>Repas</span>
    </a>
    <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>">
        <i class="fa fa-clipboard-list"></i>
        <span>Enquêtes</span>
    </a>
    <?php } ?>
    <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>">
        <i class="fa fa-plus-circle"></i>
        <span>Mesure</span>
    </a>
    <a href="<?php echo site_url('clients/profile'); ?>">
        <i class="fa fa-user"></i>
        <span>Profil</span>
    </a>
</div>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.querySelector('.mobile-menu-overlay');

    menu.classList.toggle('open');
    overlay.classList.toggle('show');
}
</script>

<?php
// Set active page for navigation
$active_page = 'dashboard';
?>

<div class="dietetic-portal-header">
    <h1><i class="fa fa-heartbeat"></i> <?php echo _l('dietetic_my_program'); ?></h1>
    <p><?php echo _l('welcome'); ?>, <?php echo $patient->client->company; ?>!</p>
</div>

<div class="portal-stat-grid">
    <div class="portal-stat-card">
        <h3><?php echo $patient->latest_measurement ? $patient->latest_measurement->weight . ' kg' : '-'; ?></h3>
        <p><?php echo _l('dietetic_current_weight'); ?></p>
    </div>

    <div class="portal-stat-card">
        <h3><?php echo $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h3>
        <p><?php echo _l('dietetic_target_weight'); ?></p>
    </div>

    <div class="portal-stat-card">
        <h3><?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? $patient->latest_measurement->bmi : '-'; ?></h3>
        <p><?php echo _l('dietetic_bmi'); ?></p>
    </div>

    <?php if ($weight_progress->weight_change !== null) { ?>
        <div class="portal-stat-card">
            <h3 class="<?php echo $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
                <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
            </h3>
            <p><?php echo _l('dietetic_weight_progress'); ?></p>
        </div>
    <?php } ?>
</div>

<?php if ($active_program) { ?>
    <div class="portal-program-card">
        <div class="portal-program-header">
            <div class="portal-program-title"><?php echo $active_program->program_name; ?></div>
            <span class="portal-program-status active"><?php echo _l('dietetic_program_active'); ?></span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong><?php echo _l('dietetic_start_date'); ?>:</strong> <?php echo _d($active_program->start_date); ?></p>
                <?php if ($active_program->end_date) { ?>
                    <p><strong><?php echo _l('dietetic_end_date'); ?>:</strong> <?php echo _d($active_program->end_date); ?></p>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <?php if ($active_program->daily_calories) { ?>
                    <p><strong><?php echo _l('dietetic_daily_calories'); ?>:</strong> <?php echo $active_program->daily_calories; ?> kcal</p>
                <?php } ?>
                <?php if ($active_program->daily_protein) { ?>
                    <p><strong><?php echo _l('dietetic_protein'); ?>:</strong> <?php echo $active_program->daily_protein; ?>g</p>
                <?php } ?>
            </div>
        </div>

        <?php if ($active_program->objective) { ?>
            <p><strong><?php echo _l('dietetic_objective'); ?>:</strong></p>
            <p><?php echo nl2br($active_program->objective); ?></p>
        <?php } ?>

        <?php
        // Get meal plans for this program
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $meal_plans = $this->dietetic_meal_plans_model->get_by_program($active_program->id);
        ?>

        <?php if (!empty($meal_plans)) { ?>
            <h5 class="mtop20"><?php echo _l('dietetic_meal_plans'); ?></h5>
            <?php foreach ($meal_plans as $plan) { ?>
                <div class="portal-meal-plan-card">
                    <div class="portal-meal-plan-header">
                        <?php echo $plan->plan_name; ?> - <?php echo _l('dietetic_week'); ?> <?php echo $plan->week_number; ?>
                    </div>
                    <div class="mtop10">
                        <a href="<?php echo site_url('dietetic/portal/meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-info">
                            <i class="fa fa-eye"></i> <?php echo _l('dietetic_view_details'); ?>
                        </a>
                        <a href="<?php echo site_url('dietetic/portal/download_meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> <?php echo _l('dietetic_download_pdf'); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_no_programs'); ?>
    </div>
<?php } ?>

<div class="row mtop30">
    <div class="col-md-6">
        <div class="portal-weight-chart-container">
            <h4><?php echo _l('dietetic_weight_evolution'); ?></h4>
            <canvas id="portalWeightChart" height="200"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo _l('dietetic_upcoming_consultations'); ?></div>
            <div class="panel-body">
                <?php if (!empty($upcoming_consultations)) { ?>
                    <ul class="portal-consultation-list">
                        <?php foreach ($upcoming_consultations as $consultation) { ?>
                            <li class="portal-consultation-item">
                                <div>
                                    <div class="portal-consultation-date"><?php echo _dt($consultation->consultation_date); ?></div>
                                    <div class="portal-consultation-type"><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></div>
                                </div>
                                <span class="portal-consultation-status badge badge-info"><?php echo _l('dietetic_consultation_scheduled'); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="text-muted"><?php echo _l('dietetic_no_consultations'); ?></p>
                <?php } ?>

                <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="btn btn-sm btn-default mtop10">
                    <?php echo _l('view_all'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// Check if food surveys are enabled
$food_surveys_enabled = $this->db->table_exists(db_prefix() . 'dietic_food_surveys');
$active_surveys = [];
if ($food_surveys_enabled) {
    $this->load->model('dietetic/dietetic_food_surveys_model');
    $active_surveys = $this->dietetic_food_surveys_model->get_active_by_patient($patient->id);
}
?>

<?php if ($food_surveys_enabled) { ?>
<style>
/* Food Surveys Section - Mobile First */
.food-surveys-section {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin: 30px 0;
}

.food-surveys-header {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    padding: 24px 20px;
    position: relative;
    overflow: hidden;
}

.food-surveys-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.food-surveys-header h3 {
    margin: 0;
    color: white;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.food-surveys-header h3 i {
    font-size: 26px;
}

.food-surveys-body {
    padding: 24px 20px;
}

/* Survey Cards */
.survey-cards-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (min-width: 768px) {
    .survey-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1200px) {
    .survey-cards-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.survey-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e8ecef;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.survey-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #01807B 0%, #F3911D 100%);
}

.survey-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(1, 128, 123, 0.15);
    border-color: #01807B;
}

.survey-card-title {
    color: #01807B;
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.survey-card-title i {
    color: #F3911D;
    font-size: 20px;
}

.survey-card-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 16px;
}

.survey-card-meta i {
    color: #01807B;
}

/* Progress Bar */
.survey-progress {
    margin: 16px 0;
}

.survey-progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.survey-progress-text {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
}

.survey-progress-percent {
    font-size: 16px;
    font-weight: 700;
    color: #01807B;
}

.survey-progress-bar-container {
    height: 10px;
    background: #e9ecef;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
}

.survey-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
    border-radius: 20px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.survey-progress-bar::after {
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

/* Action Buttons */
.survey-actions {
    display: flex;
    gap: 10px;
    margin-top: 16px;
}

@media (max-width: 480px) {
    .survey-actions {
        flex-direction: column;
    }
}

.btn-survey-primary {
    flex: 1;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-survey-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(1, 128, 123, 0.3);
    color: white;
    text-decoration: none;
}

.btn-survey-secondary {
    flex: 1;
    background: white;
    color: #01807B;
    border: 2px solid #01807B;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-survey-secondary:hover {
    background: #01807B;
    color: white;
    text-decoration: none;
}

/* Empty State */
.survey-empty-state {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 2px dashed #01807B;
}

.survey-empty-state i {
    font-size: 48px;
    color: #01807B;
    opacity: 0.3;
    margin-bottom: 16px;
}

.survey-empty-state h4 {
    color: #495057;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.survey-empty-state p {
    color: #6c757d;
    font-size: 14px;
    margin: 0;
    line-height: 1.6;
}

/* View All Button */
.survey-view-all {
    text-align: center;
    padding-top: 20px;
    border-top: 2px solid #e9ecef;
    margin-top: 20px;
}

.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    color: #01807B;
    border: 2px solid #e9ecef;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-view-all:hover {
    background: #01807B;
    color: white;
    border-color: #01807B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
    text-decoration: none;
}

/* Responsive */
@media (max-width: 768px) {
    .food-surveys-header {
        padding: 20px 16px;
    }

    .food-surveys-header h3 {
        font-size: 20px;
    }

    .food-surveys-body {
        padding: 20px 16px;
    }

    .survey-card-title {
        font-size: 16px;
    }
}
</style>

<div class="food-surveys-section">
    <div class="food-surveys-header">
        <h3>
            <i class="fa fa-clipboard-list"></i>
            Mes Enquêtes Alimentaires
        </h3>
    </div>

    <div class="food-surveys-body">
        <?php if (!empty($active_surveys)) { ?>
        <div class="survey-cards-grid">
            <?php foreach ($active_surveys as $survey) {
                $completion = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);
            ?>
            <div class="survey-card">
                <h4 class="survey-card-title">
                    <i class="fa fa-utensils"></i>
                    <?php echo htmlspecialchars($survey->survey_name); ?>
                </h4>

                <div class="survey-card-meta">
                    <i class="fa fa-calendar"></i>
                    <span><?php echo _d($survey->start_date); ?> - <?php echo _d($survey->end_date); ?></span>
                </div>

                <div class="survey-progress">
                    <div class="survey-progress-label">
                        <span class="survey-progress-text">Progression</span>
                        <span class="survey-progress-percent"><?php echo round($completion); ?>%</span>
                    </div>
                    <div class="survey-progress-bar-container">
                        <div class="survey-progress-bar" style="width: <?php echo $completion; ?>%"></div>
                    </div>
                </div>

                <div class="survey-actions">
                    <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id); ?>" class="btn-survey-primary">
                        <i class="fa fa-camera"></i>
                        Soumettre
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/view_recommendations/' . $survey->id); ?>" class="btn-survey-secondary">
                        <i class="fa fa-comments"></i>
                        Recommandations
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="survey-empty-state">
            <i class="fa fa-clipboard-list"></i>
            <h4>Aucune enquête alimentaire active</h4>
            <p>Votre diététicien ne vous a pas encore assigné d'enquête alimentaire active.<br>
            Les enquêtes vous permettent de partager vos repas et recevoir des recommandations.</p>
        </div>
        <?php } ?>

        <div class="survey-view-all">
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="btn-view-all">
                <i class="fa fa-list"></i>
                Voir toutes mes enquêtes
            </a>
        </div>
    </div>
</div>
<?php } ?>

<!-- Action Buttons -->
<div class="mtop20" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <?php if (dietetic_get_option('enable_client_measurements', true)) { ?>
        <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="btn btn-info" style="flex: 1; min-width: 200px;">
            <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_measurement'); ?>
        </a>
    <?php } ?>

    <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>"
           class="btn btn-lg"
           style="background: linear-gradient(135deg, #01807B 0%, #019B95 100%); border-color: #01807B; color: white; flex: 1; min-width: 200px; font-weight: 600; box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3); transition: all 0.3s;"
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(1, 128, 123, 0.4)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(1, 128, 123, 0.3)'">
            <i class="fa fa-clipboard-list"></i> Mes Enquêtes Alimentaires
        </a>
    <?php } ?>
</div>

<style>
@media (max-width: 768px) {
    .mtop20 > a {
        flex: 1 1 100% !important;
        min-width: 100% !important;
    }
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        <?php if (!empty($weight_evolution)) { ?>
            var labels = [<?php foreach ($weight_evolution as $m) { echo '"' . _d($m->measurement_date) . '",'; } ?>];
            var weights = [<?php foreach ($weight_evolution as $m) { echo $m->weight . ','; } ?>];
            var bmis = [<?php foreach ($weight_evolution as $m) { echo $m->bmi . ','; } ?>];

            if (typeof dietetic_portal !== 'undefined') {
                dietetic_portal.loadWeightChart('portalWeightChart', weights, bmis, labels);
            }
        <?php } ?>
    });
</script>

<?php $this->load->view('authentication/includes/footer'); ?>
