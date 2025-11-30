<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <?php echo form_open_multipart($this->uri->uri_string(), ['id' => 'blog-article-form']); ?>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title"><?php echo _l('blog_article_title'); ?> *</label>
                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo isset($article) ? htmlspecialchars($article->title) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="slug"><?php echo _l('blog_article_slug'); ?></label>
                                    <input type="text" id="slug" name="slug" class="form-control" value="<?php echo isset($article) ? htmlspecialchars($article->slug) : ''; ?>">
                                    <small class="text-muted">Laissez vide pour générer automatiquement</small>
                                </div>

                                <div class="form-group">
                                    <label for="excerpt"><?php echo _l('blog_article_excerpt'); ?></label>
                                    <textarea id="excerpt" name="excerpt" class="form-control" rows="3"><?php echo isset($article) ? htmlspecialchars($article->excerpt) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="content"><?php echo _l('blog_article_content'); ?> *</label>
                                    <textarea id="content" name="content" class="form-control tinymce" rows="20"><?php echo isset($article) ? $article->content : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status"><?php echo _l('blog_article_status'); ?></label>
                                    <select id="status" name="status" class="form-control selectpicker">
                                        <option value="draft" <?php echo (isset($article) && $article->status == 'draft') ? 'selected' : ''; ?>>
                                            <?php echo _l('blog_status_draft'); ?>
                                        </option>
                                        <option value="published" <?php echo (isset($article) && $article->status == 'published') ? 'selected' : ''; ?>>
                                            <?php echo _l('blog_status_published'); ?>
                                        </option>
                                        <option value="archived" <?php echo (isset($article) && $article->status == 'archived') ? 'selected' : ''; ?>>
                                            <?php echo _l('blog_status_archived'); ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="category"><?php echo _l('blog_article_category'); ?></label>
                                    <select id="category" name="category" class="form-control selectpicker">
                                        <option value="">- Aucune -</option>
                                        <?php foreach ($categories as $cat) { ?>
                                            <option value="<?php echo $cat->slug; ?>" <?php echo (isset($article) && $article->category == $cat->slug) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat->name); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tags"><?php echo _l('blog_article_tags'); ?></label>
                                    <input type="text" id="tags" name="tags" class="form-control" value="<?php echo isset($article) ? htmlspecialchars($article->tags) : ''; ?>" placeholder="nutrition, santé, bien-être">
                                </div>

                                <div class="form-group">
                                    <label for="published_at"><?php echo _l('blog_article_published_at'); ?></label>
                                    <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="<?php echo isset($article) && $article->published_at ? date('Y-m-d\TH:i', strtotime($article->published_at)) : ''; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="featured_image"><?php echo _l('blog_article_featured_image'); ?></label>
                                    <?php if (isset($article) && $article->featured_image) { ?>
                                        <div class="mb-2">
                                            <img src="<?php echo base_url('uploads/blog/' . $article->featured_image); ?>" class="img-responsive" style="max-width: 100%;">
                                        </div>
                                    <?php } ?>
                                    <input type="file" id="featured_image" name="featured_image" class="form-control">
                                    <small class="text-muted">JPG, PNG, GIF (max 5MB)</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-check"></i> <?php echo isset($article) ? _l('submit') : _l('submit'); ?>
                                </button>

                                <a href="<?php echo admin_url('dietetic/blog'); ?>" class="btn btn-default btn-block">
                                    <i class="fa fa-arrow-left"></i> <?php echo _l('dietetic_back_to_program'); ?>
                                </a>
                            </div>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    init_editor('.tinymce');
</script>
