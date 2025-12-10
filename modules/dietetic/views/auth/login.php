<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - DietZone</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <style>
        :root {
            --primary-color: #01807B;
            --secondary-color: #F3911D;
        }

        body {
            background: linear-gradient(135deg, #01807B 0%, #016663 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            max-width: 500px;
            width: 100%;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            max-height: 80px;
            width: auto;
            margin-bottom: 20px;
        }

        .logo-section h1 {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .auth-tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .auth-tab {
            flex: 1;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .auth-tab:hover {
            background: rgba(1, 128, 123, 0.05);
            color: #01807B;
        }

        .auth-tab.active {
            background: white;
            color: #01807B;
            border-bottom-color: #01807B;
        }

        .auth-content {
            padding: 40px 30px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            height: 45px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #01807B;
            box-shadow: 0 0 0 0.2rem rgba(1, 128, 123, 0.1);
        }

        textarea.form-control {
            height: auto;
            min-height: 100px;
        }

        .iti {
            width: 100%;
        }

        .iti__flag-container {
            border-right: 2px solid #e9ecef;
        }

        .btn-primary {
            background: #01807B;
            border: none;
            height: 50px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #016663;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(1, 128, 123, 0.3);
        }

        .btn-secondary {
            background: #F3911D;
            border: none;
            height: 50px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #d47b0f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 145, 29, 0.3);
        }

        .link-primary {
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .link-primary:hover {
            color: #016663;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
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
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #6c757d;
            font-size: 14px;
        }

        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: 700;
        }

        .resend-link {
            color: #01807B;
            cursor: pointer;
            font-weight: 600;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
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

        @media (max-width: 576px) {
            .auth-content {
                padding: 30px 20px;
            }

            .auth-tab {
                padding: 15px 10px;
                font-size: 14px;
            }
        }

        .loading-spinner {
            display: none;
            margin-left: 10px;
        }

        .btn-primary.loading .loading-spinner,
        .btn-secondary.loading .loading-spinner {
            display: inline-block;
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
            <p>Votre accompagnement diététique personnalisé</p>
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
                    <h3 style="margin-top: 0; margin-bottom: 25px; color: #2c3e50;">
                        Connectez-vous
                    </h3>

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

                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
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
                    </div>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane" id="register-tab">
                    <h3 style="margin-top: 0; margin-bottom: 25px; color: #2c3e50;">
                        Créer un compte
                    </h3>

                    <form id="register-form">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fa fa-user"></i> Prénom</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><i class="fa fa-user"></i> Nom</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                            </div>
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
                        <p style="color: #6c757d; font-size: 14px;">
                            Déjà un compte ?
                            <a href="#" class="link-primary auth-tab-link" data-tab="login">Se connecter</a>
                        </p>
                    </div>
                </div>

                <!-- OTP Tab -->
                <div class="tab-pane" id="otp-tab">
                    <h3 style="margin-top: 0; margin-bottom: 25px; color: #2c3e50;">
                        Connexion par SMS
                    </h3>

                    <div id="otp-request-form">
                        <p style="color: #6c757d; margin-bottom: 20px;">
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
                            Un code de vérification a été envoyé par SMS à votre numéro de téléphone.
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

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" class="link-primary auth-tab-link" data-tab="login">
                            <i class="fa fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <!-- Forgot Password Tab -->
                <div class="tab-pane" id="forgot-tab">
                    <h3 style="margin-top: 0; margin-bottom: 25px; color: #2c3e50;">
                        Mot de passe oublié
                    </h3>

                    <div id="forgot-request-form">
                        <p style="color: #6c757d; margin-bottom: 20px;">
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

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" class="link-primary auth-tab-link" data-tab="login">
                            <i class="fa fa-arrow-left"></i> Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; color: white;">
            <p style="margin: 0; opacity: 0.9;">
                <i class="fa fa-question-circle"></i> Besoin d'aide ?
                <a href="mailto:support@dietzone.sn" style="color: #F3911D; text-decoration: none; font-weight: 600;">
                    support@dietzone.sn
                </a>
            </p>
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
            $('html, body').animate({ scrollTop: 0 }, 300);
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
