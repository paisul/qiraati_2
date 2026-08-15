// Registrasi service worker + tombol "Pasang" (Add to Home Screen) untuk PWA Portal Wali.

if ('serviceWorker' in navigator && window.APP_BASE_URL) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register(window.APP_BASE_URL + 'sw.js').catch(function () {
      // Diamkan saja kalau gagal (mis. scope tidak cocok) - bukan fitur inti, jangan ganggu pemakaian.
    });
  });
}

let deferredInstallPrompt = null;

window.addEventListener('beforeinstallprompt', function (event) {
  event.preventDefault();
  deferredInstallPrompt = event;

  const bar = document.getElementById('mInstallBar');
  if (bar) {
    bar.classList.add('show');
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const btn = document.getElementById('mInstallBtn');
  if (btn) {
    btn.addEventListener('click', function () {
      const bar = document.getElementById('mInstallBar');
      if (bar) {
        bar.classList.remove('show');
      }

      if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        deferredInstallPrompt = null;
      }
    });
  }

  // Drawer menu (hamburger di topbar, atau tombol "Menu" di bottom nav Musyrif) - dipakai di semua
  // halaman Admin/Musyrif/Wali yang lewat templates/mobile/header_generic.php + drawer_menu.php.
  const drawer = document.getElementById('mDrawer');
  const drawerOverlay = document.getElementById('mDrawerOverlay');
  const drawerClose = document.getElementById('mDrawerClose');

  function bukaDrawer(event) {
    if (event) event.preventDefault();
    if (drawer) drawer.classList.add('show');
    if (drawerOverlay) drawerOverlay.classList.add('show');
  }

  function tutupDrawer() {
    if (drawer) drawer.classList.remove('show');
    if (drawerOverlay) drawerOverlay.classList.remove('show');
  }

  document.querySelectorAll('#mHamburger, [data-drawer-trigger]').forEach(function (pemicu) {
    pemicu.addEventListener('click', bukaDrawer);
  });
  if (drawerClose) drawerClose.addEventListener('click', tutupDrawer);
  if (drawerOverlay) drawerOverlay.addEventListener('click', tutupDrawer);

  // Toggle generik (dipakai accordion drawer & panel form Tambah/Ubah di halaman CRUD mobile) -
  // satu handler global supaya halaman baru tidak perlu menyalin ulang script ini.
  document.querySelectorAll('[data-toggle-target]').forEach(function (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const target = document.querySelector(toggleBtn.dataset.toggleTarget);
      if (target) {
        target.hidden = !target.hidden;
        // aria-expanded dipakai murni untuk animasi panah drawer (lihat .m-drawer-chevron di
        // mobile.css) - tidak berpengaruh ke panel Tambah/Ubah lain yang juga pakai atribut ini.
        toggleBtn.setAttribute('aria-expanded', target.hidden ? 'false' : 'true');
      }
    });
  });

  // Highlight menu drawer yang sedang aktif (halaman yang sedang dibuka) - kalau link itu ada di
  // dalam grup sub-menu yang sedang tertutup, buka otomatis grupnya supaya highlight-nya terlihat.
  document.querySelectorAll('.m-drawer-link').forEach(function (link) {
    try {
      if (new URL(link.href).pathname === location.pathname) {
        link.classList.add('m-drawer-active');
        const grup = link.closest('.m-drawer-group');
        if (grup && grup.hidden) {
          grup.hidden = false;
          const pemicu = document.querySelector('[data-toggle-target="#' + grup.id + '"]');
          if (pemicu) pemicu.setAttribute('aria-expanded', 'true');
        }
      }
    } catch (e) {
      // Abaikan href yang bukan URL absolut (mis. "javascript:void(0)").
    }
  });
});

