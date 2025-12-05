<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Portail Patient</title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
        }

        body {
            background: #f8f9fa;
            font-family: 'Josefin Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            padding-top: 60px; /* Space for fixed header */
            padding-bottom: 60px; /* Space for fixed footer */
        }

        /* ============================================
           HEADER MAGNIFIQUE
           ============================================ */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #f8f9fa;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }


        /* Hamburger Menu Button - Mobile App Style */
        .hamburger-btn {
            width: 44px;
            height: 44px;
            background: transparent;
            border: none;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            padding: 8px;
        }

        .hamburger-btn:hover {
            background: #e9ecef;
        }

        .hamburger-btn:active {
            transform: scale(0.9);
        }

        .hamburger-btn i {
            font-size: 24px;
            color: #2c3e50;
            transition: all 0.3s;
        }

        .hamburger-btn.active i {
            transform: rotate(90deg);
        }

        /* Notification Button */
        /* Notification Button - Enhanced Modern Design */
        .notification-btn {
            width: 44px;
            height: 44px;
            background: rgba(1, 128, 123, 0.05);
            border: 2px solid transparent;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            position: relative;
            overflow: hidden;
        }

        .notification-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(1, 128, 123, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .notification-btn:hover::before {
            width: 100px;
            height: 100px;
        }

        .notification-btn:hover {
            background: rgba(1, 128, 123, 0.1);
            border-color: rgba(1, 128, 123, 0.2);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.15);
        }

        .notification-btn:active {
            transform: scale(0.95);
        }

        .notification-btn i {
            font-size: 22px;
            color: #01807B;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .notification-btn:hover i {
            transform: scale(1.1) rotate(15deg);
        }

        /* Notification Badge - Ultra Modern */
        .notification-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%);
            color: white;
            font-size: 10px;
            font-weight: 800;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            box-shadow: 0 3px 8px rgba(255, 71, 87, 0.4), 0 0 0 3px rgba(255, 71, 87, 0.15);
            border: 2px solid white;
            animation: badgePulse 2s ease-in-out infinite;
            z-index: 10;
        }

        @keyframes badgePulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 3px 8px rgba(255, 71, 87, 0.4), 0 0 0 3px rgba(255, 71, 87, 0.15);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(255, 71, 87, 0.5), 0 0 0 5px rgba(255, 71, 87, 0.25);
            }
        }

        .notification-badge::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 25%;
            width: 40%;
            height: 40%;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            filter: blur(2px);
        }

        /* Header Profile Button */
        .header-profile-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #01807B, #F3911D, #01807B);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            margin-right: auto; /* Push to the left */
            padding: 2px;
            position: relative;
        }

        .header-profile-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 2px;
            background: linear-gradient(135deg, #01807B, #F3911D, #4CAF50, #01807B);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 1;
            animation: rotate-gradient 3s linear infinite;
        }

        @keyframes rotate-gradient {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .header-profile-btn-inner {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .header-profile-btn:hover {
            transform: scale(1.05);
            text-decoration: none;
        }

        .header-profile-btn:active {
            transform: scale(0.95);
        }

        .header-profile-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header-profile-initials {
            font-size: 14px;
            font-weight: 700;
            color: #01807B;
            text-transform: uppercase;
        }

        /* Header Logo */
        .header-logo {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            height: 40px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .header-logo img {
            height: 100%;
            max-width: 120px;
            object-fit: contain;
        }

        .header-logo-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #01807B;
        }

        .header-logo-text i {
            font-size: 24px;
        }

        .header-logo-text span {
            display: none;
        }

        @media (min-width: 480px) {
            .header-logo-text span {
                display: inline;
            }
        }

        /* Notification Panel - Ultra Modern Design */
        .notification-panel {
            position: fixed;
            top: 60px;
            right: -380px;
            width: 360px;
            max-height: calc(100vh - 80px);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 999;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            box-shadow: -8px 0 40px rgba(0, 0, 0, 0.12), -2px 0 10px rgba(0, 0, 0, 0.08);
            border-radius: 24px 0 0 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .notification-panel.active {
            right: 0;
            animation: slideInNotificationPanel 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @keyframes slideInNotificationPanel {
            0% {
                right: -380px;
                opacity: 0.5;
            }
            100% {
                right: 0;
                opacity: 1;
            }
        }

        .notification-panel-header {
            background: linear-gradient(135deg, #01807B 0%, #019B95 50%, #01807B 100%);
            background-size: 200% 100%;
            animation: gradientShift 3s ease infinite;
            color: white;
            padding: 20px 24px;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
            position: relative;
            overflow: hidden;
        }

        .notification-panel-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotateBg 20s linear infinite;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        @keyframes rotateBg {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .notification-panel-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .mark-all-read-btn {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .mark-all-read-btn:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .mark-all-read-btn:active {
            transform: translateY(0) scale(0.95);
        }

        .mark-all-read-btn.hidden {
            display: none;
        }

        .notification-panel-close {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            position: relative;
            z-index: 1;
        }

        .notification-panel-close:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: rotate(90deg) scale(1.1);
        }

        .notification-panel-close:active {
            transform: rotate(90deg) scale(0.9);
        }

        .notification-filters {
            display: flex;
            gap: 10px;
            padding: 16px;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .filter-btn {
            flex: 1;
            padding: 10px 16px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .filter-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(1, 128, 123, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .filter-btn:hover::before {
            width: 200px;
            height: 200px;
        }

        .filter-btn:hover {
            border-color: #01807B;
            color: #01807B;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(1, 128, 123, 0.15);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            border-color: #01807B;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        }

        .filter-btn:active {
            transform: scale(0.95) translateY(0);
        }

        .notification-panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        }

        /* Custom Scrollbar for Notification Panel */
        .notification-panel-content::-webkit-scrollbar {
            width: 6px;
        }

        .notification-panel-content::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.02);
        }

        .notification-panel-content::-webkit-scrollbar-thumb {
            background: rgba(1, 128, 123, 0.3);
            border-radius: 10px;
        }

        .notification-panel-content::-webkit-scrollbar-thumb:hover {
            background: rgba(1, 128, 123, 0.5);
        }

        /* Notification Items - Modern Card Design */
        .notification-item {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            position: relative;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: fadeInNotification 0.4s ease-out backwards;
        }

        @keyframes fadeInNotification {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #01807B 0%, #019B95 100%);
            transition: width 0.3s ease;
        }

        .notification-item:hover {
            transform: translateX(-8px);
            box-shadow: 0 8px 24px rgba(1, 128, 123, 0.15);
            border-color: rgba(1, 128, 123, 0.2);
        }

        .notification-item:hover::before {
            width: 6px;
        }

        .notification-item.unread {
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.1);
        }

        .notification-item.unread::after {
            content: '';
            position: absolute;
            top: 16px;
            right: 16px;
            width: 10px;
            height: 10px;
            background: #ff4757;
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .notification-item-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .notification-item-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.25);
            transition: all 0.3s ease;
        }

        .notification-item:hover .notification-item-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 16px rgba(1, 128, 123, 0.35);
        }

        /* Type-specific icon colors */
        .notification-item[data-type="consultation"] .notification-item-icon {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25);
        }

        .notification-item[data-type="recommendation"] .notification-item-icon {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.25);
        }

        .notification-item[data-type="weight_reminder"] .notification-item-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.25);
        }

        .notification-item[data-type="water_reminder"] .notification-item-icon {
            background: linear-gradient(135deg, #3498db 0%, #2ecc71 100%);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25);
        }

        .notification-item[data-type="milestone"] .notification-item-icon {
            background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
            box-shadow: 0 4px 12px rgba(241, 196, 15, 0.25);
        }

        .notification-item[data-type="program"] .notification-item-icon {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
            box-shadow: 0 4px 12px rgba(155, 89, 182, 0.25);
        }

        .notification-item-title {
            flex: 1;
            font-weight: 700;
            font-size: 15px;
            color: #2c3e50;
            line-height: 1.3;
        }

        .notification-item-time {
            font-size: 11px;
            color: #95a5a6;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notification-item-message {
            font-size: 14px;
            color: #5a6c7d;
            line-height: 1.5;
            padding-left: 56px;
            margin-top: 4px;
        }

        .notification-item-delete {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            background: rgba(255, 71, 87, 0.1);
            border: none;
            color: #ff4757;
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            opacity: 0;
            transform: scale(0.8);
        }

        .notification-item:hover .notification-item-delete {
            opacity: 1;
            transform: scale(1);
        }

        .notification-item-delete:hover {
            background: #ff4757;
            color: white;
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
        }

        .notification-item-delete:active {
            transform: scale(0.9) rotate(90deg);
        }

        .notification-date-separator {
            padding: 16px 0 12px 0;
            font-size: 11px;
            font-weight: 800;
            color: #01807B;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: sticky;
            top: -16px;
            background: linear-gradient(180deg, #ffffff 0%, rgba(255, 255, 255, 0.95) 100%);
            backdrop-filter: blur(10px);
            z-index: 2;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-date-separator::before {
            content: '';
            height: 2px;
            flex: 1;
            background: linear-gradient(90deg, transparent 0%, #01807B 50%, transparent 100%);
            opacity: 0.3;
        }

        .notification-date-separator span {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding: 4px 12px;
            background-color: rgba(1, 128, 123, 0.05);
            border-radius: 12px;
        }

        .notification-date-separator::after {
            content: '';
            height: 2px;
            flex: 1;
            background: linear-gradient(90deg, transparent 0%, #01807B 50%, transparent 100%);
            opacity: 0.3;
        }

        /* Empty State - More Elegant */
        .notification-empty {
            text-align: center;
            padding: 60px 30px;
            color: #95a5a6;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-empty i {
            font-size: 72px;
            margin-bottom: 20px;
            opacity: 0.3;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: floatIcon 3s ease-in-out infinite;
        }

        @keyframes floatIcon {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .notification-empty p {
            font-size: 15px;
            font-weight: 600;
            margin: 0;
        }

        /* Footer - More Modern */
        .notification-panel-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.06);
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        }

        .notification-settings-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.08) 0%, rgba(1, 155, 149, 0.08) 100%);
            border: 2px solid rgba(1, 128, 123, 0.15);
            border-radius: 16px;
            color: #01807B;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            position: relative;
            overflow: hidden;
        }

        .notification-settings-link::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(1, 128, 123, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .notification-settings-link:hover::before {
            width: 300px;
            height: 300px;
        }

        .notification-settings-link:hover {
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.15) 0%, rgba(1, 155, 149, 0.15) 100%);
            border-color: rgba(1, 128, 123, 0.3);
            color: #01807B;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.15);
        }

        .notification-settings-link:active {
            transform: translateY(0) scale(0.98);
        }

        .notification-settings-link i {
            font-size: 16px;
            position: relative;
            z-index: 1;
        }

        .notification-settings-link span {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 480px) {
            .notification-panel {
                width: 100%;
                right: -100%;
                border-radius: 0;
            }
        }

        /* Menu Overlay */
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Slide Menu */
        .slide-menu {
            position: fixed;
            top: 0;
            right: -350px;
            width: 320px;
            height: 100vh;
            max-height: 100vh;
            background: white;
            z-index: 1003;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch; /* Smooth scrolling sur iOS */
        }

        .slide-menu.active {
            right: 0;
        }

        .menu-header {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            padding: 30px 20px;
            color: white;
            text-align: center;
        }

        .menu-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .menu-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .menu-items {
            padding: 15px 0 80px 0; /* Padding en bas pour accès facile au dernier élément sur mobile */
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px 20px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            position: relative;
        }

        .menu-item:hover {
            background: linear-gradient(90deg, rgba(1, 128, 123, 0.1) 0%, transparent 100%);
            border-left-color: #01807B;
            padding-left: 25px;
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(1, 128, 123, 0.15) 0%, transparent 100%);
            border-left-color: #01807B;
            font-weight: 600;
        }

        .menu-item i {
            font-size: 22px;
            width: 30px;
            text-align: center;
            color: #01807B;
        }

        .menu-item span {
            flex: 1;
            font-size: 16px;
        }

        .menu-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
            margin: 10px 20px;
        }

        /* ============================================
           FOOTER MAGNIFIQUE
           ============================================ */
        .app-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 5px;
        }

        .footer-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 8px 3px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            position: relative;
        }

        .footer-item:hover {
            background: rgba(1, 128, 123, 0.05);
        }

        .footer-item:active {
            transform: scale(0.92);
        }

        .footer-item.active {
            color: #01807B;
        }

        .footer-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 32px;
            height: 3px;
            background: #01807B;
            border-radius: 0 0 3px 3px;
        }

        .footer-icon {
            font-size: 20px;
            transition: all 0.2s;
        }

        .footer-item.active .footer-icon {
            transform: scale(1.08);
            color: #01807B;
        }

        .footer-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .footer-item.active .footer-label {
            font-weight: 700;
        }

        /* Content Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 15px;
            }

            /* Optimisations pour le menu mobile */
            .slide-menu {
                width: 85%; /* Laisse 15% pour l'overlay cliquable */
                max-width: 350px;
                right: -85%;
                height: 100%;
                max-height: -webkit-fill-available; /* Fix pour Safari iOS */
                max-height: 100dvh; /* Dynamic viewport height pour mobiles modernes */
            }

            .slide-menu.active {
                right: 0;
            }

            .menu-items {
                padding: 15px 0 100px 0; /* Plus de padding en bas sur mobile */
            }

            .menu-item {
                padding: 18px 20px; /* Items plus grands pour meilleure accessibilité tactile */
            }
        }
    </style>

    <!-- Firebase Scripts - Using cdnjs as fallback for better reliability -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.22.0/firebase-app-compat.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.22.0/firebase-messaging-compat.min.js" crossorigin="anonymous"></script>

    <!-- Fallback to unpkg if cdnjs fails -->
    <script>
        if (typeof firebase === 'undefined') {
            console.warn('Primary Firebase CDN failed, loading from fallback...');
            document.write('<script src="https://unpkg.com/firebase@9.22.0/firebase-app-compat.js"><\/script>');
            document.write('<script src="https://unpkg.com/firebase@9.22.0/firebase-messaging-compat.js"><\/script>');
        }
    </script>

    <!-- OneSignal Scripts for Web Push + Median Integration -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script src="<?php echo module_dir_url('dietetic', 'assets/js/onesignal_push.js'); ?>"></script>
    <script src="<?php echo module_dir_url('dietetic', 'assets/js/median_onesignal.js'); ?>"></script>
