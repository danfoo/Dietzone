<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$active_page = 'invoices';
$page_title = 'Paiement Réussi';
$this->load->view('portal/includes/portal_header');
?>

<style>
:root {
    --primary-color: #01807B;
    --success-color: #48bb78;
    --success-light: #E6FFED;
    --text-dark: #1a202c;
    --text-light: #718096;
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f0f4f8 0%, #e8eff5 50%, #f5f7fa 100%);
    background-attachment: fixed;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.payment-result-container {
    max-width: 600px;
    margin: 60px auto;
    padding: 20px;
}

.payment-result-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-header {
    background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
    padding: 50px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.result-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.result-icon {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 50px;
    color: white;
    position: relative;
    z-index: 1;
    animation: checkmark 0.8s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0) rotate(-45deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(0deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.result-header h1 {
    color: white;
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 10px 0;
    position: relative;
    z-index: 1;
}

.result-header p {
    color: rgba(255, 255, 255, 0.95);
    font-size: 16px;
    margin: 0;
    position: relative;
    z-index: 1;
}

.result-body {
    padding: 40px 30px;
}

.success-badge {
    background: var(--success-light);
    border: 2px solid var(--success-color);
    color: var(--success-color);
    padding: 12px 20px;
    border-radius: 12px;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.success-badge i {
    font-size: 18px;
}

.invoice-details {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.invoice-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.invoice-detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-light);
    font-size: 14px;
    font-weight: 600;
}

.detail-value {
    color: var(--text-dark);
    font-size: 16px;
    font-weight: 700;
}

.detail-value.amount {
    color: var(--success-color);
    font-size: 20px;
}

.transaction-info {
    background: #f0f8ff;
    border-left: 4px solid var(--primary-color);
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.transaction-info h4 {
    color: var(--text-dark);
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 8px 0;
}

.transaction-info p {
    color: var(--text-light);
    font-size: 13px;
    margin: 4px 0;
    word-break: break-all;
}

.transaction-info strong {
    color: var(--text-dark);
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-direction: column;
}

.btn {
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #015a57 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(1, 128, 123, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(1, 128, 123, 0.4);
}

.btn-secondary {
    background: white;
    color: var(--text-dark);
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    background: var(--success-color);
    position: absolute;
    animation: confetti-fall 3s linear;
}

@keyframes confetti-fall {
    to {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

@media (max-width: 768px) {
    .payment-result-container {
        margin: 20px auto;
        padding: 12px;
    }

    .payment-result-card {
        border-radius: 16px;
    }

    .result-header {
        padding: 40px 20px;
    }

    .result-header h1 {
        font-size: 24px;
    }

    .result-body {
        padding: 30px 20px;
    }

    .btn {
        width: 100%;
        min-height: 56px;
    }
}
</style>

<div class="payment-result-container">
    <div class="payment-result-card">
        <!-- Header -->
        <div class="result-header">
            <div class="result-icon">
                <i class="fa fa-check"></i>
            </div>
            <h1>Paiement Réussi !</h1>
            <p>Votre paiement a été traité avec succès</p>
        </div>

        <!-- Body -->
        <div class="result-body">
            <!-- Success Badge -->
            <div class="success-badge">
                <i class="fa fa-shield"></i>
                Paiement sécurisé par PayPal
            </div>

            <!-- Invoice Details -->
            <div class="invoice-details">
                <div class="invoice-detail-row">
                    <span class="detail-label">Facture N°</span>
                    <span class="detail-value">#<?php echo $invoice->id; ?></span>
                </div>
                <div class="invoice-detail-row">
                    <span class="detail-label">Montant Payé</span>
                    <span class="detail-value amount">
                        <?php echo format_money($invoice->total); ?>
                    </span>
                </div>
                <div class="invoice-detail-row">
                    <span class="detail-label">Statut</span>
                    <span class="detail-value" style="color: var(--success-color);">
                        <i class="fa fa-check-circle"></i> Payée
                    </span>
                </div>
                <div class="invoice-detail-row">
                    <span class="detail-label">Date de paiement</span>
                    <span class="detail-value"><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="transaction-info">
                <h4><i class="fa fa-info-circle"></i> Informations de transaction</h4>
                <?php if (!empty($transaction_id)): ?>
                <p><strong>ID Transaction :</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
                <?php endif; ?>
                <p><strong>ID Commande PayPal :</strong> <?php echo htmlspecialchars($order_id); ?></p>
                <p><strong>Méthode :</strong> PayPal</p>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn btn-primary">
                    <i class="fa fa-file-text"></i>
                    Voir mes factures
                </a>
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-secondary">
                    <i class="fa fa-home"></i>
                    Retour au portail
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Confetti effect
function createConfetti() {
    const colors = ['#48bb78', '#01807B', '#F3911D'];
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            document.body.appendChild(confetti);

            setTimeout(() => confetti.remove(), 3000);
        }, i * 30);
    }
}

// Trigger confetti on page load
window.addEventListener('load', createConfetti);
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
