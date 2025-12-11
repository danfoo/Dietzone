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
            padding: 60px 20px 40px;
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
                <!-- Alerts Perfex -->
                <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                <?php if ($this->session->flashdata('message-success')): ?>
                    <div class="alert alert-success">
                        <?php echo $this->session->flashdata('message-success'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('message-danger')): ?>
                    <div class="alert alert-danger">
                        <?php echo $this->session->flashdata('message-danger'); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Tab -->
                <div class="tab-pane active" id="login-tab">
                    <form method="POST" action="<?php echo site_url('dietetic/portal'); ?>" id="login-form">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                        <input type="hidden" name="phone" id="login-phone-hidden">

                        <div class="form-group">
                            <label><i class="fa fa-user"></i> Email ou Téléphone</label>
                            <input type="text" id="login-phone" class="form-control" placeholder="email@exemple.com ou +221771234567" required>
                            <small class="form-text text-muted">Vous pouvez utiliser votre email ou votre numéro de téléphone</small>
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
                        </button>
                    </form>

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="#" class="link-primary" id="link-forgot-password">
                            <i class="fa fa-question-circle"></i> Mot de passe oublié ?
                        </a>
                    </div>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane" id="register-tab">
                    <form method="POST" action="<?php echo site_url('dietetic/portal/register'); ?>" id="register-form">
                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                        <input type="hidden" name="phone_full" id="register-phone-hidden">

                        <div class="form-group">
                            <label><i class="fa fa-user"></i> Prénom <span style="color:red;">*</span></label>
                            <input type="text" name="firstname" class="form-control" placeholder="Votre prénom" required minlength="2">
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-user"></i> Nom <span style="color:red;">*</span></label>
                            <input type="text" name="lastname" class="form-control" placeholder="Votre nom" required minlength="2">
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-envelope"></i> Email <span style="color:red;">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="exemple@email.com" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-phone"></i> Numéro de téléphone <span style="color:red;">*</span></label>
                            <input type="tel" id="register-phone" class="form-control" placeholder="+221 77 123 45 67" required>
                            <small class="form-text text-muted">Format international requis (ex: +221771234567)</small>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-lock"></i> Mot de passe <span style="color:red;">*</span></label>
                            <input type="password" name="password" id="register-password" class="form-control" placeholder="Au moins 6 caractères" required minlength="6">
                            <div class="password-strength" id="password-strength"></div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-lock"></i> Confirmer le mot de passe <span style="color:red;">*</span></label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="Retapez votre mot de passe" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-user-plus"></i> Créer mon compte
                        </button>
                    </form>

                    <div style="text-align: center; margin-top: 20px;">
                        <p class="text-muted-sm">
                            Déjà un compte ?
                            <a href="#" class="link-primary auth-tab-link" data-tab="login">Se connecter</a>
                        </p>
                    </div>
                </div>

                <!-- Forgot Password Tab -->
                <div class="tab-pane" id="forgot-tab">
                    <?php
                    // Vérifier si on doit afficher le formulaire de reset ou de demande
                    $show_reset_form = $this->session->userdata('forgot_password_phone');
                    ?>

                    <?php if (!$show_reset_form): ?>
                    <!-- Étape 1: Demander le code -->
                    <div id="forgot-request-form">
                        <p class="text-muted-sm" style="margin-bottom: 20px;">
                            Entrez votre numéro de téléphone pour recevoir un code de réinitialisation par SMS, Email et WhatsApp.
                        </p>

                        <form method="POST" action="<?php echo site_url('dietetic/portal/forgot_password'); ?>" id="request-reset-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" name="phone" id="forgot-phone-hidden">

                            <div class="form-group">
                                <label><i class="fa fa-phone"></i> Numéro de téléphone <span style="color:red;">*</span></label>
                                <input type="tel" id="forgot-phone" class="form-control" placeholder="+221 77 123 45 67" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane"></i> Envoyer le code
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    <!-- Étape 2: Réinitialiser le mot de passe -->
                    <div id="forgot-reset-form">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Un code de réinitialisation a été envoyé par SMS, Email et WhatsApp au numéro <?php echo $show_reset_form; ?>.
                        </div>

                        <form method="POST" action="<?php echo site_url('dietetic/portal/reset_password'); ?>" id="reset-password-form">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($show_reset_form); ?>">

                            <div class="form-group">
                                <label><i class="fa fa-key"></i> Code de réinitialisation <span style="color:red;">*</span></label>
                                <input type="text" name="code" class="form-control" maxlength="6" placeholder="123456" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-lock"></i> Nouveau mot de passe <span style="color:red;">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Au moins 6 caractères" required minlength="6">
                            </div>

                            <div class="form-group">
                                <label><i class="fa fa-lock"></i> Confirmer le mot de passe <span style="color:red;">*</span></label>
                                <input type="password" name="password_confirm" class="form-control" placeholder="Retapez votre mot de passe" required minlength="6">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Réinitialiser
                            </button>

                            <?php if ($show_reset_form): ?>
                            <div style="text-align: center; margin-top: 15px;">
                                <a href="<?php echo site_url('dietetic/portal/cancel_reset'); ?>" class="link-secondary">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div style="text-align: center; margin-top: 20px;">
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
        const phoneInputs = ['login-phone', 'register-phone', 'forgot-phone'];
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

        // Show forgot password tab
        $('#link-forgot-password').on('click', function(e) {
            e.preventDefault();
            switchTab('forgot');
        });

        // Activer automatiquement l'onglet "forgot" si URL contient #forgot
        if (window.location.hash === '#forgot') {
            switchTab('forgot');
        }

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

        // Login form - PHP redirect au lieu d'AJAX
        $('#login-form').on('submit', function(e) {
            // Récupérer la valeur saisie
            const inputValue = $('#login-phone').val().trim();

            // Vérifier si c'est un email (contient @)
            const isEmail = inputValue.includes('@');

            if (isEmail) {
                // Si c'est un email, passer tel quel
                $('#login-phone-hidden').val(inputValue);
            } else {
                // Si c'est un téléphone, utiliser intl-tel-input pour le formater
                const fullNumber = getFullNumber('login-phone');
                $('#login-phone-hidden').val(fullNumber || inputValue);
            }

            // Le formulaire se soumet normalement (pas de e.preventDefault)
        });

        // Register form - PHP POST (pas AJAX)
        $('#register-form').on('submit', function(e) {
            // Récupérer le numéro formaté et le mettre dans le champ caché
            const fullNumber = getFullNumber('register-phone');
            $('#register-phone-hidden').val(fullNumber);

            // Le formulaire se soumet normalement (pas de e.preventDefault)
        });

        // Request password reset - PHP POST (pas AJAX)
        $('#request-reset-form').on('submit', function(e) {
            // Formater le numéro avec intl-tel-input
            const fullNumber = getFullNumber('forgot-phone');
            $('#forgot-phone-hidden').val(fullNumber);

            // Le formulaire se soumet normalement
        });

        // Reset password - PHP POST (pas AJAX)
        $('#reset-password-form').on('submit', function(e) {
            // Le formulaire se soumet normalement
        });
    });
    </script>
</body>
</html>