</head>
<body>
    <!-- HEADER MAGNIFIQUE -->
    <header class="app-header">
        <a href="<?php echo site_url('dietetic/portal/profile'); ?>" class="header-profile-btn" title="Mon Profil">
            <div class="header-profile-btn-inner">
                <?php
                // Get contact for profile image
                $header_contact = null;
                $header_contact_id = null;

                if (isset($client) && !empty($client->default_contact)) {
                    $header_contact_id = $client->default_contact;
                } elseif (isset($client) && isset($client->userid)) {
                    $CI = &get_instance();
                    $CI->load->model('clients_model');
                    $contacts = $CI->clients_model->get_contacts($client->userid);
                    if (!empty($contacts)) {
                        $header_contact_id = $contacts[0]['id'];
                    }
                }

                if ($header_contact_id) {
                    $CI = &get_instance();
                    if (!isset($CI->clients_model)) {
                        $CI->load->model('clients_model');
                    }
                    $header_contact = $CI->clients_model->get_contact($header_contact_id);
                }

                if ($header_contact && !empty($header_contact->profile_image)):
                    $header_profile_image_url = base_url('uploads/client_profile_images/' . $header_contact->id . '/thumb_' . $header_contact->profile_image);
                ?>
                    <img src="<?php echo $header_profile_image_url; ?>" alt="Profil">
                <?php else:
                    // Afficher les initiales
                    $header_name = isset($client->company) ? $client->company : (isset($patient->client->company) ? $patient->client->company : 'U');
                    $header_names = explode(' ', trim($header_name));
                    $header_initials = '';
                    if (count($header_names) >= 2) {
                        $header_initials = strtoupper(substr($header_names[0], 0, 1) . substr($header_names[1], 0, 1));
                    } else {
                        $header_initials = strtoupper(substr($header_name, 0, 2));
                    }
                ?>
                    <div class="header-profile-initials"><?php echo $header_initials; ?></div>
                <?php endif; ?>
            </div>
        </a>

        <!-- Logo -->
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="header-logo">
            <?php
            $logo_path = get_option('company_logo_dark');
            if (!$logo_path || !file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                $logo_path = get_option('company_logo');
            }

            if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
            ?>
                <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="<?php echo get_option('companyname'); ?>">
            <?php } else { ?>
                <div class="header-logo-text">
                    <i class="fa fa-heartbeat"></i>
                    <span><?php echo get_option('companyname'); ?></span>
                </div>
            <?php } ?>
        </a>

        <button class="notification-btn" id="notificationBtn">
            <i class="fa fa-bell"></i>
            <span class="notification-badge" style="display: none;">0</span>
        </button>

        <button class="hamburger-btn" id="menuToggle">
            <i class="fa fa-bars"></i>
        </button>
    </header>

    <!-- MENU OVERLAY -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- SLIDE MENU -->
    <nav class="slide-menu" id="slideMenu">
        <div class="menu-header">
            <h3><?php echo isset($client->company) && $client->company ? htmlspecialchars($client->company) : (isset($patient->client->company) && $patient->client->company ? htmlspecialchars($patient->client->company) : 'Mon Compte'); ?></h3>
            <p>Portail Patient</p>
        </div>

        <div class="menu-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="menu-item <?php echo (!isset($active_page) || $active_page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'meal_plans') ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i>
                <span>Plans de Repas</span>
            </a>

            <?php
            // Check if food surveys feature is enabled
            $CI_menu = &get_instance();
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'food_surveys') ? 'active' : ''; ?>">
                <i class="fa fa-list-alt"></i>
                <span>Enquêtes Alimentaires</span>
            </a>
            <?php } ?>

            <?php
            // Check if recipes library is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_recipes')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'recipes') ? 'active' : ''; ?>">
                <i class="fa fa-book"></i>
                <span>Recettes</span>
            </a>
            <?php } ?>

            <?php
            // Check if blog is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_blog_articles')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/blog'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'blog') ? 'active' : ''; ?>">
                <i class="fa fa-newspaper-o"></i>
                <span>Conseils & Blog</span>
            </a>
            <?php } ?>

            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'add_measurement') ? 'active' : ''; ?>">
                <i class="fa fa-plus-circle"></i>
                <span>Ajouter une Mesure</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'measurements') ? 'active' : ''; ?>">
                <i class="fa fa-line-chart"></i>
                <span>Mes Mesures</span>
            </a>

            <?php
            // Check if activities tracking is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_activities')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/activities'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'activities') ? 'active' : ''; ?>">
                <i class="fa fa-heartbeat"></i>
                <span>Mes Activités</span>
            </a>
            <?php } ?>

            <a href="<?php echo site_url('clients/invoices'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'invoices') ? 'active' : ''; ?>">
                <i class="fa fa-file-text"></i>
                <span>Mon plan</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/statistics'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'statistics') ? 'active' : ''; ?>">
                <i class="fa fa-area-chart"></i>
                <span>Mes Statistiques</span>
            </a>

            <div class="menu-divider"></div>

            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'consultations') ? 'active' : ''; ?>">
                <i class="fa fa-calendar"></i>
                <span>Mes Consultations</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'my_dietitians') ? 'active' : ''; ?>">
                <i class="fa fa-user-md"></i>
                <span>Mon Diététicien</span>
            </a>

            <div class="menu-divider"></div>

            <?php
            // Check if notification preferences feature is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'notification_preferences') ? 'active' : ''; ?>">
                <i class="fa fa-bell"></i>
                <span>Préférences de Notifications</span>
            </a>
            <?php } ?>

            <div class="menu-divider"></div>

            <!-- Legal Pages -->
            <a href="<?php echo site_url('dietetic/portal/privacy'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'privacy') ? 'active' : ''; ?>">
                <i class="fa fa-shield"></i>
                <span>Politique de Confidentialité</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/terms'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'terms') ? 'active' : ''; ?>">
                <i class="fa fa-file-text"></i>
                <span>Conditions d'Utilisation</span>
            </a>

            <div class="menu-divider"></div>

            <a href="<?php echo site_url('dietetic/portal/profile'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'profile') ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>

            <a href="<?php echo site_url('authentication/logout'); ?>" class="menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>

    <!-- NOTIFICATION PANEL -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-panel-header">
            <span>Notifications</span>
            <div class="notification-panel-actions">
                <button class="mark-all-read-btn hidden" id="markAllReadBtn">
                    <i class="fa fa-check-double"></i>
                    <span>Tout lire</span>
                </button>
                <button class="notification-panel-close" id="notificationClose">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="notification-filters">
            <button class="filter-btn active" data-filter="unread">Non lues</button>
            <button class="filter-btn" data-filter="read">Lues</button>
        </div>
        <div class="notification-panel-content">
            <!-- Les notifications seront chargées dynamiquement via JavaScript -->
            <div class="notification-empty">
                <i class="fa fa-spinner fa-spin"></i>
                <p>Chargement...</p>
            </div>
        </div>
        <?php
        // Add settings link if notification preferences exist
        $CI_notif_panel = &get_instance();
        if ($CI_notif_panel->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
        ?>
        <div class="notification-panel-footer">
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="notification-settings-link">
                <i class="fa fa-cog"></i>
                <span>Gérer les notifications</span>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="content-container">

    <!-- Freemium Upgrade Banner (shown only for free users) -->
    <?php
    // Safety check: only show banner if is_premium_user function exists
    if (function_exists('is_premium_user') && !is_premium_user()):
    ?>
    <div class="freemium-upgrade-banner" style="
        background: linear-gradient(135deg, #01807B 0%, #01655f 100%);
        color: white;
        padding: 16px 20px;
        margin: -20px -20px 20px -20px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(1, 128, 123, 0.3);
        animation: slideInBanner 0.5s ease;
    ">
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="flex-shrink: 0;">
                <i class="fa fa-crown" style="font-size: 32px; opacity: 0.9;"></i>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <h4 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600;">
                    Version Gratuite
                </h4>
                <p style="margin: 0; font-size: 14px; opacity: 0.95; line-height: 1.4;">
                    Passez à Premium pour débloquer la messagerie, les plans de repas, les rappels automatiques et plus encore !
                </p>
            </div>
            <div style="flex-shrink: 0;">
                <a href="<?php echo site_url('dietetic/portal/upgrade'); ?>"
                   style="
                       display: inline-flex;
                       align-items: center;
                       gap: 8px;
                       background: white;
                       color: #01807B;
                       padding: 10px 20px;
                       border-radius: 25px;
                       text-decoration: none;
                       font-weight: 600;
                       font-size: 14px;
                       box-shadow: 0 2px 10px rgba(0,0,0,0.15);
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.25)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.15)'">
                    <i class="fa fa-star"></i>
                    Découvrir Premium
                </a>
            </div>
        </div>
    </div>
    <style>
    @keyframes slideInBanner {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (max-width: 576px) {
        .freemium-upgrade-banner {
            margin: -20px -15px 20px -15px !important;
            padding: 14px 15px !important;
        }
        .freemium-upgrade-banner > div {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }
        .freemium-upgrade-banner h4 {
            font-size: 15px !important;
        }
        .freemium-upgrade-banner p {
            font-size: 13px !important;
        }
    }
    </style>
    <?php endif; ?>
