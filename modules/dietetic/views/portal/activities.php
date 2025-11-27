<?php
$active_page = 'activities';
$page_title = 'Mes Activités Sportives';
$this->load->view('portal/includes/portal_header');
?>

<style>
.activities-page {
    max-width: 100%;
    margin: 0 auto;
    padding: 16px;
}

.activity-header {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border-radius: 20px;
    padding: 24px 20px;
    margin-bottom: 20px;
}

.activity-header h1 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 14px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: none;
    border: 1px solid #f0f0f0;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 22px;
}

.stat-card-value {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 4px;
    line-height: 1;
}

.stat-card-label {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 500;
}

.activity-section {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: none;
    border: 1px solid #f0f0f0;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 16px;
    padding-bottom: 0;
    border-bottom: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.activity-form {
    background: #fafafa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 16px;
    border: none;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 13px;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    border-color: #01807B;
    outline: none;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.08);
}

.btn-add-activity {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
}

.btn-add-activity:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.3);
}

.kcal-display {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 16px;
    border-radius: 12px;
    text-align: center;
    margin-top: 16px;
}

.kcal-display-value {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 4px;
    line-height: 1;
}

.kcal-display-label {
    font-size: 13px;
    opacity: 0.95;
}

.activity-item {
    background: #fafafa;
    border-left: 4px solid #01807B;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
    border: 1px solid #f0f0f0;
    border-left: 4px solid #01807B;
}

.activity-item:hover {
    transform: translateX(2px);
    background: #f5f5f5;
}

.activity-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 12px;
    gap: 12px;
}

.activity-name {
    font-size: 16px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 4px;
    line-height: 1.3;
}

.activity-date {
    color: #7f8c8d;
    font-size: 12px;
}

.activity-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-top: 12px;
}

.activity-detail {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #2c3e50;
    font-size: 13px;
}

.activity-detail i {
    color: #01807B;
    width: 18px;
    font-size: 14px;
}

.activity-detail strong {
    color: #01807B;
    font-weight: 600;
}

.category-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.no-activities {
    text-align: center;
    padding: 50px 20px;
    color: #7f8c8d;
}

.no-activities i {
    font-size: 60px;
    margin-bottom: 16px;
    opacity: 0.3;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btn-delete:hover {
    background: #c0392b;
    transform: scale(1.1);
}

/* Responsive */
@media (max-width: 768px) {
    .activities-page {
        padding: 12px;
    }

    .activity-header {
        padding: 20px 16px;
        border-radius: 16px;
    }

    .activity-header h1 {
        font-size: 20px;
    }

    .activity-header p {
        font-size: 13px;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .stat-card {
        padding: 16px;
    }

    .stat-card-value {
        font-size: 24px;
    }

    .stat-card-label {
        font-size: 12px;
    }

    .activity-section {
        padding: 16px;
        border-radius: 16px;
    }

    .section-title {
        font-size: 16px;
    }

    .activity-form {
        padding: 16px;
    }

    .activity-details {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .activity-name {
        font-size: 15px;
    }

    .kcal-display-value {
        font-size: 28px;
    }
}

@media (min-width: 769px) {
    .activities-page {
        max-width: 900px;
        padding: 24px;
    }

    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .activity-header {
        padding: 32px 28px;
    }

    .activity-section {
        padding: 28px;
    }
}

@media (min-width: 1024px) {
    .activities-page {
        max-width: 1100px;
    }
}

/* Confirmation Modal (Mobile App Style) */
.confirm-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.confirm-modal.show {
    display: flex;
    animation: fadeIn 0.2s ease;
}

.confirm-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
}

.confirm-modal-content {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 32px 24px 24px;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    animation: slideUp 0.3s ease;
    z-index: 1;
}

.confirm-modal-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
}

.confirm-modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 12px 0;
    line-height: 1.3;
}

.confirm-modal-message {
    font-size: 15px;
    color: #7f8c8d;
    margin: 0 0 28px 0;
    line-height: 1.5;
}

.confirm-modal-buttons {
    display: flex;
    gap: 12px;
    flex-direction: column;
}

