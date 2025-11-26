<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.activities-container {
    padding: 20px 0;
}

.activity-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
}

.activity-section h4 {
    color: #01807B;
    font-size: 20px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 3px solid #01807B;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-add-activity {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-add-activity:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
    color: white;
}

.activity-card {
    background: #f8f9fa;
    border-left: 4px solid #01807B;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.activity-card:hover {
    transform: translateX(3px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.activity-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.activity-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.activity-date {
    color: #7f8c8d;
    font-size: 13px;
}

.activity-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.activity-stat {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #2c3e50;
    font-size: 14px;
}

.activity-stat i {
    color: #01807B;
}

.activity-stat strong {
    color: #01807B;
}

.activities-table {
    width: 100%;
    margin-top: 15px;
}

.activities-table th {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    padding: 12px;
    border-bottom: 2px solid #01807B;
}

.activities-table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}

.activities-table tbody tr:hover {
    background: #f8f9fa;
}

.category-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.category-cardio {
    background: #e3f2fd;
    color: #1976d2;
}

.category-musculation {
    background: #fce4ec;
    color: #c2185b;
}

.category-sports {
    background: #e8f5e9;
    color: #388e3c;
}

.category-yoga {
    background: #f3e5f5;
    color: #7b1fa2;
}

.category-autres {
    background: #fff3e0;
    color: #f57c00;
}

.no-activities {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
}

.no-activities i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.3;
}

.total-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-box {
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.stat-box-value {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-box-label {
    font-size: 14px;
    opacity: 0.9;
}
</style>

<div class="activities-container">
    <!-- Statistics Summary -->
    <div class="activity-section">
        <h4>
            <i class="fa fa-bar-chart"></i>
            Statistiques d'Activité
        </h4>
        <div class="total-stats" id="activityStats">
            <div class="stat-box">
                <div class="stat-box-value" id="totalActivities">-</div>
                <div class="stat-box-label">Activités enregistrées</div>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #F3911D 0%, #d67a0e 100%);">
                <div class="stat-box-value" id="totalMinutes">-</div>
                <div class="stat-box-label">Minutes d'exercice</div>
            </div>
            <div class="stat-box" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <div class="stat-box-value" id="totalKcal">-</div>
                <div class="stat-box-label">Kcal brûlées</div>
            </div>
        </div>
    </div>

    <!-- Add Activity Form -->
    <div class="activity-section">
        <h4>
            <i class="fa fa-plus-circle"></i>
            Ajouter une Activité
        </h4>
        <form id="addActivityForm">
            <input type="hidden" name="patient_id" value="<?php echo $patient->userid; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Activité <span class="text-danger">*</span></label>
                        <select name="activity_id" id="activitySelect" class="form-control" required>
                            <option value="">Sélectionner une activité...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Durée (minutes) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" id="durationInput" class="form-control" value="30" min="1" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Calories brûlées</label>
                        <input type="text" id="kcalDisplay" class="form-control" readonly style="background: #f8f9fa; font-weight: 600; color: #01807B;">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="activity_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Heure</label>
                        <input type="time" name="activity_time" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="padding-top: 25px;">
                        <button type="submit" class="btn-add-activity btn-block">
                            <i class="fa fa-plus"></i>
                            Ajouter l'activité
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Notes (optionnel)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Notes ou commentaires sur cette activité..."></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Activities History -->
    <div class="activity-section">
        <h4>
            <i class="fa fa-history"></i>
            Historique des Activités
        </h4>
        <div id="activitiesHistory">
            <div class="no-activities">
                <i class="fa fa-heartbeat"></i>
                <p>Chargement des activités...</p>
            </div>
        </div>
    </div>
</div>

<script>
let activitiesData = [];
let selectedActivityKcalPerMin = 0;

// Load activities on page load
$(document).ready(function() {
    loadActivitiesList();
    loadPatientActivities();

    // Calculate kcal when activity or duration changes
    $('#activitySelect, #durationInput').on('change input', calculateKcal);
});

// Load available activities
function loadActivitiesList() {
    $.ajax({
        url: admin_url + 'dietetic/activities/get_activities',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                activitiesData = response.activities;
                let select = $('#activitySelect');
                select.empty();
                select.append('<option value="">Sélectionner une activité...</option>');

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
        },
        error: function() {
            alert_float('danger', 'Erreur lors du chargement des activités');
        }
    });
}

// Calculate calories based on activity and duration
function calculateKcal() {
    let selectedOption = $('#activitySelect option:selected');
    let kcalPerMin = parseFloat(selectedOption.data('kcal')) || 0;
    let duration = parseInt($('#durationInput').val()) || 0;

    selectedActivityKcalPerMin = kcalPerMin;
    let totalKcal = (kcalPerMin * duration).toFixed(2);

    $('#kcalDisplay').val(totalKcal + ' kcal');
}

// Load patient activities
function loadPatientActivities() {
    $.ajax({
        url: admin_url + 'dietetic/activities/get_patient_activities/<?php echo $patient->userid; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayActivities(response.activities);
                updateStats(response.stats);
            }
        },
        error: function() {
            $('#activitiesHistory').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Erreur lors du chargement des activités</div>');
        }
    });
}

