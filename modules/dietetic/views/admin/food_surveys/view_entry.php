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
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.entry-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: var(--shadow-lg);
}

.entry-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.entry-title h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.entry-title h1 i {
    font-size: 32px;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    margin-top: 10px;
    opacity: 0.9;
}

.breadcrumb-nav a {
    color: white;
    text-decoration: none;
    transition: var(--transition);
}

.breadcrumb-nav a:hover {
    opacity: 0.8;
}

.breadcrumb-nav i {
    font-size: 12px;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary-color);
    transition: var(--transition);
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.info-card.warning {
    border-left-color: var(--warning-color);
}

.info-card.success {
    border-left-color: var(--success-color);
}

.info-card.info {
    border-left-color: var(--info-color);
}

.info-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    color: var(--text-light);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-card-header i {
    font-size: 18px;
}

.info-card-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-dark);
}

.info-card-label {
    font-size: 13px;
    color: var(--text-light);
    margin-top: 5px;
}

/* Meals Section */
.meals-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
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
    border-bottom: 2px solid var(--border-color);
}

.section-title i {
    color: var(--primary-color);
    font-size: 26px;
}

.meals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.meal-card {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    transition: var(--transition);
}

.meal-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow);
}

.meal-card.has-data {
    border-color: var(--success-color);
}

.meal-card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.meal-card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.meal-time {
    font-size: 14px;
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 20px;
}

.meal-card-body {
    padding: 20px;
}

.meal-photo-container {
    width: 100%;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 15px;
    background: #f7fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}

.meal-photo-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.meal-photo-placeholder {
    color: var(--text-light);
    text-align: center;
    padding: 20px;
}

.meal-photo-placeholder i {
    font-size: 48px;
    opacity: 0.3;
    margin-bottom: 10px;
}

.meal-notes {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid var(--primary-color);
}

.meal-notes-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.meal-notes-text {
    color: var(--text-dark);
    font-size: 14px;
    line-height: 1.6;
}

.no-data {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-light);
}

.no-data i {
    font-size: 36px;
    opacity: 0.3;
    margin-bottom: 10px;
}

/* Beverages Section */
.beverages-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.beverages-list {
    display: grid;
    gap: 15px;
}

.beverage-item {
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 4px solid var(--secondary-color);
    transition: var(--transition);
}

.beverage-item:hover {
    background: #edf2f7;
    transform: translateX(5px);
}

.beverage-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.beverage-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--secondary-color) 0%, #e07d0f 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.beverage-details h4 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 600;
}

.beverage-details p {
    margin: 0;
    color: var(--text-light);
    font-size: 14px;
}

.beverage-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.beverage-quantity-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--primary-color);
}

.beverage-quantity-unit {
    font-size: 14px;
    color: var(--text-light);
}

/* Recommendations Section */
.recommendations-section {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.add-recommendation-form {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    border: 2px dashed var(--border-color);
}

.add-recommendation-form textarea {
    width: 100%;
    min-height: 120px;
    padding: 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    transition: var(--transition);
}

.add-recommendation-form textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.add-recommendation-form button {
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 10px;
}

.add-recommendation-form button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.recommendations-list {
    display: grid;
    gap: 20px;
}

.recommendation-card {
    background: #f7fafc;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.recommendation-card:hover {
    box-shadow: var(--shadow);
}

.recommendation-header {
    padding: 20px;
    background: white;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.recommendation-author {
    display: flex;
    align-items: center;
    gap: 15px;
}

.recommendation-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
    overflow: hidden;
}

.recommendation-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recommendation-author-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 600;
}

.recommendation-author-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 13px;
}

.recommendation-actions {
    display: flex;
    gap: 10px;
}

.recommendation-actions button {
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-light);
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
}

.recommendation-actions button:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.recommendation-actions button.delete:hover {
    border-color: var(--danger-color);
    color: var(--danger-color);
}

.recommendation-body {
    padding: 20px;
}

.recommendation-text {
    color: var(--text-dark);
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 20px;
}

.recommendation-comments {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.comments-header {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.comment-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 3px solid var(--secondary-color);
}

.comment-item:last-child {
    margin-bottom: 0;
}

.comment-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 13px;
    color: var(--text-light);
}

.comment-text {
    color: var(--text-dark);
    font-size: 14px;
    line-height: 1.6;
}

.no-recommendations {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-light);
}

.no-recommendations i {
    font-size: 64px;
    opacity: 0.2;
    margin-bottom: 20px;
}

