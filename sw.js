// Service worker minimal untuk PWA Portal Wali - HANYA menyimpan cache aset statis (CSS/JS/ikon)
// supaya kunjungan berikutnya lebih cepat & syarat "installable" terpenuhi. SENGAJA TIDAK
// menyimpan cache halaman PHP dinamis (setoran/rapor/dashboard) - ini aplikasi monitoring
// dengan data yang selalu berubah, cache offline untuk itu berisiko menampilkan data basi.

// CACHE_NAME dinaikkan setiap ada perubahan mobile.css/mobile.js supaya HP yang sudah pernah
// pasang PWA ini langsung ambil versi baru (bukan strategi cache-first lagi, lihat fetch di
// bawah - network-first sekarang, cache cuma jadi cadangan waktu offline).
const CACHE_NAME = 'qiroati-wali-shell-v2';
const SHELL_ASSETS = [
  'assets/css/mobile.css',
  'assets/js/mobile.js',
  'assets/icons/icon-192.png',
  'assets/icons/icon-512.png',
];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      return cache.addAll(SHELL_ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(
        keys.filter(function (key) { return key !== CACHE_NAME; })
          .map(function (key) { return caches.delete(key); })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', function (event) {
  const url = event.request.url;
  const isShellAsset = SHELL_ASSETS.some(function (path) { return url.indexOf(path) !== -1; });

  if (!isShellAsset) {
    return; // biarkan browser handle seperti biasa (langsung ke server, tidak lewat cache)
  }

  // Network-first (bukan cache-first) - selalu coba ambil versi terbaru dulu selama online,
  // cache cuma dipakai kalau benar-benar offline. Sebelumnya cache-first bikin CSS/JS lama
  // "nyangkut" terus di HP yang sudah pasang PWA ini, walau kodenya sudah diperbarui di server.
  event.respondWith(
    fetch(event.request).then(function (fresh) {
      caches.open(CACHE_NAME).then(function (cache) { cache.put(event.request, fresh.clone()); });
      return fresh;
    }).catch(function () {
      return caches.match(event.request);
    })
  );
});
