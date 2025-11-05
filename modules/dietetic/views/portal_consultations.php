<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Mes Consultations'; ?></title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
        .navbar-custom {
            background: #3498db;
            border: none;
            border-radius: 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            color: white;
            display: flex;
            align-items: center;
        }
        .navbar-custom .navbar-brand img {
            max-height: 30px;
            margin-right: 10px;
        }
        .navbar-custom .navbar-nav>li>a { color: white; }
        .navbar-custom .navbar-nav>li>a:hover { background: rgba(255,255,255,0.1); }

        .consultation-card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #2ecc71;
        }
        .consultation-card.past {
            border-left-color: #95a5a6;
            opacity: 0.8;
        }
        .consultation-card.upcoming {
            border-left-color: #3498db;
        }
        .consultation-date {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .consultation-type {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .consultation-type.followup { background: #3498db; color: white; }
        .consultation-type.initial { background: #2ecc71; color: white; }
        .consultation-type.evaluation { background: #f39c12; color: white; }
        .consultation-type.final { background: #e74c3c; color: white; }

        @media (max-width: 768px) {
            .navbar-header { float: none; }
            .navbar-toggle {
                display: block;
                float: left;
                margin-left: 15px;
            }
            .navbar-brand { display: block; text-align: center; }
            .navbar-collapse {
                border-top: 1px solid rgba(255,255,255,0.2);
                box-shadow: none;
            }
            .navbar-collapse.collapse { display: none!important; }
            .navbar-collapse.collapse.in { display: block!important; }
            .navbar-nav { float: none!important; margin: 0; }
            .navbar-nav>li { float: none; }
            .navbar-nav>li>a { padding-top: 15px; padding-bottom: 15px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Menu</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('dietetic/portal'); ?>">
                    <?php
                    $logo_path = get_option('company_logo');
                    if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                    ?>
                        <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="Logo">
                    <?php } else { ?>
                        <i class="fa fa-heartbeat"></i> Programme Diététique
                    <?php } ?>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Tableau de bord</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>"><i class="fa fa-cutlery"></i> Mes Repas</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2><i class="fa fa-calendar-check-o"></i> Mes Consultations</h2>
        <hr>

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
                <h3><i class="fa fa-clock-o"></i> Consultations à Venir</h3>
                <div id="upcoming-consultations">
                <?php foreach ($upcoming as $consultation) { ?>
                    <div class="consultation-card upcoming consultation-item">
                        <div class="consultation-date">
                            <i class="fa fa-calendar"></i> <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                        </div>
                        <span class="consultation-type <?php echo $consultation->consultation_type; ?>">
                            <?php
                            $types = [
                                'initial' => 'Consultation Initiale',
                                'followup' => 'Suivi',
                                'evaluation' => 'Évaluation',
                                'final' => 'Consultation Finale'
                            ];
                            echo isset($types[$consultation->consultation_type]) ? $types[$consultation->consultation_type] : ucfirst(str_replace('_', ' ', $consultation->consultation_type));
                            ?>
                        </span>

                        <?php if ($consultation->dietitian_name) { ?>
                            <p><strong><i class="fa fa-user-md"></i> Diététicien:</strong> <?php echo htmlspecialchars($consultation->dietitian_name); ?></p>
                        <?php } ?>

                        <?php if ($consultation->duration) { ?>
                            <p><strong><i class="fa fa-clock-o"></i> Durée:</strong> <?php echo $consultation->duration; ?> minutes</p>
                        <?php } ?>
                    </div>
                <?php } ?>
                </div>
                <div id="upcoming-pagination" class="text-center" style="margin-top: 20px;"></div>
            <?php } ?>

            <?php if (!empty($past)) { ?>
                <h3 class="mtop30"><i class="fa fa-history"></i> Consultations Passées</h3>
                <div id="past-consultations">
                <?php foreach ($past as $consultation) { ?>
                    <div class="consultation-card past consultation-item">
                        <div class="consultation-date">
                            <i class="fa fa-calendar"></i> <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                        </div>
                        <span class="consultation-type <?php echo $consultation->consultation_type; ?>">
                            <?php
                            $types = [
                                'initial' => 'Consultation Initiale',
                                'followup' => 'Suivi',
                                'evaluation' => 'Évaluation',
                                'final' => 'Consultation Finale'
                            ];
                            echo isset($types[$consultation->consultation_type]) ? $types[$consultation->consultation_type] : ucfirst(str_replace('_', ' ', $consultation->consultation_type));
                            ?>
                        </span>

                        <?php if ($consultation->dietitian_name) { ?>
                            <p><strong><i class="fa fa-user-md"></i> Diététicien:</strong> <?php echo htmlspecialchars($consultation->dietitian_name); ?></p>
                        <?php } ?>

                        <?php if ($consultation->duration) { ?>
                            <p><strong><i class="fa fa-clock-o"></i> Durée:</strong> <?php echo $consultation->duration; ?> minutes</p>
                        <?php } ?>

                        <?php if ($consultation->observations) { ?>
                            <hr style="margin: 10px 0;">
                            <p><strong><i class="fa fa-file-text-o"></i> Observations:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($consultation->observations)); ?></p>
                        <?php } ?>

                        <?php if ($consultation->recommendations) { ?>
                            <hr style="margin: 10px 0;">
                            <p><strong><i class="fa fa-lightbulb-o"></i> Recommandations:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($consultation->recommendations)); ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>
                </div>
                <div id="past-pagination" class="text-center" style="margin-top: 20px;"></div>
            <?php } ?>

        <?php } else { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Aucune consultation enregistrée pour le moment.
            </div>
        <?php } ?>

        <hr>
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function paginateItems(containerId, paginationId, itemsPerPage) {
            var $container = $('#' + containerId);
            var $items = $container.find('.consultation-item');
            var $pagination = $('#' + paginationId);
            var totalItems = $items.length;
            var totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalItems === 0) {
                return; // No items to paginate
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
