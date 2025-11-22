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

        // Check if patient_id is provided
        $patient_id = $this->input->get('patient_id');

        if ($patient_id) {
            echo "=== Test de notification pour patient #{$patient_id} ===\n\n";

            // Get patient
            $patient = $this->db->get_where(db_prefix() . 'dietic_patients', ['id' => $patient_id])->row();
            if (!$patient) {
                echo "ERREUR: Patient #{$patient_id} introuvable!\n";
                exit;
            }

            echo "Patient trouvé: ID {$patient->id}\n";
            echo "Client ID: {$patient->client_id}\n\n";

            // Get client
            $this->load->model('clients_model');
            $client = $this->clients_model->get($patient->client_id);
            if (!$client) {
                echo "ERREUR: Client #{$patient->client_id} introuvable!\n";
                exit;
            }

            echo "Client: {$client->company}\n";
            echo "Email (client): " . (isset($client->email) ? $client->email : '(non défini)') . "\n";
            echo "Téléphone (client): " . (isset($client->phonenumber) ? $client->phonenumber : '(non défini)') . "\n\n";

            // Get primary contact for this client
            echo "=== Recherche du contact principal ===\n\n";
            $this->db->where('userid', $patient->client_id);
            $this->db->where('is_primary', 1);
            $contact = $this->db->get(db_prefix() . 'contacts')->row();

            if ($contact) {
                echo "✅ Contact principal trouvé:\n";
                echo "Nom: {$contact->firstname} {$contact->lastname}\n";
                echo "Email: {$contact->email}\n";
                echo "Téléphone: {$contact->phonenumber}\n\n";
            } else {
                echo "❌ Pas de contact principal\n";
                // Try to get any contact
                $this->db->where('userid', $patient->client_id);
                $this->db->order_by('id', 'ASC');
                $this->db->limit(1);
                $contact = $this->db->get(db_prefix() . 'contacts')->row();

                if ($contact) {
                    echo "→ Contact trouvé (non principal):\n";
                    echo "Nom: {$contact->firstname} {$contact->lastname}\n";
                    echo "Email: {$contact->email}\n";
                    echo "Téléphone: {$contact->phonenumber}\n\n";
                } else {
                    echo "→ Aucun contact trouvé pour ce client!\n\n";
                }
            }

            // Use contact email/phone if available
            $email_to_use = '';
            $phone_to_use = '';

            if ($contact) {
                $email_to_use = $contact->email;
                $phone_to_use = $contact->phonenumber;
            } elseif (isset($client->email)) {
                $email_to_use = $client->email;
                $phone_to_use = $client->phonenumber ?? '';
            }

            echo "→ Email à utiliser: " . ($email_to_use ?: '(VIDE)') . "\n";
            echo "→ Téléphone à utiliser: " . ($phone_to_use ?: '(VIDE)') . "\n\n";

            // Get preferences
            echo "=== Vérification des préférences ===\n\n";

            $prefs = $this->db->get_where(db_prefix() . 'dietic_notification_preferences', ['patient_id' => $patient_id])->row();

            if (!$prefs) {
                echo "❌ PROBLÈME: Aucune préférence trouvée!\n";
                echo "→ Les préférences devraient être créées automatiquement.\n";
                echo "→ Test de création manuelle...\n\n";

                $this->dietetic_notifications_model->create_default_preferences($patient_id);
                $prefs = $this->db->get_where(db_prefix() . 'dietic_notification_preferences', ['patient_id' => $patient_id])->row();

                if ($prefs) {
                    echo "✅ Préférences créées avec succès!\n\n";
                } else {
                    echo "❌ ERREUR: Impossible de créer les préférences!\n";
                    exit;
                }
            } else {
                echo "✅ Préférences existantes\n\n";
            }

            echo "Notification consultation: " . ($prefs->notify_consultation ? '✅ Activé' : '❌ Désactivé') . "\n";
            echo "Canal Email: " . ($prefs->channel_email ? '✅ Activé' : '❌ Désactivé') . "\n";
            echo "Canal SMS: " . ($prefs->channel_sms ? '✅ Activé' : '❌ Désactivé') . "\n";
            echo "Canal WhatsApp: " . ($prefs->channel_whatsapp ? '✅ Activé' : '❌ Désactivé') . "\n";
            echo "Canal Push: " . (isset($prefs->channel_push) && $prefs->channel_push ? '✅ Activé' : '❌ Désactivé') . "\n\n";

            // Test notification
            echo "=== Test d'envoi de notification ===\n\n";

            $result = $this->dietetic_notifications_model->notify_consultation_scheduled(
                $patient_id,
                date('Y-m-d', strtotime('+1 day')),
                '10:00:00',
                'Dr. Test',
                'Consultation de suivi'
            );

            if ($result) {
                echo "✅ Notification envoyée avec succès!\n\n";
                echo "Résultats:\n";
                print_r($result);
            } else {
                echo "❌ Échec de l'envoi de la notification!\n";
                echo "→ Vérifiez que notify_consultation est activé dans les préférences.\n";
            }

            exit;
        }

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
