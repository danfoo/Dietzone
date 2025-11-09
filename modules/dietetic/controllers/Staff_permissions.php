<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Staff_permissions extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        // Only admins can manage staff permissions
        if (!is_admin()) {
            access_denied('Staff Permissions');
        }

        $this->load->helper('dietetic/dietetic');
        $this->load->model('staff_model');
    }

    /**
     * List all staff and their permissions
     */
    public function index()
    {
        // Check if permissions table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
            set_alert('warning', 'La table des permissions n\'existe pas encore. Veuillez exécuter la migration.');
            redirect(admin_url('dietetic/notifications/migrations'));
        }

        $data['title'] = 'Gestion des Permissions Diététiciens';

        // Get all active staff members with all necessary columns
        $this->db->select('staffid, firstname, lastname, email, admin, active, is_not_staff');
        $this->db->where('active', 1);
        $this->db->order_by('firstname', 'ASC');
        $query = $this->db->get(db_prefix() . 'staff');
        $data['staff_members'] = $query->result();

        // Get available permissions
        $data['available_permissions'] = dietetic_get_available_permissions();

        // Get all permissions for each staff member
        $data['staff_permissions'] = [];
        foreach ($data['staff_members'] as $staff) {
            $data['staff_permissions'][$staff->staffid] = dietetic_get_staff_permissions($staff->staffid);
        }

        $this->load->view('admin/staff_permissions/index', $data);
    }

    /**
     * Update permission for a staff member (AJAX)
     */
    public function update_permission()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $staff_id = $this->input->post('staff_id');
        $permission_key = $this->input->post('permission_key');
        $enabled = $this->input->post('enabled');

        if (!$staff_id || !$permission_key) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
            echo json_encode(['success' => false, 'message' => 'Table des permissions introuvable']);
            return;
        }

        $result = dietetic_grant_permission($staff_id, $permission_key, (bool)$enabled);

        if ($result) {
            log_activity('Permission updated: ' . $permission_key . ' for Staff ID ' . $staff_id . ' = ' . ($enabled ? 'enabled' : 'disabled'));
            echo json_encode([
                'success' => true,
                'message' => 'Permission mise à jour avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la permission'
            ]);
        }
    }

    /**
     * Bulk update permissions for a staff member
     */
    public function bulk_update()
    {
        if (!$this->input->post()) {
            redirect(admin_url('dietetic/staff_permissions'));
        }

        $staff_id = $this->input->post('staff_id');
        $permissions = $this->input->post('permissions');

        if (!$staff_id) {
            set_alert('danger', 'Staff ID manquant');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
            set_alert('danger', 'Table des permissions introuvable');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        $available_permissions = dietetic_get_available_permissions();
        $updated_count = 0;

        foreach ($available_permissions as $key => $info) {
            $enabled = isset($permissions[$key]) && $permissions[$key] == '1';
            if (dietetic_grant_permission($staff_id, $key, $enabled)) {
                $updated_count++;
            }
        }

        if ($updated_count > 0) {
            set_alert('success', $updated_count . ' permission(s) mise(s) à jour avec succès');
            log_activity('Bulk permission update for Staff ID ' . $staff_id);
        } else {
            set_alert('warning', 'Aucune permission n\'a été modifiée');
        }

        redirect(admin_url('dietetic/staff_permissions'));
    }

    /**
     * Reset all permissions for a staff member
     */
    public function reset_permissions($staff_id)
    {
        if (!$staff_id) {
            set_alert('danger', 'Staff ID manquant');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        // Check if table exists
        if (!$this->db->table_exists(db_prefix() . 'dietic_staff_permissions')) {
            set_alert('danger', 'Table des permissions introuvable');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        // Delete all permissions for this staff member
        $this->db->where('staff_id', $staff_id);
        if ($this->db->delete(db_prefix() . 'dietic_staff_permissions')) {
            set_alert('success', 'Toutes les permissions ont été réinitialisées');
            log_activity('Reset all permissions for Staff ID ' . $staff_id);
        } else {
            set_alert('danger', 'Erreur lors de la réinitialisation');
        }

        redirect(admin_url('dietetic/staff_permissions'));
    }

    /**
     * Copy permissions from one staff member to another
     */
    public function copy_permissions()
    {
        if (!$this->input->post()) {
            redirect(admin_url('dietetic/staff_permissions'));
        }

        $from_staff_id = $this->input->post('from_staff_id');
        $to_staff_id = $this->input->post('to_staff_id');

        if (!$from_staff_id || !$to_staff_id) {
            set_alert('danger', 'IDs manquants');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        if ($from_staff_id == $to_staff_id) {
            set_alert('warning', 'Impossible de copier les permissions sur le même utilisateur');
            redirect(admin_url('dietetic/staff_permissions'));
        }

        // Get source permissions
        $source_permissions = dietetic_get_staff_permissions($from_staff_id);

        $copied_count = 0;
        foreach ($source_permissions as $key => $enabled) {
            if (dietetic_grant_permission($to_staff_id, $key, $enabled)) {
                $copied_count++;
            }
        }

        if ($copied_count > 0) {
            set_alert('success', $copied_count . ' permission(s) copiée(s) avec succès');
            log_activity('Copied permissions from Staff ID ' . $from_staff_id . ' to Staff ID ' . $to_staff_id);
        } else {
            set_alert('warning', 'Aucune permission à copier');
        }

        redirect(admin_url('dietetic/staff_permissions'));
    }
}
