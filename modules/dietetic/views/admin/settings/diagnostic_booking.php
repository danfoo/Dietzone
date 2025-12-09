<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-stethoscope"></i> Diagnostic : Système de Prise de Rendez-vous
                        </h4>
                        <hr class="hr-panel-heading">

                        <!-- 1. Tables -->
                        <h5><i class="fa fa-database"></i> Tables de la base de données</h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Table disponibilités</strong></td>
                                <td><?php echo $availability_table_exists ? '<span class="label label-success">✓ Existe</span>' : '<span class="label label-danger">✗ Manquante</span>'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Table types de consultation</strong></td>
                                <td><?php echo $types_table_exists ? '<span class="label label-success">✓ Existe</span>' : '<span class="label label-danger">✗ Manquante</span>'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Table assignations</strong></td>
                                <td><?php echo $assignments_table_exists ? '<span class="label label-success">✓ Existe</span>' : '<span class="label label-danger">✗ Manquante</span>'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Champ 'booked_by_patient'</strong></td>
                                <td><?php echo $has_booking_field ? '<span class="label label-success">✓ Existe</span>' : '<span class="label label-danger">✗ Manquant</span>'; ?></td>
                            </tr>
                        </table>

                        <hr>

                        <!-- 2. Consultation Types -->
                        <h5><i class="fa fa-list"></i> Types de consultation (<?php echo count($consultation_types); ?>)</h5>
                        <?php if (!empty($consultation_types)): ?>
                            <ul>
                                <?php foreach ($consultation_types as $type): ?>
                                    <li><strong><?php echo $type->name; ?></strong> (<?php echo $type->slug; ?>) - <?php echo $type->duration; ?> minutes</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-warning">Aucun type de consultation configuré</div>
                        <?php endif; ?>

                        <hr>

                        <!-- 3. Staff Members -->
                        <h5><i class="fa fa-users"></i> Membres du staff (<?php echo count($staff); ?>)</h5>
                        <?php if (!empty($staff)): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Disponibilités</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staff as $member): ?>
                                        <?php
                                        $member_availabilities = array_filter($availabilities, function($a) use ($member) {
                                            return $a->dietitian_id == $member->staffid;
                                        });
                                        $has_availabilities = count($member_availabilities) > 0;
                                        ?>
                                        <tr>
                                            <td><?php echo $member->staffid; ?></td>
                                            <td><?php echo $member->name; ?></td>
                                            <td><?php echo $member->email; ?></td>
                                            <td>
                                                <?php if ($has_availabilities): ?>
                                                    <span class="label label-success"><?php echo count($member_availabilities); ?> créneaux</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Aucun</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!$has_availabilities): ?>
                                                    <button type="button" class="btn btn-sm btn-success btn-create-availability" data-dietitian-id="<?php echo $member->staffid; ?>" data-dietitian-name="<?php echo $member->name; ?>">
                                                        <i class="fa fa-plus"></i> Créer disponibilités d'exemple
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-success"><i class="fa fa-check"></i> Configuré</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-warning">Aucun membre du staff actif</div>
                        <?php endif; ?>

                        <hr>

                        <!-- 4. Availabilities -->
                        <h5><i class="fa fa-calendar"></i> Disponibilités configurées (<?php echo count($availabilities); ?>)</h5>
                        <?php if (!empty($availabilities)): ?>
                            <?php
                            $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                            ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Diététicien</th>
                                        <th>Jour</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Durée slot</th>
                                        <th>Actif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($availabilities as $avail): ?>
                                        <tr>
                                            <td><?php echo $avail->dietitian_name; ?></td>
                                            <td><?php echo $days[$avail->day_of_week]; ?></td>
                                            <td><?php echo substr($avail->start_time, 0, 5); ?></td>
                                            <td><?php echo substr($avail->end_time, 0, 5); ?></td>
                                            <td><?php echo $avail->slot_duration; ?> min</td>
                                            <td>
                                                <?php if ($avail->is_active): ?>
                                                    <span class="label label-success">Oui</span>
                                                <?php else: ?>
                                                    <span class="label label-danger">Non</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Aucune disponibilité configurée</strong>
                                <p>Les diététiciens doivent avoir des créneaux de disponibilité pour que les patients puissent prendre rendez-vous.</p>
                                <p>Utilisez les boutons ci-dessus pour créer des disponibilités d'exemple.</p>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- 5. Summary -->
                        <?php
                        $all_ok = $availability_table_exists && $types_table_exists && count($availabilities) > 0 && $has_booking_field;
                        ?>

                        <?php if ($all_ok): ?>
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <strong>Système prêt</strong>
                                <p>Le système de prise de rendez-vous est correctement configuré et prêt à l'emploi.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Configuration incomplète</strong>
                                <p>Certains éléments nécessitent une configuration supplémentaire (voir ci-dessus).</p>
                            </div>
                        <?php endif; ?>

                        <div class="text-right">
                            <a href="<?php echo admin_url('dietetic/settings'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn btn-primary" onclick="location.reload();">
                                <i class="fa fa-refresh"></i> Rafraîchir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
(function($) {
    'use strict';

    $(document).on('click', '.btn-create-availability', function() {
        var btn = $(this);
        var dietitianId = btn.data('dietitian-id');
        var dietitianName = btn.data('dietitian-name');

        if (!confirm('Créer des disponibilités d\'exemple pour ' + dietitianName + ' ?\n\nCela créera des créneaux du lundi au vendredi, de 9h à 12h et de 14h à 18h.')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Création...');

        $.ajax({
            url: '<?php echo admin_url('dietetic/settings/create_sample_availability'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                dietitian_id: dietitianId
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                    btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Créer disponibilités d\'exemple');
                }
            },
            error: function() {
                alert_float('danger', 'Erreur lors de la création des disponibilités');
                btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Créer disponibilités d\'exemple');
            }
        });
    });
})(jQuery);
</script>
