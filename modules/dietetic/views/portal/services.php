<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
.services-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-title {
    font-size: 28px;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 10px;
    text-align: center;
    font-family: 'Montserrat', 'Avenir Next', sans-serif;
}

.page-subtitle {
    text-align: center;
    color: #6c757d;
    margin-bottom: 30px;
    font-size: 16px;
}

/* Alert for unpaid invoices */
.unpaid-alert {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
}

.unpaid-alert h3 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: 700;
}

.unpaid-alert p {
    margin: 5px 0;
    opacity: 0.95;
}

.unpaid-alert .btn {
    background: white;
    color: #ff6b6b;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    margin-top: 10px;
}

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

/* Service Card */
.service-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #01807B 0%, #019d96 100%);
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 28px rgba(1, 128, 123, 0.2);
    border-color: rgba(1, 128, 123, 0.3);
}

/* Service Badge */
.service-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 2;
}

.service-badge.popular {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
}

.service-badge.recommended {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
}

.service-badge.new {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
}

/* Service Icon */
.service-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #01807B 0%, #019d96 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    font-size: 32px;
    color: white;
}

/* Service Title */
.service-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
    font-family: 'Montserrat', 'Avenir Next', sans-serif;
}

/* Service Description */
.service-description {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 60px;
}

/* Service Price */
.service-price-container {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 20px;
}

.service-price {
    font-size: 32px;
    font-weight: 800;
    color: #01807B;
    font-family: 'Montserrat', 'Avenir Next', sans-serif;
}

.service-currency {
    font-size: 18px;
    color: #6c757d;
    font-weight: 600;
}

/* Subscribe Button */
.subscribe-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #01807B 0%, #019d96 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.subscribe-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(1, 128, 123, 0.3);
}

.subscribe-btn:active {
    transform: translateY(0);
}

.subscribe-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 72px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state-text {
    color: #6c757d;
    font-size: 16px;
}

/* Modal */
.modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    animation: fadeIn 0.3s ease;
}

.modal-backdrop.active {
    display: block;
}

.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 20px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
    z-index: 9999;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.modal.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translate(-50%, -40%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%);
        opacity: 1;
    }
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #2c3e50;
}

.modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #f0f0f0;
    color: #2c3e50;
}

