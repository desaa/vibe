<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Clean tables
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('user_profiles')->truncate();
        $db->table('bidang')->truncate();
        $db->table('opd')->truncate();
        $db->table('users')->truncate();
        $db->table('auth_groups_users')->truncate();
        $db->table('auth_identities')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 1. Insert OPD
        $opdData = [
            [
                'nama_opd'   => 'Dinas Komunikasi dan Informatika',
                'kode_opd'   => 'DISKOMINFO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_opd'   => 'Dinas Kesehatan',
                'kode_opd'   => 'DINKES',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_opd'   => 'Dinas Pendidikan',
                'kode_opd'   => 'DISDIK',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $db->table('opd')->insertBatch($opdData);

        // Get OPD IDs
        $diskominfoId = $db->table('opd')->where('kode_opd', 'DISKOMINFO')->get()->getRow()->id;
        $dinkesId     = $db->table('opd')->where('kode_opd', 'DINKES')->get()->getRow()->id;
        $disdikId     = $db->table('opd')->where('kode_opd', 'DISDIK')->get()->getRow()->id;

        // 2. Insert Bidang
        $bidangData = [
            // DISKOMINFO
            [
                'opd_id'      => $diskominfoId,
                'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $diskominfoId,
                'nama_bidang' => 'Bidang Informasi Komunikasi Publik (IKP)',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            // DINKES
            [
                'opd_id'      => $dinkesId,
                'nama_bidang' => 'Bidang Pelayanan Kesehatan',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $dinkesId,
                'nama_bidang' => 'Bidang Kesehatan Masyarakat',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            // DISDIK
            [
                'opd_id'      => $disdikId,
                'nama_bidang' => 'Bidang Pembinaan SD',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $disdikId,
                'nama_bidang' => 'Bidang Pembinaan SMP',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $db->table('bidang')->insertBatch($bidangData);

        // Get Bidang ID Pelayanan Kesehatan DINKES
        $bidangYankesId = $db->table('bidang')->where('nama_bidang', 'Bidang Pelayanan Kesehatan')->get()->getRow()->id;

        // 3. Insert Users & Profiles
        $usersProvider = auth()->getProvider();

        $usersToCreate = [
            [
                'username'     => 'superadmin',
                'email'        => 'superadmin@vibe.id',
                'password'     => 'password123',
                'group'        => 'superadmin',
                'nama_lengkap' => 'Super Administrator',
                'nip'          => '199901012020121001',
                'opd_id'       => null,
                'bidang_id'    => null,
            ],
            [
                'username'     => 'kadin',
                'email'        => 'kadin@vibe.id',
                'password'     => 'password123',
                'group'        => 'kepala_diskominfo',
                'nama_lengkap' => 'Kepala Dinas Kominfo',
                'nip'          => '198801012010121002',
                'opd_id'       => $diskominfoId,
                'bidang_id'    => null,
            ],
            [
                'username'     => 'adminopd_dinkes',
                'email'        => 'adminopd.dinkes@vibe.id',
                'password'     => 'password123',
                'group'        => 'admin_opd',
                'nama_lengkap' => 'Admin OPD Dinas Kesehatan',
                'nip'          => '199001012015121003',
                'opd_id'       => $dinkesId,
                'bidang_id'    => null,
            ],
            [
                'username'     => 'adminbidang_yankes',
                'email'        => 'adminbidang.yankes@vibe.id',
                'password'     => 'password123',
                'group'        => 'admin_bidang',
                'nama_lengkap' => 'Admin Bidang Yankes DINKES',
                'nip'          => '199501012018121004',
                'opd_id'       => $dinkesId,
                'bidang_id'    => $bidangYankesId,
            ]
        ];

        foreach ($usersToCreate as $u) {
            // Check if user already exists
            $existingUser = $usersProvider->findByCredentials(['email' => $u['email']]);
            if ($existingUser) {
                // Remove and recreate for clean seed
                $db->table('users')->where('id', $existingUser->id)->delete();
            }

            $userEntity = new User([
                'username' => $u['username'],
                'email'    => $u['email'],
                'password' => $u['password'],
            ]);

            $usersProvider->save($userEntity);
            $userId = $usersProvider->getInsertID();

            // Find user again and add group
            $user = $usersProvider->findById($userId);
            $user->addGroup($u['group']);

            // Insert into user_profiles
            $db->table('user_profiles')->insert([
                'user_id'      => $userId,
                'nama_lengkap' => $u['nama_lengkap'],
                'nip'          => $u['nip'],
                'opd_id'       => $u['opd_id'],
                'bidang_id'    => $u['bidang_id'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
