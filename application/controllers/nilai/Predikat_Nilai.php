<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Predikat_Nilai extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //Load Dependencies
        cek_login();
        $this->load->model('Predikat_nilai_M');
    }

    // List all your items
    public function index()
    {
        $pesan = $this->input->get('pesan') ?: $this->session->flashdata('pesan');

        $data = [
            'title' => 'Data Predikat Nilai',
            'user' => $this->db->get_where('login', ['username' => $this->session->userdata('username')])->row_array(),
            'predikatnilai' => $this->Predikat_nilai_M->getAllPredikatNilai(),
            'pesan' => $pesan,
            'isi' => tampilan_mobile() ? 'nilai/mobile-predikat_nilai' : 'nilai/v-predikat_nilai',
        ];

        $this->load->view(tampilan_mobile() ? 'templates/wrapper-mobile-simple' : 'templates/wrapper-admin', $data);
    }

    // Add a new item
    public function add()
    {
        $data = [
            'BatasNilaiBawah' => $this->input->post('batas_bawah'),
            'BatasNilaiAtas' => $this->input->post('batas_atas'),
            'PredikatNilai' => $this->input->post('predikat_nilai'),
            'KetNilai' => $this->input->post('ket_nilai'),
        ];
        $this->Predikat_nilai_M->addPredikatNilai($data);
        redirect('nilai/predikat_nilai?pesan=' . rawurlencode('Berhasil ditambahkan!'));
    }

    //Update one item
    public function update($id)
    {
        $data = [
            'IdPredikatNilai' => $id,
            'BatasNilaiBawah' => $this->input->post('batas_bawah'),
            'BatasNilaiAtas' => $this->input->post('batas_atas'),
            'PredikatNilai' => $this->input->post('predikat_nilai'),
            'KetNilai' => $this->input->post('ket_nilai'),
        ];
        $this->Predikat_nilai_M->updatePredikatNilai($data);
        redirect('nilai/predikat_nilai?pesan=' . rawurlencode('Berhasil diubah!'));
    }

    //Delete one item
    public function delete($id)
    {
        $data = ['IdPredikatNilai' => $id];
        $this->Predikat_nilai_M->deletePredikatNilai($data);
        redirect('nilai/predikat_nilai?pesan=' . rawurlencode('Berhasil dihapus!'));
    }
}

/* End of file Musyrif.php */
