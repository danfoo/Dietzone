<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Soumission Quotidienne'; ?> - <?php echo get_option('companyname'); ?></title>

    <!-- Perfex CSS -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">

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
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .container-fluid {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Back link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 10px 15px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .back-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(-3px);
    }

    /* Form sections */
    .form-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-color);
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 24px;
    }

    /* Meal card */
    .meal-card {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        transition: var(--transition);
    }

    .meal-card:last-child {
        margin-bottom: 0;
    }

    .meal-card.has-photo {
        border-color: var(--success-color);
        border-style: solid;
        background: rgba(72, 187, 120, 0.05);
    }

    .meal-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .meal-card-title i {
        color: var(--secondary-color);
    }

    /* Photo upload */
    .photo-upload-area {
        border: 3px dashed var(--border-color);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        background: #f7fafc;
        position: relative;
        margin-bottom: 20px;
    }

    .photo-upload-area:hover {
        border-color: var(--primary-color);
        background: rgba(1, 128, 123, 0.05);
    }

    .photo-upload-area.dragging {
        border-color: var(--secondary-color);
        background: rgba(243, 145, 29, 0.1);
    }

    .photo-upload-icon {
        font-size: 48px;
        color: var(--text-light);
        margin-bottom: 15px;
    }

    .photo-upload-text {
        color: var(--text-dark);
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .photo-upload-hint {
        color: var(--text-light);
        font-size: 14px;
    }

    input[type="file"] {
        display: none;
    }

    .photo-preview {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .photo-preview img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 12px;
    }

    .photo-preview-remove {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(245, 101, 101, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        font-size: 18px;
    }

    .photo-preview-remove:hover {
        background: var(--danger-color);
        transform: scale(1.1);
    }

    /* Form groups */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 15px;
        font-family: inherit;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    }

    textarea.form-control {
        min-height: 80px;
        resize: vertical;
    }

    /* Beverages section */
    .beverage-list {
        margin-top: 20px;
    }

    .beverage-item {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 15px;
        margin-bottom: 15px;
        padding: 15px;
        background: #f7fafc;
        border-radius: 8px;
        align-items: end;
    }

    .beverage-item button {
        background: var(--danger-color);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 15px;
        cursor: pointer;
        transition: var(--transition);
    }

    .beverage-item button:hover {
        background: #e53e3e;
    }

    .add-beverage-btn {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 15px;
    }

    .add-beverage-btn:hover {
        background: #e07d0f;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Submit button */
    .submit-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: var(--shadow);
        text-align: center;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-content {
        background: white;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: var(--shadow-lg);
    }

    .spinner {
        border: 4px solid var(--border-color);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Alert messages */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: rgba(72, 187, 120, 0.1);
        color: var(--success-color);
        border-left: 4px solid var(--success-color);
    }

    .alert-danger {
        background: rgba(245, 101, 101, 0.1);
        color: var(--danger-color);
        border-left: 4px solid var(--danger-color);
    }

    .alert-info {
        background: rgba(66, 153, 225, 0.1);
        color: var(--info-color);
        border-left: 4px solid var(--info-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }

        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .form-section {
            padding: 20px;
        }

        .beverage-item {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Back link -->
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="back-link">
            <i class="fa fa-arrow-left"></i>
            Retour aux enquêtes
        </a>

        <!-- Page header -->
        <div class="page-header">
            <h1>
                <i class="fa fa-camera"></i>
                <?php echo htmlspecialchars($survey->survey_name); ?>
            </h1>
            <p>Soumission quotidienne du <?php echo date('d/m/Y'); ?></p>
        </div>

        <!-- Alert area -->
        <div id="alertArea"></div>

        <form id="dailyEntryForm">
            <input type="hidden" name="survey_id" value="<?php echo $survey->id; ?>">
            <input type="hidden" name="entry_date" value="<?php echo date('Y-m-d'); ?>">

            <!-- Meals Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-cutlery"></i>
                    Repas de la journée
                </div>

                <!-- Breakfast -->
                <div class="meal-card" id="breakfastCard">
                    <div class="meal-card-title">
                        <i class="fa fa-coffee"></i>
                        Petit-déjeuner
                    </div>

                    <div class="photo-upload-area" data-meal="breakfast">
                        <input type="file" id="breakfastPhoto" accept="image/*" data-meal="breakfast">
                        <input type="hidden" name="breakfast_photo" id="breakfast_photo_value">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="breakfastPreview" class="photo-preview" style="display: none;">
                        <img src="" alt="Petit-déjeuner">
                        <button type="button" class="photo-preview-remove" data-meal="breakfast">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="breakfast_time">Heure du repas</label>
                        <input type="time" class="form-control" name="breakfast_time" id="breakfast_time"
                               value="<?php echo $today_entry && $today_entry->breakfast_time ? date('H:i', strtotime($today_entry->breakfast_time)) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="breakfast_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="breakfast_notes" id="breakfast_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->breakfast_notes) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Lunch -->
                <div class="meal-card" id="lunchCard">
                    <div class="meal-card-title">
                        <i class="fa fa-sun-o"></i>
                        Déjeuner
                    </div>

                    <div class="photo-upload-area" data-meal="lunch">
                        <input type="file" id="lunchPhoto" accept="image/*" data-meal="lunch">
                        <input type="hidden" name="lunch_photo" id="lunch_photo_value">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="lunchPreview" class="photo-preview" style="display: none;">
                        <img src="" alt="Déjeuner">
                        <button type="button" class="photo-preview-remove" data-meal="lunch">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lunch_time">Heure du repas</label>
                        <input type="time" class="form-control" name="lunch_time" id="lunch_time"
                               value="<?php echo $today_entry && $today_entry->lunch_time ? date('H:i', strtotime($today_entry->lunch_time)) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lunch_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="lunch_notes" id="lunch_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->lunch_notes) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Dinner -->
                <div class="meal-card" id="dinnerCard">
                    <div class="meal-card-title">
                        <i class="fa fa-moon-o"></i>
                        Dîner
                    </div>

                    <div class="photo-upload-area" data-meal="dinner">
                        <input type="file" id="dinnerPhoto" accept="image/*" data-meal="dinner">
                        <input type="hidden" name="dinner_photo" id="dinner_photo_value">
                        <div class="photo-upload-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                        <div class="photo-upload-text">Cliquez ou glissez une photo</div>
                        <div class="photo-upload-hint">JPEG, PNG ou GIF - Max 5MB</div>
                    </div>

                    <div id="dinnerPreview" class="photo-preview" style="display: none;">
                        <img src="" alt="Dîner">
                        <button type="button" class="photo-preview-remove" data-meal="dinner">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="dinner_time">Heure du repas</label>
                        <input type="time" class="form-control" name="dinner_time" id="dinner_time"
                               value="<?php echo $today_entry && $today_entry->dinner_time ? date('H:i', strtotime($today_entry->dinner_time)) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="dinner_notes">Notes (optionnel)</label>
                        <textarea class="form-control" name="dinner_notes" id="dinner_notes"
                                  placeholder="Décrivez ce que vous avez mangé..."><?php echo $today_entry ? htmlspecialchars($today_entry->dinner_notes) : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Water Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-tint"></i>
                    Consommation d'eau
                </div>

                <div class="form-group">
                    <label class="form-label" for="water_quantity_ml">Quantité d'eau consommée (ml)</label>
                    <input type="number" class="form-control" name="water_quantity_ml" id="water_quantity_ml"
                           placeholder="Ex: 2000" min="0" step="100"
                           value="<?php echo $today_entry ? $today_entry->water_quantity_ml : ''; ?>">
                    <small style="color: var(--text-light); margin-top: 5px; display: block;">
                        <i class="fa fa-info-circle"></i>
                        Recommandé: 2000 ml (2 litres) par jour
                    </small>
                </div>
            </div>

            <!-- Beverages Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa fa-coffee"></i>
                    Autres boissons
                </div>

                <div id="beveragesList" class="beverage-list"></div>

                <button type="button" class="add-beverage-btn" onclick="addBeverageRow()">
                    <i class="fa fa-plus-circle"></i>
                    Ajouter une boisson
                </button>
            </div>

            <!-- Submit Section -->
            <div class="submit-section">
                <button type="submit" class="btn-submit">
                    <i class="fa fa-check-circle"></i>
                    Soumettre mon entrée quotidienne
                </button>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Téléchargement en cours...</p>
        </div>
    </div>

    <!-- Perfex JS -->
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

    <script>
    // Base URL for AJAX calls
    const base_url = '<?php echo base_url(); ?>';
    const site_url = '<?php echo site_url(); ?>';
    let beverageCount = 0;

    // Photo upload handling
    $(document).ready(function() {
        // Click to upload
        $('.photo-upload-area').on('click', function() {
            const meal = $(this).data('meal');
            $('#' + meal + 'Photo').click();
        });

        // File input change
        $('input[type="file"]').on('change', function() {
            const meal = $(this).data('meal');
            const file = this.files[0];

            if (file) {
                uploadPhoto(meal, file);
            }
        });

        // Drag and drop
        $('.photo-upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragging');
        });

        $('.photo-upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragging');
        });

        $('.photo-upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragging');

            const meal = $(this).data('meal');
            const file = e.originalEvent.dataTransfer.files[0];

            if (file && file.type.startsWith('image/')) {
                uploadPhoto(meal, file);
            } else {
                showAlert('Veuillez déposer un fichier image valide', 'danger');
            }
        });

        // Remove photo
        $('.photo-preview-remove').on('click', function(e) {
            e.stopPropagation();
            const meal = $(this).data('meal');
            removePhoto(meal);
        });

        // Form submission
        $('#dailyEntryForm').on('submit', function(e) {
            e.preventDefault();
            submitEntry();
        });

        // Load existing beverages if editing
        <?php if (!empty($beverages)): ?>
            <?php foreach ($beverages as $beverage): ?>
                addBeverageRow(
                    '<?php echo htmlspecialchars($beverage->beverage_name); ?>',
                    '<?php echo $beverage->quantity_ml; ?>',
                    '<?php echo date('H:i', strtotime($beverage->consumption_time)); ?>',
                    '<?php echo htmlspecialchars($beverage->notes ?? ''); ?>'
                );
            <?php endforeach; ?>
        <?php endif; ?>
    });

    // Upload photo
    function uploadPhoto(meal, file) {
        $('#loadingOverlay').addClass('active');

        const formData = new FormData();
        formData.append('photo', file);

        $.ajax({
            url: admin_url + 'dietetic/food_surveys/upload_photo',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingOverlay').removeClass('active');

                if (response.success) {
                    // Hide upload area
                    $('.photo-upload-area[data-meal="' + meal + '"]').hide();

                    // Show preview
                    $('#' + meal + 'Preview').show();
                    $('#' + meal + 'Preview img').attr('src', response.url);

                    // Store filename
                    $('#' + meal + '_photo_value').val(response.filename);

                    // Mark card as having photo
                    $('#' + meal + 'Card').addClass('has-photo');

                    showAlert('Photo téléchargée avec succès', 'success');
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                $('#loadingOverlay').removeClass('active');
                showAlert('Erreur lors du téléchargement de la photo', 'danger');
            }
        });
    }

    // Remove photo
    function removePhoto(meal) {
        const filename = $('#' + meal + '_photo_value').val();

        if (filename) {
            // Delete from server
            $.ajax({
                url: admin_url + 'dietetic/food_surveys/delete_photo',
                type: 'POST',
                data: { filename: filename },
                success: function(response) {
                    if (response.success) {
                        showAlert('Photo supprimée', 'success');
                    }
                }
            });
        }

        // Reset UI
        $('#' + meal + 'Preview').hide();
        $('#' + meal + 'Preview img').attr('src', '');
        $('#' + meal + '_photo_value').val('');
        $('.photo-upload-area[data-meal="' + meal + '"]').show();
        $('#' + meal + 'Card').removeClass('has-photo');
        $('#' + meal + 'Photo').val('');
    }

    // Add beverage row
    function addBeverageRow(name = '', quantity = '', time = '', notes = '') {
        beverageCount++;

        const html = `
            <div class="beverage-item" id="beverage_${beverageCount}">
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Nom de la boisson</label>
                    <input type="text" class="form-control" name="beverages[${beverageCount}][name]"
                           placeholder="Ex: Café, Jus d'orange..." value="${name}" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Quantité (ml)</label>
                    <input type="number" class="form-control" name="beverages[${beverageCount}][quantity]"
                           placeholder="250" min="0" value="${quantity}" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">Heure</label>
                    <input type="time" class="form-control" name="beverages[${beverageCount}][time]"
                           value="${time}" required>
                </div>
                <button type="button" onclick="removeBeverageRow(${beverageCount})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;

        $('#beveragesList').append(html);
    }

    // Remove beverage row
    function removeBeverageRow(id) {
        $('#beverage_' + id).fadeOut(300, function() {
            $(this).remove();
        });
    }

    // Submit entry
    function submitEntry() {
        const formData = $('#dailyEntryForm').serialize();

        $('#loadingOverlay').addClass('active');
        $('.btn-submit').prop('disabled', true);

        $.ajax({
            url: site_url + 'dietetic/portal/save_daily_entry',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#loadingOverlay').removeClass('active');
                $('.btn-submit').prop('disabled', false);

                if (response.success) {
                    showAlert('Entrée enregistrée avec succès ! Redirection...', 'success');
                    setTimeout(function() {
                        window.location.href = site_url + 'dietetic/portal/food_surveys';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                $('#loadingOverlay').removeClass('active');
                $('.btn-submit').prop('disabled', false);
                showAlert('Erreur lors de l\'enregistrement. Veuillez réessayer.', 'danger');
            }
        });
    }

    // Show alert
    function showAlert(message, type) {
        const icon = type === 'success' ? 'check-circle' : (type === 'danger' ? 'exclamation-triangle' : 'info-circle');

        const html = `
            <div class="alert alert-${type}">
                <i class="fa fa-${icon}"></i>
                ${message}
            </div>
        `;

        $('#alertArea').html(html);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#alertArea').fadeOut(300, function() {
                $(this).html('').show();
            });
        }, 5000);
    }

    // Set admin_url for compatibility
    const admin_url = base_url + 'admin/';
    </script>
</body>
</html>
