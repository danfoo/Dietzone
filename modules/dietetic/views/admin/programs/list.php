<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter toutes les erreurs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php init_head(); ?>

<style>
/* Modern Programs Design */
:root {
    --primary-color: #01807B;
    --program-green: #27ae60;
    --program-green-dark: #229954;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
}

.programs-header {
    background: linear-gradient(135deg, var(--program-green) 0%, var(--program-green-dark) 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
}

.programs-header h4 {
    margin: 0 0 10px 0;
    font-size: 24px;
    font-weight: 600;
}

.programs-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.btn-new-program {
    background: white;
    color: var(--program-green);
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-new-program:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: var(--program-green);
}

/* Stats Cards */
.stats-row {
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stat-card .stat-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    margin: 5px 0;
}

.stat-card .stat-label {
    font-size: 13px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 500;
}

.stat-card.total { border-left: 4px solid #17a2b8; }
.stat-card.active { border-left: 4px solid #28a745; }
.stat-card.completed { border-left: 4px solid #ffc107; }
.stat-card.month { border-left: 4px solid #27ae60; }

/* Programs Table */
.programs-table-wrapper {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

#programs-table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid var(--program-green);
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    padding: 15px 10px;
}

#programs-table tbody tr {
    transition: all 0.2s;
}

#programs-table tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

/* Status Badges */
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.status-active { background: #e8f5e9; color: #388e3c; }
.status-completed { background: #fff3e0; color: #f57c00; }
.status-cancelled { background: #ffebee; color: #d32f2f; }

/* Patient Link */
.patient-link {
    color: var(--program-green);
    font-weight: 500;
    text-decoration: none;
}

.patient-link:hover {
    color: var(--program-green-dark);
    text-decoration: underline;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Header -->
                <div class="programs-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                <i class="fa fa-list-alt"></i>
                                Programmes Alimentaires
                            </h4>
                            <p>Gérez et suivez tous vos programmes alimentaires personnalisés</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?php echo admin_url('dietetic/programs/create'); ?>"
                               class="btn btn-new-program">
                                <i class="fa fa-plus-circle"></i>
                                Nouveau Programme
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-row row">
                    <div class="col-md-3">
                        <div class="stat-card total">
                            <div class="stat-icon text-info">
                                <i class="fa fa-list-alt"></i>
                            </div>
                            <div class="stat-value text-info"><?php echo $total_count; ?></div>
                            <div class="stat-label">Total Programmes</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card active">
                            <div class="stat-icon text-success">
                                <i class="fa fa-play-circle"></i>
                            </div>
                            <div class="stat-value text-success"><?php echo $active_count; ?></div>
                            <div class="stat-label">Programmes Actifs</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card completed">
                            <div class="stat-icon text-warning">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="stat-value text-warning"><?php echo $completed_count; ?></div>
                            <div class="stat-label">Programmes Terminés</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card month">
                            <div class="stat-icon" style="color: var(--program-green);">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <div class="stat-value" style="color: var(--program-green);"><?php echo $this_month_count; ?></div>
                            <div class="stat-label">Ce Mois-ci</div>
                        </div>
                    </div>
                </div>

                <!-- Programs Table -->
                <div class="programs-table-wrapper">
                    <!-- Search Bar -->
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text"
                                       class="form-control"
                                       id="search-programs"
                                       placeholder="Rechercher par patient, programme, statut...">
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="results-count" class="text-muted"></span>
                        </div>
                    </div>

                    <table class="table table-hover" id="programs-table">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Patient</th>
                                <th width="20%">Programme</th>
                                <th width="15%">Date Début</th>
                                <th width="15%">Date Fin</th>
                                <th width="12%">Statut</th>
                                <th width="8%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($programs)): ?>
                                <?php foreach ($programs as $program): ?>
                                    <tr>
                                        <td><strong>#<?php echo $program->id; ?></strong></td>
                                        <td>
                                            <?php if (isset($program->client_name) && !empty($program->client_name)): ?>
                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $program->patient_id); ?>"
                                                   class="patient-link">
                                                    <i class="fa fa-user-circle"></i>
                                                    <?php echo $program->client_name; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo $program->program_name; ?></strong>
                                            <?php if (!empty($program->description)): ?>
                                                <br><small class="text-muted"><?php echo substr($program->description, 0, 50); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fa fa-calendar text-muted"></i>
                                            <?php echo date('d/m/Y', strtotime($program->start_date)); ?>
                                        </td>
                                        <td>
                                            <?php if ($program->end_date): ?>
                                                <i class="fa fa-calendar text-muted"></i>
                                                <?php echo date('d/m/Y', strtotime($program->end_date)); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'status-active';
                                            $status_icon = 'fa-play-circle';
                                            $status_label = 'Actif';

                                            switch ($program->status) {
                                                case 'active':
                                                    $status_class = 'status-active';
                                                    $status_icon = 'fa-play-circle';
                                                    $status_label = 'Actif';
                                                    break;
                                                case 'completed':
                                                    $status_class = 'status-completed';
                                                    $status_icon = 'fa-check-circle';
                                                    $status_label = 'Terminé';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'status-cancelled';
                                                    $status_icon = 'fa-times-circle';
                                                    $status_label = 'Annulé';
                                                    break;
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <i class="fa <?php echo $status_icon; ?>"></i>
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>"
                                                   class="btn btn-sm btn-info btn-action"
                                                   title="Voir les détails">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?php echo admin_url('dietetic/programs/edit/' . $program->id); ?>"
                                                   class="btn btn-sm btn-primary btn-action"
                                                   title="Modifier">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if (is_admin() || dietetic_has_permission('delete')) { ?>
                                                    <a href="#"
                                                       class="btn btn-sm btn-danger btn-action btn-delete-program"
                                                       data-program-id="<?php echo $program->id; ?>"
                                                       data-program-name="<?php echo htmlspecialchars($program->program_name); ?>"
                                                       title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 40px;">
                                        <i class="fa fa-inbox fa-3x text-muted" style="opacity: 0.3;"></i>
                                        <p class="text-muted" style="margin-top: 15px;">Aucun programme trouvé</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fonctionnalité de recherche simple
    var $searchInput = $('#search-programs');
    var $table = $('#programs-table');
    var $rows = $table.find('tbody tr');
    var $resultsCount = $('#results-count');

    // Fonction pour mettre à jour le compteur
    function updateResultsCount() {
        var visibleRows = $rows.filter(':visible').length;
        var totalRows = $rows.length;

        if ($searchInput.val().trim() === '') {
            $resultsCount.text(totalRows + ' programme' + (totalRows > 1 ? 's' : ''));
        } else {
            $resultsCount.text(visibleRows + ' résultat' + (visibleRows > 1 ? 's' : '') + ' sur ' + totalRows);
        }
    }

    // Initialiser le compteur
    updateResultsCount();

    // Recherche en temps réel
    $searchInput.on('keyup', function() {
        var searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm === '') {
            $rows.show();
        } else {
            $rows.each(function() {
                var $row = $(this);
                var rowText = $row.text().toLowerCase();

                if (rowText.indexOf(searchTerm) > -1) {
                    $row.show();
                } else {
                    $row.hide();
                }
            });
        }

        updateResultsCount();
    });

    // Script simple pour le menu Diététique
    setTimeout(function() {
        $('#side-menu a[href*="dietetic"]').first().parent().find('> a').on('click', function(e) {
            var $submenu = $(this).next('ul');
            if ($submenu.length > 0) {
                e.preventDefault();
                $submenu.toggleClass('in');
                $(this).parent().toggleClass('active');
            }
        });
    }, 500);

    // ============================================
    // Suppression de programme avec confirmation
    // ============================================
    $('.btn-delete-program').on('click', function(e) {
        e.preventDefault();

        var programId = $(this).data('program-id');
        var programName = $(this).data('program-name');
        var $row = $(this).closest('tr');

        // Confirmation avant suppression
        if (confirm('⚠️ ATTENTION !\n\nÊtes-vous sûr de vouloir supprimer le programme :\n"' + programName + '" ?\n\n⚠️ Cette action supprimera :\n• Le programme et tous ses détails\n• Tous les plans de repas associés\n• Toutes les données liées\n\n❌ Cette action est IRRÉVERSIBLE !\n\nVoulez-vous vraiment continuer ?')) {

            // Afficher un indicateur de chargement
            var $btn = $(this);
            var originalHtml = $btn.html();
            $btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

            // Appel AJAX pour supprimer le programme
            $.ajax({
                url: '<?php echo admin_url("dietetic/programs/delete/"); ?>' + programId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Succès - animer la suppression de la ligne
                        $row.fadeOut(400, function() {
                            $(this).remove();

                            // Mettre à jour le compteur de résultats
                            updateResultsCount();

                            // Afficher un message de succès
                            alert_float('success', '✅ Programme "' + programName + '" supprimé avec succès');

                            // Recharger la page après 1 seconde
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        });
                    } else {
                        // Erreur
                        alert_float('danger', '❌ Erreur lors de la suppression : ' + (response.message || 'Erreur inconnue'));
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    // Erreur AJAX
                    alert_float('danger', '❌ Erreur de connexion : ' + error);
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        }
    });
});
</script>

<?php init_tail(); ?>
