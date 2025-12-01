// Firebase Cloud Messaging Service Worker
// This file MUST be at the root of your site for push notifications to work

// Import Firebase scripts (compat version for service workers)
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

console.log('[SW] Firebase Messaging Service Worker loaded');

// Firebase app instance
let firebaseApp = null;
let messaging = null;

// Listen for initialization message from main thread
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data?.type);

    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        const config = event.data.config;
        console.log('[SW] Received Firebase config');

        try {
            // Initialize Firebase if not already done
            if (!firebaseApp) {
                firebaseApp = firebase.initializeApp(config);
                messaging = firebase.messaging();
                console.log('[SW] ✅ Firebase initialized successfully');
            }
        } catch (error) {
            console.error('[SW] ❌ Error initializing Firebase:', error);
        }
    }
});

// Firebase Messaging will handle background messages automatically
// We just need to customize how they're displayed
self.addEventListener('push', (event) => {
    console.log('[SW] Push event received');

    if (!event.data) {
        console.log('[SW] No data in push event');
        return;
    }

    try {
        // Parse FCM payload
        const payload = event.data.json();
        console.log('[SW] Push payload:', payload);

        // Extract notification data from FCM message
        const notificationData = payload.notification || {};
        const data = payload.data || {};

        const title = notificationData.title || 'DietSenegal';
        const options = {
            body: notificationData.body || 'Vous avez une nouvelle notification',
            icon: notificationData.icon || data.icon || '/uploads/company/favicon.png',
            badge: '/uploads/company/favicon.png',
            tag: data.type || 'dietzone',
            data: {
                url: data.click_action || '/dietetic/portal',
                ...data
            },
            requireInteraction: false,
            vibrate: [200, 100, 200]
        };

        // Add image if provided
        if (notificationData.image) {
            options.image = notificationData.image;
        }

        console.log('[SW] Showing notification:', title);

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (error) {
        console.error('[SW] Error handling push:', error);
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/dietetic/portal';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Try to focus an existing window first
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }

                // No existing window, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('[SW] Notification closed');
});

console.log('[SW] All event listeners registered');
