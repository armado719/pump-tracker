

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Registrar service worker para PWA/offline
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  });
}
