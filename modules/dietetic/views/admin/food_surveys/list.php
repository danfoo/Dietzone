<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Food Surveys List Styles with Mobile-First Approach */
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
.surveys-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 32px 24px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.surveys-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.surveys-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.surveys-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 15px;
    position: relative;
    z-index: 1;
}

.btn-new-survey {
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

.btn-new-survey:hover {
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

.stat-card.green {
    border-left-color: #27ae60;
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

.stat-card.green .stat-icon {
    background: linear-gradient(135deg, rgba(39, 174, 96, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
    color: #27ae60;
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
.surveys-table-wrapper {
    padding: 24px;
}

#surveys-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

#surveys-table thead tr {
    background: var(--background-light);
}

#surveys-table thead th {
    padding: 16px 12px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-light);
    border: none;
    white-space: nowrap;
}

#surveys-table thead th i {
    margin-right: 4px;
}

#surveys-table tbody tr {
    transition: var(--transition);
    cursor: pointer;
    border-bottom: 1px solid #ecf0f1;
}

#surveys-table tbody tr:hover {
    background: linear-gradient(to right, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.02) 100%);
    transform: translateX(4px);
}

#surveys-table tbody td {
    padding: 16px 12px;
    vertical-align: middle;
    border: none;
}

.survey-name {
    font-weight: 600;
    color: var(--text-dark);
}

.patient-name {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-light);
}

.patient-name i {
    color: var(--primary-color);
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

.status-badge.active {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.status-badge.completed {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}

.status-badge.cancelled {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

/* Progress Bar */
.progress-container {
    width: 100%;
    max-width: 120px;
}

.progress {
    height: 8px;
    background: #ecf0f1;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 4px;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 11px;
    color: var(--text-light);
    font-weight: 600;
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
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
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

.quick-actions-hint {
    color: var(--text-light);
    font-size: 13px;
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Responsive Mobile-First */
@media (max-width: 768px) {
    .surveys-header {
        padding: 20px 16px;
    }

    .surveys-header h1 {
        font-size: 22px;
    }

    .btn-new-survey {
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

    .surveys-table-wrapper {
        padding: 16px;
    }

    #surveys-table {
        font-size: 13px;
    }

    #surveys-table thead th,
    #surveys-table tbody td {
        padding: 12px 8px;
    }

    .action-buttons-group {
        flex-direction: column;
    }

    .action-btn-primary,
    .action-btn-secondary {
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
                <div class="surveys-header">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h1>
                                <i class="fa fa-camera"></i>
                                Enquêtes Alimentaires
                            </h1>
                            <p>
                                <i class="fa fa-cutlery"></i> Suivez les habitudes alimentaires de vos patients avec photos
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/food_surveys/create'); ?>" class="btn btn-new-survey">
                                    <i class="fa fa-plus-circle"></i> Nouvelle Enquête
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
                    <i class="fa fa-camera"></i>
                </div>
                <div class="stat-value"><?php echo count($surveys); ?></div>
                <div class="stat-label">Total Enquêtes</div>
            </div>

            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $active_count = 0;
                    foreach ($surveys as $s) {
                        if ($s->status === 'active') $active_count++;
                    }
                    echo $active_count;
                    ?>
                </div>
                <div class="stat-label">Actives</div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fa fa-flag-checkered"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $completed_count = 0;
                    foreach ($surveys as $s) {
                        if ($s->status === 'completed') $completed_count++;
                    }
                    echo $completed_count;
                    ?>
                </div>
                <div class="stat-label">Terminées</div>
            </div>

            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="stat-value">
                    <?php
                    $patients = array();
                    foreach ($surveys as $s) {
                        if (!in_array($s->patient_id, $patients)) {
                            $patients[] = $s->patient_id;
                        }
                    }
                    echo count($patients);
                    ?>
                </div>
                <div class="stat-label">Patients Suivis</div>
            </div>
        </div>

        <!-- Surveys Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <div class="table-header">
                        <h4>
                            <i class="fa fa-list"></i>
                            Liste des Enquêtes Alimentaires
                        </h4>
                    </div>

                    <div class="surveys-table-wrapper">
                        <table id="surveys-table">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-file-text"></i> Nom</th>
                                    <th><i class="fa fa-user"></i> Patient</th>
                                    <th><i class="fa fa-user-md"></i> Diététicien</th>
                                    <th><i class="fa fa-calendar"></i> Période</th>
                                    <th><i class="fa fa-clock-o"></i> Durée</th>
                                    <th><i class="fa fa-info-circle"></i> Statut</th>
                                    <th><i class="fa fa-percent"></i> Progrès</th>
                                    <th class="text-center"><i class="fa fa-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($surveys as $survey) { ?>
                                    <tr onclick="window.location='<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>'">
                                        <td>
                                            <div class="survey-name">
                                                <?php echo htmlspecialchars($survey->survey_name); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="patient-name">
                                                <i class="fa fa-user-circle"></i>
                                                <span><?php echo htmlspecialchars($survey->patient_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <small style="color: #7f8c8d;">
                                                <?php echo htmlspecialchars($survey->dietitian_name); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small style="color: #7f8c8d;">
                                                <?php echo date('d/m/Y', strtotime($survey->start_date)); ?> -
                                                <?php echo date('d/m/Y', strtotime($survey->end_date)); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="duration-badge">
                                                <i class="fa fa-hourglass-half"></i>
                                                <span><?php echo $survey->duration_days; ?> jours</span>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = strtolower($survey->status);
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo ucfirst($survey->status); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: <?php echo $survey->completion_percentage; ?>%"></div>
                                                </div>
                                                <div class="progress-text"><?php echo round($survey->completion_percentage); ?>%</div>
                                            </div>
                                        </td>
                                        <td onclick="event.stopPropagation();">
                                            <div class="action-buttons">
                                                <a href="<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>"
                                                   class="action-btn view"
                                                   title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/food_surveys/edit/' . $survey->id); ?>"
                                                       class="action-btn edit"
                                                       title="Modifier">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if (dietetic_has_permission('delete')) { ?>
                                                    <a href="#"
                                                       onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/food_surveys/delete/' . $survey->id); ?>', function() { location.reload(); }); return false;"
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
                            <a href="<?php echo admin_url('dietetic/food_surveys/create'); ?>" class="action-btn-primary">
                                <i class="fa fa-plus-circle"></i>
                                <span>Nouvelle Enquête</span>
                            </a>
                        <?php } ?>
                        <a href="<?php echo admin_url('dietetic/patients'); ?>" class="action-btn-secondary">
                            <i class="fa fa-users"></i>
                            <span>Voir les Patients</span>
                        </a>
                    </div>
                    <div class="quick-actions-hint">
                        <i class="fa fa-info-circle"></i>
                        <span>Cliquez sur une ligne pour voir les détails de l'enquête</span>
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
        $('#surveys-table').DataTable({
            "order": [[3, "desc"]], // Sort by start_date desc
            "pageLength": 10,
            "lengthChange": false,
            "searching": true,
            "info": true,
            "paging": true,
            "columnDefs": [
                { "orderable": false, "targets": 7 } // Disable sorting on actions column
            ]
        });
    } else {
        console.error('DataTables not loaded');
    }
});
</script>

<?php init_tail(); ?>
