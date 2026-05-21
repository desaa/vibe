<?php

namespace App\Controllers\Opd;

use App\Controllers\BaseController;
use App\Models\PengajuanBidangModel;
use App\Models\PengajuanBidangItemModel;
use App\Models\HistoriPengajuanModel;
class VerifikasiController extends BaseController
{

    public function index()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['opd_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $model = new PengajuanBidangModel();
        // Get all DIAJUKAN, DISETUJUI_OPD, DITOLAK_OPD, DIKEMBALIKAN_OPD
        $pengajuans = $model
            ->select('pengajuan_bidang.*, bidang.nama_bidang, users.nama_lengkap as pengusul')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->join('users', 'users.id = pengajuan_bidang.created_by')
            ->where('pengajuan_bidang.opd_id', $profile['opd_id'])
            ->whereIn('pengajuan_bidang.status', ['DIAJUKAN', 'DISETUJUI_OPD', 'DITOLAK_OPD', 'DIKEMBALIKAN_OPD', 'DIPROSES_KOMINFO'])
            ->orderBy('pengajuan_bidang.updated_at', 'DESC')
            ->findAll();

        return view('opd/verifikasi/index', [
            'pengajuans' => $pengajuans
        ]);
    }

    public function detail($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel
            ->select('pengajuan_bidang.*, bidang.nama_bidang, users.nama_lengkap as pengusul')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->join('users', 'users.id = pengajuan_bidang.created_by')
            ->where('pengajuan_bidang.id', $id)
            ->where('pengajuan_bidang.opd_id', $profile['opd_id'])
            ->first();

        if (!$pengajuan) {
            return redirect()->to('/opd/verifikasi')->with('error', 'Pengajuan tidak ditemukan.');
        }

        $itemModel = new PengajuanBidangItemModel();
        $items = $itemModel->where('pengajuan_bidang_id', $id)->findAll();

        $historiModel = new HistoriPengajuanModel();
        $histories = $historiModel
            ->select('histori_pengajuan.*, users.nama_lengkap')
            ->join('users', 'users.id = histori_pengajuan.actor_id')
            ->where('pengajuan_type', 'bidang')
            ->where('reference_id', $id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('opd/verifikasi/detail', [
            'pengajuan' => $pengajuan,
            'items'     => $items,
            'histories' => $histories
        ]);
    }

    public function aksi($id)
    {
        $profile = $this->getProfile();
        $pengajuanModel = new PengajuanBidangModel();
        $pengajuan = $pengajuanModel->where('id', $id)->where('opd_id', $profile['opd_id'])->first();

        if (!$pengajuan) {
            return redirect()->to('/opd/verifikasi')->with('error', 'Pengajuan tidak ditemukan.');
        }

        if ($pengajuan['status'] !== 'DIAJUKAN') {
            return redirect()->to('/opd/verifikasi')->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $action = $this->request->getPost('action'); // setuju, tolak, kembalikan
        $catatan = $this->request->getPost('catatan');

        if (in_array($action, ['tolak', 'kembalikan']) && empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan/alasan wajib diisi jika menolak atau mengembalikan pengajuan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $statusAwal = $pengajuan['status'];
        $statusAkhir = '';
        $logCatatan = '';

        if ($action === 'setuju') {
            $statusAkhir = 'DISETUJUI_OPD';
            $logCatatan = 'Menyetujui usulan bidang.';
        } elseif ($action === 'tolak') {
            $statusAkhir = 'DITOLAK_OPD';
            $logCatatan = 'Menolak usulan: ' . $catatan;
        } elseif ($action === 'kembalikan') {
            $statusAkhir = 'DIKEMBALIKAN_OPD';
            $logCatatan = 'Mengembalikan usulan untuk direvisi: ' . $catatan;
        }

        $pengajuanModel->update($id, [
            'status'         => $statusAkhir,
            'catatan_revisi' => $catatan ?: null,
        ]);

        $historiModel = new HistoriPengajuanModel();
        $historiModel->insert([
            'pengajuan_type' => 'bidang',
            'reference_id'   => $id,
            'status_awal'    => $statusAwal,
            'status_akhir'   => $statusAkhir,
            'catatan'        => $logCatatan,
            'actor_id'       => auth()->id(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses verifikasi.');
        }

        return redirect()->to('/opd/verifikasi')->with('message', 'Verifikasi usulan berhasil diproses.');
    }
}