.confirm-btn {
    padding: 14px 20px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.confirm-btn-cancel {
    background: #f0f0f0;
    color: #2c3e50;
}

.confirm-btn-cancel:hover {
    background: #e0e0e0;
    transform: translateY(-1px);
}

.confirm-btn-delete {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.confirm-btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(231, 76, 60, 0.4);
}

.confirm-btn-delete:active {
    transform: translateY(0);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Desktop adjustments */
@media (min-width: 769px) {
    .confirm-modal-buttons {
        flex-direction: row;
    }

    .confirm-btn-cancel {
        order: 1;
    }

    .confirm-btn-delete {
        order: 2;
    }
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .confirm-modal-content {
        padding: 28px 20px 20px;
        border-radius: 16px;
    }

    .confirm-modal-icon {
        width: 56px;
        height: 56px;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .confirm-modal-title {
        font-size: 18px;
    }

    .confirm-modal-message {
        font-size: 14px;
        margin-bottom: 24px;
    }

    .confirm-btn {
        padding: 12px 18px;
        font-size: 15px;
    }
}
</style>

<div class="activities-page">
    <!-- Header -->
    <div class="activity-header">
        <h1>
            <i class="fa fa-heartbeat"></i>
            Mes Activités Sportives
        </h1>
        <p>Suivez vos activités physiques et les calories brûlées</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #01807B 0%, #026660 100%); color: white;">
                <i class="fa fa-list"></i>
            </div>
            <div class="stat-card-value" id="totalActivities">0</div>
            <div class="stat-card-label">Activités enregistrées</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #F3911D 0%, #d67a0e 100%); color: white;">
                <i class="fa fa-clock-o"></i>
            </div>
            <div class="stat-card-value" id="totalMinutes">0</div>
            <div class="stat-card-label">Minutes d'exercice</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white;">
                <i class="fa fa-fire"></i>
            </div>
            <div class="stat-card-value" id="totalKcal">0</div>
            <div class="stat-card-label">Calories brûlées</div>
        </div>
    </div>

    <!-- Add Activity Form -->
    <div class="activity-section">
        <h2 class="section-title">
            <i class="fa fa-plus-circle"></i>
            Ajouter une Activité
        </h2>

        <form id="addActivityForm" class="activity-form">
            <input type="hidden" name="patient_id" value="<?php echo $patient->id; ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token_field">

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Sélectionner une activité <span style="color: #e74c3c;">*</span></label>
                        <select name="activity_id" id="activitySelect" required>
                            <option value="">Choisir une activité...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Durée (minutes) <span style="color: #e74c3c;">*</span></label>
                        <input type="number" name="duration_minutes" id="durationInput" value="30" min="1" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Date <span style="color: #e74c3c;">*</span></label>
                        <input type="date" name="activity_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Heure (optionnel)</label>
                        <input type="time" name="activity_time">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Notes (optionnel)</label>
                <textarea name="notes" rows="3" placeholder="Ajoutez des notes sur votre activité..."></textarea>
            </div>

            <div class="kcal-display">
                <div class="kcal-display-value" id="kcalValue">0</div>
                <div class="kcal-display-label">Calories brûlées estimées</div>
            </div>

            <button type="submit" class="btn-add-activity" style="margin-top: 20px;">
                <i class="fa fa-plus"></i>
                Enregistrer l'activité
            </button>
        </form>
    </div>

    <!-- Activities History -->
    <div class="activity-section">
        <h2 class="section-title">
            <i class="fa fa-history"></i>
            Historique
        </h2>
        <div id="activitiesHistory">
            <div class="no-activities">
                <i class="fa fa-heartbeat"></i>
                <p>Chargement...</p>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal (Mobile Style) -->
<div id="deleteConfirmModal" class="confirm-modal">
    <div class="confirm-modal-overlay"></div>
    <div class="confirm-modal-content">
        <div class="confirm-modal-icon">
            <i class="fa fa-trash"></i>
        </div>
        <h3 class="confirm-modal-title">Supprimer l'activité</h3>
        <p class="confirm-modal-message">Voulez-vous vraiment supprimer cette activité ? Cette action est irréversible.</p>
        <div class="confirm-modal-buttons">
            <button class="confirm-btn confirm-btn-cancel" onclick="closeDeleteModal()">Annuler</button>
            <button class="confirm-btn confirm-btn-delete" onclick="confirmDelete()">Supprimer</button>
        </div>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>

<script>
let activitiesData = [];
let selectedActivityKcalPerMin = 0;
const csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let activityToDelete = null; // Store the ID of activity to be deleted

// Custom notification function for portal (replacement for alert_float)
function showNotification(message, type) {
    // type: 'success' or 'danger'
    const bgColor = type === 'success' ? '#48bb78' : '#e74c3c';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const toast = $('<div>')
        .css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: bgColor,
            color: 'white',
            padding: '14px 20px',
            borderRadius: '10px',
            zIndex: 10000,
            boxShadow: '0 4px 16px rgba(0,0,0,0.25)',
            fontSize: '14px',
            fontWeight: '600',
            display: 'flex',
            alignItems: 'center',
            gap: '10px',
            minWidth: '250px',
            maxWidth: '400px'
        })
        .html('<i class="fa ' + icon + '"></i> ' + message)
        .appendTo('body');

    setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3500);
}

