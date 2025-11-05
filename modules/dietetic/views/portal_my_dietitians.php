<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
        .navbar-custom {
            background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
            border: none;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-nav>li>a { color: white; }
        .navbar-custom .navbar-nav>li>a:hover { background: rgba(255,255,255,0.1); }
        .navbar-custom .navbar-nav>li.active>a { background: rgba(255,255,255,0.2); }

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
    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="icon-bar" style="background-color: white;"></span>
                    <span class="icon-bar" style="background-color: white;"></span>
                    <span class="icon-bar" style="background-color: white;"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('dietetic/portal'); ?>">
                    <i class="fa fa-heartbeat"></i> Mon Programme Diététique
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-dashboard"></i> Tableau de bord</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/measurements'); ?>"><i class="fa fa-line-chart"></i> Mes Mesures</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/consultations'); ?>"><i class="fa fa-calendar"></i> Consultations</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>"><i class="fa fa-cutlery"></i> Plans Alimentaires</a></li>
                    <li class="active"><a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>"><i class="fa fa-user-md"></i> Mon Diététicien</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dietitian-profile">
            <div class="dietitian-header">
                <div class="dietitian-avatar">
                    <?php if (!empty($dietitian->profile_image)) { ?>
                        <img src="<?php echo base_url('uploads/staff_profile_images/' . $dietitian->staffid . '/' . $dietitian->profile_image); ?>" alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                    <?php } else { ?>
                        <div class="dietitian-avatar-placeholder">
                            <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="dietitian-info">
                    <h2><?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?></h2>
                    <div class="dietitian-email">
                        <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($dietitian->email); ?>
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
                            <div class="rating-stars" style="color: #bdc3c7;">
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
                    <a href="<?php echo site_url('dietetic/portal/rate_dietitian'); ?>" class="btn btn-rate">
                        <i class="fa fa-star"></i>
                        <?php echo $my_rating ? 'Modifier mon avis' : 'Noter mon diététicien'; ?>
                    </a>
                    <a href="mailto:<?php echo $dietitian->email; ?>" class="btn btn-contact">
                        <i class="fa fa-envelope"></i> Contacter mon diététicien
                    </a>
                </div>
            <?php } else { ?>
                <div class="alert-info-custom">
                    <i class="fa fa-info-circle"></i> Vous devez avoir au moins une consultation complétée pour noter votre diététicien.
                </div>
            <?php } ?>

            <?php if ($my_rating) { ?>
                <div class="my-rating-section">
                    <h4><i class="fa fa-star"></i> Mon Avis</h4>
                    <div class="rating-display" style="margin-bottom: 15px;">
                        <div class="rating-number"><?php echo number_format($my_rating->overall_rating, 1); ?></div>
                        <div class="rating-stars">
                            <?php
                            $full_stars = floor($my_rating->overall_rating);
                            for ($i = 0; $i < $full_stars; $i++) {
                                echo '<i class="fa fa-star"></i> ';
                            }
                            for ($i = $full_stars; $i < 5; $i++) {
                                echo '<i class="fa fa-star-o"></i> ';
                            }
                            ?>
                        </div>
                    </div>

                    <?php if ($my_rating->comment) { ?>
                        <p style="color: #34495e; margin-bottom: 15px;">
                            "<?php echo nl2br(htmlspecialchars($my_rating->comment)); ?>"
                        </p>
                    <?php } ?>

                    <div class="rating-criteria">
                        <div class="criterion">
                            <strong><?php echo $my_rating->professionalism_rating; ?>/5</strong> Professionnalisme
                        </div>
                        <div class="criterion">
                            <strong><?php echo $my_rating->listening_rating; ?>/5</strong> Écoute
                        </div>
                        <div class="criterion">
                            <strong><?php echo $my_rating->advice_rating; ?>/5</strong> Conseils
                        </div>
                        <div class="criterion">
                            <strong><?php echo $my_rating->results_rating; ?>/5</strong> Résultats
                        </div>
                        <div class="criterion">
                            <strong><?php echo $my_rating->availability_rating; ?>/5</strong> Disponibilité
                        </div>
                    </div>

                    <p style="color: #95a5a6; font-size: 12px; margin-top: 15px; margin-bottom: 0;">
                        <i class="fa fa-clock-o"></i> Publié le <?php echo date('d/m/Y', strtotime($my_rating->created_at)); ?>
                    </p>
                </div>
            <?php } ?>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-bottom: 30px;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default btn-lg">
                <i class="fa fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
