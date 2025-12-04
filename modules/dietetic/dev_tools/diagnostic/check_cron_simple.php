<?php
/**
 * Page standalone pour vérifier si le cron Perfex s'exécute
 * Accès: https://app.trpuftja.com/modules/dietetic/check_cron_simple.php
 */

// Configuration base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'trpuftja_app');
define('DB_USER', 'trpuftja_app');
define('DB_PASS', '');  // À remplir avec le mot de passe réel

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('❌ Erreur de connexion DB: ' . $e->getMessage() . '<br>Vérifiez les identifiants DB dans check_cron_simple.php (lignes 9-12)');
}

// Récupérer les logs du cron
$stmt = $pdo->prepare("
    SELECT date, description
    FROM tblactivity_log
    WHERE description LIKE '%Dietetic Cron%'
    ORDER BY id DESC
    LIMIT 50
");
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Analyser les logs
$cron_executions = [];
$last_start = null;
$last_end = null;
$errors = [];

foreach ($logs as $log) {
    if (strpos($log['description'], 'Démarrage') !== false) {
        if (!$last_start) $last_start = $log['date'];
    } elseif (strpos($log['description'], 'Fin à') !== false) {
        if (!$last_end) $last_end = $log['date'];
        $cron_executions[] = $log;
    } elseif (strpos($log['description'], 'Error') !== false) {
        $errors[] = $log;
    }
}

// Calculer le statut
$now = time();
$last_run_time = $last_end ? strtotime($last_end) : ($last_start ? strtotime($last_start) : null);
$minutes_since_last_run = $last_run_time ? round(($now - $last_run_time) / 60) : null;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Vérification Cron Perfex - Dietetic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #2196F3; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #4CAF50; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .error-box { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0; border-radius: 4px; }
        .success-box { background: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; border-radius: 4px; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; border-radius: 4px; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .timestamp { color: #666; font-size: 0.9em; }
        .cron-status { padding: 20px; margin: 20px 0; border-radius: 8px; font-size: 1.1em; }
        .status-running { background: #e8f5e9; border: 2px solid #4CAF50; }
        .status-stopped { background: #ffebee; border: 2px solid #f44336; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Vérification du Cron Perfex</h1>
        <p><strong>Date/Heure actuelle :</strong> <?= date('Y-m-d H:i:s') ?></p>

        <?php if (empty($logs)): ?>
            <div class="error-box">
                <h3>❌ AUCUNE TRACE D'EXÉCUTION</h3>
                <p>Aucun log trouvé pour "Dietetic Cron" dans la base de données.</p>
                <p><strong>Cela signifie soit :</strong></p>
                <ol>
                    <li>Le fichier <code>dietetic.php</code> modifié n'a pas été transféré sur le serveur</li>
                    <li>Le cron ne s'est jamais exécuté depuis la mise à jour</li>
                </ol>
                <p><strong>Action immédiate :</strong></p>
                <pre>cd /home/trpuftja/app
php index.php cron/index</pre>
                <p>Puis rechargez cette page.</p>
            </div>
        <?php else: ?>
            <div class="cron-status <?= ($minutes_since_last_run && $minutes_since_last_run <= 10) ? 'status-running' : 'status-stopped' ?>">
                <?php if ($minutes_since_last_run && $minutes_since_last_run <= 10): ?>
                    <p class="success">✅ <strong>CRON ACTIF</strong></p>
                    <p>Dernière exécution il y a <strong><?= $minutes_since_last_run ?> minute(s)</strong></p>
                    <p>Le cron fonctionne correctement (exécution toutes les 5 minutes attendue).</p>
                <?php else: ?>
                    <p class="error">❌ <strong>CRON ARRÊTÉ</strong></p>
                    <p>Dernière exécution il y a <strong><?= $minutes_since_last_run ?> minute(s)</strong></p>
                    <p>Le cron devrait s'exécuter toutes les 5 minutes.</p>
                <?php endif; ?>
                <p><strong>Dernière exécution :</strong> <?= $last_end ?: $last_start ?></p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <h3>⚠️ Erreurs récentes (<?= count($errors) ?>)</h3>
                    <table>
                        <tr><th>Date</th><th>Message</th></tr>
                        <?php foreach (array_slice($errors, 0, 10) as $error): ?>
                            <tr>
                                <td class="timestamp"><?= htmlspecialchars($error['date']) ?></td>
                                <td class="error"><?= htmlspecialchars($error['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>

            <h2>📊 Historique des 20 dernières exécutions</h2>
            <?php if (!empty($cron_executions)): ?>
                <table>
                    <tr><th>Date</th><th>Résultat</th></tr>
                    <?php foreach (array_slice($cron_executions, 0, 20) as $exec): ?>
                        <tr>
                            <td class="timestamp"><?= htmlspecialchars($exec['date']) ?></td>
                            <td><?= htmlspecialchars($exec['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="error-box"><p>Aucune exécution complète enregistrée.</p></div>
            <?php endif; ?>

            <?php if (!$last_run_time || $minutes_since_last_run > 10): ?>
                <div class="error-box">
                    <h3>❌ Le cron ne s'exécute pas automatiquement</h3>
                    <p><strong>Configuration cron serveur attendue :</strong></p>
                    <code>*/5 * * * * /usr/bin/php /home/trpuftja/app/index.php cron/index</code>

                    <p><strong>Vérifications à effectuer :</strong></p>
                    <ol>
                        <li><strong>Dans cPanel → Cron Jobs :</strong>
                            <ul>
                                <li>Vérifiez que la tâche existe et est active</li>
                                <li>Vérifiez le chemin complet</li>
                                <li>Vérifiez l'intervalle : <code>*/5 * * * *</code></li>
                            </ul>
                        </li>
                        <li><strong>Tester manuellement via SSH :</strong>
                            <pre>cd /home/trpuftja/app
php index.php cron/index</pre>
                            <p>Puis rechargez cette page pour voir si une nouvelle exécution apparaît.</p>
                        </li>
                        <li><strong>Consulter les logs cron du serveur</strong> dans cPanel</li>
                    </ol>
                </div>
            <?php else: ?>
                <div class="success-box">
                    <h3>✅ Le cron fonctionne correctement</h3>
                    <p>Le cron s'exécute automatiquement toutes les 5 minutes comme prévu.</p>
                    <p>Si vous ne recevez toujours pas de notifications, le problème se situe dans la logique de détection des rappels à envoyer.</p>
                </div>
            <?php endif; ?>

            <div class="info">
                <h3>ℹ️ Comment fonctionne le système</h3>
                <p><strong>1.</strong> Cron serveur (toutes les 5 min) : <code>*/5 * * * *</code></p>
                <p><strong>2.</strong> Exécute : <code>/usr/bin/php /home/trpuftja/app/index.php cron/index</code></p>
                <p><strong>3.</strong> Perfex déclenche le hook : <code>after_cron_run</code></p>
                <p><strong>4.</strong> Notre fonction s'exécute : <code>dietetic_send_scheduled_reminders()</code></p>
                <p><strong>5.</strong> Les notifications sont envoyées</p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <p><strong>🔄 Actions rapides :</strong></p>
            <ul>
                <li><a href="?refresh=1" style="color: #2196F3;">Recharger cette page</a></li>
                <li><a href="/dietetic/portal/debug_meal_reminder?meal_type=breakfast" style="color: #2196F3;">Tester les rappels de petit-déjeuner</a></li>
                <li><a href="/dietetic/portal/check_cron_execution" style="color: #2196F3;">Historique des notifications</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
