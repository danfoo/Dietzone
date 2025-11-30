<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('dietetic/blog'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('back_to_blog_articles'); ?>
                            </a>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal" onclick="resetCategoryForm()">
                                <i class="fa fa-plus"></i> <?php echo _l('new_category'); ?>
                            </button>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <table class="table dt-table table-blog-categories" data-order-col="5" data-order-type="asc">
                            <thead>
                                <tr>
                                    <th><?php echo _l('blog_category_order'); ?></th>
                                    <th><?php echo _l('blog_category_name'); ?></th>
                                    <th><?php echo _l('blog_category_slug'); ?></th>
                                    <th><?php echo _l('blog_category_color'); ?></th>
                                    <th><?php echo _l('blog_category_icon'); ?></th>
                                    <th><?php echo _l('dietetic_description'); ?></th>
                                    <th><?php echo _l('dietetic_actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category) { ?>
                                    <tr>
                                        <td><?php echo $category->order; ?></td>
                                        <td>
                                            <span class="label" style="background-color: <?php echo htmlspecialchars($category->color); ?>; color: white;">
                                                <i class="fa <?php echo htmlspecialchars($category->icon); ?>"></i>
                                                <?php echo htmlspecialchars($category->name); ?>
                                            </span>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($category->slug); ?></code></td>
                                        <td>
                                            <input type="color" value="<?php echo htmlspecialchars($category->color); ?>" disabled style="border: none; width: 50px; height: 30px;">
                                        </td>
                                        <td>
                                            <i class="fa <?php echo htmlspecialchars($category->icon); ?>" style="font-size: 20px;"></i>
                                            <small class="text-muted"><?php echo htmlspecialchars($category->icon); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($category->description ?? '-'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-default btn-icon btn-sm" onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <a href="<?php echo admin_url('dietetic/blog/delete_category/' . $category->id); ?>"
                                               class="btn btn-danger btn-icon btn-sm _delete"
                                               onclick="return confirm('<?php echo _l('confirm_delete_category'); ?>');">
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="categoryModalTitle"><?php echo _l('new_category'); ?></h4>
            </div>
            <?php echo form_open(admin_url('dietetic/blog/categories'), ['id' => 'category-form']); ?>
            <div class="modal-body">
                <input type="hidden" name="action" id="category-action" value="add">
                <input type="hidden" name="category_id" id="category-id" value="">

                <div class="form-group">
                    <label for="category-name"><?php echo _l('blog_category_name'); ?> *</label>
                    <input type="text" class="form-control" id="category-name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="category-slug"><?php echo _l('blog_category_slug'); ?> *</label>
                    <input type="text" class="form-control" id="category-slug" name="slug" required>
                    <small class="text-muted"><?php echo _l('slug_auto_generated'); ?></small>
                </div>

                <div class="form-group">
                    <label for="category-description"><?php echo _l('blog_category_description'); ?></label>
                    <textarea class="form-control" id="category-description" name="description" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category-color"><?php echo _l('blog_category_color'); ?></label>
                            <input type="color" class="form-control" id="category-color" name="color" value="#01807B">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category-order"><?php echo _l('blog_category_order'); ?></label>
                            <input type="number" class="form-control" id="category-order" name="order" value="0" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category-icon"><?php echo _l('blog_category_icon'); ?></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-newspaper-o" id="icon-preview"></i>
                        </span>
                        <input type="text" class="form-control" id="category-icon" name="icon" value="fa-newspaper-o" placeholder="fa-newspaper-o">
                    </div>
                    <small class="text-muted">
                        <?php echo _l('font_awesome_icons'); ?>:
                        <a href="https://fontawesome.com/v4/icons/" target="_blank">fa-apple</a>,
                        <a href="https://fontawesome.com/v4/icons/" target="_blank">fa-cutlery</a>,
                        <a href="https://fontawesome.com/v4/icons/" target="_blank">fa-heart</a>, etc.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function resetCategoryForm() {
    document.getElementById('category-form').reset();
    document.getElementById('category-action').value = 'add';
    document.getElementById('category-id').value = '';
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('new_category'); ?>';
    document.getElementById('icon-preview').className = 'fa fa-newspaper-o';
}

function editCategory(category) {
    document.getElementById('category-action').value = 'edit';
    document.getElementById('category-id').value = category.id;
    document.getElementById('category-name').value = category.name;
    document.getElementById('category-slug').value = category.slug;
    document.getElementById('category-description').value = category.description || '';
    document.getElementById('category-color').value = category.color;
    document.getElementById('category-icon').value = category.icon;
    document.getElementById('category-order').value = category.order;
    document.getElementById('icon-preview').className = 'fa ' + category.icon;
    document.getElementById('categoryModalTitle').textContent = '<?php echo _l('edit_category'); ?>';

    $('#categoryModal').modal('show');
}

// Auto-generate slug from name
document.getElementById('category-name').addEventListener('input', function() {
    var name = this.value;
    var slug = name.toLowerCase()
        .replace(/[éèê]/g, 'e')
        .replace(/[àâ]/g, 'a')
        .replace(/[ùû]/g, 'u')
        .replace(/[îï]/g, 'i')
        .replace(/[ô]/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('category-slug').value = slug;
});

// Update icon preview
document.getElementById('category-icon').addEventListener('input', function() {
    var icon = this.value.replace('fa-', '');
    document.getElementById('icon-preview').className = 'fa fa-' + icon;
});
</script>

<?php init_tail(); ?>
