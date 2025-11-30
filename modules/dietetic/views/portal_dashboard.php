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
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    border: none;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    min-height: 100px;
    height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
}

.stat-card .stat-icon {
    width: 28px;
    height: 28px;
    margin: 0 auto 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    position: relative;
}

.stat-card.weight {
    background: white;
}

.stat-card.weight .stat-icon {
    color: #4299e1;
}

.stat-card.target {
    background: white;
}

.stat-card.target .stat-icon {
    color: #48bb78;
}

.stat-card.bmi {
    background: white;
}

.stat-card.bmi .stat-icon {
    color: #FF5722;
}

.stat-card.progress {
    background: white;
}

.stat-card.progress .stat-icon {
    color: #4CAF50;
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

/* Enhanced Stat Cards - Mobile App Design */
.enhanced-stat-card {
    position: relative;
    padding: 20px 16px !important;
    min-height: 160px !important;
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.enhanced-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.05;
    border-radius: 16px;
    transition: opacity 0.3s ease;
}

.enhanced-stat-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.enhanced-stat-card:hover::before {
    opacity: 0.08;
}

/* BMI Category Backgrounds */
.enhanced-stat-card[data-category="underweight"] {
    background: linear-gradient(135deg, #ffffff 0%, #ebf8ff 100%);
}

.enhanced-stat-card[data-category="underweight"]::before {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.enhanced-stat-card[data-category="normal"] {
    background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}

.enhanced-stat-card[data-category="normal"]::before {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.enhanced-stat-card[data-category="overweight"] {
    background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
}

.enhanced-stat-card[data-category="overweight"]::before {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.enhanced-stat-card[data-category="obese"] {
    background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.enhanced-stat-card[data-category="obese"]::before {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

/* Progress Status Backgrounds */
.enhanced-stat-card[data-status="excellent"] {
    background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}

.enhanced-stat-card[data-status="excellent"]::before {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.enhanced-stat-card[data-status="attention"] {
    background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
}

.enhanced-stat-card[data-status="attention"]::before {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

/* Stat Category Badge */
.stat-category {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 12px;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    font-size: 10px;
    font-weight: 700;
    color: #2d3748;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.6);
}

.stat-category i {
    font-size: 12px;
}

/* Improved Icon Container */
.enhanced-stat-card .stat-icon {
    width: 48px !important;
    height: 48px !important;
    margin: 0 auto 12px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px !important;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Enhanced Value Styling */
.enhanced-stat-card .stat-value {
    font-size: 32px !important;
    font-weight: 900 !important;
    margin: 8px 0 !important;
}

/* Hydration Section - Compact Version */
.hydration-card-compact {
    background: white;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.hydration-card-compact:hover {
    box-shadow: 0 4px 16px rgba(79, 195, 247, 0.15);
}

.hydration-header-compact {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.hydration-title-compact {
    font-size: 16px;
    font-weight: 700;
    color: #01579b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.hydration-title-compact i {
    color: #0288d1;
    font-size: 18px;
}

.hydration-goal-display {
    font-size: 18px;
    font-weight: 700;
    color: #0288d1;
}

.hydration-goal-display span {
    color: #01579b;
}

.hydration-progress-bar {
    height: 12px;
    background: #e3f2fd;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 16px;
    position: relative;
}

.hydration-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4fc3f7 0%, #0288d1 100%);
    border-radius: 6px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.hydration-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.hydration-actions-compact {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.hydration-row {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 16px;
    align-items: flex-end;
}

.custom-input-section {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.compact-label {
    font-size: 10px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.input-with-button {
    position: relative;
    display: flex;
    align-items: center;
}

.compact-input {
    width: 100%;
    padding: 10px 50px 10px 14px;
    border: 2px solid #90caf9;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #01579b;
    transition: all 0.3s;
}

.compact-input:focus {
    outline: none;
    border-color: #0288d1;
    box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
}

.quick-buttons-inline {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    justify-content: flex-start;
}

/* Water Glass Buttons */
.quick-btn-glass {
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    transition: transform 0.3s;
}

.quick-btn-glass:hover {
    transform: translateY(-5px);
}

.quick-btn-glass:active {
    transform: translateY(-2px);
}

.water-glass-small,
.water-glass-medium,
.water-glass-large {
    position: relative;
    background: linear-gradient(180deg,
        rgba(79, 195, 247, 0.1) 0%,
        rgba(79, 195, 247, 0.2) 100%);
    border: 1.5px solid rgba(79, 195, 247, 0.5);
    border-top: none;
    border-radius: 0 0 20px 20px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.water-glass-small {
    width: 60px;
    height: 80px;
}

.water-glass-medium {
    width: 70px;
    height: 95px;
}

.water-glass-large {
    width: 80px;
    height: 110px;
}

.water-fill {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65%;
    background: linear-gradient(180deg, #4fc3f7 0%, #0288d1 100%);
    border-radius: 0 0 inherit inherit;
}

.water-wave {
    position: absolute;
    top: -10px;
    left: -50%;
    width: 200%;
    height: 15px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 45%;
    animation: wave 3s linear infinite;
}

@keyframes wave {
    0%, 100% {
        transform: translateX(0) translateY(0);
    }
    25% {
        transform: translateX(-15%) translateY(-3px);
    }
    50% {
        transform: translateX(-30%) translateY(0);
    }
    75% {
        transform: translateX(-15%) translateY(-3px);
    }
}

.water-cup-add {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 32px;
    height: 32px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 3;
}

.water-cup-add i {
    font-size: 16px;
    color: #0288d1;
}

.water-amount {
    position: absolute;
    bottom: 6px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 12px;
    font-weight: 700;
    color: white;
    z-index: 2;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.add-custom-btn-inline {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #4fc3f7 0%, #0288d1 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(79, 195, 247, 0.3);
}

.add-custom-btn-inline:hover {
    transform: translateY(-50%) scale(1.05);
    box-shadow: 0 4px 12px rgba(79, 195, 247, 0.4);
}

.add-custom-btn-inline:active {
    transform: translateY(-50%) scale(0.95);
}

/* Hydration History - Compact */
.hydration-history-compact {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e3f2fd;
}

.history-title-compact {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}

.history-bars-compact {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.history-bar-compact {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.history-bar-fill-compact {
    width: 100%;
    height: 60px;
    background: #e3f2fd;
    border-radius: 6px;
    position: relative;
    overflow: hidden;
}

.history-bar-value-compact {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(180deg, #4fc3f7 0%, #0288d1 100%);
    border-radius: 6px;
    transition: height 0.5s ease;
    overflow: hidden;
}

.history-bar-value-compact::before,
.history-bar-value-compact::after {
    content: '';
    position: absolute;
    top: -8px;
    left: -50%;
    width: 200%;
    height: 12px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 45%;
    animation: wave 3s linear infinite;
}

.history-bar-value-compact::after {
    animation-delay: -1.5s;
    opacity: 0.6;
}

.history-day-compact {
    font-size: 10px;
    font-weight: 600;
    color: #01579b;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .hydration-row {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .quick-buttons-inline {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    .water-glass-small {
        width: 55px;
        height: 70px;
    }

    .water-glass-medium {
        width: 65px;
        height: 85px;
    }

    .water-glass-large {
        width: 75px;
        height: 100px;
    }

    .water-cup-add {
        width: 28px;
        height: 28px;
    }

    .water-cup-add i {
        font-size: 14px;
    }

    .water-amount {
        font-size: 11px;
    }

    .add-custom-btn-inline {
        width: 36px;
        height: 36px;
        right: 3px;
    }

    .hydration-goal-display {
        font-size: 12px;
    }

    .hydration-title-compact {
        font-size: 14px;
    }

    .history-bars-compact {
        gap: 4px;
    }

    .history-bar-fill-compact {
        height: 50px;
    }
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
    font-size: 16px;
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
    font-size: 16px;
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
    font-size: 16px;
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
    font-size: 12px;
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
    font-size: 11px;
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

/* Meals block container */
.meals-block {
    background: white;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    margin-bottom: 8px;
}

.meals-block .daily-tracking-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 10px;
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

.meals-block .daily-item {
    box-shadow: none;
    border: 1px solid #e0e0e0;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 10px 8px;
}

/* Water item full width */
.water-item {
    width: 100%;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 14px 16px; /* Hauteur augmentée */
}

/* Secondary grid for calories and activity */
.tracking-secondary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.tracking-secondary-grid .daily-item {
    cursor: pointer;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 12px 8px;
    position: relative;
}

/* Indicateur cliquable pour calories et activité */
.tracking-secondary-grid .daily-item::after {
    content: '✏️ Cliquer pour saisir';
    font-size: 7px;
    color: #999;
    margin-top: 4px;
    opacity: 0.7;
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

.calories-item .daily-item-icon {
    color: #FF5722;
}

.activity-item .daily-item-icon {
    color: #9C27B0;
}

.daily-item-label {
    font-size: 8px;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.daily-value {
    font-size: 14px;
    font-weight: 700;
    color: #01807B;
    margin-top: 4px;
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
    padding-bottom: 50px !important;
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
    padding: 16px 14px;
    margin-bottom: 20px;
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
    margin-bottom: 10px;
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
    margin-bottom: 10px;
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
    padding: 8px 12px;
    text-align: center;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    min-height: 65px;
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
    margin-bottom: 8px;
    padding: 12px 10px;
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
    margin-bottom: 8px;
    padding: 10px 8px;
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

/* ============================================
   ACTIVITIES CARD - Simple & Clean
   ============================================ */
.activities-card-compact {
    background: white;
    border-radius: 12px;
    padding: 18px 22px;
    border: 2px solid #f1f3f5;
    margin-bottom: 24px;
    transition: all 0.3s;
}

.activities-card-compact:hover {
    border-color: #F3911D;
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.1);
}

.activities-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    border-bottom: 2px solid #f8f9fa;
    margin-bottom: 16px;
}

.total-calories-display {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
}

.total-calories-display i {
    color: #e74c3c;
    font-size: 20px;
}

.total-calories-display span {
    color: #e74c3c;
    font-size: 22px;
}

.btn-add-activity-plus {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #F3911D 0%, #e67e00 100%);
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.4);
}

.btn-add-activity-plus:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 6px 20px rgba(243, 145, 29, 0.5);
}

.btn-add-activity-plus:active {
    transform: scale(0.95);
}

.activities-simple-list {
    max-height: 300px;
    overflow-y: auto;
}

.no-activities-text {
    text-align: center;
    padding: 24px;
    color: #95a5a6;
    font-size: 13px;
    font-style: italic;
}

.activity-item-simple {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    margin-bottom: 6px;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 3px solid #F3911D;
    transition: all 0.2s;
}

.activity-item-simple:hover {
    background: #fff5e6;
    transform: translateX(4px);
}

.activity-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
    flex: 1;
}

.activity-item-details {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 11px;
    color: #7f8c8d;
}

.activity-item-minutes {
    display: flex;
    align-items: center;
    gap: 4px;
}

.activity-item-calories {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #e74c3c;
    font-weight: 700;
}

/* Modal */
.activity-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    animation: fadeIn 0.2s;
    backdrop-filter: blur(2px);
}

.activity-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.activity-modal-content {
    background: white;
    border-radius: 20px;
    width: 92%;
    max-width: 420px;
    max-height: 85vh;
    overflow-y: auto;
    animation: slideUp 0.3s;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.activity-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    border-radius: 20px 20px 0 0;
}

.activity-modal-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e0e0e0;
    color: #7f8c8d;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: #e74c3c;
    color: white;
    border-color: #e74c3c;
}

.activity-modal-body {
    padding: 20px;
}

.activity-modal-body .form-group {
    margin-bottom: 16px;
}

.activity-modal-body label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.activity-modal-body select,
.activity-modal-body input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 13px;
    transition: all 0.2s;
    background: white;
}

.activity-modal-body select:focus,
.activity-modal-body input:focus {
    border-color: #01807B;
    outline: none;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.activity-modal-body select option {
    font-size: 13px;
    padding: 8px;
}

.calories-preview {
    background: linear-gradient(135deg, #fff5e6 0%, #ffe8cc 100%);
    padding: 14px;
    border-radius: 12px;
    text-align: center;
    margin: 16px 0;
    border: 1px solid #ffe0b3;
}

.calories-preview i {
    color: #e74c3c;
    font-size: 20px;
    margin-right: 6px;
}

.calories-preview span {
    font-size: 24px;
    font-weight: 700;
    color: #e74c3c;
}

.btn-validate-activity {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

.btn-validate-activity:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
}

.btn-validate-activity:active {
    transform: translateY(0);
}

/* Custom Select Dropdown */
.custom-select-wrapper {
    position: relative;
    width: 100%;
}

.custom-select-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 13px;
}

.custom-select-trigger:hover {
    border-color: #01807B;
}

.custom-select-trigger.active {
    border-color: #01807B;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.custom-select-trigger span {
    flex: 1;
    color: #2c3e50;
}

.custom-select-trigger i {
    color: #7f8c8d;
    font-size: 11px;
    transition: transform 0.2s;
}

.custom-select-trigger.active i {
    transform: rotate(180deg);
}

.custom-select-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
    max-height: 300px;
    overflow: hidden;
}

.custom-select-dropdown.active {
    display: block;
    animation: dropdownSlide 0.2s ease;
}

@keyframes dropdownSlide {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.custom-select-search {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    gap: 8px;
    background: #fafafa;
    border-radius: 10px 10px 0 0;
}

.custom-select-search i {
    color: #7f8c8d;
    font-size: 12px;
}

.custom-select-search input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    padding: 0;
}

.custom-select-search input::placeholder {
    color: #adb5bd;
}

.custom-select-options {
    max-height: 250px;
    overflow-y: auto;
    padding: 4px;
}

.custom-select-option {
    padding: 8px 10px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.15s;
    font-size: 11px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.custom-select-option:hover {
    background: #f0f9f8;
}

.custom-select-option.selected {
    background: #01807B;
    color: white;
}

.custom-select-option-name {
    flex: 1;
    font-weight: 600;
    color: #2c3e50;
}

.custom-select-option.selected .custom-select-option-name {
    color: white;
}

.custom-select-option-kcal {
    font-size: 10px;
    color: #7f8c8d;
    padding: 2px 6px;
    background: #f8f9fa;
    border-radius: 4px;
}

.custom-select-option.selected .custom-select-option-kcal {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.custom-select-category {
    padding: 8px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #01807B;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #f8f9fa;
    margin-top: 4px;
    border-radius: 4px;
}

.custom-select-category:first-child {
    margin-top: 0;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #7f8c8d;
    font-size: 11px;
}

@media (max-width: 768px) {
    .activity-modal-content {
        width: 95%;
        max-height: 85vh;
    }
}

/* ============================================
   BLOG CAROUSEL - Conseils
   ============================================ */
.blog-carousel-section {
    background: white;
    border-radius: 16px;
    padding: 16px;
    margin-top: 24px;
    margin-bottom: 24px;
}

.blog-carousel-header {
    margin-bottom: 16px;
}

.blog-carousel-title {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.blog-carousel-title i {
    color: #01807B;
    font-size: 20px;
}

.blog-carousel-container {
    position: relative;
    overflow: hidden;
    touch-action: pan-y pinch-zoom;
}

.blog-carousel-track {
    display: flex;
    gap: 12px;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding-bottom: 10px;
}

.blog-card-carousel {
    flex: 0 0 calc(33.333% - 8px);
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    height: 200px;
    transition: all 0.3s;
}

.blog-card-carousel:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}

.blog-card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.blog-card-carousel:hover .blog-card-image {
    transform: scale(1.05);
}

.blog-card-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: rgba(255,255,255,0.3);
}

.blog-card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 60%, transparent 100%);
    padding: 40px 16px 16px;
    color: white;
}

.blog-card-title-carousel {
    font-size: 15px;
    font-weight: 700;
    color: white;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-view-all-bottom {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
}

.blog-view-all {
    color: #01807B;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.blog-view-all:hover {
    color: #026660;
    text-decoration: none;
    gap: 10px;
}

.blog-carousel-dots {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 12px;
}

.blog-carousel-dot {
    width: 8px;
    height: 8px;
    background: #e9ecef;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
}

.blog-carousel-dot.active {
    width: 24px;
    border-radius: 4px;
    background: #01807B;
}

.blog-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.blog-empty-state i {
    font-size: 48px;
    opacity: 0.3;
    margin-bottom: 12px;
    display: block;
}

@media (max-width: 992px) {
    .blog-card-carousel {
        flex: 0 0 calc(50% - 6px);
    }
}

@media (max-width: 576px) {
    .blog-card-carousel {
        flex: 0 0 calc(50% - 6px);
        height: 180px;
    }

    .blog-carousel-section {
        padding: 12px;
    }

    .blog-card-title-carousel {
        font-size: 13px;
    }

    .blog-card-overlay {
        padding: 30px 12px 12px;
    }
}
</style>

<!-- Welcome Message -->
<div class="welcome-message">
    Bonjour <span class="patient-name"><?php echo htmlspecialchars($client->company); ?></span>
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
    <?php
    // Calculate BMI category
    $bmi = $patient->latest_measurement && $patient->latest_measurement->bmi ? $patient->latest_measurement->bmi : null;
    $bmi_category = 'normal';
    $bmi_label = 'Normal';
    $bmi_color = '#48bb78';
    $bmi_icon = 'fa-check-circle';

    if ($bmi !== null) {
        if ($bmi < 18.5) {
            $bmi_category = 'underweight';
            $bmi_label = 'Insuffisance pondérale';
            $bmi_color = '#3498db';
            $bmi_icon = 'fa-arrow-down';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $bmi_category = 'normal';
            $bmi_label = 'Poids normal';
            $bmi_color = '#48bb78';
            $bmi_icon = 'fa-check-circle';
        } elseif ($bmi >= 25 && $bmi < 30) {
            $bmi_category = 'overweight';
            $bmi_label = 'Surpoids';
            $bmi_color = '#f39c12';
            $bmi_icon = 'fa-exclamation-triangle';
        } else {
            $bmi_category = 'obese';
            $bmi_label = 'Obésité';
            $bmi_color = '#e74c3c';
            $bmi_icon = 'fa-exclamation-circle';
        }
    }
    ?>

    <div class="stat-card bmi enhanced-stat-card" data-category="<?php echo $bmi_category; ?>">
        <div class="stat-icon" style="color: <?php echo $bmi_color; ?>;">
            <i class="fa fa-heartbeat"></i>
        </div>
        <div class="stat-value" style="background: linear-gradient(135deg, <?php echo $bmi_color; ?> 0%, <?php echo $bmi_color; ?>dd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <?php echo $bmi ? number_format($bmi, 1) : '-'; ?>
        </div>
        <div class="stat-label">IMC</div>
        <?php if ($bmi !== null) { ?>
        <div class="stat-category">
            <i class="fa <?php echo $bmi_icon; ?>"></i>
            <span><?php echo $bmi_label; ?></span>
        </div>
        <?php } ?>
    </div>

    <?php if ($weight_progress->weight_change !== null) { ?>
    <?php
    // Calculate progression status
    $is_loss = $weight_progress->weight_change < 0;
    $progress_color = $is_loss ? '#48bb78' : '#e74c3c';
    $progress_icon = $is_loss ? 'fa-arrow-down' : 'fa-arrow-up';
    $progress_label = $is_loss ? 'Perte de poids' : 'Gain de poids';
    $progress_status = $is_loss ? 'excellent' : 'attention';
    ?>
    <div class="stat-card progress enhanced-stat-card" data-status="<?php echo $progress_status; ?>">
        <div class="stat-icon" style="color: <?php echo $progress_color; ?>;">
            <i class="fa fa-line-chart"></i>
        </div>
        <div class="stat-value" style="background: linear-gradient(135deg, <?php echo $progress_color; ?> 0%, <?php echo $progress_color; ?>dd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
        </div>
        <div class="stat-label">Progression</div>
        <div class="stat-category">
            <i class="fa <?php echo $progress_icon; ?>"></i>
            <span><?php echo $progress_label; ?></span>
        </div>
    </div>
    <?php } ?>
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

    <!-- Bloc Repas avec jauge -->
    <div class="meals-block">
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

<!-- Hydration Card - Compact Version -->
<div class="hydration-card-compact">
    <div class="hydration-header-compact">
        <div class="hydration-title-compact">
            <i class="fa fa-tint"></i>
            Hydratation
        </div>
        <div class="hydration-goal-display">
            <span id="waterLevelText">0 ml</span> consommé / <span id="waterRemainingText">2000 ml</span> restant
        </div>
    </div>

    <div class="hydration-progress-bar">
        <div class="hydration-progress-fill" id="hydrationProgressBar" style="width: 0%"></div>
    </div>

    <div class="hydration-actions-compact">
        <!-- Custom Amount Section with Quick Buttons -->
        <div class="hydration-row">
            <div class="custom-input-section">
                <label class="compact-label">Quantité personnalisée</label>
                <div class="input-with-button">
                    <input type="number"
                           id="customWaterAmount"
                           class="compact-input"
                           placeholder="Ex: 350"
                           min="1"
                           max="2000"
                           step="50">
                    <button class="add-custom-btn-inline" onclick="addCustomWater()" title="Ajouter quantité personnalisée">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>

            <div class="quick-buttons-inline">
                <button class="quick-btn-glass" onclick="addWater(250)" title="250ml">
                    <div class="water-glass-small">
                        <div class="water-fill">
                            <div class="water-wave"></div>
                            <div class="water-wave" style="animation-delay: -1s;"></div>
                        </div>
                        <div class="water-cup-add">
                            <i class="fa fa-plus"></i>
                        </div>
                        <div class="water-amount">250ml</div>
                    </div>
                </button>
                <button class="quick-btn-glass" onclick="addWater(500)" title="500ml">
                    <div class="water-glass-medium">
                        <div class="water-fill">
                            <div class="water-wave"></div>
                            <div class="water-wave" style="animation-delay: -1s;"></div>
                        </div>
                        <div class="water-cup-add">
                            <i class="fa fa-plus"></i>
                        </div>
                        <div class="water-amount">500ml</div>
                    </div>
                </button>
                <button class="quick-btn-glass" onclick="addWater(750)" title="750ml">
                    <div class="water-glass-large">
                        <div class="water-fill">
                            <div class="water-wave"></div>
                            <div class="water-wave" style="animation-delay: -1s;"></div>
                        </div>
                        <div class="water-cup-add">
                            <i class="fa fa-plus"></i>
                        </div>
                        <div class="water-amount">750ml</div>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- 7 derniers jours -->
    <div class="hydration-history-compact">
        <div class="history-title-compact">7 derniers jours</div>
        <div class="history-bars-compact" id="hydrationHistory">
            <!-- Will be filled by JavaScript -->
        </div>
    </div>
</div>

<!-- Activities Card - Simple & Clean -->
<div class="activities-card-compact">
    <!-- Header with total calories and add button -->
    <div class="activities-header-row">
        <div class="total-calories-display">
            <i class="fa fa-fire"></i>
            <span id="totalCaloriesText">0</span> kcal brûlées
        </div>
        <button class="btn-add-activity-plus" onclick="openActivityModal()" title="Ajouter une activité">
            <i class="fa fa-plus"></i>
        </button>
    </div>

    <!-- Activities List -->
    <div class="activities-simple-list" id="todayActivitiesList">
        <div class="no-activities-text">Aucune activité aujourd'hui</div>
    </div>
</div>

<!-- Activity Modal -->
<div id="activityModal" class="activity-modal">
    <div class="activity-modal-content">
        <div class="activity-modal-header">
            <h3>Ajouter une activité</h3>
            <button class="modal-close" onclick="closeActivityModal()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="activity-modal-body">
            <form id="quickActivityForm" method="POST" action="<?php echo site_url('dietetic/portal/add_activity'); ?>">
                <?php
                $csrf_name = $this->security->get_csrf_token_name();
                $csrf_hash = $this->security->get_csrf_hash();
                ?>
                <input type="hidden" name="<?php echo $csrf_name; ?>" id="csrf_field" value="<?php echo $csrf_hash; ?>">
                <input type="hidden" name="kcal_burned" id="kcalBurnedInput" value="0">
                <input type="hidden" name="activity_date" value="<?php echo date('Y-m-d'); ?>">
                <input type="hidden" name="redirect_to_dashboard" value="1">

                <div class="form-group">
                    <label>Activité sportive</label>
                    <div class="custom-select-wrapper">
                        <div class="custom-select-trigger" id="customSelectTrigger">
                            <span id="selectedActivityText">Rechercher et sélectionner...</span>
                            <i class="fa fa-chevron-down"></i>
                        </div>
                        <div class="custom-select-dropdown" id="customSelectDropdown">
                            <div class="custom-select-search">
                                <i class="fa fa-search"></i>
                                <input type="text" id="activitySearchInput" placeholder="Rechercher une activité..." autocomplete="off">
                            </div>
                            <div class="custom-select-options" id="customSelectOptions">
                                <!-- Will be filled by JavaScript -->
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="activitySelectModal" name="activity_id" required>
                </div>

                <div class="form-group">
                    <label>Durée (minutes)</label>
                    <input type="number" id="durationInputModal" name="duration_minutes" value="30" min="1" max="600" required>
                </div>

                <div class="calories-preview">
                    <i class="fa fa-fire"></i>
                    <span id="caloriesPreview">0</span> kcal
                </div>

                <button type="submit" class="btn-validate-activity">
                    <i class="fa fa-check"></i> Valider
                </button>
            </form>
        </div>
    </div>
</div>


<!-- Blog Carousel Section - Conseils -->
<?php if (!empty($blog_articles)) { ?>
<div class="blog-carousel-section">
    <div class="blog-carousel-header">
        <div class="blog-carousel-title">
            <i class="fa fa-file-text-o"></i>
            Conseils & Actualités
        </div>
    </div>

    <div class="blog-carousel-container" id="blogCarouselContainer">
        <div class="blog-carousel-track" id="blogCarouselTrack">
            <?php foreach ($blog_articles as $article) { ?>
                <a href="<?php echo site_url('dietetic/portal/blog_article/' . $article->slug); ?>" class="blog-card-carousel">
                    <?php if ($article->featured_image) : ?>
                        <img src="<?php echo base_url('uploads/blog/' . $article->featured_image); ?>"
                             alt="<?php echo htmlspecialchars($article->title); ?>"
                             class="blog-card-image">
                    <?php else : ?>
                        <div class="blog-card-placeholder">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                    <?php endif; ?>

                    <div class="blog-card-overlay">
                        <div class="blog-card-title-carousel">
                            <?php echo htmlspecialchars($article->title); ?>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="blog-carousel-dots" id="blogCarouselDots">
        <!-- Dots will be generated by JavaScript -->
    </div>

    <div class="blog-view-all-bottom">
        <a href="<?php echo site_url('dietetic/portal/blog'); ?>" class="blog-view-all">
            <span>Voir tout</span>
            <i class="fa fa-arrow-right"></i>
        </a>
    </div>
</div>
<?php } ?>

<!-- Achievements & Badges Widget -->
<?php $this->load->view('dietetic/portal/widgets/achievements_badges', ['patient' => $patient]); ?>

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

// Hydration tracking variables
let hydrationData = {
    today_total: 0,
    daily_goal: 2000,
    percentage: 0,
    history: []
};

// Load hydration data
async function loadHydrationData() {
    try {
        const response = await fetch('<?php echo site_url('dietetic/portal/api_get_hydration_data'); ?>');
        const data = await response.json();

        if (data.success) {
            hydrationData = data;
            updateHydrationDisplay();
        }
    } catch (error) {
        console.error('Error loading hydration data:', error);
    }
}

// Update water level display (compact version)
function updateHydrationDisplay() {
    const waterLevelText = document.getElementById('waterLevelText');
    const waterRemainingText = document.getElementById('waterRemainingText');
    const progressBar = document.getElementById('hydrationProgressBar');

    if (!waterLevelText || !waterRemainingText || !progressBar) {
        console.error('Hydration display elements not found');
        return;
    }

    // Calculate remaining
    const remaining = Math.max(0, hydrationData.daily_goal - hydrationData.today_total);

    // Update progress bar width (max 100%)
    const widthPercentage = Math.min(100, hydrationData.percentage);
    progressBar.style.width = widthPercentage + '%';

    // Update text
    waterLevelText.textContent = hydrationData.today_total + ' ml';
    waterRemainingText.textContent = remaining + ' ml';

    // Update history
    updateHydrationHistory();
}

// Update history bars with French days
// Update history bars with French days
function updateHydrationHistory() {
    const historyContainer = document.getElementById('hydrationHistory');

    if (!historyContainer) {
        return;
    }

    if (!hydrationData.history || hydrationData.history.length === 0) {
        historyContainer.innerHTML = '<div style="text-align: center; color: #6c757d; padding: 20px; grid-column: 1 / -1;">Aucun historique</div>';
        return;
    }

    // French day names mapping
    const frenchDays = {
        'Mon': 'Lun',
        'Tue': 'Mar',
        'Wed': 'Mer',
        'Thu': 'Jeu',
        'Fri': 'Ven',
        'Sat': 'Sam',
        'Sun': 'Dim'
    };

    // Day order for sorting (Monday = 1, Sunday = 7)
    const dayOrder = {
        'Mon': 1,
        'Tue': 2,
        'Wed': 3,
        'Thu': 4,
        'Fri': 5,
        'Sat': 6,
        'Sun': 7
    };

    // Sort history by day of week (Mon -> Sun)
    const sortedHistory = [...hydrationData.history].sort((a, b) => {
        return dayOrder[a.day_name] - dayOrder[b.day_name];
    });

    let html = '';
    sortedHistory.forEach(day => {
        const dayFr = frenchDays[day.day_name] || day.day_name;
        html += `
            <div class="history-bar-compact">
                <div class="history-bar-fill-compact">
                    <div class="history-bar-value-compact" style="height: ${day.percentage}%"></div>
                </div>
                <div class="history-day-compact">${dayFr}</div>
            </div>
        `;
    });

    historyContainer.innerHTML = html;
}

// Add water (quick buttons)
async function addWater(amount) {
    const btn = event.target.closest('.quick-btn-glass');
    if (!btn) {
        console.error('Button not found');
        return;
    }

    const waterGlass = btn.querySelector('[class^="water-glass-"]');
    const addIcon = btn.querySelector('.water-cup-add i');
    const originalIconClass = addIcon.className;

    // Disable button and show loading
    btn.disabled = true;
    addIcon.className = 'fa fa-spinner fa-spin';

    try {
        const formData = new FormData();
        formData.append('quantity_ml', amount);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        const response = await fetch('<?php echo site_url('dietetic/portal/api_add_hydration'); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update local data
            hydrationData.today_total = data.new_total;
            hydrationData.percentage = data.percentage;

            // Animate update
            updateHydrationDisplay();

            // Show success feedback with animation
            addIcon.className = 'fa fa-check';
            waterGlass.style.transform = 'scale(1.1)';
            waterGlass.style.transition = 'transform 0.3s';

            setTimeout(() => {
                waterGlass.style.transform = 'scale(1)';
                addIcon.className = originalIconClass;
                btn.disabled = false;
            }, 1500);
        } else {
            alert('Erreur: ' + (data.message || 'Impossible d\'ajouter'));
            addIcon.className = originalIconClass;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error adding water:', error);
        alert('Erreur lors de l\'ajout');
        addIcon.className = originalIconClass;
        btn.disabled = false;
    }
}

// Add custom amount
async function addCustomWater() {
    const input = document.getElementById('customWaterAmount');
    const amount = parseInt(input.value);

    if (!amount || amount <= 0 || amount > 2000) {
        alert('Veuillez entrer une quantité valide (1-2000ml)');
        return;
    }

    const btn = event.target.closest('.add-custom-btn-compact');
    if (!btn) {
        console.error('Button not found');
        return;
    }

    const originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    try {
        const formData = new FormData();
        formData.append('quantity_ml', amount);
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        const response = await fetch('<?php echo site_url('dietetic/portal/api_add_hydration'); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update local data
            hydrationData.today_total = data.new_total;
            hydrationData.percentage = data.percentage;

            // Animate update
            updateHydrationDisplay();

            // Reset input
            input.value = '';

            // Show success feedback
            btn.innerHTML = '<i class="fa fa-check"></i>';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 1500);
        } else {
            alert('Erreur: ' + (data.message || 'Impossible d\'ajouter'));
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Error adding custom water:', error);
        alert('Erreur lors de l\'ajout');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

// Load hydration data on page load
loadHydrationData();

// ============================================
// ACTIVITIES TRACKING - Simple & Clean
// ============================================

let activitiesData = [];
let selectedActivityKcalPerMin = 0;

// Load today's activities on page load
loadTodayActivities();

async function loadTodayActivities() {
    try {
        const response = await fetch('<?php echo site_url('dietetic/portal/api_get_today_activities'); ?>');
        const data = await response.json();

        if (data.success) {
            updateActivitiesDisplay(data);
        }
    } catch (error) {
        console.error('Error loading activities:', error);
    }
}

function updateActivitiesDisplay(data) {
    // Update total calories
    $('#totalCaloriesText').text(Math.round(data.total_kcal || 0));

    // Update activities list
    const listContainer = $('#todayActivitiesList');

    if (!data.activities || data.activities.length === 0) {
        listContainer.html('<div class="no-activities-text">Aucune activité aujourd\'hui</div>');
        return;
    }

    let html = '';
    data.activities.forEach(activity => {
        html += `
            <div class="activity-item-simple">
                <div class="activity-item-name">${activity.activity_name}</div>
                <div class="activity-item-details">
                    <div class="activity-item-minutes">
                        <i class="fa fa-clock-o"></i> ${activity.duration_minutes} min
                    </div>
                    <div class="activity-item-calories">
                        <i class="fa fa-fire"></i> ${Math.round(activity.kcal_burned)} kcal
                    </div>
                </div>
            </div>
        `;
    });

    listContainer.html(html);
}

// Open activity modal
async function openActivityModal() {
    const modal = document.getElementById('activityModal');
    modal.classList.add('active');

    // Load activities if not already loaded
    if (activitiesData.length === 0) {
        await loadActivitiesList();
    }
}

// Close activity modal
function closeActivityModal() {
    const modal = document.getElementById('activityModal');
    modal.classList.remove('active');
    document.getElementById('quickActivityForm').reset();
    selectedActivityKcalPerMin = 0;
    updateCaloriesPreview();
}

// Load activities list for dropdown
async function loadActivitiesList() {
    try {
        const response = await fetch('<?php echo site_url('dietetic/portal/get_activities'); ?>');
        const data = await response.json();

        if (data.success && data.activities) {
            activitiesData = data.activities;
            populateActivitiesSelect();
        }
    } catch (error) {
        console.error('Error loading activities list:', error);
    }
}

// Populate custom select with activities
function populateActivitiesSelect() {
    const optionsContainer = document.getElementById('customSelectOptions');
    if (!optionsContainer) return;

    // Group by category
    const grouped = {};
    activitiesData.forEach(activity => {
        const cat = activity.category || 'Autres';
        if (!grouped[cat]) grouped[cat] = [];
        grouped[cat].push(activity);
    });

    // Build HTML for custom select
    let html = '';
    Object.keys(grouped).sort().forEach(category => {
        html += `<div class="custom-select-category">${category}</div>`;
        grouped[category].forEach(activity => {
            html += `
                <div class="custom-select-option" data-id="${activity.id}" data-kcal="${activity.kcal_per_minute}" data-name="${activity.name}">
                    <span class="custom-select-option-name">${activity.name}</span>
                    <span class="custom-select-option-kcal">${activity.kcal_per_minute} kcal/min</span>
                </div>
            `;
        });
    });

    optionsContainer.innerHTML = html;
    initializeCustomSelect();
}

// Initialize custom select functionality
function initializeCustomSelect() {
    const trigger = document.getElementById('customSelectTrigger');
    const dropdown = document.getElementById('customSelectDropdown');
    const searchInput = document.getElementById('activitySearchInput');
    const optionsContainer = document.getElementById('customSelectOptions');
    const hiddenInput = document.getElementById('activitySelectModal');
    const selectedText = document.getElementById('selectedActivityText');

    // Toggle dropdown
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isActive = dropdown.classList.contains('active');

        if (isActive) {
            closeCustomSelect();
        } else {
            dropdown.classList.add('active');
            trigger.classList.add('active');
            searchInput.focus();
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
            closeCustomSelect();
        }
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = optionsContainer.querySelectorAll('.custom-select-option');
        const categories = optionsContainer.querySelectorAll('.custom-select-category');
        let hasResults = false;

        categories.forEach(category => {
            let categoryHasVisibleOptions = false;
            let currentCategory = category;
            let sibling = category.nextElementSibling;

            while (sibling && !sibling.classList.contains('custom-select-category')) {
                if (sibling.classList.contains('custom-select-option')) {
                    const name = sibling.dataset.name.toLowerCase();
                    if (name.includes(searchTerm)) {
                        sibling.style.display = 'flex';
                        categoryHasVisibleOptions = true;
                        hasResults = true;
                    } else {
                        sibling.style.display = 'none';
                    }
                }
                sibling = sibling.nextElementSibling;
            }

            currentCategory.style.display = categoryHasVisibleOptions ? 'block' : 'none';
        });

        // Show "no results" message
        const existingNoResults = optionsContainer.querySelector('.no-results');
        if (existingNoResults) existingNoResults.remove();

        if (!hasResults && searchTerm) {
            optionsContainer.insertAdjacentHTML('beforeend', '<div class="no-results">Aucune activité trouvée</div>');
        }
    });

    // Handle option selection
    optionsContainer.addEventListener('click', function(e) {
        const option = e.target.closest('.custom-select-option');
        if (!option) return;

        const activityId = option.dataset.id;
        const activityName = option.dataset.name;
        const kcalPerMin = option.dataset.kcal;

        // Update hidden input
        hiddenInput.value = activityId;

        // Update selected text
        selectedText.textContent = activityName;

        // Update global variable for calorie calculation
        selectedActivityKcalPerMin = parseFloat(kcalPerMin);
        updateCaloriesPreview();

        // Update UI
        optionsContainer.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        option.classList.add('selected');

        // Close dropdown
        closeCustomSelect();
    });

    function closeCustomSelect() {
        dropdown.classList.remove('active');
        trigger.classList.remove('active');
        searchInput.value = '';
        // Reset filter
        optionsContainer.querySelectorAll('.custom-select-option, .custom-select-category').forEach(el => {
            el.style.display = '';
        });
        const noResults = optionsContainer.querySelector('.no-results');
        if (noResults) noResults.remove();
    }
}

// Handle duration input and form submission
document.addEventListener('DOMContentLoaded', function() {
    const durationInput = document.getElementById('durationInputModal');

    if (durationInput) {
        durationInput.addEventListener('input', updateCaloriesPreview);
    }

    // Update hidden field before submit
    const form = document.getElementById('quickActivityForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const duration = parseInt(document.getElementById('durationInputModal').value) || 0;
            const kcalBurned = Math.round(selectedActivityKcalPerMin * duration);
            document.getElementById('kcalBurnedInput').value = kcalBurned;
            // Form will submit normally
        });
    }

    // Close modal when clicking outside
    const modal = document.getElementById('activityModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeActivityModal();
            }
        });
    }
});

// Update calories preview
function updateCaloriesPreview() {
    const duration = parseInt(document.getElementById('durationInputModal').value) || 0;
    const calories = Math.round(selectedActivityKcalPerMin * duration);
    document.getElementById('caloriesPreview').textContent = calories;
}

// ============================================
// BLOG CAROUSEL - Swipe navigation
// ============================================

let blogCurrentIndex = 0;
let blogCardsPerView = 3;
let blogTouchStartX = 0;
let blogTouchEndX = 0;

// Calculate how many cards to show based on screen size
function updateBlogCardsPerView() {
    const width = window.innerWidth;
    if (width < 576) {
        blogCardsPerView = 2; // 2 cartes sur mobile
    } else if (width < 992) {
        blogCardsPerView = 2;
    } else {
        blogCardsPerView = 3;
    }
}

function initBlogCarousel() {
    updateBlogCardsPerView();

    const track = document.getElementById('blogCarouselTrack');
    const container = document.getElementById('blogCarouselContainer');
    const dotsContainer = document.getElementById('blogCarouselDots');

    if (!track || !dotsContainer || !container) {
        return;
    }

    const totalCards = track.children.length;
    const totalPages = Math.ceil(totalCards / blogCardsPerView);

    // Generate dots
    dotsContainer.innerHTML = '';
    for (let i = 0; i < totalPages; i++) {
        const dot = document.createElement('div');
        dot.className = 'blog-carousel-dot' + (i === 0 ? ' active' : '');
        dot.onclick = () => blogGoToPage(i);
        dotsContainer.appendChild(dot);
    }

    // Add swipe/touch events
    container.addEventListener('touchstart', blogHandleTouchStart, false);
    container.addEventListener('touchmove', blogHandleTouchMove, false);
    container.addEventListener('touchend', blogHandleTouchEnd, false);

    // Add mouse drag events for desktop
    let isDragging = false;
    let startX = 0;

    container.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.pageX;
        container.style.cursor = 'grabbing';
    });

    container.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
    });

    container.addEventListener('mouseup', (e) => {
        if (!isDragging) return;
        isDragging = false;
        container.style.cursor = 'grab';

        const endX = e.pageX;
        const diff = startX - endX;

        if (Math.abs(diff) > 50) { // Threshold pour trigger le swipe
            if (diff > 0) {
                blogCarouselNext();
            } else {
                blogCarouselPrev();
            }
        }
    });

    container.addEventListener('mouseleave', () => {
        isDragging = false;
        container.style.cursor = 'grab';
    });

    updateBlogCarousel();
}

function blogHandleTouchStart(e) {
    blogTouchStartX = e.changedTouches[0].screenX;
}

function blogHandleTouchMove(e) {
    blogTouchEndX = e.changedTouches[0].screenX;
}

function blogHandleTouchEnd() {
    const diff = blogTouchStartX - blogTouchEndX;

    if (Math.abs(diff) > 50) { // Threshold pour trigger le swipe
        if (diff > 0) {
            // Swipe left - next
            blogCarouselNext();
        } else {
            // Swipe right - prev
            blogCarouselPrev();
        }
    }
}

function blogCarouselNext() {
    const track = document.getElementById('blogCarouselTrack');
    if (!track) return;

    const totalCards = track.children.length;
    const totalPages = Math.ceil(totalCards / blogCardsPerView);

    if (blogCurrentIndex < totalPages - 1) {
        blogCurrentIndex++;
        updateBlogCarousel();
    }
}

function blogCarouselPrev() {
    if (blogCurrentIndex > 0) {
        blogCurrentIndex--;
        updateBlogCarousel();
    }
}

function blogGoToPage(pageIndex) {
    blogCurrentIndex = pageIndex;
    updateBlogCarousel();
}

function updateBlogCarousel() {
    const track = document.getElementById('blogCarouselTrack');
    const dots = document.querySelectorAll('.blog-carousel-dot');

    if (!track) return;

    const totalCards = track.children.length;
    const totalPages = Math.ceil(totalCards / blogCardsPerView);

    // Calculate the offset
    const offset = -(blogCurrentIndex * 100);
    track.style.transform = `translateX(${offset}%)`;

    // Update dots
    dots.forEach((dot, index) => {
        if (index === blogCurrentIndex) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// Initialize on page load and window resize
$(document).ready(function() {
    initBlogCarousel();
});

window.addEventListener('resize', function() {
    updateBlogCardsPerView();
    initBlogCarousel();
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
