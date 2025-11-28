<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter toutes les erreurs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php init_head(); ?>

<style>
/* Modern Consultations Design */
:root {
    --primary-color: #01807B;
    --secondary-color: #F3911D;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
}

.consultations-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.2);
}

.consultations-header h4 {
    margin: 0 0 10px 0;
    font-size: 24px;
    font-weight: 600;
}

.consultations-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.btn-new-consultation {
    background: white;
    color: var(--primary-color);
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-new-consultation:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: var(--primary-color);
}

/* Filter Tabs */
.filter-tabs {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filter-tabs .nav-tabs {
    border-bottom: none;
    display: flex;
    gap: 10px;
}

.filter-tabs .nav-tabs > li {
    margin-bottom: 0;
    flex: 1;
}

.filter-tabs .nav-tabs > li > a {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
    padding: 12px 15px;
    transition: all 0.3s;
}

.filter-tabs .nav-tabs > li > a:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.filter-tabs .nav-tabs > li.active > a {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.filter-tabs .nav-tabs > li.active > a:hover {
    background: #016663;
    border-color: #016663;
}

.filter-tabs .badge {
    background: rgba(255,255,255,0.3);
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 11px;
    margin-left: 5px;
}

.filter-tabs .nav-tabs > li.active .badge {
    background: rgba(255,255,255,0.25);
}

/* Table Styling */
.consultations-table-wrapper {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

#consultations-table {
    margin-bottom: 0;
}

#consultations-table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid var(--primary-color);
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    padding: 15px 10px;
}

#consultations-table tbody tr {
    transition: all 0.2s;
}

#consultations-table tbody tr:hover {
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

