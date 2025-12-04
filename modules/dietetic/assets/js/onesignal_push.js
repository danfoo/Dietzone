/**
 * OneSignal Push Notifications for DietZone Portal
 * Compatible avec OneSignal Web SDK v16+
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
        init: async function(config) {
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
                console.log('[OneSignal] Running in web browser - using Web SDK v16');
                await this.initWebSDK();
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
                    window.DietzonePushNotifications.savePlayerIdToServer(userId);

                    // Configurer les tags
                    window.DietzonePushNotifications.setupPlayerTags();
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
         * Initialiser le SDK Web OneSignal v16
         */
        initWebSDK: async function() {
            // Attendre que OneSignal soit chargé
            if (typeof OneSignal === 'undefined') {
                console.error('[OneSignal] SDK not loaded - make sure OneSignalSDK.page.js is included');
                return;
            }

            try {
                // Configuration pour OneSignal v16
                await OneSignal.init({
                    appId: oneSignalConfig.appId,
                    allowLocalhostAsSecureOrigin: oneSignalConfig.allowLocalhostAsSecureOrigin || false,

                    // Service Worker configuration
                    serviceWorkerParam: { scope: '/' },
                    serviceWorkerPath: oneSignalConfig.serviceWorkerPath || 'OneSignalSDKWorker.js',

                    // Notification settings
                    notifyButton: { enable: false }, // On gère manuellement

                    // Comportement
                    autoResubscribe: true,

                    // Click handlers
                    notificationClickHandlerMatch: oneSignalConfig.notificationClickHandlerMatch || 'origin',
                    notificationClickHandlerAction: oneSignalConfig.notificationClickHandlerAction || 'focus'
                });

                console.log('[OneSignal] Web SDK v16 initialized');

                // Écouter les changements de subscription
                OneSignal.User.PushSubscription.addEventListener('change', function(event) {
                    console.log('[OneSignal] Subscription state changed:', event);

                    if (event.current.id) {
                        currentPlayerId = event.current.id;
                        console.log('[OneSignal] Player ID:', currentPlayerId);
                        window.DietzonePushNotifications.savePlayerIdToServer(currentPlayerId);
                        window.DietzonePushNotifications.setupPlayerTags();
                    }
                });

                // Écouter les notifications affichées
                OneSignal.Notifications.addEventListener('foregroundWillDisplay', function(event) {
                    console.log('[OneSignal] Notification will display:', event);

                    // Afficher notification in-app si fonction existe
                    if (typeof showInAppNotification === 'function') {
                        const notification = event.notification;
                        showInAppNotification(
                            notification.title || 'DietZone',
                            notification.body || '',
                            notification.launchURL
                        );
                    }
                });

                // Écouter les clics sur les notifications
                OneSignal.Notifications.addEventListener('click', function(event) {
                    console.log('[OneSignal] Notification clicked:', event);

                    // Déclencher un événement custom
                    const customEvent = new CustomEvent('dietzone:notification-clicked', {
                        detail: event
                    });
                    document.dispatchEvent(customEvent);
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
                            currentPlayerId = subscription.id;
                            console.log('[OneSignal] Already subscribed, Player ID:', currentPlayerId);
                            this.savePlayerIdToServer(currentPlayerId);
                            this.setupPlayerTags();
                        }
                    }
                }

                isInitialized = true;

            } catch (error) {
                console.error('[OneSignal] Initialization error:', error);
            }
        },

        /**
         * Demander la permission pour les notifications
         */
        requestPermission: async function(callback) {
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

            // Web SDK v16
            try {
                console.log('[OneSignal] Requesting permission...');

                // Demander la permission
                const permission = await OneSignal.Notifications.requestPermission();
                console.log('[OneSignal] Permission response:', permission);

                if (permission) {
                    // Récupérer le Player ID après inscription
                    const subscription = OneSignal.User.PushSubscription;
                    if (subscription.id) {
                        currentPlayerId = subscription.id;
                        console.log('[OneSignal] User subscribed, Player ID:', currentPlayerId);

                        // Enregistrer sur le serveur
                        this.savePlayerIdToServer(currentPlayerId);
                        this.setupPlayerTags();

                        if (callback) callback(true, currentPlayerId);
                    } else {
                        console.warn('[OneSignal] Permission granted but no Player ID');
                        if (callback) callback(false, 'No Player ID');
                    }
                } else {
                    console.warn('[OneSignal] Permission denied');
                    if (callback) callback(false, 'Permission denied');
                }
            } catch (error) {
                console.error('[OneSignal] Permission request error:', error);
                if (callback) callback(false, error.message);
            }
        },

        /**
         * Enregistrer le Player ID sur le serveur
         */
        savePlayerIdToServer: function(playerId) {
            if (!playerId) {
                console.warn('[OneSignal] No Player ID to save');
                return;
            }

            console.log('[OneSignal] Saving Player ID to server:', playerId);

            // Déterminer le type d'appareil
            const deviceType = isMedianApp ?
                (navigator.userAgent.indexOf('GoNativeIOS') > -1 ? 'ios' : 'android') :
                'web';

            // Envoyer au serveur
            fetch(window.location.origin + '/dietetic/portal/save_onesignal_player_id', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    player_id: playerId,
                    device_type: deviceType,
                    user_agent: navigator.userAgent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('[OneSignal] Player ID saved successfully');
                } else {
                    console.error('[OneSignal] Failed to save Player ID:', data.message);
                }
            })
            .catch(error => {
                console.error('[OneSignal] Error saving Player ID:', error);
            });
        },

        /**
         * Configurer les tags du joueur
         */
        setupPlayerTags: async function() {
            if (!isInitialized) {
                console.warn('[OneSignal] Not initialized, cannot set tags');
                return;
            }

            if (isMedianApp && typeof gonative !== 'undefined' && gonative.onesignal) {
                // Tags pour Median
                gonative.onesignal.sendTags({
                    platform: 'median',
                    app_version: gonative.appVersion || 'unknown'
                });
            } else if (typeof OneSignal !== 'undefined') {
                // Tags pour Web v16
                try {
                    await OneSignal.User.addTags({
                        platform: 'web',
                        browser: navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge)/i)?.[0] || 'unknown'
                    });
                    console.log('[OneSignal] Tags set successfully');
                } catch (error) {
                    console.error('[OneSignal] Error setting tags:', error);
                }
            }
        },

        /**
         * Vérifier si les notifications sont activées
         */
        isEnabled: async function() {
            if (!isInitialized) {
                return false;
            }

            if (isMedianApp) {
                return true; // Dans Median, on suppose que c'est activé
            }

            try {
                const permission = await OneSignal.Notifications.permissionNative;
                return permission === 'granted';
            } catch (error) {
                console.error('[OneSignal] Error checking permission:', error);
                return false;
            }
        },

        /**
         * Obtenir le Player ID actuel
         */
        getPlayerId: async function() {
            if (currentPlayerId) {
                return currentPlayerId;
            }

            if (isMedianApp && typeof gonative !== 'undefined' && gonative.onesignal) {
                return new Promise((resolve) => {
                    gonative.onesignal.getUserId(function(userId) {
                        currentPlayerId = userId;
                        resolve(userId);
                    });
                });
            } else if (typeof OneSignal !== 'undefined') {
                try {
                    const subscription = OneSignal.User.PushSubscription;
                    currentPlayerId = subscription.id;
                    return currentPlayerId;
                } catch (error) {
                    console.error('[OneSignal] Error getting Player ID:', error);
                    return null;
                }
            }

            return null;
        }
    };

    // Auto-initialisation si config fournie via window
    if (window.oneSignalConfig) {
        window.DietzonePushNotifications.init(window.oneSignalConfig);
    }
})();
