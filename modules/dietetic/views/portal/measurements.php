<?php
$active_page = 'measurements';
$page_title = 'Mes Mesures';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Page-specific styles for measurements */
.page-header-mobile {
    background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
    border-radius: 16px;
    padding: 24px 20px;
    margin-bottom: 24px;
    color: white;
    box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
}

.page-header-mobile h1 {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-header-mobile p {
    margin: 0;
    font-size: 15px;
    opacity: 0.95;
}

/* Chart Box */
.chart-box {
    background: white;
    border-radius: 16px;
    padding: 24px 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.chart-box h4 {
    color: #2c3e50;
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-box h4 i {
    color: #01807B;
}

/* Measurement Cards */
.measurement-card {
    background: white;
    border-left: 4px solid #01807B;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.measurement-card:active {
    transform: scale(0.98);
}

.measurement-date {
    font-size: 18px;
    font-weight: 700;
    color: #01807B;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.measurement-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
}

.stat-item {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 10px;
    text-align: center;
}

.stat-item label {
    display: block;
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
    margin-bottom: 6px;
}

.stat-item .value {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
}

.measurement-notes {
    margin-top: 16px;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 3px solid #01807B;
}

.measurement-notes strong {
    color: #2c3e50;
    font-size: 14px;
}

.measurement-notes p {
    margin: 8px 0 0 0;
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
}

/* Buttons */
.btn-add-measure {
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 48px;
}

.btn-add-measure:hover {
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
    color: white;
    text-decoration: none;
}

.btn-add-measure:active {
    transform: scale(0.97);
}

.btn-back {
    background: white;
    color: #495057;
    border: 2px solid #dee2e6;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 48px;
}

.btn-back:hover {
    background: #f8f9fa;
    border-color: #01807B;
    color: #01807B;
    text-decoration: none;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 50px 30px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state-icon {
    font-size: 56px;
    color: #dee2e6;
    margin-bottom: 16px;
}

.empty-state-title {
    font-size: 18px;
    font-weight: 700;
    color: #495057;
    margin-bottom: 8px;
}

.empty-state-text {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
}

/* Pagination */
.pagination-container {
    text-align: center;
    margin: 24px 0;
}

.pagination {
    display: inline-flex;
    gap: 6px;
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
    background: #01807B;
    border-color: #01807B;
    color: white;
}

.pagination li.active span {
    background: #01807B;
    border-color: #01807B;
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
@media (min-width: 769px) {
    .measurement-stats {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

@media (max-width: 768px) {
    .page-header-mobile {
        padding: 20px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .page-header-mobile h1 {
        font-size: 18px;
    }

    .chart-box {
        padding: 20px 16px;
        border-radius: 12px;
    }

    .measurement-card {
        padding: 16px;
        border-radius: 12px;
    }

    .measurement-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}

@media (max-width: 375px) {
    .page-header-mobile h1 {
        font-size: 18px;
    }
}
</style>

        <!-- Page Header -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-history"></i> Mes Mesures</h1>
            <p>Suivez votre évolution</p>
        </div>

        <!-- Add Button -->
        <div style="text-align: right; margin-bottom: 24px;" class="animate-in delay-1">
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="btn-add-measure">
                <i class="fa fa-plus-circle"></i> Ajouter une Mesure
            </a>
        </div>

        <?php if (!empty($measurements)) { ?>
            <!-- Weight Evolution Chart -->
            <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
            <div class="chart-box animate-in delay-1">
                <h4><i class="fa fa-line-chart"></i> Évolution du Poids</h4>
                <canvas id="weightChart" height="100"></canvas>
            </div>
            <?php } ?>

            <!-- Measurements List -->
            <div id="measurements-list" class="animate-in delay-2">
                <?php foreach ($measurements as $measurement) { ?>
                    <div class="measurement-card">
                        <div class="measurement-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($measurement->measurement_date)); ?>
                        </div>

                        <div class="measurement-stats">
                            <div class="stat-item">
                                <label>Poids</label>
                                <div class="value"><?php echo number_format($measurement->weight, 1); ?> <span style="font-size: 14px; color: #6c757d;">kg</span></div>
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
                                <div class="value"><?php echo number_format($measurement->body_fat, 1); ?><span style="font-size: 14px; color: #6c757d;">%</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->muscle_mass) { ?>
                            <div class="stat-item">
                                <label>Masse Musculaire</label>
                                <div class="value"><?php echo number_format($measurement->muscle_mass, 1); ?><span style="font-size: 14px; color: #6c757d;">%</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->waist) { ?>
                            <div class="stat-item">
                                <label>Tour de Taille</label>
                                <div class="value"><?php echo number_format($measurement->waist, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->hips) { ?>
                            <div class="stat-item">
                                <label>Tour de Hanches</label>
                                <div class="value"><?php echo number_format($measurement->hips, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->chest) { ?>
                            <div class="stat-item">
                                <label>Tour de Poitrine</label>
                                <div class="value"><?php echo number_format($measurement->chest, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->arms) { ?>
                            <div class="stat-item">
                                <label>Tour de Bras</label>
                                <div class="value"><?php echo number_format($measurement->arms, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->thighs) { ?>
                            <div class="stat-item">
                                <label>Tour de Cuisses</label>
                                <div class="value"><?php echo number_format($measurement->thighs, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>
                        </div>

                        <?php if ($measurement->notes) { ?>
                        <div class="measurement-notes">
                            <strong><i class="fa fa-sticky-note"></i> Notes</strong>
                            <p><?php echo nl2br(htmlspecialchars($measurement->notes)); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <div id="measurements-pagination" class="pagination-container"></div>

        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-history"></i>
                </div>
                <div class="empty-state-title">Aucune mesure enregistrée</div>
                <div class="empty-state-text">
                    Ajoutez votre première mesure pour commencer à suivre votre évolution.
                </div>
                <div style="margin-top: 24px;">
                    <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="btn-add-measure">
                        <i class="fa fa-plus-circle"></i> Ajouter une Mesure
                    </a>
                </div>
            </div>
        <?php } ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Weight Evolution Chart
        <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
        var ctx = document.getElementById('weightChart').getContext('2d');
        var weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($weight_evolution as $point) {
                        echo '"' . date('d/m/Y', strtotime($point->measurement_date)) . '",';
                    } ?>
                ],
                datasets: [{
                    label: 'Poids (kg)',
                    data: [
                        <?php foreach ($weight_evolution as $point) {
                            echo $point->weight . ',';
                        } ?>
                    ],
                    borderColor: '#01807B',
                    backgroundColor: 'rgba(1, 128, 123, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#01807B',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
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
        <?php } ?>
    });

    // Haptic feedback
    if ('vibrate' in navigator) {
        document.querySelectorAll('.btn-add-measure').forEach(function(btn) {
            btn.addEventListener('click', function() {
                navigator.vibrate(10);
            });
        });
    }
    </script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
