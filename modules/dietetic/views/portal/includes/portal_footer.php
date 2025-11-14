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

        // Store all notifications globally for filtering
        let allNotifications = [];
        let currentFilter = 'all';
        let previousUnreadCount = 0;
        let autoRefreshInterval = null;

        // Load notifications on page load
        function loadNotifications(silent = false) {
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
                        const newUnreadCount = data.unread_count;

                        // Check if there are new notifications (only if not first load and not silent)
                        if (!silent && previousUnreadCount > 0 && newUnreadCount > previousUnreadCount) {
                            const newNotificationsCount = newUnreadCount - previousUnreadCount;
                            notifyUser(newNotificationsCount);
                        }

                        previousUnreadCount = newUnreadCount;
                        allNotifications = data.notifications;
                        displayNotifications(filterNotifications(allNotifications));
                        updateNotificationBadge(newUnreadCount);
                    } else {
                        // Error but valid response - show empty state
                        console.log('Notifications: ' + (data.message || data.info || 'Not available'));
                        allNotifications = [];
                        displayNotifications([]);
                        updateNotificationBadge(0);
                    }
                })
                .catch(error => {
                    // Network or parse error - show empty state
                    console.log('Notifications not loaded:', error.message);
                    allNotifications = [];
                    displayNotifications([]);
                    updateNotificationBadge(0);
                });
        }

        // Get date category for grouping
        function getDateCategory(dateString) {
            const now = new Date();
            const notifDate = new Date(dateString);
            const diffTime = Math.abs(now - notifDate);
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

            // Reset hours for accurate day comparison
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const notifDay = new Date(notifDate.getFullYear(), notifDate.getMonth(), notifDate.getDate());
            const dayDiff = Math.floor((today - notifDay) / (1000 * 60 * 60 * 24));

            if (dayDiff === 0) {
                return { key: 'today', label: "Aujourd'hui", order: 1 };
            } else if (dayDiff === 1) {
                return { key: 'yesterday', label: 'Hier', order: 2 };
            } else if (dayDiff <= 7) {
                return { key: 'this_week', label: 'Cette semaine', order: 3 };
            } else {
                return { key: 'older', label: 'Plus ancien', order: 4 };
            }
        }

        // Display notifications in the panel
        function displayNotifications(notifications) {
            const notificationContent = document.querySelector('.notification-panel-content');

            if (!notifications || notifications.length === 0) {
                notificationContent.innerHTML = '<div class="notification-empty"><i class="fa fa-bell-slash"></i><p>Aucune notification</p></div>';
                updateMarkAllReadButton(0);
                return;
            }

            // Group notifications by date
            const grouped = {};
            let unreadCount = 0;

            notifications.forEach(notification => {
                const category = getDateCategory(notification.created_at);
                if (!grouped[category.key]) {
                    grouped[category.key] = {
                        label: category.label,
                        order: category.order,
                        notifications: []
                    };
                }
                grouped[category.key].notifications.push(notification);

                if (!notification.is_read) {
                    unreadCount++;
                }
            });

            // Sort groups by order and build HTML
            let html = '';
            Object.keys(grouped)
                .sort((a, b) => grouped[a].order - grouped[b].order)
                .forEach(groupKey => {
                    const group = grouped[groupKey];

                    // Add date separator
                    html += `<div class="notification-date-separator"><span>${group.label}</span></div>`;

                    // Add notifications in this group
                    group.notifications.forEach(notification => {
                        const unreadClass = notification.is_read ? '' : 'unread';
                        const clickableClass = notification.url ? 'clickable' : '';
                        const cursorStyle = notification.url ? 'cursor: pointer;' : '';

                        html += `
                            <div class="notification-item ${unreadClass} ${clickableClass}"
                                 data-notification-id="${notification.id}"
                                 data-url="${notification.url || ''}"
                                 data-is-read="${notification.is_read ? '1' : '0'}"
                                 style="${cursorStyle}"
                                 onclick="handleNotificationClick(this, event)">
                                <button class="notification-item-delete" onclick="deleteNotification(this, event)">
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
                });

            notificationContent.innerHTML = html;
            updateMarkAllReadButton(unreadCount);
        }

        // Update "Mark all as read" button visibility
        function updateMarkAllReadButton(unreadCount) {
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            if (markAllReadBtn) {
                if (unreadCount > 0) {
                    markAllReadBtn.classList.remove('hidden');
                } else {
                    markAllReadBtn.classList.add('hidden');
                }
            }
        }

        // Filter notifications based on current filter
        function filterNotifications(notifications) {
            if (currentFilter === 'all') {
                return notifications;
            } else if (currentFilter === 'unread') {
                return notifications.filter(n => !n.is_read);
            } else if (currentFilter === 'read') {
                return notifications.filter(n => n.is_read);
            }
            return notifications;
        }

        // Apply filter and update display
        function applyFilter(filter) {
            currentFilter = filter;

            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-filter') === filter) {
                    btn.classList.add('active');
                }
            });

            // Filter and display
            const filteredNotifications = filterNotifications(allNotifications);
            displayNotifications(filteredNotifications);
        }

        // ============================================
        // AUTO-REFRESH FUNCTIONALITY
        // ============================================

        // Start auto-refresh (every 3 minutes)
        function startAutoRefresh() {
            // Clear any existing interval
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }

            // Refresh every 3 minutes (180000ms)
            autoRefreshInterval = setInterval(function() {
                loadNotifications(false); // Not silent, so we can notify about new notifications
            }, 180000);
        }

        // Stop auto-refresh
        function stopAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        // Notify user about new notifications (sound + vibration)
        function notifyUser(count) {
            // Play notification sound
            playNotificationSound();

            // Vibrate if supported (mobile)
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200]); // Vibrate pattern: 200ms, pause 100ms, 200ms
            }
        }

        // Play notification sound
        function playNotificationSound() {
            // Create a simple beep sound using Web Audio API
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 800; // Frequency in Hz
                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2);
            } catch (e) {
                console.log('Could not play notification sound:', e);
            }
        }

        // Load notifications when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifications(true); // Silent on first load
            startAutoRefresh(); // Start auto-refresh
        });

        // Add event listeners to filter buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    applyFilter(filter);
                });
            });
        });

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
        // MARK ALL AS READ FUNCTIONALITY
        // ============================================
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                // Disable button during request
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Chargement...</span>';
                this.disabled = true;

                fetch('<?php echo site_url("dietetic/portal/mark_all_notifications_read"); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update allNotifications array - mark all as read
                        allNotifications = allNotifications.map(n => {
                            return {...n, is_read: true};
                        });

                        // Mark all notification items as read in the UI
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                            item.setAttribute('data-is-read', '1');
                        });

                        // Update badge
                        updateNotificationBadge(0);

                        // Hide the button
                        updateMarkAllReadButton(0);

                        // Show success feedback
                        this.innerHTML = '<i class="fa fa-check"></i> <span>Fait!</span>';
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.disabled = false;
                        }, 1000);
                    } else {
                        // Re-enable button on error
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                        console.error('Failed to mark all as read:', data.message);
                    }
                })
                .catch(error => {
                    // Re-enable button on error
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                    console.error('Error marking all as read:', error);
                });
            });
        }

        // ============================================
        // NOTIFICATION CLICK HANDLER
        // ============================================
        window.handleNotificationClick = function(notificationElement, event) {
            // Don't trigger if clicking on delete button
            if (event.target.closest('.notification-item-delete')) {
                return;
            }

            const notificationId = notificationElement.getAttribute('data-notification-id');
            const notificationUrl = notificationElement.getAttribute('data-url');
            const isRead = notificationElement.getAttribute('data-is-read') === '1';

            // Mark as read if not already read
            if (!isRead) {
                markNotificationAsRead(notificationId, notificationElement);
            }

            // Redirect to URL if exists
            if (notificationUrl && notificationUrl !== 'null' && notificationUrl !== '') {
                setTimeout(function() {
                    window.location.href = notificationUrl;
                }, 200); // Small delay to show the read state change
            }
        };

        // Mark single notification as read
        function markNotificationAsRead(notificationId, notificationElement) {
            fetch('<?php echo site_url("dietetic/portal/mark_notification_read"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({notification_id: notificationId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    notificationElement.classList.remove('unread');
                    notificationElement.setAttribute('data-is-read', '1');

                    // Update the notification in allNotifications array
                    const notification = allNotifications.find(n => n.id == notificationId);
                    if (notification) {
                        notification.is_read = true;
                    }

                    // Update badge
                    if (data.unread_count !== undefined) {
                        updateNotificationBadge(data.unread_count);
                    }

                    // Update "Mark all as read" button
                    const unreadCount = allNotifications.filter(n => !n.is_read).length;
                    updateMarkAllReadButton(unreadCount);
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // ============================================
        // DELETE NOTIFICATION FUNCTIONALITY
        // ============================================
        window.deleteNotification = function(button, event) {
            // Stop propagation to prevent triggering notification click
            if (event) {
                event.stopPropagation();
            }

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
                    // Remove from allNotifications array
                    allNotifications = allNotifications.filter(n => n.id != notificationId);

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

                        // Update "Mark all as read" button
                        const unreadCount = allNotifications.filter(n => !n.is_read).length;
                        updateMarkAllReadButton(unreadCount);
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
