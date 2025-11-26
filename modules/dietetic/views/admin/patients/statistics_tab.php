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

/* Add Note Button */
.btn-add-note {
    margin-left: auto;
    padding: 8px 16px;
    background: #01807B;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-add-note:hover {
    background: #019B95;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(1, 128, 123, 0.2);
}

.btn-add-note i {
    font-size: 12px;
}

/* Note Modal */
.note-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.note-modal-content {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: slideUp 0.3s;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.note-modal-header {
    padding: 20px 24px;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.note-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.note-modal-header h3 i {
    color: #01807B;
}

.note-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
}

.note-modal-close:hover {
    background: #f0f0f0;
    color: #212529;
}

.note-modal-body {
    padding: 24px;
}

.note-modal-body .form-group {
    margin-bottom: 20px;
}

.note-modal-body label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.note-modal-body .form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
}

.note-modal-body .form-control:focus {
    outline: none;
    border-color: #01807B;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.note-modal-body textarea.form-control {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.note-modal-footer {
    padding: 16px 24px;
    border-top: 2px solid #f0f0f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.note-modal-footer .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.note-modal-footer .btn-cancel {
    background: #e9ecef;
    color: #495057;
}

.note-modal-footer .btn-cancel:hover {
    background: #dee2e6;
}

.note-modal-footer .btn-primary {
    background: #01807B;
    color: white;
}

.note-modal-footer .btn-primary:hover {
    background: #019B95;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(1, 128, 123, 0.2);
}

.note-item-delete {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: all 0.3s;
    opacity: 0;
}

.note-item:hover .note-item-delete {
    opacity: 1;
}

