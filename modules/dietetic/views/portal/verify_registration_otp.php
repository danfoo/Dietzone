<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Validation de votre inscription - DietZone</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #01807B 0%, #01605B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .otp-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 40px 30px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }

        .otp-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #01807B 0%, #01605B 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .otp-icon i {
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #212529;
            margin-bottom: 12px;
        }

        .otp-subtitle {
            color: #6c757d;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .phone-display {
            color: #01807B;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 32px;
        }

        /* OTP Input Fields */
        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            transition: all 0.3s;
            outline: none;
        }

        .otp-input:focus {
            border-color: #01807B;
            box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
        }

        .otp-input.filled {
            border-color: #01807B;
            background-color: #f0f9f9;
        }

        .otp-input.error {
            border-color: #dc3545;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Submit Button */
        .btn-validate {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #01807B 0%, #01605B 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 16px;
        }

        .btn-validate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 128, 123, 0.3);
        }

        .btn-validate:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* Resend Link */
        .resend-container {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #dee2e6;
        }

        .resend-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .btn-resend {
            background: none;
            border: none;
            color: #01807B;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 15px;
            padding: 8px 16px;
        }

        .btn-resend:hover {
            color: #01605B;
        }

        .btn-resend:disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        /* Timer */
        .timer {
            color: #6c757d;
            font-size: 13px;
            margin-top: 8px;
        }

        .timer.warning {
            color: #ffc107;
            font-weight: 600;
        }

        .timer.danger {
            color: #dc3545;
            font-weight: 600;
        }

        /* Alert Messages */
        .alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Back Link */
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #01807B;
        }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-validate.loading .spinner {
            display: block;
        }

        .btn-validate.loading .btn-text {
            display: none;
        }

        @media (max-width: 576px) {
            .otp-container {
                padding: 30px 20px;
            }

            .otp-input {
                width: 45px;
                height: 55px;
                font-size: 22px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <!-- Icon -->
        <div class="otp-icon">
            <i class="fas fa-mobile-alt"></i>
        </div>

        <!-- Title -->
        <h1>Vérification de votre numéro</h1>
        <p class="otp-subtitle">Nous avons envoyé un code à 6 chiffres au</p>
        <p class="phone-display"><i class="fas fa-phone"></i> <?php echo isset($phone_masked) ? $phone_masked : ''; ?></p>

        <!-- Alerts (Success/Error messages) -->
        <?php echo $this->session->flashdata('message'); ?>

        <!-- OTP Form -->
        <form method="POST" action="<?php echo site_url('dietetic/portal/verify_registration_otp'); ?>" id="otpForm">
            <div class="otp-inputs" id="otpInputs">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="0">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="1">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="2">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="3">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="4">
                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" data-index="5">
            </div>

            <!-- Hidden input for the complete OTP code -->
            <input type="hidden" name="otp_code" id="otpCode">

            <!-- Submit Button -->
            <button type="submit" class="btn-validate" id="submitBtn" disabled>
                <span class="btn-text">Valider mon inscription</span>
                <div class="spinner"></div>
            </button>

            <!-- Timer -->
            <div class="timer" id="timer">
                <i class="far fa-clock"></i> Code valide pendant <span id="countdown">5:00</span>
            </div>
        </form>

        <!-- Resend Section -->
        <div class="resend-container">
            <p class="resend-text">Vous n'avez pas reçu le code ?</p>
            <form method="POST" action="<?php echo site_url('dietetic/portal/resend_registration_otp'); ?>" id="resendForm">
                <button type="submit" class="btn-resend" id="resendBtn">
                    <i class="fas fa-redo-alt"></i> Renvoyer le code
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>
    </div>

    <!-- Scripts -->
    <script>
        // OTP Input Management
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpCode = document.getElementById('otpCode');
        const submitBtn = document.getElementById('submitBtn');
        const otpForm = document.getElementById('otpForm');

        // Focus on first input on load
        window.addEventListener('load', () => {
            otpInputs[0].focus();
        });

        // Handle input in OTP fields
        otpInputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', (e) => {
                const value = e.target.value;

                // Only allow digits
                if (!/^\d*$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Add filled class
                if (value) {
                    e.target.classList.add('filled');
                } else {
                    e.target.classList.remove('filled');
                }

                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }

                // Update hidden input and enable/disable submit button
                updateOTPCode();
            });

            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].classList.remove('filled');
                    updateOTPCode();
                }
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();

                if (/^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((digit, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = digit;
                            otpInputs[i].classList.add('filled');
                        }
                    });
                    otpInputs[5].focus();
                    updateOTPCode();
                }
            });

            // Handle focus
            input.addEventListener('focus', (e) => {
                e.target.select();
            });
        });

        // Update hidden OTP code field
        function updateOTPCode() {
            const code = Array.from(otpInputs).map(input => input.value).join('');
            otpCode.value = code;

            // Enable submit button only if all 6 digits are entered
            submitBtn.disabled = code.length !== 6;
        }

        // Form submission with loading state
        otpForm.addEventListener('submit', (e) => {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Countdown Timer (5 minutes)
        let timeLeft = 300; // 5 minutes in seconds
        const countdownElement = document.getElementById('countdown');
        const timerElement = document.getElementById('timer');

        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Change color when time is running out
            if (timeLeft <= 60) {
                timerElement.classList.add('danger');
            } else if (timeLeft <= 120) {
                timerElement.classList.add('warning');
            }

            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            } else {
                timerElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Code expiré. Demandez un nouveau code.';
                timerElement.classList.add('danger');
                submitBtn.disabled = true;
                otpInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('error');
                });
            }
        }

        // Start countdown
        updateCountdown();

        // Resend button cooldown (prevent spam)
        const resendBtn = document.getElementById('resendBtn');
        const resendForm = document.getElementById('resendForm');
        let resendCooldown = 0;

        resendForm.addEventListener('submit', (e) => {
            if (resendCooldown > 0) {
                e.preventDefault();
                return;
            }

            // Start cooldown (30 seconds)
            resendCooldown = 30;
            resendBtn.disabled = true;
            const originalText = resendBtn.innerHTML;

            const cooldownInterval = setInterval(() => {
                resendBtn.innerHTML = `<i class="fas fa-clock"></i> Attendre ${resendCooldown}s`;
                resendCooldown--;

                if (resendCooldown < 0) {
                    clearInterval(cooldownInterval);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = originalText;

                    // Reset timer
                    timeLeft = 300;
                    timerElement.classList.remove('warning', 'danger');
                }
            }, 1000);
        });

        // Auto-submit when all digits are entered (optional)
        function autoSubmit() {
            const code = otpCode.value;
            if (code.length === 6) {
                setTimeout(() => {
                    if (confirm('Voulez-vous valider le code ' + code + ' ?')) {
                        otpForm.submit();
                    }
                }, 500);
            }
        }

        // Uncomment to enable auto-submit:
        // otpInputs[5].addEventListener('input', autoSubmit);
    </script>
</body>
</html>
