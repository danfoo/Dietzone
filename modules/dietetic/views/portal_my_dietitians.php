<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $title; ?></title>
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
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
        }

        .page-header-modern h1 {
            color: #2c3e50;
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-modern h1 i {
            color: #667eea;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        /* Dietitian Profile Card */
        .dietitian-card {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .dietitian-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #ecf0f1;
        }

        .dietitian-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #667eea, #764ba2) border-box;
            flex-shrink: 0;
        }

        .dietitian-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dietitian-avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
        }

        .dietitian-info {
            flex: 1;
        }

        .dietitian-name {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 15px 0;
        }

        .dietitian-contact {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #7f8c8d;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .dietitian-contact i {
            color: #667eea;
            font-size: 18px;
        }

        /* Rating Display */
        .rating-display {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
        }

        .rating-number {
            font-size: 48px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .rating-stars {
            color: #f39c12;
            font-size: 24px;
        }

        .rating-stars-empty {
            color: #bdc3c7;
        }

        .rating-count {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-rate-modern {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }

        .btn-rate-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-contact-modern {
            flex: 1;
            min-width: 200px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .btn-contact-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-back-modern {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 15px 30px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 16px;
        }

        .btn-back-modern:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        /* My Rating Section */
        .my-rating-section {
            background: linear-gradient(135deg, #fff5e6 0%, #ffe0b2 100%);
            padding: 30px;
            border-radius: 16px;
            margin-top: 30px;
            border-left: 5px solid #f39c12;
        }

        .my-rating-section h4 {
            color: #2c3e50;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .my-rating-section h4 i {
            color: #f39c12;
        }

        .my-rating-display {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .my-rating-number {
            font-size: 36px;
            font-weight: 700;
            color: #f39c12;
        }

        .my-rating-comment {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-style: italic;
            line-height: 1.6;
            position: relative;
        }

        .my-rating-comment::before {
            content: '"';
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 48px;
            color: rgba(243, 156, 18, 0.2);
            font-family: Georgia, serif;
        }

        .my-rating-comment p {
            margin: 0;
            padding-left: 30px;
        }

        .rating-criteria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .criterion-item {
            background: white;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }

        .criterion-value {
            font-size: 28px;
            font-weight: 700;
            color: #f39c12;
            margin-bottom: 5px;
        }

        .criterion-label {
            font-size: 13px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rating-date {
            color: #95a5a6;
            font-size: 13px;
            margin-top: 20px;
            text-align: right;
        }

        /* Alert */
        .alert-modern {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 5px solid #667eea;
        }

        .alert-modern i {
            font-size: 32px;
            color: #667eea;
        }

        .alert-modern p {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
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
                margin-bottom: 20px;
            }

            .page-header-modern h1 {
                font-size: 22px;
            }

            .dietitian-card {
                padding: 25px 20px;
            }

            .dietitian-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .dietitian-avatar {
                width: 120px;
                height: 120px;
            }

            .dietitian-avatar-placeholder {
                font-size: 40px;
            }

            .dietitian-name {
                font-size: 26px;
            }

            .dietitian-contact {
                justify-content: center;
            }

            .rating-display {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
            }

            .rating-number {
                font-size: 40px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-rate-modern,
            .btn-contact-modern,
            .btn-back-modern {
                width: 100%;
            }

            .my-rating-section {
                padding: 20px 15px;
            }

            .rating-criteria-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .rating-criteria-grid {
                grid-template-columns: 1fr;
            }
        }

        .dietitian-profile {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .dietitian-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 2px solid #ecf0f1;
        }
        .dietitian-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #16a085;
            flex-shrink: 0;
        }
        .dietitian-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .dietitian-avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #16a085, #1abc9c);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
        }
        .dietitian-info h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
        }
        .dietitian-email {
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        .rating-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .rating-stars {
            color: #f39c12;
            font-size: 20px;
        }
        .rating-number {
            font-size: 28px;
            font-weight: 700;
            color: #f39c12;
        }
        .rating-count {
            color: #95a5a6;
            font-size: 13px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn-rate {
            background: #f39c12;
            color: white;
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-rate:hover {
            background: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
            color: white;
        }
        .btn-contact {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-contact:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
            color: white;
        }
        .my-rating-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 25px;
            border-left: 4px solid #f39c12;
        }
        .my-rating-section h4 {
            margin: 0 0 15px 0;
            color: #2c3e50;
        }
        .rating-criteria {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .criterion {
            font-size: 14px;
            color: #34495e;
        }
        .criterion strong {
            color: #f39c12;
            font-size: 16px;
        }
        .alert-info-custom {
            background: #e8f5e9;
            border-left: 4px solid #27ae60;
            padding: 15px;
            border-radius: 5px;
            color: #27ae60;
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
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="active">
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
            <h1><i class="fa fa-user-md"></i> Mon Diététicien</h1>
            <p>Consultez les informations de votre diététicien et laissez votre avis</p>
        </div>

        <!-- Dietitian Profile Card -->
        <div class="dietitian-card animate-in delay-1">
            <div class="dietitian-header">
                <div class="dietitian-avatar">
                    <?php if (!empty($dietitian->profile_image)) { ?>
                        <img src="<?php echo staff_profile_image_url($dietitian->staffid, 'small'); ?>" alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                    <?php } else { ?>
                        <div class="dietitian-avatar-placeholder">
                            <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="dietitian-info">
                    <h2 class="dietitian-name"><?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?></h2>
                    <div class="dietitian-contact">
                        <i class="fa fa-envelope"></i>
                        <span><?php echo htmlspecialchars($dietitian->email); ?></span>
                    </div>

                    <?php if ($dietitian_rating && $dietitian_rating->total_ratings > 0) { ?>
                        <div class="rating-display">
                            <div class="rating-number"><?php echo number_format($dietitian_rating->avg_overall, 1); ?></div>
                            <div>
                                <div class="rating-stars">
                                    <?php
                                    $full_stars = floor($dietitian_rating->avg_overall);
                                    $half_star = ($dietitian_rating->avg_overall - $full_stars) >= 0.5;

                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="fa fa-star"></i> ';
                                    }
                                    if ($half_star) {
                                        echo '<i class="fa fa-star-half-o"></i> ';
                                        $full_stars++;
                                    }
                                    for ($i = $full_stars; $i < 5; $i++) {
                                        echo '<i class="fa fa-star-o"></i> ';
                                    }
                                    ?>
                                </div>
                                <div class="rating-count"><?php echo $dietitian_rating->total_ratings; ?> avis</div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="rating-display">
                            <div class="rating-stars rating-stars-empty">
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                            </div>
                            <div class="rating-count">Aucun avis</div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php if ($can_rate) { ?>
                <div class="action-buttons">
                    <a href="<?php echo site_url('dietetic/portal/rate_dietitian'); ?>" class="btn-rate-modern">
                        <i class="fa fa-star"></i>
                        <?php echo $my_rating ? 'Modifier mon avis' : 'Noter mon diététicien'; ?>
                    </a>
                    <a href="mailto:<?php echo $dietitian->email; ?>" class="btn-contact-modern">
                        <i class="fa fa-envelope"></i>
                        Contacter par email
                    </a>
                </div>
            <?php } else { ?>
                <div class="alert-modern">
                    <i class="fa fa-info-circle"></i>
                    <p>Vous devez avoir au moins une consultation complétée pour noter votre diététicien.</p>
                </div>
            <?php } ?>

            <?php if ($my_rating) { ?>
                <div class="my-rating-section">
                    <h4><i class="fa fa-star"></i> Mon Avis</h4>

                    <div class="my-rating-display">
                        <div class="my-rating-number"><?php echo number_format($my_rating->overall_rating, 1); ?></div>
                        <div class="rating-stars">
                            <?php
                            $full_stars = floor($my_rating->overall_rating);
                            for ($i = 0; $i < $full_stars; $i++) {
                                echo '<i class="fa fa-star"></i> ';
                            }
                            for ($i = $full_stars; $i < 5; $i++) {
                                echo '<i class="fa fa-star-o rating-stars-empty"></i> ';
                            }
                            ?>
                        </div>
                    </div>

                    <?php if ($my_rating->comment) { ?>
                        <div class="my-rating-comment">
                            <p><?php echo nl2br(htmlspecialchars($my_rating->comment)); ?></p>
                        </div>
                    <?php } ?>

                    <div class="rating-criteria-grid">
                        <div class="criterion-item">
                            <div class="criterion-value"><?php echo $my_rating->professionalism_rating; ?>/5</div>
                            <div class="criterion-label">Professionnalisme</div>
                        </div>
                        <div class="criterion-item">
                            <div class="criterion-value"><?php echo $my_rating->listening_rating; ?>/5</div>
                            <div class="criterion-label">Écoute</div>
                        </div>
                        <div class="criterion-item">
                            <div class="criterion-value"><?php echo $my_rating->advice_rating; ?>/5</div>
                            <div class="criterion-label">Conseils</div>
                        </div>
                        <div class="criterion-item">
                            <div class="criterion-value"><?php echo $my_rating->results_rating; ?>/5</div>
                            <div class="criterion-label">Résultats</div>
                        </div>
                        <div class="criterion-item">
                            <div class="criterion-value"><?php echo $my_rating->availability_rating; ?>/5</div>
                            <div class="criterion-label">Disponibilité</div>
                        </div>
                    </div>

                    <div class="rating-date">
                        <i class="fa fa-clock-o"></i> Publié le <?php echo date('d/m/Y', strtotime($my_rating->created_at)); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Back Button -->
        <div style="text-align: center;" class="animate-in delay-2">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back-modern">
                <i class="fa fa-arrow-left"></i> Retour au tableau de bord
            </a>
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
