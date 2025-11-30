<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
.blog-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.blog-card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
}

.blog-card-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-category {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 10px;
}

.blog-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    line-height: 1.4;
}

.blog-card-excerpt {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 15px;
    flex: 1;
}

.blog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #e9ecef;
    font-size: 12px;
    color: #6c757d;
}

.category-filter {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.category-filter-btn {
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #e9ecef;
    background: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.category-filter-btn:hover, .category-filter-btn.active {
    border-color: #01807B;
    background: #01807B;
    color: white;
}
</style>

<div class="row">
    <div class="col-md-12">
        <h2 style="margin-bottom: 30px; color: #01807B;">
            <i class="fa fa-newspaper-o"></i> Conseils & Blog
        </h2>

        <!-- Category Filters -->
        <div class="category-filter">
            <a href="<?php echo site_url('dietetic/portal/blog'); ?>" class="category-filter-btn <?php echo !$current_category ? 'active' : ''; ?>">
                <?php echo _l('blog_all_categories'); ?>
            </a>
            <?php foreach ($categories as $cat) { ?>
                <a href="<?php echo site_url('dietetic/portal/blog?category=' . $cat->slug); ?>" class="category-filter-btn <?php echo $current_category == $cat->slug ? 'active' : ''; ?>" style="<?php echo $current_category == $cat->slug ? 'border-color: ' . $cat->color . '; background: ' . $cat->color . ';' : ''; ?>">
                    <i class="fa <?php echo $cat->icon; ?>"></i> <?php echo htmlspecialchars($cat->name); ?>
                </a>
            <?php } ?>
        </div>

        <!-- Articles Grid -->
        <div class="row">
            <?php if (empty($articles)) { ?>
                <div class="col-md-12 text-center" style="padding: 60px 20px;">
                    <i class="fa fa-newspaper-o" style="font-size: 64px; color: #e9ecef; margin-bottom: 20px;"></i>
                    <h3 style="color: #6c757d;"><?php echo _l('blog_no_articles'); ?></h3>
                </div>
            <?php } else { ?>
                <?php foreach ($articles as $article) { ?>
                    <div class="col-md-4 col-sm-6" style="margin-bottom: 30px;">
                        <a href="<?php echo site_url('dietetic/portal/blog_article/' . $article->slug); ?>" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                            <div class="blog-card">
                                <?php if ($article->featured_image) { ?>
                                    <img src="<?php echo base_url('uploads/blog/' . $article->featured_image); ?>" alt="<?php echo htmlspecialchars($article->title); ?>" class="blog-card-image">
                                <?php } else { ?>
                                    <div class="blog-card-image"></div>
                                <?php } ?>

                                <div class="blog-card-content">
                                    <?php if ($article->category && isset($article->category_details)) { ?>
                                        <span class="blog-card-category" style="background-color: <?php echo $article->category_details->color; ?>; color: white;">
                                            <i class="fa <?php echo $article->category_details->icon; ?>"></i>
                                            <?php echo htmlspecialchars($article->category_details->name); ?>
                                        </span>
                                    <?php } ?>

                                    <h3 class="blog-card-title"><?php echo htmlspecialchars($article->title); ?></h3>

                                    <?php if ($article->excerpt) { ?>
                                        <p class="blog-card-excerpt"><?php echo htmlspecialchars(substr($article->excerpt, 0, 150)) . (strlen($article->excerpt) > 150 ? '...' : ''); ?></p>
                                    <?php } ?>

                                    <div class="blog-card-footer">
                                        <span>
                                            <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($article->published_at)); ?>
                                        </span>
                                        <span>
                                            <i class="fa fa-eye"></i> <?php echo $article->views_count; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1) { ?>
            <div class="text-center" style="margin-top: 30px;">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="<?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('dietetic/portal/blog?page=' . $i . ($current_category ? '&category=' . $current_category : '')); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>

<?php $this->load->view('portal/includes/portal_footer'); ?>
