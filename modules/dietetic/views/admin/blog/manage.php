<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('dietetic/blog/create'); ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> <?php echo _l('new_blog_article'); ?>
                            </a>
                            <a href="<?php echo admin_url('dietetic/blog/categories'); ?>" class="btn btn-default">
                                <i class="fa fa-folder"></i> <?php echo _l('blog_categories'); ?>
                            </a>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <table class="table dt-table table-blog-articles" data-order-col="5" data-order-type="desc">
                            <thead>
                                <tr>
                                    <th><?php echo _l('blog_article_title'); ?></th>
                                    <th><?php echo _l('blog_article_category'); ?></th>
                                    <th><?php echo _l('blog_article_author'); ?></th>
                                    <th><?php echo _l('blog_article_status'); ?></th>
                                    <th><?php echo _l('blog_article_views'); ?></th>
                                    <th><?php echo _l('blog_article_published_at'); ?></th>
                                    <th><?php echo _l('dietetic_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $article) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url('dietetic/blog/edit/' . $article->id); ?>">
                                                <?php echo htmlspecialchars($article->title); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($article->category && isset($article->category_details)) { ?>
                                                <span class="label" style="background-color: <?php echo $article->category_details->color; ?>">
                                                    <i class="fa <?php echo $article->category_details->icon; ?>"></i>
                                                    <?php echo htmlspecialchars($article->category_details->name); ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($article->author_name ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $status_class = [
                                                'draft' => 'default',
                                                'published' => 'success',
                                                'archived' => 'warning'
                                            ];
                                            ?>
                                            <span class="label label-<?php echo $status_class[$article->status] ?? 'default'; ?>">
                                                <?php echo _l('blog_status_' . $article->status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $article->views_count; ?></td>
                                        <td><?php echo $article->published_at ? date('Y-m-d H:i', strtotime($article->published_at)) : '-'; ?></td>
                                        <td>
                                            <a href="<?php echo admin_url('dietetic/blog/edit/' . $article->id); ?>" class="btn btn-default btn-icon btn-sm">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="<?php echo admin_url('dietetic/blog/delete/' . $article->id); ?>" class="btn btn-danger btn-icon btn-sm _delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
