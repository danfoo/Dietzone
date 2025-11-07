<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Survey Detail Styles */
:root {
    --primary-color: #01807B;
    --secondary-color: #F3911D;
    --tertiary-color: #FFFFFF;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
    --background-light: #f8f9fa;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Survey Header */
.survey-detail-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 32px 24px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.survey-detail-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.survey-detail-header h1 {
    margin: 0 0 8px 0;
    font-size: 26px;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.survey-detail-header .survey-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 16px;
    position: relative;
    z-index: 1;
}

.survey-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.survey-meta-item i {
    font-size: 16px;
    opacity: 0.9;
}

.header-actions {
    position: relative;
    z-index: 1;
}

.btn-header {
    background: white;
    color: var(--primary-color);
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    margin-left: 8px;
}

.btn-header:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: var(--primary-color);
}

.btn-header.outline {
    background: transparent;
    border: 2px solid white;
    color: white;
}

.btn-header.outline:hover {
    background: white;
    color: var(--primary-color);
}

/* Info Cards */
.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.info-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--primary-color);
}

.info-card.orange {
    border-left-color: var(--secondary-color);
}

.info-card.green {
    border-left-color: #27ae60;
}

.info-card h4 {
    margin: 0 0 16px 0;
    font-size: 15px;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.info-card .value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 8px;
}

.info-card .description {
    font-size: 13px;
    color: var(--text-light);
}

/* Progress Circle */
.progress-circle {
    width: 120px;
    height: 120px;
    margin: 0 auto 16px;
    position: relative;
}

.progress-circle svg {
    transform: rotate(-90deg);
}

.progress-circle circle {
    fill: none;
    stroke-width: 8;
}

.progress-circle .bg {
    stroke: #ecf0f1;
}

.progress-circle .progress {
    stroke: url(#gradient);
    stroke-linecap: round;
    transition: stroke-dashoffset 0.5s ease;
}

.progress-circle .percentage {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 28px;
    font-weight: 700;
    color: var(--primary-color);
}

/* Entries Timeline */
.entries-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
}

.entries-header {
    border-bottom: 3px solid var(--primary-color);
    padding-bottom: 16px;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.entries-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.entries-header h3 i {
    color: var(--primary-color);
}

.filter-buttons {
    display: flex;
    gap: 8px;
}

.filter-btn {
    padding: 6px 16px;
    border-radius: 20px;
    border: 2px solid #ecf0f1;
    background: white;
    color: var(--text-light);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.filter-btn:hover,
.filter-btn.active {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
}

/* Entry Card */
.entry-card {
    background: white;
    border: 2px solid #ecf0f1;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    transition: var(--transition);
    cursor: pointer;
}

.entry-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
    transform: translateX(4px);
}

.entry-card.submitted {
    border-left: 4px solid #27ae60;
}

.entry-card.pending {
    border-left: 4px solid #f39c12;
}

.entry-card.with-recommendation {
    background: linear-gradient(to right, rgba(1, 128, 123, 0.03) 0%, transparent 100%);
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.entry-date {
    display: flex;
    align-items: center;
    gap: 10px;
}

.entry-date .date-box {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    text-align: center;
    min-width: 70px;
}

.entry-date .date-box .day {
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
}

.entry-date .date-box .month {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.9;
    letter-spacing: 0.5px;
}

.entry-date-info h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--text-dark);
}

.entry-date-info .weekday {
    font-size: 13px;
    color: var(--text-light);
}

.entry-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-badge.submitted {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.status-badge.pending {
    background: rgba(243, 145, 29, 0.1);
    color: var(--secondary-color);
}

.status-badge.recommendation {
    background: rgba(1, 128, 123, 0.1);
    color: var(--primary-color);
}

.entry-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}

.entry-summary-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: var(--background-light);
    border-radius: 8px;
}

.entry-summary-item i {
    font-size: 18px;
    color: var(--secondary-color);
}

.entry-summary-item .label {
    font-size: 12px;
    color: var(--text-light);
    margin-bottom: 2px;
}

.entry-summary-item .value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-dark);
}

.entry-actions {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #ecf0f1;
    display: flex;
    gap: 8px;
}

