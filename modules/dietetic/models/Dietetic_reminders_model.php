<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_reminders_model extends App_Model
{
    private $table = 'dietic_reminders';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get reminder by ID
     *
     * @param int $id
     * @return object|null
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    /**
     * Get all reminders
     *
     * @param array $where
     * @return array
     */
    public function get_all($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('scheduled_date', 'DESC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Get pending reminders to send
     *
     * @return array
     */
    public function get_pending()
    {
        $this->db->where('status', 'pending');
        $this->db->where('scheduled_date <=', date('Y-m-d H:i:s'));
        $this->db->order_by('scheduled_date', 'ASC');

        return $this->db->get(db_prefix() . $this->table)->result();
    }

    /**
     * Add new reminder
     *
     * @param array $data
     * @return int|bool
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if ($this->db->insert(db_prefix() . $this->table, $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update reminder
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . $this->table, $data);
    }

    /**
     * Delete reminder
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . $this->table);
    }

    /**
     * Send pending reminders (called by cron)
     *
     * @return array Stats
     */
    public function send_pending_reminders()
    {
        $reminders = $this->get_pending();
        $sent = 0;
        $failed = 0;

        foreach ($reminders as $reminder) {
            $success = false;

            // Send via email
            if (in_array($reminder->send_via, ['email', 'both'])) {
                $email_sent = $this->send_email_reminder($reminder);
                if ($email_sent) {
                    $success = true;
                }
            }

            // Send via SMS
            if (in_array($reminder->send_via, ['sms', 'both'])) {
                $this->load->model('dietetic/dietetic_patients_model');
                $patient = $this->dietetic_patients_model->get($reminder->patient_id);

                if ($patient && $patient->phone) {
                    $sms_result = dietetic_send_sms($patient->phone, $reminder->message);
                    if ($sms_result['success']) {
                        $success = true;
                    }
                }
            }

            // Update reminder status
            if ($success) {
                $this->update($reminder->id, [
                    'status'    => 'sent',
                    'sent_date' => date('Y-m-d H:i:s'),
                ]);
                $sent++;
            } else {
                $this->update($reminder->id, [
                    'status'        => 'failed',
                    'error_message' => 'Failed to send reminder',
                ]);
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'total' => count($reminders)];
    }

    /**
     * Send email reminder
     *
     * @param object $reminder
     * @return bool
     */
    private function send_email_reminder($reminder)
    {
        $this->load->model('emails_model');

        $merge_fields = [];
        $merge_fields['reminder_message'] = $reminder->message;

        try {
            return $this->emails_model->send_simple_email(
                $reminder->recipient,
                $reminder->subject,
                nl2br($reminder->message)
            );
        } catch (Exception $e) {
            log_activity('Dietetic Reminder Email Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create reminder for program renewal
     *
     * @param int $program_id
     * @return bool
     */
    public function create_program_renewal_reminder($program_id)
    {
        $this->load->model('dietetic/dietetic_programs_model');
        $program = $this->dietetic_programs_model->get($program_id);

        if (!$program || !$program->end_date) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($program->patient_id);

        if (!$patient) {
            return false;
        }

        $days_before = dietetic_get_option('reminder_renewal_days', 7);
        $reminder_date = date('Y-m-d H:i:s', strtotime($program->end_date . " -{$days_before} days"));

        $message = sprintf(
            _l('dietetic_reminder_renewal_message'),
            $patient->client->company,
            $program->program_name,
            _dt($program->end_date)
        );

        $data = [
            'patient_id'     => $program->patient_id,
            'reminder_type'  => 'renewal',
            'related_id'     => $program_id,
            'send_via'       => 'email',
            'recipient'      => $patient->email,
            'subject'        => _l('dietetic_reminder_renewal_subject'),
            'message'        => $message,
            'scheduled_date' => $reminder_date,
            'status'         => 'pending',
        ];

        return $this->add($data);
    }

    /**
     * Get statistics
     *
     * @return object
     */
    public function get_statistics()
    {
        $stats = new stdClass();

        // Pending reminders
        $this->db->where('status', 'pending');
        $stats->pending = $this->db->count_all_results(db_prefix() . $this->table);

        // Sent today
        $this->db->where('status', 'sent');
        $this->db->where('DATE(sent_date)', date('Y-m-d'));
        $stats->sent_today = $this->db->count_all_results(db_prefix() . $this->table);

        // Failed
        $this->db->where('status', 'failed');
        $stats->failed = $this->db->count_all_results(db_prefix() . $this->table);

        return $stats;
    }
}
