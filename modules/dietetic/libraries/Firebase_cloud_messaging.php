<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Firebase Cloud Messaging (FCM) Library
 *
 * Handles sending push notifications via Firebase Cloud Messaging
 * Supports both HTTP v1 API and Legacy API
 */
class Firebase_cloud_messaging
{
    private $CI;
    private $server_key;
    private $vapid_key;
    private $project_id;
    private $enabled;

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
            'firebase_project_id'
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
            }
        }
    }

    /**
     * Check if push notifications are enabled
     */
    public function is_enabled()
    {
        return $this->enabled && !empty($this->server_key);
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
                'error' => 'Push notifications are not enabled'
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'error' => 'Device token is required'
            ];
        }

        // Build notification payload
        $notification = [
            'title' => $title,
            'body' => $body,
            'icon' => $options['icon'] ?? base_url('uploads/company/favicon.png'),
            'badge' => $options['badge'] ?? base_url('uploads/company/favicon.png'),
            'click_action' => $options['click_action'] ?? site_url('dietetic/portal'),
            'sound' => $options['sound'] ?? 'default',
        ];

        // Add image if provided
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

        // Send via FCM API
        return $this->send_fcm_request($message);
    }

    /**
     * Send push notification to multiple devices
     *
     * @param array $tokens Array of FCM device tokens
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Results with success/error counts
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

        $results = [
            'success' => 0,
            'failure' => 0,
            'errors' => []
        ];

        // Split into chunks of 1000 (FCM limit)
        $chunks = array_chunk($tokens, 1000);

        foreach ($chunks as $chunk) {
            $message = [
                'registration_ids' => $chunk,
                'notification' => $notification,
                'data' => array_merge([
                    'timestamp' => date('Y-m-d H:i:s'),
                ], $data),
                'priority' => 'high',
                'time_to_live' => $options['ttl'] ?? 86400,
            ];

            $response = $this->send_fcm_request($message);

            if ($response['success']) {
                $results['success'] += $response['success_count'] ?? count($chunk);
                $results['failure'] += $response['failure_count'] ?? 0;

                if (isset($response['errors'])) {
                    $results['errors'] = array_merge($results['errors'], $response['errors']);
                }
            } else {
                $results['failure'] += count($chunk);
                $results['errors'][] = $response['error'];
            }
        }

        return $results;
    }

    /**
     * Send push notification to a patient (all their devices)
     *
     * @param int $patient_id Patient ID
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Additional notification options
     * @return array Results
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
     * Send FCM request using cURL
     *
     * @param array $message FCM message payload
     * @return array Response
     */
    private function send_fcm_request($message)
    {
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
            $result = [
                'success' => true,
                'success_count' => $response_data['success'] ?? 0,
                'failure_count' => $response_data['failure'] ?? 0,
            ];

            // Handle invalid tokens
            if (isset($response_data['results'])) {
                $invalid_tokens = [];
                foreach ($response_data['results'] as $index => $item) {
                    if (isset($item['error'])) {
                        $invalid_tokens[] = [
                            'index' => $index,
                            'error' => $item['error']
                        ];

                        // Mark token as inactive if it's invalid
                        if (in_array($item['error'], ['NotRegistered', 'InvalidRegistration'])) {
                            if (isset($message['registration_ids'][$index])) {
                                $this->deactivate_token($message['registration_ids'][$index]);
                            } elseif (isset($message['to'])) {
                                $this->deactivate_token($message['to']);
                            }
                        }
                    }
                }

                if (!empty($invalid_tokens)) {
                    $result['errors'] = $invalid_tokens;
                }
            }

            return $result;
        }

        return [
            'success' => false,
            'error' => 'FCM Error: ' . ($response_data['error'] ?? 'Unknown error'),
            'http_code' => $http_code,
            'response' => $response_data
        ];
    }

    /**
     * Deactivate an invalid token
     *
     * @param string $token Device token
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
     *
     * @param int $patient_id Patient ID
     * @param string $token FCM device token
     * @param string $device_type Device type (web/android/ios)
     * @param array $device_info Additional device information
     * @return bool Success
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
     *
     * @param string $token FCM device token
     * @return bool Success
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
     *
     * @return array|false Firebase config or false if not configured
     */
    public function get_web_config()
    {
        if (!$this->is_enabled()) {
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
