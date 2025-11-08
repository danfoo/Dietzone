<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.notifications-nav {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 30px;
}

.notifications-nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0;
}

.notifications-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 0;
}

.notifications-nav li {
    margin: 0;
}

.notifications-nav a {
    display: block;
    padding: 15px 25px;
    color: #718096;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.notifications-nav a:hover {
    color: #01807B;
    background: #f7fafc;
}

.notifications-nav a.active {
    color: #01807B;
    border-bottom-color: #01807B;
}

.notifications-nav i {
    margin-right: 5px;
}

.milestones-container {
    max-width: 1200px;
    margin: 0 auto;
}

.milestones-header {
    margin-bottom: 30px;
}

.milestones-header h1 {
    color: #2d3748;
    font-size: 24px;
    margin-bottom: 5px;
}

.milestones-header p {
    color: #718096;
    font-size: 14px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #01807B;
}

.stat-card h3 {
    color: #718096;
    font-size: 13px;
    text-transform: uppercase;
    margin: 0 0 10px 0;
    font-weight: 600;
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.stat-card .stat-icon {
    font-size: 24px;
    float: right;
    color: #01807B;
}

.milestone-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s;
}

.milestone-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.milestone-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    flex-shrink: 0;
}

.milestone-icon.gold {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
}

.milestone-icon.silver {
    background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
}

