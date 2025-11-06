<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title : 'Mes Consultations'; ?></title>
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
            border-left: 4px solid #11998e;
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
            color: #11998e;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 0;
            font-size: 15px;
        }

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .section-header i {
            font-size: 24px;
            color: #667eea;
        }

        .section-header h2 {
            color: #2c3e50;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        /* Consultation Cards */
        .consultations-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }

        .consultation-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .consultation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .consultation-card.upcoming {
            border-left-color: #11998e;
        }

        .consultation-card.past {
            border-left-color: #95a5a6;
            opacity: 0.9;
        }

        .consultation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .consultation-date {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .consultation-date i {
            color: #667eea;
            font-size: 20px;
        }

        .consultation-type {
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .consultation-type.initial {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .consultation-type.followup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .consultation-type.evaluation {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .consultation-type.final {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .consultation-body {
            display: grid;
            gap: 15px;
        }

        .consultation-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .consultation-info i {
            color: #667eea;
            font-size: 16px;
            margin-top: 3px;
        }

        .consultation-info-content {
            flex: 1;
        }

        .consultation-info strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .consultation-info p {
            color: #495057;
            margin: 0;
            line-height: 1.6;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 12px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .empty-state-icon {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #6c757d;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .empty-state p {
            color: #adb5bd;
            margin: 0;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .pagination-wrapper .pagination {
            margin: 0;
            display: flex;
            gap: 5px;
        }

        .pagination-wrapper .pagination li a,
        .pagination-wrapper .pagination li span {
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
            background: white;
        }

        .pagination-wrapper .pagination li a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination-wrapper .pagination li.active span {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination-wrapper .pagination li.disabled span {
            background: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            color: #495057;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 30px;
        }

        .back-button:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
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

            .section-header h2 {
                font-size: 18px;
            }

            .consultation-card {
                padding: 20px 15px;
            }

            .consultation-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .consultation-date {
                font-size: 16px;
            }

            .portal-logo img {
                max-height: 40px;
            }

            .portal-logo-text {
                font-size: 18px;
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
                    <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="active">
                        <i class="fa fa-calendar-check-o"></i> Consultations
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
            <h1><i class="fa fa-calendar-check-o"></i> Mes Consultations</h1>
            <p>Consultez vos rendez-vous passés et à venir avec votre diététicien</p>
        </div>

        <?php if (!empty($consultations)) { ?>
            <?php
            $now = new DateTime();
            $upcoming = [];
            $past = [];

            foreach ($consultations as $consultation) {
                $consultation_date = new DateTime($consultation->consultation_date);
                if ($consultation_date > $now) {
                    $upcoming[] = $consultation;
                } else {
                    $past[] = $consultation;
                }
            }
            ?>

            <?php if (!empty($upcoming)) { ?>
                <div class="section-header animate-in delay-1">
                    <i class="fa fa-clock-o"></i>
                    <h2>Consultations à Venir</h2>
                </div>
                <div class="consultations-grid animate-in delay-1" id="upcoming-consultations">
                <?php foreach ($upcoming as $consultation) { ?>
                    <div class="consultation-card upcoming consultation-item">
                        <div class="consultation-header">
                            <div class="consultation-date">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?></span>
                            </div>
                            <span class="consultation-type <?php echo $consultation->consultation_type; ?>">
                                <?php
                                $types = [
                                    'initial' => 'Initiale',
                                    'followup' => 'Suivi',
                                    'evaluation' => 'Évaluation',
                                    'final' => 'Finale'
                                ];
                                echo isset($types[$consultation->consultation_type]) ? $types[$consultation->consultation_type] : ucfirst(str_replace('_', ' ', $consultation->consultation_type));
                                ?>
                            </span>
                        </div>
                        <div class="consultation-body">
                            <?php if ($consultation->dietitian_name) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-user-md"></i>
                                    <div class="consultation-info-content">
                                        <strong>Diététicien</strong>
                                        <p><?php echo htmlspecialchars($consultation->dietitian_name); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($consultation->duration) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-clock-o"></i>
                                    <div class="consultation-info-content">
                                        <strong>Durée</strong>
                                        <p><?php echo $consultation->duration; ?> minutes</p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <div class="pagination-wrapper" id="upcoming-pagination"></div>
            <?php } ?>

            <?php if (!empty($past)) { ?>
                <div class="section-header animate-in delay-2" style="margin-top: 40px;">
                    <i class="fa fa-history"></i>
                    <h2>Consultations Passées</h2>
                </div>
                <div class="consultations-grid animate-in delay-2" id="past-consultations">
                <?php foreach ($past as $consultation) { ?>
                    <div class="consultation-card past consultation-item">
                        <div class="consultation-header">
                            <div class="consultation-date">
                                <i class="fa fa-calendar"></i>
                                <span><?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?></span>
                            </div>
                            <span class="consultation-type <?php echo $consultation->consultation_type; ?>">
                                <?php
                                $types = [
                                    'initial' => 'Initiale',
                                    'followup' => 'Suivi',
                                    'evaluation' => 'Évaluation',
                                    'final' => 'Finale'
                                ];
                                echo isset($types[$consultation->consultation_type]) ? $types[$consultation->consultation_type] : ucfirst(str_replace('_', ' ', $consultation->consultation_type));
                                ?>
                            </span>
                        </div>
                        <div class="consultation-body">
                            <?php if ($consultation->dietitian_name) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-user-md"></i>
                                    <div class="consultation-info-content">
                                        <strong>Diététicien</strong>
                                        <p><?php echo htmlspecialchars($consultation->dietitian_name); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($consultation->duration) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-clock-o"></i>
                                    <div class="consultation-info-content">
                                        <strong>Durée</strong>
                                        <p><?php echo $consultation->duration; ?> minutes</p>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($consultation->observations) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-file-text-o"></i>
                                    <div class="consultation-info-content">
                                        <strong>Observations</strong>
                                        <p><?php echo nl2br(htmlspecialchars($consultation->observations)); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($consultation->recommendations) { ?>
                                <div class="consultation-info">
                                    <i class="fa fa-lightbulb-o"></i>
                                    <div class="consultation-info-content">
                                        <strong>Recommandations</strong>
                                        <p><?php echo nl2br(htmlspecialchars($consultation->recommendations)); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <div class="pagination-wrapper" id="past-pagination"></div>
            <?php } ?>

        <?php } else { ?>
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-calendar-times-o"></i>
                </div>
                <h3>Aucune consultation</h3>
                <p>Vous n'avez pas encore de consultation enregistrée</p>
            </div>
        <?php } ?>

        <a href="<?php echo site_url('dietetic/portal'); ?>" class="back-button animate-in delay-2">
            <i class="fa fa-arrow-left"></i>
            Retour au Tableau de bord
        </a>
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

        function paginateItems(containerId, paginationId, itemsPerPage) {
            var $container = $('#' + containerId);
            var $items = $container.find('.consultation-item');
            var $pagination = $('#' + paginationId);
            var totalItems = $items.length;
            var totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalItems === 0) {
                return;
            }

            function showPage(page) {
                $items.hide();
                var start = (page - 1) * itemsPerPage;
                var end = start + itemsPerPage;
                $items.slice(start, end).show();

                // Update pagination buttons
                $pagination.empty();
                var paginationHtml = '<ul class="pagination" style="margin: 0;">';

                // Previous button
                if (page > 1) {
                    paginationHtml += '<li><a href="#" data-page="' + (page - 1) + '"><i class="fa fa-chevron-left"></i></a></li>';
                } else {
                    paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-left"></i></span></li>';
                }

                // Page numbers
                for (var i = 1; i <= totalPages; i++) {
                    if (i === page) {
                        paginationHtml += '<li class="active"><span>' + i + '</span></li>';
                    } else {
                        paginationHtml += '<li><a href="#" data-page="' + i + '">' + i + '</a></li>';
                    }
                }

                // Next button
                if (page < totalPages) {
                    paginationHtml += '<li><a href="#" data-page="' + (page + 1) + '"><i class="fa fa-chevron-right"></i></a></li>';
                } else {
                    paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-right"></i></span></li>';
                }

                paginationHtml += '</ul>';
                $pagination.html(paginationHtml);

                // Bind click events
                $pagination.find('a').on('click', function(e) {
                    e.preventDefault();
                    var newPage = parseInt($(this).data('page'));
                    showPage(newPage);
                    // Scroll to top of section
                    $('html, body').animate({
                        scrollTop: $container.offset().top - 100
                    }, 300);
                });
            }

            showPage(1);
        }

        $(document).ready(function() {
            paginateItems('upcoming-consultations', 'upcoming-pagination', 10);
            paginateItems('past-consultations', 'past-pagination', 10);
        });
    </script>
</body>
</html>
