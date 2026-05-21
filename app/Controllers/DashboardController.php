<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $user = auth()->user();
        
        // Fetch user profile
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel
            ->select('user_profiles.*, opd.nama_opd, opd.kode_opd, bidang.nama_bidang')
            ->join('opd', 'opd.id = user_profiles.opd_id', 'left')
            ->join('bidang', 'bidang.id = user_profiles.bidang_id', 'left')
            ->where('user_id', $user->id)
            ->first();

        // Establish Role
        $role = 'user';
        if ($user->inGroup('superadmin')) {
            $role = 'superadmin';
        } elseif ($user->inGroup('kepala_diskominfo')) {
            $role = 'kepala_diskominfo';
        } elseif ($user->inGroup('admin_opd')) {
            $role = 'admin_opd';
        } elseif ($user->inGroup('admin_bidang')) {
            $role = 'admin_bidang';
        }

        // Gather Stats based on Role
        $stats = [];
        $pengajuanBidangModel = new \App\Models\PengajuanBidangModel();
        $pengajuanOpdModel = new \App\Models\PengajuanOpdModel();

        if ($role === 'superadmin') {
            $stats['total_opd'] = (new \App\Models\OpdModel())->countAllResults();
            $stats['total_users'] = auth()->getProvider()->countAllResults();
            $stats['total_pengajuan_bidang'] = $pengajuanBidangModel->countAllResults();
            $stats['total_pengajuan_opd'] = $pengajuanOpdModel->countAllResults();
        } elseif ($role === 'admin_bidang' && $profile) {
            $stats['draft'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->where('status', 'DRAFT')->countAllResults();
            $stats['diajukan'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->where('status', 'DIAJUKAN')->countAllResults();
            $stats['proses_kominfo'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->where('status', 'DIPROSES_KOMINFO')->countAllResults();
            $stats['disetujui'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->whereIn('status', ['DISETUJUI_OPD', 'DISETUJUI_KOMINFO'])->countAllResults();
            $stats['revisi'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->whereIn('status', ['DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])->countAllResults();
            $stats['ditolak'] = $pengajuanBidangModel->where('bidang_id', $profile['bidang_id'])->whereIn('status', ['DITOLAK_OPD', 'DITOLAK_KOMINFO'])->countAllResults();
        } elseif ($role === 'admin_opd' && $profile) {
            $stats['bidang_masuk'] = $pengajuanBidangModel->where('opd_id', $profile['opd_id'])->where('status', 'DIAJUKAN')->countAllResults();
            $stats['bidang_disetujui'] = $pengajuanBidangModel->where('opd_id', $profile['opd_id'])->where('status', 'DISETUJUI_OPD')->countAllResults();
            $stats['konsolidasi_draft'] = $pengajuanOpdModel->where('opd_id', $profile['opd_id'])->where('status', 'DRAFT')->countAllResults();
            $stats['konsolidasi_diajukan'] = $pengajuanOpdModel->where('opd_id', $profile['opd_id'])->where('status', 'DIAJUKAN')->countAllResults();
            $stats['konsolidasi_disetujui'] = $pengajuanOpdModel->where('opd_id', $profile['opd_id'])->where('status', 'DISETUJUI')->countAllResults();
        } elseif ($role === 'kepala_diskominfo') {
            $stats['masuk'] = $pengajuanOpdModel->where('status', 'DIAJUKAN')->countAllResults();
            $stats['disetujui'] = $pengajuanOpdModel->where('status', 'DISETUJUI')->countAllResults();
            $stats['revisi'] = $pengajuanOpdModel->where('status', 'DIKEMBALIKAN')->countAllResults();
            $stats['ditolak'] = $pengajuanOpdModel->where('status', 'DITOLAK')->countAllResults();
        }

        return view('dashboard/index', [
            'user'    => $user,
            'profile' => $profile,
            'role'    => $role,
            'stats'   => $stats,
        ]);
    }
}
