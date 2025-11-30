<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('dietetic/portal/portal_header', ['patient' => $patient, 'client' => $client, 'title' => $title]); ?>

<style>
/* Modern Achievements Page Styles */
.achievements-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.achievements-header {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.level-card {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.level-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.level-info {
    flex: 1;
}

.level-title {
    font-size: 24px;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 5px 0;
}

.level-subtitle {
    font-size: 14px;
    color: #718096;
    margin: 0 0 10px 0;
}

.level-progress-bar {
    height: 12px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.level-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s ease;
    border-radius: 10px;
}

.level-progress-text {
    font-size: 12px;
    color: #4a5568;
    margin-top: 5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-radius: 15px;
    padding: 20px;
    text-align: center;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #667eea;
    margin: 0;
}

.stat-label {
    font-size: 12px;
    color: #718096;
    margin: 5px 0 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badges-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    color: #667eea;
}

.category-badges {
    margin-bottom: 30px;
}

.category-badges:last-child {
    margin-bottom: 0;
}

.category-header {
    font-size: 16px;
    font-weight: 600;
    color: #4a5568;
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
}

.badge-card {
    background: #f7fafc;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.badge-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.badge-card.unlocked {
    background: linear-gradient(135deg, #fff 0%, #f0f4ff 100%);
    border: 2px solid #667eea;
}

.badge-card.locked {
    opacity: 0.5;
    filter: grayscale(100%);
}

.badge-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 10px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.badge-name {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 5px 0;
}

.badge-description {
    font-size: 11px;
    color: #718096;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.badge-points {
    font-size: 12px;
    font-weight: 700;
    color: #667eea;
}

.badge-progress {
    margin-top: 8px;
    font-size: 11px;
    color: #805ad5;
    font-weight: 600;
}

.new-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #f56565;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    animation: pulse 2s infinite;
}

.history-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.history-list {
    max-height: 400px;
    overflow-y: auto;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f7fafc;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: all 0.2s ease;
}

.history-item:hover {
    background: #edf2f7;
    transform: translateX(5px);
}

.history-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.history-content {
    flex: 1;
}

.history-description {
    font-size: 14px;
    color: #2d3748;
    margin: 0 0 3px 0;
    font-weight: 500;
}

.history-date {
    font-size: 11px;
    color: #a0aec0;
    margin: 0;
}

.history-points {
    font-size: 18px;
    font-weight: 700;
    color: #48bb78;
}

.history-points.negative {
    color: #f56565;
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

/* Mobile Responsive */
@media (max-width: 768px) {
    .achievements-container {
        padding: 15px;
    }

    .achievements-header {
        padding: 20px;
    }

    .level-card {
        flex-direction: column;
        text-align: center;
    }

    .level-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }

    .badges-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Tooltip */
.badge-tooltip {
    position: fixed;
    background: rgba(0,0,0,0.9);
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    z-index: 1000;
    max-width: 250px;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.badge-tooltip.show {
    opacity: 1;
}
</style>

<div class="achievements-container">
    <!-- Header with Level Info -->
    <div class="achievements-header">
        <div class="level-card">
            <div class="level-icon" style="background: <?php echo $current_level_info['color']; ?>">
                <?php echo $current_level_info['icon']; ?>
            </div>
            <div class="level-info">
                <h1 class="level-title"><?php echo $current_level_info['name']; ?></h1>
                <p class="level-subtitle">
                    <?php echo number_format($patient_points->total_points); ?> points
                    <?php if ($next_level_info): ?>
                        · <?php echo number_format($next_level_info['min'] - $patient_points->total_points); ?> points pour <?php echo $next_level_info['name']; ?>
                    <?php else: ?>
                        · Niveau maximum atteint !
                    <?php endif; ?>
                </p>
                <?php if ($next_level_info): ?>
                    <?php
                    $current_min = $current_level_info['min'];
                    $next_min = $next_level_info['min'];
                    $progress = (($patient_points->total_points - $current_min) / ($next_min - $current_min)) * 100;
                    $progress = min(100, max(0, $progress));
                    ?>
                    <div class="level-progress-bar">
                        <div class="level-progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                    <p class="level-progress-text">
                        <?php echo number_format($patient_points->total_points - $current_min); ?> / <?php echo number_format($next_min - $current_min); ?> points
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($patient_badges); ?></div>
                <div class="stat-label">Badges Débloqués</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($patient_points->points_today); ?></div>
                <div class="stat-label">Points Aujourd'hui</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($patient_points->points_this_week); ?></div>
                <div class="stat-label">Points Cette Semaine</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($patient_points->total_points); ?></div>
                <div class="stat-label">Total des Points</div>
            </div>
        </div>
    </div>

    <!-- Badges Section -->
    <div class="badges-section">
        <h2 class="section-title">
            <i class="fa fa-trophy"></i>
            Tous les Badges
        </h2>

        <?php
        // Group badges by category
        $categories = [
            'streak' => ['name' => 'Régularité', 'icon' => '🔥'],
            'weight_loss' => ['name' => 'Perte de Poids', 'icon' => '⚖️'],
            'nutrition' => ['name' => 'Nutrition', 'icon' => '🥗'],
            'hydration' => ['name' => 'Hydratation', 'icon' => '💧'],
            'activity' => ['name' => 'Activité Physique', 'icon' => '🏃'],
            'goals' => ['name' => 'Objectifs', 'icon' => '🎯']
        ];

        foreach ($categories as $cat_key => $cat_info):
            $cat_badges = array_filter($all_badges, function($b) use ($cat_key) {
                return $b->category == $cat_key;
            });

            if (empty($cat_badges)) continue;
        ?>
            <div class="category-badges">
                <div class="category-header">
                    <?php echo $cat_info['icon']; ?> <?php echo $cat_info['name']; ?>
                </div>
                <div class="badges-grid">
                    <?php foreach ($cat_badges as $badge):
                        $is_unlocked = isset($badge->unlocked_at);
                        $is_new = $is_unlocked && (strtotime($badge->unlocked_at) > strtotime('-7 days'));
                    ?>
                        <div class="badge-card <?php echo $is_unlocked ? 'unlocked' : 'locked'; ?>"
                             data-badge-id="<?php echo $badge->id; ?>"
                             onclick="showBadgeTooltip(this, event)">
                            <?php if ($is_new): ?>
                                <div class="new-badge">NEW</div>
                            <?php endif; ?>
                            <div class="badge-icon" style="background: <?php echo $badge->color; ?>">
                                <?php echo $badge->icon; ?>
                            </div>
                            <div class="badge-name"><?php echo htmlspecialchars($badge->name); ?></div>
                            <div class="badge-description"><?php echo htmlspecialchars($badge->description); ?></div>
                            <div class="badge-points">
                                <?php if ($is_unlocked): ?>
                                    <i class="fa fa-check-circle"></i> Débloqué
                                <?php else: ?>
                                    +<?php echo $badge->points; ?> points
                                <?php endif; ?>
                            </div>
                            <?php if (!$is_unlocked && isset($badge->progress)): ?>
                                <div class="badge-progress">
                                    <?php echo $badge->progress; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Points History -->
    <div class="history-section">
        <h2 class="section-title">
            <i class="fa fa-history"></i>
            Historique des Points
        </h2>
        <div class="history-list">
            <?php if (!empty($points_history)): ?>
                <?php foreach ($points_history as $entry): ?>
                    <div class="history-item">
                        <div class="history-icon">
                            <?php
                            $icon = 'fa fa-star';
                            if ($entry->action_type == 'meal_validated') $icon = 'fa fa-cutlery';
                            elseif ($entry->action_type == 'hydration_logged') $icon = 'fa fa-tint';
                            elseif ($entry->action_type == 'activity_logged') $icon = 'fa fa-heartbeat';
                            elseif ($entry->action_type == 'badge_unlocked') $icon = 'fa fa-trophy';
                            ?>
                            <i class="<?php echo $icon; ?>"></i>
                        </div>
                        <div class="history-content">
                            <p class="history-description">
                                <?php echo htmlspecialchars($entry->action_description ?: ucfirst($entry->action_type)); ?>
                            </p>
                            <p class="history-date">
                                <?php echo date('d/m/Y à H:i', strtotime($entry->created_at)); ?>
                            </p>
                        </div>
                        <div class="history-points <?php echo $entry->points < 0 ? 'negative' : ''; ?>">
                            <?php echo $entry->points > 0 ? '+' : ''; ?><?php echo $entry->points; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #a0aec0; padding: 40px 0;">
                    Aucun historique de points pour le moment. Commencez à gagner des points en validant vos repas !
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="badge-tooltip" id="badgeTooltip"></div>

<script>
function showBadgeTooltip(element, event) {
    // This is a simple click handler - could be enhanced with detailed modal
    const badgeCard = element;
    const isUnlocked = badgeCard.classList.contains('unlocked');

    if (!isUnlocked) {
        // Could show a modal with progress details
        console.log('Badge locked - show progress modal');
    }
}

// Smooth scroll animations
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.badge-card, .history-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.5s ease';
        observer.observe(el);
    });
});
</script>

<?php $this->load->view('dietetic/portal/portal_footer'); ?>
