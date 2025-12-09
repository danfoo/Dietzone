<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'book_appointment';
$page_title = 'Prendre Rendez-vous';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Page header */
.page-header-mobile {
    margin-bottom: 25px;
}

.page-header-mobile h1 {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-mobile h1 i {
    color: #01807B;
}

.page-header-mobile p {
    color: #6c757d;
    font-size: 15px;
    margin: 0;
}

/* Booking container */
.booking-container {
    background: white;
    border-radius: 16px;
    padding: 24px 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

/* Steps indicator */
.booking-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    position: relative;
}

.booking-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 0;
}

.booking-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
}

.booking-step.active .step-number {
    background: #01807B;
    color: white;
}

.booking-step.completed .step-number {
    background: #28a745;
    color: white;
}

.step-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
}

.booking-step.active .step-label {
    color: #01807B;
    font-weight: 600;
}

/* Section styles */
.booking-section {
    display: none;
}

.booking-section.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 16px;
}

/* Dietitian selection */
.dietitian-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 16px;
}

.dietitian-card:hover {
    border-color: #01807B;
    background: #f8f9fa;
}

.dietitian-card.selected {
    border-color: #01807B;
    background: #e8f4f4;
}

.dietitian-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #01807B;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
    flex-shrink: 0;
}

.dietitian-info h3 {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px 0;
}

.dietitian-info p {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
}

.dietitian-badge {
    background: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

/* Consultation type selection */
.consultation-type-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

@media (min-width: 576px) {
    .consultation-type-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.consultation-type-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.consultation-type-card:hover {
    border-color: #01807B;
    background: #f8f9fa;
}

.consultation-type-card.selected {
    border-color: #01807B;
    background: #e8f4f4;
}

.consultation-type-icon {
    font-size: 32px;
    color: #01807B;
    margin-bottom: 8px;
}

.consultation-type-card h4 {
    font-size: 15px;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px 0;
}

.consultation-type-card p {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
}

.consultation-duration {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-top: 8px;
}

/* Date selection */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-top: 16px;
}

.calendar-day {
    aspect-ratio: 1;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 13px;
}

.calendar-day:hover:not(.disabled) {
    border-color: #01807B;
    background: #f8f9fa;
}

.calendar-day.selected {
    border-color: #01807B;
    background: #01807B;
    color: white;
}

.calendar-day.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.calendar-day .day-number {
    font-weight: 600;
    font-size: 14px;
}

.calendar-day .day-name {
    font-size: 10px;
    color: #6c757d;
}

.calendar-day.selected .day-name {
    color: white;
}

/* Time slots */
.time-slots-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 16px;
}

@media (min-width: 576px) {
    .time-slots-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.time-slot {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    color: #212529;
}

.time-slot:hover {
    border-color: #01807B;
    background: #f8f9fa;
}

.time-slot.selected {
    border-color: #01807B;
    background: #01807B;
    color: white;
}

/* Notes textarea */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;
}

.form-group textarea {
    width: 100%;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
    transition: border-color 0.3s ease;
}

.form-group textarea:focus {
    outline: none;
    border-color: #01807B;
}

/* Action buttons */
.booking-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-booking {
    flex: 1;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-primary-booking {
    background: #01807B;
    color: white;
}

.btn-primary-booking:hover:not(:disabled) {
    background: #016963;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
}

.btn-secondary-booking {
    background: #e9ecef;
    color: #495057;
}

.btn-secondary-booking:hover {
    background: #dee2e6;
}

.btn-booking:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Loading state */
.loading-spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spinner 0.8s linear infinite;
}

@keyframes spinner {
    to {
        transform: rotate(360deg);
    }
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    color: #dee2e6;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
}

/* Confirmation summary */
.confirmation-summary {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #e9ecef;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-icon {
    width: 40px;
    height: 40px;
    background: #01807B;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-content h4 {
    font-size: 14px;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px 0;
}

.summary-content p {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
}

/* Alert messages */
.alert-booking {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-info {
    background: #cfe2ff;
    color: #084298;
    border: 1px solid #b6d4fe;
}

.alert-success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.alert-error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}

.hidden {
    display: none !important;
}
</style>

<div class="page-header-mobile">
    <h1>
        <i class="fa fa-calendar-check"></i>
        Prendre Rendez-vous
    </h1>
    <p>Réservez une consultation avec votre diététicien</p>
</div>

