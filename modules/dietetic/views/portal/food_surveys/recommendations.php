<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Recommandations - <?php echo get_option('companyname'); ?></title>
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

        /* Header */
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
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .portal-logo img {
            max-height: 40px;
            width: auto;
        }

        .portal-logo-text {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #01807B;
            font-size: 20px;
            font-weight: 700;
        }

        .portal-logo-text i {
            font-size: 24px;
        }

        .portal-nav-desktop {
            display: none;
            gap: 5px;
        }

        @media (min-width: 769px) {
            .portal-nav-desktop {
                display: flex;
            }
        }

        .portal-nav-desktop a {
            padding: 10px 16px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .portal-nav-desktop a:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .portal-nav-desktop a.active {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
        }

        /* Content Container */
        .content-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        /* Page Header */
        .page-header-mobile {
            margin-bottom: 25px;
        }

        .page-header-mobile h1 {
            font-size: 26px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-mobile h1 i {
            color: #01807B;
            font-size: 28px;
        }

        .page-header-mobile p {
            color: #6c757d;
            font-size: 15px;
            margin: 0;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 80px;
            color: #01807B;
            opacity: 0.2;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 22px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 12px 0;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 8px 0;
        }

        /* Entry Card */
        .entry-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .entry-card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 16px 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .entry-date {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .entry-date i {
            color: #01807B;
        }

        /* Recommendation */
        .recommendation {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .recommendation:last-child {
            border-bottom: none;
        }

        .recommendation-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .dietitian-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
        }

        .dietitian-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recommendation-info h4 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 700;
            color: #212529;
        }

        .recommendation-info p {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        .recommendation-info i {
            font-size: 12px;
        }

        .recommendation-text {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid #01807B;
            margin-bottom: 20px;
            line-height: 1.6;
            color: #495057;
        }

        /* Comments Section */
        .comments-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
        }

        .comments-header {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .comments-header i {
            color: #01807B;
        }

        .comment-item {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .comment-meta {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .comment-meta i {
            color: #01807B;
        }

        .comment-text {
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Add Comment Form */
        .add-comment-form {
            margin-top: 12px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            transition: all 0.2s;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: #01807B;
            box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
        }

        .comment-form button {
            margin-top: 10px;
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            cursor: pointer;
            min-height: 44px;
        }

        .comment-form button:active {
            transform: scale(0.98);
        }

        @media (min-width: 769px) {
            .comment-form button {
                width: auto;
            }

            .comment-form button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
            }
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(72, 187, 120, 0.1);
            border-left: 4px solid #48bb78;
            color: #2d5f3f;
        }

        .alert-danger {
            background: rgba(245, 101, 101, 0.1);
            border-left: 4px solid #f56565;
            color: #7f2323;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
            z-index: 99;
            display: block;
        }

        @media (min-width: 769px) {
            .bottom-nav {
                display: none;
            }
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            max-width: 600px;
            margin: 0 auto;
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 6px 0;
            color: #6c757d;
            text-decoration: none;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.2s;
            min-height: 44px;
            justify-content: center;
        }

        .bottom-nav-item.active {
            color: #01807B;
        }

        .bottom-nav-item i {
            font-size: 22px;
        }

        .bottom-nav-item:active {
            transform: scale(0.95);
        }

        /* Hamburger Menu */
        .hamburger-menu {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        @media (min-width: 769px) {
            .hamburger-menu {
                display: none;
            }
        }

        .hamburger-menu:hover {
            background: #f8f9fa;
        }

        .hamburger-icon {
            width: 28px;
            height: 24px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .hamburger-icon span {
            display: block;
            height: 3px;
            background: #01807B;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .hamburger-menu.active .hamburger-icon span:nth-child(1) {
            transform: translateY(10.5px) rotate(45deg);
        }

        .hamburger-menu.active .hamburger-icon span:nth-child(2) {
            opacity: 0;
        }

        .hamburger-menu.active .hamburger-icon span:nth-child(3) {
            transform: translateY(-10.5px) rotate(-45deg);
        }

        /* Mobile Menu Overlay */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Menu Panel */
        .mobile-menu-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: 280px;
            max-width: 85%;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 999;
            overflow-y: auto;
            padding-top: 60px;
        }

        .mobile-menu-panel.active {
            transform: translateX(0);
        }

        .mobile-menu-items {
            padding: 20px 0;
        }

        .mobile-menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: #495057;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .mobile-menu-item:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .mobile-menu-item.active {
            background: #e8f5f4;
            color: #01807B;
            border-left-color: #01807B;
        }

        .mobile-menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        /* Loading */
        .btn-loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
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

                <!-- Desktop Navigation -->
                <nav class="portal-nav-desktop">
                    <a href="<?php echo site_url('dietetic/portal'); ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="active">
                        <i class="fa fa-clipboard-list"></i> Enquêtes
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
                        <i class="fa fa-user"></i> Profil
                    </a>
                </nav>

                <!-- Hamburger Menu (Mobile) -->
                <div class="hamburger-menu" id="hamburgerMenu">
                    <div class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-panel" id="mobileMenuPanel">
        <div class="mobile-menu-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="mobile-menu-item">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="mobile-menu-item">
                <i class="fa fa-cutlery"></i>
                <span>Plans de Repas</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="mobile-menu-item active">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes Alimentaires</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="mobile-menu-item">
                <i class="fa fa-heartbeat"></i>
                <span>Mes Mesures</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="mobile-menu-item">
                <i class="fa fa-user-md"></i>
                <span>Mon Diététicien</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="mobile-menu-item">
                <i class="fa fa-bell"></i>
                <span>Notifications</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('clients/profile'); ?>" class="mobile-menu-item">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>
            <a href="<?php echo site_url('authentication/logout'); ?>" class="mobile-menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-mobile">
            <h1>
                <i class="fa fa-comments"></i>
                Recommandations
            </h1>
            <p><?php echo htmlspecialchars($survey->survey_name); ?></p>
        </div>

        <!-- Alert Area -->
        <div id="alertArea"></div>

        <?php if (empty($entries)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fa fa-lightbulb-o"></i>
                <h3>Aucune recommandation</h3>
                <p>Votre diététicien n'a pas encore laissé de recommandations pour cette enquête.</p>
                <p>Continuez à soumettre vos repas quotidiens pour recevoir des conseils personnalisés.</p>
            </div>
        <?php else: ?>
            <!-- Entries with Recommendations -->
            <?php foreach ($entries as $entry): ?>
                <div class="entry-card">
                    <div class="entry-card-header">
                        <div class="entry-date">
                            <i class="fa fa-calendar"></i>
                            Entrée du <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?>
                        </div>
                    </div>

                    <?php foreach ($entry->recommendations as $recommendation): ?>
                        <div class="recommendation" data-id="<?php echo $recommendation->id; ?>">
                            <div class="recommendation-header">
                                <div class="dietitian-avatar">
                                    <?php
                                    $has_profile_image = false;
                                    if (!empty($recommendation->profile_image)) {
                                        $image_path = FCPATH . 'uploads/staff_profile_images/' . $recommendation->profile_image;
                                        if (file_exists($image_path)) {
                                            $has_profile_image = true;
                                        }
                                    }

                                    if ($has_profile_image): ?>
                                        <img src="<?php echo base_url('uploads/staff_profile_images/' . $recommendation->profile_image); ?>" alt="<?php echo htmlspecialchars($recommendation->dietitian_name); ?>">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($recommendation->dietitian_name, 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="recommendation-info">
                                    <h4><?php echo htmlspecialchars($recommendation->dietitian_name); ?></h4>
                                    <p>
                                        <i class="fa fa-clock-o"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($recommendation->created_at)); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="recommendation-text">
                                <?php echo nl2br(htmlspecialchars($recommendation->recommendation_text)); ?>
                            </div>

                            <div class="comments-section">
                                <div class="comments-header">
                                    <i class="fa fa-comment"></i>
                                    Vos commentaires
                                </div>

                                <?php if (isset($recommendation->comments) && count($recommendation->comments) > 0): ?>
                                    <?php foreach ($recommendation->comments as $comment): ?>
                                        <div class="comment-item">
                                            <div class="comment-meta">
                                                <i class="fa fa-user-circle"></i>
                                                Vous
                                                <span>•</span>
                                                <?php echo date('d/m/Y à H:i', strtotime($comment->created_at)); ?>
                                            </div>
                                            <div class="comment-text">
                                                <?php echo nl2br(htmlspecialchars($comment->comment_text)); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Add Comment Form -->
                                <div class="add-comment-form">
                                    <form class="comment-form" data-recommendation="<?php echo $recommendation->id; ?>">
                                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                        <textarea name="comment_text" placeholder="Ajoutez un commentaire ou une question..." required></textarea>
                                        <button type="submit">
                                            <i class="fa fa-paper-plane"></i>
                                            Envoyer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
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
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="bottom-nav-item active">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes</span>
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
        $(function() {
            'use strict';

            // Touch feedback
            $('.comment-form button, .bottom-nav-item').on('touchstart', function() {
                $(this).css('opacity', '0.8');
            }).on('touchend', function() {
                $(this).css('opacity', '1');
            });

            // Submit comment
            $('.comment-form').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $button = $form.find('button');
                var $textarea = $form.find('textarea');
                var recommendationId = $form.data('recommendation');
                var commentText = $textarea.val().trim();

                if (!commentText) {
                    showAlert('danger', 'Veuillez saisir un commentaire');
                    return;
                }

                // Disable button
                $button.addClass('btn-loading').prop('disabled', true);

                // Serialize form to include CSRF token
                var formData = $form.serialize() + '&recommendation_id=' + recommendationId;

                $.ajax({
                    url: '<?php echo site_url('dietetic/portal/add_comment'); ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Add comment to UI
                            var now = new Date();
                            var dateStr = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});

                            var commentHtml = '<div class="comment-item">' +
                                '<div class="comment-meta">' +
                                '<i class="fa fa-user-circle"></i> Vous <span>•</span> ' + dateStr +
                                '</div>' +
                                '<div class="comment-text">' + escapeHtml(commentText) + '</div>' +
                                '</div>';

                            $form.closest('.comments-section').find('.comments-header').after(commentHtml);

                            // Clear textarea
                            $textarea.val('');

                            showAlert('success', 'Commentaire ajouté avec succès');
                        } else {
                            showAlert('danger', response.message || 'Erreur lors de l\'ajout du commentaire');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Erreur de connexion. Veuillez réessayer.');
                    },
                    complete: function() {
                        $button.removeClass('btn-loading').prop('disabled', false);
                    }
                });
            });

            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

                var alertHtml = '<div class="alert ' + alertClass + '">' +
                    '<i class="fa fa-' + icon + '"></i> ' + message +
                    '</div>';

                $('#alertArea').html(alertHtml);

                setTimeout(function() {
                    $('#alertArea').fadeOut(function() {
                        $(this).html('').show();
                    });
                }, 3000);
            }

            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // Hamburger Menu Toggle
            const hamburgerMenu = document.getElementById('hamburgerMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const mobileMenuPanel = document.getElementById('mobileMenuPanel');

            function toggleMenu() {
                if (!hamburgerMenu || !mobileMenuOverlay || !mobileMenuPanel) return;

                hamburgerMenu.classList.toggle('active');
                mobileMenuOverlay.classList.toggle('active');
                mobileMenuPanel.classList.toggle('active');

                // Prevent body scroll when menu is open
                if (mobileMenuPanel.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

            if (hamburgerMenu) {
                hamburgerMenu.addEventListener('click', toggleMenu);
            }

            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', toggleMenu);
            }

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenuPanel && mobileMenuPanel.classList.contains('active')) {
                    toggleMenu();
                }
            });

            // Touch feedback for mobile menu items
            document.querySelectorAll('.mobile-menu-item').forEach(function(element) {
                element.addEventListener('touchstart', function() {
                    this.style.opacity = '0.7';
                });
                element.addEventListener('touchend', function() {
                    this.style.opacity = '';
                });
            });
        });
    </script>
</body>
</html>
