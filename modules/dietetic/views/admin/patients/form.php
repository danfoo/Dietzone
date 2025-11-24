<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Variables CSS - Charte graphique */
:root {
    --primary-color: #01807B;
    --primary-dark: #015a57;
    --primary-light: #019B95;
    --secondary-color: #F3911D;
    --secondary-dark: #e07d0f;
    --secondary-light: #FFA74D;
    --text-dark: #1a202c;
    --text-medium: #2d3748;
    --text-light: #718096;
    --border-color: #e2e8f0;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 8px 20px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 12px 28px rgba(0, 0, 0, 0.12);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.panel_s {
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    border: none;
    overflow: hidden;
    animation: fadeInUp 0.5s ease-out;
}

.panel-body {
    padding: 32px;
}

h4.no-margin {
    color: var(--text-dark);
    font-size: 24px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

h4.no-margin:before {
    content: "👤";
    font-size: 28px;
}

/* Form Sections */
.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    border-left: 4px solid var(--primary-color);
    animation: fadeInUp 0.6s ease-out;
}

.form-section:nth-child(2) {
    border-left-color: var(--secondary-color);
}

.form-section:nth-child(3) {
    border-left-color: #48bb78;
}

.form-section:nth-child(4) {
    border-left-color: #ed8936;
}

.form-section:nth-child(5) {
    border-left-color: #4299e1;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    font-size: 22px;
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

.form-control, select.form-control, textarea.form-control {
    border-radius: 8px;
    border: 2px solid var(--border-color);
    padding: 14px 16px;
    font-size: 15px;
    min-height: 48px;
    transition: var(--transition);
}

textarea.form-control {
    min-height: 100px;
    padding: 14px 16px;
}

.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    outline: none;
}

.selectpicker {
    border-radius: 8px !important;
}

/* Bootstrap Select - Augmenter la hauteur */
.bootstrap-select .btn {
    border-radius: 8px !important;
    border: 2px solid var(--border-color) !important;
    padding: 14px 16px !important;
    font-size: 15px !important;
    min-height: 48px !important;
    transition: var(--transition) !important;
}

.bootstrap-select .btn:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1) !important;
    outline: none !important;
}

.bootstrap-select .dropdown-toggle::after {
    margin-top: 4px;
}

/* Upload Zone */
.upload-zone {
    border: 3px dashed var(--primary-color);
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    background: rgba(1, 128, 123, 0.05);
    margin-top: 12px;
}

.upload-zone:hover {
    background: rgba(1, 128, 123, 0.1);
    border-color: var(--primary-dark);
}

.upload-zone i {
    font-size: 48px;
    color: var(--primary-color);
    margin-bottom: 16px;
    display: block;
}

.upload-zone p {
    margin: 0;
    color: var(--text-dark);
    font-weight: 600;
}

.upload-zone small {
    color: var(--text-light);
    display: block;
    margin-top: 8px;
}

#documentsList {
    margin-top: 16px;
}

.document-item {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.document-item i.fa-file {
    color: var(--primary-color);
    font-size: 20px;
    margin-right: 12px;
}

.document-item .document-name {
    flex: 1;
    font-weight: 600;
    color: var(--text-dark);
}

.document-item .btn-remove {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 4px 12px;
    font-size: 12px;
    cursor: pointer;
    transition: var(--transition);
}

.document-item .btn-remove:hover {
    background: #c82333;
}

/* Buttons */
.btn-bottom-toolbar {
    padding-top: 24px;
    border-top: 2px solid var(--border-color);
    margin-top: 32px;
}

.btn-info {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 28px;
    font-weight: 600;
    transition: var(--transition);
}

.btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
}

