<?php
$active_page = 'blog';
$page_title = $article->title;
$this->load->view('portal/includes/portal_header');
?>

<style>
/* === DESIGN MOBILE FIRST - PAGE DÉTAIL BLOG === */

/* Variables de couleurs - Charte graphique officielle */
:root {
    --primary-color: #01807B;
    --primary-dark: #026660;
    --secondary-color: #F3911D;
    --background: #F8F9FC;
    --card-bg: #FFFFFF;
    --text-primary: #1E1E1E;
    --text-secondary: #6B7280;
    --border-radius-lg: 20px;
    --border-radius-md: 16px;
    --border-radius-sm: 12px;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
}

body {
    background: var(--background);
}

/* === HERO IMAGE AVEC OVERLAY === */
.article-hero {
    position: relative;
    width: 100%;
    height: 280px;
    margin: 0 0 20px 0;
    overflow: hidden;
    border-radius: var(--border-radius-lg);
}

.article-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.article-hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    color: rgba(255,255,255,0.3);
}

/* Overlay noir en bas */
.article-hero-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 70%, transparent 100%);
    padding: 50px 20px 20px;
    color: white;
}

.article-category-badge {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(255,255,255,0.25);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 12px;
    backdrop-filter: blur(10px);
}

.article-hero-title {
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 16px 0;
    color: white;
    line-height: 1.3;
    letter-spacing: -0.3px;
}

.article-hero-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.article-hero-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    opacity: 0.95;
}

.article-hero-meta-item i {
    font-size: 14px;
}

/* === NAVIGATION BUTTONS === */
.article-actions {
    padding: 0;
    display: flex;
    gap: 12px;
    margin: 0 0 20px 0;
}

.btn-back {
    flex: 1;
    padding: 12px 16px;
    border-radius: 50px;
    background: #F3F4F6;
    color: var(--text-primary);
    border: none;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-back:hover {
    background: #E5E7EB;
    text-decoration: none;
    color: var(--text-primary);
}

/* === CONTAINER === */
.article-container {
    padding: 0;
    max-width: 100%;
}

/* === SECTION CARD === */
.section-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.section-card h3 {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 20px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.3px;
}

.section-card h3 i {
    color: var(--primary-color);
    font-size: 18px;
}

/* === ARTICLE CONTENT === */
.article-content {
    font-size: 15px;
    line-height: 1.8;
    color: var(--text-primary);
}

.article-content p {
    margin-bottom: 16px;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--border-radius-md);
    margin: 20px 0;
}

.article-content h2,
.article-content h3,
.article-content h4 {
    color: var(--primary-color);
    font-weight: 700;
    margin-top: 28px;
    margin-bottom: 16px;
}

.article-content h2 {
    font-size: 22px;
}

.article-content h3 {
    font-size: 19px;
}

.article-content h4 {
    font-size: 17px;
}

.article-content ul,
.article-content ol {
    margin: 16px 0;
    padding-left: 24px;
}

.article-content li {
    margin-bottom: 10px;
    line-height: 1.7;
}

.article-content blockquote {
    border-left: 4px solid var(--primary-color);
    padding: 16px 20px;
    margin: 20px 0;
    background: #F8F9FA;
    border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
    font-style: italic;
    color: var(--text-secondary);
}

/* === TAGS DISCRETS === */
.tags-wrapper {
    padding: 20px 0;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag-badge {
    background: #F3F4F6;
    color: var(--text-secondary);
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: -0.1px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.tag-badge i {
    font-size: 11px;
}

/* === AUTEUR DE L'ARTICLE === */
.article-author {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 16px;
}

.article-author-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--primary-color);
}

.article-author-photo-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    font-weight: 800;
    border: 3px solid var(--primary-color);
}

.article-author-info {
    flex: 1;
}

