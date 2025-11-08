<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.migration-container {
    max-width: 900px;
    margin: 50px auto;
    padding: 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.migration-header {
    text-align: center;
    margin-bottom: 30px;
}

.migration-header h1 {
    color: #01807B;
    font-size: 28px;
    margin-bottom: 10px;
}

.migration-header p {
    color: #718096;
    font-size: 16px;
}

.migration-info {
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    border-left: 4px solid #01807B;
}

.migration-info h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 15px;
}

.migration-info ul {
    margin: 0;
    padding-left: 20px;
}

.migration-info li {
    color: #4a5568;
    margin-bottom: 8px;
    line-height: 1.6;
}

.migration-info code {
    background: #e2e8f0;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    color: #2d3748;
}

.migration-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.feature-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    align-items: start;
    gap: 12px;
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
}

.feature-content h4 {
    margin: 0 0 5px 0;
    color: #2d3748;
    font-size: 15px;
}

.feature-content p {
    margin: 0;
    color: #718096;
    font-size: 13px;
    line-height: 1.4;
}

.migration-warning {
    background: #fff5f5;
    border-left: 4px solid #f56565;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
}

.migration-warning p {
    color: #c53030;
    margin: 0;
    font-weight: 600;
}

.migration-button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #01807B 0%, #026660 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.migration-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.migration-button:disabled {
    background: #cbd5e0;
    cursor: not-allowed;
    transform: none;
}

.migration-result {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    display: none;
}

.migration-result.success {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    color: #22543d;
}

.migration-result.error {
    background: #fff5f5;
    border: 1px solid #fc8181;
    color: #c53030;
}

.migration-result.info {
    background: #ebf8ff;
    border: 1px solid #90cdf4;
    color: #2c5282;
}

.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #01807B;
    text-decoration: none;
    font-weight: 600;
}

.back-link:hover {
    text-decoration: underline;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="migration-container">
            <div class="migration-header">
                <h1>
                    <i class="fa fa-bell"></i>
                    Migration Système de Notifications
                </h1>
                <p>Installation complète du système de rappels et notifications intelligents</p>
            </div>

            <div class="migration-info">
                <h3>Cette migration va créer :</h3>
                <ul>
                    <li><code>tbldietic_notification_preferences</code> - Préférences de notification par patient</li>
                    <li><code>tbldietic_notification_logs</code> - Historique des notifications envoyées</li>
                    <li><code>tbldietic_milestones</code> - Jalons et célébrations (5kg, 10kg, 15kg perdus)</li>
                    <li><code>tbldietic_notification_settings</code> - Configuration SMS LAM et WhatsApp</li>
                </ul>
            </div>

            <div class="migration-features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-scale"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Rappel Poids</h4>
                        <p>Rappel hebdomadaire personnalisé (jour et heure)</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-tint"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Rappel Eau</h4>
                        <p>3 rappels quotidiens pour s'hydrater</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-trophy"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Jalons</h4>
                        <p>Célébration automatique des objectifs atteints</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Multi-canal</h4>
                        <p>Email, SMS (LAM) et WhatsApp</p>
                    </div>
                </div>
            </div>

            <div class="migration-warning">
                <p>
                    <i class="fa fa-exclamation-triangle"></i>
                    Important : Cette opération modifie la structure de la base de données. Assurez-vous d'avoir une sauvegarde récente.
                </p>
            </div>

            <button id="runMigrationBtn" class="migration-button" onclick="runMigration()">
                <i class="fa fa-play"></i>
                Exécuter la migration
            </button>

            <div id="migrationResult" class="migration-result"></div>

            <a href="<?php echo admin_url('dietetic'); ?>" class="back-link">
                <i class="fa fa-arrow-left"></i>
                Retour au tableau de bord
            </a>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
function runMigration() {
    const btn = document.getElementById('runMigrationBtn');
    const result = document.getElementById('migrationResult');

    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Migration en cours...';

    // Hide previous result
    result.style.display = 'none';
    result.className = 'migration-result';

    // Execute migration
    $.ajax({
        url: admin_url + 'dietetic/notifications/execute_migration',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Migration response:', response); // Debug

            if (response.success) {
                result.className = 'migration-result success';
                result.innerHTML = '<i class="fa fa-check-circle"></i> ' + response.message;

                // Add details
                if (response.tables_created !== undefined && response.total_queries !== undefined) {
                    result.innerHTML += '<br><br><strong>Détails:</strong><ul style="margin: 10px 0 0 20px;">';
                    result.innerHTML += '<li>Tables créées: ' + response.tables_created + '</li>';
                    result.innerHTML += '<li>Requêtes SQL exécutées: ' + response.total_queries + '</li>';
                    result.innerHTML += '</ul>';
                }

                result.style.display = 'block';

                // Show success alert
                alert_float('success', response.message);

                // Redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = admin_url + 'dietetic/notifications/settings';
                }, 3000);
            } else {
                result.className = response.already_exists ? 'migration-result info' : 'migration-result error';
                result.innerHTML = '<i class="fa fa-' + (response.already_exists ? 'info-circle' : 'exclamation-circle') + '\"></i> ' + response.message;
                result.style.display = 'block';

                // Show errors if any
                if (response.errors && response.errors.length > 0) {
                    result.innerHTML += '<br><br><strong>Erreurs:</strong><ul style="margin: 10px 0 0 20px;">';
                    response.errors.forEach(function(error) {
                        result.innerHTML += '<li>' + error + '</li>';
                    });
                    result.innerHTML += '</ul>';
                }

                // Show details if available
                if (response.tables_created !== undefined) {
                    result.innerHTML += '<br><strong>Tables créées: ' + response.tables_created + '</strong>';
                }

                // Re-enable button if not already exists
                if (!response.already_exists) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-play"></i> Exécuter la migration';
                } else {
                    btn.innerHTML = '<i class="fa fa-check"></i> Migration déjà effectuée';
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);

            result.className = 'migration-result error';
            result.innerHTML = '<i class="fa fa-exclamation-circle"></i> Erreur lors de la migration. Consultez la console pour plus de détails.';
            result.style.display = 'block';

            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-play"></i> Exécuter la migration';
        }
    });
}
</script>
