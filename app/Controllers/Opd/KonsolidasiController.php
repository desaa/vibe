<?php

namespace App\Controllers\Opd;

use App\Controllers\BaseController;
use App\Models\PengajuanOpdModel;
use App\Models\PengajuanBidangModel;
use App\Models\PengajuanBidangItemModel;
use App\Models\HistoriPengajuanModel;
use App\Models\UserProfileModel;

class KonsolidasiController extends BaseController
{
    private function getProfile()
    {
        $profileModel = new UserProfileModel();
        return $profileModel->where('user_id', auth()->id())->first();
    }

    public function index()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['opd_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $model = new PengajuanOpdModel();
        $konsolidasis = $model
            ->where('opd_id', $profile['opd_id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('opd/konsolidasi/index', [
            'konsolidasis' => $konsolidasis
        ]);
    }

    public function create()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['opd_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $bidangPengajuanModel = new PengajuanBidangModel();
        // Fetch all DISETUJUI_OPD that are NOT yet part of any consolidation
        $approvedSubmissions = $bidangPengajuanModel
            ->select('pengajuan_bidang.*, bidang.nama_bidang, user_profiles.nama_lengkap as pengusul')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_bidang.created_by')
            ->where('pengajuan_bidang.opd_id', $profile['opd_id'])
            ->where('pengajuan_bidang.status', 'DISETUJUI_OPD')
            ->where('pengajuan_bidang.pengajuan_opd_id', null)
            ->findAll();

        return view('opd/konsolidasi/form', [
            'approvedSubmissions' => $approvedSubmissions,
            'profile'             => $profile
        ]);
    }

