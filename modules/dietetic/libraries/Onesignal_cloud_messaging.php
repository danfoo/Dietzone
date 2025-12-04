<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * OneSignal Cloud Messaging Library
 *
 * Remplace Firebase Cloud Messaging pour une meilleure intégration avec Median
 * Gère l'envoi de push notifications via OneSignal REST API
 *
 * @version 1.0
 * @author DietSenegal Team
 */
class Onesignal_cloud_messaging
{
    private $CI;

    // OneSignal API settings
    private $app_id;
    private $rest_api_key;
    private $user_auth_key; // Pour les opérations admin
    private $enabled;

    // API endpoints
    private $api_url = 'https://api.onesignal.com';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();

        // Load settings
        $this->load_settings();
    }

    /**
     * Load OneSignal settings from database
     */
    private function load_settings()
    {
        $settings = $this->CI->db->where_in('setting_key', [
            'push_enabled',
            'onesignal_app_id',
            'onesignal_rest_api_key',
            'onesignal_user_auth_key'
        ])->get(db_prefix() . 'dietic_notification_settings')->result();

        foreach ($settings as $setting) {
            switch ($setting->setting_key) {
                case 'push_enabled':
                    $this->enabled = (bool)$setting->setting_value;
                    break;
                case 'onesignal_app_id':
                    $this->app_id = $setting->setting_value;
                    break;
                case 'onesignal_rest_api_key':
                    $this->rest_api_key = $setting->setting_value;
                    break;
                case 'onesignal_user_auth_key':
                    $this->user_auth_key = $setting->setting_value;
                    break;
            }
        }
    }

    /**
     * Check if push notifications are enabled
     */
    public function is_enabled()
    {
        return $this->enabled && !empty($this->app_id) && !empty($this->rest_api_key);
    }

    /**
     * Send push notification to a single player (device)
     *
     * @param string $player_id OneSignal Player ID
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Result with success/error
     */
    public function send_to_player($player_id, $title, $body, $data = [], $options = [])
    {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'error' => 'OneSignal push notifications are not enabled or configured'
            ];
        }

        if (empty($player_id)) {
            return [
                'success' => false,
                'error' => 'Player ID is required'
            ];
        }

        // Build notification payload
        $payload = [
            'app_id' => $this->app_id,
            'include_player_ids' => [$player_id],
            'headings' => [
                'en' => $title,
                'fr' => $title
            ],
            'contents' => [
                'en' => $body,
                'fr' => $body
            ],
            'data' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'source' => 'dietzone'
            ], $data),
            'priority' => 10, // High priority
            'android_channel_id' => 'dietzone_notifications',
            'small_icon' => 'ic_notification', // Icon pour Android
            'large_icon' => $options['icon'] ?? null,
            'big_picture' => $options['image'] ?? null
        ];

        // Add URL/deep link
        if (isset($options['click_action'])) {
            $payload['url'] = $options['click_action'];
            $payload['web_url'] = $options['click_action'];
            $payload['app_url'] = $options['click_action'];
        }

        // Add iOS badge
        if (isset($options['badge'])) {
            $payload['ios_badgeType'] = 'Increase';
            $payload['ios_badgeCount'] = (int)$options['badge'];
        }

        // Add custom sound
        if (isset($options['sound'])) {
            $payload['ios_sound'] = $options['sound'];
            $payload['android_sound'] = $options['sound'];
        }

        return $this->send_notification($payload);
    }

    /**
     * Send push notification to multiple players
     *
     * @param array $player_ids Array of OneSignal Player IDs
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Result with success/error
     */
    public function send_to_players($player_ids, $title, $body, $data = [], $options = [])
    {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'error' => 'OneSignal push notifications are not enabled'
            ];
        }

        if (empty($player_ids) || !is_array($player_ids)) {
            return [
                'success' => false,
                'error' => 'Player IDs array is required'
            ];
        }

        // OneSignal peut envoyer à max 2000 players par requête
        if (count($player_ids) > 2000) {
            return [
                'success' => false,
                'error' => 'Maximum 2000 players per request'
            ];
        }

        // Build notification payload
        $payload = [
            'app_id' => $this->app_id,
            'include_player_ids' => $player_ids,
            'headings' => [
                'en' => $title,
                'fr' => $title
            ],
            'contents' => [
                'en' => $body,
                'fr' => $body
            ],
            'data' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
                'source' => 'dietzone'
            ], $data),
            'priority' => 10
        ];

        if (isset($options['click_action'])) {
            $payload['url'] = $options['click_action'];
        }

        return $this->send_notification($payload);
    }

    /**
     * Send push notification to a patient (all their devices)
     *
     * @param int $patient_id Patient ID
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Result with success/error
     */
    public function send_to_patient($patient_id, $title, $body, $data = [], $options = [])
    {
        // Get all active player IDs for this patient
        $players = $this->CI->db
            ->select('onesignal_player_id')
            ->from(db_prefix() . 'dietic_fcm_tokens')
            ->where('patient_id', $patient_id)
            ->where('is_active', 1)
            ->where('onesignal_player_id IS NOT NULL')
            ->get()
            ->result_array();

        if (empty($players)) {
            return [
                'success' => false,
                'error' => 'No active devices found for patient'
            ];
        }

        $player_ids = array_column($players, 'onesignal_player_id');
        return $this->send_to_players($player_ids, $title, $body, $data, $options);
    }

    /**
     * Send notification by segment
     *
     * @param array|string $segments Segment name(s) - 'All', 'Active Users', etc.
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data
     * @param array $options Additional options
     * @return array Result
     */
    public function send_to_segment($segments, $title, $body, $data = [], $options = [])
    {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'error' => 'OneSignal not configured'
            ];
        }

        if (!is_array($segments)) {
            $segments = [$segments];
        }

        $payload = [
            'app_id' => $this->app_id,
            'included_segments' => $segments,
            'headings' => [
                'en' => $title,
                'fr' => $title
            ],
            'contents' => [
                'en' => $body,
                'fr' => $body
            ],
            'data' => $data
        ];

        if (isset($options['click_action'])) {
            $payload['url'] = $options['click_action'];
        }

        return $this->send_notification($payload);
    }

    /**
     * Send notification request to OneSignal API
     *
     * @param array $payload Notification payload
     * @return array Result
     */
    private function send_notification($payload)
    {
        $url = $this->api_url . '/notifications';

        $headers = [
            'Authorization: Basic ' . $this->rest_api_key,
            'Content-Type: application/json; charset=utf-8'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_activity('[OneSignal] CURL Error: ' . $curl_error);
            return [
                'success' => false,
                'error' => 'CURL Error: ' . $curl_error
            ];
        }

        $response_data = json_decode($response, true);

        // Enhanced logging
        log_activity('[OneSignal] HTTP Code: ' . $http_code);
        log_activity('[OneSignal] Response: ' . substr($response, 0, 500));

        if ($http_code === 200 && isset($response_data['id'])) {
            log_activity('[OneSignal] ✓ SUCCESS - Notification ID: ' . $response_data['id']);
            log_activity('[OneSignal] Recipients: ' . ($response_data['recipients'] ?? 0));

            return [
                'success' => true,
                'notification_id' => $response_data['id'],
                'recipients' => $response_data['recipients'] ?? 0,
                'external_id' => $response_data['external_id'] ?? null
            ];
        } else {
            // Handle errors
            $errors = [];
            if (isset($response_data['errors'])) {
                $errors = $response_data['errors'];
            }

            $error_message = 'Unknown error';
            if (!empty($errors)) {
                $error_message = is_array($errors) ? implode(', ', $errors) : $errors;
            }

            log_activity('[OneSignal] ✗ ERROR: ' . $error_message);

            // Handle invalid player IDs
            if (isset($response_data['errors']['invalid_player_ids'])) {
                $invalid_ids = $response_data['errors']['invalid_player_ids'];
                log_activity('[OneSignal] Deactivating invalid player IDs: ' . implode(', ', $invalid_ids));
                $this->deactivate_player_ids($invalid_ids);
            }

            return [
                'success' => false,
                'error' => 'OneSignal Error: ' . $error_message,
                'http_code' => $http_code,
                'response' => $response_data
            ];
        }
    }

    /**
     * Register a new player (device)
     *
     * @param int $patient_id Patient ID
     * @param string $player_id OneSignal Player ID
     * @param string $device_type web, android, ios
     * @param array $device_info Additional device information
     * @return bool Success
     */
    public function register_player($patient_id, $player_id, $device_type = 'web', $device_info = [])
    {
        // Check if player already exists
        $existing = $this->CI->db
            ->where('onesignal_player_id', $player_id)
            ->get(db_prefix() . 'dietic_fcm_tokens')
            ->row();

        $data = [
            'patient_id' => $patient_id,
            'onesignal_player_id' => $player_id,
            'device_type' => $device_type,
            'device_name' => $device_info['device_name'] ?? null,
            'user_agent' => $device_info['user_agent'] ?? null,
            'ip_address' => $device_info['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            'is_active' => 1,
            'last_used_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            // Update existing player
            $this->CI->db
                ->where('id', $existing->id)
                ->update(db_prefix() . 'dietic_fcm_tokens', $data);
        } else {
            // Insert new player
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->CI->db->insert(db_prefix() . 'dietic_fcm_tokens', $data);
        }

        // Clear cache
        $cache_key = 'dietic_onesignal_players_' . $patient_id;
        $this->CI->app_object_cache->delete($cache_key);

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Unregister a player (device)
     *
     * @param string $player_id OneSignal Player ID
     * @return bool Success
     */
    public function unregister_player($player_id)
    {
        $this->CI->db
            ->where('onesignal_player_id', $player_id)
            ->delete(db_prefix() . 'dietic_fcm_tokens');

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Deactivate invalid player IDs
     *
     * @param array $player_ids Array of invalid player IDs
     */
    private function deactivate_player_ids($player_ids)
    {
        if (empty($player_ids)) {
            return;
        }

        $this->CI->db
            ->where_in('onesignal_player_id', $player_ids)
            ->update(db_prefix() . 'dietic_fcm_tokens', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Get player information from OneSignal API
     *
     * @param string $player_id Player ID
     * @return array|false Player info or false on error
     */
    public function get_player_info($player_id)
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $url = $this->api_url . '/players/' . $player_id . '?app_id=' . $this->app_id;

        $headers = [
            'Authorization: Basic ' . $this->rest_api_key
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            return json_decode($response, true);
        }

        return false;
    }

    /**
     * Get notification view statistics
     *
     * @param string $notification_id Notification ID
     * @return array|false Stats or false on error
     */
    public function get_notification_stats($notification_id)
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $url = $this->api_url . '/notifications/' . $notification_id . '?app_id=' . $this->app_id;

        $headers = [
            'Authorization: Basic ' . $this->rest_api_key
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $data = json_decode($response, true);
            return [
                'sent' => $data['successful'] ?? 0,
                'failed' => $data['failed'] ?? 0,
                'converted' => $data['converted'] ?? 0,
                'remaining' => $data['remaining'] ?? 0,
                'queued_at' => $data['queued_at'] ?? null,
                'send_after' => $data['send_after'] ?? null,
                'completed_at' => $data['completed_at'] ?? null
            ];
        }

        return false;
    }

    /**
     * Get OneSignal config for web client
     *
     * @return array|false Configuration or false if not enabled
     */
    public function get_web_config()
    {
        if (!$this->enabled || empty($this->app_id)) {
            return false;
        }

        return [
            'appId' => $this->app_id,
            'safari_web_id' => 'web.onesignal.auto.' . substr($this->app_id, 0, 8),
            'notifyButton' => [
                'enable' => false // Désactivé car on gère manuellement
            ],
            'autoRegister' => false, // On va gérer manuellement
            'autoResubscribe' => true,
            'notificationClickHandlerMatch' => 'origin',
            'notificationClickHandlerAction' => 'navigate'
        ];
    }

    /**
     * Tag a player with custom data
     *
     * @param string $player_id Player ID
     * @param array $tags Key-value pairs of tags
     * @return bool Success
     */
    public function tag_player($player_id, $tags)
    {
        if (!$this->is_enabled() || empty($tags)) {
            return false;
        }

        $url = $this->api_url . '/players/' . $player_id;

        $payload = [
            'app_id' => $this->app_id,
            'tags' => $tags
        ];

        $headers = [
            'Authorization: Basic ' . $this->rest_api_key,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $http_code === 200;
    }

    /**
     * Get app statistics
     *
     * @return array|false Statistics or false on error
     */
    public function get_app_stats()
    {
        if (!$this->is_enabled()) {
            return false;
        }

        $url = $this->api_url . '/apps/' . $this->app_id;

        $headers = [
            'Authorization: Basic ' . $this->rest_api_key
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $data = json_decode($response, true);
            return [
                'name' => $data['name'] ?? '',
                'players' => $data['players'] ?? 0,
                'messageable_players' => $data['messageable_players'] ?? 0,
                'updated_at' => $data['updated_at'] ?? null
            ];
        }

        return false;
    }
}
