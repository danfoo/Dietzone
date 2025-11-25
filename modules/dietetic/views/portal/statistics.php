<?php
$active_page = 'statistics';
$page_title = 'Mes Statistiques';
$this->load->view('portal/includes/portal_header');
?>

<!-- Include Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8f9fa;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
}

.stats-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Page Header */
.stats-header {
    background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 24px;
    color: white;
    box-shadow: 0 8px 24px rgba(1, 128, 123, 0.25);
}

.stats-header h1 {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.stats-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

/* Period Filter */
.period-filter {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.period-filter label {
    font-weight: 600;
    color: #495057;
    margin-right: 8px;
}

.period-btn {
    padding: 8px 16px;
    border: 2px solid #e9ecef;
    background: white;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all 0.3s;
}

.period-btn:hover {
    border-color: #01807B;
    color: #01807B;
}

.period-btn.active {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    border-color: #01807B;
    color: white;
}

/* Stats Cards Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 2px solid #f1f3f5;
    transition: all 0.3s;
}

.stat-card:hover {
    border-color: #01807B;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.15);
}

.stat-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

.stat-icon.weight { background: linear-gradient(135deg, #01807B 0%, #019B95 100%); }
.stat-icon.bmi { background: linear-gradient(135deg, #F3911D 0%, #ff9f3d 100%); }
.stat-icon.waist { background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%); }
.stat-icon.goal { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }

.stat-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #212529;
    margin: 8px 0;
}

.stat-change {
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.stat-change.positive { color: #48bb78; }
.stat-change.negative { color: #f56565; }
.stat-change.neutral { color: #6c757d; }

/* Progress Bar */
.progress-bar-container {
    margin-top: 12px;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
    transition: width 0.8s ease-out;
    border-radius: 10px;
}

.progress-text {
    font-size: 11px;
    color: #6c757d;
    margin-top: 6px;
    text-align: right;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 2px solid #f1f3f5;
}

.chart-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-card-title i {
    color: #01807B;
}

.chart-container {
    position: relative;
    height: 300px;
}

.chart-container.large {
    height: 400px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
    margin-bottom: 20px;
}

.empty-state .btn {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.empty-state .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.25);
}

/* Loading State */
.loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: #6c757d;
}

.loading i {
    font-size: 32px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .stats-container {
        padding: 16px;
    }

    .stats-header {
        padding: 20px;
    }

    .stats-header h1 {
        font-size: 20px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .period-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .period-btn {
        width: 100%;
        text-align: center;
    }

    .chart-container {
        height: 250px;
    }

    .chart-container.large {
        height: 300px;
    }
}
</style>

<div class="stats-container">
    <!-- Header -->
    <div class="stats-header">
        <h1>
            <i class="fa fa-line-chart"></i>
            Mes Statistiques
        </h1>
        <p>Suivez votre évolution et vos progrès vers votre objectif</p>
    </div>

    <!-- Period Filter -->
    <div class="period-filter">
        <label><i class="fa fa-calendar"></i> Période :</label>
        <button class="period-btn" data-period="1month">1 mois</button>
        <button class="period-btn" data-period="3months">3 mois</button>
        <button class="period-btn active" data-period="6months">6 mois</button>
        <button class="period-btn" data-period="1year">1 an</button>
        <button class="period-btn" data-period="all">Tout</button>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="loading">
        <i class="fa fa-spinner fa-spin"></i>
        <span style="margin-left: 12px;">Chargement de vos statistiques...</span>
    </div>

    <!-- Content (hidden initially) -->
    <div id="statsContent" style="display: none;">
        <!-- Stats Cards -->
        <div class="stats-grid" id="statsCards"></div>

        <!-- Weight Chart -->
        <div class="chart-card">
            <div class="chart-card-title">
                <i class="fa fa-line-chart"></i>
                Évolution du poids
            </div>
            <div class="chart-container large">
                <canvas id="weightChart"></canvas>
            </div>
        </div>

        <!-- BMI Chart -->
        <div class="chart-card">
            <div class="chart-card-title">
                <i class="fa fa-bar-chart"></i>
                Évolution de l'IMC
            </div>
            <div class="chart-container">
                <canvas id="bmiChart"></canvas>
            </div>
        </div>

        <!-- Body Measurements Chart -->
        <div class="chart-card" id="measurementsChartCard" style="display: none;">
            <div class="chart-card-title">
                <i class="fa fa-expand"></i>
                Mesures corporelles
            </div>
            <div class="chart-container">
                <canvas id="measurementsChart"></canvas>
            </div>
        </div>

        <!-- Compliance Chart -->
        <div class="chart-card" id="complianceChartCard" style="display: none;">
            <div class="chart-card-title">
                <i class="fa fa-check-circle"></i>
                Suivi alimentaire
            </div>
            <div class="chart-container">
                <canvas id="complianceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-state" style="display: none;">
        <i class="fa fa-chart-line"></i>
        <h3>Aucune donnée disponible</h3>
        <p>Commencez à enregistrer vos mesures pour voir votre évolution</p>
        <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="btn">
            <i class="fa fa-plus"></i> Ajouter une mesure
        </a>
    </div>
</div>

<script>
let currentPeriod = '6months';
let charts = {};

// Chart.js default config
Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif";
Chart.defaults.plugins.legend.display = true;
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(33, 37, 41, 0.95)';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 8;

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics(currentPeriod);

    // Period filter buttons
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = this.dataset.period;
            loadStatistics(currentPeriod);
        });
    });
});

