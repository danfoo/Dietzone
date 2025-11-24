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

                        <!-- Section 3: Antécédents & Historique Médical (ENRICHIE) -->
                        <div class="form-section" style="border-left-color: #e67e22;">
                            <div class="section-title" style="color: #e67e22;">
                                <i class="fa fa-medkit"></i>
                                <span>Antécédents & Historique Médical</span>
                            </div>

                            <h5 style="color: #e67e22; margin-bottom: 15px; font-weight: 600;">
                                <i class="fa fa-stethoscope"></i> État de Santé Actuel
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medical_conditions"><?php echo _l('dietetic_medical_conditions'); ?> <i class="fa fa-info-circle" title="Diabète, hypertension, cholestérol..."></i></label>
                                        <textarea class="form-control" name="medical_conditions" rows="3" placeholder="Ex: Diabète type 2, Hypertension artérielle, Hypothyroïdie..."><?php echo isset($patient) ? $patient->medical_conditions : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="medications"><?php echo _l('dietetic_medications'); ?> <i class="fa fa-info-circle" title="Nom, dosage et fréquence"></i></label>
                                        <textarea class="form-control" name="medications" rows="3" placeholder="Ex: Metformine 500mg 2x/jour, Levothyrox 75µg 1x/jour..."><?php echo isset($patient) ? $patient->medications : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="allergies"><?php echo _l('dietetic_allergies'); ?> <i class="fa fa-info-circle" title="Alimentaires et médicamenteuses"></i></label>
                                        <textarea class="form-control" name="allergies" rows="2" placeholder="Ex: Arachides, Lactose, Gluten, Pénicilline..."><?php echo isset($patient) ? $patient->allergies : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="supplements">Compléments alimentaires & Vitamines</label>
                                        <textarea class="form-control" name="supplements" rows="2" placeholder="Ex: Vitamine D, Oméga-3, Magnésium..."><?php echo isset($patient) ? $patient->supplements : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="recent_blood_work">Dernières analyses sanguines <i class="fa fa-info-circle" title="Date et résultats clés"></i></label>
                                        <textarea class="form-control" name="recent_blood_work" rows="2" placeholder="Ex: Glycémie à jeun 1.2g/L, Cholestérol total 2.5g/L (01/2025)..."><?php echo isset($patient) ? $patient->recent_blood_work : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #e67e22; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-history"></i> Historique de Poids & Régimes
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight_history">Historique de poids</label>
                                        <textarea class="form-control" name="weight_history" rows="2" placeholder="Ex: Poids maximum 90kg (2020), Poids minimum 65kg (2018)..."><?php echo isset($patient) ? $patient->weight_history : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="previous_diets">Régimes précédents <i class="fa fa-info-circle" title="Régimes tentés et résultats"></i></label>
                                        <textarea class="form-control" name="previous_diets" rows="2" placeholder="Ex: Régime hypocalorique (-8kg puis reprise), Keto (difficultés)..."><?php echo isset($patient) ? $patient->previous_diets : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="weight_gain_triggers">Facteurs de prise de poids</label>
                                        <textarea class="form-control" name="weight_gain_triggers" rows="2" placeholder="Ex: Grossesse, Arrêt du sport, Stress, Médicaments..."><?php echo isset($patient) ? $patient->weight_gain_triggers : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #e67e22; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-users"></i> Antécédents Familiaux
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="family_history">Maladies familiales <i class="fa fa-info-circle" title="Diabète, obésité, maladies cardiovasculaires..."></i></label>
                                        <textarea class="form-control" name="family_history" rows="2" placeholder="Ex: Mère diabétique, Père hypertension, Grand-mère obésité..."><?php echo isset($patient) ? $patient->family_history : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surgeries">Chirurgies & Hospitalisations</label>
                                        <textarea class="form-control" name="surgeries" rows="2" placeholder="Ex: Appendicectomie (2015), Césarienne (2018)..."><?php echo isset($patient) ? $patient->surgeries : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #e67e22; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-moon-o"></i> Sommeil & Santé Mentale
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sleep_hours">Heures de sommeil/nuit</label>
                                        <input type="number" step="0.5" class="form-control" name="sleep_hours" value="<?php echo isset($patient) ? $patient->sleep_hours : ''; ?>" placeholder="7.5" min="0" max="24" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sleep_quality">Qualité du sommeil</label>
                                        <select name="sleep_quality" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="very_good" <?php echo set_select('sleep_quality', 'very_good', isset($patient) && $patient->sleep_quality == 'very_good'); ?>>Très bonne</option>
                                            <option value="good" <?php echo set_select('sleep_quality', 'good', isset($patient) && $patient->sleep_quality == 'good'); ?>>Bonne</option>
                                            <option value="average" <?php echo set_select('sleep_quality', 'average', isset($patient) && $patient->sleep_quality == 'average'); ?>>Moyenne</option>
                                            <option value="poor" <?php echo set_select('sleep_quality', 'poor', isset($patient) && $patient->sleep_quality == 'poor'); ?>>Mauvaise</option>
                                            <option value="very_poor" <?php echo set_select('sleep_quality', 'very_poor', isset($patient) && $patient->sleep_quality == 'very_poor'); ?>>Très mauvaise</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="stress_level">Niveau de stress <i class="fa fa-info-circle" title="1=Très faible, 10=Extrême"></i></label>
                                        <input type="number" class="form-control" name="stress_level" value="<?php echo isset($patient) ? $patient->stress_level : ''; ?>" placeholder="5" min="1" max="10" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mental_health">Santé mentale</label>
                                        <select name="mental_health" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="excellent" <?php echo set_select('mental_health', 'excellent', isset($patient) && $patient->mental_health == 'excellent'); ?>>Excellente</option>
                                            <option value="good" <?php echo set_select('mental_health', 'good', isset($patient) && $patient->mental_health == 'good'); ?>>Bonne</option>
                                            <option value="moderate" <?php echo set_select('mental_health', 'moderate', isset($patient) && $patient->mental_health == 'moderate'); ?>>Modérée</option>
                                            <option value="anxiety" <?php echo set_select('mental_health', 'anxiety', isset($patient) && $patient->mental_health == 'anxiety'); ?>>Anxiété</option>
                                            <option value="depression" <?php echo set_select('mental_health', 'depression', isset($patient) && $patient->mental_health == 'depression'); ?>>Dépression</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stress_eating">Alimentation émotionnelle ? <i class="fa fa-info-circle" title="Manger en réponse au stress/émotions"></i></label>
                                        <select name="stress_eating" class="form-control">
                                            <option value="no" <?php echo set_select('stress_eating', 'no', !isset($patient) || (isset($patient) && $patient->stress_eating == 'no')); ?>>Non</option>
                                            <option value="sometimes" <?php echo set_select('stress_eating', 'sometimes', isset($patient) && $patient->stress_eating == 'sometimes'); ?>>Parfois</option>
                                            <option value="often" <?php echo set_select('stress_eating', 'often', isset($patient) && $patient->stress_eating == 'often'); ?>>Souvent</option>
                                            <option value="always" <?php echo set_select('stress_eating', 'always', isset($patient) && $patient->stress_eating == 'always'); ?>>Toujours</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="eating_disorders_history">Historique de troubles alimentaires ?</label>
                                        <textarea class="form-control" name="eating_disorders_history" rows="2" placeholder="Ex: Anorexie, boulimie, hyperphagie boulimique..."><?php echo isset($patient) ? $patient->eating_disorders_history : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="alert" style="background: #fef5e7; border-left: 4px solid #e67e22; color: #7d4d1a;">
                                <i class="fa fa-exclamation-triangle"></i> <strong>Confidentialité:</strong> Toutes ces informations médicales sont strictement confidentielles et sécurisées.
                            </div>
                        </div>

                        <!-- Section 4: Système Digestif & Intolérances (NOUVELLE) -->
                        <div class="form-section" style="border-left-color: #3498db;">
                            <div class="section-title" style="color: #3498db;">
                                <i class="fa fa-heartbeat"></i>
                                <span>Système Digestif & Intolérances</span>
                            </div>

                            <h5 style="color: #3498db; margin-bottom: 15px; font-weight: 600;">
                                <i class="fa fa-cutlery"></i> Système Digestif
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="digestive_issues">Problèmes digestifs</label>
                                        <select name="digestive_issues" id="digestive_issues" class="form-control">
                                            <option value="none" <?php echo set_select('digestive_issues', 'none', !isset($patient) || (isset($patient) && $patient->digestive_issues == 'none')); ?>>Aucun</option>
                                            <option value="constipation" <?php echo set_select('digestive_issues', 'constipation', isset($patient) && $patient->digestive_issues == 'constipation'); ?>>Constipation</option>
                                            <option value="diarrhea" <?php echo set_select('digestive_issues', 'diarrhea', isset($patient) && $patient->digestive_issues == 'diarrhea'); ?>>Diarrhée</option>
                                            <option value="bloating" <?php echo set_select('digestive_issues', 'bloating', isset($patient) && $patient->digestive_issues == 'bloating'); ?>>Ballonnements</option>
                                            <option value="reflux" <?php echo set_select('digestive_issues', 'reflux', isset($patient) && $patient->digestive_issues == 'reflux'); ?>>Reflux gastrique (RGO)</option>
                                            <option value="ibs" <?php echo set_select('digestive_issues', 'ibs', isset($patient) && $patient->digestive_issues == 'ibs'); ?>>Syndrome intestin irritable (SII)</option>
                                            <option value="multiple" <?php echo set_select('digestive_issues', 'multiple', isset($patient) && $patient->digestive_issues == 'multiple'); ?>>Plusieurs symptômes</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bowel_frequency">Fréquence des selles/jour</label>
                                        <input type="number" step="0.5" class="form-control" name="bowel_frequency" value="<?php echo isset($patient) ? $patient->bowel_frequency : ''; ?>" placeholder="1.0" min="0" max="10" />
                                        <small class="text-muted">Normal: 1-3 fois/jour</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="water_intake">Consommation d'eau (L/jour) <i class="fa fa-tint"></i></label>
                                        <input type="number" step="0.1" class="form-control" name="water_intake" value="<?php echo isset($patient) ? $patient->water_intake : ''; ?>" placeholder="1.5" min="0" max="10" />
                                        <small class="text-muted">Recommandé: 1.5-2L</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="digestive_details">Détails des problèmes digestifs</label>
                                        <textarea class="form-control" name="digestive_details" rows="2" placeholder="Ex: Ballonnements après les repas, constipation chronique depuis 2 ans..."><?php echo isset($patient) ? $patient->digestive_details : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #3498db; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-ban"></i> Intolérances & Sensibilités Alimentaires
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="lactose_intolerance" value="1" <?php echo (isset($patient) && $patient->lactose_intolerance) ? 'checked' : ''; ?>>
                                            Intolérance au lactose
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="gluten_intolerance" value="1" <?php echo (isset($patient) && $patient->gluten_intolerance) ? 'checked' : ''; ?>>
                                            Intolérance au gluten / Maladie cœliaque
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="fructose_intolerance" value="1" <?php echo (isset($patient) && $patient->fructose_intolerance) ? 'checked' : ''; ?>>
                                            Intolérance au fructose
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="fodmap_sensitivity" value="1" <?php echo (isset($patient) && $patient->fodmap_sensitivity) ? 'checked' : ''; ?>>
                                            Sensibilité aux FODMAPs
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="histamine_intolerance" value="1" <?php echo (isset($patient) && $patient->histamine_intolerance) ? 'checked' : ''; ?>>
                                            Intolérance à l'histamine
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="caffeine_sensitivity" value="1" <?php echo (isset($patient) && $patient->caffeine_sensitivity) ? 'checked' : ''; ?>>
                                            Sensibilité à la caféine
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="food_dislikes">Aliments non tolérés / Aversions</label>
                                        <textarea class="form-control" name="food_dislikes" rows="4" placeholder="Ex: Oignons (ballonnements), Poivrons (indigestion), Œufs (nausées)..."><?php echo isset($patient) ? $patient->food_dislikes : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #3498db; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-leaf"></i> Préférences & Restrictions Alimentaires
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dietary_preferences"><?php echo _l('dietetic_dietary_preferences'); ?> <i class="fa fa-info-circle" title="Végétarien, végan, halal, etc."></i></label>
                                        <input type="text" class="form-control" name="dietary_preferences" value="<?php echo isset($patient) ? $patient->dietary_preferences : ''; ?>" placeholder="Ex: Végétarien, Végan, Halal, Casher, Pescetarien..." />
                                    </div>

                                    <div class="form-group">
                                        <label for="favorite_foods">Aliments favoris <i class="fa fa-heart" style="color: #e74c3c;"></i></label>
                                        <textarea class="form-control" name="favorite_foods" rows="2" placeholder="Ex: Poulet grillé, Avocat, Riz basmati, Chocolat noir..."><?php echo isset($patient) ? $patient->favorite_foods : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cultural_food_preferences">Préférences culturelles alimentaires</label>
                                        <textarea class="form-control" name="cultural_food_preferences" rows="2" placeholder="Ex: Cuisine sénégalaise, asiatique, méditerranéenne..."><?php echo isset($patient) ? $patient->cultural_food_preferences : ''; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="cooking_skills">Compétences culinaires</label>
                                        <select name="cooking_skills" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="beginner" <?php echo set_select('cooking_skills', 'beginner', isset($patient) && $patient->cooking_skills == 'beginner'); ?>>Débutant</option>
                                            <option value="basic" <?php echo set_select('cooking_skills', 'basic', isset($patient) && $patient->cooking_skills == 'basic'); ?>>Basique</option>
                                            <option value="intermediate" <?php echo set_select('cooking_skills', 'intermediate', isset($patient) && $patient->cooking_skills == 'intermediate'); ?>>Intermédiaire</option>
                                            <option value="advanced" <?php echo set_select('cooking_skills', 'advanced', isset($patient) && $patient->cooking_skills == 'advanced'); ?>>Avancé</option>
                                            <option value="professional" <?php echo set_select('cooking_skills', 'professional', isset($patient) && $patient->cooking_skills == 'professional'); ?>>Professionnel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="alert" style="background: #ebf5fb; border-left: 4px solid #3498db; color: #1b4f72;">
                                <i class="fa fa-info-circle"></i> <strong>Conseil:</strong> Ces informations permettent de personnaliser les recommandations alimentaires en tenant compte des intolérances et préférences.
                            </div>
                        </div>

                        <!-- Section 5: Mode de Vie Détaillé (NOUVELLE) -->
                        <div class="form-section" style="border-left-color: #9b59b6;">
                            <div class="section-title" style="color: #9b59b6;">
                                <i class="fa fa-life-ring"></i>
                                <span>Mode de Vie Détaillé</span>
                            </div>

                            <h5 style="color: #9b59b6; margin-bottom: 15px; font-weight: 600;">
                                <i class="fa fa-coffee"></i> Habitudes Alimentaires Quotidiennes
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="meals_per_day">Nombre de repas/jour</label>
                                        <input type="number" class="form-control" name="meals_per_day" value="<?php echo isset($patient) ? $patient->meals_per_day : ''; ?>" placeholder="3" min="1" max="10" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="snacks_per_day">Collations/jour</label>
                                        <input type="number" class="form-control" name="snacks_per_day" value="<?php echo isset($patient) ? $patient->snacks_per_day : ''; ?>" placeholder="2" min="0" max="10" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="breakfast_time">Heure petit-déjeuner</label>
                                        <input type="time" class="form-control" name="breakfast_time" value="<?php echo isset($patient) ? $patient->breakfast_time : ''; ?>" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dinner_time">Heure dîner</label>
                                        <input type="time" class="form-control" name="dinner_time" value="<?php echo isset($patient) ? $patient->dinner_time : ''; ?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eating_speed">Vitesse d'alimentation</label>
                                        <select name="eating_speed" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="very_slow" <?php echo set_select('eating_speed', 'very_slow', isset($patient) && $patient->eating_speed == 'very_slow'); ?>>Très lent (>30min)</option>
                                            <option value="slow" <?php echo set_select('eating_speed', 'slow', isset($patient) && $patient->eating_speed == 'slow'); ?>>Lent (20-30min)</option>
                                            <option value="normal" <?php echo set_select('eating_speed', 'normal', isset($patient) && $patient->eating_speed == 'normal'); ?>>Normal (15-20min)</option>
                                            <option value="fast" <?php echo set_select('eating_speed', 'fast', isset($patient) && $patient->eating_speed == 'fast'); ?>>Rapide (10-15min)</option>
                                            <option value="very_fast" <?php echo set_select('eating_speed', 'very_fast', isset($patient) && $patient->eating_speed == 'very_fast'); ?>>Très rapide (<10min)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="eating_environment">Environnement de repas</label>
                                        <select name="eating_environment" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="table_calm" <?php echo set_select('eating_environment', 'table_calm', isset($patient) && $patient->eating_environment == 'table_calm'); ?>>Table, calme, en pleine conscience</option>
                                            <option value="table_distracted" <?php echo set_select('eating_environment', 'table_distracted', isset($patient) && $patient->eating_environment == 'table_distracted'); ?>>Table, avec TV/téléphone</option>
                                            <option value="standing" <?php echo set_select('eating_environment', 'standing', isset($patient) && $patient->eating_environment == 'standing'); ?>>Debout / en marchant</option>
                                            <option value="working" <?php echo set_select('eating_environment', 'working', isset($patient) && $patient->eating_environment == 'working'); ?>>En travaillant (bureau)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="meal_preparation">Préparation des repas</label>
                                        <select name="meal_preparation" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="home_fresh" <?php echo set_select('meal_preparation', 'home_fresh', isset($patient) && $patient->meal_preparation == 'home_fresh'); ?>>Maison (frais quotidien)</option>
                                            <option value="home_batch" <?php echo set_select('meal_preparation', 'home_batch', isset($patient) && $patient->meal_preparation == 'home_batch'); ?>>Maison (batch cooking)</option>
                                            <option value="mixed" <?php echo set_select('meal_preparation', 'mixed', isset($patient) && $patient->meal_preparation == 'mixed'); ?>>Mixte (maison + restaurant)</option>
                                            <option value="mostly_out" <?php echo set_select('meal_preparation', 'mostly_out', isset($patient) && $patient->meal_preparation == 'mostly_out'); ?>>Principalement restaurant/livraison</option>
                                            <option value="processed" <?php echo set_select('meal_preparation', 'processed', isset($patient) && $patient->meal_preparation == 'processed'); ?>>Plats préparés/surgelés</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="typical_day_diet">Description d'une journée alimentaire typique</label>
                                        <textarea class="form-control" name="typical_day_diet" rows="3" placeholder="Ex: Matin: Café + pain beurre. Midi: Riz + poisson + légumes. Soir: Soupe + salade..."><?php echo isset($patient) ? $patient->typical_day_diet : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #9b59b6; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-glass"></i> Consommations Spécifiques
                            </h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="coffee_per_day">Cafés/jour <i class="fa fa-coffee"></i></label>
                                        <input type="number" class="form-control" name="coffee_per_day" value="<?php echo isset($patient) ? $patient->coffee_per_day : ''; ?>" placeholder="0" min="0" max="20" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tea_per_day">Thés/jour</label>
                                        <input type="number" class="form-control" name="tea_per_day" value="<?php echo isset($patient) ? $patient->tea_per_day : ''; ?>" placeholder="0" min="0" max="20" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soda_per_week">Sodas/semaine</label>
                                        <input type="number" class="form-control" name="soda_per_week" value="<?php echo isset($patient) ? $patient->soda_per_week : ''; ?>" placeholder="0" min="0" max="50" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="alcohol_per_week">Verres d'alcool/semaine</label>
                                        <input type="number" class="form-control" name="alcohol_per_week" value="<?php echo isset($patient) ? $patient->alcohol_per_week : ''; ?>" placeholder="0" min="0" max="50" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sweet_cravings">Envies de sucré</label>
                                        <select name="sweet_cravings" class="form-control">
                                            <option value="never" <?php echo set_select('sweet_cravings', 'never', isset($patient) && $patient->sweet_cravings == 'never'); ?>>Jamais</option>
                                            <option value="rarely" <?php echo set_select('sweet_cravings', 'rarely', isset($patient) && $patient->sweet_cravings == 'rarely'); ?>>Rarement</option>
                                            <option value="sometimes" <?php echo set_select('sweet_cravings', 'sometimes', isset($patient) && $patient->sweet_cravings == 'sometimes'); ?>>Parfois</option>
                                            <option value="often" <?php echo set_select('sweet_cravings', 'often', isset($patient) && $patient->sweet_cravings == 'often'); ?>>Souvent</option>
                                            <option value="daily" <?php echo set_select('sweet_cravings', 'daily', isset($patient) && $patient->sweet_cravings == 'daily'); ?>>Quotidien</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="salt_preference">Préférence pour le sel</label>
                                        <select name="salt_preference" class="form-control">
                                            <option value="low" <?php echo set_select('salt_preference', 'low', isset($patient) && $patient->salt_preference == 'low'); ?>>Faible</option>
                                            <option value="normal" <?php echo set_select('salt_preference', 'normal', isset($patient) && $patient->salt_preference == 'normal'); ?>>Normal</option>
                                            <option value="high" <?php echo set_select('salt_preference', 'high', isset($patient) && $patient->salt_preference == 'high'); ?>>Élevé</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="smoking">Tabagisme</label>
                                        <select name="smoking" class="form-control">
                                            <option value="no" <?php echo set_select('smoking', 'no', !isset($patient) || (isset($patient) && $patient->smoking == 'no')); ?>>Non</option>
                                            <option value="former" <?php echo set_select('smoking', 'former', isset($patient) && $patient->smoking == 'former'); ?>>Ancien fumeur</option>
                                            <option value="occasional" <?php echo set_select('smoking', 'occasional', isset($patient) && $patient->smoking == 'occasional'); ?>>Occasionnel</option>
                                            <option value="light" <?php echo set_select('smoking', 'light', isset($patient) && $patient->smoking == 'light'); ?>>Léger (<10/jour)</option>
                                            <option value="moderate" <?php echo set_select('smoking', 'moderate', isset($patient) && $patient->smoking == 'moderate'); ?>>Modéré (10-20/jour)</option>
                                            <option value="heavy" <?php echo set_select('smoking', 'heavy', isset($patient) && $patient->smoking == 'heavy'); ?>>Fort (>20/jour)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h5 style="color: #9b59b6; margin: 25px 0 15px 0; font-weight: 600;">
                                <i class="fa fa-calendar"></i> Contraintes & Organisation
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="time_for_cooking">Temps disponible pour cuisiner/jour</label>
                                        <select name="time_for_cooking" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="none" <?php echo set_select('time_for_cooking', 'none', isset($patient) && $patient->time_for_cooking == 'none'); ?>>Aucun / Très peu</option>
                                            <option value="15_30min" <?php echo set_select('time_for_cooking', '15_30min', isset($patient) && $patient->time_for_cooking == '15_30min'); ?>>15-30 minutes</option>
                                            <option value="30_60min" <?php echo set_select('time_for_cooking', '30_60min', isset($patient) && $patient->time_for_cooking == '30_60min'); ?>>30-60 minutes</option>
                                            <option value="1_2hours" <?php echo set_select('time_for_cooking', '1_2hours', isset($patient) && $patient->time_for_cooking == '1_2hours'); ?>>1-2 heures</option>
                                            <option value="flexible" <?php echo set_select('time_for_cooking', 'flexible', isset($patient) && $patient->time_for_cooking == 'flexible'); ?>>Flexible / Beaucoup</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="budget_level">Niveau de budget alimentaire</label>
                                        <select name="budget_level" class="form-control">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="tight" <?php echo set_select('budget_level', 'tight', isset($patient) && $patient->budget_level == 'tight'); ?>>Serré</option>
                                            <option value="moderate" <?php echo set_select('budget_level', 'moderate', isset($patient) && $patient->budget_level == 'moderate'); ?>>Modéré</option>
                                            <option value="comfortable" <?php echo set_select('budget_level', 'comfortable', isset($patient) && $patient->budget_level == 'comfortable'); ?>>Confortable</option>
                                            <option value="high" <?php echo set_select('budget_level', 'high', isset($patient) && $patient->budget_level == 'high'); ?>>Élevé</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lifestyle_notes"><?php echo _l('dietetic_lifestyle_notes'); ?></label>
                                        <textarea class="form-control" name="lifestyle_notes" rows="5" placeholder="Ex: Horaires de travail variables, mange souvent au restaurant le midi, famille nombreuse..."><?php echo isset($patient) ? $patient->lifestyle_notes : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="barriers_to_change">Obstacles au changement alimentaire</label>
                                        <textarea class="form-control" name="barriers_to_change" rows="2" placeholder="Ex: Manque de temps, budget limité, stress, famille peu coopérative, environnement social..."><?php echo isset($patient) ? $patient->barriers_to_change : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="motivation_level">Niveau de motivation pour le changement <i class="fa fa-info-circle" title="1=Très faible, 10=Extrême"></i></label>
                                        <input type="range" class="form-control" name="motivation_level" id="motivation_level_slider" value="<?php echo isset($patient) ? $patient->motivation_level : '5'; ?>" min="1" max="10" style="height: auto;" />
                                        <div style="text-align: center; margin-top: 10px;">
                                            <span style="font-size: 24px; font-weight: bold; color: #9b59b6;" id="motivation_display">5</span> / 10
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert" style="background: #f4ecf7; border-left: 4px solid #9b59b6; color: #4a235a;">
                                <i class="fa fa-lightbulb-o"></i> <strong>Personnalisation:</strong> Ces informations permettent d'adapter le plan nutritionnel à votre style de vie réel et d'identifier les obstacles potentiels au succès.
                            </div>
                        </div>

                        <!-- Section 6: Contact d'Urgence -->
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

