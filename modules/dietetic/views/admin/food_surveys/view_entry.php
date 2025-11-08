<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

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
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.entry-view-container {
    padding: 20px;
    max-width: 1600px;
    margin: 0 auto;
}

/* Header */
.entry-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

.entry-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

.entry-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    position: relative;
    z-index: 1;
}

.entry-title h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
}

.entry-title h1 i {
    font-size: 36px;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.9;
}

.breadcrumb-nav a {
    color: white;
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 5px;
}

.breadcrumb-nav a:hover {
    opacity: 0.8;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.action-btn {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 12px 24px;
    border-radius: 10px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.action-btn i {
    font-size: 16px;
}

/* Dashboard Stats */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

.stat-card.warning .stat-card-icon {
    background: linear-gradient(135deg, var(--warning-color) 0%, #e07d0f 100%);
    box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
}

.stat-card.success .stat-card-icon {
    background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
    box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

.stat-card.info .stat-card-icon {
    background: linear-gradient(135deg, var(--info-color) 0%, #3182ce 100%);
    box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
}

.stat-card-content h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card-value {
    font-size: 36px;
    font-weight: 800;
    color: var(--text-dark);
    margin: 10px 0 5px;
    line-height: 1;
}

.stat-card-label {
    font-size: 13px;
    color: var(--text-light);
}

.stat-progress {
    margin-top: 15px;
    background: #f7fafc;
    height: 8px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 10px;
    transition: width 1s ease;
    position: relative;
    overflow: hidden;
}

.stat-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.3),
        transparent
    );
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Meals Day View */
.meals-day-section {
    margin-bottom: 30px;
}

.section-header {
    background: white;
    padding: 25px 30px;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid var(--primary-color);
    box-shadow: var(--shadow);
}

.section-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-header h2 i {
    color: var(--primary-color);
    font-size: 28px;
}

.section-subheader {
    font-size: 14px;
    color: var(--text-light);
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.meals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    padding: 30px;
    background: #f8fafb;
    border-radius: 0 0 16px 16px;
}

.meal-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
    border: 2px solid transparent;
    position: relative;
}

.meal-card.has-data {
    border-color: var(--success-color);
}

.meal-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.meal-card-header {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid var(--border-color);
}

.meal-card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.meal-card-header h3 i {
    font-size: 22px;
    color: var(--primary-color);
}

.meal-time {
    background: var(--primary-color);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.meal-recommendation-badge {
    background: linear-gradient(135deg, var(--warning-color), var(--secondary-color));
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-left: 10px;
}

.meal-card-body {
    padding: 20px;
}

.meal-photo-container {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 15px;
    background: #f7fafc;
    position: relative;
}

.meal-photo-container img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
}

.meal-photo-container.clickable {
    cursor: pointer;
}

.meal-photo-container.clickable::after {
    content: '🔍 Cliquer pour agrandir';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white;
    padding: 15px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.meal-photo-container.clickable:hover::after {
    opacity: 1;
}

.meal-photo-placeholder {
    color: var(--text-light);
    text-align: center;
    padding: 60px 20px;
}

.meal-photo-placeholder i {
    font-size: 56px;
    opacity: 0.3;
    margin-bottom: 15px;
    display: block;
}

.meal-notes {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 18px;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
}

.meal-notes-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.meal-notes-text {
    color: var(--text-dark);
    font-size: 14px;
    line-height: 1.7;
}

.no-data {
    text-align: center;
    padding: 50px 20px;
    color: var(--text-light);
}

.no-data i {
    font-size: 42px;
    opacity: 0.3;
    margin-bottom: 12px;
    display: block;
}

/* Beverages Section */
.beverages-section {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.section-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 15px;
    border-bottom: 3px solid var(--primary-color);
}

.section-title i {
    color: var(--primary-color);
    font-size: 26px;
}

.beverages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.beverage-card {
    background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 18px;
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.beverage-card:hover {
    border-color: var(--secondary-color);
    transform: translateX(5px);
    box-shadow: var(--shadow);
}

.beverage-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--secondary-color) 0%, #e07d0f 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(243, 145, 29, 0.3);
    flex-shrink: 0;
}

.beverage-details {
    flex: 1;
}

.beverage-details h4 {
    margin: 0 0 6px 0;
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 700;
}

.beverage-details p {
    margin: 0;
    color: var(--text-light);
    font-size: 13px;
}

.beverage-quantity {
    background: white;
    padding: 12px 18px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    text-align: center;
}

.beverage-quantity-value {
    font-size: 22px;
    font-weight: 800;
    color: var(--primary-color);
    display: block;
}

.beverage-quantity-unit {
    font-size: 12px;
    color: var(--text-light);
    text-transform: uppercase;
}

/* Recommendations Section */
.recommendations-section {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.add-recommendation-form {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 30px;
    border-radius: 14px;
    margin-bottom: 30px;
    border: 2px dashed var(--border-color);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 14px;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    transition: var(--transition);
}

.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.form-group textarea {
    min-height: 120px;
    resize: vertical;
}

/* Meal Type Radio Buttons */
.meal-type-radio-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 10px;
}

.meal-radio-option {
    position: relative;
    cursor: pointer;
    margin: 0;
}

.meal-radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.meal-radio-label {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 600;
    color: var(--text-dark);
}

.meal-radio-label i {
    font-size: 20px;
    color: var(--text-light);
    transition: color 0.3s ease;
}

.meal-radio-label .meal-name {
    font-size: 14px;
}

.meal-radio-option:hover .meal-radio-label {
    border-color: var(--primary-color);
    background: rgba(1, 128, 123, 0.05);
}

.meal-radio-option input[type="radio"]:checked + .meal-radio-label {
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.25);
}

.meal-radio-option input[type="radio"]:checked + .meal-radio-label i {
    color: white;
}

@media (max-width: 768px) {
    .meal-type-radio-group {
        grid-template-columns: 1fr;
    }
}

.btn-submit {
    background: linear-gradient(135deg, var(--primary-color), #026660);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.4);
}

.recommendations-list {
    display: grid;
    gap: 20px;
}

.recommendation-card {
    background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
    border-radius: 14px;
    overflow: hidden;
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.recommendation-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.recommendation-header {
    padding: 25px;
    background: white;
    border-bottom: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.recommendation-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.recommendation-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 700;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

.recommendation-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recommendation-author-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
    font-size: 17px;
    font-weight: 700;
}

.recommendation-author-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 13px;
}

.recommendation-meal-badge {
    background: linear-gradient(135deg, var(--secondary-color), #e07d0f);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.recommendation-content {
    padding: 25px;
    color: var(--text-dark);
    font-size: 15px;
    line-height: 1.8;
}

.recommendation-meta {
    padding: 15px 25px;
    background: #f7fafc;
    border-top: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 13px;
    color: var(--text-light);
}

.recommendation-meta i {
    margin-right: 5px;
}

/* Responsive */
@media (max-width: 1200px) {
    .meals-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    .dashboard-stats {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .entry-view-container {
        padding: 15px;
    }

    .entry-header {
        padding: 20px;
    }

    .entry-title h1 {
        font-size: 24px;
    }

    .header-actions {
        width: 100%;
    }

    .action-btn {
        flex: 1;
        justify-content: center;
    }

    .meals-grid {
        grid-template-columns: 1fr;
        padding: 20px;
    }

    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .beverages-grid {
        grid-template-columns: 1fr;
    }
}

@media print {
    .entry-header,
    .header-actions,
    .add-recommendation-form {
        display: none;
    }

    .entry-view-container {
        padding: 0;
    }

    .meal-card,
    .stat-card,
    .beverages-section,
    .recommendations-section {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="entry-view-container">
            <!-- Header -->
            <div class="entry-header">
                <div class="entry-title">
                    <div>
                        <h1>
                            <i class="fa fa-calendar-check-o"></i>
                            Entrée du <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?>
                        </h1>
                        <div class="breadcrumb-nav">
                            <a href="<?php echo admin_url('dietetic/food_surveys'); ?>">
                                <i class="fa fa-list"></i> Enquêtes
                            </a>
                            <i class="fa fa-angle-right"></i>
                            <a href="<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>">
                                <?php echo htmlspecialchars($survey->survey_name); ?>
                            </a>
                            <i class="fa fa-angle-right"></i>
                            <span><?php echo htmlspecialchars($patient->client->company); ?></span>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>" class="action-btn">
                            <i class="fa fa-arrow-left"></i>
                            Retour
                        </a>
                        <a href="javascript:window.print();" class="action-btn">
                            <i class="fa fa-print"></i>
                            Imprimer
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <?php
                // Calculate meal completion
                $meals_logged = 0;
                $total_meals = 3;
                if ($entry->breakfast_photo || $entry->breakfast_notes) $meals_logged++;
                if ($entry->lunch_photo || $entry->lunch_notes) $meals_logged++;
                if ($entry->dinner_photo || $entry->dinner_notes) $meals_logged++;
                $completion_percentage = round(($meals_logged / $total_meals) * 100);
                ?>

                <div class="stat-card <?php echo $meals_logged == $total_meals ? 'success' : ($meals_logged > 0 ? 'warning' : ''); ?>">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fa fa-cutlery"></i>
                        </div>
                    </div>
                    <div class="stat-card-content">
                        <h3>Repas complétés</h3>
                        <div class="stat-card-value"><?php echo $meals_logged; ?> <span style="font-size: 20px; color: var(--text-light);">/ <?php echo $total_meals; ?></span></div>
                        <div class="stat-card-label"><?php echo $completion_percentage; ?>% de complétion</div>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: <?php echo $completion_percentage; ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fa fa-tint"></i>
                        </div>
                    </div>
                    <div class="stat-card-content">
                        <h3>Eau consommée</h3>
                        <div class="stat-card-value">
                            <?php echo $entry->water_quantity_ml ? number_format($entry->water_quantity_ml) : '0'; ?> <span style="font-size: 18px;">ml</span>
                        </div>
                        <div class="stat-card-label">Objectif: 2000 ml/jour</div>
                        <?php
                        $water_percentage = $entry->water_quantity_ml ? min(($entry->water_quantity_ml / 2000) * 100, 100) : 0;
                        ?>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: <?php echo $water_percentage; ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fa fa-coffee"></i>
                        </div>
                    </div>
                    <div class="stat-card-content">
                        <h3>Boissons</h3>
                        <div class="stat-card-value"><?php echo count($beverages); ?></div>
                        <div class="stat-card-label">Boissons enregistrées</div>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                    </div>
                    <div class="stat-card-content">
                        <h3>Recommandations</h3>
                        <div class="stat-card-value">
                            <?php
                            $total_recommendations = array_sum(array_map('count', $recommendations_by_meal));
                            echo $total_recommendations;
                            ?>
                        </div>
                        <div class="stat-card-label">Par le diététicien</div>
                    </div>
                </div>
            </div>

            <!-- Meals Day View -->
            <div class="meals-day-section">
                <div class="section-header">
                    <h2>
                        <i class="fa fa-sun-o"></i>
                        Vue Journalière
                    </h2>
                    <div class="section-subheader">
                        <i class="fa fa-calendar"></i>
                        <?php echo strftime('%A %d %B %Y', strtotime($entry->entry_date)); ?>
                    </div>
                </div>

                <div class="meals-grid">
                    <!-- Breakfast -->
                    <div class="meal-card <?php echo $entry->breakfast_photo ? 'has-data' : ''; ?>">
                        <div class="meal-card-header">
                            <h3>
                                <i class="fa fa-coffee"></i>
                                Petit-déjeuner
                                <?php if (count($recommendations_by_meal['breakfast']) > 0): ?>
                                    <span class="meal-recommendation-badge">
                                        <i class="fa fa-lightbulb-o"></i> <?php echo count($recommendations_by_meal['breakfast']); ?>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <?php if ($entry->breakfast_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->breakfast_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <?php if ($entry->breakfast_photo): ?>
                                <a href="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->breakfast_photo); ?>" target="_blank">
                                    <div class="meal-photo-container clickable">
                                        <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->breakfast_photo); ?>" alt="Petit-déjeuner">
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="meal-photo-container">
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($entry->breakfast_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label"><i class="fa fa-sticky-note-o"></i> Notes</div>
                                    <div class="meal-notes-text"><?php echo nl2br(htmlspecialchars($entry->breakfast_notes)); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!$entry->breakfast_photo && !$entry->breakfast_notes): ?>
                                <div class="no-data">
                                    <i class="fa fa-info-circle"></i>
                                    <p>Aucune donnée enregistrée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Lunch -->
                    <div class="meal-card <?php echo $entry->lunch_photo ? 'has-data' : ''; ?>">
                        <div class="meal-card-header">
                            <h3>
                                <i class="fa fa-sun-o"></i>
                                Déjeuner
                                <?php if (count($recommendations_by_meal['lunch']) > 0): ?>
                                    <span class="meal-recommendation-badge">
                                        <i class="fa fa-lightbulb-o"></i> <?php echo count($recommendations_by_meal['lunch']); ?>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <?php if ($entry->lunch_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->lunch_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <?php if ($entry->lunch_photo): ?>
                                <a href="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->lunch_photo); ?>" target="_blank">
                                    <div class="meal-photo-container clickable">
                                        <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->lunch_photo); ?>" alt="Déjeuner">
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="meal-photo-container">
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($entry->lunch_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label"><i class="fa fa-sticky-note-o"></i> Notes</div>
                                    <div class="meal-notes-text"><?php echo nl2br(htmlspecialchars($entry->lunch_notes)); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!$entry->lunch_photo && !$entry->lunch_notes): ?>
                                <div class="no-data">
                                    <i class="fa fa-info-circle"></i>
                                    <p>Aucune donnée enregistrée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dinner -->
                    <div class="meal-card <?php echo $entry->dinner_photo ? 'has-data' : ''; ?>">
                        <div class="meal-card-header">
                            <h3>
                                <i class="fa fa-moon-o"></i>
                                Dîner
                                <?php if (count($recommendations_by_meal['dinner']) > 0): ?>
                                    <span class="meal-recommendation-badge">
                                        <i class="fa fa-lightbulb-o"></i> <?php echo count($recommendations_by_meal['dinner']); ?>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <?php if ($entry->dinner_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->dinner_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <?php if ($entry->dinner_photo): ?>
                                <a href="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->dinner_photo); ?>" target="_blank">
                                    <div class="meal-photo-container clickable">
                                        <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->dinner_photo); ?>" alt="Dîner">
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="meal-photo-container">
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($entry->dinner_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label"><i class="fa fa-sticky-note-o"></i> Notes</div>
                                    <div class="meal-notes-text"><?php echo nl2br(htmlspecialchars($entry->dinner_notes)); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if (!$entry->dinner_photo && !$entry->dinner_notes): ?>
                                <div class="no-data">
                                    <i class="fa fa-info-circle"></i>
                                    <p>Aucune donnée enregistrée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Beverages Section -->
            <?php if (!empty($beverages)): ?>
                <div class="beverages-section">
                    <h2 class="section-title">
                        <i class="fa fa-glass"></i>
                        Boissons de la journée
                    </h2>
                    <div class="beverages-grid">
                        <?php foreach ($beverages as $beverage): ?>
                            <div class="beverage-card">
                                <div class="beverage-icon">
                                    <i class="fa fa-coffee"></i>
                                </div>
                                <div class="beverage-details">
                                    <h4><?php echo htmlspecialchars($beverage->beverage_name); ?></h4>
                                    <p><?php echo $beverage->time_consumed ? date('H:i', strtotime($beverage->time_consumed)) : 'Heure non spécifiée'; ?></p>
                                </div>
                                <div class="beverage-quantity">
                                    <span class="beverage-quantity-value"><?php echo number_format($beverage->quantity_ml); ?></span>
                                    <span class="beverage-quantity-unit">ml</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recommendations Section -->
            <div class="recommendations-section">
                <h2 class="section-title">
                    <i class="fa fa-lightbulb-o"></i>
                    Recommandations du diététicien
                </h2>

                <!-- Add Recommendation Form -->
                <div class="add-recommendation-form">
                    <h3 style="margin-top: 0; color: var(--text-dark);">
                        <i class="fa fa-plus-circle"></i>
                        Ajouter une nouvelle recommandation
                    </h3>
                    <form action="<?php echo admin_url('dietetic/food_surveys/add_recommendation'); ?>" method="POST">
                        <?php echo form_hidden('entry_id', $entry->id); ?>
                        <?php echo form_hidden('survey_id', $survey->id); ?>
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label>
                                <i class="fa fa-cutlery"></i>
                                Repas concerné
                            </label>
                            <div class="meal-type-radio-group">
                                <label class="meal-radio-option">
                                    <input type="radio" name="meal_type" value="breakfast" required>
                                    <span class="meal-radio-label">
                                        <i class="fa fa-coffee"></i>
                                        <span class="meal-name">Petit-déjeuner</span>
                                    </span>
                                </label>
                                <label class="meal-radio-option">
                                    <input type="radio" name="meal_type" value="lunch" required>
                                    <span class="meal-radio-label">
                                        <i class="fa fa-sun-o"></i>
                                        <span class="meal-name">Déjeuner</span>
                                    </span>
                                </label>
                                <label class="meal-radio-option">
                                    <input type="radio" name="meal_type" value="dinner" required>
                                    <span class="meal-radio-label">
                                        <i class="fa fa-moon-o"></i>
                                        <span class="meal-name">Dîner</span>
                                    </span>
                                </label>
                                <label class="meal-radio-option">
                                    <input type="radio" name="meal_type" value="global" required>
                                    <span class="meal-radio-label">
                                        <i class="fa fa-star"></i>
                                        <span class="meal-name">Recommandation générale</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="recommendation_text">
                                <i class="fa fa-comment"></i>
                                Votre recommandation
                            </label>
                            <textarea name="recommendation_text" id="recommendation_text"
                                      placeholder="Entrez votre recommandation détaillée..."
                                      required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fa fa-paper-plane"></i>
                            Envoyer la recommandation
                        </button>
                    </form>
                </div>

                <!-- Recommendations List -->
                <?php if (!empty($recommendations_by_meal)): ?>
                    <div class="recommendations-list">
                        <?php
                        $meal_labels = [
                            'breakfast' => ['Petit-déjeuner', 'fa-coffee'],
                            'lunch' => ['Déjeuner', 'fa-sun-o'],
                            'dinner' => ['Dîner', 'fa-moon-o'],
                            'global' => ['Recommandation générale', 'fa-star']
                        ];

                        foreach ($recommendations_by_meal as $meal_type => $recommendations):
                            if (!empty($recommendations)):
                        ?>
                            <?php foreach ($recommendations as $rec): ?>
                                <div class="recommendation-card">
                                    <div class="recommendation-header">
                                        <div class="recommendation-author">
                                            <div class="recommendation-avatar">
                                                <?php
                                                $staff = $this->db->get_where('staff', ['staffid' => $rec->staff_id])->row();
                                                if ($staff && $staff->profile_image) {
                                                    echo '<img src="' . base_url('uploads/staff_profile_images/' . $staff->staffid . '/' . $staff->profile_image) . '" alt="' . $staff->firstname . '">';
                                                } else {
                                                    echo strtoupper(substr($staff->firstname, 0, 1));
                                                }
                                                ?>
                                            </div>
                                            <div class="recommendation-author-info">
                                                <h4>
                                                    <?php echo $staff->firstname . ' ' . $staff->lastname; ?>
                                                </h4>
                                                <p>Diététicien</p>
                                            </div>
                                        </div>
                                        <span class="recommendation-meal-badge">
                                            <i class="fa <?php echo $meal_labels[$meal_type][1]; ?>"></i>
                                            <?php echo $meal_labels[$meal_type][0]; ?>
                                        </span>
                                    </div>
                                    <div class="recommendation-content">
                                        <?php echo nl2br(htmlspecialchars($rec->recommendation_text)); ?>
                                    </div>
                                    <div class="recommendation-meta">
                                        <span>
                                            <i class="fa fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($rec->created_at)); ?>
                                        </span>
                                        <span>
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo date('H:i', strtotime($rec->created_at)); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                <?php else: ?>
                    <div class="no-data" style="background: #f7fafc; padding: 60px; border-radius: 12px;">
                        <i class="fa fa-comments-o"></i>
                        <p style="font-size: 16px; margin-top: 15px;">Aucune recommandation pour cette entrée</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
