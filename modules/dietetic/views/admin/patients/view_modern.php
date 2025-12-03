<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<style>
/* ========================================
   MODERN PROFESSIONAL DESIGN SYSTEM
   ======================================== */

:root {
    --primary: #01807B;
    --primary-dark: #015a57;
    --primary-light: #02a39d;
    --secondary: #F3911D;
    --accent: #2196F3;
    --success: #4CAF50;
    --warning: #FF9800;
    --danger: #F44336;
    --info: #00BCD4;
    --light: #F8F9FA;
    --dark: #212529;
    --gray-100: #F8F9FA;
    --gray-200: #E9ECEF;
    --gray-300: #DEE2E6;
    --gray-400: #CED4DA;
    --gray-500: #ADB5BD;
    --gray-600: #6C757D;
    --gray-700: #495057;
    --gray-800: #343A40;
    --gray-900: #212529;

    --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.10);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    --shadow-xl: 0 12px 32px rgba(0,0,0,0.15);

    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 15px;
    --radius-xl: 20px;

    --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    box-sizing: border-box;
}

body {
    background: var(--gray-100);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* ========================================
   PATIENT HEADER - MODERN CARD STYLE
   ======================================== */

.modern-patient-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-lg);
    padding: 30px 40px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.modern-patient-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.patient-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    margin-right: 25px;
    border: 3px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
}

