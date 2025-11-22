<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Consultations List Styles with Mobile-First Approach */
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
.consultations-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 32px 24px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.consultations-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.consultations-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.consultations-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

.header-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    z-index: 1;
}

.btn-new-consultation {
    background: white;
    color: var(--primary-color);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
    text-align: center;
}

.btn-new-consultation:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: var(--primary-color);
}

.btn-calendar {
    background: transparent;
    color: white;
    border: 2px solid white;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    text-align: center;
}

.btn-calendar:hover {
    background: white;
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

.stat-card.green {
    border-left-color: #27ae60;
}

.stat-card.red {
    border-left-color: #e74c3c;
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

.stat-card.green .stat-icon {
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
    color: #27ae60;
}

.stat-card.red .stat-icon {
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.1) 0%, rgba(231, 76, 60, 0.05) 100%);
    color: #e74c3c;
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

/* Table Container */
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
.consultations-table-wrapper {
    padding: 24px;
}

#consultations-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#consultations-table thead tr {
    background: var(--background-light);
}

#consultations-table thead th {
    padding: 16px 12px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-light);
    border: none;
    white-space: nowrap;
}

#consultations-table thead th i {
    margin-right: 4px;
}

#consultations-table tbody tr {
    transition: var(--transition);
    cursor: pointer;
    border-bottom: 1px solid #ecf0f1;
}

#consultations-table tbody tr:hover {
    background: linear-gradient(to right, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.02) 100%);
    transform: translateX(4px);
}

#consultations-table tbody td {
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

