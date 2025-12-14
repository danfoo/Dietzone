<?php
$active_page = 'my_dietitians';
$page_title = 'Mon Diététicien';
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

.dietitian-card {
    border-radius: 24px;
    padding: 0px;
    margin-bottom: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}

/* Gradient accent bar at top */
.dietitian-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
}

        .dietitian-card:active {
            transform: scale(0.98);
        }

        .dietitian-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }

        .dietitian-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .dietitian-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dietitian-info {
            flex: 1;
        }

        .dietitian-name {
            font-size: 14px;
            padding-top: 10px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .dietitian-specialty {
            color: #6c757d;
            font-size: 12px;
        }

        .referral-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.1) 0%, rgba(243, 145, 29, 0.1) 100%);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #01807B;
            margin-top: 4px;
            border: 1px solid rgba(1, 128, 123, 0.2);
        }

        .referral-code-badge i {
            font-size: 14px;
        }

        .specialties-section {
            margin: 16px 0;
        }

        .specialties-title {
            font-size: 18px;
            font-weight: 800;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .specialties-title::before,
        .specialties-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #dee2e6;
            max-width: 100px;
        }

        .specialties-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .specialty-card {
            background: white;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .specialty-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: var(--specialty-color, #01807B);
        }

        .specialty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.1) 0%, rgba(243, 145, 29, 0.1) 100%);
        }

        .specialty-icon i {
            font-size: 40px;
            color: var(--specialty-color, #01807B);
        }

        .specialty-name {
            font-size: 13px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .specialty-description {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
        }

        .bio-section {
            margin: 16px 0;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
        }

        .bio-title {
            font-size: 13px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .bio-title i {
            color: #01807B;
            font-size: 14px;
        }

        .bio-text {
            color: #2c3e50;
            font-size: 12px;
            line-height: 1.8;
        }

        .info-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 16px 0;
        }

        .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: #495057;
            transition: all 0.2s ease;
        }

        .info-pill:hover {
            border-color: #01807B;
            color: #01807B;
        }

        .info-pill i {
            font-size: 14px;
            color: #01807B;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 16px 0;
        }

        .stat-card {
            background: white;
            padding: 14px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #f8f9fa;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #01807B;
            transform: translateY(-2px);
        }

        .stat-card i {
            font-size: 24px;
            color: #01807B;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .certifications-section {
            margin: 16px 0;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .certifications-title {
            font-size: 13px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .certification-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 8px;
            border-left: 3px solid #F3911D;
        }

        .certification-item:last-child {
            margin-bottom: 0;
        }

        .certification-item i {
            color: #F3911D;
            font-size: 18px;
        }

        .certification-text {
            flex: 1;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
        }

        .dietitian-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .detail-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .detail-item i {
            color: #01807B;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .detail-item label {
            display: block;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .detail-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-contact {
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

        .btn-contact:hover {
            box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-contact:active {
            transform: scale(0.97);
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

        .rating-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            position: relative;
        }

        /* Gradient accent bar at top */
        .rating-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #F3911D 0%, #01807B 100%);
        }

        .rating-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .rating-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .rating-header i {
            color: #F3911D;
            font-size: 22px;
        }

        .average-rating {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 16px;
        }

        .rating-score {
            font-size: 36px;
            font-weight: 700;
            color: #01807B;
            margin-bottom: 8px;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .rating-stars i {
            font-size: 24px;
            color: #F3911D;
        }

        .rating-stars i.fa-star-o {
            color: #dee2e6;
        }

        .rating-count {
            color: #6c757d;
            font-size: 13px;
        }

        .my-rating-section {
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.1) 0%, rgba(243, 145, 29, 0.1) 100%);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        .my-rating-header {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .my-rating-header i {
            color: #01807B;
        }

        .interactive-stars {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .interactive-stars i {
            font-size: 36px;
            color: #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .interactive-stars i:hover,
        .interactive-stars i.hover {
            color: #F3911D;
            transform: scale(1.15);
        }

        .interactive-stars i.selected {
            color: #F3911D;
        }

        .rating-comment {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            margin-bottom: 12px;
            transition: border-color 0.3s ease;
        }

        .rating-comment:focus {
            outline: none;
            border-color: #01807B;
        }

        .btn-submit-rating {
            width: 100%;
            background: linear-gradient(135deg, #F3911D 0%, #d97e0a 100%);
            color: white;
            padding: 14px 20px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
            font-size: 16px;
        }

        .btn-submit-rating:hover {
            box-shadow: 0 6px 16px rgba(243, 145, 29, 0.4);
        }

        .btn-submit-rating:active {
            transform: scale(0.97);
        }

        .btn-submit-rating:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .rating-message {
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
            display: none;
            text-align: center;
            font-weight: 600;
        }

        .rating-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .rating-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .current-rating-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .current-rating-display .rating-stars {
            margin: 0;
        }

        .current-rating-text {
            font-size: 14px;
            color: #6c757d;
        }

        .criteria-ratings {
            margin-top: 16px;
        }

        .criterion-item {
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e9ecef;
        }

        .criterion-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .criterion-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .criterion-label i {
            color: #01807B;
            font-size: 16px;
        }

        .criterion-stars {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .criterion-stars i {
            font-size: 18px;
            color: #F3911D;
        }

        .criterion-stars i.fa-star-o {
            color: #dee2e6;
        }

        .criterion-stars .criterion-score {
            margin-left: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
        }

        .rating-form-group {
            margin-bottom: 20px;
        }

        .rating-form-label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-form-label i {
            color: #01807B;
        }

        .rating-form-label .required {
            color: #dc3545;
            margin-left: 4px;
        }

        .btn-edit-rating {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            margin-top: 12px;
        }

        .btn-edit-rating:hover {
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.4);
        }

        .btn-edit-rating:active {
            transform: scale(0.97);
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
            body {
                padding-bottom: 0;
            }

            .bottom-nav {
                display: none !important;
            }

            .portal-nav-desktop {
                display: flex !important;
            }

            .dietitian-details {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .bottom-nav {
                display: block;
            }

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

            .dietitian-card {
                border-radius: 12px;
            }

            .dietitian-details {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
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
</style>

<div class="content-container">
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-user-md"></i> Mon Diététicien</h1>
            <p>Contact et informations</p>
        </div>

        <?php if (!empty($dietitian)) { ?>
            <div class="animate-in delay-1">
                <div class="dietitian-card">
                    <div class="dietitian-header">
                        <div class="dietitian-avatar">
                            <?php if (!empty($dietitian->profile_image)) { ?>
                                <img src="<?php echo staff_profile_image_url($dietitian->staffid, 'small'); ?>" alt="<?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>">
                            <?php } else { ?>
                                <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                            <?php } ?>
                        </div>
                        <div class="dietitian-info">
                            <div class="dietitian-name"><?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?></div>
                            <div class="dietitian-specialty">Diététicien-Nutritionniste</div>
                            <?php if (!empty($dietitian->dietitian_referral_code)) { ?>
                            <div class="referral-code-badge">
                                <i class="fa fa-qrcode"></i>
                                <span><?php echo htmlspecialchars($dietitian->dietitian_referral_code); ?></span>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Info Pills: Experience & Languages -->
                    <?php if (!empty($dietitian->dietitian_years_experience) || !empty($languages)) { ?>
                    <div class="info-pills">
                        <?php if (!empty($dietitian->dietitian_years_experience) && $dietitian->dietitian_years_experience > 0) { ?>
                        <div class="info-pill">
                            <i class="fa fa-briefcase"></i>
                            <span><?php echo $dietitian->dietitian_years_experience; ?> an<?php echo $dietitian->dietitian_years_experience > 1 ? 's' : ''; ?> d'expérience</span>
                        </div>
                        <?php } ?>
                        <?php if (!empty($languages)) { ?>
                        <div class="info-pill">
                            <i class="fa fa-globe"></i>
                            <span><?php echo implode(', ', $languages); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <!-- Bio -->
                    <?php if (!empty($dietitian->dietitian_bio)) { ?>
                    <div class="bio-section">
                        <div class="bio-title">
                            <i class="fa fa-info-circle"></i>
                            À propos
                        </div>
                        <div class="bio-text">
                            <?php echo nl2br(htmlspecialchars($dietitian->dietitian_bio)); ?>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- Specialties -->
                    <?php if (!empty($specialties)) {
                        // Descriptions pour chaque spécialité
                        $specialty_descriptions = [
                            'Perte de poids' => 'Atteignez vos objectifs santé durablement',
                            'Nutrition sportive' => 'Optimisez performance et récupération',
                            'Diabète' => 'Équilibrez votre glycémie au quotidien',
                            'Nutrition pédiatrique' => 'Croissance et développement de l\'enfant',
                            'Grossesse' => 'Accompagnement avant, pendant, après',
                            'Troubles du comportement alimentaire (TCA)' => 'Retrouvez une relation saine avec la nourriture',
                            'Végétarisme/Véganisme' => 'Équilibre nutritionnel sans produits animaux',
                            'Maladies cardiovasculaires' => 'Protégez votre cœur par l\'alimentation',
                            'Allergies alimentaires' => 'Gérez vos intolérances en toute sécurité',
                            'Nutrition gériatrique' => 'Vitalité et santé à tout âge',
                            'Nutrition clinique' => 'Prise en charge pathologies chroniques',
                            'Bien-être général' => 'Équilibre et vitalité au quotidien',
                            'Nutrition de la femme' => 'Hormones, cycles et étapes à vie',
                            'Nutrition santé publique & collective' => 'Programs pour communautés',
                            'Nutrition fonctionnelle & préventive' => 'Approche personnalisée et globale'
                        ];
                    ?>
                    <div class="specialties-section">
                        <div class="specialties-title">
                            Spécialités du Diététicien
                        </div>
                        <div class="specialties-list">
                            <?php foreach ($specialties as $specialty) {
                                $description = isset($specialty_descriptions[$specialty['name_fr']])
                                    ? $specialty_descriptions[$specialty['name_fr']]
                                    : '';
                            ?>
                            <div class="specialty-card" style="--specialty-color: <?php echo htmlspecialchars($specialty['color']); ?>">
                                <div class="specialty-icon">
                                    <i class="fa <?php echo htmlspecialchars($specialty['icon']); ?>"></i>
                                </div>
                                <div class="specialty-name"><?php echo htmlspecialchars($specialty['name_fr']); ?></div>
                                <?php if ($description) { ?>
                                <div class="specialty-description"><?php echo htmlspecialchars($description); ?></div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- Stats Grid (Only referrals and rating) -->
                    <?php if (isset($dietitian_stats) && $dietitian_stats && ($dietitian_stats['total_referrals'] > 0 || $dietitian_stats['average_rating'] > 0)) { ?>
                    <div class="stats-grid">
                        <?php if ($dietitian_stats['total_referrals'] > 0) { ?>
                        <div class="stat-card">
                            <i class="fa fa-share-alt"></i>
                            <div class="stat-value"><?php echo $dietitian_stats['total_referrals']; ?></div>
                            <div class="stat-label">Références</div>
                        </div>
                        <?php } ?>
                        <?php if ($dietitian_stats['average_rating'] > 0) { ?>
                        <div class="stat-card">
                            <i class="fa fa-star"></i>
                            <div class="stat-value"><?php echo number_format($dietitian_stats['average_rating'], 1); ?></div>
                            <div class="stat-label">Note moyenne</div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <!-- Certifications -->
                    <?php if (!empty($certifications)) { ?>
                    <div class="certifications-section">
                        <div class="certifications-title">
                            <i class="fa fa-certificate"></i>
                            Certifications & Diplômes
                        </div>
                        <?php foreach ($certifications as $certification) { ?>
                        <div class="certification-item">
                            <i class="fa fa-check-circle"></i>
                            <span class="certification-text"><?php echo htmlspecialchars($certification); ?></span>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <div class="dietitian-details">
                        <?php if (isset($dietitian->email)) { ?>
                        <div class="detail-item">
                            <i class="fa fa-envelope"></i>
                            <label>Email</label>
                            <div class="value"><?php echo htmlspecialchars($dietitian->email); ?></div>
                        </div>
                        <?php } ?>

                        <?php if (isset($dietitian->phonenumber)) { ?>
                        <div class="detail-item">
                            <i class="fa fa-phone"></i>
                            <label>Téléphone</label>
                            <div class="value"><?php echo htmlspecialchars($dietitian->phonenumber); ?></div>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="action-buttons">
                        <?php if (isset($dietitian->email)) { ?>
                        <a href="mailto:<?php echo htmlspecialchars($dietitian->email); ?>" class="btn-contact">
                            <i class="fa fa-envelope"></i> Contacter
                        </a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Rating Section -->
                <div class="rating-card">
                    <div class="rating-header">
                        <i class="fa fa-star"></i>
                        <h3>Évaluation</h3>
                    </div>

                    <?php if (isset($dietitian_rating) && $dietitian_rating && $dietitian_rating->total_ratings > 0) { ?>
                    <!-- Average Rating Display -->
                    <div class="average-rating">
                        <div class="rating-score">
                            <?php echo number_format($dietitian_rating->avg_overall, 1); ?> / 5
                        </div>
                        <div class="rating-stars">
                            <?php
                            $avg = round($dietitian_rating->avg_overall);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg) {
                                    echo '<i class="fa fa-star"></i>';
                                } else {
                                    echo '<i class="fa fa-star-o"></i>';
                                }
                            }
                            ?>
                        </div>
                        <div class="rating-count">
                            Basé sur <?php echo $dietitian_rating->total_ratings; ?>
                            <?php echo $dietitian_rating->total_ratings > 1 ? 'évaluations' : 'évaluation'; ?>
                        </div>

                        <!-- Criteria Details -->
                        <div class="criteria-ratings">
                            <?php
                            $criteria = [
                                'professionalism' => ['label' => 'Professionnalisme', 'icon' => 'fa-user-md'],
                                'listening' => ['label' => 'Écoute', 'icon' => 'fa-comments'],
                                'advice' => ['label' => 'Qualité des conseils', 'icon' => 'fa-lightbulb-o'],
                                'results' => ['label' => 'Résultats obtenus', 'icon' => 'fa-line-chart'],
                                'availability' => ['label' => 'Disponibilité', 'icon' => 'fa-clock-o']
                            ];

                            foreach ($criteria as $key => $info) {
                                $avg_key = 'avg_' . $key;
                                if (isset($dietitian_rating->$avg_key) && $dietitian_rating->$avg_key > 0) {
                                    $criterion_avg = $dietitian_rating->$avg_key;
                                    $criterion_rounded = round($criterion_avg);
                            ?>
                            <div class="criterion-item">
                                <div class="criterion-label">
                                    <i class="fa <?php echo $info['icon']; ?>"></i>
                                    <?php echo $info['label']; ?>
                                </div>
                                <div class="criterion-stars">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $criterion_rounded) {
                                            echo '<i class="fa fa-star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o"></i>';
                                        }
                                    }
                                    ?>
                                    <span class="criterion-score"><?php echo number_format($criterion_avg, 1); ?>/5</span>
                                </div>
                            </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if (isset($my_rating) && $my_rating) { ?>
                    <!-- Patient's Current Rating -->
                    <div class="my-rating-section" id="myRatingDisplay">
                        <div class="my-rating-header">
                            <i class="fa fa-user"></i>
                            Votre évaluation
                        </div>
                        <div class="current-rating-display">
                            <div class="rating-stars">
                                <?php
                                $my_overall = round($my_rating->overall_rating);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $my_overall) {
                                        echo '<i class="fa fa-star"></i>';
                                    } else {
                                        echo '<i class="fa fa-star-o"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="current-rating-text">
                                (<?php echo number_format($my_rating->overall_rating, 1); ?>/5)
                            </span>
                        </div>

                        <!-- My Criteria Details -->
                        <div class="criteria-ratings">
                            <?php
                            $my_criteria = [
                                'professionalism_rating' => ['label' => 'Professionnalisme', 'icon' => 'fa-user-md'],
                                'listening_rating' => ['label' => 'Écoute', 'icon' => 'fa-comments'],
                                'advice_rating' => ['label' => 'Qualité des conseils', 'icon' => 'fa-lightbulb-o'],
                                'results_rating' => ['label' => 'Résultats obtenus', 'icon' => 'fa-line-chart'],
                                'availability_rating' => ['label' => 'Disponibilité', 'icon' => 'fa-clock-o']
                            ];

                            foreach ($my_criteria as $key => $info) {
                                if (isset($my_rating->$key) && $my_rating->$key > 0) {
                                    $my_score = $my_rating->$key;
                            ?>
                            <div class="criterion-item">
                                <div class="criterion-label">
                                    <i class="fa <?php echo $info['icon']; ?>"></i>
                                    <?php echo $info['label']; ?>
                                </div>
                                <div class="criterion-stars">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $my_score) {
                                            echo '<i class="fa fa-star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o"></i>';
                                        }
                                    }
                                    ?>
                                    <span class="criterion-score"><?php echo $my_score; ?>/5</span>
                                </div>
                            </div>
                            <?php
                                }
                            }
                            ?>
                        </div>

                        <?php if (!empty($my_rating->comment)) { ?>
                        <div style="margin-top: 12px; padding: 12px; background: white; border-radius: 8px;">
                            <div style="font-size: 13px; color: #6c757d; margin-bottom: 6px;">Votre commentaire :</div>
                            <div style="color: #2c3e50; font-size: 14px; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($my_rating->comment)); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <button type="button" class="btn-edit-rating" onclick="showEditForm()">
                            <i class="fa fa-edit"></i>
                            Modifier mon évaluation
                        </button>
                    </div>

                    <!-- Edit Rating Form (Hidden by default) -->
                    <div class="my-rating-section" id="editRatingForm" style="display: none;">
                        <div class="my-rating-header">
                            <i class="fa fa-pencil"></i>
                            Modifier votre évaluation
                        </div>
                        <form id="ratingForm" action="<?php echo site_url('dietetic/portal/rate_dietitian/' . $dietitian->staffid); ?>" method="post">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <?php
                            $form_criteria = [
                                'professionalism_rating' => ['label' => 'Professionnalisme', 'icon' => 'fa-user-md'],
                                'listening_rating' => ['label' => 'Écoute', 'icon' => 'fa-comments'],
                                'advice_rating' => ['label' => 'Qualité des conseils', 'icon' => 'fa-lightbulb-o'],
                                'results_rating' => ['label' => 'Résultats obtenus', 'icon' => 'fa-line-chart'],
                                'availability_rating' => ['label' => 'Disponibilité', 'icon' => 'fa-clock-o']
                            ];

                            foreach ($form_criteria as $key => $info) {
                                $current_value = isset($my_rating->$key) ? $my_rating->$key : 0;
                            ?>
                            <div class="rating-form-group">
                                <div class="rating-form-label">
                                    <i class="fa <?php echo $info['icon']; ?>"></i>
                                    <?php echo $info['label']; ?>
                                    <span class="required">*</span>
                                </div>
                                <div class="interactive-stars" data-criterion="<?php echo $key; ?>">
                                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <i class="fa <?php echo ($i <= $current_value) ? 'fa-star selected' : 'fa-star-o'; ?>" data-rating="<?php echo $i; ?>"></i>
                                    <?php } ?>
                                </div>
                                <input type="hidden" name="<?php echo $key; ?>" class="criterion-value" value="<?php echo $current_value; ?>" required>
                            </div>
                            <?php } ?>

                            <div class="rating-form-group">
                                <div class="rating-form-label">
                                    <i class="fa fa-comment"></i>
                                    Votre commentaire
                                </div>
                                <textarea
                                    name="comment"
                                    class="rating-comment"
                                    placeholder="Partagez votre expérience avec ce diététicien (optionnel)..."
                                ><?php echo isset($my_rating->comment) ? htmlspecialchars($my_rating->comment) : ''; ?></textarea>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn-submit-rating" id="submitRatingBtn">
                                    <i class="fa fa-check"></i>
                                    Enregistrer les modifications
                                </button>
                                <button type="button" class="btn-back" onclick="cancelEdit()" style="flex: 0; min-width: auto; padding: 14px 20px;">
                                    <i class="fa fa-times"></i>
                                    Annuler
                                </button>
                            </div>
                            <div class="rating-message" id="ratingMessage"></div>
                        </form>
                    </div>
                    <?php } elseif (isset($can_rate) && $can_rate) { ?>
                    <!-- New Rating Form -->
                    <div class="my-rating-section">
                        <div class="my-rating-header">
                            <i class="fa fa-pencil"></i>
                            Évaluez votre diététicien
                        </div>
                        <form id="ratingForm" action="<?php echo site_url('dietetic/portal/rate_dietitian/' . $dietitian->staffid); ?>" method="post">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <?php
                            $form_criteria = [
                                'professionalism_rating' => ['label' => 'Professionnalisme', 'icon' => 'fa-user-md'],
                                'listening_rating' => ['label' => 'Écoute', 'icon' => 'fa-comments'],
                                'advice_rating' => ['label' => 'Qualité des conseils', 'icon' => 'fa-lightbulb-o'],
                                'results_rating' => ['label' => 'Résultats obtenus', 'icon' => 'fa-line-chart'],
                                'availability_rating' => ['label' => 'Disponibilité', 'icon' => 'fa-clock-o']
                            ];

                            foreach ($form_criteria as $key => $info) {
                            ?>
                            <div class="rating-form-group">
                                <div class="rating-form-label">
                                    <i class="fa <?php echo $info['icon']; ?>"></i>
                                    <?php echo $info['label']; ?>
                                    <span class="required">*</span>
                                </div>
                                <div class="interactive-stars" data-criterion="<?php echo $key; ?>">
                                    <i class="fa fa-star-o" data-rating="1"></i>
                                    <i class="fa fa-star-o" data-rating="2"></i>
                                    <i class="fa fa-star-o" data-rating="3"></i>
                                    <i class="fa fa-star-o" data-rating="4"></i>
                                    <i class="fa fa-star-o" data-rating="5"></i>
                                </div>
                                <input type="hidden" name="<?php echo $key; ?>" class="criterion-value" value="0" required>
                            </div>
                            <?php } ?>

                            <div class="rating-form-group">
                                <div class="rating-form-label">
                                    <i class="fa fa-comment"></i>
                                    Votre commentaire
                                </div>
                                <textarea
                                    name="comment"
                                    class="rating-comment"
                                    placeholder="Partagez votre expérience avec ce diététicien (optionnel)..."
                                ></textarea>
                            </div>

                            <button type="submit" class="btn-submit-rating" id="submitRatingBtn" disabled>
                                <i class="fa fa-check"></i>
                                Envoyer mon évaluation
                            </button>
                            <div class="rating-message" id="ratingMessage"></div>
                        </form>
                    </div>
                    <?php } else { ?>
                    <div style="text-align: center; padding: 20px; color: #6c757d; font-size: 14px;">
                        <i class="fa fa-info-circle" style="font-size: 24px; display: block; margin-bottom: 10px; color: #dee2e6;"></i>
                        <?php if (isset($can_rate) && !$can_rate) { ?>
                        Vous devez avoir au moins une consultation complétée pour évaluer votre diététicien.
                        <?php } else { ?>
                        L'évaluation n'est pas disponible pour le moment.
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="empty-state-title">Aucun diététicien</div>
                <div class="empty-state-text">
                    Aucun diététicien n'est actuellement assigné à votre programme.
                </div>
            </div>
        <?php } ?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>
    </div>

<script>
    // Rating functionality for multiple criteria
    function showEditForm() {
        document.getElementById('myRatingDisplay').style.display = 'none';
        document.getElementById('editRatingForm').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('editRatingForm').style.display = 'none';
        document.getElementById('myRatingDisplay').style.display = 'block';
    }

    // Initialize rating stars for all criteria
    var criteriaRatings = {};
    var submitBtn = document.getElementById('submitRatingBtn');

    document.querySelectorAll('.interactive-stars').forEach(function(starGroup) {
        var criterion = starGroup.getAttribute('data-criterion');
        var stars = starGroup.querySelectorAll('i');
        var input = starGroup.nextElementSibling;

        if (!criterion || !input) return;

        criteriaRatings[criterion] = parseInt(input.value) || 0;

        stars.forEach(function(star) {
            // Hover effect
            star.addEventListener('mouseenter', function() {
                var rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(stars, rating);
            });

            // Click/touch to select
            star.addEventListener('click', function() {
                var rating = parseInt(this.getAttribute('data-rating'));
                criteriaRatings[criterion] = rating;
                input.value = rating;

                // Mark as selected
                stars.forEach(function(s) {
                    s.classList.remove('selected');
                });
                for (var i = 0; i < rating; i++) {
                    stars[i].classList.add('selected');
                }

                highlightStars(stars, rating);
                checkFormValidity();

                // Haptic feedback
                if ('vibrate' in navigator) {
                    navigator.vibrate(10);
                }
            });
        });

        // Reset hover effect
        starGroup.addEventListener('mouseleave', function() {
            var currentRating = criteriaRatings[criterion] || 0;
            highlightStars(stars, currentRating);
        });
    });

    function highlightStars(stars, rating) {
        stars.forEach(function(star, index) {
            if (index < rating) {
                star.classList.remove('fa-star-o');
                star.classList.add('fa-star');
            } else {
                star.classList.remove('fa-star');
                star.classList.add('fa-star-o');
            }
        });
    }

    function checkFormValidity() {
        if (!submitBtn) return;

        var allFilled = true;
        var requiredCriteria = ['professionalism_rating', 'listening_rating', 'advice_rating', 'results_rating', 'availability_rating'];

        requiredCriteria.forEach(function(criterion) {
            if (!criteriaRatings[criterion] || criteriaRatings[criterion] === 0) {
                allFilled = false;
            }
        });

        submitBtn.disabled = !allFilled;
    }

    // Form submission with AJAX
    var ratingForm = document.getElementById('ratingForm');
    if (ratingForm) {
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var messageDiv = document.getElementById('ratingMessage');
            var originalBtnText = submitBtn.innerHTML;

            // Disable submit button during submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi en cours...';

            $.ajax({
                url: this.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    messageDiv.className = 'rating-message success';
                    messageDiv.textContent = 'Merci pour votre évaluation !';
                    messageDiv.style.display = 'block';

                    // Reload page after 2 seconds to show updated rating
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                },
                error: function() {
                    messageDiv.className = 'rating-message error';
                    messageDiv.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                    messageDiv.style.display = 'block';

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        });
    }

    // Check initial form validity
    checkFormValidity();
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>

