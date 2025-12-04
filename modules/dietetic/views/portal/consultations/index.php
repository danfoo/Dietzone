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

        /* Stats Bar */
        .stats-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 1;
            min-width: 140px;
            background: white;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1976d2;
        }

        .stat-icon.upcoming {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #388e3c;
        }

        .stat-icon.past {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
            color: #7b1fa2;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Accordion Panel */
        .panel-group {
            margin-bottom: 20px;
        }

        .consultation-panel {
            background: white;
            border-radius: 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .consultation-panel:hover {
            border-color: #01807B;
            box-shadow: 0 4px 16px rgba(1, 128, 123, 0.15);
        }

        .consultation-panel.past {
            opacity: 0.9;
        }

        /* Panel Heading */
        .panel-heading {
            background: white !important;
            border: none !important;
            padding: 0 !important;
        }

        .panel-title {
            margin: 0;
        }


        .consultation-summary {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            flex-wrap: wrap;
        }

        .consultation-date-header {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
        }

        .date-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .date-day {
            font-size: 18px;
            line-height: 1;
        }

        .date-month {
            font-size: 10px;
            text-transform: uppercase;
        }

        .consultation-info-header {
            flex: 1;
        }

        .consultation-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .consultation-time {
            font-size: 13px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge-header {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge-header.scheduled {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
        }

        .status-badge-header.completed {
            background: #e7f5ff;
            color: #0c8599;
        }

        .status-badge-header.cancelled {
            background: #ffe0e0;
            color: #dc3545;
        }

        .status-badge-header.no_show {
            background: #fff3cd;
            color: #856404;
        }

        .expand-icon {
            font-size: 18px;
            color: #01807B;
            transition: transform 0.3s ease;
        }

        .expand-icon.open {
            transform: rotate(180deg);
        }

        /* Panel Body */
        .panel-collapse {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
        }

        .panel-collapse.open {
            max-height: 5000px;
        }

        .panel-body {
            padding: 20px !important;
            background: #f8f9fa;
            border-top: none !important;
        }

        .detail-section {
            background: white;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #01807B;
            font-size: 16px;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 2px;
        }

        /* Action Buttons in Panel */
        .action-buttons-panel {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action-panel {
            flex: 1;
            min-width: 140px;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
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
            box-shadow: 0 2px 8px rgba(1, 128, 123, 0.3);
        }

        .btn-join:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-directions {
            background: linear-gradient(135deg, #F3911D 0%, #E67E22 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(243, 145, 29, 0.3);
        }

        .btn-directions:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 145, 29, 0.4);
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

        /* Notes/Reason Display */
        .notes-box {
            background: #fff3e0;
            border-left: 3px solid #F3911D;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .notes-box strong {
            color: #2c3e50;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-box p {
            margin: 8px 0 0 0;
            color: #6c757d;
            font-size: 13px;
            line-height: 1.6;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
            font-weight: 600;
        }

        .pagination {
            margin: 0;
        }

        .pagination > li > a,
        .pagination > li > span {
            padding: 10px 16px;
            border-radius: 8px;
            margin: 0 4px;
            border: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination > li > a:hover {
            background: #f8f9fa;
            border-color: #01807B;
            color: #01807B;
        }

        .pagination > .active > a,
        .pagination > .active > span {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            border-color: #01807B;
            color: white;
        }

        .pagination > .active > a:hover {
            background: linear-gradient(135deg, #026661 0%, #01807B 100%);
            color: white;
        }

        .pagination > .disabled > a,
        .pagination > .disabled > span {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
            cursor: not-allowed;
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
        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .content-container {
                padding: 16px 12px 20px;
            }

            .page-header-mobile h1 {
                font-size: 20px;
            }

            .stats-bar {
                flex-direction: column;
            }

            .stat-item {
                min-width: 100%;
            }

            .consultation-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .consultation-date-header {
                width: 100%;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons-panel {
                flex-direction: column;
            }

            .btn-action-panel {
                min-width: 100%;
            }
        }

        @media (min-width: 769px) {
            body {
                padding-bottom: 0;
            }

            .portal-nav-desktop {
                display: flex !important;
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

        <?php if (!empty($consultations) || $total_consultations > 0) { ?>
            <?php
            $now = new DateTime();
            $upcoming_count = 0;
            $past_count = 0;

            // Count upcoming and past consultations from all consultations
            $all_consultations_for_count = $this->dietetic_consultations_model->get_by_patient($patient->id);
            foreach ($all_consultations_for_count as $c) {
                $c_date = new DateTime($c->consultation_date);
                if ($c_date > $now && $c->status !== 'cancelled') {
                    $upcoming_count++;
                } else {
                    $past_count++;
                }
            }
            ?>

            <!-- Stats Bar -->
            <div class="stats-bar animate-in delay-1">
                <div class="stat-item">
                    <div class="stat-icon total">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $total_consultations; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon upcoming">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $upcoming_count; ?></div>
                        <div class="stat-label">À venir</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon past">
                        <i class="fa fa-history"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $past_count; ?></div>
                        <div class="stat-label">Passées</div>
                    </div>
                </div>
            </div>

            <!-- Accordion -->
            <div class="panel-group animate-in delay-2" id="consultationsAccordion" role="tablist">
                <?php foreach ($consultations as $index => $consultation) {
                    $consultation_datetime = new DateTime($consultation->consultation_date);
                    $is_upcoming = ($consultation_datetime > $now && $consultation->status !== 'cancelled');
                    $panel_class = $is_upcoming ? 'upcoming' : 'past';
                    if ($consultation->status === 'cancelled') {
                        $panel_class = 'cancelled';
                    }

                    // Status badge
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

                    // Format date
                    $day = date('d', strtotime($consultation->consultation_date));
                    $month = date('M', strtotime($consultation->consultation_date));
                    $time = date('H:i', strtotime($consultation->consultation_date));
                    $full_date = date('d/m/Y', strtotime($consultation->consultation_date));
                ?>
                    <div class="consultation-panel <?php echo $panel_class; ?>">
                        <div class="panel-heading" role="tab" id="heading<?php echo $consultation->id; ?>">
                            <h4 class="panel-title">
                                <div role="button" onclick="toggleConsultationAccordion('consultation-<?php echo $consultation->id; ?>')" style="display: flex; align-items: center; justify-content: space-between; padding: 20px; cursor: pointer; user-select: none;">
                                    <div class="consultation-summary">
                                        <div class="consultation-date-header">
                                            <div class="date-icon">
                                                <div class="date-day"><?php echo $day; ?></div>
                                                <div class="date-month"><?php echo $month; ?></div>
                                            </div>
                                            <div class="consultation-info-header">
                                                <div class="consultation-title"><?php echo dietetic_consultation_type_label($consultation->consultation_type); ?></div>
                                                <div class="consultation-time">
                                                    <i class="fa fa-clock-o"></i>
                                                    <?php echo $full_date; ?> à <?php echo $time; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="status-badge-header <?php echo $status_class; ?>">
                                            <i class="fa <?php echo $status_icon; ?>"></i>
                                            <?php echo $status_text; ?>
                                        </span>
                                    </div>
                                    <i class="fa fa-chevron-down expand-icon <?php echo $index === 0 ? 'open' : ''; ?>" id="icon-consultation-<?php echo $consultation->id; ?>"></i>
                                </div>
                            </h4>
                        </div>
                        <div id="content-consultation-<?php echo $consultation->id; ?>"
                             class="panel-collapse <?php echo $index === 0 ? 'open' : ''; ?>">
                            <div class="panel-body">
                                <!-- Details Section -->
                                <div class="detail-section">
                                    <div class="section-title">
                                        <i class="fa fa-info-circle"></i>
                                        Informations
                                    </div>
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fa fa-stethoscope"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Type</div>
                                                <div class="detail-value"><?php echo dietetic_consultation_type_label($consultation->consultation_type); ?></div>
                                            </div>
                                        </div>

                                        <?php if (isset($consultation->duration)) { ?>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fa fa-hourglass-half"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Durée</div>
                                                <div class="detail-value"><?php echo $consultation->duration; ?> min</div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (isset($consultation->dietitian_name)) { ?>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fa fa-user-md"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Diététicien</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($consultation->dietitian_name); ?></div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php
                                        $mode = isset($consultation->consultation_mode) ? $consultation->consultation_mode : 'in_person';
                                        $mode_text = ($mode === 'online') ? 'En ligne' : 'En présentiel';
                                        ?>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fa <?php echo ($mode === 'online') ? 'fa-video-camera' : 'fa-hospital-o'; ?>"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Mode</div>
                                                <div class="detail-value"><?php echo $mode_text; ?></div>
                                            </div>
                                        </div>

                                        <?php if ($mode === 'online' && !empty($consultation->online_platform)) { ?>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <i class="fa fa-desktop"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Plateforme</div>
                                                <div class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $consultation->online_platform)); ?></div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if ($mode === 'in_person' && !empty($consultation->location)) { ?>
                                        <div class="detail-item" style="grid-column: 1 / -1;">
                                            <div class="detail-icon">
                                                <i class="fa fa-map-marker"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Lieu</div>
                                                <div class="detail-value"><?php echo htmlspecialchars($consultation->location); ?></div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Reason/Notes -->
                                <?php if (!empty($consultation->reason)) { ?>
                                <div class="detail-section">
                                    <div class="notes-box">
                                        <strong><i class="fa fa-comment"></i> Motif de consultation</strong>
                                        <p><?php echo nl2br(htmlspecialchars($consultation->reason)); ?></p>
                                    </div>
                                </div>
                                <?php } ?>

                                <?php if (isset($consultation->observations) && $consultation->observations) { ?>
                                <div class="detail-section">
                                    <div class="notes-box">
                                        <strong><i class="fa fa-sticky-note"></i> Observations</strong>
                                        <p><?php echo nl2br(htmlspecialchars($consultation->observations)); ?></p>
                                    </div>
                                </div>
                                <?php } ?>

                                <!-- Action Buttons -->
                                <div class="detail-section">
                                    <div class="action-buttons-panel">
                                        <?php if ($mode === 'online' && !empty($consultation->meeting_link) && $is_upcoming) { ?>
                                            <a href="<?php echo htmlspecialchars($consultation->meeting_link); ?>" target="_blank" class="btn-action-panel btn-join">
                                                <i class="fa fa-video-camera"></i>
                                                Rejoindre la consultation
                                            </a>
                                        <?php } elseif ($mode === 'in_person' && !empty($consultation->location) && $is_upcoming) { ?>
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($consultation->location); ?>" target="_blank" class="btn-action-panel btn-directions">
                                                <i class="fa fa-map-marker"></i>
                                                Voir l'itinéraire
                                            </a>
                                        <?php } ?>

                                        <a href="<?php echo site_url('dietetic/portal/consultation/' . $consultation->id); ?>" class="btn-action-panel btn-details">
                                            <i class="fa fa-info-circle"></i>
                                            Voir détails complets
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1) { ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?>
                    (<?php echo $total_consultations; ?> consultation<?php echo $total_consultations > 1 ? 's' : ''; ?>)
                </div>
                <ul class="pagination">
                    <?php if ($current_page > 1) { ?>
                        <li>
                            <a href="<?php echo site_url('dietetic/portal/consultations/1'); ?>" aria-label="Première">
                                <i class="fa fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('dietetic/portal/consultations/' . ($current_page - 1)); ?>" aria-label="Précédente">
                                <i class="fa fa-angle-left"></i>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="disabled">
                            <span><i class="fa fa-angle-double-left"></i></span>
                        </li>
                        <li class="disabled">
                            <span><i class="fa fa-angle-left"></i></span>
                        </li>
                    <?php } ?>

                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        if ($i == $current_page) {
                            echo '<li class="active"><span>' . $i . '</span></li>';
                        } else {
                            echo '<li><a href="' . site_url('dietetic/portal/consultations/' . $i) . '">' . $i . '</a></li>';
                        }
                    }
                    ?>

                    <?php if ($current_page < $total_pages) { ?>
                        <li>
                            <a href="<?php echo site_url('dietetic/portal/consultations/' . ($current_page + 1)); ?>" aria-label="Suivante">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('dietetic/portal/consultations/' . $total_pages); ?>" aria-label="Dernière">
                                <i class="fa fa-angle-double-right"></i>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="disabled">
                            <span><i class="fa fa-angle-right"></i></span>
                        </li>
                        <li class="disabled">
                            <span><i class="fa fa-angle-double-right"></i></span>
                        </li>
                    <?php } ?>
                </ul>
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

    <script>
        // Toggle consultation accordion function
        function toggleConsultationAccordion(accordionId) {
            const content = document.getElementById('content-' + accordionId);
            const icon = document.getElementById('icon-' + accordionId);

            if (content && icon) {
                const isOpen = content.classList.contains('open');

                if (isOpen) {
                    // Close
                    content.classList.remove('open');
                    icon.classList.remove('open');
                } else {
                    // Open
                    content.classList.add('open');
                    icon.classList.add('open');
                }
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Touch feedback for mobile
        document.querySelectorAll('.panel-title, .btn-action-panel').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.97)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Haptic feedback
        if ('vibrate' in navigator) {
            document.querySelectorAll('.btn-action-panel').forEach(function(button) {
                button.addEventListener('click', function() {
                    navigator.vibrate(10);
                });
            });
        }
    </script>
</body>
</html>
