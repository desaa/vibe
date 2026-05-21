<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanBidangModel extends Model
{
    protected $table            = 'pengajuan_bidang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengajuan_opd_id', 
        'nomor_pengajuan', 
        'bidang_id', 
        'opd_id', 
        'tahun_anggaran', 
        'status', 
        'catatan_revisi', 
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
