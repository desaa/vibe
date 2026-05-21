<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Entities\User;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $users = auth()->getProvider()
            ->select('users.*, auth_identities.secret as email, auth_groups_users.group, opd.nama_opd, bidang.nama_bidang')
            ->join('auth_identities', 'auth_identities.user_id = users.id AND auth_identities.type = "email_password"', 'left')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
            ->join('opd', 'opd.kode_opd = users.kd_opd', 'left')
            ->join('bidang', 'bidang.kode_bidang = users.kd_bidang', 'left')
            ->findAll();

        return view('admin/users/index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $opdModel = new \App\Models\OpdModel();
        $bidangModel = new \App\Models\BidangModel();

        return view('admin/users/create', [
            'opds' => $opdModel->findAll(),
            'bidangs' => $bidangModel->findAll(),
        ]);
    }

    public function store()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'username'     => 'required|is_unique[users.username]|alpha_numeric_space|min_length[3]|max_length[30]',
            'email'        => 'required|valid_email|is_unique[auth_identities.secret]',
            'password'     => 'required|min_length[6]',
            'nama_lengkap' => 'required',
            'group'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kd_opd = null;
        $opd_id = $this->request->getPost('opd_id');
        if (!empty($opd_id)) {
            $opd = (new \App\Models\OpdModel())->find($opd_id);
            if ($opd) {
                $kd_opd = $opd['kode_opd'];
            }
        }

        $kd_bidang = null;
        $bidang_id = $this->request->getPost('bidang_id');
        if (!empty($bidang_id)) {
            $bidang = (new \App\Models\BidangModel())->find($bidang_id);
            if ($bidang) {
                $kd_bidang = $bidang['kode_bidang'];
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $usersProvider = auth()->getProvider();
        $userEntity = new User([
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip') ?: null,
            'kd_opd'       => $kd_opd,
            'kd_bidang'    => $kd_bidang,
            'active'       => 1, // Admin-created users are active by default
        ]);

        $usersProvider->save($userEntity);
        $userId = $usersProvider->getInsertID();

        // Add to group
        $user = $usersProvider->findById($userId);
        $user->addGroup($this->request->getPost('group'));

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat user.');
        }

        return redirect()->to('/admin/users')->with('message', 'User berhasil dibuat.');
    }

    public function showRegisterKadinForm()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        return view('admin/users/register_kadin');
    }

    public function processRegisterKadin()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'username'     => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[auth_identities.secret]',
            'password'     => 'required|min_length[6]',
            'nama_lengkap' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $usersProvider = auth()->getProvider();
        $newUser = new User([
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip') ?: null,
            'kd_opd'       => 'DISKOMINFO', // Otomatis terikat dengan DISKOMINFO
            'kd_bidang'    => null,
            'active'       => 0, // Akun belum aktif, wajib verifikasi email
        ]);

        $usersProvider->save($newUser);
        $userId = $usersProvider->getInsertID();

        // Tambahkan grup role 'kepala_diskominfo'
        $user = $usersProvider->findById($userId);
        $user->addGroup('kepala_diskominfo');

        // Trigger Pembuatan Identity Aktivasi Email
        // Buat identity aktivasi menggunakan EmailActivator langsung
        $activatorClass = setting('Auth.actions')['register'] ?? null;
        if ($activatorClass !== null) {
            $activator = new $activatorClass();
            $activator->createIdentity($user);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan Kepala DISKOMINFO.');
        }

        return redirect()->to('/admin/users')->with('message', 'Kepala DISKOMINFO berhasil didaftarkan. Email verifikasi telah dikirim.');
    }
}
