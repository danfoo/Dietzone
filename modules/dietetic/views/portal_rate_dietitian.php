<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title : 'Noter Mon Diététicien'; ?></title>
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
            border-left: 4px solid #f39c12;
            text-align: center;
        }

        .page-header-modern h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-modern h1 i {
            color: #f39c12;
        }

        .dietitian-name-display {
            font-size: 20px;
            font-weight: 600;
            color: #667eea;
            margin-top: 10px;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 10px 0 0 0;
            font-size: 15px;
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

        .alert-modern.alert-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .alert-modern i {
            font-size: 20px;
        }

        /* Overall Rating Display */
        .overall-rating-card {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }

        .overall-rating-card h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .overall-rating-number {
            font-size: 56px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 10px;
        }

        .overall-stars {
            font-size: 28px;
        }

        .overall-stars i {
            margin: 0 3px;
        }

        /* Form Container */
        .form-modern {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        /* Rating Criterion */
        .rating-criterion {
            margin-bottom: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .rating-criterion:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .criterion-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .criterion-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .criterion-label {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .criterion-description {
            font-size: 13px;
            color: #6c757d;
            margin: 0 0 15px 52px;
        }

        /* Star Rating */
        .star-rating {
            display: flex;
            gap: 8px;
            margin-left: 52px;
        }

        .star {
            cursor: pointer;
            color: #dee2e6;
            font-size: 36px;
            transition: all 0.2s ease;
        }

        .star:hover,
        .star.hovered {
            color: #f39c12;
            transform: scale(1.15);
        }

        .star.selected {
            color: #f39c12;
        }

        /* Comment Section */
        .comment-section {
            margin-top: 30px;
        }

        .comment-section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .comment-section-title i {
            font-size: 20px;
            color: #667eea;
        }

        .comment-section-title h3 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .comment-description {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        textarea.form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            font-size: 14px;
            min-height: 120px;
            resize: vertical;
            transition: all 0.3s ease;
        }

        textarea.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
        }

        .btn-modern {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-modern.btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-modern.btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-modern.btn-default {
            background: white;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .btn-modern.btn-default:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            text-decoration: none;
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
        .delay-3 { animation-delay: 0.3s; opacity: 0; }

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

            .dietitian-name-display {
                font-size: 18px;
            }

            .form-modern {
                padding: 20px 15px;
            }

            .rating-criterion {
                padding: 20px 15px;
            }

            .criterion-description,
            .star-rating {
                margin-left: 0;
            }

            .star {
                font-size: 28px;
            }

            .overall-rating-number {
                font-size: 42px;
            }

            .overall-stars {
                font-size: 22px;
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
            .criterion-label {
                font-size: 16px;
            }

            .star {
                font-size: 24px;
                gap: 5px;
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
            <h1><i class="fa fa-star"></i> Noter Mon Diététicien</h1>
            <div class="dietitian-name-display">
                <i class="fa fa-user-md"></i> <?php echo isset($dietitian) ? htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname) : ''; ?>
            </div>
            <p>Partagez votre expérience pour aider les autres patients</p>
        </div>

        <!-- Alert -->
        <?php if (isset($error)) { ?>
            <div class="alert-modern alert-danger animate-in delay-1">
                <i class="fa fa-exclamation-triangle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php } ?>

        <!-- Overall Rating Display -->
        <div class="overall-rating-card animate-in delay-1">
            <h3>Note Globale Calculée</h3>
            <div class="overall-rating-number" id="overall-rating-number">0.0</div>
            <div class="overall-stars" id="overall-stars">
                <i class="fa fa-star-o"></i>
                <i class="fa fa-star-o"></i>
                <i class="fa fa-star-o"></i>
                <i class="fa fa-star-o"></i>
                <i class="fa fa-star-o"></i>
            </div>
        </div>

        <!-- Form -->
        <div class="form-modern animate-in delay-2">
            <form method="POST" action="<?php echo site_url('dietetic/portal/rate_dietitian'); ?>" id="rating-form">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

                <!-- Professionalism -->
                <div class="rating-criterion">
                    <div class="criterion-header">
                        <div class="criterion-icon">
                            <i class="fa fa-briefcase"></i>
                        </div>
                        <h4 class="criterion-label">Professionnalisme</h4>
                    </div>
                    <div class="criterion-description">
                        Ponctualité, présentation, respect des rendez-vous
                    </div>
                    <div class="star-rating" data-criterion="professionalism">
                        <span class="star" data-value="1"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fa fa-star"></i></span>
                    </div>
                    <input type="hidden" name="professionalism_rating" id="professionalism_rating" value="<?php echo isset($existing_rating) ? $existing_rating->professionalism_rating : ''; ?>">
                </div>

                <!-- Listening -->
                <div class="rating-criterion">
                    <div class="criterion-header">
                        <div class="criterion-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                        <h4 class="criterion-label">Écoute</h4>
                    </div>
                    <div class="criterion-description">
                        Capacité d'écoute, compréhension de vos besoins
                    </div>
                    <div class="star-rating" data-criterion="listening">
                        <span class="star" data-value="1"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fa fa-star"></i></span>
                    </div>
                    <input type="hidden" name="listening_rating" id="listening_rating" value="<?php echo isset($existing_rating) ? $existing_rating->listening_rating : ''; ?>">
                </div>

                <!-- Advice -->
                <div class="rating-criterion">
                    <div class="criterion-header">
                        <div class="criterion-icon">
                            <i class="fa fa-lightbulb-o"></i>
                        </div>
                        <h4 class="criterion-label">Conseils Pratiques</h4>
                    </div>
                    <div class="criterion-description">
                        Qualité et utilité des conseils donnés
                    </div>
                    <div class="star-rating" data-criterion="advice">
                        <span class="star" data-value="1"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fa fa-star"></i></span>
                    </div>
                    <input type="hidden" name="advice_rating" id="advice_rating" value="<?php echo isset($existing_rating) ? $existing_rating->advice_rating : ''; ?>">
                </div>

                <!-- Results -->
                <div class="rating-criterion">
                    <div class="criterion-header">
                        <div class="criterion-icon">
                            <i class="fa fa-line-chart"></i>
                        </div>
                        <h4 class="criterion-label">Résultats Obtenus</h4>
                    </div>
                    <div class="criterion-description">
                        Efficacité du programme, atteinte des objectifs
                    </div>
                    <div class="star-rating" data-criterion="results">
                        <span class="star" data-value="1"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fa fa-star"></i></span>
                    </div>
                    <input type="hidden" name="results_rating" id="results_rating" value="<?php echo isset($existing_rating) ? $existing_rating->results_rating : ''; ?>">
                </div>

                <!-- Availability -->
                <div class="rating-criterion">
                    <div class="criterion-header">
                        <div class="criterion-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <h4 class="criterion-label">Disponibilité</h4>
                    </div>
                    <div class="criterion-description">
                        Facilité pour prendre rendez-vous, réactivité
                    </div>
                    <div class="star-rating" data-criterion="availability">
                        <span class="star" data-value="1"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="2"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="3"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="4"><i class="fa fa-star"></i></span>
                        <span class="star" data-value="5"><i class="fa fa-star"></i></span>
                    </div>
                    <input type="hidden" name="availability_rating" id="availability_rating" value="<?php echo isset($existing_rating) ? $existing_rating->availability_rating : ''; ?>">
                </div>

                <!-- Comment Section -->
                <div class="comment-section">
                    <div class="comment-section-title">
                        <i class="fa fa-comment"></i>
                        <h3>Votre Commentaire (Optionnel)</h3>
                    </div>
                    <div class="comment-description">
                        Partagez votre expérience en détail pour aider les autres patients
                    </div>
                    <textarea name="comment" id="comment" class="form-control" placeholder="Écrivez votre avis ici..."><?php echo isset($existing_rating) ? htmlspecialchars($existing_rating->comment) : ''; ?></textarea>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="btn-modern btn-default">
                        <i class="fa fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fa fa-check"></i> <?php echo isset($existing_rating) ? 'Mettre à Jour' : 'Publier mon Avis'; ?>
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

        $(document).ready(function() {
            // Initialize ratings from existing values
            <?php if (isset($existing_rating)) { ?>
                updateStars('professionalism', <?php echo $existing_rating->professionalism_rating; ?>);
                updateStars('listening', <?php echo $existing_rating->listening_rating; ?>);
                updateStars('advice', <?php echo $existing_rating->advice_rating; ?>);
                updateStars('results', <?php echo $existing_rating->results_rating; ?>);
                updateStars('availability', <?php echo $existing_rating->availability_rating; ?>);
                calculateOverallRating();
            <?php } ?>

            // Star rating interaction
            $('.star').on('click', function() {
                var value = $(this).data('value');
                var criterion = $(this).closest('.star-rating').data('criterion');

                updateStars(criterion, value);
                $('#' + criterion + '_rating').val(value);
                calculateOverallRating();
            });

            // Hover effect
            $('.star').on('mouseenter', function() {
                var value = $(this).data('value');
                var container = $(this).closest('.star-rating');

                container.find('.star').each(function(index) {
                    if (index < value) {
                        $(this).addClass('hovered');
                    } else {
                        $(this).removeClass('hovered');
                    }
                });
            });

            $('.star-rating').on('mouseleave', function() {
                $(this).find('.star').removeClass('hovered');
            });

            function updateStars(criterion, value) {
                var container = $('[data-criterion="' + criterion + '"]');
                container.find('.star').each(function(index) {
                    if (index < value) {
                        $(this).addClass('selected');
                    } else {
                        $(this).removeClass('selected');
                    }
                });
            }

            function calculateOverallRating() {
                var ratings = [];
                var sum = 0;

                ['professionalism', 'listening', 'advice', 'results', 'availability'].forEach(function(criterion) {
                    var value = parseInt($('#' + criterion + '_rating').val());
                    if (value > 0) {
                        ratings.push(value);
                        sum += value;
                    }
                });

                if (ratings.length > 0) {
                    var average = sum / ratings.length;
                    $('#overall-rating-number').text(average.toFixed(1));

                    // Update stars display
                    var stars = '';
                    var fullStars = Math.floor(average);
                    var hasHalfStar = (average - fullStars) >= 0.5;

                    for (var i = 0; i < fullStars; i++) {
                        stars += '<i class="fa fa-star"></i> ';
                    }
                    if (hasHalfStar) {
                        stars += '<i class="fa fa-star-half-o"></i> ';
                        fullStars++;
                    }
                    for (var i = fullStars; i < 5; i++) {
                        stars += '<i class="fa fa-star-o"></i> ';
                    }

                    $('#overall-stars').html(stars);
                } else {
                    $('#overall-rating-number').text('0.0');
                    $('#overall-stars').html('<i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i>');
                }
            }

            // Form validation
            $('#rating-form').on('submit', function(e) {
                var hasRating = false;
                ['professionalism', 'listening', 'advice', 'results', 'availability'].forEach(function(criterion) {
                    if (parseInt($('#' + criterion + '_rating').val()) > 0) {
                        hasRating = true;
                    }
                });

                if (!hasRating) {
                    e.preventDefault();
                    alert('Veuillez noter au moins un critère avant de soumettre votre avis.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
