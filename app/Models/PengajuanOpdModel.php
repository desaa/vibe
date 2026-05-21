<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanOpdModel extends Model
{
    protected $table            = 'pengajuan_opd';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_surat_opd', 
        'opd_id', 
        'tahun_anggaran', 
        'status', 
        'nomor_rekomendasi', 
        'tanggal_rekomendasi', 
        'catatan_kominfo', 
        'approved_by', 
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
