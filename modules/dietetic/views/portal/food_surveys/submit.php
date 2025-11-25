<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'food_surveys';
$page_title = isset($title) ? $title : 'Soumission Quotidienne';
$this->load->view('portal/includes/portal_header');
?>

    <style>
    :root {
        --primary-color: #01807B;
        --primary-dark: #015a57;
        --primary-light: #019B95;
        --secondary-color: #F3911D;
        --secondary-dark: #e07d0f;
        --secondary-light: #FFA74D;
        --tertiary-color: #FFFFFF;
        --text-dark: #1a202c;
        --text-medium: #2d3748;
        --text-light: #718096;
        --border-color: #e2e8f0;
        --border-light: #edf2f7;
        --success-color: #48bb78;
        --success-light: #9ae6b4;
        --danger-color: #f56565;
        --warning-color: #ed8936;
        --info-color: #4299e1;
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 8px 20px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 12px 28px rgba(0, 0, 0, 0.12);
        --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-bounce: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

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

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #f0f4f8 0%, #e8eff5 50%, #f5f7fa 100%);
        background-attachment: fixed;
        margin: 0;
        padding: 0;
        padding-top: 50px;
        padding-bottom: 80px;
        min-height: 100vh;
        color: var(--text-medium);
        line-height: 1.6;
    }

    /* Override content-container padding for this page */
    .content-container {
        padding: 0 !important;
    }

    .container-fluid {
        padding: 15px;
        max-width: calc(100% - 30px);
        margin: 0 auto;
    }

    @media (min-width: 1400px) {
        .container-fluid {
            max-width: 1800px;
        }
    }

    /* Page header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.5s ease-out;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.05) 100%);
        pointer-events: none;
    }

    .page-header h1 {
        margin: 0 0 8px 0;
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .page-header h1 i {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px;
        border-radius: 10px;
        font-size: 20px;
    }

    .page-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 15px;
        position: relative;
        z-index: 1;
    }

    /* Date Navigation */
    .date-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 1;
    }

    .date-nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        color: white;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .date-nav-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transition: left 0.3s ease;
    }

    .date-nav-btn:hover:not(.disabled)::before {
        left: 100%;
    }

    .date-nav-btn:hover:not(.disabled) {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .date-nav-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .date-display {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 24px;
        background: white;
        color: var(--primary-color);
        border-radius: 12px;
        font-weight: 700;
        font-size: 17px;
        box-shadow: var(--shadow-md);
        transition: var(--transition-fast);
    }

    .date-display:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }

    .date-display.is-today {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-light) 100%);
        color: white;
        box-shadow: 0 8px 24px rgba(243, 145, 29, 0.3);
    }

    .date-display i {
        font-size: 18px;
    }

    .today-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .date-navigation {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }

        .date-nav-btn {
            flex: 0 0 auto;
        }

        .date-display {
            flex: 1 1 100%;
            order: -1;
            justify-content: center;
        }

        .date-nav-label {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 18px;
        }

        .container-fluid {
            padding: 15px;
        }
    }

    /* Form sections */
    .form-section {
        background: white;
        padding: 35px;
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        margin-bottom: 30px;
        border: 1px solid var(--border-light);
        transition: var(--transition);
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-section:nth-child(1) { animation-delay: 0.1s; }
    .form-section:nth-child(2) { animation-delay: 0.2s; }
    .form-section:nth-child(3) { animation-delay: 0.3s; }

    .form-section:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .section-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding-bottom: 18px;
        border-bottom: 3px solid transparent;
        background: linear-gradient(to right, var(--border-color) 0%, transparent 100%) bottom no-repeat;
        background-size: 100% 3px;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .section-title i {
        color: white;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        padding: 12px;
        border-radius: 12px;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(1, 128, 123, 0.25);
    }

    /* Meal card */
    .meal-card {
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 24px;
        transition: var(--transition);
        background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .meal-card:hover {
        box-shadow: var(--shadow-md);
    }

    .meal-card:last-child {
        margin-bottom: 0;
    }

    .meal-card.has-photo {
        border-color: var(--success-color);
        border-width: 2px;
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.03) 0%, rgba(72, 187, 120, 0.08) 100%);
        box-shadow: 0 4px 16px rgba(72, 187, 120, 0.15);
    }

    .meal-card.has-photo:hover {
        box-shadow: 0 8px 24px rgba(72, 187, 120, 0.25);
    }

    .meal-card-title {
        font-size: 19px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border-light);
    }

    .meal-card-title i {
        color: var(--secondary-color);
        background: rgba(243, 145, 29, 0.1);
        padding: 10px;
        border-radius: 10px;
        font-size: 18px;
        transition: var(--transition-bounce);
    }

    .meal-card:hover .meal-card-title i {
        transform: scale(1.1) rotate(5deg);
    }

    /* Photo upload */
    .photo-upload-area {
        border: 3px dashed var(--border-color);
        border-radius: 16px;
        padding: 45px 25px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        background: linear-gradient(135deg, #f8fafb 0%, #f0f4f7 100%);
        position: relative;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .photo-upload-area::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        height: 80%;
        background: radial-gradient(circle, rgba(1, 128, 123, 0.05) 0%, transparent 70%);
        opacity: 0;
        transition: var(--transition);
    }

    .photo-upload-area:hover::before {
        opacity: 1;
    }

    .photo-upload-area:hover {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(1, 128, 123, 0.03) 0%, rgba(1, 128, 123, 0.08) 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .photo-upload-area.dragging {
        border-color: var(--secondary-color);
        border-width: 3px;
        background: linear-gradient(135deg, rgba(243, 145, 29, 0.08) 0%, rgba(243, 145, 29, 0.12) 100%);
        transform: scale(1.02);
        box-shadow: var(--shadow-lg);
    }

    .photo-upload-icon {
        font-size: 56px;
        color: var(--primary-color);
        margin-bottom: 16px;
        transition: var(--transition-bounce);
        display: inline-block;
    }

    .photo-upload-area:hover .photo-upload-icon {
        transform: scale(1.1);
        color: var(--primary-dark);
    }

    .photo-upload-area.dragging .photo-upload-icon {
        animation: pulse 0.6s ease-in-out infinite;
        color: var(--secondary-color);
    }

    .photo-upload-text {
        color: var(--text-dark);
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .photo-upload-hint {
        color: var(--text-light);
        font-size: 14px;
        font-weight: 500;
    }

    .photo-upload-hint i {
        margin-right: 5px;
    }

    input[type="file"] {
        display: none;
    }

    .photo-preview {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: var(--shadow-md);
        border: 3px solid var(--success-color);
        transition: var(--transition);
        animation: scaleIn 0.4s ease-out;
    }

    .photo-preview:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .photo-preview img {
        width: 100%;
        height: auto;
        display: block;
        transition: var(--transition);
    }

    .photo-preview:hover img {
        transform: scale(1.02);
    }

    .photo-preview-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(245, 101, 101, 0.95);
        color: white;
        border: 2px solid white;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-bounce);
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.4);
        backdrop-filter: blur(4px);
    }

    .photo-preview-remove:hover {
        background: var(--danger-color);
        transform: scale(1.15) rotate(90deg);
        box-shadow: 0 6px 20px rgba(245, 101, 101, 0.6);
    }

    .photo-preview-remove:active {
        transform: scale(0.95);
    }

    /* Form groups */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: flex;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 10px;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: var(--secondary-color);
        font-size: 16px;
        background: rgba(243, 145, 29, 0.1);
        padding: 6px;
        border-radius: 6px;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 15px;
        font-family: inherit;
        transition: var(--transition);
        background: white;
        color: var(--text-medium);
    }

    .form-control:hover {
        border-color: var(--primary-light);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(1, 128, 123, 0.12);
        background: white;
        transform: translateY(-1px);
    }

    .time-input {
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        background: white;
    }

    .time-input::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(48%) sepia(79%) saturate(346%) hue-rotate(135deg) brightness(95%) contrast(91%);
    }

    .time-hint {
        display: block;
        color: var(--text-light);
        font-size: 12px;
        margin-top: 5px;
        font-style: italic;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
        line-height: 1.6;
    }

    /* Beverages section */
    .beverage-list {
        margin-top: 24px;
    }

    .beverage-item {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 16px;
        margin-bottom: 18px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafb 0%, #f0f4f7 100%);
        border: 2px solid var(--border-light);
        border-radius: 12px;
        align-items: end;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .beverage-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, var(--info-color), var(--primary-color));
        opacity: 0;
        transition: var(--transition);
    }

    .beverage-item:hover {
        border-color: var(--info-color);
        box-shadow: var(--shadow);
        transform: translateX(3px);
    }

    .beverage-item:hover::before {
        opacity: 1;
    }

    .beverage-item button {
        background: linear-gradient(135deg, var(--danger-color), #e53e3e);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 14px 16px;
        cursor: pointer;
        transition: var(--transition-bounce);
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(245, 101, 101, 0.25);
    }

    .beverage-item button:hover {
        background: linear-gradient(135deg, #e53e3e, #c53030);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 16px rgba(245, 101, 101, 0.35);
    }

    .beverage-item button:active {
        transform: scale(0.95);
    }

    .add-beverage-btn {
        background: linear-gradient(135deg, var(--secondary-color), var(--secondary-light));
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-bounce);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        box-shadow: 0 4px 16px rgba(243, 145, 29, 0.25);
        position: relative;
        overflow: hidden;
    }

    .add-beverage-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .add-beverage-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .add-beverage-btn:hover {
        background: linear-gradient(135deg, var(--secondary-dark), var(--secondary-color));
        transform: translateY(-3px);
        box-shadow: 0 6px 24px rgba(243, 145, 29, 0.35);
    }

    .add-beverage-btn:active {
        transform: translateY(-1px);
    }

    .add-beverage-btn i {
        font-size: 18px;
        transition: var(--transition-bounce);
    }

    .add-beverage-btn:hover i {
        transform: rotate(90deg);
    }

    /* Submit button */
    .submit-section {
        background: linear-gradient(135deg, white 0%, #f8fafb 100%);
        padding: 35px;
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        text-align: center;
        border: 2px solid var(--border-light);
        animation: fadeInUp 0.8s ease-out;
        animation-delay: 0.4s;
        animation-fill-mode: both;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 18px 48px;
        border-radius: 12px;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-bounce);
        display: inline-flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 8px 24px rgba(1, 128, 123, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 32px rgba(1, 128, 123, 0.4);
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
    }

    .btn-submit:active {
        transform: translateY(-2px) scale(0.98);
    }

    .btn-submit i {
        font-size: 20px;
        transition: var(--transition-bounce);
    }

    .btn-submit:hover i {
        transform: scale(1.2);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: var(--shadow);
    }

    .btn-submit:disabled:hover {
        transform: none;
        box-shadow: var(--shadow);
    }

    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-content {
        background: white;
        padding: 45px 55px;
        border-radius: 20px;
        text-align: center;
        box-shadow: var(--shadow-xl);
        animation: scaleIn 0.3s ease-out;
    }

    .loading-content p {
        color: var(--text-dark);
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .spinner {
        border: 5px solid var(--border-light);
        border-top-color: var(--primary-color);
        border-right-color: var(--secondary-color);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        margin: 0 auto 24px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Alert messages */
    .alert {
        padding: 18px 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow);
        animation: fadeInUp 0.4s ease-out;
        border: 2px solid transparent;
        font-weight: 500;
        font-size: 15px;
    }

    .alert i {
        font-size: 22px;
        flex-shrink: 0;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(72, 187, 120, 0.1) 0%, rgba(72, 187, 120, 0.15) 100%);
        color: #2f855a;
        border-left: 5px solid var(--success-color);
        border-color: rgba(72, 187, 120, 0.3);
    }

    .alert-success i {
        color: var(--success-color);
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(245, 101, 101, 0.1) 0%, rgba(245, 101, 101, 0.15) 100%);
        color: #c53030;
        border-left: 5px solid var(--danger-color);
        border-color: rgba(245, 101, 101, 0.3);
    }

    .alert-danger i {
        color: var(--danger-color);
    }

    .alert-info {
        background: linear-gradient(135deg, rgba(66, 153, 225, 0.1) 0%, rgba(66, 153, 225, 0.15) 100%);
        color: #2c5282;
        border-left: 5px solid var(--info-color);
        border-color: rgba(66, 153, 225, 0.3);
    }

    .alert-info i {
        color: var(--info-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-section {
            padding: 20px;
        }

        .beverage-item {
            grid-template-columns: 1fr;
        }
    }
    </style>

    <div class="container-fluid">
        <!-- Page header -->
        <div class="page-header">
            <h1>
                <i class="fa fa-camera"></i>
                <?php echo htmlspecialchars($survey->survey_name); ?>
            </h1>

            <!-- Date Navigation -->
            <div class="date-navigation">
                <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id . '/' . $prev_date); ?>" class="date-nav-btn">
                    <i class="fa fa-chevron-left"></i>
                    <span class="date-nav-label">Jour précédent</span>
                </a>

                <div class="date-display <?php echo $is_today ? 'is-today' : ''; ?>">
                    <i class="fa fa-calendar"></i>
                    <span><?php echo date('d/m/Y', strtotime($selected_date)); ?></span>
                    <?php if ($is_today): ?>
                        <span class="today-badge">Aujourd'hui</span>
                    <?php endif; ?>
                </div>

                <?php if ($can_go_next): ?>
                    <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id . '/' . $next_date); ?>" class="date-nav-btn">
                        <span class="date-nav-label">Jour suivant</span>
                        <i class="fa fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <div class="date-nav-btn disabled">
                        <span class="date-nav-label">Jour suivant</span>
                        <i class="fa fa-chevron-right"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alert area -->
        <div id="alertArea"></div>

        <form id="dailyEntryForm">
            <input type="hidden" name="survey_id" value="<?php echo $survey->id; ?>">
            <input type="hidden" name="entry_date" value="<?php echo $selected_date; ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <!-- Meals Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-cutlery"></i>
                    Repas de la journée
                </div>

                <!-- Breakfast -->
                <div class="meal-card <?php echo ($today_entry && $today_entry->breakfast_photo) ? 'has-photo' : ''; ?>" id="breakfastCard">
                    <div class="meal-card-title">
                        <i class="fa fa-coffee"></i>
                        Petit-déjeuner
                    </div>

                    <div class="photo-upload-area" data-meal="breakfast" style="<?php echo ($today_entry && $today_entry->breakfast_photo) ? 'display: none;' : ''; ?>">
                        <input type="file" id="breakfastPhoto" accept="image/*" data-meal="breakfast">
                        <input type="hidden" name="breakfast_photo" id="breakfast_photo_value" value="<?php echo $today_entry ? htmlspecialchars($today_entry->breakfast_photo) : ''; ?>">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="breakfastPreview" class="photo-preview" style="<?php echo ($today_entry && $today_entry->breakfast_photo) ? '' : 'display: none;'; ?>">
                        <img src="<?php echo ($today_entry && $today_entry->breakfast_photo) ? base_url('uploads/dietetic/food_surveys/' . $today_entry->breakfast_photo) : ''; ?>" alt="Petit-déjeuner">
                        <button type="button" class="photo-preview-remove" data-meal="breakfast">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="breakfast_time">
                            <i class="fa fa-clock-o"></i> Heure du repas
                        </label>
                        <input type="time" class="form-control time-input" name="breakfast_time" id="breakfast_time"
                               value="<?php echo $today_entry && $today_entry->breakfast_time ? date('H:i', strtotime($today_entry->breakfast_time)) : '07:30'; ?>"
                               placeholder="07:30">
                        <small class="time-hint">Recommandé: entre 7h et 10h</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="breakfast_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="breakfast_notes" id="breakfast_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->breakfast_notes) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Lunch -->
                <div class="meal-card <?php echo ($today_entry && $today_entry->lunch_photo) ? 'has-photo' : ''; ?>" id="lunchCard">
                    <div class="meal-card-title">
                        <i class="fa fa-sun-o"></i>
                        Déjeuner
                    </div>

                    <div class="photo-upload-area" data-meal="lunch" style="<?php echo ($today_entry && $today_entry->lunch_photo) ? 'display: none;' : ''; ?>">
                        <input type="file" id="lunchPhoto" accept="image/*" data-meal="lunch">
                        <input type="hidden" name="lunch_photo" id="lunch_photo_value" value="<?php echo $today_entry ? htmlspecialchars($today_entry->lunch_photo) : ''; ?>">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="lunchPreview" class="photo-preview" style="<?php echo ($today_entry && $today_entry->lunch_photo) ? '' : 'display: none;'; ?>">
                        <img src="<?php echo ($today_entry && $today_entry->lunch_photo) ? base_url('uploads/dietetic/food_surveys/' . $today_entry->lunch_photo) : ''; ?>" alt="Déjeuner">
                        <button type="button" class="photo-preview-remove" data-meal="lunch">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lunch_time">
                            <i class="fa fa-clock-o"></i> Heure du repas
                        </label>
                        <input type="time" class="form-control time-input" name="lunch_time" id="lunch_time"
                               value="<?php echo $today_entry && $today_entry->lunch_time ? date('H:i', strtotime($today_entry->lunch_time)) : '13:00'; ?>"
                               placeholder="13:00">
                        <small class="time-hint">Recommandé: entre 12h et 15h</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lunch_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="lunch_notes" id="lunch_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->lunch_notes) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Dinner -->
                <div class="meal-card <?php echo ($today_entry && $today_entry->dinner_photo) ? 'has-photo' : ''; ?>" id="dinnerCard">
                    <div class="meal-card-title">
                        <i class="fa fa-moon-o"></i>
                        Dîner
                    </div>

                    <div class="photo-upload-area" data-meal="dinner" style="<?php echo ($today_entry && $today_entry->dinner_photo) ? 'display: none;' : ''; ?>">
                        <input type="file" id="dinnerPhoto" accept="image/*" data-meal="dinner">
                        <input type="hidden" name="dinner_photo" id="dinner_photo_value" value="<?php echo $today_entry ? htmlspecialchars($today_entry->dinner_photo) : ''; ?>">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="dinnerPreview" class="photo-preview" style="<?php echo ($today_entry && $today_entry->dinner_photo) ? '' : 'display: none;'; ?>">
                        <img src="<?php echo ($today_entry && $today_entry->dinner_photo) ? base_url('uploads/dietetic/food_surveys/' . $today_entry->dinner_photo) : ''; ?>" alt="Dîner">
                        <button type="button" class="photo-preview-remove" data-meal="dinner">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="dinner_time">
                            <i class="fa fa-clock-o"></i> Heure du repas
                        </label>
                        <input type="time" class="form-control time-input" name="dinner_time" id="dinner_time"
                               value="<?php echo $today_entry && $today_entry->dinner_time ? date('H:i', strtotime($today_entry->dinner_time)) : '20:00'; ?>"
                               placeholder="20:00">
                        <small class="time-hint">Recommandé: entre 19h et 22h</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="dinner_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="dinner_notes" id="dinner_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->dinner_notes) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Snack / Collation -->
                <div class="meal-card <?php echo ($today_entry && !empty($today_entry->snack_photo)) ? 'has-photo' : ''; ?>" id="snackCard">
                    <div class="meal-card-title">
                        <i class="fa fa-apple"></i>
                        Collation
                    </div>

                    <div class="photo-upload-area" data-meal="snack" style="<?php echo ($today_entry && !empty($today_entry->snack_photo)) ? 'display: none;' : ''; ?>">
                        <input type="file" id="snackPhoto" accept="image/*" data-meal="snack">
                        <input type="hidden" name="snack_photo" id="snack_photo_value" value="<?php echo ($today_entry && !empty($today_entry->snack_photo)) ? htmlspecialchars($today_entry->snack_photo) : ''; ?>">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="snackPreview" class="photo-preview" style="<?php echo ($today_entry && !empty($today_entry->snack_photo)) ? '' : 'display: none;'; ?>">
                        <img src="<?php echo ($today_entry && !empty($today_entry->snack_photo)) ? base_url('uploads/dietetic/food_surveys/' . $today_entry->snack_photo) : ''; ?>" alt="Collation">
                        <button type="button" class="photo-preview-remove" data-meal="snack">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="snack_time">
                            <i class="fa fa-clock-o"></i> Heure de la collation
                        </label>
                        <input type="time" class="form-control time-input" name="snack_time" id="snack_time"
                               value="<?php echo ($today_entry && !empty($today_entry->snack_time)) ? date('H:i', strtotime($today_entry->snack_time)) : '16:00'; ?>"
                               placeholder="16:00">
                        <small class="time-hint">Recommandé: entre 15h et 18h</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="snack_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="snack_notes" id="snack_notes"
                                  placeholder="Décrivez votre collation..."><?php echo ($today_entry && !empty($today_entry->snack_notes)) ? htmlspecialchars($today_entry->snack_notes) : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Water Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-tint"></i>
                    Consommation d'eau
                </div>

                <div class="form-group">
                    <label class="form-label" for="water_quantity_ml">Quantité d'eau consommée (ml)</label>
                    <input type="number" class="form-control" name="water_quantity_ml" id="water_quantity_ml"
                           placeholder="Ex: 2000" min="0" step="100"
                           value="<?php echo $today_entry ? $today_entry->water_quantity_ml : ''; ?>">
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">
                        <i class="fa fa-info-circle"></i>
                        Recommandé: 2000 ml (2 litres) par jour
                    </small>
                </div>
            </div>

            <!-- Beverages Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-coffee"></i>
                    Autres boissons
                </div>

                <div id="beveragesList" class="beverage-list"></div>

                <button type="button" class="add-beverage-btn" onclick="addBeverageRow()">
                    <i class="fa fa-plus-circle"></i>
                    Ajouter une boisson
                </button>
            </div>

            <!-- Submit Section -->
            <div class="submit-section">
                <button type="submit" class="btn-submit">
                    <i class="fa fa-check-circle"></i>
                    Soumettre l'entrée
                </button>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Téléchargement en cours...</p>
        </div>
    </div>

    <!-- jQuery & Bootstrap JS from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
    // Base URL for AJAX calls (site_url is already defined in portal_footer.php)
    const base_url = '<?php echo base_url(); ?>';
    let beverageCount = 0;

    // Photo upload handling
    $(document).ready(function() {
        // Click to upload - éviter la récursion
        $('.photo-upload-area').on('click', function(e) {
            // Si on clique sur l'input file lui-même, ne rien faire
            if ($(e.target).is('input[type="file"]')) {
                return;
            }
            const meal = $(this).data('meal');
            $('#' + meal + 'Photo').click();
        });

        // File input change
        $('input[type="file"]').on('change', function() {
            const meal = $(this).data('meal');
            const file = this.files[0];

            if (file) {
                uploadPhoto(meal, file);
            }
        }).on('click', function(e) {
            // Empêcher la propagation au parent pour éviter la récursion
            e.stopPropagation();
        });

        // Drag and drop
        $('.photo-upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragging');
        });

        $('.photo-upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragging');
        });

        $('.photo-upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragging');

            const meal = $(this).data('meal');
            const file = e.originalEvent.dataTransfer.files[0];

            if (file && file.type.startsWith('image/')) {
                uploadPhoto(meal, file);
            } else {
                showAlert('Veuillez déposer un fichier image valide', 'danger');
            }
        });

        // Remove photo
        $('.photo-preview-remove').on('click', function(e) {
            e.stopPropagation();
            const meal = $(this).data('meal');
            removePhoto(meal);
        });

        // Form submission
        $('#dailyEntryForm').on('submit', function(e) {
            e.preventDefault();
            submitEntry();
        });

        // Load existing beverages if editing
        <?php if (!empty($beverages)): ?>
            <?php foreach ($beverages as $beverage): ?>
                addBeverageRow(
                    '<?php echo htmlspecialchars($beverage->beverage_name); ?>',
                    '<?php echo $beverage->quantity_ml; ?>',
                    '<?php echo date('H:i', strtotime($beverage->consumption_time)); ?>',
                    '<?php echo htmlspecialchars($beverage->notes ?? ''); ?>'
                );
            <?php endforeach; ?>
        <?php endif; ?>
    });

    // Upload photo
    function uploadPhoto(meal, file) {
        $('#loadingOverlay').addClass('active');

        const formData = new FormData();
        formData.append('photo', file);

        // Add CSRF token for Perfex CRM
        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

        $.ajax({
            url: site_url + 'dietetic/portal/upload_photo',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('#loadingOverlay').removeClass('active');

                if (response.success) {
                    // Hide upload area
                    $('.photo-upload-area[data-meal="' + meal + '"]').hide();

                    // Show preview
                    $('#' + meal + 'Preview').show();
                    $('#' + meal + 'Preview img').attr('src', response.url);

                    // Store filename
                    $('#' + meal + '_photo_value').val(response.filename);

                    // Mark card as having photo
                    $('#' + meal + 'Card').addClass('has-photo');

                    showAlert('Photo téléchargée avec succès', 'success');
                } else {
                    showAlert(response.message || 'Erreur lors du téléchargement', 'danger');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingOverlay').removeClass('active');
                console.error('Upload error:', xhr.responseText || error);

                // Show more detailed error message
                let errorMsg = 'Erreur lors du téléchargement de la photo';
                if (xhr.status === 419) {
                    errorMsg = 'Erreur de sécurité (CSRF). Veuillez rafraîchir la page.';
                } else if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMsg = errorData.message;
                        }
                    } catch (e) {
                        // Keep default message
                    }
                }
                showAlert(errorMsg, 'danger');
            }
        });
    }

    // Remove photo
    function removePhoto(meal) {
        const filename = $('#' + meal + '_photo_value').val();

        if (filename) {
            // Delete from server
            $.ajax({
                url: site_url + 'dietetic/portal/delete_photo',
                type: 'POST',
                data: {
                    filename: filename,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('Photo supprimée', 'success');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', xhr.responseText || error);
                }
            });
        }

        // Reset UI
        $('#' + meal + 'Preview').hide();
        $('#' + meal + 'Preview img').attr('src', '');
        $('#' + meal + '_photo_value').val('');
        $('.photo-upload-area[data-meal="' + meal + '"]').show();
        $('#' + meal + 'Card').removeClass('has-photo');
        $('#' + meal + 'Photo').val('');
    }

    // Add beverage row
    function addBeverageRow(name = '', quantity = '', time = '', notes = '') {
        beverageCount++;

        const html = `
            <div class="beverage-item" id="beverage_${beverageCount}">
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Nom de la boisson</label>
                    <input type="text" class="form-control" name="beverages[${beverageCount}][name]"
                           placeholder="Ex: Café, Jus d'orange..." value="${name}" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Quantité (ml)</label>
                    <input type="number" class="form-control" name="beverages[${beverageCount}][quantity]"
                           placeholder="250" min="0" value="${quantity}" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Heure</label>
                    <input type="time" class="form-control" name="beverages[${beverageCount}][time]"
                           value="${time}" required>
                </div>
                <button type="button" onclick="removeBeverageRow(${beverageCount})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;

        $('#beveragesList').append(html);
    }

    // Remove beverage row
    function removeBeverageRow(id) {
        $('#beverage_' + id).fadeOut(300, function() {
            $(this).remove();
        });
    }

    // Submit entry
    function submitEntry() {
        const formData = $('#dailyEntryForm').serialize();

        $('#loadingOverlay').addClass('active');
        $('.btn-submit').prop('disabled', true);

        $.ajax({
            url: site_url + 'dietetic/portal/save_daily_entry',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#loadingOverlay').removeClass('active');
                $('.btn-submit').prop('disabled', false);

                if (response.success) {
                    showAlert('Entrée enregistrée avec succès ! Redirection...', 'success');
                    setTimeout(function() {
                        window.location.href = site_url + 'dietetic/portal/food_surveys';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingOverlay').removeClass('active');
                $('.btn-submit').prop('disabled', false);

                console.error('Submit error:', xhr.responseText || error);

                // Show more detailed error message
                let errorMsg = 'Erreur lors de l\'enregistrement. Veuillez réessayer.';
                if (xhr.status === 419) {
                    errorMsg = 'Erreur de sécurité (CSRF). Veuillez rafraîchir la page.';
                } else if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMsg = errorData.message;
                        }
                    } catch (e) {
                        // Keep default message
                    }
                }
                showAlert(errorMsg, 'danger');
            }
        });
    }

    // Show alert
    function showAlert(message, type) {
        const icon = type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : 'info-circle');

        const html = `
            <div class="alert alert-${type}">
                <i class="fa fa-${icon}"></i>
                ${message}
            </div>
        `;

        $('#alertArea').html(html);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#alertArea').fadeOut(300, function() {
                $(this).html('').show();
            });
        }, 5000);
    }

    </script>

    <!-- Include Audio Recorder Component -->
    <?php $this->load->view('portal/food_surveys/audio_recorder_component'); ?>

    <script>
    // Add audio recorders to each meal notes section
    document.addEventListener('DOMContentLoaded', function() {
        const mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        const entryId = <?php echo $today_entry ? $today_entry->id : 'null'; ?>;

        if (entryId) {
            mealTypes.forEach(mealType => {
                const notesField = document.getElementById(mealType + '_notes');
                if (notesField && notesField.closest('.form-group')) {
                    const formGroup = notesField.closest('.form-group');

                    // Create audio recorder HTML
                    const audioRecorderHTML = `
                        <div class="audio-recorder-wrapper" id="audio-recorder-${mealType}">
                            <div class="audio-recorder-controls">
                                <button type="button" class="audio-record-btn record" id="record-btn-${mealType}" onclick="toggleRecording('${mealType}', ${entryId})">
                                    <i class="fa fa-microphone"></i>
                                    Enregistrer une note vocale
                                </button>
                                <button type="button" class="audio-record-btn stop" id="stop-btn-${mealType}" style="display: none;" onclick="toggleRecording('${mealType}', ${entryId})">
                                    <i class="fa fa-stop"></i>
                                    Arrêter
                                </button>
                                <div class="audio-recording-indicator" id="recording-indicator-${mealType}" style="display: none;">
                                    <div class="audio-recording-pulse"></div>
                                    Enregistrement en cours...
                                </div>
                            </div>
                            <div class="audio-player-list" id="audio-list-${mealType}">
                                <p style="color: #6c757d; font-size: 13px; margin: 10px 0;">Chargement...</p>
                            </div>
                        </div>
                    `;

                    // Insert after form-group
                    formGroup.insertAdjacentHTML('afterend', audioRecorderHTML);

                    // Load existing audio notes
                    loadAudioNotes(mealType, entryId);
                }
            });
        }
    });
    </script>

<?php $this->load->view("portal/includes/portal_footer"); ?>
