<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.migrations-container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.migrations-header {
    text-align: center;
    margin-bottom: 40px;
}

.migrations-header h1 {
    color: #01807B;
    font-size: 32px;
    margin-bottom: 10px;
}

.migrations-header p {
    color: #718096;
    font-size: 16px;
}

.migrations-grid {
    display: grid;
    gap: 20px;
    margin-bottom: 30px;
}

.migration-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.migration-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.migration-card-header {
    padding: 20px;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.migration-card-header h3 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.migration-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.migration-status.pending {
    background: #fef5e7;
    color: #d68910;
}

.migration-status.completed {
    background: #d5f4e6;
    color: #0e6655;
}

.migration-status.error {
    background: #fadbd8;
    color: #943126;
}

.migration-card-body {
    padding: 20px;
}

.migration-description {
    color: #4a5568;
    margin-bottom: 15px;
    line-height: 1.6;
}

.migration-tables {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.migration-tables h4 {
    color: #2d3748;
    font-size: 14px;
    margin: 0 0 10px 0;
}

.migration-tables ul {
    margin: 0;
    padding-left: 20px;
}

.migration-tables li {
    color: #718096;
    font-size: 13px;
    margin-bottom: 4px;
}

.migration-tables code {
    background: #e2e8f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
}

.migration-card-footer {
    padding: 15px 20px;
    background: #f7fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.migration-meta {
    color: #718096;
    font-size: 13px;
}

.migration-actions {
    display: flex;
    gap: 10px;
}

.btn-migrate {
    padding: 8px 20px;
    background: #01807B;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-migrate:hover {
    background: #026660;
}

.btn-migrate:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
}

.btn-check {
    padding: 8px 20px;
    background: #718096;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

.btn-check:hover {
    background: #4a5568;
}

.global-actions {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.global-actions h3 {
    margin: 0;
    color: #2d3748;
    font-size: 18px;
}

.global-buttons {
    display: flex;
    gap: 10px;
}

.btn-primary-large {
    padding: 12px 30px;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(1, 128, 123, 0.3);
}

.btn-primary-large:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary-large {
    padding: 12px 30px;
    background: white;
    color: #01807B;
    border: 2px solid #01807B;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
}

.btn-secondary-large:hover {
    background: #f7fafc;
}

.alert-box {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.alert-box.success {
    background: #d5f4e6;
    border-left: 4px solid #0e6655;
    color: #0e6655;
}

.alert-box.error {
    background: #fadbd8;
    border-left: 4px solid #943126;
    color: #943126;
}

.alert-box.info {
    background: #d6eaf8;
    border-left: 4px solid #1f618d;
    color: #1f618d;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 10px;
    display: none;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #01807B 0%, #026660 100%);
    width: 0%;
    transition: width 0.3s ease;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="migrations-container">
            <!-- Header -->
            <div class="migrations-header">
                <h1>
                    <i class="fa fa-database"></i>
                    Migrations Système de Notifications
                </h1>
                <p>Installez et gérez les migrations SQL pour le système de notifications complet</p>
            </div>

            <!-- Alert Box -->
            <div id="alertBox" class="alert-box"></div>

            <!-- Global Actions -->
            <div class="global-actions">
                <h3>Actions groupées</h3>
                <div class="global-buttons">
                    <button id="checkAllBtn" class="btn-secondary-large" onclick="checkAllMigrations()">
                        <i class="fa fa-search"></i>
                        Vérifier tout
                    </button>
                    <button id="runAllBtn" class="btn-primary-large" onclick="runAllMigrations()">
                        <i class="fa fa-rocket"></i>
                        Tout installer
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-bar" id="progressBar">
                <div class="progress-bar-fill" id="progressBarFill"></div>
            </div>

            <!-- Migrations Grid -->
            <div class="migrations-grid">
                <!-- Migration 0: LAM SMS Config Update (CRITICAL FIX) -->
                <div class="migration-card" data-migration="lam_update" style="border: 2px solid #f39c12;">
                    <div class="migration-card-header" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                        <h3>
                            <i class="fa fa-wrench"></i>
                            🔧 Mise à jour LAM SMS (IMPORTANT)
                        </h3>
                        <span class="migration-status pending" id="status-lam_update">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            <strong>Migration corrective nécessaire :</strong> Met à jour la configuration LAM SMS vers le nouveau format avec accountid + password.
                            Corrige l'erreur "Duplicate entry 'lam_api_url'" et remplace les anciens paramètres obsolètes.
                        </div>
                        <div class="migration-tables">
                            <h4>Actions :</h4>
                            <ul>
                                <li>🗑️ Suppression des anciens paramètres : <code>lam_api_url</code>, <code>lam_api_key</code>, <code>sms_lam_api_key</code></li>
                                <li>✅ Ajout des nouveaux paramètres : <code>sms_lam_account_id</code>, <code>sms_lam_password</code></li>
                                <li>✅ Ajout de : <code>sms_lam_ret_url</code>, <code>sms_lam_priority</code></li>
                                <li>✅ Mise à jour du sender_id par défaut : <code>API_LAMSMS</code></li>
                            </ul>
                        </div>
                        <div class="alert alert-warning" style="margin-top: 15px; background: #fff3cd; border-left: 4px solid #f39c12; padding: 10px;">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Important :</strong> Cette migration est requise si vous aviez déjà installé le système de notifications avant le 11 novembre 2025.
                            Elle ne supprime aucune donnée, seulement les clés de configuration obsolètes.
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> update_lam_sms_config.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('lam_update')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('lam_update')" style="background: #f39c12;">
                                <i class="fa fa-wrench"></i> Appliquer le correctif
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Migration 1: Base Notifications -->
                <div class="migration-card" data-migration="notifications">
                    <div class="migration-card-header">
                        <h3>
                            <i class="fa fa-bell"></i>
                            Système de Notifications de Base
                        </h3>
                        <span class="migration-status pending" id="status-notifications">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            Installation du système de notifications complet avec préférences patients, logs, jalons et paramètres SMS/WhatsApp.
                        </div>
                        <div class="migration-tables">
                            <h4>Tables créées :</h4>
                            <ul>
                                <li><code>tbldietic_notification_preferences</code> - Préférences par patient</li>
                                <li><code>tbldietic_notification_logs</code> - Historique notifications</li>
                                <li><code>tbldietic_milestones</code> - Jalons atteints</li>
                                <li><code>tbldietic_notification_settings</code> - Configuration globale</li>
                            </ul>
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> add_notifications_system.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('notifications')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('notifications')">
                                <i class="fa fa-play"></i> Installer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Migration 2: Firebase Push -->
                <div class="migration-card" data-migration="firebase">
                    <div class="migration-card-header">
                        <h3>
                            <i class="fa fa-mobile"></i>
                            Notifications Push Firebase
                        </h3>
                        <span class="migration-status pending" id="status-firebase">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            Ajout du support des notifications push via Firebase Cloud Messaging. Permet d'envoyer des notifications instantanées sur web et mobile.
                        </div>
                        <div class="migration-tables">
                            <h4>Ajouts :</h4>
                            <ul>
                                <li><code>tbldietic_fcm_tokens</code> - Tokens Firebase par appareil</li>
                                <li><code>channel_push</code> - Nouveau canal dans preferences</li>
                                <li><code>push</code> - Nouveau type dans notification_logs</li>
                                <li>Paramètres Firebase dans settings</li>
                            </ul>
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> add_firebase_push_notifications.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('firebase')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('firebase')">
                                <i class="fa fa-play"></i> Installer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Migration 3: Performance Optimizations -->
                <div class="migration-card" data-migration="optimizations">
                    <div class="migration-card-header">
                        <h3>
                            <i class="fa fa-tachometer"></i>
                            Optimisations Performance
                        </h3>
                        <span class="migration-status pending" id="status-optimizations">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            Ajout d'index composites pour améliorer les performances des requêtes fréquentes. Accélère les recherches et les statistiques.
                        </div>
                        <div class="migration-tables">
                            <h4>Index ajoutés :</h4>
                            <ul>
                                <li><code>idx_patient_created</code> - Logs par patient et date</li>
                                <li><code>idx_status_created</code> - Recherche par statut</li>
                                <li><code>idx_patient_active</code> - Tokens FCM actifs</li>
                                <li><code>idx_reminder_weight</code> - Rappels de pesée</li>
                                <li>+ 6 autres index de performance</li>
                            </ul>
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> optimize_notifications_performance.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('optimizations')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('optimizations')">
                                <i class="fa fa-play"></i> Installer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Migration 4: Staff Permissions System -->
                <div class="migration-card" data-migration="permissions">
                    <div class="migration-card-header">
                        <h3>
                            <i class="fa fa-shield"></i>
                            Système de Permissions Granulaires
                        </h3>
                        <span class="migration-status pending" id="status-permissions">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            Système de permissions granulaires permettant de contrôler l'accès de chaque diététicien aux différentes fonctionnalités (Enquêtes alimentaires, Gestion notifications, etc.)
                        </div>
                        <div class="migration-tables">
                            <h4>Ajouts :</h4>
                            <ul>
                                <li><code>tbldietic_staff_permissions</code> - Permissions par diététicien</li>
                                <li>Paramètres par défaut pour nouvelles permissions</li>
                                <li>Support permissions : food_surveys, notifications_manage, reports_advanced, settings_module</li>
                                <li>Permissions par défaut pour staff existants</li>
                            </ul>
                        </div>
                        <div class="alert alert-info" style="margin-top: 15px;">
                            <i class="fa fa-info-circle"></i>
                            <strong>Important :</strong> Après installation, gérez les permissions via <strong>Diététique > Permissions Diététiciens</strong>
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> add_staff_permissions.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('permissions')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('permissions')">
                                <i class="fa fa-play"></i> Installer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Migration 5: Firebase v1 API Support -->
                <div class="migration-card" data-migration="firebase_v1" style="border: 2px solid #4285F4;">
                    <div class="migration-card-header" style="background: linear-gradient(135deg, #4285F4 0%, #3367D6 100%);">
                        <h3>
                            <i class="fa fa-rocket"></i>
                            🆕 Firebase Cloud Messaging API v1
                        </h3>
                        <span class="migration-status pending" id="status-firebase_v1">En attente</span>
                    </div>
                    <div class="migration-card-body">
                        <div class="migration-description">
                            <strong>Migration vers l'API moderne Firebase v1 :</strong> Ajoute le support de l'API Firebase Cloud Messaging v1 avec authentification OAuth 2.0 via Service Account.
                            Remplace progressivement l'API Legacy qui sera désactivée par Google.
                        </div>
                        <div class="migration-tables">
                            <h4>Paramètres ajoutés :</h4>
                            <ul>
                                <li>✅ <code>firebase_use_v1_api</code> - Toggle pour activer l'API v1 (recommandé)</li>
                                <li>✅ <code>firebase_service_account_json</code> - Credentials Service Account pour OAuth 2.0</li>
                            </ul>
                        </div>
                        <div class="alert alert-success" style="margin-top: 15px; background: #d5f4e6; border-left: 4px solid #0e6655; padding: 10px;">
                            <i class="fa fa-check-circle"></i>
                            <strong>Recommandé :</strong> Cette migration est nécessaire pour profiter de l'API v1 moderne avec sécurité renforcée (OAuth 2.0),
                            meilleure gestion des erreurs et compatibilité future. L'API Legacy reste disponible pour la transition.
                        </div>
                        <div class="alert alert-info" style="margin-top: 10px; background: #e3f2fd; border-left: 4px solid #2196F3; padding: 10px;">
                            <i class="fa fa-info-circle"></i>
                            <strong>Après installation :</strong>
                            <ol style="margin: 10px 0 0 20px;">
                                <li>Allez dans <strong>Configuration → Firebase (Notifications Push)</strong></li>
                                <li>Sélectionnez <strong>"API v1 (Service Account)"</strong></li>
                                <li>Obtenez votre Service Account JSON depuis Firebase Console</li>
                                <li>Collez le contenu JSON dans le champ prévu</li>
                            </ol>
                        </div>
                    </div>
                    <div class="migration-card-footer">
                        <div class="migration-meta">
                            <i class="fa fa-file-code-o"></i> add_firebase_v1_api_support.sql
                        </div>
                        <div class="migration-actions">
                            <button class="btn-check" onclick="checkMigration('firebase_v1')">
                                <i class="fa fa-search"></i> Vérifier
                            </button>
                            <button class="btn-migrate" onclick="runMigration('firebase_v1')" style="background: #4285F4;">
                                <i class="fa fa-rocket"></i> Installer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i>
                Retour aux paramètres
            </a>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
// Migration configurations
const migrations = {
    lam_update: {
        name: 'Mise à jour LAM SMS',
        file: 'update_lam_sms_config.sql',
        settings: ['sms_lam_account_id', 'sms_lam_password', 'sms_lam_ret_url', 'sms_lam_priority']
    },
    notifications: {
        name: 'Système de Notifications',
        file: 'add_notifications_system.sql',
        tables: ['dietic_notification_preferences', 'dietic_notification_logs', 'dietic_milestones', 'dietic_notification_settings']
    },
    firebase: {
        name: 'Firebase Push',
        file: 'add_firebase_push_notifications.sql',
        tables: ['dietic_fcm_tokens']
    },
    optimizations: {
        name: 'Optimisations Performance',
        file: 'optimize_notifications_performance.sql',
        indexes: true
    },
    permissions: {
        name: 'Permissions Granulaires',
        file: 'add_staff_permissions.sql',
        tables: ['dietic_staff_permissions']
    },
    firebase_v1: {
        name: 'Firebase API v1',
        file: 'add_firebase_v1_api_support.sql',
        settings: ['firebase_use_v1_api', 'firebase_service_account_json']
    }
};

// Check single migration
function checkMigration(migrationId) {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Vérification...';

    $.ajax({
        url: admin_url + 'dietetic/notifications/check_migration',
        type: 'POST',
        data: { migration: migrationId },
        dataType: 'json',
        success: function(response) {
            updateMigrationStatus(migrationId, response.installed, response.message);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        },
        error: function() {
            alert_float('danger', 'Erreur lors de la vérification');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

// Check all migrations
function checkAllMigrations() {
    const btn = document.getElementById('checkAllBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Vérification...';

    let checked = 0;
    const total = Object.keys(migrations).length;

    Object.keys(migrations).forEach(migrationId => {
        $.ajax({
            url: admin_url + 'dietetic/notifications/check_migration',
            type: 'POST',
            data: { migration: migrationId },
            dataType: 'json',
            success: function(response) {
                updateMigrationStatus(migrationId, response.installed, response.message);
                checked++;

                if (checked === total) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-search"></i> Vérifier tout';
                    showAlert('info', 'Vérification terminée pour ' + total + ' migration(s)');
                }
            }
        });
    });
}

// Run single migration
function runMigration(migrationId) {
    const btn = event.target;
    const originalHtml = btn.innerHTML;

    if (!confirm('Voulez-vous vraiment exécuter cette migration ?')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Installation...';

    $.ajax({
        url: admin_url + 'dietetic/notifications/execute_migration',
        type: 'POST',
        data: { migration: migrationId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateMigrationStatus(migrationId, true, response.message);
                showAlert('success', response.message);
                btn.innerHTML = '<i class="fa fa-check"></i> Installé';
            } else {
                showAlert('error', response.message);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Erreur lors de l\'installation';
            showAlert('error', message);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
}

// Run all migrations
function runAllMigrations() {
    const btn = document.getElementById('runAllBtn');

    if (!confirm('Voulez-vous installer toutes les migrations manquantes ?')) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Installation...';

    const progressBar = document.getElementById('progressBar');
    const progressBarFill = document.getElementById('progressBarFill');
    progressBar.style.display = 'block';

    const migrationIds = Object.keys(migrations);
    let completed = 0;
    let errors = 0;

    function runNext(index) {
        if (index >= migrationIds.length) {
            // All done
            progressBar.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-rocket"></i> Tout installer';

            if (errors === 0) {
                showAlert('success', 'Toutes les migrations ont été installées avec succès !');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showAlert('error', errors + ' migration(s) ont échoué');
            }
            return;
        }

        const migrationId = migrationIds[index];
        const progress = ((index + 1) / migrationIds.length) * 100;
        progressBarFill.style.width = progress + '%';

        $.ajax({
            url: admin_url + 'dietetic/notifications/execute_migration',
            type: 'POST',
            data: { migration: migrationId },
            dataType: 'json',
            success: function(response) {
                if (response.success || response.already_exists) {
                    updateMigrationStatus(migrationId, true, response.message);
                } else {
                    errors++;
                    updateMigrationStatus(migrationId, false, 'Erreur');
                }
                runNext(index + 1);
            },
            error: function() {
                errors++;
                updateMigrationStatus(migrationId, false, 'Erreur');
                runNext(index + 1);
            }
        });
    }

    runNext(0);
}

// Update migration status
function updateMigrationStatus(migrationId, installed, message) {
    const statusEl = document.getElementById('status-' + migrationId);
    if (!statusEl) return;

    statusEl.className = 'migration-status ' + (installed ? 'completed' : 'pending');
    statusEl.textContent = installed ? 'Installé' : 'En attente';
}

// Show alert
function showAlert(type, message) {
    const alertBox = document.getElementById('alertBox');
    alertBox.className = 'alert-box ' + type;
    alertBox.innerHTML = '<i class="fa fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + '"></i> ' + message;
    alertBox.style.display = 'block';

    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

// Check all migrations on load
$(document).ready(function() {
    checkAllMigrations();
});
</script>