// Bunyi notifikasi "Info Penting" (dashboard Wali/Musyrif) - dibangkitkan langsung lewat Web
// Audio API (nada pendek), jadi tidak perlu file audio terpisah. Dipanggil dari
// wali/mobile/dashboard.php & halaman_musyrif/mobile-dashboard.php.
function mainkanBunyiNotifikasi() {
  try {
    var Ctx = window.AudioContext || window.webkitAudioContext;
    if (!Ctx) return;
    var ctx = new Ctx();
    // Kalau context kebentur kebijakan autoplay browser (suspended), coba resume dulu - tidak
    // dijamin selalu berhasil tanpa gestur pengguna, tapi tidak merugikan untuk dicoba.
    if (ctx.state === 'suspended' && ctx.resume) ctx.resume();
    var osc = ctx.createOscillator();
    var gain = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.value = 880;
    gain.gain.setValueAtTime(0.2, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + 0.5);
  } catch (e) {
    // Diamkan - browser lama/kebijakan autoplay kadang menolak, bukan fitur inti.
  }
}

// Dipanggil lewat onerror= pada <img> pasfoto (data-fallback-class/-text disiapkan di HTML-nya) -
// kalau file fotonya gagal dimuat (mis. sudah dihapus/rusak dari server, sisa upload lama yang
// gagal), ganti jadi tampilan fallback yang sama seperti waktu memang belum ada foto sama sekali,
// daripada ikon gambar rusak bawaan browser yang kelihatan seperti bug aplikasi.
function mFotoGagalMuat(img) {
  var pengganti = document.createElement('div');
  if (img.id) pengganti.id = img.id;
  if (img.dataset.fallbackClass) pengganti.className = img.dataset.fallbackClass;
  if (img.dataset.fallbackStyle) pengganti.style.cssText = img.dataset.fallbackStyle;

  if (img.dataset.fallbackIcon) {
    var ikon = document.createElement('i');
    ikon.className = img.dataset.fallbackIcon;
    pengganti.appendChild(ikon);
  } else {
    pengganti.textContent = img.dataset.fallbackText || '';
  }

  img.replaceWith(pengganti);
}

// $kunci: kunci localStorage unik per akun (mis. "pengumuman_terakhir_" + username), supaya HP
// yang dipakai gantian beberapa akun tidak saling ketuker penandanya. $idTerbaru: IdPengumuman
// paling baru yang sedang ditampilkan di halaman ini (0/null kalau belum ada pengumuman sama sekali).
function cekPengumumanBaru(kunci, idTerbaru) {
  if (!idTerbaru) return;

  var idTersimpan = parseInt(localStorage.getItem(kunci) || '0', 10);
  if (idTersimpan && idTerbaru > idTersimpan) {
    mainkanBunyiNotifikasi();
  }
  localStorage.setItem(kunci, String(idTerbaru));
}

// Toggle tema gelap/terang (tombol di topbar, lihat header_generic.php/header_wali.php) - flip
// langsung di layar (tanpa reload) lalu simpan ke akun lewat AJAX, supaya ikut terbawa di HP lain.
(function () {
  var tombolTema = document.getElementById('mThemeToggle');
  if (!tombolTema) return;

  tombolTema.addEventListener('click', function () {
    var sekarang = document.documentElement.getAttribute('data-theme') === 'gelap' ? 'gelap' : 'terang';
    var baru = sekarang === 'gelap' ? 'terang' : 'gelap';

    document.documentElement.setAttribute('data-theme', baru);
    var ikon = tombolTema.querySelector('i');
    if (ikon) {
      ikon.className = baru === 'gelap' ? 'fas fa-sun' : 'fas fa-moon';
    }

    fetch(tombolTema.dataset.toggleUrl, { method: 'POST' }).catch(function () {
      // Diamkan - tampilan sudah berubah di layar ini, kalau gagal tersimpan cukup coba lagi nanti.
    });
  });
})();

