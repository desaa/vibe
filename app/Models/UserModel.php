<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        parent::initialize();
        
        // Add custom fields to allowed fields
        $this->allowedFields = array_merge($this->allowedFields, [
            'nama_lengkap',
            'nip',
            'kd_opd',
            'kd_bidang'
        ]);
    }
}