<div class="booking-container">
    <!-- Steps indicator -->
    <div class="booking-steps">
        <div class="booking-step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-label">Diététicien</div>
        </div>
        <div class="booking-step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-label">Type</div>
        </div>
        <div class="booking-step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-label">Date</div>
        </div>
        <div class="booking-step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-label">Heure</div>
        </div>
        <div class="booking-step" data-step="5">
            <div class="step-number">5</div>
            <div class="step-label">Confirmer</div>
        </div>
    </div>

    <!-- Alert messages -->
    <div id="bookingAlert" class="alert-booking hidden">
        <i class="fa fa-info-circle"></i>
        <span id="bookingAlertMessage"></span>
    </div>

    <!-- Step 1: Select Dietitian -->
    <div class="booking-section active" id="step1">
        <h2 class="section-title">Choisissez votre diététicien</h2>

        <?php if (empty($dietitians)): ?>
            <div class="empty-state">
                <i class="fa fa-user-md"></i>
                <h3>Aucun diététicien assigné</h3>
                <p>Vous n'avez pas encore de diététicien assigné. Veuillez contacter l'administration.</p>
            </div>
        <?php else: ?>
            <div id="dietitiansList">
                <?php foreach ($dietitians as $dietitian): ?>
                    <div class="dietitian-card" data-dietitian-id="<?php echo $dietitian->dietitian_id; ?>">
                        <div class="dietitian-avatar">
                            <?php echo strtoupper(substr($dietitian->firstname, 0, 1) . substr($dietitian->lastname, 0, 1)); ?>
                        </div>
                        <div class="dietitian-info">
                            <h3>
                                <?php echo htmlspecialchars($dietitian->firstname . ' ' . $dietitian->lastname); ?>
                                <?php if ($dietitian->is_primary): ?>
                                    <span class="dietitian-badge">Principal</span>
                                <?php endif; ?>
                            </h3>
                            <p>
                                <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($dietitian->email); ?>
                            </p>
                            <?php if ($dietitian->phonenumber): ?>
                                <p>
                                    <i class="fa fa-phone"></i> <?php echo htmlspecialchars($dietitian->phonenumber); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="booking-actions">
                <button type="button" class="btn-booking btn-primary-booking" id="btnNextStep1" disabled>
                    Suivant <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Step 2: Select Consultation Type -->
    <div class="booking-section" id="step2">
        <h2 class="section-title">Type de consultation</h2>

        <div class="consultation-type-grid">
            <?php foreach ($consultation_types as $type): ?>
                <div class="consultation-type-card" data-type-id="<?php echo $type->id; ?>" data-duration="<?php echo $type->duration; ?>">
                    <div class="consultation-type-icon">
                        <i class="fa <?php echo $type->icon ?? 'fa-stethoscope'; ?>"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($type->name); ?></h4>
                    <?php if ($type->description): ?>
                        <p><?php echo htmlspecialchars($type->description); ?></p>
                    <?php endif; ?>
                    <span class="consultation-duration">
                        <i class="fa fa-clock"></i> <?php echo $type->duration; ?> min
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="booking-actions">
            <button type="button" class="btn-booking btn-secondary-booking" id="btnPrevStep2">
                <i class="fa fa-arrow-left"></i> Retour
            </button>
            <button type="button" class="btn-booking btn-primary-booking" id="btnNextStep2" disabled>
                Suivant <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Select Date -->
    <div class="booking-section" id="step3">
        <h2 class="section-title">Choisissez une date</h2>

        <div id="datesLoading" class="text-center" style="padding: 40px;">
            <div class="loading-spinner" style="border-color: #01807B; border-top-color: transparent;"></div>
            <p style="margin-top: 16px; color: #6c757d;">Chargement des dates disponibles...</p>
        </div>

        <div id="datesContainer" class="hidden">
            <div id="availableDates"></div>
        </div>

        <div class="booking-actions">
            <button type="button" class="btn-booking btn-secondary-booking" id="btnPrevStep3">
                <i class="fa fa-arrow-left"></i> Retour
            </button>
            <button type="button" class="btn-booking btn-primary-booking" id="btnNextStep3" disabled>
                Suivant <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 4: Select Time Slot -->
    <div class="booking-section" id="step4">
        <h2 class="section-title">Choisissez un horaire</h2>

        <div id="slotsLoading" class="text-center" style="padding: 40px;">
            <div class="loading-spinner" style="border-color: #01807B; border-top-color: transparent;"></div>
            <p style="margin-top: 16px; color: #6c757d;">Chargement des créneaux disponibles...</p>
        </div>

        <div id="slotsContainer" class="hidden">
            <div class="time-slots-grid" id="availableSlots"></div>
        </div>

        <div class="booking-actions">
            <button type="button" class="btn-booking btn-secondary-booking" id="btnPrevStep4">
                <i class="fa fa-arrow-left"></i> Retour
            </button>
            <button type="button" class="btn-booking btn-primary-booking" id="btnNextStep4" disabled>
                Suivant <i class="fa fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 5: Confirmation -->
    <div class="booking-section" id="step5">
        <h2 class="section-title">Confirmation</h2>

        <div class="confirmation-summary">
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fa fa-user-md"></i>
                </div>
                <div class="summary-content">
                    <h4>Diététicien</h4>
                    <p id="summaryDietitian">-</p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fa fa-stethoscope"></i>
                </div>
                <div class="summary-content">
                    <h4>Type de consultation</h4>
                    <p id="summaryType">-</p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="summary-content">
                    <h4>Date</h4>
                    <p id="summaryDate">-</p>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon">
                    <i class="fa fa-clock"></i>
                </div>
                <div class="summary-content">
                    <h4>Heure</h4>
                    <p id="summaryTime">-</p>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="bookingNotes">Notes ou raison de la consultation (optionnel)</label>
            <textarea id="bookingNotes" placeholder="Décrivez brièvement la raison de votre consultation..."></textarea>
        </div>

        <div class="booking-actions">
            <button type="button" class="btn-booking btn-secondary-booking" id="btnPrevStep5">
                <i class="fa fa-arrow-left"></i> Retour
            </button>
            <button type="button" class="btn-booking btn-primary-booking" id="btnConfirmBooking">
                <i class="fa fa-check"></i> Confirmer le rendez-vous
            </button>
        </div>
    </div>
