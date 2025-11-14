<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Firebase Cloud Messaging (FCM) Library
 *
 * Handles sending push notifications via Firebase Cloud Messaging
 * Supports both FCM HTTP v1 API (Modern) and Legacy API
 *
 * @version 2.0
 * @author DietSenegal Team
 */
class Firebase_cloud_messaging
{
    private $CI;

    // Legacy API settings
    private $server_key;

    // Common settings
    private $vapid_key;
    private $project_id;
    private $enabled;

    // v1 API settings
    private $service_account_json;
    private $use_v1_api = false;
    private $access_token;
    private $access_token_expiry;

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
     * Load Firebase settings from database
     */
    private function load_settings()
    {
        $settings = $this->CI->db->where_in('setting_key', [
            'push_enabled',
            'firebase_server_key',
            'firebase_vapid_key',
            'firebase_project_id',
            'firebase_service_account_json',
            'firebase_use_v1_api'
        ])->get(db_prefix() . 'dietic_notification_settings')->result();

        foreach ($settings as $setting) {
            switch ($setting->setting_key) {
                case 'push_enabled':
                    $this->enabled = (bool)$setting->setting_value;
                    break;
                case 'firebase_server_key':
                    $this->server_key = $setting->setting_value;
                    break;
                case 'firebase_vapid_key':
                    $this->vapid_key = $setting->setting_value;
                    break;
                case 'firebase_project_id':
                    $this->project_id = $setting->setting_value;
                    break;
                case 'firebase_service_account_json':
                    if (!empty($setting->setting_value)) {
                        $this->service_account_json = json_decode($setting->setting_value, true);
                    }
                    break;
                case 'firebase_use_v1_api':
                    $this->use_v1_api = (bool)$setting->setting_value;
                    break;
            }
        }
    }

