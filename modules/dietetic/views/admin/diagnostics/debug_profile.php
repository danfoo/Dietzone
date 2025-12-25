<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="no-margin">
                                    <i class="fa fa-bug"></i> <?php echo $title; ?>
                                </h4>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo admin_url('dietetic/my_profile'); ?>" class="btn btn-primary">
                                    <i class="fa fa-user-circle"></i> Essayer Mon Profil
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Cette page teste toutes les composantes de "Mon Profil" pour identifier l'erreur.
                        </div>

                        <div class="diagnostic-results">
                            <?php foreach ($results as $index => $result): ?>
                                <div class="diagnostic-test-item" style="margin-bottom: 15px; padding: 15px; border-left: 4px solid <?php
                                    echo $result['status'] === 'success' ? '#27ae60' : ($result['status'] === 'error' ? '#e74c3c' : '#f39c12');
                                ?>; background: <?php
                                    echo $result['status'] === 'success' ? '#d5f4e6' : ($result['status'] === 'error' ? '#fadbd8' : '#fff3cd');
                                ?>;">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <strong style="font-size: 16px;">
                                                <?php if ($result['status'] === 'success'): ?>
                                                    <i class="fa fa-check-circle" style="color: #27ae60;"></i>
                                                <?php elseif ($result['status'] === 'error'): ?>
                                                    <i class="fa fa-times-circle" style="color: #e74c3c;"></i>
                                                <?php elseif ($result['status'] === 'warning'): ?>
                                                    <i class="fa fa-exclamation-triangle" style="color: #f39c12;"></i>
                                                <?php else: ?>
                                                    <i class="fa fa-spinner fa-spin" style="color: #3498db;"></i>
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($result['test']); ?>
                                            </strong>
                                            <span class="label label-<?php
                                                echo $result['status'] === 'success' ? 'success' : ($result['status'] === 'error' ? 'danger' : 'warning');
                                            ?>" style="margin-left: 10px;">
                                                <?php echo strtoupper($result['status']); ?>
                                            </span>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-xs btn-default" onclick="toggleDetails('test-<?php echo $index; ?>')">
                                                <i class="fa fa-eye"></i> Détails
                                            </button>
                                        </div>
                                    </div>

                                    <?php if (isset($result['message'])): ?>
                                        <div style="margin-top: 10px; color: #555;">
                                            <i class="fa fa-info"></i> <?php echo htmlspecialchars($result['message']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div id="test-<?php echo $index; ?>" style="display: none; margin-top: 15px; padding: 10px; background: white; border-radius: 4px;">
                                        <?php if (isset($result['data'])): ?>
                                            <h5><i class="fa fa-database"></i> Données retournées:</h5>
                                            <pre style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 4px;"><?php echo json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                                        <?php endif; ?>

                                        <?php if (isset($result['path'])): ?>
                                            <h5><i class="fa fa-folder"></i> Chemin du fichier:</h5>
                                            <code><?php echo htmlspecialchars($result['path']); ?></code>
                                        <?php endif; ?>

                                        <?php if (isset($result['trace'])): ?>
                                            <h5><i class="fa fa-code"></i> Stack Trace:</h5>
                                            <pre style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 11px;"><?php echo htmlspecialchars($result['trace']); ?></pre>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="alert alert-success" style="margin-top: 20px;">
                            <h4><i class="fa fa-lightbulb-o"></i> Résumé</h4>
                            <p>
                                <strong>Tests réussis:</strong>
                                <?php echo count(array_filter($results, function($r) { return $r['status'] === 'success'; })); ?><br>
                                <strong>Tests en erreur:</strong>
                                <?php echo count(array_filter($results, function($r) { return $r['status'] === 'error'; })); ?><br>
                                <strong>Avertissements:</strong>
                                <?php echo count(array_filter($results, function($r) { return $r['status'] === 'warning'; })); ?>
                            </p>
                        </div>

                        <div class="mtop20">
                            <a href="<?php echo admin_url('dietetic/dashboard'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour au Dashboard
                            </a>
                            <a href="<?php echo admin_url('dietetic/debug_profile'); ?>" class="btn btn-info">
                                <i class="fa fa-refresh"></i> Re-tester
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDetails(elementId) {
    var element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}
</script>

<style>
.diagnostic-test-item {
    transition: all 0.3s ease;
}

.diagnostic-test-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

pre {
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>

<?php init_tail(); ?>
