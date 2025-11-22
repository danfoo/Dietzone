<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Legal Pages Controller
 * Manages Privacy Policy and Terms of Service
 */
class Legal_pages extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_settings_model');
    }

    /**
     * Admin page to manage legal pages (Admin only)
     */
    public function manage()
    {
        // Only admins can manage legal pages
        if (!is_admin()) {
            access_denied('Legal Pages Management');
        }

        if ($this->input->post()) {
            $this->save_legal_pages();
        }

        $data['title'] = 'Gestion des Pages Légales';

        // Load current content
        $data['privacy_policy'] = $this->dietetic_settings_model->get_setting('privacy_policy') ?? '';
        $data['terms_of_service'] = $this->dietetic_settings_model->get_setting('terms_of_service') ?? '';

        $this->load->view('admin/legal_pages/manage', $data);
    }

    /**
     * Save legal pages content
     */
    private function save_legal_pages()
    {
        $privacy_policy = $this->input->post('privacy_policy', false);
        $terms_of_service = $this->input->post('terms_of_service', false);

        // Update or insert privacy policy
        $this->dietetic_settings_model->update_setting('privacy_policy', $privacy_policy);

        // Update or insert terms of service
        $this->dietetic_settings_model->update_setting('terms_of_service', $terms_of_service);

        set_alert('success', 'Pages légales mises à jour avec succès');
        redirect(admin_url('dietetic/legal_pages/manage'));
    }

    /**
     * Public view of Privacy Policy
     */
    public function privacy()
    {
        $data['title'] = 'Politique de Confidentialité';
        $data['content'] = $this->dietetic_settings_model->get_setting('privacy_policy') ?? '<p>Aucune politique de confidentialité n\'a été définie.</p>';

        $this->load->view('admin/legal_pages/view', $data);
    }

    /**
     * Public view of Terms of Service
     */
    public function terms()
    {
        $data['title'] = 'Conditions Générales d\'Utilisation';
        $data['content'] = $this->dietetic_settings_model->get_setting('terms_of_service') ?? '<p>Aucune condition d\'utilisation n\'a été définie.</p>';

        $this->load->view('admin/legal_pages/view', $data);
    }
}
