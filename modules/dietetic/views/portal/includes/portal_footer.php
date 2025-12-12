    </div> <!-- Close content-container -->

    <!-- FOOTER MAGNIFIQUE - ULTRA MODERN -->
    <footer class="app-footer">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="footer-item <?php echo (!isset($active_page) || $active_page == 'dashboard') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">home</span>
            </div>
            <span class="footer-label">Accueil</span>
        </a>

        <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'recipes') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">menu_book</span>
            </div>
            <span class="footer-label">Recettes</span>
        </a>

        <?php
        // Check if food surveys feature is enabled
        $CI_footer = &get_instance();
        if ($CI_footer->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
        ?>
        <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'food_surveys') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">assignment</span>
            </div>
            <span class="footer-label">Enquêtes</span>
        </a>
        <?php } else { ?>
        <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'add_measurement') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">add_circle</span>
            </div>
            <span class="footer-label">Mesure</span>
        </a>
        <?php } ?>

        <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'my_dietitians') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">stethoscope</span>
            </div>
            <span class="footer-label">Coach</span>
        </a>

        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="footer-item <?php echo (isset($active_page) && $active_page == 'meal_plans') ? 'active' : ''; ?>">
            <div class="footer-icon-container">
                <span class="material-symbols-rounded footer-icon">calendar_today</span>
            </div>
            <span class="footer-label">Plans</span>
        </a>
    </footer>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Define site_url for dietetic_portal.js
        var site_url = '<?php echo site_url(); ?>';

        // ============================================
        // CSRF TOKEN HELPER FUNCTIONS
        // ============================================
        // Get CSRF token dynamically from meta tag or cookie
        function getCSRFToken() {
            // Try to get from meta tag first
            const tokenNameMeta = document.querySelector('meta[name="csrf-token-name"]');
            const tokenHashMeta = document.querySelector('meta[name="csrf-token-hash"]');

            if (tokenNameMeta && tokenHashMeta) {
                return {
                    name: tokenNameMeta.getAttribute('content'),
                    hash: tokenHashMeta.getAttribute('content')
                };
            }

            // Fallback: Try to get from cookie
            const tokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i].trim();
                if (cookie.startsWith(tokenName + '=')) {
                    return {
                        name: tokenName,
                        hash: cookie.substring(tokenName.length + 1)
                    };
                }
            }

            // Last resort: Use hardcoded values (may be stale)
            return {
                name: '<?php echo $this->security->get_csrf_token_name(); ?>',
                hash: '<?php echo $this->security->get_csrf_hash(); ?>'
            };
        }

        // Update CSRF token meta tags with new values from server response
        function updateCSRFToken(tokenName, tokenHash) {
            if (!tokenName || !tokenHash) return;

            const tokenNameMeta = document.querySelector('meta[name="csrf-token-name"]');
            const tokenHashMeta = document.querySelector('meta[name="csrf-token-hash"]');

            if (tokenNameMeta) {
                tokenNameMeta.setAttribute('content', tokenName);
            }
            if (tokenHashMeta) {
                tokenHashMeta.setAttribute('content', tokenHash);
            }
        }
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
        let currentFilter = 'unread'; // Default to unread notifications
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
                    console.log('📥 [NOTIF] API Response:', data);

                    if (data.success) {
                        const newUnreadCount = data.unread_count;
                        console.log('✅ [NOTIF] Success! Found ' + data.notifications.length + ' notifications, unread: ' + newUnreadCount);

                        // Check if there are new notifications (only if not first load and not silent)
                        if (!silent && previousUnreadCount > 0 && newUnreadCount > previousUnreadCount) {
                            const newNotificationsCount = newUnreadCount - previousUnreadCount;
                            notifyUser(newNotificationsCount);
                        }

                        previousUnreadCount = newUnreadCount;
                        allNotifications = data.notifications;

                        const filtered = filterNotifications(allNotifications);
                        console.log('🔍 [NOTIF] Filtered notifications (filter=' + currentFilter + '):', filtered.length);

                        displayNotifications(filtered);
                        updateNotificationBadge(newUnreadCount);
                    } else {
                        // Error but valid response - show empty state
                        console.log('❌ [NOTIF] API returned error:', data.message || data.info || 'Not available');
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
            console.log('🎨 [NOTIF] displayNotifications called with', notifications ? notifications.length : 0, 'notifications');

            const notificationContent = document.querySelector('.notification-panel-content');

            if (!notificationContent) {
                console.error('❌ [NOTIF] notification-panel-content element not found!');
                return;
            }

            console.log('✅ [NOTIF] Found notification-panel-content element');

            if (!notifications || notifications.length === 0) {
                console.log('⚠️ [NOTIF] No notifications to display, showing empty state');
                notificationContent.innerHTML = '<div class="notification-empty"><i class="fa fa-bell-slash"></i><p>Aucune notification</p></div>';
                updateMarkAllReadButton(0);
                return;
            }

            console.log('📝 [NOTIF] Building HTML for', notifications.length, 'notifications');

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
                    let notificationIndex = 0;
                    group.notifications.forEach(notification => {
                        const unreadClass = notification.is_read ? '' : 'unread';
                        const clickableClass = notification.url ? 'clickable' : '';
                        const cursorStyle = notification.url ? 'cursor: pointer;' : '';
                        const animationDelay = `animation-delay: ${notificationIndex * 0.05}s;`;
                        notificationIndex++;

                        html += `
                            <div class="notification-item ${unreadClass} ${clickableClass}"
                                 data-notification-id="${notification.id}"
                                 data-type="${notification.type || 'info'}"
                                 data-url="${notification.url || ''}"
                                 data-is-read="${notification.is_read ? '1' : '0'}"
                                 style="${cursorStyle} ${animationDelay}"
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
            if (currentFilter === 'unread') {
                return notifications.filter(n => !n.is_read);
            } else if (currentFilter === 'read') {
                return notifications.filter(n => n.is_read);
            }
            // Default to unread if filter is invalid
            return notifications.filter(n => !n.is_read);
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
        document.addEventListener('DOMContentLoaded', function() {
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                // Disable button during request
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Chargement...</span>';
                this.disabled = true;

                fetch('<?php echo site_url("dietetic/portal/mark_all_notifications_read"); ?>', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
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
        });

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
            fetch('<?php echo site_url("dietetic/portal/mark_notification_read"); ?>?notification_id=' + notificationId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
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
            fetch('<?php echo site_url("dietetic/portal/delete_notification"); ?>?notification_id=' + notificationId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
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

        // ============================================
        // FIREBASE PUSH NOTIFICATIONS
        // ============================================

        let firebaseApp;
        let messaging;
        let fcmToken = null;
        let swRegistration = null; // Store service worker registration

        // Initialize Firebase Push Notifications
        function initFirebasePush() {
            console.log('🚀 [INIT] Initializing Firebase Push Notifications...');

            // Check if browser supports notifications
            if (!('Notification' in window)) {
                console.error('❌ [INIT] This browser does not support notifications');
                return;
            }

            // Check if service workers are supported
            if (!('serviceWorker' in navigator)) {
                console.error('❌ [INIT] Service Workers are not supported');
                return;
            }

            console.log('✅ [INIT] Browser supports notifications and service workers');

            // Fetch Firebase config from server
            console.log('📡 [INIT] Fetching Firebase config from server...');
            fetch('<?php echo site_url("dietetic/portal/get_firebase_config"); ?>')
                .then(response => response.json())
                .then(data => {
                    console.log('📡 [INIT] Server response:', data);

                    if (data.success && data.config) {
                        console.log('✅ [INIT] Firebase config received');
                        console.log('📋 [INIT] Config keys:', Object.keys(data.config));

                        // Initialize Firebase
                        if (!firebase.apps.length) {
                            firebaseApp = firebase.initializeApp(data.config);
                            messaging = firebase.messaging();

                            console.log('✅ [INIT] Firebase initialized successfully');
                            console.log('✅ [INIT] Messaging instance created');

                            // Setup foreground message handler AFTER Firebase is ready
                            handleForegroundMessages();

                            // Register service worker first, then handle messaging
                            registerServiceWorker()
                                .then(() => {
                                    console.log('✅ [INIT] Service Worker ready for messaging');

                                    // Request permission if user previously granted it
                                    if (Notification.permission === 'granted') {
                                        console.log('✅ [INIT] Permission already granted, getting token...');
                                        getFirebaseToken();
                                    } else {
                                        console.log('⚠️ [INIT] Permission not granted yet, status:', Notification.permission);
                                    }
                                })
                                .catch(error => {
                                    console.error('❌ [INIT] Failed to register service worker:', error);
                                });
                        } else {
                            console.log('⚠️ [INIT] Firebase already initialized');
                        }
                    } else {
                        console.error('❌ [INIT] Firebase push notifications not configured');
                        console.error('❌ [INIT] Server message:', data.message);
                    }
                })
                .catch(error => {
                    console.error('❌ [INIT] Error fetching Firebase config:', error);
                });
        }

        // Register service worker
        function registerServiceWorker() {
            // CRITICAL: Service Worker MUST be at site root for Firebase push notifications
            // Firebase requires the SW to be at '/' scope to receive push events
            // Add version parameter to bypass cache (v3 = fixed importScripts)
            const swPath = '<?php echo base_url("firebase-messaging-sw.js?v=3"); ?>';

            return navigator.serviceWorker.register(swPath)
                .then(function(registration) {
                    console.log('✅ Service Worker registered successfully at root scope');
                    console.log('Registration:', registration);

                    // Store registration globally (will be passed to getToken later)
                    swRegistration = registration;

                    // Wait for service worker to be active
                    return navigator.serviceWorker.ready.then(() => {
                        // Pass Firebase config to service worker
                        if (registration.active) {
                            registration.active.postMessage({
                                type: 'FIREBASE_CONFIG',  // Changed from 'INIT_FIREBASE' to match SW listener
                                config: firebaseApp.options
                            });
                            console.log('✅ Firebase config sent to Service Worker');
                        }
                        return registration;
                    });
                })
                .catch(function(error) {
                    console.error('❌ Service Worker registration failed:', error);
                    throw error;
                });
        }

        // Request notification permission and get FCM token
        function requestNotificationPermission() {
            console.log('🔔 [PUSH] requestNotificationPermission called');
            console.log('🔔 [PUSH] Current permission:', Notification.permission);
            console.log('🔔 [PUSH] Firebase app initialized:', !!firebaseApp);
            console.log('🔔 [PUSH] Messaging initialized:', !!messaging);
            console.log('🔔 [PUSH] SW Registration exists:', !!swRegistration);

            // Check if Firebase is initialized
            if (!firebaseApp || !messaging) {
                console.error('❌ [PUSH] Firebase not initialized yet, waiting...');
                return new Promise((resolve, reject) => {
                    // Wait a bit for Firebase to initialize
                    setTimeout(() => {
                        if (firebaseApp && messaging) {
                            console.log('✅ [PUSH] Firebase ready after wait');
                            proceedWithPermission().then(resolve).catch(reject);
                        } else {
                            console.error('❌ [PUSH] Firebase still not ready');
                            reject(new Error('Firebase not initialized. Please refresh the page.'));
                        }
                    }, 1000);
                });
            }

            return proceedWithPermission();
        }

        function proceedWithPermission() {
            // Ensure service worker is registered first
            const swPromise = swRegistration
                ? Promise.resolve(swRegistration)
                : registerServiceWorker();

            return swPromise
                .then(() => {
                    console.log('✅ [PUSH] Service worker ready, requesting permission...');
                    return Notification.requestPermission();
                })
                .then(permission => {
                    console.log('🔔 [PUSH] Permission result:', permission);

                    if (permission === 'granted') {
                        console.log('✅ [PUSH] Permission granted! Getting FCM token...');
                        return getFirebaseToken();
                    } else if (permission === 'denied') {
                        console.log('❌ [PUSH] Permission denied by user');
                        return null;
                    } else {
                        console.log('⚠️ [PUSH] Permission default (dismissed)');
                        return null;
                    }
                })
                .catch(error => {
                    console.error('❌ [PUSH] Error requesting permission:', error);
                    return null;
                });
        }

        // Get Firebase Cloud Messaging token
        function getFirebaseToken() {
            console.log('🎫 [FCM] getFirebaseToken called');
            console.log('🎫 [FCM] Messaging available:', !!messaging);
            console.log('🎫 [FCM] SW Registration available:', !!swRegistration);

            if (!messaging) {
                console.error('❌ [FCM] Firebase messaging not initialized');
                return Promise.resolve(null);
            }

            if (!swRegistration) {
                console.error('❌ [FCM] Service Worker not registered yet');
                return Promise.resolve(null);
            }

            const vapidKey = '<?php echo $this->config->item("firebase_vapid_key") ?: ""; ?>';
            console.log('🎫 [FCM] VAPID key length:', vapidKey ? vapidKey.length : 0);

            const tokenOptions = {
                vapidKey: vapidKey,
                serviceWorkerRegistration: swRegistration
            };

            console.log('🎫 [FCM] Requesting token from Firebase...');

            return messaging.getToken(tokenOptions)
                .then(token => {
                    if (token) {
                        console.log('✅ [FCM] Token received:', token.substring(0, 20) + '...');
                        fcmToken = token;

                        // Save token to server
                        saveFCMToken(token);

                        return token;
                    } else {
                        console.warn('⚠️ [FCM] No registration token available. Possible reasons:');
                        console.warn('  - Notifications blocked');
                        console.warn('  - Service worker not active');
                        console.warn('  - VAPID key missing/invalid');
                        return null;
                    }
                })
                .catch(error => {
                    console.error('❌ [FCM] Error getting token:', error);
                    console.error('❌ [FCM] Error details:', error.code, error.message);
                    return null;
                });
        }

        // Save FCM token to server
        function saveFCMToken(token) {
            // Get CSRF token dynamically
            const csrf = getCSRFToken();

            // Use URLSearchParams for form-encoded POST
            const formData = new URLSearchParams();
            formData.append('token', token);
            formData.append('device_type', 'web');
            formData.append('device_name', navigator.userAgent.substring(0, 100));
            formData.append(csrf.name, csrf.hash);

            fetch('<?php echo site_url("dietetic/portal/save_fcm_token"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: formData.toString()
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('FCM token saved successfully');
                    } else {
                        console.error('Failed to save FCM token:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving FCM token:', error);
                });
        }

        // Handle foreground messages
        function handleForegroundMessages() {
            if (!messaging) {
                console.error('[FOREGROUND] messaging not initialized');
                return;
            }

            console.log('[FOREGROUND] Setting up onMessage handler');

            messaging.onMessage(payload => {
                console.log('[FOREGROUND] Message received:', payload);
                console.log('[FOREGROUND] Notification permission:', Notification.permission);

                const notificationTitle = payload.notification?.title || 'Nouvelle notification';
                const notificationOptions = {
                    body: payload.notification?.body || '',
                    icon: payload.notification?.icon || '/uploads/company/favicon.png',
                    tag: 'dietetic-notification',
                    requireInteraction: false
                };

                console.log('[FOREGROUND] Showing notification:', notificationTitle);

                // Show notification
                if (Notification.permission === 'granted') {
                    new Notification(notificationTitle, notificationOptions);
                    console.log('[FOREGROUND] Notification displayed');

                    // Play sound and vibrate
                    notifyUser(1);

                    // Reload notifications
                    loadNotifications(false);
                } else {
                    console.error('[FOREGROUND] Notification permission not granted:', Notification.permission);
                }
            });

            console.log('[FOREGROUND] onMessage handler registered successfully');
        }

        // Initialize Firebase on page load
        document.addEventListener('DOMContentLoaded', function() {
            initFirebasePush();
            // handleForegroundMessages() is now called AFTER Firebase is initialized (inside initFirebasePush)
        });

        // ============================================
        // ONESIGNAL PUSH NOTIFICATIONS (NEW - FOR MEDIAN)
        // ============================================

        // Fonction pour afficher une bannière d'invitation aux notifications
        function showNotificationPromptBanner(OneSignal) {
            // Vérifier si la bannière a déjà été fermée
            if (localStorage.getItem('onesignal_prompt_dismissed') === 'true') {
                console.log('[OneSignal] Prompt banner was previously dismissed');
                return;
            }

            // Créer la bannière
            const banner = document.createElement('div');
            banner.id = 'onesignal-prompt-banner';
            banner.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                max-width: 400px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                z-index: 99999;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                animation: slideInUp 0.5s ease-out;
            `;

            banner.innerHTML = `
                <div style="display: flex; align-items: start; gap: 15px;">
                    <div style="font-size: 32px; flex-shrink: 0;">🔔</div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 8px;">
                            Restez informé !
                        </div>
                        <div style="font-size: 14px; opacity: 0.95; line-height: 1.5; margin-bottom: 15px;">
                            Recevez des notifications pour vos rendez-vous, rappels et nouveaux messages.
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button id="onesignal-allow-btn" style="
                                background: white;
                                color: #667eea;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 6px;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 14px;
                                flex: 1;
                            ">
                                ✓ Activer
                            </button>
                            <button id="onesignal-dismiss-btn" style="
                                background: rgba(255,255,255,0.2);
                                color: white;
                                border: none;
                                padding: 10px 20px;
                                border-radius: 6px;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 14px;
                            ">
                                Plus tard
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Ajouter l'animation CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInUp {
                    from {
                        transform: translateY(100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
                #onesignal-allow-btn:hover {
                    transform: scale(1.05);
                    transition: transform 0.2s;
                }
                #onesignal-dismiss-btn:hover {
                    background: rgba(255,255,255,0.3);
                    transition: background 0.2s;
                }
            `;
            document.head.appendChild(style);

            // Ajouter au body
            document.body.appendChild(banner);

            // Gérer le clic sur "Activer"
            document.getElementById('onesignal-allow-btn').addEventListener('click', async function() {
                banner.remove();
                try {
                    await OneSignal.Notifications.requestPermission();
                    console.log('[OneSignal] User clicked to enable notifications');
                } catch (error) {
                    console.error('[OneSignal] Permission request failed:', error);
                }
            });

            // Gérer le clic sur "Plus tard"
            document.getElementById('onesignal-dismiss-btn').addEventListener('click', function() {
                banner.remove();
                localStorage.setItem('onesignal_prompt_dismissed', 'true');
                console.log('[OneSignal] User dismissed notification prompt');
            });

            // Afficher la bannière après 2 secondes
            setTimeout(() => {
                banner.style.display = 'block';
            }, 2000);
        }

        // Initialize OneSignal on page load
        // Using OneSignalDeferred to wait for SDK to load (since it uses defer)
        window.OneSignalDeferred = window.OneSignalDeferred || [];

        OneSignalDeferred.push(async function(OneSignal) {
            console.log('🚀 [ONESIGNAL] Initializing OneSignal Push Notifications...');

            // Check if browser supports notifications
            if (!('Notification' in window)) {
                console.error('❌ [ONESIGNAL] This browser does not support notifications');
                return;
            }

            try {
                // Fetch OneSignal config from server
                console.log('📡 [ONESIGNAL] Fetching OneSignal config from server...');
                const response = await fetch('<?php echo site_url("dietetic/portal/get_onesignal_config"); ?>');
                const data = await response.json();

                console.log('📡 [ONESIGNAL] Server response:', data);

                if (data.success && data.config && data.config.appId) {
                    console.log('✅ [ONESIGNAL] Config received, App ID:', data.config.appId);

                    // Initialize OneSignal v16
                    await OneSignal.init({
                        appId: data.config.appId,
                        allowLocalhostAsSecureOrigin: data.config.allowLocalhostAsSecureOrigin || false,

                        // Service Worker configuration
                        serviceWorkerParam: { scope: '/' },
                        serviceWorkerPath: data.config.serviceWorkerPath || 'OneSignalSDKWorker.js',

                        // Notification settings
                        notifyButton: { enable: false },

                        // Comportement
                        autoResubscribe: true
                    });

                    console.log('✅ [ONESIGNAL] OneSignal initialized successfully');

                    // Écouter les changements de subscription
                    OneSignal.User.PushSubscription.addEventListener('change', function(event) {
                        console.log('[OneSignal] Subscription state changed:', event);

                        if (event.current.id) {
                            const playerId = event.current.id;
                            console.log('[OneSignal] Player ID:', playerId);

                            // Enregistrer sur le serveur (avec CSRF token dynamique)
                            // Use form-encoded instead of JSON for better CSRF compatibility
                            const csrf = getCSRFToken();
                            const formData = new URLSearchParams();
                            formData.append('player_id', playerId);
                            formData.append('device_type', 'web');
                            formData.append('device_name', navigator.userAgent.substring(0, 100));
                            formData.append(csrf.name, csrf.hash);

                            fetch('<?php echo site_url("dietetic/portal/save_onesignal_player_id"); ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                body: formData.toString()
                            })
                            .then(async res => {
                                const contentType = res.headers.get('content-type');
                                if (!contentType || !contentType.includes('application/json')) {
                                    const text = await res.text();
                                    console.error('[OneSignal] Server returned non-JSON response:', text.substring(0, 200));
                                    throw new Error('Server returned non-JSON response (Status: ' + res.status + ')');
                                }
                                return res.json();
                            })
                            .then(result => {
                                // Update CSRF token from response
                                if (result.csrf_token_name && result.csrf_token_hash) {
                                    updateCSRFToken(result.csrf_token_name, result.csrf_token_hash);
                                }

                                if (result.success) {
                                    console.log('[OneSignal] ✅ Player ID saved successfully on subscription change');
                                } else {
                                    console.error('[OneSignal] ❌ Failed to save Player ID:', result.message);
                                }
                            })
                            .catch(error => {
                                console.error('[OneSignal] ❌ Error saving Player ID:', error);
                            });

                            // Ajouter des tags (OneSignal v16 - méthode synchrone)
                            try {
                                OneSignal.User.addTags({
                                    platform: 'web',
                                    browser: navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge)/i)?.[0] || 'unknown'
                                });
                                console.log('[OneSignal] Tags set successfully');
                            } catch (error) {
                                console.error('[OneSignal] Error setting tags:', error);
                            }
                        }
                    });

                    // Écouter les notifications affichées
                    OneSignal.Notifications.addEventListener('foregroundWillDisplay', function(event) {
                        console.log('[OneSignal] Notification will display:', event);
                    });

                    // Écouter les clics sur les notifications
                    OneSignal.Notifications.addEventListener('click', function(event) {
                        console.log('[OneSignal] Notification clicked:', event);
                    });

                    // Vérifier si déjà inscrit
                    const isPushSupported = await OneSignal.Notifications.isPushSupported();
                    console.log('[OneSignal] Push supported:', isPushSupported);

                    if (isPushSupported) {
                        const permission = await OneSignal.Notifications.permissionNative;
                        console.log('[OneSignal] Current permission:', permission);

                        // Si permission accordée, récupérer le Player ID
                        if (permission === 'granted') {
                            const subscription = OneSignal.User.PushSubscription;
                            if (subscription.id) {
                                console.log('[OneSignal] Already subscribed, Player ID:', subscription.id);

                                // 🔥 FIX: Enregistrer le Player ID sur le serveur (cas où l'utilisateur est déjà abonné)
                                // Use form-encoded instead of JSON for better CSRF compatibility
                                const csrf = getCSRFToken();
                                const formData = new URLSearchParams();
                                formData.append('player_id', subscription.id);
                                formData.append('device_type', 'web');
                                formData.append('device_name', navigator.userAgent.substring(0, 100));
                                formData.append(csrf.name, csrf.hash);

                                fetch('<?php echo site_url("dietetic/portal/save_onesignal_player_id"); ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    credentials: 'same-origin',
                                    body: formData.toString()
                                })
                                .then(async res => {
                                    const contentType = res.headers.get('content-type');
                                    if (!contentType || !contentType.includes('application/json')) {
                                        const text = await res.text();
                                        console.error('[OneSignal] Server returned non-JSON response:', text.substring(0, 200));
                                        throw new Error('Server returned non-JSON response (Status: ' + res.status + ')');
                                    }
                                    return res.json();
                                })
                                .then(result => {
                                    // Update CSRF token from response
                                    if (result.csrf_token_name && result.csrf_token_hash) {
                                        updateCSRFToken(result.csrf_token_name, result.csrf_token_hash);
                                    }

                                    if (result.success) {
                                        console.log('[OneSignal] ✅ Player ID saved successfully on page load');
                                    } else {
                                        console.error('[OneSignal] ❌ Failed to save Player ID:', result.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('[OneSignal] ❌ Error saving Player ID:', error);
                                });

                                // Ajouter des tags
                                try {
                                    OneSignal.User.addTags({
                                        platform: 'web',
                                        browser: navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge)/i)?.[0] || 'unknown'
                                    });
                                    console.log('[OneSignal] Tags set successfully');
                                } catch (error) {
                                    console.error('[OneSignal] Error setting tags:', error);
                                }
                            }
                        } else if (permission === 'default') {
                            // Afficher une bannière d'invitation (ne PAS demander automatiquement)
                            console.log('[OneSignal] Permission not granted. Showing prompt banner.');
                            showNotificationPromptBanner(OneSignal);
                        }
                    }

                    // Fonction globale pour demander la permission (utilisée depuis les préférences)
                    window.requestOneSignalPermission = async function() {
                        try {
                            console.log('[OneSignal] User clicked to enable notifications');
                            await OneSignal.Notifications.requestPermission();
                        } catch (error) {
                            console.error('[OneSignal] Permission request failed:', error);
                        }
                    };

                } else {
                    console.error('❌ [ONESIGNAL] OneSignal not configured or disabled');
                    console.error('❌ [ONESIGNAL] Server message:', data.message);
                }
            } catch (error) {
                console.error('❌ [ONESIGNAL] Initialization error:', error);
            }
        });

        // Expose function globally for use in preferences page
        window.requestNotificationPermission = requestNotificationPermission;
    </script>
</body>
</html>
