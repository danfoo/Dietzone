<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<style>
/* Modern Patient List Styles with Mobile-First Approach */
:root {
    --primary-color: #01807B;
    --secondary-color: #F3911D;
    --tertiary-color: #FFFFFF;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
    --background-light: #f8f9fa;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Header */
.patients-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 32px 24px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.patients-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.patients-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.patients-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

.btn-new-patient {
    background: white;
    color: var(--primary-color);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
    position: relative;
    z-index: 1;
}

.btn-new-patient:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: var(--primary-color);
}

/* Statistics Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border-left: 4px solid var(--primary-color);
    position: relative;
    overflow: hidden;
}

.stat-card::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.05) 100%);
    border-radius: 0 12px 0 100%;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.stat-card.orange {
    border-left-color: var(--secondary-color);
}

.stat-card.blue {
    border-left-color: #3498db;
}

.stat-card.purple {
    border-left-color: #9b59b6;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, rgba(1, 128, 123, 0.1) 0%, rgba(1, 128, 123, 0.05) 100%);
    color: var(--primary-color);
}

.stat-card.orange .stat-icon {
    background: linear-gradient(135deg, rgba(243, 145, 29, 0.1) 0%, rgba(243, 145, 29, 0.05) 100%);
    color: var(--secondary-color);
}

.stat-card.blue .stat-icon {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
    color: #3498db;
}

.stat-card.purple .stat-icon {
    background: linear-gradient(135deg, rgba(155, 89, 182, 0.1) 0%, rgba(155, 89, 182, 0.05) 100%);
    color: #9b59b6;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
    line-height: 1;
}

.stat-label {
    font-size: 13px;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* Patients Table Container */
.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
}

.table-header {
    padding: 20px 24px;
    border-bottom: 3px solid var(--primary-color);
    background: linear-gradient(to right, rgba(1, 128, 123, 0.03) 0%, transparent 100%);
}

.table-header h4 {
    margin: 0;
    color: var(--text-dark);
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-header h4 i {
    color: var(--primary-color);
}

/* Enhanced Table */
.patients-table-wrapper {
    padding: 24px;
}

#patients-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#patients-table thead tr {
    background: var(--background-light);
}

#patients-table thead th {
    padding: 16px 12px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-light);
    border: none;
    white-space: nowrap;
}

#patients-table thead th i {
    margin-right: 4px;
}

#patients-table tbody tr {
    transition: var(--transition);
    cursor: pointer;
    border-bottom: 1px solid #ecf0f1;
}

#patients-table tbody tr:hover {
    background: linear-gradient(to right, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.02) 100%);
    transform: translateX(4px);
}

#patients-table tbody td {
    padding: 16px 12px;
    vertical-align: middle;
    border: none;
}

.patient-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.patient-name i {
    color: var(--primary-color);
    font-size: 18px;
}

.dietitian-info {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-light);
}

