  </div>
  <!-- /.m-app -->

  <!-- SweetAlert2 (pesan berhasil/gagal - sama seperti versi desktop) -->
  <script src="<?= base_url('vendors/'); ?>plugins/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?= base_url('assets'); ?>/js/myscript.js?v=<?= filemtime(FCPATH . 'assets/js/myscript.js'); ?>"></script>
  <script>
    // Base URL aplikasi bisa di subfolder (mis. /qiraati/), bukan selalu root domain -
    // sw.js perlu path ini, tidak boleh di-hardcode "/sw.js".
    window.APP_BASE_URL = <?= json_encode(base_url()); ?>;
  </script>
  <script src="<?= base_url('assets'); ?>/js/mobile.js?v=<?= filemtime(FCPATH . 'assets/js/mobile.js'); ?>"></script>
</body>

</html>
