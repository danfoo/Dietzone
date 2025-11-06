<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title : 'Mes Plans Alimentaires'; ?></title>
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
            max-width: 1200px;
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
            border-left: 4px solid #667eea;
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
            color: #667eea;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 0;
            font-size: 15px;
        }

        /* Program Info */
        .program-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);
        }

        .program-info i {
            font-size: 32px;
            opacity: 0.9;
        }

        .program-info-text {
            flex: 1;
        }

        .program-info-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
        }

        .program-info-name {
            font-size: 20px;
            font-weight: 700;
        }

        /* Meal Plans Grid */
        .meal-plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .meal-plan-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
            border-left: 5px solid #667eea;
        }

        .meal-plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .meal-plan-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
        }

        .week-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .meal-plan-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .meal-plan-body {
            padding: 25px;
        }

        .meal-plan-notes {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }

        .meal-plan-actions {
            display: flex;
            gap: 10px;
        }

        .btn-view-meal {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-view-meal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 147, 251, 0.4);
            color: white;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 20px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 10px;
        }

        .empty-state-text {
            color: #6c757d;
            font-size: 15px;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Back Button */
        .btn-back {
            background: white;
            color: #495057;
            border: 2px solid #dee2e6;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: #f8f9fa;
            border-color: #667eea;
            color: #667eea;
            text-decoration: none;
        }

        /* Pagination */
        .pagination-container {
            text-align: center;
            margin-top: 30px;
        }

        .pagination {
            display: inline-flex;
            gap: 5px;
            margin: 0;
        }

        .pagination li {
            list-style: none;
        }

        .pagination a,
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #495057;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .pagination li.active span {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .pagination li.disabled span {
            opacity: 0.5;
            cursor: not-allowed;
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

            .meal-plans-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .program-info {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .portal-logo img {
                max-height: 40px;
            }

            .portal-logo-text {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .meal-plan-header,
            .meal-plan-body {
                padding: 20px;
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
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="active">
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
            <h1><i class="fa fa-cutlery"></i> Mes Plans Alimentaires</h1>
            <p>Consultez vos plans de repas hebdomadaires personnalisés</p>
        </div>

        <?php if (isset($active_program) && $active_program) { ?>
            <!-- Program Info -->
            <div class="program-info animate-in delay-1">
                <i class="fa fa-heartbeat"></i>
                <div class="program-info-text">
                    <div class="program-info-label">Programme Actif</div>
                    <div class="program-info-name"><?php echo htmlspecialchars($active_program->program_name); ?></div>
                </div>
            </div>

            <?php if (!empty($meal_plans)) { ?>
                <!-- Meal Plans Grid -->
                <div class="meal-plans-grid animate-in delay-2" id="meal-plans-list">
                    <?php foreach ($meal_plans as $plan) { ?>
                        <div class="meal-plan-card meal-plan-item">
                            <div class="meal-plan-header">
                                <div class="week-badge">
                                    <i class="fa fa-calendar"></i>
                                    Semaine <?php echo $plan->week_number; ?>
                                </div>
                                <h3 class="meal-plan-title"><?php echo htmlspecialchars($plan->plan_name); ?></h3>
                            </div>
                            <div class="meal-plan-body">
                                <?php if ($plan->notes) { ?>
                                    <div class="meal-plan-notes">
                                        <i class="fa fa-sticky-note-o"></i> <?php echo nl2br(htmlspecialchars($plan->notes)); ?>
                                    </div>
                                <?php } ?>
                                <div class="meal-plan-actions">
                                    <a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>" class="btn-view-meal">
                                        <i class="fa fa-eye"></i> Voir les Repas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-container" id="meal-plans-pagination"></div>

            <?php } else { ?>
                <!-- Empty State -->
                <div class="empty-state animate-in delay-2">
                    <div class="empty-state-icon">
                        <i class="fa fa-cutlery"></i>
                    </div>
                    <div class="empty-state-title">Aucun plan alimentaire</div>
                    <div class="empty-state-text">
                        Votre diététicien n'a pas encore créé de plan alimentaire pour votre programme. Contactez-le pour plus d'informations.
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- No Program Empty State -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div class="empty-state-title">Aucun programme actif</div>
                <div class="empty-state-text">
                    Vous n'avez pas de programme diététique actif pour le moment. Contactez votre diététicien pour commencer votre suivi.
                </div>
            </div>
        <?php } ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
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

        // Pagination
        function paginateItems(containerId, paginationId, itemsPerPage) {
            var $container = $('#' + containerId);
            var $items = $container.find('.meal-plan-item');
            var $pagination = $('#' + paginationId);
            var totalItems = $items.length;
            var totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalItems === 0 || totalPages <= 1) {
                return; // No pagination needed
            }

            function showPage(page) {
                $items.hide();
                var start = (page - 1) * itemsPerPage;
                var end = start + itemsPerPage;
                $items.slice(start, end).show();

                // Update pagination
                $pagination.empty();
                var paginationHtml = '<ul class="pagination">';

                // Previous
                if (page > 1) {
                    paginationHtml += '<li><a href="#" data-page="' + (page - 1) + '"><i class="fa fa-chevron-left"></i></a></li>';
                } else {
                    paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-left"></i></span></li>';
                }

                // Pages
                for (var i = 1; i <= totalPages; i++) {
                    if (i === page) {
                        paginationHtml += '<li class="active"><span>' + i + '</span></li>';
                    } else {
                        paginationHtml += '<li><a href="#" data-page="' + i + '">' + i + '</a></li>';
                    }
                }

                // Next
                if (page < totalPages) {
                    paginationHtml += '<li><a href="#" data-page="' + (page + 1) + '"><i class="fa fa-chevron-right"></i></a></li>';
                } else {
                    paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-right"></i></span></li>';
                }

                paginationHtml += '</ul>';
                $pagination.html(paginationHtml);

                // Bind clicks
                $pagination.find('a').on('click', function(e) {
                    e.preventDefault();
                    var newPage = parseInt($(this).data('page'));
                    showPage(newPage);
                    $('html, body').animate({
                        scrollTop: $container.offset().top - 100
                    }, 300);
                });
            }

            showPage(1);
        }

        $(document).ready(function() {
            paginateItems('meal-plans-list', 'meal-plans-pagination', 9);
        });
    </script>
</body>
</html>
