<?php
$active_page = 'profile';
$page_title = 'Mon Profil';
?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<!-- jsPDF Library for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
    /* Modern Profile Page Styles */
    .profile-header {
        background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
        border-radius: 24px;
        padding: 28px 24px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 12px 24px rgba(1, 128, 123, 0.25);
        text-align: center;
        position: relative;
    }

    .profile-avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 16px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #01807B;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-upload {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 36px;
        height: 36px;
        background: #F3911D;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s;
    }

    .profile-avatar-upload:hover {
        background: #e8850f;
        transform: scale(1.1);
    }

    .profile-avatar-upload i {
        color: white;
        font-size: 16px;
    }

    #avatarInput {
        display: none;
    }

    .profile-actions {
        position: absolute;
        top: 24px;
        right: 24px;
        display: flex;
        gap: 10px;
    }

    .profile-action-btn {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        color: white;
        font-size: 18px;
    }

    .profile-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .profile-name {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 8px 0;
        color: white;
    }

    .profile-email {
        font-size: 15px;
        opacity: 0.95;
        font-weight: 500;
        color: white;
    }

    .profile-section {
        background: white;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f8f9fa;
    }

    .section-title-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #01807B;
        font-size: 22px;
    }

    .btn-edit {
        background: linear-gradient(135deg, #01807B 0%, #026661 100%);
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-edit:hover {
        box-shadow: 0 4px 12px rgba(1, 128, 123, 0.4);
        transform: translateY(-2px);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .info-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .info-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 16px;
        color: #2c3e50;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-value i {
        color: #01807B;
        font-size: 18px;
    }

    .info-value.empty {
        color: #adb5bd;
        font-style: italic;
        font-weight: 500;
    }

    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-status.active {
        background: #d1f2eb;
        color: #01807B;
    }

    .badge-status.inactive {
        background: #f8d7da;
        color: #dc3545;
    }

    /* Password Strength Indicator */
    .password-strength {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        margin-top: 8px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.3s;
        border-radius: 2px;
    }

    .password-strength-bar.weak {
        width: 33%;
        background: #dc3545;
    }

    .password-strength-bar.medium {
        width: 66%;
        background: #ffc107;
    }

    .password-strength-bar.strong {
        width: 100%;
        background: #28a745;
    }

    .password-strength-text {
        font-size: 12px;
        margin-top: 4px;
        font-weight: 600;
    }

    .password-strength-text.weak {
        color: #dc3545;
    }

    .password-strength-text.medium {
        color: #ffc107;
    }

    .password-strength-text.strong {
        color: #28a745;
    }

    .password-suggestions {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 12px;
        margin-top: 12px;
        font-size: 13px;
    }

    .password-suggestions strong {
        display: block;
        margin-bottom: 6px;
        color: #856404;
    }

    .password-suggestions ul {
        margin: 0;
        padding-left: 20px;
        color: #856404;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
        color: white;
        padding: 24px;
        border-radius: 20px 20px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control, textarea.form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 15px;
        font-family: 'Josefin Sans', sans-serif;
        transition: all 0.3s;
        background: white;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .form-control:focus, textarea.form-control:focus {
        outline: none;
        border-color: #01807B;
        box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #01807B 0%, #026661 100%);
        color: white;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
    }

    .btn-primary:hover {
        box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
        transform: translateY(-2px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
        margin-top: 10px;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease;
    }

    .alert-success {
        background: #d1f2eb;
        color: #01807B;
        border: 2px solid #01807B;
    }

    .alert-error {
        background: #f8d7da;
        color: #dc3545;
        border: 2px solid #dc3545;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .text-content {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
        color: #495057;
        font-size: 14px;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .text-content.empty {
        color: #adb5bd;
        font-style: italic;
        text-align: center;
    }

    /* Documents Section */
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .document-card {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .document-card:hover {
        background: #e9ecef;
        border-color: #01807B;
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .document-icon {
        font-size: 48px;
        color: #01807B;
        margin-bottom: 12px;
    }

    .document-name {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 6px;
        word-break: break-word;
    }

    .document-date {
        font-size: 12px;
        color: #6c757d;
    }

    .document-actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .document-action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .document-action-btn.view {
        background: #01807B;
        color: white;
    }

    .document-action-btn.delete {
        background: #dc3545;
        color: white;
    }

    .upload-zone {
        border: 3px dashed #01807B;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: rgba(1, 128, 123, 0.05);
    }

    .upload-zone:hover {
        background: rgba(1, 128, 123, 0.1);
        border-color: #026661;
    }

    .upload-zone i {
        font-size: 48px;
        color: #01807B;
        margin-bottom: 16px;
    }

    .upload-zone p {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .upload-zone small {
        color: #6c757d;
        display: block;
        margin-top: 8px;
    }

    /* QR Code Section */
    #qrcode {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    #qrcode canvas {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-header {
            padding: 20px 16px;
            border-radius: 12px;
        }

        .profile-name {
            font-size: 20px;
        }

        .profile-actions {
            position: static;
            justify-content: center;
            margin-top: 16px;
        }

        .documents-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-actions">
            <button class="profile-action-btn" onclick="showQRCode()" title="QR Code du profil">
                <i class="fa fa-qrcode"></i>
            </button>
            <button class="profile-action-btn" onclick="exportProfilePDF()" title="Exporter en PDF">
                <i class="fa fa-file-pdf-o"></i>
            </button>
        </div>

        <div class="profile-avatar-container">
            <div class="profile-avatar" id="profileAvatar">
                <?php
                // Utiliser la photo de profil Perfex si disponible
                if (isset($contact) && $contact && !empty($contact->profile_image)):
                ?>
                    <img src="<?php echo contact_profile_image_url($contact->id, 'small'); ?>" alt="<?php echo htmlspecialchars($client->company); ?>" id="profileImage">
                <?php else:
                    // Afficher les initiales
                    $names = explode(' ', trim($client->company));
                    $initials = '';
                    if (count($names) >= 2) {
                        $initials = strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($client->company, 0, 2));
                    }
                ?>
                    <div style="font-size: 36px; font-weight: 700; color: #01807B;" id="profileInitials">
                        <?php echo $initials; ?>
                    </div>
                <?php endif; ?>
            </div>
            <input type="file" id="profileImageInput" accept="image/jpeg,image/jpg,image/png,image/gif" style="display: none;">
            <button type="button" class="profile-avatar-upload" onclick="document.getElementById('profileImageInput').click()" title="Modifier ma photo de profil">
                <i class="fa fa-camera"></i>
            </button>
        </div>

        <h1 class="profile-name"><?php echo htmlspecialchars($client->company); ?></h1>
        <p class="profile-email">
            <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($patient->email ?: $client->email); ?>
        </p>
    </div>

    <!-- Personal Information Section -->
    <div class="profile-section">
        <div class="section-title">
            <div class="section-title-left">
                <i class="fa fa-user-circle"></i>
                <span>Informations Personnelles</span>
            </div>
            <button class="btn-edit" onclick="openEditModal()">
                <i class="fa fa-pencil"></i> Modifier
            </button>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Prénom & Nom</div>
                <div class="info-value">
                    <i class="fa fa-user"></i>
                    <?php echo htmlspecialchars($client->company); ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Âge</div>
                <div class="info-value <?php echo !$age ? 'empty' : ''; ?>">
                    <i class="fa fa-birthday-cake"></i>
                    <?php echo $age ? $age . ' ans' : 'Non renseigné'; ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Sexe</div>
                <div class="info-value <?php echo !$patient->gender ? 'empty' : ''; ?>">
                    <i class="fa <?php echo $patient->gender == 'male' ? 'fa-mars' : ($patient->gender == 'female' ? 'fa-venus' : 'fa-genderless'); ?>"></i>
                    <?php
                    if ($patient->gender == 'male') {
                        echo 'Homme';
                    } elseif ($patient->gender == 'female') {
                        echo 'Femme';
                    } else {
                        echo 'Non renseigné';
                    }
                    ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Taille</div>
                <div class="info-value <?php echo !$patient->height ? 'empty' : ''; ?>">
                    <i class="fa fa-arrows-v"></i>
                    <?php echo $patient->height ? number_format($patient->height, 0) . ' cm' : 'Non renseigné'; ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Poids Initial</div>
                <div class="info-value <?php echo !$patient->initial_weight ? 'empty' : ''; ?>">
                    <i class="fa fa-balance-scale"></i>
                    <?php echo $patient->initial_weight ? number_format($patient->initial_weight, 1) . ' kg' : 'Non renseigné'; ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Poids Actuel</div>
                <div class="info-value <?php echo !$latest_measurement || !$latest_measurement->weight ? 'empty' : ''; ?>">
                    <i class="fa fa-heartbeat"></i>
                    <?php echo ($latest_measurement && $latest_measurement->weight) ? number_format($latest_measurement->weight, 1) . ' kg' : 'Non renseigné'; ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">E-mail</div>
                <div class="info-value">
                    <i class="fa fa-envelope"></i>
                    <span id="emailValue"><?php echo htmlspecialchars($patient->email ?: $client->email); ?></span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">N° de téléphone</div>
                <div class="info-value <?php echo !$patient->phone ? 'empty' : ''; ?>">
                    <i class="fa fa-phone"></i>
                    <span id="phoneValue"><?php echo $patient->phone ? htmlspecialchars($patient->phone) : 'Non renseigné'; ?></span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Statut</div>
                <div class="info-value">
                    <span class="badge-status <?php echo $patient->status; ?>">
                        <?php echo $patient->status == 'active' ? 'Actif' : 'Inactif'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Contact Section -->
    <div class="profile-section">
        <div class="section-title">
            <div class="section-title-left">
                <i class="fa fa-phone-square"></i>
                <span>Contact d'Urgence</span>
            </div>
            <button class="btn-edit" onclick="openEmergencyContactModal()">
                <i class="fa fa-pencil"></i> Modifier
            </button>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nom du Contact</div>
                <div class="info-value <?php echo !$patient->emergency_contact ? 'empty' : ''; ?>">
                    <i class="fa fa-user"></i>
                    <span id="emergencyContactValue"><?php echo $patient->emergency_contact ? htmlspecialchars($patient->emergency_contact) : 'Non renseigné'; ?></span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Téléphone d'Urgence</div>
                <div class="info-value <?php echo !$patient->emergency_phone ? 'empty' : ''; ?>">
                    <i class="fa fa-phone"></i>
                    <span id="emergencyPhoneValue"><?php echo $patient->emergency_phone ? htmlspecialchars($patient->emergency_phone) : 'Non renseigné'; ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Diet Information Section -->
    <div class="profile-section">
        <div class="section-title">
            <div class="section-title-left">
                <i class="fa fa-apple"></i>
                <span>Informations Diététiques</span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Objectif</div>
                <div class="info-value <?php echo !$patient->objective ? 'empty' : ''; ?>">
                    <i class="fa fa-bullseye"></i>
                    <?php echo $patient->objective ? htmlspecialchars($patient->objective) : 'Non renseigné'; ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">Niveau d'Activité</div>
                <div class="info-value <?php echo !$patient->activity_level ? 'empty' : ''; ?>">
                    <i class="fa fa-bicycle"></i>
                    <?php
                    $activity_levels = [
                        'sedentary' => 'Sédentaire',
                        'light' => 'Léger',
                        'moderate' => 'Modéré',
                        'active' => 'Actif',
                        'very_active' => 'Très Actif'
                    ];
                    echo isset($activity_levels[$patient->activity_level]) ? $activity_levels[$patient->activity_level] : 'Non renseigné';
                    ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Préférences Alimentaires</div>
            <div class="text-content <?php echo !$patient->dietary_preferences ? 'empty' : ''; ?>" id="dietaryPreferencesValue">
                <?php echo $patient->dietary_preferences ? nl2br(htmlspecialchars($patient->dietary_preferences)) : 'Aucune préférence alimentaire renseignée'; ?>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Conditions Médicales</div>
            <div class="text-content <?php echo !$patient->medical_conditions ? 'empty' : ''; ?>">
                <?php echo $patient->medical_conditions ? nl2br(htmlspecialchars($patient->medical_conditions)) : 'Aucune condition médicale renseignée'; ?>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Allergies</div>
            <div class="text-content <?php echo !$patient->allergies ? 'empty' : ''; ?>" id="allergiesValue">
                <?php echo $patient->allergies ? nl2br(htmlspecialchars($patient->allergies)) : 'Aucune allergie renseignée'; ?>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="profile-section">
        <div class="section-title">
            <div class="section-title-left">
                <i class="fa fa-file-text"></i>
                <span>Documents Médicaux</span>
            </div>
        </div>

        <div class="upload-zone" onclick="$('#documentInput').click()">
            <i class="fa fa-cloud-upload"></i>
            <p>Cliquez pour uploader un document</p>
            <small>PDF, images (max 10MB)</small>
        </div>
        <input type="file" id="documentInput" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">

        <div class="documents-grid" id="documentsGrid">
            <!-- Documents will be loaded here dynamically -->
            <div class="text-content empty" style="grid-column: 1 / -1;">
                Aucun document médical uploadé
            </div>
        </div>
    </div>

    <!-- Password Change Section -->
    <div class="profile-section">
        <div class="section-title">
            <div class="section-title-left">
                <i class="fa fa-lock"></i>
                <span>Modifier le Mot de Passe</span>
            </div>
        </div>

        <div id="passwordAlert" style="display: none;"></div>

        <form id="passwordForm" class="password-form">
            <div class="form-group">
                <label for="current_password">Mot de passe actuel *</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="new_password">Nouveau mot de passe * (minimum 6 caractères)</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-strength-text" id="strengthText"></div>
                <div id="passwordSuggestions" class="password-suggestions" style="display: none;">
                    <strong>💡 Suggestions pour un mot de passe fort :</strong>
                    <ul>
                        <li>Au moins 8 caractères</li>
                        <li>Mélange de majuscules et minuscules</li>
                        <li>Inclure des chiffres</li>
                        <li>Utiliser des caractères spéciaux (!@#$%)</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le nouveau mot de passe *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">
                <i class="fa fa-key"></i>
                Changer le Mot de Passe
            </button>
        </form>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa fa-pencil"></i>
                Modifier le Profil
            </div>
            <button class="modal-close" onclick="closeEditModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="editAlert" style="display: none;"></div>
            <form id="editForm">
                <div class="form-group">
                    <label for="edit_phone">N° de téléphone</label>
                    <input type="tel" id="edit_phone" class="form-control" value="<?php echo htmlspecialchars($patient->phone ?: ''); ?>">
                </div>

                <div class="form-group">
                    <label for="edit_dietary_preferences">Préférences Alimentaires</label>
                    <textarea id="edit_dietary_preferences" class="form-control"><?php echo htmlspecialchars($patient->dietary_preferences ?: ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_allergies">Allergies</label>
                    <textarea id="edit_allergies" class="form-control"><?php echo htmlspecialchars($patient->allergies ?: ''); ?></textarea>
                </div>

                <button type="submit" class="btn-primary" id="editSubmitBtn">
                    <i class="fa fa-check"></i>
                    Enregistrer
                </button>
                <button type="button" class="btn-secondary" onclick="closeEditModal()">
                    <i class="fa fa-times"></i>
                    Annuler
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Emergency Contact Modal -->
<div class="modal-overlay" id="emergencyModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa fa-phone-square"></i>
                Contact d'Urgence
            </div>
            <button class="modal-close" onclick="closeEmergencyContactModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="emergencyAlert" style="display: none;"></div>
            <form id="emergencyForm">
                <div class="form-group">
                    <label for="emergency_contact">Nom du contact</label>
                    <input type="text" id="emergency_contact" class="form-control" value="<?php echo htmlspecialchars($patient->emergency_contact ?: ''); ?>">
                </div>

                <div class="form-group">
                    <label for="emergency_phone">Téléphone d'urgence</label>
                    <input type="tel" id="emergency_phone" class="form-control" value="<?php echo htmlspecialchars($patient->emergency_phone ?: ''); ?>">
                </div>

                <button type="submit" class="btn-primary" id="emergencySubmitBtn">
                    <i class="fa fa-check"></i>
                    Enregistrer
                </button>
                <button type="button" class="btn-secondary" onclick="closeEmergencyContactModal()">
                    <i class="fa fa-times"></i>
                    Annuler
                </button>
            </form>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal-overlay" id="qrModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fa fa-qrcode"></i>
                QR Code du Profil
            </div>
            <button class="modal-close" onclick="closeQRModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="qrcode"></div>
            <p style="text-align: center; color: #6c757d; margin-top: 16px;">
                Scannez ce code pour partager votre profil avec votre diététicien
            </p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Password strength checker
    $('#new_password').on('input', function() {
        var password = $(this).val();
        var strength = checkPasswordStrength(password);

        $('#strengthBar').removeClass('weak medium strong').addClass(strength.class);
        $('#strengthText').removeClass('weak medium strong').addClass(strength.class).text(strength.text);

        if (strength.class === 'weak' || strength.class === 'medium') {
            $('#passwordSuggestions').show();
        } else {
            $('#passwordSuggestions').hide();
        }
    });

    // Password form submission
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();

        $('#passwordAlert').hide();

        var currentPassword = $('#current_password').val();
        var newPassword = $('#new_password').val();
        var confirmPassword = $('#confirm_password').val();

        if (newPassword !== confirmPassword) {
            showAlert('passwordAlert', 'error', 'Les mots de passe ne correspondent pas');
            return;
        }

        if (newPassword.length < 6) {
            showAlert('passwordAlert', 'error', 'Le mot de passe doit contenir au moins 6 caractères');
            return;
        }

        $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Traitement...');

        $.ajax({
            url: '<?php echo site_url('dietetic/portal/update_password'); ?>',
            type: 'POST',
            data: {
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('passwordAlert', 'success', response.message);
                    $('#passwordForm')[0].reset();
                    $('#strengthBar').removeClass('weak medium strong');
                    $('#strengthText').text('');
                    $('#passwordSuggestions').hide();
                } else {
                    showAlert('passwordAlert', 'error', response.message);
                }
            },
            error: function() {
                showAlert('passwordAlert', 'error', 'Une erreur est survenue. Veuillez réessayer.');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="fa fa-key"></i> Changer le Mot de Passe');
            }
        });
    });

    // Edit profile form
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $('#editAlert').hide();
        $('#editSubmitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: '<?php echo site_url('dietetic/portal/update_profile'); ?>',
            type: 'POST',
            data: {
                phone: $('#edit_phone').val(),
                dietary_preferences: $('#edit_dietary_preferences').val(),
                allergies: $('#edit_allergies').val(),
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('editAlert', 'success', response.message);
                    // Update values on page
                    $('#phoneValue').text($('#edit_phone').val() || 'Non renseigné');
                    $('#dietaryPreferencesValue').text($('#edit_dietary_preferences').val() || 'Aucune préférence alimentaire renseignée');
                    $('#allergiesValue').text($('#edit_allergies').val() || 'Aucune allergie renseignée');

                    setTimeout(function() {
                        closeEditModal();
                    }, 1500);
                } else {
                    showAlert('editAlert', 'error', response.message);
                }
            },
            error: function() {
                showAlert('editAlert', 'error', 'Une erreur est survenue.');
            },
            complete: function() {
                $('#editSubmitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Enregistrer');
            }
        });
    });

    // Emergency contact form
    $('#emergencyForm').on('submit', function(e) {
        e.preventDefault();

        $('#emergencyAlert').hide();
        $('#emergencySubmitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        $.ajax({
            url: '<?php echo site_url('dietetic/portal/update_emergency_contact'); ?>',
            type: 'POST',
            data: {
                emergency_contact: $('#emergency_contact').val(),
                emergency_phone: $('#emergency_phone').val(),
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('emergencyAlert', 'success', response.message);
                    // Update values on page
                    $('#emergencyContactValue').text($('#emergency_contact').val() || 'Non renseigné');
                    $('#emergencyPhoneValue').text($('#emergency_phone').val() || 'Non renseigné');

                    setTimeout(function() {
                        closeEmergencyContactModal();
                    }, 1500);
                } else {
                    showAlert('emergencyAlert', 'error', response.message);
                }
            },
            error: function() {
                showAlert('emergencyAlert', 'error', 'Une erreur est survenue.');
            },
            complete: function() {
                $('#emergencySubmitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Enregistrer');
            }
        });
    });

    // Document upload
    $('#documentInput').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                alert('Le fichier ne doit pas dépasser 10MB');
                return;
            }

            var formData = new FormData();
            formData.append('document', file);
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

            $.ajax({
                url: '<?php echo site_url('dietetic/portal/upload_document'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Document uploadé avec succès');
                        location.reload();
                    } else {
                        alert(response.message || 'Erreur lors de l\'upload');
                    }
                },
                error: function() {
                    alert('Erreur lors de l\'upload du document');
                }
            });
        }
    });

    // Profile image upload
    $('#profileImageInput').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Type de fichier non autorisé. Utilisez une image (JPG, PNG ou GIF)');
                this.value = '';
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('L\'image ne doit pas dépasser 5MB');
                this.value = '';
                return;
            }

            // Show loading state
            var $avatar = $('#profileAvatar');
            var originalContent = $avatar.html();
            $avatar.html('<div style="font-size: 24px; color: #01807B;"><i class="fa fa-spinner fa-spin"></i><br><small>Upload...</small></div>');

            var formData = new FormData();
            formData.append('profile_image', file);
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

            $.ajax({
                url: '<?php echo site_url('dietetic/portal/upload_profile_photo'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the avatar with new image
                        $avatar.html('<img src="' + response.image_url + '?t=' + new Date().getTime() + '" alt="Photo de profil" id="profileImage" style="width: 100%; height: 100%; object-fit: cover;">');
                        alert(response.message);
                    } else {
                        alert(response.message || 'Erreur lors de l\'upload de l\'image');
                        $avatar.html(originalContent);
                    }
                },
                error: function() {
                    alert('Erreur lors de l\'upload de l\'image');
                    $avatar.html(originalContent);
                },
                complete: function() {
                    // Reset input
                    $('#profileImageInput').val('');
                }
            });
        }
    });
});

