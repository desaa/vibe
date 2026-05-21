<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Entities\User;

class UserController extends BaseController
{
    public function index()
    {
        if (!auth()->user()->inGroup('superadmin')) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $profileModel = new \App\Models\UserProfileModel();
        $users = $profileModel
            ->select('user_profiles.*, users.username, auth_groups_users.group')
            ->join('users', 'users.id = user_profiles.user_id')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id', 'left')
            ->join('opd', 'opd.id = user_profiles.opd_id', 'left')
            ->join('bidang', 'bidang.id = user_profiles.bidang_id', 'left')
            ->select('opd.nama_opd, bidang.nama_bidang')
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

        $db = \Config\Database::connect();
        $db->transStart();

        $usersProvider = auth()->getProvider();
        $userEntity = new User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        $usersProvider->save($userEntity);
        $userId = $usersProvider->getInsertID();

        // Add to group
        $user = $usersProvider->findById($userId);
        $user->addGroup($this->request->getPost('group'));

        // Insert Profile
        $profileModel = new \App\Models\UserProfileModel();
        $profileModel->insert([
            'user_id'      => $userId,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'opd_id'       => $this->request->getPost('opd_id') ?: null,
            'bidang_id'    => $this->request->getPost('bidang_id') ?: null,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat user.');
        }

        return redirect()->to('/admin/users')->with('message', 'User berhasil dibuat.');
    }
}
