<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.dietitian-profile-header {
    background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
    color: white;
    padding: 40px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(22, 160, 133, 0.3);
}

.profile-top {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-avatar-placeholder {
    width: 100%;
    height: 100%;
    background: white;
    color: #16a085;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
}

.profile-info h1 {
    margin: 0 0 10px 0;
    font-size: 32px;
    font-weight: 700;
}

.profile-email {
    font-size: 16px;
    opacity: 0.95;
}

.profile-rating-summary {
    display: flex;
    align-items: center;
    gap: 30px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 8px;
}

.rating-big {
    text-align: center;
}

.rating-big-number {
    font-size: 64px;
    font-weight: 700;
    line-height: 1;
}

.rating-big-stars {
    font-size: 24px;
    margin-top: 10px;
}

.rating-big-count {
    font-size: 14px;
    opacity: 0.9;
    margin-top: 5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.stat-card.patients { border-left-color: #3498db; }
.stat-card.consultations { border-left-color: #9b59b6; }
.stat-card.programs { border-left-color: #e67e22; }

.stat-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.stat-card.patients .stat-icon { color: #3498db; }
.stat-card.consultations .stat-icon { color: #9b59b6; }
.stat-card.programs .stat-icon { color: #e67e22; }

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    color: #7f8c8d;
    font-size: 13px;
    text-transform: uppercase;
}

.criteria-ratings {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.criteria-ratings h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
}

.criterion-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #ecf0f1;
}

.criterion-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.criterion-label {
    flex: 0 0 200px;
    font-weight: 600;
    color: #2c3e50;
}

.criterion-bar {
    flex: 1;
    height: 30px;
    background: #ecf0f1;
    border-radius: 15px;
    position: relative;
    overflow: hidden;
}

.criterion-fill {
    height: 100%;
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
    border-radius: 15px;
    transition: width 0.5s ease;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: white;
    font-weight: 700;
}

.criterion-value {
    font-size: 18px;
    font-weight: 700;
    color: #f39c12;
    min-width: 50px;
    text-align: right;
}

.reviews-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.reviews-section h3 {
    margin: 0 0 25px 0;
    color: #2c3e50;
}

.review-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #16a085;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.review-patient {
    display: flex;
    align-items: center;
    gap: 10px;
}

.review-patient i {
    font-size: 20px;
    color: #16a085;
}

.review-patient-name {
    font-weight: 700;
    color: #2c3e50;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 10px;
}

.review-stars {
    color: #f39c12;
    font-size: 16px;
}

.review-number {
    font-size: 20px;
    font-weight: 700;
    color: #f39c12;
}

.review-comment {
    color: #34495e;
    line-height: 1.6;
    margin-bottom: 15px;
}

.review-criteria {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.review-criterion {
    font-size: 12px;
    color: #7f8c8d;
}

.review-criterion strong {
    color: #2c3e50;
}

.review-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.review-date {
    font-size: 12px;
    color: #95a5a6;
}

.review-actions {
    display: flex;
    gap: 10px;
}

.no-reviews {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
}

.no-reviews i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.back-btn {
    background: white;
    color: #16a085;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Back Button -->
        <div style="margin-bottom: 15px;">
            <a href="<?php echo admin_url('dietetic/dietitians'); ?>" class="back-btn">
                <i class="fa fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <!-- Profile Header -->
        <div class="dietitian-profile-header">
            <div class="profile-top">
                <div class="profile-avatar">
                    <?php if (!empty($dietitian->profile_image)) { ?>
                        <img src="<?php echo base_url('uploads/staff_profile_images/' . $dietitian->staffid . '/' . $dietitian->profile_image); ?>" alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                    <?php } else { ?>
                        <div class="profile-avatar-placeholder">
                            <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?></h1>
                    <div class="profile-email">
                        <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($dietitian->email); ?>
                    </div>
                </div>
            </div>

            <?php if ($average_ratings && $average_ratings->total_ratings > 0) { ?>
                <div class="profile-rating-summary">
                    <div class="rating-big">
                        <div class="rating-big-number"><?php echo number_format($average_ratings->avg_overall, 1); ?></div>
                        <div class="rating-big-stars">
                            <?php
                            $full_stars = floor($average_ratings->avg_overall);
                            $half_star = ($average_ratings->avg_overall - $full_stars) >= 0.5;

                            for ($i = 0; $i < $full_stars; $i++) {
                                echo '<i class="fa fa-star"></i> ';
                            }
                            if ($half_star) {
                                echo '<i class="fa fa-star-half-o"></i> ';
                                $full_stars++;
                            }
                            for ($i = $full_stars; $i < 5; $i++) {
                                echo '<i class="fa fa-star-o"></i> ';
                            }
                            ?>
                        </div>
                        <div class="rating-big-count">Basé sur <?php echo $average_ratings->total_ratings; ?> avis</div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card patients">
                <div class="stat-icon"><i class="fa fa-users"></i></div>
                <div class="stat-number"><?php echo $stats->total_patients; ?></div>
                <div class="stat-label">Total Patients</div>
                <div style="margin-top: 5px; font-size: 12px; color: #3498db;">
                    <?php echo $stats->active_patients; ?> actifs
                </div>
            </div>

            <div class="stat-card consultations">
                <div class="stat-icon"><i class="fa fa-calendar-check-o"></i></div>
                <div class="stat-number"><?php echo $stats->total_consultations; ?></div>
                <div class="stat-label">Total Consultations</div>
                <div style="margin-top: 5px; font-size: 12px; color: #9b59b6;">
                    <?php echo $stats->completed_consultations; ?> complétées
                </div>
            </div>

            <div class="stat-card programs">
                <div class="stat-icon"><i class="fa fa-list-alt"></i></div>
                <div class="stat-number"><?php echo $stats->total_programs; ?></div>
                <div class="stat-label">Total Programmes</div>
                <div style="margin-top: 5px; font-size: 12px; color: #e67e22;">
                    <?php echo $stats->active_programs; ?> actifs
                </div>
            </div>
        </div>

        <!-- Criteria Ratings -->
        <?php if ($average_ratings && $average_ratings->total_ratings > 0) { ?>
            <div class="criteria-ratings">
                <h3><i class="fa fa-bar-chart"></i> Évaluations par Critère</h3>

                <div class="criterion-item">
                    <div class="criterion-label">Professionnalisme</div>
                    <div class="criterion-bar">
                        <div class="criterion-fill" style="width: <?php echo ($average_ratings->avg_professionalism / 5 * 100); ?>%;">
                            <?php echo number_format($average_ratings->avg_professionalism, 1); ?>
                        </div>
                    </div>
                    <div class="criterion-value"><?php echo number_format($average_ratings->avg_professionalism, 1); ?>/5</div>
                </div>

                <div class="criterion-item">
                    <div class="criterion-label">Écoute</div>
                    <div class="criterion-bar">
                        <div class="criterion-fill" style="width: <?php echo ($average_ratings->avg_listening / 5 * 100); ?>%;">
                            <?php echo number_format($average_ratings->avg_listening, 1); ?>
                        </div>
                    </div>
                    <div class="criterion-value"><?php echo number_format($average_ratings->avg_listening, 1); ?>/5</div>
                </div>

                <div class="criterion-item">
                    <div class="criterion-label">Conseils Pratiques</div>
                    <div class="criterion-bar">
                        <div class="criterion-fill" style="width: <?php echo ($average_ratings->avg_advice / 5 * 100); ?>%;">
                            <?php echo number_format($average_ratings->avg_advice, 1); ?>
                        </div>
                    </div>
                    <div class="criterion-value"><?php echo number_format($average_ratings->avg_advice, 1); ?>/5</div>
                </div>

                <div class="criterion-item">
                    <div class="criterion-label">Résultats Obtenus</div>
                    <div class="criterion-bar">
                        <div class="criterion-fill" style="width: <?php echo ($average_ratings->avg_results / 5 * 100); ?>%;">
                            <?php echo number_format($average_ratings->avg_results, 1); ?>
                        </div>
                    </div>
                    <div class="criterion-value"><?php echo number_format($average_ratings->avg_results, 1); ?>/5</div>
                </div>

                <div class="criterion-item">
                    <div class="criterion-label">Disponibilité</div>
                    <div class="criterion-bar">
                        <div class="criterion-fill" style="width: <?php echo ($average_ratings->avg_availability / 5 * 100); ?>%;">
                            <?php echo number_format($average_ratings->avg_availability, 1); ?>
                        </div>
                    </div>
                    <div class="criterion-value"><?php echo number_format($average_ratings->avg_availability, 1); ?>/5</div>
                </div>
            </div>
        <?php } ?>

        <!-- Reviews List -->
        <div class="reviews-section">
            <h3><i class="fa fa-comments"></i> Avis des Patients (<?php echo count($ratings); ?>)</h3>

            <?php if (!empty($ratings)) { ?>
                <?php foreach ($ratings as $rating) { ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-patient">
                                <i class="fa fa-user-circle"></i>
                                <span class="review-patient-name"><?php echo htmlspecialchars($rating->patient_name); ?></span>
                                <?php if (!$rating->is_public) { ?>
                                    <span class="label label-warning">Masqué</span>
                                <?php } ?>
                            </div>

                            <div class="review-rating">
                                <div class="review-number"><?php echo number_format($rating->overall_rating, 1); ?></div>
                                <div class="review-stars">
                                    <?php
                                    $full_stars = floor($rating->overall_rating);
                                    $half_star = ($rating->overall_rating - $full_stars) >= 0.5;

                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<i class="fa fa-star"></i> ';
                                    }
                                    if ($half_star) {
                                        echo '<i class="fa fa-star-half-o"></i> ';
                                        $full_stars++;
                                    }
                                    for ($i = $full_stars; $i < 5; $i++) {
                                        echo '<i class="fa fa-star-o"></i> ';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($rating->comment) { ?>
                            <div class="review-comment">
                                "<?php echo nl2br(htmlspecialchars($rating->comment)); ?>"
                            </div>
                        <?php } ?>

                        <div class="review-criteria">
                            <?php if ($rating->professionalism_rating) { ?>
                                <div class="review-criterion">
                                    <strong>Professionnalisme:</strong> <?php echo $rating->professionalism_rating; ?>/5
                                </div>
                            <?php } ?>
                            <?php if ($rating->listening_rating) { ?>
                                <div class="review-criterion">
                                    <strong>Écoute:</strong> <?php echo $rating->listening_rating; ?>/5
                                </div>
                            <?php } ?>
                            <?php if ($rating->advice_rating) { ?>
                                <div class="review-criterion">
                                    <strong>Conseils:</strong> <?php echo $rating->advice_rating; ?>/5
                                </div>
                            <?php } ?>
                            <?php if ($rating->results_rating) { ?>
                                <div class="review-criterion">
                                    <strong>Résultats:</strong> <?php echo $rating->results_rating; ?>/5
                                </div>
                            <?php } ?>
                            <?php if ($rating->availability_rating) { ?>
                                <div class="review-criterion">
                                    <strong>Disponibilité:</strong> <?php echo $rating->availability_rating; ?>/5
                                </div>
                            <?php } ?>
                        </div>

                        <div class="review-meta">
                            <div class="review-date">
                                <i class="fa fa-clock-o"></i> <?php echo _dt($rating->created_at); ?>
                            </div>

                            <?php if (is_admin()) { ?>
                                <div class="review-actions">
                                    <button class="btn btn-sm btn-warning toggle-visibility" data-id="<?php echo $rating->id; ?>" data-public="<?php echo $rating->is_public; ?>">
                                        <i class="fa fa-eye<?php echo $rating->is_public ? '-slash' : ''; ?>"></i>
                                        <?php echo $rating->is_public ? 'Masquer' : 'Publier'; ?>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-rating" data-id="<?php echo $rating->id; ?>">
                                        <i class="fa fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="no-reviews">
                    <i class="fa fa-comment-o"></i>
                    <p>Aucun avis pour le moment</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle visibility
    $('.toggle-visibility').on('click', function() {
        var btn = $(this);
        var ratingId = btn.data('id');
        var isPublic = btn.data('public');

        $.ajax({
            url: '<?php echo admin_url('dietetic/dietitians/toggle_rating_visibility/'); ?>' + ratingId,
            type: 'POST',
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert_float('success', data.message);
                    location.reload();
                } else {
                    alert_float('danger', data.message);
                }
            }
        });
    });

    // Delete rating
    $('.delete-rating').on('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
            return;
        }

        var btn = $(this);
        var ratingId = btn.data('id');

        $.ajax({
            url: '<?php echo admin_url('dietetic/dietitians/delete_rating/'); ?>' + ratingId,
            type: 'POST',
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert_float('success', data.message);
                    location.reload();
                } else {
                    alert_float('danger', data.message);
                }
            }
        });
    });
});
</script>

<?php init_tail(); ?>
