<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'consultations';
$page_title = 'Mes Consultations';
$this->load->view('portal/includes/portal_header');
?>

<style>
/* Page-specific styles for consultations */
.page-header-mobile {
    margin-bottom: 25px;
}

.page-header-mobile h1 {
    font-size: 26px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-mobile h1 i {
    color: #01807B;
}

.page-header-mobile p {
    color: #6c757d;
    font-size: 15px;
    margin: 0;
}

.consultations-table {
    background: white;
    border-radius: 16px;
    padding: 24px 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.empty-state {
    background: white;
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-state i {
    font-size: 80px;
    color: #01807B;
    opacity: 0.2;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 22px;
    font-weight: 700;
    color: #212529;
    margin: 0 0 12px 0;
}

.empty-state p {
    color: #6c757d;
    font-size: 15px;
    line-height: 1.6;
    margin: 0;
}

.btn-back {
    background: white;
    color: #495057;
    border: 2px solid #dee2e6;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 48px;
}

.btn-back:hover {
    background: #f8f9fa;
    border-color: #01807B;
    color: #01807B;
    text-decoration: none;
}
</style>

<!-- Page Header -->
<div class="page-header-mobile">
    <h1><i class="fa fa-calendar"></i> Mes Consultations</h1>
    <p>Historique de vos rendez-vous</p>
</div>

<?php if (!empty($consultations)) { ?>
    <div class="consultations-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo _l('dietetic_date'); ?></th>
                    <th><?php echo _l('dietetic_type'); ?></th>
                    <th><?php echo _l('dietetic_status'); ?></th>
                    <th><?php echo _l('dietetic_duration'); ?></th>
                    <th><?php echo _l('dietetic_location'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultations as $consultation) { ?>
                    <tr>
                        <td><?php echo _dt($consultation->consultation_date); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                        <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                        <td><?php echo $consultation->duration; ?> min</td>
                        <td><?php echo $consultation->location ?? '-'; ?></td>
                    </tr>
                    <?php if ($consultation->notes) { ?>
                        <tr>
                            <td colspan="5">
                                <small class="text-muted">
                                    <strong><?php echo _l('dietetic_notes'); ?>:</strong> <?php echo nl2br($consultation->notes); ?>
                                </small>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <div class="empty-state">
        <i class="fa fa-calendar"></i>
        <h3>Aucune consultation</h3>
        <p>Vous n'avez pas encore de consultations enregistrées.</p>
    </div>
<?php } ?>

<div style="margin-top: 30px; text-align: center;">
    <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
        <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
    </a>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
