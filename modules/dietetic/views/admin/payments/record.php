<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-money"></i> Enregistrer un Paiement
                        </h4>
                        <hr />

                        <!-- Invoice Info Summary -->
                        <div class="alert alert-info">
                            <h5><strong>Facture : <?php echo htmlspecialchars($invoice->invoice_number); ?></strong></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Patient :</strong> <?php echo htmlspecialchars($invoice->patient_name); ?></p>
                                    <p><strong>Plan :</strong> <?php echo htmlspecialchars($invoice->plan_name_fr ?: $invoice->plan_name); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Montant total (TTC) :</strong> <span class="text-success" style="font-size: 18px;"><?php echo number_format($invoice->total_amount, 0, ',', ' '); ?> FCFA</span></p>
                                    <p><strong>Statut :</strong> <span class="label label-warning"><?php echo ucfirst($invoice->status); ?></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form action="<?php echo admin_url('dietetic/payments/record/' . $invoice_id); ?>" method="POST">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Montant du paiement (FCFA) <span class="text-danger">*</span></label>
                                        <input type="number"
                                               name="amount"
                                               id="amount"
                                               class="form-control"
                                               value="<?php echo $invoice->total_amount; ?>"
                                               step="0.01"
                                               min="0.01"
                                               required>
                                        <small class="text-muted">Montant à payer (TTC)</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">Méthode de paiement <span class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                            <option value="">Sélectionnez une méthode</option>
                                            <option value="card">Carte bancaire</option>
                                            <option value="bank_transfer">Virement bancaire</option>
                                            <option value="cash">Espèces</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="wave">Wave</option>
                                            <option value="orange_money">Orange Money</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_date">Date du paiement <span class="text-danger">*</span></label>
                                        <input type="date"
                                               name="payment_date"
                                               id="payment_date"
                                               class="form-control"
                                               value="<?php echo date('Y-m-d'); ?>"
                                               required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Référence / N° de transaction</label>
                                        <input type="text"
                                               name="reference"
                                               id="reference"
                                               class="form-control"
                                               placeholder="Ex: TRX123456789">
                                        <small class="text-muted">Numéro de transaction ou référence du paiement</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes"
                                          id="notes"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Notes additionnelles sur ce paiement..."></textarea>
                            </div>

                            <!-- Commission Info -->
                            <div class="alert alert-success">
                                <h5><i class="fa fa-info-circle"></i> Information sur les commissions</h5>
                                <p class="text-muted">
                                    Une fois le paiement enregistré, les commissions seront automatiquement calculées
                                    selon le type de référence (<?php echo ucfirst($invoice->referral_source); ?>)
                                    et enregistrées dans le système.
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="form-group text-right">
                                <a href="<?php echo admin_url('dietetic/invoices/view/' . $invoice_id); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-check"></i> Enregistrer le Paiement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Auto-calculate tax breakdown display
    var totalAmount = parseFloat($('#amount').val());

    // Add visual feedback on payment method selection
    $('#payment_method').on('change', function() {
        var method = $(this).val();
        var $reference = $('#reference');

        switch(method) {
            case 'mobile_money':
            case 'wave':
            case 'orange_money':
                $reference.attr('placeholder', 'Ex: +221 XX XXX XX XX (numéro du payeur)');
                break;
            case 'bank_transfer':
                $reference.attr('placeholder', 'Ex: VIR20250128123456');
                break;
            case 'paypal':
                $reference.attr('placeholder', 'Ex: PAYID-M123456789');
                break;
            default:
                $reference.attr('placeholder', 'Ex: TRX123456789');
        }
    });

    // Confirm before submit
    $('form').on('submit', function(e) {
        var amount = $('#amount').val();
        var method = $('#payment_method option:selected').text();

        if (!confirm('Confirmer le paiement de ' + parseFloat(amount).toLocaleString('fr-FR') + ' FCFA par ' + method + ' ?')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
