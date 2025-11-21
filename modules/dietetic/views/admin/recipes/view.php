<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.recipe-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.recipe-header h1 {
    margin: 0 0 10px 0;
    font-size: 32px;
}

.recipe-status-badge {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
}

.recipe-status-badge.pending {
    background: #f39c12;
}

.recipe-status-badge.approved {
    background: #27ae60;
}

.recipe-status-badge.rejected {
    background: #e74c3c;
}

.recipe-meta {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.recipe-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.recipe-gallery img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.recipe-main-photo {
    grid-column: 1 / 3;
    grid-row: 1 / 3;
}

.recipe-main-photo img {
    height: 415px;
}

.ingredients-list {
    list-style: none;
    padding: 0;
}

.ingredients-list li {
    padding: 10px;
    border-bottom: 1px solid #ecf0f1;
    display: flex;
    justify-content: space-between;
}

.ingredients-list li:last-child {
    border-bottom: none;
}

.instructions-list {
    list-style: none;
    padding: 0;
    counter-reset: step-counter;
}

.instructions-list li {
    padding: 15px;
    border-left: 3px solid #e74c3c;
    margin-bottom: 15px;
    background: #f8f9fa;
    border-radius: 4px;
    position: relative;
    padding-left: 60px;
    counter-increment: step-counter;
}

.instructions-list li:before {
    content: counter(step-counter);
    position: absolute;
    left: 15px;
    top: 15px;
    background: #e74c3c;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.nutrition-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.nutrition-item .value {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
}

.nutrition-item .label {
    font-size: 13px;
    color: #7f8c8d;
    margin-top: 5px;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-badge {
    background: #e74c3c;
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 13px;
}

.rating-item {
    padding: 15px;
    border-bottom: 1px solid #ecf0f1;
}

.rating-stars {
    color: #f39c12;
}

.approval-section {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.rejection-section {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="recipe-header">
                    <h1>
                        <?php echo htmlspecialchars($recipe->name); ?>
                        <span class="recipe-status-badge <?php echo $recipe->status; ?>">
                            <?php
                            $status_labels = [
                                'pending' => 'EN ATTENTE',
                                'approved' => 'APPROUVÉE',
                                'rejected' => 'REJETÉE'
                            ];
                            echo $status_labels[$recipe->status] ?? $recipe->status;
                            ?>
                        </span>
                    </h1>

                    <div class="recipe-meta">
                        <div class="recipe-meta-item">
                            <i class="fa fa-user"></i>
                            <span>Créé par : <?php echo htmlspecialchars($recipe->dietitian_name); ?></span>
                        </div>
                        <?php if ($recipe->category) : ?>
                            <div class="recipe-meta-item">
                                <i class="fa fa-tag"></i>
                                <span>
                                    <?php
                                    $categories = [
                                        'breakfast' => 'Petit-déjeuner',
                                        'lunch' => 'Déjeuner',
                                        'dinner' => 'Dîner',
                                        'snack' => 'Collation'
                                    ];
                                    echo $categories[$recipe->category] ?? $recipe->category;
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ($recipe->preparation_time) : ?>
                            <div class="recipe-meta-item">
                                <i class="fa fa-clock-o"></i>
                                <span><?php echo $recipe->preparation_time; ?> minutes</span>
                            </div>
                        <?php endif; ?>
                        <div class="recipe-meta-item">
                            <i class="fa fa-calendar"></i>
                            <span>Créé le : <?php echo date('d/m/Y', strtotime($recipe->created_at)); ?></span>
                        </div>
                        <?php if ($recipe->average_rating > 0) : ?>
                            <div class="recipe-meta-item">
                                <i class="fa fa-star" style="color: #f39c12;"></i>
                                <span><?php echo number_format($recipe->average_rating, 1); ?> / 5 (<?php echo $recipe->ratings_count; ?> avis)</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="<?php echo admin_url('dietetic/recipes'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                        <?php if (dietetic_has_permission('edit')) : ?>
                            <a href="<?php echo admin_url('dietetic/recipes/edit/' . $recipe->id); ?>" class="btn btn-info">
                                <i class="fa fa-pencil"></i> Modifier
                            </a>
                        <?php endif; ?>
                        <?php if (dietetic_has_permission('delete')) : ?>
                            <a href="<?php echo admin_url('dietetic/recipes/delete/' . $recipe->id); ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                                <i class="fa fa-trash"></i> Supprimer
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Approval/Rejection Section (Admins only) -->
                <?php if (is_admin()) : ?>
                    <?php if ($recipe->status == 'pending') : ?>
                        <div class="approval-section">
                            <h4><i class="fa fa-exclamation-triangle"></i> Cette recette est en attente d'approbation</h4>
                            <p>En tant qu'administrateur, vous pouvez approuver ou rejeter cette recette.</p>
                            <div style="margin-top: 15px;">
                                <a href="<?php echo admin_url('dietetic/recipes/approve/' . $recipe->id); ?>"
                                   class="btn btn-success btn-lg">
                                    <i class="fa fa-check"></i> Approuver cette recette
                                </a>
                                <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#rejectModal">
                                    <i class="fa fa-times"></i> Rejeter cette recette
                                </button>
                            </div>
                        </div>
                    <?php elseif ($recipe->status == 'rejected') : ?>
                        <div class="rejection-section">
                            <h4><i class="fa fa-times-circle"></i> Recette Rejetée</h4>
                            <p><strong>Raison du rejet :</strong> <?php echo htmlspecialchars($recipe->rejection_reason); ?></p>
                            <p><strong>Rejetée par :</strong> <?php echo htmlspecialchars($recipe->approved_by_name); ?> le <?php echo date('d/m/Y à H:i', strtotime($recipe->approved_at)); ?></p>
                        </div>
                    <?php elseif ($recipe->status == 'approved') : ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i>
                            <strong>Recette approuvée</strong> par <?php echo htmlspecialchars($recipe->approved_by_name); ?> le <?php echo date('d/m/Y à H:i', strtotime($recipe->approved_at)); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="row">
                    <!-- Photos -->
                    <?php if (!empty($recipe->photos)) : ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-camera"></i> Photos
                                </div>
                                <div class="panel-body">
                                    <div class="recipe-gallery">
                                        <?php foreach ($recipe->photos as $index => $photo) : ?>
                                            <div class="<?php echo (isset($photo->is_main) && $photo->is_main) ? 'recipe-main-photo' : ''; ?>">
                                                <img src="<?php echo base_url($photo->photo_url); ?>"
                                                     alt="<?php echo htmlspecialchars($recipe->name); ?>">
                                                <?php if (isset($photo->is_main) && $photo->is_main) : ?>
                                                    <span class="label label-success" style="position: absolute; top: 10px; left: 10px;">
                                                        Photo principale
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <?php if ($recipe->description) : ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-align-left"></i> Description
                                </div>
                                <div class="panel-body">
                                    <p><?php echo nl2br(htmlspecialchars($recipe->description)); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Ingredients -->
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-list"></i> Ingrédients (<?php echo count($recipe->ingredients); ?>)
                            </div>
                            <div class="panel-body">
                                <ul class="ingredients-list">
                                    <?php foreach ($recipe->ingredients as $ingredient) : ?>
                                        <li>
                                            <span><?php echo htmlspecialchars($ingredient->ingredient_name); ?></span>
                                            <?php if ($ingredient->quantity) : ?>
                                                <span class="text-muted">
                                                    <?php echo number_format($ingredient->quantity, 2); ?>
                                                    <?php echo htmlspecialchars($ingredient->unit); ?>
                                                </span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Nutrition -->
                    <?php if ($recipe->nutrition) : ?>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-heartbeat"></i> Valeurs Nutritionnelles (par portion)
                                </div>
                                <div class="panel-body">
                                    <div class="nutrition-grid">
                                        <?php if ($recipe->nutrition->calories) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->calories, 0); ?></div>
                                                <div class="label">Calories (kcal)</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->protein) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->protein, 1); ?>g</div>
                                                <div class="label">Protéines</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->carbs) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->carbs, 1); ?>g</div>
                                                <div class="label">Glucides</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->fat) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->fat, 1); ?>g</div>
                                                <div class="label">Lipides</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->fiber) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->fiber, 1); ?>g</div>
                                                <div class="label">Fibres</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->sodium) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->sodium, 0); ?>mg</div>
                                                <div class="label">Sodium</div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($recipe->nutrition->sugar) : ?>
                                            <div class="nutrition-item">
                                                <div class="value"><?php echo number_format($recipe->nutrition->sugar, 1); ?>g</div>
                                                <div class="label">Sucre</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Instructions -->
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-tasks"></i> Instructions de Préparation
                            </div>
                            <div class="panel-body">
                                <ul class="instructions-list">
                                    <?php foreach ($recipe->instructions as $instruction) : ?>
                                        <li><?php echo nl2br(htmlspecialchars($instruction->instruction)); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($recipe->tags)) : ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-tags"></i> Tags
                                </div>
                                <div class="panel-body">
                                    <div class="tags-list">
                                        <?php foreach ($recipe->tags as $tag) : ?>
                                            <span class="tag-badge"><?php echo htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Ratings -->
                    <?php if (!empty($ratings)) : ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-star"></i> Avis des Patients (<?php echo count($ratings); ?>)
                                </div>
                                <div class="panel-body">
                                    <?php foreach ($ratings as $rating) : ?>
                                        <div class="rating-item">
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <i class="fa fa-star<?php echo $i <= $rating->rating ? '' : '-o'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <p><strong><?php echo htmlspecialchars($rating->patient_name); ?></strong></p>
                                            <?php if ($rating->comment) : ?>
                                                <p><?php echo nl2br(htmlspecialchars($rating->comment)); ?></p>
                                            <?php endif; ?>
                                            <small class="text-muted"><?php echo date('d/m/Y à H:i', strtotime($rating->created_at)); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Assign to Patient -->
                    <?php if (dietetic_has_permission('edit') && $recipe->status == 'approved') : ?>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-users"></i> Assigner aux Patients
                                </div>
                                <div class="panel-body">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignModal">
                                        <i class="fa fa-plus"></i> Assigner à un patient
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-times-circle"></i> Rejeter la Recette</h4>
            </div>
            <?php echo form_open(admin_url('dietetic/recipes/reject/' . $recipe->id)); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="rejection_reason">Raison du rejet <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger">Rejeter</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-users"></i> Assigner la Recette à un Patient</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                    <select class="form-control selectpicker" id="patient_id" name="patient_id" data-live-search="true" required>
                        <option value="">-- Sélectionner un patient --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="assignment_notes">Notes pour le patient</label>
                    <textarea class="form-control" id="assignment_notes" name="notes" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-assign">Assigner</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load patients when modal opens
    $('#assignModal').on('show.bs.modal', function() {
        console.log('[Recipe View] Chargement des patients pour assignation...');

        $.ajax({
            url: '<?php echo admin_url('dietetic/recipes/get_patients'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(patients) {
                console.log('[Recipe View] Patients chargés:', patients.length);
                const select = $('#patient_id');
                select.empty();
                select.append('<option value="">-- Sélectionner un patient --</option>');

                if (patients.error) {
                    console.error('[Recipe View] Erreur:', patients.error);
                    alert('Erreur lors du chargement des patients: ' + patients.error);
                    return;
                }

                patients.forEach(function(patient) {
                    select.append('<option value="' + patient.id + '">' + patient.name + '</option>');
                    console.log('[Recipe View] Patient ajouté:', patient.name);
                });

                select.selectpicker('refresh');
                console.log('[Recipe View] Selectpicker rafraîchi');
            },
            error: function(xhr, status, error) {
                console.error('[Recipe View] Erreur AJAX:', status, error);
                console.error('[Recipe View] Response:', xhr.responseText);
                alert('Erreur lors du chargement des patients. Vérifiez la console.');
            }
        });
    });

    // Assign recipe to patient
    $('#btn-assign').click(function() {
        const patientId = $('#patient_id').val();
        const notes = $('#assignment_notes').val();

        if (!patientId) {
            alert('Veuillez sélectionner un patient');
            return;
        }

        $.post('<?php echo admin_url('dietetic/recipes/assign'); ?>', {
            recipe_id: <?php echo $recipe->id; ?>,
            patient_id: patientId,
            notes: notes
        }, function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                alert_float('success', data.message);
                $('#assignModal').modal('hide');
                $('#patient_id').val('').selectpicker('refresh');
                $('#assignment_notes').val('');
            } else {
                alert_float('danger', data.message);
            }
        });
    });
});
</script>

<?php init_tail(); ?>
