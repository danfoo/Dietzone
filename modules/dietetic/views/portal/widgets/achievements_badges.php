<?php
/**
 * Achievements & Badges Widget
 * Affichage moderne des badges et du niveau du patient
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Load gamification model
$CI = &get_instance();
$CI->load->model('dietetic/dietetic_gamification_model');

// Get patient info
$patient_points = $CI->dietetic_gamification_model->get_patient_points($patient->id);
$badge_wall = $CI->dietetic_gamification_model->get_patient_badge_wall($patient->id);
$recent_badges = array_slice(array_filter($badge_wall, function($b) { return $b['unlocked']; }), 0, 6);
$unseen_count = $CI->dietetic_gamification_model->get_unseen_badges_count($patient->id);

// Group badges by category
$badges_by_category = [];
foreach ($badge_wall as $badge) {
    $badges_by_category[$badge['category']][] = $badge;
}
?>

<style>
/* ============================================
   ACHIEVEMENTS & BADGES - Modern Design
   ============================================ */

.achievements-section {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.achievements-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f0f0;
}

.achievements-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.achievements-title i {
    color: #FFD700;
    font-size: 24px;
}

.view-all-btn {
    color: #01807B;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
}

.view-all-btn:hover {
    color: #026660;
    text-decoration: none;
    transform: translateX(4px);
}

/* Level Progress */
.level-progress-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    color: white;
    position: relative;
    overflow: hidden;
}

.level-progress-container::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.level-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.current-level {
    display: flex;
    align-items: center;
    gap: 8px;
}

