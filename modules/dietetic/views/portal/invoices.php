<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('portal/includes/portal_header'); ?>

<div class="portal-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header-modern">
            <div class="header-content">
                <div class="header-icon" style="background: linear-gradient(135deg, #01807B 0%, #01655f 100%);">
                    <i class="fa fa-file-text"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title">Mes Factures</h1>
                    <p class="page-subtitle">Consultez et téléchargez vos factures</p>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card-modern">
            <div class="card-body">
                <?php if (empty($invoices)): ?>
                    <!-- Empty State -->
                    <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        <h3 style="color: #666; font-size: 20px; margin-bottom: 10px;">Aucune facture</h3>
                        <p style="color: #999; font-size: 14px;">
                            Vous n'avez pas encore de factures.
                        </p>
                    </div>
                <?php else: ?>
                    <!-- Invoices Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 2px solid #01807B;">
                                <tr>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-hashtag" style="color: #01807B;"></i> Numéro
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-calendar" style="color: #01807B;"></i> Date
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-calendar-check-o" style="color: #01807B;"></i> Échéance
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-money" style="color: #01807B;"></i> Montant
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-credit-card" style="color: #01807B;"></i> Payé
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50;">
                                        <i class="fa fa-info-circle" style="color: #01807B;"></i> Statut
                                    </th>
                                    <th style="padding: 15px; font-weight: 700; color: #2c3e50; text-align: center;">
                                        <i class="fa fa-cog" style="color: #01807B;"></i> Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                    <?php
                                    // Determine status
                                    $status_class = '';
                                    $status_text = '';
                                    $status_icon = '';

                                    if ($invoice->status == 1) {
                                        $status_class = 'label-danger';
                                        $status_text = 'Impayée';
                                        $status_icon = 'fa-exclamation-circle';
                                    } elseif ($invoice->status == 2) {
                                        $status_class = 'label-success';
                                        $status_text = 'Payée';
                                        $status_icon = 'fa-check-circle';
                                    } elseif ($invoice->status == 3) {
                                        $status_class = 'label-warning';
                                        $status_text = 'Partiellement payée';
                                        $status_icon = 'fa-adjust';
                                    } elseif ($invoice->status == 4) {
                                        $status_class = 'label-warning';
                                        $status_text = 'En retard';
                                        $status_icon = 'fa-clock-o';
                                    } elseif ($invoice->status == 5) {
                                        $status_class = 'label-default';
                                        $status_text = 'Annulée';
                                        $status_icon = 'fa-ban';
                                    } elseif ($invoice->status == 6) {
                                        $status_class = 'label-info';
                                        $status_text = 'Brouillon';
                                        $status_icon = 'fa-file-o';
                                    }

                                    // Calculate amounts
                                    $total = $invoice->total;
                                    $total_paid = 0;

                                    // Get total paid amount
                                    if (isset($invoice->payments) && is_array($invoice->payments)) {
                                        foreach ($invoice->payments as $payment) {
                                            $total_paid += $payment->amount;
                                        }
                                    }

                                    $balance = $total - $total_paid;
                                    ?>
                                    <tr style="transition: all 0.3s ease;">
                                        <td data-label="Numéro" style="padding: 15px; vertical-align: middle;">
                                            <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>"
                                               style="color: #01807B; font-weight: 600; text-decoration: none;">
                                                <?php
                                                // Format invoice number
                                                if (function_exists('format_invoice_number')) {
                                                    echo '#' . format_invoice_number($invoice->id);
                                                } else {
                                                    echo '#' . str_pad($invoice->number, 6, '0', STR_PAD_LEFT);
                                                }
                                                ?>
                                            </a>
                                        </td>
                                        <td data-label="Date" style="padding: 15px; vertical-align: middle;">
                                            <?php
                                            // Format date
                                            if (function_exists('_d')) {
                                                echo _d($invoice->date);
                                            } else {
                                                echo date('d/m/Y', strtotime($invoice->date));
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Échéance" style="padding: 15px; vertical-align: middle;">
                                            <?php
                                            // Format due date
                                            if (function_exists('_d')) {
                                                echo _d($invoice->duedate);
                                            } else {
                                                echo date('d/m/Y', strtotime($invoice->duedate));
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Montant" style="padding: 15px; vertical-align: middle; font-weight: 600;">
                                            <?php
                                            // Format money
                                            if (function_exists('app_format_money')) {
                                                echo app_format_money($total, $invoice->currency_name);
                                            } else {
                                                $currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
                                                echo number_format($total, 0, ',', ' ') . ' ' . $currency;
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Payé" style="padding: 15px; vertical-align: middle; color: #43a047;">
                                            <?php
                                            // Format money paid
                                            if (function_exists('app_format_money')) {
                                                echo app_format_money($total_paid, $invoice->currency_name);
                                            } else {
                                                $currency = isset($invoice->currency_name) ? $invoice->currency_name : 'XOF';
                                                echo number_format($total_paid, 0, ',', ' ') . ' ' . $currency;
                                            }
                                            ?>
                                        </td>
                                        <td data-label="Statut" style="padding: 15px; vertical-align: middle;">
                                            <span class="label <?php echo $status_class; ?>"
                                                  style="padding: 6px 12px; font-size: 12px; border-radius: 20px;">
                                                <i class="fa <?php echo $status_icon; ?>"></i> <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td data-label="Actions" style="padding: 15px; vertical-align: middle; text-align: center;">
                                            <a href="<?php echo site_url('dietetic/portal/invoice/' . $invoice->id); ?>"
                                               class="btn btn-sm btn-primary"
                                               style="background: linear-gradient(135deg, #01807B 0%, #01655f 100%); border: none; padding: 6px 12px; border-radius: 6px; margin: 3px;">
                                                <i class="fa fa-eye"></i> Voir
                                            </a>
                                            <a href="<?php echo site_url('invoice/' . $invoice->id . '/' . $invoice->hash . '/pdf'); ?>"
                                               class="btn btn-sm btn-default"
                                               target="_blank"
                                               style="padding: 6px 12px; border-radius: 6px; margin: 3px;">
                                                <i class="fa fa-download"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 4px solid #01807B;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                            <div>
                                <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Total des factures</div>
                                <div style="font-size: 24px; font-weight: 700; color: #2c3e50;">
                                    <?php echo count($invoices); ?> facture<?php echo count($invoices) > 1 ? 's' : ''; ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour au tableau de bord
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.portal-content {
    padding: 30px 0;
    min-height: calc(100vh - 200px);
}

.page-header-modern {
    margin-bottom: 30px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
    box-shadow: 0 4px 15px rgba(1, 128, 123, 0.3);
}

.header-text {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.page-subtitle {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

.card-modern {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-body {
    padding: 0;
}

.table > thead > tr > th,
.table > tbody > tr > td {
    border-top: 1px solid #ecf0f1;
}

.table > tbody > tr:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

@media (max-width: 768px) {
    .header-icon {
        width: 50px;
        height: 50px;
        font-size: 24px;
    }

    .page-title {
        font-size: 22px;
    }

    .page-subtitle {
        font-size: 13px;
    }

    .table-responsive {
        border: none;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #ecf0f1;
        border-radius: 8px;
        overflow: hidden;
    }

    .table tbody td {
        display: block;
        text-align: right;
        padding: 10px 15px !important;
        border: none;
    }

    .table tbody td:before {
        content: attr(data-label);
        float: left;
        font-weight: 600;
        color: #7f8c8d;
    }
}
</style>

<?php $this->load->view('portal/includes/portal_footer'); ?>
