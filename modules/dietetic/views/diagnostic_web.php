<!DOCTYPE html>
<html>
<head>
    <title>Dietetic Module - Diagnostic 404</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .result {
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #ddd;
            background: #f9f9f9;
        }
        .result.success { border-color: #28a745; background: #d4edda; }
        .result.warning { border-color: #ffc107; background: #fff3cd; }
        .result.error { border-color: #dc3545; background: #f8d7da; }
        .result.info { border-color: #17a2b8; background: #d1ecf1; }
        .summary {
            background: #333;
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .action-btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 10px 0;
            font-weight: bold;
        }
        .action-btn:hover {
            background: #5568d3;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Dietetic Module - Diagnostic 404</h1>
        <p>Diagnostic complet de l'erreur 404 sur /admin/clients/client/</p>
    </div>

    <?php
    $issues = 0;
    $warnings = 0;
    $tests_run = 0;
    $CI = &get_instance();
    $prefix = db_prefix();
    ?>

    <!-- Test 1: Module Status -->
    <div class="test-section">
        <div class="test-title">[1/10] Status du Module</div>
        <?php
        $tests_run++;
        try {
            $module = $CI->db->query("SELECT * FROM {$prefix}modules WHERE module_name = 'dietetic'")->row();
            if ($module) {
                echo '<div class="result success">✓ Module enregistré dans la base de données</div>';
                echo '<div class="result info">Active: ' . ($module->active ? 'OUI' : 'NON') . '</div>';
            } else {
                echo '<div class="result error">❌ Module NON enregistré dans la base de données</div>';
                $issues++;
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 2: Database Connection -->
    <div class="test-section">
        <div class="test-title">[2/10] Connexion Base de Données</div>
        <?php
        $tests_run++;
        try {
            $test = $CI->db->query("SELECT 1 as test")->row();
            if ($test && $test->test == 1) {
                echo '<div class="result success">✓ Connexion base de données OK</div>';
            } else {
                echo '<div class="result error">❌ Réponse inattendue de la base</div>';
                $issues++;
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur de connexion: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 3: Foreign Keys -->
    <div class="test-section">
        <div class="test-title">[3/10] Contraintes de Clé Étrangère</div>
        <?php
        $tests_run++;
        try {
            $fks = $CI->db->query("
                SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME, UPDATE_RULE, DELETE_RULE
                FROM information_schema.REFERENTIAL_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = '{$prefix}dietic_patients'
            ")->result();

            if (empty($fks)) {
                echo '<div class="result warning">⚠ Aucune contrainte FK trouvée</div>';
                echo '<div class="result info">Ceci peut être normal si elles ont été supprimées pour résoudre les conflits</div>';
                $warnings++;
            } else {
                echo '<div class="result success">✓ ' . count($fks) . ' contrainte(s) FK trouvée(s)</div>';
                foreach ($fks as $fk) {
                    echo '<div class="result info">';
                    echo "<strong>{$fk->CONSTRAINT_NAME}</strong>: {$fk->TABLE_NAME} → {$fk->REFERENCED_TABLE_NAME}<br>";
                    echo "DELETE: {$fk->DELETE_RULE}, UPDATE: {$fk->UPDATE_RULE}";
                    echo '</div>';
                }
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 4: Client Table -->
    <div class="test-section">
        <div class="test-title">[4/10] Table Clients</div>
        <?php
        $tests_run++;
        try {
            $client_count = $CI->db->query("SELECT COUNT(*) as cnt FROM {$prefix}clients")->row();
            echo '<div class="result success">✓ Table clients accessible</div>';
            echo '<div class="result info">Nombre de clients: ' . $client_count->cnt . '</div>';

            // Check specific client 8
            $client8 = $CI->db->query("SELECT userid, company FROM {$prefix}clients WHERE userid = 8")->row();
            if ($client8) {
                echo '<div class="result success">✓ Client ID 8 existe: ' . htmlspecialchars($client8->company) . '</div>';
            } else {
                echo '<div class="result warning">⚠ Client ID 8 n\'existe pas</div>';
                $warnings++;
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 5: Models Loading -->
    <div class="test-section">
        <div class="test-title">[5/10] Chargement des Modèles</div>
        <?php
        $tests_run++;
        try {
            $CI->load->model('dietetic/dietetic_patients_model');
            echo '<div class="result success">✓ dietetic_patients_model chargé</div>';

            $CI->load->model('clients_model');
            echo '<div class="result success">✓ clients_model chargé</div>';
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur de chargement: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 6: Hooks Status -->
    <div class="test-section">
        <div class="test-title">[6/10] Status des Hooks</div>
        <?php
        $tests_run++;
        $hooks_file = FCPATH . 'modules/dietetic/dietetic.php';
        if (file_exists($hooks_file)) {
            $content = file_get_contents($hooks_file);

            // Check if customer_profile_tabs hook is active
            if (strpos($content, "// hooks()->add_action('customer_profile_tabs'") !== false) {
                echo '<div class="result info">ℹ Hook customer_profile_tabs est DÉSACTIVÉ (commenté)</div>';
            } elseif (strpos($content, "hooks()->add_action('customer_profile_tabs'") !== false) {
                echo '<div class="result warning">⚠ Hook customer_profile_tabs est ACTIF</div>';
                $warnings++;
            } else {
                echo '<div class="result error">❌ Hook customer_profile_tabs introuvable</div>';
                $issues++;
            }

            // Check if disable_hooks is included
            if (strpos($content, 'disable_hooks_temporarily.php') !== false) {
                echo '<div class="result warning">⚠ Le fichier disable_hooks_temporarily.php est inclus (mode test)</div>';
                $warnings++;
            }
        } else {
            echo '<div class="result error">❌ Fichier dietetic.php introuvable</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 7: Routing Conflicts -->
    <div class="test-section">
        <div class="test-title">[7/10] Conflits de Routing</div>
        <?php
        $tests_run++;
        $controllers_dir = FCPATH . 'modules/dietetic/controllers/';
        if (is_dir($controllers_dir)) {
            $controllers = array_diff(scandir($controllers_dir), ['.', '..']);
            $conflicts = [];

            foreach ($controllers as $file) {
                $name = strtolower(str_replace('.php', '', $file));
                if (in_array($name, ['client', 'clients', 'contact', 'contacts'])) {
                    $conflicts[] = $file;
                }
            }

            if (empty($conflicts)) {
                echo '<div class="result success">✓ Aucun conflit de nom de controller</div>';
            } else {
                echo '<div class="result error">❌ Conflits possibles: ' . implode(', ', $conflicts) . '</div>';
                $issues++;
            }

            echo '<div class="result info">Controllers trouvés: ' . implode(', ', $controllers) . '</div>';
        } else {
            echo '<div class="result error">❌ Dossier controllers introuvable</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 8: Activity Log -->
    <div class="test-section">
        <div class="test-title">[8/10] Logs d'Activité Récents</div>
        <?php
        $tests_run++;
        try {
            $logs = $CI->db->query("
                SELECT date, description
                FROM {$prefix}activity_log
                WHERE description LIKE '%Dietetic%' OR description LIKE '%dietetic%'
                ORDER BY date DESC
                LIMIT 10
            ")->result();

            if (empty($logs)) {
                echo '<div class="result info">ℹ Aucun log Dietetic récent</div>';
            } else {
                echo '<div class="result info">Logs récents trouvés:</div>';
                foreach ($logs as $log) {
                    $desc = htmlspecialchars(substr($log->description, 0, 100));
                    echo '<div class="result info"><small>[' . $log->date . ']</small> ' . $desc . '</div>';
                }
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $issues++;
        }
        ?>
    </div>

    <!-- Test 9: Permissions -->
    <div class="test-section">
        <div class="test-title">[9/10] Permissions du Module</div>
        <?php
        $tests_run++;
        if (is_admin()) {
            echo '<div class="result success">✓ Vous êtes administrateur</div>';

            if (has_permission('dietetic', '', 'view')) {
                echo '<div class="result success">✓ Permission dietetic.view OK</div>';
            } else {
                echo '<div class="result warning">⚠ Permission dietetic.view manquante</div>';
                $warnings++;
            }
        } else {
            echo '<div class="result warning">⚠ Vous n\'êtes pas administrateur</div>';
            $warnings++;
        }
        ?>
    </div>

    <!-- Test 10: File Permissions -->
    <div class="test-section">
        <div class="test-title">[10/10] Permissions Fichiers</div>
        <?php
        $tests_run++;
        $check_files = [
            'modules/dietetic/dietetic.php',
            'modules/dietetic/controllers/Patients.php',
            'modules/dietetic/models/Dietetic_patients_model.php'
        ];

        foreach ($check_files as $file) {
            $full_path = FCPATH . $file;
            if (file_exists($full_path)) {
                $perms = fileperms($full_path);
                $perms_str = substr(sprintf('%o', $perms), -4);
                echo '<div class="result success">✓ ' . $file . ' (permissions: ' . $perms_str . ')</div>';
            } else {
                echo '<div class="result error">❌ Fichier introuvable: ' . $file . '</div>';
                $issues++;
            }
        }
        ?>
    </div>

    <!-- Summary -->
    <div class="summary">
        <h2>📊 Résumé du Diagnostic</h2>
        <p><strong>Tests exécutés:</strong> <?php echo $tests_run; ?>/10</p>
        <p><strong>Problèmes critiques:</strong> <span class="<?php echo $issues > 0 ? 'error' : 'success'; ?>"><?php echo $issues; ?></span></p>
        <p><strong>Avertissements:</strong> <span class="<?php echo $warnings > 0 ? 'warning' : 'success'; ?>"><?php echo $warnings; ?></span></p>

        <h3>🎯 Recommandations</h3>

        <?php if ($issues == 0 && $warnings == 0): ?>
            <p class="success">✅ Aucun problème détecté dans le module lui-même.</p>
            <p>L'erreur 404 peut être causée par:</p>
            <ul>
                <li>Un problème de routing Perfex (fichiers core)</li>
                <li>Un conflit avec un autre module</li>
                <li>Une configuration serveur (.htaccess, nginx config)</li>
                <li>Un problème de cache</li>
            </ul>
        <?php else: ?>
            <?php if ($issues > 0): ?>
                <p class="error">❌ <?php echo $issues; ?> problème(s) critique(s) détecté(s)</p>
                <p>Actions recommandées:</p>
                <ul>
                    <li>Corrigez les erreurs critiques listées ci-dessus</li>
                    <li>Désactivez temporairement le module pour confirmer qu'il est la cause</li>
                    <li>Vérifiez les logs PHP serveur</li>
                </ul>
            <?php endif; ?>

            <?php if ($warnings > 0): ?>
                <p class="warning">⚠ <?php echo $warnings; ?> avertissement(s)</p>
                <p>Ces avertissements peuvent ne pas causer l'erreur 404 mais devraient être examinés.</p>
            <?php endif; ?>
        <?php endif; ?>

        <h3>🔧 Prochaines Étapes</h3>
        <div class="code">
# 1. Testez avec le module désactivé
Setup > Modules > Dietetic > Deactivate
Puis accédez à /admin/clients/client/8

# 2. Vérifiez les logs PHP en temps réel
tail -f /var/log/apache2/error.log
# OU
tail -f /var/log/nginx/error.log

# 3. Clear le cache Perfex
rm -rf application/cache/*
        </div>

        <div style="margin-top: 20px;">
            <a href="<?php echo admin_url('dietetic'); ?>" class="action-btn">← Retour au Module</a>
            <a href="<?php echo admin_url('clients/client/8'); ?>" class="action-btn">Tester Client #8</a>
            <a href="<?php echo current_url(); ?>" class="action-btn">🔄 Rafraîchir</a>
        </div>
    </div>

</body>
</html>
