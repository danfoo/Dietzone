<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Check_logs extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dietetic/dietetic_notifications_model');
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Notification Logs');
        }

        header('Content-Type: text/plain; charset=utf-8');

        echo "=== Derniers logs de notifications consultation_scheduled ===\n\n";

        $this->db->select('*');
        $this->db->from(db_prefix() . 'dietic_notification_logs');
        $this->db->where('notification_type', 'consultation_scheduled');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(10);
        $logs = $this->db->get()->result();

        if (empty($logs)) {
            echo "Aucun log trouvé.\n";
        } else {
            foreach ($logs as $log) {
                echo "ID: {$log->id}\n";
                echo "Patient ID: {$log->patient_id}\n";
                echo "Canal: {$log->channel}\n";
                echo "Destinataire: {$log->recipient}\n";
                echo "Statut: {$log->status}\n";
                echo "Créé le: {$log->created_at}\n";
                if (!empty($log->sent_at)) {
                    echo "Envoyé le: {$log->sent_at}\n";
                }
                if (!empty($log->error_message)) {
                    echo "Erreur: {$log->error_message}\n";
                }
                if (!empty($log->subject)) {
                    echo "Sujet: {$log->subject}\n";
                }
                echo "---\n\n";
            }

            echo "\n=== Préférences de notification du dernier patient ===\n\n";

            $patient_id = $logs[0]->patient_id;

            $this->db->select('*');
            $this->db->from(db_prefix() . 'dietic_notification_preferences');
            $this->db->where('patient_id', $patient_id);
            $prefs = $this->db->get()->row();

            if ($prefs) {
                echo "Patient ID: {$prefs->patient_id}\n";
                echo "Notification consultation: " . ($prefs->notify_consultation ? 'Oui' : 'Non') . "\n";
                echo "Canal Email: " . ($prefs->channel_email ? 'Activé' : 'Désactivé') . "\n";
                echo "Canal SMS: " . ($prefs->channel_sms ? 'Activé' : 'Désactivé') . "\n";
                echo "Canal WhatsApp: " . ($prefs->channel_whatsapp ? 'Activé' : 'Désactivé') . "\n";
                echo "Canal Push: " . (isset($prefs->channel_push) && $prefs->channel_push ? 'Activé' : 'Désactivé') . "\n";
            } else {
                echo "Aucune préférence trouvée pour le patient ID: {$patient_id}\n";
            }

            echo "\n=== Informations du client ===\n\n";

            $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
            if ($patient) {
                $this->load->model('clients_model');
                $client = $this->clients_model->get($patient->client_id);
                if ($client) {
                    echo "Nom: {$client->company}\n";
                    echo "Email: {$client->email}\n";
                    echo "Téléphone: {$client->phonenumber}\n";
                }
            }
        }

        exit;
    }
}
