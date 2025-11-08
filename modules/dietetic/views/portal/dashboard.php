<?php
$active_page = 'dashboard';
$page_title = 'Mon Programme';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Flat Design - Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    border: 2px solid #f1f3f5;
    transition: all 0.3s;
}

.stat-card:hover {
    border-color: #01807B;
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.15);
}

.stat-card .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-card.weight .stat-icon {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
}

.stat-card.target .stat-icon {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.stat-card.bmi .stat-icon {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    color: white;
}

.stat-card.progress .stat-icon {
    background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
    color: white;
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 8px 0;
    line-height: 1;
}

.stat-card .stat-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value.text-success {
    color: #48bb78 !important;
}

/* Program Card - Flat Design */
.program-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid #f1f3f5;
    margin-bottom: 30px;
}

.program-header {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    padding: 32px 28px;
    color: white;
    position: relative;
}

.program-header::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.program-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px 0;
    position: relative;
    z-index: 1;
}

.program-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.25);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.program-status i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.program-body {
    padding: 28px;
}

.program-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #01807B;
}

.info-item i {
    font-size: 20px;
    color: #01807B;
    margin-top: 2px;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.info-value {
    font-size: 16px;
    color: #212529;
    font-weight: 600;
}

.program-objective {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #F3911D;
    margin: 20px 0;
}

.program-objective strong {
    display: block;
    color: #01807B;
    font-size: 14px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.program-objective p {
    color: #495057;
    line-height: 1.8;
    margin: 0;
}

/* Meal Plans Section */
.meal-plans-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 2px solid #f1f3f5;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #01807B;
}

.meal-plan-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 12px;
    transition: all 0.3s;
}

.meal-plan-item:hover {
    border-color: #01807B;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.1);
}

.meal-plan-name {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 12px;
}

.meal-plan-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-flat {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-flat-primary {
    background: #01807B;
    color: white;
}

.btn-flat-primary:hover {
    background: #026660;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
    color: white;
    text-decoration: none;
}

.btn-flat-success {
    background: #48bb78;
    color: white;
}

.btn-flat-success:hover {
    background: #38a169;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    color: white;
    text-decoration: none;
}

/* Consultations Card - Timeline Design */
.consultations-card {
    background: white;
    border-radius: 16px;
    border: 2px solid #f1f3f5;
    overflow: hidden;
}

.consultations-header {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    padding: 20px 24px;
    color: white;
}

.consultations-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.consultations-body {
    padding: 24px;
}

.consultation-timeline {
    position: relative;
}

.consultation-timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #4299e1 0%, #e9ecef 100%);
}

.consultation-item {
    position: relative;
    padding-left: 56px;
    margin-bottom: 24px;
}

.consultation-item:last-child {
    margin-bottom: 0;
}

.consultation-dot {
    position: absolute;
    left: 12px;
    top: 4px;
    width: 18px;
    height: 18px;
    background: #4299e1;
    border: 3px solid white;
    border-radius: 50%;
    box-shadow: 0 0 0 2px #4299e1;
    z-index: 1;
}

.consultation-content {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 10px;
    border-left: 3px solid #4299e1;
}

.consultation-date {
    font-size: 14px;
    font-weight: 700;
    color: #01807B;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.consultation-type {
    font-size: 13px;
    color: #495057;
    margin-bottom: 8px;
}

.consultation-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #4299e1;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empty-consultations {
    text-align: center;
    padding: 32px 20px;
    color: #6c757d;
}

.empty-consultations i {
    font-size: 48px;
    opacity: 0.3;
    margin-bottom: 12px;
}

.chart-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    border: 2px solid #f1f3f5;
}

