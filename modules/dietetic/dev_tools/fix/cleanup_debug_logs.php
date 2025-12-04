<?php
/**
 * Script de nettoyage des logs de debug
 * Remplace tous les log_activity debug par dietetic_debug_log()
 *
 * UTILISATION:
 * php cleanup_debug_logs.php
 *
 * OU via navigateur:
 * https://votredomaine.com/admin/dietetic/../cleanup_debug_logs.php
 */

// Patterns à remplacer
$replacements = [
    // Logs avec emojis
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]🔍\s*\[DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log($1, \'debug\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]✅\s*\[DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log($1, \'success\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]⚠️\s*\[DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log($1, \'warning\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]❌\s*\[DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log($1, \'error\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]ℹ️\s*\[DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log($1, \'info\')'
    ],
    // Logs textuels DEBUG
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]DEBUG:\s*(.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'$1\', \'debug\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]\[DEBUG\]\s*(.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'$1\', \'debug\')'
    ],
    // Logs de test
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]\[TEST_PUSH DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'TEST_PUSH:$1\', \'debug\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]\[DIETETIC DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'$1\', \'debug\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]\[MODEL DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'MODEL:$1\', \'debug\')'
    ],
    [
        'pattern' => '/log_activity\s*\(\s*[\'"]\[FCM DEBUG\](.*?)[\'"]\s*\)/i',
        'replacement' => 'dietetic_debug_log(\'FCM:$1\', \'debug\')'
    ],
];

// Fichiers à traiter
$files_to_clean = [
    __DIR__ . '/models/Dietetic_notifications_model.php',
    __DIR__ . '/controllers/Notifications.php',
    __DIR__ . '/controllers/Portal.php',
    __DIR__ . '/controllers/Food_surveys.php',
];

$total_replacements = 0;
$files_modified = 0;

echo "<html><head><title>Cleanup Debug Logs</title>";
echo "<style>
body { font-family: Arial; margin: 20px; background: #f5f5f5; }
.container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
h2 { color: #01807B; }
.file { background: #e8f4f3; padding: 15px; margin: 10px 0; border-left: 4px solid #01807B; }
.success { color: #28a745; font-weight: bold; }
.info { color: #17a2b8; }
.warning { color: #ffc107; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🧹 Nettoyage des Logs de Debug</h2>";

foreach ($files_to_clean as $file_path) {
    if (!file_exists($file_path)) {
        echo "<div class='file'><span class='warning'>⚠️ Fichier non trouvé:</span> {$file_path}</div>";
        continue;
    }

    $original_content = file_get_contents($file_path);
    $modified_content = $original_content;
    $file_replacements = 0;

    foreach ($replacements as $replacement) {
        $count = 0;
        $modified_content = preg_replace(
            $replacement['pattern'],
            $replacement['replacement'],
            $modified_content,
            -1,
            $count
        );
        $file_replacements += $count;
    }

    if ($file_replacements > 0) {
        // Backup original
        $backup_file = $file_path . '.backup_' . date('YmdHis');
        file_put_contents($backup_file, $original_content);

        // Save modified
        file_put_contents($file_path, $modified_content);

        $total_replacements += $file_replacements;
        $files_modified++;

        $file_name = basename($file_path);
        echo "<div class='file'>";
        echo "<span class='success'>✅ {$file_name}</span><br>";
        echo "<span class='info'>{$file_replacements} remplacement(s)</span><br>";
        echo "<small>Backup: {$backup_file}</small>";
        echo "</div>";
    } else {
        $file_name = basename($file_path);
        echo "<div class='file'><span class='info'>ℹ️ {$file_name}:</span> Aucun remplacement nécessaire</div>";
    }
}

echo "<div style='background: #d4edda; padding: 20px; margin-top: 20px; border-radius: 5px; border-left: 4px solid #28a745;'>";
echo "<h3 style='color: #155724; margin: 0 0 10px 0;'>🎉 Nettoyage Terminé</h3>";
echo "<ul style='margin: 0; color: #155724;'>";
echo "<li><strong>{$files_modified}</strong> fichier(s) modifié(s)</li>";
echo "<li><strong>{$total_replacements}</strong> remplacement(s) effectué(s)</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; margin-top: 20px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<strong>⚠️ Note:</strong> Les fichiers originaux ont été sauvegardés avec l'extension .backup_YYYYMMDDHHMMSS";
echo "</div>";

echo "<div style='margin-top: 20px;'>";
echo "<h3>📝 Prochaines Étapes</h3>";
echo "<ol>";
echo "<li>Vérifier que les fichiers modifiés fonctionnent correctement</li>";
echo "<li>Définir <code>DIETETIC_DEBUG = false</code> dans <code>config.php</code> pour production</li>";
echo "<li>Tester l'application en mode debug désactivé</li>";
echo "<li>Commit les modifications dans Git</li>";
echo "</ol>";
echo "</div>";

echo "</div></body></html>";