.no-recommendations p {
    font-size: 16px;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.status-badge.submitted {
    background: rgba(72, 187, 120, 0.1);
    color: var(--success-color);
}

.status-badge.pending {
    background: rgba(237, 137, 54, 0.1);
    color: var(--warning-color);
}

/* Responsive */
@media (max-width: 768px) {
    .entry-view-container {
        padding: 15px;
    }

    .entry-header {
        padding: 20px;
    }

    .entry-title h1 {
        font-size: 22px;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .meals-grid {
        grid-template-columns: 1fr;
    }

    .meals-section,
    .beverages-section,
    .recommendations-section {
        padding: 20px;
    }

    .beverage-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .recommendation-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .entry-title {
        flex-direction: column;
        align-items: flex-start;
    }

    .meal-photo-container {
        height: 150px;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="entry-view-container">
            <!-- Header -->
            <div class="entry-header">
                <div class="entry-title">
                    <h1>
                        <i class="fa fa-calendar-check-o"></i>
                        Entrée du <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?>
                    </h1>
                    <?php if ($entry->submitted_at): ?>
                        <span class="status-badge submitted">
                            <i class="fa fa-check-circle"></i>
                            Soumise le <?php echo date('d/m/Y à H:i', strtotime($entry->submitted_at)); ?>
                        </span>
                    <?php else: ?>
                        <span class="status-badge pending">
                            <i class="fa fa-clock-o"></i>
                            En attente de soumission
                        </span>
                    <?php endif; ?>
                </div>
                <div class="breadcrumb-nav">
                    <a href="<?php echo admin_url('dietetic/food_surveys'); ?>">
                        <i class="fa fa-list"></i> Enquêtes
                    </a>
                    <i class="fa fa-angle-right"></i>
                    <a href="<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>">
                        <?php echo htmlspecialchars($survey->survey_name); ?>
                    </a>
                    <i class="fa fa-angle-right"></i>
                    <span>Entrée du <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?></span>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fa fa-user"></i>
                        Patient
                    </div>
                    <div class="info-card-value"><?php echo htmlspecialchars($survey->patient_name); ?></div>
                </div>

                <div class="info-card warning">
                    <div class="info-card-header">
                        <i class="fa fa-tint"></i>
                        Eau consommée
                    </div>
                    <div class="info-card-value">
                        <?php echo $entry->water_quantity_ml ? number_format($entry->water_quantity_ml) : '0'; ?> ml
                    </div>
                    <div class="info-card-label">Objectif recommandé: 2000 ml/jour</div>
                </div>

                <div class="info-card info">
                    <div class="info-card-header">
                        <i class="fa fa-coffee"></i>
                        Boissons
                    </div>
                    <div class="info-card-value"><?php echo count($beverages); ?></div>
                    <div class="info-card-label">Boissons enregistrées</div>
                </div>

                <div class="info-card success">
                    <div class="info-card-header">
                        <i class="fa fa-comments"></i>
                        Recommandations
                    </div>
                    <div class="info-card-value"><?php echo count($recommendations); ?></div>
                    <div class="info-card-label">Par le diététicien</div>
                </div>
            </div>

            <!-- Meals Section -->
            <div class="meals-section">
                <div class="section-title">
                    <i class="fa fa-cutlery"></i>
                    Repas de la journée
                </div>

                <div class="meals-grid">
                    <!-- Breakfast -->
                    <div class="meal-card <?php echo $entry->breakfast_photo ? 'has-data' : ''; ?>">
                        <div class="meal-card-header">
                            <h3>
                                <i class="fa fa-coffee"></i>
                                Petit-déjeuner
                            </h3>
                            <?php if ($entry->breakfast_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->breakfast_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <div class="meal-photo-container">
                                <?php if ($entry->breakfast_photo): ?>
                                    <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->breakfast_photo); ?>" alt="Petit-déjeuner">
                                <?php else: ?>
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($entry->breakfast_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label">Notes</div>
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
                            </h3>
                            <?php if ($entry->lunch_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->lunch_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <div class="meal-photo-container">
                                <?php if ($entry->lunch_photo): ?>
                                    <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->lunch_photo); ?>" alt="Déjeuner">
                                <?php else: ?>
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($entry->lunch_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label">Notes</div>
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
                            </h3>
                            <?php if ($entry->dinner_time): ?>
                                <span class="meal-time">
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($entry->dinner_time)); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="meal-card-body">
                            <div class="meal-photo-container">
                                <?php if ($entry->dinner_photo): ?>
                                    <img src="<?php echo base_url('uploads/dietetic/food_surveys/' . $entry->dinner_photo); ?>" alt="Dîner">
                                <?php else: ?>
                                    <div class="meal-photo-placeholder">
                                        <i class="fa fa-image"></i>
                                        <p>Aucune photo</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($entry->dinner_notes): ?>
                                <div class="meal-notes">
                                    <div class="meal-notes-label">Notes</div>
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
            <?php if (count($beverages) > 0): ?>
            <div class="beverages-section">
                <div class="section-title">
                    <i class="fa fa-coffee"></i>
                    Boissons consommées
                </div>

                <div class="beverages-list">
                    <?php foreach ($beverages as $beverage): ?>
                    <div class="beverage-item">
                        <div class="beverage-info">
                            <div class="beverage-icon">
                                <i class="fa fa-tint"></i>
                            </div>
                            <div class="beverage-details">
                                <h4><?php echo htmlspecialchars($beverage->beverage_name); ?></h4>
                                <p>
                                    <i class="fa fa-clock-o"></i>
                                    <?php echo date('H:i', strtotime($beverage->consumption_time)); ?>
                                    <?php if ($beverage->notes): ?>
                                        - <?php echo htmlspecialchars($beverage->notes); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
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
                <div class="section-title">
                    <i class="fa fa-lightbulb-o"></i>
                    Recommandations du diététicien
                </div>

                <?php if (dietetic_has_permission('edit')): ?>
                <!-- Add Recommendation Form -->
                <div class="add-recommendation-form">
                    <form id="addRecommendationForm">
                        <input type="hidden" name="entry_id" value="<?php echo $entry->id; ?>">
                        <div class="form-group">
                            <label for="recommendation_text">Nouvelle recommandation</label>
                            <textarea name="recommendation_text" id="recommendation_text"
                                      placeholder="Entrez votre recommandation pour le patient..." required></textarea>
                        </div>
                        <button type="submit">
                            <i class="fa fa-plus-circle"></i>
                            Ajouter une recommandation
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Recommendations List -->
                <div class="recommendations-list">
                    <?php if (count($recommendations) > 0): ?>
                        <?php foreach ($recommendations as $recommendation): ?>
                        <div class="recommendation-card" data-id="<?php echo $recommendation->id; ?>">
                            <div class="recommendation-header">
                                <div class="recommendation-author">
                                    <div class="recommendation-avatar">
                                        <?php if ($recommendation->profile_image): ?>
                                            <img src="<?php echo base_url('uploads/staff_profile_images/' . $recommendation->profile_image); ?>" alt="">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($recommendation->dietitian_name, 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="recommendation-author-info">
                                        <h4><?php echo htmlspecialchars($recommendation->dietitian_name); ?></h4>
                                        <p>
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo date('d/m/Y à H:i', strtotime($recommendation->created_at)); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if (dietetic_has_permission('delete')): ?>
                                <div class="recommendation-actions">
                                    <button class="delete" onclick="deleteRecommendation(<?php echo $recommendation->id; ?>)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="recommendation-body">
                                <div class="recommendation-text">
                                    <?php echo nl2br(htmlspecialchars($recommendation->recommendation_text)); ?>
                                </div>

                                <?php if (count($recommendation->comments) > 0): ?>
                                <div class="recommendation-comments">
                                    <div class="comments-header">
                                        <i class="fa fa-comment"></i>
                                        Commentaires du patient (<?php echo count($recommendation->comments); ?>)
                                    </div>
                                    <?php foreach ($recommendation->comments as $comment): ?>
                                    <div class="comment-item">
                                        <div class="comment-meta">
                                            <i class="fa fa-user-circle"></i>
                                            Patient
                                            <span>•</span>
                                            <?php echo date('d/m/Y à H:i', strtotime($comment->created_at)); ?>
                                        </div>
                                        <div class="comment-text">
                                            <?php echo nl2br(htmlspecialchars($comment->comment_text)); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-recommendations">
                            <i class="fa fa-lightbulb-o"></i>
                            <p>Aucune recommandation pour cette entrée</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
(function() {
    'use strict';

    // Add recommendation form submission
    $('#addRecommendationForm').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var $textarea = $form.find('textarea');

        // Validation
        if (!$textarea.val().trim()) {
            alert_float('warning', 'Veuillez entrer une recommandation');
            return;
        }

        // Disable button
        $button.prop('disabled', true);
        $button.html('<i class="fa fa-spinner fa-spin"></i> Ajout en cours...');

        $.ajax({
            url: admin_url + 'dietetic/food_surveys/add_recommendation',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    // Reload page to show new recommendation
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert_float('danger', response.message || 'Une erreur est survenue');
                    // Re-enable button
                    $button.prop('disabled', false);
                    $button.html('<i class="fa fa-plus-circle"></i> Ajouter une recommandation');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert_float('danger', 'Une erreur est survenue. Veuillez réessayer.');
                // Re-enable button
                $button.prop('disabled', false);
                $button.html('<i class="fa fa-plus-circle"></i> Ajouter une recommandation');
            }
        });
    });

    // Delete recommendation function
    window.deleteRecommendation = function(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette recommandation ?')) {
            return;
        }

        $.ajax({
            url: admin_url + 'dietetic/food_surveys/delete_recommendation/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    // Remove recommendation card
                    $('.recommendation-card[data-id="' + id + '"]').fadeOut(300, function() {
                        $(this).remove();
                        // Check if no recommendations left
                        if ($('.recommendation-card').length === 0) {
                            $('.recommendations-list').html('<div class="no-recommendations"><i class="fa fa-lightbulb-o"></i><p>Aucune recommandation pour cette entrée</p></div>');
                        }
                    });
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert_float('danger', 'Une erreur est survenue. Veuillez réessayer.');
            }
        });
    };
})();
</script>
