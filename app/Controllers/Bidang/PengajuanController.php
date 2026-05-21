<?php

namespace App\Controllers\Bidang;

use App\Controllers\BaseController;
use App\Models\PengajuanBidangModel;
use App\Models\PengajuanBidangItemModel;
use App\Models\HistoriPengajuanModel;
use App\Models\UserProfileModel;

class PengajuanController extends BaseController
{
    private function getProfile()
    {
        $profileModel = new UserProfileModel();
        return $profileModel->where('user_id', auth()->id())->first();
    }

    public function index()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['bidang_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $model = new PengajuanBidangModel();
        $pengajuans = $model->where('bidang_id', $profile['bidang_id'])->orderBy('created_at', 'DESC')->findAll();

        return view('bidang/pengajuan/index', [
            'pengajuans' => $pengajuans,
            'profile'    => $profile
        ]);
    }

    public function create()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['bidang_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        return view('bidang/pengajuan/form', [
            'profile' => $profile
        ]);
    }

    public function store()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['bidang_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $rules = [
            'tahun_anggaran' => 'required|numeric',
            'items'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $pengajuanModel = new PengajuanBidangModel();
        $itemModel = new PengajuanBidangItemModel();
        $historiModel = new HistoriPengajuanModel();

        $nomorPengajuan = 'REQ-' . $profile['opd_id'] . '-' . $profile['bidang_id'] . '-' . time();

        $pengajuanId = $pengajuanModel->insert([
            'nomor_pengajuan' => $nomorPengajuan,
            'bidang_id'       => $profile['bidang_id'],
            'opd_id'          => $profile['opd_id'],
            'tahun_anggaran'  => $this->request->getPost('tahun_anggaran'),
            'status'          => 'DRAFT',
            'created_by'      => auth()->id(),
        ]);

        $items = $this->request->getPost('items');
        foreach ($items as $item) {
            $itemModel->insert([
                'pengajuan_bidang_id' => $pengajuanId,
                'nama_aset'           => $item['nama_aset'],
                'spesifikasi'         => $item['spesifikasi'],
                'jumlah'              => $item['jumlah'],
                'satuan'              => $item['satuan'],
                'estimasi_harga'      => $item['estimasi_harga'],
                'kegunaan'            => $item['kegunaan'],
            ]);
        }

        $historiModel->insert([
            'pengajuan_type' => 'bidang',
            'reference_id'   => $pengajuanId,
            'status_awal'    => '-',
            'status_akhir'   => 'DRAFT',
            'catatan'        => 'Membuat draf usulan pengadaan.',
            'actor_id'       => auth()->id(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengajuan.');
        }

        return redirect()->to('/bidang/pengajuan')->with('message', 'Pengajuan berhasil disimpan sebagai DRAFT.');
    }

    public function detail($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel->find($id);

        if (!$pengajuan || $pengajuan['bidang_id'] != $profile['bidang_id']) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Pengajuan tidak ditemukan.');
        }

        $itemModel = new PengajuanBidangItemModel();
        $items = $itemModel->where('pengajuan_bidang_id', $id)->findAll();

        $historiModel = new HistoriPengajuanModel();
        $histories = $historiModel
            ->select('histori_pengajuan.*, user_profiles.nama_lengkap')
            ->join('user_profiles', 'user_profiles.user_id = histori_pengajuan.actor_id')
            ->where('pengajuan_type', 'bidang')
            ->where('reference_id', $id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('bidang/pengajuan/detail', [
            'pengajuan' => $pengajuan,
            'items'     => $items,
            'histories' => $histories
        ]);
    }

    public function kirim($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel->find($id);

        if (!$pengajuan || $pengajuan['bidang_id'] != $profile['bidang_id']) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Pengajuan tidak ditemukan.');
        }

        if (!in_array($pengajuan['status'], ['DRAFT', 'DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Pengajuan tidak dapat dikirim.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $statusAwal = $pengajuan['status'];
        $statusAkhir = 'DIAJUKAN';

        $pengajuanModel->update($id, [
            'status' => $statusAkhir
        ]);

        $historiModel = new HistoriPengajuanModel();
        $historiModel->insert([
            'pengajuan_type' => 'bidang',
            'reference_id'   => $id,
            'status_awal'    => $statusAwal,
            'status_akhir'   => $statusAkhir,
            'catatan'        => 'Mengirim usulan pengadaan ke Admin OPD.',
            'actor_id'       => auth()->id(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal mengirim pengajuan.');
        }

        return redirect()->to('/bidang/pengajuan')->with('message', 'Pengajuan berhasil dikirim.');
    }

    public function edit($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel->find($id);

        if (!$pengajuan || $pengajuan['bidang_id'] != $profile['bidang_id']) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Pengajuan tidak ditemukan.');
        }

        if (!in_array($pengajuan['status'], ['DRAFT', 'DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Hanya pengajuan draf/revisi yang dapat diedit.');
        }

        $itemModel = new PengajuanBidangItemModel();
        $items = $itemModel->where('pengajuan_bidang_id', $id)->findAll();

        return view('bidang/pengajuan/form', [
            'profile'   => $profile,
            'pengajuan' => $pengajuan,
            'items'     => $items
        ]);
    }

    public function update($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel->find($id);

        if (!$pengajuan || $pengajuan['bidang_id'] != $profile['bidang_id']) {
            return redirect()->to('/bidang/pengajuan')->with('error', 'Pengajuan tidak ditemukan.');
        }

        $rules = [
            'tahun_anggaran' => 'required|numeric',
            'items'          => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $itemModel = new PengajuanBidangItemModel();
        $historiModel = new HistoriPengajuanModel();

        $pengajuanModel->update($id, [
            'tahun_anggaran' => $this->request->getPost('tahun_anggaran'),
        ]);

        // Delete old items
        $itemModel->where('pengajuan_bidang_id', $id)->delete();

        // Add new items
        $items = $this->request->getPost('items');
        foreach ($items as $item) {
            $itemModel->insert([
                'pengajuan_bidang_id' => $id,
                'nama_aset'           => $item['nama_aset'],
                'spesifikasi'         => $item['spesifikasi'],
                'jumlah'              => $item['jumlah'],
                'satuan'              => $item['satuan'],
                'estimasi_harga'      => $item['estimasi_harga'],
                'kegunaan'            => $item['kegunaan'],
            ]);
        }

        $historiModel->insert([
            'pengajuan_type' => 'bidang',
            'reference_id'   => $id,
            'status_awal'    => $pengajuan['status'],
            'status_akhir'   => $pengajuan['status'],
            'catatan'        => 'Melakukan update/revisi item usulan.',
            'actor_id'       => auth()->id(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengajuan.');
        }

        return redirect()->to('/bidang/pengajuan')->with('message', 'Pengajuan berhasil diperbarui.');
    }
}
