<?php
$active_page = 'profile';
$page_title = 'Mon Profil';
?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        background: white;
        border-radius: 50%;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #01807B;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f8f9fa;
    }

    .section-title i {
        color: #01807B;
        font-size: 22px;
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

    /* Password Change Form */
    .password-form {
        background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.05) 100%);
        padding: 20px;
        border-radius: 12px;
        border: 2px solid #e9ecef;
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

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 15px;
        font-family: 'Josefin Sans', sans-serif;
        transition: all 0.3s;
        background: white;
    }

    .form-control:focus {
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
    }
</style>

<div class="content-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fa fa-user"></i>
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars($client->company); ?></h1>
        <p class="profile-email">
            <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($patient->email ?: $client->email); ?>
        </p>
    </div>

    <!-- Personal Information Section -->
    <div class="profile-section">
        <div class="section-title">
            <i class="fa fa-user-circle"></i>
            Informations Personnelles
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
                    <?php echo htmlspecialchars($patient->email ?: $client->email); ?>
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">N° de téléphone</div>
                <div class="info-value <?php echo !$patient->phone ? 'empty' : ''; ?>">
                    <i class="fa fa-phone"></i>
                    <?php echo $patient->phone ? htmlspecialchars($patient->phone) : 'Non renseigné'; ?>
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

    <!-- Diet Information Section -->
    <div class="profile-section">
        <div class="section-title">
            <i class="fa fa-apple"></i>
            Informations Diététiques
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

        <?php if ($patient->dietary_preferences) { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Préférences Alimentaires</div>
            <div class="text-content">
                <?php echo nl2br(htmlspecialchars($patient->dietary_preferences)); ?>
            </div>
        </div>
        <?php } else { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Préférences Alimentaires</div>
            <div class="text-content empty">
                Aucune préférence alimentaire renseignée
            </div>
        </div>
        <?php } ?>

        <?php if ($patient->medical_conditions) { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Conditions Médicales</div>
            <div class="text-content">
                <?php echo nl2br(htmlspecialchars($patient->medical_conditions)); ?>
            </div>
        </div>
        <?php } else { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Conditions Médicales</div>
            <div class="text-content empty">
                Aucune condition médicale renseignée
            </div>
        </div>
        <?php } ?>

        <?php if ($patient->allergies) { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Allergies</div>
            <div class="text-content">
                <?php echo nl2br(htmlspecialchars($patient->allergies)); ?>
            </div>
        </div>
        <?php } else { ?>
        <div style="margin-top: 20px;">
            <div class="info-label" style="margin-bottom: 10px;">Allergies</div>
            <div class="text-content empty">
                Aucune allergie renseignée
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Password Change Section -->
    <div class="profile-section">
        <div class="section-title">
            <i class="fa fa-lock"></i>
            Modifier le Mot de Passe
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

<script>
$(document).ready(function() {
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();

        // Hide previous alert
        $('#passwordAlert').hide();

        // Get form values
        var currentPassword = $('#current_password').val();
        var newPassword = $('#new_password').val();
        var confirmPassword = $('#confirm_password').val();

        // Client-side validation
        if (newPassword !== confirmPassword) {
            showAlert('error', 'Les mots de passe ne correspondent pas');
            return;
        }

        if (newPassword.length < 6) {
            showAlert('error', 'Le mot de passe doit contenir au moins 6 caractères');
            return;
        }

        // Disable submit button
        $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Traitement...');

        // Send AJAX request
        $.ajax({
            url: '<?php echo site_url('dietetic/portal/update_password'); ?>',
            type: 'POST',
            data: {
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#passwordForm')[0].reset();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Une erreur est survenue. Veuillez réessayer.');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="fa fa-key"></i> Changer le Mot de Passe');
            }
        });
    });

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        $('#passwordAlert')
            .removeClass('alert-success alert-error')
            .addClass('alert ' + alertClass)
            .html('<i class="fa ' + icon + '"></i> ' + message)
            .show();

        // Scroll to alert
        $('html, body').animate({
            scrollTop: $('#passwordAlert').offset().top - 100
        }, 300);
    }
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