.article-author-label {
    font-size: 11px;
    text-transform: uppercase;
    color: var(--text-secondary);
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.article-author-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

/* === ARTICLES CONNEXES === */
.related-articles {
    margin-top: 30px;
}

.related-article-card {
    background: var(--card-bg);
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.related-article-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.related-article-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.related-article-content {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.related-article-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
    line-height: 1.4;
}

.related-article-excerpt {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 12px;
    flex: 1;
    line-height: 1.6;
}

.related-article-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #F1F3F5;
    font-size: 11px;
    color: var(--text-secondary);
}

.related-article-read-more {
    font-size: 13px;
    color: var(--primary-color);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* === TABLET (576px+) === */
@media (min-width: 576px) {
    .article-hero {
        height: 300px;
        margin: 0 0 24px 0;
    }

    .article-hero-title {
        font-size: 28px;
    }

    .article-hero-meta-item {
        font-size: 14px;
    }

    .article-actions {
        margin: 0 0 24px 0;
    }

    .section-card {
        padding: 28px;
        margin-bottom: 24px;
    }

    .article-author {
        padding: 24px;
    }

    .article-author-photo,
    .article-author-photo-placeholder {
        width: 70px;
        height: 70px;
    }

    .article-author-name {
        font-size: 18px;
    }
}

/* === DESKTOP (992px+) === */
@media (min-width: 992px) {
    .article-hero {
        height: 350px;
        margin: 0 0 28px 0;
    }

    .article-hero-overlay {
        padding: 60px 40px 30px;
    }

    .article-hero-title {
        font-size: 36px;
    }

    .article-actions {
        margin: 0 0 28px 0;
    }

    .section-card {
        padding: 32px;
        margin-bottom: 28px;
    }

    .section-card h3 {
        font-size: 20px;
    }

    .article-content {
        font-size: 16px;
    }

    .article-content h2 {
        font-size: 26px;
    }

    .article-content h3 {
        font-size: 22px;
    }

    .article-content h4 {
        font-size: 19px;
    }

    .related-article-card {
        margin-bottom: 0;
    }
}
</style>

<!-- Hero Image avec Overlay -->
<div class="article-hero">
    <?php if ($article->featured_image) : ?>
        <img src="<?php echo module_dir_url('dietetic', 'uploads/blog/' . $article->featured_image); ?>"
             alt="<?php echo htmlspecialchars($article->title); ?>"
             class="article-hero-image">
    <?php else : ?>
        <div class="article-hero-placeholder">
            <i class="fa fa-file-text-o"></i>
        </div>
    <?php endif; ?>

    <!-- Overlay avec infos -->
    <div class="article-hero-overlay">
        <?php if ($article->category && isset($article->category_details)) : ?>
            <span class="article-category-badge" style="color: white;">
                <i class="fa <?php echo $article->category_details->icon; ?>"></i>
                <?php echo htmlspecialchars($article->category_details->name); ?>
            </span>
        <?php endif; ?>

        <h1 class="article-hero-title"><?php echo htmlspecialchars($article->title); ?></h1>

        <div class="article-hero-meta">
            <!-- Date et auteur à gauche -->
            <div class="article-hero-meta-item">
                <i class="fa fa-calendar"></i>
                <span><?php echo date('d M Y', strtotime($article->published_at)); ?></span>
            </div>

            <!-- Nombre de vues à droite -->
            <div class="article-hero-meta-item">
                <i class="fa fa-eye"></i>
                <span><?php echo number_format($article->views_count); ?> vues</span>
            </div>
        </div>
    </div>
</div>

<!-- Actions (Retour) -->
<div class="article-actions">
    <a href="<?php echo site_url('dietetic/portal/blog'); ?>" class="btn-back">
        <i class="fa fa-arrow-left"></i> Retour au blog
    </a>
</div>

<div class="article-container">
    <!-- Contenu de l'article -->
    <div class="section-card">
        <h3><i class="fa fa-align-left"></i> Article</h3>
        <div class="article-content">
            <?php echo $article->content; ?>
        </div>
    </div>

    <!-- Tags - Discrets -->
    <?php if (!empty($article->tags_array)) : ?>
        <div class="tags-wrapper">
            <div class="tags-list">
                <?php foreach ($article->tags_array as $tag) : ?>
                    <span class="tag-badge">
                        <i class="fa fa-tag"></i> <?php echo htmlspecialchars($tag); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Auteur de l'article -->
    <div class="article-author">
        <?php
        // Créer les initiales de l'auteur
        $initials = '';
        if ($article->author_name) {
            $names = explode(' ', $article->author_name);
            $initials = strtoupper(substr($names[0], 0, 1));
            if (count($names) > 1) {
                $initials .= strtoupper(substr($names[count($names) - 1], 0, 1));
            }
        }
        ?>
        <div class="article-author-photo-placeholder">
            <?php echo $initials; ?>
        </div>
        <div class="article-author-info">
            <div class="article-author-label">Écrit par</div>
            <div class="article-author-name"><?php echo htmlspecialchars($article->author_name); ?></div>
        </div>
    </div>

    <!-- Articles Connexes -->
    <?php if (!empty($related)) : ?>
        <div class="related-articles">
            <div class="section-card">
                <h3><i class="fa fa-newspaper-o"></i> <?php echo _l('blog_related_articles'); ?></h3>

                <div class="row">
                    <?php foreach ($related as $rel) : ?>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?php echo site_url('dietetic/portal/blog_article/' . $rel->slug); ?>"
                               style="text-decoration: none; color: inherit; display: block;">
                                <div class="related-article-card">
                                    <?php if ($rel->featured_image) : ?>
                                        <img src="<?php echo module_dir_url('dietetic', 'uploads/blog/' . $rel->featured_image); ?>"
                                             alt="<?php echo htmlspecialchars($rel->title); ?>"
                                             class="related-article-image">
                                    <?php else : ?>
                                        <div class="related-article-image"></div>
                                    <?php endif; ?>

                                    <div class="related-article-content">
                                        <h4 class="related-article-title">
                                            <?php echo htmlspecialchars($rel->title); ?>
                                        </h4>
                                        <?php if ($rel->excerpt) : ?>
                                            <p class="related-article-excerpt">
                                                <?php echo htmlspecialchars(substr($rel->excerpt, 0, 100)) . (strlen($rel->excerpt) > 100 ? '...' : ''); ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="related-article-footer">
                                            <span>
                                                <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($rel->published_at)); ?>
                                            </span>
                                            <span class="related-article-read-more">
                                                Lire <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
