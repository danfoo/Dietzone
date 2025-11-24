<?php
/**
 * Script d'application de la migration des champs d'anamnèse
 * URL d'accès: https://votredomaine.com/admin/dietetic/apply_anamnesis_migration
 *
 * ⚠️ ATTENTION: Ce script modifie la structure de la base de données
 */

defined('BASEPATH') or exit('No direct script access allowed');

// Get database instance
$CI =& get_instance();
$CI->load->database();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Application Migration Anamnèse</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #01807B; border-bottom: 3px solid #01807B; padding-bottom: 10px; }
h3 { color: #333; margin-top: 30px; }
.log-entry { padding: 10px; margin: 5px 0; border-radius: 5px; border-left: 4px solid; font-family: monospace; font-size: 13px; }
.log-success { background: #d4edda; border-color: #28a745; color: #155724; }
.log-error { background: #f8d7da; border-color: #dc3545; color: #721c24; }
.log-info { background: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
.log-warning { background: #fff3cd; border-color: #ffc107; color: #856404; }
.summary { background: #e8f4f3; padding: 20px; border-radius: 5px; border-left: 4px solid #01807B; margin: 20px 0; }
.summary-item { display: inline-block; margin: 10px 20px 10px 0; }
.count { background: #01807B; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 16px; }
.count-success { background: #28a745; }
.count-error { background: #dc3545; }
.count-skip { background: #6c757d; }
.btn { display: inline-block; padding: 12px 24px; background: #01807B; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 10px 0; font-weight: bold; }
.btn:hover { background: #015a57; }
.progress-bar { width: 100%; height: 30px; background: #e9ecef; border-radius: 5px; overflow: hidden; margin: 20px 0; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #01807B 0%, #019B95 100%); transition: width 0.3s; text-align: center; line-height: 30px; color: white; font-weight: bold; }
.alert { padding: 15px; border-radius: 5px; margin: 20px 0; }
.alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
.alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
.alert-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🚀 Application de la Migration Anamnèse</h2>";

// Vérifier si la migration a déjà été appliquée partiellement
$query = $CI->db->query("SHOW COLUMNS FROM " . db_prefix() . "dietic_patients");
$existing_columns = [];
foreach ($query->result() as $column) {
    $existing_columns[] = $column->Field;
}

// Charger le fichier SQL de migration
$migration_file = __DIR__ . '/anamnesis_fields_migration.sql';

if (!file_exists($migration_file)) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>❌ ERREUR:</strong> Le fichier de migration n'a pas été trouvé : {$migration_file}";
    echo "</div>";
    echo "<a href='" . admin_url('dietetic/check_anamnesis_fields') . "' class='btn'>Retour à la vérification</a>";
    echo "</div></body></html>";
    exit;
}

$migration_sql = file_get_contents($migration_file);

// Remplacer le préfixe des tables
$migration_sql = str_replace('tbldietic_', db_prefix() . 'dietic_', $migration_sql);

// Extraire les statements SQL (séparer par point-virgule)
$statements = array_filter(array_map('trim', explode(';', $migration_sql)));

// Nettoyer les commentaires
$clean_statements = [];
foreach ($statements as $statement) {
    // Ignorer les lignes de commentaire
    if (strpos(trim($statement), '--') === 0) {
        continue;
    }
    // Supprimer les commentaires en fin de ligne
    $statement = preg_replace('/--.*$/', '', $statement);
    $statement = trim($statement);

    if (!empty($statement) && stripos($statement, 'ALTER TABLE') !== false) {
        $clean_statements[] = $statement;
    }
}

echo "<div class='alert alert-info'>";
echo "<strong>ℹ️ Information:</strong> {count($clean_statements)} commandes SQL à exécuter.";
echo "</div>";

// Compteurs
$total = count($clean_statements);
$success_count = 0;
$error_count = 0;
$skip_count = 0;

echo "<div class='progress-bar'>";
echo "<div class='progress-fill' id='progress' style='width: 0%;'>0%</div>";
echo "</div>";

echo "<h3>📝 Journal d'exécution</h3>";
echo "<div id='log-container'>";

// Exécuter chaque statement
$execution_logs = [];

foreach ($clean_statements as $index => $statement) {
    $statement_number = $index + 1;
    $progress = round(($statement_number / $total) * 100);

    // Extraire le nom de la colonne de l'ALTER TABLE
    preg_match('/ADD COLUMN `([^`]+)`/', $statement, $matches);
    $column_name = $matches[1] ?? 'unknown';

    // Vérifier si la colonne existe déjà
    if (in_array($column_name, $existing_columns)) {
        $skip_count++;
        $log_class = 'log-warning';
        $log_icon = '⚠️';
        $log_message = "SKIPPED [{$statement_number}/{$total}]: La colonne '{$column_name}' existe déjà";
        $execution_logs[] = compact('log_class', 'log_icon', 'log_message');
        continue;
    }

    try {
        $CI->db->query($statement);
        $success_count++;
        $log_class = 'log-success';
        $log_icon = '✅';
        $log_message = "SUCCESS [{$statement_number}/{$total}]: Colonne '{$column_name}' ajoutée avec succès";
        $execution_logs[] = compact('log_class', 'log_icon', 'log_message');

        // Ajouter la colonne à la liste des colonnes existantes
        $existing_columns[] = $column_name;

    } catch (Exception $e) {
        $error_count++;
        $error_message = $e->getMessage();
        // Limiter la longueur du message d'erreur
        if (strlen($error_message) > 200) {
            $error_message = substr($error_message, 0, 200) . '...';
        }
        $log_class = 'log-error';
        $log_icon = '❌';
        $log_message = "ERROR [{$statement_number}/{$total}]: Échec pour '{$column_name}' - " . $error_message;
        $execution_logs[] = compact('log_class', 'log_icon', 'log_message');
    }

    // Mettre à jour la barre de progression (simulation avec JavaScript inline)
    echo "<script>document.getElementById('progress').style.width = '{$progress}%'; document.getElementById('progress').textContent = '{$progress}%';</script>";
    flush();
}

// Afficher tous les logs
foreach ($execution_logs as $log) {
    echo "<div class='{$log['log_class']}'>{$log['log_icon']} {$log['log_message']}</div>";
}

echo "</div>"; // log-container

// Résumé final
echo "<h3>📊 Résumé de la Migration</h3>";
echo "<div class='summary'>";
echo "<div class='summary-item'><strong>Total:</strong> <span class='count'>{$total}</span></div>";
echo "<div class='summary-item'><strong>Succès:</strong> <span class='count count-success'>{$success_count}</span></div>";
echo "<div class='summary-item'><strong>Erreurs:</strong> <span class='count count-error'>{$error_count}</span></div>";
echo "<div class='summary-item'><strong>Ignorés:</strong> <span class='count count-skip'>{$skip_count}</span></div>";
echo "</div>";

// Message final
if ($error_count > 0) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>⚠️ ATTENTION:</strong> La migration s'est terminée avec {$error_count} erreur(s). ";
    echo "Certains champs n'ont peut-être pas été ajoutés correctement. ";
    echo "Veuillez vérifier les logs ci-dessus et contacter le support si nécessaire.";
    echo "</div>";
} elseif ($success_count > 0) {
    echo "<div class='alert alert-success'>";
    echo "<strong>✅ SUCCÈS:</strong> Migration complétée avec succès ! ";
    echo "{$success_count} nouveau(x) champ(s) ont été ajoutés à la table patients.";
    echo "</div>";

    // Log activity
    log_activity('Dietetic Module: Migration anamnèse appliquée - ' . $success_count . ' champs ajoutés');
} else {
    echo "<div class='alert alert-info'>";
    echo "<strong>ℹ️ INFORMATION:</strong> Aucune modification nécessaire. ";
    echo "Tous les champs d'anamnèse sont déjà présents dans la base de données.";
    echo "</div>";
}

// Boutons d'action
echo "<h3>🛠️ Actions</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='" . admin_url('dietetic/check_anamnesis_fields') . "' class='btn'>🔍 Vérifier les Champs</a>";
echo "<a href='" . admin_url('dietetic/patients') . "' class='btn'>📋 Aller aux Patients</a>";
echo "<a href='" . admin_url('dietetic/patients/patient') . "' class='btn'>➕ Créer un Patient</a>";
echo "</div>";

echo "<div style='margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px; border-left: 4px solid #666;'>";
echo "<p style='color: #666; font-size: 13px; margin: 0;'>";
echo "<strong>Note:</strong> Cette migration a ajouté 60+ nouveaux champs pour un suivi d'anamnèse complet. ";
echo "Vous pouvez maintenant utiliser le formulaire patient enrichi avec toutes les sections d'anamnèse.";
echo "</p>";
echo "</div>";

echo "</div>"; // container
echo "</body></html>";
