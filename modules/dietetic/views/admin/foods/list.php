<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="btn-group pull-left mbot15">
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/foods/create'); ?>" class="btn btn-info">
                                    <i class="fa fa-plus"></i> <?php echo _l('dietetic_new_food'); ?>
                                </a>
                                <a href="<?php echo admin_url('dietetic/foods/import'); ?>" class="btn btn-default">
                                    <i class="fa fa-upload"></i> <?php echo _l('dietetic_import_csv'); ?>
                                </a>
                            <?php } ?>
                            <a href="<?php echo admin_url('dietetic/foods/export'); ?>" class="btn btn-default">
                                <i class="fa fa-download"></i> <?php echo _l('dietetic_export_csv'); ?>
                            </a>
                        </div>

                        <div class="pull-right mbot15">
                            <div class="label label-info">
                                <?php echo _l('total'); ?>: <?php echo $total_count; ?> <?php echo _l('dietetic_foods'); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <table class="table table-striped dietetic-table" id="foods-table">
                            <thead>
                                <tr>
                                    <th><?php echo _l('dietetic_food'); ?></th>
                                    <th><?php echo _l('category'); ?></th>
                                    <th><?php echo _l('dietetic_serving_size'); ?></th>
                                    <th><?php echo _l('dietetic_calories'); ?></th>
                                    <th><?php echo _l('dietetic_protein'); ?></th>
                                    <th><?php echo _l('dietetic_carbs'); ?></th>
                                    <th><?php echo _l('dietetic_fats'); ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($foods as $food) { ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo dietetic_get_food_name($food); ?></strong>
                                            <?php if ($food->food_name_fr && $food->food_name) { ?>
                                                <br /><small class="text-muted"><?php echo $food->food_name; ?></small>
                                            <?php } ?>
                                        </td>
                                        <td><span class="label label-info"><?php echo ucfirst($food->category); ?></span></td>
                                        <td><?php echo $food->serving_size . ' ' . $food->serving_unit; ?></td>
                                        <td><?php echo round($food->calories); ?> kcal</td>
                                        <td><?php echo number_format($food->protein, 1); ?>g</td>
                                        <td><?php echo number_format($food->carbs, 1); ?>g</td>
                                        <td><?php echo number_format($food->fats, 1); ?>g</td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/foods/edit/' . $food->id); ?>" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i></a>
                                                <?php } ?>
                                                <?php if (dietetic_has_permission('delete')) { ?>
                                                    <a href="#" onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/foods/delete/' . $food->id); ?>', function() { location.reload(); }); return false;" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
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
    $('#foods-table').DataTable({
        "order": [[0, "asc"]], // Sort by food_name asc
        "pageLength": 10,
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
        }
    });
});
</script>

<?php init_tail(); ?>
