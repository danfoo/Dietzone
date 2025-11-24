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
/* Welcome Message */
.welcome-message {
    font-size: 18px;
    color: #495057;
    margin-bottom: 24px;
}

.welcome-message .patient-name {
    font-weight: 700;
    color: #212529;
}

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
    min-height: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
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
    min-height: 220px;
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

/* Food Survey Card - Orange Design */
.survey-card {
    background: linear-gradient(135deg, #F3911D 0%, #e07d0a 100%);
    border-radius: 24px;
    padding: 32px 28px;
    margin-bottom: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(243, 145, 29, 0.3);
    min-height: 220px;
    display: flex;
    flex-direction: column;
}

.survey-card::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
}

.survey-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.survey-info-left {
    flex: 1;
}

.survey-greeting {
    font-size: 15px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.survey-title {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
    margin: 0 0 16px 0;
}

.survey-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
}

.survey-view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 255, 255, 0.3);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.25) 100%);
    color: white;
    text-decoration: none;
}

.survey-status {
    margin-top: 12px;
    font-size: 13px;
    opacity: 0.85;
    display: flex;
    align-items: center;
    gap: 6px;
}

.survey-status i {
    font-size: 8px;
    color: #a3f3a3;
    animation: pulse 2s infinite;
}

.survey-progress-ring {
    position: relative;
    width: 100px;
    height: 100px;
}

.progress-ring-fill-orange {
    fill: none;
    stroke: white;
    stroke-width: 8;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    transition: stroke-dashoffset 1s ease-out;
}

.survey-body {
    position: relative;
    z-index: 1;
}

.survey-dates {
    font-size: 14px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 8px;
}

.survey-dates i {
    margin-right: 6px;
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

/* Consultations Card - Peach Design #FFE9D2 */
.consultations-card {
    background: linear-gradient(135deg, #FFE9D2 0%, #f5d9bd 100%);
    border-radius: 24px;
    padding: 32px 28px;
    margin-bottom: 30px;
    color: #5a3e2b;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(255, 233, 210, 0.4);
    min-height: 220px;
    display: flex;
    flex-direction: column;
}

.consultations-card::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
}

.consultations-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.consultations-info-left {
    flex: 1;
}

.consultations-greeting {
    font-size: 15px;
    opacity: 0.85;
    margin-bottom: 8px;
    color: #5a3e2b;
}

.consultations-title {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.3;
    margin: 0 0 16px 0;
    color: #5a3e2b;
}

.consultations-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(90, 62, 43, 0.15) 0%, rgba(90, 62, 43, 0.1) 100%);
    color: #5a3e2b;
    padding: 12px 24px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(90, 62, 43, 0.15);
}

.consultations-view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(90, 62, 43, 0.25);
    background: linear-gradient(135deg, rgba(90, 62, 43, 0.25) 0%, rgba(90, 62, 43, 0.15) 100%);
    color: #5a3e2b;
    text-decoration: none;
}

.consultations-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(90, 62, 43, 0.15);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #5a3e2b;
    margin-top: 12px;
}

.consultations-status i {
    font-size: 8px;
    color: #48bb78;
    animation: pulse 2s infinite;
}

.consultations-icon-ring {
    position: relative;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(90, 62, 43, 0.15);
}

.consultations-icon-ring i {
    font-size: 40px;
    color: #5a3e2b;
}

.consultations-body {
    position: relative;
    z-index: 1;
}

.consultations-info {
    background: rgba(255, 255, 255, 0.4);
    padding: 16px;
    border-radius: 12px;
    border-left: 3px solid #5a3e2b;
    backdrop-filter: blur(10px);
}

.consultation-date {
    font-size: 14px;
    font-weight: 600;
    color: #5a3e2b;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.consultation-date i {
    color: #d4a574;
}

.consultation-time {
    font-size: 13px;
    color: rgba(90, 62, 43, 0.8);
}

.empty-consultations {
    text-align: center;
    padding: 20px;
    color: rgba(90, 62, 43, 0.6);
}

