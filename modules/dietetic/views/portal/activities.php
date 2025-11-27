<?php
$active_page = 'activities';
$page_title = 'Mes Activités Sportives';
$this->load->view('portal/includes/portal_header');
?>

<style>
.activities-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.activity-header {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.2);
}

.activity-header h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.activity-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 15px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.stat-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 24px;
}

.stat-card-value {
    font-size: 32px;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 8px;
}

.stat-card-label {
    font-size: 14px;
    color: #7f8c8d;
    font-weight: 500;
}

.activity-section {
    background: white;
    border-radius: 12px;
    padding: 28px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 3px solid #01807B;
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-form {
    background: #f8f9fa;
    padding: 24px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    border-color: #01807B;
    outline: none;
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
}

.btn-add-activity {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    justify-content: center;
}

.btn-add-activity:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(1, 128, 123, 0.3);
}

.kcal-display {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
    margin-top: 16px;
}

.kcal-display-value {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 4px;
}

.kcal-display-label {
    font-size: 14px;
    opacity: 0.9;
}

.activity-item {
    background: #f8f9fa;
    border-left: 4px solid #01807B;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.activity-item:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.activity-item-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 12px;
}

.activity-name {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 4px;
}

.activity-date {
    color: #7f8c8d;
    font-size: 13px;
}

.activity-details {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.activity-detail {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #2c3e50;
    font-size: 14px;
}

.activity-detail i {
    color: #01807B;
    width: 20px;
}

.activity-detail strong {
    color: #01807B;
    font-weight: 600;
}

.category-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.no-activities {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
}

.no-activities i {
    font-size: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 13px;
}

.btn-delete:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .activity-details {
        flex-direction: column;
        gap: 12px;
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
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

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

<?php $this->load->view('portal/includes/portal_footer'); ?>

<script>
let activitiesData = [];
let selectedActivityKcalPerMin = 0;

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
            html += '<button class="btn-delete" onclick="deleteActivity(' + activity.id + ')"><i class="fa fa-trash"></i> Supprimer</button>';
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
                alert_float('success', 'Activité enregistrée !');
                $('#addActivityForm')[0].reset();
                $('#addActivityForm input[name="csrf_token"]').val(response.csrf_token);
                $('#kcalValue').text('0');
                loadMyActivities();
            } else {
                alert_float('danger', response.message || 'Erreur');
                if (response.csrf_token) {
                    $('#addActivityForm input[name="csrf_token"]').val(response.csrf_token);
                }
            }
        }
    });
});

// Delete activity
function deleteActivity(id) {
    if (!confirm('Voulez-vous vraiment supprimer cette activité ?')) {
        return;
    }

    $.ajax({
        url: site_url + 'dietetic/portal/delete_activity/' + id,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Activité supprimée');
                $('input[name="csrf_token"]').val(response.csrf_token);
                loadMyActivities();
            } else {
                alert_float('danger', response.message || 'Erreur');
                if (response.csrf_token) {
                    $('input[name="csrf_token"]').val(response.csrf_token);
                }
            }
        }
    });
}
</script>
