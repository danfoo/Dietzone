<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<style>
/* Modern Foods Design */
:root {
    --food-orange: #f39c12;
    --food-orange-dark: #e67e22;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s;
    text-align: center;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stat-card .stat-icon {
    font-size: 48px;
    margin-bottom: 10px;
    opacity: 0.3;
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    margin: 5px 0;
}

.stat-card .stat-label {
    font-size: 13px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.stat-card.total {
    border-left: 4px solid #f39c12;
}

.stat-card.total .stat-icon,
.stat-card.total .stat-value {
    color: #f39c12;
}

.stat-card.active {
    border-left: 4px solid #27ae60;
}

.stat-card.active .stat-icon,
.stat-card.active .stat-value {
    color: #27ae60;
}

.stat-card.categories {
    border-left: 4px solid #3498db;
}

.stat-card.categories .stat-icon,
.stat-card.categories .stat-value {
    color: #3498db;
}

.stat-card.calories {
    border-left: 4px solid #e67e22;
}

.stat-card.calories .stat-icon,
.stat-card.calories .stat-value {
    color: #e67e22;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); color: white; border-radius: 8px; padding: 30px;">
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
                            <div class="col-md-4 text-right">
                                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 8px; display: inline-block;">
                                    <div style="font-size: 36px; font-weight: bold; color: white;">
                                        <?php echo $total_count; ?>
                                    </div>
                                    <div style="font-size: 14px; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 1px;">
                                        Aliments
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <?php
        $active_count = 0;
        $categories = [];
        $total_calories = 0;
        foreach ($foods as $food) {
            if (isset($food->is_active) && $food->is_active == 1) {
                $active_count++;
            }
            if (isset($food->category)) {
                if (!isset($categories[$food->category])) {
                    $categories[$food->category] = 0;
                }
                $categories[$food->category]++;
            }
            if (isset($food->calories)) {
                $total_calories += $food->calories;
            }
        }
        $avg_calories = $total_count > 0 ? round($total_calories / $total_count) : 0;
        $category_count = count($categories);
        ?>

        <div class="stats-row row" style="margin-bottom: 20px;">
            <div class="col-md-3">
                <div class="stat-card total">
                    <div class="stat-icon">
                        <i class="fa fa-database"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_count; ?></div>
                    <div class="stat-label">Total Aliments</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card active">
                    <div class="stat-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php echo $active_count; ?></div>
                    <div class="stat-label">Aliments Actifs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card categories">
                    <div class="stat-icon">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div class="stat-value"><?php echo $category_count; ?></div>
                    <div class="stat-label">Catégories</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card calories">
                    <div class="stat-icon">
                        <i class="fa fa-fire"></i>
                    </div>
                    <div class="stat-value"><?php echo $avg_calories; ?></div>
                    <div class="stat-label">Calories Moy.</div>
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
                                                <i class="fa fa-plus-circle"></i> Nouvel Aliment
                                            </a>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo admin_url('dietetic/foods/import'); ?>" class="btn btn-primary" style="padding: 10px 20px;">
                                                <i class="fa fa-upload"></i> Importer CSV
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo admin_url('dietetic/foods/export'); ?>" class="btn btn-info" style="padding: 10px 20px;">
                                            <i class="fa fa-download"></i> Exporter CSV
                                        </a>
                                    </div>
                                    <?php if (is_admin()) { ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo admin_url('dietetic/foods/cleanup_duplicates'); ?>" class="btn btn-warning" style="padding: 10px 20px;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer les doublons ? Cette action est irréversible.');">
                                                <i class="fa fa-clone"></i> Nettoyer Doublons
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if (dietetic_has_permission('delete')) { ?>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-danger" id="bulk-delete-btn" style="display:none; padding: 10px 20px;">
                                                <i class="fa fa-trash-o"></i> Supprimer Sélection
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="padding: 20px; background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="position: relative;">
                                    <input type="text" id="food-search" class="form-control" placeholder="🔍 Rechercher un aliment par nom, catégorie, ou valeur nutritionnelle..." style="padding: 15px 50px 15px 20px; font-size: 16px; border: 2px solid #f39c12; border-radius: 25px; box-shadow: 0 2px 8px rgba(243, 156, 18, 0.2);">
                                    <i class="fa fa-search" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: #f39c12; font-size: 18px;"></i>
                                </div>
                                <div id="search-results-count" style="margin-top: 10px; color: #7f8c8d; font-size: 14px;">
                                    <i class="fa fa-info-circle"></i> <span id="visible-count"><?php echo count($foods); ?></span> aliment(s) affiché(s)
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
                            <table class="table table-hover" id="foods-table" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th width="30">
                                            <?php if (dietetic_has_permission('delete')) { ?>
                                                <input type="checkbox" id="select-all" style="width: 18px; height: 18px; cursor: pointer;">
                                            <?php } ?>
                                        </th>
                                        <th><i class="fa fa-apple" style="color: #e74c3c;"></i> Aliment</th>
                                        <th><i class="fa fa-tag" style="color: #3498db;"></i> Catégorie</th>
                                        <th><i class="fa fa-balance-scale" style="color: #9b59b6;"></i> Portion</th>
                                        <th><i class="fa fa-fire" style="color: #e67e22;"></i> Calories</th>
                                        <th><i class="fa fa-leaf" style="color: #27ae60;"></i> Protéines</th>
                                        <th><i class="fa fa-pie-chart" style="color: #f39c12;"></i> Glucides</th>
                                        <th><i class="fa fa-tint" style="color: #e74c3c;"></i> Lipides</th>
                                        <th class="text-center"><i class="fa fa-cog"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="foods-tbody">
                                    <?php foreach ($foods as $food) { ?>
                                        <tr class="food-row">
                                            <td>
                                                <?php if (dietetic_has_permission('delete')) { ?>
                                                    <input type="checkbox" class="food-checkbox" value="<?php echo $food->id; ?>" style="width: 18px; height: 18px; cursor: pointer;">
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

                        <div id="no-results" style="display: none; padding: 40px; text-align: center;">
                            <i class="fa fa-search" style="font-size: 64px; color: #bdc3c7; margin-bottom: 20px;"></i>
                            <h4 style="color: #7f8c8d;">Aucun aliment trouvé</h4>
                            <p style="color: #95a5a6;">Essayez de modifier votre recherche</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: #ecf0f1; border-left: 4px solid #3498db; padding: 20px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 style="margin-top: 0; color: #2c3e50;">
                                    <i class="fa fa-info-circle" style="color: #3498db;"></i> Conseils d'utilisation
                                </h5>
                                <ul style="margin-bottom: 0; color: #7f8c8d;">
                                    <li>Utilisez la <strong>recherche</strong> pour trouver rapidement un aliment par nom, catégorie ou valeur nutritionnelle</li>
                                    <li>Cochez les cases pour <strong>sélectionner plusieurs aliments</strong> et les supprimer en masse</li>
                                    <li>Cliquez sur <strong>"Nettoyer Doublons"</strong> pour supprimer les entrées en double automatiquement</li>
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

    /* Search box focus */
    #food-search:focus {
        border-color: #e67e22 !important;
        box-shadow: 0 0 12px rgba(243, 156, 18, 0.4) !important;
        outline: none;
    }
</style>

<script>
$(document).ready(function() {
    var ENABLE_DATATABLES = false; // DataTables désactivé pour éviter les conflits

    // Search functionality
    var $searchInput = $('#food-search');
    var $rows = $('#foods-tbody .food-row');
    var $noResults = $('#no-results');
    var $table = $('#foods-table');

    $searchInput.on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm === '') {
            $rows.show();
            $noResults.hide();
            $table.show();
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
                $noResults.show();
                $table.hide();
            } else {
                $noResults.hide();
                $table.show();
            }
        }

        updateResultsCount();
        updateBulkDeleteButton();
    });

    function updateResultsCount() {
        var visibleCount = $rows.filter(':visible').length;
        $('#visible-count').text(visibleCount);
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
            $('#bulk-delete-btn').html('<i class="fa fa-trash-o"></i> Supprimer Sélection (' + checkedCount + ')');
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
});
</script>

<?php init_tail(); ?>