.empty-consultations i {
    font-size: 32px;
    opacity: 0.4;
    margin-bottom: 8px;
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

.weight-chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.weight-chart-title i {
    color: #48bb78;
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
    bottom: 30px;
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

/* =====================================================
   SECTION "MA JOURNÉE" (Daily Tracking) - Horizontal Compact
   ===================================================== */
.my-day-section {
    background: transparent;
    margin-bottom: 20px;
}

.my-day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.my-day-title {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 8px;
}

.my-day-title i {
    color: #01807B;
    font-size: 18px;
}

.my-day-streak {
    background: linear-gradient(135deg, #FF6B6B 0%, #EE5A52 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 600;
}

.daily-tracking-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

/* Compact horizontal item style */
.daily-item {
    background: white;
    border-radius: 12px;
    padding: 8px 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    transition: all 0.2s ease;
}

/* Water item full width */
.water-item {
    width: 100%;
    justify-content: space-between;
}

.daily-item:hover {
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    transform: translateY(-1px);
}

.daily-item-icon {
    font-size: 16px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.breakfast-item .daily-item-icon {
    color: #FF9800;
}

.lunch-item .daily-item-icon {
    color: #4CAF50;
}

.dinner-item .daily-item-icon {
    color: #3F51B5;
}

.water-item .daily-item-icon {
    color: #2196F3;
}

.daily-item-label {
    font-size: 8px;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

/* Checkbox custom style */
.daily-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    flex-shrink: 0;
    accent-color: #01807B;
}

/* Progress bar for meals */
.meals-progress-container {
    width: 100%;
    margin: 10px 0;
}

.meals-progress-bar {
    width: 100%;
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.meals-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
    border-radius: 10px;
    transition: width 0.3s ease;
    width: 0%;
}

.meals-progress-text {
    font-size: 10px;
    color: #6c757d;
    text-align: center;
    margin-top: 4px;
    font-weight: 600;
}

/* Water counter buttons */
.water-actions {
    display: flex;
    gap: 4px;
    align-items: center;
}

.water-count-display {
    font-size: 11px;
    font-weight: 700;
    color: #2c3e50;
    min-width: 18px;
    text-align: center;
}

.btn-water {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #e0e0e0;
    background: white;
    color: #01807B;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    padding: 0;
}

.btn-water:hover:not(:disabled) {
    background: #01807B;
    color: white;
    border-color: #01807B;
}

.btn-water:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.daily-motivation {
    background: linear-gradient(135deg, #FFF9E6 0%, #FFF3CD 100%);
    padding: 10px 16px;
    border-radius: 10px;
    text-align: center;
    font-size: 12px;
    color: #856404;
    font-weight: 500;
    border-left: 3px solid #FFC107;
    margin-top: 10px;
}

.daily-motivation i {
    color: #FFC107;
    margin-right: 4px;
}

.daily-motivation strong {
    font-weight: 700;
    color: #795500;
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

    .weight-goal-card {
        margin-bottom: 24px;
    }

    .weight-values {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .weight-arrow {
        transform: rotate(90deg);
        margin: 8px 0;
    }

    /* Responsive Ma Journée */
    .daily-tracking-grid {
        gap: 6px;
    }

    .daily-item {
        padding: 6px 10px;
        font-size: 7px;
    }

    .daily-item-icon {
        font-size: 14px;
        width: 20px;
        height: 20px;
    }

    .btn-water {
        width: 18px;
        height: 18px;
        font-size: 9px;
    }

    .water-count-display {
        font-size: 10px;
        min-width: 16px;
    }
}

/* Weight Goal Card - Program Style Design */
.weight-goal-card {
    background: #BAE2E1;
    border-radius: 24px;
    padding: 24px 20px;
    margin-bottom: 30px;
    color: #2c3e50;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(44, 95, 111, 0.15);
    display: flex;
    flex-direction: column;
}

.weight-goal-card::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
}

.weight-goal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}

.weight-goal-title {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    padding: 0;
}

.weight-goal-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(245, 165, 74, 0.25);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: #d67f3a;
    white-space: nowrap;
}

.weight-goal-status i {
    font-size: 12px;
}

.weight-goal-status.achieved {
    background: rgba(72, 187, 120, 0.25);
    color: #2d7a4f;
}

.weight-goal-status.on-track {
    background: rgba(66, 153, 225, 0.25);
    color: #2b5a99;
}

.weight-goal-status.ahead {
    background: rgba(159, 122, 234, 0.25);
    color: #6b46c1;
}

.weight-goal-status.behind {
    background: rgba(243, 145, 29, 0.25);
    color: #c77219;
}

.weight-goal-status.no-data {
    background: rgba(0, 0, 0, 0.1);
    color: #4a5568;
}

.weight-values-compact {
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
    margin-left: -4px;
    margin-right: -4px;
}

.weight-values-compact .col-xs-6 {
    padding-left: 4px;
    padding-right: 4px;
}

.weight-value-compact {
    background: white;
    border-radius: 12px;
    padding: 10px 16px;
    text-align: center;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    min-height: 75px;
    transition: all 0.3s ease;
    gap: 6px;
}

.weight-value-compact:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.weight-value-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.weight-value-icon i {
    font-size: 20px;
    color: #01807B;
}

.weight-value-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.weight-value-compact-number {
    font-size: 18px;
    font-weight: 800;
    color: #212529;
    line-height: 1.2;
    margin: 0 0 4px 0;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.weight-value-compact-label {
    font-size: 9px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.2;
}

.weight-value-compact-unit {
    font-size: 11px;
    color: #6c757d;
    font-weight: 600;
}

.weight-arrow-compact {
    display: none;
}

.weight-remaining-simple {
    text-align: center;
    margin-bottom: 12px;
    padding: 16px 12px;
    background: #F3911D;
    border-radius: 12px;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.3);
}

.weight-remaining-simple-text {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.weight-remaining-simple-value {
    font-size: 24px;
    font-weight: 700;
    color: white;
}

.progress-gauge-container {
    margin-bottom: 12px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 1;
}

.progress-gauge-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    gap: 12px;
}

.progress-gauge-label {
    font-size: 10px;
    color: #4a5568;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.progress-gauge-label i {
    color: #01807B;
    font-size: 12px;
}

.progress-gauge-percent {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
}

.progress-gauge-bar {
    position: relative;
    height: 10px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    overflow: hidden;
}

.progress-gauge-fill {
    height: 100%;
    background: linear-gradient(90deg, #01807B 0%, #019B95 100%);
    border-radius: 10px;
    transition: width 1.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.progress-gauge-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.4) 50%, transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.weight-motivation-simple {
    font-size: 13px;
    line-height: 1.5;
    font-weight: 600;
    text-align: center;
    color: #2c3e50;
    position: relative;
    z-index: 1;
}

.weight-motivation-simple.achieved {
    color: #2d7a4f;
}

.weight-motivation-simple.on-track {
    color: #2b5a99;
}

.weight-motivation-simple.ahead {
    color: #6b46c1;
}

.weight-motivation-simple.behind {
    color: #c77219;
}

.weight-motivation-simple.no-data {
    color: #4a5568;
}

.weight-motivation-simple strong {
    font-weight: 800;
    color: #01807B;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .weight-goal-card {
        padding: 20px 16px;
    }
}
</style>

<!-- Welcome Message -->
<div class="welcome-message">
    Bonjour <span class="patient-name"><?php echo htmlspecialchars($client->company); ?></span>
</div>

<!-- =====================================================
     SECTION: MA JOURNÉE (Daily Tracking)
     ===================================================== -->
<div class="my-day-section">
    <div class="my-day-header">
        <div class="my-day-title">
            <i class="fa fa-calendar-check-o"></i>
            Ma Journée
        </div>
        <?php if ($tracking_streak > 0) { ?>
        <div class="my-day-streak">
            🔥 <strong><?php echo $tracking_streak; ?></strong> <?php echo $tracking_streak > 1 ? 'jours' : 'jour'; ?>
        </div>
        <?php } ?>
    </div>

    <div class="daily-tracking-grid">
        <!-- Item 1: Petit déjeuner -->
        <div class="daily-item breakfast-item">
            <div class="daily-item-icon">
                <i class="fa fa-coffee"></i>
            </div>
            <span class="daily-item-label">Petit déjeuner</span>
            <input type="checkbox" class="daily-checkbox" data-meal="breakfast"
                   <?php echo $daily_tracking->breakfast_checked ? 'checked' : ''; ?>
                   onchange="toggleMeal('breakfast', this.checked)">
        </div>

        <!-- Item 2: Déjeuner -->
        <div class="daily-item lunch-item">
            <div class="daily-item-icon">
                <i class="fa fa-cutlery"></i>
            </div>
            <span class="daily-item-label">Déjeuner</span>
            <input type="checkbox" class="daily-checkbox" data-meal="lunch"
                   <?php echo $daily_tracking->lunch_checked ? 'checked' : ''; ?>
                   onchange="toggleMeal('lunch', this.checked)">
        </div>

        <!-- Item 3: Dîner -->
        <div class="daily-item dinner-item">
            <div class="daily-item-icon">
                <i class="fa fa-moon-o"></i>
            </div>
            <span class="daily-item-label">Dîner</span>
            <input type="checkbox" class="daily-checkbox" data-meal="dinner"
                   <?php echo $daily_tracking->dinner_checked ? 'checked' : ''; ?>
                   onchange="toggleMeal('dinner', this.checked)">
        </div>
    </div>

    <!-- Progress bar for meals -->
    <div class="meals-progress-container">
        <div class="meals-progress-bar">
            <div class="meals-progress-fill" id="meals-progress-fill" style="width: <?php
                $meals_count = ($daily_tracking->breakfast_checked ? 1 : 0) +
                              ($daily_tracking->lunch_checked ? 1 : 0) +
                              ($daily_tracking->dinner_checked ? 1 : 0);
                echo ($meals_count / 3 * 100);
            ?>%;"></div>
        </div>
        <div class="meals-progress-text">
            <span id="meals-progress-text"><?php echo $meals_count; ?>/3 repas validés</span>
        </div>
    </div>

    <!-- Item 4: Hydratation (full width) -->
    <div class="daily-item water-item">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div class="daily-item-icon">
                <i class="fa fa-tint"></i>
            </div>
            <span class="daily-item-label">Hydratation</span>
        </div>
        <div class="water-actions">
            <button class="btn-water" onclick="updateWater('decrement')" <?php echo $daily_tracking->water_glasses == 0 ? 'disabled' : ''; ?>>
                <i class="fa fa-minus"></i>
            </button>
            <span class="water-count-display" id="water-count"><?php echo $daily_tracking->water_glasses; ?></span>
            <button class="btn-water" onclick="updateWater('increment')" <?php echo $daily_tracking->water_glasses >= 20 ? 'disabled' : ''; ?>>
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>

    <?php if ($tracking_streak > 0) { ?>
    <div class="daily-motivation">
        <i class="fa fa-star"></i>
        <?php
        if ($tracking_streak == 1) {
            echo "Excellent départ ! Continuez comme ça.";
        } elseif ($tracking_streak < 7) {
            echo "Vous êtes sur la bonne voie ! <strong>$tracking_streak jours d'affilée</strong>.";
        } elseif ($tracking_streak < 30) {
            echo "Incroyable série de <strong>$tracking_streak jours</strong> ! 🎯";
        } else {
            echo "🏆 Champion ! <strong>$tracking_streak jours consécutifs</strong> de suivi !";
        }
        ?>
    </div>
    <?php } ?>
</div>

<?php
// === Weight Goal Progress Calculation ===
$current_weight = $patient->latest_measurement ? $patient->latest_measurement->weight : null;
$target_weight = $patient->target_weight;
$initial_weight = $weight_progress->initial_weight;

// Calculate remaining weight and determine goal type
$weight_remaining = null;
$goal_type = 'lose'; // 'lose', 'gain', or 'maintain'
if ($current_weight && $target_weight) {
    $weight_remaining = abs($current_weight - $target_weight);

    // Determine if client needs to lose or gain weight
    if ($current_weight > $target_weight) {
        $goal_type = 'lose';
    } elseif ($current_weight < $target_weight) {
        $goal_type = 'gain';
    } else {
        $goal_type = 'maintain';
    }
}

// Calculate progress percentage
// % = (Initial - Current) / (Initial - Target) * 100
$progress_percent = 0;
if ($initial_weight && $current_weight && $target_weight && $initial_weight != $target_weight) {
    $weight_lost = $initial_weight - $current_weight;
    $total_to_lose = $initial_weight - $target_weight;
    $progress_percent = ($weight_lost / $total_to_lose) * 100;
    $progress_percent = max(0, min(100, $progress_percent)); // Clamp between 0-100
}

// Calculate timeline status if program has dates
$timeline_status = 'no-data';
$timeline_message = '';
$weeks_elapsed = 0;
$weeks_total = 0;
$expected_loss = 0;
$actual_loss = 0;

if ($active_program && $active_program->start_date && $active_program->end_date) {
    $start = new DateTime($active_program->start_date);
    $end = new DateTime($active_program->end_date);
    $now = new DateTime();

    $total_days = $start->diff($end)->days;
    $elapsed_days = $start->diff($now)->days;

    $weeks_total = ceil($total_days / 7);
    $weeks_elapsed = ceil($elapsed_days / 7);

    if ($weeks_total > 0 && $initial_weight && $target_weight && $current_weight) {
        // Expected weekly loss rate
        $total_to_lose = $initial_weight - $target_weight;
        $weekly_loss_rate = $total_to_lose / $weeks_total;

        // Expected loss by now
        $expected_loss = $weekly_loss_rate * $weeks_elapsed;

        // Actual loss
        $actual_loss = $initial_weight - $current_weight;

        // Determine status
        $difference = $actual_loss - $expected_loss;

        if ($difference >= 0.5) {
            $timeline_status = 'ahead';
        } elseif ($difference <= -0.5) {
            $timeline_status = 'behind';
        } else {
            $timeline_status = 'on-track';
        }
    }
}

// Determine overall status
$overall_status = 'no-data';
$status_icon = 'fa-circle';

if (!$current_weight || !$target_weight) {
    $overall_status = 'no-data';
    $status_icon = 'fa-exclamation-circle';
} elseif ($progress_percent >= 100) {
    $overall_status = 'achieved';
    $status_icon = 'fa-trophy';
} elseif ($progress_percent >= 80 || $weight_remaining <= 2) {
    $overall_status = 'achieved'; // Close enough
    $status_icon = 'fa-check-circle';
} else {
    $overall_status = $timeline_status;
    if ($timeline_status == 'ahead') {
        $status_icon = 'fa-rocket';
    } elseif ($timeline_status == 'on-track') {
        $status_icon = 'fa-check';
    } elseif ($timeline_status == 'behind') {
        $status_icon = 'fa-clock';
    } else {
        $status_icon = 'fa-circle';
    }
}

// Generate motivation message
$motivation_message = '';
if (!$current_weight || !$target_weight) {
    $motivation_message = 'Entrez votre poids de la semaine pour mettre à jour votre progression.';
} elseif ($progress_percent >= 100) {
    $motivation_message = '🎉 <strong>Félicitations !</strong> Objectif atteint.';
} elseif ($weight_remaining <= 2) {
    $motivation_message = '<strong>Presque au but</strong> — plus que ' . number_format($weight_remaining, 1) . ' kg.';
} elseif ($timeline_status == 'ahead') {
    $ahead_percent = $expected_loss > 0 ? abs(round(($actual_loss - $expected_loss) / $expected_loss * 100)) : 0;
    $motivation_message = '<strong>Excellent !</strong> Vous devancez le planning de ' . $ahead_percent . ' %.';
} elseif ($timeline_status == 'on-track') {
    $end_date = $active_program && $active_program->end_date ? date('d/m', strtotime($active_program->end_date)) : '';
    $motivation_message = '<strong>Solide !</strong> Vous êtes dans les temps' . ($end_date ? ' pour le ' . $end_date : '') . '.';
} elseif ($timeline_status == 'behind') {
    $behind_kg = abs(round($actual_loss - $expected_loss, 1));
    $motivation_message = '<strong>Courage</strong> — encore ' . $behind_kg . ' kg à rattraper pour revenir dans les temps.';
} else {
    $motivation_message = '<strong>Continue comme ça !</strong> Vous progressez vers votre objectif.';
}
?>

<!-- Weight Goal Card -->
<div class="weight-goal-card">
    <div class="weight-goal-header">
        <h3 class="weight-goal-title">Objectif de poids</h3>
        <div class="weight-goal-status <?php echo $overall_status; ?>">
            <i class="fa <?php echo $status_icon; ?>"></i>
            <?php
            if ($overall_status == 'achieved') {
                echo 'Objectif atteint';
            } elseif ($overall_status == 'ahead') {
                echo 'En avance';
            } elseif ($overall_status == 'on-track') {
                echo 'Dans les temps';
            } elseif ($overall_status == 'behind') {
                echo 'En retard';
            } else {
                echo 'En cours';
            }
            ?>
        </div>
    </div>

    <div class="row weight-values-compact">
        <div class="col-xs-6">
            <div class="weight-value-compact weight-value-icon-left">
                <div class="weight-value-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="weight-value-content">
                    <div class="weight-value-compact-number">
                        <?php echo $current_weight ? number_format($current_weight, 1) : '-'; ?><span class="weight-value-compact-unit"> kg</span>
                    </div>
                    <div class="weight-value-compact-label">Poids actuel</div>
                </div>
            </div>
        </div>

        <div class="col-xs-6">
            <div class="weight-value-compact weight-value-icon-right">
                <div class="weight-value-content">
                    <div class="weight-value-compact-number">
                        <?php echo $target_weight ? number_format($target_weight, 1) : '-'; ?><span class="weight-value-compact-unit"> kg</span>
                    </div>
                    <div class="weight-value-compact-label">Poids cible</div>
                </div>
                <div class="weight-value-icon">
                    <i class="fa fa-bullseye"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if ($weight_remaining !== null) { ?>
    <div class="weight-remaining-simple">
        <div class="weight-remaining-simple-text">
            <?php
            if ($overall_status == 'achieved' || $weight_remaining <= 0.5) {
                echo 'Objectif atteint !';
            } elseif ($goal_type == 'gain') {
                echo 'À rattraper';
            } else {
                echo 'Encore à perdre';
            }
            ?>
        </div>
        <div class="weight-remaining-simple-value">
            <?php
            if ($overall_status == 'achieved' || $weight_remaining <= 0.5) {
                echo '🎉';
            } else {
                echo number_format($weight_remaining, 1) . ' kg';
            }
            ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($current_weight && $target_weight) { ?>
    <div class="progress-gauge-container">
        <div class="progress-gauge-header">
            <div class="progress-gauge-label">
                <i class="fa fa-chart-line"></i>
                Progression
            </div>
            <div class="progress-gauge-percent"><?php echo round($progress_percent); ?>%</div>
        </div>
        <div class="progress-gauge-bar">
            <div class="progress-gauge-fill" style="width: <?php echo round($progress_percent); ?>%;"></div>
        </div>
    </div>
    <?php } ?>

    <div class="weight-motivation-simple <?php echo $overall_status; ?>">
        <?php echo $motivation_message; ?>
    </div>
</div>

<!-- Other Stats Cards -->
<div class="stats-grid">
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

<!-- Weight Evolution Chart -->
<div class="weight-progress-card">
    <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
        <h4 class="weight-chart-title"><i class="fa fa-line-chart"></i> Évolution du Poids</h4>
        <canvas id="weightEvolutionChart" height="100"></canvas>
    <?php } elseif ($weight_progress->weight_change !== null) { ?>
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
        <?php if ($active_program->daily_calories) { ?>
        <div class="program-info-grid">
            <div class="info-item">
                <i class="fa fa-fire"></i>
                <div class="info-content">
                    <div class="info-label">Calories quotidiennes</div>
                    <div class="info-value"><?php echo $active_program->daily_calories; ?> kcal</div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($active_program->objective) { ?>
        <div class="program-objective">
            <strong><i class="fa fa-bullseye"></i> Objectif</strong>
            <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
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

<!-- Food Survey Card -->
<?php if ($active_survey) { ?>
    <?php
    // Circle calculation for SVG
    $radius = 42;
    $circumference = 2 * pi() * $radius;
    $stroke_offset = $circumference - ($survey_completion / 100) * $circumference;
    ?>

<div class="survey-card">
    <div class="survey-header">
        <div class="survey-info-left">
            <div class="survey-greeting">Votre enquête alimentaire</div>
            <div class="survey-title"><?php echo $active_survey->survey_name; ?></div>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="survey-view-btn">
                <i class="fa fa-eye"></i>
                Voir les détails
            </a>
            <div class="survey-status">
                <i class="fa fa-circle"></i>
                Enquête active
            </div>
        </div>

        <div class="survey-progress-ring">
            <svg width="100" height="100">
                <circle class="progress-ring-bg" cx="50" cy="50" r="<?php echo $radius; ?>"/>
                <circle class="progress-ring-fill-orange" cx="50" cy="50" r="<?php echo $radius; ?>"
                        style="stroke-dasharray: <?php echo $circumference; ?>; stroke-dashoffset: <?php echo $stroke_offset; ?>;"/>
            </svg>
            <div class="progress-ring-text"><?php echo round($survey_completion); ?>%</div>
        </div>
    </div>

    <div class="survey-body">
        <?php if ($active_survey->start_date && $active_survey->end_date) { ?>
        <div class="survey-dates">
            <span><i class="fa fa-calendar"></i> Du <?php echo _d($active_survey->start_date); ?> au <?php echo _d($active_survey->end_date); ?></span>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<!-- Consultation Card -->
<div class="consultations-card">
    <div class="consultations-header">
        <div class="consultations-info-left">
            <div class="consultations-greeting">Vos rendez-vous</div>
            <div class="consultations-title">
                <?php if (!empty($upcoming_consultations)) { ?>
                    Prochain rendez-vous prévu
                <?php } else { ?>
                    Rendez-vous
                <?php } ?>
            </div>
            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="consultations-view-btn">
                <i class="fa fa-eye"></i>
                Voir les détails
            </a>
            <?php if (!empty($upcoming_consultations)) { ?>
                <div class="consultations-status">
                    <i class="fa fa-circle"></i>
                    Rendez-vous programmé
                </div>
            <?php } ?>
        </div>

        <div class="consultations-icon-ring">
            <i class="fa fa-calendar"></i>
        </div>
    </div>

    <div class="consultations-body">
        <?php if (!empty($upcoming_consultations)) { ?>
            <?php $next_consultation = $upcoming_consultations[0]; ?>
            <div class="consultations-info">
                <div class="consultation-date">
                    <i class="fa fa-calendar-check-o"></i>
                    <?php echo _d($next_consultation->consultation_date); ?>
                </div>
                <div class="consultation-time">
                    <i class="fa fa-clock-o"></i>
                    <?php echo date('H:i', strtotime($next_consultation->consultation_date)); ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="empty-consultations">
                <i class="fa fa-calendar-times-o"></i>
                <p>Aucun rendez-vous programmé</p>
            </div>
        <?php } ?>
    </div>
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

    <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="footer-link">
        <i class="fa fa-cutlery footer-link-icon"></i>
        <span class="footer-link-label">Recettes</span>
    </a>
</footer>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
// Weight Evolution Chart
<?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('weightEvolutionChart');
    if (ctx) {
        ctx = ctx.getContext('2d');
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
                    borderColor: '#48bb78',
                    backgroundColor: 'rgba(72, 187, 120, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#48bb78',
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
                            beginAtZero: false,
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", sans-serif',
                            fontSize: 12
                        },
                        gridLines: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            zeroLineColor: 'rgba(0, 0, 0, 0.1)'
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", sans-serif',
                            fontSize: 12
                        },
                        gridLines: {
                            display: false
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", sans-serif',
                        fontSize: 13,
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltips: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", sans-serif',
                    bodyFontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", sans-serif',
                    cornerRadius: 8,
                    displayColors: false
                }
            }
        });
    }
});
<?php } ?>

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

