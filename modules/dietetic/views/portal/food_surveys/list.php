<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Mes Enquêtes Alimentaires'; ?> - <?php echo get_option('companyname'); ?></title>

    <!-- Perfex CSS -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">

    <style>
    :root {
        --primary-color: #01807B;
        --secondary-color: #F3911D;
        --tertiary-color: #FFFFFF;
        --text-dark: #2d3748;
        --text-light: #718096;
        --border-color: #e2e8f0;
        --success-color: #48bb78;
        --danger-color: #f56565;
        --warning-color: #ed8936;
        --info-color: #4299e1;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .container-fluid {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header h1 i {
        font-size: 32px;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Back link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 10px 15px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .back-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(-3px);
    }

    /* Empty state */
    .empty-state {
        background: white;
        padding: 60px 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: var(--shadow);
    }

    .empty-state i {
        font-size: 64px;
        color: var(--text-light);
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--text-dark);
        font-size: 22px;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-light);
        font-size: 15px;
        margin-bottom: 30px;
    }

    /* Surveys grid */
    .surveys-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
    }

    .survey-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }

    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .survey-card-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        padding: 20px;
    }

    .survey-card-header h3 {
        margin: 0 0 8px 0;
        font-size: 20px;
        font-weight: 600;
    }

    .survey-objective {
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.5;
    }

    .survey-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .survey-info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: var(--text-dark);
    }

    .survey-info-item i {
        width: 20px;
        text-align: center;
        color: var(--primary-color);
    }

    .survey-info-item strong {
        font-weight: 600;
    }

    /* Progress */
    .progress-section {
        margin-top: 10px;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .progress-percentage {
        color: var(--primary-color);
        font-size: 16px;
    }

    .progress-bar-container {
        background: #f7fafc;
        border-radius: 20px;
        height: 12px;
        overflow: hidden;
        position: relative;
    }

    .progress-bar-fill {
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        height: 100%;
        border-radius: 20px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
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

    /* Status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-badge.active {
        background: rgba(72, 187, 120, 0.1);
        color: var(--success-color);
    }

    .status-badge.completed {
        background: rgba(66, 153, 225, 0.1);
        color: var(--info-color);
    }

    .status-badge.cancelled {
        background: rgba(245, 101, 101, 0.1);
        color: var(--danger-color);
    }

    /* Action buttons */
    .survey-card-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 10px;
    }

    .btn-action {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        color: white;
    }

    .btn-secondary {
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .btn-secondary:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }

        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 22px;
        }

        .surveys-grid {
            grid-template-columns: 1fr;
        }

        .survey-card-footer {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 20px;
        }

        .survey-card-header h3 {
            font-size: 18px;
        }
    }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Back link -->
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="back-link">
            <i class="fa fa-arrow-left"></i>
            Retour au tableau de bord
        </a>

        <!-- Page header -->
        <div class="page-header">
            <h1>
                <i class="fa fa-camera"></i>
                Mes Enquêtes Alimentaires
            </h1>
            <p>Suivez vos enquêtes alimentaires et soumettez vos repas quotidiens</p>
        </div>

        <?php if (empty($surveys)): ?>
            <!-- Empty state -->
            <div class="empty-state">
                <i class="fa fa-inbox"></i>
                <h3>Aucune enquête alimentaire</h3>
                <p>Votre diététicien ne vous a pas encore assigné d'enquête alimentaire.</p>
                <p>Les enquêtes alimentaires vous permettent de partager vos repas quotidiens<br>et de recevoir des recommandations personnalisées.</p>
            </div>
        <?php else: ?>
            <!-- Surveys grid -->
            <div class="surveys-grid">
                <?php foreach ($surveys as $survey): ?>
                    <div class="survey-card">
                        <div class="survey-card-header">
                            <h3><?php echo htmlspecialchars($survey->survey_name); ?></h3>
                            <?php if ($survey->objective): ?>
                                <div class="survey-objective">
                                    <?php echo htmlspecialchars($survey->objective); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="survey-card-body">
                            <!-- Status -->
                            <div class="survey-info-item">
                                <i class="fa fa-info-circle"></i>
                                <span>
                                    Statut:
                                    <?php
                                    $status_class = 'active';
                                    $status_text = 'Active';
                                    $status_icon = 'check-circle';

                                    if ($survey->status == 'completed') {
                                        $status_class = 'completed';
                                        $status_text = 'Terminée';
                                        $status_icon = 'flag-checkered';
                                    } elseif ($survey->status == 'cancelled') {
                                        $status_class = 'cancelled';
                                        $status_text = 'Annulée';
                                        $status_icon = 'times-circle';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fa fa-<?php echo $status_icon; ?>"></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </span>
                            </div>

                            <!-- Duration -->
                            <div class="survey-info-item">
                                <i class="fa fa-calendar"></i>
                                <span>
                                    <strong><?php echo $survey->duration_days; ?> jours</strong>
                                    (<?php echo date('d/m/Y', strtotime($survey->start_date)); ?> - <?php echo date('d/m/Y', strtotime($survey->end_date)); ?>)
                                </span>
                            </div>

                            <!-- Dietitian -->
                            <?php if ($survey->dietitian_name): ?>
                                <div class="survey-info-item">
                                    <i class="fa fa-user-md"></i>
                                    <span>Diététicien: <strong><?php echo htmlspecialchars($survey->dietitian_name); ?></strong></span>
                                </div>
                            <?php endif; ?>

                            <!-- Progress -->
                            <div class="progress-section">
                                <div class="progress-label">
                                    <span>Progression</span>
                                    <span class="progress-percentage"><?php echo round($survey->completion_percentage); ?>%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: <?php echo $survey->completion_percentage; ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="survey-card-footer">
                            <?php if ($survey->status == 'active'): ?>
                                <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id); ?>" class="btn-action btn-primary">
                                    <i class="fa fa-camera"></i>
                                    Soumettre aujourd'hui
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('dietetic/portal/view_recommendations/' . $survey->id); ?>" class="btn-action btn-secondary">
                                <i class="fa fa-comments"></i>
                                Recommandations
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Perfex JS -->
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
</body>
</html>
