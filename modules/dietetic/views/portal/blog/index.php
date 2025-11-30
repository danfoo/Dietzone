<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
/* ============================================
   MODERN BLOG PAGE - Mobile First Design
   ============================================ */

:root {
    --primary-color: #01807B;
    --primary-dark: #026660;
    --text-dark: #2c3e50;
    --text-muted: #6c757d;
    --border-color: #e9ecef;
    --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
    --card-shadow-hover: 0 8px 20px rgba(0,0,0,0.12);
}

.blog-page-container {
    padding: 0;
    margin: 0;
}

/* ============================================
   SEARCH BAR - Modern sticky design
   ============================================ */
.blog-search-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 20px 16px;
    margin: -20px -15px 24px -15px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.15);
}

.blog-search-container {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
}

.blog-search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 24px;
    padding: 4px 4px 4px 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.blog-search-input-wrapper:focus-within {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.blog-search-icon {
    color: var(--text-muted);
    font-size: 18px;
    margin-right: 12px;
}

.blog-search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 15px;
    padding: 10px 0;
    color: var(--text-dark);
    background: transparent;
}

.blog-search-input::placeholder {
    color: #adb5bd;
}

.blog-search-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
}

.blog-search-btn:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}

/* ============================================
   CATEGORY DROPDOWN - Modern design
   ============================================ */
.blog-filter-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    align-items: center;
    flex-wrap: wrap;
}

.category-dropdown-container {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.category-dropdown-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    color: var(--text-dark);
    transition: all 0.3s;
    font-size: 15px;
}

.category-dropdown-btn:hover {
    border-color: var(--primary-color);
    box-shadow: var(--card-shadow);
}

.category-dropdown-btn.active {
    border-color: var(--primary-color);
    background: #f0fffe;
}

.category-dropdown-label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.category-dropdown-icon {
    font-size: 16px;
    transition: transform 0.3s;
}

.category-dropdown-btn.active .category-dropdown-icon {
    transform: rotate(180deg);
}

.category-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
    z-index: 50;
}

.category-dropdown-menu.active {
    max-height: 400px;
    opacity: 1;
    visibility: visible;
}

.category-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: var(--text-dark);
}

.category-dropdown-item:hover {
    background: #f8f9fa;
    color: var(--primary-color);
}

.category-dropdown-item.selected {
    background: #f0fffe;
    color: var(--primary-color);
    font-weight: 600;
}

.category-item-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.category-item-name {
    flex: 1;
    font-size: 14px;
}

/* ============================================
   BLOG CARDS - Modern mobile design
   ============================================ */
.blog-articles-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 32px;
}

.blog-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
    display: block;
}

.blog-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
    text-decoration: none;
}

.blog-card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
}

.blog-card-content {
    padding: 16px;
}

.blog-card-category {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    margin-bottom: 12px;
}

.blog-card-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 10px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-excerpt {
    font-size: 14px;
    color: var(--text-muted);
    margin-bottom: 12px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid var(--border-color);
    font-size: 12px;
    color: var(--text-muted);
}

.blog-card-footer i {
    margin-right: 4px;
}

/* ============================================
   PAGINATION - Modern design
   ============================================ */
.blog-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 32px;
    padding: 20px 0;
}

.pagination-btn {
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: white;
    border: 2px solid var(--border-color);
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    cursor: pointer;
}

.pagination-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
    text-decoration: none;
    transform: translateY(-2px);
}

.pagination-btn.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination-btn.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-ellipsis {
    padding: 0 8px;
    color: var(--text-muted);
}

/* ============================================
   EMPTY STATE
   ============================================ */
.blog-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    margin: 32px 0;
}

.blog-empty-state i {
    font-size: 64px;
    color: var(--border-color);
    margin-bottom: 20px;
    display: block;
}

.blog-empty-state h3 {
    color: var(--text-muted);
    font-size: 18px;
    margin: 0;
}

/* ============================================
   RESPONSIVE - Tablet & Desktop
   ============================================ */
@media (min-width: 576px) {
    .blog-articles-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .blog-card-image {
        height: 220px;
    }
}

@media (min-width: 992px) {
    .blog-page-container {
        padding: 0 20px;
    }

    .blog-search-header {
        padding: 24px 20px;
        margin: -20px -35px 32px -35px;
    }

    .blog-articles-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .blog-card-image {
        height: 200px;
    }

    .blog-card-content {
        padding: 20px;
    }
}
</style>

