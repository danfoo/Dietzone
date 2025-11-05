<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.dietitians-header {
    background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(22, 160, 133, 0.3);
}

.dietitians-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 15px;
}

.dietitian-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #16a085;
}

.dietitian-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.dietitian-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.dietitian-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #16a085;
    flex-shrink: 0;
}

.dietitian-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.dietitian-avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #16a085, #1abc9c);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    font-weight: 700;
}

.dietitian-info {
    flex: 1;
}

.dietitian-name {
    font-size: 22px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.dietitian-email {
    color: #7f8c8d;
    font-size: 14px;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rating-stars {
    color: #f39c12;
    font-size: 20px;
}

.rating-number {
    font-size: 24px;
    font-weight: 700;
    color: #f39c12;
}

.rating-count {
    color: #95a5a6;
    font-size: 13px;
}

.dietitian-stats {
    display: flex;
    gap: 30px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-item i {
    font-size: 18px;
    color: #16a085;
}

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
}

.view-profile-btn {
    padding: 10px 20px;
    background: #16a085;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.view-profile-btn:hover {
    background: #138d75;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
    color: white;
}

.no-dietitians {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.no-dietitians i {
    font-size: 64px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

.no-dietitians p {
    color: #7f8c8d;
    font-size: 16px;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Header Section -->
        <div class="dietitians-header">
            <h1>
                <i class="fa fa-user-md"></i>
                Diététiciens
            </h1>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger" style="padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php } ?>

        <!-- Dietitians List -->
        <?php if (!empty($dietitians)) { ?>
            <?php foreach ($dietitians as $dietitian) { ?>
                <div class="dietitian-card">
                    <div class="dietitian-header">
                        <div class="dietitian-avatar">
                            <?php if (!empty($dietitian->profile_image)) { ?>
                                <img src="<?php echo base_url('uploads/staff_profile_images/' . $dietitian->staffid . '/' . $dietitian->profile_image); ?>" alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                            <?php } else { ?>
                                <div class="dietitian-avatar-placeholder">
                                    <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="dietitian-info">
                            <h2 class="dietitian-name">
                                <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                            </h2>
                            <div class="dietitian-email">
                                <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($dietitian->email); ?>
                            </div>
                        </div>

                        <div>
                            <?php if ($dietitian->total_ratings > 0) { ?>
                                <div class="rating-display">
                                    <div class="rating-number"><?php echo number_format($dietitian->avg_rating, 1); ?></div>
                                    <div>
                                        <div class="rating-stars">
                                            <?php
                                            $full_stars = floor($dietitian->avg_rating);
                                            $half_star = ($dietitian->avg_rating - $full_stars) >= 0.5;

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
                                        <div class="rating-count"><?php echo $dietitian->total_ratings; ?> avis</div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="rating-display">
                                    <div class="rating-stars" style="color: #bdc3c7;">
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>
                                    <div class="rating-count">Aucun avis</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="dietitian-stats">
                        <div class="stat-item">
                            <i class="fa fa-users"></i>
                            <div>
                                <div class="stat-value"><?php echo $dietitian->total_patients; ?></div>
                                <div class="stat-label">Patients</div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <i class="fa fa-calendar-check-o"></i>
                            <div>
                                <div class="stat-value"><?php echo $dietitian->total_consultations; ?></div>
                                <div class="stat-label">Consultations</div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <i class="fa fa-list-alt"></i>
                            <div>
                                <div class="stat-value"><?php echo $dietitian->total_programs; ?></div>
                                <div class="stat-label">Programmes</div>
                            </div>
                        </div>

                        <div style="margin-left: auto;">
                            <a href="<?php echo admin_url('dietetic/dietitians/view/' . $dietitian->staffid); ?>" class="view-profile-btn">
                                <i class="fa fa-eye"></i> Voir le Profil
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="no-dietitians">
                <i class="fa fa-user-md"></i>
                <p>Aucun diététicien trouvé</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php init_tail(); ?>
