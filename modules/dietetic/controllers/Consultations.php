<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consultations extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Load Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic models
        $this->load->model('dietetic/dietetic_consultations_model');
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');

        if (!dietetic_has_permission('view')) {
            access_denied('dietetic');
        }
    }

    /**
     * List all consultations
     */
    public function index()
    {
        $data['title'] = _l('dietetic_consultations');
        $data['consultations'] = $this->dietetic_consultations_model->get_all();

        $this->load->view('admin/consultations/list', $data);
    }

    /**
     * View consultation detail
     *
     * @param int $id
     */
    public function view($id)
    {
        $data['consultation'] = $this->dietetic_consultations_model->get($id);

        if (!$data['consultation']) {
            show_404();
        }

        $data['title'] = _l('dietetic_consultation') . ' #' . $id;
        $data['patient'] = $this->dietetic_patients_model->get($data['consultation']->patient_id);

        // Get primary contact for patient
        $this->load->model('dietetic/dietetic_notifications_model');
        $data['primary_contact'] = $this->dietetic_notifications_model->get_client_primary_contact($data['patient']->client_id);

        $this->load->view('admin/consultations/view', $data);
    }

    /**
     * Create new consultation
     */
    public function create()
    {
        if (!dietetic_has_permission('create')) {
            access_denied('dietetic');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Remove fields that don't exist in database
            unset($data['height_patient']); // Used only for BMI calculation in form

            // Set dietitian if not set
            if (!isset($data['dietitian_id'])) {
                $data['dietitian_id'] = get_staff_user_id();
            }

            // Set default duration
            if (!isset($data['duration']) || empty($data['duration'])) {
                $data['duration'] = dietetic_get_option('default_consultation_duration', 60);
            }

            $consultation_id = $this->dietetic_consultations_model->add($data);

            if ($consultation_id) {
                // Send notification to patient
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') && isset($data['patient_id'])) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get consultation info
                        $consultation = $this->dietetic_consultations_model->get($consultation_id);

                        // Get dietitian info
                        $dietitian_id = $data['dietitian_id'] ?? get_staff_user_id();
                        $dietitian = $this->staff_model->get($dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        // Send notification
                        $this->dietetic_notifications_model->notify_consultation_scheduled(
                            $data['patient_id'],
                            $data['consultation_date'],
                            $data['consultation_time'] ?? null,
                            $dietitian_name,
                            $data['consultation_type'] ?? 'Consultation'
                        );
                    }
                } catch (Exception $e) {
                    log_activity('Consultation notification error: ' . $e->getMessage());
                }

                set_alert('success', _l('added_successfully'));
                redirect(admin_url('dietetic/consultations/view/' . $consultation_id));
            } else {
                set_alert('danger', _l('dietetic_error_add_failed'));
            }
        }

        $data['title'] = _l('dietetic_new_consultation');

        // Get patients
        $data['patients'] = $this->dietetic_patients_model->get_all();

        // Get staff members
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/consultations/form', $data);
    }

    /**
     * Edit consultation
     *
     * @param int $id
     */
    public function edit($id)
    {
        if (!dietetic_has_permission('edit')) {
            access_denied('dietetic');
        }

        $data['consultation'] = $this->dietetic_consultations_model->get($id);

        if (!$data['consultation']) {
            show_404();
        }

        if ($this->input->post()) {
            $update_data = $this->input->post();

            // Remove fields that don't exist in database
            unset($update_data['height_patient']); // Used only for BMI calculation in form

            // Check if consultation was cancelled
            $was_cancelled = (isset($update_data['status']) &&
                             $update_data['status'] == 'cancelled' &&
                             $data['consultation']->status != 'cancelled');

            if ($this->dietetic_consultations_model->update($id, $update_data)) {
                // Send appropriate notification
                try {
                    if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences') && $data['consultation']->patient_id) {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get dietitian info
                        $dietitian = $this->staff_model->get($data['consultation']->dietitian_id);
                        $dietitian_name = $dietitian ? ($dietitian->firstname . ' ' . $dietitian->lastname) : 'Votre diététicien';

                        if ($was_cancelled) {
                            // Notify cancellation
                            $this->dietetic_notifications_model->notify_consultation_cancelled(
                                $data['consultation']->patient_id,
                                $data['consultation']->consultation_date,
                                $dietitian_name,
                                $update_data['cancellation_reason'] ?? ''
                            );
                        } else {
                            // Notify modification (date/time changed)
                            $date_changed = isset($update_data['consultation_date']) &&
                                          $update_data['consultation_date'] != $data['consultation']->consultation_date;
                            $time_changed = isset($update_data['consultation_time']) &&
                                          $update_data['consultation_time'] != $data['consultation']->consultation_time;

                            if ($date_changed || $time_changed) {
                                $this->dietetic_notifications_model->notify_consultation_scheduled(
                                    $data['consultation']->patient_id,
                                    $update_data['consultation_date'] ?? $data['consultation']->consultation_date,
                                    $update_data['consultation_time'] ?? $data['consultation']->consultation_time,
                                    $dietitian_name,
                                    $update_data['consultation_type'] ?? $data['consultation']->consultation_type
                                );
                            }
                        }
                    }
                } catch (Exception $e) {
                    log_activity('Consultation update notification error: ' . $e->getMessage());
                }

                set_alert('success', _l('updated_successfully'));
                redirect(admin_url('dietetic/consultations/view/' . $id));
            } else {
                set_alert('danger', _l('dietetic_error_update_failed'));
            }
        }

        $data['title'] = _l('dietetic_edit_consultation');

        // Get patients
        $data['patients'] = $this->dietetic_patients_model->get_all();

        // Get staff members
        $data['staff'] = $this->staff_model->get();

        $this->load->view('admin/consultations/form', $data);
    }

    /**
     * Delete consultation
     *
     * @param int $id
     */
    public function delete($id)
    {
        if (!dietetic_has_permission('delete')) {
            ajax_access_denied();
        }

        if ($this->dietetic_consultations_model->delete($id)) {
            echo json_encode(['success' => true, 'message' => _l('deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_delete_failed')]);
        }
    }

    /**
     * Get consultations for calendar view
     */
    public function calendar()
    {
        $data['title'] = _l('dietetic_consultation_calendar');

        $this->load->view('admin/consultations/calendar', $data);
    }

    /**
     * Get consultations for FullCalendar
     */
    public function get_calendar_data()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');

        $consultations = $this->dietetic_consultations_model->get_by_date_range($start, $end);

        $events = [];

        foreach ($consultations as $consultation) {
            $color = '#28a745'; // green for scheduled

            if ($consultation->status == 'completed') {
                $color = '#007bff'; // blue
            } elseif ($consultation->status == 'cancelled') {
                $color = '#dc3545'; // red
            } elseif ($consultation->status == 'no_show') {
                $color = '#ffc107'; // yellow
            }

            $events[] = [
                'id'              => $consultation->id,
                'title'           => $consultation->client_name,
                'start'           => $consultation->consultation_date,
                'end'             => date('Y-m-d H:i:s', strtotime($consultation->consultation_date . ' +' . $consultation->duration . ' minutes')),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'url'             => admin_url('dietetic/consultations/view/' . $consultation->id),
            ];
        }

        echo json_encode($events);
    }

    /**
     * Mark consultation as completed
     *
     * @param int $id
     */
    public function mark_completed($id)
    {
        if (!dietetic_has_permission('edit')) {
            ajax_access_denied();
        }

        if ($this->dietetic_consultations_model->update($id, ['status' => 'completed'])) {
            echo json_encode(['success' => true, 'message' => _l('dietetic_consultation_marked_completed')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('dietetic_error_update_failed')]);
        }
    }
}
