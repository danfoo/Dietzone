<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* ===== Page Container ===== */
.edit-profile-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 30px 0;
}

/* ===== Page Header ===== */
.page-header-modern {
    background: linear-gradient(135deg, #01807B 0%, #00A99D 100%);
    color: white;
    border-radius: 20px;
    padding: 40px 50px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(1, 128, 123, 0.2);
}

.page-header-modern h1 {
    font-size: 36px;
    font-weight: 700;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-header-modern p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

/* ===== Form Card ===== */
.form-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.form-section-title {
    font-size: 24px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-section-title i {
    color: #01807B;
    font-size: 28px;
}

/* ===== Form Groups ===== */
.form-row-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 25px;
}

.form-group-modern {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.form-group-modern label {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group-modern label i {
    color: #01807B;
    font-size: 16px;
}

.label-required {
    color: #e53e3e;
    margin-left: 4px;
}

.form-control-modern {
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.form-control-modern:focus {
    outline: none;
    border-color: #01807B;
    background: white;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

textarea.form-control-modern {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
}

/* ===== Referral Code Display ===== */
.referral-code-display {
    background: linear-gradient(135deg, #01807B 0%, #00A99D 100%);
    color: white;
    padding: 20px 25px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.referral-info {
    flex: 1;
}

.referral-label {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.referral-value {
    font-size: 28px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    letter-spacing: 2px;
}

.referral-actions {
    display: flex;
    gap: 10px;
}

.btn-copy-code,
.btn-regenerate-code {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-copy-code:hover,
.btn-regenerate-code:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* ===== Specialties Selector ===== */
.specialties-selector {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.specialty-checkbox-item {
    position: relative;
}

.specialty-checkbox-item input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.specialty-checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.specialty-checkbox-label:hover {
    background: #edf2f7;
    border-color: #cbd5e0;
}

.specialty-checkbox-item input[type="checkbox"]:checked + .specialty-checkbox-label {
    background: linear-gradient(135deg, var(--specialty-color, #01807B), var(--specialty-color-light, #00A99D));
    color: white;
    border-color: var(--specialty-color, #01807B);
}

.specialty-icon {
    font-size: 20px;
}

.specialty-name {
    font-weight: 600;
    font-size: 14px;
}

/* ===== Languages Input ===== */
.languages-input-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 12px;
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    min-height: 120px;
}

.language-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #01807B;
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.language-tag-remove {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s ease;
}

.language-tag-remove:hover {
    background: rgba(255, 255, 255, 0.3);
}

.language-input-wrapper {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.language-input-wrapper input {
    flex: 1;
    padding: 10px 14px;
    border: 2px solid #cbd5e0;
    border-radius: 8px;
    font-size: 14px;
}

.btn-add-language {
    background: #01807B;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add-language:hover {
    background: #00A99D;
}

/* ===== Certifications Manager ===== */
.certifications-manager {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.certification-entry {
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    position: relative;
}

.certification-entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.certification-entry-number {
    font-weight: 700;
    color: #01807B;
    font-size: 16px;
}

.btn-remove-certification {
    background: #fc8181;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-remove-certification:hover {
    background: #f56565;
}

.certification-fields {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1fr;
    gap: 15px;
}

.certification-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.certification-field label {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
}

.certification-field input {
    padding: 10px 12px;
    border: 1px solid #cbd5e0;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.btn-add-certification {
    background: #01807B;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.btn-add-certification:hover {
    background: #00A99D;
    transform: translateY(-2px);
}

/* ===== Form Actions ===== */
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    padding-top: 30px;
    border-top: 2px solid #e2e8f0;
    margin-top: 30px;
}

.btn-save-profile {
    background: linear-gradient(135deg, #01807B, #00A99D);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 20px rgba(1, 128, 123, 0.3);
}

.btn-save-profile:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(1, 128, 123, 0.4);
}

.btn-cancel {
    background: white;
    color: #4a5568;
    border: 2px solid #e2e8f0;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

/* ===== Help Text ===== */
.help-text {
    font-size: 13px;
    color: #718096;
    margin-top: 6px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}

.help-text i {
    margin-top: 2px;
}

/* ===== Info Box ===== */
.info-box {
    background: #ebf8ff;
    border-left: 4px solid #4299e1;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.info-box-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.info-box-icon {
    color: #4299e1;
    font-size: 20px;
    margin-top: 2px;
}

.info-box-text {
    flex: 1;
    font-size: 14px;
    color: #2c5282;
    line-height: 1.6;
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .page-header-modern {
        padding: 30px 25px;
    }

    .page-header-modern h1 {
        font-size: 28px;
    }

    .form-card {
        padding: 25px 20px;
    }

    .form-row-modern {
        grid-template-columns: 1fr;
    }

    .referral-code-display {
        flex-direction: column;
        text-align: center;
    }

    .referral-actions {
        flex-direction: column;
        width: 100%;
    }

    .btn-copy-code,
    .btn-regenerate-code {
        justify-content: center;
        width: 100%;
    }

    .specialties-selector {
        grid-template-columns: 1fr;
    }

    .certification-fields {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-save-profile,
    .btn-cancel {
        width: 100%;
        justify-content: center;
    }
}

/* ===== Toast Notification ===== */
.toast-notification {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #48bb78;
    color: white;
    padding: 18px 25px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(72, 187, 120, 0.4);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    font-weight: 600;
    z-index: 9999;
    animation: slideInUp 0.4s ease;
}

@keyframes slideInUp {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.toast-notification.hide {
    animation: slideOutDown 0.4s ease forwards;
}

@keyframes slideOutDown {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100px);
        opacity: 0;
    }
}

.toast-notification.error {
    background: #f56565;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="edit-profile-page">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="page-header-modern">
                    <h1>
                        <i class="fa fa-edit"></i>
                        Modifier Mon Profil
                    </h1>
                    <p>Personnalisez votre profil professionnel et gérez vos informations</p>
                </div>

                <?= form_open(admin_url('dietetic/update_my_profile'), ['id' => 'profile-form']); ?>

                <!-- Referral Code Section -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-qrcode"></i>
                        Code de Référence
                    </h2>

                    <div class="info-box">
                        <div class="info-box-content">
                            <i class="fa fa-info-circle info-box-icon"></i>
                            <div class="info-box-text">
                                <strong>Votre code de référence unique</strong><br>
                                Ce code permet aux patients de vous identifier lors de leur inscription. Partagez-le avec vos patients pour qu'ils puissent être automatiquement assignés à vous.
                            </div>
                        </div>
                    </div>

                    <div class="referral-code-display">
                        <div class="referral-info">
                            <div class="referral-label">Votre code de référence</div>
                            <div class="referral-value" id="currentReferralCode">
                                <?= isset($staff_member['dietitian_referral_code']) && !empty($staff_member['dietitian_referral_code'])
                                    ? htmlspecialchars($staff_member['dietitian_referral_code'])
                                    : 'Non généré'; ?>
                            </div>
                        </div>
                        <div class="referral-actions">
                            <?php if (isset($staff_member['dietitian_referral_code']) && !empty($staff_member['dietitian_referral_code'])): ?>
                                <button type="button" class="btn-copy-code" onclick="copyReferralCode()">
                                    <i class="fa fa-copy"></i>
                                    Copier
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-regenerate-code" onclick="generateReferralCode()">
                                    <i class="fa fa-refresh"></i>
                                    Générer Code
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-user-md"></i>
                        Informations Professionnelles
                    </h2>

                    <div class="form-row-modern">
                        <div class="form-group-modern">
                            <label>
                                <i class="fa fa-calendar"></i>
                                Années d'Expérience
                                <span class="label-required">*</span>
                            </label>
                            <input type="number"
                                   name="dietitian_years_experience"
                                   class="form-control-modern"
                                   min="0"
                                   max="50"
                                   value="<?= isset($staff_member['dietitian_years_experience']) ? $staff_member['dietitian_years_experience'] : 0; ?>"
                                   placeholder="Nombre d'années d'expérience">
                            <div class="help-text">
                                <i class="fa fa-info-circle"></i>
                                <span>Nombre d'années d'expérience en tant que diététicien</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialties -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-stethoscope"></i>
                        Spécialités & Compétences
                    </h2>

                    <div class="info-box">
                        <div class="info-box-content">
                            <i class="fa fa-info-circle info-box-icon"></i>
                            <div class="info-box-text">
                                Sélectionnez vos domaines d'expertise. Ces informations aident les patients à trouver le diététicien qui correspond le mieux à leurs besoins.
                            </div>
                        </div>
                    </div>

                    <div class="specialties-selector">
                        <?php foreach ($available_specialties as $specialty): ?>
                            <div class="specialty-checkbox-item">
                                <input type="checkbox"
                                       name="specialties[]"
                                       value="<?= $specialty['id']; ?>"
                                       id="specialty_<?= $specialty['id']; ?>"
                                       <?= in_array($specialty['id'], $selected_specialties) ? 'checked' : ''; ?>>
                                <label for="specialty_<?= $specialty['id']; ?>"
                                       class="specialty-checkbox-label"
                                       style="--specialty-color: <?= $specialty['color']; ?>; --specialty-color-light: <?= $specialty['color']; ?>99;">
                                    <i class="fa <?= $specialty['icon']; ?> specialty-icon"></i>
                                    <span class="specialty-name"><?= htmlspecialchars($specialty['name_fr']); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Biography -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-file-text"></i>
                        Biographie Professionnelle
                    </h2>

                    <div class="form-group-modern">
                        <label>
                            <i class="fa fa-pencil"></i>
                            Présentez-vous
                        </label>
                        <textarea name="dietitian_bio"
                                  class="form-control-modern"
                                  rows="8"
                                  placeholder="Partagez votre parcours professionnel, votre approche de la nutrition, vos valeurs..."><?= isset($staff_member['dietitian_bio']) ? htmlspecialchars($staff_member['dietitian_bio']) : ''; ?></textarea>
                        <div class="help-text">
                            <i class="fa fa-info-circle"></i>
                            <span>Une biographie complète aide à établir la confiance avec vos futurs patients</span>
                        </div>
                    </div>
                </div>

                <!-- Languages -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-language"></i>
                        Langues Parlées
                    </h2>

                    <div class="form-group-modern">
                        <label>
                            <i class="fa fa-globe"></i>
                            Ajoutez les langues que vous parlez
                        </label>

                        <div class="languages-input-container" id="languagesContainer">
                            <?php
                            $languages = [];
                            if (isset($staff_member['dietitian_languages']) && !empty($staff_member['dietitian_languages'])) {
                                $languages = explode(',', $staff_member['dietitian_languages']);
                            }
                            foreach ($languages as $language):
                                $language = trim($language);
                                if (!empty($language)):
                            ?>
                                <div class="language-tag">
                                    <span><?= htmlspecialchars($language); ?></span>
                                    <button type="button" class="language-tag-remove" onclick="removeLanguage(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>

                        <div class="language-input-wrapper">
                            <input type="text"
                                   id="languageInput"
                                   placeholder="Ex: Français, Anglais, Wolof..."
                                   onkeypress="if(event.key === 'Enter') { event.preventDefault(); addLanguage(); }">
                            <button type="button" class="btn-add-language" onclick="addLanguage()">
                                <i class="fa fa-plus"></i>
                                Ajouter
                            </button>
                        </div>

                        <input type="hidden" name="dietitian_languages" id="languagesHiddenInput" value="<?= isset($staff_member['dietitian_languages']) ? htmlspecialchars($staff_member['dietitian_languages']) : ''; ?>">
                    </div>
                </div>

                <!-- Certifications -->
                <div class="form-card">
                    <h2 class="form-section-title">
                        <i class="fa fa-certificate"></i>
                        Certifications & Diplômes
                    </h2>

                    <div class="info-box">
                        <div class="info-box-content">
                            <i class="fa fa-info-circle info-box-icon"></i>
                            <div class="info-box-text">
                                Ajoutez vos diplômes, certifications et formations professionnelles. Ces informations renforcent votre crédibilité.
                            </div>
                        </div>
                    </div>

                    <div class="certifications-manager" id="certificationsManager">
                        <?php
                        if (isset($certifications) && !empty($certifications)):
                            foreach ($certifications as $index => $cert):
                        ?>
                            <div class="certification-entry">
                                <div class="certification-entry-header">
                                    <span class="certification-entry-number">
                                        <i class="fa fa-graduation-cap"></i>
                                        Certification <?= $index + 1; ?>
                                    </span>
                                    <button type="button" class="btn-remove-certification" onclick="removeCertification(this)">
                                        <i class="fa fa-trash"></i>
                                        Supprimer
                                    </button>
                                </div>
                                <div class="certification-fields">
                                    <div class="certification-field">
                                        <label>Nom du diplôme/certification</label>
                                        <input type="text"
                                               name="certifications[<?= $index; ?>][name]"
                                               value="<?= htmlspecialchars($cert['name']); ?>"
                                               placeholder="Ex: Master en Nutrition Clinique">
                                    </div>
                                    <div class="certification-field">
                                        <label>Institution</label>
                                        <input type="text"
                                               name="certifications[<?= $index; ?>][institution]"
                                               value="<?= isset($cert['institution']) ? htmlspecialchars($cert['institution']) : ''; ?>"
                                               placeholder="Ex: Université Cheikh Anta Diop">
                                    </div>
                                    <div class="certification-field">
                                        <label>Année</label>
                                        <input type="text"
                                               name="certifications[<?= $index; ?>][year]"
                                               value="<?= isset($cert['year']) ? htmlspecialchars($cert['year']) : ''; ?>"
                                               placeholder="Ex: 2020">
                                    </div>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>

                    <button type="button" class="btn-add-certification" onclick="addCertification()">
                        <i class="fa fa-plus-circle"></i>
                        Ajouter une Certification
                    </button>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.location.href='<?= admin_url('dietetic/my_profile'); ?>'">
                        <i class="fa fa-times"></i>
                        Annuler
                    </button>
                    <button type="submit" class="btn-save-profile">
                        <i class="fa fa-save"></i>
                        Enregistrer les Modifications
                    </button>
                </div>

                <?= form_close(); ?>

            </div>
        </div>
    </div>
</div>

<script>
// Referral Code Copy
function copyReferralCode() {
    const code = document.getElementById('currentReferralCode').textContent.trim();

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('Code de référence copié !', 'success');
        }).catch(() => {
            fallbackCopy(code);
        });
    } else {
        fallbackCopy(code);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        showToast('Code de référence copié !', 'success');
    } catch (err) {
        alert('Code: ' + text);
    }

    document.body.removeChild(textarea);
}

function generateReferralCode() {
    // AJAX call to generate code
    $.ajax({
        url: '<?= admin_url('dietetic/generate_my_referral_code'); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                document.getElementById('currentReferralCode').textContent = response.code;
                showToast('Code de référence généré avec succès !', 'success');
                // Update button
                location.reload();
            } else {
                showToast(response.message || 'Erreur lors de la génération du code', 'error');
            }
        },
        error: function() {
            showToast('Erreur lors de la génération du code', 'error');
        }
    });
}

// Languages Management
function addLanguage() {
    const input = document.getElementById('languageInput');
    const language = input.value.trim();

    if (language === '') {
        return;
    }

    const container = document.getElementById('languagesContainer');

    const tag = document.createElement('div');
    tag.className = 'language-tag';
    tag.innerHTML = `
        <span>${escapeHtml(language)}</span>
        <button type="button" class="language-tag-remove" onclick="removeLanguage(this)">
            <i class="fa fa-times"></i>
        </button>
    `;

    container.appendChild(tag);
    input.value = '';

    updateLanguagesHiddenInput();
}

function removeLanguage(button) {
    button.closest('.language-tag').remove();
    updateLanguagesHiddenInput();
}

function updateLanguagesHiddenInput() {
    const container = document.getElementById('languagesContainer');
    const tags = container.querySelectorAll('.language-tag span');
    const languages = Array.from(tags).map(tag => tag.textContent.trim());

    document.getElementById('languagesHiddenInput').value = languages.join(', ');
}

// Certifications Management
let certificationCounter = <?= isset($certifications) ? count($certifications) : 0; ?>;

function addCertification() {
    const manager = document.getElementById('certificationsManager');

    const entry = document.createElement('div');
    entry.className = 'certification-entry';
    entry.innerHTML = `
        <div class="certification-entry-header">
            <span class="certification-entry-number">
                <i class="fa fa-graduation-cap"></i>
                Certification ${certificationCounter + 1}
            </span>
            <button type="button" class="btn-remove-certification" onclick="removeCertification(this)">
                <i class="fa fa-trash"></i>
                Supprimer
            </button>
        </div>
        <div class="certification-fields">
            <div class="certification-field">
                <label>Nom du diplôme/certification</label>
                <input type="text"
                       name="certifications[${certificationCounter}][name]"
                       placeholder="Ex: Master en Nutrition Clinique">
            </div>
            <div class="certification-field">
                <label>Institution</label>
                <input type="text"
                       name="certifications[${certificationCounter}][institution]"
                       placeholder="Ex: Université Cheikh Anta Diop">
            </div>
            <div class="certification-field">
                <label>Année</label>
                <input type="text"
                       name="certifications[${certificationCounter}][year]"
                       placeholder="Ex: 2020">
            </div>
        </div>
    `;

    manager.appendChild(entry);
    certificationCounter++;
}

function removeCertification(button) {
    const entry = button.closest('.certification-entry');
    entry.remove();
    updateCertificationNumbers();
}

function updateCertificationNumbers() {
    const entries = document.querySelectorAll('.certification-entry');
    entries.forEach((entry, index) => {
        const numberSpan = entry.querySelector('.certification-entry-number');
        numberSpan.innerHTML = `
            <i class="fa fa-graduation-cap"></i>
            Certification ${index + 1}
        `;
    });
}

// Toast Notification
function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type === 'error' ? 'error' : ''}`;
    toast.innerHTML = `
        <i class="fa ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 3000);
}

// Utility function
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Form submission
document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Update languages hidden input before submit
    updateLanguagesHiddenInput();

    // Submit via AJAX
    const formData = new FormData(this);

    $.ajax({
        url: this.action,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Profil mis à jour avec succès !', 'success');
                setTimeout(() => {
                    window.location.href = '<?= admin_url('dietetic/my_profile'); ?>';
                }, 1500);
            } else {
                showToast(response.message || 'Erreur lors de la mise à jour', 'error');
            }
        },
        error: function() {
            showToast('Erreur lors de la mise à jour du profil', 'error');
        }
    });
});
</script>

<?php init_tail(); ?>
