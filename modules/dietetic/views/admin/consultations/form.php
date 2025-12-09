<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Consultation Form Design */
* {
    box-sizing: border-box;
}

.consultation-form-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section */
.form-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.form-header-modern::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.form-header-content {
    position: relative;
    z-index: 1;
}

.form-header-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.form-header-subtitle {
    font-size: 16px;
    opacity: 0.9;
}

/* Section Cards */
.form-section {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.icon-basic {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.icon-communication {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.icon-medical {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.icon-notes {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.icon-followup {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

/* Form Groups */
.form-group-modern {
    margin-bottom: 20px;
}

.form-group-modern label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.form-group-modern label .required {
    color: #e74c3c;
    margin-left: 3px;
}

.form-group-modern .form-control {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-group-modern .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.form-group-modern .help-text {
    font-size: 13px;
    color: #7f8c8d;
    margin-top: 5px;
    display: block;
}

/* Patient Quick Link */
.patient-quick-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    margin-top: 5px;
    transition: all 0.3s ease;
}

.patient-quick-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Channel Selection */
.channel-toggle {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.channel-option {
    flex: 1;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.channel-option input[type="radio"] {
    display: none;
}

.channel-option.active {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.channel-option:hover {
    border-color: #667eea;
}

.channel-option .channel-icon {
    font-size: 24px;
    margin-bottom: 8px;
}

.channel-option .channel-label {
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
}

/* Online Platforms */
.platform-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.platform-option {
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    position: relative;
}

.platform-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.platform-option.selected {
    border-color: #11998e;
    background: rgba(17, 153, 142, 0.1);
}

.platform-option:hover {
    border-color: #11998e;
    transform: translateY(-2px);
}

.platform-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.platform-name {
    font-weight: 600;
    font-size: 13px;
    color: #2c3e50;
}

/* Star Rating */
.star-rating {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.star {
    font-size: 32px;
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.star:hover,
.star.active {
    color: #f39c12;
    transform: scale(1.1);
}

/* BMI Display */
.bmi-display {
    margin-top: 10px;
    padding: 15px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 12px;
    display: none;
}

.bmi-display.show {
    display: block;
}

.bmi-value {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.bmi-category {
    font-size: 14px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-block;
}

.bmi-underweight { background: #3498db; color: white; }
.bmi-normal { background: #2ecc71; color: white; }
.bmi-overweight { background: #f39c12; color: white; }
.bmi-obese { background: #e74c3c; color: white; }

/* Action Buttons */
.btn-bottom-toolbar-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 25px 30px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    margin-top: 30px;
}

.btn-modern {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 15px;
}

.btn-cancel-modern {
    background: #f5f5f5;
    color: #666;
}

.btn-cancel-modern:hover {
    background: #e0e0e0;
    color: #666;
}

.btn-submit-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-submit-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .form-header-modern {
        padding: 25px 20px;
    }

    .form-header-title {
        font-size: 24px;
    }

    .form-section {
        padding: 20px;
    }

    .platform-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .btn-bottom-toolbar-modern {
        flex-direction: column;
    }

    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

/* Hidden fields */
.hidden {
    display: none;
}

/* Alternative Slots */
.alternative-slots {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.alternative-slots-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alternative-slots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}

.slot-option {
    padding: 10px;
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.slot-option:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}

.slot-date {
    font-size: 12px;
    color: #7f8c8d;
    margin-bottom: 4px;
}

.slot-time {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="consultation-form-wrapper">

            <!-- Header -->
            <div class="form-header-modern">
                <div class="form-header-content">
                    <h1 class="form-header-title">
                        <i class="fa fa-calendar-plus-o"></i>
                        <?php echo $title; ?>
                    </h1>
                    <p class="form-header-subtitle">
                        Planifiez et gérez les consultations diététiques avec vos patients
                    </p>
                </div>
            </div>

            <?php echo form_open($this->uri->uri_string()); ?>

            <!-- Section 1: Informations de Base -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon icon-basic">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <h3 class="section-title">Informations de Base</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="patient_id">
                                Patient <span class="required">*</span>
                            </label>
                            <select name="patient_id" id="patient_id" class="form-control selectpicker" data-live-search="true" required>
                                <option value="">-- Sélectionner un patient --</option>
                                <?php foreach ($patients as $patient) { ?>
                                    <option value="<?php echo $patient->id; ?>"
                                            data-client-id="<?php echo $patient->client_id; ?>"
                                            <?php echo set_select('patient_id', $patient->id, (isset($consultation) && $consultation->patient_id == $patient->id) || (isset($_GET['patient_id']) && $_GET['patient_id'] == $patient->id)); ?>>
                                        <?php echo $patient->client_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <a href="#" id="patient-link" class="patient-quick-link hidden">
                                <i class="fa fa-user"></i> Voir le dossier patient
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="dietitian_id">
                                Diététicien <span class="required">*</span>
                            </label>
                            <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                <?php foreach ($staff as $member) { ?>
                                    <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($consultation) && $consultation->dietitian_id == $member['staffid']) || (!isset($consultation) && $member['staffid'] == get_staff_user_id())); ?>>
                                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="consultation_date">
                                Date et Heure <span class="required">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" id="consultation_date" name="consultation_date"
                                   value="<?php echo isset($consultation) ? date('Y-m-d\TH:i', strtotime($consultation->consultation_date)) : ''; ?>" required />
                            <span class="help-text">Sélectionnez la date et l'heure de la consultation</span>

                            <!-- Availability Status Indicator -->
                            <div id="availability-status" style="margin-top: 10px; display: none;">
                                <div id="availability-indicator" style="padding: 10px 15px; border-radius: 8px; font-weight: 600; font-size: 14px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="duration">Durée (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                   value="<?php echo isset($consultation) ? $consultation->duration : '60'; ?>" min="15" step="15" />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="status">Statut</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" <?php echo set_select('status', 'pending', isset($consultation) && $consultation->status == 'pending'); ?>>En attente de validation</option>
                                <option value="scheduled" <?php echo set_select('status', 'scheduled', (isset($consultation) && $consultation->status == 'scheduled') || !isset($consultation)); ?>>Planifiée</option>
                                <option value="completed" <?php echo set_select('status', 'completed', isset($consultation) && $consultation->status == 'completed'); ?>>Terminée</option>
                                <option value="cancelled" <?php echo set_select('status', 'cancelled', isset($consultation) && $consultation->status == 'cancelled'); ?>>Annulée</option>
                                <option value="rejected" <?php echo set_select('status', 'rejected', isset($consultation) && $consultation->status == 'rejected'); ?>>Refusée</option>
                                <option value="no_show" <?php echo set_select('status', 'no_show', isset($consultation) && $consultation->status == 'no_show'); ?>>Absence</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group-modern">
                            <label for="consultation_type_id">Type de Consultation</label>
                            <select name="consultation_type_id" id="consultation_type_id" class="form-control">
                                <option value="">-- Sélectionner un type --</option>
                                <?php if (isset($consultation_types) && !empty($consultation_types)): ?>
                                    <?php foreach ($consultation_types as $type): ?>
                                        <option value="<?php echo $type->id; ?>"
                                                data-duration="<?php echo $type->duration; ?>"
                                                data-slug="<?php echo $type->slug; ?>"
                                                <?php echo set_select('consultation_type_id', $type->id, isset($consultation) && $consultation->consultation_type_id == $type->id); ?>>
                                            <?php echo $type->name; ?> (<?php echo $type->duration; ?> min)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Consultation Générale (60 min)</option>
                                <?php endif; ?>
                            </select>
                            <span class="help-text">Le type de consultation détermine la durée</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Canal de Communication -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon icon-communication">
                        <i class="fa fa-comments"></i>
                    </div>
                    <h3 class="section-title">Canal de Communication</h3>
                </div>

                <div class="form-group-modern">
                    <label>Mode de Consultation</label>
                    <div class="channel-toggle">
                        <label class="channel-option active" data-channel="in_person">
                            <input type="radio" name="consultation_mode" value="in_person" checked>
                            <div class="channel-icon">🏥</div>
                            <div class="channel-label">En Présentiel</div>
                        </label>
                        <label class="channel-option" data-channel="online">
                            <input type="radio" name="consultation_mode" value="online">
                            <div class="channel-icon">💻</div>
                            <div class="channel-label">En Ligne</div>
                        </label>
                    </div>
                </div>

                <div id="location-field" class="form-group-modern">
                    <label for="location">Lieu de la Consultation</label>
                    <input type="text" class="form-control" id="location" name="location"
                           value="<?php echo isset($consultation) ? $consultation->location : ''; ?>"
                           placeholder="Ex: Cabinet médical, Hôpital Principal, etc." />
                </div>

                <div id="online-platform-field" class="form-group-modern hidden">
                    <label>Plateforme de Visioconférence</label>
                    <div class="platform-grid">
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="zoom">
                            <div class="platform-icon">📹</div>
                            <div class="platform-name">Zoom</div>
                        </label>
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="google_meet">
                            <div class="platform-icon">📞</div>
                            <div class="platform-name">Google Meet</div>
                        </label>
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="teams">
                            <div class="platform-icon">👥</div>
                            <div class="platform-name">Microsoft Teams</div>
                        </label>
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="whatsapp">
                            <div class="platform-icon">💬</div>
                            <div class="platform-name">WhatsApp</div>
                        </label>
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="skype">
                            <div class="platform-icon">📲</div>
                            <div class="platform-name">Skype</div>
                        </label>
                        <label class="platform-option">
                            <input type="radio" name="online_platform" value="other">
                            <div class="platform-icon">🌐</div>
                            <div class="platform-name">Autre</div>
                        </label>
                    </div>
                </div>

                <div id="meeting-link-field" class="form-group-modern hidden">
                    <label for="meeting_link">Lien de la Réunion</label>
                    <input type="url" class="form-control" id="meeting_link" name="meeting_link"
                           placeholder="https://zoom.us/j/123456789 ou https://meet.google.com/abc-defg-hij" />
                    <span class="help-text">Copiez le lien de la réunion depuis votre plateforme</span>
                </div>
            </div>

            <!-- Section 3: Informations Médicales -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon icon-medical">
                        <i class="fa fa-heartbeat"></i>
                    </div>
                    <h3 class="section-title">Informations Médicales</h3>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group-modern">
                            <label for="reason">Motif de la Consultation</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3"
                                      placeholder="Décrivez brièvement la raison de cette consultation..."><?php echo isset($consultation) ? $consultation->reason : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label for="weight_at_visit">Poids lors de la Visite (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="weight_at_visit" name="weight_at_visit"
                                   value="<?php echo isset($consultation) ? $consultation->weight_at_visit : ''; ?>"
                                   placeholder="Ex: 75.5" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-modern">
                            <label for="height_patient">Taille du Patient (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="height_patient" name="height_patient"
                                   placeholder="Ex: 170" />
                            <span class="help-text">Pour calculer l'IMC automatiquement</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bmi-display" id="bmi-display">
                            <div class="bmi-value">IMC: <span id="bmi-value">0</span></div>
                            <span class="bmi-category" id="bmi-category">Normal</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group-modern">
                            <label for="observations">Observations</label>
                            <textarea class="form-control" id="observations" name="observations" rows="4"
                                      placeholder="Notez vos observations pendant la consultation..."><?php echo isset($consultation) ? $consultation->observations : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Notes et Recommandations -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon icon-notes">
                        <i class="fa fa-file-text"></i>
                    </div>
                    <h3 class="section-title">Notes et Recommandations</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="notes">Notes Privées</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                      placeholder="Notes personnelles (non visibles par le patient)..."><?php echo isset($consultation) ? $consultation->notes : ''; ?></textarea>
                            <span class="help-text">Ces notes ne seront visibles que par vous</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="recommendations">Recommandations</label>
                            <textarea class="form-control" id="recommendations" name="recommendations" rows="4"
                                      placeholder="Recommandations pour le patient..."><?php echo isset($consultation) ? $consultation->recommendations : ''; ?></textarea>
                            <span class="help-text">Partagées avec le patient</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Suivi -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon icon-followup">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <h3 class="section-title">Suivi et Satisfaction</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label for="next_consultation_date">Prochaine Consultation</label>
                            <input type="datetime-local" class="form-control" id="next_consultation_date" name="next_consultation_date"
                                   value="<?php echo isset($consultation) && $consultation->next_consultation_date ? date('Y-m-d\TH:i', strtotime($consultation->next_consultation_date)) : ''; ?>" />
                            <span class="help-text">Planifiez la prochaine consultation si nécessaire</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label>Score de Satisfaction Patient</label>
                            <div class="star-rating">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <input type="hidden" id="satisfaction_score" name="satisfaction_score" value="<?php echo isset($consultation) ? $consultation->satisfaction_score : ''; ?>">
                            <span class="help-text">Évaluation de la satisfaction du patient (1-5 étoiles)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="btn-bottom-toolbar-modern">
                <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn-modern btn-cancel-modern">
                    <i class="fa fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-modern btn-submit-modern">
                    <i class="fa fa-save"></i> Enregistrer la Consultation
                </button>
            </div>

            <?php echo form_close(); ?>

        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
(function() {
    'use strict';

    // Patient quick link
    $('#patient_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const patientId = selectedOption.val();

        if (patientId) {
            const link = '<?php echo admin_url("dietetic/patients/view/"); ?>' + patientId;
            $('#patient-link').attr('href', link).removeClass('hidden');
        } else {
            $('#patient-link').addClass('hidden');
        }
    });

    // Initialize if patient already selected
    if ($('#patient_id').val()) {
        $('#patient_id').trigger('change');
    }

    // Channel mode toggle
    $('.channel-option').on('click', function() {
        $('.channel-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);

        const mode = $(this).data('channel');

        if (mode === 'online') {
            $('#location-field').addClass('hidden');
            $('#online-platform-field').removeClass('hidden');
            $('#meeting-link-field').removeClass('hidden');
        } else {
            $('#location-field').removeClass('hidden');
            $('#online-platform-field').addClass('hidden');
            $('#meeting-link-field').addClass('hidden');
        }
    });

    // Platform selection
    $('.platform-option').on('click', function() {
        $('.platform-option').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Star rating
    let selectedRating = <?php echo isset($consultation) && $consultation->satisfaction_score ? $consultation->satisfaction_score : 0; ?>;

    // Initialize stars if editing
    if (selectedRating > 0) {
        updateStars(selectedRating);
    }

    $('.star').on('click', function() {
        selectedRating = $(this).data('value');
        $('#satisfaction_score').val(selectedRating);
        updateStars(selectedRating);
    });

    $('.star').on('mouseenter', function() {
        const hoverValue = $(this).data('value');
        updateStars(hoverValue);
    });

    $('.star-rating').on('mouseleave', function() {
        updateStars(selectedRating);
    });

    function updateStars(rating) {
        $('.star').each(function() {
            if ($(this).data('value') <= rating) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    // BMI Calculator
    function calculateBMI() {
        const weight = parseFloat($('#weight_at_visit').val());
        const height = parseFloat($('#height_patient').val());

        if (weight && height && height > 0) {
            const heightM = height / 100;
            const bmi = weight / (heightM * heightM);
            const bmiRounded = Math.round(bmi * 10) / 10;

            $('#bmi-value').text(bmiRounded);

            // Determine category
            let category = '';
            let categoryClass = '';

            if (bmi < 18.5) {
                category = 'Insuffisance pondérale';
                categoryClass = 'bmi-underweight';
            } else if (bmi < 25) {
                category = 'Poids normal';
                categoryClass = 'bmi-normal';
            } else if (bmi < 30) {
                category = 'Surpoids';
                categoryClass = 'bmi-overweight';
            } else {
                category = 'Obésité';
                categoryClass = 'bmi-obese';
            }

            $('#bmi-category').text(category)
                .removeClass('bmi-underweight bmi-normal bmi-overweight bmi-obese')
                .addClass(categoryClass);

            $('#bmi-display').addClass('show');
        } else {
            $('#bmi-display').removeClass('show');
        }
    }

    $('#weight_at_visit, #height_patient').on('input', calculateBMI);

    // Initialize BMI if editing
    calculateBMI();

    // ============================================
    // Availability Checking System
    // ============================================

    let availabilityCheckTimeout = null;

    // Auto-update duration when consultation type changes
    $('#consultation_type_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const duration = selectedOption.data('duration');

        if (duration) {
            $('#duration').val(duration);
            // Trigger availability check
            checkAvailability();
        }
    });

    // Check availability when relevant fields change
    $('#consultation_date, #dietitian_id, #duration').on('change input', function() {
        // Debounce the availability check
        clearTimeout(availabilityCheckTimeout);
        availabilityCheckTimeout = setTimeout(function() {
            checkAvailability();
        }, 500);
    });

    // Function to check availability
    function checkAvailability() {
        const dietitianId = $('#dietitian_id').val();
        const datetime = $('#consultation_date').val();
        const duration = $('#duration').val() || 60;

        // Hide indicator if required fields are missing
        if (!dietitianId || !datetime) {
            $('#availability-status').hide();
            return;
        }

        // Convert datetime-local format to MySQL format
        const mysqlDatetime = datetime.replace('T', ' ') + ':00';

        // Get consultation ID if editing
        const consultationId = <?php echo isset($consultation) ? $consultation->id : 'null'; ?>;

        // Show loading state
        $('#availability-status').show();
        $('#availability-indicator')
            .css({
                'background': '#f0f0f0',
                'color': '#666',
                'border': '2px solid #ddd'
            })
            .html('<i class="fa fa-spinner fa-spin"></i> Vérification de la disponibilité...');

        // Make AJAX request
        $.ajax({
            url: '<?php echo admin_url("dietetic/consultations/check_availability"); ?>',
            type: 'GET',
            data: {
                dietitian_id: dietitianId,
                datetime: mysqlDatetime,
                duration: duration,
                consultation_id: consultationId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayAvailabilityStatus(response.available, response.reason, response.conflicts, response.alternative_slots);
                } else {
                    $('#availability-indicator')
                        .css({
                            'background': '#fff3cd',
                            'color': '#856404',
                            'border': '2px solid #ffc107'
                        })
                        .html('⚠️ ' + (response.message || 'Erreur de vérification'));
                }
            },
            error: function() {
                $('#availability-indicator')
                    .css({
                        'background': '#f8d7da',
                        'color': '#721c24',
                        'border': '2px solid #dc3545'
                    })
                    .html('❌ Erreur de connexion');
            }
        });
    }

    // Display availability status visually
    function displayAvailabilityStatus(available, reason, conflicts, alternativeSlots) {
        const indicator = $('#availability-indicator');
        const statusDiv = $('#availability-status');

        // Remove any existing alternative slots section
        statusDiv.find('.alternative-slots').remove();

        if (available) {
            // Available - Green
            indicator
                .css({
                    'background': '#d4edda',
                    'color': '#155724',
                    'border': '2px solid #28a745'
                })
                .html('🟢 <strong>Disponible</strong> - ' + reason);
        } else {
            // Not available - Red/Orange
            let icon = '🔴';
            let bgColor = '#f8d7da';
            let textColor = '#721c24';
            let borderColor = '#dc3545';

            // Different styling for different reasons
            if (reason.includes('ne travaille pas')) {
                icon = '⏰';
                bgColor = '#fff3cd';
                textColor = '#856404';
                borderColor = '#ffc107';
            } else if (reason.includes('Hors des horaires')) {
                icon = '⚠️';
                bgColor = '#fff3cd';
                textColor = '#856404';
                borderColor = '#ffc107';
            }

            let html = icon + ' <strong>Non disponible</strong> - ' + reason;

            // Show conflicts if any
            if (conflicts && conflicts.length > 0) {
                html += '<br><small>Conflits: ';
                conflicts.forEach(function(conflict, index) {
                    html += conflict.start + ' - ' + conflict.end;
                    if (index < conflicts.length - 1) html += ', ';
                });
                html += '</small>';
            }

            indicator
                .css({
                    'background': bgColor,
                    'color': textColor,
                    'border': '2px solid ' + borderColor
                })
                .html(html);

            // Display alternative slots if available
            if (alternativeSlots && alternativeSlots.length > 0) {
                let alternativesHtml = '<div class="alternative-slots">';
                alternativesHtml += '<div class="alternative-slots-title">';
                alternativesHtml += '<i class="fa fa-clock-o"></i> Créneaux alternatifs disponibles :';
                alternativesHtml += '</div>';
                alternativesHtml += '<div class="alternative-slots-grid">';

                alternativeSlots.forEach(function(slot) {
                    alternativesHtml += '<div class="slot-option" data-datetime="' + slot.datetime + '">';
                    alternativesHtml += '<div class="slot-date">' + slot.display_date + '</div>';
                    alternativesHtml += '<div class="slot-time">' + slot.display_time + '</div>';
                    alternativesHtml += '</div>';
                });

                alternativesHtml += '</div></div>';
                statusDiv.append(alternativesHtml);
            }
        }
    }

    // Make slots clickable using event delegation (for dynamically created elements)
    $(document).on('click', '.slot-option', function() {
        const datetime = $(this).data('datetime');

        // Convert from 'Y-m-d H:i:s' to 'Y-m-d\TH:i' format for datetime-local input
        const datetimeLocal = datetime.substring(0, 16).replace(' ', 'T');
        $('#consultation_date').val(datetimeLocal);

        // Re-check availability for the new time
        checkAvailability();
    });

    // Check availability on page load if editing
    <?php if (isset($consultation)): ?>
        setTimeout(function() {
            checkAvailability();
        }, 500);
    <?php endif; ?>

    // ============================================
    // End Availability Checking System
    // ============================================

    // Form validation
    $('form').on('submit', function(e) {
        const mode = $('input[name="consultation_mode"]:checked').val();

        if (mode === 'online') {
            const platform = $('input[name="online_platform"]:checked').val();
            const meetingLink = $('#meeting_link').val();

            if (!platform) {
                e.preventDefault();
                alert('Veuillez sélectionner une plateforme de visioconférence');
                return false;
            }

            if (!meetingLink) {
                e.preventDefault();
                alert('Veuillez saisir le lien de la réunion');
                return false;
            }
        }
    });
})();
</script>
