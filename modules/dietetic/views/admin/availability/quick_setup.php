<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Header -->
                        <div class="text-center" style="margin-bottom: 30px;">
                            <i class="fa fa-magic" style="font-size: 48px; color: #01807B;"></i>
                            <h3 style="margin-top: 15px; margin-bottom: 5px;">Configuration Rapide</h3>
                            <p class="text-muted">Créez un emploi du temps hebdomadaire en quelques clics</p>
                            <?php if ($dietitian): ?>
                                <div class="alert alert-info" style="margin-top: 15px;">
                                    <strong><?php echo $dietitian->firstname . ' ' . $dietitian->lastname; ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <!-- Quick Setup Form -->
                        <form method="POST" action="<?php echo admin_url('dietetic/availability/quick_setup?dietitian_id=' . $dietitian_id); ?>">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <!-- Step 1: Select Working Days -->
                            <div class="form-group">
                                <label class="control-label">
                                    <i class="fa fa-calendar-check-o"></i>
                                    Jours de travail <span class="text-danger">*</span>
                                </label>
                                <p class="text-muted">Sélectionnez les jours où vous êtes disponible pour des consultations</p>

                                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="1" id="day_1" checked>
                                                <label for="day_1">
                                                    <strong>Lundi</strong>
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="2" id="day_2" checked>
                                                <label for="day_2">
                                                    <strong>Mardi</strong>
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="3" id="day_3" checked>
                                                <label for="day_3">
                                                    <strong>Mercredi</strong>
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="4" id="day_4" checked>
                                                <label for="day_4">
                                                    <strong>Jeudi</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="5" id="day_5" checked>
                                                <label for="day_5">
                                                    <strong>Vendredi</strong>
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="6" id="day_6">
                                                <label for="day_6">
                                                    <strong>Samedi</strong>
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="working_days[]" value="0" id="day_0">
                                                <label for="day_0">
                                                    <strong>Dimanche</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Step 2: Working Hours -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">
                                            <i class="fa fa-clock-o"></i>
                                            Heure de début <span class="text-danger">*</span>
                                        </label>
                                        <input type="time"
                                               name="start_time"
                                               class="form-control"
                                               value="09:00"
                                               required
                                               style="font-size: 16px; padding: 12px;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">
                                            <i class="fa fa-clock-o"></i>
                                            Heure de fin <span class="text-danger">*</span>
                                        </label>
                                        <input type="time"
                                               name="end_time"
                                               class="form-control"
                                               value="17:00"
                                               required
                                               style="font-size: 16px; padding: 12px;">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Step 3: Lunch Break -->
                            <div class="form-group">
                                <label class="control-label">
                                    <i class="fa fa-cutlery"></i>
                                    Pause déjeuner
                                </label>
                                <p class="text-muted">Souhaitez-vous une pause déjeuner de 12h à 14h ?</p>

                                <div class="radio radio-primary">
                                    <input type="radio" name="lunch_break" value="1" id="lunch_yes" checked>
                                    <label for="lunch_yes">
                                        <strong>Oui</strong> - Créer 2 créneaux (matin 9h-12h et après-midi 14h-17h)
                                    </label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio" name="lunch_break" value="0" id="lunch_no">
                                    <label for="lunch_no">
                                        <strong>Non</strong> - Créer 1 créneau continu pour toute la journée
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <!-- Summary -->
                            <div class="alert alert-info">
                                <h4><i class="fa fa-info-circle"></i> Résumé</h4>
                                <p class="mbot0">
                                    Cette configuration va créer automatiquement vos créneaux de disponibilité
                                    pour tous les jours sélectionnés. Vous pourrez les modifier individuellement
                                    par la suite si nécessaire.
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="text-center" style="margin-top: 30px;">
                                <a href="<?php echo admin_url('dietetic/availability?dietitian_id=' . $dietitian_id); ?>"
                                   class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg" style="margin-left: 10px;">
                                    <i class="fa fa-magic"></i> Créer les créneaux
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

                <!-- Help Card -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-question-circle"></i> Besoin d'aide ?</h4>
                        <p class="text-muted mbot0">
                            <strong>Conseil :</strong> Vous pouvez toujours modifier, désactiver ou supprimer
                            les créneaux créés depuis la page de gestion des disponibilités. Cette configuration
                            rapide est juste un point de départ pour vous faire gagner du temps.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
