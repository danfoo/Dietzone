# Méthodes Controller à ajouter pour OneSignal

## Instructions

Ajoutez ces 3 nouvelles méthodes à la fin du fichier `modules/dietetic/controllers/Notifications.php` (avant la dernière accolade `}`).

---

## 1. Modifier test_push() - Ligne 514-520

**REMPLACER** le SQL (lignes 514-520) :

```php
// ANCIEN CODE (lignes 514-520):
$sql = "SELECT p.id,
        c.company as patient_name,
        (SELECT COUNT(*) FROM " . db_prefix() . "dietic_fcm_tokens f
         WHERE f.patient_id = p.id AND f.is_active = 1) as fcm_tokens
        FROM " . db_prefix() . "dietic_patients p
        LEFT JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
        ORDER BY c.company ASC";
```

**PAR CE NOUVEAU CODE** :

```php
// NOUVEAU CODE - Charge les patients avec OneSignal Player IDs:
$sql = "SELECT p.id,
        c.company as patient_name,
        (SELECT COUNT(DISTINCT onesignal_player_id) FROM " . db_prefix() . "dietic_fcm_tokens f
         WHERE f.patient_id = p.id
         AND f.onesignal_player_id IS NOT NULL
         AND f.onesignal_player_id != ''
         AND f.is_active = 1) as player_ids
        FROM " . db_prefix() . "dietic_patients p
        LEFT JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
        ORDER BY c.company ASC";
```

**ET MODIFIER** la ligne 531 :

```php
// ANCIEN:
return $p['fcm_tokens'] > 0;

// NOUVEAU:
return $p['player_ids'] > 0;
```

---

## 2. Ajouter send_test_onesignal() - Nouvelle méthode

**AJOUTER** cette nouvelle méthode à la fin du fichier (avant la dernière accolade `}`):

```php
/**
 * Send test push notification via OneSignal (AJAX)
 */
public function send_test_onesignal()
{
    header('Content-Type: application/json');

    if (!is_admin()) {
        echo json_encode([
            'success' => false,
            'message' => 'Accès refusé'
        ]);
        return;
    }

    // Get POST data
    $patient_id = $this->input->post('patient_id');
    $title = $this->input->post('title');
    $body = $this->input->post('body');
    $url = $this->input->post('url');
    $notification_type = $this->input->post('notification_type') ?? 'test';

    if (!$patient_id || !$title || !$body) {
        echo json_encode([
            'success' => false,
            'message' => 'Paramètres manquants'
        ]);
        return;
    }

    try {
        // Load OneSignal library
        $this->load->library('dietetic/onesignal_cloud_messaging');

        // Send notification to patient
        $result = $this->onesignal_cloud_messaging->send_to_patient(
            $patient_id,
            $title,
            $body,
            [
                'type' => $notification_type,
                'url' => $url
            ]
        );

        if ($result['success']) {
            // Log success
            log_activity(sprintf(
                '[OneSignal Test] Notification sent to patient #%d: %s',
                $patient_id,
                $title
            ));

            echo json_encode([
                'success' => true,
                'message' => 'Notification envoyée avec succès via OneSignal !',
                'title' => $title,
                'body' => $body,
                'devices_count' => $result['recipients'] ?? 0,
                'notification_id' => $result['id'] ?? null
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur OneSignal: ' . ($result['error'] ?? 'Erreur inconnue'),
                'title' => $title,
                'body' => $body
            ]);
        }

    } catch (Exception $e) {
        log_activity('[OneSignal Test] Error: ' . $e->getMessage());

        echo json_encode([
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage()
        ]);
    }
}
```

---

## 3. Ajouter get_onesignal_stats() - Nouvelle méthode

**AJOUTER** cette méthode à la fin du fichier (après `send_test_onesignal()`):

```php
/**
 * Get OneSignal push notification statistics (AJAX)
 */
public function get_onesignal_stats()
{
    header('Content-Type: application/json');

    if (!is_admin()) {
        echo json_encode([
            'success' => false,
            'message' => 'Accès refusé'
        ]);
        return;
    }

    try {
        // Count active OneSignal Player IDs
        $this->db->select('COUNT(DISTINCT onesignal_player_id) as total');
        $this->db->where('is_active', 1);
        $this->db->where('onesignal_player_id IS NOT NULL');
        $this->db->where('onesignal_player_id !=', '');
        $result = $this->db->get(db_prefix() . 'dietic_fcm_tokens')->row();
        $total_players = $result ? $result->total : 0;

        // Count patients with at least one active Player ID
        $this->db->select('COUNT(DISTINCT patient_id) as total');
        $this->db->where('is_active', 1);
        $this->db->where('onesignal_player_id IS NOT NULL');
        $this->db->where('onesignal_player_id !=', '');
        $result = $this->db->get(db_prefix() . 'dietic_fcm_tokens')->row();
        $patients_with_players = $result ? $result->total : 0;

        // Total devices = total Player IDs (each Player ID = 1 device)
        $total_devices = $total_players;

        // Count notifications sent today via OneSignal
        $this->db->where('channel', 'push');
        $this->db->where('DATE(sent_at) =', date('Y-m-d'));
        $this->db->like('response', 'OneSignal', 'both'); // Filter only OneSignal notifications
        $sent_today = $this->db->count_all_results(db_prefix() . 'dietic_notification_logs');

        echo json_encode([
            'success' => true,
            'stats' => [
                'total_players' => $total_players,
                'patients_with_players' => $patients_with_players,
                'total_devices' => $total_devices,
                'sent_today' => $sent_today
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
}
```

---

## Récapitulatif des modifications

1. **test_push()** → Modifié pour charger les patients avec Player IDs OneSignal
2. **send_test_onesignal()** → Nouvelle méthode pour envoyer via OneSignal
3. **get_onesignal_stats()** → Nouvelle méthode pour afficher les statistiques OneSignal

---

## Emplacement dans le fichier

Ajoutez les 2 nouvelles méthodes **AVANT la dernière accolade** du fichier Notifications.php (généralement autour de la ligne 1700-1800).

---

## Test après upload

1. Uploadez le fichier modifié
2. Allez sur : https://app.dietsenegal.net/admin/dietetic/notifications/test_push
3. Vous devriez voir :
   - Les statistiques OneSignal (Player IDs, pas FCM tokens)
   - Les patients avec OneSignal Player IDs dans le select
   - Le formulaire prêt à envoyer via OneSignal