// Display activities
function displayActivities(activities) {
    let html = '';

    if (activities.length === 0) {
        html = '<div class="no-activities"><i class="fa fa-heartbeat"></i><p>Aucune activité enregistrée pour ce patient.</p></div>';
    } else {
        html += '<table class="activities-table table">';
        html += '<thead><tr>';
        html += '<th>Date</th>';
        html += '<th>Activité</th>';
        html += '<th>Catégorie</th>';
        html += '<th>Durée</th>';
        html += '<th>Kcal brûlées</th>';
        html += '<th>Notes</th>';
        html += '<th>Actions</th>';
        html += '</tr></thead><tbody>';

        activities.forEach(activity => {
            let categoryClass = 'category-autres';
            if (activity.category === 'Cardio') categoryClass = 'category-cardio';
            else if (activity.category === 'Musculation') categoryClass = 'category-musculation';
            else if (activity.category === 'Sports collectifs') categoryClass = 'category-sports';
            else if (activity.category === 'Yoga/Étirements') categoryClass = 'category-yoga';

            html += '<tr>';
            html += '<td>' + formatDate(activity.activity_date) + (activity.activity_time ? ' ' + activity.activity_time : '') + '</td>';
            html += '<td><strong>' + activity.activity_name + '</strong></td>';
            html += '<td><span class="category-badge ' + categoryClass + '">' + activity.category + '</span></td>';
            html += '<td>' + activity.duration_minutes + ' min</td>';
            html += '<td><strong style="color: #e74c3c;">' + parseFloat(activity.kcal_burned).toFixed(0) + ' kcal</strong></td>';
            html += '<td>' + (activity.notes || '-') + '</td>';
            html += '<td><button class="btn btn-sm btn-danger" onclick="deleteActivity(' + activity.id + ')"><i class="fa fa-trash"></i></button></td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
    }

    $('#activitiesHistory').html(html);
}

// Update statistics
function updateStats(stats) {
    $('#totalActivities').text(stats.total_activities || 0);
    $('#totalMinutes').text(stats.total_minutes || 0);
    $('#totalKcal').text((stats.total_kcal || 0).toFixed(0));
}

// Format date
function formatDate(dateString) {
    let date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

// Submit activity form
$('#addActivityForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();
    let duration = parseInt($('#durationInput').val());
    let kcalBurned = selectedActivityKcalPerMin * duration;

    formData += '&kcal_burned=' + kcalBurned;

    $.ajax({
        url: admin_url + 'dietetic/activities/add_patient_activity',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Activité ajoutée avec succès');
                $('#addActivityForm')[0].reset();
                $('#addActivityForm input[name="csrf_token"]').val(response.csrf_token);
                $('#kcalDisplay').val('');
                loadPatientActivities();
            } else {
                alert_float('danger', response.message || 'Erreur lors de l\'ajout de l\'activité');
                if (response.csrf_token) {
                    $('#addActivityForm input[name="csrf_token"]').val(response.csrf_token);
                }
            }
        },
        error: function() {
            alert_float('danger', 'Erreur lors de l\'ajout de l\'activité');
        }
    });
});

// Delete activity
function deleteActivity(activityId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) {
        return;
    }

    $.ajax({
        url: admin_url + 'dietetic/activities/delete_patient_activity/' + activityId,
        type: 'POST',
        data: {
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Activité supprimée');
                $('input[name="csrf_token"]').val(response.csrf_token);
                loadPatientActivities();
            } else {
                alert_float('danger', response.message || 'Erreur lors de la suppression');
                if (response.csrf_token) {
                    $('input[name="csrf_token"]').val(response.csrf_token);
                }
            }
        },
        error: function() {
            alert_float('danger', 'Erreur lors de la suppression');
        }
    });
}
</script>
