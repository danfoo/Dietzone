// Test Service Worker - Ultra simple pour diagnostiquer
console.log('[TEST-SW] Service Worker loaded at', new Date().toISOString());

self.addEventListener('install', (event) => {
    console.log('[TEST-SW] Install event triggered');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[TEST-SW] Activate event triggered');
    event.waitUntil(clients.claim());
});

console.log('[TEST-SW] All events registered successfully');
