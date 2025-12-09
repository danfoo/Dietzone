<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .consultation-header {
        background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .consultation-header h2 {
        margin: 0 0 15px 0;
        font-size: 28px;
        font-weight: 700;
    }

    .header-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .status-badge.pending {
        background: #f39c12;
        color: white;
    }

    .status-badge.scheduled {
        background: #3498db;
        color: white;
    }

    .status-badge.completed {
        background: #27ae60;
        color: white;
    }

    .status-badge.cancelled {
        background: #e74c3c;
        color: white;
    }

    .status-badge.rejected {
        background: #c0392b;
        color: white;
    }

    .status-badge.no_show {
        background: #95a5a6;
        color: white;
    }

    .mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        background: rgba(255,255,255,0.2);
        font-weight: 600;
        font-size: 14px;
    }

    .platform-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 16px;
        background: rgba(255,255,255,0.15);
        font-size: 13px;
    }

    .countdown-timer {
        background: rgba(255,255,255,0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid #e8e8e8;
    }

    .info-card h5 {
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-card h5 i {
        color: #01807B;
        font-size: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 15px;
        color: #2c3e50;
        font-weight: 600;
    }

    .patient-summary {
        display: flex;
        gap: 20px;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .patient-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #01807B, #F3911D);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 700;
    }

    .patient-info h6 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 700;
    }

    .patient-info p {
        margin: 0;
        font-size: 13px;
        color: #7f8c8d;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e8e8e8;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -26px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #01807B;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #01807B;
    }

    .timeline-item .time {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 3px;
    }

    .timeline-item .event {
        font-size: 14px;
        color: #2c3e50;
        font-weight: 600;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .action-btn-primary {
        background: linear-gradient(135deg, #01807B, #026661);
        color: white;
    }

    .action-btn-primary:hover {
        background: linear-gradient(135deg, #026661, #01807B);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(1,128,123,0.3);
    }

    .action-btn-success {
        background: #27ae60;
        color: white;
    }

    .action-btn-success:hover {
        background: #229954;
        color: white;
        transform: translateY(-2px);
    }

    .action-btn-danger {
        background: #e74c3c;
        color: white;
    }

    .action-btn-danger:hover {
        background: #c0392b;
        color: white;
        transform: translateY(-2px);
    }

    .action-btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .action-btn-secondary:hover {
        background: #7f8c8d;
        color: white;
        transform: translateY(-2px);
    }

    .text-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #01807B;
        line-height: 1.6;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .meeting-link-box {
        background: #e8f8f5;
        border: 2px dashed #01807B;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .meeting-link-box a {
        color: #01807B;
        font-weight: 700;
        font-size: 16px;
        word-break: break-all;
    }

    .map-container {
        width: 100%;
        height: 250px;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 10px;
    }

    .weight-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #3498db;
        color: white;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .satisfaction-stars {
        color: #f39c12;
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .header-meta {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <!-- Header -->
        <div class="consultation-header">
            <h2>
                <i class="fa fa-stethoscope"></i>
                Consultation #<?php echo $consultation->id; ?>
            </h2>

            <div class="header-meta">
                <!-- Status Badge -->
                <div class="status-badge <?php echo $consultation->status; ?>">
                    <?php if ($consultation->status == 'pending'): ?>
                        <i class="fa fa-hourglass-half"></i> En attente de validation
                    <?php elseif ($consultation->status == 'scheduled'): ?>
                        <i class="fa fa-clock-o"></i> Planifiée
                    <?php elseif ($consultation->status == 'completed'): ?>
                        <i class="fa fa-check-circle"></i> Complétée
                    <?php elseif ($consultation->status == 'cancelled'): ?>
                        <i class="fa fa-times-circle"></i> Annulée
                    <?php elseif ($consultation->status == 'rejected'): ?>
                        <i class="fa fa-ban"></i> Refusée
                    <?php else: ?>
                        <i class="fa fa-user-times"></i> Absent
                    <?php endif; ?>
                </div>

                <!-- Mode Badge -->
                <?php if (!empty($consultation->consultation_mode)): ?>
                    <div class="mode-badge">
                        <?php if ($consultation->consultation_mode === 'online'): ?>
                            <i class="fa fa-video-camera"></i> En ligne
                        <?php else: ?>
                            <i class="fa fa-map-marker"></i> Présentiel
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Platform Badge (if online) -->
                <?php if (!empty($consultation->platform) && $consultation->consultation_mode === 'online'): ?>
                    <div class="platform-badge">
                        <?php
                        $platform_icons = [
                            'zoom' => 'fa-video-camera',
                            'google_meet' => 'fa-google',
                            'microsoft_teams' => 'fa-windows',
                            'whatsapp' => 'fa-whatsapp',
                            'skype' => 'fa-skype',
                            'other' => 'fa-globe'
                        ];
                        $icon = $platform_icons[$consultation->platform] ?? 'fa-globe';
                        ?>
                        <i class="fa <?php echo $icon; ?>"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $consultation->platform)); ?>
                    </div>
                <?php endif; ?>

                <!-- Countdown Timer (if scheduled and upcoming) -->
                <?php if ($consultation->status == 'scheduled'): ?>
                    <?php
                    $now = new DateTime();
                    // Build datetime string correctly
                    $datetime_string = $consultation->consultation_date;
                    if (!empty($consultation->consultation_time) && strpos($consultation->consultation_date, ':') === false) {
                        // Only append time if date doesn't already contain time
                        $datetime_string .= ' ' . $consultation->consultation_time;
                    } elseif (strpos($consultation->consultation_date, ':') === false) {
                        // No time in date and no consultation_time, use midnight
                        $datetime_string .= ' 00:00:00';
                    }
                    $consultation_datetime = new DateTime($datetime_string);
                    $interval = $now->diff($consultation_datetime);

                    if ($consultation_datetime > $now):
                        $days = $interval->days;
                        $hours = $interval->h;
                        $minutes = $interval->i;
                    ?>
                        <div class="countdown-timer">
                            <i class="fa fa-clock-o"></i>
                            <?php if ($days > 0): ?>
                                Dans <?php echo $days; ?> jour<?php echo $days > 1 ? 's' : ''; ?>
                            <?php elseif ($hours > 0): ?>
                                Dans <?php echo $hours; ?>h <?php echo $minutes; ?>min
                            <?php else: ?>
                                Dans <?php echo $minutes; ?> minutes
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Patient Info & Details -->
            <div class="col-md-8">
                <!-- Patient Summary -->
                <div class="info-card">
                    <h5><i class="fa fa-user"></i> Informations Patient</h5>

                    <div class="patient-summary">
                        <div class="patient-avatar">
                            <?php echo strtoupper(substr($patient->client_name ?? 'P', 0, 1)); ?>
                        </div>
                        <div class="patient-info">
                            <h6>
                                <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>">
                                    <?php echo $patient->client_name ?? 'Patient'; ?>
                                </a>
                            </h6>
                            <p>
                                <i class="fa fa-envelope"></i>
                                <?php echo isset($primary_contact->email) ? $primary_contact->email : 'Non défini'; ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <i class="fa fa-phone"></i>
                                <?php echo isset($primary_contact->phonenumber) ? $primary_contact->phonenumber : 'Non défini'; ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($consultation->weight_at_visit)): ?>
                        <div style="margin-top: 15px;">
                            <span class="weight-badge">
                                <i class="fa fa-balance-scale"></i>
                                Poids: <?php echo $consultation->weight_at_visit; ?> kg
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Consultation Details -->
                <div class="info-card">
                    <h5><i class="fa fa-info-circle"></i> Détails de la Consultation</h5>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Diététicien</span>
                            <span class="info-value"><?php echo $consultation->dietitian_name; ?></span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Date</span>
                            <span class="info-value">
                                <?php echo date('d/m/Y', strtotime($consultation->consultation_date)); ?>
                            </span>
                        </div>

                        <?php if (!empty($consultation->consultation_time)): ?>
                            <div class="info-item">
                                <span class="info-label">Heure</span>
                                <span class="info-value">
                                    <?php echo date('H:i', strtotime($consultation->consultation_time)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="info-item">
                            <span class="info-label">Durée</span>
                            <span class="info-value"><?php echo $consultation->duration; ?> min</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Type</span>
                            <span class="info-value">
                                <?php echo dietetic_consultation_type_label($consultation->consultation_type); ?>
                            </span>
                        </div>

                        <?php if (!empty($consultation->satisfaction_score)): ?>
                            <div class="info-item">
                                <span class="info-label">Satisfaction</span>
                                <span class="info-value satisfaction-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $consultation->satisfaction_score): ?>
                                            <i class="fa fa-star"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star-o"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Meeting Link (if online) -->
                    <?php if ($consultation->consultation_mode === 'online' && !empty($consultation->meeting_link)): ?>
                        <div class="meeting-link-box" style="margin-top: 20px;">
                            <p style="margin: 0 0 10px 0; font-weight: 600; color: #01807B;">
                                <i class="fa fa-video-camera"></i> Lien de visioconférence
                            </p>
                            <a href="<?php echo $consultation->meeting_link; ?>" target="_blank">
                                <?php echo $consultation->meeting_link; ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Location Map (if in-person) -->
                    <?php if ($consultation->consultation_mode === 'in_person' && !empty($consultation->location)): ?>
                        <div style="margin-top: 20px;">
                            <div class="info-label" style="margin-bottom: 10px;">
                                <i class="fa fa-map-marker"></i> Lieu
                            </div>
                            <div class="info-value" style="margin-bottom: 10px;">
                                <?php echo $consultation->location; ?>
                            </div>
                            <div class="map-container">
                                <iframe
                                    width="100%"
                                    height="100%"
                                    frameborder="0"
                                    style="border:0"
                                    src="https://www.google.com/maps/embed/v1/place?key=YOUR_GOOGLE_MAPS_API_KEY&q=<?php echo urlencode($consultation->location); ?>"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reason -->
                <?php if (!empty($consultation->reason)): ?>
                    <div class="info-card">
                        <h5><i class="fa fa-file-text-o"></i> Motif de Consultation</h5>
                        <div class="text-content">
                            <?php echo nl2br(htmlspecialchars($consultation->reason)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Observations -->
                <?php if (!empty($consultation->observations)): ?>
                    <div class="info-card">
                        <h5><i class="fa fa-eye"></i> Observations</h5>
                        <div class="text-content">
                            <?php echo nl2br(htmlspecialchars($consultation->observations)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recommendations -->
                <?php if (!empty($consultation->recommendations)): ?>
                    <div class="info-card">
                        <h5><i class="fa fa-lightbulb-o"></i> Recommandations</h5>
                        <div class="text-content">
                            <?php echo nl2br(htmlspecialchars($consultation->recommendations)); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Notes -->
                <?php if (!empty($consultation->notes)): ?>
                    <div class="info-card">
                        <h5><i class="fa fa-pencil"></i> Notes Privées</h5>
                        <div class="text-content">
                            <?php echo nl2br(htmlspecialchars($consultation->notes)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Actions & Timeline -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="info-card">
                    <h5><i class="fa fa-bolt"></i> Actions Rapides</h5>

                    <div class="quick-actions">
                        <?php if ($consultation->status == 'pending' && dietetic_has_permission('edit')): ?>
                            <button onclick="acceptAppointment(<?php echo $consultation->id; ?>)" class="action-btn action-btn-success" style="width: 100%; margin-bottom: 10px;">
                                <i class="fa fa-check-circle"></i>
                                Accepter la demande
                            </button>
                            <button onclick="rejectAppointment(<?php echo $consultation->id; ?>)" class="action-btn action-btn-danger" style="width: 100%; margin-bottom: 10px;">
                                <i class="fa fa-times-circle"></i>
                                Refuser la demande
                            </button>
                        <?php endif; ?>

                        <?php if ($consultation->consultation_mode === 'online' && !empty($consultation->meeting_link) && $consultation->status == 'scheduled'): ?>
                            <a href="<?php echo $consultation->meeting_link; ?>" target="_blank" class="action-btn action-btn-primary">
                                <i class="fa fa-video-camera"></i>
                                Rejoindre la Visio
                            </a>
                        <?php endif; ?>

                        <?php if ($consultation->status == 'scheduled' && dietetic_has_permission('edit')): ?>
                            <button onclick="markAsCompleted(<?php echo $consultation->id; ?>)" class="action-btn action-btn-success">
                                <i class="fa fa-check"></i>
                                Marquer Complétée
                            </button>
                        <?php endif; ?>

                        <?php if (dietetic_has_permission('edit')): ?>
                            <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>" class="action-btn action-btn-primary">
                                <i class="fa fa-pencil"></i>
                                Modifier
                            </a>
                        <?php endif; ?>

                        <?php if ($consultation->status == 'scheduled'): ?>
                            <button onclick="sendReminder(<?php echo $consultation->id; ?>)" class="action-btn action-btn-secondary">
                                <i class="fa fa-bell"></i>
                                Envoyer un Rappel
                            </button>

                            <button onclick="cancelConsultation(<?php echo $consultation->id; ?>)" class="action-btn action-btn-danger">
                                <i class="fa fa-times"></i>
                                Annuler
                            </button>
                        <?php endif; ?>

                        <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="action-btn action-btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                            Retour à la Liste
                        </a>
                    </div>
                </div>

                <!-- Timeline -->
                <?php if (!empty($consultation->next_consultation_date)): ?>
                    <div class="info-card">
                        <h5><i class="fa fa-clock-o"></i> Prochaine Consultation</h5>
                        <p style="margin: 0; font-size: 16px; font-weight: 600; color: #01807B;">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($consultation->next_consultation_date)); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Activity Timeline -->
                <div class="info-card">
                    <h5><i class="fa fa-history"></i> Historique</h5>

                    <div class="timeline">
                        <?php if (!empty($consultation->completed_at)): ?>
                            <div class="timeline-item">
                                <div class="time">
                                    <?php echo date('d/m/Y H:i', strtotime($consultation->completed_at)); ?>
                                </div>
                                <div class="event">
                                    <i class="fa fa-check-circle text-success"></i>
                                    Consultation complétée
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="timeline-item">
                            <div class="time">
                                <?php echo date('d/m/Y H:i', strtotime($consultation->created_at)); ?>
                            </div>
                            <div class="event">
                                <i class="fa fa-plus-circle text-info"></i>
                                Consultation créée
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour le refus de rendez-vous -->
<div class="modal fade" id="rejectAppointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-ban"></i> Refuser la demande de rendez-vous
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Attention :</strong> Le patient recevra une notification de refus par SMS, Email et WhatsApp.
                </div>
                <div class="form-group">
                    <label for="rejection-reason">Motif du refus <span class="text-muted">(optionnel)</span></label>
                    <textarea class="form-control" id="rejection-reason" rows="4"
                              placeholder="Expliquez la raison du refus au patient (ex: Horaire non disponible, patient déjà suivi par un autre diététicien, etc.)"></textarea>
                    <small class="text-muted">Ce motif sera envoyé au patient dans la notification.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirm-reject-btn">
                    <i class="fa fa-ban"></i> Confirmer le refus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function markAsCompleted(id) {
    if (confirm('Marquer cette consultation comme complétée ?')) {
        $.post('<?php echo admin_url('dietetic/consultations/mark_completed/'); ?>' + id, function(response) {
            if (response.success) {
                alert_float('success', 'Consultation marquée comme complétée');
                location.reload();
            } else {
                alert_float('danger', 'Erreur lors de la mise à jour');
            }
        }, 'json');
    }
}

function sendReminder(id) {
    if (confirm('Envoyer un rappel au patient pour cette consultation ?')) {
        $.post('<?php echo admin_url('dietetic/consultations/send_reminder/'); ?>' + id, function(response) {
            if (response.success) {
                alert_float('success', 'Rappel envoyé avec succès');
            } else {
                alert_float('danger', 'Erreur lors de l\'envoi du rappel');
            }
        }, 'json');
    }
}

function cancelConsultation(id) {
    var reason = prompt('Raison de l\'annulation (optionnel):');
    if (reason !== null) {
        $.post('<?php echo admin_url('dietetic/consultations/cancel/'); ?>' + id, {
            reason: reason
        }, function(response) {
            if (response.success) {
                alert_float('success', 'Consultation annulée');
                location.reload();
            } else {
                alert_float('danger', 'Erreur lors de l\'annulation');
            }
        }, 'json');
    }
}

function acceptAppointment(id) {
    if (confirm('Accepter cette demande de rendez-vous ?')) {
        $.post('<?php echo admin_url('dietetic/consultations/accept_appointment/'); ?>' + id, function(response) {
            if (response.success) {
                alert_float('success', 'Demande de rendez-vous acceptée');
                location.reload();
            } else {
                alert_float('danger', 'Erreur lors de l\'acceptation');
            }
        }, 'json');
    }
}

// Variable globale pour stocker l'ID de la consultation à refuser
var consultationToReject = null;

function rejectAppointment(id) {
    console.log('rejectAppointment called with id:', id);
    consultationToReject = id;
    $('#rejection-reason').val(''); // Réinitialiser le champ
    $('#rejectAppointmentModal').modal('show');
}

// Gestionnaire de clic pour le bouton de confirmation du refus
$(document).ready(function() {
    console.log('Document ready - attaching reject button handler');

    $('#confirm-reject-btn').on('click', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut
        console.log('Confirm reject button clicked, consultation ID:', consultationToReject);

        if (consultationToReject === null) {
            console.error('No consultation ID to reject');
            return;
        }

        var reason = $('#rejection-reason').val().trim();
        var $btn = $(this);

        console.log('Rejection reason:', reason);

        // Désactiver le bouton pendant l'envoi
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Envoi en cours...');

        var ajaxUrl = '<?php echo admin_url('dietetic/consultations/reject_appointment/'); ?>' + consultationToReject;
        console.log('Sending AJAX request to:', ajaxUrl);

        // Préparer les données avec le token CSRF
        var postData = {
            reason: reason,
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        };

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                console.log('AJAX response received:', response);
                if (response.success) {
                    $('#rejectAppointmentModal').modal('hide');
                    alert_float('success', 'Demande de rendez-vous refusée avec succès');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message || 'Erreur lors du refus');
                    $btn.prop('disabled', false).html('<i class="fa fa-ban"></i> Confirmer le refus');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.error('Response:', xhr.responseText);
                alert_float('danger', 'Erreur de communication avec le serveur: ' + error);
                $btn.prop('disabled', false).html('<i class="fa fa-ban"></i> Confirmer le refus');
            }
        });
    });

    // Réinitialiser quand le modal est fermé
    $('#rejectAppointmentModal').on('hidden.bs.modal', function() {
        console.log('Modal closed, resetting consultation ID');
        consultationToReject = null;
        $('#confirm-reject-btn').prop('disabled', false).html('<i class="fa fa-ban"></i> Confirmer le refus');
    });

    console.log('Reject appointment handlers attached successfully');
});
</script>

<?php init_tail(); ?>