</div>

<script>
// Booking state
const bookingState = {
    currentStep: 1,
    dietitianId: null,
    dietitianName: '',
    consultationTypeId: null,
    consultationTypeName: '',
    consultationDuration: 60,
    selectedDate: null,
    selectedDateDisplay: '',
    selectedTime: null,
    selectedTimeDisplay: '',
    availableDates: [],
    availableSlots: []
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeBooking();
});

function initializeBooking() {
    // Step 1: Dietitian selection
    document.querySelectorAll('.dietitian-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.dietitian-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');

            bookingState.dietitianId = this.dataset.dietitianId;
            bookingState.dietitianName = this.querySelector('.dietitian-info h3').textContent.trim();

            document.getElementById('btnNextStep1').disabled = false;
        });
    });

    // Step 2: Consultation type selection
    document.querySelectorAll('.consultation-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.consultation-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');

            bookingState.consultationTypeId = this.dataset.typeId;
            bookingState.consultationTypeName = this.querySelector('h4').textContent.trim();
            bookingState.consultationDuration = this.dataset.duration;

            document.getElementById('btnNextStep2').disabled = false;
        });
    });

    // Navigation buttons
    document.getElementById('btnNextStep1')?.addEventListener('click', () => goToStep(2));
    document.getElementById('btnNextStep2')?.addEventListener('click', () => {
        goToStep(3);
        loadAvailableDates();
    });
    document.getElementById('btnNextStep3')?.addEventListener('click', () => {
        goToStep(4);
        loadAvailableSlots();
    });
    document.getElementById('btnNextStep4')?.addEventListener('click', () => {
        goToStep(5);
        updateConfirmationSummary();
    });

    document.getElementById('btnPrevStep2')?.addEventListener('click', () => goToStep(1));
    document.getElementById('btnPrevStep3')?.addEventListener('click', () => goToStep(2));
    document.getElementById('btnPrevStep4')?.addEventListener('click', () => goToStep(3));
    document.getElementById('btnPrevStep5')?.addEventListener('click', () => goToStep(4));

    document.getElementById('btnConfirmBooking')?.addEventListener('click', submitBooking);
}

function goToStep(step) {
    // Hide all sections
    document.querySelectorAll('.booking-section').forEach(section => {
        section.classList.remove('active');
    });

    // Show target section
    document.getElementById('step' + step).classList.add('active');

    // Update steps indicator
    document.querySelectorAll('.booking-step').forEach(stepEl => {
        const stepNum = parseInt(stepEl.dataset.step);
        stepEl.classList.remove('active', 'completed');

        if (stepNum < step) {
            stepEl.classList.add('completed');
        } else if (stepNum === step) {
            stepEl.classList.add('active');
        }
    });

    bookingState.currentStep = step;

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function loadAvailableDates() {
    const datesLoading = document.getElementById('datesLoading');
    const datesContainer = document.getElementById('datesContainer');

    datesLoading.classList.remove('hidden');
    datesContainer.classList.add('hidden');

    fetch('<?php echo site_url('dietetic/portal/get_available_dates'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            dietitian_id: bookingState.dietitianId,
            consultation_type_id: bookingState.consultationTypeId
        })
    })
    .then(response => response.json())
    .then(data => {
        datesLoading.classList.add('hidden');

        if (data.success && data.dates.length > 0) {
            bookingState.availableDates = data.dates;
            renderAvailableDates(data.dates);
            datesContainer.classList.remove('hidden');
        } else {
            showAlert('info', 'Aucune date disponible pour le moment. Veuillez réessayer plus tard.');
        }
    })
    .catch(error => {
        datesLoading.classList.add('hidden');
        showAlert('error', 'Erreur lors du chargement des dates disponibles.');
        console.error('Error:', error);
    });
}

