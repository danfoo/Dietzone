<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * TEMPORARY DEBUG CONTROLLER
 * DELETE THIS FILE AFTER DEBUGGING
 */
class Debug_tracking extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic_patients_model');
        $this->load->model('dietetic_daily_tracking_model');
    }

    /**
     * Show debug information about daily tracking
     */
    public function index()
    {
        if (!is_client_logged_in()) {
            die('Not authenticated');
        }

        $client_id = get_client_user_id();
        $patient = $this->dietetic_patients_model->get_by_client($client_id);

        if (!$patient) {
            die('Patient not found');
        }

        echo '<html><head><meta charset="UTF-8"><title>Debug Tracking</title>';
        echo '<style>
            body { font-family: monospace; padding: 20px; background: #f5f5f5; }
            .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border: 2px solid #333; }
            .success { background: #d4edda; border-color: #28a745; }
            .error { background: #f8d7da; border-color: #dc3545; }
            .warning { background: #fff3cd; border-color: #ffc107; }
            h2 { margin-top: 0; }
            pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        </style></head><body>';

        echo '<h1>🔍 DEBUG - Daily Tracking Diagnostic</h1>';
        echo '<p>Time: ' . date('Y-m-d H:i:s') . '</p>';

        // Section 1: Patient Info
        echo '<div class="section">';
        echo '<h2>1. Patient Information</h2>';
        echo '<pre>';
        echo 'Patient ID: ' . $patient->id . "\n";
        echo 'Client ID: ' . $patient->client_id . "\n";
        echo 'Patient Name: ' . ($patient->first_name ?? '') . ' ' . ($patient->last_name ?? '') . "\n";
        echo '</pre>';
        echo '</div>';

        // Section 2: Call get_today() and show result
        echo '<div class="section warning">';
        echo '<h2>2. Calling get_today()</h2>';
        echo '<p><strong>This will trigger the method and log everything</strong></p>';

        $tracking = $this->dietetic_daily_tracking_model->get_today($patient->id);

        echo '<h3>Result from get_today():</h3>';
        echo '<pre>';
        print_r($tracking);
        echo '</pre>';
        echo '</div>';

        // Section 3: Direct database query
        echo '<div class="section">';
        echo '<h2>3. Direct Database Query</h2>';
        $today = date('Y-m-d');
        $this->db->where('patient_id', $patient->id);
        $this->db->where('tracking_date', $today);
        $direct_result = $this->db->get(db_prefix() . 'dietic_daily_tracking')->row();

        echo '<pre>';
        echo 'Query: SELECT * FROM ' . db_prefix() . 'dietic_daily_tracking WHERE patient_id = ' . $patient->id . ' AND tracking_date = \'' . $today . "'\n\n";
        echo 'Result:' . "\n";
        print_r($direct_result);
        echo '</pre>';

        if ($direct_result) {
            echo '<div class="success"><strong>✅ Record found in database!</strong></div>';
        } else {
            echo '<div class="error"><strong>❌ No record found in database!</strong></div>';
        }
        echo '</div>';

        // Section 4: Check logs location
        echo '<div class="section">';
        echo '<h2>4. PHP Logs Information</h2>';
        echo '<p>Check these log files for debug messages:</p>';
        echo '<pre>';
        echo 'Error log: ' . ini_get('error_log') . "\n";
        echo 'Log path (BASEPATH): ' . BASEPATH . '../application/logs/' . "\n";
        echo 'Today log file: log-' . date('Y-m-d') . '.php' . "\n";
        echo '</pre>';

        // Try to read the log file
        $log_file = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        if (file_exists($log_file)) {
            $log_contents = file_get_contents($log_file);
            // Get last 50 lines related to get_today
            $lines = explode("\n", $log_contents);
            $relevant_lines = array_filter($lines, function($line) {
                return strpos($line, 'get_today') !== false ||
                       strpos($line, 'get_empty_tracking') !== false ||
                       strpos($line, 'Portal::index()') !== false;
            });

            if (!empty($relevant_lines)) {
                echo '<h3>Recent log entries (last 50 matching lines):</h3>';
                echo '<pre>';
                echo implode("\n", array_slice($relevant_lines, -50));
                echo '</pre>';
            } else {
                echo '<div class="warning"><strong>⚠️ No relevant log entries found</strong></div>';
            }
        } else {
            echo '<div class="error"><strong>❌ Log file not found: ' . $log_file . '</strong></div>';
            echo '<p>Check if logging is enabled in application/config/config.php</p>';
        }
        echo '</div>';

        echo '<div class="section warning">';
        echo '<h2>5. Instructions</h2>';
        echo '<p>1. Compare the results from sections 2 and 3</p>';
        echo '<p>2. Check section 4 for log messages showing the execution flow</p>';
        echo '<p>3. Share this entire page output with me</p>';
        echo '</div>';

        echo '</body></html>';
    }
}