// Tap-langsung-di-foto untuk ganti Pasfoto/Tanda Tangan (lihat .m-photo-upload di mobile.css) -
// begitu file dipilih, preview di layar langsung diganti (pakai object URL sementara) tanpa
// perlu menunggu Simpan ditekan/reload halaman.
document.querySelectorAll('[data-photo-input]').forEach(function (input) {
  input.addEventListener('change', function () {
    if (!input.files || !input.files[0]) return;

    var target = document.querySelector(input.dataset.photoInput);
    if (!target) return;

    var url = URL.createObjectURL(input.files[0]);

    if (target.tagName === 'IMG') {
      target.src = url;
      return;
    }

    // Sebelumnya belum ada foto (elemen fallback berisi inisial/ikon) - ganti jadi <img> di
    // tempat yang sama supaya preview-nya benar-benar tampil.
    var img = document.createElement('img');
    img.id = target.id;
    img.className = target.className;
    img.src = url;
    target.replaceWith(img);
  });
});

// Tombol Simpan sticky yang baru aktif setelah form-nya benar-benar diubah (lihat .m-btn-sticky
// di mobile.css) - dipakai di form Ubah Profil Musyrif & form Data Santri supaya user tidak asal
// tekan Simpan tanpa mengubah apa pun.
document.querySelectorAll('[data-dirty-submit]').forEach(function (tombol) {
  var form = tombol.closest('form');
  if (!form) return;

  function tandaiBerubah() {
    tombol.disabled = false;
    tombol.classList.add('is-dirty');
  }

  form.addEventListener('input', tandaiBerubah);
  form.addEventListener('change', tandaiBerubah);
});

// Tombol Simpan Data Dana - beda dari [data-dirty-submit] generik di atas (yang aktif begitu ADA
// perubahan apa pun): di sini baru aktif kalau Perihal terisi DAN salah satu Jumlah Masuk/Keluar
// lebih dari 0, karena menyimpan dengan Perihal kosong atau kedua jumlah nol tidak ada gunanya.
// Dicek ulang tiap kali ada perubahan, bukan cuma sekali di awal.
document.querySelectorAll('[data-dana-submit]').forEach(function (tombol) {
  var form = tombol.closest('form');
  if (!form) return;

  var perihal = form.querySelector('[name="perihal"]');
  var jumlahMasuk = form.querySelector('[name="jumlah_masuk"]');
  var jumlahKeluar = form.querySelector('[name="jumlah_keluar"]');

  function angkaBersih(input) {
    return input ? (parseInt((input.value || '').replace(/\D/g, ''), 10) || 0) : 0;
  }

  function cekTerisi() {
    var perihalTerisi = !!(perihal && perihal.value.trim() !== '');
    var jumlahTerisi = angkaBersih(jumlahMasuk) > 0 || angkaBersih(jumlahKeluar) > 0;
    var valid = perihalTerisi && jumlahTerisi;

    tombol.disabled = !valid;
    tombol.classList.toggle('is-dirty', valid);
  }

  form.addEventListener('input', cekTerisi);
  form.addEventListener('change', cekTerisi);
  cekTerisi();
});