// ============================================
// CALCULS AUTOMATIQUES
// ============================================

// Calcul de l'IMC (Indice de Masse Corporelle)
function calculateBMI() {
    const weight = parseFloat(document.getElementById('initial_weight').value);
    const height = parseFloat(document.getElementById('height').value);
    const bmiDisplay = document.getElementById('bmi_display');

    if (weight && height && height > 0) {
        const heightInMeters = height / 100;
        const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(1);
        bmiDisplay.value = bmi;

        // Colorier selon l'IMC
        if (bmi < 18.5) {
            bmiDisplay.style.color = '#3498db'; // Sous-poids - bleu
        } else if (bmi >= 18.5 && bmi < 25) {
            bmiDisplay.style.color = '#2ecc71'; // Normal - vert
        } else if (bmi >= 25 && bmi < 30) {
            bmiDisplay.style.color = '#f39c12'; // Surpoids - orange
        } else {
            bmiDisplay.style.color = '#e74c3c'; // Obésité - rouge
        }
    } else {
        bmiDisplay.value = '';
        bmiDisplay.style.color = '#2ecc71';
    }
}

// Calcul du Rapport Taille/Hanches et Morphologie
function calculateWaistHipRatio() {
    const waist = parseFloat(document.getElementById('waist_circumference').value);
    const hip = parseFloat(document.getElementById('hip_circumference').value);
    const ratioDisplay = document.getElementById('waist_hip_ratio_display');
    const shapeDisplay = document.getElementById('body_shape_display');
    const gender = document.getElementById('gender').value;

    if (waist && hip && hip > 0) {
        const ratio = (waist / hip).toFixed(2);
        ratioDisplay.value = ratio;

        // Déterminer la morphologie selon le sexe
        let bodyShape = '';
        let shapeColor = '#9b59b6';

        if (gender === 'male') {
            if (ratio > 0.95) {
                bodyShape = 'Androïde (Pomme)';
                shapeColor = '#e74c3c'; // Rouge - risque cardiovasculaire
            } else {
                bodyShape = 'Gynoïde (Poire)';
                shapeColor = '#2ecc71'; // Vert - plus sain
            }
        } else if (gender === 'female') {
            if (ratio > 0.85) {
                bodyShape = 'Androïde (Pomme)';
                shapeColor = '#e74c3c'; // Rouge - risque cardiovasculaire
            } else {
                bodyShape = 'Gynoïde (Poire)';
                shapeColor = '#2ecc71'; // Vert - plus sain
            }
        } else {
            bodyShape = 'Non déterminé';
            shapeColor = '#95a5a6';
        }

        shapeDisplay.value = bodyShape;
        shapeDisplay.style.color = shapeColor;
        ratioDisplay.style.color = shapeColor;
    } else {
        ratioDisplay.value = '';
        shapeDisplay.value = '';
        ratioDisplay.style.color = '#2ecc71';
        shapeDisplay.style.color = '#9b59b6';
    }
}