function checkPasswordStrength(password) {
    var strength = {
        class: 'weak',
        text: 'Faible'
    };

    if (password.length === 0) {
        return { class: '', text: '' };
    }

    var score = 0;

    // Length
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;

    // Lowercase
    if (/[a-z]/.test(password)) score++;

    // Uppercase
    if (/[A-Z]/.test(password)) score++;

    // Numbers
    if (/\d/.test(password)) score++;

    // Special chars
    if (/[^a-zA-Z\d]/.test(password)) score++;

    if (score <= 2) {
        strength = { class: 'weak', text: 'Faible' };
    } else if (score <= 4) {
        strength = { class: 'medium', text: 'Moyen' };
    } else {
        strength = { class: 'strong', text: 'Fort' };
    }

    return strength;
}

function showAlert(elementId, type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    $('#' + elementId)
        .removeClass('alert-success alert-error')
        .addClass('alert ' + alertClass)
        .html('<i class="fa ' + icon + '"></i> ' + message)
        .show();

    $('html, body').animate({
        scrollTop: $('#' + elementId).offset().top - 100
    }, 300);
}

function openEditModal() {
    $('#editModal').addClass('active');
}

function closeEditModal() {
    $('#editModal').removeClass('active');
    $('#editAlert').hide();
}

function openEmergencyContactModal() {
    $('#emergencyModal').addClass('active');
}