function renderAvailableDates(dates) {
    const container = document.getElementById('availableDates');
    container.innerHTML = '';

    // Group dates by week
    const weeks = [];
    let currentWeek = [];

    dates.forEach((dateObj, index) => {
        currentWeek.push(dateObj);

        if (currentWeek.length === 7 || index === dates.length - 1) {
            weeks.push(currentWeek);
            currentWeek = [];
        }
    });

    // Render each week
    weeks.forEach(week => {
        const weekContainer = document.createElement('div');
        weekContainer.className = 'calendar-grid';
        weekContainer.style.marginBottom = '16px';

        week.forEach(dateObj => {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'calendar-day';
            dayDiv.dataset.date = dateObj.date;
            dayDiv.innerHTML = `
                <div class="day-name">${dateObj.day_name.substring(0, 3)}</div>
                <div class="day-number">${dateObj.display.split('/')[0]}</div>
            `;

            dayDiv.addEventListener('click', function() {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');

                bookingState.selectedDate = this.dataset.date;
                bookingState.selectedDateDisplay = dateObj.display + ' (' + dateObj.day_name + ')';

                document.getElementById('btnNextStep3').disabled = false;
            });

            weekContainer.appendChild(dayDiv);
        });

        container.appendChild(weekContainer);
    });
}

function loadAvailableSlots() {
    const slotsLoading = document.getElementById('slotsLoading');
    const slotsContainer = document.getElementById('slotsContainer');

    slotsLoading.classList.remove('hidden');
    slotsContainer.classList.add('hidden');

    fetch('<?php echo site_url('dietetic/portal/get_available_slots'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            dietitian_id: bookingState.dietitianId,
            date: bookingState.selectedDate,
            consultation_type_id: bookingState.consultationTypeId
        })
    })
    .then(response => response.json())
    .then(data => {
        slotsLoading.classList.add('hidden');

        if (data.success && data.slots.length > 0) {
            bookingState.availableSlots = data.slots;
            renderAvailableSlots(data.slots);
            slotsContainer.classList.remove('hidden');
        } else {
            showAlert('info', 'Aucun créneau disponible pour cette date.');
        }
    })
    .catch(error => {
        slotsLoading.classList.add('hidden');
        showAlert('error', 'Erreur lors du chargement des créneaux disponibles.');
        console.error('Error:', error);
    });
}

function renderAvailableSlots(slots) {
    const container = document.getElementById('availableSlots');
    container.innerHTML = '';

    slots.forEach(slot => {
        const slotDiv = document.createElement('div');
        slotDiv.className = 'time-slot';
        slotDiv.dataset.time = slot.start_time;
        slotDiv.textContent = slot.start_time + ' - ' + slot.end_time;

        slotDiv.addEventListener('click', function() {
            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');

            bookingState.selectedTime = this.dataset.time;
            bookingState.selectedTimeDisplay = this.textContent;

            document.getElementById('btnNextStep4').disabled = false;
        });

        container.appendChild(slotDiv);
    });
}

function updateConfirmationSummary() {
    document.getElementById('summaryDietitian').textContent = bookingState.dietitianName;
    document.getElementById('summaryType').textContent = bookingState.consultationTypeName + ' (' + bookingState.consultationDuration + ' min)';
    document.getElementById('summaryDate').textContent = bookingState.selectedDateDisplay;
    document.getElementById('summaryTime').textContent = bookingState.selectedTimeDisplay;
}

function submitBooking() {
    const btn = document.getElementById('btnConfirmBooking');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<div class="loading-spinner"></div> Envoi en cours...';

    const notes = document.getElementById('bookingNotes').value;

    fetch('<?php echo site_url('dietetic/portal/submit_appointment_request'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            dietitian_id: bookingState.dietitianId,
            date: bookingState.selectedDate,
            time: bookingState.selectedTime,
            consultation_type_id: bookingState.consultationTypeId,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);

            // Redirect to consultations page after 2 seconds
            setTimeout(() => {
                window.location.href = '<?php echo site_url('dietetic/portal/consultations'); ?>';
            }, 2000);
        } else {
            showAlert('error', data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        showAlert('error', 'Erreur lors de l\'envoi de la demande.');
        btn.disabled = false;
        btn.innerHTML = originalText;
        console.error('Error:', error);
    });
}

function showAlert(type, message) {
    const alert = document.getElementById('bookingAlert');
    const alertMessage = document.getElementById('bookingAlertMessage');

    alert.className = 'alert-booking alert-' + type;
    alertMessage.textContent = message;
    alert.classList.remove('hidden');

    // Auto-hide after 5 seconds for success messages
    if (type === 'success') {
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 5000);
    }

    // Scroll to alert
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
