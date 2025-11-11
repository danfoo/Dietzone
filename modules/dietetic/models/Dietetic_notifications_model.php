<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dietetic_notifications_model extends App_Model
{
    private $table_preferences = 'dietic_notification_preferences';
    private $table_logs = 'dietic_notification_logs';
    private $table_milestones = 'dietic_milestones';
    private $table_settings = 'dietic_notification_settings';
    private $table_patient_notifications = 'dietic_patient_notifications';

    public function __construct()
    {
        parent::__construct();
    }

    // ==================== PREFERENCES ====================

    /**
     * Get patient notification preferences (with cache)
     */
    public function get_preferences($patient_id)
    {
        // Check cache first
        $cache_key = 'dietic_notif_prefs_' . $patient_id;

        if ($this->app_object_cache->get($cache_key)) {
            $cached = $this->app_object_cache->get($cache_key);
            if ($cached) {
                return $cached;
            }
        }

        // Not in cache, fetch from DB
        $this->db->where('patient_id', $patient_id);
        $prefs = $this->db->get(db_prefix() . $this->table_preferences)->row();

        // Create default preferences if not exists
        if (!$prefs) {
            $this->create_default_preferences($patient_id);
            return $this->get_preferences($patient_id);
        }

        // Store in cache for 1 hour
        $this->app_object_cache->add($cache_key, $prefs, 3600);

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
            'notify_program' => 1,
            'notify_food_entry' => 1,
            'channel_email' => 1,
            'channel_sms' => 0,
            'channel_whatsapp' => 0,
            'channel_push' => 1,
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
            // Clear cache
            $cache_key = 'dietic_notif_prefs_' . $patient_id;
            $this->app_object_cache->delete($cache_key);

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

        $result = $this->send_notification_with_frontend([
            'patient_id' => $patient->patient_id,
            'type' => 'reminder_weight',
            'subject' => '⚖️ Rappel : Pesée Hebdomadaire',
            'message' => $message,
            'email' => $patient->email,
            'phone' => $patient->phonenumber,
            'url' => site_url('dietetic/portal/add_measurement'),
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

        // Push Notifications (Firebase)
        if (!empty($params['channels']['push'])) {
            $results['push'] = $this->send_push_notification(
                $params['patient_id'],
                $params['type'],
                $params['subject'] ?? 'Notification',
                $params['message'],
                $params['push_data'] ?? []
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
            // Get LAM SMS settings
            $account_id = $this->get_setting('sms_lam_account_id');
            $password = $this->get_setting('sms_lam_password');
            $sender_id = $this->get_setting('sms_lam_sender_id') ?: 'API_LAMSMS';

            if (empty($account_id) || empty($password)) {
                throw new Exception('LAM SMS credentials not configured (account_id and password required)');
            }

            // LAM SMS API integration
            $result = $this->send_lam_sms($phone, $message, $account_id, $password, $sender_id);

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
     * Documentation: https://developers.lafricamobile.com/docs/sms/introduction
     */
    private function send_lam_sms($phone, $message, $account_id, $password, $sender_id = 'API_LAMSMS')
    {
        // LAM SMS API endpoint
        $url = 'https://lamsms.lafricamobile.com/api';

        // Get additional settings
        $ret_url = $this->get_setting('sms_lam_ret_url') ?: site_url('dietetic/sms_callback');
        $priority = $this->get_setting('sms_lam_priority') ?: '2';

        // Format phone number for LAM API
        // Ensure phone starts with country code (e.g., 221 for Senegal)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!preg_match('/^221/', $phone) && strlen($phone) == 9) {
            $phone = '221' . $phone;
        }

        // Prepare LAM API request
        $data = [
            'accountid' => $account_id,
            'password' => $password,
            'sender' => $sender_id,
            'ret_id' => 'dietetic_' . time(),
            'ret_url' => $ret_url,
            'priority' => $priority,
            'text' => $message,
            'to' => [
                [
                    'ret_id_1' => $phone
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log the request for debugging
        log_activity('LAM SMS sent to ' . $phone . ' - HTTP Code: ' . $http_code . ' - Response: ' . $response);

        if ($http_code == 200 || $http_code == 201) {
            $response_data = json_decode($response, true);
            return ['success' => true, 'response' => $response_data];
        } else {
            $error_msg = $curl_error ?: $response;
            return ['success' => false, 'error' => $error_msg];
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
     * Send push notification via Firebase Cloud Messaging
     */
    private function send_push_notification($patient_id, $type, $title, $message, $data = [])
    {
        $log_data = [
            'patient_id' => $patient_id,
            'notification_type' => $type,
            'channel' => 'push',
            'recipient' => 'firebase_token',
            'subject' => $title,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Load Firebase library
            $this->load->library('dietetic/firebase_cloud_messaging');

            if (!$this->firebase_cloud_messaging->is_enabled()) {
                throw new Exception('Push notifications are not enabled');
            }

            // Prepare notification options
            $options = [
                'click_action' => $data['click_action'] ?? site_url('dietetic/portal'),
                'icon' => $data['icon'] ?? base_url('uploads/company/favicon.png'),
            ];

            if (isset($data['image'])) {
                $options['image'] = $data['image'];
            }

            // Send to patient (all devices)
            $result = $this->firebase_cloud_messaging->send_to_patient(
                $patient_id,
                $title,
                $message,
                $data,
                $options
            );

            if ($result['success'] || (isset($result['success_count']) && $result['success_count'] > 0)) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
                $log_data['recipient'] = ($result['success_count'] ?? 1) . ' device(s)';
            } else {
                $log_data['status'] = 'failed';
                $log_data['error_message'] = $result['error'] ?? 'Push notification failed';
            }
        } catch (Exception $e) {
            $log_data['status'] = 'failed';
            $log_data['error_message'] = $e->getMessage();
        }

        $this->db->insert(db_prefix() . $this->table_logs, $log_data);
        return $log_data['status'] === 'sent';
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
        $display_value = (strpos($key, 'password') !== false && $value) ? '***SET***' : $value;
        log_activity("🔍 [MODEL DEBUG] update_setting called: key={$key}, value=" . var_export($display_value, true));

        // Skip empty values (but allow '0')
        if ($value === null || $value === '') {
            log_activity("⚠️ [MODEL DEBUG] Skipping {$key} because value is empty/null");
            return true; // Consider empty values as successful (no-op)
        }

        $table = db_prefix() . $this->table_settings;
        log_activity("🔍 [MODEL DEBUG] Using table: {$table}");

        // Check if setting exists
        $this->db->where('setting_key', $key);
        $existing = $this->db->get($table)->row();

        if ($existing) {
            log_activity("🔍 [MODEL DEBUG] Setting {$key} EXISTS - will UPDATE");
            log_activity("🔍 [MODEL DEBUG] Current value in DB: " . var_export($existing->setting_value, true));

            // Update existing setting
            $this->db->where('setting_key', $key);
            $result = $this->db->update($table, [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $affected_rows = $this->db->affected_rows();
            log_activity("🔍 [MODEL DEBUG] UPDATE result: " . ($result ? 'TRUE' : 'FALSE') . ", affected_rows: {$affected_rows}");

            if (!$result) {
                $error = $this->db->error();
                log_activity("❌ [MODEL DEBUG] UPDATE FAILED for {$key}: " . $error['message']);
            } else {
                log_activity("✅ [MODEL DEBUG] UPDATE SUCCESS for {$key}");
            }

            return $result;
        } else {
            log_activity("🔍 [MODEL DEBUG] Setting {$key} DOES NOT EXIST - will INSERT");

            // Insert new setting
            $result = $this->db->insert($table, [
                'setting_key' => $key,
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            log_activity("🔍 [MODEL DEBUG] INSERT result: " . ($result ? 'TRUE' : 'FALSE'));

            if (!$result) {
                $error = $this->db->error();
                log_activity("❌ [MODEL DEBUG] INSERT FAILED for {$key}: " . $error['message']);
            } else {
                log_activity("✅ [MODEL DEBUG] INSERT SUCCESS for {$key}");
            }

            return $result;
        }
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

    // ==================== PROGRAM NOTIFICATIONS ====================

    /**
     * Notify patient when new program is assigned
     */
    public function notify_program_assigned($patient_id, $program_name, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_program) {
            return false;
        }

        // Get patient info
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $message = "Bonjour {$client->company},\n\n";
        $message .= "📋 Votre diététicien {$dietitian_name} vous a assigné un nouveau programme :\n\n";
        $message .= "🎯 {$program_name}\n\n";
        $message .= "Consultez votre portail patient pour voir les détails et commencer votre programme.\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/meal_plans') . "\n\n";
        $message .= "Bonne continuation ! 💪";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'program_assigned',
            'subject' => '📋 Nouveau Programme Diététique',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient when program is updated
     */
    public function notify_program_updated($patient_id, $program_name, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_program) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $message = "Bonjour {$client->company},\n\n";
        $message .= "🔄 Votre diététicien {$dietitian_name} a mis à jour votre programme :\n\n";
        $message .= "{$program_name}\n\n";
        $message .= "Consultez les modifications sur votre portail.\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/meal_plans');

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'program_updated',
            'subject' => '🔄 Programme Diététique Mis à Jour',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient when program is ending soon (7 days before)
     */
    public function notify_program_ending_soon($patient_id, $program_name, $end_date)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_program) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $message = "Bonjour {$client->company},\n\n";
        $message .= "⏰ Votre programme \"{$program_name}\" se termine bientôt :\n\n";
        $message .= "📅 Date de fin : " . date('d/m/Y', strtotime($end_date)) . "\n\n";
        $message .= "C'est le moment de faire le bilan avec votre diététicien et planifier la suite ! 💪";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'program_ending',
            'subject' => '⏰ Fin de Programme Approchant',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    // ==================== CONSULTATION NOTIFICATIONS ====================

    /**
     * Notify patient when new consultation is scheduled
     */
    public function notify_consultation_scheduled($patient_id, $consultation_date, $consultation_time, $dietitian_name, $consultation_type = 'Consultation')
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_consultation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $formatted_date = date('d/m/Y', strtotime($consultation_date));
        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : '';

        $message = "Bonjour {$client->company},\n\n";
        $message .= "📅 Une nouvelle consultation a été planifiée :\n\n";
        $message .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message .= "📆 Date : {$formatted_date}\n";
        if ($formatted_time) {
            $message .= "🕐 Heure : {$formatted_time}\n";
        }
        $message .= "📝 Type : {$consultation_type}\n\n";
        $message .= "Nous avons hâte de vous voir ! 😊";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'consultation_scheduled',
            'subject' => '📅 Nouvelle Consultation Planifiée',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient 1 day before consultation
     */
    public function notify_consultation_reminder_day($patient_id, $consultation_date, $consultation_time, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_consultation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : 'à confirmer';

        $message = "Bonjour {$client->company},\n\n";
        $message .= "⏰ Rappel : Votre consultation est demain !\n\n";
        $message .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message .= "🕐 Heure : {$formatted_time}\n\n";
        $message .= "N'oubliez pas votre rendez-vous ! 📋";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'consultation_reminder_day',
            'subject' => '⏰ Rappel : Consultation Demain',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient 1 hour before consultation
     */
    public function notify_consultation_reminder_hour($patient_id, $consultation_time, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_consultation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : 'bientôt';

        $message = "Bonjour {$client->company},\n\n";
        $message .= "⏰ Votre consultation commence dans 1 heure !\n\n";
        $message .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message .= "🕐 Heure : {$formatted_time}\n\n";
        $message .= "À tout de suite ! 😊";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'consultation_reminder_hour',
            'subject' => '⏰ Consultation dans 1 heure',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient when consultation is cancelled
     */
    public function notify_consultation_cancelled($patient_id, $consultation_date, $dietitian_name, $reason = '')
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_consultation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $formatted_date = date('d/m/Y', strtotime($consultation_date));

        $message = "Bonjour {$client->company},\n\n";
        $message .= "❌ Votre consultation du {$formatted_date} avec {$dietitian_name} a été annulée.\n\n";
        if ($reason) {
            $message .= "Raison : {$reason}\n\n";
        }
        $message .= "Veuillez contacter votre diététicien pour reprogrammer.";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'consultation_cancelled',
            'subject' => '❌ Consultation Annulée',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Get consultations that need reminders (1 day before)
     */
    public function get_consultations_for_day_reminder()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $this->db->select('c.*, p.id as patient_id, p.client_id, s.firstname as dietitian_firstname, s.lastname as dietitian_lastname');
        $this->db->from(db_prefix() . 'dietic_consultations c');
        $this->db->join(db_prefix() . 'dietic_patients p', 'c.patient_id = p.id', 'left');
        $this->db->join(db_prefix() . 'staff s', 'c.dietitian_id = s.staffid', 'left');
        $this->db->where('DATE(c.consultation_date)', $tomorrow);
        $this->db->where('c.status !=', 'cancelled');

        return $this->db->get()->result();
    }

    /**
     * Get consultations that need reminders (1 hour before)
     */
    public function get_consultations_for_hour_reminder()
    {
        $now = date('Y-m-d H:i:s');
        $one_hour_later = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->select('c.*, p.id as patient_id, p.client_id, s.firstname as dietitian_firstname, s.lastname as dietitian_lastname');
        $this->db->from(db_prefix() . 'dietic_consultations c');
        $this->db->join(db_prefix() . 'dietic_patients p', 'c.patient_id = p.id', 'left');
        $this->db->join(db_prefix() . 'staff s', 'c.dietitian_id = s.staffid', 'left');
        $this->db->where('CONCAT(c.consultation_date, " ", COALESCE(c.consultation_time, "00:00:00")) <=', $one_hour_later);
        $this->db->where('CONCAT(c.consultation_date, " ", COALESCE(c.consultation_time, "00:00:00")) >', $now);
        $this->db->where('c.status !=', 'cancelled');

        return $this->db->get()->result();
    }

    // ==================== FOOD SURVEY NOTIFICATIONS ====================

    /**
     * Notify dietitian when patient submits daily food entry
     */
    public function notify_dietitian_food_entry($dietitian_id, $patient_name, $date)
    {
        try {
            $this->load->model('staff_model');
            $dietitian = $this->staff_model->get($dietitian_id);

            if (!$dietitian || empty($dietitian->email)) {
                log_activity("❌ Diététicien non trouvé ou sans email: ID {$dietitian_id}");
                return false;
            }

            $formatted_date = date('d/m/Y', strtotime($date));

            // Préparer le message
            $message = "Bonjour {$dietitian->firstname},\n\n";
            $message .= "📝 {$patient_name} a soumis son journal alimentaire du {$formatted_date}.\n\n";
            $message .= "Consultez les détails et ajoutez vos recommandations :\n";
            $message .= "🔗 " . admin_url('dietetic/food_surveys') . "\n\n";
            $message .= "Bonne journée !";

            // Vérifier si le template mail existe avant de l'utiliser
            $template_path = FCPATH . 'application/libraries/mails/Dietetic_food_entry_submitted.php';

            if (file_exists($template_path)) {
                // Template existe, utiliser send_mail_template
                log_activity("📧 Utilisation du template email personnalisé");
                $email_sent = send_mail_template('dietetic_food_entry_submitted', [
                    'email' => $dietitian->email,
                    'subject' => "📝 Nouveau journal alimentaire - {$patient_name}",
                    'message' => $message
                ]);
            } else {
                // Template n'existe pas, envoyer email simple
                log_activity("📧 Template non trouvé, envoi email simple");

                $this->load->library('email');
                $this->email->clear();
                $this->email->from(get_option('smtp_email'), get_option('companyname'));
                $this->email->to($dietitian->email);
                $this->email->subject("📝 Nouveau journal alimentaire - {$patient_name}");
                $this->email->message(nl2br($message));
                $email_sent = $this->email->send();
            }

            if ($email_sent) {
                log_activity("✅ Email envoyé au diététicien {$dietitian->firstname} {$dietitian->lastname} pour {$patient_name}");
            } else {
                log_activity("❌ Échec envoi email au diététicien {$dietitian->firstname} {$dietitian->lastname}");
            }

            return $email_sent;

        } catch (Exception $e) {
            log_activity("❌ Erreur notification diététicien: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify patient that their food entry was received
     */
    public function notify_patient_food_entry_received($patient_id, $date)
    {
        try {
            // Get patient preferences
            $preferences = $this->get_preferences($patient_id);
            if (!$preferences || !$preferences->notify_food_entry) {
                log_activity("⚠️ Patient {$patient_id} n'a pas activé les notifications d'enquête alimentaire");
                return false;
            }

            // Get patient info
            $this->load->model('dietetic/dietetic_patients_model');
            $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
            if (!$patient) {
                log_activity("❌ Patient {$patient_id} non trouvé");
                return false;
            }

            $this->load->model('clients_model');
            $client = $this->clients_model->get($patient->client_id);
            if (!$client) {
                log_activity("❌ Client du patient {$patient_id} non trouvé");
                return false;
            }

            $formatted_date = date('d/m/Y', strtotime($date));

            // Préparer le message
            $subject = "✅ Journal alimentaire reçu";
            $message = "Bonjour,\n\n";
            $message .= "✅ Votre journal alimentaire du {$formatted_date} a bien été reçu.\n\n";
            $message .= "Votre diététicien va l'examiner et vous faire des recommandations personnalisées.\n\n";
            $message .= "📱 Consultez vos recommandations dans votre espace patient :\n";
            $message .= "🔗 " . site_url('dietetic/portal/food_surveys') . "\n\n";
            $message .= "Merci de votre engagement ! 💪";

            // Envoyer via le système de notifications multi-canal
            $result = $this->send_notification([
                'patient_id' => $patient_id,
                'type' => 'food_entry_confirmation',
                'subject' => $subject,
                'message' => $message,
                'email' => $client->email,
                'phone' => $client->phonenumber,
                'channels' => [
                    'email' => $preferences->channel_email ? 1 : 0,
                    'sms' => $preferences->channel_sms ? 1 : 0,
                    'whatsapp' => $preferences->channel_whatsapp ? 1 : 0
                ]
            ]);

            if ($result['email'] || $result['sms'] || $result['whatsapp']) {
                log_activity("✅ Confirmation envoyée au patient (ID: {$patient_id}) pour enquête du {$formatted_date}");
                return true;
            } else {
                log_activity("❌ Échec envoi confirmation au patient (ID: {$patient_id})");
                return false;
            }

        } catch (Exception $e) {
            log_activity("❌ Erreur notification patient: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify patient when dietitian adds recommendation
     */
    public function notify_patient_recommendation($patient_id, $dietitian_name, $meal_type = 'général')
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_recommendation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $meal_labels = [
            'breakfast' => 'petit-déjeuner',
            'lunch' => 'déjeuner',
            'dinner' => 'dîner',
            'global' => 'général'
        ];
        $meal_label = $meal_labels[$meal_type] ?? 'général';

        $message = "Bonjour {$client->company},\n\n";
        $message .= "💡 Votre diététicien {$dietitian_name} a ajouté une nouvelle recommandation pour votre {$meal_label}.\n\n";
        $message .= "Consultez vos recommandations :\n";
        $message .= "🔗 " . site_url('dietetic/portal/food_surveys') . "\n\n";
        $message .= "Suivez ces conseils pour progresser ! 💪";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'recommendation_added',
            'subject' => '💡 Nouvelle Recommandation Diététique',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify patient when dietitian adds comment
     */
    public function notify_patient_comment($patient_id, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_recommendation) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $message = "Bonjour {$client->company},\n\n";
        $message .= "💬 {$dietitian_name} a ajouté un commentaire sur votre journal alimentaire.\n\n";
        $message .= "Consultez le commentaire :\n";
        $message .= "🔗 " . site_url('dietetic/portal/food_surveys') . "\n\n";
        $message .= "Continuez vos efforts ! 🌟";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'comment_added',
            'subject' => '💬 Nouveau Commentaire de votre Diététicien',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Get patients who haven't submitted food entry today (for daily reminder)
     */
    public function get_patients_for_food_entry_reminder()
    {
        $today = date('Y-m-d');

        // Get all active patients with active surveys
        $this->db->select('p.id as patient_id, p.client_id, fs.id as survey_id');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'dietic_food_surveys fs', 'p.id = fs.patient_id', 'inner');
        $this->db->where('fs.status', 'active');

        $patients_with_surveys = $this->db->get()->result();

        $patients_to_remind = [];

        foreach ($patients_with_surveys as $patient) {
            // Check if patient already submitted today
            $this->db->where('survey_id', $patient->survey_id);
            $this->db->where('DATE(entry_date)', $today);
            $count = $this->db->count_all_results(db_prefix() . 'dietic_food_survey_entries');

            if ($count == 0) {
                // Check if patient has reminder enabled
                $prefs = $this->get_preferences($patient->patient_id);
                if ($prefs && $prefs->notify_food_entry) {
                    $patients_to_remind[] = $patient;
                }
            }
        }

        return $patients_to_remind;
    }

    /**
     * Send daily food entry reminder to patient
     */
    public function send_food_entry_reminder($patient_id)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_food_entry) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        $message = "Bonjour {$client->company},\n\n";
        $message .= "📝 N'oubliez pas de remplir votre journal alimentaire d'aujourd'hui !\n\n";
        $message .= "Quelques minutes suffisent pour noter vos repas et boissons.\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/food_surveys') . "\n\n";
        $message .= "Votre suivi régulier est la clé du succès ! 🌟";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'food_entry_reminder',
            'subject' => '📝 Rappel : Journal Alimentaire',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Get all notification settings
     */
    public function get_all_settings()
    {
        $settings_array = $this->db->get(db_prefix() . $this->table_settings)->result();

        // Convert to associative array
        $settings = [];
        foreach ($settings_array as $setting) {
            $settings[$setting->setting_key] = $setting->setting_value;
        }

        return $settings;
    }

    /**
     * Get notification logs with filters
     */
    public function get_logs($filters = [], $limit = 50, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table_logs);

        // Apply filters
        if (!empty($filters['patient_id'])) {
            $this->db->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['notification_type'])) {
            $this->db->where('notification_type', $filters['notification_type']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['channel'])) {
            $this->db->where('channel', $filters['channel']);
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count notification logs with filters
     */
    public function count_logs($filters = [])
    {
        $this->db->from(db_prefix() . $this->table_logs);

        // Apply filters
        if (!empty($filters['patient_id'])) {
            $this->db->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['notification_type'])) {
            $this->db->where('notification_type', $filters['notification_type']);
        }

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['channel'])) {
            $this->db->where('channel', $filters['channel']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get recent notification logs
     */
    public function get_recent_logs($limit = 20)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table_logs);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get all milestones
     */
    public function get_all_milestones($limit = 50)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table_milestones);
        $this->db->order_by('achieved_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get milestone statistics
     */
    public function get_milestone_statistics()
    {
        $stats = [];

        // Total milestones
        $stats['total'] = $this->db->count_all(db_prefix() . $this->table_milestones);

        // Count by type
        $milestone_types = ['weight_loss_5kg', 'weight_loss_10kg', 'weight_loss_15kg', 'weight_loss_20kg', 'weight_loss_25kg'];

        foreach ($milestone_types as $type) {
            $this->db->where('milestone_type', $type);
            $count = $this->db->count_all_results(db_prefix() . $this->table_milestones);

            // Extract kg from type (e.g., "weight_loss_5kg" -> "5kg")
            $key = str_replace('weight_loss_', '', $type);
            $stats[$key] = $count;
        }

        // This month
        $this->db->where('achieved_at >=', date('Y-m-01 00:00:00'));
        $this->db->where('achieved_at <=', date('Y-m-t 23:59:59'));
        $stats['this_month'] = $this->db->count_all_results(db_prefix() . $this->table_milestones);

        return $stats;
    }

    // ==================== PATIENT NOTIFICATIONS (FRONTEND) ====================

    /**
     * Create a patient notification for frontend display
     */
    public function create_patient_notification($patient_id, $data)
    {
        $notification_data = [
            'patient_id' => $patient_id,
            'notification_type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? 'fa-bell',
            'url' => $data['url'] ?? null,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert(db_prefix() . $this->table_patient_notifications, $notification_data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Get patient notifications for frontend
     */
    public function get_patient_notifications($patient_id, $limit = 50, $unread_only = false)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table_patient_notifications);
        $this->db->where('patient_id', $patient_id);

        if ($unread_only) {
            $this->db->where('is_read', 0);
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Get unread notification count for patient
     */
    public function get_unread_count($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('is_read', 0);
        return $this->db->count_all_results(db_prefix() . $this->table_patient_notifications);
    }

    /**
     * Mark notification as read
     */
    public function mark_as_read($notification_id, $patient_id)
    {
        $this->db->where('id', $notification_id);
        $this->db->where('patient_id', $patient_id);
        return $this->db->update(db_prefix() . $this->table_patient_notifications, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all notifications as read for patient
     */
    public function mark_all_as_read($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('is_read', 0);
        return $this->db->update(db_prefix() . $this->table_patient_notifications, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Delete a patient notification
     */
    public function delete_patient_notification($notification_id, $patient_id)
    {
        $this->db->where('id', $notification_id);
        $this->db->where('patient_id', $patient_id);
        return $this->db->delete(db_prefix() . $this->table_patient_notifications);
    }

    /**
     * Delete all read notifications for patient (cleanup)
     */
    public function delete_all_read($patient_id)
    {
        $this->db->where('patient_id', $patient_id);
        $this->db->where('is_read', 1);
        return $this->db->delete(db_prefix() . $this->table_patient_notifications);
    }

    /**
     * Delete old notifications (older than X days)
     */
    public function delete_old_notifications($days = 30)
    {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff_date);
        $this->db->where('is_read', 1);
        return $this->db->delete(db_prefix() . $this->table_patient_notifications);
    }

    // ==================== EXTENDED NOTIFICATION SENDING (with frontend creation) ====================

    /**
     * Enhanced send_notification that also creates frontend notification
     */
    public function send_notification_with_frontend($params)
    {
        // Send via channels (email, sms, whatsapp, push)
        $results = $this->send_notification($params);

        // Also create frontend notification
        $frontend_created = $this->create_patient_notification($params['patient_id'], [
            'type' => $params['type'],
            'title' => $params['subject'],
            'message' => $this->format_message_for_frontend($params['message']),
            'icon' => $this->get_icon_for_type($params['type']),
            'url' => $params['url'] ?? null
        ]);

        $results['frontend'] = $frontend_created !== false;

        return $results;
    }

    /**
     * Format message for frontend display (remove excessive newlines, limit length)
     */
    private function format_message_for_frontend($message)
    {
        // Remove excessive newlines
        $message = preg_replace("/\n{3,}/", "\n\n", $message);

        // Limit to 200 characters for preview
        if (strlen($message) > 200) {
            $message = substr($message, 0, 197) . '...';
        }

        return trim($message);
    }

    /**
     * Get appropriate icon for notification type
     */
    private function get_icon_for_type($type)
    {
        $icons = [
            'reminder_weight' => 'fa-balance-scale',
            'reminder_water' => 'fa-tint',
            'milestone' => 'fa-trophy',
            'program_assigned' => 'fa-clipboard',
            'program_updated' => 'fa-refresh',
            'program_ending' => 'fa-clock-o',
            'consultation_scheduled' => 'fa-calendar-plus-o',
            'consultation_reminder_day' => 'fa-calendar',
            'consultation_reminder_hour' => 'fa-clock-o',
            'consultation_cancelled' => 'fa-calendar-times-o',
            'recommendation_added' => 'fa-lightbulb-o',
            'comment_added' => 'fa-comment',
            'food_entry_reminder' => 'fa-cutlery',
            'default' => 'fa-bell'
        ];

        return $icons[$type] ?? $icons['default'];
    }
}
