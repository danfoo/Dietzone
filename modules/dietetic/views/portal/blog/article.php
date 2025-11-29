<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
.article-header {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    padding: 40px 20px;
    border-radius: 12px;
    color: white;
    margin-bottom: 30px;
}

.article-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 20px;
}

.article-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    font-size: 14px;
    opacity: 0.9;
}

.article-content {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    line-height: 1.8;
    font-size: 16px;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.article-content h2, .article-content h3 {
    margin-top: 30px;
    margin-bottom: 15px;
    color: #01807B;
}

.related-articles {
    margin-top: 50px;
}

.related-article-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
    height: 100%;
}

.related-article-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<div class="row">
    <div class="col-md-12">
        <!-- Back Button -->
        <a href="<?php echo site_url('dietetic/portal/blog'); ?>" class="btn btn-default" style="margin-bottom: 20px;">
            <i class="fa fa-arrow-left"></i> Retour au blog
        </a>

        <!-- Article Header -->
        <div class="article-header">
            <?php if ($article->category && isset($article->category_details)) { ?>
                <span style="display: inline-block; padding: 6px 14px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 13px; margin-bottom: 15px;">
                    <i class="fa <?php echo $article->category_details->icon; ?>"></i>
                    <?php echo htmlspecialchars($article->category_details->name); ?>
                </span>
            <?php } ?>

            <h1 class="article-title"><?php echo htmlspecialchars($article->title); ?></h1>

            <div class="article-meta">
                <span>
                    <i class="fa fa-user"></i> <?php echo htmlspecialchars($article->author_name); ?>
                </span>
                <span>
                    <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($article->published_at)); ?>
                </span>
                <span>
                    <i class="fa fa-eye"></i> <?php echo $article->views_count; ?> vues
                </span>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if ($article->featured_image) { ?>
            <div style="margin-bottom: 30px;">
                <img src="<?php echo module_dir_url('dietetic', 'uploads/blog/' . $article->featured_image); ?>" alt="<?php echo htmlspecialchars($article->title); ?>" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            </div>
        <?php } ?>

        <!-- Article Content -->
        <div class="article-content">
            <?php echo $article->content; ?>
        </div>

        <!-- Tags -->
        <?php if (!empty($article->tags_array)) { ?>
            <div style="margin-top: 30px;">
                <h4 style="margin-bottom: 15px; color: #01807B;">Tags :</h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php foreach ($article->tags_array as $tag) { ?>
                        <span style="padding: 6px 14px; background: #f8f9fa; border-radius: 20px; font-size: 13px; color: #6c757d;">
                            <i class="fa fa-tag"></i> <?php echo htmlspecialchars($tag); ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- Related Articles -->
        <?php if (!empty($related)) { ?>
            <div class="related-articles">
                <h3 style="margin-bottom: 25px; color: #01807B;">
                    <i class="fa fa-newspaper-o"></i> <?php echo _l('blog_related_articles'); ?>
                </h3>

                <div class="row">
                    <?php foreach ($related as $rel) { ?>
                        <div class="col-md-4">
                            <a href="<?php echo site_url('dietetic/portal/blog_article/' . $rel->slug); ?>" style="text-decoration: none; color: inherit; display: block;">
                                <div class="related-article-card">
                                    <h5 style="font-weight: 600; margin-bottom: 10px; color: #2c3e50;">
                                        <?php echo htmlspecialchars($rel->title); ?>
                                    </h5>
                                    <?php if ($rel->excerpt) { ?>
                                        <p style="font-size: 14px; color: #6c757d; margin-bottom: 10px;">
                                            <?php echo htmlspecialchars(substr($rel->excerpt, 0, 100)) . '...'; ?>
                                        </p>
                                    <?php } ?>
                                    <div style="font-size: 12px; color: #01807B; font-weight: 600;">
                                        Lire la suite <i class="fa fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