$(document).ready(function() {
    loadActivitiesList();
    loadMyActivities();

    // Calculate kcal when inputs change
    $('#activitySelect, #durationInput').on('change input', calculateKcal);
});

// Load available activities
function loadActivitiesList() {
    $.ajax({
        url: site_url + 'dietetic/portal/get_activities',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let select = $('#activitySelect');
                select.empty();
                select.append('<option value="">Choisir une activité...</option>');

                // Group by category
                let categories = {};
                response.activities.forEach(activity => {
                    if (!categories[activity.category]) {
                        categories[activity.category] = [];
                    }
                    categories[activity.category].push(activity);
                });

                // Add grouped options
                Object.keys(categories).forEach(category => {
                    let optgroup = $('<optgroup label="' + category + '"></optgroup>');
                    categories[category].forEach(activity => {
                        optgroup.append('<option value="' + activity.id + '" data-kcal="' + activity.kcal_per_minute + '">' +
                            activity.name + ' (' + activity.kcal_per_minute + ' kcal/min)</option>');
                    });
                    select.append(optgroup);
                });
            }
        }
    });
}

// Calculate calories
function calculateKcal() {
    let selectedOption = $('#activitySelect option:selected');
    let kcalPerMin = parseFloat(selectedOption.data('kcal')) || 0;
    let duration = parseInt($('#durationInput').val()) || 0;

    selectedActivityKcalPerMin = kcalPerMin;
    let totalKcal = Math.round(kcalPerMin * duration);

    $('#kcalValue').text(totalKcal);
}

// Load my activities
function loadMyActivities() {
    $.ajax({
        url: site_url + 'dietetic/portal/get_my_activities',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayActivities(response.activities);
                updateStats(response.stats);
            }
        }
    });
}

// Display activities
function displayActivities(activities) {
    let html = '';

    if (!activities || activities.length === 0) {
        html = '<div class="no-activities"><i class="fa fa-heartbeat"></i><p>Aucune activité enregistrée.<br>Commencez à suivre vos activités sportives !</p></div>';
    } else {
        activities.forEach(activity => {
            let categoryClass = 'category-badge';
            let categoryStyle = '';

            if (activity.category === 'Cardio') {
                categoryStyle = 'background: #e3f2fd; color: #1976d2;';
            } else if (activity.category === 'Musculation') {
                categoryStyle = 'background: #fce4ec; color: #c2185b;';
            } else if (activity.category === 'Sports collectifs') {
                categoryStyle = 'background: #e8f5e9; color: #388e3c;';
            } else if (activity.category === 'Yoga/Étirements') {
                categoryStyle = 'background: #f3e5f5; color: #7b1fa2;';
            } else {
                categoryStyle = 'background: #fff3e0; color: #f57c00;';
            }

            html += '<div class="activity-item">';
            html += '<div class="activity-item-header">';
            html += '<div>';
            html += '<div class="activity-name">' + activity.activity_name + '</div>';
            html += '<div class="activity-date">' + formatDate(activity.activity_date);
            if (activity.activity_time) html += ' à ' + activity.activity_time;
            html += '</div>';
            html += '</div>';
            html += '<button class="btn-delete" onclick="deleteActivity(' + activity.id + ')" title="Supprimer"><i class="fa fa-trash"></i></button>';
            html += '</div>';

            html += '<div class="activity-details">';
            html += '<div class="activity-detail"><i class="fa fa-tag"></i> <span class="category-badge" style="' + categoryStyle + '">' + activity.category + '</span></div>';
            html += '<div class="activity-detail"><i class="fa fa-clock-o"></i> Durée: <strong>' + activity.duration_minutes + ' min</strong></div>';
            html += '<div class="activity-detail"><i class="fa fa-fire"></i> Calories: <strong>' + Math.round(activity.kcal_burned) + ' kcal</strong></div>';
            html += '</div>';

            if (activity.notes) {
                html += '<div style="margin-top: 12px; padding: 12px; background: white; border-radius: 6px; font-size: 14px; color: #6c757d;">';
                html += '<i class="fa fa-comment"></i> ' + activity.notes;
                html += '</div>';
            }

            html += '</div>';
        });
    }

    $('#activitiesHistory').html(html);
}

