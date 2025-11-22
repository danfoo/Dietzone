<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Mes Consultations</title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
        }

        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Header */
        .portal-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .portal-header .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .portal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .portal-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .portal-logo img {
            max-height: 40px;
            max-width: 150px;
        }

        .portal-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-logo-text i {
            color: #01807B;
        }

        .portal-nav-desktop {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .portal-nav-desktop a {
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .portal-nav-desktop a:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .portal-nav-desktop a.active {
            background: #01807B;
            color: white;
        }

        /* Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        /* Page Header */
        .page-header-mobile {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
        }

        .page-header-mobile h1 {
            font-size: 24px;
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

        /* Section Title */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin: 30px 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title i {
            color: #01807B;
        }

        /* Consultation Card - MODERNIZED */
        .consultation-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .consultation-card:hover {
            box-shadow: 0 8px 30px rgba(1, 128, 123, 0.15);
            border-color: #01807B;
        }

        .consultation-card.past {
            opacity: 0.85;
        }

        /* Card Header with colored strip */
        .card-strip {
            height: 6px;
            background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
        }

        .card-strip.past {
            background: #6c757d;
        }

        .card-strip.cancelled {
            background: #dc3545;
        }

        .card-content {
            padding: 20px;
        }

        /* Consultation Header */
        .consultation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .consultation-date {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .consultation-date i {
            color: #01807B;
        }

        /* Status Badge - IMPROVED */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.scheduled {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
        }

        .status-badge.completed {
            background: #e7f5ff;
            color: #0c8599;
        }

        .status-badge.cancelled {
            background: #ffe0e0;
            color: #dc3545;
        }

        .status-badge.no_show {
            background: #fff3cd;
            color: #856404;
        }

        /* Mode Badge - NEW */
        .mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .mode-badge i {
            font-size: 18px;
        }

        .mode-badge.online {
            background: linear-gradient(135deg, #e7f5ff 0%, #d0ebff 100%);
            color: #0c8599;
        }

        .mode-badge.online i {
            color: #0c8599;
        }

        .mode-badge.in-person i {
            color: #01807B;
        }

        /* Platform Badge - NEW */
        .platform-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .platform-badge.zoom {
            border-color: #2D8CFF;
            color: #2D8CFF;
        }

        .platform-badge.google_meet {
            border-color: #00897B;
            color: #00897B;
        }

        .platform-badge.teams {
            border-color: #6264A7;
            color: #6264A7;
        }

        .platform-badge.whatsapp {
            border-color: #25D366;
            color: #25D366;
        }

        /* Countdown - NEW */
        .countdown {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border-left: 4px solid #F3911D;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .countdown i {
            font-size: 20px;
            color: #F3911D;
        }

        .countdown-text {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .countdown-time {
            font-weight: 700;
            color: #F3911D;
        }

        /* Consultation Info Grid */
        .consultation-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .info-item i {
            color: #01807B;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Action Buttons - NEW */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-width: 140px;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-join {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        }

        .btn-join:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-directions {
            background: linear-gradient(135deg, #F3911D 0%, #E67E22 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(243, 145, 29, 0.3);
        }

        .btn-directions:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(243, 145, 29, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-details {
            background: white;
            color: #01807B;
            border: 2px solid #01807B;
        }

        .btn-details:hover {
            background: #01807B;
            color: white;
            text-decoration: none;
        }

        .btn-calendar {
            background: white;
            color: #6c757d;
            border: 2px solid #dee2e6;
        }

        .btn-calendar:hover {
            background: #f8f9fa;
            border-color: #01807B;
            color: #01807B;
            text-decoration: none;
        }

        /* Location/Link Display - NEW */
        .location-display, .link-display {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .location-display i, .link-display i {
            color: #01807B;
            font-size: 18px;
        }

        .location-display a, .link-display a {
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
        }

        .location-display a:hover, .link-display a:hover {
            text-decoration: underline;
        }

        /* Consultation Notes */
        .consultation-notes {
            padding: 14px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #F3911D;
            margin-top: 16px;
        }

        .consultation-notes strong {
            color: #2c3e50;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .consultation-notes p {
            margin: 8px 0 0 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .empty-state-icon {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 20px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 12px;
        }

        .empty-state-text {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Buttons */
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

        /* Bottom Nav */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            padding: 8px 0 env(safe-area-inset-bottom, 8px) 0;
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 12px;
            min-width: 60px;
            position: relative;
        }

        .bottom-nav-item.active {
            color: #01807B;
        }

        .bottom-nav-item i {
            font-size: 24px;
        }

        .bottom-nav-item.active i {
            transform: scale(1.1);
        }

        .bottom-nav-item span {
            font-size: 11px;
            font-weight: 600;
        }

        .bottom-nav-item.active::before {
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

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        /* Desktop */
        @media (min-width: 769px) {
            body {
                padding-bottom: 0;
            }

            .bottom-nav {
                display: none !important;
            }

            .portal-nav-desktop {
                display: flex !important;
            }

            .consultation-info {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .action-buttons {
                flex-wrap: nowrap;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .bottom-nav {
                display: block;
            }

            .content-container {
                padding: 16px 12px 20px;
            }

            .page-header-mobile {
                padding: 20px 16px;
                border-radius: 12px;
                margin-bottom: 20px;
            }

            .page-header-mobile h1 {
                font-size: 20px;
            }

            .card-content {
                padding: 16px;
            }

            .consultation-info {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .consultation-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                min-width: 100%;
            }
        }

        @media (max-width: 375px) {
            .portal-logo img {
                max-height: 32px;
                max-width: 120px;
            }

            .portal-logo-text {
                font-size: 16px;
            }

            .page-header-mobile h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php $this->load->view('portal/includes/portal_header'); ?>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-calendar-check-o"></i> Mes Consultations</h1>
            <p>Historique et rendez-vous à venir</p>
        </div>

        <?php if (!empty($consultations)) { ?>
            <?php
            $now = new DateTime();
            $upcoming = [];
            $past = [];

            foreach ($consultations as $consultation) {
                $consultation_date = new DateTime($consultation->consultation_date);
                if ($consultation_date > $now && $consultation->status !== 'cancelled') {
                    $upcoming[] = $consultation;
                } else {
                    $past[] = $consultation;
                }
            }
            ?>

            <?php if (!empty($upcoming)) { ?>
                <div class="section-title animate-in delay-1">
                    <i class="fa fa-clock-o"></i>
                    <span>Rendez-vous à venir</span>
                </div>
                <div class="animate-in delay-1">
                    <?php foreach ($upcoming as $consultation) {
                        $consultation_datetime = new DateTime($consultation->consultation_date);
                        $interval = $now->diff($consultation_datetime);

                        // Calculate countdown
                        $days = $interval->days;
                        $hours = $interval->h;
                        $minutes = $interval->i;

                        $countdown_text = '';
                        if ($days > 0) {
                            $countdown_text = "Dans $days jour" . ($days > 1 ? 's' : '');
                            if ($hours > 0) {
                                $countdown_text .= " et $hours heure" . ($hours > 1 ? 's' : '');
                            }
                        } elseif ($hours > 0) {
                            $countdown_text = "Dans $hours heure" . ($hours > 1 ? 's' : '');
                            if ($minutes > 0) {
                                $countdown_text .= " et $minutes minute" . ($minutes > 1 ? 's' : '');
                            }
                        } else {
                            $countdown_text = "Dans $minutes minute" . ($minutes > 1 ? 's' : '');
                        }
                    ?>
                        <div class="consultation-card <?php echo $consultation->status == 'cancelled' ? 'cancelled' : ''; ?>">
                            <div class="card-strip <?php echo $consultation->status == 'cancelled' ? 'cancelled' : ''; ?>"></div>
                            <div class="card-content">
                                <div class="consultation-header">
                                    <div class="consultation-date">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                                    </div>
                                    <?php
                                    $status_class = $consultation->status;
                                    $status_text = '';
                                    $status_icon = '';
                                    switch ($consultation->status) {
                                        case 'scheduled':
                                            $status_text = 'À venir';
                                            $status_icon = 'fa-clock-o';
                                            break;
                                        case 'completed':
                                            $status_text = 'Terminée';
                                            $status_icon = 'fa-check';
                                            break;
                                        case 'cancelled':
                                            $status_text = 'Annulée';
                                            $status_icon = 'fa-times';
                                            break;
                                        case 'no_show':
                                            $status_text = 'Manquée';
                                            $status_icon = 'fa-exclamation';
                                            break;
                                        default:
                                            $status_text = 'Planifiée';
                                            $status_icon = 'fa-calendar';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fa <?php echo $status_icon; ?>"></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>

                                <!-- Countdown -->
                                <?php if ($consultation->status !== 'cancelled') { ?>
                                <div class="countdown pulse-animation">
                                    <i class="fa fa-hourglass-half"></i>
                                    <div class="countdown-text">
                                        <span class="countdown-time"><?php echo $countdown_text; ?></span>
                                    </div>
                                </div>
                                <?php } ?>

                                <!-- Mode Badge -->
                                <?php
                                $mode = isset($consultation->consultation_mode) ? $consultation->consultation_mode : 'in_person';
                                $mode_text = ($mode === 'online') ? 'Consultation en ligne' : 'Consultation en présentiel';
                                $mode_icon = ($mode === 'online') ? 'fa-video-camera' : 'fa-hospital-o';
                                ?>
                                <div class="mode-badge <?php echo ($mode === 'online') ? 'online' : 'in-person'; ?>">
                                    <i class="fa <?php echo $mode_icon; ?>"></i>
                                    <span><?php echo $mode_text; ?></span>

                                    <?php if ($mode === 'online' && !empty($consultation->online_platform)) {
                                        $platform_name = ucfirst(str_replace('_', ' ', $consultation->online_platform));
                                    ?>
                                        <span class="platform-badge <?php echo $consultation->online_platform; ?>">
                                            <?php echo $platform_name; ?>
                                        </span>
                                    <?php } ?>
                                </div>

                                <!-- Location for in-person -->
                                <?php if ($mode === 'in_person' && !empty($consultation->location)) { ?>
                                <div class="location-display">
                                    <i class="fa fa-map-marker"></i>
                                    <span><?php echo htmlspecialchars($consultation->location); ?></span>
                                </div>
                                <?php } ?>

                                <div class="consultation-info">
                                    <div class="info-item">
                                        <i class="fa fa-stethoscope"></i>
                                        <label>Type</label>
                                        <div class="value"><?php echo dietetic_consultation_type_label($consultation->consultation_type); ?></div>
                                    </div>

                                    <?php if (isset($consultation->duration)) { ?>
                                    <div class="info-item">
                                        <i class="fa fa-clock-o"></i>
                                        <label>Durée</label>
                                        <div class="value"><?php echo $consultation->duration; ?> min</div>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($consultation->dietitian_name)) { ?>
                                    <div class="info-item">
                                        <i class="fa fa-user-md"></i>
                                        <label>Diététicien</label>
                                        <div class="value"><?php echo htmlspecialchars($consultation->dietitian_name); ?></div>
                                    </div>
                                    <?php } ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="action-buttons">
                                    <?php if ($mode === 'online' && !empty($consultation->meeting_link)) { ?>
                                        <a href="<?php echo htmlspecialchars($consultation->meeting_link); ?>" target="_blank" class="btn-action btn-join">
                                            <i class="fa fa-video-camera"></i>
                                            Rejoindre la consultation
                                        </a>
                                    <?php } elseif ($mode === 'in_person' && !empty($consultation->location)) { ?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($consultation->location); ?>" target="_blank" class="btn-action btn-directions">
                                            <i class="fa fa-map-marker"></i>
                                            Voir l'itinéraire
                                        </a>
                                    <?php } ?>

                                    <a href="<?php echo site_url('dietetic/portal/consultation/' . $consultation->id); ?>" class="btn-action btn-details">
                                        <i class="fa fa-info-circle"></i>
                                        Voir détails
                                    </a>

                                    <button class="btn-action btn-calendar" onclick="addToCalendar(<?php echo $consultation->id; ?>)">
                                        <i class="fa fa-calendar-plus-o"></i>
                                        Ajouter au calendrier
                                    </button>
                                </div>

                                <?php if (!empty($consultation->reason)) { ?>
                                <div class="consultation-notes">
                                    <strong><i class="fa fa-info-circle"></i> Motif</strong>
                                    <p><?php echo nl2br(htmlspecialchars($consultation->reason)); ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($past)) { ?>
                <div class="section-title animate-in delay-2" style="margin-top: 30px;">
                    <i class="fa fa-history"></i>
                    <span>Consultations passées</span>
                </div>
                <div class="animate-in delay-2">
                    <?php foreach ($past as $consultation) { ?>
                        <div class="consultation-card past">
                            <div class="card-strip past"></div>
                            <div class="card-content">
                                <div class="consultation-header">
                                    <div class="consultation-date">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                                    </div>
                                    <?php
                                    $status_class = $consultation->status;
                                    $status_text = '';
                                    $status_icon = '';
                                    switch ($consultation->status) {
                                        case 'completed':
                                            $status_text = 'Terminée';
                                            $status_icon = 'fa-check';
                                            break;
                                        case 'cancelled':
                                            $status_text = 'Annulée';
                                            $status_icon = 'fa-times';
                                            break;
                                        case 'no_show':
                                            $status_text = 'Manquée';
                                            $status_icon = 'fa-exclamation';
                                            break;
                                        default:
                                            $status_text = 'Passée';
                                            $status_icon = 'fa-history';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fa <?php echo $status_icon; ?>"></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>

                                <div class="consultation-info">
                                    <div class="info-item">
                                        <i class="fa fa-stethoscope"></i>
                                        <label>Type</label>
                                        <div class="value"><?php echo dietetic_consultation_type_label($consultation->consultation_type); ?></div>
                                    </div>

                                    <?php if (isset($consultation->duration)) { ?>
                                    <div class="info-item">
                                        <i class="fa fa-clock-o"></i>
                                        <label>Durée</label>
                                        <div class="value"><?php echo $consultation->duration; ?> min</div>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($consultation->dietitian_name)) { ?>
                                    <div class="info-item">
                                        <i class="fa fa-user-md"></i>
                                        <label>Diététicien</label>
                                        <div class="value"><?php echo htmlspecialchars($consultation->dietitian_name); ?></div>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div class="action-buttons">
                                    <a href="<?php echo site_url('dietetic/portal/consultation/' . $consultation->id); ?>" class="btn-action btn-details">
                                        <i class="fa fa-info-circle"></i>
                                        Voir le compte-rendu
                                    </a>
                                </div>

                                <?php if (isset($consultation->observations) && $consultation->observations) { ?>
                                <div class="consultation-notes">
                                    <strong><i class="fa fa-sticky-note"></i> Observations</strong>
                                    <p><?php echo nl2br(htmlspecialchars($consultation->observations)); ?></p>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-calendar-o"></i>
                </div>
                <div class="empty-state-title">Aucune consultation</div>
                <div class="empty-state-text">
                    Vous n'avez pas encore de consultations enregistrées. Contactez votre diététicien pour prendre rendez-vous.
                </div>
            </div>
        <?php } ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <?php $this->load->view('portal/includes/portal_footer'); ?>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Add to Calendar Function
        function addToCalendar(consultationId) {
            // Get consultation data
            <?php if (!empty($consultations)) { ?>
                var consultations = <?php echo json_encode($consultations); ?>;
                var consultation = consultations.find(c => c.id == consultationId);

                if (consultation) {
                    var startDate = new Date(consultation.consultation_date);
                    var endDate = new Date(startDate.getTime() + (consultation.duration || 60) * 60000);

                    var title = 'Consultation - ' + (consultation.dietitian_name || 'Diététicien');
                    var description = 'Type: ' + consultation.consultation_type;
                    if (consultation.reason) {
                        description += '\\nMotif: ' + consultation.reason;
                    }
                    if (consultation.consultation_mode === 'online' && consultation.meeting_link) {
                        description += '\\nLien: ' + consultation.meeting_link;
                    }

                    var location = consultation.consultation_mode === 'in_person' ? (consultation.location || '') : 'En ligne';

                    // Create .ics file
                    var icsContent = 'BEGIN:VCALENDAR\n';
                    icsContent += 'VERSION:2.0\n';
                    icsContent += 'BEGIN:VEVENT\n';
                    icsContent += 'DTSTART:' + startDate.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z\n';
                    icsContent += 'DTEND:' + endDate.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z\n';
                    icsContent += 'SUMMARY:' + title + '\n';
                    icsContent += 'DESCRIPTION:' + description.replace(/\n/g, '\\n') + '\n';
                    icsContent += 'LOCATION:' + location + '\n';
                    icsContent += 'END:VEVENT\n';
                    icsContent += 'END:VCALENDAR';

                    // Download file
                    var blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'consultation-' + consultationId + '.ics';
                    link.click();

                    // Haptic feedback
                    if ('vibrate' in navigator) {
                        navigator.vibrate(50);
                    }
                }
            <?php } ?>
        }

        // Touch feedback
        document.querySelectorAll('.consultation-card, .bottom-nav-item, .btn-action').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.97)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Haptic feedback
        if ('vibrate' in navigator) {
            document.querySelectorAll('.btn-action').forEach(function(button) {
                button.addEventListener('click', function() {
                    navigator.vibrate(10);
                });
            });
        }

        // Update countdown every minute for upcoming consultations
        setInterval(function() {
            location.reload();
        }, 60000); // Refresh every minute to update countdown
    </script>
</body>
</html>
