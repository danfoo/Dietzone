<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.dietetic-recipes-header {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.dietetic-recipes-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 15px;
}

.dietetic-recipes-header h1 i {
    font-size: 32px;
}

.dietetic-recipes-header .header-actions {
    margin-top: 15px;
}

.dietetic-recipes-header .btn-new-recipe {
    background: white;
    color: #e74c3c;
    border: none;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.dietetic-recipes-header .btn-new-recipe:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.dietetic-stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #e74c3c;
    margin-bottom: 20px;
}

.dietetic-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.dietetic-stat-card.pending {
    border-left-color: #f39c12;
}

.dietetic-stat-card.approved {
    border-left-color: #27ae60;
}

.dietetic-stat-card.rejected {
    border-left-color: #e74c3c;
}

.dietetic-stat-card .stat-icon {
    font-size: 36px;
    margin-bottom: 10px;
}

.dietetic-stat-card.pending .stat-icon {
    color: #f39c12;
}

.dietetic-stat-card.approved .stat-icon {
    color: #27ae60;
}

.dietetic-stat-card.rejected .stat-icon {
    color: #e74c3c;
}

.dietetic-stat-card h3 {
    margin: 0 0 5px 0;
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

.dietetic-stat-card p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
}

.recipe-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    display: flex;
    gap: 20px;
}

.recipe-card:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.recipe-photo {
    width: 150px;
    height: 150px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.recipe-photo-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 8px;
    background: #ecf0f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #95a5a6;
    flex-shrink: 0;
}

.recipe-info {
    flex: 1;
}

.recipe-info h3 {
    margin: 0 0 10px 0;
    font-size: 20px;
}

.recipe-info h3 a {
    color: #2c3e50;
    text-decoration: none;
}

.recipe-info h3 a:hover {
    color: #e74c3c;
}

.recipe-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.recipe-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #7f8c8d;
    font-size: 13px;
}

.recipe-meta-item i {
    color: #95a5a6;
}

.recipe-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.recipe-status.pending {
    background: #fef5e7;
    color: #f39c12;
}

.recipe-status.approved {
    background: #eafaf1;
    color: #27ae60;
}

.recipe-status.rejected {
    background: #fadbd8;
    color: #e74c3c;
}

.recipe-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.filter-tabs {
    margin-bottom: 20px;
}

.filter-tabs .nav-tabs {
    border-bottom: 2px solid #ecf0f1;
}

.filter-tabs .nav-tabs li a {
    color: #7f8c8d;
    border: none;
    padding: 10px 20px;
}

