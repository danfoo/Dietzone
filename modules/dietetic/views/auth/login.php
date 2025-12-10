<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Connexion - DietZone</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --primary-color: #01807B;
            --secondary-color: #F3911D;
        }

        body {
            background: #01807B;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .logo-section {
            text-align: center;
            padding: 30px 20px 20px;
            background: #01807B;
        }

        .logo-section img {
            max-height: 50px;
            width: auto;
            margin-bottom: 10px;
        }

        .logo-section h1 {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .logo-section h1 i {
            font-size: 26px;
            margin-right: 8px;
        }

        .auth-card {
            background: #f5f5f5;
            flex: 1;
            border-radius: 30px 30px 0 0;
            overflow: hidden;
            margin-top: 10px;
        }

        .auth-tabs {
            display: flex;
            background: white;
            padding: 5px;
            margin: 15px 15px 0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .auth-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #6c757d;
            transition: all 0.3s;
            border-radius: 8px;
            background: transparent;
        }

        .auth-tab.active {
            background: #01807B;
            color: white;
        }

        .auth-content {
            padding: 20px 20px 30px;
            background: #f5f5f5;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 20px 0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 13px;
        }

        .form-control {
            height: 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus {
            border-color: #01807B;
            box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
            outline: none;
        }

        .iti {
            width: 100%;
        }

        .iti__flag-container {
            border-right: 2px solid #e0e0e0;
        }

        .iti__selected-flag {
            padding: 0 16px;
        }

        .btn-primary {
            background: #01807B;
            border: none;
            height: 54px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
            color: white;
            margin-top: 10px;
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        .btn-primary:disabled,
        .btn-primary.loading {
            opacity: 0.7;
            transform: none;
        }

        .btn-secondary {
            background: white;
            border: 2px solid #01807B;
            color: #01807B;
            height: 54px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-secondary:active {
            transform: scale(0.98);
        }

        .link-primary {
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .checkbox label {
            font-weight: normal;
            color: #6c757d;
            font-size: 14px;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #999;
            font-size: 13px;
            font-weight: 500;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            background: #f5f5f5;
            padding: 0 15px;
            position: relative;
        }

        .otp-input {
            text-align: center;
            font-size: 22px;
            letter-spacing: 8px;
            font-weight: 700;
        }

        .resend-link {
            color: #01807B;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            font-weight: 600;
        }

        .password-strength.weak {
            color: #dc3545;
        }

        .password-strength.medium {
            color: #ffc107;
        }

        .password-strength.strong {
            color: #28a745;
        }

        .loading-spinner {
            display: none;
            margin-left: 8px;
        }

        .btn-primary.loading .loading-spinner,
        .btn-secondary.loading .loading-spinner {
            display: inline-block;
        }

        .footer-link {
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
        }

        .footer-link p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .footer-link a {
            color: #F3911D;
            text-decoration: none;
            font-weight: 600;
        }

        .text-muted-sm {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.5;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <?php
            $company_logo = get_option('company_logo');
            if (!empty($company_logo)) { ?>
                <img src="<?php echo base_url('uploads/company/' . $company_logo); ?>" alt="DietZone Logo">
            <?php } ?>
            <h1><i class="fa fa-heartbeat"></i> DietZone</h1>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <!-- Tabs -->
            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="login">
                    <i class="fa fa-sign-in"></i> Connexion
                </div>
                <div class="auth-tab" data-tab="register">
                    <i class="fa fa-user-plus"></i> Inscription
                </div>
            </div>

            <!-- Content -->
            <div class="auth-content">
                <div id="alert-container"></div>

                <!-- Login Tab -->
                <div class="tab-pane active" id="login-tab">
                    <form id="login-form">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                        <div class="form-group">
                            <label><i class="fa fa-phone"></i> Numéro de téléphone</label>
                            <input type="tel" id="login-phone" name="phone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-lock"></i> Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" value="1"> Se souvenir de moi
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-sign-in"></i> Se connecter
                            <i class="fa fa-spinner fa-spin loading-spinner"></i>
                        </button>
                    </form>

                    <div class="divider">
                        <span>OU</span>
                    </div>

                    <button type="button" class="btn btn-secondary" id="btn-otp">
                        <i class="fa fa-mobile"></i> Connexion par SMS
                    </button>

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" class="link-primary" id="link-forgot-password">
                            <i class="fa fa-question-circle"></i> Mot de passe oublié ?
                        </a>
                        <br>
                        <a href="#" class="link-primary" id="link-diagnostic" style="margin-top: 10px; display: inline-block;">
                            <i class="fa fa-stethoscope"></i> Problème de connexion ?
                        </a>
                    </div>

                    <!-- Diagnostic Section (Hidden by default) -->
                    <div id="diagnostic-section" style="display: none; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 2px solid #01807B;">
                        <h4 style="color: #01807B; margin: 0 0 15px 0; font-size: 16px;">
                            🔍 Diagnostic de connexion
                        </h4>
                        <p style="color: #6c757d; font-size: 13px; margin-bottom: 15px;">
                            Entrez votre numéro pour vérifier si votre compte existe :
                        </p>
                        <div class="form-group">
                            <input type="tel" id="diagnostic-phone" class="form-control" placeholder="+221 77 123 45 67">
                        </div>
                        <button type="button" class="btn btn-primary" id="btn-check-phone">
                            <i class="fa fa-search"></i> Vérifier
                            <i class="fa fa-spinner fa-spin loading-spinner"></i>
                        </button>
                        <div id="diagnostic-result" style="margin-top: 15px;"></div>
                    </div>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane" id="register-tab">
                    <form id="register-form">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                        <div class="form-group">
                            <label><i class="fa fa-user"></i> Prénom</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-user"></i> Nom</label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-envelope"></i> Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-phone"></i> Numéro de téléphone</label>
                            <input type="tel" id="register-phone" name="phone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-lock"></i> Mot de passe</label>
                            <input type="password" name="password" id="register-password" class="form-control" placeholder="Au moins 6 caractères" required>
                            <div class="password-strength" id="password-strength"></div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-lock"></i> Confirmer le mot de passe</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-user-plus"></i> Créer mon compte
                            <i class="fa fa-spinner fa-spin loading-spinner"></i>
                        </button>
                    </form>

                    <div style="text-align: center; margin-top: 20px;">
                        <p class="text-muted-sm">
                            Déjà un compte ?
                            <a href="#" class="link-primary auth-tab-link" data-tab="login">Se connecter</a>
                        </p>
                    </div>
                </div>

                <!-- OTP Tab -->
                <div class="tab-pane" id="otp-tab">
                    <div id="otp-request-form">
                        <p class="text-muted-sm" style="margin-bottom: 20px;">
                            Entrez votre numéro de téléphone pour recevoir un code de vérification par SMS.
                        </p>

                        <form id="request-otp-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <div class="form-group">
                                <label><i class="fa fa-phone"></i> Numéro de téléphone</label>
                                <input type="tel" id="otp-phone" name="phone" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane"></i> Envoyer le code
                                <i class="fa fa-spinner fa-spin loading-spinner"></i>
                            </button>
                        </form>
                    </div>

                    <div id="otp-verify-form" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Un code de vérification a été envoyé par SMS.
                        </div>

                        <form id="verify-otp-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" id="verify-phone" name="phone">

                            <div class="form-group">
                                <label><i class="fa fa-key"></i> Code de vérification</label>
                                <input type="text" name="code" class="form-control otp-input" maxlength="6" placeholder="••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Vérifier
                                <i class="fa fa-spinner fa-spin loading-spinner"></i>
                            </button>

                            <div style="text-align: center; margin-top: 15px;">
                                <a href="#" class="resend-link" id="resend-otp">
                                    <i class="fa fa-refresh"></i> Renvoyer le code
                                </a>
                            </div>
                        </form>
                    </div>

                    <div style="text-align: center;">
                        <a href="#" class="back-link auth-tab-link" data-tab="login">
                            <i class="fa fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <!-- Forgot Password Tab -->
                <div class="tab-pane" id="forgot-tab">
                    <div id="forgot-request-form">
                        <p class="text-muted-sm" style="margin-bottom: 20px;">
                            Entrez votre numéro de téléphone pour recevoir un code de réinitialisation par SMS.
                        </p>

                        <form id="request-reset-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <div class="form-group">
                                <label><i class="fa fa-phone"></i> Numéro de téléphone</label>
                                <input type="tel" id="forgot-phone" name="phone" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane"></i> Envoyer le code
                                <i class="fa fa-spinner fa-spin loading-spinner"></i>
                            </button>
                        </form>
                    </div>

                    <div id="forgot-reset-form" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Un code de réinitialisation a été envoyé par SMS.
                        </div>

                        <form id="reset-password-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" id="reset-phone" name="phone">

                            <div class="form-group">
                                <label><i class="fa fa-key"></i> Code de réinitialisation</label>
                                <input type="text" name="code" class="form-control" maxlength="6" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-lock"></i> Nouveau mot de passe</label>
                                <input type="password" name="password" class="form-control" placeholder="Au moins 6 caractères" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-lock"></i> Confirmer le mot de passe</label>
                                <input type="password" name="password_confirm" class="form-control" placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Réinitialiser
                                <i class="fa fa-spinner fa-spin loading-spinner"></i>
                            </button>
                        </form>
                    </div>

                    <div style="text-align: center;">
                        <a href="#" class="back-link auth-tab-link" data-tab="login">
                            <i class="fa fa-arrow-left"></i> Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer-link">
                <p>
                    <i class="fa fa-question-circle"></i> Besoin d'aide ?
                    <a href="mailto:support@dietzone.sn">support@dietzone.sn</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

    <script>
    $(document).ready(function() {
        // Initialiser intl-tel-input pour tous les champs téléphone
        const phoneInputs = ['login-phone', 'register-phone', 'otp-phone', 'forgot-phone'];
        const itiInstances = {};

        phoneInputs.forEach(function(inputId) {
            const input = document.querySelector('#' + inputId);
            if (input) {
                itiInstances[inputId] = window.intlTelInput(input, {
                    initialCountry: 'sn', // Sénégal par défaut
                    preferredCountries: ['sn', 'fr', 'ci', 'ml', 'gn', 'bf'],
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
                });
            }
        });

        // Fonction pour obtenir le numéro complet
        function getFullNumber(inputId) {
            if (itiInstances[inputId]) {
                return itiInstances[inputId].getNumber();
            }
            return '';
        }

        // Tabs switching
        $('.auth-tab, .auth-tab-link').on('click', function(e) {
            e.preventDefault();
            const tab = $(this).data('tab');
            switchTab(tab);
        });

        function switchTab(tab) {
            $('.auth-tab').removeClass('active');
            $('.auth-tab[data-tab="' + tab + '"]').addClass('active');
            $('.tab-pane').removeClass('active');
            $('#' + tab + '-tab').addClass('active');
            clearAlert();
        }

        // Show OTP tab
        $('#btn-otp').on('click', function() {
            switchTab('otp');
        });

        // Show forgot password tab
        $('#link-forgot-password').on('click', function(e) {
            e.preventDefault();
            switchTab('forgot');
        });

        // Show/hide diagnostic section
        $('#link-diagnostic').on('click', function(e) {
            e.preventDefault();
            $('#diagnostic-section').slideToggle(300);
            $('#diagnostic-result').html('');
        });

        // Initialize intl-tel-input for diagnostic phone
        const diagnosticInput = document.querySelector('#diagnostic-phone');
        const diagnosticIti = window.intlTelInput(diagnosticInput, {
            initialCountry: 'sn',
            preferredCountries: ['sn', 'fr', 'ci', 'ml', 'gn', 'bf'],
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        // Check phone diagnostic
        $('#btn-check-phone').on('click', function() {
            const $btn = $(this);
            const phone = diagnosticIti.getNumber();

            if (!phone) {
                $('#diagnostic-result').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px;"><i class="fa fa-exclamation-circle"></i> Veuillez entrer un numéro de téléphone</div>');
                return;
            }

            $btn.addClass('loading').prop('disabled', true);
            $('#diagnostic-result').html('');

            $.post('/mobile_auth.php?action=check_phone', {
                phone: phone
            }, function(response) {
                let html = '';

                if (response.success) {
                    // Afficher le numéro nettoyé
                    html += '<div style="background: white; padding: 12px; border-radius: 8px; margin-bottom: 10px; font-size: 13px;">';
                    html += '<strong>Numéro nettoyé :</strong> <code style="background: #f4f4f4; padding: 2px 6px; border-radius: 3px;">' + response.phone_cleaned + '</code>';
                    html += '</div>';

                    if (response.patient_found) {
                        // Patient trouvé
                        html += '<div class="alert alert-success" style="font-size: 13px; padding: 12px; margin-bottom: 10px;">';
                        html += '<i class="fa fa-check-circle"></i> <strong>Compte trouvé !</strong><br>';
                        html += 'Nom : ' + response.patient_info.name + '<br>';
                        html += 'Email : ' + response.patient_info.email;
                        html += '</div>';

                        if (!response.has_password) {
                            html += '<div class="alert alert-danger" style="font-size: 13px; padding: 12px;">';
                            html += '<i class="fa fa-exclamation-triangle"></i> <strong>Problème : Pas de mot de passe</strong><br>';
                            html += 'Utilisez "Mot de passe oublié" pour créer un mot de passe.';
                            html += '</div>';
                        } else if (response.phone_match) {
                            html += '<div class="alert alert-info" style="font-size: 13px; padding: 12px;">';
                            html += '<i class="fa fa-info-circle"></i> <strong>Tout est OK !</strong><br>';
                            html += 'Votre compte existe et a un mot de passe.<br>';
                            html += 'Si la connexion échoue, le mot de passe est incorrect.';
                            html += '</div>';
                        } else {
                            html += '<div class="alert alert-warning" style="font-size: 13px; padding: 12px;">';
                            html += '<i class="fa fa-exclamation-triangle"></i> <strong>Format différent</strong><br>';
                            html += 'Numéro en base : <code>' + response.patient_info.phone_db + '</code><br>';
                            html += 'Utilisez exactement ce format.';
                            html += '</div>';
                        }
                    } else {
                        // Patient non trouvé
                        html += '<div class="alert alert-danger" style="font-size: 13px; padding: 12px; margin-bottom: 10px;">';
                        html += '<i class="fa fa-times-circle"></i> <strong>Compte introuvable</strong><br>';
                        html += 'Aucun patient trouvé avec ce numéro.';
                        html += '</div>';

                        if (response.similar_numbers && response.similar_numbers.length > 0) {
                            html += '<div style="background: white; padding: 12px; border-radius: 8px; font-size: 13px;">';
                            html += '<strong>Numéros similaires trouvés :</strong><br>';
                            response.similar_numbers.forEach(function(sim) {
                                html += '• ' + sim.firstname + ' ' + sim.lastname + ' : <code>' + sim.phonenumber + '</code><br>';
                            });
                            html += '</div>';
                        } else {
                            html += '<div class="alert alert-info" style="font-size: 13px; padding: 12px;">';
                            html += '<i class="fa fa-user-plus"></i> Ce compte n\'existe pas encore.<br>';
                            html += 'Cliquez sur l\'onglet "Inscription" pour créer un compte.';
                            html += '</div>';
                        }
                    }
                } else {
                    html = '<div class="alert alert-danger" style="font-size: 13px; padding: 10px;"><i class="fa fa-exclamation-circle"></i> ' + response.message + '</div>';
                }

                $('#diagnostic-result').html(html);
                $btn.removeClass('loading').prop('disabled', false);
            }, 'json').fail(function() {
                $('#diagnostic-result').html('<div class="alert alert-danger" style="font-size: 13px; padding: 10px;"><i class="fa fa-exclamation-circle"></i> Erreur de connexion au serveur</div>');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Password strength checker
        $('#register-password').on('keyup', function() {
            const password = $(this).val();
            const strength = checkPasswordStrength(password);
            const $indicator = $('#password-strength');

            if (password.length === 0) {
                $indicator.text('').removeClass('weak medium strong');
            } else if (strength === 'weak') {
                $indicator.text('Mot de passe faible').removeClass('medium strong').addClass('weak');
            } else if (strength === 'medium') {
                $indicator.text('Mot de passe moyen').removeClass('weak strong').addClass('medium');
            } else {
                $indicator.text('Mot de passe fort').removeClass('weak medium').addClass('strong');
            }
        });

        function checkPasswordStrength(password) {
            if (password.length < 6) return 'weak';
            if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password)) return 'strong';
            return 'medium';
        }

        // Alert helpers
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type}">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i>
                    ${message}
                </div>
            `;
            $('#alert-container').html(alertHtml);
            $('.auth-content').animate({ scrollTop: 0 }, 300);
        }

        function clearAlert() {
            $('#alert-container').html('');
        }

        // Login form
        $('#login-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            // Get full phone number from intl-tel-input
            const fullNumber = getFullNumber('login-phone');
            const formData = $(this).serialize();
            const updatedFormData = formData.replace(/phone=[^&]*/, 'phone=' + encodeURIComponent(fullNumber));

            $.post('<?php echo site_url('dietetic/auth/login'); ?>', updatedFormData, function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    showAlert('danger', response.message);
                    $btn.removeClass('loading').prop('disabled', false);
                }
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Register form
        $('#register-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            const fullNumber = getFullNumber('register-phone');
            const formData = $(this).serialize();
            const updatedFormData = formData.replace(/phone=[^&]*/, 'phone=' + encodeURIComponent(fullNumber));

            $.post('<?php echo site_url('dietetic/auth/register'); ?>', updatedFormData, function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                    $btn.removeClass('loading').prop('disabled', false);
                }
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Request OTP
        $('#request-otp-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            const fullNumber = getFullNumber('otp-phone');
            const formData = $(this).serialize();
            const updatedFormData = formData.replace(/phone=[^&]*/, 'phone=' + encodeURIComponent(fullNumber));

            $.post('<?php echo site_url('dietetic/auth/request_otp'); ?>', updatedFormData, function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#otp-request-form').hide();
                    $('#otp-verify-form').show();
                    $('#verify-phone').val(fullNumber);
                } else {
                    showAlert('danger', response.message);
                }
                $btn.removeClass('loading').prop('disabled', false);
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Verify OTP
        $('#verify-otp-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            $.post('<?php echo site_url('dietetic/auth/verify_otp'); ?>', $(this).serialize(), function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1000);
                } else {
                    showAlert('danger', response.message);
                    $btn.removeClass('loading').prop('disabled', false);
                }
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Resend OTP
        $('#resend-otp').on('click', function(e) {
            e.preventDefault();
            clearAlert();

            const phone = $('#verify-phone').val();
            $.post('<?php echo site_url('dietetic/auth/request_otp'); ?>', {
                phone: phone,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            }, function(response) {
                if (response.success) {
                    showAlert('success', 'Code renvoyé avec succès');
                } else {
                    showAlert('danger', response.message);
                }
            }, 'json');
        });

        // Request password reset
        $('#request-reset-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            const fullNumber = getFullNumber('forgot-phone');
            const formData = $(this).serialize();
            const updatedFormData = formData.replace(/phone=[^&]*/, 'phone=' + encodeURIComponent(fullNumber));

            $.post('<?php echo site_url('dietetic/auth/forgot_password'); ?>', updatedFormData, function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    if (response.show_reset_form) {
                        $('#forgot-request-form').hide();
                        $('#forgot-reset-form').show();
                        $('#reset-phone').val(fullNumber);
                    }
                } else {
                    showAlert('danger', response.message);
                }
                $btn.removeClass('loading').prop('disabled', false);
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });

        // Reset password
        $('#reset-password-form').on('submit', function(e) {
            e.preventDefault();
            clearAlert();

            const $btn = $(this).find('button[type="submit"]');
            $btn.addClass('loading').prop('disabled', true);

            $.post('<?php echo site_url('dietetic/auth/reset_password'); ?>', $(this).serialize(), function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                    $btn.removeClass('loading').prop('disabled', false);
                }
            }, 'json').fail(function() {
                showAlert('danger', 'Erreur de connexion au serveur');
                $btn.removeClass('loading').prop('disabled', false);
            });
        });
    });
    </script>
</body>
</html>
