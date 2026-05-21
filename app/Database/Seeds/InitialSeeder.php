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
                'kode_bidang' => 'APTIKA',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $diskominfoId,
                'nama_bidang' => 'Bidang Informasi Komunikasi Publik (IKP)',
                'kode_bidang' => 'IKP',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            // DINKES
            [
                'opd_id'      => $dinkesId,
                'nama_bidang' => 'Bidang Pelayanan Kesehatan',
                'kode_bidang' => 'YANKES',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $dinkesId,
                'nama_bidang' => 'Bidang Kesehatan Masyarakat',
                'kode_bidang' => 'KESMAS',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            // DISDIK
            [
                'opd_id'      => $disdikId,
                'nama_bidang' => 'Bidang Pembinaan SD',
                'kode_bidang' => 'SD',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'opd_id'      => $disdikId,
                'nama_bidang' => 'Bidang Pembinaan SMP',
                'kode_bidang' => 'SMP',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $db->table('bidang')->insertBatch($bidangData);

        // 3. Insert Users directly to users table
        $usersProvider = auth()->getProvider();

        $usersToCreate = [
            [
                'username'     => 'superadmin',
                'email'        => 'superadmin@vibe.id',
                'password'     => 'password123',
                'group'        => 'superadmin',
                'nama_lengkap' => 'Super Administrator',
                'nip'          => '199901012020121001',
                'kd_opd'       => null,
                'kd_bidang'    => null,
            ],
            [
                'username'     => 'kadin',
                'email'        => 'kadin@vibe.id',
                'password'     => 'password123',
                'group'        => 'kepala_diskominfo',
                'nama_lengkap' => 'Kepala Dinas Kominfo',
                'nip'          => '198801012010121002',
                'kd_opd'       => 'DISKOMINFO',
                'kd_bidang'    => null,
            ],
            [
                'username'     => 'adminopd_dinkes',
                'email'        => 'adminopd.dinkes@vibe.id',
                'password'     => 'password123',
                'group'        => 'admin_opd',
                'nama_lengkap' => 'Admin OPD Dinas Kesehatan',
                'nip'          => '199001012015121003',
                'kd_opd'       => 'DINKES',
                'kd_bidang'    => null,
            ],
            [
                'username'     => 'adminbidang_yankes',
                'email'        => 'adminbidang.yankes@vibe.id',
                'password'     => 'password123',
                'group'        => 'admin_bidang',
                'nama_lengkap' => 'Admin Bidang Yankes DINKES',
                'nip'          => '199501012018121004',
                'kd_opd'       => 'DINKES',
                'kd_bidang'    => 'YANKES',
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
                'username'     => $u['username'],
                'email'        => $u['email'],
                'password'     => $u['password'],
                'nama_lengkap' => $u['nama_lengkap'],
                'nip'          => $u['nip'],
                'kd_opd'       => $u['kd_opd'],
                'kd_bidang'    => $u['kd_bidang'],
                'active'       => 1, // Seeded users are active by default
            ]);

            $usersProvider->save($userEntity);
            $userId = $usersProvider->getInsertID();

            // Find user again and add group
            $user = $usersProvider->findById($userId);
            $user->addGroup($u['group']);
        }
    }
}
