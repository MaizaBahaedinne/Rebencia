/**
 * sw.js – Service Worker Rebencia
 *
 * Stratégies :
 *   - Navigation (pages HTML)  : Network-first avec fallback offline
 *   - Assets statiques (JS/CSS): Cache-first avec revalidation en arrière-plan
 *   - Événement push           : Affiche la notification native navigateur
 *
 * VERSION: 1.0.0
 */

'use strict';

const CACHE_NAME    = 'rebencia-v1';
const OFFLINE_PAGE  = '/offline.html';

// Ressources pré-cachées à l'installation
const PRECACHE_URLS = [
    '/admin/dashboard',
    OFFLINE_PAGE,
];

// ── Install ──────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

// ── Activate – nettoyage des anciens caches ─────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// ── Fetch – Network-first pour HTML, Cache-first pour assets ────────
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Ignorer les requêtes non-GET et les API/AJAX
    if (request.method !== 'GET') return;
    if (request.headers.get('accept')?.includes('application/json')) return;
    if (request.url.includes('/api/') || request.url.includes('notifications/unread')) return;

    const isNavigation = request.mode === 'navigate';

    if (isNavigation) {
        // Network-first pour les pages
        event.respondWith(
            fetch(request)
                .then((res) => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then((c) => c.put(request, clone));
                    return res;
                })
                .catch(() => caches.match(request).then((r) => r || caches.match(OFFLINE_PAGE)))
        );
    } else {
        // Cache-first pour les assets
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) {
                    // Revalidation en arrière-plan
                    fetch(request).then((res) => {
                        caches.open(CACHE_NAME).then((c) => c.put(request, res));
                    }).catch(() => {});
                    return cached;
                }
                return fetch(request).then((res) => {
                    const clone = res.clone();
                    caches.open(CACHE_NAME).then((c) => c.put(request, clone));
                    return res;
                }).catch(() => caches.match(OFFLINE_PAGE));
            })
        );
    }
});

// ── Push – Affichage de la notification navigateur ──────────────────
self.addEventListener('push', (event) => {
    let payload = {};
    try {
        payload = event.data?.json() ?? {};
    } catch (e) {
        payload = { title: 'Rebencia', body: event.data?.text() ?? 'Nouvelle notification' };
    }

    const title   = payload.title   ?? 'Rebencia';
    const options = {
        body:    payload.body    ?? payload.message ?? '',
        icon:    payload.icon    ?? '/img/icon-192.png',
        badge:   '/img/icon-192.png',
        tag:     payload.tag     ?? 'rebencia-notif',
        data:    { url: payload.url ?? '/admin/dashboard' },
        vibrate: [200, 100, 200],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// ── NotificationClick – ouvrir / focus l'onglet ─────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url ?? '/admin/dashboard';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Chercher un onglet déjà ouvert sur la bonne URL
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            // Sinon ouvrir un nouvel onglet
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
    );
});