// ============================================
// CHAMPS CONDITIONNELS
// ============================================

// Afficher/Masquer la section femmes
function toggleWomenSection() {
    const gender = document.getElementById('gender').value;
    const womenSection = document.getElementById('women-section');

    if (gender === 'female') {
        womenSection.style.display = 'block';
    } else {
        womenSection.style.display = 'none';
    }
}

// Afficher/Masquer le champ mois de grossesse
function togglePregnancyMonths() {
    const isPregnant = document.getElementById('is_pregnant').value;
    const pregnancyMonthsGroup = document.getElementById('pregnancy-months-group');

    if (isPregnant === 'yes') {
        pregnancyMonthsGroup.style.display = 'block';
    } else {
        pregnancyMonthsGroup.style.display = 'none';
    }
}

// Mise à jour du slider de motivation
function updateMotivationDisplay() {
    const slider = document.getElementById('motivation_level_slider');
    const display = document.getElementById('motivation_display');

    if (slider && display) {
        display.textContent = slider.value;

        // Colorier selon le niveau
        const value = parseInt(slider.value);
        if (value <= 3) {
            display.style.color = '#e74c3c'; // Rouge - faible
        } else if (value <= 6) {
            display.style.color = '#f39c12'; // Orange - moyen
        } else {
            display.style.color = '#2ecc71'; // Vert - élevé
        }
    }
}