.status-scheduled { background: #e3f2fd; color: #1976d2; }
.status-completed { background: #e8f5e9; color: #388e3c; }
.status-cancelled { background: #ffebee; color: #d32f2f; }
.status-no-show { background: #fff3e0; color: #f57c00; }

/* Type Badges */
.type-badge {
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
    background: #e9ecef;
    color: #495057;
}

/* Mode Icons */
.mode-online { color: #17a2b8; font-weight: 500; }
.mode-onsite { color: #28a745; font-weight: 500; }

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

/* Patient Link */
.patient-link {
    color: var(--primary-color);
    font-weight: 500;
    text-decoration: none;
}

.patient-link:hover {
    color: #016663;
    text-decoration: underline;
}

/* DataTables Custom Styling */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_paginate {
    margin-top: 15px;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- Header avec gradient -->
                <div class="consultations-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>
                                <i class="fa fa-stethoscope"></i>
                                Gestion des Consultations
                            </h4>
                            <p>Gérez et suivez toutes vos consultations diététiques</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?php echo admin_url('dietetic/availability'); ?>"
                               class="btn btn-info"
                               style="margin-right: 10px;"
                               title="Gérer les horaires de disponibilité">
                                <i class="fa fa-calendar"></i>
                                Disponibilités
                            </a>
                            <a href="<?php echo admin_url('dietetic/consultations/create'); ?>"
                               class="btn btn-new-consultation">
                                <i class="fa fa-plus-circle"></i>
                                Nouvelle Consultation
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <ul class="nav nav-tabs">
                        <li class="<?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=all'); ?>">
                                <i class="fa fa-list"></i> Toutes
                                <span class="badge"><?php echo count($consultations); ?></span>
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'scheduled') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=scheduled'); ?>">
                                <i class="fa fa-clock-o"></i> Programmées
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=completed'); ?>">
                                <i class="fa fa-check-circle"></i> Complétées
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=cancelled'); ?>">
                                <i class="fa fa-times-circle"></i> Annulées
                            </a>
                        </li>
                        <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'no_show') ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/consultations?status=no_show'); ?>">
                                <i class="fa fa-user-times"></i> Absences
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Barre de recherche -->
                <div class="consultations-table-wrapper">
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text"
                                       class="form-control"
                                       id="search-consultations"
                                       placeholder="Rechercher par patient, date, type, statut...">
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="results-count" class="text-muted"></span>
                        </div>
                    </div>

                    <table class="table table-hover" id="consultations-table">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Patient</th>
                                <th width="12%">Date & Heure</th>
                                <th width="15%">Type</th>
                                <th width="12%">Mode</th>
                                <th width="12%">Statut</th>
                                <th width="14%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($consultations)): ?>
                                <?php foreach ($consultations as $consultation): ?>
                                    <tr>
                                        <td><strong>#<?php echo $consultation->id; ?></strong></td>
                                        <td>
                                            <?php if (isset($consultation->client_name) && !empty($consultation->client_name)): ?>
                                                <a href="<?php echo admin_url('dietetic/patients/view/' . $consultation->patient_id); ?>"
                                                   class="patient-link">
                                                    <i class="fa fa-user-circle"></i>
                                                    <?php echo $consultation->client_name; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fa fa-calendar text-muted"></i>
                                            <?php echo date('d/m/Y', strtotime($consultation->consultation_date)); ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fa fa-clock-o"></i>
                                                <?php echo $consultation->consultation_time; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="type-badge">
                                                <?php echo dietetic_consultation_type_label($consultation->consultation_type); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($consultation->consultation_mode == 'online'): ?>
                                                <span class="mode-online">
                                                    <i class="fa fa-video-camera"></i> En ligne
                                                </span>
                                            <?php else: ?>
                                                <span class="mode-onsite">
                                                    <i class="fa fa-map-marker"></i> Présentiel
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = 'status-scheduled';
                                            $status_icon = 'fa-clock-o';
                                            $status_label = 'Programmée';

                                            switch ($consultation->status) {
                                                case 'scheduled':
                                                    $status_class = 'status-scheduled';
                                                    $status_icon = 'fa-clock-o';
                                                    $status_label = 'Programmée';
                                                    break;
                                                case 'completed':
                                                    $status_class = 'status-completed';
                                                    $status_icon = 'fa-check-circle';
                                                    $status_label = 'Complétée';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'status-cancelled';
                                                    $status_icon = 'fa-times-circle';
                                                    $status_label = 'Annulée';
                                                    break;
                                                case 'no_show':
                                                    $status_class = 'status-no-show';
                                                    $status_icon = 'fa-user-times';
                                                    $status_label = 'Absence';
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
                                                <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>"
                                                   class="btn btn-sm btn-info btn-action"
                                                   title="Voir les détails">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>"
                                                   class="btn btn-sm btn-primary btn-action"
                                                   title="Modifier">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger btn-action btn-delete-consultation"
                                                        data-id="<?php echo $consultation->id; ?>"
                                                        data-patient="<?php echo htmlspecialchars($consultation->client_name); ?>"
                                                        title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 40px;">
                                        <i class="fa fa-inbox fa-3x text-muted" style="opacity: 0.3;"></i>
                                        <p class="text-muted" style="margin-top: 15px;">Aucune consultation trouvée</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Affichage de <?php echo count($consultations); ?> sur <?php echo $total_consultations; ?> consultations
                                    (Page <?php echo $current_page; ?> sur <?php echo $total_pages; ?>)
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Pagination des consultations">
                                    <ul class="pagination pull-right" style="margin: 0;">
                                        <!-- Bouton Précédent -->
                                        <li class="<?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                            <?php if ($current_page > 1): ?>
                                                <a href="<?php echo admin_url('dietetic/consultations?status=' . ($status_filter ?: 'all') . '&page=' . ($current_page - 1)); ?>"
                                                   aria-label="Précédent">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            <?php else: ?>
                                                <span>&laquo;</span>
                                            <?php endif; ?>
                                        </li>

                                        <!-- Numéros de page -->
                                        <?php
                                        $range = 2; // Nombre de pages à afficher de chaque côté
                                        $start_page = max(1, $current_page - $range);
                                        $end_page = min($total_pages, $current_page + $range);

                                        // Première page
                                        if ($start_page > 1): ?>
                                            <li>
                                                <a href="<?php echo admin_url('dietetic/consultations?status=' . ($status_filter ?: 'all') . '&page=1'); ?>">1</a>
                                            </li>
                                            <?php if ($start_page > 2): ?>
                                                <li class="disabled"><span>...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Pages du milieu -->
                                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                <a href="<?php echo admin_url('dietetic/consultations?status=' . ($status_filter ?: 'all') . '&page=' . $i); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Dernière page -->
                                        <?php if ($end_page < $total_pages): ?>
                                            <?php if ($end_page < $total_pages - 1): ?>
                                                <li class="disabled"><span>...</span></li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="<?php echo admin_url('dietetic/consultations?status=' . ($status_filter ?: 'all') . '&page=' . $total_pages); ?>">
                                                    <?php echo $total_pages; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Bouton Suivant -->
                                        <li class="<?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                            <?php if ($current_page < $total_pages): ?>
                                                <a href="<?php echo admin_url('dietetic/consultations?status=' . ($status_filter ?: 'all') . '&page=' . ($current_page + 1)); ?>"
                                                   aria-label="Suivant">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            <?php else: ?>
                                                <span>&raquo;</span>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulaire de suppression caché avec CSRF -->
                <form id="delete-consultation-form" method="POST" action="" style="display: none;">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    <input type="hidden" name="consultation_id" id="delete-consultation-id">
                    <input type="hidden" name="status_filter" value="<?php echo $status_filter ?: 'all'; ?>">
                    <input type="hidden" name="current_page" value="<?php echo $current_page; ?>">
                </form>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fonctionnalité de recherche simple
    var $searchInput = $('#search-consultations');
    var $table = $('#consultations-table');
    var $rows = $table.find('tbody tr');
    var $resultsCount = $('#results-count');

    // Fonction pour mettre à jour le compteur
    function updateResultsCount() {
        var visibleRows = $rows.filter(':visible').length;
        var totalRows = $rows.length;

        if ($searchInput.val().trim() === '') {
            $resultsCount.text(totalRows + ' consultation' + (totalRows > 1 ? 's' : ''));
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

    // ============================================
    // Suppression de consultation avec confirmation
    // ============================================
    $('.btn-delete-consultation').on('click', function() {
        var consultationId = $(this).data('id');
        var patientName = $(this).data('patient');

        // Confirmation de suppression
        if (confirm('Êtes-vous sûr de vouloir supprimer la consultation pour ' + patientName + ' ?\n\nCette action est irréversible.')) {
            // Remplir le formulaire caché
            $('#delete-consultation-id').val(consultationId);
            $('#delete-consultation-form').attr('action', '<?php echo admin_url("dietetic/consultations/delete/"); ?>' + consultationId);

            // Soumettre le formulaire
            $('#delete-consultation-form').submit();
        }
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
});
</script>

<?php init_tail(); ?>
