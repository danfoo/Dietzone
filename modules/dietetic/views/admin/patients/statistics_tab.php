<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
/* Statistics Tab Styles */
.admin-stats-container {
    padding: 0;
}

/* Period Filter */
.period-filter {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.period-filter label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
    font-size: 14px;
}

.period-filter label i {
    color: #01807B;
    margin-right: 6px;
}

.period-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    background: white;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all 0.3s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2301807B' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 44px;
}

.period-select:hover {
    border-color: #01807B;
    box-shadow: 0 2px 8px rgba(1, 128, 123, 0.15);
}

.period-select:focus {
    outline: none;
    border-color: #01807B;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
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
    transition: all 0.3s;
}

.stat-card:hover {
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
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.stat-change {
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.stat-change.positive { color: #48bb78; }
.stat-change.negative { color: #e74c3c; }
.stat-change.neutral { color: #6c757d; }

.progress-bar-container {
    margin-top: 12px;
}

.progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
    transition: width 0.6s ease;
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

/* Insights */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.insight-card {
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    border-left: 4px solid;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s;
}

.insight-card:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.insight-card.positive {
    border-left-color: #48bb78;
    background: linear-gradient(90deg, rgba(72, 187, 120, 0.05) 0%, white 100%);
}

.insight-card.warning {
    border-left-color: #F3911D;
    background: linear-gradient(90deg, rgba(243, 145, 29, 0.05) 0%, white 100%);
}

.insight-card.info {
    border-left-color: #4299e1;
    background: linear-gradient(90deg, rgba(66, 153, 225, 0.05) 0%, white 100%);
}

.insight-card.neutral {
    border-left-color: #6c757d;
    background: linear-gradient(90deg, rgba(108, 117, 125, 0.05) 0%, white 100%);
}

.insight-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.insight-card.positive .insight-icon {
    background: rgba(72, 187, 120, 0.1);
    color: #48bb78;
}

.insight-card.warning .insight-icon {
    background: rgba(243, 145, 29, 0.1);
    color: #F3911D;
}

.insight-card.info .insight-icon {
    background: rgba(66, 153, 225, 0.1);
    color: #4299e1;
}

.insight-card.neutral .insight-icon {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.insight-message {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
    line-height: 1.5;
}

/* Loading State */
.loading-state {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    color: #6c757d;
    font-size: 16px;
}

.loading-state i {
    font-size: 32px;
    margin-right: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    color: #cbd5e0;
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

/* Notes Section */
.notes-container {
    min-height: 50px;
}

.note-item {
    background: linear-gradient(90deg, rgba(1, 128, 123, 0.05) 0%, white 100%);
    border-left: 4px solid #01807B;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
}

.note-item:hover {
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.note-item-icon {
    width: 36px;
    height: 36px;
    background: rgba(1, 128, 123, 0.1);
    color: #01807B;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.note-item-content {
    flex: 1;
}

.note-item-date {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 4px;
}

.note-item-text {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.note-item-author {
    font-size: 11px;
    color: #01807B;
    font-weight: 600;
    margin-top: 4px;
}

.notes-empty {
    text-align: center;
    padding: 30px;
    color: #6c757d;
    font-size: 14px;
}
</style>

<div class="admin-stats-container">
    <!-- Period Filter -->
    <div class="period-filter">
        <label for="adminPeriodSelect"><i class="fa fa-calendar"></i> Période d'analyse</label>
        <select id="adminPeriodSelect" class="period-select">
            <option value="1month">1 mois</option>
            <option value="3months">3 mois</option>
            <option value="6months" selected>6 mois</option>
            <option value="1year">1 an</option>
            <option value="all">Toute la période</option>
        </select>
    </div>

    <!-- Loading State -->
    <div id="adminLoadingState" class="loading-state">
        <i class="fa fa-spinner fa-spin"></i>
        <span>Chargement des statistiques...</span>
    </div>

    <!-- Content -->
    <div id="adminStatsContent" style="display: none;">
        <!-- Stats Cards -->
        <div class="stats-grid" id="adminStatsCards"></div>

        <!-- Insights -->
        <div id="adminInsightsContainer" style="display: none;"></div>

        <!-- Weight Chart -->
        <div class="chart-card">
            <div class="chart-card-title">
                <i class="fa fa-line-chart"></i>
                Évolution du poids
            </div>
            <div class="chart-container large">
                <canvas id="adminWeightChart"></canvas>
            </div>
        </div>

        <!-- BMI Chart -->
        <div class="chart-card">
            <div class="chart-card-title">
                <i class="fa fa-bar-chart"></i>
                Évolution de l'IMC
            </div>
            <div class="chart-container">
                <canvas id="adminBMIChart"></canvas>
            </div>
        </div>

        <!-- Body Measurements Chart -->
        <div class="chart-card" id="adminMeasurementsChartCard" style="display: none;">
            <div class="chart-card-title">
                <i class="fa fa-expand"></i>
                Mesures corporelles
            </div>
            <div class="chart-container">
                <canvas id="adminMeasurementsChart"></canvas>
            </div>
        </div>

        <!-- Compliance Chart -->
        <div class="chart-card" id="adminComplianceChartCard" style="display: none;">
            <div class="chart-card-title">
                <i class="fa fa-check-circle"></i>
                Suivi alimentaire
            </div>
            <div class="chart-container">
                <canvas id="adminComplianceChart"></canvas>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="chart-card">
            <div class="chart-card-title">
                <i class="fa fa-sticky-note"></i>
                Notes du patient
            </div>
            <div id="adminNotesContainer" class="notes-container"></div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="adminEmptyState" class="empty-state" style="display: none;">
        <i class="fa fa-area-chart"></i>
        <h3>Aucune donnée disponible</h3>
        <p>Le patient n'a pas encore de mesures enregistrées</p>
    </div>
</div>

<script>
// Patient ID from PHP
const adminPatientId = <?php echo $patient->id; ?>;
let adminCurrentPeriod = '6months';
let adminCharts = {};

// Chart.js default config
if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "-apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif";
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(33, 37, 41, 0.95)';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
}

// Load statistics when tab is clicked
$('a[href="#tab-statistics"]').on('shown.bs.tab', function () {
    // Only load once
    if (!window.adminStatsLoaded) {
        loadAdminStatistics(adminCurrentPeriod);
        window.adminStatsLoaded = true;
    }
});

// Period filter
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('adminPeriodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            adminCurrentPeriod = this.value;
            loadAdminStatistics(adminCurrentPeriod);
        });
    }
});

async function loadAdminStatistics(period) {
    // Show loading
    document.getElementById('adminLoadingState').style.display = 'flex';
    document.getElementById('adminStatsContent').style.display = 'none';
    document.getElementById('adminEmptyState').style.display = 'none';

    try {
        const response = await fetch(admin_url + 'dietetic/patients/api_get_patient_statistics/' + adminPatientId + '?period=' + period);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Erreur lors du chargement');
        }

        if (!data.measurements || data.measurements.length === 0) {
            // No data - show empty state
            document.getElementById('adminLoadingState').style.display = 'none';
            document.getElementById('adminEmptyState').style.display = 'block';
            return;
        }

        // Hide loading, show content
        document.getElementById('adminLoadingState').style.display = 'none';
        document.getElementById('adminStatsContent').style.display = 'block';

        // Render stats cards
        renderAdminStatsCards(data);

        // Render insights
        renderAdminInsights(data.insights || []);

        // Render charts
        renderAdminWeightChart(data.measurements, data.stats, data.trends || {});
        renderAdminBMIChart(data.measurements);
        renderAdminMeasurementsChart(data.measurements);
        renderAdminComplianceChart(data.compliance, data.compliance_rate);

        // Render notes
        renderAdminNotes(data.notes || []);

    } catch (error) {
        console.error('Error loading statistics:', error);
        document.getElementById('adminLoadingState').innerHTML = '<div style="color: #dc3545;"><i class="fa fa-exclamation-triangle"></i> Erreur lors du chargement des statistiques</div>';
    }
}

// Helper function to safely format numbers
function safeFixed(value, decimals = 1) {
    if (value === null || value === undefined || value === '') return '0.0';
    const num = parseFloat(value);
    return isNaN(num) ? '0.0' : num.toFixed(decimals);
}

// Render functions will be continued in next part...
</script>
