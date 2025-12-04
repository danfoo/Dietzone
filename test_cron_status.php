#!/usr/bin/env php
<?php
/**
 * Script simple pour vérifier si le cron Perfex s'exécute
 * Usage: php test_cron_status.php
 */

// Configuration - ADAPTEZ CES VALEURS
$db_host = 'localhost';
$db_name = 'trpuftja_app';  // Nom de votre base de données
$db_user = 'trpuftja_app';  // Utilisateur DB
$db_pass = '';              // Mot de passe DB (à remplir)

echo "=== Vérification du Cron Perfex ===\n";
echo "Date actuelle: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les 20 derniers logs du cron Dietetic
    $stmt = $pdo->prepare("
        SELECT date, description
        FROM tblactivity_log
        WHERE description LIKE '%Dietetic Cron%'
        ORDER BY id DESC
        LIMIT 20
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($logs)) {
        echo "❌ AUCUNE TRACE D'EXÉCUTION du cron Dietetic\n";
        echo "Le cron ne s'est jamais exécuté OU les nouveaux fichiers ne sont pas sur le serveur.\n\n";
        echo "Actions:\n";
        echo "1. Vérifiez que dietetic.php a été transféré sur le serveur\n";
        echo "2. Testez manuellement: cd /home/trpuftja/app && php index.php cron/index\n";
        echo "3. Re-exécutez ce script\n";
    } else {
        // Analyser le dernier log
        $last_log = $logs[0];
        $last_time = strtotime($last_log['date']);
        $minutes_ago = round((time() - $last_time) / 60);

        echo "📊 STATUT DU CRON\n";
        echo str_repeat('-', 50) . "\n";

        if ($minutes_ago <= 10) {
            echo "✅ CRON ACTIF - Dernière exécution il y a $minutes_ago minute(s)\n";
            echo "Le cron s'exécute correctement toutes les 5 minutes.\n";
        } else {
            echo "❌ CRON ARRÊTÉ - Dernière exécution il y a $minutes_ago minute(s)\n";
            echo "Le cron devrait s'exécuter toutes les 5 minutes.\n";
        }

        echo "\nDernière exécution: " . $last_log['date'] . "\n";
        echo "Message: " . $last_log['description'] . "\n";

        echo "\n📋 Historique des 10 dernières exécutions:\n";
        echo str_repeat('-', 50) . "\n";

        foreach (array_slice($logs, 0, 10) as $log) {
            echo $log['date'] . " | " . $log['description'] . "\n";
        }

        // Compter les exécutions aujourd'hui
        $today = date('Y-m-d');
        $count_today = 0;
        foreach ($logs as $log) {
            if (strpos($log['date'], $today) === 0) {
                $count_today++;
            }
        }

        echo "\n📈 Statistiques aujourd'hui ($today):\n";
        echo "Nombre d'exécutions: " . ($count_today / 2) . " (chaque exécution = 2 logs: début + fin)\n";
        $expected = floor((date('H') * 60 + date('i')) / 5);
        echo "Attendu: ~$expected exécutions\n";

        if ($count_today / 2 < $expected * 0.8) {
            echo "⚠️ Le nombre d'exécutions est inférieur à l'attendu. Le cron a peut-être des interruptions.\n";
        }
    }

    echo "\n";

} catch (PDOException $e) {
    echo "❌ ERREUR de connexion à la base de données:\n";
    echo $e->getMessage() . "\n\n";
    echo "Vérifiez les informations de connexion dans ce script (lignes 8-11):\n";
    echo "- \$db_host = '$db_host'\n";
    echo "- \$db_name = '$db_name'\n";
    echo "- \$db_user = '$db_user'\n";
    echo "- \$db_pass = '********'\n";
}

echo "\n=== Fin de la vérification ===\n";
