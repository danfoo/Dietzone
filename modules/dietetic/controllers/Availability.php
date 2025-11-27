<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Dietitian Availability Controller
 *
 * Manages dietitian schedules and available time slots
 */
class Availability extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_availability_model');
        $this->load->model('staff_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * Main availability management page
     * Shows all dietitians and their schedules
     */
    public function index()
    {
        // Get dietitian_id from URL or default to current staff
        $dietitian_id = $this->input->get('dietitian_id');

        if (!$dietitian_id) {
            $dietitian_id = get_staff_user_id();
        }

        $data['title'] = _l('dietetic_availability');
        $data['dietitian_id'] = $dietitian_id;

        // Get all staff members (dietitians)
        $data['dietitians'] = $this->staff_model->get('', ['active' => 1]);

        // Get current dietitian info
        $data['current_dietitian'] = $this->staff_model->get($dietitian_id);

        // Get availability slots for current dietitian
        $data['availability_slots'] = $this->dietetic_availability_model->get_by_dietitian($dietitian_id, false);

        // Group by day of week
        $data['slots_by_day'] = [];
        foreach ($data['availability_slots'] as $slot) {
            $data['slots_by_day'][$slot->day_of_week][] = $slot;
        }

        // Days of week labels
        $data['days_of_week'] = [
            0 => 'Dimanche',
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi'
        ];

        $this->load->view('admin/availability/manage', $data);
    }

    /**
     * Add new availability slot
     */
    public function add()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Validate times
            if ($data['start_time'] >= $data['end_time']) {
                set_alert('danger', 'L\'heure de fin doit être après l\'heure de début');
                redirect(admin_url('dietetic/availability?dietitian_id=' . $data['dietitian_id']));
                return;
            }

            $id = $this->dietetic_availability_model->add($data);

            if ($id) {
                set_alert('success', 'Disponibilité ajoutée avec succès');
            } else {
                set_alert('danger', 'Erreur lors de l\'ajout');
            }

            redirect(admin_url('dietetic/availability?dietitian_id=' . $data['dietitian_id']));
        }
    }

    /**
     * Update availability slot
     */
    public function update($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Validate times
            if (isset($data['start_time']) && isset($data['end_time'])) {
                if ($data['start_time'] >= $data['end_time']) {
                    set_alert('danger', 'L\'heure de fin doit être après l\'heure de début');
                    redirect(admin_url('dietetic/availability?dietitian_id=' . $data['dietitian_id']));
                    return;
                }
            }

            if ($this->dietetic_availability_model->update($id, $data)) {
                set_alert('success', 'Disponibilité mise à jour');
            } else {
                set_alert('danger', 'Erreur lors de la mise à jour');
            }

            redirect(admin_url('dietetic/availability?dietitian_id=' . $data['dietitian_id']));
        }
    }

    /**
     * Delete availability slot
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            access_denied('dietetic');
        }

        $slot = $this->dietetic_availability_model->get($id);

        if ($this->dietetic_availability_model->delete($id)) {
            set_alert('success', 'Disponibilité supprimée');
        } else {
            set_alert('danger', 'Erreur lors de la suppression');
        }

        redirect(admin_url('dietetic/availability?dietitian_id=' . ($slot->dietitian_id ?? '')));
    }

    /**
     * Toggle active status
     */
    public function toggle_active($id)
    {
        if (!dietetic_has_permission('edit')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $slot = $this->dietetic_availability_model->get($id);

        if ($slot) {
            $new_status = $slot->is_active ? 0 : 1;
            $success = $this->dietetic_availability_model->update($id, ['is_active' => $new_status]);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'new_status' => $new_status
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Slot not found']);
        }
    }

    /**
     * API: Check availability for a specific datetime
     */
    public function check()
    {
        header('Content-Type: application/json');

        $dietitian_id = $this->input->post('dietitian_id') ?? $this->input->get('dietitian_id');
        $datetime = $this->input->post('datetime') ?? $this->input->get('datetime');
        $duration = $this->input->post('duration') ?? $this->input->get('duration') ?? 60;
        $exclude_id = $this->input->post('exclude_consultation_id') ?? $this->input->get('exclude_consultation_id');

        if (!$dietitian_id || !$datetime) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        $result = $this->dietetic_availability_model->check_availability(
            $dietitian_id,
            $datetime,
            $duration,
            $exclude_id
        );

        echo json_encode([
            'success' => true,
            'available' => $result['available'],
            'reason' => $result['reason'],
            'conflicts' => $result['conflicts']
        ]);
    }

    /**
     * API: Get available slots for a date
     */
    public function get_slots()
    {
        header('Content-Type: application/json');

        $dietitian_id = $this->input->get('dietitian_id');
        $date = $this->input->get('date');
        $consultation_type_id = $this->input->get('consultation_type_id');

        if (!$dietitian_id || !$date) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
            return;
        }

        $slots = $this->dietetic_availability_model->get_available_slots(
            $dietitian_id,
            $date,
            $consultation_type_id
        );

        echo json_encode([
            'success' => true,
            'date' => $date,
            'slots' => $slots
        ]);
    }

    /**
     * Quick setup wizard for new dietitian
     * Sets up a default weekly schedule
     */
    public function quick_setup()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        $dietitian_id = $this->input->get('dietitian_id') ?? get_staff_user_id();

        if ($this->input->post()) {
            $start_time = $this->input->post('start_time');
            $end_time = $this->input->post('end_time');
            $lunch_break = $this->input->post('lunch_break');
            $working_days = $this->input->post('working_days'); // Array of day numbers

            if (!$working_days || empty($working_days)) {
                set_alert('danger', 'Veuillez sélectionner au moins un jour de travail');
                redirect(admin_url('dietetic/availability/quick_setup?dietitian_id=' . $dietitian_id));
                return;
            }

            $added_count = 0;

            foreach ($working_days as $day) {
                if ($lunch_break) {
                    // Morning slot
                    $morning_data = [
                        'dietitian_id' => $dietitian_id,
                        'day_of_week' => $day,
                        'start_time' => $start_time,
                        'end_time' => '12:00:00',
                        'slot_duration' => 60,
                        'is_active' => 1
                    ];

                    if ($this->dietetic_availability_model->add($morning_data)) {
                        $added_count++;
                    }

                    // Afternoon slot
                    $afternoon_data = [
                        'dietitian_id' => $dietitian_id,
                        'day_of_week' => $day,
                        'start_time' => '14:00:00',
                        'end_time' => $end_time,
                        'slot_duration' => 60,
                        'is_active' => 1
                    ];

                    if ($this->dietetic_availability_model->add($afternoon_data)) {
                        $added_count++;
                    }
                } else {
                    // Full day slot
                    $data = [
                        'dietitian_id' => $dietitian_id,
                        'day_of_week' => $day,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'slot_duration' => 60,
                        'is_active' => 1
                    ];

                    if ($this->dietetic_availability_model->add($data)) {
                        $added_count++;
                    }
                }
            }

            set_alert('success', $added_count . ' créneaux de disponibilité créés');
            redirect(admin_url('dietetic/availability?dietitian_id=' . $dietitian_id));
        }

        $data['title'] = 'Configuration Rapide - Disponibilités';
        $data['dietitian_id'] = $dietitian_id;
        $data['dietitian'] = $this->staff_model->get($dietitian_id);

        $this->load->view('admin/availability/quick_setup', $data);
    }
}