<div class="blog-page-container">
    <!-- Modern Search Header -->
    <div class="blog-search-header">
        <div class="blog-search-container">
            <form action="<?php echo site_url('dietetic/portal/blog_search'); ?>" method="get">
                <div class="blog-search-input-wrapper">
                    <i class="fa fa-search blog-search-icon"></i>
                    <input
                        type="text"
                        name="q"
                        class="blog-search-input"
                        placeholder="Rechercher des articles, conseils..."
                        value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                    >
                    <button type="submit" class="blog-search-btn">
                        <i class="fa fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Category Dropdown Filter -->
            <div class="blog-filter-bar">
                <div class="category-dropdown-container">
                    <button type="button" class="category-dropdown-btn" id="categoryDropdownBtn">
                        <span class="category-dropdown-label">
                            <?php if ($current_category): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if ($cat->slug == $current_category): ?>
                                        <i class="fa <?php echo $cat->icon; ?>" style="color: <?php echo $cat->color; ?>;"></i>
                                        <span><?php echo htmlspecialchars($cat->name); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <i class="fa fa-th-large"></i>
                                <span>Toutes les catégories</span>
                            <?php endif; ?>
                        </span>
                        <i class="fa fa-chevron-down category-dropdown-icon"></i>
                    </button>

                    <div class="category-dropdown-menu" id="categoryDropdownMenu">
                        <a href="<?php echo site_url('dietetic/portal/blog'); ?>"
                           class="category-dropdown-item <?php echo !$current_category ? 'selected' : ''; ?>">
                            <div class="category-item-icon" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                                <i class="fa fa-th-large"></i>
                            </div>
                            <span class="category-item-name">Toutes les catégories</span>
                        </a>

                        <?php foreach ($categories as $cat): ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?category=' . $cat->slug); ?>"
                               class="category-dropdown-item <?php echo $current_category == $cat->slug ? 'selected' : ''; ?>">
                                <div class="category-item-icon" style="background: <?php echo $cat->color; ?>;">
                                    <i class="fa <?php echo $cat->icon; ?>"></i>
                                </div>
                                <span class="category-item-name"><?php echo htmlspecialchars($cat->name); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Articles Grid -->
            <?php if (empty($articles)): ?>
                <div class="blog-empty-state">
                    <i class="fa fa-newspaper-o"></i>
                    <h3>Aucun article trouvé</h3>
                    <p style="color: #adb5bd; margin-top: 8px;">Essayez de changer de catégorie ou de rechercher autre chose</p>
                </div>
            <?php else: ?>
                <div class="blog-articles-grid">
                    <?php foreach ($articles as $article): ?>
                        <a href="<?php echo site_url('dietetic/portal/blog_article/' . $article->slug); ?>" class="blog-card">
                            <?php if ($article->featured_image): ?>
                                <img src="<?php echo base_url('uploads/blog/' . $article->featured_image); ?>"
                                     alt="<?php echo htmlspecialchars($article->title); ?>"
                                     class="blog-card-image">
                            <?php else: ?>
                                <div class="blog-card-image"></div>
                            <?php endif; ?>

                            <div class="blog-card-content">
                                <?php if ($article->category && isset($article->category_details)): ?>
                                    <span class="blog-card-category" style="background-color: <?php echo $article->category_details->color; ?>;">
                                        <i class="fa <?php echo $article->category_details->icon; ?>"></i>
                                        <?php echo htmlspecialchars($article->category_details->name); ?>
                                    </span>
                                <?php endif; ?>

                                <h3 class="blog-card-title"><?php echo htmlspecialchars($article->title); ?></h3>

                                <?php if ($article->excerpt): ?>
                                    <p class="blog-card-excerpt"><?php echo htmlspecialchars(substr($article->excerpt, 0, 120)) . (strlen($article->excerpt) > 120 ? '...' : ''); ?></p>
                                <?php endif; ?>

                                <div class="blog-card-footer">
                                    <span>
                                        <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($article->published_at)); ?>
                                    </span>
                                    <span>
                                        <i class="fa fa-eye"></i> <?php echo $article->views_count; ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Modern Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="blog-pagination">
                        <!-- Previous Button -->
                        <?php if ($current_page > 1): ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?page=' . ($current_page - 1) . ($current_category ? '&category=' . $current_category : '')); ?>"
                               class="pagination-btn">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn disabled">
                                <i class="fa fa-chevron-left"></i>
                            </span>
                        <?php endif; ?>

                        <!-- Page Numbers (Smart pagination) -->
                        <?php
                        $show_pages = 5; // Number of pages to show
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $start_page + $show_pages - 1);

                        if ($end_page - $start_page < $show_pages - 1) {
                            $start_page = max(1, $end_page - $show_pages + 1);
                        }

                        // First page
                        if ($start_page > 1):
                        ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?page=1' . ($current_category ? '&category=' . $current_category : '')); ?>"
                               class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?page=' . $i . ($current_category ? '&category=' . $current_category : '')); ?>"
                               class="pagination-btn <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Last page -->
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?page=' . $total_pages . ($current_category ? '&category=' . $current_category : '')); ?>"
                               class="pagination-btn"><?php echo $total_pages; ?></a>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?php echo site_url('dietetic/portal/blog?page=' . ($current_page + 1) . ($current_category ? '&category=' . $current_category : '')); ?>"
                               class="pagination-btn">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="pagination-btn disabled">
                                <i class="fa fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Category Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('categoryDropdownBtn');
    const dropdownMenu = document.getElementById('categoryDropdownMenu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownBtn.classList.toggle('active');
            dropdownMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownBtn.classList.remove('active');
                dropdownMenu.classList.remove('active');
            }
        });

        // Close dropdown when selecting a category
        const categoryItems = dropdownMenu.querySelectorAll('.category-dropdown-item');
        categoryItems.forEach(item => {
            item.addEventListener('click', function() {
                dropdownBtn.classList.remove('active');
                dropdownMenu.classList.remove('active');
            });
        });
    }
});
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