// ============================================================
// DAILY TRACKING JAVASCRIPT FUNCTIONS
// ============================================================

/**
 * Update water count (increment or decrement)
 */
function updateWater(action) {
    const url = '<?php echo site_url('dietetic/portal/api_update_water'); ?>';

    const formData = new FormData();
    formData.append('action', action);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update counter
            document.getElementById('water-count').textContent = data.water_glasses;

            // Update buttons disabled state
            const decrementBtn = document.querySelector('[onclick="updateWater(\'decrement\')"]');
            const incrementBtn = document.querySelector('[onclick="updateWater(\'increment\')"]');

            decrementBtn.disabled = data.water_glasses == 0;
            incrementBtn.disabled = data.water_glasses >= 20;

            // Visual feedback
            showToast('💧 Hydratation mise à jour !', 'success');
        } else {
            showToast('❌ Erreur: ' + (data.error || 'Impossible de mettre à jour'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Erreur de connexion', 'error');
    });
}

/**
 * Toggle meal checkbox (breakfast, lunch, dinner)
 */
function toggleMeal(mealType, checked) {
    const url = '<?php echo site_url('dietetic/portal/api_toggle_meal'); ?>';

    const formData = new FormData();
    formData.append('meal', mealType);
    formData.append('checked', checked);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress bar
            updateMealsProgress();

            // Visual feedback
            const mealNames = {
                breakfast: 'Petit-déjeuner',
                lunch: 'Déjeuner',
                dinner: 'Dîner'
            };
            const icon = checked ? '✅' : '🔲';
            showToast(icon + ' ' + mealNames[mealType] + ' ' + (checked ? 'validé' : 'non validé'), 'success');
        } else {
            // Revert checkbox on error
            const checkbox = document.querySelector(`.daily-checkbox[data-meal="${mealType}"]`);
            if (checkbox) {
                checkbox.checked = !checked;
            }
            showToast('❌ Erreur: ' + (data.error || 'Impossible de mettre à jour'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert checkbox on error
        const checkbox = document.querySelector(`.daily-checkbox[data-meal="${mealType}"]`);
        if (checkbox) {
            checkbox.checked = !checked;
        }
        showToast('❌ Erreur de connexion', 'error');
    });
}

