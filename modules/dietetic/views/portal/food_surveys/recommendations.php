<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Recommandations'; ?> - <?php echo get_option('companyname'); ?></title>

    <!-- Perfex CSS -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/plugins/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">

    <style>
    :root {
        --primary-color: #01807B;
        --secondary-color: #F3911D;
        --tertiary-color: #FFFFFF;
        --text-dark: #2d3748;
        --text-light: #718096;
        --border-color: #e2e8f0;
        --success-color: #48bb78;
        --danger-color: #f56565;
        --warning-color: #ed8936;
        --info-color: #4299e1;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .container-fluid {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 15px;
    }

    /* Back link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 10px 15px;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow);
        transition: var(--transition);
    }

    .back-link:hover {
        background: var(--primary-color);
        color: white;
        transform: translateX(-3px);
    }

    /* Empty state */
    .empty-state {
        background: white;
        padding: 60px 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: var(--shadow);
    }

    .empty-state i {
        font-size: 64px;
        color: var(--text-light);
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--text-dark);
        font-size: 22px;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-light);
        font-size: 15px;
    }

    /* Entry card */
    .entry-card {
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .entry-card-header {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        padding: 20px 25px;
        border-bottom: 2px solid var(--border-color);
    }

    .entry-date {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .entry-date i {
        color: var(--primary-color);
    }

    /* Recommendation */
    .recommendation {
        border-bottom: 1px solid var(--border-color);
        padding: 25px;
    }

    .recommendation:last-child {
        border-bottom: none;
    }

    .recommendation-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .dietitian-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color) 0%, #026660 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 600;
        overflow: hidden;
    }

    .dietitian-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .recommendation-info h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .recommendation-info p {
        margin: 0;
        font-size: 13px;
        color: var(--text-light);
    }

    .recommendation-text {
        background: #f7fafc;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        margin-bottom: 20px;
        font-size: 15px;
        line-height: 1.7;
        color: var(--text-dark);
    }

    /* Comments section */
    .comments-section {
        background: rgba(243, 145, 29, 0.05);
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .comments-header {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .comments-header i {
        color: var(--secondary-color);
    }

    .comment-item {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 3px solid var(--secondary-color);
    }

    .comment-item:last-child {
        margin-bottom: 0;
    }

    .comment-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-light);
        margin-bottom: 8px;
    }

    .comment-text {
        font-size: 14px;
        color: var(--text-dark);
        line-height: 1.6;
    }

    /* Add comment form */
    .add-comment-form {
        margin-top: 15px;
    }

    .add-comment-form textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
        transition: var(--transition);
    }

    .add-comment-form textarea:focus {
        outline: none;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(243, 145, 29, 0.1);
    }

    .add-comment-form button {
        background: linear-gradient(135deg, var(--secondary-color) 0%, #e07d0f 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .add-comment-form button:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .add-comment-form button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Alert */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: rgba(72, 187, 120, 0.1);
        color: var(--success-color);
        border-left: 4px solid var(--success-color);
    }

    .alert-danger {
        background: rgba(245, 101, 101, 0.1);
        color: var(--danger-color);
        border-left: 4px solid var(--danger-color);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }

        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .recommendation {
            padding: 20px 15px;
        }
    }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Back link -->
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="back-link">
            <i class="fa fa-arrow-left"></i>
            Retour aux enquêtes
        </a>

        <!-- Page header -->
        <div class="page-header">
            <h1>
                <i class="fa fa-comments"></i>
                Recommandations
            </h1>
            <p><?php echo htmlspecialchars($survey->survey_name); ?></p>
        </div>

        <!-- Alert area -->
        <div id="alertArea"></div>

        <?php if (empty($entries)): ?>
            <!-- Empty state -->
            <div class="empty-state">
                <i class="fa fa-lightbulb-o"></i>
                <h3>Aucune recommandation</h3>
                <p>Votre diététicien n'a pas encore laissé de recommandations pour cette enquête.</p>
                <p>Continuez à soumettre vos repas quotidiens pour recevoir des conseils personnalisés.</p>
            </div>
        <?php else: ?>
            <!-- Entries with recommendations -->
            <?php foreach ($entries as $entry): ?>
                <div class="entry-card">
                    <div class="entry-card-header">
                        <div class="entry-date">
                            <i class="fa fa-calendar"></i>
                            Entrée du <?php echo date('d/m/Y', strtotime($entry->entry_date)); ?>
                        </div>
                    </div>

                    <?php foreach ($entry->recommendations as $recommendation): ?>
                        <div class="recommendation" data-id="<?php echo $recommendation->id; ?>">
                            <div class="recommendation-header">
                                <div class="dietitian-avatar">
                                    <?php if ($recommendation->profile_image): ?>
                                        <img src="<?php echo base_url('uploads/staff_profile_images/' . $recommendation->profile_image); ?>" alt="">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($recommendation->dietitian_name, 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="recommendation-info">
                                    <h4><?php echo htmlspecialchars($recommendation->dietitian_name); ?></h4>
                                    <p>
                                        <i class="fa fa-clock-o"></i>
                                        <?php echo date('d/m/Y à H:i', strtotime($recommendation->created_at)); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="recommendation-text">
                                <?php echo nl2br(htmlspecialchars($recommendation->recommendation_text)); ?>
                            </div>

                            <div class="comments-section">
                                <div class="comments-header">
                                    <i class="fa fa-comment"></i>
                                    Vos commentaires
                                </div>

                                <?php if (count($recommendation->comments) > 0): ?>
                                    <?php foreach ($recommendation->comments as $comment): ?>
                                        <div class="comment-item">
                                            <div class="comment-meta">
                                                <i class="fa fa-user-circle"></i>
                                                Vous
                                                <span>•</span>
                                                <?php echo date('d/m/Y à H:i', strtotime($comment->created_at)); ?>
                                            </div>
                                            <div class="comment-text">
                                                <?php echo nl2br(htmlspecialchars($comment->comment_text)); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- Add comment form -->
                                <div class="add-comment-form">
                                    <form class="comment-form" data-recommendation="<?php echo $recommendation->id; ?>">
                                        <textarea name="comment_text" placeholder="Ajoutez un commentaire ou une question..." required></textarea>
                                        <button type="submit">
                                            <i class="fa fa-paper-plane"></i>
                                            Envoyer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Perfex JS -->
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>

    <script>
    const base_url = '<?php echo base_url(); ?>';
    const site_url = '<?php echo site_url(); ?>';

    $(document).ready(function() {
        // Comment form submission
        $('.comment-form').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const $textarea = $form.find('textarea');
            const recommendationId = $form.data('recommendation');
            const commentText = $textarea.val().trim();

            if (!commentText) {
                showAlert('Veuillez entrer un commentaire', 'danger');
                return;
            }

            // Disable form
            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Envoi...');

            $.ajax({
                url: site_url + 'dietetic/portal/add_comment',
                type: 'POST',
                data: {
                    recommendation_id: recommendationId,
                    comment_text: commentText
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('Commentaire ajouté avec succès', 'success');
                        // Reload page to show new comment
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showAlert(response.message, 'danger');
                        // Re-enable form
                        $button.prop('disabled', false);
                        $button.html('<i class="fa fa-paper-plane"></i> Envoyer');
                    }
                },
                error: function() {
                    showAlert('Erreur lors de l\'ajout du commentaire', 'danger');
                    // Re-enable form
                    $button.prop('disabled', false);
                    $button.html('<i class="fa fa-paper-plane"></i> Envoyer');
                }
            });
        });
    });

    // Show alert
    function showAlert(message, type) {
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';

        const html = `
            <div class="alert alert-${type}">
                <i class="fa fa-${icon}"></i>
                ${message}
            </div>
        `;

        $('#alertArea').html(html);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#alertArea').fadeOut(300, function() {
                $(this).html('').show();
            });
        }, 5000);

        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
    }
    </script>
</body>
</html>