.level-badge {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.level-name {
    font-size: 18px;
    font-weight: 700;
}

.total-points {
    text-align: right;
}

.points-value {
    font-size: 24px;
    font-weight: 700;
}

.points-label {
    font-size: 12px;
    opacity: 0.9;
}

.level-progress-bar {
    background: rgba(255,255,255,0.2);
    height: 8px;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 8px;
}

.level-progress-fill {
    height: 100%;
    background: white;
    border-radius: 10px;
    transition: width 0.5s ease;
}

.next-level-text {
    font-size: 11px;
    opacity: 0.9;
    text-align: center;
}

/* Badges Grid */
.badges-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.badge-item {
    aspect-ratio: 1;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    text-decoration: none;
}

.badge-item.unlocked {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.badge-item.unlocked:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.badge-item.locked {
    background: #f8f9fa;
    border: 2px dashed #e0e0e0;
    opacity: 0.5;
}

.badge-new {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #FF6B6B;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.badge-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.badge-item.unlocked .badge-icon {
    animation: bounceIn 0.6s;
}

.badge-item.locked .badge-icon {
    filter: grayscale(100%);
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.badge-name {
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    color: #2c3e50;
    line-height: 1.2;
}

.badge-item.locked .badge-name {
    color: #95a5a6;
}

/* Stats Row */
.achievements-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 16px;
}

.stat-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #01807B;
}

.stat-label {
    font-size: 11px;
    color: #6c757d;
    margin-top: 4px;
}

/* Responsive */
@media (min-width: 576px) {
    .badges-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 768px) {
    .badges-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Badge Modal/Tooltip */
.badge-tooltip {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    z-index: 1000;
    max-width: 90%;
    display: none;
    animation: slideUp 0.3s;
}

@keyframes slideUp {
    from {
        transform: translate(-50%, 20px);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.badge-tooltip.active {
    display: block;
}

.badge-tooltip-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.badge-tooltip-icon {
    font-size: 36px;
}

.badge-tooltip-title {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
}

.badge-tooltip-description {
    font-size: 13px;
    color: #6c757d;
    line-height: 1.4;
}

.badge-tooltip-points {
    margin-top: 8px;
    font-size: 12px;
    color: #01807B;
    font-weight: 600;
}
</style>

<!-- Achievements Section -->
<div class="achievements-section">
    <!-- Header -->
    <div class="achievements-header">
        <div class="achievements-title">
            <i class="fa fa-trophy"></i>
            Mes Succès
            <?php if ($unseen_count > 0): ?>
                <span class="badge-new"><?php echo $unseen_count; ?></span>
            <?php endif; ?>
        </div>
        <a href="#" class="view-all-btn" onclick="showAllBadges(); return false;">
            Voir tout <i class="fa fa-chevron-right"></i>
        </a>
    </div>

    <!-- Level Progress -->
    <div class="level-progress-container">
        <div class="level-info">
            <div class="current-level">
                <div class="level-badge">
                    <i class="fa <?php echo $patient_points->level_info['icon']; ?>"></i>
                </div>
                <div>
                    <div class="level-name">Niveau <?php echo $patient_points->level_info['name']; ?></div>
                </div>
            </div>
            <div class="total-points">
                <div class="points-value"><?php echo number_format($patient_points->total_points); ?></div>
                <div class="points-label">points</div>
            </div>
        </div>

        <div class="level-progress-bar">
            <div class="level-progress-fill" style="width: <?php echo $patient_points->progress_to_next; ?>%"></div>
        </div>

        <?php if ($patient_points->next_level): ?>
            <div class="next-level-text">
                <?php
                $points_needed = $patient_points->next_level['min'] - $patient_points->total_points;
                echo "Plus que {$points_needed} points pour atteindre le niveau {$patient_points->next_level['name']} !";
                ?>
            </div>
        <?php else: ?>
            <div class="next-level-text">🏆 Niveau maximum atteint - Vous êtes une légende !</div>
        <?php endif; ?>
    </div>

    <!-- Recent Badges -->
    <div class="badges-grid">
        <?php if (empty($recent_badges)): ?>
            <!-- Show placeholder if no badges -->
            <div class="badge-item locked">
                <div class="badge-icon">🏆</div>
                <div class="badge-name">À débloquer</div>
            </div>
            <div class="badge-item locked">
                <div class="badge-icon">⭐</div>
                <div class="badge-name">À débloquer</div>
            </div>
            <div class="badge-item locked">
                <div class="badge-icon">🔥</div>
                <div class="badge-name">À débloquer</div>
            </div>
        <?php else: ?>
            <?php foreach ($recent_badges as $badge): ?>
                <div class="badge-item <?php echo $badge['unlocked'] ? 'unlocked' : 'locked'; ?>"
                     onclick="showBadgeDetail(<?php echo htmlspecialchars(json_encode($badge)); ?>)">
                    <?php if ($badge['unlocked'] && isset($badge['unlocked_at'])): ?>
                        <?php
                        $unlock_date = new DateTime($badge['unlocked_at']);
                        $now = new DateTime();
                        $diff = $now->diff($unlock_date);
                        if ($diff->days < 7): // New badge if unlocked less than 7 days ago
                        ?>
                            <div class="badge-new">NEW</div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="badge-icon" style="color: <?php echo $badge['color']; ?>;">
                        <i class="fa <?php echo $badge['icon']; ?>"></i>
                    </div>
                    <div class="badge-name"><?php echo htmlspecialchars($badge['name']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Stats -->
    <div class="achievements-stats">
        <div class="stat-box">
            <div class="stat-value"><?php echo count(array_filter($badge_wall, function($b) { return $b['unlocked']; })); ?></div>
            <div class="stat-label">Badges débloqués</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo $patient_points->points_this_week; ?></div>
            <div class="stat-label">Points cette semaine</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo $patient_points->points_today; ?></div>
            <div class="stat-label">Points aujourd'hui</div>
        </div>
    </div>
</div>

<!-- Badge Tooltip -->
<div id="badgeTooltip" class="badge-tooltip">
    <div class="badge-tooltip-header">
        <div class="badge-tooltip-icon" id="tooltipIcon"></div>
        <div>
            <div class="badge-tooltip-title" id="tooltipTitle"></div>
        </div>
    </div>
    <div class="badge-tooltip-description" id="tooltipDescription"></div>
    <div class="badge-tooltip-points" id="tooltipPoints"></div>
</div>

<script>
let tooltipTimeout;

function showBadgeDetail(badge) {
    const tooltip = document.getElementById('badgeTooltip');
    const icon = document.getElementById('tooltipIcon');
    const title = document.getElementById('tooltipTitle');
    const description = document.getElementById('tooltipDescription');
    const points = document.getElementById('tooltipPoints');

    icon.innerHTML = '<i class="fa ' + badge.icon + '" style="color: ' + badge.color + ';"></i>';
    title.textContent = badge.name;
    description.textContent = badge.description;

    if (badge.unlocked) {
        points.innerHTML = '<i class="fa fa-check-circle"></i> Badge débloqué le ' +
            new Date(badge.unlocked_at).toLocaleDateString('fr-FR');
    } else {
        points.innerHTML = '<i class="fa fa-lock"></i> ' + badge.points + ' points à gagner';
    }

    tooltip.classList.add('active');

    // Auto-hide after 3 seconds
    clearTimeout(tooltipTimeout);
    tooltipTimeout = setTimeout(() => {
        tooltip.classList.remove('active');
    }, 3000);
}

function showAllBadges() {
    // TODO: Navigate to full badges page or open modal
    alert('Page complète des badges à venir !');
}

// Close tooltip on click outside
document.addEventListener('click', function(e) {
    const tooltip = document.getElementById('badgeTooltip');
    if (!e.target.closest('.badge-item') && !e.target.closest('.badge-tooltip')) {
        tooltip.classList.remove('active');
    }
});
</script>
