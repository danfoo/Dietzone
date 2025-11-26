<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-heartbeat"></i>
                                    Gestion des Activités Sportives
                                </h4>
                                <p class="text-muted">
                                    Gérer la base de données des activités sportives et leurs valeurs caloriques
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-info" onclick="openActivityModal()">
                                    <i class="fa fa-plus"></i> Nouvelle Activité
                                </button>
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="activitiesTable">
                                <thead>
                                    <tr>
                                        <th>Nom de l'activité</th>
                                        <th>Catégorie</th>
                                        <th>Kcal/minute</th>
                                        <th>Kcal/30min</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="activitiesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <i class="fa fa-spinner fa-spin"></i> Chargement...
                                        </td>
                                    </tr>
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
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
// CSRF token
var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Load activities on page load
$(document).ready(function() {
    loadActivities();
});

// Load all activities
function loadActivities() {
    $.ajax({
        url: admin_url + 'dietetic/activities/get_activities',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayActivities(response.activities);
            } else {
                $('#activitiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur lors du chargement</td></tr>');
            }
        },
        error: function() {
            $('#activitiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur lors du chargement</td></tr>');
        }
    });
}

// Display activities in table
function displayActivities(activities) {
    let html = '';

    if (activities.length === 0) {
        html = '<tr><td colspan="7" class="text-center text-muted">Aucune activité trouvée</td></tr>';
    } else {
        activities.forEach(activity => {
            let kcal30min = (parseFloat(activity.kcal_per_minute) * 30).toFixed(0);
            let statusBadge = activity.is_active == 1
                ? '<span class="label label-success">Active</span>'
                : '<span class="label label-default">Inactive</span>';

            html += '<tr>';
            html += '<td><strong>' + activity.name + '</strong></td>';
            html += '<td><span class="label label-info">' + activity.category + '</span></td>';
            html += '<td>' + activity.kcal_per_minute + '</td>';
            html += '<td><strong>' + kcal30min + ' kcal</strong></td>';
            html += '<td>' + (activity.description || '-') + '</td>';
            html += '<td>' + statusBadge + '</td>';
            html += '<td>';
            html += '<button class="btn btn-default btn-sm" onclick="editActivity(' + activity.id + ')" title="Modifier">';
            html += '<i class="fa fa-edit"></i>';
            html += '</button> ';
            html += '<button class="btn btn-danger btn-sm" onclick="deleteActivity(' + activity.id + ')" title="Supprimer">';
            html += '<i class="fa fa-trash"></i>';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });
    }

    $('#activitiesTableBody').html(html);
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
    $.ajax({
        url: admin_url + 'dietetic/activities/get_activities',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let activity = response.activities.find(a => a.id == id);
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
        }
    });
}

// Submit form
$('#activityForm').on('submit', function(e) {
    e.preventDefault();

    let activityId = $('#activity_id').val();
    let url = activityId
        ? admin_url + 'dietetic/activities/update_activity/' + activityId
        : admin_url + 'dietetic/activities/add_activity';

    let formData = {
        name: $('#activity_name').val(),
        category: $('#activity_category').val(),
        kcal_per_minute: $('#activity_kcal').val(),
        description: $('#activity_description').val(),
        is_active: $('#activity_is_active').is(':checked') ? 1 : 0,
        csrf_token: $('#csrf_token').val()
    };

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#activityModal').modal('hide');
                $('#csrf_token').val(response.csrf_token);
                csrfHash = response.csrf_token;
                loadActivities();
            } else {
                alert_float('danger', response.message);
                if (response.csrf_token) {
                    $('#csrf_token').val(response.csrf_token);
                    csrfHash = response.csrf_token;
                }
            }
        },
        error: function() {
            alert_float('danger', 'Erreur lors de l\'enregistrement');
        }
    });
});

// Delete activity
function deleteActivity(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
        return;
    }

    var csrfData = {};
    csrfData[csrfTokenName] = csrfHash;

    $.ajax({
        url: admin_url + 'dietetic/activities/delete_activity/' + id,
        type: 'POST',
        data: csrfData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                csrfHash = response.csrf_token;
                loadActivities();
            } else {
                alert_float('danger', response.message);
                if (response.csrf_token) {
                    csrfHash = response.csrf_token;
                }
            }
        },
        error: function() {
            alert_float('danger', 'Erreur lors de la suppression');
        }
    });
}
</script>
