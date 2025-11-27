<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-calendar"></i>
                                    Gestion des Disponibilités
                                </h4>
                                <p class="text-muted">Configurez les horaires de travail pour les consultations</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('dietetic/availability/quick_setup?dietitian_id=' . $dietitian_id); ?>"
                                   class="btn btn-info">
                                    <i class="fa fa-magic"></i> Configuration Rapide
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Dietitian Selector -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sélectionner un diététicien</label>
                                    <select class="form-control selectpicker"
                                            data-live-search="true"
                                            onchange="window.location.href='<?php echo admin_url('dietetic/availability?dietitian_id='); ?>' + this.value">
                                        <?php foreach ($dietitians as $dietitian): ?>
                                            <option value="<?php echo $dietitian['staffid']; ?>"
                                                    <?php echo ($dietitian['staffid'] == $dietitian_id) ? 'selected' : ''; ?>>
                                                <?php echo $dietitian['firstname'] . ' ' . $dietitian['lastname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if ($current_dietitian): ?>
                                    <div class="alert alert-info" style="margin-top: 25px;">
                                        <i class="fa fa-user"></i>
                                        <strong><?php echo $current_dietitian->firstname . ' ' . $current_dietitian->lastname; ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo $current_dietitian->email; ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Weekly Schedule Display -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Horaires de la semaine</h4>

                                <?php if (empty($availability_slots)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Aucun horaire configuré</strong>
                                        <p class="mbot0">Utilisez le bouton "Configuration Rapide" pour créer rapidement un emploi du temps hebdomadaire, ou ajoutez des créneaux manuellement ci-dessous.</p>
                                    </div>
                                <?php else: ?>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr style="background: #f9f9f9;">
                                                <th width="15%">Jour</th>
                                                <th width="15%">Début</th>
                                                <th width="15%">Fin</th>
                                                <th width="10%">Durée créneaux</th>
                                                <th width="15%">Lieu</th>
                                                <th width="10%">Statut</th>
                                                <th width="20%" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($days_of_week as $day_num => $day_name): ?>
                                                <?php if (isset($slots_by_day[$day_num])): ?>
                                                    <?php foreach ($slots_by_day[$day_num] as $index => $slot): ?>
                                                        <tr class="<?php echo $slot->is_active ? '' : 'text-muted'; ?>">
                                                            <?php if ($index === 0): ?>
                                                                <td rowspan="<?php echo count($slots_by_day[$day_num]); ?>"
                                                                    style="vertical-align: middle; font-weight: bold; background: #f5f5f5;">
                                                                    <i class="fa fa-calendar-o"></i>
                                                                    <?php echo $day_name; ?>
                                                                </td>
                                                            <?php endif; ?>

                                                            <td><?php echo date('H:i', strtotime($slot->start_time)); ?></td>
                                                            <td><?php echo date('H:i', strtotime($slot->end_time)); ?></td>
                                                            <td><?php echo $slot->slot_duration; ?> min</td>
                                                            <td>
                                                                <?php if ($slot->location): ?>
                                                                    <span class="label label-default">
                                                                        <i class="fa fa-map-marker"></i> <?php echo $slot->location; ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="label label-<?php echo $slot->is_active ? 'success' : 'default'; ?>">
                                                                    <?php echo $slot->is_active ? 'Actif' : 'Inactif'; ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button"
                                                                        class="btn btn-xs btn-info"
                                                                        onclick="editSlot(<?php echo $slot->id; ?>)">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>

                                                                <button type="button"
                                                                        class="btn btn-xs btn-<?php echo $slot->is_active ? 'warning' : 'success'; ?>"
                                                                        onclick="toggleActive(<?php echo $slot->id; ?>)">
                                                                    <i class="fa fa-<?php echo $slot->is_active ? 'pause' : 'play'; ?>"></i>
                                                                </button>

                                                                <a href="<?php echo admin_url('dietetic/availability/delete/' . $slot->id); ?>"
                                                                   class="btn btn-xs btn-danger"
                                                                   onclick="return confirm('Supprimer ce créneau ?');">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr style="background: #fafafa;">
                                                        <td style="font-weight: bold; color: #999;">
                                                            <i class="fa fa-calendar-o"></i>
                                                            <?php echo $day_name; ?>
                                                        </td>
                                                        <td colspan="6" class="text-muted text-center">
                                                            <em>Pas de disponibilité</em>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Add New Slot Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4>
                                    <i class="fa fa-plus-circle"></i>
                                    Ajouter un nouveau créneau
                                </h4>

                                <form method="POST" action="<?php echo admin_url('dietetic/availability/add'); ?>">
                                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                    <input type="hidden" name="dietitian_id" value="<?php echo $dietitian_id; ?>">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Jour <span class="text-danger">*</span></label>
                                                <select name="day_of_week" class="form-control" required>
                                                    <?php foreach ($days_of_week as $day_num => $day_name): ?>
                                                        <option value="<?php echo $day_num; ?>"><?php echo $day_name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Heure de début <span class="text-danger">*</span></label>
                                                <input type="time" name="start_time" class="form-control" value="09:00" required>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Heure de fin <span class="text-danger">*</span></label>
                                                <input type="time" name="end_time" class="form-control" value="17:00" required>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Durée créneaux (min)</label>
                                                <input type="number" name="slot_duration" class="form-control" value="60" min="15" step="15">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Lieu</label>
                                                <input type="text" name="location" class="form-control" placeholder="Ex: Cabinet, En ligne">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-plus"></i> Ajouter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleActive(slotId) {
    $.ajax({
        url: '<?php echo admin_url('dietetic/availability/toggle_active/'); ?>' + slotId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Erreur lors de la modification');
            }
        }
    });
}

function editSlot(slotId) {
    // TODO: Open edit modal
    alert('Fonctionnalité d\'édition à implémenter (modal)');
}
</script>

<?php init_tail(); ?>
