<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-info pull-left mbot15">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_consultation'); ?>
                            </a>
                            <a href="<?php echo admin_url('dietetic/consultations/calendar'); ?>" class="btn btn-default pull-left mbot15 mleft5">
                                <i class="fa fa-calendar"></i> <?php echo _l('dietetic_consultation_calendar'); ?>
                            </a>
                        <?php } ?>
                        <div class="clearfix"></div>

                        <table class="table table-striped dietetic-table" id="consultations-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('dietetic_patient'); ?></th>
                                    <th><?php echo _l('dietetic_dietitian'); ?></th>
                                    <th><?php echo _l('dietetic_date'); ?></th>
                                    <th><?php echo _l('dietetic_type'); ?></th>
                                    <th><?php echo _l('dietetic_status'); ?></th>
                                    <th><?php echo _l('dietetic_duration'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consultations as $consultation) { ?>
                                    <tr>
                                        <td><a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>"><?php echo $consultation->client_name; ?></a></td>
                                        <td><?php echo $consultation->dietitian_name; ?></td>
                                        <td><?php echo _dt($consultation->consultation_date); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                                        <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                        <td><?php echo $consultation->duration; ?> min</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-eye"></i></a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></a>
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
    $('#consultations-table').DataTable({
        "order": [[2, "desc"]], // Sort by consultation_date desc
        "pageLength": 10,
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
        }
    });
});
</script>

<?php init_tail(); ?>
