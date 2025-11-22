<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Page Title -->
                        <h2 style="margin-top: 0; color: #333; border-bottom: 3px solid #84c529; padding-bottom: 15px;">
                            <i class="fa fa-<?php echo strpos($title, 'Confidentialité') !== false ? 'shield' : 'file-text'; ?>"></i>
                            <?php echo htmlspecialchars($title); ?>
                        </h2>

                        <!-- Page Content -->
                        <div class="legal-content" style="margin-top: 30px;">
                            <?php echo $content; ?>
                        </div>

                        <!-- Last Updated -->
                        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee; color: #999; font-size: 12px;">
                            <i class="fa fa-clock-o"></i> Dernière mise à jour : <?php echo date('d/m/Y'); ?>
                        </div>

                        <!-- Back Button -->
                        <?php if (is_staff_logged_in()): ?>
                        <div style="margin-top: 20px;">
                            <a href="<?php echo admin_url('dietetic/dashboard'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour au Dashboard
                            </a>

                            <?php if (is_admin()): ?>
                            <a href="<?php echo admin_url('dietetic/legal_pages/manage'); ?>" class="btn btn-info">
                                <i class="fa fa-edit"></i> Modifier cette page
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
    color: #444;
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
    color: #84c529;
    text-decoration: underline;
}

.legal-content a:hover {
    color: #6fa320;
}

.legal-content code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 90%;
}

.legal-content blockquote {
    border-left: 4px solid #84c529;
    padding-left: 20px;
    margin: 20px 0;
    color: #666;
    font-style: italic;
}
</style>

<?php init_tail(); ?>
