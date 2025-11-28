<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Diagnostic extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Test subscription view specifically
     */
    public function test_subscription_view($subscription_id = 1)
    {
        if (!is_admin()) {
            echo "You must be an admin to view this page";
            return;
        }

        echo "<h1>Diagnostic - Subscription View Test</h1>";
        echo "<style>
            .success { color: green; }
            .error { color: red; font-weight: bold; }
            .warning { color: orange; }
            pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
        </style>";

        echo "<h2>Test 1: Load Subscription Model</h2>";
        try {
            $this->load->model('dietetic/dietetic_subscriptions_model');
            echo "<p class='success'>✓ Model loaded successfully</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error loading model: " . htmlspecialchars($e->getMessage()) . "</p>";
            return;
        }

        echo "<h2>Test 2: Get Subscription #$subscription_id</h2>";
        try {
            $subscription = $this->dietetic_subscriptions_model->get($subscription_id);
            if ($subscription) {
                echo "<p class='success'>✓ Subscription loaded</p>";
                echo "<pre>" . print_r($subscription, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ Subscription not found (returned null)</p>";
                return;
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ SQL Error getting subscription: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($this->db->last_query()) . "</pre>";
            return;
        }

        echo "<h2>Test 3: Load Invoices Model</h2>";
        try {
            $this->load->model('dietetic/dietetic_invoices_model');
            echo "<p class='success'>✓ Invoices model loaded</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            return;
        }

        echo "<h2>Test 4: Get Invoices for Subscription</h2>";
        try {
            $invoices = $this->dietetic_invoices_model->get_by_subscription($subscription_id);
            echo "<p class='success'>✓ Invoices query executed</p>";
            echo "<p>Found " . count($invoices) . " invoice(s)</p>";
            if (!empty($invoices)) {
                echo "<pre>" . print_r($invoices, true) . "</pre>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ SQL Error getting invoices: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($this->db->last_query()) . "</pre>";
            return;
        }

        echo "<h2>Test 5: Load Service Plans Model</h2>";
        try {
            $this->load->model('dietetic/dietetic_service_plans_model');
            echo "<p class='success'>✓ Service plans model loaded</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            return;
        }

        echo "<h2>Test 6: Get All Service Plans (for edit form)</h2>";
        try {
            $plans = $this->dietetic_service_plans_model->get_all([], null, null);
            echo "<p class='success'>✓ Service plans query executed</p>";
            echo "<p>Found " . count($plans) . " plan(s)</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ SQL Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($this->db->last_query()) . "</pre>";
            return;
        }

        echo "<h2>Test 7: Get All Patients (for edit form)</h2>";
        try {
            $this->load->model('dietetic/dietetic_patients_model');
            $patients = $this->dietetic_patients_model->get_all([], null, null);
            echo "<p class='success'>✓ Patients query executed</p>";
            echo "<p>Found " . count($patients) . " patient(s)</p>";
            if (!empty($patients)) {
                echo "<p>First patient: " . htmlspecialchars($patients[0]->client_name) . "</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ SQL Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($this->db->last_query()) . "</pre>";
            return;
        }

        echo "<h2>✅ All Tests Passed!</h2>";
        echo "<p class='success'>The view and edit pages should work. If you still get 500 errors, check PHP error logs.</p>";
    }
}
