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
                            Base de données des activités sportives avec calcul automatique des calories (<?php echo count($activities); ?> activités)
                        </p>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="activitiesTable">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th>Catégorie</th>
                                        <th>Kcal/min</th>
                                        <th>Kcal/30min</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($activities)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Aucune activité trouvée</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($activities as $activity): ?>
                                            <tr data-activity-id="<?php echo $activity['id']; ?>">
                                                <td><strong><?php echo htmlspecialchars($activity['name']); ?></strong></td>
                                                <td>
                                                    <?php
                                                    $colorClass = 'label-default';
                                                    if ($activity['category'] === 'Cardio') $colorClass = 'label-primary';
                                                    elseif ($activity['category'] === 'Musculation') $colorClass = 'label-danger';
                                                    elseif ($activity['category'] === 'Sports collectifs') $colorClass = 'label-success';
                                                    elseif ($activity['category'] === 'Arts martiaux') $colorClass = 'label-warning';
                                                    elseif ($activity['category'] === 'Danse') $colorClass = 'label-info';
                                                    elseif ($activity['category'] === 'Yoga/Étirements') $colorClass = 'label-purple';
                                                    ?>
                                                    <span class="label <?php echo $colorClass; ?>"><?php echo htmlspecialchars($activity['category']); ?></span>
                                                </td>
                                                <td class="text-center"><strong><?php echo number_format($activity['kcal_per_minute'], 1); ?></strong></td>
                                                <td class="text-center"><strong class="text-success"><?php echo number_format($activity['kcal_per_minute'] * 30, 0); ?></strong></td>
                                                <td><?php echo $activity['description'] ? htmlspecialchars($activity['description']) : '<span class="text-muted">-</span>'; ?></td>
                                                <td class="text-center">
                                                    <?php if ($activity['is_active'] == 1): ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="label label-default">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-default btn-sm" onclick="editActivity(<?php echo $activity['id']; ?>)" title="Modifier">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" onclick="deleteActivity(<?php echo $activity['id']; ?>)" title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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

// Store activities data for editing
var activitiesData = <?php echo json_encode($activities); ?>;

// Initialize DataTable on page load (simple client-side)
$(document).ready(function() {
    $('#activitiesTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc'], [0, 'asc']], // Sort by category, then name
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
});

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
    var activity = activitiesData.find(a => a.id == id);

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

                // Reload page to show updated data
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
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

                // Reload page to show updated data
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
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
