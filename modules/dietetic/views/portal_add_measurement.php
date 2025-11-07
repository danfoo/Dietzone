<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Ajouter une Mesure</title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
        }

        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            padding-bottom: 80px;
        }

        .portal-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .portal-header .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .portal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .portal-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .portal-logo img {
            max-height: 40px;
            max-width: 150px;
        }

        .portal-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-logo-text i {
            color: #01807B;
        }

        .portal-nav-desktop {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .portal-nav-desktop a {
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .portal-nav-desktop a:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .portal-nav-desktop a.active {
            background: #01807B;
            color: white;
        }

        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            padding: 8px 0 env(safe-area-inset-bottom, 8px) 0;
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 12px;
            min-width: 60px;
            position: relative;
        }

        .bottom-nav-item.active {
            color: #01807B;
        }

        .bottom-nav-item i {
            font-size: 24px;
        }

        .bottom-nav-item.active i {
            transform: scale(1.1);
        }

        .bottom-nav-item span {
            font-size: 11px;
            font-weight: 600;
        }

        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: #01807B;
            border-radius: 0 0 3px 3px;
        }

        .content-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        .page-header-mobile {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
        }

        .page-header-mobile h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-header-mobile p {
            margin: 0;
            font-size: 15px;
            opacity: 0.95;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title i {
            color: #01807B;
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

        .form-group label .required {
            color: #F3911D;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            border-color: #01807B;
            outline: none;
            box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #01807B;
            font-size: 16px;
        }

        .input-icon-wrapper .form-control {
            padding-left: 48px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .input-unit {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
            pointer-events: none;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 56px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(1, 128, 123, 0.4);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-cancel {
            flex: 1;
            background: white;
            color: #495057;
            padding: 16px 24px;
            border-radius: 10px;
            font-weight: 600;
            border: 2px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 56px;
        }

        .btn-cancel:hover {
            background: #f8f9fa;
            border-color: #01807B;
            color: #01807B;
            text-decoration: none;
        }

        .help-text {
            font-size: 13px;
            color: #6c757d;
            margin-top: 6px;
            font-style: italic;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
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

        .animate-in {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }

        @media (min-width: 769px) {
            body {
                padding-bottom: 0;
            }

            .bottom-nav {
                display: none !important;
            }

            .portal-nav-desktop {
                display: flex !important;
            }

            .form-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .bottom-nav {
                display: block;
            }

            .content-container {
                padding: 16px 12px 20px;
            }

            .page-header-mobile {
                padding: 20px 16px;
                border-radius: 12px;
                margin-bottom: 20px;
            }

            .page-header-mobile h1 {
                font-size: 20px;
            }

            .form-card {
                padding: 20px 16px;
            }

            .form-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 375px) {
            .portal-logo img {
                max-height: 32px;
                max-width: 120px;
            }

            .portal-logo-text {
                font-size: 16px;
            }

            .page-header-mobile h1 {
                font-size: 18px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="portal-header">
        <div class="container-fluid">
            <div class="portal-header-content">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="portal-logo">
                    <?php
                    $logo_path = get_option('company_logo_dark');
                    if (!$logo_path || !file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                        $logo_path = get_option('company_logo');
                    }

                    if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                    ?>
                        <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="<?php echo get_option('companyname'); ?>">
                    <?php } else { ?>
                        <div class="portal-logo-text">
                            <i class="fa fa-heartbeat"></i>
                            <span><?php echo get_option('companyname') ? get_option('companyname') : 'Dietetic'; ?></span>
                        </div>
                    <?php } ?>
                </a>

                <nav class="portal-nav-desktop">
                    <a href="<?php echo site_url('dietetic/portal'); ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
                        <i class="fa fa-user"></i> Profil
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="content-container">
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-plus-circle"></i> Ajouter une Mesure</h1>
            <p>Enregistrez vos progrès</p>
        </div>

        <?php if (isset($success) && $success) { ?>
            <div class="alert alert-success animate-in delay-1">
                <i class="fa fa-check-circle"></i> Votre mesure a été enregistrée avec succès !
            </div>
        <?php } ?>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger animate-in delay-1">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="<?php echo site_url('dietetic/portal/add_measurement'); ?>" method="post" class="animate-in delay-1">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

            <div class="form-card">
                <div class="form-section-title">
                    <i class="fa fa-calendar"></i> Date de Mesure
                </div>

                <div class="form-group">
                    <label for="measurement_date">
                        Date <span class="required">*</span>
                    </label>
                    <div class="input-icon-wrapper">
                        <i class="fa fa-calendar"></i>
                        <input type="date"
                               id="measurement_date"
                               name="measurement_date"
                               class="form-control"
                               value="<?php echo date('Y-m-d'); ?>"
                               required>
                    </div>
                </div>
            </div>

            <div class="form-card animate-in delay-2">
                <div class="form-section-title">
                    <i class="fa fa-balance-scale"></i> Poids et Composition
                </div>

                <div class="form-group">
                    <label for="weight">
                        Poids <span class="required">*</span>
                    </label>
                    <div class="input-icon-wrapper">
                        <i class="fa fa-balance-scale"></i>
                        <input type="number"
                               id="weight"
                               name="weight"
                               class="form-control"
                               step="0.1"
                               min="0"
                               placeholder="Ex: 70.5"
                               required
                               style="padding-right: 50px;">
                        <span class="input-unit">kg</span>
                    </div>
                    <div class="help-text">Pesez-vous de préférence le matin à jeun</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="body_fat">
                            Masse Grasse
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-percent"></i>
                            <input type="number"
                                   id="body_fat"
                                   name="body_fat"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   max="100"
                                   placeholder="Ex: 25.0"
                                   style="padding-right: 40px;">
                            <span class="input-unit">%</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="muscle_mass">
                            Masse Musculaire
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-heartbeat"></i>
                            <input type="number"
                                   id="muscle_mass"
                                   name="muscle_mass"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   max="100"
                                   placeholder="Ex: 35.0"
                                   style="padding-right: 40px;">
                            <span class="input-unit">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card animate-in delay-2">
                <div class="form-section-title">
                    <i class="fa fa-arrows-h"></i> Mensurations
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="waist">
                            Tour de Taille
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-arrows-h"></i>
                            <input type="number"
                                   id="waist"
                                   name="waist"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   placeholder="Ex: 75.0"
                                   style="padding-right: 45px;">
                            <span class="input-unit">cm</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hips">
                            Tour de Hanches
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-arrows-h"></i>
                            <input type="number"
                                   id="hips"
                                   name="hips"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   placeholder="Ex: 95.0"
                                   style="padding-right: 45px;">
                            <span class="input-unit">cm</span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="chest">
                            Tour de Poitrine
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-arrows-h"></i>
                            <input type="number"
                                   id="chest"
                                   name="chest"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   placeholder="Ex: 90.0"
                                   style="padding-right: 45px;">
                            <span class="input-unit">cm</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="arms">
                            Tour de Bras
                        </label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-arrows-h"></i>
                            <input type="number"
                                   id="arms"
                                   name="arms"
                                   class="form-control"
                                   step="0.1"
                                   min="0"
                                   placeholder="Ex: 30.0"
                                   style="padding-right: 45px;">
                            <span class="input-unit">cm</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="thighs">
                        Tour de Cuisses
                    </label>
                    <div class="input-icon-wrapper">
                        <i class="fa fa-arrows-h"></i>
                        <input type="number"
                               id="thighs"
                               name="thighs"
                               class="form-control"
                               step="0.1"
                               min="0"
                               placeholder="Ex: 55.0"
                               style="padding-right: 45px;">
                        <span class="input-unit">cm</span>
                    </div>
                </div>
            </div>

            <div class="form-card animate-in delay-2">
                <div class="form-section-title">
                    <i class="fa fa-comment"></i> Notes
                </div>

                <div class="form-group">
                    <label for="notes">
                        Remarques et Observations
                    </label>
                    <textarea id="notes"
                              name="notes"
                              class="form-control"
                              rows="4"
                              placeholder="Comment vous sentez-vous ? Avez-vous remarqué des changements ? Ajoutez vos observations..."></textarea>
                    <div class="help-text">Facultatif - Partagez vos ressentis et observations</div>
                </div>
            </div>

            <div class="form-actions animate-in delay-2">
                <button type="submit" class="btn-submit">
                    <i class="fa fa-check"></i> Enregistrer la Mesure
                </button>
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-cancel">
                    <i class="fa fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>

    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item">
                <i class="fa fa-cutlery"></i>
                <span>Repas</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="bottom-nav-item active">
                <i class="fa fa-plus-circle"></i>
                <span>Mesure</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="bottom-nav-item">
                <i class="fa fa-user-md"></i>
                <span>Contact</span>
            </a>
            <a href="<?php echo site_url('clients/profile'); ?>" class="bottom-nav-item">
                <i class="fa fa-user"></i>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        document.querySelectorAll('.form-card, .bottom-nav-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                if (this.classList.contains('form-card')) {
                    this.style.transform = 'scale(0.99)';
                }
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        if ('vibrate' in navigator) {
            document.querySelector('.btn-submit').addEventListener('click', function() {
                navigator.vibrate(10);
            });
        }

        // Auto-focus sur le premier champ en desktop
        if (window.innerWidth > 768) {
            document.getElementById('measurement_date').focus();
        }
    </script>
</body>
</html>
