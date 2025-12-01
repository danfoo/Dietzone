// Firebase Cloud Messaging Service Worker
// Version: 2025-12-01-v3 (fixed importScripts)

// CRITICAL: importScripts MUST be called at global scope, NOT in try/catch
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

console.log('[SW] ✅ Firebase Service Worker v3 loaded');

// Skip waiting to activate immediately
self.addEventListener('install', (event) => {
    console.log('[SW] Install event');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activate event');
    event.waitUntil(clients.claim());
});

// Initialize Firebase when config is received
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        console.log('[SW] Received Firebase config');

        try {
            if (!firebase.apps.length) {
                firebase.initializeApp(event.data.config);
                console.log('[SW] ✅ Firebase initialized');
            }
        } catch (error) {
            console.error('[SW] ❌ Firebase init error:', error);
        }
    }
});

// Handle push notifications
self.addEventListener('push', (event) => {
    console.log('[SW] Push received');

    if (!event.data) {
        return;
    }

    try {
        const payload = event.data.json();
        const notification = payload.notification || {};
        const data = payload.data || {};

        const title = notification.title || 'DietSenegal';
        const options = {
            body: notification.body || '',
            icon: notification.icon || '/uploads/company/favicon.png',
            badge: '/uploads/company/favicon.png',
            data: { url: data.click_action || '/dietetic/portal' }
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (error) {
        console.error('[SW] Push error:', error);
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    event.notification.close();

    const url = event.notification.data?.url || '/dietetic/portal';

    event.waitUntil(
        clients.openWindow(url)
    );
});

console.log('[SW] ✅ Service Worker v3 ready');
