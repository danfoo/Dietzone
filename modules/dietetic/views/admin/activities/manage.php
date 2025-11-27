<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <button type="button" class="btn btn-info pull-left" data-toggle="modal" data-target="#addActivityModal">
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
                            <table class="table table-striped table-bordered dt-table">
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
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($activity->name); ?></strong></td>
                                                <td>
                                                    <?php
                                                    $colorClass = 'label-default';
                                                    if ($activity->category === 'Cardio') $colorClass = 'label-primary';
                                                    elseif ($activity->category === 'Musculation') $colorClass = 'label-danger';
                                                    elseif ($activity->category === 'Sports collectifs') $colorClass = 'label-success';
                                                    elseif ($activity->category === 'Arts martiaux') $colorClass = 'label-warning';
                                                    elseif ($activity->category === 'Danse') $colorClass = 'label-info';
                                                    elseif ($activity->category === 'Yoga/Étirements') $colorClass = 'label-purple';
                                                    ?>
                                                    <span class="label <?php echo $colorClass; ?>"><?php echo htmlspecialchars($activity->category); ?></span>
                                                </td>
                                                <td class="text-center"><strong><?php echo number_format($activity->kcal_per_minute, 1); ?></strong></td>
                                                <td class="text-center"><strong class="text-success"><?php echo number_format($activity->kcal_per_minute * 30, 0); ?></strong></td>
                                                <td><?php echo $activity->description ? htmlspecialchars($activity->description) : '<span class="text-muted">-</span>'; ?></td>
                                                <td class="text-center">
                                                    <?php if ($activity->is_active == 1): ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="label label-default">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-default btn-sm"
                                                                onclick="editActivity(<?php echo $activity->id; ?>, '<?php echo addslashes($activity->name); ?>', '<?php echo $activity->category; ?>', <?php echo $activity->kcal_per_minute; ?>, '<?php echo addslashes($activity->description); ?>', <?php echo $activity->is_active; ?>)"
                                                                title="Modifier">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm"
                                                                onclick="deleteActivity(<?php echo $activity->id; ?>, '<?php echo addslashes($activity->name); ?>')"
                                                                title="Supprimer">
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

<!-- Add Activity Modal -->
<div class="modal fade" id="addActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-heartbeat"></i>
                    Nouvelle Activité
                </h4>
            </div>
            <?php echo form_open(admin_url('dietetic/activities/manage')); ?>
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name">Nom de l'activité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="add_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_category">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-control selectpicker" name="category" id="add_category" required>
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
                                <label for="add_kcal">Kcal/minute <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="kcal_per_minute" id="add_kcal" required>
                                <small class="text-muted">Calories brûlées par minute</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="add_description">Description</label>
                        <textarea class="form-control" name="description" id="add_description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" value="1" checked>
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
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
<div class="modal fade" id="editActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-heartbeat"></i>
                    Modifier l'Activité
                </h4>
            </div>
            <?php echo form_open(admin_url('dietetic/activities/manage')); ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="activity_id" id="edit_activity_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Nom de l'activité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_category">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-control selectpicker" name="category" id="edit_category" required>
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
                                <label for="edit_kcal">Kcal/minute <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="kcal_per_minute" id="edit_kcal" required>
                                <small class="text-muted">Calories brûlées par minute</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                                Activité active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-save"></i> Mettre à jour
                    </button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form (hidden) -->
<div style="display: none;">
    <?php echo form_open(admin_url('dietetic/activities/manage'), ['id' => 'deleteActivityForm']); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="activity_id" id="delete_activity_id">
    <?php echo form_close(); ?>
</div>

<?php init_tail(); ?>

<script>
function editActivity(id, name, category, kcal, description, is_active) {
    $('#edit_activity_id').val(id);
    $('#edit_name').val(name);
    $('#edit_category').val(category).selectpicker('refresh');
    $('#edit_kcal').val(kcal);
    $('#edit_description').val(description);
    $('#edit_is_active').prop('checked', is_active == 1);
    $('#editActivityModal').modal('show');
}

function deleteActivity(id, name) {
    if (confirm('Êtes-vous sûr de vouloir supprimer l\'activité "' + name + '" ?')) {
        $('#delete_activity_id').val(id);
        $('#deleteActivityForm').submit();
    }
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
</style>
