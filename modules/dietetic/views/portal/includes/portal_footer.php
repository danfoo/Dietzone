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
                // Close notifications if open
                const notificationPanel = document.getElementById('notificationPanel');
                if (notificationPanel && notificationPanel.classList.contains('active')) {
                    notificationPanel.classList.remove('active');
                }
                openMenu();
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
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationClose = document.getElementById('notificationClose');

        // Load notifications on page load
        function loadNotifications() {
            // Check if notification panel exists before loading
            if (!document.getElementById('notificationPanel')) {
                return;
            }

            fetch('<?php echo site_url("dietetic/portal/get_notifications"); ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displayNotifications(data.notifications);
                        updateNotificationBadge(data.unread_count);
                    } else {
                        console.log('Notifications: ' + (data.message || data.info || 'Not available'));
                    }
                })
                .catch(error => {
                    // Silently fail - notifications are not critical
                    console.log('Notifications not loaded:', error.message);
                });
        }

        // Display notifications in the panel
        function displayNotifications(notifications) {
            const notificationContent = document.querySelector('.notification-panel-content');

            if (!notifications || notifications.length === 0) {
                notificationContent.innerHTML = '<div class="notification-empty"><i class="fa fa-bell-slash"></i><p>Aucune notification</p></div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const unreadClass = notification.is_read ? '' : 'unread';
                html += `
                    <div class="notification-item ${unreadClass}" data-notification-id="${notification.id}">
                        <button class="notification-item-delete" onclick="deleteNotification(this)">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="notification-item-header">
                            <div class="notification-item-icon">
                                <i class="fa ${notification.icon}"></i>
                            </div>
                            <div class="notification-item-title">${notification.title}</div>
                            <div class="notification-item-time">${notification.time_ago}</div>
                        </div>
                        <div class="notification-item-message">${notification.message}</div>
                    </div>
                `;
            });

            notificationContent.innerHTML = html;
        }

        // Load notifications when page loads
        document.addEventListener('DOMContentLoaded', loadNotifications);

        function openNotifications() {
            notificationPanel.classList.add('active');
            menuOverlay.classList.add('active');
        }

        function closeNotifications() {
            notificationPanel.classList.remove('active');
            if (!slideMenu.classList.contains('active')) {
                menuOverlay.classList.remove('active');
            }
        }

        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                if (notificationPanel.classList.contains('active')) {
                    closeNotifications();
                } else {
                    // Close menu if open
                    if (slideMenu.classList.contains('active')) {
                        closeMenu();
                    }
                    openNotifications();
                }
            });

            // Add touch feedback for notification button
            notificationBtn.addEventListener('touchstart', function() {
                this.style.opacity = '0.7';
            });
            notificationBtn.addEventListener('touchend', function() {
                this.style.opacity = '';
            });
        }

        if (notificationClose) {
            notificationClose.addEventListener('click', closeNotifications);
        }

        // Close notifications when clicking overlay
        menuOverlay.addEventListener('click', function() {
            if (notificationPanel.classList.contains('active')) {
                closeNotifications();
            } else {
                closeMenu();
            }
        });

        // Close menu or notifications on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (notificationPanel && notificationPanel.classList.contains('active')) {
                    closeNotifications();
                } else if (slideMenu.classList.contains('active')) {
                    closeMenu();
                }
            }
        });

        // ============================================
        // DELETE NOTIFICATION FUNCTIONALITY
        // ============================================
        window.deleteNotification = function(button) {
            const notificationItem = button.closest('.notification-item');
            const notificationId = notificationItem.getAttribute('data-notification-id');

            // Animation de suppression
            notificationItem.style.transform = 'translateX(100%)';
            notificationItem.style.opacity = '0';

            // Call API to delete notification
            fetch('<?php echo site_url("dietetic/portal/delete_notification"); ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({notification_id: notificationId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer l'élément après l'animation
                    setTimeout(function() {
                        notificationItem.remove();

                        // Vérifier s'il reste des notifications
                        const remainingNotifications = document.querySelectorAll('.notification-item');
                        if (remainingNotifications.length === 0) {
                            // Afficher le message "Aucune notification"
                            const notificationContent = document.querySelector('.notification-panel-content');
                            notificationContent.innerHTML = '<div class="notification-empty"><i class="fa fa-bell-slash"></i><p>Aucune notification</p></div>';
                        }

                        // Mettre à jour le badge
                        if (data.unread_count !== undefined) {
                            updateNotificationBadge(data.unread_count);
                        }
                    }, 300);
                } else {
                    // Annuler l'animation si la suppression échoue
                    notificationItem.style.transform = '';
                    notificationItem.style.opacity = '';
                    console.error('Failed to delete notification:', data.message);
                }
            })
            .catch(error => {
                // Annuler l'animation en cas d'erreur
                notificationItem.style.transform = '';
                notificationItem.style.opacity = '';
                console.error('Error deleting notification:', error);
            });
        };

        // Mettre à jour le badge de notifications
        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-badge');

            if (badge) {
                // Use provided count or count from DOM
                const unreadCount = count !== undefined ? count : document.querySelectorAll('.notification-item.unread').length;

                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
