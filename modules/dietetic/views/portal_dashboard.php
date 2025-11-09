<?php
/**
 * Portal Patient Dashboard
 *
 * @author Eric Gilles SAGNA
 * @website https://maestrodan.art
 */
$active_page = 'dashboard';
$page_title = 'Mon Programme';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Fix Bootstrap progress bar height conflict */
.stat-card.progress {
    height: auto !important;
}

/* Modern Stats Cards with Animations */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px 20px;
    text-align: center;
    border: none;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s;
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
}

.stat-card .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    position: relative;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.stat-card.weight {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

.stat-card.weight .stat-icon {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
    box-shadow: 0 8px 16px rgba(66, 153, 225, 0.3);
}

.stat-card.target {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
}

.stat-card.target .stat-icon {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    box-shadow: 0 8px 16px rgba(72, 187, 120, 0.3);
}

.stat-card.bmi {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
}

.stat-card.bmi .stat-icon {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    color: white;
    box-shadow: 0 8px 16px rgba(237, 137, 54, 0.3);
}

.stat-card.progress {
    background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
}

.stat-card.progress .stat-icon {
    background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
    color: white;
    box-shadow: 0 8px 16px rgba(159, 122, 234, 0.3);
}

.stat-card .stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #212529;
    margin: 0 0 6px 0;
    line-height: 1;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-card .stat-label {
    font-size: 11px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value.text-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Program Card - Modern Design with Progress Ring */
.program-card {
    background: linear-gradient(135deg, #2c5f6f 0%, #1e4a5a 100%);
    border-radius: 24px;
    padding: 32px 28px;
    margin-bottom: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(44, 95, 111, 0.3);
    min-height: 280px;
    display: flex;
    flex-direction: column;
}

.program-card::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
}

.program-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.program-info-left {
    flex: 1;
}

.program-greeting {
    font-size: 15px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.program-title {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
    margin: 0 0 16px 0;
}

.program-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #f5a54a 0%, #e8944a 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(245, 165, 74, 0.3);
}

.program-view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(245, 165, 74, 0.4);
    color: white;
    text-decoration: none;
}

.program-progress-ring {
    position: relative;
    width: 100px;
    height: 100px;
}

.progress-ring-bg {
    fill: none;
    stroke: rgba(255, 255, 255, 0.15);
    stroke-width: 8;
}

.progress-ring-fill {
    fill: none;
    stroke: #f5a54a;
    stroke-width: 8;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 1s ease-out;
}

.progress-ring-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: 700;
    color: #f5a54a;
}

.program-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(245, 165, 74, 0.2);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #f5a54a;
    margin-top: 12px;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.program-body {
    position: relative;
    z-index: 1;
}

.program-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    border-left: 3px solid #f5a54a;
    backdrop-filter: blur(10px);
}

.info-item i {
    font-size: 20px;
    color: #f5a54a;
    margin-top: 2px;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.info-value {
    font-size: 16px;
    color: white;
    font-weight: 700;
}

.program-objective {
    background: rgba(255, 255, 255, 0.1);
    padding: 18px;
    border-radius: 12px;
    border-left: 3px solid #f5a54a;
    margin: 20px 0;
    backdrop-filter: blur(10px);
}

.program-objective strong {
    display: block;
    color: #f5a54a;
    font-size: 13px;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 700;
}

.program-objective p {
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.7;
    margin: 0;
    font-size: 14px;
}

/* Meal Plans Section */
.meal-plans-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: white;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #f5a54a;
}

.meal-plan-item {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 12px;
    transition: all 0.3s;
    backdrop-filter: blur(10px);
}

.meal-plan-item:hover {
    border-color: #f5a54a;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(245, 165, 74, 0.2);
    background: rgba(255, 255, 255, 0.15);
}

.meal-plan-name {
    font-size: 15px;
    font-weight: 600;
    color: white;
    margin-bottom: 12px;
}

.meal-plan-name i {
    color: #f5a54a;
    margin-right: 8px;
}

