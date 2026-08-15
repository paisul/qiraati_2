<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Predikat_Hasil1 extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		cek_login();
		$this->load->model('Predikat_hasil1_M');
	}

	// List all your items
	public function index()
	{
		$pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

		$data = [
			'title' => 'Predikat Hasil Ujian Kelas 1 dan 2',
			'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
			'predikathasil1' => $this->Predikat_hasil1_M->getAllPredikatHasil1(),
			'pesan' => $pesan,
			'isi' => tampilan_mobile() ? 'nilai/mobile-predikat_hasil1' : 'nilai/v-predikat_hasil1',
		];

		$this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
	}

	// Add a new item
	public function add()
	{
		$data = [
			'BatasNilaiBawah1' => $this->input->post('batas_bawah1'),
			'BatasNilaiAtas1' => $this->input->post('batas_atas1'),
			'PredikatHasil1' => $this->input->post('predikat_hasil1'),
			'KetHasil1' => $this->input->post('ket_hasil1'),
		];
		$this->Predikat_hasil1_M->addPredikatHasil1($data);
		redirect('nilai/predikat_hasil1?pesan=' . rawurlencode('Berhasil ditambahkan!'));
	}

	//Update one item
	public function update($id)
	{
		$data = [
			'IdPredikatHasil1' => $id,
			'BatasNilaiBawah1' => $this->input->post('batas_bawah1'),
			'BatasNilaiAtas1' => $this->input->post('batas_atas1'),
			'PredikatHasil1' => $this->input->post('predikat_hasil1'),
			'KetHasil1' => $this->input->post('ket_hasil1'),
		];
		$this->Predikat_hasil1_M->updatePredikatHasil1($data);
		redirect('nilai/predikat_hasil1?pesan=' . rawurlencode('Berhasil diubah!'));
	}

	//Delete one item
	public function delete($id)
	{
		$data = ['IdPredikatHasil1' => $id];
		$this->Predikat_hasil1_M->deletePredikatHasil1($data);
		redirect('nilai/predikat_hasil1?pesan=' . rawurlencode('Berhasil dihapus!'));
	}
}

/* End of file Predikat_Hasil1.php */