// Swipe-to-reveal Hapus gaya iOS (dipakai Data Santri, .m-swipe-wrap/.m-swipe-item/.m-swipe-actions
// di mobile.css) - geser kartu ke kiri memunculkan tombol Hapus di baliknya; ketuk kartu (tanpa
// geser) buka data-detail-url. Pakai Pointer Events supaya bekerja sama untuk sentuhan/mouse.
(function () {
  var kartuSemua = document.querySelectorAll('.m-swipe-item');
  if (!kartuSemua.length) return;

  var LEBAR_AKSI = 88;
  var kartuTerbuka = null;

  function tutupSemua(kecuali) {
    kartuSemua.forEach(function (k) {
      if (k !== kecuali) k.style.transform = 'translateX(0)';
    });
    if (kartuTerbuka !== kecuali) kartuTerbuka = null;
  }

  kartuSemua.forEach(function (kartu) {
    var mulaiX = 0;
    var mulaiY = 0;
    var posisiAwal = 0;
    var sedangGeser = false;
    var arahTerkunci = null;

    kartu.addEventListener('pointerdown', function (e) {
      // Biarkan dropdown status pilih sendiri - jangan mulai geser dari situ.
      if (e.target.closest('.m-santri-status-form')) return;

      mulaiX = e.clientX;
      mulaiY = e.clientY;
      posisiAwal = kartu === kartuTerbuka ? -LEBAR_AKSI : 0;
      sedangGeser = true;
      arahTerkunci = null;
      kartu.style.transition = 'none';
    });

    kartu.addEventListener('pointermove', function (e) {
      if (!sedangGeser) return;
      var dx = e.clientX - mulaiX;
      var dy = e.clientY - mulaiY;

      if (!arahTerkunci) {
        // Ambang 12px (bukan 6px) - jari manusia wajar bergeser sedikit walau cuma bermaksud
        // mengetuk, apalagi di area lebar seperti nama/NIS/kelas (bukan cuma pasfoto yang kecil).
        // Ambang terlalu kecil bikin ketukan di area lebar salah dikira geser lalu tidak terjadi
        // apa-apa (tidak buka Hapus, tidak juga ke Detail).
        if (Math.abs(dx) < 12 && Math.abs(dy) < 12) return;
        arahTerkunci = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
      }
      if (arahTerkunci === 'y') return;

      var posisi = Math.max(-LEBAR_AKSI, Math.min(0, posisiAwal + dx));
      kartu.style.transform = 'translateX(' + posisi + 'px)';
      e.preventDefault();
    });

    function selesaiGeser(e, dibatalkanBrowser) {
      if (!sedangGeser) return;
      sedangGeser = false;
      kartu.style.transition = '';

      // pointercancel artinya browser sudah mengambil alih gestur ini (paling sering karena
      // scroll vertikal lewat touch-action: pan-y) - dan arahTerkunci === 'y' artinya kita sendiri
      // sudah mendeteksi gerakan itu vertikal. Di dua kondisi ini JANGAN pernah dianggap ketukan -
      // sebelumnya kode ini menganggap "bukan geser horizontal" = ketukan = buka Detail, sehingga
      // geser layar ke bawah untuk scroll malah sering nyasar membuka Detail Santri.
      if (dibatalkanBrowser || arahTerkunci === 'y') {
        kartu.style.transform = kartu === kartuTerbuka ? 'translateX(-' + LEBAR_AKSI + 'px)' : 'translateX(0)';
        return;
      }

      var dx = (typeof e.clientX === 'number' ? e.clientX : mulaiX) - mulaiX;

      // Kalau memang belum sempat terkunci ke arah manapun, ATAU sudah terkunci horizontal tapi
      // jarak akhirnya masih kecil (baru sedikit lewat ambang 12px, bukan niat menggeser jauh) -
      // anggap tetap ketukan biasa, bukan geser gagal yang tidak melakukan apa-apa.
      if (arahTerkunci !== 'x' || Math.abs(dx) < 20) {
        if (kartu === kartuTerbuka) {
          tutupSemua(null);
        } else if (kartu.dataset.detailUrl) {
          location.href = kartu.dataset.detailUrl;
        }
        kartu.style.transform = kartu === kartuTerbuka ? 'translateX(-' + LEBAR_AKSI + 'px)' : 'translateX(0)';
        return;
      }

      var posisiAkhir = posisiAwal + dx;

      if (posisiAkhir < -LEBAR_AKSI / 2) {
        kartu.style.transform = 'translateX(-' + LEBAR_AKSI + 'px)';
        tutupSemua(kartu);
        kartuTerbuka = kartu;
      } else {
        kartu.style.transform = 'translateX(0)';
        if (kartuTerbuka === kartu) kartuTerbuka = null;
      }
    }

    kartu.addEventListener('pointerup', function (e) { selesaiGeser(e, false); });
    kartu.addEventListener('pointercancel', function (e) { selesaiGeser(e, true); });
  });
})();

