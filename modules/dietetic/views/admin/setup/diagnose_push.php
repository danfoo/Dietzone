<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h1 class="tw-font-bold tw-text-lg tw-mb-4">
                            <i class="fa fa-bell-o"></i> <?php echo $title; ?>
                        </h1>
                        <p class="text-muted">Généré le : <strong><?php echo date('d/m/Y H:i:s'); ?></strong></p>
                        <hr>

                        <!-- 1. Service Worker File -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="tw-font-semibold">📄 Fichier Service Worker</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">firebase-messaging-sw.js</th>
                                        <td>
                                            <?php if ($sw_exists): ?>
                                                <span class="label label-success">✓ EXISTE</span>
                                                <br><br>
                                                <strong>Chemin:</strong> <code><?php echo $sw_path; ?></code><br>
                                                <strong>Taille:</strong> <?php echo number_format($sw_size); ?> octets<br>
                                                <strong>Dernière modification:</strong> <?php echo $sw_modified; ?><br>
                                                <strong>Version:</strong>
                                                <?php if ($sw_has_v3): ?>
                                                    <span class="label label-success">v3 (Correcte)</span>
                                                <?php else: ?>
                                                    <span class="label label-warning">Ancienne version</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="label label-danger">✗ MANQUANT</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ($sw_exists): ?>
                                    <tr>
                                        <th>Aperçu (15 premières lignes)</th>
                                        <td>
                                            <pre style="background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow: auto;"><?php echo htmlspecialchars($sw_preview); ?></pre>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </table>

                                <?php if ($sw_exists): ?>
                                    <div class="alert alert-info">
                                        <strong>Test d'accès:</strong><br>
                                        <a href="<?php echo base_url('firebase-messaging-sw.js'); ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-external-link"></i> Ouvrir /firebase-messaging-sw.js
                                        </a>
                                        <a href="<?php echo base_url('firebase-messaging-sw.js?v=3'); ?>" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fa fa-external-link"></i> Ouvrir /firebase-messaging-sw.js?v=3
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 2. Firebase Configuration -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="tw-font-semibold">🔧 Configuration Firebase</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Paramètre</th>
                                            <th>Valeur</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $labels = [
                                            'push_enabled' => 'Notifications Push',
                                            'firebase_api_key' => 'API Key',
                                            'firebase_project_id' => 'Project ID',
                                            'firebase_messaging_sender_id' => 'Sender ID',
                                            'firebase_app_id' => 'App ID',
                                            'firebase_vapid_key' => 'VAPID Key',
                                            'firebase_use_v1_api' => 'API v1 (Moderne)',
                                            'firebase_service_account_json' => 'Service Account JSON'
                                        ];

                                        foreach ($firebase_config as $key => $config):
                                            $has_value = $config['has_value'];
                                            $value = $config['value'];

                                            // Mask sensitive values
                                            if ($has_value) {
                                                if (in_array($key, ['firebase_api_key', 'firebase_vapid_key'])) {
                                                    $display_value = substr($value, 0, 20) . '... (' . strlen($value) . ' chars)';
                                                } elseif ($key === 'firebase_service_account_json') {
                                                    $display_value = 'JSON configuré (' . strlen($value) . ' chars)';
                                                } else {
                                                    $display_value = htmlspecialchars($value);
                                                }
                                            } else {
                                                $display_value = '<em class="text-muted">Vide</em>';
                                            }
                                        ?>
                                        <tr>
                                            <td><strong><?php echo $labels[$key]; ?></strong></td>
                                            <td><code><?php echo $display_value; ?></code></td>
                                            <td>
                                                <?php if ($has_value): ?>
                                                    <span class="label label-success">✓ Configuré</span>
                                                <?php else: ?>
                                                    <span class="label label-danger">✗ Manquant</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 3. FCM Tokens -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="tw-font-semibold">📱 Tokens FCM Enregistrés</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Tokens Actifs</th>
                                        <td><strong class="text-primary" style="font-size: 24px;"><?php echo $total_tokens; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Patients avec Push</th>
                                        <td><strong class="text-success" style="font-size: 24px;"><?php echo $patients_with_tokens; ?></strong></td>
                                    </tr>
                                </table>

                                <?php if ($total_tokens > 0): ?>
                                    <h5 class="tw-font-semibold mtop20">Derniers Tokens Enregistrés</h5>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Token (début)</th>
                                                <th>Type</th>
                                                <th>Créé le</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($latest_tokens as $token): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($token['patient_name'] ?: 'N/A'); ?></td>
                                                <td><code><?php echo substr($token['token'], 0, 30); ?>...</code></td>
                                                <td><span class="label label-info"><?php echo htmlspecialchars($token['device_type']); ?></span></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($token['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>Aucun token FCM enregistré.</strong> Les patients doivent activer les notifications depuis le portail.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 4. Notification Logs -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="tw-font-semibold">📨 Historique Notifications Push</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Total Notifications Push</th>
                                        <td><strong style="font-size: 20px;"><?php echo $total_push; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Envoyées avec Succès</th>
                                        <td><strong class="text-success" style="font-size: 20px;"><?php echo $sent_push; ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Échouées</th>
                                        <td><strong class="text-danger" style="font-size: 20px;"><?php echo $failed_push; ?></strong></td>
                                    </tr>
                                </table>

                                <?php if ($total_push > 0): ?>
                                    <h5 class="tw-font-semibold mtop20">Dernières Notifications Push</h5>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Patient</th>
                                                <th>Type</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($latest_push as $log): ?>
                                            <tr>
                                                <td><?php echo date('d/m H:i', strtotime($log['sent_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($log['patient_name'] ?: 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($log['notification_type']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['message'], 0, 50)); ?>...</td>
                                                <td>
                                                    <?php if ($log['status'] === 'sent'): ?>
                                                        <span class="label label-success">✓ Envoyé</span>
                                                    <?php else: ?>
                                                        <span class="label label-danger">✗ Échec</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 5. Recommendations -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="tw-font-semibold">💡 Recommandations</h4>
                            </div>
                            <div class="panel-body">
                                <?php
                                $issues = [];

                                if (!$sw_exists) {
                                    $issues[] = "Le fichier Service Worker n'existe pas à la racine du site";
                                } elseif (!$sw_has_v3) {
                                    $issues[] = "Le Service Worker n'est pas à la version v3 (dernière version)";
                                }

                                if (empty($firebase_config['push_enabled']['has_value']) || $firebase_config['push_enabled']['value'] != '1') {
                                    $issues[] = "Les notifications push ne sont pas activées dans les paramètres";
                                }

                                if (!$firebase_config['firebase_vapid_key']['has_value']) {
                                    $issues[] = "La clé VAPID Firebase n'est pas configurée";
                                }

                                if (!$firebase_config['firebase_service_account_json']['has_value']) {
                                    $issues[] = "Le Service Account JSON Firebase n'est pas configuré";
                                }

                                if ($total_tokens == 0) {
                                    $issues[] = "Aucun patient n'a activé les notifications push";
                                }

                                if (empty($issues)):
                                ?>
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle"></i> <strong>Tout est configuré correctement !</strong>
                                    </div>
                                <?php else: ?>
                                    <ul class="list-unstyled">
                                        <?php foreach ($issues as $issue): ?>
                                            <li class="mtop10">
                                                <i class="fa fa-exclamation-triangle text-warning"></i>
                                                <?php echo $issue; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
