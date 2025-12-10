<?php
/**
 * Script de diagnostic - Liste tous les patients avec leurs numéros
 * Accessible via : /modules/dietetic/list_patients_phones.php
 */

defined('BASEPATH') or define('BASEPATH', true);
chdir(__DIR__ . '/../..');
require_once('application/libraries/App_controller.php');

$CI = &get_instance();
$CI->load->database();

// Récupérer tous les patients avec leurs numéros
$query = $CI->db->query("
    SELECT
        p.id as patient_id,
        p.client_id,
        c.company,
        ct.contactid,
        ct.firstname,
        ct.lastname,
        ct.email,
        ct.phonenumber,
        ct.active,
        LENGTH(ct.password) as password_length,
        ct.password IS NOT NULL as has_password,
        ct.last_login
    FROM " . db_prefix() . "dietic_patients p
    JOIN " . db_prefix() . "contacts ct ON ct.userid = p.client_id AND ct.is_primary = 1
    JOIN " . db_prefix() . "clients c ON c.userid = p.client_id
    ORDER BY p.id DESC
    LIMIT 20
");

$patients = $query->result_array();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste Patients - Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        h1 { color: #01807B; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #01807B; color: white; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #0c5460; }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic - Liste des Patients</h1>

    <div class="info">
        <strong>Ce diagnostic montre :</strong><br>
        ✅ Les 20 derniers patients DietZone<br>
        ✅ Leur numéro de téléphone EXACT dans la BDD<br>
        ✅ S'ils ont un mot de passe défini<br>
        ✅ Leur email (utilisé par Perfex pour l'authentification)
    </div>

    <p><strong>Total patients trouvés :</strong> <?php echo count($patients); ?></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone (BDD)</th>
                <th>Mot de passe</th>
                <th>Actif</th>
                <th>Dernière connexion</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                        Aucun patient trouvé
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?php echo $p['patient_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['firstname'] . ' ' . $p['lastname']); ?></strong><br>
                        <small style="color: #666;">Client ID: <?php echo $p['client_id']; ?></small>
                    </td>
                    <td>
                        <code><?php echo htmlspecialchars($p['email']); ?></code>
                    </td>
                    <td>
                        <?php if ($p['phonenumber']): ?>
                            <code style="background: #e7f3f1; color: #01807B; font-weight: 600;">
                                <?php echo htmlspecialchars($p['phonenumber']); ?>
                            </code>
                        <?php else: ?>
                            <span style="color: #999;">Non renseigné</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['has_password']): ?>
                            <span class="badge badge-success">✓ OUI (<?php echo $p['password_length']; ?> car.)</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✗ NON</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['active']): ?>
                            <span class="badge badge-success">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Inactif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($p['last_login']): ?>
                            <?php echo date('d/m/Y H:i', strtotime($p['last_login'])); ?>
                        <?php else: ?>
                            <span style="color: #999;">Jamais</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="info" style="margin-top: 30px;">
        <strong>📝 Comment utiliser cette information :</strong><br>
        1. Trouvez votre compte dans la liste<br>
        2. Copiez EXACTEMENT le format du téléphone affiché<br>
        3. Vérifiez que vous avez un mot de passe (badge vert "✓ OUI")<br>
        4. Si pas de mot de passe, utilisez "Mot de passe oublié" ou demandez à l'admin de vous en créer un
    </div>
</body>
</html>
