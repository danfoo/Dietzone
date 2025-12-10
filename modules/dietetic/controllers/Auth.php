<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Contrôleur d'authentification DietZone
 * Gère la connexion, l'inscription et la récupération de mot de passe des patients
 *
 * IMPORTANT: Ce contrôleur hérite de CI_Controller pour permettre l'accès public
 * sans authentification requise
 */
class Auth extends ClientsController
{
    public function __construct()
    {
        parent::__construct();

        // Désactiver les contrôles d'authentification
        $this->disableNavigation();
        $this->disableSubMenu();

        // Charger uniquement les modèles nécessaires
        $this->load->model('dietetic/dietetic_patients_model');
        $this->load->helper('dietetic/dietetic');
    }

    /**
     * Page de connexion/inscription
     */
    public function index()
    {
        // Si déjà connecté, rediriger vers le portail
        if (is_client_logged_in()) {
            redirect(site_url('dietetic/portal'));
        }

        $this->load->view('dietetic/auth/login');
    }

    /**
     * Traitement de la connexion
     */
    public function login()
    {
        if ($this->input->post()) {
            $phone = $this->input->post('phone');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember');

            log_message('debug', 'Login attempt - Phone: ' . $phone);

            // Valider les champs
            if (empty($phone) || empty($password)) {
                log_message('debug', 'Login failed - Empty fields');
                echo json_encode([
                    'success' => false,
                    'message' => 'Veuillez remplir tous les champs'
                ]);
                return;
            }

            // Nettoyer le numéro de téléphone
            $phone = $this->clean_phone_number($phone);
            log_message('debug', 'Login - Cleaned phone: ' . $phone);

            // Chercher le patient par téléphone
            $patient = $this->find_patient_by_phone($phone);

            if (!$patient) {
                log_message('debug', 'Login failed - Patient not found for phone: ' . $phone);
                echo json_encode([
                    'success' => false,
                    'message' => 'Numéro de téléphone ou mot de passe incorrect'
                ]);
                return;
            }

            log_message('debug', 'Login - Patient found, checking password for client_id: ' . $patient->client_id);

            // Vérifier le mot de passe
            if (!$this->verify_password($patient->client_id, $password)) {
                log_message('debug', 'Login failed - Invalid password for client_id: ' . $patient->client_id);
                echo json_encode([
                    'success' => false,
                    'message' => 'Numéro de téléphone ou mot de passe incorrect'
                ]);
                return;
            }

            log_message('debug', 'Login success - Client ID: ' . $patient->client_id);

            // Connecter le patient
            $this->connect_patient($patient->client_id, $remember);

            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => site_url('dietetic/portal')
            ]);
        }
    }

    /**
     * Inscription d'un nouveau patient
     */
    public function register()
    {
        if ($this->input->post()) {
            $firstname = $this->input->post('firstname');
            $lastname = $this->input->post('lastname');
            $email = $this->input->post('email');
            $phone = $this->input->post('phone');
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');

            // Validation
            $errors = $this->validate_registration($firstname, $lastname, $email, $phone, $password, $password_confirm);

            if (!empty($errors)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errors)
                ]);
                return;
            }

            // Nettoyer le téléphone
            $phone = $this->clean_phone_number($phone);

            // Vérifier si le téléphone existe déjà
            if ($this->find_patient_by_phone($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone est déjà utilisé'
                ]);
                return;
            }

            // Vérifier si l'email existe déjà
            if ($this->find_patient_by_email($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Cette adresse email est déjà utilisée'
                ]);
                return;
            }

            // Créer le client Perfex
            $client_id = $this->create_client($firstname, $lastname, $email, $phone, $password);

            if (!$client_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création du compte'
                ]);
                return;
            }

            // Créer le patient DietZone
            $patient_id = $this->create_patient($client_id, $firstname, $lastname, $phone);

            if (!$patient_id) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la création du profil patient'
                ]);
                return;
            }

            // Envoyer SMS de bienvenue
            $this->send_welcome_sms($phone, $firstname);

            // Connecter automatiquement
            $this->connect_patient($client_id, false);

            echo json_encode([
                'success' => true,
                'message' => 'Inscription réussie ! Bienvenue sur DietZone',
                'redirect' => site_url('dietetic/portal')
            ]);
        }
    }

    /**
     * Demande de code OTP par SMS
     */
    public function request_otp()
    {
        if ($this->input->post()) {
            $phone = $this->input->post('phone');

            if (empty($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Veuillez entrer votre numéro de téléphone'
                ]);
                return;
            }

            $phone = $this->clean_phone_number($phone);

            // Vérifier si le numéro existe
            $patient = $this->find_patient_by_phone($phone);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone n\'est pas enregistré'
                ]);
                return;
            }

            // Générer un code OTP
            $otp_code = $this->generate_otp();

            // Stocker l'OTP dans la session (expire dans 5 minutes)
            $this->session->set_userdata('otp_code', $otp_code);
            $this->session->set_userdata('otp_phone', $phone);
            $this->session->set_userdata('otp_expiry', time() + 300); // 5 minutes

            // Envoyer le code par SMS
            $this->send_otp_sms($phone, $otp_code);

            echo json_encode([
                'success' => true,
                'message' => 'Code de vérification envoyé par SMS'
            ]);
        }
    }

    /**
     * Vérification du code OTP
     */
    public function verify_otp()
    {
        if ($this->input->post()) {
            $code = $this->input->post('code');
            $phone = $this->input->post('phone');

            if (empty($code)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Veuillez entrer le code de vérification'
                ]);
                return;
            }

            // Vérifier si l'OTP est valide
            $stored_code = $this->session->userdata('otp_code');
            $stored_phone = $this->session->userdata('otp_phone');
            $expiry = $this->session->userdata('otp_expiry');

            if (!$stored_code || !$stored_phone || !$expiry) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Code de vérification expiré ou invalide'
                ]);
                return;
            }

            // Vérifier l'expiration
            if (time() > $expiry) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le code de vérification a expiré'
                ]);
                return;
            }

            // Vérifier le code et le téléphone
            $phone = $this->clean_phone_number($phone);

            if ($code !== $stored_code || $phone !== $stored_phone) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Code de vérification incorrect'
                ]);
                return;
            }

            // OTP valide, connecter le patient
            $patient = $this->find_patient_by_phone($phone);
            $this->connect_patient($patient->client_id, false);

            // Nettoyer la session OTP
            $this->session->unset_userdata('otp_code');
            $this->session->unset_userdata('otp_phone');
            $this->session->unset_userdata('otp_expiry');

            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => site_url('dietetic/portal')
            ]);
        }
    }

    /**
     * Demande de réinitialisation de mot de passe
     */
    public function forgot_password()
    {
        if ($this->input->post()) {
            $phone = $this->input->post('phone');

            if (empty($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Veuillez entrer votre numéro de téléphone'
                ]);
                return;
            }

            $phone = $this->clean_phone_number($phone);

            // Vérifier si le numéro existe
            $patient = $this->find_patient_by_phone($phone);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone n\'est pas enregistré'
                ]);
                return;
            }

            // Générer un code de réinitialisation
            $reset_code = $this->generate_otp();

            // Stocker dans la session
            $this->session->set_userdata('reset_code', $reset_code);
            $this->session->set_userdata('reset_phone', $phone);
            $this->session->set_userdata('reset_expiry', time() + 600); // 10 minutes

            // Envoyer par SMS
            $this->send_reset_sms($phone, $reset_code);

            echo json_encode([
                'success' => true,
                'message' => 'Code de réinitialisation envoyé par SMS',
                'show_reset_form' => true
            ]);
        }
    }

    /**
     * Réinitialisation du mot de passe avec code
     */
    public function reset_password()
    {
        if ($this->input->post()) {
            $phone = $this->input->post('phone');
            $code = $this->input->post('code');
            $password = $this->input->post('password');
            $password_confirm = $this->input->post('password_confirm');

            // Validation
            if (empty($code) || empty($password) || empty($password_confirm)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Veuillez remplir tous les champs'
                ]);
                return;
            }

            if ($password !== $password_confirm) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Les mots de passe ne correspondent pas'
                ]);
                return;
            }

            if (strlen($password) < 6) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le mot de passe doit contenir au moins 6 caractères'
                ]);
                return;
            }

            // Vérifier le code
            $stored_code = $this->session->userdata('reset_code');
            $stored_phone = $this->session->userdata('reset_phone');
            $expiry = $this->session->userdata('reset_expiry');

            if (!$stored_code || !$stored_phone || !$expiry) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Code de réinitialisation expiré ou invalide'
                ]);
                return;
            }

            if (time() > $expiry) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le code de réinitialisation a expiré'
                ]);
                return;
            }

            $phone = $this->clean_phone_number($phone);

            if ($code !== $stored_code || $phone !== $stored_phone) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Code de réinitialisation incorrect'
                ]);
                return;
            }

            // Trouver le patient
            $patient = $this->find_patient_by_phone($phone);

            if (!$patient) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Patient introuvable'
                ]);
                return;
            }

            // Mettre à jour le mot de passe
            $this->update_client_password($patient->client_id, $password);

            // Nettoyer la session
            $this->session->unset_userdata('reset_code');
            $this->session->unset_userdata('reset_phone');
            $this->session->unset_userdata('reset_expiry');

            echo json_encode([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès',
                'redirect' => site_url('dietetic/auth')
            ]);
        }
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        $this->session->unset_userdata('client_logged_in');
        $this->session->unset_userdata('client_user_id');
        $this->session->sess_destroy();
        redirect(site_url('dietetic/auth'));
    }

    /**
     * Diagnostic AJAX pour tester la connexion
     */
    public function check_phone()
    {
        if (!$this->input->post('phone')) {
            echo json_encode([
                'success' => false,
                'message' => 'Numéro de téléphone requis'
            ]);
            return;
        }

        $phone_input = $this->input->post('phone');
        $phone_cleaned = $this->clean_phone_number($phone_input);

        $result = [
            'success' => true,
            'phone_input' => $phone_input,
            'phone_cleaned' => $phone_cleaned,
            'patient_found' => false,
            'has_password' => false,
            'phone_match' => false,
            'patient_info' => null,
            'similar_numbers' => []
        ];

        // Chercher le patient
        $patient = $this->find_patient_by_phone($phone_cleaned);

        if ($patient) {
            // Récupérer les infos complètes du contact
            $this->db->select('ct.*, c.company, LENGTH(ct.password) as password_length');
            $this->db->from(db_prefix() . 'contacts ct');
            $this->db->join(db_prefix() . 'clients c', 'c.userid = ct.userid');
            $this->db->where('ct.userid', $patient->client_id);
            $this->db->where('ct.is_primary', 1);
            $contact = $this->db->get()->row();

            if ($contact) {
                $result['patient_found'] = true;
                $result['has_password'] = !empty($contact->password);
                $result['phone_match'] = ($contact->phonenumber === $phone_cleaned);
                $result['patient_info'] = [
                    'name' => $contact->firstname . ' ' . $contact->lastname,
                    'email' => $contact->email,
                    'phone_db' => $contact->phonenumber,
                    'password_length' => $contact->password_length,
                    'active' => $contact->active
                ];
            }
        } else {
            // Chercher des numéros similaires (8 derniers chiffres)
            $search_pattern = '%' . substr($phone_cleaned, -8) . '%';
            $this->db->select('ct.phonenumber, ct.firstname, ct.lastname');
            $this->db->from(db_prefix() . 'contacts ct');
            $this->db->join(db_prefix() . 'dietic_patients p', 'ct.userid = p.client_id');
            $this->db->where('ct.phonenumber LIKE', $search_pattern);
            $this->db->group_by('ct.phonenumber');
            $this->db->limit(5);
            $similar = $this->db->get()->result_array();
            $result['similar_numbers'] = $similar;
        }

        echo json_encode($result);
    }

    // ==================== MÉTHODES PRIVÉES ====================

    /**
     * Nettoyer le numéro de téléphone
     */
    private function clean_phone_number($phone)
    {
        // Supprimer les espaces, tirets, parenthèses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // S'assurer qu'il commence par +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Trouver un patient par téléphone
     */
    private function find_patient_by_phone($phone)
    {
        // Chercher dans les contacts
        $this->db->select('p.*, ct.contactid');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = p.client_id AND ct.is_primary = 1');
        $this->db->where('ct.phonenumber', $phone);

        $result = $this->db->get()->row();

        // Log pour debug
        if ($result) {
            log_message('debug', 'Patient found for phone ' . $phone . ' - Client ID: ' . $result->client_id);
        } else {
            log_message('debug', 'No patient found for phone ' . $phone);
        }

        return $result;
    }

    /**
     * Trouver un patient par email
     */
    private function find_patient_by_email($email)
    {
        $this->db->select('p.*');
        $this->db->from(db_prefix() . 'dietic_patients p');
        $this->db->join(db_prefix() . 'contacts ct', 'ct.userid = p.client_id');
        $this->db->where('ct.email', $email);
        $this->db->where('ct.is_primary', 1);

        return $this->db->get()->row();
    }

    /**
     * Vérifier le mot de passe
     */
    private function verify_password($client_id, $password)
    {
        // Récupérer le contact principal
        $this->db->where('userid', $client_id);
        $this->db->where('is_primary', 1);
        $contact = $this->db->get(db_prefix() . 'contacts')->row();

        if (!$contact || empty($contact->password)) {
            return false;
        }

        return app_hasher()->CheckPassword($password, $contact->password);
    }

    /**
     * Connecter le patient
     */
    private function connect_patient($client_id, $remember = false)
    {
        $this->session->set_userdata('client_logged_in', true);
        $this->session->set_userdata('client_user_id', $client_id);

        if ($remember) {
            // Cookie de 30 jours
            set_cookie('remember_client', $client_id, 2592000);
        }

        log_activity('Patient logged in [Client ID: ' . $client_id . ']');
    }

    /**
     * Créer un client Perfex
     */
    private function create_client($firstname, $lastname, $email, $phone, $password)
    {
        $data = [
            'company' => $firstname . ' ' . $lastname,
            'vat' => '',
            'phonenumber' => $phone,
            'country' => 221, // Sénégal
            'city' => '',
            'zip' => '',
            'state' => '',
            'address' => '',
            'website' => '',
            'datecreated' => date('Y-m-d H:i:s'),
            'active' => 1,
            'leadid' => null,
            'billing_street' => '',
            'billing_city' => '',
            'billing_state' => '',
            'billing_zip' => '',
            'billing_country' => 221,
            'shipping_street' => '',
            'shipping_city' => '',
            'shipping_state' => '',
            'shipping_zip' => '',
            'shipping_country' => 221
        ];

        $this->db->insert(db_prefix() . 'clients', $data);
        $client_id = $this->db->insert_id();

        // Créer le contact principal
        if ($client_id) {
            $contact_data = [
                'userid' => $client_id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'phonenumber' => $phone,
                'title' => '',
                'password' => app_hash_password($password),
                'datecreated' => date('Y-m-d H:i:s'),
                'is_primary' => 1,
                'active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'email_verification_key' => null,
                'email_verification_sent_at' => null,
                'last_ip' => $this->input->ip_address(),
                'last_login' => null,
                'last_password_change' => date('Y-m-d H:i:s')
            ];

            $this->db->insert(db_prefix() . 'contacts', $contact_data);
        }

        return $client_id;
    }

    /**
     * Créer un patient DietZone
     */
    private function create_patient($client_id, $firstname, $lastname, $phone)
    {
        $data = [
            'client_id' => $client_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'dietic_patients', $data);
        return $this->db->insert_id();
    }

    /**
     * Mettre à jour le mot de passe d'un client
     */
    private function update_client_password($client_id, $password)
    {
        $this->db->where('userid', $client_id);
        $this->db->where('is_primary', 1);
        $this->db->update(db_prefix() . 'contacts', [
            'password' => app_hash_password($password),
            'last_password_change' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Générer un code OTP à 6 chiffres
     */
    private function generate_otp()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Envoyer SMS de bienvenue
     */
    private function send_welcome_sms($phone, $name)
    {
        $message = "Bienvenue sur DietZone, {$name} ! Votre compte a ete cree avec succes. Connectez-vous pour commencer votre parcours sante.";
        $this->send_sms($phone, $message);
    }

    /**
     * Envoyer code OTP par SMS
     */
    private function send_otp_sms($phone, $code)
    {
        $message = "Votre code de verification DietZone : {$code}. Valide pendant 5 minutes.";
        $this->send_sms($phone, $message);
    }

    /**
     * Envoyer code de réinitialisation par SMS
     */
    private function send_reset_sms($phone, $code)
    {
        $message = "Code de reinitialisation DietZone : {$code}. Valide pendant 10 minutes.";
        $this->send_sms($phone, $message);
    }

    /**
     * Envoyer un SMS via l'API LAM
     */
    private function send_sms($phone, $message)
    {
        try {
            $this->load->model('dietetic/dietetic_notifications_model');

            // Utiliser le système de notification existant
            $success = $this->dietetic_notifications_model->send_sms_simple($phone, $message);

            if ($success) {
                log_activity('SMS sent to ' . $phone . ' [Auth system]');
            } else {
                log_message('error', 'Failed to send SMS to ' . $phone);
            }

            return $success;
        } catch (Exception $e) {
            log_message('error', 'Failed to send SMS: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validation de l'inscription
     */
    private function validate_registration($firstname, $lastname, $email, $phone, $password, $password_confirm)
    {
        $errors = [];

        if (empty($firstname)) {
            $errors[] = 'Le prénom est requis';
        }

        if (empty($lastname)) {
            $errors[] = 'Le nom est requis';
        }

        if (empty($email)) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide';
        }

        if (empty($phone)) {
            $errors[] = 'Le numéro de téléphone est requis';
        }

        if (empty($password)) {
            $errors[] = 'Le mot de passe est requis';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

        if ($password !== $password_confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        return $errors;
    }
}
