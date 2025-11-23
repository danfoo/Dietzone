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

                        <!-- Section 1: Informations de Base -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-user-circle"></i>
                                <span>Informations de Base</span>
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
                                        <label for="dietitian_id"><?php echo _l('dietetic_dietitian'); ?> *</label>
                                        <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                            <?php foreach ($staff as $member) { ?>
                                                <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($patient) && $patient->dietitian_id == $member['staffid'])); ?>>
                                                    <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status"><?php echo _l('dietetic_status'); ?></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active" <?php echo set_select('status', 'active', (isset($patient) && $patient->status == 'active') || !isset($patient)); ?>>Active</option>
                                            <option value="inactive" <?php echo set_select('status', 'inactive', isset($patient) && $patient->status == 'inactive'); ?>>Inactive</option>
                                            <option value="archived" <?php echo set_select('status', 'archived', isset($patient) && $patient->status == 'archived'); ?>>Archived</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender"><?php echo _l('dietetic_gender'); ?></label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="">-- <?php echo _l('select'); ?> --</option>
                                            <option value="male" <?php echo set_select('gender', 'male', isset($patient) && $patient->gender == 'male'); ?>>Male</option>
                                            <option value="female" <?php echo set_select('gender', 'female', isset($patient) && $patient->gender == 'female'); ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_date"><?php echo _l('dietetic_birth_date'); ?></label>
                                        <input type="date" class="form-control" name="birth_date" value="<?php echo isset($patient) ? $patient->birth_date : ''; ?>" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone"><?php echo _l('dietetic_phone'); ?></label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo isset($patient) ? $patient->phone : ''; ?>" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email"><?php echo _l('dietetic_email'); ?></label>
                                        <input type="email" class="form-control" name="email" value="<?php echo isset($patient) ? $patient->email : ''; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Données Physiques et Objectifs -->
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fa fa-heartbeat"></i>
                                <span>Données Physiques & Objectifs</span>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="initial_weight"><?php echo _l('dietetic_initial_weight'); ?> (kg)</label>
                                        <input type="number" step="0.1" class="form-control" name="initial_weight" value="<?php echo isset($patient) ? $patient->initial_weight : ''; ?>" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="target_weight"><?php echo _l('dietetic_target_weight'); ?> (kg)</label>
                                        <input type="number" step="0.1" class="form-control" name="target_weight" value="<?php echo isset($patient) ? $patient->target_weight : ''; ?>" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height"><?php echo _l('dietetic_height'); ?> (cm)</label>
                                        <input type="number" step="0.1" class="form-control" name="height" value="<?php echo isset($patient) ? $patient->height : ''; ?>" />
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
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="objective"><?php echo _l('dietetic_objective'); ?></label>
                                        <textarea class="form-control" name="objective" rows="3"><?php echo isset($patient) ? $patient->objective : ''; ?></textarea>
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
