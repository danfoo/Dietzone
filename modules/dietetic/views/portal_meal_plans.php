<?php
$active_page = 'meal_plans';
$page_title = 'Mes Plans de Repas';
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
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-mobile p {
    margin: 0;
    font-size: 15px;
    opacity: 0.95;
    font-weight: 500;
}

.program-badge {
    background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
    color: white;
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.25);
}

.program-badge i {
    font-size: 28px;
    opacity: 0.95;
}

.program-badge-text {
    flex: 1;
}

.program-badge-label {
    font-size: 11px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
    font-weight: 600;
}

.program-badge-name {
    font-size: 17px;
    font-weight: 800;
}

.meal-plans-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 24px;
}

/* Card without borders - floating effect */
.meal-plan-card {
    background: white;
    border-radius: 24px;
    padding: 0;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}

.meal-plan-card:active {
    transform: translateY(2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Gradient accent bar - subtle top accent instead of left border */
.meal-plan-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
}

.meal-plan-header {
    background: linear-gradient(135deg, #f8fcfc 0%, #f0f9f9 100%);
    padding: 24px;
}

.week-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 24px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
}

.meal-plan-title {
    font-size: 19px;
    font-weight: 800;
    color: #1a1a1a;
    margin: 0;
    line-height: 1.4;
}

.meal-plan-body {
    padding: 24px;
}

/* Notes without border - soft background only */
.meal-plan-notes {
    color: #495057;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 20px;
    padding: 16px 18px;
    background: linear-gradient(135deg, #f8fcfc 0%, #f0f9f9 100%);
    border-radius: 16px;
}

.meal-plan-notes i {
    color: #01807B;
    margin-right: 6px;
}

.meal-plan-actions {
    display: flex;
    gap: 12px;
}

.btn-view-meal {
    flex: 1;
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 16px 24px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 52px;
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.25);
}

.btn-view-meal:active {
    transform: translateY(2px);
    box-shadow: 0 3px 8px rgba(1, 128, 123, 0.3);
}

.btn-view-meal:hover {
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.35);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.empty-state {
    background: white;
    border-radius: 24px;
    padding: 60px 30px;
    text-align: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.empty-state-icon {
    font-size: 64px;
    color: #e0e0e0;
    margin-bottom: 20px;
}

.empty-state-title {
    font-size: 20px;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state-text {
    color: #6c757d;
    font-size: 15px;
    line-height: 1.7;
    max-width: 400px;
    margin: 0 auto;
}

.btn-back {
    background: white;
    color: #495057;
    border: none;
    padding: 14px 28px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 52px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.btn-back:hover {
    background: #01807B;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.25);
}

.btn-back:active {
    transform: translateY(1px);
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

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-left: auto;
}

.status-badge.active {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
}

.status-badge.completed {
    background: #95a5a6;
    color: white;
}

.status-badge.cancelled {
    background: #e74c3c;
    color: white;
}

/* Historical Programs - Grayed Out */
.program-badge.historical {
    opacity: 0.7;
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.meal-plan-card.historical {
    opacity: 0.75;
}

.meal-plan-card.historical::before {
    background: linear-gradient(90deg, #95a5a6 0%, #7f8c8d 100%);
}

.meal-plan-card.historical .week-badge {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
}

.meal-plan-card.historical .btn-view-meal {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    box-shadow: 0 6px 16px rgba(149, 165, 166, 0.25);
}

.meal-plan-card.historical .btn-view-meal:hover {
    box-shadow: 0 8px 20px rgba(149, 165, 166, 0.35);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: fadeInUp 0.6s ease-out forwards;
}

.delay-1 { animation-delay: 0.1s; opacity: 0; }
.delay-2 { animation-delay: 0.2s; opacity: 0; }

@media (min-width: 769px) {
    .meal-plans-grid {
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .content-container {
        padding: 16px 12px 20px;
    }

    .page-header-mobile {
        padding: 24px 20px;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .page-header-mobile h1 {
        font-size: 22px;
    }

    .page-header-mobile p {
        font-size: 14px;
    }

    .meal-plan-card {
        border-radius: 20px;
    }

    .meal-plan-header {
        padding: 20px;
    }

    .meal-plan-body {
        padding: 20px;
    }

    .program-badge {
        border-radius: 18px;
        padding: 18px 20px;
    }
}

@media (max-width: 375px) {
    .page-header-mobile {
        padding: 20px 16px;
        border-radius: 18px;
    }

    .page-header-mobile h1 {
        font-size: 20px;
    }

    .meal-plan-header,
    .meal-plan-body {
        padding: 16px;
    }
}
</style>

<!-- Page Header -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-cutlery"></i> Mes Repas</h1>
            <p>Plans alimentaires personnalisés</p>
        </div>

        <?php
        // Helper function to render program section
        function render_program_section($program, $is_historical = false) {
            $status_class = $program->status;
            $status_label = $program->status === 'active' ? 'ACTIF' : ($program->status === 'completed' ? 'TERMINÉ' : 'ANNULÉ');
            $status_icon = $program->status === 'active' ? 'check-circle' : ($program->status === 'completed' ? 'check' : 'times-circle');
            ?>
            <!-- Program Badge -->
            <div class="program-badge <?php echo $is_historical ? 'historical' : ''; ?> animate-in delay-1">
                <i class="fa fa-heartbeat"></i>
                <div class="program-badge-text">
                    <div class="program-badge-label"><?php echo $is_historical ? 'Programme' : 'Programme Actif'; ?></div>
                    <div class="program-badge-name"><?php echo htmlspecialchars($program->program_name); ?></div>
                </div>
                <span class="status-badge <?php echo $status_class; ?>">
                    <i class="fa fa-<?php echo $status_icon; ?>"></i>
                    <?php echo $status_label; ?>
                </span>
            </div>

            <?php if (!empty($program->meal_plans)) { ?>
                <!-- Meal Plans Grid -->
                <div class="meal-plans-grid animate-in delay-2">
                    <?php foreach ($program->meal_plans as $plan) { ?>
                        <div class="meal-plan-card <?php echo $is_historical ? 'historical' : ''; ?>">
                            <div class="meal-plan-header">
                                <div class="week-badge">
                                    <i class="fa fa-calendar"></i>
                                    Semaine <?php echo $plan->week_number; ?>
                                </div>
                                <h3 class="meal-plan-title"><?php echo htmlspecialchars($plan->plan_name); ?></h3>
                            </div>
                            <div class="meal-plan-body">
                                <?php if ($plan->notes) { ?>
                                    <div class="meal-plan-notes">
                                        <i class="fa fa-sticky-note-o"></i> <?php echo nl2br(htmlspecialchars($plan->notes)); ?>
                                    </div>
                                <?php } ?>
                                <div class="meal-plan-actions">
                                    <a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>" class="btn-view-meal">
                                        <i class="fa fa-eye"></i> Voir les Repas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php }
        }
        ?>

        <?php if (!empty($active_programs) || !empty($historical_programs)) { ?>

            <!-- ACTIVE PROGRAMS SECTION -->
            <?php if (!empty($active_programs)) { ?>
                <div class="section-header first">
                    <h2>
                        <i class="fa fa-heartbeat"></i> Programmes Actifs
                        <span class="badge-count"><?php echo count($active_programs); ?></span>
                    </h2>
                </div>

                <?php foreach ($active_programs as $program) {
                    render_program_section($program, false);
                } ?>
            <?php } else { ?>
                <div class="empty-state animate-in delay-1">
                    <div class="empty-state-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div class="empty-state-title">Aucun programme actif</div>
                    <div class="empty-state-text">
                        Vous n'avez pas de programme diététique actif. Contactez votre diététicien pour commencer.
                    </div>
                </div>
            <?php } ?>

            <!-- HISTORICAL PROGRAMS SECTION -->
            <?php if (!empty($historical_programs)) { ?>
                <div class="section-header">
                    <h2>
                        <i class="fa fa-history"></i> Historique
                        <span class="badge-count"><?php echo count($historical_programs); ?></span>
                    </h2>
                </div>

                <?php foreach ($historical_programs as $program) {
                    render_program_section($program, true);
                } ?>
            <?php } ?>

        <?php } else { ?>
            <!-- No Programs at all -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div class="empty-state-title">Aucun programme</div>
                <div class="empty-state-text">
                    Vous n'avez aucun programme diététique. Contactez votre diététicien pour commencer.
                </div>
            </div>
        <?php } ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