.btn-default {
    border-radius: 8px;
    padding: 12px 28px;
    font-weight: 600;
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.btn-default:hover {
    border-color: var(--text-dark);
    background: #f8f9fa;
}

.alert {
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 20px;
    border: none;
}

.alert-info {
    background: #d1f2eb;
    color: var(--primary-dark);
    border-left: 4px solid var(--primary-color);
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr style="margin: 20px 0; border-color: var(--border-color);" />

                        <?php echo form_open_multipart($this->uri->uri_string()); ?>

                        <!-- Section 1: Informations Personnelles -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-user-circle"></i>
                                <span>Informations Personnelles</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php if (!isset($patient)) { ?>
                                        <div class="form-group">
                                            <label for="client_id"><?php echo _l('dietetic_client_name'); ?> *</label>
                                            <select name="client_id" id="client_id" class="form-control selectpicker" data-live-search="true" required>
                                                <option value="">-- <?php echo _l('select'); ?> --</option>
                                                <?php foreach ($clients as $client) { ?>
                                                    <option value="<?php echo $client['userid']; ?>" <?php echo set_select('client_id', $client['userid'], (isset($_GET['client_id']) && $_GET['client_id'] == $client['userid'])); ?>>
                                                        <?php echo $client['company']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <label><?php echo _l('dietetic_client_name'); ?></label>
                                            <p class="form-control-static"><strong><?php echo $patient->client->company; ?></strong></p>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group">
                                        <label for="title">Civilité</label>
                                        <select name="title" id="title" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="M." <?php echo set_select('title', 'M.', isset($patient) && $patient->title == 'M.'); ?>>M.</option>
                                            <option value="Mme" <?php echo set_select('title', 'Mme', isset($patient) && $patient->title == 'Mme'); ?>>Mme</option>
                                            <option value="Mlle" <?php echo set_select('title', 'Mlle', isset($patient) && $patient->title == 'Mlle'); ?>>Mlle</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender"><?php echo _l('dietetic_gender'); ?> <i class="fa fa-info-circle" title="Important pour les calculs spécifiques"></i></label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="">-- <?php echo _l('select'); ?> --</option>
                                            <option value="male" <?php echo set_select('gender', 'male', isset($patient) && $patient->gender == 'male'); ?>>Homme</option>
                                            <option value="female" <?php echo set_select('gender', 'female', isset($patient) && $patient->gender == 'female'); ?>>Femme</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date"><?php echo _l('dietetic_birth_date'); ?></label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo isset($patient) ? $patient->birth_date : ''; ?>" />
                                        <small class="text-muted">L'âge sera calculé automatiquement</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dietitian_id"><?php echo _l('dietetic_dietitian'); ?> *</label>
                                        <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                            <?php foreach ($staff as $member) { ?>
                                                <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($patient) && $patient->dietitian_id == $member['staffid'])); ?>>
                                                    <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="occupation">Profession</label>
                                        <input type="text" class="form-control" name="occupation" id="occupation" value="<?php echo isset($patient) ? $patient->occupation : ''; ?>" placeholder="Ex: Enseignant, Infirmière, Informaticien..." />
                                        <small class="text-muted">Important pour évaluer le niveau d'activité</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="status"><?php echo _l('dietetic_status'); ?></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active" <?php echo set_select('status', 'active', (isset($patient) && $patient->status == 'active') || !isset($patient)); ?>>Actif</option>
                                            <option value="inactive" <?php echo set_select('status', 'inactive', isset($patient) && $patient->status == 'inactive'); ?>>Inactif</option>
                                            <option value="archived" <?php echo set_select('status', 'archived', isset($patient) && $patient->status == 'archived'); ?>>Archivé</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone"><?php echo _l('dietetic_phone'); ?></label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo isset($patient) ? $patient->phone : ''; ?>" placeholder="+221 XX XXX XX XX" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email"><?php echo _l('dietetic_email'); ?></label>
                                        <input type="email" class="form-control" name="email" value="<?php echo isset($patient) ? $patient->email : ''; ?>" placeholder="email@example.com" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="work_type">Type de travail</label>
                                        <select name="work_type" id="work_type" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="sedentary" <?php echo set_select('work_type', 'sedentary', isset($patient) && $patient->work_type == 'sedentary'); ?>>Sédentaire (Bureau)</option>
                                            <option value="light" <?php echo set_select('work_type', 'light', isset($patient) && $patient->work_type == 'light'); ?>>Léger (Debout occasionnellement)</option>
                                            <option value="moderate" <?php echo set_select('work_type', 'moderate', isset($patient) && $patient->work_type == 'moderate'); ?>>Modéré (Marche régulière)</option>
                                            <option value="physical" <?php echo set_select('work_type', 'physical', isset($patient) && $patient->work_type == 'physical'); ?>>Physique (Charges, déplacements)</option>
                                            <option value="very_physical" <?php echo set_select('work_type', 'very_physical', isset($patient) && $patient->work_type == 'very_physical'); ?>>Très physique (Travail intense)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Adresse complète</label>
                                        <textarea class="form-control" name="address" id="address" rows="2" placeholder="Rue, Quartier, Ville, Pays"><?php echo isset($patient) ? $patient->address : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1.5: Informations Spécifiques Femmes (Conditionnelle) -->
                        <div class="form-section" id="women-section" style="display: none; border-left-color: #e91e63;">
                            <div class="section-title" style="color: #e91e63;">
                                <i class="fa fa-venus"></i>
                                <span>Informations Spécifiques Femmes</span>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="is_pregnant">Grossesse en cours ?</label>
                                        <select name="is_pregnant" id="is_pregnant" class="form-control">
                                            <option value="no" <?php echo set_select('is_pregnant', 'no', !isset($patient) || (isset($patient) && $patient->is_pregnant == 'no')); ?>>Non</option>
                                            <option value="yes" <?php echo set_select('is_pregnant', 'yes', isset($patient) && $patient->is_pregnant == 'yes'); ?>>Oui</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3" id="pregnancy-months-group" style="display: none;">
                                    <div class="form-group">
                                        <label for="pregnancy_months">Mois de grossesse <i class="fa fa-info-circle" title="1 à 9 mois"></i></label>
                                        <input type="number" class="form-control" name="pregnancy_months" id="pregnancy_months" min="1" max="9" value="<?php echo isset($patient) ? $patient->pregnancy_months : ''; ?>" placeholder="1-9" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="breastfeeding">Allaitement ?</label>
                                        <select name="breastfeeding" id="breastfeeding" class="form-control">
                                            <option value="no" <?php echo set_select('breastfeeding', 'no', !isset($patient) || (isset($patient) && $patient->breastfeeding == 'no')); ?>>Non</option>
                                            <option value="yes" <?php echo set_select('breastfeeding', 'yes', isset($patient) && $patient->breastfeeding == 'yes'); ?>>Oui</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="menstrual_cycle">Cycle menstruel</label>
                                        <select name="menstrual_cycle" id="menstrual_cycle" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="regular" <?php echo set_select('menstrual_cycle', 'regular', isset($patient) && $patient->menstrual_cycle == 'regular'); ?>>Régulier</option>
                                            <option value="irregular" <?php echo set_select('menstrual_cycle', 'irregular', isset($patient) && $patient->menstrual_cycle == 'irregular'); ?>>Irrégulier</option>
                                            <option value="absent" <?php echo set_select('menstrual_cycle', 'absent', isset($patient) && $patient->menstrual_cycle == 'absent'); ?>>Absent (aménorrhée)</option>
                                            <option value="menopause" <?php echo set_select('menstrual_cycle', 'menopause', isset($patient) && $patient->menstrual_cycle == 'menopause'); ?>>Ménopause</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="alert" style="background: #fce4ec; border-left: 4px solid #e91e63; color: #880e4f;">
                                <i class="fa fa-info-circle"></i> <strong>Important:</strong> Ces informations permettent d'adapter les besoins nutritionnels spécifiques à la grossesse et l'allaitement.
                            </div>
                        </div>

                        <!-- Section 2: Données Physiques, Mensurations & Objectifs -->
                        <div class="form-section" style="border-left-color: #2ecc71;">
                            <div class="section-title" style="color: #2ecc71;">
                                <i class="fa fa-heartbeat"></i>
                                <span>Données Physiques, Mensurations & Objectifs</span>
                            </div>

                            <h5 style="color: #2ecc71; margin-bottom: 15px; font-weight: 600;">
                                <i class="fa fa-weight"></i> Poids et Taille
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="initial_weight"><?php echo _l('dietetic_initial_weight'); ?> (kg) <i class="fa fa-info-circle" title="Poids actuel du patient"></i></label>
                                        <input type="number" step="0.1" class="form-control" id="initial_weight" name="initial_weight" value="<?php echo isset($patient) ? $patient->initial_weight : ''; ?>" placeholder="70.5" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="target_weight"><?php echo _l('dietetic_target_weight'); ?> (kg) <i class="fa fa-info-circle" title="Objectif de poids"></i></label>
                                        <input type="number" step="0.1" class="form-control" name="target_weight" value="<?php echo isset($patient) ? $patient->target_weight : ''; ?>" placeholder="65.0" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height"><?php echo _l('dietetic_height'); ?> (cm) <i class="fa fa-info-circle" title="Taille en centimètres"></i></label>
                                        <input type="number" step="0.1" class="form-control" id="height" name="height" value="<?php echo isset($patient) ? $patient->height : ''; ?>" placeholder="170" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bmi_display">IMC (kg/m²)</label>
                                        <input type="text" class="form-control" id="bmi_display" readonly style="background: #f0f0f0; font-weight: bold; color: #2ecc71;" placeholder="Calculé auto" />
                                        <small class="text-muted">Calculé automatiquement</small>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #2ecc71; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-arrows-h"></i> Circonférences et Mensurations
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="waist_circumference">Tour de taille (cm) <i class="fa fa-info-circle" title="Mesurer à l'ombilic"></i></label>
                                        <input type="number" step="0.1" class="form-control" id="waist_circumference" name="waist_circumference" value="<?php echo isset($patient) ? $patient->waist_circumference : ''; ?>" placeholder="85" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hip_circumference">Tour de hanches (cm) <i class="fa fa-info-circle" title="Au point le plus large"></i></label>
                                        <input type="number" step="0.1" class="form-control" id="hip_circumference" name="hip_circumference" value="<?php echo isset($patient) ? $patient->hip_circumference : ''; ?>" placeholder="100" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="waist_hip_ratio_display">Rapport T/H</label>
                                        <input type="text" class="form-control" id="waist_hip_ratio_display" readonly style="background: #f0f0f0; font-weight: bold; color: #2ecc71;" placeholder="Calculé auto" />
                                        <small class="text-muted">Taille/Hanches</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="body_shape_display">Morphologie</label>
                                        <input type="text" class="form-control" id="body_shape_display" readonly style="background: #f0f0f0; font-weight: bold; color: #9b59b6;" placeholder="Auto" />
                                        <small class="text-muted">Android/Gynoïde</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="neck_circumference">Tour de cou (cm)</label>
                                        <input type="number" step="0.1" class="form-control" name="neck_circumference" value="<?php echo isset($patient) ? $patient->neck_circumference : ''; ?>" placeholder="35" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="chest_circumference">Tour de poitrine (cm)</label>
                                        <input type="number" step="0.1" class="form-control" name="chest_circumference" value="<?php echo isset($patient) ? $patient->chest_circumference : ''; ?>" placeholder="95" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="arm_circumference">Tour de bras (cm) <i class="fa fa-info-circle" title="Biceps relâché"></i></label>
                                        <input type="number" step="0.1" class="form-control" name="arm_circumference" value="<?php echo isset($patient) ? $patient->arm_circumference : ''; ?>" placeholder="28" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="thigh_circumference">Tour de cuisse (cm)</label>
                                        <input type="number" step="0.1" class="form-control" name="thigh_circumference" value="<?php echo isset($patient) ? $patient->thigh_circumference : ''; ?>" placeholder="55" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="calf_circumference">Tour de mollet (cm)</label>
                                        <input type="number" step="0.1" class="form-control" name="calf_circumference" value="<?php echo isset($patient) ? $patient->calf_circumference : ''; ?>" placeholder="36" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="activity_level"><?php echo _l('dietetic_activity_level'); ?></label>
                                        <select name="activity_level" id="activity_level" class="form-control">
                                            <option value="">-- <?php echo _l('select'); ?> --</option>
                                            <?php foreach (dietetic_get_activity_levels() as $key => $label) { ?>
                                                <option value="<?php echo $key; ?>" <?php echo set_select('activity_level', $key, isset($patient) && $patient->activity_level == $key); ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="physical_activity_details">Détails activité physique</label>
                                        <input type="text" class="form-control" name="physical_activity_details" value="<?php echo isset($patient) ? $patient->physical_activity_details : ''; ?>" placeholder="Ex: Marche 30min/jour, Gym 3x/semaine..." />
                                    </div>
                                </div>
                            </div>

                            <div class="alert" style="background: #e8f8f5; border-left: 4px solid #2ecc71; color: #0e6655;">
                                <i class="fa fa-lightbulb-o"></i> <strong>Astuce:</strong> Les mensurations permettent de suivre l'évolution de la composition corporelle au-delà du simple poids. Mesurer régulièrement pour un suivi optimal!
                            </div>

                            <h5 style="color: #2ecc71; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-bullseye"></i> Objectifs
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="objective"><?php echo _l('dietetic_objective'); ?></label>
                                        <textarea class="form-control" name="objective" rows="3" placeholder="Ex: Perdre 5kg en 3 mois, Améliorer l'énergie, Réduire le cholestérol..."><?php echo isset($patient) ? $patient->objective : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Informations Médicales -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-medkit"></i>
                                <span>Informations Médicales</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medical_conditions"><?php echo _l('dietetic_medical_conditions'); ?></label>
                                        <textarea class="form-control" name="medical_conditions" rows="3"><?php echo isset($patient) ? $patient->medical_conditions : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="allergies"><?php echo _l('dietetic_allergies'); ?></label>
                                        <textarea class="form-control" name="allergies" rows="2"><?php echo isset($patient) ? $patient->allergies : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medications"><?php echo _l('dietetic_medications'); ?></label>
                                        <textarea class="form-control" name="medications" rows="2"><?php echo isset($patient) ? $patient->medications : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="dietary_preferences"><?php echo _l('dietetic_dietary_preferences'); ?></label>
                                        <input type="text" class="form-control" name="dietary_preferences" value="<?php echo isset($patient) ? $patient->dietary_preferences : ''; ?>" placeholder="e.g., vegetarian, vegan, halal" />
                                    </div>

                                    <div class="form-group">
                                        <label for="lifestyle_notes"><?php echo _l('dietetic_lifestyle_notes'); ?></label>
                                        <textarea class="form-control" name="lifestyle_notes" rows="2"><?php echo isset($patient) ? $patient->lifestyle_notes : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Contact d'Urgence -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-phone-square"></i>
                                <span>Contact d'Urgence</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emergency_contact">Nom du Contact d'Urgence</label>
                                        <input type="text" class="form-control" name="emergency_contact" value="<?php echo isset($patient) ? $patient->emergency_contact : ''; ?>" placeholder="Nom complet du contact" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emergency_phone">Téléphone d'Urgence</label>
                                        <input type="tel" class="form-control" name="emergency_phone" value="<?php echo isset($patient) ? $patient->emergency_phone : ''; ?>" placeholder="+221 XX XXX XX XX" />
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Ces informations sont importantes en cas d'urgence médicale.
                            </div>
                        </div>

                        <!-- Section 5: Documents Médicaux -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-file-text"></i>
                                <span>Documents Médicaux</span>
                            </div>

                            <div class="upload-zone" onclick="document.getElementById('documentInput').click()">
                                <i class="fa fa-cloud-upload"></i>
                                <p>Cliquez pour uploader des documents</p>
                                <small>PDF, images (max 10MB par fichier)</small>
                            </div>
                            <input type="file" id="documentInput" accept=".pdf,.jpg,.jpeg,.png" multiple style="display: none;">

                            <div id="documentsList"></div>

                            <div class="alert alert-info" style="margin-top: 16px;">
                                <i class="fa fa-info-circle"></i> Vous pouvez uploader plusieurs documents (résultats d'analyses, ordonnances, etc.)
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> <?php echo _l('cancel'); ?>
                            </a>
                            <button type="submit" class="btn btn-info" id="submitBtn">
                                <i class="fa fa-check"></i> <?php echo _l('submit'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store documents to upload
let documentsToUpload = [];

// Handle document selection
document.getElementById('documentInput').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);

    files.forEach(file => {
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert('Le fichier ' + file.name + ' dépasse 10MB');
            return;
        }

        // Validate file type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Type de fichier non autorisé pour ' + file.name);
            return;
        }

        // Add to list
        documentsToUpload.push(file);
    });

    // Update display
    updateDocumentsList();

    // Reset input
    e.target.value = '';
});

function updateDocumentsList() {
    const listDiv = document.getElementById('documentsList');

    if (documentsToUpload.length === 0) {
        listDiv.innerHTML = '';
        return;
    }

    let html = '';
    documentsToUpload.forEach((file, index) => {
        const sizeKB = (file.size / 1024).toFixed(1);
        html += `
            <div class="document-item">
                <i class="fa fa-file"></i>
                <span class="document-name">${file.name} (${sizeKB} KB)</span>
                <button type="button" class="btn-remove" onclick="removeDocument(${index})">
                    <i class="fa fa-trash"></i> Retirer
                </button>
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

function removeDocument(index) {
    documentsToUpload.splice(index, 1);
    updateDocumentsList();
}

// Handle form submission
document.querySelector('form').addEventListener('submit', function(e) {
    if (documentsToUpload.length > 0) {
        e.preventDefault();

        const formData = new FormData(this);

        // Add documents
        documentsToUpload.forEach((file, index) => {
            formData.append('documents[]', file);
        });

        // Disable submit button
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enregistrement en cours...';

        // Submit with AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Redirect or show success
            window.location.href = '<?php echo admin_url('dietetic/patients'); ?>';
        })
        .catch(error => {
            alert('Erreur lors de l\'enregistrement');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-check"></i> <?php echo _l('submit'); ?>';
        });
    }
});
</script>

<?php init_tail(); ?>
