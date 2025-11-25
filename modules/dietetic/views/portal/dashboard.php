<?php
$active_page = 'dashboard';
$page_title = 'Mon Programme';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Weight Goal Card - Unified Block */
.weight-goal-card {
    background: white;
    border-radius: 16px;
    padding: 28px 32px;
    border: 2px solid #f1f3f5;
    margin-bottom: 24px;
    transition: all 0.3s;
}

.weight-goal-card:hover {
    border-color: #01807B;
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.12);
}

.weight-goal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f1f3f5;
}

.weight-goal-title {
    font-size: 20px;
    font-weight: 700;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.weight-goal-title i {
    color: #01807B;
}

.weight-goal-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.weight-goal-status.achieved {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.weight-goal-status.on-track {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
}

.weight-goal-status.ahead {
    background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
    color: white;
}

.weight-goal-status.behind {
    background: linear-gradient(135deg, #F3911D 0%, #dd7711 100%);
    color: white;
}

.weight-goal-status.no-data {
    background: #e9ecef;
    color: #6c757d;
}

.weight-values {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 24px;
    align-items: center;
    margin-bottom: 24px;
}

.weight-value-item {
    text-align: center;
}

.weight-value-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.weight-value-number {
    font-size: 36px;
    font-weight: 700;
    color: #212529;
    line-height: 1;
}

.weight-value-unit {
    font-size: 16px;
    color: #6c757d;
    font-weight: 500;
    margin-left: 4px;
}

.weight-arrow {
    font-size: 32px;
    color: #01807B;
}

.weight-remaining {
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border-left: 4px solid #01807B;
    text-align: center;
    margin-bottom: 20px;
}

.weight-remaining-text {
    font-size: 14px;
    color: #495057;
    font-weight: 600;
    margin-bottom: 4px;
}

.weight-remaining-value {
    font-size: 24px;
    font-weight: 700;
    color: #01807B;
}

.progress-gauge-container {
    margin-bottom: 20px;
}

.progress-gauge-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-gauge-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-gauge-percent {
    font-size: 20px;
    font-weight: 700;
    color: #01807B;
}

.progress-gauge-bar {
    position: relative;
    height: 20px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-gauge-fill {
    height: 100%;
    background: linear-gradient(90deg, #01807B 0%, #019B95 100%);
    border-radius: 10px;
    transition: width 1s ease-out;
    position: relative;
}

.progress-gauge-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.weight-motivation-message {
    padding: 16px 20px;
    border-radius: 12px;
    font-size: 15px;
    line-height: 1.6;
    font-weight: 500;
    text-align: center;
}

.weight-motivation-message.achieved {
    background: linear-gradient(135deg, #e6ffed 0%, #d4fce3 100%);
    border: 2px solid #48bb78;
    color: #1e6b39;
}

.weight-motivation-message.on-track {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 2px solid #4299e1;
    color: #1e40af;
}

.weight-motivation-message.ahead {
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    border: 2px solid #9f7aea;
    color: #5b21b6;
}

.weight-motivation-message.behind {
    background: linear-gradient(135deg, #fff4e6 0%, #ffe8cc 100%);
    border: 2px solid #F3911D;
    color: #92400e;
}

.weight-motivation-message.no-data {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    color: #6c757d;
}

.weight-motivation-message strong {
    font-weight: 700;
}

/* Other Stats Cards */
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

<?php
// === Weight Goal Progress Calculation ===
$current_weight = $patient->latest_measurement ? $patient->latest_measurement->weight : null;
$target_weight = $patient->target_weight;
$initial_weight = $weight_progress->initial_weight;

// Calculate remaining weight and determine goal type
$weight_remaining = null;
$goal_type = 'lose'; // 'lose', 'gain', or 'maintain'
if ($current_weight && $target_weight) {
    $weight_remaining = abs($current_weight - $target_weight);

    // Determine if client needs to lose or gain weight
    if ($current_weight > $target_weight) {
        $goal_type = 'lose';
    } elseif ($current_weight < $target_weight) {
        $goal_type = 'gain';
    } else {
        $goal_type = 'maintain';
    }
}

// Calculate progress percentage
// % = (Initial - Current) / (Initial - Target) * 100
$progress_percent = 0;
if ($initial_weight && $current_weight && $target_weight && $initial_weight != $target_weight) {
    $weight_lost = $initial_weight - $current_weight;
    $total_to_lose = $initial_weight - $target_weight;
    $progress_percent = ($weight_lost / $total_to_lose) * 100;
    $progress_percent = max(0, min(100, $progress_percent)); // Clamp between 0-100
}

// Calculate timeline status if program has dates
$timeline_status = 'no-data';
$timeline_message = '';
$weeks_elapsed = 0;
$weeks_total = 0;
$expected_loss = 0;
$actual_loss = 0;

if ($active_program && $active_program->start_date && $active_program->end_date) {
    $start = new DateTime($active_program->start_date);
    $end = new DateTime($active_program->end_date);
    $now = new DateTime();

    $total_days = $start->diff($end)->days;
    $elapsed_days = $start->diff($now)->days;

    $weeks_total = ceil($total_days / 7);
    $weeks_elapsed = ceil($elapsed_days / 7);

    if ($weeks_total > 0 && $initial_weight && $target_weight) {
        // Expected weekly loss rate
        $total_to_lose = $initial_weight - $target_weight;
        $weekly_loss_rate = $total_to_lose / $weeks_total;

        // Expected loss by now
        $expected_loss = $weekly_loss_rate * $weeks_elapsed;

        // Actual loss
        $actual_loss = $initial_weight - $current_weight;

        // Determine status
        $difference = $actual_loss - $expected_loss;

        if ($difference >= 0.5) {
            $timeline_status = 'ahead';
        } elseif ($difference <= -0.5) {
            $timeline_status = 'behind';
        } else {
            $timeline_status = 'on-track';
        }
    }
}

// Determine overall status
$overall_status = 'no-data';
$status_icon = 'fa-circle';

if (!$current_weight || !$target_weight) {
    $overall_status = 'no-data';
    $status_icon = 'fa-exclamation-circle';
} elseif ($progress_percent >= 100) {
    $overall_status = 'achieved';
    $status_icon = 'fa-trophy';
} elseif ($progress_percent >= 80 || $weight_remaining <= 2) {
    $overall_status = 'achieved'; // Close enough
    $status_icon = 'fa-check-circle';
} else {
    $overall_status = $timeline_status;
    if ($timeline_status == 'ahead') {
        $status_icon = 'fa-rocket';
    } elseif ($timeline_status == 'on-track') {
        $status_icon = 'fa-check';
    } elseif ($timeline_status == 'behind') {
        $status_icon = 'fa-clock';
    } else {
        $status_icon = 'fa-circle';
    }
}

// Generate motivation message
$motivation_message = '';
if (!$current_weight || !$target_weight) {
    $motivation_message = 'Entrez votre poids de la semaine pour mettre à jour votre progression.';
} else {
    // Calculate difference WITH direction for better messaging
    $weight_diff = $current_weight - $target_weight;
    $total_change = $initial_weight ? abs($current_weight - $initial_weight) : 0;

    if ($goal_type == 'lose') {
        if ($weight_diff > 0.5) {
            // Pas encore atteint
            if ($weight_remaining <= 2) {
                $motivation_message = '<strong>Presque au but</strong> — plus que ' . number_format($weight_diff, 1) . ' kg à perdre.';
            } elseif ($timeline_status == 'ahead') {
                $ahead_percent = abs(round(($actual_loss - $expected_loss) / $expected_loss * 100));
                $motivation_message = '<strong>Excellent !</strong> Vous devancez le planning de ' . $ahead_percent . ' %.';
            } elseif ($timeline_status == 'on-track') {
                $end_date = $active_program && $active_program->end_date ? date('d/m', strtotime($active_program->end_date)) : '';
                $motivation_message = '<strong>Solide !</strong> Vous êtes dans les temps' . ($end_date ? ' pour le ' . $end_date : '') . '.';
            } elseif ($timeline_status == 'behind') {
                $behind_kg = abs(round($actual_loss - $expected_loss, 1));
                $motivation_message = '<strong>Courage</strong> — encore ' . $behind_kg . ' kg à rattraper pour revenir dans les temps.';
            } else {
                $motivation_message = '<strong>Continue comme ça !</strong> Vous progressez vers votre objectif.';
            }
        } elseif (abs($weight_diff) <= 0.5) {
            // Objectif atteint
            $motivation_message = '🎉 <strong>Félicitations !</strong> Objectif atteint avec ' . number_format($total_change, 1) . ' kg perdus !';
        } else {
            // Objectif dépassé
            $excess = abs($weight_diff);
            $motivation_message = '🎉 <strong>Incroyable !</strong> Objectif dépassé ! Vous avez perdu ' . number_format($total_change, 1) . ' kg, soit ' . number_format($excess, 1) . ' kg de plus que l\'objectif.';
        }
    } elseif ($goal_type == 'gain') {
        $weight_diff_gain = $target_weight - $current_weight;
        if ($weight_diff_gain > 0.5) {
            // Pas encore atteint
            if ($weight_diff_gain <= 2) {
                $motivation_message = '<strong>Presque au but</strong> — plus que ' . number_format($weight_diff_gain, 1) . ' kg à gagner.';
            } else {
                $motivation_message = '<strong>Continue comme ça !</strong> Vous progressez vers votre objectif.';
            }
        } elseif (abs($weight_diff_gain) <= 0.5) {
            // Objectif atteint
            $motivation_message = '🎉 <strong>Félicitations !</strong> Objectif atteint avec ' . number_format($total_change, 1) . ' kg gagnés !';
        } else {
            // Objectif dépassé
            $excess = abs($weight_diff_gain);
            $motivation_message = '🎉 <strong>Incroyable !</strong> Objectif dépassé ! Vous avez gagné ' . number_format($total_change, 1) . ' kg, soit ' . number_format($excess, 1) . ' kg de plus que l\'objectif.';
        }
    } else {
        $motivation_message = '🎉 <strong>Parfait !</strong> Poids maintenu.';
    }
}
?>

<!-- Weight Goal Card -->
<div class="weight-goal-card">
    <div class="weight-goal-header">
        <div class="weight-goal-title">
            <i class="fa fa-bullseye"></i>
            Objectif de poids
        </div>
        <div class="weight-goal-status <?php echo $overall_status; ?>">
            <i class="fa <?php echo $status_icon; ?>"></i>
            <?php
            // Déterminer si objectif est atteint ou dépassé
            $weight_diff_status = $current_weight - $target_weight;
            $is_exceeded = false;

            if ($goal_type == 'lose' && $weight_diff_status < -0.5) {
                $is_exceeded = true;
            } elseif ($goal_type == 'gain' && $weight_diff_status > 0.5) {
                $is_exceeded = true;
            }

            if ($is_exceeded) {
                echo 'Objectif dépassé';
            } elseif ($overall_status == 'achieved') {
                echo 'Objectif atteint';
            } elseif ($overall_status == 'ahead') {
                echo 'En avance';
            } elseif ($overall_status == 'on-track') {
                echo 'Dans les temps';
            } elseif ($overall_status == 'behind') {
                echo 'En retard';
            } else {
                echo 'En cours';
            }
            ?>
        </div>
    </div>

    <div class="weight-values">
        <div class="weight-value-item">
            <div class="weight-value-label">Poids actuel</div>
            <div class="weight-value-number">
                <?php echo $current_weight ? number_format($current_weight, 1) : '-'; ?>
                <span class="weight-value-unit">kg</span>
            </div>
        </div>

        <div class="weight-arrow">
            <i class="fa fa-arrow-right"></i>
        </div>

        <div class="weight-value-item">
            <div class="weight-value-label">Poids cible</div>
            <div class="weight-value-number">
                <?php echo $target_weight ? number_format($target_weight, 1) : '-'; ?>
                <span class="weight-value-unit">kg</span>
            </div>
        </div>
    </div>

    <?php if ($weight_remaining !== null) { ?>
    <div class="weight-remaining">
        <div class="weight-remaining-text">
            <?php
            // Calculate difference WITH direction (not abs)
            $weight_diff = $current_weight - $target_weight;
            $total_change = $initial_weight ? abs($current_weight - $initial_weight) : 0;

            if ($goal_type == 'lose') {
                // Objectif = PERDRE du poids
                if ($weight_diff > 0.5) {
                    // Pas encore atteint
                    echo 'Encore à perdre';
                } elseif (abs($weight_diff) <= 0.5) {
                    // Objectif atteint (dans la marge de 0.5kg)
                    echo 'Objectif atteint !';
                } else {
                    // Dépassé (poids actuel < poids cible)
                    echo 'Objectif dépassé !';
                }
            } elseif ($goal_type == 'gain') {
                // Objectif = PRENDRE du poids
                if ($weight_diff < -0.5) {
                    // Pas encore atteint
                    echo 'À rattraper';
                } elseif (abs($weight_diff) <= 0.5) {
                    // Objectif atteint
                    echo 'Objectif atteint !';
                } else {
                    // Dépassé (poids actuel > poids cible)
                    echo 'Objectif dépassé !';
                }
            } else {
                echo 'Objectif maintenu';
            }
            ?>
        </div>
        <div class="weight-remaining-value">
            <?php
            if ($goal_type == 'lose') {
                if ($weight_diff > 0.5) {
                    // Pas atteint : afficher kg restant à perdre
                    echo number_format($weight_diff, 1) . ' kg restants';
                } elseif (abs($weight_diff) <= 0.5) {
                    // Atteint : afficher total perdu + différence exacte avec objectif
                    echo '🎉 ' . number_format($total_change, 1) . ' kg perdus';
                    if (abs($weight_diff) > 0) {
                        if ($weight_diff > 0) {
                            // Encore un peu à perdre (ex: 100kg pour objectif 99kg = +1kg)
                            echo '<br><small style="font-size:14px; color: #ff9800;">(encore ' . number_format($weight_diff, 1) . ' kg)</small>';
                        } else {
                            // Un peu en dessous (ex: 98kg pour objectif 99kg = -1kg)
                            echo '<br><small style="font-size:14px; color: #4caf50;">(' . number_format(abs($weight_diff), 1) . ' kg de moins)</small>';
                        }
                    }
                } else {
                    // Dépassé : afficher total perdu + excédent
                    $excess = abs($weight_diff);
                    echo '🎉 ' . number_format($total_change, 1) . ' kg perdus<br><small style="font-size:14px">(+' . number_format($excess, 1) . ' kg de plus)</small>';
                }
            } elseif ($goal_type == 'gain') {
                $weight_diff_gain = $target_weight - $current_weight; // Pour gain, on inverse
                if ($weight_diff_gain > 0.5) {
                    // Pas atteint : afficher kg restant à gagner
                    echo number_format($weight_diff_gain, 1) . ' kg restants';
                } elseif (abs($weight_diff_gain) <= 0.5) {
                    // Atteint : afficher total gagné + différence exacte avec objectif
                    echo '🎉 ' . number_format($total_change, 1) . ' kg gagnés';
                    if (abs($weight_diff_gain) > 0) {
                        if ($weight_diff_gain > 0) {
                            // Encore un peu à gagner
                            echo '<br><small style="font-size:14px; color: #ff9800;">(encore ' . number_format($weight_diff_gain, 1) . ' kg)</small>';
                        } else {
                            // Un peu au-dessus
                            echo '<br><small style="font-size:14px; color: #4caf50;">(' . number_format(abs($weight_diff_gain), 1) . ' kg de plus)</small>';
                        }
                    }
                } else {
                    // Dépassé : afficher total gagné + excédent
                    $excess = abs($weight_diff_gain);
                    echo '🎉 ' . number_format($total_change, 1) . ' kg gagnés<br><small style="font-size:14px">(+' . number_format($excess, 1) . ' kg de plus)</small>';
                }
            } else {
                echo '🎉';
            }
            ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($current_weight && $target_weight) { ?>
    <div class="progress-gauge-container">
        <div class="progress-gauge-header">
            <div class="progress-gauge-label">Progression</div>
            <div class="progress-gauge-percent"><?php echo round($progress_percent); ?>%</div>
        </div>
        <div class="progress-gauge-bar">
            <div class="progress-gauge-fill" style="width: <?php echo round($progress_percent); ?>%;"></div>
        </div>
    </div>
    <?php } ?>

    <div class="weight-motivation-message <?php echo $overall_status; ?>">
        <?php echo $motivation_message; ?>
    </div>
</div>

<!-- Other Stats Cards -->
<div class="stats-grid">
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
            <div style="margin-top: 20px; text-align: center;">
                <a href="<?php echo site_url('dietetic/portal/statistics'); ?>" class="btn-flat btn-flat-primary">
                    <i class="fa fa-chart-line"></i> Voir les statistiques détaillées
                </a>
            </div>
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
