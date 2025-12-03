<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_consultations_model extends App_Model
{
    private $table = 'dietic_consultations';

    public function __construct()
    {
        parent::__construct();

        // Load required Perfex models
        $this->load->model('clients_model');
        $this->load->model('staff_model');

        // Load dietetic helper for permissions
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Get consultation by ID
     *
     * @param int $id
     * @param bool $check_access If true, verify user has access to this consultation
     * @return object|null
     */
    public function get($id, $check_access = true)
    {
        $this->db->select('cons.*, ' .
            'c.company as client_name, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name');
        $this->db->from(db_prefix() . $this->table . ' cons');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = cons.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = cons.dietitian_id', 'left');
        $this->db->where('cons.id', $id);

        $consultation = $this->db->get()->row();

        if ($consultation && $check_access) {
            // Check access permissions if not admin
            if (!dietetic_can_access_patient($consultation->patient_id)) {
                return null;
            }
        }

        return $consultation;
    }

    /**
     * Get all consultations with filters
     *
     * @param array $where
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function get_all($where = [], $limit = null, $offset = null)
    {
        $this->db->select('cons.*, ' .
            'c.company as client_name, ' .
            'CONCAT(s.firstname, " ", s.lastname) as dietitian_name', false);
        $this->db->from(db_prefix() . $this->table . ' cons');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = cons.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = p.client_id', 'left');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = cons.dietitian_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions using new many-to-many system
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            // Use new permission system
            dietetic_apply_dietitian_filter($this->db, 'pd');
        } else {
            // Fallback to old system if table doesn't exist yet
            if (!is_admin()) {
                $this->db->where('cons.dietitian_id', get_staff_user_id());
            }
        }

        $this->db->group_by('cons.id'); // Group by to avoid duplicates from join
        $this->db->order_by('cons.consultation_date', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Count all consultations with filters
     *
     * @param array $where
     * @return int
     */
    public function count_all($where = [])
    {
        $this->db->from(db_prefix() . $this->table . ' cons');
        $this->db->join(db_prefix() . 'dietic_patients p', 'p.id = cons.patient_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Apply staff permissions
        if ($this->db->table_exists(db_prefix() . 'dietic_patient_dietitians')) {
            dietetic_apply_dietitian_filter($this->db, 'pd');
        } else {
            if (!is_admin()) {
                $this->db->where('cons.dietitian_id', get_staff_user_id());
            }
        }

        return $this->db->count_all_results();
    }

    /**
     * Get consultations by status
     *
     * @param string $status
     * @return array
     */
    public function get_by_status($status)
    {
        return $this->get_all(['cons.status' => $status]);
    }

    /**
     * Get consultations by patient
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_by_patient($patient_id, $limit = null)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access consultations for Patient ID ' . $patient_id);
            return [];
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('consultation_date', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get upcoming consultations for a specific patient
     * Retourne uniquement les consultations futures (scheduled et date future)
     *
     * @param int $patient_id
     * @param int $limit
     * @return array
     */
    public function get_upcoming_by_patient($patient_id, $limit = 10)
    {
        // Check access permissions
        if (!dietetic_can_access_patient($patient_id)) {
            log_activity('Unauthorized attempt to access consultations for Patient ID ' . $patient_id);
            return [];
        }

        $this->db->where('patient_id', $patient_id);
        $this->db->where('status', 'scheduled');
        $this->db->where('consultation_date >=', date('Y-m-d H:i:s'));
        $this->db->order_by('consultation_date', 'ASC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get upcoming consultations
     *
     * @param int $limit
     * @return array
     */
    public function get_upcoming($limit = 10)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as client_name, ' .
            'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as dietitian_name');
        $this->db->join(db_prefix() . 'dietic_patients', db_prefix() . 'dietic_patients.id = ' . db_prefix() . $this->table . '.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'dietic_patients.client_id', 'left');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . $this->table . '.dietitian_id', 'left');
        $this->db->where(db_prefix() . $this->table . '.consultation_date >=', date('Y-m-d H:i:s'));
        $this->db->where(db_prefix() . $this->table . '.status', 'scheduled');

        if (!is_admin()) {
            $this->db->where(db_prefix() . $this->table . '.dietitian_id', get_staff_user_id());
        }

        $this->db->order_by(db_prefix() . $this->table . '.consultation_date', 'ASC');
        $this->db->limit($limit);

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new consultation
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        // Check access permissions to patient
        if (isset($data['patient_id']) && !dietetic_can_access_patient($data['patient_id'])) {
            log_activity('Unauthorized attempt to create consultation for Patient ID ' . $data['patient_id']);
            return false;
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            $consultation_id = $this->db->insert_id();

            // Create reminder for this consultation (wrapped in try-catch to prevent errors)
            if (isset($data['status']) && $data['status'] == 'scheduled') {
                try {
                    $this->create_consultation_reminder($consultation_id);
                } catch (Exception $e) {
                    log_activity('Consultation reminder creation failed [ID: ' . $consultation_id . ']: ' . $e->getMessage());
                }

                // Notify patient of new consultation
                if (isset($data['patient_id']) && isset($data['consultation_date'])) {
                    try {
                        $this->load->model('dietetic/dietetic_notifications_model');

                        // Get dietitian name
                        $dietitian_id = isset($data['dietitian_id']) ? $data['dietitian_id'] : get_staff_user_id();
                        $this->db->select('CONCAT(firstname, " ", lastname) as name');
                        $this->db->where('staffid', $dietitian_id);
                        $dietitian = $this->db->get(db_prefix() . 'staff')->row();
                        $dietitian_name = $dietitian ? $dietitian->name : 'Votre diététicien';

                        $consultation_time = isset($data['consultation_time']) ? $data['consultation_time'] : null;
                        $consultation_type = isset($data['consultation_type']) ? $data['consultation_type'] : 'Consultation';

                        $this->dietetic_notifications_model->notify_consultation_scheduled(
                            $data['patient_id'],
                            $data['consultation_date'],
                            $consultation_time,
                            $dietitian_name,
                            $consultation_type
                        );
                    } catch (Exception $e) {
                        log_activity('Consultation notification failed [ID: ' . $consultation_id . ']: ' . $e->getMessage());
                    }
                }
            }

            log_activity('New Consultation Created [ID: ' . $consultation_id . ']');
            return $consultation_id;
        }

        return false;
    }

    /**
     * Update consultation
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        // Get consultation to check access
        $consultation = $this->get($id);
        if (!$consultation) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($consultation->patient_id)) {
            log_activity('Unauthorized attempt to update consultation [ID: ' . $id . ']');
            return false;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);

        if ($this->db->update(db_prefix() . $this->table, $data)) {
            // Check if consultation was cancelled
            if (isset($data['status']) && $data['status'] == 'cancelled' && $consultation->status != 'cancelled') {
                // Notify patient of cancellation
                $this->load->model('dietetic/dietetic_notifications_model');

                // Get dietitian name
                $this->db->select('CONCAT(firstname, " ", lastname) as name');
                $this->db->where('staffid', $consultation->dietitian_id);
                $dietitian = $this->db->get(db_prefix() . 'staff')->row();
                $dietitian_name = $dietitian ? $dietitian->name : 'Votre diététicien';

                $reason = isset($data['cancellation_reason']) ? $data['cancellation_reason'] : '';

                $this->dietetic_notifications_model->notify_consultation_cancelled(
                    $consultation->patient_id,
                    $consultation->consultation_date,
                    $dietitian_name,
                    $reason
                );
            }

            // Update reminder if date or status changed
            if (isset($data['consultation_date']) || isset($data['status'])) {
                $this->update_consultation_reminder($id);
            }

            log_activity('Consultation Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete consultation
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Get consultation to check access
        $consultation = $this->get($id, false); // Don't check access yet, we'll do it manually
        if (!$consultation) {
            return false;
        }

        // Check access permissions
        if (!dietetic_can_access_patient($consultation->patient_id)) {
            log_activity('Unauthorized attempt to delete consultation [ID: ' . $id . ']');
            return false;
        }

        // Delete associated reminders (if table exists)
        if ($this->db->table_exists(db_prefix() . 'dietic_reminders')) {
            $this->db->where('reminder_type', 'appointment');
            $this->db->where('related_id', $id);
            $this->db->delete(db_prefix() . 'dietic_reminders');
        }

        $this->db->where('id', $id);
        if ($this->db->delete(db_prefix() . $this->table)) {
            log_activity('Consultation Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get consultations for a date range
     *
     * @param string $start_date
     * @param string $end_date
     * @param int $dietitian_id
     * @return array
     */
    public function get_by_date_range($start_date, $end_date, $dietitian_id = null)
    {
        $this->db->select(db_prefix() . $this->table . '.*, ' .
            db_prefix() . 'clients.company as client_name');
        $this->db->join(db_prefix() . 'dietic_patients', db_prefix() . 'dietic_patients.id = ' . db_prefix() . $this->table . '.patient_id', 'left');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'dietic_patients.client_id', 'left');
        $this->db->where(db_prefix() . $this->table . '.consultation_date >=', $start_date);
        $this->db->where(db_prefix() . $this->table . '.consultation_date <=', $end_date);

        if ($dietitian_id) {
            $this->db->where(db_prefix() . $this->table . '.dietitian_id', $dietitian_id);
        } elseif (!is_admin()) {
            $this->db->where(db_prefix() . $this->table . '.dietitian_id', get_staff_user_id());
        }

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get consultation statistics
     *
     * @return object
     */
    public function get_statistics()
    {
        $stats = new stdClass();

        // Total consultations
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->total_consultations = $this->db->count_all_results(db_prefix() . $this->table);

        // Scheduled consultations
        $this->db->where('status', 'scheduled');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->scheduled = $this->db->count_all_results(db_prefix() . $this->table);

        // Completed this month
        $this->db->where('status', 'completed');
        $this->db->where('MONTH(consultation_date)', date('m'));
        $this->db->where('YEAR(consultation_date)', date('Y'));
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $stats->completed_this_month = $this->db->count_all_results(db_prefix() . $this->table);

        // Average satisfaction
        $this->db->select_avg('satisfaction_score');
        $this->db->where('satisfaction_score IS NOT NULL');
        if (!is_admin()) {
            $this->db->where('dietitian_id', get_staff_user_id());
        }
        $result = $this->db->get(db_prefix() . $this->table)->row();
        $stats->avg_satisfaction = $result->satisfaction_score ? round($result->satisfaction_score, 1) : 0;

        return $stats;
    }

    /**
     * Create reminder for consultation
     *
     * @param int $consultation_id
     * @return bool
     */
    private function create_consultation_reminder($consultation_id)
    {
        // Check if reminders table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_reminders')) {
            log_activity('Reminders table does not exist, skipping reminder creation for consultation [ID: ' . $consultation_id . ']');
            return false;
        }

        $consultation = $this->get($consultation_id);

        if (!$consultation) {
            return false;
        }

        // Load patient to get contact info
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($consultation->patient_id);

        if (!$patient) {
            return false;
        }

        $hours_before = dietetic_get_option('reminder_before_appointment_hours', 24);
        $reminder_time = date('Y-m-d H:i:s', strtotime($consultation->consultation_date . " -{$hours_before} hours"));

        $message = sprintf(
            _l('dietetic_reminder_consultation_message'),
            $consultation->client_name,
            _dt($consultation->consultation_date)
        );

        $reminder_data = [
            'patient_id'     => $consultation->patient_id,
            'reminder_type'  => 'appointment',
            'related_id'     => $consultation_id,
            'send_via'       => 'both',
            'recipient'      => $patient->email,
            'subject'        => _l('dietetic_reminder_consultation_subject'),
            'message'        => $message,
            'scheduled_date' => $reminder_time,
            'status'         => 'pending',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'dietic_reminders', $reminder_data);

        return true;
    }

    /**
     * Update consultation reminder
     *
     * @param int $consultation_id
     * @return bool
     */
    private function update_consultation_reminder($consultation_id)
    {
        // Check if reminders table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_reminders')) {
            return false;
        }

        // Delete old reminder
        $this->db->where('reminder_type', 'appointment');
        $this->db->where('related_id', $consultation_id);
        $this->db->where('status', 'pending');
        $this->db->delete(db_prefix() . 'dietic_reminders');

        // Create new reminder
        return $this->create_consultation_reminder($consultation_id);
    }

    /**
     * Mettre à jour automatiquement les consultations passées
     * Met le statut à 'completed' pour les consultations dont la date est dépassée
     *
     * @return int Nombre de consultations mises à jour
     */
    public function update_past_consultations()
    {
        $now = date('Y-m-d H:i:s');

        // Mettre à jour les consultations dont la date est passée
        $this->db->where('status', 'scheduled');
        $this->db->where('consultation_date <', $now);
        $this->db->update(db_prefix() . $this->table, [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $affected_rows = $this->db->affected_rows();

        if ($affected_rows > 0) {
            log_activity("Auto-completed $affected_rows past consultations");
        }

        return $affected_rows;
    }
}
