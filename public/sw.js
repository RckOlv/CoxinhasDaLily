self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : { title: 'Coxinhas da Lily', body: 'Notificación nueva' };

    const options = {
        body: data.body,
        icon: '/img/logolily.png',
        badge: '/img/logolily.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
        actions: [
            { action: 'open', title: 'Ver', icon: '/img/logolily.png' },
        ],
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
