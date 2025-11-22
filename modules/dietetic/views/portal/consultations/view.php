<?php
defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('portal/includes/portal_header');
?>
<style>
    .consultation-detail-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 0 15px;
    }
    
    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .detail-header {
        background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
        color: white;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
    }
    
    .detail-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .detail-subtitle {
        opacity: 0.9;
        font-size: 15px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .info-box {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
    }
    
    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2c3e50;
        margin: 24px 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title i {
        color: #01807B;
    }
    
    .btn-action-big {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        margin-right: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #01807B 0%, #026661 100%);
        color: white;
    }
    
    .btn-secondary {
        background: white;
        color: #6c757d;
        border: 2px solid #dee2e6;
    }
    
    .content-box {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 12px;
        border-left: 4px solid #01807B;
        margin-bottom: 16px;
    }
</style>

<div class="consultation-detail-container">
    <div class="detail-header">
        <div class="detail-title">
            <i class="fa fa-calendar"></i>
            Consultation du <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
        </div>
        <div class="detail-subtitle">
            <?php echo dietetic_consultation_type_label($consultation->consultation_type); ?>
            <?php if ($dietitian) { ?>
                · Dr. <?php echo $dietitian->firstname . ' ' . $dietitian->lastname; ?>
            <?php } ?>
        </div>
    </div>

    <div class="detail-card">
        <h3 class="section-title"><i class="fa fa-info-circle"></i> Informations</h3>
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Mode</div>
                <div class="info-value">
                    <?php echo ($consultation->consultation_mode === 'online') ? '💻 En ligne' : '🏥 Présentiel'; ?>
                </div>
            </div>
            
            <?php if ($consultation->duration) { ?>
            <div class="info-box">
                <div class="info-label">Durée</div>
                <div class="info-value"><?php echo $consultation->duration; ?> minutes</div>
            </div>
            <?php } ?>
            
            <div class="info-box">
                <div class="info-label">Status</div>
                <div class="info-value"><?php echo ucfirst($consultation->status); ?></div>
            </div>
        </div>

        <?php if ($consultation->consultation_mode === 'online' && $consultation->meeting_link) { ?>
            <a href="<?php echo htmlspecialchars($consultation->meeting_link); ?>" target="_blank" class="btn-action-big btn-primary">
                <i class="fa fa-video-camera"></i> Rejoindre la consultation
            </a>
        <?php } elseif ($consultation->consultation_mode === 'in_person' && $consultation->location) { ?>
            <div class="content-box">
                <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($consultation->location); ?>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($consultation->location); ?>" target="_blank" class="btn-action-big btn-primary">
                <i class="fa fa-map-marker"></i> Voir l'itinéraire
            </a>
        <?php } ?>
    </div>

    <?php if ($consultation->reason) { ?>
    <div class="detail-card">
        <h3 class="section-title"><i class="fa fa-file-text-o"></i> Motif de consultation</h3>
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($consultation->reason)); ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($consultation->observations) { ?>
    <div class="detail-card">
        <h3 class="section-title"><i class="fa fa-sticky-note"></i> Observations</h3>
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($consultation->observations)); ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($consultation->recommendations) { ?>
    <div class="detail-card">
        <h3 class="section-title"><i class="fa fa-lightbulb-o"></i> Recommandations</h3>
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($consultation->recommendations)); ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($consultation->notes) { ?>
    <div class="detail-card">
        <h3 class="section-title"><i class="fa fa-pencil"></i> Notes</h3>
        <div class="content-box">
            <?php echo nl2br(htmlspecialchars($consultation->notes)); ?>
        </div>
    </div>
    <?php } ?>

    <div style="text-align: center; margin-top: 30px;">
        <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="btn-action-big btn-secondary">
            <i class="fa fa-arrow-left"></i> Retour aux consultations
        </a>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