    /**
     * Check if push notifications are enabled
     */
    public function is_enabled()
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->use_v1_api) {
            // For v1 API, we need project_id and service account
            return !empty($this->project_id) && !empty($this->service_account_json);
        } else {
            // For Legacy API, we need server key
            return !empty($this->server_key);
        }
    }

    /**
     * Send push notification to a single device
     *
     * @param string $token FCM device token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Result with success/error
     */
    public function send_to_device($token, $title, $body, $data = [], $options = [])
    {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'error' => 'Push notifications are not enabled or configured'
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'error' => 'Device token is required'
            ];
        }

        if ($this->use_v1_api) {
            return $this->send_via_v1_api($token, $title, $body, $data, $options);
        } else {
            return $this->send_via_legacy_api($token, $title, $body, $data, $options);
        }
    }

    /**
     * Send via FCM HTTP v1 API (Modern)
     */
    private function send_via_v1_api($token, $title, $body, $data = [], $options = [])
    {
        // Get OAuth 2.0 access token
        $access_token = $this->get_access_token();

        if (!$access_token) {
            return [
                'success' => false,
                'error' => 'Failed to obtain access token'
            ];
        }

        // Build v1 API message format
        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => array_merge([
                    'timestamp' => date('Y-m-d H:i:s'),
                    'click_action' => $options['click_action'] ?? site_url('dietetic/portal')
                ], $data),
                'webpush' => [
                    'notification' => [
                        'icon' => $options['icon'] ?? base_url('uploads/company/favicon.png'),
                        'badge' => $options['badge'] ?? base_url('uploads/company/favicon.png')
                    ],
                    'fcm_options' => [
                        'link' => $options['click_action'] ?? site_url('dietetic/portal')
                    ]
                ]
            ]
        ];

        // Add image if provided
        if (isset($options['image'])) {
            $message['message']['webpush']['notification']['image'] = $options['image'];
        }

        // Send request
        $url = "https://fcm.googleapis.com/v1/projects/{$this->project_id}/messages:send";

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'CURL Error: ' . $curl_error
            ];
        }

        $response_data = json_decode($response, true);

        if ($http_code === 200 && isset($response_data['name'])) {
            return [
                'success' => true,
                'message_id' => $response_data['name']
            ];
        } else {
            // Handle errors
            $error_message = 'Unknown error';
            if (isset($response_data['error']['message'])) {
                $error_message = $response_data['error']['message'];

                // Handle invalid token
                if (strpos($error_message, 'not a valid FCM registration token') !== false ||
                    strpos($error_message, 'Requested entity was not found') !== false) {
                    $this->deactivate_token($token);
                }
            }

            return [
                'success' => false,
                'error' => 'FCM Error: ' . $error_message,
                'http_code' => $http_code,
                'response' => $response_data
            ];
        }
    }

    /**
     * Send via Legacy API (for backward compatibility)
     */
    private function send_via_legacy_api($token, $title, $body, $data = [], $options = [])
    {
        // Build notification payload
        $notification = [
            'title' => $title,
            'body' => $body,
            'icon' => $options['icon'] ?? base_url('uploads/company/favicon.png'),
            'badge' => $options['badge'] ?? base_url('uploads/company/favicon.png'),
            'click_action' => $options['click_action'] ?? site_url('dietetic/portal'),
            'sound' => $options['sound'] ?? 'default',
        ];

        if (isset($options['image'])) {
            $notification['image'] = $options['image'];
        }

        // Build FCM message
        $message = [
            'to' => $token,
            'notification' => $notification,
            'data' => array_merge([
                'timestamp' => date('Y-m-d H:i:s'),
            ], $data),
            'priority' => 'high',
            'time_to_live' => $options['ttl'] ?? 86400, // 24 hours default
        ];

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization: key=' . $this->server_key,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'success' => false,
                'error' => 'CURL Error: ' . $curl_error
            ];
        }

        $response_data = json_decode($response, true);

        if ($http_code === 200 && isset($response_data['success'])) {
            return [
                'success' => true,
                'success_count' => $response_data['success'] ?? 0,
                'failure_count' => $response_data['failure'] ?? 0,
            ];
        }

        return [
            'success' => false,
            'error' => 'FCM Error: ' . ($response_data['error'] ?? 'Unknown error'),
            'http_code' => $http_code,
            'response' => $response_data
        ];
    }

    /**
     * Get OAuth 2.0 access token for v1 API
     */
    private function get_access_token()
    {
        // Check if we have a cached valid token
        if ($this->access_token && $this->access_token_expiry > time()) {
            return $this->access_token;
        }

        if (empty($this->service_account_json)) {
            log_activity('FCM: Service account not configured');
            return false;
        }

        // Create JWT assertion
        $now = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $claim_set = [
            'iss' => $this->service_account_json['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600 // 1 hour
        ];

        $jwt = $this->create_jwt($header, $claim_set);

        if (!$jwt) {
            log_activity('FCM: Failed to create JWT');
            return false;
        }

        // Exchange JWT for access token
        $token_url = 'https://oauth2.googleapis.com/token';
        $post_data = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_activity('FCM: CURL Error getting token: ' . $curl_error);
            return false;
        }

        $token_data = json_decode($response, true);

        if (isset($token_data['access_token'])) {
            $this->access_token = $token_data['access_token'];
            $this->access_token_expiry = time() + ($token_data['expires_in'] ?? 3600) - 300; // Refresh 5 min before expiry

            return $this->access_token;
        }

        log_activity('FCM: Failed to obtain access token: ' . $response);
        return false;
    }

    /**
     * Create JWT (JSON Web Token)
     */
    private function create_jwt($header, $payload)
    {
        if (empty($this->service_account_json['private_key'])) {
            return false;
        }

        // Encode header
        $header_encoded = $this->base64url_encode(json_encode($header));

        // Encode payload
        $payload_encoded = $this->base64url_encode(json_encode($payload));

        // Create signature
        $signature_input = $header_encoded . '.' . $payload_encoded;
        $private_key = openssl_pkey_get_private($this->service_account_json['private_key']);

        if (!$private_key) {
            log_activity('FCM: Invalid private key');
            return false;
        }

        $signature = '';
        $success = openssl_sign($signature_input, $signature, $private_key, 'SHA256');
        openssl_free_key($private_key);

        if (!$success) {
            log_activity('FCM: Failed to sign JWT');
            return false;
        }

        $signature_encoded = $this->base64url_encode($signature);

        return $signature_input . '.' . $signature_encoded;
    }

    /**
     * Base64 URL encode
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send push notification to multiple devices
     */
    public function send_to_devices($tokens, $title, $body, $data = [], $options = [])
    {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'error' => 'Push notifications are not enabled'
            ];
        }

        if (empty($tokens) || !is_array($tokens)) {
            return [
                'success' => false,
                'error' => 'Device tokens array is required'
            ];
        }

        $results = [
            'success' => 0,
            'failure' => 0,
            'errors' => []
        ];

        // For v1 API, send individually (batch sending requires different endpoint)
        foreach ($tokens as $token) {
            $result = $this->send_to_device($token, $title, $body, $data, $options);

            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failure']++;
                $results['errors'][] = [
                    'token' => $token,
                    'error' => $result['error'] ?? 'Unknown error'
                ];
            }
        }

        return $results;
    }

    /**
     * Send push notification to a patient (all their devices)
     */
    public function send_to_patient($patient_id, $title, $body, $data = [], $options = [])
    {
        // Get all active tokens for this patient
        $tokens = $this->CI->db
            ->select('token')
            ->from(db_prefix() . 'dietic_fcm_tokens')
            ->where('patient_id', $patient_id)
            ->where('is_active', 1)
            ->get()
            ->result_array();

        if (empty($tokens)) {
            return [
                'success' => false,
                'error' => 'No active devices found for patient'
            ];
        }

        $token_list = array_column($tokens, 'token');
        return $this->send_to_devices($token_list, $title, $body, $data, $options);
    }

    /**
     * Deactivate an invalid token
     */
    private function deactivate_token($token)
    {
        $this->CI->db
            ->where('token', $token)
            ->update(db_prefix() . 'dietic_fcm_tokens', [
                'is_active' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Register a new device token
     */
    public function register_token($patient_id, $token, $device_type = 'web', $device_info = [])
    {
        // Check if token already exists
        $existing = $this->CI->db
            ->where('token', $token)
            ->get(db_prefix() . 'dietic_fcm_tokens')
            ->row();

        $data = [
            'patient_id' => $patient_id,
            'token' => $token,
            'device_type' => $device_type,
            'device_name' => $device_info['device_name'] ?? null,
            'user_agent' => $device_info['user_agent'] ?? null,
            'ip_address' => $device_info['ip_address'] ?? null,
            'is_active' => 1,
            'last_used_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            // Update existing token
            $this->CI->db
                ->where('id', $existing->id)
                ->update(db_prefix() . 'dietic_fcm_tokens', $data);
        } else {
            // Insert new token
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->CI->db->insert(db_prefix() . 'dietic_fcm_tokens', $data);
        }

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Unregister a device token
     */
    public function unregister_token($token)
    {
        $this->CI->db
            ->where('token', $token)
            ->delete(db_prefix() . 'dietic_fcm_tokens');

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Get Firebase config for web client
     */
    public function get_web_config()
    {
        if (!$this->enabled) {
            return false;
        }

        $settings = $this->CI->db->where_in('setting_key', [
            'firebase_api_key',
            'firebase_auth_domain',
            'firebase_project_id',
            'firebase_storage_bucket',
            'firebase_messaging_sender_id',
            'firebase_app_id',
            'firebase_vapid_key'
        ])->get(db_prefix() . 'dietic_notification_settings')->result();

        $config = [];
        foreach ($settings as $setting) {
            $key = str_replace('firebase_', '', $setting->setting_key);
            $config[$key] = $setting->setting_value;
        }

        // Check if all required fields are present
        $required = ['api_key', 'project_id', 'messaging_sender_id', 'app_id'];
        foreach ($required as $field) {
            if (empty($config[$field])) {
                return false;
            }
        }

        return [
            'apiKey' => $config['api_key'],
            'authDomain' => $config['auth_domain'] ?: $config['project_id'] . '.firebaseapp.com',
            'projectId' => $config['project_id'],
            'storageBucket' => $config['storage_bucket'] ?: $config['project_id'] . '.appspot.com',
            'messagingSenderId' => $config['messaging_sender_id'],
            'appId' => $config['app_id'],
            'vapidKey' => $config['vapid_key']
        ];
    }
}
