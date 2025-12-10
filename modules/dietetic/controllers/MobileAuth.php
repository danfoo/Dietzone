<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Contrôleur d'authentification mobile DietZone
 * Hérite directement de CI_Controller (pas de Perfex)
 * Pour éviter les restrictions de ClientsController et App_Controller
 */
class MobileAuth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Charger les librairies de base
        $this->load->database();
        $this->load->library('session');
        $this->load->library('security'); // Pour CSRF
        $this->load->helper(['url', 'cookie', 'form']);

        // Charger les fonctions Perfex nécessaires
        if (!function_exists('db_prefix')) {
            require_once(APPPATH . 'helpers/app_helper.php');
        }
        if (!function_exists('get_option')) {
            $this->load->helper('app');
        }
    }

    /**
     * Page de connexion
     */
    public function index()
    {
        // Si déjà connecté, rediriger
        if ($this->session->userdata('client_logged_in')) {
            redirect(site_url('dietetic/portal'));
        }

        $this->load->view('dietetic/auth/login');
    }

    /**
     * Traitement de la connexion - POST PHP
     */
    public function login()
    {
        if (!$this->input->post()) {
            redirect(site_url('mobileauth'));
            return;
        }

        $phone = $this->input->post('phone');
        $password = $this->input->post('password');
        $remember = $this->input->post('remember');

        // Valider
        if (empty($phone) || empty($password)) {
            $this->session->set_flashdata('message-danger', 'Veuillez remplir tous les champs');
            redirect(site_url('mobileauth'));
            return;
        }

        // Nettoyer le téléphone
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        // Chercher le patient
        $this->db->select('p.client_id, ct.password, ct.firstname, ct.lastname');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = p.client_id AND ct.is_primary = 1');
        $this->db->where('ct.phonenumber', $phone);
        $patient = $this->db->get()->row();

        if (!$patient) {
            $this->session->set_flashdata('message-danger', 'Numéro de téléphone ou mot de passe incorrect');
            redirect(site_url('mobileauth'));
            return;
        }

        // Vérifier le mot de passe avec le hasher de Perfex
        if (!function_exists('app_hasher')) {
            $this->load->helper('app');
        }

        if (!app_hasher()->CheckPassword($password, $patient->password)) {
            $this->session->set_flashdata('message-danger', 'Numéro de téléphone ou mot de passe incorrect');
            redirect(site_url('mobileauth'));
            return;
        }

        // Connexion réussie - créer la session
        $this->session->set_userdata([
            'client_logged_in' => true,
            'client_user_id' => $patient->client_id
        ]);

        if ($remember) {
            set_cookie('remember_client', $patient->client_id, 2592000); // 30 jours
        }

        log_activity('Patient logged in via mobile [Client ID: ' . $patient->client_id . ']');

        $this->session->set_flashdata('message-success', 'Connexion réussie ! Bienvenue ' . $patient->firstname . '.');
        redirect(site_url('dietetic/portal'));
    }
}
