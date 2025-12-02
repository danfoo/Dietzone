<?php
/**
 * Script de Test des Notifications Push Firebase
 * À exécuter via : https://app.dietsenegal.net/test_push_notification.php
 *
 * Ce script envoie une notification push de test à tous les tokens actifs
 */

// Load CodeIgniter - Find the correct path
$ci_paths = [
    __DIR__ . '/index.php',
    __DIR__ . '/../index.php',
];

$ci_loaded = false;
foreach ($ci_paths as $path) {
    if (file_exists($path)) {
        define('BASEPATH', dirname($path) . '/');
        $_SERVER['REQUEST_URI'] = '/test_push';
        require_once $path;
        $ci_loaded = true;
        break;
    }
}

if (!$ci_loaded) {
    die('❌ Cannot load CodeIgniter');
}

// Get CodeIgniter instance
$CI =& get_instance();

// Check if user is admin (basic auth check)
if (!function_exists('is_admin') || !is_admin()) {
    // Simple password protection for this test script
    $test_password = 'dietsenegal2025'; // Change this!

    if (!isset($_GET['password']) || $_GET['password'] !== $test_password) {
        die('❌ Access denied. Use ?password=dietsenegal2025');
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Push Notifications - DietSenegal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f7fafc;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #01807B;
            border-bottom: 3px solid #01807B;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2d3748;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 5px;
            font-size: 14px;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        button {
            background: linear-gradient(135deg, #01807B 0%, #026660 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background: #f0fff4;
            border: 1px solid #9ae6b4;
            color: #22543d;
        }
        .error {
            background: #fff5f5;
            border: 1px solid #fc8181;
            color: #c53030;
        }
        .info {
            background: #ebf8ff;
            border: 1px solid #90cdf4;
            color: #2c5282;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #01807B;
            color: white;
        }
        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #01807B;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Test des Notifications Push Firebase</h1>

        <div class="info" style="display: block; margin-bottom: 20px;">
            <strong>ℹ️ Information</strong><br>
            Ce script permet d'envoyer une notification push de test à un token FCM spécifique ou à tous les tokens actifs.
        </div>

        <form id="testForm" method="POST">
            <div class="form-group">
                <label>Mode d'envoi</label>
                <select name="send_mode" id="send_mode">
                    <option value="all">Envoyer à tous les tokens actifs</option>
                    <option value="single">Envoyer à un token spécifique</option>
                    <option value="patient">Envoyer à un patient spécifique</option>
                </select>
            </div>

            <div class="form-group" id="token_field" style="display: none;">
                <label>Token FCM</label>
                <input type="text" name="fcm_token" placeholder="Token FCM (commence par c... ou d...)">
            </div>

            <div class="form-group" id="patient_field" style="display: none;">
                <label>Patient ID</label>
                <input type="number" name="patient_id" placeholder="ID du patient">
            </div>

            <div class="form-group">
                <label>Titre de la notification</label>
                <input type="text" name="title" value="🧪 Test DietSenegal" required>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" required>Ceci est une notification de test envoyée à <?php echo date('H:i:s'); ?>. Si vous recevez ce message, vos notifications push fonctionnent correctement ! 🎉</textarea>
            </div>

            <div class="form-group">
                <label>URL de redirection (optionnel)</label>
                <input type="text" name="click_action" value="<?php echo site_url('dietetic/portal'); ?>">
            </div>

            <button type="submit">📤 Envoyer la Notification</button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Envoi en cours...</p>
        </div>

        <div class="result" id="result"></div>

        <?php
        // Display active tokens
        $CI->load->database();
        $tokens = $CI->db
            ->select('t.id, t.patient_id, t.token, t.device_type, t.device_name, t.created_at, t.last_used_at, p.client_id')
            ->from(db_prefix() . 'dietic_fcm_tokens t')
            ->join(db_prefix() . 'dietic_patients p', 'p.id = t.patient_id', 'left')
            ->where('t.is_active', 1)
            ->order_by('t.created_at', 'DESC')
            ->get()
            ->result();

        if (!empty($tokens)) {
            echo '<h2>📱 Tokens FCM Actifs (' . count($tokens) . ')</h2>';
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Patient</th><th>Device</th><th>Token</th><th>Créé le</th><th>Dernier usage</th></tr></thead>';
            echo '<tbody>';
            foreach ($tokens as $token) {
                echo '<tr>';
                echo '<td>' . $token->id . '</td>';
                echo '<td>Patient #' . $token->patient_id . ' (Client #' . $token->client_id . ')</td>';
                echo '<td>' . $token->device_type . '</td>';
                echo '<td><code style="font-size: 11px;">' . substr($token->token, 0, 30) . '...</code></td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($token->created_at)) . '</td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($token->last_used_at)) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="error" style="display: block; margin-top: 20px;">';
            echo '<strong>❌ Aucun token actif trouvé</strong><br>';
            echo 'Les patients doivent d\'abord activer les notifications push sur le portail.';
            echo '</div>';
        }
        ?>
    </div>

    <script>
        // Toggle fields based on send mode
        document.getElementById('send_mode').addEventListener('change', function() {
            const mode = this.value;
            document.getElementById('token_field').style.display = mode === 'single' ? 'block' : 'none';
            document.getElementById('patient_field').style.display = mode === 'patient' ? 'block' : 'none';
        });

        // Handle form submission
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const loading = document.getElementById('loading');
            const result = document.getElementById('result');

            loading.style.display = 'block';
            result.style.display = 'none';

            fetch('test_push_notification.php?action=send&password=<?php echo isset($_GET['password']) ? $_GET['password'] : ''; ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                result.style.display = 'block';
                result.className = 'result ' + (data.success ? 'success' : 'error');
                result.innerHTML = '<strong>' + (data.success ? '✅ Succès' : '❌ Erreur') + '</strong><br>' + data.message;

                if (data.details) {
                    result.innerHTML += '<br><br><pre>' + JSON.stringify(data.details, null, 2) + '</pre>';
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                result.style.display = 'block';
                result.className = 'result error';
                result.innerHTML = '<strong>❌ Erreur</strong><br>' + error.message;
            });
        });
    </script>
</body>
</html>

<?php
// Handle AJAX send request
if (isset($_GET['action']) && $_GET['action'] === 'send') {
    header('Content-Type: application/json');

    $CI->load->library('dietetic/firebase_cloud_messaging');

    $send_mode = $CI->input->post('send_mode');
    $title = $CI->input->post('title');
    $message = $CI->input->post('message');
    $click_action = $CI->input->post('click_action');

    if (empty($title) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Le titre et le message sont requis'
        ]);
        exit;
    }

    $options = [
        'click_action' => $click_action ?: site_url('dietetic/portal')
    ];

    $results = [
        'success' => false,
        'message' => '',
        'details' => []
    ];

    try {
        if ($send_mode === 'single') {
            // Send to specific token
            $token = $CI->input->post('fcm_token');
            if (empty($token)) {
                throw new Exception('Token FCM requis');
            }

            $result = $CI->firebase_cloud_messaging->send_to_device($token, $title, $message, [], $options);
            $results['success'] = $result['success'];
            $results['message'] = $result['success'] ? 'Notification envoyée avec succès!' : 'Échec de l\'envoi';
            $results['details'] = $result;

        } elseif ($send_mode === 'patient') {
            // Send to patient
            $patient_id = $CI->input->post('patient_id');
            if (empty($patient_id)) {
                throw new Exception('Patient ID requis');
            }

            $result = $CI->firebase_cloud_messaging->send_to_patient($patient_id, $title, $message, [], $options);
            $results['success'] = isset($result['success']) && $result['success'] > 0;
            $results['message'] = $results['success']
                ? "Notification envoyée à {$result['success']} appareil(s)"
                : 'Aucun appareil actif trouvé pour ce patient';
            $results['details'] = $result;

        } else {
            // Send to all active tokens
            $tokens = $CI->db
                ->select('token')
                ->from(db_prefix() . 'dietic_fcm_tokens')
                ->where('is_active', 1)
                ->get()
                ->result_array();

            if (empty($tokens)) {
                throw new Exception('Aucun token actif trouvé');
            }

            $token_list = array_column($tokens, 'token');
            $result = $CI->firebase_cloud_messaging->send_to_devices($token_list, $title, $message, [], $options);

            $results['success'] = $result['success'] > 0;
            $results['message'] = "Envoyé à {$result['success']} appareil(s), {$result['failure']} échec(s)";
            $results['details'] = $result;
        }
    } catch (Exception $e) {
        $results['success'] = false;
        $results['message'] = $e->getMessage();
    }

    echo json_encode($results);
    exit;
}
?>
