<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* ===== Page Container ===== */
.dietitian-my-profile-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 30px 0;
}

/* ===== Hero Header with Referral Code ===== */
.profile-hero-header {
    position: relative;
    background: linear-gradient(135deg, #01807B 0%, #00A99D 100%);
    color: white;
    border-radius: 20px;
    padding: 50px;
    margin-bottom: 30px;
    box-shadow: 0 20px 60px rgba(1, 128, 123, 0.3);
    overflow: hidden;
}

.profile-hero-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.hero-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 40px;
    align-items: center;
}

.profile-avatar-section {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    border: 6px solid white;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    position: relative;
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar-section img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-initials {
    font-size: 64px;
    font-weight: 700;
    color: #8b6914;
}

.profile-main-info {
    flex: 1;
}

.profile-name-title {
    font-size: 42px;
    font-weight: 700;
    margin: 0 0 15px 0;
    line-height: 1.2;
}

.profile-role {
    font-size: 18px;
    opacity: 0.9;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ===== Referral Code Badge ===== */
.referral-code-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    padding: 15px 25px;
    border-radius: 50px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.referral-code-badge:hover {
    background: rgba(255, 255, 255, 0.35);
    transform: scale(1.05);
}

.referral-code-badge i {
    font-size: 24px;
}

.referral-code-text {
    letter-spacing: 2px;
    font-family: 'Courier New', monospace;
}

.copy-code-btn {
    background: white;
    color: #01807B;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.copy-code-btn:hover {
    background: #f0f0f0;
    transform: scale(1.05);
}

.profile-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.btn-edit-profile {
    background: white;
    color: #01807B;
    padding: 12px 30px;
    border-radius: 30px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-edit-profile:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* ===== Statistics Cards ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-modern {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color, #01807B) 0%, var(--card-color-light, #00A99D) 100%);
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 20px;
    background: var(--icon-bg, rgba(1, 128, 123, 0.1));
    color: var(--icon-color, #01807B);
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: #718096;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-trend {
    font-size: 12px;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.trend-up {
    color: #48bb78;
}

.trend-down {
    color: #f56565;
}

/* ===== Content Sections ===== */
.profile-content-section {
    background: white;
    border-radius: 16px;
    padding: 35px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
}

.section-title {
    font-size: 24px;
    font-weight: 700;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title i {
    color: #01807B;
    font-size: 28px;
}

.section-edit-btn {
    background: #01807B;
    color: white;
    padding: 10px 24px;
    border-radius: 25px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-edit-btn:hover {
    background: #00A99D;
    transform: translateY(-2px);
}

/* ===== Info Grid ===== */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-label {
    font-size: 13px;
    font-weight: 600;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 16px;
    color: #2d3748;
    font-weight: 500;
}

.info-value.empty {
    color: #cbd5e0;
    font-style: italic;
}

/* ===== Specialties ===== */
.specialties-container {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.specialty-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--specialty-color, #01807B) 0%, var(--specialty-color-light, #00A99D) 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.2);
    transition: all 0.3s ease;
}

.specialty-badge:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(1, 128, 123, 0.3);
}

.specialty-badge i {
    font-size: 16px;
}

.no-specialties {
    color: #a0aec0;
    font-style: italic;
    padding: 20px;
    text-align: center;
    background: #f7fafc;
    border-radius: 12px;
}

/* ===== Bio Section ===== */
.bio-content {
    font-size: 16px;
    line-height: 1.8;
    color: #4a5568;
    padding: 20px;
    background: #f7fafc;
    border-radius: 12px;
    border-left: 4px solid #01807B;
}

.bio-content.empty {
    color: #a0aec0;
    font-style: italic;
}

/* ===== Certifications ===== */
.certifications-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.certification-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: #f7fafc;
    border-radius: 12px;
    border-left: 4px solid #01807B;
    transition: all 0.3s ease;
}

.certification-item:hover {
    background: #edf2f7;
    transform: translateX(5px);
}

.certification-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #01807B, #00A99D);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.certification-details {
    flex: 1;
}

.certification-name {
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 5px;
}

.certification-meta {
    font-size: 14px;
    color: #718096;
}

.no-certifications {
    color: #a0aec0;
    font-style: italic;
    padding: 20px;
    text-align: center;
    background: #f7fafc;
    border-radius: 12px;
}

/* ===== Languages ===== */
.languages-container {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.language-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: #01807B;
    padding: 10px 18px;
    border-radius: 25px;
    border: 2px solid #01807B;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.language-badge:hover {
    background: #01807B;
    color: white;
    transform: translateY(-2px);
}

.language-badge i {
    font-size: 16px;
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .profile-hero-header {
        padding: 30px 20px;
    }

    .hero-grid {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 25px;
    }

    .profile-avatar-section {
        margin: 0 auto;
    }

    .profile-name-title {
        font-size: 32px;
    }

    .referral-code-badge {
        justify-content: center;
    }

    .profile-actions {
        flex-direction: column;
    }

    .btn-edit-profile {
        justify-content: center;
        width: 100%;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

/* ===== Toast Notification ===== */
.toast-notification {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #48bb78;
    color: white;
    padding: 18px 25px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(72, 187, 120, 0.4);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    font-weight: 600;
    z-index: 9999;
    animation: slideInUp 0.4s ease;
}

@keyframes slideInUp {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.toast-notification.hide {
    animation: slideOutDown 0.4s ease forwards;
}

@keyframes slideOutDown {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100px);
        opacity: 0;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="dietitian-my-profile-page">
            <div class="container-fluid">

                <!-- Hero Header -->
                <div class="profile-hero-header">
                    <div class="hero-grid">
                        <!-- Avatar -->
                        <div class="profile-avatar-section">
                            <?php if (isset($staff_member['profile_image']) && !empty($staff_member['profile_image'])): ?>
                                <img src="<?= staff_profile_image_url($staff_member['staffid']); ?>" alt="<?= htmlspecialchars($staff_member['firstname'] . ' ' . $staff_member['lastname']); ?>">
                            <?php else: ?>
                                <div class="avatar-initials">
                                    <?= strtoupper(substr($staff_member['firstname'], 0, 1) . substr($staff_member['lastname'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Main Info -->
                        <div class="profile-main-info">
                            <h1 class="profile-name-title">
                                <?= htmlspecialchars($staff_member['firstname'] . ' ' . $staff_member['lastname']); ?>
                            </h1>

                            <div class="profile-role">
                                <i class="fa fa-user-md"></i>
                                <span>Diététicien(ne) Professionnel(le)</span>
                            </div>

                            <!-- Referral Code Badge -->
                            <div class="referral-code-badge">
                                <i class="fa fa-qrcode"></i>
                                <span class="referral-code-text" id="referralCode">
                                    <?= isset($staff_member['dietitian_referral_code']) && !empty($staff_member['dietitian_referral_code'])
                                        ? htmlspecialchars($staff_member['dietitian_referral_code'])
                                        : 'Non généré'; ?>
                                </span>
                                <?php if (isset($staff_member['dietitian_referral_code']) && !empty($staff_member['dietitian_referral_code'])): ?>
                                    <button class="copy-code-btn" onclick="copyReferralCode()">
                                        <i class="fa fa-copy"></i>
                                        Copier
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="profile-actions">
                                <button class="btn-edit-profile" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                                    <i class="fa fa-edit"></i>
                                    Modifier mon profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card-modern" style="--card-color: #667eea; --card-color-light: #764ba2; --icon-bg: rgba(102, 126, 234, 0.1); --icon-color: #667eea;">
                        <div class="stat-icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="stat-value"><?= isset($stats['total_patients']) ? $stats['total_patients'] : 0; ?></div>
                        <div class="stat-label">Patients Actifs</div>
                        <?php if (isset($stats['new_patients_this_month']) && $stats['new_patients_this_month'] > 0): ?>
                            <div class="stat-trend trend-up">
                                <i class="fa fa-arrow-up"></i>
                                +<?= $stats['new_patients_this_month']; ?> ce mois
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="stat-card-modern" style="--card-color: #f093fb; --card-color-light: #f5576c; --icon-bg: rgba(240, 147, 251, 0.1); --icon-color: #f093fb;">
                        <div class="stat-icon">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <div class="stat-value"><?= isset($stats['total_consultations']) ? $stats['total_consultations'] : 0; ?></div>
                        <div class="stat-label">Consultations</div>
                        <?php if (isset($stats['consultations_this_month']) && $stats['consultations_this_month'] > 0): ?>
                            <div class="stat-trend trend-up">
                                <i class="fa fa-arrow-up"></i>
                                <?= $stats['consultations_this_month']; ?> ce mois
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="stat-card-modern" style="--card-color: #4facfe; --card-color-light: #00f2fe; --icon-bg: rgba(79, 172, 254, 0.1); --icon-color: #4facfe;">
                        <div class="stat-icon">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <div class="stat-value"><?= isset($stats['total_referrals']) ? $stats['total_referrals'] : 0; ?></div>
                        <div class="stat-label">Références</div>
                        <?php if (isset($stats['referrals_this_month']) && $stats['referrals_this_month'] > 0): ?>
                            <div class="stat-trend trend-up">
                                <i class="fa fa-arrow-up"></i>
                                +<?= $stats['referrals_this_month']; ?> ce mois
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="stat-card-modern" style="--card-color: #43e97b; --card-color-light: #38f9d7; --icon-bg: rgba(67, 233, 123, 0.1); --icon-color: #43e97b;">
                        <div class="stat-icon">
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="stat-value">
                            <?= isset($stats['average_rating']) ? number_format($stats['average_rating'], 1) : '0.0'; ?>
                        </div>
                        <div class="stat-label">Note Moyenne</div>
                        <?php if (isset($stats['total_reviews']) && $stats['total_reviews'] > 0): ?>
                            <div class="stat-trend">
                                <i class="fa fa-comment"></i>
                                <?= $stats['total_reviews']; ?> avis
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Professional Information Section -->
                <div class="profile-content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa fa-id-card"></i>
                            Informations Professionnelles
                        </h2>
                        <button class="section-edit-btn" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </button>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-envelope"></i> Email
                            </div>
                            <div class="info-value">
                                <?= htmlspecialchars($staff_member['email']); ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-phone"></i> Téléphone
                            </div>
                            <div class="info-value <?= empty($staff_member['phonenumber']) ? 'empty' : ''; ?>">
                                <?= !empty($staff_member['phonenumber']) ? htmlspecialchars($staff_member['phonenumber']) : 'Non renseigné'; ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-calendar"></i> Années d'Expérience
                            </div>
                            <div class="info-value">
                                <?php
                                    $years = isset($staff_member['dietitian_years_experience']) ? $staff_member['dietitian_years_experience'] : 0;
                                    echo $years > 0 ? $years . ' an' . ($years > 1 ? 's' : '') : 'Non renseigné';
                                ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-clock-o"></i> Membre Depuis
                            </div>
                            <div class="info-value">
                                <?= date('d/m/Y', strtotime($staff_member['datecreated'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialties Section -->
                <div class="profile-content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa fa-stethoscope"></i>
                            Spécialités & Compétences
                        </h2>
                        <button class="section-edit-btn" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </button>
                    </div>

                    <?php if (isset($specialties) && !empty($specialties)): ?>
                        <div class="specialties-container">
                            <?php foreach ($specialties as $specialty): ?>
                                <div class="specialty-badge" style="--specialty-color: <?= $specialty['color']; ?>; --specialty-color-light: <?= $specialty['color']; ?>99;">
                                    <i class="fa <?= $specialty['icon']; ?>"></i>
                                    <span><?= htmlspecialchars($specialty['name_fr']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-specialties">
                            <i class="fa fa-info-circle"></i>
                            Aucune spécialité définie. Cliquez sur "Modifier" pour ajouter vos spécialités.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bio Section -->
                <div class="profile-content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa fa-user"></i>
                            Biographie Professionnelle
                        </h2>
                        <button class="section-edit-btn" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </button>
                    </div>

                    <div class="bio-content <?= empty($staff_member['dietitian_bio']) ? 'empty' : ''; ?>">
                        <?= !empty($staff_member['dietitian_bio']) ? nl2br(htmlspecialchars($staff_member['dietitian_bio'])) : 'Aucune biographie ajoutée. Partagez votre parcours professionnel et votre approche de la nutrition.'; ?>
                    </div>
                </div>

                <!-- Languages Section -->
                <div class="profile-content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa fa-language"></i>
                            Langues Parlées
                        </h2>
                        <button class="section-edit-btn" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </button>
                    </div>

                    <?php if (isset($staff_member['dietitian_languages']) && !empty($staff_member['dietitian_languages'])): ?>
                        <div class="languages-container">
                            <?php
                                $languages = explode(',', $staff_member['dietitian_languages']);
                                foreach ($languages as $language):
                            ?>
                                <div class="language-badge">
                                    <i class="fa fa-globe"></i>
                                    <span><?= htmlspecialchars(trim($language)); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-specialties">
                            <i class="fa fa-info-circle"></i>
                            Aucune langue renseignée. Ajoutez les langues que vous parlez.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Certifications Section -->
                <div class="profile-content-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa fa-certificate"></i>
                            Certifications & Diplômes
                        </h2>
                        <button class="section-edit-btn" onclick="window.location.href='<?= admin_url('dietetic/edit_my_profile'); ?>'">
                            <i class="fa fa-edit"></i>
                            Modifier
                        </button>
                    </div>

                    <?php if (isset($certifications) && !empty($certifications)): ?>
                        <div class="certifications-list">
                            <?php foreach ($certifications as $cert): ?>
                                <div class="certification-item">
                                    <div class="certification-icon">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                    <div class="certification-details">
                                        <div class="certification-name"><?= htmlspecialchars($cert['name']); ?></div>
                                        <?php if (!empty($cert['institution']) || !empty($cert['year'])): ?>
                                            <div class="certification-meta">
                                                <?= !empty($cert['institution']) ? htmlspecialchars($cert['institution']) : ''; ?>
                                                <?= !empty($cert['year']) ? ' • ' . htmlspecialchars($cert['year']) : ''; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-certifications">
                            <i class="fa fa-info-circle"></i>
                            Aucune certification ajoutée. Ajoutez vos diplômes et certifications professionnelles.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function copyReferralCode() {
    const code = document.getElementById('referralCode').textContent.trim();

    // Modern clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('Code de référence copié !');
        }).catch(err => {
            // Fallback
            fallbackCopy(code);
        });
    } else {
        fallbackCopy(code);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        showToast('Code de référence copié !');
    } catch (err) {
        alert('Code: ' + text);
    }

    document.body.removeChild(textarea);
}

function showToast(message) {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    // Create new toast
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
        <i class="fa fa-check-circle"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => {
            toast.remove();
        }, 400);
    }, 3000);
}
</script>

<?php init_tail(); ?>