.dietitian-info i {
    color: var(--secondary-color);
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-badge.patient-active {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.status-badge.patient-inactive {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

.status-badge.patient-completed {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

/* Metric Badges */
.metric-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.weight-badge {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.bmi-value {
    font-weight: 700;
    font-size: 14px;
}

.bmi-normal { color: #27ae60; }
.bmi-underweight { color: #3498db; }
.bmi-overweight { color: #f39c12; }
.bmi-obese { color: #e74c3c; }

.activity-badge {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: var(--transition);
    font-size: 14px;
}

.action-btn.view {
    background: rgba(1, 128, 123, 0.1);
    color: var(--primary-color);
}

.action-btn.view:hover {
    background: var(--primary-color);
    color: white;
}

.action-btn.edit {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.action-btn.edit:hover {
    background: var(--secondary-color);
    color: white;
}

.action-btn.delete {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.action-btn.delete:hover {
    background: #e74c3c;
    color: white;
}

/* Quick Actions Panel */
.quick-actions {
    background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.05) 100%);
    border-radius: 12px;
    padding: 24px;
    border-left: 4px solid var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.quick-actions h5 {
    margin: 0 0 16px 0;
    color: var(--text-dark);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.quick-actions h5 i {
    color: var(--primary-color);
}

.action-buttons-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.action-btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.action-btn-primary:hover {
    background: #016663;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.action-btn-secondary {
    background: var(--secondary-color);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.action-btn-secondary:hover {
    background: #d97e0f;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.action-btn-info {
    background: #3498db;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.action-btn-info:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.quick-actions-hint {
    color: var(--text-light);
    font-size: 13px;
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Search Box Styling */
#patient-search:focus {
    border-color: var(--secondary-color) !important;
    box-shadow: 0 0 12px rgba(1, 128, 123, 0.4) !important;
    outline: none;
}

/* Responsive Mobile-First */
@media (max-width: 768px) {
    .patients-header {
        padding: 20px 16px;
    }

    .patients-header h1 {
        font-size: 22px;
    }

    .btn-new-patient {
        width: 100%;
        margin-top: 12px;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .stat-card {
        padding: 16px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        font-size: 24px;
    }

    .stat-value {
        font-size: 24px;
    }

    .table-container {
        overflow-x: auto;
    }

    .patients-table-wrapper {
        padding: 16px;
    }

    #patients-table {
        font-size: 13px;
    }

    #patients-table thead th,
    #patients-table tbody td {
        padding: 12px 8px;
    }

    .action-buttons-group {
        flex-direction: column;
    }

    .action-btn-primary,
    .action-btn-secondary,
    .action-btn-info {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="patients-header">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h1>
                                <i class="fa fa-users"></i>
                                Gestion des Patients
                            </h1>
                            <p>
                                <i class="fa fa-heartbeat"></i> Suivez et gérez tous vos patients diététiques
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-new-patient">
                                    <i class="fa fa-user-plus"></i> Nouveau Patient
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-value"><?php echo $total_patients; ?></div>
                <div class="stat-label">Total Patients</div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $active_count = 0;
                    foreach ($patients as $p) {
                        if ($p->status === 'active') $active_count++;
                    }
                    echo $active_count;
                    ?>
                </div>
                <div class="stat-label">Patients Actifs (page)</div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $today_count = 0;
                    foreach ($patients as $p) {
                        if ($p->created_at && date('Y-m-d', strtotime($p->created_at)) === date('Y-m-d')) {
                            $today_count++;
                        }
                    }
                    echo $today_count;
                    ?>
                </div>
                <div class="stat-label">Nouveaux Aujourd'hui</div>
            </div>

            <div class="stat-card purple">
                <div class="stat-icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $dietitians = array();
                    foreach ($patients as $p) {
                        if ($p->dietitian_name && !in_array($p->dietitian_name, $dietitians)) {
                            $dietitians[] = $p->dietitian_name;
                        }
                    }
                    echo count($dietitians);
                    ?>
                </div>
                <div class="stat-label">Diététiciens (page)</div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row">
            <div class="col-md-12">
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 24px;">
                    <div style="position: relative;">
                        <input type="text" id="patient-search" class="form-control" placeholder="🔍 Rechercher un patient par nom, diététicien, statut..." style="padding: 15px 50px 15px 20px; font-size: 16px; border: 2px solid var(--primary-color); border-radius: 25px; box-shadow: 0 2px 8px rgba(1, 128, 123, 0.2);">
                        <i class="fa fa-search" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: var(--primary-color); font-size: 18px;"></i>
                    </div>
                    <div id="search-results-count" style="margin-top: 10px; color: var(--text-light); font-size: 14px;">
                        <i class="fa fa-info-circle"></i> <span id="visible-count"><?php echo count($patients); ?></span> patient(s) affiché(s)
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-container" id="table-wrapper">
                    <div class="table-header">
                        <h4>
                            <i class="fa fa-list"></i>
                            Liste des Patients
                        </h4>
                    </div>

                    <div class="patients-table-wrapper">
                        <table id="patients-table">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-user"></i> <?php echo _l('dietetic_client_name'); ?></th>
                                    <th><i class="fa fa-user-md"></i> <?php echo _l('dietetic_dietitian'); ?></th>
                                    <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                    <th><i class="fa fa-balance-scale"></i> <?php echo _l('dietetic_weight'); ?></th>
                                    <th><i class="fa fa-heartbeat"></i> <?php echo _l('dietetic_bmi'); ?></th>
                                    <th><i class="fa fa-running"></i> <?php echo _l('dietetic_activity_level'); ?></th>
                                    <th><i class="fa fa-clock-o"></i> <?php echo _l('created_at'); ?></th>
                                    <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($patients as $patient) { ?>
                                    <tr onclick="window.location='<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>'">
                                        <td>
                                            <div class="patient-name">
                                                <i class="fa fa-user-circle"></i>
                                                <span><?php echo htmlspecialchars($patient->client_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dietitian-info">
                                                <i class="fa fa-stethoscope"></i>
                                                <span><?php echo htmlspecialchars($patient->dietitian_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'patient-active';
                                            if ($patient->status === 'inactive') $status_class = 'patient-inactive';
                                            if ($patient->status === 'completed') $status_class = 'patient-completed';
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($patient->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($patient->initial_weight) { ?>
                                                <span class="metric-badge weight-badge">
                                                    <?php echo $patient->initial_weight; ?> kg
                                                </span>
                                            <?php } else { ?>
                                                <span style="color: #bdc3c7;">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($patient->initial_weight && $patient->height) {
                                                $bmi = dietetic_calculate_bmi($patient->initial_weight, $patient->height);
                                                $bmi_class = 'bmi-normal';
                                                if ($bmi < 18.5) $bmi_class = 'bmi-underweight';
                                                elseif ($bmi >= 25 && $bmi < 30) $bmi_class = 'bmi-overweight';
                                                elseif ($bmi >= 30) $bmi_class = 'bmi-obese';
                                            ?>
                                                <span class="bmi-value <?php echo $bmi_class; ?>">
                                                    <?php echo $bmi; ?>
                                                </span>
                                            <?php } else { ?>
                                                <span style="color: #bdc3c7;">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($patient->activity_level) { ?>
                                                <span class="activity-badge">
                                                    <?php echo ucfirst(str_replace('_', ' ', $patient->activity_level)); ?>
                                                </span>
                                            <?php } else { ?>
                                                <span style="color: #bdc3c7;">-</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <small style="color: #7f8c8d;">
                                                <?php echo _dt($patient->created_at); ?>
                                            </small>
                                        </td>
                                        <td onclick="event.stopPropagation();">
                                            <div class="action-buttons">
                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"
                                                   class="action-btn view"
                                                   title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>"
                                                       class="action-btn edit"
                                                       title="Modifier">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if (is_admin() && dietetic_has_permission('delete')) { ?>
                                                    <a href="#"
                                                       onclick="deletePatient(<?php echo $patient->id; ?>); return false;"
                                                       class="action-btn delete"
                                                       title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        Affichage de <?php echo count($patients); ?> sur <?php echo $total_patients; ?> patients
                                        (Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?>)
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <nav aria-label="Pagination des patients">
                                        <ul class="pagination pull-right" style="margin: 0;">
                                            <!-- Bouton Précédent -->
                                            <li class="<?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                                <?php if ($current_page > 1): ?>
                                                    <a href="<?php echo admin_url('dietetic/patients?page=' . ($current_page - 1)); ?>"
                                                       aria-label="Précédent">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span>&laquo;</span>
                                                <?php endif; ?>
                                            </li>

                                            <!-- Numéros de page -->
                                            <?php
                                            $range = 2;
                                            $start_page = max(1, $current_page - $range);
                                            $end_page = min($total_pages, $current_page + $range);

                                            // Première page
                                            if ($start_page > 1): ?>
                                                <li>
                                                    <a href="<?php echo admin_url('dietetic/patients?page=1'); ?>">1</a>
                                                </li>
                                                <?php if ($start_page > 2): ?>
                                                    <li class="disabled"><span>...</span></li>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Pages du milieu -->
                                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                    <a href="<?php echo admin_url('dietetic/patients?page=' . $i); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Dernière page -->
                                            <?php if ($end_page < $total_pages): ?>
                                                <?php if ($end_page < $total_pages - 1): ?>
                                                    <li class="disabled"><span>...</span></li>
                                                <?php endif; ?>
                                                <li>
                                                    <a href="<?php echo admin_url('dietetic/patients?page=' . $total_pages); ?>">
                                                        <?php echo $total_pages; ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Bouton Suivant -->
                                            <li class="<?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                                <?php if ($current_page < $total_pages): ?>
                                                    <a href="<?php echo admin_url('dietetic/patients?page=' . ($current_page + 1)); ?>"
                                                       aria-label="Suivant">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span>&raquo;</span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="no-results" style="display: none; background: white; border-radius: 12px; padding: 60px 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <i class="fa fa-search" style="font-size: 64px; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <h4 style="color: #7f8c8d; margin-bottom: 10px;">Aucun patient trouvé</h4>
                    <p style="color: #95a5a6;">Essayez de modifier votre recherche</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="quick-actions">
                    <h5>
                        <i class="fa fa-bolt"></i>
                        Actions Rapides
                    </h5>
                    <div class="action-buttons-group">
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="action-btn-primary">
                                <i class="fa fa-user-plus"></i>
                                <span>Ajouter un Patient</span>
                            </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="action-btn-info">
                            <i class="fa fa-calendar-plus-o"></i>
                            <span>Nouvelle Consultation</span>
                        </a>
                        <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="action-btn-secondary">
                            <i class="fa fa-file-text-o"></i>
                            <span>Nouveau Programme</span>
                        </a>
                    </div>
                    <div class="quick-actions-hint">
                        <i class="fa fa-info-circle"></i>
                        <span>Cliquez sur une ligne pour voir les détails du patient</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var ENABLE_DATATABLES = false; // DataTables désactivé pour éviter les conflits

    // Search functionality
    var $searchInput = $('#patient-search');
    var $rows = $('#patients-table tbody tr');
    var $tableWrapper = $('#table-wrapper');
    var $noResults = $('#no-results');

    $searchInput.on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm === '') {
            $rows.show();
            $tableWrapper.show();
            $noResults.hide();
        } else {
            var visibleCount = 0;
            $rows.each(function() {
                var $row = $(this);
                var rowText = $row.text().toLowerCase();

                if (rowText.indexOf(searchTerm) > -1) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });

            if (visibleCount === 0) {
                $tableWrapper.hide();
                $noResults.show();
            } else {
                $tableWrapper.show();
                $noResults.hide();
            }
        }

        updateResultsCount();
    });

    function updateResultsCount() {
        var visibleCount = $rows.filter(':visible').length;
        $('#visible-count').text(visibleCount);
    }

    // Fix menu toggle
    setTimeout(function() {
        $('#side-menu a[href*="dietetic"]').first().parent().find('> a').on('click', function(e) {
            var $submenu = $(this).next('ul');
            if ($submenu.length > 0) {
                e.preventDefault();
                $submenu.toggleClass('in');
                $(this).parent().toggleClass('active');
            }
        });
    }, 500);

    // Delete patient function with inline CSRF token
    window.deletePatient = function(patientId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible.')) {
            $.ajax({
                url: admin_url + 'dietetic/patients/delete/' + patientId,
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', response.message || 'Patient supprimé avec succès');
                        location.reload();
                    } else {
                        alert_float('danger', response.message || 'Erreur lors de la suppression');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete failed:', xhr.status, error);
                    if (xhr.status === 419) {
                        alert_float('danger', 'Session expirée. Veuillez rafraîchir la page et réessayer.');
                    } else {
                        alert_float('danger', 'Erreur lors de la suppression: ' + error);
                    }
                }
            });
        }
    };
});
</script>

<script src="<?php echo module_dir_url('dietetic', 'assets/js/dietetic.js'); ?>"></script>

<?php init_tail(); ?>
