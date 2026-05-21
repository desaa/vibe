<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriPengajuanModel extends Model
{
    protected $table            = 'histori_pengajuan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengajuan_type', 
        'reference_id', 
        'status_awal', 
        'status_akhir', 
        'catatan', 
        'actor_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field for log table
}
