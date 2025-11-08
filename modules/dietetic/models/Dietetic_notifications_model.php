<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_notifications_model extends App_Model
{
    private $table_preferences = 'dietic_notification_preferences';
    private $table_logs = 'dietic_notification_logs';
    private $table_milestones = 'dietic_milestones';
    private $table_settings = 'dietic_notification_settings';

    public function __construct()
    {
        parent::__construct();
    }

    // ==================== PREFERENCES ====================

    /**
     * Get patient notification preferences
     */
    public function get_preferences($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $prefs = $this->db->get(db_prefix() . $this->table_preferences)->row();

        // Create default preferences if not exists
        if (!$prefs) {
            $this->create_default_preferences($patient_id);
            return $this->get_preferences($patient_id);
        }

        return $prefs;
    }

    /**
     * Create default preferences for new patient
     */
    public function create_default_preferences($patient_id)
    {
        $data = [
            'patient_id' => $patient_id,
            'reminder_weight' => 1,
            'reminder_weight_day' => 'friday',
            'reminder_weight_time' => '09:00:00',
            'reminder_water' => 1,
            'reminder_water_times' => '10:00,14:00,18:00',
            'notify_recommendation' => 1,
            'notify_consultation' => 1,
            'notify_milestone' => 1,
            'channel_email' => 1,
            'channel_sms' => 0,
            'channel_whatsapp' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert(db_prefix() . $this->table_preferences, $data);
    }

    /**
     * Update patient notification preferences
     */
    public function update_preferences($patient_id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('patient_id', $patient_id);

        if ($this->db->update(db_prefix() . $this->table_preferences, $data)) {
            return true;
        }

        return false;
    }

    // ==================== WEIGHT REMINDER ====================

    /**
     * Get patients who need weight reminder today
     */
    public function get_patients_for_weight_reminder()
    {
        $today = strtolower(date('l')); // monday, tuesday, etc.
        $current_time = date('H:i:00');

        $this->db->select('p.*, prefs.*, patient.email, patient.phonenumber, patient.firstname, patient.lastname');
        $this->db->from(db_prefix() . $this->table_preferences . ' as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'clients as patient', 'patient.userid = p.client_id');
        $this->db->where('prefs.reminder_weight', 1);
        $this->db->where('prefs.reminder_weight_day', $today);
        $this->db->where('prefs.reminder_weight_time', $current_time);

        return $this->db->get()->result();
    }

    /**
     * Send weight reminder to patient
     */
    public function send_weight_reminder($patient)
    {
        $message = "Bonjour {$patient->firstname},\n\n";
        $message .= "📊 C'est l'heure de votre pesée hebdomadaire !\n\n";
        $message .= "Prenez quelques minutes pour enregistrer votre poids. Cela nous aide à suivre vos progrès ensemble.\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/measurements/add') . "\n\n";
        $message .= "Courage, vous faites du super travail ! 💪";

        $result = $this->send_notification([
            'patient_id' => $patient->patient_id,
            'type' => 'reminder_weight',
            'subject' => '⚖️ Rappel : Pesée Hebdomadaire',
            'message' => $message,
            'email' => $patient->email,
            'phone' => $patient->phonenumber,
            'channels' => [
                'email' => $patient->channel_email,
                'sms' => $patient->channel_sms,
                'whatsapp' => $patient->channel_whatsapp
            ]
        ]);

        return $result;
    }

    // ==================== WATER REMINDER ====================

    /**
     * Get patients who need water reminder now
     */
    public function get_patients_for_water_reminder()
    {
        $current_time = date('H:i');

        $this->db->select('p.*, prefs.*, patient.email, patient.phonenumber, patient.firstname, patient.lastname');
        $this->db->from(db_prefix() . $this->table_preferences . ' as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'clients as patient', 'patient.userid = p.client_id');
        $this->db->where('prefs.reminder_water', 1);
        $this->db->like('prefs.reminder_water_times', $current_time);

        return $this->db->get()->result();
    }

    /**
     * Send water reminder to patient
     */
    public function send_water_reminder($patient)
    {
        $messages = [
            "💧 N'oubliez pas de boire de l'eau ! Votre corps vous remerciera.",
            "🚰 Hydratez-vous ! Un verre d'eau maintenant pour rester en forme.",
            "💦 Pause hydratation ! Prenez un moment pour boire un verre d'eau.",
            "🌊 Gardez le cap ! Buvez un verre d'eau pour atteindre votre objectif quotidien."
        ];

        $random_message = $messages[array_rand($messages)];

        $message = "Bonjour {$patient->firstname},\n\n{$random_message}\n\n";
        $message .= "🎯 Objectif : 2 litres par jour\n\n";
        $message .= "Chaque gorgée compte ! 😊";

        $result = $this->send_notification([
            'patient_id' => $patient->patient_id,
            'type' => 'reminder_water',
            'subject' => '💧 Rappel Hydratation',
            'message' => $message,
            'email' => $patient->email,
            'phone' => $patient->phonenumber,
            'channels' => [
                'email' => $patient->channel_email,
                'sms' => $patient->channel_sms,
                'whatsapp' => $patient->channel_whatsapp
            ]
        ]);

        return $result;
    }

    // ==================== MILESTONES ====================

    /**
     * Check and record milestone achievements for patient
     */
    public function check_milestones($patient_id)
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');

        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $measurements = $this->dietetic_measurements_model->get_all(['patient_id' => $patient_id]);
        if (empty($measurements)) return false;

        // Get first and last measurement
        $first_measurement = end($measurements);
        $latest_measurement = reset($measurements);

        $starting_weight = $first_measurement->weight;
        $current_weight = $latest_measurement->weight;
        $weight_lost = $starting_weight - $current_weight;

        // Check weight loss milestones
        $milestones_to_check = [
            5 => 'weight_loss_5kg',
            10 => 'weight_loss_10kg',
            15 => 'weight_loss_15kg',
            20 => 'weight_loss_20kg',
            25 => 'weight_loss_25kg'
        ];

        foreach ($milestones_to_check as $kg => $milestone_type) {
            if ($weight_lost >= $kg) {
                $this->record_milestone($patient_id, $milestone_type, [
                    'starting_weight' => $starting_weight,
                    'current_weight' => $current_weight,
                    'weight_lost' => $weight_lost
                ]);
            }
        }

        return true;
    }

    /**
     * Record a milestone achievement
     */
    public function record_milestone($patient_id, $milestone_type, $data = [])
    {
        // Check if already recorded
        $this->db->where('patient_id', $patient_id);
        $this->db->where('milestone_type', $milestone_type);
        $existing = $this->db->get(db_prefix() . $this->table_milestones)->row();

        if ($existing) {
            return false; // Already recorded
        }

        $milestone_data = [
            'patient_id' => $patient_id,
            'milestone_type' => $milestone_type,
            'achieved_at' => date('Y-m-d H:i:s'),
            'starting_weight' => $data['starting_weight'] ?? null,
            'current_weight' => $data['current_weight'] ?? null,
            'weight_lost' => $data['weight_lost'] ?? null,
            'notified' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert(db_prefix() . $this->table_milestones, $milestone_data)) {
            $milestone_id = $this->db->insert_id();
            // Send celebration notification
            $this->send_milestone_notification($patient_id, $milestone_type, $data);

            // Mark as notified
            $this->db->where('id', $milestone_id);
            $this->db->update(db_prefix() . $this->table_milestones, ['notified' => 1]);

            return $milestone_id;
        }

        return false;
    }

    /**
     * Send milestone celebration notification
     */
    public function send_milestone_notification($patient_id, $milestone_type, $data)
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $celebrations = [
            'weight_loss_5kg' => [
                'title' => '🎉 Bravo ! Vous avez perdu 5kg !',
                'message' => "Félicitations {$client->company} !\n\nVous venez d'atteindre un jalon important : 5kg perdus ! 🎊\n\nC'est une victoire à célébrer ! Continuez comme ça, vous êtes sur la bonne voie ! 💪✨"
            ],
            'weight_loss_10kg' => [
                'title' => '🏆 Incroyable ! 10kg perdus !',
                'message' => "WOW {$client->company} !\n\nVous avez atteint les 10kg perdus ! C'est extraordinaire ! 🏆🎉\n\nVotre détermination paie. Vous devriez être vraiment fier(e) de vous ! 👏💯"
            ],
            'weight_loss_15kg' => [
                'title' => '⭐ Exceptionnel ! 15kg perdus !',
                'message' => "C'est fantastique {$client->company} !\n\n15kg perdus ! Vous êtes une véritable inspiration ! ⭐🌟\n\nVotre parcours est remarquable. Continuez à briller ! ✨💪"
            ]
        ];

        $celebration = $celebrations[$milestone_type] ?? [
            'title' => '🎉 Félicitations !',
            'message' => "Bravo {$client->company} ! Vous avez franchi une étape importante dans votre parcours !"
        ];

        $message = $celebration['message'];
        if (isset($data['weight_lost'])) {
            $message .= "\n\n📊 Poids perdu : " . number_format($data['weight_lost'], 1) . " kg";
        }

        $prefs = $this->get_preferences($patient_id);
        if (!$prefs->notify_milestone) {
            return false; // Patient doesn't want milestone notifications
        }

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'milestone',
            'subject' => $celebration['title'],
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $prefs->channel_email,
                'sms' => $prefs->channel_sms,
                'whatsapp' => $prefs->channel_whatsapp
            ]
        ]);
    }

    // ==================== GENERAL NOTIFICATIONS ====================

    /**
     * Send notification through all enabled channels
     */
    public function send_notification($params)
    {
        $results = [];

        // Email
        if (!empty($params['channels']['email']) && !empty($params['email'])) {
            $results['email'] = $this->send_email_notification(
                $params['patient_id'],
                $params['type'],
                $params['email'],
                $params['subject'],
                $params['message']
            );
        }

        // SMS
        if (!empty($params['channels']['sms']) && !empty($params['phone'])) {
            $results['sms'] = $this->send_sms_notification(
                $params['patient_id'],
                $params['type'],
                $params['phone'],
                $params['message']
            );
        }

        // WhatsApp
        if (!empty($params['channels']['whatsapp']) && !empty($params['phone'])) {
            $results['whatsapp'] = $this->send_whatsapp_notification(
                $params['patient_id'],
                $params['type'],
                $params['phone'],
                $params['message']
            );
        }

        return $results;
    }

    /**
     * Send email notification
     */
    private function send_email_notification($patient_id, $type, $email, $subject, $message)
    {
        $log_data = [
            'patient_id' => $patient_id,
            'notification_type' => $type,
            'channel' => 'email',
            'recipient' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->load->model('emails_model');

            $sent = $this->emails_model->send_simple_email(
                $email,
                $subject,
                nl2br($message)
            );

            if ($sent) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
            } else {
                $log_data['status'] = 'failed';
                $log_data['error_message'] = 'Email sending failed';
            }
        } catch (Exception $e) {
            $log_data['status'] = 'failed';
            $log_data['error_message'] = $e->getMessage();
        }

        $this->db->insert(db_prefix() . $this->table_logs, $log_data);
        return $log_data['status'] === 'sent';
    }

    /**
     * Send SMS notification via LAM
     */
    private function send_sms_notification($patient_id, $type, $phone, $message)
    {
        $log_data = [
            'patient_id' => $patient_id,
            'notification_type' => $type,
            'channel' => 'sms',
            'recipient' => $phone,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Get SMS settings
            $api_key = $this->get_setting('sms_lam_api_key');
            $sender_id = $this->get_setting('sms_lam_sender_id');

            if (empty($api_key)) {
                throw new Exception('SMS API key not configured');
            }

            // LAM SMS API integration
            $result = $this->send_lam_sms($phone, $message, $api_key, $sender_id);

            if ($result['success']) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
            } else {
                $log_data['status'] = 'failed';
                $log_data['error_message'] = $result['error'] ?? 'SMS sending failed';
            }
        } catch (Exception $e) {
            $log_data['status'] = 'failed';
            $log_data['error_message'] = $e->getMessage();
        }

        $this->db->insert(db_prefix() . $this->table_logs, $log_data);
        return $log_data['status'] === 'sent';
    }

    /**
     * LAM SMS API call
     */
    private function send_lam_sms($phone, $message, $api_key, $sender_id)
    {
        // LAM SMS API endpoint (à configurer selon la doc LAM)
        $url = 'https://api.lam.sn/sms/send'; // URL à confirmer

        $data = [
            'api_key' => $api_key,
            'sender' => $sender_id,
            'recipient' => $phone,
            'message' => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $response];
        }
    }

    /**
     * Send WhatsApp notification
     */
    private function send_whatsapp_notification($patient_id, $type, $phone, $message)
    {
        $log_data = [
            'patient_id' => $patient_id,
            'notification_type' => $type,
            'channel' => 'whatsapp',
            'recipient' => $phone,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Get WhatsApp settings
            $api_key = $this->get_setting('whatsapp_api_key');
            $provider = $this->get_setting('whatsapp_provider');

            if (empty($api_key)) {
                throw new Exception('WhatsApp API key not configured');
            }

            // WhatsApp API integration (Twilio, Meta, etc.)
            $result = $this->send_whatsapp_api($phone, $message, $provider, $api_key);

            if ($result['success']) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
            } else {
                $log_data['status'] = 'failed';
                $log_data['error_message'] = $result['error'] ?? 'WhatsApp sending failed';
            }
        } catch (Exception $e) {
            $log_data['status'] = 'failed';
            $log_data['error_message'] = $e->getMessage();
        }

        $this->db->insert(db_prefix() . $this->table_logs, $log_data);
        return $log_data['status'] === 'sent';
    }

    /**
     * WhatsApp API call (placeholder - implement based on provider)
     */
    private function send_whatsapp_api($phone, $message, $provider, $api_key)
    {
        // À implémenter selon le provider (Twilio, Meta Business API, etc.)
        // Pour l'instant, retourne un placeholder
        return ['success' => false, 'error' => 'WhatsApp provider not fully configured'];
    }

    /**
     * Get notification setting
     */
    public function get_setting($key)
    {
        $this->db->where('setting_key', $key);
        $setting = $this->db->get(db_prefix() . $this->table_settings)->row();
        return $setting ? $setting->setting_value : null;
    }

    /**
     * Update notification setting
     */
    public function update_setting($key, $value)
    {
        $this->db->where('setting_key', $key);
        return $this->db->update(db_prefix() . $this->table_settings, [
            'setting_value' => $value,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get notification statistics
     */
    public function get_statistics($patient_id = null)
    {
        $stats = new stdClass();

        $where = [];
        if ($patient_id) {
            $where['patient_id'] = $patient_id;
        }

        // Total sent
        $this->db->where('status', 'sent');
        if (!empty($where)) $this->db->where($where);
        $stats->total_sent = $this->db->count_all_results(db_prefix() . $this->table_logs);

        // Sent today
        $this->db->where('status', 'sent');
        $this->db->where('DATE(sent_at)', date('Y-m-d'));
        if (!empty($where)) $this->db->where($where);
        $stats->sent_today = $this->db->count_all_results(db_prefix() . $this->table_logs);

        // Failed
        $this->db->where('status', 'failed');
        if (!empty($where)) $this->db->where($where);
        $stats->failed = $this->db->count_all_results(db_prefix() . $this->table_logs);

        // By type
        $this->db->select('notification_type, COUNT(*) as count');
        $this->db->where('status', 'sent');
        if (!empty($where)) $this->db->where($where);
        $this->db->group_by('notification_type');
        $stats->by_type = $this->db->get(db_prefix() . $this->table_logs)->result();

        return $stats;
    }
}
