<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-info pull-left mbot15">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_program'); ?>
                            </a>
                        <?php } ?>
                        <div class="clearfix"></div>

                        <table class="table table-striped dietetic-table" id="programs-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('dietetic_patient'); ?></th>
                                    <th><?php echo _l('dietetic_program_name'); ?></th>
                                    <th><?php echo _l('dietetic_start_date'); ?></th>
                                    <th><?php echo _l('dietetic_end_date'); ?></th>
                                    <th><?php echo _l('dietetic_status'); ?></th>
                                    <th><?php echo _l('dietetic_daily_calories'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($programs as $program) { ?>
                                    <tr>
                                        <td><a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>"><?php echo $program->client_name; ?></a></td>
                                        <td><?php echo $program->program_name; ?></td>
                                        <td><?php echo _d($program->start_date); ?></td>
                                        <td><?php echo $program->end_date ? _d($program->end_date) : '-'; ?></td>
                                        <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                        <td><?php echo $program->daily_calories ? $program->daily_calories . ' kcal' : '-'; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/programs/edit/' . $program->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></a>
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
$(document).ready(function() {
    $('#programs-table').DataTable({
        "order": [[2, "desc"]], // Sort by start_date desc
        "pageLength": 10,
        "lengthChange": false,
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
        }
    });
});
</script>

<?php init_tail(); ?>
