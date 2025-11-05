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

        .rating-form-container {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto 30px;
        }
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
        }
        .form-header h2 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .form-header .dietitian-name {
            color: #16a085;
            font-size: 20px;
            font-weight: 600;
        }

        .rating-criterion {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #16a085;
        }
        .criterion-label {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: block;
        }
        .criterion-description {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }

        /* Star rating system */
        .star-rating {
            display: flex;
            gap: 5px;
            font-size: 32px;
        }
        .star {
            cursor: pointer;
            color: #ddd;
            transition: all 0.2s ease;
        }
        .star:hover,
        .star.hovered {
            color: #f39c12;
            transform: scale(1.1);
        }
        .star.selected {
            color: #f39c12;
        }

        .overall-rating-display {
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
        }
        .overall-rating-display h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            opacity: 0.9;
        }
        .overall-rating-number {
            font-size: 48px;
            font-weight: 700;
            line-height: 1;
        }
        .overall-stars {
            font-size: 24px;
            margin-top: 10px;
        }

        .comment-section {
            margin-top: 30px;
        }
        .comment-section textarea {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .comment-section textarea:focus {
            border-color: #16a085;
            outline: none;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-submit {
            background: #16a085;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background: #138d75;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
            color: white;
        }
        .btn-cancel {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-cancel:hover {
            background: #7f8c8d;
            color: white;
        }

        .alert-custom {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-custom.success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .alert-custom.error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
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
                    <li><a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>"><i class="fa fa-user-md"></i> Mon Diététicien</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($error)) { ?>
            <div class="alert-custom error">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <div class="rating-form-container">
            <div class="form-header">
                <h2><i class="fa fa-star"></i> Noter Mon Diététicien</h2>
                <div class="dietitian-name">
                    <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                </div>
            </div>

            <!-- Overall Rating Display -->
            <div class="overall-rating-display">
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

            <form method="POST" action="<?php echo site_url('dietetic/portal/rate_dietitian'); ?>" id="rating-form">
                <!-- Professionalism -->
                <div class="rating-criterion">
                    <label class="criterion-label">
                        <i class="fa fa-briefcase"></i> Professionnalisme
                    </label>
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
                    <label class="criterion-label">
                        <i class="fa fa-comments"></i> Écoute
                    </label>
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
                    <label class="criterion-label">
                        <i class="fa fa-lightbulb-o"></i> Conseils Pratiques
                    </label>
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
                    <label class="criterion-label">
                        <i class="fa fa-line-chart"></i> Résultats Obtenus
                    </label>
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
                    <label class="criterion-label">
                        <i class="fa fa-clock-o"></i> Disponibilité
                    </label>
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

                <!-- Comment -->
                <div class="comment-section">
                    <label class="criterion-label">
                        <i class="fa fa-comment"></i> Votre Commentaire (Optionnel)
                    </label>
                    <div class="criterion-description">
                        Partagez votre expérience en détail pour aider les autres patients
                    </div>
                    <textarea name="comment" id="comment" placeholder="Écrivez votre avis ici..."><?php echo isset($existing_rating) ? htmlspecialchars($existing_rating->comment) : ''; ?></textarea>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">
                        <i class="fa fa-check"></i> <?php echo isset($existing_rating) ? 'Mettre à Jour' : 'Publier mon Avis'; ?>
                    </button>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="btn btn-cancel">
                        <i class="fa fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
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