/**
 * Update meals progress bar
 */
function updateMealsProgress() {
    const checkboxes = document.querySelectorAll('.daily-checkbox');
    let count = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) count++;
    });

    const percentage = (count / 3 * 100);
    const progressFill = document.getElementById('meals-progress-fill');
    const progressText = document.getElementById('meals-progress-text');

    if (progressFill) {
        progressFill.style.width = percentage + '%';
    }

    if (progressText) {
        progressText.textContent = count + '/3 repas validés';
    }
}

/**
 * Simple toast notification
 */
function showToast(message, type = 'info') {
    // Check if toast container exists, if not create it
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; bottom: 100px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toast = document.createElement('div');
    const bgColors = {
        success: 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)',
        error: 'linear-gradient(135deg, #f56565 0%, #e53e3e 100%)',
        info: 'linear-gradient(135deg, #4299e1 0%, #3182ce 100%)'
    };

    toast.style.cssText = `
        background: ${bgColors[type] || bgColors.info};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        font-size: 14px;
        font-weight: 600;
        min-width: 250px;
        animation: slideInRight 0.3s ease-out;
    `;
    toast.textContent = message;

    // Add CSS animation
    if (!document.getElementById('toast-animation-style')) {
        const style = document.createElement('style');
        style.id = 'toast-animation-style';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    toastContainer.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