.note-item-delete:hover {
    background: rgba(220, 53, 69, 0.1);
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
                <button id="adminAddNoteBtn" class="btn-add-note">
                    <i class="fa fa-plus"></i> Ajouter une note
                </button>
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

<!-- Note Modal -->
<div id="adminNoteModal" class="note-modal" style="display: none;">
    <div class="note-modal-content">
        <div class="note-modal-header">
            <h3><i class="fa fa-sticky-note"></i> Ajouter une note</h3>
            <button class="note-modal-close" onclick="closeAdminNoteModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="note-modal-body">
            <div class="form-group">
                <label for="adminNoteDate">Date</label>
                <input type="date" id="adminNoteDate" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="adminNoteText">Note</label>
                <textarea id="adminNoteText" class="form-control" rows="3" placeholder="Ex: Patient très motivé, changement d'approche nutritionnelle, événement important..." required></textarea>
            </div>
            <div class="form-group">
                <label for="adminNoteIcon">Icône (optionnel)</label>
                <select id="adminNoteIcon" class="form-control">
                    <option value="fa-sticky-note">📝 Note</option>
                    <option value="fa-check-circle">✓ Succès</option>
                    <option value="fa-exclamation-triangle">⚠ Attention</option>
                    <option value="fa-star">⭐ Milestone</option>
                    <option value="fa-heart">❤️ Santé</option>
                    <option value="fa-comments">💬 Consultation</option>
                    <option value="fa-chart-line">📈 Progrès</option>
                </select>
            </div>
        </div>
        <div class="note-modal-footer">
            <button class="btn btn-cancel" onclick="closeAdminNoteModal()">Annuler</button>
            <button class="btn btn-primary" onclick="saveAdminNote()">
                <i class="fa fa-save"></i> Enregistrer
            </button>
        </div>
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

// Period filter and Add Note button
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('adminPeriodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            adminCurrentPeriod = this.value;
            loadAdminStatistics(adminCurrentPeriod);
        });
    }

    // Add note button
    const addNoteBtn = document.getElementById('adminAddNoteBtn');
    if (addNoteBtn) {
        addNoteBtn.addEventListener('click', openAdminNoteModal);
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

function renderAdminStatsCards(data) {
    const container = document.getElementById('adminStatsCards');
    const stats = data.stats;
    let html = '';

    // Weight Card
    if (stats.weight && stats.weight.current) {
        const change = parseFloat(stats.weight.change) || 0;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon weight"><i class="fa fa-balance-scale"></i></div>
                    <div class="stat-label">Poids</div>
                </div>
                <div class="stat-value">${safeFixed(stats.weight.current, 1)} kg</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${safeFixed(Math.abs(change), 1)} kg depuis le début
                </div>
            </div>
        `;
    }

    // BMI Card
    if (stats.bmi && stats.bmi.current) {
        const change = parseFloat(stats.bmi.change) || 0;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon bmi"><i class="fa fa-tachometer"></i></div>
                    <div class="stat-label">IMC</div>
                </div>
                <div class="stat-value">${safeFixed(stats.bmi.current, 1)}</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${safeFixed(Math.abs(change), 1)} depuis le début
                </div>
            </div>
        `;
    }

    // Waist Card
    if (stats.waist && stats.waist.current) {
        const change = parseFloat(stats.waist.change) || 0;
        const changeClass = change < 0 ? 'positive' : change > 0 ? 'negative' : 'neutral';
        const changeIcon = change < 0 ? 'fa-arrow-down' : change > 0 ? 'fa-arrow-up' : 'fa-minus';

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon waist"><i class="fa fa-expand"></i></div>
                    <div class="stat-label">Tour de taille</div>
                </div>
                <div class="stat-value">${safeFixed(stats.waist.current, 1)} cm</div>
                <div class="stat-change ${changeClass}">
                    <i class="fa ${changeIcon}"></i>
                    ${safeFixed(Math.abs(change), 1)} cm depuis le début
                </div>
            </div>
        `;
    }

    // Goal Progress Card
    if (stats.goal_progress && stats.goal_progress.percent !== undefined) {
        const remaining = parseFloat(stats.goal_progress.remaining) || 0;
        const percent = parseFloat(stats.goal_progress.percent) || 0;

        html += `
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-icon goal"><i class="fa fa-bullseye"></i></div>
                    <div class="stat-label">Progrès vers l'objectif</div>
                </div>
                <div class="stat-value">${safeFixed(percent, 1)}%</div>
                <div class="stat-change neutral">
                    <i class="fa fa-flag-checkered"></i>
                    Encore ${safeFixed(remaining, 1)} kg
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: ${Math.min(percent, 100)}%"></div>
                    </div>
                    <div class="progress-text">${Math.min(percent, 100).toFixed(0)}% accompli</div>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

function renderAdminInsights(insights) {
    const container = document.getElementById('adminInsightsContainer');

    if (!insights || insights.length === 0) {
        container.style.display = 'none';
        return;
    }

    let html = '<div class="insights-grid">';

    insights.forEach(insight => {
        html += `
            <div class="insight-card ${insight.type}">
                <div class="insight-icon">
                    <i class="fa ${insight.icon}"></i>
                </div>
                <div class="insight-message">${insight.message}</div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
    container.style.display = 'block';
}

function renderAdminWeightChart(measurements, stats, trends) {
    const ctx = document.getElementById('adminWeightChart');

    // Destroy existing chart
    if (adminCharts.weight) {
        adminCharts.weight.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measurement_date);
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

    // Add trend line if exists
    if (trends && trends.trend_line && trends.trend_line.length > 0) {
        const trendValues = trends.trend_line.map(t => t.value);
        datasets.push({
            label: 'Tendance',
            data: trendValues,
            borderColor: '#9f7aea',
            backgroundColor: 'rgba(159, 122, 234, 0.05)',
            borderWidth: 2,
            borderDash: [5, 5],
            fill: false,
            pointRadius: 0,
            tension: 0
        });
    }

    adminCharts.weight = new Chart(ctx, {
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

function renderAdminBMIChart(measurements) {
    const ctx = document.getElementById('adminBMIChart');

    if (adminCharts.bmi) {
        adminCharts.bmi.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measurement_date);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const bmis = measurements.map(m => parseFloat(m.bmi));

    adminCharts.bmi = new Chart(ctx, {
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

function renderAdminMeasurementsChart(measurements) {
    // Check if we have waist or hip measurements
    const hasWaist = measurements.some(m => m.waist_circumference);
    const hasHip = measurements.some(m => m.hip_circumference);

    if (!hasWaist && !hasHip) {
        document.getElementById('adminMeasurementsChartCard').style.display = 'none';
        return;
    }

    document.getElementById('adminMeasurementsChartCard').style.display = 'block';

    const ctx = document.getElementById('adminMeasurementsChart');

    if (adminCharts.measurements) {
        adminCharts.measurements.destroy();
    }

    const labels = measurements.map(m => {
        const date = new Date(m.measurement_date);
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

    adminCharts.measurements = new Chart(ctx, {
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

function renderAdminComplianceChart(compliance, rate) {
    if (!compliance || compliance.length === 0) {
        document.getElementById('adminComplianceChartCard').style.display = 'none';
        return;
    }

    document.getElementById('adminComplianceChartCard').style.display = 'block';

    const ctx = document.getElementById('adminComplianceChart');

    if (adminCharts.compliance) {
        adminCharts.compliance.destroy();
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

    adminCharts.compliance = new Chart(ctx, {
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

function renderAdminNotes(notes) {
    const container = document.getElementById('adminNotesContainer');

    if (!notes || notes.length === 0) {
        container.innerHTML = '<div class="notes-empty"><i class="fa fa-sticky-note-o"></i><br>Aucune note pour cette période.</div>';
        return;
    }

    let html = '';
    notes.forEach(note => {
        const date = new Date(note.note_date);
        const formattedDate = date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        const createdBy = note.created_by_type === 'staff' ? 'Diététicien' : 'Patient';

        html += `
            <div class="note-item">
                <div class="note-item-icon">
                    <i class="fa ${note.icon || 'fa-sticky-note'}"></i>
                </div>
                <div class="note-item-content">
                    <div class="note-item-date">${formattedDate}</div>
                    <div class="note-item-text">${note.note_text}</div>
                    <div class="note-item-author">Par: ${createdBy}</div>
                </div>
                <button class="note-item-delete" onclick="deleteAdminNote(${note.id})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Note Modal Functions
function openAdminNoteModal() {
    document.getElementById('adminNoteModal').style.display = 'flex';
    // Set default date to today
    document.getElementById('adminNoteDate').valueAsDate = new Date();
}

function closeAdminNoteModal() {
    document.getElementById('adminNoteModal').style.display = 'none';
    // Reset form
    document.getElementById('adminNoteDate').value = '';
    document.getElementById('adminNoteText').value = '';
    document.getElementById('adminNoteIcon').value = 'fa-sticky-note';
}

async function saveAdminNote() {
    const noteDate = document.getElementById('adminNoteDate').value;
    const noteText = document.getElementById('adminNoteText').value;
    const noteIcon = document.getElementById('adminNoteIcon').value;

    if (!noteDate || !noteText.trim()) {
        alert('Veuillez remplir la date et le texte de la note');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('patient_id', adminPatientId);
        formData.append('note_date', noteDate);
        formData.append('note_text', noteText.trim());
        formData.append('note_icon', noteIcon);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        const response = await fetch(admin_url + 'dietetic/patients/api_add_statistic_note', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            closeAdminNoteModal();
            // Reload statistics to show new note
            loadAdminStatistics(adminCurrentPeriod);
        } else {
            alert('Erreur: ' + (data.message || 'Impossible d\'ajouter la note'));
        }
    } catch (error) {
        console.error('Error saving note:', error);
        alert('Erreur lors de l\'enregistrement de la note');
    }
}

async function deleteAdminNote(noteId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('note_id', noteId);
        formData.append('patient_id', adminPatientId);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        const response = await fetch(admin_url + 'dietetic/patients/api_delete_statistic_note', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Reload statistics to update notes
            loadAdminStatistics(adminCurrentPeriod);
        } else {
            alert('Erreur: ' + (data.message || 'Impossible de supprimer la note'));
        }
    } catch (error) {
        console.error('Error deleting note:', error);
        alert('Erreur lors de la suppression de la note');
    }
}
</script>