function closeEmergencyContactModal() {
    $('#emergencyModal').removeClass('active');
    $('#emergencyAlert').hide();
}

function showQRCode() {
    $('#qrcode').empty();
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo site_url('dietetic/portal/profile'); ?>",
        width: 256,
        height: 256,
        colorDark : "#01807B",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    $('#qrModal').addClass('active');
}

function closeQRModal() {
    $('#qrModal').removeClass('active');
}

async function exportProfilePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add content to PDF
    doc.setFontSize(20);
    doc.setTextColor(1, 128, 123);
    doc.text('Mon Profil Patient', 20, 20);

    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text('Nom: <?php echo addslashes($client->company); ?>', 20, 40);
    doc.text('Email: <?php echo addslashes($patient->email ?: $client->email); ?>', 20, 50);
    <?php if ($age): ?>
    doc.text('Âge: <?php echo $age; ?> ans', 20, 60);
    <?php endif; ?>
    <?php if ($patient->phone): ?>
    doc.text('Téléphone: <?php echo addslashes($patient->phone); ?>', 20, 70);
    <?php endif; ?>

    // Save PDF
    doc.save('mon-profil.pdf');
}

// Close modals on overlay click
$('.modal-overlay').on('click', function(e) {
    if (e.target === this) {
        $(this).removeClass('active');
    }
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
