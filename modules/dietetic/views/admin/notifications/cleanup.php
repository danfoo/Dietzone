<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.cleanup-container {
    max-width: 900px;
    margin: 30px auto;
    padding: 0 20px;
}

.cleanup-header {
    text-align: center;
    margin-bottom: 40px;
}

.cleanup-header h1 {
    color: #01807B;
    font-size: 32px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.cleanup-header p {
    color: #718096;
    font-size: 16px;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.cleanup-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.cleanup-card-header {
    padding: 20px;
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.cleanup-card-header.success {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
}

.cleanup-card-header.danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.cleanup-card-header h3 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.cleanup-card-body {
    padding: 20px;
}

.status-grid {
    display: grid;
    gap: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: #f7fafc;
    border-radius: 8px;
    border-left: 4px solid #cbd5e0;
}

.status-item.found {
    background: #fff5f5;
    border-left-color: #e53e3e;
}

.status-item.missing {
    background: #f0fff4;
    border-left-color: #38a169;
}

.status-item-label {
    font-weight: 600;
    color: #2d3748;
    font-family: monospace;
}

.status-item-value {
    color: #718096;
    font-size: 14px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.danger {
    background: #fee;
    color: #c53030;
}

.status-badge.success {
    background: #d5f4e6;
    color: #0e6655;
}

.cleanup-actions {
    margin-top: 20px;
    text-align: center;
}

.btn-cleanup {
    padding: 14px 32px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-cleanup:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.btn-cleanup:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-settings {
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    border: 2px solid #01807B;
    background: white;
    color: #01807B;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    margin-left: 10px;
}

.btn-settings:hover {
    background: #01807B;
    color: white;
}

.alert-box {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.alert-box.info {
    background: #ebf8ff;
    border-left: 4px solid #3182ce;
    color: #2c5282;
}

.alert-box.warning {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    color: #92400e;
}

.alert-box.success {
    background: #d5f4e6;
    border-left: 4px solid #0e6655;
    color: #0e6655;
}

.alert-box.error {
    background: #fee;
    border-left: 4px solid #e53e3e;
    color: #742a2a;
}

#result-container {
    display: none;
}

.spinner {
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top: 3px solid white;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.result-details {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.result-details strong {
    color: #2d3748;
}

.instruction-list {
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
}

.instruction-list ol {
    margin: 10px 0;
    padding-left: 20px;
}

.instruction-list li {
    margin-bottom: 10px;
    color: #4a5568;
    line-height: 1.6;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #01807B;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.back-link:hover {
    color: #026660;
    gap: 12px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="cleanup-container">
            <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="back-link">
                ← Retour aux paramètres
            </a>

            <div class="cleanup-header">
                <h1>
                    🧹 Nettoyage Configuration LAM SMS
                </h1>
                <p>
                    Cette page vous permet de nettoyer les anciennes clés de configuration LAM SMS
                    et de résoudre l'erreur "Duplicate entry 'lam_api_url'"
                </p>
            </div>

            <?php if (!empty($old_keys_found)): ?>
                <div class="alert-box warning">
                    <div style="font-size: 24px;">⚠️</div>
                    <div>
                        <strong>Anciennes clés détectées !</strong><br>
                        <?php echo count($old_keys_found); ?> ancienne(s) clé(s) LAM SMS trouvée(s) dans la base de données.
                        Ces clés obsolètes empêchent l'enregistrement des nouvelles configurations.
                    </div>
                </div>

                <div class="cleanup-card">
                    <div class="cleanup-card-header danger">
                        <h3>🗑️ Anciennes clés à supprimer</h3>
                    </div>
                    <div class="cleanup-card-body">
                        <div class="status-grid">
                            <?php foreach ($old_keys_found as $key_data): ?>
                                <div class="status-item found">
                                    <div>
                                        <div class="status-item-label"><?php echo $key_data['key']; ?></div>
                                        <?php if (!empty($key_data['value'])): ?>
                                            <div class="status-item-value"><?php echo substr($key_data['value'], 0, 50); ?><?php echo strlen($key_data['value']) > 50 ? '...' : ''; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="status-badge danger">À supprimer</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert-box success">
                    <div style="font-size: 24px;">✅</div>
                    <div>
                        <strong>Aucune ancienne clé détectée</strong><br>
                        Votre configuration est à jour ! Aucun nettoyage n'est nécessaire.
                    </div>
                </div>
            <?php endif; ?>

            <div class="cleanup-card">
                <div class="cleanup-card-header <?php echo empty($old_keys_found) ? 'success' : ''; ?>">
                    <h3>✅ Nouvelles clés LAM SMS</h3>
                </div>
                <div class="cleanup-card-body">
                    <div class="status-grid">
                        <?php foreach ($new_keys_status as $key_data): ?>
                            <div class="status-item <?php echo $key_data['exists'] ? 'missing' : ''; ?>">
                                <div class="status-item-label"><?php echo $key_data['key']; ?></div>
                                <span class="status-badge <?php echo $key_data['exists'] ? 'success' : 'danger'; ?>">
                                    <?php echo $key_data['exists'] ? 'Existe' : 'Manquante'; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="cleanup-card">
                <div class="cleanup-card-header">
                    <h3>🔧 Action de nettoyage</h3>
                </div>
                <div class="cleanup-card-body">
                    <div class="instruction-list">
                        <strong>Ce qui va se passer :</strong>
                        <ol>
                            <li>Suppression des 4 anciennes clés LAM SMS (lam_api_url, lam_api_key, lam_api_sender, sms_lam_api_key)</li>
                            <li>Création des nouvelles clés si elles n'existent pas (sms_lam_account_id, sms_lam_password, sms_lam_sender_id, sms_lam_ret_url, sms_lam_priority)</li>
                            <li>Vérification que le nettoyage s'est bien déroulé</li>
                        </ol>
                    </div>

                    <div id="result-container"></div>

                    <div class="cleanup-actions">
                        <?php if (!empty($old_keys_found)): ?>
                            <button type="button" class="btn-cleanup" id="btn-execute-cleanup">
                                <span id="cleanup-spinner" style="display: none;" class="spinner"></span>
                                <span id="cleanup-text">🧹 Nettoyer maintenant</span>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn-settings">
                                ⚙️ Aller aux paramètres
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCleanup = document.getElementById('btn-execute-cleanup');
    const resultContainer = document.getElementById('result-container');
    const cleanupSpinner = document.getElementById('cleanup-spinner');
    const cleanupText = document.getElementById('cleanup-text');

    if (btnCleanup) {
        btnCleanup.addEventListener('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir nettoyer les anciennes clés LAM SMS ? Cette action est irréversible.')) {
                return;
            }

            // Show loading state
            btnCleanup.disabled = true;
            cleanupSpinner.style.display = 'block';
            cleanupText.textContent = 'Nettoyage en cours...';
            resultContainer.style.display = 'none';

            // Execute cleanup
            fetch('<?php echo admin_url('dietetic/notifications/execute_cleanup'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading state
                btnCleanup.disabled = false;
                cleanupSpinner.style.display = 'none';
                cleanupText.textContent = '🧹 Nettoyer maintenant';

                // Show result
                resultContainer.style.display = 'block';

                if (data.success) {
                    resultContainer.innerHTML = `
                        <div class="alert-box success">
                            <div style="font-size: 24px;">✅</div>
                            <div>
                                <strong>${data.message}</strong>
                                <div class="result-details">
                                    <div><strong>Anciennes clés supprimées :</strong> ${data.deleted_count}</div>
                                    <div><strong>Nouvelles clés créées :</strong> ${data.created_count}</div>
                                    <div><strong>Anciennes clés restantes :</strong> ${data.remaining_old_keys}</div>
                                </div>
                                <div style="margin-top: 15px;">
                                    <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn-settings">
                                        ⚙️ Configurer LAM SMS maintenant
                                    </a>
                                    <button onclick="window.location.reload()" class="btn-settings" style="margin-left: 10px;">
                                        🔄 Rafraîchir la page
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    // Reload page after 3 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    resultContainer.innerHTML = `
                        <div class="alert-box error">
                            <div style="font-size: 24px;">❌</div>
                            <div>
                                <strong>Erreur lors du nettoyage</strong><br>
                                ${data.message}
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                // Hide loading state
                btnCleanup.disabled = false;
                cleanupSpinner.style.display = 'none';
                cleanupText.textContent = '🧹 Nettoyer maintenant';

                // Show error
                resultContainer.style.display = 'block';
                resultContainer.innerHTML = `
                    <div class="alert-box error">
                        <div style="font-size: 24px;">❌</div>
                        <div>
                            <strong>Erreur réseau</strong><br>
                            ${error.message}
                        </div>
                    </div>
                `;
            });
        });
    }
});
</script>

<?php init_tail(); ?>
