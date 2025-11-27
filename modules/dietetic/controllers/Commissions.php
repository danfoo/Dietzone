<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Commissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dietetic/dietetic_commission_settings_model');
        $this->load->model('dietetic/dietetic_revenue_shares_model');

        // Only admins can manage commissions
        if (!is_admin()) {
            access_denied('dietetic');
        }
    }

    /**
     * Commission settings page
     */
    public function settings()
    {
        $data['title'] = 'Configuration des Commissions';

        // Get current active settings
        $data['current_settings'] = $this->dietetic_commission_settings_model->get_current_settings();

        // Get history for both sources
        $data['platform_history'] = $this->dietetic_commission_settings_model->get_history('platform');
        $data['dietitian_history'] = $this->dietetic_commission_settings_model->get_history('dietitian');

        // Get platform statistics
        $data['platform_stats'] = $this->dietetic_revenue_shares_model->get_platform_statistics();

        $this->load->view('admin/commissions/settings', $data);
    }

    /**
     * Update commission setting
     */
    public function update()
    {
        if (!$this->input->post()) {
            redirect(admin_url('dietetic/commissions/settings'));
            return;
        }

        $referral_source = $this->input->post('referral_source');
        $dietitian_percentage = (float)$this->input->post('dietitian_percentage');
        $platform_percentage = (float)$this->input->post('platform_percentage');
        $effective_from = $this->input->post('effective_from');
        $notes = $this->input->post('notes');

        // Validate
        if (empty($referral_source) || !in_array($referral_source, ['platform', 'dietitian'])) {
            set_alert('danger', 'Source de référence invalide');
            redirect(admin_url('dietetic/commissions/settings'));
            return;
        }

        if ($dietitian_percentage + $platform_percentage != 100) {
            set_alert('danger', 'La somme des pourcentages doit être égale à 100%');
            redirect(admin_url('dietetic/commissions/settings'));
            return;
        }

        if (empty($effective_from)) {
            $effective_from = date('Y-m-d');
        }

        $data = [
            'referral_source' => $referral_source,
            'dietitian_percentage' => $dietitian_percentage,
            'platform_percentage' => $platform_percentage,
            'effective_from' => $effective_from,
            'is_active' => 1,
            'notes' => $notes
        ];

        $result = $this->dietetic_commission_settings_model->add($data);

        if ($result) {
            set_alert('success', 'Configuration des commissions mise à jour avec succès');
        } else {
            set_alert('danger', 'Erreur lors de la mise à jour de la configuration');
        }

        redirect(admin_url('dietetic/commissions/settings'));
    }

    /**
     * Get commission calculation preview (AJAX)
     */
    public function preview()
    {
        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $amount = (float)$this->input->post('amount');
        $dietitian_percentage = (float)$this->input->post('dietitian_percentage');
        $platform_percentage = (float)$this->input->post('platform_percentage');

        if ($dietitian_percentage + $platform_percentage != 100) {
            echo json_encode([
                'success' => false,
                'message' => 'La somme des pourcentages doit être égale à 100%'
            ]);
            return;
        }

        $dietitian_share = $amount * ($dietitian_percentage / 100);
        $platform_share = $amount * ($platform_percentage / 100);

        echo json_encode([
            'success' => true,
            'amount' => number_format($amount, 2, ',', ' '),
            'dietitian_share' => number_format($dietitian_share, 2, ',', ' '),
            'platform_share' => number_format($platform_share, 2, ',', ' '),
            'dietitian_percentage' => $dietitian_percentage,
            'platform_percentage' => $platform_percentage
        ]);
    }

    /**
     * Deactivate commission setting
     */
    public function deactivate($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $effective_to = $this->input->post('effective_to');

        if (empty($effective_to)) {
            $effective_to = date('Y-m-d');
        }

        $result = $this->dietetic_commission_settings_model->deactivate($id, $effective_to);

        if ($result) {
            set_alert('success', 'Configuration désactivée avec succès');
        } else {
            set_alert('danger', 'Erreur lors de la désactivation');
        }

        redirect(admin_url('dietetic/commissions/settings'));
    }

    /**
     * View commission history
     */
    public function history($referral_source = null)
    {
        $data['title'] = 'Historique des Commissions';

        if ($referral_source && in_array($referral_source, ['platform', 'dietitian'])) {
            $data['history'] = $this->dietetic_commission_settings_model->get_history($referral_source);
            $data['referral_source'] = $referral_source;
        } else {
            $data['platform_history'] = $this->dietetic_commission_settings_model->get_history('platform');
            $data['dietitian_history'] = $this->dietetic_commission_settings_model->get_history('dietitian');
        }

        $this->load->view('admin/commissions/history', $data);
    }
}
