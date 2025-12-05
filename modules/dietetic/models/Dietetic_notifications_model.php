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
        // Check if SMS is configured to enable it by default
        $sms_configured = false;
        $account_id = $this->get_setting('sms_lam_account_id');
        $password = $this->get_setting('sms_lam_password');
        if (!empty($account_id) && !empty($password)) {
            $sms_configured = true;
        }

        // Check if WhatsApp is configured
        $whatsapp_configured = false;
        $whatsapp_api_key = $this->get_setting('whatsapp_api_key');
        if (!empty($whatsapp_api_key)) {
            $whatsapp_configured = true;
        }

        $data = [
            'patient_id' => $patient_id,
            'reminder_weight' => 1,
            'reminder_weight_day' => 'friday',
            'reminder_weight_time' => '09:00:00',
            'reminder_water' => 1,
            'reminder_water_times' => '10:00,14:00,18:00',
            'reminder_breakfast' => 1,
            'reminder_breakfast_time' => '08:00:00',
            'reminder_lunch' => 1,
            'reminder_lunch_time' => '12:30:00',
            'reminder_dinner' => 1,
            'reminder_dinner_time' => '19:00:00',
            'notify_recommendation' => 1,
            'notify_consultation' => 1,
            'notify_milestone' => 1,
            'notify_program' => 1,
            'notify_food_entry' => 1,
            'channel_email' => 1,
            'channel_sms' => $sms_configured ? 1 : 0, // Enable SMS if configured
            'channel_whatsapp' => $whatsapp_configured ? 1 : 0, // Enable WhatsApp if configured
            'channel_push' => 0, // Disabled by default - Enable after Firebase configuration
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert(db_prefix() . $this->table_preferences, $data);
    }

    /**
     * Get primary contact for a client
     * Returns the primary contact with email and phone information
     *
     * @param int $client_id
     * @return object|null
     */
    public function get_client_primary_contact($client_id)
    {
        // Try to get primary contact first
        $this->db->where('userid', $client_id);
        $this->db->where('is_primary', 1);
        $contact = $this->db->get(db_prefix() . 'contacts')->row();

        // If no primary contact, get the first contact available
        if (!$contact) {
            $this->db->where('userid', $client_id);
            $this->db->order_by('id', 'ASC');
            $this->db->limit(1);
            $contact = $this->db->get(db_prefix() . 'contacts')->row();
        }

        return $contact;
    }

    /**
     * Update patient notification preferences
     */
    public function update_preferences($patient_id, $data)
    {
        log_activity('📊 [MODEL] update_preferences called for patient_id: ' . $patient_id);

        // Check if preferences exist for this patient
        $this->db->where('patient_id', $patient_id);
        $existing = $this->db->get(db_prefix() . $this->table_preferences)->row();

        if ($existing) {
            log_activity('📊 [MODEL] Existing preferences found - performing UPDATE');
            // Update existing preferences
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('patient_id', $patient_id);
            $result = $this->db->update(db_prefix() . $this->table_preferences, $data);

            if ($this->db->affected_rows() > 0) {
                log_activity('📊 [MODEL] UPDATE successful - ' . $this->db->affected_rows() . ' row(s) affected');
            } else {
                log_activity('⚠️ [MODEL] UPDATE completed but 0 rows affected (data may be identical)');
            }
        } else {
            log_activity('📊 [MODEL] No existing preferences - performing INSERT');
            // Insert new preferences
            $data['patient_id'] = $patient_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $result = $this->db->insert(db_prefix() . $this->table_preferences, $data);

            if ($result) {
                log_activity('📊 [MODEL] INSERT successful - new ID: ' . $this->db->insert_id());
            } else {
                log_activity('❌ [MODEL] INSERT failed - DB error: ' . $this->db->error()['message']);
            }
        }

        if ($result || $this->db->affected_rows() >= 0) {
            // Clear cache
            $cache_key = 'dietic_notif_prefs_' . $patient_id;
            $this->app_object_cache->delete($cache_key);
            log_activity('📊 [MODEL] Cache cleared for key: ' . $cache_key);

            return true;
        }

        log_activity('❌ [MODEL] update_preferences FAILED');
        return false;
    }

    /**
     * Check if SMS is configured
     *
     * @return bool
     */
    public function is_sms_configured()
    {
        $account_id = $this->get_setting('sms_lam_account_id');
        $password = $this->get_setting('sms_lam_password');
        return !empty($account_id) && !empty($password);
    }

    /**
     * Check if WhatsApp is configured
     *
     * @return bool
     */
    public function is_whatsapp_configured()
    {
        $provider = $this->get_setting('whatsapp_provider') ?: 'lam';

        if ($provider == 'lam') {
            // LAM WhatsApp configuration
            $account_id = $this->get_setting('whatsapp_lam_account_id');
            $password = $this->get_setting('whatsapp_lam_password');
            return !empty($account_id) && !empty($password);
        } else {
            // Other providers
            $api_key = $this->get_setting('whatsapp_api_key');
            return !empty($api_key);
        }
    }

    /**
     * Enable SMS notifications for all existing patients
     * Only call this after configuring LAM SMS credentials
     *
     * @return int Number of patients updated
     */
    public function enable_sms_for_all_patients()
    {
        // Check if SMS is configured
        if (!$this->is_sms_configured()) {
            log_activity('SMS not configured - cannot enable for all patients');
            return 0;
        }

        $this->db->where('channel_sms', 0);
        $this->db->update(db_prefix() . $this->table_preferences, ['channel_sms' => 1]);

        $affected = $this->db->affected_rows();
        log_activity("SMS enabled for {$affected} existing patient(s)");

        return $affected;
    }

    /**
     * Enable WhatsApp notifications for all existing patients
     * Only call this after configuring WhatsApp API credentials
     *
     * @return int Number of patients updated
     */
    public function enable_whatsapp_for_all_patients()
    {
        // Check if WhatsApp is configured
        if (!$this->is_whatsapp_configured()) {
            log_activity('WhatsApp not configured - cannot enable for all patients');
            return 0;
        }

        $this->db->where('channel_whatsapp', 0);
        $this->db->update(db_prefix() . $this->table_preferences, ['channel_whatsapp' => 1]);

        $affected = $this->db->affected_rows();
        log_activity("WhatsApp enabled for {$affected} existing patient(s)");

        return $affected;
    }

    // ==================== WEIGHT REMINDER ====================

    /**
     * Get patients who need weight reminder today
     */
    public function get_patients_for_weight_reminder()
    {
        $today = strtolower(date('l')); // monday, tuesday, etc.
        $current_time = date('H:i:00');

        // Select from dietic_patients directly (has email and phone columns)
        // Join with contacts to get firstname/lastname
        $this->db->select('p.*, prefs.*, p.email, p.phone as phonenumber, c.firstname, c.lastname');
        $this->db->from(db_prefix() . $this->table_preferences . ' as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('prefs.reminder_weight', 1);
        $this->db->where('prefs.reminder_weight_day', $today);
        $this->db->where('prefs.reminder_weight_time', $current_time);

        $results = $this->db->get()->result();

        // Filtre anti-doublons : exclure les patients qui ont déjà reçu ce rappel aujourd'hui
        $filtered = [];
        foreach ($results as $patient) {
            if (!$this->was_sent_today($patient->patient_id, 'reminder_weight')) {
                $filtered[] = $patient;
            }
        }

        return $filtered;
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
                'whatsapp' => $patient->channel_whatsapp,
                'push' => $patient->channel_push ?? 1
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

        // Select from dietic_patients directly (has email and phone columns)
        // Join with contacts to get firstname/lastname
        $this->db->select('p.*, prefs.*, p.email, p.phone as phonenumber, c.firstname, c.lastname');
        $this->db->from(db_prefix() . $this->table_preferences . ' as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('prefs.reminder_water', 1);
        $this->db->like('prefs.reminder_water_times', $current_time);

        $results = $this->db->get()->result();

        // Filtre anti-doublons : exclure les patients qui ont déjà reçu un rappel eau cette heure
        $filtered = [];
        foreach ($results as $patient) {
            if (!$this->was_sent_this_hour($patient->patient_id, 'reminder_water')) {
                $filtered[] = $patient;
            }
        }

        return $filtered;
    }

    /**
     * Get patients who need meal reminder at current time
     *
     * @param string $meal_type Type of meal: 'breakfast', 'lunch', or 'dinner'
     * @return array
     */
    public function get_patients_for_meal_reminder($meal_type)
    {
        // Fenêtre de 5 minutes pour capturer les rappels
        // Si cron passe à 22:25, on envoie les rappels entre 22:20 et 22:25
        $current_time = date('H:i:00');
        $time_5min_ago = date('H:i:00', strtotime('-5 minutes'));

        $column_enabled = 'reminder_' . $meal_type;
        $column_time = 'reminder_' . $meal_type . '_time';

        // Select from dietic_patients directly (has email and phone columns)
        // Join with contacts to get firstname/lastname
        $this->db->select('p.*, prefs.*, p.email, p.phone as phonenumber, c.firstname, c.lastname');
        $this->db->from(db_prefix() . $this->table_preferences . ' as prefs');
        $this->db->join(db_prefix() . 'dietic_patients as p', 'p.id = prefs.patient_id');
        $this->db->join(db_prefix() . 'contacts as c', 'c.userid = p.client_id AND c.is_primary = 1', 'left');
        $this->db->where('prefs.' . $column_enabled, 1);

        // Fenêtre de 5 minutes : entre time_5min_ago et current_time
        $this->db->where('prefs.' . $column_time . ' >', $time_5min_ago);
        $this->db->where('prefs.' . $column_time . ' <=', $current_time);

        $results = $this->db->get()->result();

        // Filtre anti-doublons : exclure les patients qui ont déjà reçu ce rappel aujourd'hui
        $filtered = [];
        foreach ($results as $patient) {
            if (!$this->was_sent_today($patient->patient_id, 'reminder_' . $meal_type)) {
                $filtered[] = $patient;
            }
        }

        return $filtered;
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

    /**
     * Vérifie si une notification a déjà été envoyée aujourd'hui
     * Protection anti-doublons pour éviter d'envoyer plusieurs fois le même rappel
     *
     * @param int $patient_id ID du patient
     * @param string $notification_type Type de notification
     * @return bool True si déjà envoyé aujourd'hui
     */
    private function was_sent_today($patient_id, $notification_type)
    {
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        // Cherche dans dietic_notification_logs (table réellement utilisée pour les logs)
        $this->db->select('COUNT(*) as count');
        $this->db->from(db_prefix() . 'dietic_notification_logs');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('notification_type', $notification_type);
        $this->db->where('created_at >=', $today_start);
        $this->db->where('created_at <=', $today_end);
        $this->db->where('status', 'sent'); // Ne compter que les notifications réussies

        $query = $this->db->get();
        $result = $query->row();

        return ($result && $result->count > 0);
    }

    /**
     * Vérifie si une notification a été envoyée dans la dernière heure
     * Protection anti-doublons pour les rappels H-1 de consultation
     *
     * @param int $patient_id ID du patient
     * @param string $notification_type Type de notification
     * @return bool True si déjà envoyé dans la dernière heure
     */
    private function was_sent_last_hour($patient_id, $notification_type)
    {
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));

        // Cherche dans dietic_notification_logs (table réellement utilisée pour les logs)
        $this->db->select('COUNT(*) as count');
        $this->db->from(db_prefix() . 'dietic_notification_logs');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('notification_type', $notification_type);
        $this->db->where('created_at >=', $one_hour_ago);
        $this->db->where('status', 'sent'); // Ne compter que les notifications réussies

        $query = $this->db->get();
        $result = $query->row();

        return ($result && $result->count > 0);
    }

    /**
     * Vérifie si une notification a été envoyée dans l'heure courante
     * Protection anti-doublons pour les rappels d'eau (plusieurs par jour)
     *
     * @param int $patient_id ID du patient
     * @param string $notification_type Type de notification
     * @return bool True si déjà envoyé cette heure
     */
    private function was_sent_this_hour($patient_id, $notification_type)
    {
        $current_hour_start = date('Y-m-d H:00:00');
        $current_hour_end = date('Y-m-d H:59:59');

        // Cherche dans dietic_notification_logs (table réellement utilisée pour les logs)
        $this->db->select('COUNT(*) as count');
        $this->db->from(db_prefix() . 'dietic_notification_logs');
        $this->db->where('patient_id', $patient_id);
        $this->db->where('notification_type', $notification_type);
        $this->db->where('created_at >=', $current_hour_start);
        $this->db->where('created_at <=', $current_hour_end);
        $this->db->where('status', 'sent'); // Ne compter que les notifications réussies

        $query = $this->db->get();
        $result = $query->row();

        return ($result && $result->count > 0);
    }

    /**
     * Send meal reminder (breakfast, lunch, dinner)
     *
     * @param object $patient Patient object with preferences
     * @param string $meal_type Type of meal: 'breakfast', 'lunch', or 'dinner'
     * @return array
     */
    public function send_meal_reminder($patient, $meal_type)
    {
        $meal_config = [
            'breakfast' => [
                'icon' => '🥐',
                'title' => 'Petit Dejeuner',
                'messages' => [
                    "C est l heure du petit dejeuner ! Un bon depart pour une belle journee.",
                    "Bonjour ! N oubliez pas votre petit dejeuner, le repas le plus important de la journee.",
                    "Reveillez vos papilles ! Votre petit dejeuner vous attend.",
                    "Prenez le temps de bien dejeuner ce matin. Votre corps a besoin d energie !"
                ]
            ],
            'lunch' => [
                'icon' => '🍽️',
                'title' => 'Dejeuner',
                'messages' => [
                    "C est l heure du dejeuner ! Prenez une pause bien meritee.",
                    "Il est midi ! N oubliez pas de dejeuner pour garder votre energie.",
                    "Pause dejeuner ! Rechargez vos batteries avec un bon repas.",
                    "Midi sonne ! Pensez a vous restaurer pour tenir jusqu au soir."
                ]
            ],
            'dinner' => [
                'icon' => '🍲',
                'title' => 'Diner',
                'messages' => [
                    "C est l heure du diner ! Terminez la journee avec un bon repas.",
                    "Le diner est servi ! Pensez a manger leger ce soir.",
                    "Bonsoir ! N oubliez pas votre diner avant de vous reposer.",
                    "Il est temps de diner. Un repas equilibre pour une bonne nuit !"
                ]
            ]
        ];

        if (!isset($meal_config[$meal_type])) {
            log_activity("send_meal_reminder: Invalid meal type '{$meal_type}'");
            return ['success' => false, 'error' => 'Invalid meal type'];
        }

        $config = $meal_config[$meal_type];
        $random_message = $config['messages'][array_rand($config['messages'])];

        $icon = $config['icon'];
        $title = $config['title'];

        $message = "Bonjour {$patient->firstname},\n\n{$icon} {$random_message}\n\n";
        $message .= "Restez fidele a vos objectifs nutritionnels !\n\n";
        $message .= "Bon appetit !";

        $result = $this->send_notification([
            'patient_id' => $patient->patient_id,
            'type' => 'reminder_' . $meal_type,
            'subject' => "{$icon} Rappel {$title}",
            'message' => $message,
            'email' => $patient->email,
            'phone' => $patient->phonenumber,
            'channels' => [
                'email' => $patient->channel_email,
                'sms' => $patient->channel_sms,
                'whatsapp' => $patient->channel_whatsapp,
                'push' => $patient->channel_push
            ],
            'push_data' => [
                'meal_type' => $meal_type,
                'url' => site_url('dietetic/portal')
            ]
        ]);

        return $result;
    }

    /**
     * Send broadcast meal reminder to ALL users (web + mobile)
     * Uses OneSignal segment broadcasting instead of individual notifications
     *
     * @param string $meal_type Type of meal: 'breakfast', 'lunch', or 'dinner'
     * @return array Result with success status and details
     */
    public function send_broadcast_meal_reminder($meal_type)
    {
        $meal_config = [
            'breakfast' => [
                'icon' => '🥐',
                'title' => 'Petit Dejeuner',
                'messages' => [
                    "C est l heure du petit dejeuner ! Un bon depart pour une belle journee.",
                    "Bonjour ! N oubliez pas votre petit dejeuner, le repas le plus important de la journee.",
                    "Reveillez vos papilles ! Votre petit dejeuner vous attend.",
                    "Prenez le temps de bien dejeuner ce matin. Votre corps a besoin d energie !"
                ]
            ],
            'lunch' => [
                'icon' => '🍽️',
                'title' => 'Dejeuner',
                'messages' => [
                    "C est l heure du dejeuner ! Prenez une pause bien meritee.",
                    "Il est midi ! N oubliez pas de dejeuner pour garder votre energie.",
                    "Pause dejeuner ! Rechargez vos batteries avec un bon repas.",
                    "Midi sonne ! Pensez a vous restaurer pour tenir jusqu au soir."
                ]
            ],
            'dinner' => [
                'icon' => '🍲',
                'title' => 'Diner',
                'messages' => [
                    "C est l heure du diner ! Terminez la journee avec un bon repas.",
                    "Le diner est servi ! Pensez a manger leger ce soir.",
                    "Bonsoir ! N oubliez pas votre diner avant de vous reposer.",
                    "Il est temps de diner. Un repas equilibre pour une bonne nuit !"
                ]
            ]
        ];

        if (!isset($meal_config[$meal_type])) {
            log_activity("send_broadcast_meal_reminder: Invalid meal type '{$meal_type}'");
            return ['success' => false, 'error' => 'Invalid meal type'];
        }

        $config = $meal_config[$meal_type];
        $random_message = $config['messages'][array_rand($config['messages'])];

        $icon = $config['icon'];
        $title = $config['title'];
        $subject = "{$icon} Rappel {$title}";

        // Message without personalization (for broadcast)
        $message = "{$icon} {$random_message}\n\nRestez fidele a vos objectifs nutritionnels !\n\nBon appetit !";

        log_activity("send_broadcast_meal_reminder: Sending {$meal_type} reminder to ALL users");

        try {
            // Load OneSignal library
            $this->load->library('dietetic/onesignal_cloud_messaging');

            if (!$this->onesignal_cloud_messaging->is_enabled()) {
                throw new Exception('Push notifications are not enabled');
            }

            // Send to ALL users using OneSignal segment
            $result = $this->onesignal_cloud_messaging->send_to_segment(
                'All', // OneSignal built-in segment for all subscribed users
                $subject,
                $message,
                [
                    'type' => 'reminder_' . $meal_type,
                    'meal_type' => $meal_type,
                    'timestamp' => date('Y-m-d H:i:s')
                ],
                [] // No click_action URL - app will open to main screen
            );

            if ($result['success']) {
                log_activity("send_broadcast_meal_reminder: {$meal_type} reminder sent successfully - Recipients: " . ($result['recipients'] ?? 'unknown'));

                // Log the broadcast in notification logs (patient_id = 0 for broadcast)
                $this->db->insert(db_prefix() . $this->table_logs, [
                    'patient_id' => 0, // 0 = broadcast to all
                    'notification_type' => 'reminder_' . $meal_type . '_broadcast',
                    'channel' => 'push',
                    'recipient' => 'all_users',
                    'subject' => $subject,
                    'message' => $message,
                    'status' => 'sent',
                    'created_at' => date('Y-m-d H:i:s'),
                    'sent_at' => date('Y-m-d H:i:s'),
                    'external_id' => $result['notification_id'] ?? null
                ]);

                return ['success' => true, 'recipients' => $result['recipients'] ?? 0];
            } else {
                log_activity("send_broadcast_meal_reminder: Failed to send {$meal_type} reminder - Error: " . ($result['error'] ?? 'unknown'));

                // Log the failure
                $this->db->insert(db_prefix() . $this->table_logs, [
                    'patient_id' => 0,
                    'notification_type' => 'reminder_' . $meal_type . '_broadcast',
                    'channel' => 'push',
                    'recipient' => 'all_users',
                    'subject' => $subject,
                    'message' => $message,
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return ['success' => false, 'error' => $result['error'] ?? 'Unknown error'];
            }
        } catch (Exception $e) {
            log_activity("send_broadcast_meal_reminder: Exception - " . $e->getMessage());

            // Log the exception
            $this->db->insert(db_prefix() . $this->table_logs, [
                'patient_id' => 0,
                'notification_type' => 'reminder_' . $meal_type . '_broadcast',
                'channel' => 'push',
                'recipient' => 'all_users',
                'subject' => $subject ?? 'Meal Reminder',
                'message' => $message ?? '',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
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
        if (!$patient) {
            log_activity("check_milestones: Patient {$patient_id} not found");
            return false;
        }

        $measurements = $this->dietetic_measurements_model->get_by_patient($patient_id);
        if (empty($measurements)) {
            log_activity("check_milestones: No measurements for patient {$patient_id}");
            return false;
        }

        // Get first and last measurement
        $first_measurement = end($measurements);
        $latest_measurement = reset($measurements);

        $starting_weight = floatval($first_measurement->weight);
        $current_weight = floatval($latest_measurement->weight);
        $weight_lost = $starting_weight - $current_weight;

        log_activity("check_milestones: Patient {$patient_id} - Starting: {$starting_weight}kg, Current: {$current_weight}kg, Lost: {$weight_lost}kg");

        // Only process if weight was actually lost
        if ($weight_lost <= 0) {
            log_activity("check_milestones: No weight loss for patient {$patient_id}");
            return false;
        }

        // Check weight loss milestones
        $milestones_to_check = [
            5 => 'weight_loss_5kg',
            10 => 'weight_loss_10kg',
            15 => 'weight_loss_15kg',
            20 => 'weight_loss_20kg',
            25 => 'weight_loss_25kg'
        ];

        $milestones_detected = 0;
        foreach ($milestones_to_check as $kg => $milestone_type) {
            if ($weight_lost >= $kg) {
                $milestone_id = $this->record_milestone($patient_id, $milestone_type, [
                    'starting_weight' => $starting_weight,
                    'current_weight' => $current_weight,
                    'weight_lost' => $weight_lost
                ]);

                if ($milestone_id) {
                    $milestones_detected++;
                    log_activity("check_milestones: NEW milestone {$milestone_type} detected for patient {$patient_id}");
                }
            }
        }

        log_activity("check_milestones: {$milestones_detected} new milestone(s) detected for patient {$patient_id}");
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
            log_activity("record_milestone: Milestone {$milestone_type} already recorded for patient {$patient_id} (ID: {$existing->id})");
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

        log_activity("record_milestone: Creating milestone {$milestone_type} for patient {$patient_id} - Weight lost: " . ($data['weight_lost'] ?? 'N/A') . "kg");

        if ($this->db->insert(db_prefix() . $this->table_milestones, $milestone_data)) {
            $milestone_id = $this->db->insert_id();
            log_activity("record_milestone: Milestone {$milestone_type} recorded successfully (ID: {$milestone_id})");

            // Send celebration notification
            try {
                $this->send_milestone_notification($patient_id, $milestone_type, $data);
                log_activity("record_milestone: Notification sent for milestone {$milestone_id}");
            } catch (Exception $e) {
                log_activity("record_milestone: Error sending notification: " . $e->getMessage());
            }

            // Mark as notified
            $this->db->where('id', $milestone_id);
            $this->db->update(db_prefix() . $this->table_milestones, ['notified' => 1]);

            return $milestone_id;
        }

        log_activity("record_milestone: Failed to insert milestone {$milestone_type} for patient {$patient_id}");
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
                'whatsapp' => $prefs->channel_whatsapp,
                'push' => $prefs->channel_push ?? 1 // Enable push by default
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/dashboard'),
                'milestone_type' => $milestone_type,
                'weight_lost' => $data['weight_lost'] ?? null
            ]
        ]);
    }

    // ==================== WELCOME NOTIFICATION ====================

    /**
     * Send welcome notification to new patient
     * Sends Push, SMS, and WhatsApp notifications when a patient account is created
     *
     * @param int $patient_id
     * @return array Results of notification sending
     */
    public function send_welcome_notification($patient_id)
    {
        // Load models
        $this->load->model('dietetic/dietetic_patients_model');

        // Get patient data
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient || !$patient->client) {
            log_activity('send_welcome_notification: Patient or client not found [ID: ' . $patient_id . ']');
            return ['success' => false, 'error' => 'Patient not found'];
        }

        // Get or create notification preferences
        $prefs = $this->get_preferences($patient_id);
        if (!$prefs) {
            $this->create_default_preferences($patient_id);
            $prefs = $this->get_preferences($patient_id);
        }

        // Get primary contact
        $contact = $this->get_client_primary_contact($patient->client_id);
        $email = $contact ? $contact->email : $patient->client->email ?? null;
        $phone = $contact ? $contact->phonenumber : $patient->client->phonenumber ?? null;

        // Get client name
        $firstname = $patient->client->company; // In Perfex, company name is often used for individual clients
        if ($contact && !empty($contact->firstname)) {
            $firstname = $contact->firstname;
        }

        // Build welcome message
        $subject = "🎉 Bienvenue dans votre espace DietSénégal !";

        $message = "Bonjour {$firstname},\n\n";
        $message .= "Bienvenue dans votre espace personnel DietSénégal ! 🎉\n\n";
        $message .= "Votre compte a été créé avec succès. Vous pouvez maintenant accéder à votre portail patient pour :\n\n";
        $message .= "✅ Suivre votre évolution de poids\n";
        $message .= "✅ Enregistrer vos mesures\n";
        $message .= "✅ Suivre vos repas et hydratation quotidienne\n";
        $message .= "✅ Consulter vos programmes nutritionnels\n";
        $message .= "✅ Gérer vos rendez-vous\n\n";
        $message .= "🔗 Accédez à votre portail :\n";
        $message .= site_url('dietetic/portal') . "\n\n";

        // Add credentials info if available
        if (!empty($patient->client->password)) {
            $message .= "📧 Email : {$email}\n";
            $message .= "🔑 Mot de passe : (envoyé séparément par email)\n\n";
        }

        $message .= "💪 Nous sommes ravis de vous accompagner dans votre parcours vers une meilleure santé !\n\n";
        $message .= "L'équipe DietSénégal";

        // Send notification through all enabled channels
        $result = $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'welcome',
            'subject' => $subject,
            'message' => $message,
            'email' => $email,
            'phone' => $phone,
            'channels' => [
                'email' => $prefs->channel_email ?? 1,
                'sms' => $prefs->channel_sms ?? 0,
                'whatsapp' => $prefs->channel_whatsapp ?? 0,
                'push' => $prefs->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/dashboard'),
                'action' => 'welcome'
            ]
        ]);

        log_activity('Welcome notification sent to patient [ID: ' . $patient_id . ']');

        return $result;
    }

    // ==================== GENERAL NOTIFICATIONS ====================

    /**
     * Send notification through all enabled channels
     */
    public function send_notification($params)
    {
        $results = [];

        // Debug logging
        log_activity('[SEND_NOTIFICATION] Type: ' . ($params['type'] ?? 'unknown') .
                    ', Patient: ' . ($params['patient_id'] ?? 'unknown') .
                    ', Phone: ' . ($params['phone'] ?? 'empty') .
                    ', SMS channel: ' . (isset($params['channels']['sms']) ? $params['channels']['sms'] : 'not set') .
                    ', Email channel: ' . (isset($params['channels']['email']) ? $params['channels']['email'] : 'not set'));

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

        // SMS (use short message if provided, otherwise use full message)
        if (!empty($params['channels']['sms']) && !empty($params['phone'])) {
            $sms_message = $params['message_sms'] ?? $params['message'];
            log_activity('[SEND_NOTIFICATION] SMS channel check passed, calling send_sms_notification()');
            $results['sms'] = $this->send_sms_notification(
                $params['patient_id'],
                $params['type'],
                $params['phone'],
                $sms_message
            );
        } else {
            log_activity('[SEND_NOTIFICATION] SMS channel check FAILED - SMS channel: ' .
                        (isset($params['channels']['sms']) ? ($params['channels']['sms'] ? 'YES' : 'NO') : 'NOT SET') .
                        ', Phone: ' . ($params['phone'] ?? 'EMPTY'));
        }

        // WhatsApp (use short message if provided, otherwise use full message)
        if (!empty($params['channels']['whatsapp']) && !empty($params['phone'])) {
            $whatsapp_message = $params['message_sms'] ?? $params['message'];
            $results['whatsapp'] = $this->send_whatsapp_notification(
                $params['patient_id'],
                $params['type'],
                $params['phone'],
                $whatsapp_message
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
        log_activity('[SMS_NOTIFICATION] Starting - Patient: ' . $patient_id . ', Type: ' . $type . ', Phone: ' . $phone);

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
            log_activity('[SMS_NOTIFICATION] Getting LAM SMS settings...');
            $account_id = $this->get_setting('sms_lam_account_id');
            $password = $this->get_setting('sms_lam_password');
            $sender_id = $this->get_setting('sms_lam_sender_id') ?: 'API_LAMSMS';

            log_activity('[SMS_NOTIFICATION] Settings retrieved - Account ID: ' . ($account_id ? 'SET' : 'EMPTY') .
                        ', Password: ' . ($password ? 'SET' : 'EMPTY') .
                        ', Sender ID: ' . $sender_id);

            if (empty($account_id) || empty($password)) {
                throw new Exception('LAM SMS credentials not configured (account_id and password required)');
            }

            // LAM SMS API integration
            log_activity('[SMS_NOTIFICATION] Calling send_lam_sms()...');
            $result = $this->send_lam_sms($phone, $message, $account_id, $password, $sender_id);
            log_activity('[SMS_NOTIFICATION] send_lam_sms() returned - Success: ' . ($result['success'] ? 'YES' : 'NO'));

            if ($result['success']) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
            } else {
                $log_data['status'] = 'failed';
                $log_data['error_message'] = $result['error'] ?? 'SMS sending failed';
            }
        } catch (Exception $e) {
            log_activity('[SMS_NOTIFICATION] EXCEPTION: ' . $e->getMessage());
            $log_data['status'] = 'failed';
            $log_data['error_message'] = $e->getMessage();
        }

        log_activity('[SMS_NOTIFICATION] Inserting into notification logs - Status: ' . $log_data['status']);
        $this->db->insert(db_prefix() . $this->table_logs, $log_data);

        $insert_success = $this->db->affected_rows() > 0;
        log_activity('[SMS_NOTIFICATION] DB insert result: ' . ($insert_success ? 'SUCCESS' : 'FAILED') .
                    ' - Returning: ' . ($log_data['status'] === 'sent' ? 'true' : 'false'));

        return $log_data['status'] === 'sent';
    }

    /**
     * LAM SMS API call
     * Documentation: https://developers.lafricamobile.com/docs/sms/introduction
     */
    private function send_lam_sms($phone, $message, $account_id, $password, $sender_id = 'API_LAMSMS')
    {
        log_activity('[LAM_SMS] Starting - Phone: ' . $phone . ', Message length: ' . strlen($message));

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

        log_activity('[LAM_SMS] Phone formatted: ' . $phone);

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

        log_activity('[LAM_SMS] Data prepared, encoding JSON...');

        $json_data = json_encode($data);
        if ($json_data === false) {
            log_activity('[LAM_SMS] JSON encoding FAILED: ' . json_last_error_msg());
            return ['success' => false, 'error' => 'JSON encoding failed: ' . json_last_error_msg()];
        }

        log_activity('[LAM_SMS] JSON encoded successfully, length: ' . strlen($json_data));

        $ch = curl_init($url);
        log_activity('[LAM_SMS] cURL initialized');

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ]
        ]);

        log_activity('[LAM_SMS] cURL options set, executing request...');

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        log_activity('[LAM_SMS] cURL executed - HTTP Code: ' . $http_code . ', Error: ' . ($curl_error ?: 'none'));

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
            $provider = $this->get_setting('whatsapp_provider') ?: 'lam';

            if ($provider == 'lam') {
                // LAM WhatsApp API
                $account_id = $this->get_setting('whatsapp_lam_account_id');
                $password = $this->get_setting('whatsapp_lam_password');
                $sender_number = $this->get_setting('whatsapp_lam_sender_number');

                if (empty($account_id) || empty($password)) {
                    throw new Exception('LAM WhatsApp credentials not configured (account_id and password required)');
                }

                $result = $this->send_lam_whatsapp($phone, $message, $account_id, $password, $sender_number);
            } else {
                // Other providers (Twilio, Meta, etc.)
                $api_key = $this->get_setting('whatsapp_api_key');
                if (empty($api_key)) {
                    throw new Exception('WhatsApp API key not configured');
                }
                $result = $this->send_whatsapp_api($phone, $message, $provider, $api_key);
            }

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
     * LAM WhatsApp API call
     * Documentation: https://developers.lafricamobile.com/docs/whatsapp
     */
    private function send_lam_whatsapp($phone, $message, $account_id, $password, $sender_number = null)
    {
        // LAM WhatsApp API endpoint
        $url = 'https://lamwhatsapp.lafricamobile.com/api';

        // Get additional settings
        $ret_url = $this->get_setting('whatsapp_lam_ret_url') ?: site_url('dietetic/whatsapp_callback');

        // Format phone number for LAM API
        // Ensure phone starts with country code (e.g., 221 for Senegal)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!preg_match('/^221/', $phone) && strlen($phone) == 9) {
            $phone = '221' . $phone;
        }

        // Add + prefix for WhatsApp format
        if (!preg_match('/^\+/', $phone)) {
            $phone = '+' . $phone;
        }

        // Prepare LAM WhatsApp API request
        $data = [
            'accountid' => $account_id,
            'password' => $password,
            'ret_id' => 'dietetic_wa_' . time(),
            'ret_url' => $ret_url,
            'text' => $message,
            'to' => [
                [
                    'ret_id_1' => $phone
                ]
            ]
        ];

        // Add sender number if provided
        if ($sender_number) {
            $data['sender'] = $sender_number;
        }

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
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log the request for debugging
        log_activity('LAM WhatsApp sent to ' . $phone . ' - HTTP Code: ' . $http_code . ' - Response: ' . $response);

        if ($http_code == 200 || $http_code == 201) {
            $response_data = json_decode($response, true);
            return ['success' => true, 'response' => $response_data];
        } else {
            $error_msg = $curl_error ?: $response;
            return ['success' => false, 'error' => $error_msg];
        }
    }

    /**
     * WhatsApp API call for other providers (Twilio, Meta Business API, etc.)
     */
    private function send_whatsapp_api($phone, $message, $provider, $api_key)
    {
        // Placeholder for other providers
        // Can be implemented based on specific provider requirements

        if ($provider == 'twilio') {
            // Twilio WhatsApp implementation
            return ['success' => false, 'error' => 'Twilio WhatsApp not yet implemented'];
        } elseif ($provider == 'meta') {
            // Meta Business API implementation
            return ['success' => false, 'error' => 'Meta WhatsApp not yet implemented'];
        } else {
            return ['success' => false, 'error' => 'Unknown WhatsApp provider: ' . $provider];
        }
    }

    /**
     * Send push notification via OneSignal
     * (Remplace Firebase pour compatibilité Median + Web)
     */
    private function send_push_notification($patient_id, $type, $title, $message, $data = [])
    {
        $log_data = [
            'patient_id' => $patient_id,
            'notification_type' => $type,
            'channel' => 'push',
            'recipient' => 'onesignal_player',
            'subject' => $title,
            'message' => $message,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Load OneSignal library (remplace Firebase)
            $this->load->library('dietetic/onesignal_cloud_messaging');

            if (!$this->onesignal_cloud_messaging->is_enabled()) {
                throw new Exception('Push notifications are not enabled');
            }

            // Prepare notification options
            $options = [
                'click_action' => $data['click_action'] ?? $data['url'] ?? site_url('dietetic/portal'),
                'icon' => $data['icon'] ?? base_url('uploads/company/favicon.png'),
            ];

            if (isset($data['image'])) {
                $options['image'] = $data['image'];
            }

            // Send to patient (all devices via OneSignal)
            $result = $this->onesignal_cloud_messaging->send_to_patient(
                $patient_id,
                $title,
                $message,
                $data,
                $options
            );

            if ($result['success']) {
                $log_data['status'] = 'sent';
                $log_data['sent_at'] = date('Y-m-d H:i:s');
                $log_data['recipient'] = ($result['recipients'] ?? 1) . ' device(s)';

                // Log notification ID for tracking
                if (isset($result['notification_id'])) {
                    $log_data['external_id'] = $result['notification_id'];
                }
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
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
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
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
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
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
            ]
        ]);
    }

    // ==================== RECIPE NOTIFICATIONS ====================

    /**
     * Notify patient when a recipe is assigned to them
     */
    public function notify_recipe_assigned($patient_id, $recipe_name, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_recommendation) {
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
        $message .= "🍽️ Votre diététicien {$dietitian_name} vous a recommandé une nouvelle recette :\n\n";
        $message .= "👨‍🍳 {$recipe_name}\n\n";
        $message .= "Découvrez cette recette savoureuse et adaptée à votre programme alimentaire !\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/recipes') . "\n\n";
        $message .= "Bon appétit ! 😋";

        return $this->send_notification_with_frontend([
            'patient_id' => $patient_id,
            'type' => 'recipe_assigned',
            'subject' => '🍽️ Nouvelle Recette Recommandée',
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'url' => site_url('dietetic/portal/recipes'),
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp
            ]
        ]);
    }

    /**
     * Notify dietitian when their recipe is approved
     */
    public function notify_recipe_approved($dietitian_id, $recipe_name)
    {
        try {
            $this->load->model('staff_model');
            $dietitian = $this->staff_model->get($dietitian_id);

            if (!$dietitian || empty($dietitian->email)) {
                log_activity("Diététicien non trouvé ou sans email: ID {$dietitian_id}");
                return false;
            }

            // Préparer le message
            $message = "Bonjour {$dietitian->firstname},\n\n";
            $message .= "Votre recette \"{$recipe_name}\" a été approuvée par l'administrateur.\n\n";
            $message .= "Elle est maintenant disponible dans votre liste de recettes et peut être assignée à vos patients.\n\n";
            $message .= "Consultez vos recettes :\n";
            $message .= admin_url('dietetic/recipes') . "\n\n";
            $message .= "Bonne journée !";

            // Envoyer l'email
            $this->load->library('email');
            $this->email->clear();
            $this->email->from(get_option('smtp_email'), get_option('companyname'));
            $this->email->to($dietitian->email);
            $this->email->subject("Recette Approuvée - {$recipe_name}");
            $this->email->message(nl2br($message));
            $email_sent = $this->email->send();

            if ($email_sent) {
                log_activity("Email envoyé au diététicien {$dietitian->firstname} {$dietitian->lastname} - Recette approuvée: {$recipe_name}");
            } else {
                log_activity("Échec envoi email au diététicien {$dietitian->firstname} {$dietitian->lastname} - Recette approuvée");
            }

            return $email_sent;

        } catch (Exception $e) {
            log_activity("Erreur notification diététicien (approbation recette): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify dietitian when their recipe is rejected
     */
    public function notify_recipe_rejected($dietitian_id, $recipe_name, $reason = '')
    {
        try {
            $this->load->model('staff_model');
            $dietitian = $this->staff_model->get($dietitian_id);

            if (!$dietitian || empty($dietitian->email)) {
                log_activity("Diététicien non trouvé ou sans email: ID {$dietitian_id}");
                return false;
            }

            // Préparer le message
            $message = "Bonjour {$dietitian->firstname},\n\n";
            $message .= "Votre recette \"{$recipe_name}\" n'a pas été approuvée par l'administrateur.\n\n";

            if (!empty($reason)) {
                $message .= "Raison du rejet:\n";
                $message .= "{$reason}\n\n";
            }

            $message .= "Vous pouvez modifier votre recette et la soumettre à nouveau.\n\n";
            $message .= "Consultez vos recettes :\n";
            $message .= admin_url('dietetic/recipes') . "\n\n";
            $message .= "Bonne journée !";

            // Envoyer l'email
            $this->load->library('email');
            $this->email->clear();
            $this->email->from(get_option('smtp_email'), get_option('companyname'));
            $this->email->to($dietitian->email);
            $this->email->subject("Recette Non Approuvée - {$recipe_name}");
            $this->email->message(nl2br($message));
            $email_sent = $this->email->send();

            if ($email_sent) {
                log_activity("Email envoyé au diététicien {$dietitian->firstname} {$dietitian->lastname} - Recette rejetée: {$recipe_name}");
            } else {
                log_activity("Échec envoi email au diététicien {$dietitian->firstname} {$dietitian->lastname} - Recette rejetée");
            }

            return $email_sent;

        } catch (Exception $e) {
            log_activity("Erreur notification diététicien (rejet recette): " . $e->getMessage());
            return false;
        }
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

        // Get primary contact for email and phone (instead of using client directly)
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? "{$contact->firstname} {$contact->lastname}" : $client->company;

        $formatted_date = date('d/m/Y', strtotime($consultation_date));
        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : '';

        // Full message for email and frontend
        $message_full = "Bonjour {$contact_name},\n\n";
        $message_full .= "📅 Une nouvelle consultation a été planifiée :\n\n";
        $message_full .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message_full .= "📆 Date : {$formatted_date}\n";
        if ($formatted_time) {
            $message_full .= "🕐 Heure : {$formatted_time}\n";
        }
        $message_full .= "📝 Type : {$consultation_type}\n\n";
        $message_full .= "Nous avons hâte de vous voir ! 😊";

        // Extract first name for SMS personalization
        $firstname = $contact ? $contact->firstname : explode(' ', $contact_name)[0];

        // Short SMS message (max 160 characters)
        $message_sms = "Bonjour {$firstname}, consultation prevue le {$formatted_date}";
        if ($formatted_time) {
            $message_sms .= " a {$formatted_time}";
        }
        $message_sms .= " avec {$dietitian_name}. A bientot !";

        // Debug logging
        log_activity('Consultation notification - Patient ID: ' . $patient_id . ', Phone: ' . $contact_phone . ', SMS enabled: ' . ($preferences->channel_sms ? 'YES' : 'NO') . ', SMS length: ' . strlen($message_sms));

        return $this->send_notification_with_frontend([
            'patient_id' => $patient_id,
            'type' => 'consultation_scheduled',
            'subject' => '📅 Nouvelle Consultation Planifiée',
            'message' => $message_full,
            'message_sms' => $message_sms,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'url' => site_url('dietetic/portal/consultations'),
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1 // Fixed: Added missing push channel
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

        // Get primary contact for email and phone
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? "{$contact->firstname} {$contact->lastname}" : $client->company;

        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : 'à confirmer';

        // Full message for email
        $message_full = "Bonjour {$contact_name},\n\n";
        $message_full .= "⏰ Rappel : Votre consultation est demain !\n\n";
        $message_full .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message_full .= "🕐 Heure : {$formatted_time}\n\n";
        $message_full .= "N'oubliez pas votre rendez-vous ! 📋";

        // Extract first name for SMS personalization
        $firstname = $contact ? $contact->firstname : explode(' ', $contact_name)[0];

        // Short SMS message
        $message_sms = "Bonjour {$firstname}, rappel : consultation demain à {$formatted_time} avec {$dietitian_name}.";

        return $this->send_notification_with_frontend([
            'patient_id' => $patient_id,
            'type' => 'consultation_reminder_day',
            'subject' => '⏰ Rappel : Consultation Demain',
            'message' => $message_full,
            'message_sms' => $message_sms,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'url' => site_url('dietetic/portal/consultations'),
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
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

        // Get primary contact for email and phone
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? "{$contact->firstname} {$contact->lastname}" : $client->company;

        $formatted_time = $consultation_time ? date('H:i', strtotime($consultation_time)) : 'bientôt';

        // Full message for email
        $message_full = "Bonjour {$contact_name},\n\n";
        $message_full .= "⏰ Votre consultation commence dans 1 heure !\n\n";
        $message_full .= "👨‍⚕️ Avec : {$dietitian_name}\n";
        $message_full .= "🕐 Heure : {$formatted_time}\n\n";
        $message_full .= "À tout de suite ! 😊";

        // Extract first name for SMS personalization
        $firstname = $contact ? $contact->firstname : explode(' ', $contact_name)[0];

        // Short SMS message
        $message_sms = "Bonjour {$firstname}, rappel : consultation dans 1h a {$formatted_time} avec {$dietitian_name}.";

        return $this->send_notification_with_frontend([
            'patient_id' => $patient_id,
            'type' => 'consultation_reminder_hour',
            'subject' => '⏰ Consultation dans 1 heure',
            'message' => $message_full,
            'message_sms' => $message_sms,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'url' => site_url('dietetic/portal/consultations'),
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
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

        // Get primary contact for email and phone
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? "{$contact->firstname} {$contact->lastname}" : $client->company;

        $formatted_date = date('d/m/Y', strtotime($consultation_date));

        // Full message for email
        $message_full = "Bonjour {$contact_name},\n\n";
        $message_full .= "❌ Votre consultation du {$formatted_date} avec {$dietitian_name} a été annulée.\n\n";
        if ($reason) {
            $message_full .= "Raison : {$reason}\n\n";
        }
        $message_full .= "Veuillez contacter votre diététicien pour reprogrammer.";

        // Extract first name for SMS personalization
        $firstname = $contact ? $contact->firstname : explode(' ', $contact_name)[0];

        // Short SMS message
        $message_sms = "Bonjour {$firstname}, consultation du {$formatted_date} annulee.";
        if ($reason && strlen($reason) < 80) {
            $message_sms .= " Raison: {$reason}";
        }

        return $this->send_notification_with_frontend([
            'patient_id' => $patient_id,
            'type' => 'consultation_cancelled',
            'subject' => '❌ Consultation Annulée',
            'message' => $message_full,
            'message_sms' => $message_sms,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'url' => site_url('dietetic/portal/consultations'),
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
            ]
        ]);
    }

    /**
     * Get consultations that need reminders (1 day before)
     */
    public function get_consultations_for_day_reminder()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // Extract TIME from consultation_date as virtual column 'consultation_time'
        $this->db->select('c.*, p.id as patient_id, p.client_id, s.firstname as dietitian_firstname, s.lastname as dietitian_lastname, TIME(c.consultation_date) as consultation_time');
        $this->db->from(db_prefix() . 'dietic_consultations c');
        $this->db->join(db_prefix() . 'dietic_patients p', 'c.patient_id = p.id', 'left');
        $this->db->join(db_prefix() . 'staff s', 'c.dietitian_id = s.staffid', 'left');
        $this->db->where('DATE(c.consultation_date)', $tomorrow);
        $this->db->where('c.status !=', 'cancelled');

        $results = $this->db->get()->result();

        // Filtre anti-doublons : exclure les patients qui ont déjà reçu le rappel J-1 aujourd'hui
        $filtered = [];
        foreach ($results as $consultation) {
            if (!$this->was_sent_today($consultation->patient_id, 'consultation_reminder_day')) {
                $filtered[] = $consultation;
            }
        }

        return $filtered;
    }

    /**
     * Get consultations that need reminders (1 hour before)
     */
    public function get_consultations_for_hour_reminder()
    {
        $now = date('Y-m-d H:i:s');
        $one_hour_later = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Extract TIME from consultation_date as virtual column 'consultation_time'
        $this->db->select('c.*, p.id as patient_id, p.client_id, s.firstname as dietitian_firstname, s.lastname as dietitian_lastname, TIME(c.consultation_date) as consultation_time');
        $this->db->from(db_prefix() . 'dietic_consultations c');
        $this->db->join(db_prefix() . 'dietic_patients p', 'c.patient_id = p.id', 'left');
        $this->db->join(db_prefix() . 'staff s', 'c.dietitian_id = s.staffid', 'left');
        // consultation_date is already a DATETIME containing both date and time
        $this->db->where('c.consultation_date <=', $one_hour_later);
        $this->db->where('c.consultation_date >', $now);
        $this->db->where('c.status !=', 'cancelled');

        $results = $this->db->get()->result();

        // Filtre anti-doublons : exclure les patients qui ont déjà reçu le rappel H-1 dans la dernière heure
        $filtered = [];
        foreach ($results as $consultation) {
            if (!$this->was_sent_last_hour($consultation->patient_id, 'consultation_reminder_hour')) {
                $filtered[] = $consultation;
            }
        }

        return $filtered;
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
     * Log a notification to the logs table
     *
     * @param int $patient_id
     * @param string $channel (email, sms, whatsapp, push)
     * @param string $notification_type
     * @param string $message
     * @param string $status (sent, failed, pending)
     * @param string|null $error_message
     * @return bool
     */
    public function log_notification($patient_id, $channel, $notification_type, $message, $status = 'sent', $error_message = null)
    {
        $data = [
            'patient_id' => $patient_id,
            'channel' => $channel,
            'notification_type' => $notification_type,
            'message' => $message,
            'status' => $status,
            'error_message' => $error_message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert(db_prefix() . $this->table_logs, $data);
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
     * Scan all patients and detect milestones retroactively
     */
    public function scan_all_patients_for_milestones()
    {
        $this->load->model('dietetic/dietetic_patients_model');

        // Get all patients
        $all_patients = $this->db->get(db_prefix() . 'dietic_patients')->result();

        $results = [
            'total_patients' => count($all_patients),
            'patients_checked' => 0,
            'milestones_detected' => 0,
            'errors' => []
        ];

        foreach ($all_patients as $patient) {
            try {
                $results['patients_checked']++;

                // Check milestones for this patient
                $milestones_found = $this->check_milestones_with_count($patient->id);
                $results['milestones_detected'] += $milestones_found;

            } catch (Exception $e) {
                $results['errors'][] = "Patient {$patient->id}: " . $e->getMessage();
                log_activity("Error scanning patient {$patient->id} for milestones: " . $e->getMessage());
            }
        }

        log_activity("Milestone scan completed: {$results['patients_checked']} patients checked, {$results['milestones_detected']} milestones detected");

        return $results;
    }

    /**
     * Check milestones and return count of new milestones detected
     */
    public function check_milestones_with_count($patient_id)
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->model('dietetic/dietetic_measurements_model');

        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return 0;

        // Get measurements directly from database (bypass permission check for admin scan)
        $this->db->where('patient_id', $patient_id);
        $this->db->order_by('measurement_date', 'DESC');
        $measurements = $this->db->get(db_prefix() . 'dietic_measurements')->result();

        if (empty($measurements)) return 0;

        // Get first and last measurement
        $first_measurement = end($measurements);
        $latest_measurement = reset($measurements);

        $starting_weight = floatval($first_measurement->weight);
        $current_weight = floatval($latest_measurement->weight);
        $weight_lost = $starting_weight - $current_weight;

        // Only process if weight was actually lost
        if ($weight_lost <= 0) return 0;

        // Check weight loss milestones
        $milestones_to_check = [
            5 => 'weight_loss_5kg',
            10 => 'weight_loss_10kg',
            15 => 'weight_loss_15kg',
            20 => 'weight_loss_20kg',
            25 => 'weight_loss_25kg'
        ];

        $count = 0;
        foreach ($milestones_to_check as $kg => $milestone_type) {
            if ($weight_lost >= $kg) {
                $milestone_id = $this->record_milestone($patient_id, $milestone_type, [
                    'starting_weight' => $starting_weight,
                    'current_weight' => $current_weight,
                    'weight_lost' => $weight_lost
                ]);

                if ($milestone_id) {
                    $count++;
                }
            }
        }

        return $count;
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
            'recipe_assigned' => 'fa-cutlery',
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

    // ==================== PUSH NOTIFICATION HELPERS ====================

    /**
     * Send appointment reminder push notification
     */
    public function send_appointment_reminder_push($patient_id, $appointment_date, $appointment_time, $dietitian_name = '')
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $prefs = $this->get_preferences($patient_id);

        $title = 'Rappel de rendez-vous';
        $message = "Bonjour {$client->company},\n\n";
        $message .= "N'oubliez pas votre rendez-vous de suivi nutritionnel ";
        $message .= "le {$appointment_date} à {$appointment_time}";
        if ($dietitian_name) {
            $message .= " avec {$dietitian_name}";
        }
        $message .= ".\n\nÀ bientôt ! 📅";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'consultation_reminder',
            'subject' => $title,
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $prefs->channel_email,
                'sms' => $prefs->channel_sms,
                'whatsapp' => $prefs->channel_whatsapp,
                'push' => $prefs->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/appointments'),
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time
            ]
        ]);
    }

    /**
     * Send weight tracking reminder push notification
     */
    public function send_weight_reminder_push($patient_id)
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $prefs = $this->get_preferences($patient_id);

        $title = 'Rappel de pesée 📊';
        $message = "Bonjour {$client->company},\n\n";
        $message .= "C'est le moment de vous peser ! ⚖️\n\n";
        $message .= "N'oubliez pas d'enregistrer votre poids dans votre journal de suivi.\n\n";
        $message .= "Chaque mesure compte pour suivre vos progrès ! 💪";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'reminder_weight',
            'subject' => $title,
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $prefs->channel_email,
                'sms' => $prefs->channel_sms,
                'whatsapp' => $prefs->channel_whatsapp,
                'push' => $prefs->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/weights')
            ]
        ]);
    }

    /**
     * Send new message notification
     */
    public function send_new_message_push($patient_id, $sender_name, $message_preview = '')
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $prefs = $this->get_preferences($patient_id);

        $title = 'Nouveau message 💬';
        $message = "Bonjour {$client->company},\n\n";
        $message .= "Vous avez reçu un nouveau message de {$sender_name}.\n\n";
        if ($message_preview) {
            $preview = strlen($message_preview) > 100 ? substr($message_preview, 0, 97) . '...' : $message_preview;
            $message .= "Aperçu : \"{$preview}\"\n\n";
        }
        $message .= "Consultez-le maintenant ! 📩";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'new_message',
            'subject' => $title,
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $prefs->channel_email,
                'sms' => $prefs->channel_sms,
                'whatsapp' => $prefs->channel_whatsapp,
                'push' => $prefs->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/messages'),
                'sender_name' => $sender_name
            ]
        ]);
    }

    // ==================== FOOD SURVEY NOTIFICATIONS ====================

    /**
     * Notify patient when a new food survey is assigned
     *
     * @param int $patient_id
     * @param string $survey_name
     * @param string $start_date
     * @param int $duration_days
     * @param string $dietitian_name
     * @return array|false
     */
    public function notify_food_survey_assigned($patient_id, $survey_name, $start_date, $duration_days, $dietitian_name)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_food_entry) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        // Get primary contact
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? ($contact->firstname ?: $client->company) : $client->company;

        $formatted_date = date('d/m/Y', strtotime($start_date));
        $end_date = date('d/m/Y', strtotime($start_date . ' + ' . $duration_days . ' days'));

        $subject = '📋 Nouvelle Enquête Alimentaire';

        $message = "Bonjour {$contact_name},\n\n";
        $message .= "Votre diététicien {$dietitian_name} vous a assigné une nouvelle enquête alimentaire :\n\n";
        $message .= "📝 {$survey_name}\n";
        $message .= "📅 Du {$formatted_date} au {$end_date}\n";
        $message .= "⏱️ Durée : {$duration_days} jour" . ($duration_days > 1 ? 's' : '') . "\n\n";
        $message .= "Cette enquête nous aidera à mieux comprendre vos habitudes alimentaires et à adapter votre programme.\n\n";
        $message .= "🔗 Accédez à votre enquête :\n";
        $message .= site_url('dietetic/portal/food_surveys') . "\n\n";
        $message .= "Merci de votre collaboration ! 💪\n\n";
        $message .= "L'équipe DietSénégal";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'food_survey_assigned',
            'subject' => $subject,
            'message' => $message,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/food_surveys'),
                'survey_name' => $survey_name,
                'start_date' => $start_date
            ]
        ]);
    }

    /**
     * Notify patient when a food survey is about to expire
     *
     * @param int $patient_id
     * @param string $survey_name
     * @param string $end_date
     * @return array|false
     */
    public function notify_food_survey_reminder($patient_id, $survey_name, $end_date)
    {
        $preferences = $this->get_preferences($patient_id);
        if (!$preferences || !$preferences->notify_food_entry) {
            return false;
        }

        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);
        if (!$client) return false;

        // Get primary contact
        $contact = $this->get_client_primary_contact($patient->client_id);
        $contact_email = $contact ? $contact->email : ($client->email ?? '');
        $contact_phone = $contact ? $contact->phonenumber : ($client->phonenumber ?? '');
        $contact_name = $contact ? ($contact->firstname ?: $client->company) : $client->company;

        $formatted_date = date('d/m/Y', strtotime($end_date));

        $subject = '⏰ Rappel : Enquête Alimentaire';

        $message = "Bonjour {$contact_name},\n\n";
        $message .= "Votre enquête alimentaire \"{$survey_name}\" se termine bientôt !\n\n";
        $message .= "📅 Date de fin : {$formatted_date}\n\n";
        $message .= "N'oubliez pas de compléter vos entrées pour nous aider à mieux vous accompagner.\n\n";
        $message .= "🔗 " . site_url('dietetic/portal/food_surveys') . "\n\n";
        $message .= "Merci ! 😊";

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'food_survey_reminder',
            'subject' => $subject,
            'message' => $message,
            'email' => $contact_email,
            'phone' => $contact_phone,
            'channels' => [
                'email' => $preferences->channel_email,
                'sms' => $preferences->channel_sms,
                'whatsapp' => $preferences->channel_whatsapp,
                'push' => $preferences->channel_push ?? 1
            ],
            'push_data' => [
                'url' => site_url('dietetic/portal/food_surveys'),
                'survey_name' => $survey_name
            ]
        ]);
    }

    // ==================== GENERIC NOTIFICATIONS ====================

    /**
     * Send generic push notification
     *
     * @param int $patient_id Patient ID
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $url Destination URL (optional)
     * @param array $data Additional data (optional)
     * @return array Results from notification sending
     */
    public function send_generic_push($patient_id, $title, $message, $url = '', $data = [])
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($patient_id);
        if (!$patient) return false;

        $this->load->model('clients_model');
        $client = $this->clients_model->get($patient->client_id);

        $prefs = $this->get_preferences($patient_id);

        return $this->send_notification([
            'patient_id' => $patient_id,
            'type' => 'generic',
            'subject' => $title,
            'message' => $message,
            'email' => $client->email,
            'phone' => $client->phonenumber,
            'channels' => [
                'email' => $prefs->channel_email ?? 0,
                'sms' => $prefs->channel_sms ?? 0,
                'whatsapp' => $prefs->channel_whatsapp ?? 0,
                'push' => $prefs->channel_push ?? 1
            ],
            'push_data' => array_merge([
                'url' => $url ?: site_url('dietetic/portal/dashboard')
            ], $data)
        ]);
    }
}
