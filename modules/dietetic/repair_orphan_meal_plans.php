<?php
/**
 * Script de réparation pour les meal plans orphelins
 *
 * Ce script détecte et répare les meal plans qui référencent des programmes inexistants
 *
 * Usage: Accéder à https://app.dietsenegal.net/modules/dietetic/repair_orphan_meal_plans.php
 */

// Ne pas exécuter si non autorisé
define('REPAIR_ENABLED', true); // Mettre à false après réparation pour sécurité

if (!REPAIR_ENABLED) {
    die('Script de réparation désactivé pour des raisons de sécurité.');
}

// Inclure le fichier de configuration de Perfex
require_once(__DIR__ . '/../../application/config/database.php');

// Créer une connexion MySQLi
$mysqli = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if ($mysqli->connect_error) {
    die('Erreur de connexion: ' . $mysqli->connect_error);
}

// Définir le préfixe de table
$prefix = $db['default']['dbprefix'];

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Réparation des Meal Plans Orphelins</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; background: white; margin: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th { background: #3498db; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f8f9fa; }
        .actions { margin: 30px 0; }
        button { background: #3498db; color: white; border: none; padding: 12px 24px; font-size: 16px; cursor: pointer; border-radius: 5px; margin-right: 10px; }
        button:hover { background: #2980b9; }
        button.danger { background: #e74c3c; }
        button.danger:hover { background: #c0392b; }
        .stat { display: inline-block; background: white; padding: 20px; margin: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); min-width: 200px; }
        .stat-value { font-size: 36px; font-weight: bold; color: #3498db; }
        .stat-label { color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>🔧 Réparation des Meal Plans Orphelins</h1>
    <p>Ce script détecte et répare les meal plans qui référencent des programmes inexistants.</p>
";

// MODE: Diagnostic ou Réparation
$action = isset($_GET['action']) ? $_GET['action'] : 'diagnostic';

if ($action === 'diagnostic') {
    echo "<div class='info'><strong>MODE: DIAGNOSTIC</strong> - Aucune modification ne sera effectuée</div>";

    // 1. Trouver tous les meal plans orphelins
    $query = "
        SELECT
            mp.id as meal_plan_id,
            mp.program_id as missing_program_id,
            mp.plan_name,
            mp.week_number,
            mp.start_date,
            mp.end_date,
            mp.created_at
        FROM {$prefix}dietic_meal_plans mp
        LEFT JOIN {$prefix}dietic_programs p ON p.id = mp.program_id
        WHERE p.id IS NULL
        ORDER BY mp.id
    ";

    $result = $mysqli->query($query);
    $orphans = [];
    while ($row = $result->fetch_assoc()) {
        $orphans[] = $row;
    }

    // 2. Statistiques
    $total_meal_plans_query = "SELECT COUNT(*) as total FROM {$prefix}dietic_meal_plans";
    $total_meal_plans = $mysqli->query($total_meal_plans_query)->fetch_assoc()['total'];

    $orphan_count = count($orphans);
    $healthy_count = $total_meal_plans - $orphan_count;

    echo "<h2>📊 Statistiques</h2>";
    echo "<div class='stat'>
            <div class='stat-value'>$total_meal_plans</div>
            <div class='stat-label'>Total Meal Plans</div>
          </div>";
    echo "<div class='stat'>
            <div class='stat-value' style='color: #e74c3c;'>$orphan_count</div>
            <div class='stat-label'>Meal Plans Orphelins</div>
          </div>";
    echo "<div class='stat'>
            <div class='stat-value' style='color: #27ae60;'>$healthy_count</div>
            <div class='stat-label'>Meal Plans Valides</div>
          </div>";

    if ($orphan_count === 0) {
        echo "<div class='success'>✅ <strong>Aucun meal plan orphelin détecté !</strong> Toutes les références sont valides.</div>";
    } else {
        echo "<div class='warning'>⚠️ <strong>$orphan_count meal plan(s) orphelin(s) détecté(s)</strong></div>";

        // Afficher la liste des orphelins
        echo "<h2>📋 Liste des Meal Plans Orphelins</h2>";
        echo "<table>";
        echo "<tr>
                <th>Meal Plan ID</th>
                <th>Nom du Plan</th>
                <th>Programme Manquant (ID)</th>
                <th>Semaine</th>
                <th>Date Début</th>
                <th>Date Fin</th>
                <th>Créé le</th>
              </tr>";

        foreach ($orphans as $orphan) {
            echo "<tr>";
            echo "<td><strong>#{$orphan['meal_plan_id']}</strong></td>";
            echo "<td>" . htmlspecialchars($orphan['plan_name'] ?? 'Sans nom') . "</td>";
            echo "<td><span style='color: #e74c3c;'>Programme #{$orphan['missing_program_id']} (inexistant)</span></td>";
            echo "<td>Semaine {$orphan['week_number']}</td>";
            echo "<td>" . ($orphan['start_date'] ?? 'N/A') . "</td>";
            echo "<td>" . ($orphan['end_date'] ?? 'N/A') . "</td>";
            echo "<td>" . $orphan['created_at'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Proposition de réparation
        echo "<h2>🔧 Proposition de Réparation</h2>";
        echo "<div class='info'>";
        echo "<p><strong>Pour chaque meal plan orphelin, le script va :</strong></p>";
        echo "<ol>";
        echo "<li>Créer un programme de remplacement</li>";
        echo "<li>Associer ce programme au patient ID 1</li>";
        echo "<li>Utiliser le diététicien du patient</li>";
        echo "<li>Définir des valeurs par défaut appropriées</li>";
        echo "<li>Mettre à jour le meal plan pour référencer le nouveau programme</li>";
        echo "</ol>";
        echo "</div>";

        // Récupérer les infos du patient ID 1 pour la réparation
        $patient_query = "
            SELECT
                p.id,
                p.client_id,
                p.dietitian_id,
                c.company as client_name
            FROM {$prefix}dietic_patients p
            LEFT JOIN {$prefix}clients c ON c.userid = p.client_id
            WHERE p.id = 1
        ";
        $patient_result = $mysqli->query($patient_query);
        $patient = $patient_result->fetch_assoc();

        if ($patient) {
            echo "<div class='info'>";
            echo "<p><strong>Patient cible :</strong> {$patient['client_name']} (ID: {$patient['id']})</p>";
            echo "<p><strong>Diététicien :</strong> ID #{$patient['dietitian_id']}</p>";
            echo "</div>";

            echo "<div class='actions'>";
            echo "<button onclick=\"if(confirm('Êtes-vous sûr de vouloir réparer tous les meal plans orphelins ?')) { window.location.href='?action=repair'; }\">🔧 Lancer la Réparation</button>";
            echo "</div>";
        } else {
            echo "<div class='error'>❌ <strong>Erreur :</strong> Patient ID 1 introuvable. Impossible de continuer.</div>";
        }
    }

} elseif ($action === 'repair') {
    echo "<div class='warning'><strong>MODE: RÉPARATION</strong> - Modifications en cours...</div>";

    // Récupérer les infos du patient ID 1
    $patient_query = "
        SELECT
            p.id,
            p.client_id,
            p.dietitian_id,
            c.company as client_name
        FROM {$prefix}dietic_patients p
        LEFT JOIN {$prefix}clients c ON c.userid = p.client_id
        WHERE p.id = 1
    ";
    $patient_result = $mysqli->query($patient_query);
    $patient = $patient_result->fetch_assoc();

    if (!$patient) {
        echo "<div class='error'>❌ <strong>Erreur :</strong> Patient ID 1 introuvable.</div>";
        die();
    }

    // 1. Trouver tous les meal plans orphelins
    $query = "
        SELECT
            mp.id as meal_plan_id,
            mp.program_id as missing_program_id,
            mp.plan_name,
            mp.week_number,
            mp.start_date,
            mp.end_date,
            mp.created_at
        FROM {$prefix}dietic_meal_plans mp
        LEFT JOIN {$prefix}dietic_programs p ON p.id = mp.program_id
        WHERE p.id IS NULL
        ORDER BY mp.id
    ";

    $result = $mysqli->query($query);
    $orphans = [];
    while ($row = $result->fetch_assoc()) {
        $orphans[] = $row;
    }

    if (count($orphans) === 0) {
        echo "<div class='success'>✅ Aucun meal plan orphelin à réparer.</div>";
    } else {
        echo "<h2>🔧 Réparation en cours...</h2>";

        $repaired = 0;
        $errors = 0;

        // Début de la transaction
        $mysqli->begin_transaction();

        try {
            foreach ($orphans as $orphan) {
                $meal_plan_id = $orphan['meal_plan_id'];
                $missing_program_id = $orphan['missing_program_id'];
                $plan_name = $orphan['plan_name'] ?? "Plan de repas #{$meal_plan_id}";

                // Créer un programme de remplacement
                $program_name = "Programme pour " . $plan_name;
                $description = "Programme créé automatiquement pour réparer le meal plan #{$meal_plan_id}";
                $start_date = $orphan['start_date'] ?? date('Y-m-d');
                $end_date = $orphan['end_date'] ?? null;
                $created_at = $orphan['created_at'];

                $insert_program_query = "
                    INSERT INTO {$prefix}dietic_programs
                    (id, patient_id, dietitian_id, program_name, description, start_date, end_date, status, created_at, updated_at)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())
                ";

                $stmt = $mysqli->prepare($insert_program_query);
                $stmt->bind_param(
                    'iiisssss',
                    $missing_program_id,
                    $patient['id'],
                    $patient['dietitian_id'],
                    $program_name,
                    $description,
                    $start_date,
                    $end_date,
                    $created_at
                );

                if ($stmt->execute()) {
                    $new_program_id = $missing_program_id; // On utilise l'ID manquant pour ne pas avoir à mettre à jour le meal plan

                    echo "<div class='success'>";
                    echo "✅ <strong>Meal Plan #{$meal_plan_id}</strong>: Programme #{$new_program_id} créé avec succès";
                    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Nom: $program_name";
                    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Patient: {$patient['client_name']}";
                    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;→ Diététicien: ID #{$patient['dietitian_id']}";
                    echo "</div>";

                    $repaired++;
                } else {
                    throw new Exception("Erreur lors de la création du programme pour le meal plan #{$meal_plan_id}: " . $stmt->error);
                }

                $stmt->close();
            }

            // Valider la transaction
            $mysqli->commit();

            echo "<div class='success'>";
            echo "<h3>✅ Réparation terminée avec succès !</h3>";
            echo "<p><strong>$repaired</strong> meal plan(s) réparé(s)</p>";
            echo "<p><strong>$errors</strong> erreur(s)</p>";
            echo "</div>";

            echo "<div class='actions'>";
            echo "<button onclick=\"window.location.href='?action=diagnostic'\">📊 Voir le Diagnostic</button>";
            echo "<button onclick=\"window.location.href='/dietetic/portal/view_meal_plan/2'\">🧪 Tester /view_meal_plan/2</button>";
            echo "</div>";

        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $mysqli->rollback();

            echo "<div class='error'>";
            echo "<h3>❌ Erreur lors de la réparation</h3>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Aucune modification n'a été effectuée (transaction annulée).</p>";
            echo "</div>";
        }
    }
}

// Fermer la connexion
$mysqli->close();

echo "
    <hr>
    <p style='color: #7f8c8d; font-size: 12px;'>
        Script de réparation - Module Dietetic Perfex CRM<br>
        Date: " . date('Y-m-d H:i:s') . "
    </p>
</body>
</html>
";
?>