/* Date Badges */
.date-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.date-badge.today {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.date-badge.thisweek {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.date-badge.normal {
    color: var(--text-light);
}

/* Type Badge */
.type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
    text-transform: capitalize;
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

.status-badge.scheduled {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.status-badge.completed {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.status-badge.cancelled {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.status-badge.no_show {
    background: rgba(149, 165, 166, 0.1);
    color: #95a5a6;
}

/* Duration Badge */
.duration-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(1, 128, 123, 0.1);
    color: var(--primary-color);
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
    margin-bottom: 24px;
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

/* Legend Panel */
.legend-panel {
    background: white;
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: var(--shadow-sm);
}

.legend-panel h5 {
    margin: 0 0 16px 0;
    color: var(--text-dark);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.legend-panel h5 i {
    color: var(--primary-color);
}

.legend-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Filter Tabs */
.filter-tabs {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
}

.filter-tabs .nav-tabs {
    border-bottom: 3px solid var(--primary-color);
    margin: 0;
    padding: 0 24px;
    background: linear-gradient(to right, rgba(1, 128, 123, 0.03) 0%, transparent 100%);
}

.filter-tabs .nav-tabs li {
    margin-bottom: -3px;
}

.filter-tabs .nav-tabs li a {
    color: var(--text-light);
    border: none;
    padding: 16px 24px;
    font-weight: 600;
    transition: var(--transition);
    border-bottom: 3px solid transparent;
}

.filter-tabs .nav-tabs li a:hover {
    background: rgba(1, 128, 123, 0.05);
    color: var(--primary-color);
}

.filter-tabs .nav-tabs li.active a {
    color: var(--primary-color);
    border-bottom: 3px solid var(--primary-color);
    background: transparent;
}

.filter-tabs .nav-tabs li a .badge {
    background: var(--primary-color);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    margin-left: 8px;
}

.filter-tabs .nav-tabs li.active a .badge {
    background: var(--secondary-color);
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_filter input {
    border: 2px solid var(--primary-color) !important;
    border-radius: 8px;
    padding: 10px 16px !important;
    font-size: 14px;
    transition: var(--transition);
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: var(--secondary-color) !important;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    outline: none;
}

.dataTables_wrapper .dataTables_length select {
    border: 2px solid var(--primary-color);
    border-radius: 8px;
    padding: 8px 12px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
    color: white !important;
}

/* Responsive Mobile-First */
@media (max-width: 768px) {
    .consultations-header {
        padding: 20px 16px;
    }

    .consultations-header h1 {
        font-size: 22px;
    }

    .header-actions {
        margin-top: 12px;
    }

    .btn-new-consultation,
    .btn-calendar {
        width: 100%;
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

    .consultations-table-wrapper {
        padding: 16px;
    }

    #consultations-table {
        font-size: 13px;
    }

    #consultations-table thead th,
    #consultations-table tbody td {
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

    .legend-items {
        flex-direction: column;
        gap: 12px;
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
                <div class="consultations-header">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h1>
                                <i class="fa fa-stethoscope"></i>
                                Gestion des Consultations
                            </h1>
                            <p>
                                <i class="fa fa-calendar-check-o"></i> Planifiez et suivez toutes vos consultations diététiques
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <?php if (dietetic_has_permission('create')) { ?>
                                <div class="header-actions">
                                    <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-new-consultation">
                                        <i class="fa fa-plus-circle"></i> Nouvelle Consultation
                                    </a>
                                    <a href="<?php echo admin_url('dietetic/consultations/calendar'); ?>" class="btn btn-calendar">
                                        <i class="fa fa-calendar"></i> Voir le Calendrier
                                    </a>
                                </div>
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
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="stat-value"><?php echo count($consultations); ?></div>
                <div class="stat-label">Total Consultations</div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $scheduled_count = 0;
                    foreach ($consultations as $c) {
                        if ($c->status === 'scheduled') $scheduled_count++;
                    }
                    echo $scheduled_count;
                    ?>
                </div>
                <div class="stat-label">Programmées</div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $completed_count = 0;
                    foreach ($consultations as $c) {
                        if ($c->status === 'completed') $completed_count++;
                    }
                    echo $completed_count;
                    ?>
                </div>
                <div class="stat-label">Complétées</div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon">
                    <i class="fa fa-calendar-o"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $today_count = 0;
                    foreach ($consultations as $c) {
                        if (date('Y-m-d', strtotime($c->consultation_date)) === date('Y-m-d')) {
                            $today_count++;
                        }
                    }
                    echo $today_count;
                    ?>
                </div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="row">
            <div class="col-md-12">
                <div class="filter-tabs">
                    <ul class="nav nav-tabs">
                        <li class="<?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=all'); ?>">
                                <i class="fa fa-list"></i> Toutes
                                <span class="badge"><?php echo count($consultations); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'scheduled') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=scheduled'); ?>">
                                <i class="fa fa-clock-o"></i> Programmées
                                <span class="badge"><?php echo $scheduled_count; ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=completed'); ?>">
                                <i class="fa fa-check-circle"></i> Complétées
                                <span class="badge"><?php echo $completed_count; ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=cancelled'); ?>">
                                <i class="fa fa-times-circle"></i> Annulées
                                <span class="badge">
                                    <?php
                                    $cancelled_count = 0;
                                    foreach ($consultations as $c) {
                                        if ($c->status === 'cancelled') $cancelled_count++;
                                    }
                                    echo $cancelled_count;
                                    ?>
                                </span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'no_show') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=no_show'); ?>">
                                <i class="fa fa-user-times"></i> Absents
                                <span class="badge">
                                    <?php
                                    $no_show_count = 0;
                                    foreach ($consultations as $c) {
                                        if ($c->status === 'no_show') $no_show_count++;
                                    }
                                    echo $no_show_count;
                                    ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Consultations Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <div class="table-header">
                        <h4>
                            <i class="fa fa-list"></i>
                            Liste des Consultations
                        </h4>
                    </div>

                    <div class="consultations-table-wrapper">
                        <table id="consultations-table">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-user"></i> <?php echo _l('dietetic_patient'); ?></th>
                                    <th><i class="fa fa-user-md"></i> <?php echo _l('dietetic_dietitian'); ?></th>
                                    <th><i class="fa fa-calendar"></i> <?php echo _l('dietetic_date'); ?></th>
                                    <th><i class="fa fa-tag"></i> <?php echo _l('dietetic_type'); ?></th>
                                    <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                    <th><i class="fa fa-clock-o"></i> <?php echo _l('dietetic_duration'); ?></th>
                                    <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consultations as $consultation) { ?>
                                    <tr onclick="window.location='<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>'">
                                        <td>
                                            <div class="patient-name">
                                                <i class="fa fa-user-circle"></i>
                                                <span><?php echo htmlspecialchars($consultation->client_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dietitian-info">
                                                <i class="fa fa-stethoscope"></i>
                                                <span><?php echo htmlspecialchars($consultation->dietitian_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $consult_date = strtotime($consultation->consultation_date);
                                            $today = strtotime(date('Y-m-d'));

                                            if (date('Y-m-d', $consult_date) === date('Y-m-d')) {
                                                $badge_class = 'today';
                                                $date_text = '<i class="fa fa-exclamation-circle"></i> Aujourd\'hui ' . date('H:i', $consult_date);
                                            } elseif ($consult_date > $today && $consult_date <= strtotime('+7 days')) {
                                                $badge_class = 'thisweek';
                                                $date_text = '<i class="fa fa-calendar"></i> ' . _dt($consultation->consultation_date);
                                            } else {
                                                $badge_class = 'normal';
                                                $date_text = '<i class="fa fa-calendar-o"></i> ' . _dt($consultation->consultation_date);
                                            }
                                            ?>
                                            <span class="date-badge <?php echo $badge_class; ?>">
                                                <?php echo $date_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="type-badge">
                                                <?php echo dietetic_consultation_type_label($consultation->consultation_type); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = strtolower(str_replace(' ', '_', $consultation->status));
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($consultation->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="duration-badge">
                                                <i class="fa fa-hourglass-half"></i>
                                                <span><?php echo $consultation->duration; ?> min</span>
                                            </span>
                                        </td>
                                        <td onclick="event.stopPropagation();">
                                            <div class="action-buttons">
                                                <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>"
                                                   class="action-btn view"
                                                   title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>"
                                                       class="action-btn edit"
                                                       title="Modifier">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if (dietetic_has_permission('delete')) { ?>
                                                    <a href="#"
                                                       onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/consultations/delete/' . $consultation->id); ?>', function() { location.reload(); }); return false;"
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
                    </div>
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
                            <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="action-btn-primary">
                                <i class="fa fa-plus-circle"></i>
                                <span>Nouvelle Consultation</span>
                            </a>
                            <a href="<?php echo admin_url('dietetic/consultations/calendar'); ?>" class="action-btn-secondary">
                                <i class="fa fa-calendar"></i>
                                <span>Calendrier</span>
                            </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('dietetic/patients'); ?>" class="action-btn-info">
                            <i class="fa fa-users"></i>
                            <span>Voir les Patients</span>
                        </a>
                    </div>
                    <div class="quick-actions-hint">
                        <i class="fa fa-info-circle"></i>
                        <span>Cliquez sur une ligne pour voir les détails de la consultation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="legend-panel">
                    <h5>
                        <i class="fa fa-info-circle"></i>
                        Légende des Dates
                    </h5>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="date-badge today">
                                <i class="fa fa-exclamation-circle"></i> Aujourd'hui
                            </span>
                        </div>
                        <div class="legend-item">
                            <span class="date-badge thisweek">
                                <i class="fa fa-calendar"></i> Cette semaine (7 jours)
                            </span>
                        </div>
                        <div class="legend-item">
                            <span class="date-badge normal">
                                <i class="fa fa-calendar-o"></i> Autres dates
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(window).on('load', function() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('#consultations-table').DataTable({
            "order": [[2, "desc"]], // Sort by consultation_date desc
            "pageLength": 10,
            "lengthChange": false,
            "searching": true,
            "info": true,
            "paging": true,
            "language": {
                "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
            },
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Disable sorting on actions column
            ]
        });
    } else {
        console.error('DataTables not loaded');
    }
});
</script>

<!-- Script ultra-simple pour le menu Diététique -->
<script>
$(document).ready(function() {
    // Trouver le lien du menu Diététique et empêcher la navigation
    $('#side-menu a[href*="dietetic"]').first().parent().find('> a').on('click', function(e) {
        if ($(this).next('ul').length > 0) {
            e.preventDefault();
            $(this).next('ul').toggleClass('in');
            $(this).parent().toggleClass('active');
        }
    });
});
</script>


<?php init_tail(); ?>
