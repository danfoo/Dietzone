<?php
/**
 * API d'authentification mobile DietZone
 * Endpoint public pour l'application mobile
 *
 * URLs:
 * - GET  /mobile_auth.php?action=login_page  -> Page de connexion
 * - POST /mobile_auth.php?action=login        -> Connexion
 * - POST /mobile_auth.php?action=check_phone  -> Diagnostic
 */

// Charger Perfex CRM
define('FCPATH', __DIR__ . '/');
require_once(FCPATH . 'application/config/config.php');
require_once(FCPATH . 'application/config/database.php');

// Démarrer la session
session_start();

// Connexion à la base de données
$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($mysqli->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erreur de connexion BDD']));
}

$mysqli->set_charset('utf8mb4');
$db_prefix = $db['default']['dbprefix'];

// Fonction pour nettoyer le numéro de téléphone
function clean_phone($phone) {
    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
    if (!str_starts_with($phone, '+')) {
        $phone = '+' . $phone;
    }
    return $phone;
}

// Router
$action = $_GET['action'] ?? 'login_page';

switch ($action) {
    case 'check_phone':
        check_phone($mysqli, $db_prefix);
        break;

    case 'login':
        login($mysqli, $db_prefix);
        break;

    case 'login_page':
    default:
        login_page();
        break;
}

/**
 * Diagnostic téléphone
 */
function check_phone($mysqli, $db_prefix) {
    header('Content-Type: application/json');

    if (!isset($_POST['phone'])) {
        echo json_encode(['success' => false, 'message' => 'Numéro requis']);
        exit;
    }

    $phone = clean_phone($_POST['phone']);

    // Chercher le patient
    $stmt = $mysqli->prepare("
        SELECT p.id, p.client_id, ct.firstname, ct.lastname, ct.email, ct.phonenumber,
               LENGTH(ct.password) as password_length, ct.password IS NOT NULL as has_password
        FROM {$db_prefix}dietic_patients p
        JOIN {$db_prefix}contacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
        WHERE ct.phonenumber = ?
    ");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($patient = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'patient_found' => true,
            'has_password' => (bool)$patient['has_password'],
            'phone_match' => true,
            'patient_info' => [
                'name' => $patient['firstname'] . ' ' . $patient['lastname'],
                'email' => $patient['email'],
                'phone_db' => $patient['phonenumber'],
                'password_length' => $patient['password_length']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'patient_found' => false,
            'similar_numbers' => []
        ]);
    }
    exit;
}

/**
 * Connexion
 */
function login($mysqli, $db_prefix) {
    header('Content-Type: application/json');

    $phone = clean_phone($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Champs requis']);
        exit;
    }

    // Chercher le patient
    $stmt = $mysqli->prepare("
        SELECT p.client_id, ct.password, ct.firstname, ct.lastname
        FROM {$db_prefix}dietic_patients p
        JOIN {$db_prefix}contacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
        WHERE ct.phonenumber = ?
    ");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$patient = $result->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }

    // Vérifier mot de passe (utiliser password_verify si hashé avec password_hash)
    // Pour Perfex, utiliser leur système de hash
    require_once(FCPATH . 'application/helpers/app_helper.php');

    if (!app_hasher()->CheckPassword($password, $patient['password'])) {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
        exit;
    }

    // Créer session
    $_SESSION['client_logged_in'] = true;
    $_SESSION['client_user_id'] = $patient['client_id'];

    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'redirect' => '/dietetic/portal'
    ]);
    exit;
}

/**
 * Page de connexion
 */
function login_page() {
    // Inclure la vue login que nous avons créée
    include(__DIR__ . '/modules/dietetic/views/auth/login.php');
}
