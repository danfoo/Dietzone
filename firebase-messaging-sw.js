// Firebase Cloud Messaging Service Worker
// This file MUST be at the root of your site

// Import Firebase scripts
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

console.log('[SW] Firebase Messaging Service Worker loaded');

// Firebase config - will be received from main thread
let isFirebaseInitialized = false;

// Listen for config from main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        console.log('[SW] Received Firebase config');

        try {
            if (!isFirebaseInitialized) {
                firebase.initializeApp(event.data.config);
                isFirebaseInitialized = true;
                console.log('[SW] ✅ Firebase initialized');
            }
        } catch (error) {
            console.error('[SW] ❌ Error initializing Firebase:', error);
        }
    }
});

// Handle push notifications
self.addEventListener('push', (event) => {
    console.log('[SW] Push event received');

    if (!event.data) {
        console.log('[SW] Push event has no data');
        return;
    }

    try {
        const payload = event.data.json();
        console.log('[SW] Push payload:', payload);

        // Extract notification from FCM payload
        const notification = payload.notification || {};
        const data = payload.data || {};

        const title = notification.title || 'DietSenegal';
        const options = {
            body: notification.body || 'Nouvelle notification',
            icon: notification.icon || data.icon || '/uploads/company/favicon.png',
            badge: '/uploads/company/favicon.png',
            tag: 'dietzone',
            data: {
                url: data.click_action || '/dietetic/portal'
            }
        };

        if (notification.image) {
            options.image = notification.image;
        }

        console.log('[SW] Showing notification:', title);

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (error) {
        console.error('[SW] Error in push handler:', error);
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    event.notification.close();

    const url = event.notification.data?.url || '/dietetic/portal';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            // Focus existing window if found
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise open new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

console.log('[SW] Service Worker ready');
