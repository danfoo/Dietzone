<?php
$active_page = 'meal_plans';
$page_title = 'Mes Plans de Repas';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Page-specific styles for meal plans */
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

.program-badge {
    background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
    color: white;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

.program-badge i {
    font-size: 24px;
    opacity: 0.9;
}

.program-badge-text {
    flex: 1;
}

.program-badge-label {
    font-size: 11px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.program-badge-name {
    font-size: 16px;
    font-weight: 700;
}

.meal-plans-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-bottom: 24px;
}

.meal-plan-card {
    background: white;
    border-radius: 16px;
    padding: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    border-left: 5px solid #01807B;
}

.meal-plan-card:active {
    transform: scale(0.98);
}

.meal-plan-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.week-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.meal-plan-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.meal-plan-body {
    padding: 20px;
}

.meal-plan-notes {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 16px;
    padding: 14px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 3px solid #01807B;
}

.meal-plan-actions {
    display: flex;
    gap: 10px;
}

.btn-view-meal {
    flex: 1;
    background: linear-gradient(135deg, #01807B 0%, #026661 100%);
    color: white;
    padding: 14px 20px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 48px;
}

.btn-view-meal:active {
    transform: scale(0.97);
}

.btn-view-meal:hover {
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
    color: white;
    text-decoration: none;
}

.empty-state {
    background: white;
    border-radius: 16px;
    padding: 50px 30px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state-icon {
    font-size: 56px;
    color: #dee2e6;
    margin-bottom: 16px;
}

.empty-state-title {
    font-size: 18px;
    font-weight: 700;
    color: #495057;
    margin-bottom: 8px;
}

.empty-state-text {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    max-width: 400px;
    margin: 0 auto;
}

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

@media (min-width: 769px) {
    .meal-plans-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }
}

@media (max-width: 768px) {
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

    .page-header-mobile p {
        font-size: 14px;
    }

    .meal-plan-card {
        border-radius: 12px;
    }

    .meal-plan-header,
    .meal-plan-body {
        padding: 16px;
    }

    .program-badge {
        border-radius: 12px;
        padding: 14px 16px;
    }
}

@media (max-width: 375px) {
    .page-header-mobile h1 {
        font-size: 18px;
    }
}
</style>

<!-- Page Header -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-cutlery"></i> Mes Repas</h1>
            <p>Plans alimentaires personnalisés</p>
        </div>

        <?php if (isset($active_program) && $active_program) { ?>
            <!-- Program Badge -->
            <div class="program-badge animate-in delay-1">
                <i class="fa fa-heartbeat"></i>
                <div class="program-badge-text">
                    <div class="program-badge-label">Programme Actif</div>
                    <div class="program-badge-name"><?php echo htmlspecialchars($active_program->program_name); ?></div>
                </div>
            </div>

            <?php if (!empty($meal_plans)) { ?>
                <!-- Meal Plans Grid -->
                <div class="meal-plans-grid animate-in delay-2">
                    <?php foreach ($meal_plans as $plan) { ?>
                        <div class="meal-plan-card">
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
            <?php } else { ?>
                <!-- Empty State -->
                <div class="empty-state animate-in delay-2">
                    <div class="empty-state-icon">
                        <i class="fa fa-cutlery"></i>
                    </div>
                    <div class="empty-state-title">Aucun plan alimentaire</div>
                    <div class="empty-state-text">
                        Votre diététicien n'a pas encore créé de plan alimentaire. Contactez-le pour plus d'informations.
                    </div>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- No Program -->
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

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