.meal-plan-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-flat {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-flat-primary {
    background: linear-gradient(135deg, #f5a54a 0%, #e8944a 100%);
    color: white;
    box-shadow: 0 3px 10px rgba(245, 165, 74, 0.3);
}

.btn-flat-primary:hover {
    background: linear-gradient(135deg, #e8944a 0%, #d67f3a 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 165, 74, 0.4);
    color: white;
    text-decoration: none;
}

.btn-flat-success {
    background: #48bb78;
    color: white;
}

.btn-flat-success:hover {
    background: #38a169;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    color: white;
    text-decoration: none;
}

/* Single Consultation Card */
.consultations-section {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 30px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
}

.consultations-header {
    margin-bottom: 20px;
}

.consultations-header h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.consultations-header h4 i {
    color: #2c5f6f;
}

.consultation-card {
    background: linear-gradient(135deg, #fef4e8 0%, #f5e6d3 100%);
    border-radius: 16px;
    padding: 24px;
    position: relative;
    transition: all 0.3s;
    border: 2px solid transparent;
    margin-bottom: 16px;
}

.consultation-time {
    font-size: 14px;
    font-weight: 600;
    color: #8b6f47;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.consultation-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.consultation-type-label {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 16px;
}

.consultation-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: white;
    color: #2c5f6f;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.consultation-view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #2c5f6f 0%, #1e4a5a 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(44, 95, 111, 0.3);
    margin-top: 12px;
}

.consultation-view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(44, 95, 111, 0.4);
    color: white;
    text-decoration: none;
}

.empty-consultations {
    text-align: center;
    padding: 32px 20px;
    color: #6c757d;
}

.empty-consultations i {
    font-size: 48px;
    opacity: 0.3;
    margin-bottom: 12px;
}

/* Weight Progress Simple Card */
.weight-progress-card {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 30px;
    text-align: center;
    box-shadow: 0 4px 16px rgba(72, 187, 120, 0.15);
    min-height: 280px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.weight-progress-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.3);
}

.weight-progress-icon i {
    font-size: 40px;
    color: white;
}

.weight-progress-value {
    font-size: 48px;
    font-weight: 800;
    color: #38a169;
    margin-bottom: 8px;
}

.weight-progress-label {
    font-size: 16px;
    color: #2c3e50;
    font-weight: 600;
}

.weight-progress-empty {
    font-size: 16px;
    color: #6c757d;
}

.no-program-alert {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 2px solid #4299e1;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    margin-bottom: 30px;
}

.no-program-alert i {
    font-size: 56px;
    color: #4299e1;
    margin-bottom: 16px;
}

.no-program-alert p {
    font-size: 16px;
    color: #1e40af;
    font-weight: 500;
    margin: 0;
}

/* Floating Action Button with Modal */
.fab-container {
    position: fixed;
    bottom: 35px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1002;
}

.fab-button {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f5a54a 0%, #e8944a 100%);
    color: white;
    border: none;
    box-shadow: 0 8px 24px rgba(245, 165, 74, 0.4);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    transition: all 0.3s;
    animation: pulse-fab 2s infinite;
}

@keyframes pulse-fab {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 8px 24px rgba(245, 165, 74, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 12px 32px rgba(245, 165, 74, 0.6);
    }
}

.fab-button:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(245, 165, 74, 0.6);
}

.fab-button:active {
    transform: scale(0.95);
}

/* Modal for Quick Actions */
.fab-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 1001;
    display: none;
    align-items: center;
    justify-content: center;
}

.fab-modal.active {
    display: flex;
}

.fab-modal-content {
    background: white;
    border-radius: 24px;
    padding: 32px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s;
}

.fab-modal.active .fab-modal-content {
    transform: scale(1);
    opacity: 1;
}

.fab-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.fab-modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.fab-modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f3f5;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.fab-modal-close:hover {
    background: #e9ecef;
    transform: rotate(90deg);
}

