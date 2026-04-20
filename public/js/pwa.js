/**
 * pwa.js – Rebencia PWA Frontend
 *
 * Responsabilités :
 *   1. Enregistrement du Service Worker (sw.js)
 *   2. Demande de permission pour les notifications navigateur
 *   3. Récupération de la clé publique VAPID et abonnement push
 *   4. Synchronisation de la subscription avec le backend
 */

'use strict';

(async function () {
    // ── 1. Service Worker ─────────────────────────────────────────────
    if (!('serviceWorker' in navigator)) return;

    let registration;
    try {
        registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
    } catch (e) {
        console.warn('[PWA] SW registration failed:', e);
        return;
    }

    // ── 2. Push supporté ? ────────────────────────────────────────────
    if (!('PushManager' in window)) return;

    // Ne pas re-demander si déjà refusé
    if (Notification.permission === 'denied') return;

    // ── 3. Récupérer la clé VAPID publique ───────────────────────────
    let vapidPublicKey;
    try {
        const res = await fetch('/api/push/vapid-key', { credentials: 'same-origin' });
        if (!res.ok) return;
        const data = await res.json();
        vapidPublicKey = data.publicKey;
    } catch (e) {
        return; // Push non configuré côté serveur – on s'arrête silencieusement
    }

    if (!vapidPublicKey) return;

    // ── 4. Vérifier si déjà abonné ───────────────────────────────────
    let subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        // Demander permission (seulement si pas encore accordée)
        let permission = Notification.permission;
        if (permission === 'default') {
            permission = await Notification.requestPermission();
        }
        if (permission !== 'granted') return;

        // S'abonner
        try {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly:      true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
            });
        } catch (e) {
            console.warn('[PWA] Push subscribe failed:', e);
            return;
        }
    }

    // ── 5. Envoyer la subscription au backend ─────────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    try {
        await fetch('/api/push/subscribe', {
            method:      'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(subscription),
        });
    } catch (e) {
        console.warn('[PWA] Failed to send subscription to server:', e);
    }

    // ── Utilitaire : Base64 URL → Uint8Array ─────────────────────────
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw     = atob(base64);
        return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
    }
})();
