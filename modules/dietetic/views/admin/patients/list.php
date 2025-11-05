<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-info pull-left mbot15">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_patient'); ?>
                            </a>
                        <?php } ?>

                        <div class="clearfix"></div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover dietetic-table" id="patients-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('dietetic_client_name'); ?></th>
                                        <th><?php echo _l('dietetic_dietitian'); ?></th>
                                        <th><?php echo _l('dietetic_status'); ?></th>
                                        <th><?php echo _l('dietetic_weight'); ?></th>
                                        <th><?php echo _l('dietetic_bmi'); ?></th>
                                        <th><?php echo _l('dietetic_activity_level'); ?></th>
                                        <th><?php echo _l('created_at'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients as $patient) { ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>">
                                                    <strong><?php echo $patient->client_name; ?></strong>
                                                </a>
                                            </td>
                                            <td><?php echo $patient->dietitian_name; ?></td>
                                            <td><?php echo dietetic_patient_status_badge($patient->status); ?></td>
                                            <td><?php echo $patient->initial_weight ? $patient->initial_weight . ' kg' : '-'; ?></td>
                                            <td><?php echo $patient->initial_weight && $patient->height ? dietetic_calculate_bmi($patient->initial_weight, $patient->height) : '-'; ?></td>
                                            <td><?php echo $patient->activity_level ? ucfirst($patient->activity_level) : '-'; ?></td>
                                            <td><?php echo _dt($patient->created_at); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>" class="btn btn-default btn-sm">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (dietetic_has_permission('edit')) { ?>
                                                        <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>" class="btn btn-default btn-sm">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if (dietetic_has_permission('delete')) { ?>
                                                        <a href="#" onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/patients/delete/' . $patient->id); ?>', function() { location.reload(); }); return false;" class="btn btn-danger btn-sm">
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
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#patients-table').DataTable({
        "order": [[6, "desc"]], // Sort by created_at desc
        "pageLength": 25,
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
        }
    });
});
</script>

<?php init_tail(); ?>
