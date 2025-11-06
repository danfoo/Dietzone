<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title : 'Ajouter une Mesure'; ?></title>
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
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* Header Uniforme Perfex */
        .portal-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
            max-height: 50px;
            max-width: 200px;
        }

        .portal-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .portal-logo-text i {
            color: #667eea;
        }

        .portal-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .portal-nav a {
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-nav a:hover {
            background: #f8f9fa;
            color: #667eea;
        }

        .portal-nav a.active {
            background: #667eea;
            color: white;
        }

        .portal-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #495057;
            cursor: pointer;
            padding: 5px 10px;
        }

        /* Container */
        .content-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 15px;
        }

        /* Page Header */
        .page-header-modern {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #fa709a;
        }

        .page-header-modern h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-modern h1 i {
            color: #fa709a;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 0;
            font-size: 15px;
        }

        /* Form Container */
        .form-modern {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        /* Alert Messages */
        .alert-modern {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: none;
        }

        .alert-modern.alert-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .alert-modern.alert-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .alert-modern i {
            font-size: 20px;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e9ecef;
        }

        .form-section-title i {
            font-size: 20px;
            color: #667eea;
        }

        .form-section-title h3 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            display: block;
        }

        .form-group label .required {
            color: #f5576c;
            margin-left: 3px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            height: auto;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Input Icons */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 16px;
        }

        .input-icon-wrapper .form-control {
            padding-left: 45px;
        }

        /* Buttons */
        .btn-modern {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-modern.btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-modern.btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-modern.btn-default {
            background: white;
            color: #495057;
            border-color: #dee2e6;
        }

        .btn-modern.btn-default:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            text-decoration: none;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            margin-top: 30px;
        }

        /* Animations */
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

        /* Responsive */
        @media (max-width: 768px) {
            .portal-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                gap: 5px;
                z-index: 1000;
            }

            .portal-nav.show {
                display: flex;
            }

            .portal-nav a {
                width: 100%;
                justify-content: flex-start;
            }

            .portal-menu-toggle {
                display: block;
            }

            .portal-header-content {
                position: relative;
            }

            .content-container {
                padding: 20px 10px;
            }

            .page-header-modern {
                padding: 20px 15px;
            }

            .page-header-modern h1 {
                font-size: 22px;
            }

            .form-modern {
                padding: 20px 15px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }

            .portal-logo img {
                max-height: 40px;
            }

            .portal-logo-text {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .form-section-title h3 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Uniforme -->
    <div class="portal-header">
        <div class="container-fluid">
            <div class="portal-header-content">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="portal-logo">
                    <?php
                    // Essayer d'abord le logo sombre, puis le logo normal
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
                            <?php echo get_option('companyname') ? get_option('companyname') : 'Programme Diététique'; ?>
                        </div>
                    <?php } ?>
                </a>

                <button class="portal-menu-toggle" onclick="toggleMenu()">
                    <i class="fa fa-bars"></i>
                </button>

                <nav class="portal-nav" id="portalNav">
                    <a href="<?php echo site_url('dietetic/portal'); ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Mon Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
                        <i class="fa fa-user"></i> Profil
                    </a>
                    <a href="<?php echo site_url('authentication/logout'); ?>">
                        <i class="fa fa-sign-out"></i> Déconnexion
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-modern animate-in">
            <h1><i class="fa fa-plus-circle"></i> Ajouter une Mesure</h1>
            <p>Enregistrez vos nouvelles mensurations pour suivre votre progression</p>
        </div>

        <!-- Alerts -->
        <?php if (isset($error)) { ?>
            <div class="alert-modern alert-danger animate-in delay-1">
                <i class="fa fa-exclamation-triangle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php } ?>

        <?php if (isset($success)) { ?>
            <div class="alert-modern alert-success animate-in delay-1">
                <i class="fa fa-check-circle"></i>
                <span><?php echo $success; ?></span>
            </div>
        <?php } ?>

        <!-- Form -->
        <div class="form-modern animate-in delay-1">
            <form method="POST" action="<?php echo site_url('dietetic/portal/add_measurement'); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                <!-- Date Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-calendar"></i>
                        <h3>Date de la Mesure</h3>
                    </div>
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fa fa-calendar-o"></i>
                            <input type="date" name="measurement_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Weight & Body Composition -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-balance-scale"></i>
                        <h3>Poids et Composition Corporelle</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Poids (kg) <span class="required">*</span></label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-balance-scale"></i>
                                    <input type="number" name="weight" class="form-control" step="0.1" min="0" placeholder="Ex: 70.5" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Masse Grasse (%)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-pie-chart"></i>
                                    <input type="number" name="body_fat" class="form-control" step="0.1" min="0" max="100" placeholder="Ex: 25.0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Masse Musculaire (%)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-heartbeat"></i>
                                    <input type="number" name="muscle_mass" class="form-control" step="0.1" min="0" max="100" placeholder="Ex: 35.0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body Measurements -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-arrows-h"></i>
                        <h3>Mensurations Corporelles</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tour de Taille (cm)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-arrows-h"></i>
                                    <input type="number" name="waist" class="form-control" step="0.1" min="0" placeholder="Ex: 75.0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tour de Hanches (cm)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-arrows-h"></i>
                                    <input type="number" name="hips" class="form-control" step="0.1" min="0" placeholder="Ex: 95.0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tour de Poitrine (cm)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-arrows-h"></i>
                                    <input type="number" name="chest" class="form-control" step="0.1" min="0" placeholder="Ex: 90.0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tour de Bras (cm)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-arrows-h"></i>
                                    <input type="number" name="arms" class="form-control" step="0.1" min="0" placeholder="Ex: 30.0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tour de Cuisses (cm)</label>
                                <div class="input-icon-wrapper">
                                    <i class="fa fa-arrows-h"></i>
                                    <input type="number" name="thighs" class="form-control" step="0.1" min="0" placeholder="Ex: 55.0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa fa-sticky-note-o"></i>
                        <h3>Notes et Observations</h3>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Ajoutez vos remarques, observations ou ressentis..."></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-modern btn-default">
                        <i class="fa fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fa fa-save"></i> Enregistrer la Mesure
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function toggleMenu() {
            var nav = document.getElementById('portalNav');
            nav.classList.toggle('show');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            var nav = document.getElementById('portalNav');
            var toggle = document.querySelector('.portal-menu-toggle');
            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('show');
            }
        });
    </script>
</body>
</html>
