/**
 * OneSignal Push Notifications for DietZone Portal
 * Remplace firebase_push.js pour une meilleure intégration avec Median
 *
 * Gère les push notifications via OneSignal Web SDK
 * Compatible avec Web + Median APK
 */

(function() {
    'use strict';

    // Configuration
    let oneSignalConfig = null;
    let isInitialized = false;
    let currentPlayerId = null;
    let isMedianApp = false;

    /**
     * Dietzone Push Notifications Manager (OneSignal)
     */
    window.DietzonePushNotifications = {
        /**
         * Initialize OneSignal
         */
        init: function(config) {
            if (!config || !config.appId) {
                console.warn('[OneSignal] No app ID provided');
                return;
            }

            oneSignalConfig = config;

            // Détecter si on est dans l'app Median
            isMedianApp = this.detectMedianApp();

            if (isMedianApp) {
                console.log('[OneSignal] Running in Median app - using native SDK');
                this.initMedianApp();
            } else {
                console.log('[OneSignal] Running in web browser - using Web SDK');
                this.initWebSDK();
            }
        },

        /**
         * Détecter si on est dans l'app Median
         */
        detectMedianApp: function() {
            // Median expose l'objet gonative
            return typeof gonative !== 'undefined' &&
                   (typeof gonative.onesignal !== 'undefined' ||
                    window.navigator.userAgent.indexOf('GoNativeIOS') > -1 ||
                    window.navigator.userAgent.indexOf('GoNativeAndroid') > -1);
        },

        /**
         * Initialiser dans l'app Median (utilise le SDK natif)
         */
        initMedianApp: function() {
            // Vérifier que gonative.onesignal existe
            if (typeof gonative === 'undefined' || typeof gonative.onesignal === 'undefined') {
                console.warn('[OneSignal] Median app detected but gonative.onesignal not available');
                return;
            }

            // Récupérer le Player ID depuis le SDK natif
            gonative.onesignal.getUserId(function(userId) {
                if (userId) {
                    currentPlayerId = userId;
                    console.log('[OneSignal] Player ID received from Median:', userId);

                    // Enregistrer le Player ID sur le serveur
                    DietzonePushNotifications.savePlayerIdToServer(userId);

                    // Configurer les tags
                    DietzonePushNotifications.setupPlayerTags();
                } else {
                    console.warn('[OneSignal] No player ID received from Median');
                }
            });

            // Écouter les notifications reçues
            gonative.onesignal.addNotificationOpenedHandler(function(data) {
                console.log('[OneSignal] Notification opened:', data);

                // Déclencher un événement custom
                const event = new CustomEvent('dietzone:notification-opened', {
                    detail: data
                });
                document.dispatchEvent(event);

                // Rediriger si URL fournie
                if (data.url || data.launchURL) {
                    window.location.href = data.url || data.launchURL;
                }
            });

            isInitialized = true;
        },

        /**
         * Initialiser le SDK Web OneSignal
         */
        initWebSDK: function() {
            // Charger le SDK OneSignal si pas déjà chargé
            if (typeof OneSignal === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.onesignal.com/sdks/OneSignalSDK.js';
                script.async = true;
                script.onload = () => {
                    this.configureWebSDK();
                };
                document.head.appendChild(script);
            } else {
                this.configureWebSDK();
            }
        },

        /**
         * Configurer le SDK Web
         */
        configureWebSDK: function() {
            if (typeof OneSignal === 'undefined') {
                console.error('[OneSignal] SDK not loaded');
                return;
            }

            window.OneSignal = window.OneSignal || [];

            OneSignal.push(function() {
                OneSignal.init({
                    appId: oneSignalConfig.appId,
                    safari_web_id: oneSignalConfig.safari_web_id,
                    notifyButton: {
                        enable: false // On gère manuellement
                    },
                    autoRegister: false, // On va demander manuellement
                    autoResubscribe: true,
                    notificationClickHandlerMatch: 'origin',
                    notificationClickHandlerAction: 'navigate',
                    serviceWorkerParam: {
                        scope: '/push/onesignal/'
                    },
                    serviceWorkerPath: 'OneSignalSDKWorker.js'
                });

                // Écouter les événements
                OneSignal.on('subscriptionChange', function(isSubscribed) {
                    console.log('[OneSignal] Subscription state changed:', isSubscribed);

                    if (isSubscribed) {
                        OneSignal.getUserId(function(userId) {
                            currentPlayerId = userId;
                            console.log('[OneSignal] Player ID:', userId);
                            DietzonePushNotifications.savePlayerIdToServer(userId);
                        });
                    }
                });

                OneSignal.on('notificationDisplay', function(event) {
                    console.log('[OneSignal] Notification displayed:', event);

                    // Afficher notification in-app si fonction existe
                    if (typeof showInAppNotification === 'function') {
                        showInAppNotification(
                            event.heading || 'DietZone',
                            event.content || '',
                            event.url
                        );
                    }
                });

                OneSignal.on('notificationDismiss', function(event) {
                    console.log('[OneSignal] Notification dismissed:', event);
                });

                isInitialized = true;
                console.log('[OneSignal] Web SDK initialized');
            });
        },

        /**
         * Demander la permission pour les notifications
         */
        requestPermission: function(callback) {
            if (!isInitialized) {
                console.warn('[OneSignal] Not initialized');
                if (callback) callback(false, 'Not initialized');
                return;
            }

            if (isMedianApp) {
                // Dans Median, la permission est gérée automatiquement
                console.log('[OneSignal] Permission handled by Median app');
                if (callback) callback(true, currentPlayerId);
                return;
            }

            // Web SDK
            if (typeof OneSignal === 'undefined') {
                if (callback) callback(false, 'OneSignal SDK not loaded');
                return;
            }

            OneSignal.push(function() {
                OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                    if (isEnabled) {
                        // Déjà abonné
                        OneSignal.getUserId(function(userId) {
                            currentPlayerId = userId;
                            if (callback) callback(true, userId);
                        });
                    } else {
                        // Demander la permission
                        OneSignal.showNativePrompt();

                        // Attendre la réponse
                        OneSignal.on('subscriptionChange', function(isSubscribed) {
                            if (isSubscribed) {
                                OneSignal.getUserId(function(userId) {
                                    currentPlayerId = userId;
                                    if (callback) callback(true, userId);
                                });
                            } else {
                                if (callback) callback(false, 'Permission denied');
                            }
                        });
                    }
                });
            });
        },

        /**
         * Enregistrer le Player ID sur le serveur
         */
        savePlayerIdToServer: function(playerId) {
            if (!playerId) {
                console.warn('[OneSignal] No player ID to save');
                return;
            }

            const data = {
                player_id: playerId,
                device_type: isMedianApp ? 'median_app' : 'web',
                device_name: this.getDeviceName(),
                user_agent: navigator.userAgent
            };

            fetch(site_url + 'dietetic/portal/save_onesignal_player_id', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log('[OneSignal] Player ID saved to server');
                } else {
                    console.error('[OneSignal] Failed to save player ID:', result.message);
                }
            })
            .catch(error => {
                console.error('[OneSignal] Error saving player ID:', error);
            });
        },

        /**
         * Configurer les tags du player
         */
        setupPlayerTags: function() {
            if (!currentPlayerId) {
                return;
            }

            const tags = {
                platform: isMedianApp ? 'median_app' : 'web',
                language: navigator.language || 'fr',
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'Africa/Dakar'
            };

            if (isMedianApp && typeof gonative !== 'undefined' && gonative.onesignal) {
                // Median app
                gonative.onesignal.idsAvailable(function(result) {
                    if (result.pushToken) {
                        tags.push_token = result.pushToken;
                    }
                });

                gonative.onesignal.setTags(tags);
            } else if (typeof OneSignal !== 'undefined') {
                // Web SDK
                OneSignal.push(function() {
                    OneSignal.sendTags(tags);
                });
            }

            console.log('[OneSignal] Player tags set:', tags);
        },

        /**
         * Supprimer l'abonnement
         */
        unsubscribe: function(callback) {
            if (!isInitialized) {
                if (callback) callback(false);
                return;
            }

            if (isMedianApp) {
                console.warn('[OneSignal] Cannot unsubscribe from Median app (use device settings)');
                if (callback) callback(false);
                return;
            }

            if (typeof OneSignal === 'undefined') {
                if (callback) callback(false);
                return;
            }

            OneSignal.push(function() {
                OneSignal.setSubscription(false);

                // Supprimer du serveur
                if (currentPlayerId) {
                    fetch(site_url + 'dietetic/portal/delete_onesignal_player_id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ player_id: currentPlayerId })
                    });
                }

                currentPlayerId = null;
                if (callback) callback(true);
            });
        },

        /**
         * Obtenir le nom de l'appareil
         */
        getDeviceName: function() {
            if (isMedianApp) {
                if (window.navigator.userAgent.indexOf('GoNativeIOS') > -1) {
                    return 'iPhone/iPad';
                } else if (window.navigator.userAgent.indexOf('GoNativeAndroid') > -1) {
                    return 'Android';
                }
                return 'Median App';
            }

            const ua = navigator.userAgent;
            if (ua.includes('Chrome')) return 'Chrome';
            if (ua.includes('Firefox')) return 'Firefox';
            if (ua.includes('Safari')) return 'Safari';
            if (ua.includes('Edge')) return 'Edge';
            if (ua.includes('Opera')) return 'Opera';
            return 'Unknown Browser';
        },

        /**
         * Vérifier le statut des permissions
         */
        getPermissionStatus: function() {
            if (isMedianApp) {
                return 'granted'; // Dans Median c'est géré automatiquement
            }

            if (!('Notification' in window)) {
                return 'unsupported';
            }

            return Notification.permission;
        },

        /**
         * Vérifier si les notifications sont activées
         */
        isEnabled: function() {
            if (isMedianApp) {
                return !!currentPlayerId;
            }

            return isInitialized && Notification.permission === 'granted';
        },

        /**
         * Obtenir le Player ID actuel
         */
        getCurrentPlayerId: function() {
            return currentPlayerId;
        },

        /**
         * Vérifier si on est dans Median
         */
        isMedianApp: function() {
            return isMedianApp;
        }
    };

    /**
     * Afficher une notification in-app (réutilisé de firebase_push.js)
     */
    window.showInAppNotification = function(title, message, clickUrl) {
        // Créer l'élément de notification
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

        // Ajouter le gestionnaire de clic
        if (clickUrl) {
            notification.style.cursor = 'pointer';
            notification.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-close')) {
                    window.location.href = clickUrl;
                }
            });
        }

        // Ajouter le gestionnaire de fermeture
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.classList.add('hiding');
            setTimeout(() => notification.remove(), 300);
        });

        // Ajouter à la page
        document.body.appendChild(notification);

        // Afficher avec animation
        setTimeout(() => notification.classList.add('show'), 100);

        // Masquer automatiquement après 5 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('hiding');
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    };

})();
