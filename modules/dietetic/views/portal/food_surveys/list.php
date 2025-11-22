<?php
$active_page = 'food_surveys';
$page_title = 'Mes Enquêtes Alimentaires';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Modern Mobile App Design - No Borders */
.page-header-mobile {
    background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
    border-radius: 24px;
    padding: 28px 24px;
    margin-bottom: 24px;
    color: white;
    box-shadow: 0 12px 24px rgba(1, 128, 123, 0.25);
}

.page-header-mobile h1 {
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    color: white;
}

.page-header-mobile h1 i {
    color: white;
    font-size: 28px;
}

.page-header-mobile p {
    margin: 0;
    font-size: 15px;
    opacity: 0.95;
    font-weight: 500;
    color: white;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 24px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
}

.empty-state i {
    font-size: 80px;
    color: #01807B;
    opacity: 0.2;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 22px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 12px 0;
}

.empty-state p {
    color: #6c757d;
    font-size: 15px;
    line-height: 1.6;
    margin: 0 0 8px 0;
}

/* Surveys Grid */
.surveys-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 769px) {
    .surveys-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1200px) {
    .surveys-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Survey Card */
.survey-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

/* Gradient accent bar at top */
.survey-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
    z-index: 1;
}

.survey-card:active {
    transform: scale(0.98);
}

@media (min-width: 769px) {
    .survey-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(1, 128, 123, 0.15);
    }
}

.survey-card-header {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    padding: 20px;
    color: white;
}

.survey-card-header h3 {
    margin: 0 0 8px 0;
    font-size: 19px;
    font-weight: 700;
}

.survey-objective {
    font-size: 14px;
    opacity: 0.95;
    line-height: 1.5;
}

.survey-card-body {
    padding: 20px;
}

.survey-info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 14px;
    font-size: 14px;
    color: #495057;
}

.survey-info-item i {
    color: #01807B;
    margin-top: 2px;
    flex-shrink: 0;
}

.survey-info-item strong {
    font-weight: 600;
    color: #212529;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.active {
    background: rgba(72, 187, 120, 0.1);
    color: #48bb78;
}

.status-badge.completed {
    background: rgba(66, 153, 225, 0.1);
    color: #4299e1;
}

.status-badge.cancelled {
    background: rgba(245, 101, 101, 0.1);
    color: #f56565;
}

/* Progress Section */
.progress-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e9ecef;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.progress-label span {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
}

.progress-percentage {
    color: #01807B !important;
    font-size: 16px !important;
}

.progress-bar-container {
    height: 10px;
    background: #e9ecef;
    border-radius: 20px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
    border-radius: 20px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Action Buttons */
.survey-card-footer {
    padding: 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 10px;
}

.btn-action {
    flex: 1;
    padding: 12px 16px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    min-height: 44px;
}

.btn-primary-action {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    color: white;
}

.btn-primary-action:active {
    transform: scale(0.96);
}

@media (min-width: 769px) {
    .btn-primary-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        color: white;
        text-decoration: none;
    }
}

.btn-secondary-action {
    background: white;
    color: #01807B;
    border: 2px solid #01807B;
}

.btn-secondary-action:active {
    background: #01807B;
    color: white;
}

@media (min-width: 769px) {
    .btn-secondary-action:hover {
        background: #01807B;
        color: white;
        text-decoration: none;
    }
}

@media (max-width: 480px) {
    .survey-card-footer {
        flex-direction: column;
    }
}

/* Section Headers */
.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 32px 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #e9ecef;
}

.section-header.first {
    margin-top: 0;
}

