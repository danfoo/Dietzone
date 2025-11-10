    </div> <!-- Close content-container -->

    <!-- FOOTER MAGNIFIQUE -->
    <footer class="app-footer">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="footer-item <?php echo (!isset($active_page) || $active_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fa fa-home footer-icon"></i>
            <span class="footer-label">Accueil</span>
        </a>

        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'meal_plans') ? 'active' : ''; ?>">
            <i class="fa fa-cutlery footer-icon"></i>
            <span class="footer-label">Repas</span>
        </a>

        <?php
        // Check if food surveys feature is enabled
        $CI_footer = &get_instance();
        if ($CI_footer->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
        ?>
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'food_surveys') ? 'active' : ''; ?>">
            <i class="fa fa-list-alt footer-icon"></i>
            <span class="footer-label">Enquêtes</span>
        </a>
        <?php } else { ?>
        <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'add_measurement') ? 'active' : ''; ?>">
            <i class="fa fa-plus-circle footer-icon"></i>
            <span class="footer-label">Mesure</span>
        </a>
        <?php } ?>

        <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'my_dietitians') ? 'active' : ''; ?>">
            <i class="fa fa-user-md footer-icon"></i>
            <span class="footer-label">Contact</span>
        </a>

        <a href="<?php echo site_url('clients/profile'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'profile') ? 'active' : ''; ?>">
            <i class="fa fa-user footer-icon"></i>
            <span class="footer-label">Profil</span>
        </a>
    </footer>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Define site_url for dietetic_portal.js
        var site_url = '<?php echo site_url(); ?>';
    </script>
    <script src="<?php echo module_dir_url('dietetic', 'assets/js/dietetic_portal.js'); ?>"></script>
    <script>
        // ============================================
        // MENU TOGGLE FUNCTIONALITY
        // ============================================
        const menuToggle = document.getElementById('menuToggle');
        const slideMenu = document.getElementById('slideMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        function openMenu() {
            slideMenu.classList.add('active');
            menuOverlay.classList.add('active');
            menuToggle.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            slideMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            menuToggle.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle.addEventListener('click', function() {
            if (slideMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        menuOverlay.addEventListener('click', closeMenu);

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && slideMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        // Prevent body scroll when menu is open
        slideMenu.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        });

        // Touch feedback for footer items
        document.querySelectorAll('.footer-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.opacity = '0.7';
            });
            element.addEventListener('touchend', function() {
                this.style.opacity = '';
            });
        });

        // ============================================
        // NOTIFICATION BUTTON FUNCTIONALITY
        // ============================================
        const notificationBtn = document.getElementById('notificationBtn');

        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                // Check if notification preferences page exists
                <?php
                $CI_notif = &get_instance();
                if ($CI_notif->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
                ?>
                    window.location.href = '<?php echo site_url('dietetic/portal/notification_preferences'); ?>';
                <?php } else { ?>
                    // Fallback: show simple alert or redirect to profile
                    alert('Vous avez 3 nouvelles notifications');
                    // Or redirect to profile/dashboard
                    // window.location.href = '<?php echo site_url('dietetic/portal'); ?>';
                <?php } ?>
            });

            // Add touch feedback for notification button
            notificationBtn.addEventListener('touchstart', function() {
                this.style.opacity = '0.7';
            });
            notificationBtn.addEventListener('touchend', function() {
                this.style.opacity = '';
            });
        }
    </script>
</body>
</html>
