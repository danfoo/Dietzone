<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Portal Header -->
<div class="portal-header">
    <div class="portal-header-content">
        <!-- Logo -->
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="portal-logo">
            <?php
            $logo_path = get_option('company_logo_dark');
            if (!$logo_path || !file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                $logo_path = get_option('company_logo');
            }

            if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
            ?>
                <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="<?php echo get_option('companyname'); ?>">
            <?php } else { ?>
                <div class="portal-logo-text">
                    <i class="fa fa-heartbeat"></i>
                    <span><?php echo get_option('companyname') ? get_option('companyname') : 'Dietetic'; ?></span>
                </div>
            <?php } ?>
        </a>

        <!-- Desktop Navigation -->
        <nav class="portal-nav-desktop">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="<?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fa fa-home"></i> Accueil
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="<?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i> Repas
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="<?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
                <i class="fa fa-clipboard-list"></i> Enquêtes
            </a>
            <?php } ?>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="<?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
                <i class="fa fa-heartbeat"></i> Mesures
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="<?php echo isset($active_page) && $active_page == 'dietitians' ? 'active' : ''; ?>">
                <i class="fa fa-user-md"></i> Diététicien
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="<?php echo isset($active_page) && $active_page == 'notifications' ? 'active' : ''; ?>">
                <i class="fa fa-bell"></i> Notifications
            </a>
            <?php } ?>
            <a href="<?php echo site_url('clients/profile'); ?>" class="<?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
                <i class="fa fa-user"></i> Profil
            </a>
        </nav>

        <!-- Hamburger Menu (Mobile) -->
        <div class="hamburger-menu" id="hamburgerMenu">
            <div class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu Panel -->
<div class="mobile-menu-panel" id="mobileMenuPanel">
    <div class="mobile-menu-items">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="mobile-menu-item <?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fa fa-home"></i>
            <span>Accueil</span>
        </a>
        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
            <i class="fa fa-cutlery"></i>
            <span>Plans de Repas</span>
        </a>
        <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
            <i class="fa fa-clipboard-list"></i>
            <span>Enquêtes Alimentaires</span>
        </a>
        <?php } ?>
        <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
            <i class="fa fa-heartbeat"></i>
            <span>Mes Mesures</span>
        </a>
        <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'dietitians' ? 'active' : ''; ?>">
            <i class="fa fa-user-md"></i>
            <span>Mon Diététicien</span>
        </a>
        <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
        <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'notifications' ? 'active' : ''; ?>">
            <i class="fa fa-bell"></i>
            <span>Notifications</span>
        </a>
        <?php } ?>
        <a href="<?php echo site_url('clients/profile'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
            <i class="fa fa-user"></i>
            <span>Mon Profil</span>
        </a>
        <a href="<?php echo site_url('authentication/logout'); ?>" class="mobile-menu-item">
            <i class="fa fa-sign-out"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>

<!-- Bottom Navigation (Mobile Only) -->
<nav class="bottom-nav">
    <div class="bottom-nav-items">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item <?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fa fa-home"></i>
            <span>Accueil</span>
        </a>
        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
            <i class="fa fa-cutlery"></i>
            <span>Repas</span>
        </a>
        <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
            <i class="fa fa-clipboard-list"></i>
            <span>Enquêtes</span>
        </a>
        <?php } ?>
        <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
            <i class="fa fa-plus-circle"></i>
            <span>Mesure</span>
        </a>
        <a href="<?php echo site_url('clients/profile'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
            <i class="fa fa-user"></i>
            <span>Profil</span>
        </a>
    </div>
</nav>
