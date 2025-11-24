<!DOCTYPE html>
<html>
<head>
    <title>Diagnostic Rate Limiting</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #cce5ff; color: #004085; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; margin: 10px 0; border-radius: 4px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic: Rate Limiting & Requêtes</h1>
        <p>Ce script analyse les causes possibles de l'erreur "Too Many Requests" (429)</p>

        <h2>1️⃣ Informations Serveur</h2>
        <?php
        $server_info = [
            'Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'Protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'N/A',
            'IP Client' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'User Agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'Request Method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'Request URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        ];

        echo '<table>';
        echo '<thead><tr><th>Paramètre</th><th>Valeur</th></tr></thead>';
        echo '<tbody>';
        foreach ($server_info as $key => $value) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($key) . '</strong></td>';
            echo '<td><code>' . htmlspecialchars($value) . '</code></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        ?>

        <h2>2️⃣ Headers HTTP Reçus</h2>
        <?php
        $headers = getallheaders();
        if ($headers) {
            echo '<table>';
            echo '<thead><tr><th>Header</th><th>Valeur</th></tr></thead>';
            echo '<tbody>';
            foreach ($headers as $name => $value) {
                // Highlight rate limit related headers
                $highlight = (stripos($name, 'limit') !== false || stripos($name, 'rate') !== false) ? 'background: #fff3cd;' : '';
                echo '<tr style="' . $highlight . '">';
                echo '<td><strong>' . htmlspecialchars($name) . '</strong></td>';
                echo '<td><code>' . htmlspecialchars($value) . '</code></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="info">Aucun header HTTP détecté (fonction getallheaders() non disponible)</div>';
        }
        ?>

        <h2>3️⃣ Configuration PHP</h2>
        <?php
        $php_config = [
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_input_vars' => ini_get('max_input_vars'),
            'max_input_time' => ini_get('max_input_time'),
        ];

        echo '<table>';
        echo '<thead><tr><th>Paramètre</th><th>Valeur</th></tr></thead>';
        echo '<tbody>';
        foreach ($php_config as $key => $value) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($key) . '</strong></td>';
            echo '<td><code>' . htmlspecialchars($value) . '</code></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        ?>

        <h2>4️⃣ Test de Requêtes Successives</h2>
        <div class="info">
            <strong>ℹ️ Instructions :</strong>
            <p>Cliquez sur le bouton ci-dessous pour tester 10 requêtes rapides vers la page patient.</p>
            <p>Si une requête échoue avec une erreur 429, cela confirme le rate limiting.</p>
        </div>

        <button onclick="testRateLimit()" style="background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 15px 0;">
            🧪 Tester le Rate Limiting (10 requêtes)
        </button>

        <div id="testResults" style="margin-top: 20px;"></div>

        <h2>5️⃣ Solutions Possibles</h2>
        <div class="warning">
            <h3 style="margin-top: 0;">⚠️ Causes Possibles</h3>
            <ul>
                <li><strong>Nginx/Apache Rate Limiting</strong> : Votre serveur web limite les requêtes par IP (ex: 10 req/sec)</li>
                <li><strong>CloudFlare/WAF</strong> : Un pare-feu applicatif bloque les requêtes trop fréquentes</li>
                <li><strong>Perfex CRM Throttling</strong> : L'application a un middleware de rate limiting</li>
                <li><strong>Cache Problem</strong> : Le navigateur ou un proxy fait trop de requêtes</li>
            </ul>

            <h3>✅ Solutions</h3>
            <ol>
                <li><strong>Attendre quelques minutes</strong> : Le rate limiter se réinitialise généralement après 1-5 minutes</li>
                <li><strong>Vider le cache navigateur</strong> : Ctrl+Shift+Delete ou Cmd+Shift+Delete</li>
                <li><strong>Utiliser un autre navigateur</strong> : Pour tester si c'est spécifique au navigateur</li>
                <li><strong>Vérifier les logs serveur</strong> : Voir les logs Nginx/Apache pour identifier le rate limiter</li>
                <li><strong>Augmenter les limites</strong> : Si vous êtes admin serveur, ajuster la config Nginx/Apache</li>
            </ol>
        </div>

        <h2>6️⃣ Vérification Base de Données</h2>
        <?php
        // Check if we can connect to DB
        $CI =& get_instance();

        try {
            $query = $CI->db->query("SELECT COUNT(*) as count FROM " . db_prefix() . "dietic_patients");
            $result = $query->row();

            echo '<div class="success">';
            echo '✅ <strong>Connexion DB OK</strong><br>';
            echo 'Nombre de patients: ' . $result->count;
            echo '</div>';

            // Test a simple patient query
            $test_query = $CI->db->query("SELECT id FROM " . db_prefix() . "dietic_patients LIMIT 1");
            if ($test_query && $test_query->num_rows() > 0) {
                $patient = $test_query->row();
                echo '<div class="info">';
                echo 'Patient ID de test disponible: <strong>' . $patient->id . '</strong><br>';
                echo '<a href="' . admin_url('dietetic/patients/view/' . $patient->id) . '" target="_blank" style="color: #007bff; font-weight: bold;">→ Tenter d\'accéder à ce patient</a>';
                echo '</div>';
            }

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '❌ <strong>Erreur DB:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>

        <h2>7️⃣ Recommandations Immédiates</h2>
        <div class="info">
            <p><strong>✅ Étape 1:</strong> Attendez <strong>5 minutes</strong> avant de réessayer</p>
            <p><strong>✅ Étape 2:</strong> Videz complètement le cache de votre navigateur</p>
            <p><strong>✅ Étape 3:</strong> Si ça persiste, essayez en navigation privée</p>
            <p><strong>✅ Étape 4:</strong> Contactez votre hébergeur pour vérifier les logs serveur</p>
        </div>
    </div>

    <script>
    function testRateLimit() {
        const resultsDiv = document.getElementById('testResults');
        resultsDiv.innerHTML = '<div class="info"><strong>Test en cours...</strong></div>';

        const results = [];
        const testUrl = '<?php echo admin_url('dietetic/patients/view/1'); ?>';
        let completed = 0;

        for (let i = 0; i < 10; i++) {
            fetch(testUrl, { method: 'HEAD' })
                .then(response => {
                    results.push({
                        request: i + 1,
                        status: response.status,
                        ok: response.ok,
                        headers: {
                            'X-RateLimit-Limit': response.headers.get('X-RateLimit-Limit'),
                            'X-RateLimit-Remaining': response.headers.get('X-RateLimit-Remaining'),
                            'Retry-After': response.headers.get('Retry-After')
                        }
                    });
                })
                .catch(error => {
                    results.push({
                        request: i + 1,
                        status: 'ERROR',
                        error: error.message
                    });
                })
                .finally(() => {
                    completed++;
                    if (completed === 10) {
                        displayResults(results);
                    }
                });
        }
    }

    function displayResults(results) {
        const resultsDiv = document.getElementById('testResults');
        let html = '<table><thead><tr><th>#</th><th>Status</th><th>OK</th><th>Détails</th></tr></thead><tbody>';

        results.forEach(result => {
            const statusClass = result.status === 429 ? 'background: #f8d7da;' : (result.ok ? 'background: #d4edda;' : 'background: #fff3cd;');
            html += '<tr style="' + statusClass + '">';
            html += '<td>' + result.request + '</td>';
            html += '<td><strong>' + result.status + '</strong></td>';
            html += '<td>' + (result.ok ? '✅' : '❌') + '</td>';
            html += '<td>';
            if (result.error) {
                html += '<code>' + result.error + '</code>';
            } else if (result.headers['X-RateLimit-Limit']) {
                html += 'Limit: ' + result.headers['X-RateLimit-Limit'] + '<br>';
                html += 'Remaining: ' + result.headers['X-RateLimit-Remaining'];
            } else {
                html += 'No rate limit headers';
            }
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';

        const hasError429 = results.some(r => r.status === 429);
        if (hasError429) {
            html = '<div class="error"><strong>❌ Rate Limiting Détecté !</strong><br>Au moins une requête a retourné un code 429. Votre serveur limite les requêtes.</div>' + html;
        } else {
            html = '<div class="success"><strong>✅ Aucun Rate Limiting Détecté</strong><br>Toutes les requêtes ont réussi. Le problème vient peut-être d\'ailleurs.</div>' + html;
        }

        resultsDiv.innerHTML = html;
    }
    </script>
</body>
</html>