.modal-body {
    margin-bottom: 24px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-cancel {
    padding: 12px 24px;
    background: #e9ecef;
    color: #495057;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #dee2e6;
}

.btn-confirm {
    padding: 12px 24px;
    background: linear-gradient(135deg, #01807B 0%, #019d96 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(1, 128, 123, 0.3);
}

.btn-confirm:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

/* Toast Notifications - Mobile First Ultra Modern */
.toast-container {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10000;
    width: 90%;
    max-width: 500px;
    pointer-events: none;
}

.toast {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    display: flex;
    align-items: center;
    gap: 12px;
    pointer-events: all;
    transform: translateY(-100px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast.hide {
    transform: translateY(-100px);
    opacity: 0;
}

.toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}

.toast.success .toast-icon {
    background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
    color: white;
}

.toast.error .toast-icon {
    background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
    color: white;
}

.toast.warning .toast-icon {
    background: linear-gradient(135deg, #ffbb33 0%, #ff8800 100%);
    color: white;
}

.toast.info .toast-icon {
    background: linear-gradient(135deg, #33b5e5 0%, #0099cc 100%);
    color: white;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 700;
    font-size: 15px;
    margin-bottom: 4px;
    color: #2c3e50;
}

.toast-message {
    font-size: 14px;
    color: #6c757d;
    line-height: 1.4;
}

.toast-close {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.05);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #6c757d;
    transition: all 0.2s;
    flex-shrink: 0;
}

.toast-close:hover {
    background: rgba(0, 0, 0, 0.1);
    transform: rotate(90deg);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .services-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .page-title {
        font-size: 24px;
    }

    .service-card {
        padding: 20px;
    }

    .modal {
        width: 95%;
        padding: 24px;
    }

    .toast-container {
        width: 95%;
        top: 10px;
    }

    .toast {
        padding: 14px 16px;
    }

    .toast-icon {
        width: 28px;
        height: 28px;
        font-size: 16px;
    }

    .toast-title {
        font-size: 14px;
    }

    .toast-message {
        font-size: 13px;
    }
}
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<div class="services-page">
    <h1 class="page-title">🎯 Nos Services</h1>
    <p class="page-subtitle">Découvrez nos services et souscrivez en quelques clics</p>

    <?php if ($has_unpaid_invoices): ?>
    <div class="unpaid-alert">
        <h3>⚠️ Factures impayées</h3>
        <p>Vous avez <?php echo count($unpaid_invoices); ?> facture(s) impayée(s).</p>
        <p>Veuillez régler vos factures avant de souscrire à un nouveau service.</p>
        <a href="<?php echo site_url('dietetic/portal/invoices'); ?>" class="btn">
            Voir mes factures
        </a>
    </div>
    <?php endif; ?>

    <?php if (!empty($services)): ?>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
        <div class="service-card" data-service-id="<?php echo $service->id; ?>">
            <?php
            // Determine badge based on price or custom field (you can adjust logic)
            $badge_type = '';
            $badge_label = '';

            // Example logic: highest price = popular, lowest = recommended, new if added recently
            if ($service->rate > 50000) {
                $badge_type = 'popular';
                $badge_label = 'Populaire';
            } elseif ($service->rate < 20000) {
                $badge_type = 'recommended';
                $badge_label = 'Recommandé';
            }

            // If you want to check for recent additions, you can add created_at field logic
            // For now, we'll just display badges based on price

            if ($badge_type):
            ?>
            <div class="service-badge <?php echo $badge_type; ?>"><?php echo $badge_label; ?></div>
            <?php endif; ?>

            <div class="service-icon">
                <?php
                // Icon based on service name/type - you can customize
                $icon = '🎯';
                if (stripos($service->description, 'consultation') !== false) {
                    $icon = '👨‍⚕️';
                } elseif (stripos($service->description, 'nutrition') !== false || stripos($service->description, 'plan') !== false) {
                    $icon = '🥗';
                } elseif (stripos($service->description, 'suivi') !== false) {
                    $icon = '📊';
                }
                echo $icon;
                ?>
            </div>

            <h3 class="service-title"><?php echo htmlspecialchars($service->description); ?></h3>

            <p class="service-description">
                <?php
                echo htmlspecialchars(
                    $service->long_description ?
                    (strlen($service->long_description) > 120 ? substr($service->long_description, 0, 120) . '...' : $service->long_description) :
                    'Service de qualité pour vous accompagner dans votre parcours.'
                );
                ?>
            </p>

            <div class="service-price-container">
                <span class="service-price"><?php echo number_format($service->rate, 0, ',', ' '); ?></span>
                <span class="service-currency">FCFA</span>
            </div>

            <button
                class="subscribe-btn"
                onclick="checkEligibilityAndSubscribe(event, <?php echo $service->id; ?>, '<?php echo addslashes(htmlspecialchars($service->description)); ?>', <?php echo $service->rate; ?>)"
                <?php echo $has_unpaid_invoices ? 'disabled' : ''; ?>>
                <?php echo $has_unpaid_invoices ? 'Réglez vos factures' : 'Souscrire'; ?>
            </button>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">🎯</div>
        <h2 class="empty-state-title">Aucun service disponible</h2>
        <p class="empty-state-text">Nos services seront bientôt disponibles. Revenez plus tard !</p>
    </div>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div class="modal-backdrop" id="modalBackdrop" onclick="closeModal()"></div>
<div class="modal" id="subscriptionModal">
    <div class="modal-header">
        <h3 class="modal-title">Confirmer la souscription</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <p>Vous êtes sur le point de souscrire au service :</p>
        <p style="font-weight: 700; color: #01807B; font-size: 18px; margin: 15px 0;" id="modalServiceName"></p>
        <p>Montant : <strong id="modalServicePrice"></strong> FCFA</p>
        <p style="color: #6c757d; font-size: 14px; margin-top: 15px;">
            Une facture sera générée automatiquement après confirmation.
        </p>
    </div>
    <div class="modal-footer">
        <button class="btn-cancel" onclick="closeModal()">Annuler</button>
        <button class="btn-confirm" id="confirmBtn" onclick="confirmSubscription()">Confirmer</button>
    </div>
</div>

<script>
let currentServiceId = null;

// Modern Toast Notification System
function showToast(type, title, message, duration = 5000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    toast.innerHTML = `
        <div class="toast-icon">${icons[type] || '✓'}</div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);">×</button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }, duration);
}

function checkEligibilityAndSubscribe(event, serviceId, serviceName, servicePrice) {
    // Show loading state
    event.target.disabled = true;
    event.target.textContent = 'Vérification...';

    // Check eligibility
    fetch('<?php echo site_url("dietetic/portal/check_service_eligibility"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>&item_id=' + serviceId
    })
    .then(response => response.json())
    .then(data => {
        // Reset button
        event.target.disabled = false;
        event.target.textContent = 'Souscrire';

        if (data.success) {
            // Show confirmation modal
            currentServiceId = serviceId;
            document.getElementById('modalServiceName').textContent = serviceName;
            document.getElementById('modalServicePrice').textContent = new Intl.NumberFormat('fr-FR').format(servicePrice);
            openModal();
        } else {
            // Show error toast
            showToast('error', 'Erreur', data.message);

            // Redirect if needed
            if (data.requires_payment) {
                setTimeout(() => {
                    window.location.href = '<?php echo site_url("dietetic/portal/invoices"); ?>';
                }, 2000);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        event.target.disabled = false;
        event.target.textContent = 'Souscrire';
        showToast('error', 'Erreur', 'Une erreur est survenue. Veuillez réessayer.');
    });
}

function openModal() {
    document.getElementById('modalBackdrop').classList.add('active');
    document.getElementById('subscriptionModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalBackdrop').classList.remove('active');
    document.getElementById('subscriptionModal').classList.remove('active');
    document.body.style.overflow = '';
    currentServiceId = null;
}

function confirmSubscription() {
    if (!currentServiceId) return;

    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Création...';

    fetch('<?php echo site_url("dietetic/portal/subscribe_service"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: '<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>&item_id=' + currentServiceId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success! Close modal and show toast
            closeModal();
            showToast('success', 'Souscription réussie !', 'Votre facture a été créée. Redirection en cours...', 3000);

            // Redirect to invoices page
            setTimeout(() => {
                window.location.href = data.invoice_url;
            }, 2000);
        } else {
            // Error
            showToast('error', 'Erreur', data.message);
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirmer';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Erreur', 'Une erreur est survenue. Veuillez réessayer.');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirmer';
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('subscriptionModal').classList.contains('active')) {
        closeModal();
    }
});
</script>
