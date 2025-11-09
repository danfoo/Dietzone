// Firebase Cloud Messaging Service Worker
// This file must be at the root of your site

// Give the service worker access to Firebase Messaging
// Note: Firebase SDK will be imported dynamically
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Initialize Firebase in the service worker
// Config will be loaded from the main thread
let firebaseConfig = null;
let messaging = null;

// Listen for messages from the main thread to initialize Firebase
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        firebaseConfig = event.data.config;

        if (firebaseConfig && !messaging) {
            // Initialize Firebase
            firebase.initializeApp(firebaseConfig);
            messaging = firebase.messaging();

            console.log('[SW] Firebase initialized with config');
        }
    }
});

// Handle background messages
self.addEventListener('push', (event) => {
    console.log('[SW] Push received:', event);

    if (!event.data) {
        console.log('[SW] No data in push event');
        return;
    }

    try {
        const data = event.data.json();
        const notification = data.notification || {};
        const notificationData = data.data || {};

        const notificationTitle = notification.title || 'DietZone';
        const notificationOptions = {
            body: notification.body || 'Vous avez une nouvelle notification',
            icon: notification.icon || '/uploads/company/favicon.png',
            badge: notification.badge || '/uploads/company/favicon.png',
            tag: notificationData.type || 'dietzone-notification',
            data: {
                url: notification.click_action || notificationData.click_action || '/dietetic/portal',
                ...notificationData
            },
            requireInteraction: false,
            silent: false
        };

        if (notification.image) {
            notificationOptions.image = notification.image;
        }

        event.waitUntil(
            self.registration.showNotification(notificationTitle, notificationOptions)
        );
    } catch (error) {
        console.error('[SW] Error handling push:', error);
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event);

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/dietetic/portal';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open with this URL
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    const clientUrl = new URL(client.url);
                    const targetUrl = new URL(urlToOpen, self.location.origin);

                    if (clientUrl.pathname === targetUrl.pathname && 'focus' in client) {
                        return client.focus();
                    }
                }

                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('[SW] Notification closed:', event);
});

console.log('[SW] Firebase Messaging Service Worker loaded');
