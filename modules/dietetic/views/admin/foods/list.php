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
                            <?php if (is_admin()) { ?>
                                <a href="<?php echo admin_url('dietetic/foods/cleanup_duplicates'); ?>" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les doublons ? Cette action est irréversible.');">
                                    <i class="fa fa-clone"></i> Nettoyer les doublons
                                </a>
                            <?php } ?>
                            <?php if (dietetic_has_permission('delete')) { ?>
                                <button type="button" class="btn btn-danger" id="bulk-delete-btn" style="display:none;">
                                    <i class="fa fa-trash"></i> Supprimer la sélection
                                </button>
                            <?php } ?>
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
                                    <th width="20">
                                        <?php if (dietetic_has_permission('delete')) { ?>
                                            <input type="checkbox" id="select-all">
                                        <?php } ?>
                                    </th>
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
                                            <?php if (dietetic_has_permission('delete')) { ?>
                                                <input type="checkbox" class="food-checkbox" value="<?php echo $food->id; ?>">
                                            <?php } ?>
                                        </td>
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
    var table = $('#foods-table').DataTable({
        "order": [[1, "asc"]], // Sort by food_name asc (column 1 now, because we added checkbox column)
        "pageLength": 10,
        "lengthChange": false,
        "searching": true,
        "info": true,
        "columnDefs": [
            {
                "orderable": false,
                "targets": 0 // Disable sorting on checkbox column
            }
        ],
        "language": {
            "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
        }
    });

    // Select all checkboxes
    $('#select-all').on('click', function() {
        var checked = $(this).prop('checked');
        $('.food-checkbox:visible').prop('checked', checked);
        updateBulkDeleteButton();
    });

    // Update bulk delete button visibility when checkboxes change
    $(document).on('change', '.food-checkbox', function() {
        updateBulkDeleteButton();
    });

    function updateBulkDeleteButton() {
        var checkedCount = $('.food-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulk-delete-btn').show().find('i').next().remove();
            $('#bulk-delete-btn').append(' (' + checkedCount + ')');
        } else {
            $('#bulk-delete-btn').hide();
        }
    }

    // Bulk delete functionality
    $('#bulk-delete-btn').on('click', function() {
        var selectedIds = [];
        $('.food-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir supprimer ' + selectedIds.length + ' aliment(s) ? Cette action est irréversible.')) {
            $.ajax({
                url: '<?php echo admin_url('dietetic/foods/bulk_delete'); ?>',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert('Erreur: ' + result.message);
                    }
                },
                error: function() {
                    alert('Erreur lors de la suppression');
                }
            });
        }
    });
});
</script>

<?php init_tail(); ?>