.section-header h2 {
    font-size: 18px;
    font-weight: 800;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header .badge-count {
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
}

/* Historical Surveys - Grayed Out */
.survey-card.historical {
    opacity: 0.75;
}

.survey-card.historical::before {
    background: linear-gradient(90deg, #95a5a6 0%, #7f8c8d 100%);
}

.survey-card.historical .survey-card-header {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.survey-card.historical .btn-primary-action {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.survey-card.historical .btn-secondary-action {
    border-color: #95a5a6;
    color: #95a5a6;
}

.survey-card.historical .btn-secondary-action:hover {
    background: #95a5a6;
    color: white;
}

/* Accordion Styles for Surveys */
.accordion-survey {
    background: white;
    border-radius: 20px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.accordion-survey.historical {
    opacity: 0.75;
}

.accordion-survey-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
    position: relative;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    color: white;
}

.accordion-survey-header.historical {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.accordion-survey-header:hover {
    filter: brightness(1.05);
}

.accordion-survey-header:active {
    transform: scale(0.99);
}

.accordion-survey-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.accordion-survey-content-wrapper {
    flex: 1;
    min-width: 0;
}

.accordion-survey-name {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.accordion-survey-meta {
    font-size: 11px;
    opacity: 0.95;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.accordion-survey-meta i {
    font-size: 10px;
    margin-right: 4px;
}

.accordion-survey-chevron {
    font-size: 20px;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.accordion-survey-chevron.open {
    transform: rotate(90deg);
}

.accordion-survey-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.4s ease;
    padding: 0 24px;
}

.accordion-survey-content.open {
    max-height: 3000px;
    padding: 24px;
}

.survey-details {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
</style>

        <!-- Page Header -->
        <div class="page-header-mobile">
            <h1><i class="fa fa-list-alt"></i> Enquêtes Alimentaires</h1>
            <p>Suivez vos habitudes alimentaires</p>
        </div>

        <?php
        // Helper function to render survey as accordion
        function render_survey_card($survey, $is_historical = false, $is_first = false) {
            $completion = $survey->completion_percentage;
            $status_class = '';
            $status_text = '';
            $status_icon = '';

            if ($survey->status == 'active') {
                $status_class = 'active';
                $status_text = 'Active';
                $status_icon = 'play-circle';
            } elseif ($survey->status == 'completed') {
                $status_class = 'completed';
                $status_text = 'Terminée';
                $status_icon = 'check-circle';
            } elseif ($survey->status == 'cancelled') {
                $status_class = 'cancelled';
                $status_text = 'Annulée';
                $status_icon = 'times-circle';
            }

            // Dates
            $start_date = date('d/m/Y', strtotime($survey->start_date));
            $end_date = date('d/m/Y', strtotime($survey->end_date));
            $date_range = $start_date . ' - ' . $end_date;

            // Duration
            $duration_text = $survey->duration_days . ' jours';

            // Unique ID
            $accordion_id = 'survey-' . $survey->id;

            // Check if should be open by default (first active survey only)
            $is_open = $is_first && !$is_historical;
            ?>
            <!-- Accordion Survey -->
            <div class="accordion-survey <?php echo $is_historical ? 'historical' : ''; ?>">
                <div class="accordion-survey-header <?php echo $is_historical ? 'historical' : ''; ?>" onclick="toggleSurveyAccordion('<?php echo $accordion_id; ?>')">
                    <i class="fa fa-list-alt accordion-survey-icon"></i>
                    <div class="accordion-survey-content-wrapper">
                        <div class="accordion-survey-name">
                            <?php echo htmlspecialchars($survey->survey_name); ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <i class="fa fa-<?php echo $status_icon; ?>"></i>
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                        <div class="accordion-survey-meta">
                            <span><i class="fa fa-calendar"></i><?php echo $date_range; ?></span>
                            <span><i class="fa fa-clock-o"></i><?php echo $duration_text; ?></span>
                            <?php if ($survey->status == 'active') { ?>
                                <span><i class="fa fa-bar-chart"></i><?php echo round($completion); ?>% complété</span>
                            <?php } ?>
                        </div>
                    </div>
                    <i class="fa fa-chevron-right accordion-survey-chevron <?php echo $is_open ? 'open' : ''; ?>" id="chevron-<?php echo $accordion_id; ?>"></i>
                </div>

                <div class="accordion-survey-content <?php echo $is_open ? 'open' : ''; ?>" id="content-<?php echo $accordion_id; ?>">
                    <div class="survey-details">
                        <?php if (!empty($survey->objective)) { ?>
                            <div class="survey-info-item">
                                <i class="fa fa-bullseye"></i>
                                <div>
                                    <strong>Objectif:</strong>
                                    <?php echo nl2br(htmlspecialchars($survey->objective)); ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($survey->status == 'active') { ?>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Progression</span>
                                <span class="progress-percentage"><?php echo round($completion); ?>%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: <?php echo $completion; ?>%"></div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="survey-card-footer">
                            <?php if ($survey->status == 'active') { ?>
                                <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id); ?>" class="btn-action btn-primary-action">
                                    <i class="fa fa-camera"></i> Soumettre un repas
                                </a>
                            <?php } ?>
                            <a href="<?php echo site_url('dietetic/portal/view_recommendations/' . $survey->id); ?>" class="btn-action btn-secondary-action">
                                <i class="fa fa-comments"></i> Recommandations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <?php if (!empty($active_surveys) || !empty($historical_surveys)) { ?>

            <!-- ACTIVE SURVEYS SECTION -->
            <?php if (!empty($active_surveys)) { ?>
                <div class="section-header first">
                    <h2>
                        <i class="fa fa-play-circle"></i> Enquêtes Actives
                        <span class="badge-count"><?php echo count($active_surveys); ?></span>
                    </h2>
                </div>

                <div>
                    <?php
                    $is_first_active = true;
                    foreach ($active_surveys as $survey) {
                        render_survey_card($survey, false, $is_first_active);
                        $is_first_active = false; // Only first one is open
                    } ?>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <i class="fa fa-info-circle"></i>
                    <h3>Aucune enquête active</h3>
                    <p>Vous n'avez pas d'enquête alimentaire en cours.</p>
                </div>
            <?php } ?>

            <!-- HISTORICAL SURVEYS SECTION -->
            <?php if (!empty($historical_surveys)) { ?>
                <div class="section-header">
                    <h2>
                        <i class="fa fa-history"></i> Historique
                        <span class="badge-count"><?php echo count($historical_surveys); ?></span>
                    </h2>
                </div>

                <div>
                    <?php foreach ($historical_surveys as $survey) {
                        render_survey_card($survey, true, false); // All historical surveys closed by default
                    } ?>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- No Surveys at all -->
            <div class="empty-state">
                <i class="fa fa-list-alt"></i>
                <h3>Aucune enquête disponible</h3>
                <p>Votre diététicien ne vous a pas encore assigné d'enquête alimentaire.</p>
                <p>Les enquêtes vous permettent de partager vos repas et de recevoir des recommandations personnalisées.</p>
            </div>
        <?php } ?>

<script>
// Toggle survey accordion function
function toggleSurveyAccordion(accordionId) {
    const content = document.getElementById('content-' + accordionId);
    const chevron = document.getElementById('chevron-' + accordionId);

    if (content && chevron) {
        const isOpen = content.classList.contains('open');

        if (isOpen) {
            // Close
            content.classList.remove('open');
            chevron.classList.remove('open');
        } else {
            // Open
            content.classList.add('open');
            chevron.classList.add('open');
        }
    }
}
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