// Geser dua arah gaya iOS untuk Data Dana (.m-dana-swipe-wrap/-item/-actions-kiri/-kanan) - beda
// dari swipe Santri di atas (yang cuma satu arah): geser kiri membuka Hapus (nongkrong di kanan
// kartu), geser kanan membuka Ubah (nongkrong di kiri kartu). Ketukan biasa (tanpa geser
// berarti) selalu menutup kembali ke posisi netral - Data Dana tidak punya halaman Detail
// seperti Santri, jadi tidak ada navigasi saat ketukan.
(function () {
  var kartuSemua = document.querySelectorAll('.m-dana-swipe-item');
  if (!kartuSemua.length) return;

  var LEBAR_AKSI = 88;
  var kartuTerbuka = null;

  function tutupSemua(kecuali) {
    kartuSemua.forEach(function (k) {
      if (k !== kecuali) {
        k.style.transform = 'translateX(0)';
        k.dataset.posisiTerbuka = '0';
      }
    });
    if (kartuTerbuka !== kecuali) kartuTerbuka = null;
  }

  kartuSemua.forEach(function (kartu) {
    kartu.dataset.posisiTerbuka = '0';

    var mulaiX = 0;
    var mulaiY = 0;
    var posisiAwal = 0;
    var sedangGeser = false;
    var arahTerkunci = null;

    kartu.addEventListener('pointerdown', function (e) {
      // Biarkan tombol/link di dalam kartu (mis. email Musyrif yang jadi tautan) berfungsi
      // sendiri - jangan mulai geser dari situ (dipakai juga oleh Data Musyrif, bukan cuma Dana).
      if (e.target.closest('.m-link-btn')) return;

      mulaiX = e.clientX;
      mulaiY = e.clientY;
      posisiAwal = parseInt(kartu.dataset.posisiTerbuka, 10) || 0;
      sedangGeser = true;
      arahTerkunci = null;
      kartu.style.transition = 'none';
    });

    kartu.addEventListener('pointermove', function (e) {
      if (!sedangGeser) return;
      var dx = e.clientX - mulaiX;
      var dy = e.clientY - mulaiY;

      if (!arahTerkunci) {
        if (Math.abs(dx) < 12 && Math.abs(dy) < 12) return;
        arahTerkunci = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
      }
      if (arahTerkunci === 'y') return;

      var posisi = Math.max(-LEBAR_AKSI, Math.min(LEBAR_AKSI, posisiAwal + dx));
      kartu.style.transform = 'translateX(' + posisi + 'px)';
      e.preventDefault();
    });

    function selesaiGeser(e, dibatalkanBrowser) {
      if (!sedangGeser) return;
      sedangGeser = false;
      kartu.style.transition = '';

      if (dibatalkanBrowser || arahTerkunci === 'y') {
        kartu.style.transform = 'translateX(' + posisiAwal + 'px)';
        return;
      }

      var dx = (typeof e.clientX === 'number' ? e.clientX : mulaiX) - mulaiX;

      if (arahTerkunci !== 'x' || Math.abs(dx) < 20) {
        kartu.style.transform = 'translateX(0)';
        kartu.dataset.posisiTerbuka = '0';
        if (kartuTerbuka === kartu) kartuTerbuka = null;
        return;
      }

      var posisiAkhir = posisiAwal + dx;

      if (posisiAkhir < -LEBAR_AKSI / 2) {
        kartu.style.transform = 'translateX(-' + LEBAR_AKSI + 'px)';
        kartu.dataset.posisiTerbuka = String(-LEBAR_AKSI);
        tutupSemua(kartu);
        kartuTerbuka = kartu;
      } else if (posisiAkhir > LEBAR_AKSI / 2) {
        kartu.style.transform = 'translateX(' + LEBAR_AKSI + 'px)';
        kartu.dataset.posisiTerbuka = String(LEBAR_AKSI);
        tutupSemua(kartu);
        kartuTerbuka = kartu;
      } else {
        kartu.style.transform = 'translateX(0)';
        kartu.dataset.posisiTerbuka = '0';
        if (kartuTerbuka === kartu) kartuTerbuka = null;
      }
    }

    kartu.addEventListener('pointerup', function (e) { selesaiGeser(e, false); });
    kartu.addEventListener('pointercancel', function (e) { selesaiGeser(e, true); });
  });
})();

