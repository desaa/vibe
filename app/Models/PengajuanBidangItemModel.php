<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanBidangItemModel extends Model
{
    protected $table            = 'pengajuan_bidang_item';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengajuan_bidang_id', 
        'nama_aset', 
        'spesifikasi', 
        'jumlah', 
        'satuan', 
        'estimasi_harga', 
        'kegunaan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
