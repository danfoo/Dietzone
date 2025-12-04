/**
 * Median OneSignal Integration
 * Script minimaliste pour récupérer le Player ID depuis l'app Median
 * et l'envoyer au backend Dietzone
 *
 * Ce script s'exécute uniquement dans l'APK généré par Median
 */

(function() {
    'use strict';

    // Vérifier qu'on est bien dans Median
    if (typeof gonative === 'undefined') {
        console.log('[Median] Not running in Median app');
        return;
    }

    console.log('[Median] Initializing OneSignal integration...');

    /**
     * Initialiser OneSignal dans Median
     */
    function initMedianOneSignal() {
        // Vérifier que le plugin OneSignal est disponible
        if (typeof gonative.onesignal === 'undefined') {
            console.warn('[Median] OneSignal plugin not available');
            return;
        }

        // Récupérer le Player ID
        gonative.onesignal.getUserId(function(userId) {
            if (userId) {
                console.log('[Median] OneSignal Player ID:', userId);
                registerPlayerOnServer(userId);
                setupNotificationHandlers(userId);
            } else {
                console.warn('[Median] No OneSignal Player ID received');
                // Réessayer après 2 secondes
                setTimeout(function() {
                    gonative.onesignal.getUserId(function(retryUserId) {
                        if (retryUserId) {
                            console.log('[Median] OneSignal Player ID (retry):', retryUserId);
                            registerPlayerOnServer(retryUserId);
                            setupNotificationHandlers(retryUserId);
                        }
                    });
                }, 2000);
            }
        });

        // Récupérer aussi le Push Token si disponible
        if (typeof gonative.onesignal.idsAvailable !== 'undefined') {
            gonative.onesignal.idsAvailable(function(ids) {
                if (ids.userId) {
                    console.log('[Median] IDs Available:', ids);
                    // ids.userId = Player ID
                    // ids.pushToken = Push Token (Firebase/APNs)
                }
            });
        }
    }

    /**
     * Enregistrer le Player ID sur le serveur Dietzone
     */
    function registerPlayerOnServer(playerId) {
        if (!playerId) return;

        const data = {
            player_id: playerId,
            device_type: getDeviceType(),
            device_name: getDeviceName(),
            user_agent: navigator.userAgent,
            app_version: getAppVersion(),
            platform: navigator.platform
        };

        // Endpoint du backend
        const endpoint = (typeof site_url !== 'undefined' ? site_url : '/') +
                        'dietetic/portal/save_onesignal_player_id';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            if (result.success) {
                console.log('[Median] ✓ Player ID registered on server');

                // Configurer les tags OneSignal
                setupPlayerTags(playerId);
            } else {
                console.error('[Median] Failed to register Player ID:', result.message);
            }
        })
        .catch(function(error) {
            console.error('[Median] Error registering Player ID:', error);
        });
    }

    /**
     * Configurer les handlers de notifications
     */
    function setupNotificationHandlers(playerId) {
        // Handler quand une notification est reçue (app ouverte)
        if (typeof gonative.onesignal.setNotificationReceivedHandler !== 'undefined') {
            gonative.onesignal.setNotificationReceivedHandler(function(notification) {
                console.log('[Median] Notification received:', notification);

                // Déclencher un événement custom
                var event = new CustomEvent('dietzone:notification-received', {
                    detail: notification
                });
                document.dispatchEvent(event);

                // Afficher notification in-app si disponible
                if (typeof showInAppNotification === 'function') {
                    showInAppNotification(
                        notification.title || 'DietZone',
                        notification.message || notification.body || '',
                        notification.launchURL || notification.url
                    );
                }
            });
        }

        // Handler quand une notification est ouverte/cliquée
        if (typeof gonative.onesignal.addNotificationOpenedHandler !== 'undefined') {
            gonative.onesignal.addNotificationOpenedHandler(function(data) {
                console.log('[Median] Notification opened:', data);

                // Déclencher un événement custom
                var event = new CustomEvent('dietzone:notification-opened', {
                    detail: data
                });
                document.dispatchEvent(event);

                // Naviguer vers l'URL si fournie
                if (data.url || data.launchURL) {
                    var targetUrl = data.url || data.launchURL;
                    console.log('[Median] Navigating to:', targetUrl);
                    window.location.href = targetUrl;
                } else if (data.additionalData && data.additionalData.click_action) {
                    console.log('[Median] Navigating to:', data.additionalData.click_action);
                    window.location.href = data.additionalData.click_action;
                }
            });
        }
    }

    /**
     * Configurer les tags du player
     */
    function setupPlayerTags(playerId) {
        if (!gonative.onesignal.setTags) {
            return;
        }

        var tags = {
            platform: getDeviceType(),
            device: getDeviceName(),
            language: navigator.language || 'fr',
            app_version: getAppVersion(),
            registered_at: new Date().toISOString()
        };

        gonative.onesignal.setTags(tags);
        console.log('[Median] Player tags set:', tags);
    }

    /**
     * Déterminer le type d'appareil
     */
    function getDeviceType() {
        var ua = navigator.userAgent;
        if (ua.indexOf('GoNativeIOS') > -1 || ua.indexOf('iPhone') > -1 || ua.indexOf('iPad') > -1) {
            return 'ios';
        } else if (ua.indexOf('GoNativeAndroid') > -1 || ua.indexOf('Android') > -1) {
            return 'android';
        }
        return 'median_app';
    }

    /**
     * Obtenir le nom de l'appareil
     */
    function getDeviceName() {
        var ua = navigator.userAgent;

        if (ua.indexOf('GoNativeIOS') > -1) {
            if (ua.indexOf('iPad') > -1) return 'iPad';
            if (ua.indexOf('iPhone') > -1) return 'iPhone';
            return 'iOS Device';
        }

        if (ua.indexOf('GoNativeAndroid') > -1) {
            // Extraire le modèle Android si possible
            var match = ua.match(/Android.*?;\s*([^)]+)/);
            if (match && match[1]) {
                return match[1].trim();
            }
            return 'Android Device';
        }

        return 'Median App';
    }

    /**
     * Obtenir la version de l'app (si disponible)
     */
    function getAppVersion() {
        if (typeof gonative !== 'undefined' && gonative.appVersion) {
            return gonative.appVersion;
        }
        return '1.0.0';
    }

    /**
     * API publique pour Median
     */
    window.MedianOneSignal = {
        /**
         * Obtenir le Player ID actuel
         */
        getPlayerId: function(callback) {
            if (typeof gonative !== 'undefined' && gonative.onesignal) {
                gonative.onesignal.getUserId(callback);
            } else {
                callback(null);
            }
        },

        /**
         * Envoyer un tag custom
         */
        sendTag: function(key, value) {
            if (typeof gonative !== 'undefined' && gonative.onesignal && gonative.onesignal.setTags) {
                var tags = {};
                tags[key] = value;
                gonative.onesignal.setTags(tags);
            }
        },

        /**
         * Supprimer un tag
         */
        deleteTag: function(key) {
            if (typeof gonative !== 'undefined' && gonative.onesignal && gonative.onesignal.deleteTags) {
                gonative.onesignal.deleteTags([key]);
            }
        },

        /**
         * Obtenir le statut de la permission
         */
        getPermissionStatus: function(callback) {
            if (typeof gonative !== 'undefined' && gonative.onesignal && gonative.onesignal.getPermissionSubscriptionState) {
                gonative.onesignal.getPermissionSubscriptionState(function(state) {
                    callback(state);
                });
            } else {
                callback({ subscriptionStatus: { subscribed: false } });
            }
        },

        /**
         * Demander la permission (iOS principalement)
         */
        promptForPushNotifications: function(callback) {
            if (typeof gonative !== 'undefined' && gonative.onesignal && gonative.onesignal.promptForPushNotifications) {
                gonative.onesignal.promptForPushNotifications(callback);
            } else {
                // Sur Android, la permission est automatique
                if (callback) callback(true);
            }
        }
    };

    // Initialiser dès que le DOM est prêt
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMedianOneSignal);
    } else {
        initMedianOneSignal();
    }

})();
