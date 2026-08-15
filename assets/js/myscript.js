const flashData = $('.flash-data').data('flashdata');
const title = $('.flash-data').data('title');

// sweetalert data
if (flashData) {
	// Pesan gagal/tidak bisa diproses (mis. diblokir FK, validasi gagal) ditandai kata kunci ini -
	// tampil merah (icon 'error'), bukan hijau seperti pesan berhasil.
	const kataGagal = ['tidak dapat', 'tidak boleh', 'tidak ditemukan', 'tidak valid', 'tidak diizinkan', 'gagal', 'sudah digunakan', 'sudah terdaftar', 'password salah', 'belum lengkap'];
	const teksKecil = flashData.toLowerCase();
	const gagal = kataGagal.some(function (kata) { return teksKecil.indexOf(kata) !== -1; });

	Swal.fire({
		title: title,
		text: flashData,
		icon: gagal ? 'error' : 'success'
	})
}

// sweetalert tombol reset
$('.tombol-reset').on('click', function (e) {
	// const data = $(this).attr('namaData')
	const tipeData = $(this).attr('tipeData')
	e.preventDefault();
	const href = $(this).attr('href');

	Swal.fire({
		title: 'Reset ' + title + '?',
		text: 'Yakin reset data ' + tipeData + '?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Batal',
		confirmButtonText: 'Reset Data!'
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	})

});

// sweetalert tombol hapus
$('.tombol-hapus').on('click', function (e) {
	const data = $(this).attr('namaData')
	const tipeData = $(this).attr('tipeData')
	e.preventDefault();
	const href = $(this).attr('href');
	
	Swal.fire({
		title: 'Hapus ' + title + '?',
		text: 'Yakin hapus ' + tipeData + ' ' + data + '?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Batal',
		confirmButtonText: 'Hapus Data!'
	}).then((result) => {
		if (result.value) {
			document.location.href = href;
		}
	})
	
});



