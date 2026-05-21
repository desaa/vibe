<?php

namespace App\Controllers\Kominfo;

use App\Controllers\BaseController;
use App\Models\PengajuanOpdModel;
use App\Models\PengajuanBidangModel;
use App\Models\PengajuanBidangItemModel;
use App\Models\HistoriPengajuanModel;
use App\Models\UserProfileModel;

class PersetujuanController extends BaseController
{
    public function index()
    {
        if (!auth()->user()->inGroup('kepala_diskominfo') && !auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $model = new PengajuanOpdModel();
        $konsolidasis = $model
            ->select('pengajuan_opd.*, opd.nama_opd, user_profiles.nama_lengkap as pengirim')
            ->join('opd', 'opd.id = pengajuan_opd.opd_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_opd.created_by')
            ->orderBy('pengajuan_opd.updated_at', 'DESC')
            ->findAll();

        return view('kominfo/persetujuan/index', [
            'konsolidasis' => $konsolidasis
        ]);
    }

    public function detail($id)
    {
        if (!auth()->user()->inGroup('kepala_diskominfo') && !auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $pengajuanOpdModel = new PengajuanOpdModel();
        $konsolidasi = $pengajuanOpdModel
            ->select('pengajuan_opd.*, opd.nama_opd, user_profiles.nama_lengkap as pengirim')
            ->join('opd', 'opd.id = pengajuan_opd.opd_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_opd.created_by')
            ->where('pengajuan_opd.id', $id)
            ->first();

        if (!$konsolidasi) {
            return redirect()->to('/kominfo/persetujuan')->with('error', 'Konsolidasi tidak ditemukan.');
        }

        $bidangPengajuanModel = new PengajuanBidangModel();
        $submissions = $bidangPengajuanModel
            ->select('pengajuan_bidang.*, bidang.nama_bidang, user_profiles.nama_lengkap as pengusul')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_bidang.created_by')
            ->where('pengajuan_bidang.pengajuan_opd_id', $id)
            ->findAll();

        $itemModel = new PengajuanBidangItemModel();
        foreach ($submissions as &$sub) {
            $sub['items'] = $itemModel->where('pengajuan_bidang_id', $sub['id'])->findAll();
        }

        $historiModel = new HistoriPengajuanModel();
        $histories = $historiModel
            ->select('histori_pengajuan.*, user_profiles.nama_lengkap')
            ->join('user_profiles', 'user_profiles.user_id = histori_pengajuan.actor_id')
            ->where('pengajuan_type', 'opd')
            ->where('reference_id', $id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('kominfo/persetujuan/detail', [
            'konsolidasi' => $konsolidasi,
            'submissions' => $submissions,
            'histories'   => $histories
        ]);
    }

    public function aksi($id)
    {
        if (!auth()->user()->inGroup('kepala_diskominfo') && !auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $pengajuanOpdModel = new PengajuanOpdModel();
        $konsolidasi = $pengajuanOpdModel->find($id);

        if (!$konsolidasi) {
            return redirect()->to('/kominfo/persetujuan')->with('error', 'Konsolidasi tidak ditemukan.');
        }

        if ($konsolidasi['status'] !== 'DIAJUKAN') {
            return redirect()->to('/kominfo/persetujuan')->with('error', 'Usulan konsolidasi sudah diproses sebelumnya.');
        }

        $action = $this->request->getPost('action'); // setuju, tolak, kembalikan
        $catatan = $this->request->getPost('catatan');

        if (in_array($action, ['tolak', 'kembalikan']) && empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan/alasan revisi atau penolakan wajib diisi.');
        }

        if ($action === 'setuju') {
            $rules = [
                'nomor_rekomendasi'   => 'required',
                'tanggal_rekomendasi' => 'required|valid_date',
            ];
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $pengajuanBidangModel = new PengajuanBidangModel();
        $historiModel = new HistoriPengajuanModel();

        $statusAwal = $konsolidasi['status'];
        $statusAkhir = '';
        $logCatatan = '';

        if ($action === 'setuju') {
            $statusAkhir = 'DISETUJUI';
            $logCatatan = 'Menyetujui usulan konsolidasi dan menerbitkan surat REKOMENDASI nomor: ' . $this->request->getPost('nomor_rekomendasi');
            
            $pengajuanOpdModel->update($id, [
                'status'              => $statusAkhir,
                'nomor_rekomendasi'   => $this->request->getPost('nomor_rekomendasi'),
                'tanggal_rekomendasi' => $this->request->getPost('tanggal_rekomendasi'),
                'approved_by'         => auth()->id(),
            ]);

            // Update Bidang requests inside this consolidation
            $pengajuanBidangModel->where('pengajuan_opd_id', $id)->set(['status' => 'DISETUJUI_KOMINFO'])->update();

            // Insert log for each Bidang request
            $bidangReqs = $pengajuanBidangModel->where('pengajuan_opd_id', $id)->findAll();
            foreach ($bidangReqs as $br) {
                $historiModel->insert([
                    'pengajuan_type' => 'bidang',
                    'reference_id'   => $br['id'],
                    'status_awal'    => 'DIPROSES_KOMINFO',
                    'status_akhir'   => 'DISETUJUI_KOMINFO',
                    'catatan'        => 'Usulan disetujui Kepala DISKOMINFO dengan Surat Rekomendasi No: ' . $this->request->getPost('nomor_rekomendasi'),
                    'actor_id'       => auth()->id(),
                ]);
            }

        } elseif ($action === 'tolak') {
            $statusAkhir = 'DITOLAK';
            $logCatatan = 'Menolak usulan konsolidasi: ' . $catatan;

            $pengajuanOpdModel->update($id, [
                'status'          => $statusAkhir,
                'catatan_kominfo' => $catatan,
                'approved_by'     => auth()->id(),
            ]);

            // Update Bidang requests
            $pengajuanBidangModel->where('pengajuan_opd_id', $id)->set(['status' => 'DITOLAK_KOMINFO'])->update();

            $bidangReqs = $pengajuanBidangModel->where('pengajuan_opd_id', $id)->findAll();
            foreach ($bidangReqs as $br) {
                $historiModel->insert([
                    'pengajuan_type' => 'bidang',
                    'reference_id'   => $br['id'],
                    'status_awal'    => 'DIPROSES_KOMINFO',
                    'status_akhir'   => 'DITOLAK_KOMINFO',
                    'catatan'        => 'Usulan ditolak oleh Kepala DISKOMINFO dengan catatan: ' . $catatan,
                    'actor_id'       => auth()->id(),
                ]);
            }

        } elseif ($action === 'kembalikan') {
            $statusAkhir = 'DIKEMBALIKAN';
            $logCatatan = 'Mengembalikan usulan konsolidasi untuk direvisi: ' . $catatan;

            $pengajuanOpdModel->update($id, [
                'status'          => $statusAkhir,
                'catatan_kominfo' => $catatan,
                'approved_by'     => auth()->id(),
            ]);

            // Update Bidang requests
            $pengajuanBidangModel->where('pengajuan_opd_id', $id)->set(['status' => 'DIKEMBALIKAN_KOMINFO'])->update();

            $bidangReqs = $pengajuanBidangModel->where('pengajuan_opd_id', $id)->findAll();
            foreach ($bidangReqs as $br) {
                $historiModel->insert([
                    'pengajuan_type' => 'bidang',
                    'reference_id'   => $br['id'],
                    'status_awal'    => 'DIPROSES_KOMINFO',
                    'status_akhir'   => 'DIKEMBALIKAN_KOMINFO',
                    'catatan'        => 'Usulan dikembalikan oleh Kepala DISKOMINFO dengan catatan revisi: ' . $catatan,
                    'actor_id'       => auth()->id(),
                ]);
            }
        }

        // Insert log for OPD Consolidation
        $historiModel->insert([
            'pengajuan_type' => 'opd',
            'reference_id'   => $id,
            'status_awal'    => $statusAwal,
            'status_akhir'   => $statusAkhir,
            'catatan'        => $logCatatan,
            'actor_id'       => auth()->id(),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses persetujuan.');
        }

        return redirect()->to('/kominfo/persetujuan')->with('message', 'Persetujuan konsolidasi berhasil diproses.');
    }
}
