<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; border-radius: 8px; padding: 25px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin-top: 0;">
                                    <i class="fa fa-cutlery" style="font-size: 42px; vertical-align: middle; margin-right: 15px;"></i>
                                    Base de Données Alimentaire
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin-bottom: 0; font-size: 16px;">
                                    <i class="fa fa-database"></i> Gérez votre catalogue d'aliments et leurs informations nutritionnelles
                                </p>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 15px;">
                                <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; display: inline-block;">
                                    <div style="font-size: 28px; font-weight: bold; color: white;">
                                        <?php echo $total_count; ?>
                                    </div>
                                    <div style="font-size: 12px; color: rgba(255,255,255,0.9); text-transform: uppercase;">
                                        <?php echo _l('dietetic_foods'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="btn-toolbar" role="toolbar">
                                    <?php if (dietetic_has_permission('create')) { ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo admin_url('dietetic/foods/create'); ?>" class="btn btn-success" style="padding: 10px 20px;">
                                                <i class="fa fa-plus-circle"></i> <?php echo _l('dietetic_new_food'); ?>
                                            </a>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo admin_url('dietetic/foods/import'); ?>" class="btn btn-primary" style="padding: 10px 20px;">
                                                <i class="fa fa-upload"></i> <?php echo _l('dietetic_import_csv'); ?>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo admin_url('dietetic/foods/export'); ?>" class="btn btn-info" style="padding: 10px 20px;">
                                            <i class="fa fa-download"></i> <?php echo _l('dietetic_export_csv'); ?>
                                        </a>
                                    </div>
                                    <?php if (is_admin()) { ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo admin_url('dietetic/foods/cleanup_duplicates'); ?>" class="btn btn-warning" style="padding: 10px 20px;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les doublons ? Cette action est irréversible.');">
                                                <i class="fa fa-clone"></i> Nettoyer les doublons
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if (dietetic_has_permission('delete')) { ?>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-danger" id="bulk-delete-btn" style="display:none; padding: 10px 20px;">
                                                <i class="fa fa-trash-o"></i> Supprimer la sélection
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <div style="padding-top: 8px;">
                                    <span class="label label-default" style="font-size: 13px; padding: 8px 15px;">
                                        <i class="fa fa-filter"></i> Utilisez la recherche pour filtrer
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foods Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="border-bottom: 3px solid #f39c12; padding-bottom: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0;">
                                <i class="fa fa-list" style="color: #f39c12;"></i> Liste des Aliments
                            </h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover dietetic-table" id="foods-table" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th width="30">
                                            <?php if (dietetic_has_permission('delete')) { ?>
                                                <input type="checkbox" id="select-all">
                                            <?php } ?>
                                        </th>
                                        <th><i class="fa fa-apple" style="color: #e74c3c;"></i> <?php echo _l('dietetic_food'); ?></th>
                                        <th><i class="fa fa-tag" style="color: #3498db;"></i> <?php echo _l('category'); ?></th>
                                        <th><i class="fa fa-balance-scale" style="color: #9b59b6;"></i> <?php echo _l('dietetic_serving_size'); ?></th>
                                        <th><i class="fa fa-fire" style="color: #e67e22;"></i> <?php echo _l('dietetic_calories'); ?></th>
                                        <th><i class="fa fa-leaf" style="color: #27ae60;"></i> <?php echo _l('dietetic_protein'); ?></th>
                                        <th><i class="fa fa-pie-chart" style="color: #f39c12;"></i> <?php echo _l('dietetic_carbs'); ?></th>
                                        <th><i class="fa fa-tint" style="color: #e74c3c;"></i> <?php echo _l('dietetic_fats'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
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
                                                <strong style="color: #2c3e50;"><?php echo dietetic_get_food_name($food); ?></strong>
                                                <?php if ($food->food_name_fr && $food->food_name) { ?>
                                                    <br /><small class="text-muted"><i class="fa fa-language"></i> <?php echo $food->food_name; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="label" style="background: #3498db; font-size: 11px; padding: 5px 10px;">
                                                    <?php echo ucfirst($food->category); ?>
                                                </span>
                                            </td>
                                            <td><span style="color: #7f8c8d;"><?php echo $food->serving_size . ' ' . $food->serving_unit; ?></span></td>
                                            <td>
                                                <span class="badge" style="background: #e67e22; font-size: 12px; padding: 5px 10px;">
                                                    <?php echo round($food->calories); ?> kcal
                                                </span>
                                            </td>
                                            <td><span style="color: #27ae60; font-weight: 500;"><?php echo number_format($food->protein, 1); ?>g</span></td>
                                            <td><span style="color: #f39c12; font-weight: 500;"><?php echo number_format($food->carbs, 1); ?>g</span></td>
                                            <td><span style="color: #e74c3c; font-weight: 500;"><?php echo number_format($food->fats, 1); ?>g</span></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <?php if (dietetic_has_permission('edit')) { ?>
                                                        <a href="<?php echo admin_url('dietetic/foods/edit/' . $food->id); ?>" class="btn btn-default btn-sm" title="Modifier">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if (dietetic_has_permission('delete')) { ?>
                                                        <a href="#" onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/foods/delete/' . $food->id); ?>', function() { location.reload(); }); return false;" class="btn btn-danger btn-sm" title="Supprimer">
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

        <!-- Info Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: #ecf0f1; border-left: 4px solid #3498db;">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 style="margin-top: 0; color: #2c3e50;">
                                    <i class="fa fa-info-circle" style="color: #3498db;"></i> Conseils d'utilisation
                                </h5>
                                <ul style="margin-bottom: 0; color: #7f8c8d;">
                                    <li>Utilisez la <strong>recherche</strong> en haut à droite pour trouver rapidement un aliment</li>
                                    <li>Cochez les cases pour <strong>sélectionner plusieurs aliments</strong> et les supprimer en masse</li>
                                    <li>Cliquez sur <strong>"Nettoyer les doublons"</strong> pour supprimer les entrées en double automatiquement</li>
                                    <li>Exportez la base de données en <strong>CSV</strong> pour une sauvegarde ou un traitement externe</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 20px;">
                                <a href="<?php echo admin_url('dietetic/foods/create'); ?>" class="btn btn-success btn-lg">
                                    <i class="fa fa-plus-circle"></i> Ajouter un aliment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced table styles */
    #foods-table tbody tr {
        transition: all 0.2s ease;
    }

    #foods-table tbody tr:hover {
        background-color: #fff8e1 !important;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Button hover effects */
    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Checkbox styling */
    #foods-table input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    /* Badge and label enhancements */
    .label, .badge {
        transition: all 0.2s ease;
    }

    .label:hover, .badge:hover {
        transform: scale(1.05);
    }

    /* DataTables search box enhancement */
    .dataTables_filter input {
        border: 2px solid #f39c12 !important;
        border-radius: 20px;
        padding: 8px 15px !important;
        font-size: 14px;
    }

    .dataTables_filter input:focus {
        border-color: #e67e22 !important;
        box-shadow: 0 0 8px rgba(243, 156, 18, 0.3);
    }
</style>

<script>
$(window).on('load', function() {
    // Ensure DataTables is loaded
    if ($.fn.DataTable) {
        var table = $('#foods-table').DataTable({
            "order": [[1, "asc"]], // Sort by food_name asc (column 1 now, because we added checkbox column)
            "pageLength": 10,
            "lengthChange": false,
            "searching": true,
            "info": true,
            "paging": true,
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
    } else {
        console.error('DataTables not loaded');
    }

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
            $('#bulk-delete-btn').show();
            var text = $('#bulk-delete-btn').html().split('(')[0];
            $('#bulk-delete-btn').html(text + ' (' + checkedCount + ')');
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
