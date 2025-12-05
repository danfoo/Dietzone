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
     * IMPORTANT: XSS filtering is DISABLED to allow full HTML/CSS styling
     */
    private function save_legal_pages()
    {
        // Get raw POST data without ANY filtering (allows full HTML/CSS)
        // Using $_POST directly to completely bypass CodeIgniter's XSS filtering
        $privacy_policy = isset($_POST['privacy_policy']) ? $_POST['privacy_policy'] : '';
        $terms_of_service = isset($_POST['terms_of_service']) ? $_POST['terms_of_service'] : '';

        // Additional security: Only allow if user is confirmed admin
        if (!is_admin()) {
            set_alert('danger', 'Accès refusé. Vous devez être administrateur.');
            redirect(admin_url('dietetic/legal_pages/manage'));
            return;
        }

        // Update or insert privacy policy
        $privacy_result = $this->dietetic_settings_model->update_setting('privacy_policy', $privacy_policy);

        // Update or insert terms of service
        $terms_result = $this->dietetic_settings_model->update_setting('terms_of_service', $terms_of_service);

        if ($privacy_result !== false || $terms_result !== false) {
            set_alert('success', 'Pages légales mises à jour avec succès. HTML et CSS complets acceptés.');
        } else {
            set_alert('danger', 'Erreur lors de la mise à jour. Vérifiez les logs.');
        }

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
