<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetTikTables extends Migration
{
    public function up()
    {
        // 1. OPD
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('opd');

        // 2. Bidang
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'opd_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kode_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('opd_id', 'opd', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bidang');

        // 3. Add OPD fields directly to users table
        $fields = [
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'username'
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nama_lengkap'
            ],
            'kd_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nip'
            ],
            'kd_bidang' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'kd_opd'
            ],
        ];
        $this->forge->addColumn('users', $fields);

        // Add foreign keys directly
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_kd_opd_fk FOREIGN KEY (kd_opd) REFERENCES opd(kode_opd) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_kd_bidang_fk FOREIGN KEY (kd_bidang) REFERENCES bidang(kode_bidang) ON DELETE SET NULL ON UPDATE CASCADE');

        // 4. Pengajuan OPD
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_surat_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'opd_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun_anggaran' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'DRAFT',
            ],
            'nomor_rekomendasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'tanggal_rekomendasi' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'catatan_kominfo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('opd_id', 'opd', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_opd');

        // 5. Pengajuan Bidang
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pengajuan_opd_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nomor_pengajuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'bidang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'opd_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tahun_anggaran' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'DRAFT',
            ],
            'catatan_revisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pengajuan_opd_id', 'pengajuan_opd', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('bidang_id', 'bidang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('opd_id', 'opd', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_bidang');

        // 6. Pengajuan Bidang Item
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pengajuan_bidang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_aset' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'spesifikasi' => [
                'type' => 'TEXT',
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'estimasi_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'kegunaan' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pengajuan_bidang_id', 'pengajuan_bidang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengajuan_bidang_item');

        // 7. Histori Pengajuan
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pengajuan_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'reference_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status_awal' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'status_akhir' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'actor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('actor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('histori_pengajuan');
    }

    public function down()
    {
        $this->forge->dropTable('histori_pengajuan', true);
        $this->forge->dropTable('pengajuan_bidang_item', true);
        $this->forge->dropTable('pengajuan_bidang', true);
        $this->forge->dropTable('pengajuan_opd', true);
        
        // Drop foreign keys and columns on users table
        try {
            $this->db->query('ALTER TABLE users DROP FOREIGN KEY users_kd_opd_fk');
        } catch (\Exception $e) {}
        try {
            $this->db->query('ALTER TABLE users DROP FOREIGN KEY users_kd_bidang_fk');
        } catch (\Exception $e) {}
        
        $fieldsToDrop = [];
        foreach (['nama_lengkap', 'nip', 'kd_opd', 'kd_bidang'] as $col) {
            if ($this->db->fieldExists($col, 'users')) {
                $fieldsToDrop[] = $col;
            }
        }
        if (!empty($fieldsToDrop)) {
            $this->forge->dropColumn('users', $fieldsToDrop);
        }
        
        $this->forge->dropTable('bidang', true);
        $this->forge->dropTable('opd', true);
    }
}

