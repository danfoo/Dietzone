<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            padding: 30px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
        }
        .diagnostic-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ddd;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .test-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .test-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .test-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .test-title {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .test-result {
            margin-top: 5px;
            font-size: 14px;
        }
        .code-block {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-top: 10px;
        }
        .badge-custom {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success-custom {
            background: #28a745;
            color: white;
        }
        .badge-error-custom {
            background: #dc3545;
            color: white;
        }
        .btn-test-api {
            margin-top: 15px;
        }
        #api-response {
            display: none;
            margin-top: 15px;
        }
        .header-diagnostic {
            background: linear-gradient(135deg, #01807B, #019B95);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-diagnostic">
            <h1><i class="fa fa-stethoscope"></i> <?php echo $title; ?></h1>
            <p>Diagnostic complet du système de notifications du portail patient</p>
        </div>

        <!-- Test 1: User Login Status -->
        <div class="diagnostic-card">
            <div class="test-item <?php echo ($is_client_logged_in || $is_staff_logged_in) ? 'success' : 'error'; ?>">
                <div class="test-title">
                    <i class="fa fa-user"></i> Test 1: Statut de Connexion
                </div>
                <div class="test-result">
                    <strong>Staff connecté:</strong>
                    <span class="badge-custom <?php echo $is_staff_logged_in ? 'badge-success-custom' : 'badge-error-custom'; ?>">
                        <?php echo $is_staff_logged_in ? 'OUI' : 'NON'; ?>
                    </span>
                    <br>
                    <strong>Client connecté:</strong>
                    <span class="badge-custom <?php echo $is_client_logged_in ? 'badge-success-custom' : 'badge-error-custom'; ?>">
                        <?php echo $is_client_logged_in ? 'OUI' : 'NON'; ?>
                    </span>
                    <?php if (isset($client_id)): ?>
                        <br><strong>Client ID:</strong> <?php echo $client_id; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Test 2: Patient Record -->
        <?php if (isset($patient_exists)): ?>
        <div class="diagnostic-card">
            <div class="test-item <?php echo $patient_exists ? 'success' : 'error'; ?>">
                <div class="test-title">
                    <i class="fa fa-user-md"></i> Test 2: Enregistrement Patient
                </div>
                <div class="test-result">
                    <?php if ($patient_exists): ?>
                        <strong>Patient trouvé:</strong> OUI<br>
                        <strong>Patient ID:</strong> <?php echo $patient->id; ?><br>
                        <strong>Nom:</strong> <?php echo $patient->first_name . ' ' . $patient->last_name; ?>
                    <?php else: ?>
                        <strong>Patient trouvé:</strong> NON<br>
                        <span style="color: #dc3545;">⚠️ Aucun enregistrement patient trouvé pour ce client</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Test 3: Database Tables -->
        <div class="diagnostic-card">
            <h4><i class="fa fa-database"></i> Test 3: Tables de Base de Données</h4>

            <div class="test-item <?php echo $notification_logs_exists ? 'success' : 'error'; ?>">
                <div class="test-title">Table: tbldietic_notification_logs</div>
                <div class="test-result">
                    <span class="badge-custom <?php echo $notification_logs_exists ? 'badge-success-custom' : 'badge-error-custom'; ?>">
                        <?php echo $notification_logs_exists ? 'EXISTE' : 'MANQUANTE'; ?>
                    </span>
                    <?php if ($notification_logs_count !== null): ?>
                        <br><strong>Nombre de notifications:</strong> <?php echo $notification_logs_count; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="test-item <?php echo $patient_notifications_exists ? 'success' : 'warning'; ?>">
                <div class="test-title">Table: tbldietic_patient_notifications</div>
                <div class="test-result">
                    <span class="badge-custom <?php echo $patient_notifications_exists ? 'badge-success-custom' : 'badge-error-custom'; ?>">
                        <?php echo $patient_notifications_exists ? 'EXISTE' : 'MANQUANTE'; ?>
                    </span>
                    <?php if ($patient_notifications_count !== null): ?>
                        <br><strong>Nombre de notifications patient:</strong> <?php echo $patient_notifications_count; ?>
                    <?php elseif (!$patient_notifications_exists): ?>
                        <br><span style="color: #856404;">⚠️ Table manquante - Le système utilisera tbldietic_notification_logs en fallback</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="test-item <?php echo $notification_settings_exists ? 'success' : 'warning'; ?>">
                <div class="test-title">Table: tbldietic_notification_settings</div>
                <div class="test-result">
                    <span class="badge-custom <?php echo $notification_settings_exists ? 'badge-success-custom' : 'badge-error-custom'; ?>">
                        <?php echo $notification_settings_exists ? 'EXISTE' : 'MANQUANTE'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Test 4: API Test -->
        <?php if (isset($api_test_url)): ?>
        <div class="diagnostic-card">
            <div class="test-item">
                <div class="test-title">
                    <i class="fa fa-plug"></i> Test 4: API get_notifications
                </div>
                <div class="test-result">
                    <strong>URL de l'API:</strong> <code><?php echo $api_test_url; ?></code>
                    <br>
                    <button class="btn btn-primary btn-test-api" onclick="testAPI()">
                        <i class="fa fa-play"></i> Tester l'API maintenant
                    </button>
                    <div id="api-response">
                        <h5>Réponse de l'API:</h5>
                        <div class="code-block" id="api-response-content"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Test 5: JavaScript Console Check -->
        <div class="diagnostic-card">
            <div class="test-item warning">
                <div class="test-title">
                    <i class="fa fa-code"></i> Test 5: Vérification Console JavaScript
                </div>
                <div class="test-result">
                    <p><strong>Instructions:</strong></p>
                    <ol>
                        <li>Ouvrez le portail patient: <a href="<?php echo site_url('dietetic/portal'); ?>" target="_blank"><?php echo site_url('dietetic/portal'); ?></a></li>
                        <li>Appuyez sur <kbd>F12</kbd> pour ouvrir la console du navigateur</li>
                        <li>Allez dans l'onglet "Console"</li>
                        <li>Cliquez sur l'icône de cloche <i class="fa fa-bell"></i> dans le header</li>
                        <li>Regardez les messages dans la console qui commencent par <code>[NOTIF]</code></li>
                    </ol>
                    <p><strong>Messages attendus:</strong></p>
                    <div class="code-block">
📥 [NOTIF] API Response: {success: true, ...}
✅ [NOTIF] Success! Found X notifications, unread: Y
🔍 [NOTIF] Filtered notifications (filter=all): X
🎨 [NOTIF] displayNotifications called with X notifications
                    </div>
                </div>
            </div>
        </div>

        <!-- Diagnostic Summary -->
        <div class="diagnostic-card">
            <h4><i class="fa fa-check-circle"></i> Résumé du Diagnostic</h4>
            <?php
            $all_good = true;
            $errors = [];
            $warnings = [];

            if (!$is_client_logged_in && !$is_staff_logged_in) {
                $errors[] = "Utilisateur non connecté";
                $all_good = false;
            }

            if (isset($patient_exists) && !$patient_exists) {
                $errors[] = "Enregistrement patient manquant";
                $all_good = false;
            }

            if (!$notification_logs_exists) {
                $errors[] = "Table tbldietic_notification_logs manquante";
                $all_good = false;
            }

            if (!$patient_notifications_exists) {
                $warnings[] = "Table tbldietic_patient_notifications manquante (fallback activé)";
            }

            if (!$notification_settings_exists) {
                $warnings[] = "Table tbldietic_notification_settings manquante";
            }

            if (isset($notification_logs_count) && $notification_logs_count == 0) {
                $warnings[] = "Aucune notification dans la base de données";
            }
            ?>

            <?php if ($all_good && empty($warnings)): ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <strong>Tous les tests sont au vert !</strong>
                    <p>Le système de notifications devrait fonctionner correctement.</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Erreurs détectées:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($warnings)): ?>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-circle"></i> <strong>Avertissements:</strong>
                    <ul>
                        <?php foreach ($warnings as $warning): ?>
                            <li><?php echo $warning; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions Recommandées -->
        <div class="diagnostic-card">
            <h4><i class="fa fa-wrench"></i> Actions Recommandées</h4>
            <div class="alert alert-info">
                <h5>Pour résoudre les problèmes de notifications:</h5>
                <ol>
                    <li>Si des tables sont manquantes, exécutez les migrations: <a href="<?php echo admin_url('dietetic/migrations'); ?>" target="_blank">Admin > Dietetic > Migrations</a></li>
                    <li>Si aucune notification n'existe, testez en créant une notification de test depuis l'admin</li>
                    <li>Vérifiez que le patient est bien assigné à un diététicien</li>
                    <li>Consultez la console JavaScript (F12) pour voir les erreurs en temps réel</li>
                    <li>Si le problème persiste, contactez le support technique</li>
                </ol>
            </div>
        </div>

        <div class="text-center" style="margin-top: 30px;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-lg btn-primary">
                <i class="fa fa-arrow-left"></i> Retour au Portail Patient
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function testAPI() {
            const btn = document.querySelector('.btn-test-api');
            const responseDiv = document.getElementById('api-response');
            const responseContent = document.getElementById('api-response-content');

            // Disable button
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Test en cours...';

            // Show response div
            responseDiv.style.display = 'block';
            responseContent.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Chargement...';

            // Call API
            fetch('<?php echo isset($api_test_url) ? $api_test_url : ""; ?>')
                .then(response => {
                    console.log('Status:', response.status);
                    console.log('Headers:', response.headers);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);

                    // Format JSON for display
                    const formatted = JSON.stringify(data, null, 2);
                    responseContent.innerHTML = '<pre>' + formatted + '</pre>';

                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-refresh"></i> Tester à nouveau';

                    // Add success/error class
                    if (data.success) {
                        responseDiv.className = 'test-item success';
                    } else {
                        responseDiv.className = 'test-item error';
                    }
                })
                .catch(error => {
                    console.error('API Error:', error);
                    responseContent.innerHTML = '<pre style="color: red;">Erreur: ' + error.message + '</pre>';

                    // Re-enable button
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-refresh"></i> Tester à nouveau';

                    responseDiv.className = 'test-item error';
                });
        }
    </script>
</body>
</html>