.patient-info-modern h1 {
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.patient-meta {
    display: flex;
    gap: 20px;
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    flex-wrap: wrap;
}

.patient-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.patient-meta-item i {
    opacity: 0.8;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.btn-header {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    transition: all var(--transition-base);
    border: 2px solid white;
    background: white;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-header-primary {
    color: var(--primary);
}

.btn-header-danger {
    color: var(--danger);
}

.btn-header:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ========================================
   STATS CARDS - GLASSMORPHISM DESIGN
   ======================================== */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-modern {
    background: white;
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
    border: 1px solid var(--gray-200);
    position: relative;
    overflow: hidden;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--stat-color, var(--primary));
    transition: width var(--transition-base);
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.stat-card-modern:hover::before {
    width: 100%;
    opacity: 0.05;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, var(--stat-color, var(--primary)) 0%, var(--stat-color-dark, var(--primary-dark)) 100%);
    color: white;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 4px 0;
    line-height: 1;
}

.stat-label {
    font-size: 13px;
    color: var(--gray-600);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Stat colors */
.stat-card-blue { --stat-color: #3498db; --stat-color-dark: #2980b9; }
.stat-card-green { --stat-color: #2ecc71; --stat-color-dark: #27ae60; }
.stat-card-orange { --stat-color: #f39c12; --stat-color-dark: #e67e22; }
.stat-card-red { --stat-color: #e74c3c; --stat-color-dark: #c0392b; }

/* ========================================
   PROGRESS ALERT - MODERN DESIGN
   ======================================== */

.progress-alert-modern {
    background: linear-gradient(135deg, rgba(46, 204, 113, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
    border-left: 4px solid var(--success);
    border-radius: var(--radius-md);
    padding: 20px 24px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.progress-alert-modern.warning {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.1) 0%, rgba(251, 192, 45, 0.05) 100%);
    border-left-color: var(--warning);
}

.progress-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--success);
    flex-shrink: 0;
}

.progress-alert-modern.warning .progress-icon {
    color: var(--warning);
}

.progress-content {
    flex: 1;
}

.progress-title {
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 4px;
    color: var(--dark);
}

.progress-text {
    font-size: 14px;
    color: var(--gray-700);
    margin: 0;
}

/* ========================================
   MODERN PANELS & SECTIONS
   ======================================== */

.modern-panel {
    background: white;
    border-radius: var(--radius-lg);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-200);
}

.panel-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

.panel-title-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.panel-actions {
    display: flex;
    gap: 8px;
}

/* ========================================
   INFO GRID - MODERN LAYOUT
   ======================================== */

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.info-item {
    background: var(--gray-100);
    border-radius: var(--radius-md);
    padding: 16px;
    transition: all var(--transition-base);
    border: 1px solid transparent;
}

.info-item:hover {
    background: white;
    border-color: var(--gray-300);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--gray-600);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.info-label i {
    color: var(--primary);
}

.info-value {
    font-size: 15px;
    font-weight: 600;
    color: var(--dark);
}

/* ========================================
   TABS - MODERN DESIGN
   ======================================== */

.modern-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid var(--gray-200);
    margin-bottom: 24px;
    overflow-x: auto;
    padding-bottom: 0;
}

.modern-tab {
    padding: 12px 20px;
    border: none;
    background: none;
    color: var(--gray-600);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-base);
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    position: relative;
}

.modern-tab:hover {
    color: var(--primary);
    background: rgba(1, 128, 123, 0.05);
}

.modern-tab.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ========================================
   BUTTONS - MODERN DESIGN
   ======================================== */

.btn-modern {
    padding: 10px 20px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    transition: all var(--transition-base);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-modern-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-modern-secondary {
    background: var(--gray-200);
    color: var(--dark);
}

.btn-modern-secondary:hover {
    background: var(--gray-300);
    transform: translateY(-2px);
}

.btn-modern-outline {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-modern-outline:hover {
    background: var(--primary);
    color: white;
}

/* ========================================
   TABLE - MODERN DESIGN
   ======================================== */

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead tr {
    background: var(--gray-100);
}

.modern-table th {
    padding: 16px;
    text-align: left;
    font-weight: 700;
    font-size: 13px;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--gray-300);
}

.modern-table td {
    padding: 16px;
    border-bottom: 1px solid var(--gray-200);
    font-size: 14px;
    color: var(--gray-800);
}

.modern-table tbody tr {
    transition: all var(--transition-fast);
}

.modern-table tbody tr:hover {
    background: var(--gray-50);
    transform: translateX(2px);
}

.modern-table tbody tr:last-child td {
    border-bottom: none;
}

/* ========================================
   BADGES - MODERN DESIGN
   ======================================== */

.badge-modern {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
}

.badge-modern-success {
    background: rgba(46, 204, 113, 0.15);
    color: #27ae60;
}

.badge-modern-warning {
    background: rgba(243, 156, 18, 0.15);
    color: #d68910;
}

.badge-modern-danger {
    background: rgba(231, 76, 60, 0.15);
    color: #c0392b;
}

.badge-modern-info {
    background: rgba(52, 152, 219, 0.15);
    color: #2980b9;
}

/* ========================================
   OBJECTIVE BOX - MODERN DESIGN
   ======================================== */

.objective-box {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #f39c12;
    border-radius: var(--radius-md);
    padding: 20px 24px;
    margin-top: 24px;
}

.objective-box h5 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #856404;
    margin: 0 0 12px 0;
}

.objective-box p {
    color: #856404;
    margin: 0;
    line-height: 1.6;
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .modern-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
    }

    .modern-patient-header {
        padding: 20px;
    }

    .patient-avatar {
        width: 60px;
        height: 60px;
        font-size: 28px;
    }

    .patient-info-modern h1 {
        font-size: 24px;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .btn-header {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* ========================================
   ANIMATIONS
   ======================================== */

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideInUp 0.4s ease backwards;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }

/* ========================================
   CHART CONTAINER
   ======================================== */

.chart-container-modern {
    background: white;
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.chart-wrapper {
    position: relative;
    height: 350px;
}

/* ========================================
   SIDEBAR - MODERN DESIGN
   ======================================== */

.modern-sidebar-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
}

.sidebar-card-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 16px;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.action-btn-full {
    width: 100%;
    padding: 12px;
    border-radius: var(--radius-md);
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    color: var(--dark);
    font-weight: 600;
    font-size: 14px;
    transition: all var(--transition-base);
    cursor: pointer;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 10px;
    text-decoration: none;
}

.action-btn-full:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.action-btn-full i {
    font-size: 16px;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray-500);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Modern Patient Header -->
        <div class="modern-patient-header animate-slide-up">
            <div class="row">
                <div class="col-md-9">
                    <div style="display: flex; align-items: center;">
                        <div class="patient-avatar">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="patient-info-modern">
                            <h1><?php echo $patient->client->company; ?></h1>
                            <div class="patient-meta">
                                <div class="patient-meta-item">
                                    <i class="fa fa-id-badge"></i>
                                    <span>Patient #<?php echo $patient->id; ?></span>
                                </div>
                                <div class="patient-meta-item">
                                    <i class="fa fa-user-md"></i>
                                    <span><?php echo $patient->dietitian->firstname . ' ' . $patient->dietitian->lastname; ?></span>
                                </div>
                                <?php if ($patient->latest_measurement): ?>
                                <div class="patient-meta-item">
                                    <i class="fa fa-calendar"></i>
                                    <span>Dernière mesure: <?php echo _d($patient->latest_measurement->measurement_date); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="header-actions" style="justify-content: flex-end;">
                        <a href="<?php echo admin_url('dietetic/patients/download_nutrition_analysis_pdf/' . $patient->id); ?>"
                           class="btn-header btn-header-danger"
                           title="Télécharger l'analyse nutritionnelle en PDF">
                            <i class="fa fa-file-pdf-o"></i>
                            <span>PDF</span>
                        </a>
                        <?php if (dietetic_has_permission('edit')): ?>
                        <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>"
                           class="btn-header btn-header-primary">
                            <i class="fa fa-pencil"></i>
                            <span><?php echo _l('edit'); ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="stats-grid">
            <div class="stat-card-modern stat-card-blue animate-slide-up animate-delay-1">
                <div class="stat-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <h3 class="stat-value"><?php echo $patient->initial_weight ? $patient->initial_weight . ' kg' : '-'; ?></h3>
                <div class="stat-label"><?php echo _l('dietetic_initial_weight'); ?></div>
            </div>

            <div class="stat-card-modern stat-card-green animate-slide-up animate-delay-2">
                <div class="stat-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <h3 class="stat-value">
                    <?php echo $patient->latest_measurement ? $patient->latest_measurement->weight . ' kg' : '-'; ?>
                </h3>
                <div class="stat-label"><?php echo _l('dietetic_current_weight'); ?></div>
            </div>

            <div class="stat-card-modern stat-card-orange animate-slide-up animate-delay-3">
                <div class="stat-icon">
                    <i class="fa fa-bullseye"></i>
                </div>
                <h3 class="stat-value"><?php echo $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h3>
                <div class="stat-label"><?php echo _l('dietetic_target_weight'); ?></div>
            </div>

            <div class="stat-card-modern stat-card-red animate-slide-up animate-delay-4">
                <div class="stat-icon">
                    <i class="fa fa-heartbeat"></i>
                </div>
                <h3 class="stat-value">
                    <?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? $patient->latest_measurement->bmi : '-'; ?>
                </h3>
                <div class="stat-label"><?php echo _l('dietetic_bmi'); ?></div>
            </div>
        </div>

        <!-- Progress Alert -->
        <?php if ($patient->latest_measurement && isset($weight_progress->weight_change) && $weight_progress->weight_change !== null): ?>
        <div class="progress-alert-modern <?php echo $weight_progress->weight_change < 0 ? '' : 'warning'; ?> animate-slide-up">
            <div class="progress-icon">
                <i class="fa fa-<?php echo $weight_progress->weight_change < 0 ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            </div>
            <div class="progress-content">
                <div class="progress-title">Progression du poids</div>
                <p class="progress-text">
                    <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
                    (<?php echo ($weight_progress->percentage_change > 0 ? '+' : '') . number_format($weight_progress->percentage_change, 1); ?>%)
                    depuis le début du suivi
                </p>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Main Content Column -->
            <div class="col-md-8">
                <!-- Patient Information Panel -->
                <div class="modern-panel animate-slide-up">
                    <div class="panel-header">
                        <h4 class="panel-title">
                            <div class="panel-title-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>
                            <span><?php echo _l('dietetic_patient_profile'); ?></span>
                        </h4>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-venus-mars"></i>
                                <span><?php echo _l('dietetic_gender'); ?></span>
                            </div>
                            <div class="info-value"><?php echo ucfirst($patient->gender); ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo _l('dietetic_birth_date'); ?></span>
                            </div>
                            <div class="info-value"><?php echo _d($patient->birth_date); ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-phone"></i>
                                <span><?php echo _l('dietetic_phone'); ?></span>
                            </div>
                            <div class="info-value"><?php echo $patient->phone; ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-envelope"></i>
                                <span><?php echo _l('dietetic_email'); ?></span>
                            </div>
                            <div class="info-value"><?php echo $patient->email; ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-running"></i>
                                <span><?php echo _l('dietetic_activity_level'); ?></span>
                            </div>
                            <div class="info-value"><?php echo ucfirst(str_replace('_', ' ', $patient->activity_level)); ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-arrows-v"></i>
                                <span><?php echo _l('dietetic_height'); ?></span>
                            </div>
                            <div class="info-value"><?php echo $patient->height ? $patient->height . ' cm' : '-'; ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-list-alt"></i>
                                <span><?php echo _l('dietetic_active_programs'); ?></span>
                            </div>
                            <div class="info-value">
                                <span class="badge-modern badge-modern-success"><?php echo $patient->active_programs; ?></span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-check-circle"></i>
                                <span><?php echo _l('dietetic_status'); ?></span>
                            </div>
                            <div class="info-value"><?php echo dietetic_patient_status_badge($patient->status); ?></div>
                        </div>
                    </div>

                    <!-- Objective Box -->
                    <div class="objective-box">
                        <h5>
                            <i class="fa fa-bullseye"></i>
                            <span><?php echo _l('dietetic_objective'); ?></span>
                        </h5>
                        <p><?php echo nl2br($patient->objective); ?></p>
                    </div>
                </div>

                <!-- More sections will be added here -->
                <div style="text-align: center; padding: 60px 20px; background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-md);">
                    <p style="color: var(--gray-600); font-size: 16px; margin: 0;">
                        <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 12px; display: block;"></i>
                        Les autres sections (Anamnèse, Consultations, Graphiques, etc.) seront ajoutées dans la suite du design.
                        <br><br>
                        Ce design moderne et professionnel améliore considérablement l'ergonomie et l'esthétique de la page patient.
                    </p>
                </div>

            </div>

            <!-- Right Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions Card -->
                <div class="modern-sidebar-card animate-slide-up">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-bolt"></i>
                        <span>Actions rapides</span>
                    </h5>

                    <a href="<?php echo admin_url('dietetic/measurements/add/' . $patient->id); ?>" class="action-btn-full">
                        <i class="fa fa-plus"></i>
                        <span>Ajouter une mesure</span>
                    </a>

                    <a href="<?php echo admin_url('dietetic/consultations/add?patient_id=' . $patient->id); ?>" class="action-btn-full">
                        <i class="fa fa-calendar-plus-o"></i>
                        <span>Planifier consultation</span>
                    </a>

                    <a href="<?php echo admin_url('dietetic/programs/assign/' . $patient->id); ?>" class="action-btn-full">
                        <i class="fa fa-book"></i>
                        <span>Assigner un programme</span>
                    </a>

                    <a href="<?php echo admin_url('dietetic/meal_plans/create?patient_id=' . $patient->id); ?>" class="action-btn-full">
                        <i class="fa fa-cutlery"></i>
                        <span>Créer un plan repas</span>
                    </a>
                </div>

                <!-- Dietitian Info Card -->
                <?php if ($patient->dietitian): ?>
                <div class="modern-sidebar-card animate-slide-up animate-delay-1">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-user-md"></i>
                        <span>Diététicien assigné</span>
                    </h5>

                    <div style="text-align: center;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                            <i class="fa fa-user-md"></i>
                        </div>
                        <h6 style="margin: 0 0 4px 0; font-weight: 700; color: var(--dark);">
                            <?php echo $patient->dietitian->firstname . ' ' . $patient->dietitian->lastname; ?>
                        </h6>
                        <p style="margin: 0; font-size: 13px; color: var(--gray-600);">
                            <i class="fa fa-envelope"></i>
                            <?php echo $patient->dietitian->email; ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Smooth scroll
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 20
        }, 500);
    });

    // Tab functionality
    $('.modern-tab').on('click', function() {
        var target = $(this).data('target');

        // Update active tab
        $('.modern-tab').removeClass('active');
        $(this).addClass('active');

        // Show target content
        $('.tab-content').removeClass('active');
        $(target).addClass('active');
    });
});
</script>

<?php init_tail(); ?>