// ============================================
// ÉVÉNEMENTS
// ============================================

// Attacher les événements après le chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    // Calculs automatiques
    const weightInput = document.getElementById('initial_weight');
    const heightInput = document.getElementById('height');
    const waistInput = document.getElementById('waist_circumference');
    const hipInput = document.getElementById('hip_circumference');
    const genderSelect = document.getElementById('gender');

    if (weightInput) weightInput.addEventListener('input', calculateBMI);
    if (heightInput) heightInput.addEventListener('input', calculateBMI);
    if (waistInput) waistInput.addEventListener('input', calculateWaistHipRatio);
    if (hipInput) hipInput.addEventListener('input', calculateWaistHipRatio);
    if (genderSelect) {
        genderSelect.addEventListener('change', function() {
            toggleWomenSection();
            calculateWaistHipRatio();
        });
    }

    // Champs conditionnels
    const isPregnantSelect = document.getElementById('is_pregnant');
    if (isPregnantSelect) {
        isPregnantSelect.addEventListener('change', togglePregnancyMonths);
    }

    // Slider motivation
    const motivationSlider = document.getElementById('motivation_level_slider');
    if (motivationSlider) {
        motivationSlider.addEventListener('input', updateMotivationDisplay);
    }

    // Initialiser les valeurs au chargement (pour mode édition)
    calculateBMI();
    calculateWaistHipRatio();
    toggleWomenSection();
    togglePregnancyMonths();
    updateMotivationDisplay();
});

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
