<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur DietZone - Votre Accompagnement Diététique</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --primary-color: #01807B;
            --secondary-color: #F3911D;
        }

        body {
            background: linear-gradient(135deg, #01807B 0%, #016663 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .navbar-custom {
            background: rgba(0, 0, 0, 0.2);
            border: none;
            border-radius: 0;
            margin-bottom: 0;
            backdrop-filter: blur(10px);
        }

        .navbar-custom .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 15px 15px;
            display: flex;
            align-items: center;
        }

        .navbar-custom .navbar-brand img {
            max-height: 40px;
            width: auto;
            margin-right: 10px;
        }

        .navbar-custom .navbar-nav>li>a {
            color: white;
            padding: 20px 15px;
            transition: all 0.3s;
        }

        .navbar-custom .navbar-nav>li>a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.95) 0%, rgba(1, 102, 99, 0.95) 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section .lead {
            font-size: 22px;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .hero-icon {
            font-size: 100px;
            color: #F3911D;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .content-section {
            background: #f8f9fa;
            padding: 60px 0;
        }

        .welcome-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .welcome-card h2 {
            color: #01807B;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            text-align: center;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .info-card i {
            font-size: 50px;
            color: #F3911D;
            margin-bottom: 20px;
        }

        .info-card h3 {
            color: #01807B;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-card p {
            color: #666;
            line-height: 1.6;
            margin: 0;
        }

        .timeline {
            position: relative;
            padding: 40px 0;
            margin-top: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #01807B, #F3911D);
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
        }

        .timeline-item:nth-child(odd) {
            flex-direction: row;
        }

        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .timeline-content {
            background: white;
            border-radius: 12px;
            padding: 25px;
            width: 45%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .timeline-item:nth-child(odd) .timeline-content {
            margin-right: auto;
            margin-left: 0;
        }

        .timeline-item:nth-child(even) .timeline-content {
            margin-left: auto;
            margin-right: 0;
        }

        .timeline-number {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: #F3911D;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 0 0 5px #f8f9fa;
            z-index: 2;
        }

        .timeline-content h4 {
            color: #01807B;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .timeline-content p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }

        .cta-section {
            background: linear-gradient(135deg, #01807B 0%, #016663 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .btn-custom {
            background: #F3911D;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(243, 145, 29, 0.3);
        }

        .btn-custom:hover {
            background: #d47b0f;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(243, 145, 29, 0.4);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #F3911D;
        }

        .feature-item i {
            font-size: 40px;
            color: #F3911D;
            margin-bottom: 15px;
        }

        .feature-item h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }

            .timeline::before {
                left: 30px;
            }

            .timeline-item {
                flex-direction: row !important;
            }

            .timeline-content {
                width: calc(100% - 80px);
                margin-left: 80px !important;
                margin-right: 0 !important;
            }

            .timeline-number {
                left: 30px;
                transform: none;
            }
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: none;
            border-left: 5px solid #01807B;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .alert-info-custom i {
            color: #01807B;
            font-size: 24px;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background: white;"></span>
                    <span class="icon-bar" style="background: white;"></span>
                    <span class="icon-bar" style="background: white;"></span>
                </button>
                <a class="navbar-brand" href="#">
                    <?php
                    $company_logo = get_option('company_logo');
                    if (!empty($company_logo)) { ?>
                        <img src="<?php echo base_url('uploads/company/' . $company_logo); ?>" alt="DietZone Logo">
                    <?php } else { ?>
                        <i class="fa fa-heartbeat"></i>
                    <?php } ?>
                    <span>DietZone</span>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Mon Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <i class="fa fa-check-circle hero-icon"></i>
            <h1>🎉 Bienvenue sur DietZone !</h1>
            <p class="lead">Votre inscription a été validée avec succès. Votre voyage vers une meilleure santé commence ici.</p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <div class="container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h2><i class="fa fa-info-circle" style="color: #F3911D; margin-right: 10px;"></i> Étape suivante</h2>
                <p style="font-size: 18px; color: #666; text-align: center; line-height: 1.8;">
                    Votre compte a été créé avec succès ! Un <strong>diététicien professionnel</strong> va bientôt prendre contact avec vous
                    pour configurer votre programme personnalisé et débuter votre accompagnement.
                </p>

                <div class="alert-info-custom" style="margin-top: 30px;">
                    <i class="fa fa-clock-o pull-left"></i>
                    <strong>Temps d'attente estimé :</strong> 24 à 48 heures<br>
                    Vous recevrez une notification par SMS et Email dès que votre programme sera prêt.
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-cards">
                <div class="info-card">
                    <i class="fa fa-user-md"></i>
                    <h3>Diététicien Dédié</h3>
                    <p>Un professionnel de la nutrition va être assigné à votre dossier pour un suivi personnalisé et adapté à vos besoins.</p>
                </div>

                <div class="info-card">
                    <i class="fa fa-clipboard"></i>
                    <h3>Programme Sur-Mesure</h3>
                    <p>Recevez un plan alimentaire personnalisé basé sur vos objectifs, vos préférences et votre état de santé.</p>
                </div>

                <div class="info-card">
                    <i class="fa fa-line-chart"></i>
                    <h3>Suivi en Temps Réel</h3>
                    <p>Suivez vos progrès quotidiennement avec des graphiques et des statistiques détaillées de votre évolution.</p>
                </div>
            </div>

            <!-- Timeline -->
            <div style="background: white; border-radius: 15px; padding: 50px 30px; margin-top: 50px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                <h2 style="text-align: center; color: #01807B; font-size: 32px; font-weight: 700; margin-bottom: 20px;">
                    📋 Les Prochaines Étapes
                </h2>
                <p style="text-align: center; color: #666; font-size: 16px; margin-bottom: 50px;">
                    Voici ce qui va se passer dans les prochains jours
                </p>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-number">1</div>
                        <div class="timeline-content">
                            <h4><i class="fa fa-envelope" style="color: #F3911D; margin-right: 8px;"></i> Validation de votre inscription</h4>
                            <p>Un email de confirmation vous a été envoyé. Votre compte est maintenant actif et prêt à être utilisé.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">2</div>
                        <div class="timeline-content">
                            <h4><i class="fa fa-user-plus" style="color: #F3911D; margin-right: 8px;"></i> Attribution d'un diététicien</h4>
                            <p>Notre équipe va vous assigner un diététicien qualifié en fonction de vos besoins et de votre profil.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">3</div>
                        <div class="timeline-content">
                            <h4><i class="fa fa-phone" style="color: #F3911D; margin-right: 8px;"></i> Premier contact</h4>
                            <p>Votre diététicien vous contactera par téléphone ou email pour planifier votre première consultation.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">4</div>
                        <div class="timeline-content">
                            <h4><i class="fa fa-calendar-check-o" style="color: #F3911D; margin-right: 8px;"></i> Consultation initiale</h4>
                            <p>Première rencontre pour définir vos objectifs, évaluer vos habitudes et établir votre programme personnalisé.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">5</div>
                        <div class="timeline-content">
                            <h4><i class="fa fa-rocket" style="color: #F3911D; margin-right: 8px;"></i> Début du programme</h4>
                            <p>Accédez à toutes les fonctionnalités : plan alimentaire, suivi des repas, consultations, messagerie et bien plus !</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Preview -->
    <div class="cta-section">
        <div class="container">
            <h2>🌟 Ce qui vous attend sur DietZone</h2>
            <p>Découvrez toutes les fonctionnalités qui seront disponibles une fois votre programme activé</p>

            <div class="features-grid">
                <div class="feature-item">
                    <i class="fa fa-cutlery"></i>
                    <h4>Plans Alimentaires</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-camera"></i>
                    <h4>Journal Alimentaire</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-balance-scale"></i>
                    <h4>Suivi du Poids</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-tint"></i>
                    <h4>Hydratation</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-video-camera"></i>
                    <h4>Consultations Vidéo</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-comments"></i>
                    <h4>Messagerie Directe</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-trophy"></i>
                    <h4>Gamification</h4>
                </div>
                <div class="feature-item">
                    <i class="fa fa-bell"></i>
                    <h4>Notifications SMS</h4>
                </div>
            </div>

            <div style="margin-top: 50px;">
                <a href="<?php echo site_url('clients/profile'); ?>" class="btn btn-custom">
                    <i class="fa fa-user"></i> Compléter Mon Profil
                </a>
            </div>

            <p style="margin-top: 30px; opacity: 0.9;">
                <i class="fa fa-question-circle"></i> Des questions ? Consultez notre
                <a href="#" style="color: #F3911D; text-decoration: underline;">FAQ</a> ou contactez-nous à
                <a href="mailto:support@dietzone.sn" style="color: #F3911D; text-decoration: underline; font-weight: 600;">
                    <i class="fa fa-envelope"></i> support@dietzone.sn
                </a>
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
