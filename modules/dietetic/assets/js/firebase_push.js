/**
 * Firebase Push Notifications for DietZone Portal
 * Handles push notification registration and messaging
 */

(function() {
    'use strict';

    // Firebase configuration will be injected by PHP
    let firebaseConfig = null;
    let messaging = null;
    let isSupported = false;
    let currentToken = null;

    /**
     * Initialize Firebase Cloud Messaging
     */
    window.DietzonePushNotifications = {
        init: function(config) {
            if (!config) {
                console.warn('[Firebase] No configuration provided');
                return;
            }

            firebaseConfig = config;

            // Check if browser supports notifications
            if (!('Notification' in window)) {
                console.warn('[Firebase] This browser does not support notifications');
                return;
            }

            if (!('serviceWorker' in navigator)) {
                console.warn('[Firebase] This browser does not support service workers');
                return;
            }

            // Check if Firebase Messaging is supported
            if (typeof firebase === 'undefined') {
                console.error('[Firebase] Firebase SDK not loaded');
                return;
            }

            try {
                // Initialize Firebase
                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                } else {
                    firebase.app(); // Use existing app
                }

                // Check if messaging is supported (not in private mode, etc.)
                if (firebase.messaging.isSupported()) {
                    messaging = firebase.messaging();
                    isSupported = true;

                    // Register service worker
                    this.registerServiceWorker();

                    // Handle foreground messages
                    this.handleForegroundMessages();
                } else {
                    console.warn('[Firebase] Messaging not supported in this browser');
                }
            } catch (error) {
                console.error('[Firebase] Initialization error:', error);
            }
        },

        /**
         * Register service worker
         */
        registerServiceWorker: function() {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    // Pass config to service worker
                    if (registration.active) {
                        registration.active.postMessage({
                            type: 'FIREBASE_CONFIG',
                            config: firebaseConfig
                        });
                    }

                    messaging.useServiceWorker(registration);
                })
                .catch((error) => {
                    console.error('[Firebase] Service Worker registration failed:', error);
                });
        },

        /**
         * Request notification permission and get token
         */
        requestPermission: function(callback) {
            if (!isSupported) {
                if (callback) callback(false, 'Not supported');
                return;
            }

            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    // Get FCM token
                    messaging.getToken({ vapidKey: firebaseConfig.vapidKey })
                        .then((token) => {
                            if (token) {
                                currentToken = token;

                                // Save token to server
                                this.saveTokenToServer(token);

                                if (callback) callback(true, token);
                            } else {
                                console.warn('[Firebase] No registration token available');
                                if (callback) callback(false, 'No token');
                            }
                        })
                        .catch((error) => {
                            console.error('[Firebase] Error getting token:', error);
                            if (callback) callback(false, error.message);
                        });
                } else {
                    if (callback) callback(false, 'Permission denied');
                }
            }).catch((error) => {
                console.error('[Firebase] Error requesting permission:', error);
                if (callback) callback(false, error.message);
            });
        },

        /**
         * Save FCM token to server
         */
        saveTokenToServer: function(token) {
            const data = {
                token: token,
                device_type: 'web',
                device_name: this.getDeviceName(),
                user_agent: navigator.userAgent
            };

            fetch(site_url + 'dietetic/portal/save_fcm_token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    console.error('[Firebase] Failed to save token:', result.message);
                }
            })
            .catch(error => {
                console.error('[Firebase] Error saving token:', error);
            });
        },

        /**
         * Handle foreground messages (when app is open)
         */
        handleForegroundMessages: function() {
            messaging.onMessage((payload) => {
                const notificationTitle = payload.notification?.title || 'DietZone';
                const notificationOptions = {
                    body: payload.notification?.body || 'Nouvelle notification',
                    icon: payload.notification?.icon || '/uploads/company/favicon.png',
                    badge: payload.notification?.badge || '/uploads/company/favicon.png',
                    tag: payload.data?.type || 'dietzone-notification',
                    data: payload.data || {},
                    requireInteraction: false
                };

                if (payload.notification?.image) {
                    notificationOptions.image = payload.notification.image;
                }

                // Show notification
                if (Notification.permission === 'granted') {
                    new Notification(notificationTitle, notificationOptions);
                }

                // Show in-app notification (if function exists)
                if (typeof showInAppNotification === 'function') {
                    showInAppNotification(
                        notificationTitle,
                        notificationOptions.body,
                        payload.data?.click_action
                    );
                }

                // Trigger custom event
                const event = new CustomEvent('dietzone:notification', {
                    detail: payload
                });
                document.dispatchEvent(event);
            });
        },

        /**
         * Delete current token
         */
        deleteToken: function(callback) {
            if (!messaging || !currentToken) {
                if (callback) callback(false);
                return;
            }

            messaging.deleteToken()
                .then(() => {
                    currentToken = null;

                    // Remove from server
                    fetch(site_url + 'dietetic/portal/delete_fcm_token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ token: currentToken })
                    });

                    if (callback) callback(true);
                })
                .catch((error) => {
                    console.error('[Firebase] Error deleting token:', error);
                    if (callback) callback(false);
                });
        },

        /**
         * Get device name from user agent
         */
        getDeviceName: function() {
            const ua = navigator.userAgent;

            if (ua.includes('Chrome')) return 'Chrome';
            if (ua.includes('Firefox')) return 'Firefox';
            if (ua.includes('Safari')) return 'Safari';
            if (ua.includes('Edge')) return 'Edge';
            if (ua.includes('Opera')) return 'Opera';

            return 'Unknown Browser';
        },

        /**
         * Get current permission status
         */
        getPermissionStatus: function() {
            if (!('Notification' in window)) {
                return 'unsupported';
            }
            return Notification.permission;
        },

        /**
         * Check if notifications are enabled
         */
        isEnabled: function() {
            return isSupported && Notification.permission === 'granted';
        },

        /**
         * Get current token
         */
        getCurrentToken: function() {
            return currentToken;
        }
    };

    /**
     * Show in-app notification banner
     */
    window.showInAppNotification = function(title, message, clickUrl) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'dietzone-in-app-notification';
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fa fa-bell"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <div class="notification-close">
                <i class="fa fa-times"></i>
            </div>
        `;

        // Add click handler
        if (clickUrl) {
            notification.style.cursor = 'pointer';
            notification.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-close')) {
                    window.location.href = clickUrl;
                }
            });
        }

        // Add close handler
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.classList.add('hiding');
            setTimeout(() => notification.remove(), 300);
        });

        // Add to page
        document.body.appendChild(notification);

        // Show with animation
        setTimeout(() => notification.classList.add('show'), 100);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.classList.add('hiding');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    };

})();