.chart-card h4 {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-card h4 i {
    color: #01807B;
}

.no-program-alert {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 2px solid #4299e1;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    margin-bottom: 30px;
}

.no-program-alert i {
    font-size: 56px;
    color: #4299e1;
    margin-bottom: 16px;
}

.no-program-alert p {
    font-size: 16px;
    color: #1e40af;
    font-weight: 500;
    margin: 0;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .program-info-grid {
        grid-template-columns: 1fr;
    }

    .meal-plan-actions {
        flex-direction: column;
    }

    .btn-flat {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card weight">
        <div class="stat-icon">
            <i class="fa fa-weight"></i>
        </div>
        <div class="stat-value"><?php echo $patient->latest_measurement ? $patient->latest_measurement->weight : '-'; ?></div>
        <div class="stat-label">Poids actuel (kg)</div>
    </div>

    <div class="stat-card target">
        <div class="stat-icon">
            <i class="fa fa-bullseye"></i>
        </div>
        <div class="stat-value"><?php echo $patient->target_weight ? $patient->target_weight : '-'; ?></div>
        <div class="stat-label">Objectif (kg)</div>
    </div>

    <div class="stat-card bmi">
        <div class="stat-icon">
            <i class="fa fa-heartbeat"></i>
        </div>
        <div class="stat-value"><?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? number_format($patient->latest_measurement->bmi, 1) : '-'; ?></div>
        <div class="stat-label">IMC</div>
    </div>

    <?php if ($weight_progress->weight_change !== null) { ?>
    <div class="stat-card progress">
        <div class="stat-icon">
            <i class="fa fa-chart-line"></i>
        </div>
        <div class="stat-value <?php echo $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
            <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?>
        </div>
        <div class="stat-label">Progression (kg)</div>
    </div>
    <?php } ?>
</div>

<!-- Program Card -->
<?php if ($active_program) { ?>
<div class="program-card">
    <div class="program-header">
        <div class="program-title"><i class="fa fa-trophy"></i> <?php echo $active_program->program_name; ?></div>
        <div class="program-status">
            <i class="fa fa-circle"></i>
            Programme actif
        </div>
    </div>

    <div class="program-body">
        <div class="program-info-grid">
            <div class="info-item">
                <i class="fa fa-calendar-alt"></i>
                <div class="info-content">
                    <div class="info-label">Date de début</div>
                    <div class="info-value"><?php echo _d($active_program->start_date); ?></div>
                </div>
            </div>

            <?php if ($active_program->end_date) { ?>
            <div class="info-item">
                <i class="fa fa-calendar-check"></i>
                <div class="info-content">
                    <div class="info-label">Date de fin</div>
                    <div class="info-value"><?php echo _d($active_program->end_date); ?></div>
                </div>
            </div>
            <?php } ?>

            <?php if ($active_program->daily_calories) { ?>
            <div class="info-item">
                <i class="fa fa-fire"></i>
                <div class="info-content">
                    <div class="info-label">Calories quotidiennes</div>
                    <div class="info-value"><?php echo $active_program->daily_calories; ?> kcal</div>
                </div>
            </div>
            <?php } ?>

            <?php if ($active_program->daily_protein) { ?>
            <div class="info-item">
                <i class="fa fa-drumstick-bite"></i>
                <div class="info-content">
                    <div class="info-label">Protéines</div>
                    <div class="info-value"><?php echo $active_program->daily_protein; ?>g</div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if ($active_program->objective) { ?>
        <div class="program-objective">
            <strong><i class="fa fa-bullseye"></i> Objectif</strong>
            <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
        </div>
        <?php } ?>

        <?php
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $meal_plans = $this->dietetic_meal_plans_model->get_by_program($active_program->id);
        ?>

        <?php if (!empty($meal_plans)) { ?>
        <div class="meal-plans-section">
            <h5 class="section-title">
                <i class="fa fa-utensils"></i>
                Plans de repas
            </h5>

            <?php foreach ($meal_plans as $plan) { ?>
            <div class="meal-plan-item">
                <div class="meal-plan-name">
                    <i class="fa fa-calendar-week"></i>
                    <?php echo $plan->plan_name; ?> - Semaine <?php echo $plan->week_number; ?>
                </div>
                <div class="meal-plan-actions">
                    <a href="<?php echo site_url('dietetic/portal/meal_plan/' . $plan->id); ?>" class="btn-flat btn-flat-primary">
                        <i class="fa fa-eye"></i> Voir les détails
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/download_meal_plan/' . $plan->id); ?>" class="btn-flat btn-flat-success">
                        <i class="fa fa-download"></i> Télécharger PDF
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php } else { ?>
<div class="no-program-alert">
    <i class="fa fa-info-circle"></i>
    <p>Aucun programme actif pour le moment. Votre diététicien vous en assignera un prochainement.</p>
</div>
<?php } ?>

<!-- Row for Chart and Consultations -->
<div class="row">
    <div class="col-md-6">
        <div class="chart-card">
            <h4><i class="fa fa-chart-area"></i> Évolution du poids</h4>
            <canvas id="portalWeightChart" height="200"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="consultations-card">
            <div class="consultations-header">
                <h4><i class="fa fa-calendar-alt"></i> Prochains rendez-vous</h4>
            </div>
            <div class="consultations-body">
                <?php if (!empty($upcoming_consultations)) { ?>
                <div class="consultation-timeline">
                    <?php foreach ($upcoming_consultations as $consultation) { ?>
                    <div class="consultation-item">
                        <div class="consultation-dot"></div>
                        <div class="consultation-content">
                            <div class="consultation-date">
                                <i class="fa fa-clock"></i>
                                <?php echo _dt($consultation->consultation_date); ?>
                            </div>
                            <div class="consultation-type">
                                <?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?>
                            </div>
                            <span class="consultation-badge">
                                <i class="fa fa-check-circle"></i>
                                Programmé
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="btn-flat btn-flat-primary">
                        <i class="fa fa-list"></i> Voir tous les rendez-vous
                    </a>
                </div>
                <?php } else { ?>
                <div class="empty-consultations">
                    <i class="fa fa-calendar-times"></i>
                    <p>Aucun rendez-vous programmé</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

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

<?php $this->load->view('portal/includes/portal_footer'); ?>
