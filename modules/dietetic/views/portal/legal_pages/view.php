<?php
$active_page = 'legal';
$page_title = $title;
$this->load->view('portal/includes/portal_header');
?>

<style>
.legal-page-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 20px;
}

.legal-page-header {
    background: linear-gradient(135deg, #01807B 0%, #01655f 100%);
    color: white;
    padding: 40px 30px;
    border-radius: 16px;
    margin-bottom: 40px;
    text-align: center;
}

.legal-page-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: white;
}

.legal-page-header .icon {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.9;
}

.legal-content-wrapper {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.legal-content {
    line-height: 1.8;
    font-size: 15px;
    color: #555;
}

.legal-content h1 {
    font-size: 28px;
    margin-top: 30px;
    margin-bottom: 15px;
    color: #333;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}

.legal-content h2 {
    font-size: 24px;
    margin-top: 25px;
    margin-bottom: 12px;
    color: #01807B;
}

.legal-content h3 {
    font-size: 20px;
    margin-top: 20px;
    margin-bottom: 10px;
    color: #555;
}

.legal-content p {
    margin-bottom: 15px;
    text-align: justify;
}

.legal-content ul, .legal-content ol {
    margin-bottom: 15px;
    padding-left: 30px;
}

.legal-content li {
    margin-bottom: 8px;
}

.legal-content strong {
    color: #333;
    font-weight: 600;
}

.legal-content a {
    color: #01807B;
    text-decoration: underline;
}

.legal-content a:hover {
    color: #01655f;
}

.legal-content code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 90%;
}

.legal-content blockquote {
    border-left: 4px solid #01807B;
    padding-left: 20px;
    margin: 20px 0;
    color: #666;
    font-style: italic;
}

.legal-footer {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    color: #999;
    font-size: 13px;
    margin-bottom: 30px;
}

.legal-footer i {
    margin-right: 5px;
}

.back-button {
    display: inline-block;
    padding: 12px 30px;
    background: #01807B;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
}

.back-button:hover {
    background: #01655f;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
    color: white;
    text-decoration: none;
}

.back-button i {
    margin-right: 8px;
}

@media (max-width: 768px) {
    .legal-content-wrapper {
        padding: 25px;
    }

    .legal-page-header {
        padding: 30px 20px;
    }

    .legal-page-header h1 {
        font-size: 24px;
    }

    .legal-content h1 {
        font-size: 22px;
    }

    .legal-content h2 {
        font-size: 20px;
    }
}
</style>

<div class="legal-page-container">
    <!-- Page Header -->
    <div class="legal-page-header">
        <div class="icon">
            <i class="fa fa-<?php echo strpos($title, 'Confidentialité') !== false ? 'shield' : 'file-text'; ?>"></i>
        </div>
        <h1><?php echo htmlspecialchars($title); ?></h1>
    </div>

    <!-- Content Wrapper -->
    <div class="legal-content-wrapper">
        <div class="legal-content">
            <?php echo $content; ?>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="legal-footer">
        <i class="fa fa-clock-o"></i> Dernière mise à jour : <?php echo date('d/m/Y'); ?>
    </div>

    <!-- Back Button -->
    <div style="text-align: center;">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="back-button">
            <i class="fa fa-arrow-left"></i> Retour au Portail
        </a>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
