// Firebase Cloud Messaging Service Worker
// This file handles background push notifications

// Import Firebase scripts for service worker
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

// Firebase configuration will be injected dynamically
// Initialize Firebase (config will be loaded from server)
let firebaseApp;
let messaging;

// Listen for messages from the main thread to initialize Firebase
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'INIT_FIREBASE') {
        const firebaseConfig = event.data.config;

        if (!firebaseApp) {
            firebaseApp = firebase.initializeApp(firebaseConfig);
            messaging = firebase.messaging();

        }
    }
});

// Handle background messages
self.addEventListener('push', function(event) {

    if (!event.data) {
        return;
    }

    try {
        const data = event.data.json();

        const notificationTitle = data.notification?.title || data.data?.title || 'Nouvelle notification';
        const notificationOptions = {
            body: data.notification?.body || data.data?.body || '',
            icon: data.notification?.icon || data.data?.icon || '/uploads/company/favicon.png',
            badge: data.notification?.badge || '/uploads/company/favicon.png',
            tag: data.data?.tag || 'dietetic-notification',
            requireInteraction: false,
            data: {
                url: data.data?.url || data.notification?.click_action || '/',
                notificationId: data.data?.notification_id
            },
            vibrate: [200, 100, 200],
            actions: [
                {
                    action: 'open',
                    title: 'Ouvrir',
                    icon: '/modules/dietetic/assets/images/icon-open.png'
                },
                {
                    action: 'dismiss',
                    title: 'Fermer',
                    icon: '/modules/dietetic/assets/images/icon-close.png'
                }
            ]
        };

        event.waitUntil(
            self.registration.showNotification(notificationTitle, notificationOptions)
        );
    } catch (error) {
        console.error('[SW] Error processing push event:', error);
    }
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {

    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/';

    // Handle action buttons
    if (event.action === 'dismiss') {
        return; // Just close the notification
    }

    // Open or focus the app
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Check if there's already a window/tab open
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }

            // If no matching window found, open a new one
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Handle notification close
self.addEventListener('notificationclose', function(event) {
});