.btn-entry {
    padding: 8px 16px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 13px;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-entry.primary {
    background: var(--primary-color);
    color: white;
}

.btn-entry.primary:hover {
    background: #016663;
}

.btn-entry.secondary {
    background: var(--secondary-color);
    color: white;
}

.btn-entry.secondary:hover {
    background: #d97e0f;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #ecf0f1;
    margin-bottom: 16px;
}

.empty-state h4 {
    margin: 0 0 8px 0;
    font-size: 18px;
    color: var(--text-dark);
}

.empty-state p {
    margin: 0;
    color: var(--text-light);
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .survey-detail-header h1 {
        font-size: 20px;
    }

    .survey-meta {
        flex-direction: column;
        gap: 12px;
    }

    .header-actions {
        margin-top: 16px;
    }

    .btn-header {
        width: 100%;
        margin-left: 0;
        margin-top: 8px;
    }

    .info-cards {
        grid-template-columns: 1fr;
    }

    .entry-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .entry-status {
        align-items: flex-start;
    }

    .entry-content {
        grid-template-columns: 1fr;
    }

    .entry-actions {
        flex-direction: column;
    }

    .btn-entry {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Survey Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="survey-detail-header">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <h1><?php echo htmlspecialchars($survey->survey_name); ?></h1>
                            <div class="survey-meta">
                                <div class="survey-meta-item">
                                    <i class="fa fa-user-circle"></i>
                                    <span><?php echo htmlspecialchars($survey->patient_name); ?></span>
                                </div>
                                <div class="survey-meta-item">
                                    <i class="fa fa-calendar"></i>
                                    <span><?php echo date('d/m/Y', strtotime($survey->start_date)); ?> - <?php echo date('d/m/Y', strtotime($survey->end_date)); ?></span>
                                </div>
                                <div class="survey-meta-item">
                                    <i class="fa fa-clock-o"></i>
                                    <span><?php echo $survey->duration_days; ?> jours</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="header-actions">
                                <?php if (dietetic_has_permission('edit')) { ?>
                                    <a href="<?php echo admin_url('dietetic/food_surveys/edit/' . $survey->id); ?>" class="btn btn-header">
                                        <i class="fa fa-pencil"></i> Modifier
                                    </a>
                                <?php } ?>
                                <a href="<?php echo admin_url('dietetic/food_surveys'); ?>" class="btn btn-header outline">
                                    <i class="fa fa-arrow-left"></i> Retour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="info-cards">
            <!-- Progress Card -->
            <div class="info-card">
                <h4>Progression</h4>
                <div class="progress-circle">
                    <svg width="120" height="120">
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#01807B;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#F3911D;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <circle class="bg" cx="60" cy="60" r="52"></circle>
                        <circle class="progress" cx="60" cy="60" r="52"
                                style="stroke-dasharray: 326.56; stroke-dashoffset: <?php echo 326.56 - (326.56 * $completion_percentage / 100); ?>;"></circle>
                    </svg>
                    <div class="percentage"><?php echo round($completion_percentage); ?>%</div>
                </div>
                <div class="description" style="text-align: center;">
                    <?php
                    $submitted_count = 0;
                    foreach ($entries as $entry) {
                        if ($entry->submitted_at) $submitted_count++;
                    }
                    echo $submitted_count . ' / ' . $survey->duration_days . ' jours complétés';
                    ?>
                </div>
            </div>

            <!-- Status Card -->
            <div class="info-card orange">
                <h4>Statut</h4>
                <div class="value">
                    <?php
                    $status_labels = [
                        'active' => 'Active',
                        'completed' => 'Terminée',
                        'cancelled' => 'Annulée'
                    ];
                    echo $status_labels[$survey->status];
                    ?>
                </div>
                <div class="description">
                    <?php if ($survey->status === 'active') { ?>
                        <?php
                        $today = date('Y-m-d');
                        $start = $survey->start_date;
                        $end = $survey->end_date;

                        if ($today < $start) {
                            $days_until = (strtotime($start) - strtotime($today)) / (60 * 60 * 24);
                            echo 'Débute dans ' . ceil($days_until) . ' jour(s)';
                        } elseif ($today > $end) {
                            echo 'Période terminée';
                        } else {
                            $days_remaining = (strtotime($end) - strtotime($today)) / (60 * 60 * 24);
                            echo ceil($days_remaining) . ' jour(s) restant(s)';
                        }
                        ?>
                    <?php } elseif ($survey->status === 'completed') { ?>
                        Enquête terminée avec succès
                    <?php } else { ?>
                        Enquête annulée
                    <?php } ?>
                </div>
            </div>

            <!-- Objective Card -->
            <div class="info-card green">
                <h4>Objectif</h4>
                <div class="description" style="line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($survey->objective)); ?>
                </div>
            </div>
        </div>

        <!-- Entries List -->
        <div class="row">
            <div class="col-md-12">
                <div class="entries-container">
                    <div class="entries-header">
                        <h3>
                            <i class="fa fa-calendar-check-o"></i>
                            Entrées Quotidiennes
                        </h3>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">Toutes</button>
                            <button class="filter-btn" data-filter="submitted">Soumises</button>
                            <button class="filter-btn" data-filter="pending">En attente</button>
                        </div>
                    </div>

                    <div class="entries-list">
                        <?php if (empty($entries)) { ?>
                            <div class="empty-state">
                                <i class="fa fa-calendar-times-o"></i>
                                <h4>Aucune entrée</h4>
                                <p>Les entrées quotidiennes apparaîtront ici une fois que le patient commencera à soumettre ses repas.</p>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($entries as $entry) { ?>
                                <?php
                                $entry_date = new DateTime($entry->entry_date);
                                $is_submitted = !empty($entry->submitted_at);
                                $has_recommendation = $entry->recommendation_count > 0;
                                $weekday_fr = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                $months_fr = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
                                ?>
                                <div class="entry-card <?php echo $is_submitted ? 'submitted' : 'pending'; ?> <?php echo $has_recommendation ? 'with-recommendation' : ''; ?>"
                                     data-status="<?php echo $is_submitted ? 'submitted' : 'pending'; ?>"
                                     onclick="window.location='<?php echo admin_url('dietetic/food_surveys/view_entry/' . $entry->id); ?>'">

                                    <div class="entry-header">
                                        <div class="entry-date">
                                            <div class="date-box">
                                                <div class="day"><?php echo $entry_date->format('d'); ?></div>
                                                <div class="month"><?php echo $months_fr[(int)$entry_date->format('n')]; ?></div>
                                            </div>
                                            <div class="entry-date-info">
                                                <h4><?php echo $entry_date->format('d/m/Y'); ?></h4>
                                                <div class="weekday"><?php echo $weekday_fr[(int)$entry_date->format('w')]; ?></div>
                                            </div>
                                        </div>

                                        <div class="entry-status">
                                            <span class="status-badge <?php echo $is_submitted ? 'submitted' : 'pending'; ?>">
                                                <i class="fa <?php echo $is_submitted ? 'fa-check-circle' : 'fa-clock-o'; ?>"></i>
                                                <?php echo $is_submitted ? 'Soumise' : 'En attente'; ?>
                                            </span>
                                            <?php if ($has_recommendation) { ?>
                                                <span class="status-badge recommendation">
                                                    <i class="fa fa-comment"></i>
                                                    <?php echo $entry->recommendation_count; ?> Recommandation(s)
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <?php if ($is_submitted) { ?>
                                        <div class="entry-content">
                                            <?php if ($entry->breakfast_photo) { ?>
                                                <div class="entry-summary-item">
                                                    <i class="fa fa-coffee"></i>
                                                    <div>
                                                        <div class="label">Petit-déjeuner</div>
                                                        <div class="value"><?php echo $entry->breakfast_time ? date('H:i', strtotime($entry->breakfast_time)) : '-'; ?></div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($entry->lunch_photo) { ?>
                                                <div class="entry-summary-item">
                                                    <i class="fa fa-cutlery"></i>
                                                    <div>
                                                        <div class="label">Déjeuner</div>
                                                        <div class="value"><?php echo $entry->lunch_time ? date('H:i', strtotime($entry->lunch_time)) : '-'; ?></div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($entry->dinner_photo) { ?>
                                                <div class="entry-summary-item">
                                                    <i class="fa fa-moon-o"></i>
                                                    <div>
                                                        <div class="label">Dîner</div>
                                                        <div class="value"><?php echo $entry->dinner_time ? date('H:i', strtotime($entry->dinner_time)) : '-'; ?></div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if ($entry->water_quantity_ml) { ?>
                                                <div class="entry-summary-item">
                                                    <i class="fa fa-tint"></i>
                                                    <div>
                                                        <div class="label">Eau</div>
                                                        <div class="value"><?php echo $entry->water_quantity_ml; ?> ml</div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="entry-actions" onclick="event.stopPropagation();">
                                            <a href="<?php echo admin_url('dietetic/food_surveys/view_entry/' . $entry->id); ?>" class="btn-entry primary">
                                                <i class="fa fa-eye"></i>
                                                Voir Détails
                                            </a>
                                            <?php if (!$has_recommendation && dietetic_has_permission('edit')) { ?>
                                                <a href="<?php echo admin_url('dietetic/food_surveys/view_entry/' . $entry->id); ?>" class="btn-entry secondary">
                                                    <i class="fa fa-comment"></i>
                                                    Ajouter Recommandation
                                                </a>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="entry-content">
                                            <div class="empty-state" style="padding: 20px;">
                                                <p style="margin: 0;">Le patient n'a pas encore soumis cette entrée</p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filter entries
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');

        // Update active state
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        // Filter cards
        if (filter === 'all') {
            $('.entry-card').show();
        } else {
            $('.entry-card').hide();
            $('.entry-card[data-status="' + filter + '"]').show();
        }
    });
});
</script>

<?php init_tail(); ?>
