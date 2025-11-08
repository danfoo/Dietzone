<?php
$active_page = 'food_surveys';
$page_title = 'Mes Enquêtes Alimentaires';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Page-specific styles for food surveys list */
.page-header-mobile {
    margin-bottom: 25px;
}

.page-header-mobile h1 {
    font-size: 26px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-mobile h1 i {
    color: #01807B;
    font-size: 28px;
}

.page-header-mobile p {
    color: #6c757d;
    font-size: 15px;
    margin: 0;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
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
</style>

        <!-- Page Header -->
        <div class="page-header-mobile">
            <h1><i class="fa fa-list-alt"></i> Enquêtes Alimentaires</h1>
            <p>Suivez vos habitudes alimentaires</p>
        </div>

        <?php if (empty($surveys)) { ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fa fa-list-alt"></i>
                <h3>Aucune enquête disponible</h3>
                <p>Votre diététicien ne vous a pas encore assigné d'enquête alimentaire.</p>
                <p>Les enquêtes vous permettent de partager vos repas et de recevoir des recommandations personnalisées.</p>
            </div>
        <?php } else { ?>
            <!-- Surveys Grid -->
            <div class="surveys-grid">
                <?php foreach ($surveys as $survey) {
                    $completion = $this->dietetic_food_surveys_model->get_completion_percentage($survey->id);
                    $status_class = '';
                    $status_text = '';

                    if ($survey->status == 'active') {
                        $status_class = 'active';
                        $status_text = 'Active';
                    } elseif ($survey->status == 'completed') {
                        $status_class = 'completed';
                        $status_text = 'Terminée';
                    } elseif ($survey->status == 'cancelled') {
                        $status_class = 'cancelled';
                        $status_text = 'Annulée';
                    }
                ?>
                <div class="survey-card">
                    <div class="survey-card-header">
                        <h3><?php echo htmlspecialchars($survey->survey_name); ?></h3>
                        <?php if (!empty($survey->objective)) { ?>
                            <div class="survey-objective"><?php echo nl2br(htmlspecialchars($survey->objective)); ?></div>
                        <?php } ?>
                    </div>

                    <div class="survey-card-body">
                        <div class="survey-info-item">
                            <i class="fa fa-calendar"></i>
                            <div>
                                <strong>Période:</strong>
                                <?php echo date('d/m/Y', strtotime($survey->start_date)); ?> -
                                <?php echo date('d/m/Y', strtotime($survey->end_date)); ?>
                            </div>
                        </div>

                        <div class="survey-info-item">
                            <i class="fa fa-info-circle"></i>
                            <div>
                                <strong>Statut:</strong>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </div>
                        </div>

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
                    </div>

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
                <?php } ?>
            </div>
        <?php } ?>

<?php $this->load->view('portal/includes/portal_footer'); ?>
