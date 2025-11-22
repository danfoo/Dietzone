<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.dietetic-programs-header {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
}

.dietetic-programs-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 15px;
}

.dietetic-programs-header h1 i {
    font-size: 32px;
}

.dietetic-programs-header .header-actions {
    margin-top: 15px;
}

.dietetic-programs-header .btn-new-program {
    background: white;
    color: #27ae60;
    border: none;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.dietetic-programs-header .btn-new-program:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.dietetic-stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #27ae60;
    margin-bottom: 20px;
}

.dietetic-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.dietetic-stat-card.active {
    border-left-color: #2ecc71;
}

.dietetic-stat-card.completed {
    border-left-color: #3498db;
}

.dietetic-stat-card.month {
    border-left-color: #f39c12;
}

.dietetic-stat-card .stat-icon {
    font-size: 36px;
    margin-bottom: 10px;
}

.dietetic-stat-card.active .stat-icon {
    color: #2ecc71;
}

.dietetic-stat-card.completed .stat-icon {
    color: #3498db;
}

.dietetic-stat-card.month .stat-icon {
    color: #f39c12;
}

.dietetic-stat-card h3 {
    margin: 0 0 5px 0;
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

.dietetic-stat-card p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
}

#programs-table thead th {
    background: #ecf0f1;
    color: #2c3e50;
    font-weight: 600;
    border: none;
}

#programs-table tbody tr {
    transition: all 0.2s ease;
    cursor: pointer;
}

#programs-table tbody tr:hover {
    background: #f8f9fa !important;
    transform: scale(1.01);
}

#programs-table .btn-group {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

#programs-table tbody tr:hover .btn-group {
    opacity: 1;
}

.program-patient-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.program-patient-cell i {
    color: #27ae60;
    font-size: 18px;
}

.program-name-cell {
    font-weight: 600;
    color: #2c3e50;
}

.program-calories {
    background: #e8f5e9;
    color: #27ae60;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Header Section -->
        <div class="dietetic-programs-header">
            <h1>
                <i class="fa fa-list-alt"></i>
                <?php echo _l('dietetic_programs'); ?>
            </h1>
            <div class="header-actions">
                <?php if (dietetic_has_permission('create')) { ?>
                    <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-new-program">
                        <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_program'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="dietetic-stat-card">
                    <div class="stat-icon">
                        <i class="fa fa-list-alt"></i>
                    </div>
                    <h3><?php echo $total_count; ?></h3>
                    <p>Total des Programmes</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="dietetic-stat-card active">
                    <div class="stat-icon">
                        <i class="fa fa-play-circle"></i>
                    </div>
                    <h3><?php echo $active_count; ?></h3>
                    <p>Programmes Actifs</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="dietetic-stat-card completed">
                    <div class="stat-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h3><?php echo $completed_count; ?></h3>
                    <p>Programmes Terminés</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="dietetic-stat-card month">
                    <div class="stat-icon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <h3><?php echo $this_month_count; ?></h3>
                    <p>Ce Mois-ci</p>
                </div>
            </div>
        </div>

        <!-- Programs Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <table class="table table-striped dietetic-table" id="programs-table">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-user"></i> <?php echo _l('dietetic_patient'); ?></th>
                                    <th><i class="fa fa-file-text"></i> <?php echo _l('dietetic_program_name'); ?></th>
                                    <th><i class="fa fa-calendar-o"></i> <?php echo _l('dietetic_start_date'); ?></th>
                                    <th><i class="fa fa-calendar-check-o"></i> <?php echo _l('dietetic_end_date'); ?></th>
                                    <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                    <th><i class="fa fa-fire"></i> <?php echo _l('dietetic_daily_calories'); ?></th>
                                    <th><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($programs as $program) { ?>
                                    <tr onclick="window.location='<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>'">
                                        <td>
                                            <div class="program-patient-cell">
                                                <i class="fa fa-user-circle"></i>
                                                <strong><?php echo htmlspecialchars($program->client_name); ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="program-name-cell"><?php echo htmlspecialchars($program->program_name); ?></span>
                                        </td>
                                        <td>
                                            <i class="fa fa-calendar text-muted"></i> <?php echo _d($program->start_date); ?>
                                        </td>
                                        <td>
                                            <?php if ($program->end_date) { ?>
                                                <i class="fa fa-calendar-check-o text-muted"></i> <?php echo _d($program->end_date); ?>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                        <td>
                                            <?php if ($program->daily_calories) { ?>
                                                <span class="program-calories">
                                                    <i class="fa fa-fire"></i> <?php echo $program->daily_calories; ?> kcal
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td onclick="event.stopPropagation();">
                                            <div class="btn-group">
                                                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>"
                                                   class="btn btn-default btn-sm"
                                                   title="Voir">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/programs/edit/' . $program->id); ?>"
                                                       class="btn btn-default btn-sm"
                                                       title="Modifier">
                                                        <i class="fa fa-pencil"></i>
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
    </div>
</div>

<script>
$(window).on('load', function() {
    // Ensure DataTables is loaded
    if ($.fn.DataTable) {
        $('#programs-table').DataTable({
            "order": [[2, "desc"]], // Sort by start_date desc
            "pageLength": 10,
            "lengthChange": false,
            "searching": true,
            "info": true,
            "paging": true,
            "language": {
                "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
            }
        });
    } else {
        console.error('DataTables not loaded');
    }
});
</script>

<?php init_tail(); ?>
