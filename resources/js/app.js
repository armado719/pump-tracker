

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Registrar service worker — desinstala cualquier SW antiguo primero
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.getRegistrations().then(registrations => {
      for (const reg of registrations) {
        const script = reg.active?.scriptURL || reg.installing?.scriptURL || '';
        if (!script.endsWith('/sw.js')) {
          reg.unregister();
        }
      }
    });
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  });
}
