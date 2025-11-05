<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Mesures</title>
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
        .navbar-custom .navbar-nav>li>a {
            color: white;
        }
        .navbar-custom .navbar-nav>li>a:hover {
            background: rgba(255,255,255,0.1);
        }

        .content-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .measurement-item {
            background: #f9f9f9;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .measurement-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .measurement-date {
            font-size: 18px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }

        .measurement-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .stat-item {
            background: white;
            padding: 10px 15px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            min-width: 120px;
        }

        .stat-item label {
            display: block;
            font-size: 12px;
            color: #777;
            margin-bottom: 5px;
        }

        .stat-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .measurement-stats {
                flex-direction: column;
            }
            .stat-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('dietetic/portal'); ?>">
                    <i class="fa fa-heartbeat"></i> Programme Diététique
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Tableau de bord</a></li>
                    <li class="active"><a href="<?php echo site_url('dietetic/portal/measurements'); ?>"><i class="fa fa-history"></i> Mesures</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="content-box">
            <h2><i class="fa fa-history text-info"></i> Historique des Mesures</h2>
            <hr>

            <div class="text-right" style="margin-bottom: 20px;">
                <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="btn btn-success">
                    <i class="fa fa-plus-circle"></i> Ajouter une Mesure
                </a>
            </div>

            <?php if (!empty($measurements)) { ?>
                <!-- Weight Evolution Chart -->
                <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
                <div class="chart-container">
                    <h4><i class="fa fa-line-chart"></i> Évolution du Poids</h4>
                    <canvas id="weightChart" height="80"></canvas>
                </div>
                <?php } ?>

                <!-- Measurements List -->
                <div id="measurements-list">
                <?php foreach ($measurements as $measurement) { ?>
                    <div class="measurement-item measurement-card">
                        <div class="measurement-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($measurement->measurement_date)); ?>
                        </div>

                        <div class="measurement-stats">
                            <div class="stat-item">
                                <label>Poids</label>
                                <div class="value"><?php echo number_format($measurement->weight, 1); ?> kg</div>
                            </div>

                            <?php if ($measurement->bmi) { ?>
                            <div class="stat-item">
                                <label>IMC</label>
                                <div class="value"><?php echo number_format($measurement->bmi, 1); ?></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->body_fat) { ?>
                            <div class="stat-item">
                                <label>Masse Grasse</label>
                                <div class="value"><?php echo number_format($measurement->body_fat, 1); ?>%</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->muscle_mass) { ?>
                            <div class="stat-item">
                                <label>Masse Musculaire</label>
                                <div class="value"><?php echo number_format($measurement->muscle_mass, 1); ?>%</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->waist) { ?>
                            <div class="stat-item">
                                <label>Tour de Taille</label>
                                <div class="value"><?php echo number_format($measurement->waist, 1); ?> cm</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->hips) { ?>
                            <div class="stat-item">
                                <label>Tour de Hanches</label>
                                <div class="value"><?php echo number_format($measurement->hips, 1); ?> cm</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->chest) { ?>
                            <div class="stat-item">
                                <label>Tour de Poitrine</label>
                                <div class="value"><?php echo number_format($measurement->chest, 1); ?> cm</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->arms) { ?>
                            <div class="stat-item">
                                <label>Tour de Bras</label>
                                <div class="value"><?php echo number_format($measurement->arms, 1); ?> cm</div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->thighs) { ?>
                            <div class="stat-item">
                                <label>Tour de Cuisses</label>
                                <div class="value"><?php echo number_format($measurement->thighs, 1); ?> cm</div>
                            </div>
                            <?php } ?>
                        </div>

                        <?php if ($measurement->notes) { ?>
                        <div style="margin-top: 15px; padding: 10px; background: white; border-radius: 4px;">
                            <strong><i class="fa fa-sticky-note"></i> Notes:</strong>
                            <p style="margin: 5px 0 0 0;"><?php echo nl2br(htmlspecialchars($measurement->notes)); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                </div>
                <div id="measurements-pagination" class="text-center" style="margin-top: 20px;"></div>

            <?php } else { ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Aucune mesure enregistrée pour le moment.
                    <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>">Ajoutez votre première mesure</a> pour commencer à suivre votre évolution.
                </div>
            <?php } ?>

            <div class="text-center" style="margin-top: 30px;">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
                </a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script>
    function paginateItems(containerId, paginationId, itemsPerPage) {
        var $container = $('#' + containerId);
        var $items = $container.find('.measurement-card');
        var $pagination = $('#' + paginationId);
        var totalItems = $items.length;
        var totalPages = Math.ceil(totalItems / itemsPerPage);

        if (totalPages <= 1) {
            return; // No pagination needed
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
    </script>

    <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
    <script>
    $(document).ready(function() {
        var ctx = document.getElementById('weightChart').getContext('2d');
        var weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($weight_evolution as $point) {
                        echo '"' . date('d/m', strtotime($point->measurement_date)) . '",';
                    } ?>
                ],
                datasets: [{
                    label: 'Poids (kg)',
                    data: [
                        <?php foreach ($weight_evolution as $point) {
                            echo $point->weight . ',';
                        } ?>
                    ],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        });

        // Add pagination to measurements list
        paginateItems('measurements-list', 'measurements-pagination', 10);
    });
    </script>
    <?php } else { ?>
    <script>
    $(document).ready(function() {
        // Add pagination to measurements list
        paginateItems('measurements-list', 'measurements-pagination', 10);
    });
    </script>
    <?php } ?>
</body>
</html>