.fab-actions-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.fab-action-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.fab-action-item:hover {
    transform: translateX(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.fab-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.fab-action-icon.add {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.fab-action-icon.survey {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.fab-action-icon.consult {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.fab-action-icon.dietitian {
    background: linear-gradient(135deg, #9f7aea 0%, #805ad5 100%);
}

.fab-action-text {
    flex: 1;
}

.fab-action-title {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.fab-action-desc {
    font-size: 12px;
    color: #6c757d;
}

/* Custom Dashboard Footer */
.dashboard-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 75px;
    background: white;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 40px;
}

.dashboard-footer::before {
    content: '';
    position: absolute;
    top: -35px;
    left: 50%;
    transform: translateX(-50%);
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
}

.footer-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    color: #6c757d;
    transition: all 0.3s;
    padding: 8px 24px;
    border-radius: 12px;
}

.footer-link:hover {
    background: #f8f9fa;
    color: #2c5f6f;
    text-decoration: none;
    transform: translateY(-2px);
}

.footer-link.active {
    color: #2c5f6f;
}

.footer-link-icon {
    font-size: 24px;
}

.footer-link-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.footer-spacer {
    width: 100px;
}

/* Progress Ring for Stats */
.stat-progress {
    position: relative;
    width: 56px;
    height: 56px;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dashoffset 0.5s;
    stroke-dasharray: 176;
    stroke-dashoffset: 0;
}

/* Skeleton Loader */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 8px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Fade-in animation */
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

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.stats-grid > * {
    animation: fadeInUp 0.6s ease-out;
}

.stats-grid > *:nth-child(1) { animation-delay: 0.1s; }
.stats-grid > *:nth-child(2) { animation-delay: 0.2s; }
.stats-grid > *:nth-child(3) { animation-delay: 0.3s; }
.stats-grid > *:nth-child(4) { animation-delay: 0.4s; }

/* Tooltip */
.tooltip-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(1, 128, 123, 0.15);
    color: #01807B;
    font-size: 11px;
    margin-left: 5px;
    cursor: help;
}

/* Hide footer on dashboard page */
.app-footer {
    display: none !important;
}

body {
    padding-bottom: 20px !important;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .program-info-grid {
        grid-template-columns: 1fr;
    }

    .meal-plan-actions {
        flex-direction: column;
    }

    .btn-flat {
        width: 100%;
        justify-content: center;
    }

    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .action-btn {
        padding: 16px 12px;
    }
}
</style>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card weight">
        <div class="stat-icon">
            <i class="fa fa-balance-scale"></i>
        </div>
        <div class="stat-value"><?php echo $patient->latest_measurement ? $patient->latest_measurement->weight : '-'; ?></div>
        <div class="stat-label">Poids actuel</div>
    </div>

    <div class="stat-card target">
        <div class="stat-icon">
            <i class="fa fa-bullseye"></i>
        </div>
        <div class="stat-value"><?php echo $patient->target_weight ? $patient->target_weight : '-'; ?></div>
        <div class="stat-label">Objectif</div>
    </div>

    <div class="stat-card bmi">
        <div class="stat-icon">
            <i class="fa fa-heartbeat"></i>
        </div>
        <div class="stat-value"><?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? number_format($patient->latest_measurement->bmi, 1) : '-'; ?></div>
        <div class="stat-label">IMC</div>
    </div>

    <?php if ($weight_progress->weight_change !== null) { ?>
    <div class="stat-card progress">
        <div class="stat-icon">
            <i class="fa fa-line-chart"></i>
        </div>
        <div class="stat-value <?php echo $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
            <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?>
        </div>
        <div class="stat-label">Progression</div>
    </div>
    <?php } ?>
</div>

<!-- Program Card -->
<?php if ($active_program) { ?>
    <?php
    // Calculate program progress
    $progress_percentage = 50; // Default
    if ($active_program->start_date && $active_program->end_date) {
        $start = strtotime($active_program->start_date);
        $end = strtotime($active_program->end_date);
        $now = time();
        if ($now >= $start && $now <= $end) {
            $total_duration = $end - $start;
            $elapsed = $now - $start;
            $progress_percentage = round(($elapsed / $total_duration) * 100);
        } elseif ($now > $end) {
            $progress_percentage = 100;
        }
    }

    // Circle calculation for SVG
    $radius = 42;
    $circumference = 2 * pi() * $radius;
    $stroke_offset = $circumference - ($progress_percentage / 100) * $circumference;
    ?>

<div class="program-card">
    <div class="program-header">
        <div class="program-info-left">
            <div class="program-greeting">Excellent, votre programme</div>
            <div class="program-title"><?php echo $active_program->program_name; ?></div>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="program-view-btn">
                <i class="fa fa-eye"></i>
                Voir les détails
            </a>
            <div class="program-status">
                <i class="fa fa-circle"></i>
                Programme actif
            </div>
        </div>

        <div class="program-progress-ring">
            <svg width="100" height="100">
                <circle class="progress-ring-bg" cx="50" cy="50" r="<?php echo $radius; ?>"/>
                <circle class="progress-ring-fill" cx="50" cy="50" r="<?php echo $radius; ?>"
                        style="stroke-dasharray: <?php echo $circumference; ?>; stroke-dashoffset: <?php echo $stroke_offset; ?>;"/>
            </svg>
            <div class="progress-ring-text"><?php echo $progress_percentage; ?>%</div>
        </div>
    </div>

    <div class="program-body">
        <div class="program-info-grid">
            <div class="info-item">
                <i class="fa fa-calendar-alt"></i>
                <div class="info-content">
                    <div class="info-label">Date de début</div>
                    <div class="info-value"><?php echo _d($active_program->start_date); ?></div>
                </div>
            </div>

            <?php if ($active_program->end_date) { ?>
            <div class="info-item">
                <i class="fa fa-calendar-check"></i>
                <div class="info-content">
                    <div class="info-label">Date de fin</div>
                    <div class="info-value"><?php echo _d($active_program->end_date); ?></div>
                </div>
            </div>
            <?php } ?>

            <?php if ($active_program->daily_calories) { ?>
            <div class="info-item">
                <i class="fa fa-fire"></i>
                <div class="info-content">
                    <div class="info-label">Calories quotidiennes</div>
                    <div class="info-value"><?php echo $active_program->daily_calories; ?> kcal</div>
                </div>
            </div>
            <?php } ?>

            <?php if ($active_program->daily_protein) { ?>
            <div class="info-item">
                <i class="fa fa-drumstick-bite"></i>
                <div class="info-content">
                    <div class="info-label">Protéines</div>
                    <div class="info-value"><?php echo $active_program->daily_protein; ?>g</div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if ($active_program->objective) { ?>
        <div class="program-objective">
            <strong><i class="fa fa-bullseye"></i> Objectif</strong>
            <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
        </div>
        <?php } ?>

        <?php
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $meal_plans = $this->dietetic_meal_plans_model->get_by_program($active_program->id);
        ?>

        <?php if (!empty($meal_plans)) { ?>
        <div class="meal-plans-section">
            <h5 class="section-title">
                <i class="fa fa-utensils"></i>
                Plans de repas
            </h5>

            <?php foreach ($meal_plans as $plan) { ?>
            <div class="meal-plan-item">
                <div class="meal-plan-name">
                    <i class="fa fa-calendar-week"></i>
                    <?php echo $plan->plan_name; ?> - Semaine <?php echo $plan->week_number; ?>
                </div>
                <div class="meal-plan-actions">
                    <a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>" class="btn-flat btn-flat-primary">
                        <i class="fa fa-eye"></i> Voir les détails
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php } else { ?>
<div class="no-program-alert">
    <i class="fa fa-info-circle"></i>
    <p>Aucun programme actif pour le moment. Votre diététicien vous en assignera un prochainement.</p>
</div>
<?php } ?>

<!-- Weight Progress Simple -->
<div class="weight-progress-card">
    <?php if ($weight_progress->weight_change !== null) { ?>
        <div class="weight-progress-icon">
            <i class="fa fa-<?php echo $weight_progress->weight_change < 0 ? 'arrow-down' : 'arrow-up'; ?>"></i>
        </div>
        <div class="weight-progress-value">
            <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
        </div>
        <div class="weight-progress-label">Évolution du poids</div>
    <?php } else { ?>
        <div class="weight-progress-icon">
            <i class="fa fa-balance-scale"></i>
        </div>
        <div class="weight-progress-empty">Aucune évolution disponible</div>
    <?php } ?>
</div>

<!-- Single Consultation -->
<div class="consultations-section">
    <div class="consultations-header">
        <h4><i class="fa fa-calendar-alt"></i> Prochain rendez-vous</h4>
    </div>

    <?php if (!empty($upcoming_consultations)) { ?>
        <?php $next_consultation = $upcoming_consultations[0]; ?>
        <div class="consultation-card">
            <div class="consultation-time">
                <i class="fa fa-clock"></i>
                <?php echo date('H:i', strtotime($next_consultation->consultation_date)); ?>
            </div>
            <div class="consultation-title">
                Consultation avec votre diététicien
            </div>
            <div class="consultation-type-label">
                <?php echo _d($next_consultation->consultation_date); ?>
            </div>
            <span class="consultation-status-badge">
                <i class="fa fa-check-circle"></i>
                Programmé
            </span>
            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="consultation-view-all-btn">
                <span>Voir tous les rendez-vous</span>
                <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    <?php } else { ?>
        <div class="empty-consultations">
            <i class="fa fa-calendar-times"></i>
            <p>Aucun rendez-vous programmé</p>
        </div>
    <?php } ?>
</div>

<!-- Floating Action Button -->
<div class="fab-container">
    <button class="fab-button" id="fabBtn" onclick="toggleFabModal()">
        <i class="fa fa-rocket"></i>
    </button>
</div>

<!-- FAB Modal for Quick Actions -->
<div class="fab-modal" id="fabModal" onclick="closeFabModal(event)">
    <div class="fab-modal-content" onclick="event.stopPropagation()">
        <div class="fab-modal-header">
            <h3 class="fab-modal-title">
                <i class="fa fa-bolt"></i>
                Actions rapides
            </h3>
            <button class="fab-modal-close" onclick="closeFabModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="fab-actions-list">
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="fab-action-item">
                <div class="fab-action-icon add">
                    <i class="fa fa-plus"></i>
                </div>
                <div class="fab-action-text">
                    <div class="fab-action-title">Ajouter une mesure</div>
                    <div class="fab-action-desc">Enregistrer votre poids et mesures</div>
                </div>
            </a>

            <?php
            // Check if food surveys feature is enabled
            if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="fab-action-item">
                <div class="fab-action-icon survey">
                    <i class="fa fa-list-alt"></i>
                </div>
                <div class="fab-action-text">
                    <div class="fab-action-title">Enquête alimentaire</div>
                    <div class="fab-action-desc">Remplir votre journal alimentaire</div>
                </div>
            </a>
            <?php } ?>

            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="fab-action-item">
                <div class="fab-action-icon consult">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="fab-action-text">
                    <div class="fab-action-title">Mes consultations</div>
                    <div class="fab-action-desc">Voir tous mes rendez-vous</div>
                </div>
            </a>

            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="fab-action-item">
                <div class="fab-action-icon dietitian">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="fab-action-text">
                    <div class="fab-action-title">Mon diététicien</div>
                    <div class="fab-action-desc">Contacter mon diététicien</div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Custom Dashboard Footer -->
<footer class="dashboard-footer">
    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="footer-link active">
        <i class="fa fa-list-alt footer-link-icon"></i>
        <span class="footer-link-label">Enquête</span>
    </a>

    <div class="footer-spacer"></div>

    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="footer-link">
        <i class="fa fa-cutlery footer-link-icon"></i>
        <span class="footer-link-label">Repas</span>
    </a>
</footer>

<script>
function toggleFabModal() {
    const modal = document.getElementById('fabModal');
    modal.classList.toggle('active');
}

function closeFabModal(event) {
    const modal = document.getElementById('fabModal');
    if (event) {
        // Only close if clicking the overlay, not the content
        modal.classList.remove('active');
    } else {
        // Close button clicked
        modal.classList.remove('active');
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFabModal();
    }
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
