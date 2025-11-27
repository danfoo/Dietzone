<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <button type="button" class="btn btn-info pull-left" onclick="openActivityModal()">
                                <i class="fa fa-plus"></i> Nouvelle Activité
                            </button>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading">

                        <h4 class="no-margin">
                            <i class="fa fa-heartbeat"></i>
                            Gestion des Activités Sportives
                        </h4>
                        <p class="text-muted">
                            Base de données des activités sportives avec calcul automatique des calories
                        </p>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-table" id="activitiesTable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th>Catégorie</th>
                                        <th>Kcal/min</th>
                                        <th>Kcal/30min</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-heartbeat"></i>
                    <span id="modalTitle">Nouvelle Activité</span>
                </h4>
            </div>
            <form id="activityForm">
                <input type="hidden" name="activity_id" id="activity_id">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" id="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="activity_name">Nom de l'activité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="activity_name" name="name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="activity_category">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-control" id="activity_category" name="category" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="Cardio">Cardio</option>
                                    <option value="Musculation">Musculation</option>
                                    <option value="Sports collectifs">Sports collectifs</option>
                                    <option value="Arts martiaux">Arts martiaux</option>
                                    <option value="Danse">Danse</option>
                                    <option value="Yoga/Étirements">Yoga/Étirements</option>
                                    <option value="Autres">Autres</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="activity_kcal">Kcal/minute <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="activity_kcal" name="kcal_per_minute" required>
                                <small class="text-muted">Calories brûlées par minute</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="activity_description">Description</label>
                        <textarea class="form-control" id="activity_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="activity_is_active" value="1" checked>
                                Activité active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
// CSRF token management
var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

var activitiesTable;

// Initialize DataTable on page load
$(document).ready(function() {
    initDataTable();
});

// Initialize DataTable with AJAX
function initDataTable() {
    activitiesTable = $('#activitiesTable').DataTable({
        ajax: {
            url: admin_url + 'dietetic/activities/get_activities',
            type: 'GET',
            dataSrc: function(response) {
                if (response.success) {
                    return response.activities;
                }
                return [];
            }
        },
        columns: [
            {
                data: 'name',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            {
                data: 'category',
                render: function(data, type, row) {
                    var colorClass = 'label-default';
                    if (data === 'Cardio') colorClass = 'label-primary';
                    else if (data === 'Musculation') colorClass = 'label-danger';
                    else if (data === 'Sports collectifs') colorClass = 'label-success';
                    else if (data === 'Arts martiaux') colorClass = 'label-warning';
                    else if (data === 'Danse') colorClass = 'label-info';
                    else if (data === 'Yoga/Étirements') colorClass = 'label-purple';

                    return '<span class="label ' + colorClass + '">' + data + '</span>';
                }
            },
            {
                data: 'kcal_per_minute',
                className: 'text-center',
                render: function(data, type, row) {
                    return '<strong>' + parseFloat(data).toFixed(1) + '</strong>';
                }
            },
            {
                data: 'kcal_per_minute',
                className: 'text-center',
                render: function(data, type, row) {
                    var kcal30 = (parseFloat(data) * 30).toFixed(0);
                    return '<strong class="text-success">' + kcal30 + '</strong>';
                }
            },
            {
                data: 'description',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'is_active',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="label label-success">Active</span>';
                    }
                    return '<span class="label label-default">Inactive</span>';
                }
            },
            {
                data: 'id',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="btn-group">' +
                        '<button class="btn btn-default btn-sm" onclick="editActivity(' + data + ')" title="Modifier">' +
                        '<i class="fa fa-edit"></i>' +
                        '</button>' +
                        '<button class="btn btn-danger btn-sm" onclick="deleteActivity(' + data + ')" title="Supprimer">' +
                        '<i class="fa fa-trash"></i>' +
                        '</button>' +
                        '</div>';
                }
            }
        ],
        order: [[1, 'asc'], [0, 'asc']], // Sort by category, then name
        pageLength: 25,
        language: {
            search: 'Rechercher:',
            lengthMenu: 'Afficher _MENU_ activités',
            info: 'Affichage de _START_ à _END_ sur _TOTAL_ activités',
            infoEmpty: 'Aucune activité',
            infoFiltered: '(filtré de _MAX_ activités au total)',
            paginate: {
                first: 'Premier',
                last: 'Dernier',
                next: 'Suivant',
                previous: 'Précédent'
            },
            emptyTable: 'Aucune activité disponible',
            zeroRecords: 'Aucune activité trouvée'
        },
        responsive: true,
        stateSave: true
    });
}

// Open modal to add new activity
function openActivityModal() {
    $('#activityForm')[0].reset();
    $('#activity_id').val('');
    $('#modalTitle').text('Nouvelle Activité');
    $('#activity_is_active').prop('checked', true);
    $('#csrf_token').val(csrfHash);
    $('#activityModal').modal('show');
}

// Edit activity
function editActivity(id) {
    var rowData = activitiesTable.rows().data();
    var activity = null;

    for (var i = 0; i < rowData.length; i++) {
        if (rowData[i].id == id) {
            activity = rowData[i];
            break;
        }
    }

    if (activity) {
        $('#activity_id').val(activity.id);
        $('#activity_name').val(activity.name);
        $('#activity_category').val(activity.category);
        $('#activity_kcal').val(activity.kcal_per_minute);
        $('#activity_description').val(activity.description);
        $('#activity_is_active').prop('checked', activity.is_active == 1);
        $('#modalTitle').text('Modifier l\'activité');
        $('#csrf_token').val(csrfHash);
        $('#activityModal').modal('show');
    }
}

// Submit form
$('#activityForm').on('submit', function(e) {
    e.preventDefault();

    var activityId = $('#activity_id').val();
    var url = activityId
        ? admin_url + 'dietetic/activities/update_activity/' + activityId
        : admin_url + 'dietetic/activities/add_activity';

    var formData = {
        name: $('#activity_name').val(),
        category: $('#activity_category').val(),
        kcal_per_minute: $('#activity_kcal').val(),
        description: $('#activity_description').val(),
        is_active: $('#activity_is_active').is(':checked') ? 1 : 0
    };

    // Add CSRF token using the correct name
    formData[csrfTokenName] = $('#csrf_token').val();

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#activityModal').modal('hide');

                // Update CSRF token
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                    $('#csrf_token').val(response.csrf_token);
                }

                // Reload table
                activitiesTable.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message || 'Erreur lors de l\'enregistrement');

                // Update CSRF token even on error
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                    $('#csrf_token').val(response.csrf_token);
                }
            }
        },
        error: function(xhr) {
            alert_float('danger', 'Erreur lors de l\'enregistrement');
        }
    });
});

// Delete activity with proper CSRF
function deleteActivity(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
        return;
    }

    var formData = {};
    formData[csrfTokenName] = csrfHash;

    $.ajax({
        url: admin_url + 'dietetic/activities/delete_activity/' + id,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);

                // Update CSRF token
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                }

                // Reload table
                activitiesTable.ajax.reload(null, false);
            } else {
                alert_float('danger', response.message || 'Erreur lors de la suppression');

                // Update CSRF token even on error
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                }
            }
        },
        error: function(xhr) {
            alert_float('danger', 'Erreur lors de la suppression');
        }
    });
}
</script>

<style>
/* Custom purple label for Yoga */
.label-purple {
    background-color: #9c27b0;
}

/* Better spacing for action buttons */
.btn-group .btn {
    margin: 0 2px;
}

/* DataTables custom styling */
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 5px 10px;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 5px;
}
</style>