// iPhone/iPad khusus - class ini dipakai mobile.css untuk mematikan layout footer sticky
// terpisah pada popup (.m-popup-footer ikut discroll bersama form biasa saja). Beberapa
// perilaku keyboard/viewport iOS Safari (auto-scroll waktu fokus input, visualViewport yang
// telat/berlebihan melapor) bikin penyesuaian dinamis lewat JS di bawah ini rawan meleset -
// lebih aman biarkan iOS pakai scroll biasa daripada terus mengejar posisi keyboard.
var adalahIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
if (adalahIOS) {
  document.documentElement.classList.add('m-ios');
}

// Popup gaya sheet (.m-popup-overlay, dipakai Data Dana) - "100vh"/"100%" tinggi layar bawaan
// browser TIDAK ikut menyusut waktu keyboard HP muncul, jadi tanpa ini sheet-nya (dan tombol
// Simpan sticky di dalamnya, lihat .m-popup-footer) bisa nyangkut di belakang keyboard.
// window.visualViewport melaporkan tinggi area yang BENAR-BENAR terlihat (sudah dikurangi
// keyboard) - overlay yang sedang terbuka disetel mengikuti itu tiap kali berubah. KHUSUS
// non-iOS (Android dkk) - di iOS popup sengaja dibiarkan scroll biasa (lihat .m-ios di atas
// & mobile.css), jadi penyesuaian dinamis ini tidak diperlukan/tidak dijalankan di sana.
if (window.visualViewport && !adalahIOS) {
  // Popup AKTIF dilacak lewat variabel ini (bukan query ulang "siapa yang sedang :not([hidden])"
  // tiap kali resize) - di iPhone, animasi buka/tutup keyboard memicu BERUNTUN event resize
  // selama animasinya jalan (bukan cuma sekali di akhir). Kalau popup A ditutup lalu popup B
  // buru-buru dibuka SEBELUM animasi penutupan keyboard A selesai, event resize susulan dari A
  // masih bisa ke-query "ketemu" popup B (karena B sudah kelihatan), lalu keliru menimpa tinggi
  // B dengan nilai transisi milik A - itu sebabnya cuma popup PERTAMA yang kelihatan benar.
  var popupAktif = null;

  // "height" saja tidak cukup - di Safari iOS, begitu sebuah input difokus, halaman ikut
  // discroll sendiri oleh iOS supaya field itu kelihatan (di atas keyboard), tapi elemen
  // position:fixed dengan top:0 tetap merujuk ke titik asal LAYOUT viewport, yang sekarang
  // sudah tidak sama lagi dengan bagian layar yang BENAR-BENAR kelihatan. offsetTop dari
  // visualViewport itulah selisihnya - tanpa ikut menyesuaikan top juga, popup bisa "nyangkut"
  // di bagian atas layar yang sebenarnya sudah discroll keluar dari pandangan.
  function sesuaikanPopupKeKeyboard() {
    if (!popupAktif) return;
    var vv = window.visualViewport;
    popupAktif.style.height = vv.height + 'px';
    popupAktif.style.top = vv.offsetTop + 'px';
  }

  window.visualViewport.addEventListener('resize', sesuaikanPopupKeKeyboard);
  window.visualViewport.addEventListener('scroll', sesuaikanPopupKeKeyboard);

  document.querySelectorAll('.m-popup-overlay').forEach(function (overlay) {
    new MutationObserver(function () {
      // Baik baru dibuka maupun baru ditutup, selalu bersihkan dulu tinggi/posisi manual
      // sebelumnya - supaya sesi berikutnya (overlay ini dibuka lagi, atau overlay lain) selalu
      // mulai dari keadaan bersih (penuh layar), bukan mewarisi sisa dari sesi keyboard sebelumnya.
      overlay.style.height = '';
      overlay.style.top = '';
      popupAktif = overlay.hidden ? null : overlay;
    }).observe(overlay, { attributes: true, attributeFilter: ['hidden'] });
  });
}
