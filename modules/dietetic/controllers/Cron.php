<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Cron Jobs Controller
 * Handles automated tasks for dietetic module
 *
 * Phase 9 - Recurring Payments & Refunds
 *
 * Setup Instructions:
 * Add to crontab to run every hour:
 * 0 * * * * php /path/to/perfex/index.php dietetic/cron/process_recurring_payments
 *
 * Or use Perfex's built-in cron by adding hook in modules/dietetic/dietetic.php:
 * hooks()->add_action('after_cron_run', 'dietetic_process_recurring_payments');
 */
class Cron extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_recurring_payments_model');
        $this->load->model('dietetic/dietetic_settings_model');
    }

    /**
     * Main cron entry point - processes all recurring payments
     * This should be called by the system cron job
     */
    public function process_recurring_payments()
    {
        // Security: Only allow execution from CLI or with secret key
        if (!$this->is_cron_request_valid()) {
            show_error('Unauthorized access', 403);
            return;
        }

        // Check if recurring payments are enabled
        $enabled = $this->dietetic_settings_model->get_setting('recurring_payments_enabled');
        if ($enabled != '1') {
            $this->log_cron('Recurring payments are disabled. Skipping.');
            return;
        }

        $this->log_cron('Starting recurring payments processing...');

        $start_time = microtime(true);
        $processed_count = 0;
        $success_count = 0;
        $failed_count = 0;

        // Get all due payments
        $due_payments = $this->dietetic_recurring_payments_model->get_due_payments();

        $this->log_cron(sprintf('Found %d payments due for processing', count($due_payments)));

        foreach ($due_payments as $payment) {
            $processed_count++;

            $this->log_cron(sprintf(
                'Processing recurring payment #%d for patient #%d (Amount: %s FCFA)',
                $payment->id,
                $payment->patient_id,
                number_format($payment->amount, 0)
            ));

            try {
                $result = $this->dietetic_recurring_payments_model->process_payment($payment->id);

                if ($result) {
                    $success_count++;
                    $this->log_cron(sprintf('✓ Successfully processed recurring payment #%d', $payment->id));
                } else {
                    $failed_count++;
                    $this->log_cron(sprintf('✗ Failed to process recurring payment #%d', $payment->id), 'error');
                }
            } catch (Exception $e) {
                $failed_count++;
                $this->log_cron(sprintf(
                    '✗ Exception processing recurring payment #%d: %s',
                    $payment->id,
                    $e->getMessage()
                ), 'error');
            }
        }

        $execution_time = round(microtime(true) - $start_time, 2);

        // Summary
        $this->log_cron('========================================');
        $this->log_cron('Recurring Payments Processing Complete');
        $this->log_cron('========================================');
        $this->log_cron(sprintf('Total processed: %d', $processed_count));
        $this->log_cron(sprintf('Successful: %d', $success_count));
        $this->log_cron(sprintf('Failed: %d', $failed_count));
        $this->log_cron(sprintf('Execution time: %s seconds', $execution_time));

        // Send notification to admin if there were failures
        if ($failed_count > 0) {
            $this->notify_admin_of_failures($failed_count, $processed_count);
        }

        // Return JSON response if called via AJAX/API
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'processed' => $processed_count,
                'successful' => $success_count,
                'failed' => $failed_count,
                'execution_time' => $execution_time
            ]);
        }
    }

    /**
     * Process retry attempts for failed payments
     */
    public function process_retries()
    {
        if (!$this->is_cron_request_valid()) {
            show_error('Unauthorized access', 403);
            return;
        }

        $this->log_cron('Starting retry processing...');

        $retry_delay_hours = $this->dietetic_settings_model->get_setting('recurring_payment_retry_delay');
        if (empty($retry_delay_hours)) {
            $retry_delay_hours = 24; // Default 24 hours
        }

        // Get failed payments that are ready for retry
        $failed_payments = $this->dietetic_recurring_payments_model->get_retry_eligible();

        $this->log_cron(sprintf('Found %d payments eligible for retry', count($failed_payments)));

        $retry_count = 0;
        $success_count = 0;

        foreach ($failed_payments as $payment) {
            $retry_count++;

            $result = $this->dietetic_recurring_payments_model->retry_payment($payment->id);

            if ($result) {
                $success_count++;
                $this->log_cron(sprintf('✓ Retry successful for payment #%d', $payment->id));
            } else {
                $this->log_cron(sprintf('✗ Retry failed for payment #%d', $payment->id), 'error');
            }
        }

        $this->log_cron(sprintf('Retry processing complete: %d attempted, %d successful', $retry_count, $success_count));
    }

    /**
     * Send reminders for upcoming payments
     */
    public function send_payment_reminders()
    {
        if (!$this->is_cron_request_valid()) {
            show_error('Unauthorized access', 403);
            return;
        }

        $reminder_enabled = $this->dietetic_settings_model->get_setting('recurring_payment_reminders_enabled');
        if ($reminder_enabled != '1') {
            $this->log_cron('Payment reminders are disabled. Skipping.');
            return;
        }

        $this->log_cron('Starting payment reminder processing...');

        $reminder_days = $this->dietetic_settings_model->get_setting('recurring_payment_reminder_days');
        if (empty($reminder_days)) {
            $reminder_days = 3; // Default 3 days before
        }

        // Get upcoming payments
        $upcoming_date = date('Y-m-d', strtotime("+{$reminder_days} days"));
        $upcoming_payments = $this->dietetic_recurring_payments_model->get_upcoming_payments($upcoming_date);

        $this->log_cron(sprintf('Found %d upcoming payments to remind', count($upcoming_payments)));

        $this->load->model('dietetic/dietetic_notifications_model');
        $sent_count = 0;

        foreach ($upcoming_payments as $payment) {
            // Check if reminder already sent
            $last_reminder = $this->db->select('value')
                ->from(db_prefix() . 'dietic_recurring_payment_meta')
                ->where('recurring_payment_id', $payment->id)
                ->where('meta_key', 'last_reminder_sent')
                ->get()
                ->row();

            if ($last_reminder && $last_reminder->value == date('Y-m-d')) {
                continue; // Already sent today
            }

            // Send reminder email/SMS
            $success = $this->send_reminder_notification($payment);

            if ($success) {
                $sent_count++;

                // Mark reminder as sent
                $this->db->replace(db_prefix() . 'dietic_recurring_payment_meta', [
                    'recurring_payment_id' => $payment->id,
                    'meta_key' => 'last_reminder_sent',
                    'value' => date('Y-m-d')
                ]);

                $this->log_cron(sprintf('✓ Reminder sent for payment #%d', $payment->id));
            }
        }

        $this->log_cron(sprintf('Payment reminder processing complete: %d sent', $sent_count));
    }

    /**
     * Clean up old transaction logs
     */
    public function cleanup_old_logs()
    {
        if (!$this->is_cron_request_valid()) {
            show_error('Unauthorized access', 403);
            return;
        }

        $this->log_cron('Starting log cleanup...');

        $retention_days = $this->dietetic_settings_model->get_setting('recurring_payment_log_retention_days');
        if (empty($retention_days)) {
            $retention_days = 365; // Default 1 year
        }

        $cutoff_date = date('Y-m-d', strtotime("-{$retention_days} days"));

        // Delete old transaction logs (keep only metadata, not detailed logs)
        $this->db->where('created_at <', $cutoff_date)
                 ->where('status', 'completed')
                 ->delete(db_prefix() . 'dietic_recurring_payment_transactions');

        $deleted_count = $this->db->affected_rows();

        $this->log_cron(sprintf('Cleaned up %d old transaction logs', $deleted_count));
    }

    /**
     * Manual trigger for testing (admin only)
     */
    public function manual_trigger()
    {
        if (!is_admin()) {
            access_denied('dietetic');
        }

        $this->process_recurring_payments();

        set_alert('info', 'Recurring payments processing triggered manually. Check logs for details.');
        redirect(admin_url('dietetic/recurring_payments'));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Validate if this is a legitimate cron request
     */
    private function is_cron_request_valid()
    {
        // Allow CLI execution
        if (php_sapi_name() === 'cli') {
            return true;
        }

        // Allow admin users
        if (is_admin()) {
            return true;
        }

        // Check for cron secret key
        $secret_key = $this->dietetic_settings_model->get_setting('cron_secret_key');
        $provided_key = $this->input->get('key');

        if (!empty($secret_key) && $provided_key === $secret_key) {
            return true;
        }

        // Allow localhost requests
        if (in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
            return true;
        }

        return false;
    }

    /**
     * Log cron activity
     */
    private function log_cron($message, $level = 'info')
    {
        $log_file = FCPATH . 'modules/dietetic/logs/cron_' . date('Y-m-d') . '.log';
        $log_dir = dirname($log_file);

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $log_message = sprintf("[%s] [%s] %s\n", $timestamp, strtoupper($level), $message);

        file_put_contents($log_file, $log_message, FILE_APPEND);

        // Also log to Perfex activity log for important events
        if ($level == 'error') {
            log_activity(sprintf('Dietetic Cron Error: %s', $message));
        }
    }

    /**
     * Send reminder notification to patient
     */
    private function send_reminder_notification($payment)
    {
        $this->load->model('dietetic/dietetic_patients_model');
        $patient = $this->dietetic_patients_model->get($payment->patient_id);

        if (!$patient || empty($patient->email)) {
            return false;
        }

        $days_until = round((strtotime($payment->next_payment_date) - time()) / 86400);

        $subject = sprintf('Rappel: Paiement automatique dans %d jours', $days_until);
        $message = sprintf(
            "Bonjour %s,\n\n" .
            "Ceci est un rappel que votre paiement automatique de %s FCFA sera traité le %s.\n\n" .
            "Détails:\n" .
            "- Montant: %s FCFA\n" .
            "- Date: %s\n" .
            "- Méthode: %s\n\n" .
            "Assurez-vous que votre mode de paiement est à jour.\n\n" .
            "Cordialement,\n" .
            "L'équipe DietZone",
            $patient->firstname,
            number_format($payment->amount, 0, ',', ' '),
            date('d/m/Y', strtotime($payment->next_payment_date)),
            number_format($payment->amount, 0, ',', ' '),
            date('d/m/Y', strtotime($payment->next_payment_date)),
            ucfirst(str_replace('_', ' ', $payment->payment_method))
        );

        // Send email
        $this->load->library('email');
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($patient->email);
        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }

    /**
     * Notify admin of payment failures
     */
    private function notify_admin_of_failures($failed_count, $total_count)
    {
        $admin_email = get_option('smtp_email');
        if (empty($admin_email)) {
            return;
        }

        $subject = sprintf('[DietZone] %d Recurring Payment(s) Failed', $failed_count);
        $message = sprintf(
            "Recurring payment processing summary:\n\n" .
            "Total processed: %d\n" .
            "Failed: %d\n\n" .
            "Please review the failed payments in the admin panel.\n\n" .
            "View details: %s",
            $total_count,
            $failed_count,
            admin_url('dietetic/recurring_payments?status=failed')
        );

        $this->load->library('email');
        $this->email->from(get_option('smtp_email'), 'DietZone Cron');
        $this->email->to($admin_email);
        $this->email->subject($subject);
        $this->email->message($message);

        $this->email->send();
    }
}