// Update statistics
function updateStats(stats) {
    $('#totalActivities').text(stats.total_activities || 0);
    $('#totalMinutes').text(stats.total_minutes || 0);
    $('#totalKcal').text(Math.round(stats.total_kcal || 0));
}

// Format date
function formatDate(dateString) {
    let date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
}

// Submit form
$('#addActivityForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();
    let duration = parseInt($('#durationInput').val());
    let kcalBurned = selectedActivityKcalPerMin * duration;

    formData += '&kcal_burned=' + kcalBurned;

    $.ajax({
        url: site_url + 'dietetic/portal/add_activity',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Activité enregistrée !', 'success');
                $('#addActivityForm')[0].reset();
                if (response.csrf_token) {
                    $('#csrf_token_field').val(response.csrf_token);
                }
                $('#kcalValue').text('0');
                loadMyActivities();
            } else {
                showNotification(response.message || 'Erreur', 'danger');
                if (response.csrf_token) {
                    $('#csrf_token_field').val(response.csrf_token);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            showNotification('Erreur lors de l\'ajout de l\'activité', 'danger');
        }
    });
});

// Delete activity - Show modal
function deleteActivity(id) {
    activityToDelete = id;
    showDeleteModal();
}

// Show delete confirmation modal
function showDeleteModal() {
    $('#deleteConfirmModal').addClass('show');
    $('body').css('overflow', 'hidden'); // Prevent background scrolling
}

// Close delete confirmation modal
function closeDeleteModal() {
    $('#deleteConfirmModal').removeClass('show');
    $('body').css('overflow', ''); // Restore scrolling
    activityToDelete = null;
}

// Confirm and execute deletion
function confirmDelete() {
    if (!activityToDelete) {
        return;
    }

    // Prepare data with dynamic CSRF token name
    let deleteData = {};
    deleteData[csrfTokenName] = $('#csrf_token_field').val();

    $.ajax({
        url: site_url + 'dietetic/portal/delete_activity/' + activityToDelete,
        type: 'POST',
        data: deleteData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Activité supprimée', 'success');
                if (response.csrf_token) {
                    $('#csrf_token_field').val(response.csrf_token);
                }
                loadMyActivities();
            } else {
                showNotification(response.message || 'Erreur', 'danger');
                if (response.csrf_token) {
                    $('#csrf_token_field').val(response.csrf_token);
                }
            }
            closeDeleteModal();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            showNotification('Erreur lors de la suppression', 'danger');
            closeDeleteModal();
        }
    });
}

// Close modal when clicking on overlay
$(document).on('click', '.confirm-modal-overlay', function() {
    closeDeleteModal();
});

// Prevent modal content clicks from closing modal
$(document).on('click', '.confirm-modal-content', function(e) {
    e.stopPropagation();
});

// Close modal with Escape key
$(document).keyup(function(e) {
    if (e.key === "Escape") {
        closeDeleteModal();
    }
});
</script>
