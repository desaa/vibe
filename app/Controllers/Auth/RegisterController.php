<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;
use App\Models\UserModel;

class RegisterController extends ShieldRegister
{
    public function registerView()
    {
        // Tampilkan form registrasi kustom velzone dengan drop-down OPD dan Bidang
        $opdModel = new \App\Models\OpdModel();
        $bidangModel = new \App\Models\BidangModel();
        
        return view('auth/register', [
            'opd_list' => $opdModel->findAll(),
            'bidang_list' => $bidangModel->findAll(),
        ]);
    }

    public function registerAction(): \CodeIgniter\HTTP\RedirectResponse
    {
        // 1. Aturan Validasi Kustom
        $rules = [
            'username'     => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[auth_identities.secret]',
            'password'     => 'required|min_length[6]',
            'nama_lengkap' => 'required',
            'role'         => 'required|in_list[admin_opd,admin_bidang]',
            'kd_opd'       => 'required',
            'kd_bidang'    => 'required_without[role,admin_opd]', // Wajib diisi jika rolenya adalah admin_bidang
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Buat User & Simpan Langsung ke Kolom Tabel `users`
        $users = model(UserModel::class);
        $role = $this->request->getPost('role');
        
        $newUser = new \CodeIgniter\Shield\Entities\User([
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip') ?: null,
            'kd_opd'       => $this->request->getPost('kd_opd'),
            'kd_bidang'     => ($role === 'admin_bidang') ? $this->request->getPost('kd_bidang') : null,
            'active'       => 0, // Akun belum aktif, wajib verifikasi email
        ]);

        $users->save($newUser);
        $newUser = $users->findById($users->getInsertID());

        // 3. Tambahkan Grup Role Shield
        $newUser->addGroup($role);

        // 4. Gunakan Shield's Session Authenticator untuk memulai proses aktivasi
        /** @var \CodeIgniter\Shield\Authentication\Authenticators\Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // startLogin menyimpan user ke session sebagai "pending"
        $authenticator->startLogin($newUser);

        // startUpAction membuat identity aktivasi (kode verifikasi) di database
        // Ketika user mengunjungi auth/a/show, Shield akan mengirim email berisi kode
        $hasAction = $authenticator->startUpAction('register', $newUser);

        if ($hasAction) {
            return redirect()->route('auth-action-show')
                             ->with('message', 'Registrasi sukses! Silakan verifikasi email Anda.');
        }

        // Jika tidak ada action (seharusnya tidak terjadi karena kita set EmailActivator),
        // aktifkan langsung dan complete login
        $newUser->activate();
        $authenticator->completeLogin($newUser);

        return redirect()->to(config('Auth')->registerRedirect())
                         ->with('message', 'Registrasi sukses!');
    }
}