async function loadStatistics(period) {
    // Show loading
    document.getElementById('loadingState').style.display = 'flex';
    document.getElementById('statsContent').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';

    try {
        const response = await fetch('<?php echo site_url('dietetic/portal/api_get_evolution_data'); ?>?period=' + period);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Erreur lors du chargement');
        }

        if (!data.measurements || data.measurements.length === 0) {
            // No data - show empty state
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            return;
        }

        // Hide loading, show content
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('statsContent').style.display = 'block';

        // Render stats cards
        renderStatsCards(data);

        // Render charts
        renderWeightChart(data.measurements, data.stats);
        renderBMIChart(data.measurements);
        renderMeasurementsChart(data.measurements);
        renderComplianceChart(data.compliance, data.compliance_rate);

    } catch (error) {
        console.error('Error loading statistics:', error);
        document.getElementById('loadingState').innerHTML = '<div style="color: #dc3545;"><i class="fa fa-exclamation-triangle"></i> Erreur lors du chargement des statistiques</div>';
    }
}

function renderStatsCards(data) {
    const container = document.getElementById('statsCards');
    const stats = data.stats;
    let html = '';

    // Weight Card
    if (stats.weight) {
        const change = stats.weight.change;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon weight"><i class="fa fa-balance-scale"></i></div>
                    <div class="stat-label">Poids</div>
                </div>
                <div class="stat-value">${stats.weight.current} kg</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${Math.abs(change).toFixed(1)} kg depuis le début
                </div>
            </div>
        `;
    }

    // BMI Card
    if (stats.bmi) {
        const change = stats.bmi.change;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon bmi"><i class="fa fa-tachometer"></i></div>
                    <div class="stat-label">IMC</div>
                </div>
                <div class="stat-value">${stats.bmi.current.toFixed(1)}</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${Math.abs(change).toFixed(1)} depuis le début
                </div>
            </div>
        `;
    }

    // Waist Card
    if (stats.waist) {
        const change = stats.waist.change;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon waist"><i class="fa fa-expand"></i></div>
                    <div class="stat-label">Tour de taille</div>
                </div>
                <div class="stat-value">${stats.waist.current} cm</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${Math.abs(change).toFixed(1)} cm depuis le début
                </div>
            </div>
        `;
    }

    // Goal Progress Card
    if (stats.goal_progress) {
        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon goal"><i class="fa fa-bullseye"></i></div>
                    <div class="stat-label">Progrès vers l'objectif</div>
                </div>
                <div class="stat-value">${stats.goal_progress.percent}%</div>
                <div class="stat-change neutral">
                    <i class="fa fa-flag-checkered"></i>
                    Encore ${stats.goal_progress.remaining.toFixed(1)} kg
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: ${Math.min(stats.goal_progress.percent, 100)}%"></div>
                    </div>
                    <div class="progress-text">${Math.min(stats.goal_progress.percent, 100)}% accompli</div>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

function renderWeightChart(measurements, stats) {
    const ctx = document.getElementById('weightChart');

    // Destroy existing chart
    if (charts.weight) {
        charts.weight.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measured_at);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const weights = measurements.map(m => parseFloat(m.weight));
    const target = stats.weight && stats.weight.target ? parseFloat(stats.weight.target) : null;

    const datasets = [{
        label: 'Poids (kg)',
        data: weights,
        borderColor: '#01807B',
        backgroundColor: 'rgba(1, 128, 123, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBackgroundColor: '#01807B',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
    }];

    // Add target line if exists
    if (target) {
        datasets.push({
            label: 'Objectif',
            data: Array(weights.length).fill(target),
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.05)',
            borderWidth: 2,
            borderDash: [10, 5],
            fill: false,
            pointRadius: 0
        });
    }

    charts.weight = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + ' kg';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return value + ' kg';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function renderBMIChart(measurements) {
    const ctx = document.getElementById('bmiChart');

    if (charts.bmi) {
        charts.bmi.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measured_at);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const bmis = measurements.map(m => parseFloat(m.bmi));

    charts.bmi = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'IMC',
                data: bmis,
                borderColor: '#F3911D',
                backgroundColor: 'rgba(243, 145, 29, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#F3911D',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const bmi = context.parsed.y;
                            let category = '';
                            if (bmi < 18.5) category = ' (Insuffisance pondérale)';
                            else if (bmi < 25) category = ' (Normal)';
                            else if (bmi < 30) category = ' (Surpoids)';
                            else category = ' (Obésité)';
                            return 'IMC: ' + bmi.toFixed(1) + category;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    suggestedMin: 15,
                    suggestedMax: 40,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function renderMeasurementsChart(measurements) {
    // Check if we have waist or hip measurements
    const hasWaist = measurements.some(m => m.waist_circumference);
    const hasHip = measurements.some(m => m.hip_circumference);

    if (!hasWaist && !hasHip) {
        document.getElementById('measurementsChartCard').style.display = 'none';
        return;
    }

    document.getElementById('measurementsChartCard').style.display = 'block';

    const ctx = document.getElementById('measurementsChart');

    if (charts.measurements) {
        charts.measurements.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measured_at);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const datasets = [];

    if (hasWaist) {
        datasets.push({
            label: 'Tour de taille (cm)',
            data: measurements.map(m => m.waist_circumference ? parseFloat(m.waist_circumference) : null),
            borderColor: '#9f7aea',
            backgroundColor: 'rgba(159, 122, 234, 0.1)',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            spanGaps: true
        });
    }

    if (hasHip) {
        datasets.push({
            label: 'Tour de hanches (cm)',
            data: measurements.map(m => m.hip_circumference ? parseFloat(m.hip_circumference) : null),
            borderColor: '#ed64a6',
            backgroundColor: 'rgba(237, 100, 166, 0.1)',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            spanGaps: true
        });
    }

    charts.measurements = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return value + ' cm';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function renderComplianceChart(compliance, rate) {
    if (!compliance || compliance.length === 0) {
        document.getElementById('complianceChartCard').style.display = 'none';
        return;
    }

    document.getElementById('complianceChartCard').style.display = 'block';

    const ctx = document.getElementById('complianceChart');

    if (charts.compliance) {
        charts.compliance.destroy();
    }

    // Group by week
    const weeklyData = {};
    compliance.forEach(item => {
        const date = new Date(item.date);
        const weekStart = new Date(date.setDate(date.getDate() - date.getDay()));
        const weekKey = weekStart.toISOString().split('T')[0];

        if (!weeklyData[weekKey]) {
            weeklyData[weekKey] = 0;
        }
        weeklyData[weekKey] += parseInt(item.count);
    });

    const labels = Object.keys(weeklyData).map(date => {
        const d = new Date(date);
        return 'Sem ' + d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const data = Object.values(weeklyData);

    charts.compliance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Entrées par semaine',
                data: data,
                backgroundColor: 'rgba(72, 187, 120, 0.8)',
                borderColor: '#48bb78',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: `Taux de suivi: ${rate}%`,
                    font: {
                        size: 14,
                        weight: '600'
                    },
                    color: '#48bb78',
                    padding: {
                        bottom: 15
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