.filter-tabs .nav-tabs li.active a {
    color: #e74c3c;
    border-bottom: 3px solid #e74c3c;
    background: transparent;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="dietetic-recipes-header">
                    <h1>
                        <i class="fa fa-book"></i>
                        Bibliothèque de Recettes
                    </h1>
                    <div class="header-actions">
                        <?php if (dietetic_has_permission('create')) : ?>
                            <a href="<?php echo admin_url('dietetic/recipes/create'); ?>" class="btn btn-new-recipe">
                                <i class="fa fa-plus"></i> Nouvelle Recette
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="dietetic-stat-card">
                            <div class="stat-icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <h3><?php echo $stats->total; ?></h3>
                            <p>Total Recettes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dietetic-stat-card pending">
                            <div class="stat-icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <h3><?php echo $stats->pending; ?></h3>
                            <p>En Attente</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dietetic-stat-card approved">
                            <div class="stat-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <h3><?php echo $stats->approved; ?></h3>
                            <p>Approuvées</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="dietetic-stat-card rejected">
                            <div class="stat-icon">
                                <i class="fa fa-times-circle"></i>
                            </div>
                            <h3><?php echo $stats->rejected; ?></h3>
                            <p>Rejetées</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-tabs">
                    <ul class="nav nav-tabs">
                        <li class="<?php echo $current_status == 'all' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=all'); ?>">
                                Toutes (<?php echo $stats->total; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'pending' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=pending'); ?>">
                                En Attente (<?php echo $stats->pending; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'approved' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=approved'); ?>">
                                Approuvées (<?php echo $stats->approved; ?>)
                            </a>
                        </li>
                        <li class="<?php echo $current_status == 'rejected' ? 'active' : ''; ?>">
                            <a href="<?php echo admin_url('dietetic/recipes?status=rejected'); ?>">
                                Rejetées (<?php echo $stats->rejected; ?>)
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Recipes List -->
                <?php if (empty($recipes)) : ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Aucune recette trouvée.
                    </div>
                <?php else : ?>
                    <?php foreach ($recipes as $recipe) : ?>
                        <div class="recipe-card">
                            <?php if ($recipe->main_photo) : ?>
                                <img src="<?php echo base_url($recipe->main_photo->photo_url); ?>"
                                     alt="<?php echo htmlspecialchars($recipe->name); ?>"
                                     class="recipe-photo">
                            <?php else : ?>
                                <div class="recipe-photo-placeholder">
                                    <i class="fa fa-cutlery"></i>
                                </div>
                            <?php endif; ?>

                            <div class="recipe-info">
                                <h3>
                                    <a href="<?php echo admin_url('dietetic/recipes/view/' . $recipe->id); ?>">
                                        <?php echo htmlspecialchars($recipe->name); ?>
                                    </a>
                                    <span class="recipe-status <?php echo $recipe->status; ?>">
                                        <?php
                                        $status_labels = [
                                            'pending' => 'En attente',
                                            'approved' => 'Approuvée',
                                            'rejected' => 'Rejetée'
                                        ];
                                        echo $status_labels[$recipe->status] ?? $recipe->status;
                                        ?>
                                    </span>
                                </h3>

                                <div class="recipe-meta">
                                    <div class="recipe-meta-item">
                                        <i class="fa fa-user"></i>
                                        <?php echo htmlspecialchars($recipe->dietitian_name); ?>
                                    </div>
                                    <?php if ($recipe->category) : ?>
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-tag"></i>
                                            <?php
                                            $categories = [
                                                'breakfast' => 'Petit-déjeuner',
                                                'lunch' => 'Déjeuner',
                                                'dinner' => 'Dîner',
                                                'snack' => 'Collation'
                                            ];
                                            echo $categories[$recipe->category] ?? $recipe->category;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($recipe->preparation_time) : ?>
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo $recipe->preparation_time; ?> min
                                        </div>
                                    <?php endif; ?>
                                    <div class="recipe-meta-item">
                                        <i class="fa fa-list"></i>
                                        <?php echo $recipe->ingredients_count; ?> ingrédients
                                    </div>
                                    <?php if ($recipe->ratings_count > 0) : ?>
                                        <div class="recipe-meta-item">
                                            <i class="fa fa-star" style="color: #f39c12;"></i>
                                            <?php echo number_format($recipe->average_rating, 1); ?> (<?php echo $recipe->ratings_count; ?>)
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($recipe->description) : ?>
                                    <p style="color: #7f8c8d; margin-bottom: 10px;">
                                        <?php echo nl2br(htmlspecialchars(substr($recipe->description, 0, 200))); ?>
                                        <?php if (strlen($recipe->description) > 200) echo '...'; ?>
                                    </p>
                                <?php endif; ?>

                                <div class="recipe-actions">
                                    <a href="<?php echo admin_url('dietetic/recipes/view/' . $recipe->id); ?>"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-eye"></i> Voir
                                    </a>
                                    <?php if (dietetic_has_permission('edit')) : ?>
                                        <a href="<?php echo admin_url('dietetic/recipes/edit/' . $recipe->id); ?>"
                                           class="btn btn-default btn-sm">
                                            <i class="fa fa-pencil"></i> Modifier
                                        </a>
                                    <?php endif; ?>
                                    <?php if (is_admin() && $recipe->status == 'pending') : ?>
                                        <a href="<?php echo admin_url('dietetic/recipes/approve/' . $recipe->id); ?>"
                                           class="btn btn-success btn-sm">
                                            <i class="fa fa-check"></i> Approuver
                                        </a>
                                    <?php endif; ?>
                                    <?php if (dietetic_has_permission('delete')) : ?>
                                        <a href="<?php echo admin_url('dietetic/recipes/delete/' . $recipe->id); ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                                            <i class="fa fa-trash"></i> Supprimer
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