.milestone-icon.bronze {
    background: linear-gradient(135deg, #cd7f32 0%, #e8a76e 100%);
}

.milestone-content {
    flex: 1;
}

.milestone-content h3 {
    color: #2d3748;
    font-size: 20px;
    margin: 0 0 5px 0;
}

.milestone-content .patient-name {
    color: #01807B;
    font-weight: 600;
    font-size: 16px;
}

.milestone-content .milestone-details {
    color: #718096;
    font-size: 14px;
    margin-top: 10px;
}

.milestone-date {
    text-align: right;
    color: #718096;
    font-size: 13px;
}

.milestone-date .date {
    display: block;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 5px;
}

.weight-progress {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.weight-badge {
    background: #f7fafc;
    padding: 5px 12px;
    border-radius: 5px;
    font-size: 13px;
    color: #4a5568;
}

.weight-badge strong {
    color: #2d3748;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
    background: white;
    border-radius: 8px;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #cbd5e0;
}

.empty-state h3 {
    color: #4a5568;
    margin-bottom: 10px;
}

.celebration-banner {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 30px;
}

.celebration-banner h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
}

.celebration-banner p {
    margin: 0;
    opacity: 0.9;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Notifications Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li><a href="<?php echo admin_url('dietetic/notifications/settings'); ?>"><i class="fa fa-cog"></i> Configuration</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/templates'); ?>"><i class="fa fa-file-text-o"></i> Modèles</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/logs'); ?>"><i class="fa fa-list"></i> Historique</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>" class="active"><i class="fa fa-trophy"></i> Jalons</a></li>
                </ul>
            </div>
        </div>

        <div class="milestones-container">
            <div class="milestones-header">
                <h1><i class="fa fa-trophy"></i> Jalons Atteints</h1>
                <p>Célébrez les réussites de vos patients</p>
            </div>

            <?php if (!empty($milestone_stats)): ?>
                <div class="celebration-banner">
                    <h2>🎉 Célébrations du Mois</h2>
                    <p><?php echo $milestone_stats['this_month'] ?? 0; ?> jalon(s) atteint(s) ce mois-ci !</p>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-icon">🏆</span>
                        <h3>Total Jalons</h3>
                        <p class="stat-value"><?php echo $milestone_stats['total'] ?? 0; ?></p>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon">⚖️</span>
                        <h3>5kg Perdus</h3>
                        <p class="stat-value"><?php echo $milestone_stats['5kg'] ?? 0; ?></p>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon">💪</span>
                        <h3>10kg Perdus</h3>
                        <p class="stat-value"><?php echo $milestone_stats['10kg'] ?? 0; ?></p>
                    </div>

                    <div class="stat-card">
                        <span class="stat-icon">🌟</span>
                        <h3>15kg+ Perdus</h3>
                        <p class="stat-value"><?php echo ($milestone_stats['15kg'] ?? 0) + ($milestone_stats['20kg'] ?? 0) + ($milestone_stats['25kg'] ?? 0); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Milestones List -->
            <h2 style="color: #2d3748; margin-bottom: 20px;">Derniers Jalons Atteints</h2>

            <?php if (!empty($milestones)): ?>
                <?php foreach ($milestones as $milestone): ?>
                    <?php
                    // Determine icon and color based on milestone type
                    $icons = [
                        'weight_loss_5kg' => ['icon' => '🥉', 'class' => 'bronze', 'label' => '5kg Perdus'],
                        'weight_loss_10kg' => ['icon' => '🥈', 'class' => 'silver', 'label' => '10kg Perdus'],
                        'weight_loss_15kg' => ['icon' => '🥇', 'class' => 'gold', 'label' => '15kg Perdus'],
                        'weight_loss_20kg' => ['icon' => '🏆', 'class' => 'gold', 'label' => '20kg Perdus'],
                        'weight_loss_25kg' => ['icon' => '👑', 'class' => 'gold', 'label' => '25kg Perdus']
                    ];
                    $milestone_info = $icons[$milestone->milestone_type] ?? ['icon' => '🎯', 'class' => 'bronze', 'label' => 'Objectif Atteint'];
                    ?>

                    <div class="milestone-card">
                        <div class="milestone-icon <?php echo $milestone_info['class']; ?>">
                            <?php echo $milestone_info['icon']; ?>
                        </div>

                        <div class="milestone-content">
                            <h3><?php echo $milestone_info['label']; ?></h3>
                            <div class="patient-name">
                                <a href="<?php echo admin_url('dietetic/patients/view/' . $milestone->patient_id); ?>">
                                    Patient #<?php echo $milestone->patient_id; ?>
                                </a>
                            </div>

                            <div class="weight-progress">
                                <span class="weight-badge">
                                    Départ: <strong><?php echo number_format($milestone->starting_weight, 1); ?> kg</strong>
                                </span>
                                <i class="fa fa-arrow-right" style="color: #01807B;"></i>
                                <span class="weight-badge">
                                    Actuel: <strong><?php echo number_format($milestone->current_weight, 1); ?> kg</strong>
                                </span>
                                <span class="weight-badge" style="background: #c6f6d5; color: #22543d;">
                                    Perte: <strong><?php echo number_format($milestone->weight_lost, 1); ?> kg</strong>
                                </span>
                            </div>

                            <?php if ($milestone->notes): ?>
                                <div class="milestone-details">
                                    <i class="fa fa-quote-left"></i>
                                    <?php echo htmlspecialchars($milestone->notes); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="milestone-date">
                            <span class="date">
                                <?php echo date('d/m/Y', strtotime($milestone->achieved_at)); ?>
                            </span>
                            <span style="font-size: 12px;">
                                <?php
                                $days_ago = floor((time() - strtotime($milestone->achieved_at)) / 86400);
                                if ($days_ago == 0) {
                                    echo "Aujourd'hui";
                                } elseif ($days_ago == 1) {
                                    echo "Hier";
                                } else {
                                    echo "Il y a " . $days_ago . " jours";
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-trophy"></i>
                    <h3>Aucun jalon atteint pour le moment</h3>
                    <p>Les jalons de vos patients apparaîtront ici automatiquement lorsqu'ils atteindront leurs objectifs de perte de poids.</p>
                    <p style="margin-top: 20px; font-size: 13px;">
                        <strong>Jalons disponibles :</strong> 5kg, 10kg, 15kg, 20kg, 25kg perdus
                    </p>
                </div>
            <?php endif; ?>

            <!-- Info Box -->
            <div style="margin-top: 30px; padding: 20px; background: #ebf8ff; border-left: 4px solid #4299e1; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #2c5282;">
                    <i class="fa fa-info-circle"></i> Comment fonctionnent les jalons ?
                </h4>
                <ul style="color: #2d3748; line-height: 1.8;">
                    <li>Les jalons sont automatiquement détectés lorsqu'un patient enregistre son poids</li>
                    <li>Une notification de célébration est envoyée au patient par email, SMS ou WhatsApp</li>
                    <li>Les jalons motivent les patients à continuer leurs efforts</li>
                    <li>Vous pouvez consulter tous les jalons atteints sur cette page</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