    public function store()
    {
        $profile = $this->getProfile();
        if (!$profile || !$profile['opd_id']) {
            return redirect()->to('/dashboard')->with('error', 'Profil instansi Anda belum diatur.');
        }

        $rules = [
            'nomor_surat_opd' => 'required|is_unique[pengajuan_opd.nomor_surat_opd]',
            'tahun_anggaran'  => 'required|numeric',
            'submissions'     => 'required', // Array of pengajuan_bidang.id
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $pengajuanOpdModel = new PengajuanOpdModel();
        $pengajuanBidangModel = new PengajuanBidangModel();
        $historiModel = new HistoriPengajuanModel();

        // Create Konsolidasi OPD
        $opdSubmissionId = $pengajuanOpdModel->insert([
            'nomor_surat_opd' => $this->request->getPost('nomor_surat_opd'),
            'opd_id'          => $profile['opd_id'],
            'tahun_anggaran'  => $this->request->getPost('tahun_anggaran'),
            'status'          => 'DIAJUKAN', // Auto submit to DISKOMINFO
            'created_by'      => auth()->id(),
        ]);

        // Insert log for OPD Consolidation
        $historiModel->insert([
            'pengajuan_type' => 'opd',
            'reference_id'   => $opdSubmissionId,
            'status_awal'    => '-',
            'status_akhir'   => 'DIAJUKAN',
            'catatan'        => 'Membuat usulan konsolidasi dan mengirim ke Kepala DISKOMINFO.',
            'actor_id'       => auth()->id(),
        ]);

        // Associate and update Bidang submissions
        $submissions = $this->request->getPost('submissions');
        foreach ($submissions as $bidangReqId) {
            $pengajuanBidangModel->update($bidangReqId, [
                'pengajuan_opd_id' => $opdSubmissionId,
                'status'           => 'DIPROSES_KOMINFO'
            ]);

            // Log for each Bidang submission
            $historiModel->insert([
                'pengajuan_type' => 'bidang',
                'reference_id'   => $bidangReqId,
                'status_awal'    => 'DISETUJUI_OPD',
                'status_akhir'   => 'DIPROSES_KOMINFO',
                'catatan'        => 'Dikonsolidasikan dalam Surat Pengantar Nomor: ' . $this->request->getPost('nomor_surat_opd') . ' dan diajukan ke DISKOMINFO.',
                'actor_id'       => auth()->id(),
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat konsolidasi.');
        }

        return redirect()->to('/opd/konsolidasi')->with('message', 'Usulan konsolidasi berhasil dikirim ke Kepala DISKOMINFO.');
    }

    public function detail($id)
    {
        $profile = $this->getProfile();
        $pengajuanOpdModel = new PengajuanOpdModel();
        $konsolidasi = $pengajuanOpdModel
            ->select('pengajuan_opd.*, opd.nama_opd, user_profiles.nama_lengkap as pengirim')
            ->join('opd', 'opd.id = pengajuan_opd.opd_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_opd.created_by')
            ->where('pengajuan_opd.id', $id)
            ->where('pengajuan_opd.opd_id', $profile['opd_id'])
            ->first();

        if (!$konsolidasi) {
            return redirect()->to('/opd/konsolidasi')->with('error', 'Data konsolidasi tidak ditemukan.');
        }

        // Get all Bidang Submissions in this Consolidation
        $bidangPengajuanModel = new PengajuanBidangModel();
        $submissions = $bidangPengajuanModel
            ->select('pengajuan_bidang.*, bidang.nama_bidang, user_profiles.nama_lengkap as pengusul')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->join('user_profiles', 'user_profiles.user_id = pengajuan_bidang.created_by')
            ->where('pengajuan_bidang.pengajuan_opd_id', $id)
            ->findAll();

        // Get all items count and total price
        $itemModel = new PengajuanBidangItemModel();
        foreach ($submissions as &$sub) {
            $sub['items'] = $itemModel->where('pengajuan_bidang_id', $sub['id'])->findAll();
        }

        // Logs for this Consolidation
        $historiModel = new HistoriPengajuanModel();
        $histories = $historiModel
            ->select('histori_pengajuan.*, user_profiles.nama_lengkap')
            ->join('user_profiles', 'user_profiles.user_id = histori_pengajuan.actor_id')
            ->where('pengajuan_type', 'opd')
            ->where('reference_id', $id)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('opd/konsolidasi/detail', [
            'konsolidasi' => $konsolidasi,
            'submissions' => $submissions,
            'histories'   => $histories
        ]);
    }

    public function cetak($id)
    {
        $profile = $this->getProfile();
        $pengajuanOpdModel = new PengajuanOpdModel();
        $konsolidasi = $pengajuanOpdModel
            ->select('pengajuan_opd.*, opd.nama_opd, approved_profile.nama_lengkap as kadin_nama, approved_profile.nip as kadin_nip')
            ->join('opd', 'opd.id = pengajuan_opd.opd_id')
            ->join('user_profiles as approved_profile', 'approved_profile.user_id = pengajuan_opd.approved_by', 'left')
            ->where('pengajuan_opd.id', $id)
            ->where('pengajuan_opd.opd_id', $profile['opd_id'])
            ->where('pengajuan_opd.status', 'DISETUJUI')
            ->first();

        if (!$konsolidasi) {
            return redirect()->to('/opd/konsolidasi')->with('error', 'Cetak Surat Rekomendasi hanya tersedia jika usulan telah DISETUJUI oleh Kepala DISKOMINFO.');
        }

        // Get all items in this consolidation
        $bidangPengajuanModel = new PengajuanBidangModel();
        $submissions = $bidangPengajuanModel
            ->select('pengajuan_bidang.id, bidang.nama_bidang')
            ->join('bidang', 'bidang.id = pengajuan_bidang.bidang_id')
            ->where('pengajuan_bidang.pengajuan_opd_id', $id)
            ->whereIn('pengajuan_bidang.status', ['DISETUJUI_KOMINFO'])
            ->findAll();

        $itemModel = new PengajuanBidangItemModel();
        $allItems = [];
        foreach ($submissions as $sub) {
            $items = $itemModel->where('pengajuan_bidang_id', $sub['id'])->findAll();
            foreach ($items as $item) {
                $item['nama_bidang'] = $sub['nama_bidang'];
                $allItems[] = $item;
            }
        }

        return view('rekomendasi/layout_cetak', [
            'konsolidasi' => $konsolidasi,
            'items'       => $allItems
        ]);
    }
}
