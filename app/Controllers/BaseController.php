<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Helper to get user profile by combining user custom fields and master tables lookup.
     */
    protected function getProfile()
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        // Initialize default empty profile array
        $profile = [
            'id'           => $user->id,
            'user_id'      => $user->id,
            'nama_lengkap' => $user->nama_lengkap ?? '',
            'nip'          => $user->nip ?? '',
            'opd_id'       => null,
            'bidang_id'    => null,
            'nama_opd'     => null,
            'kode_opd'     => $user->kd_opd,
            'nama_bidang'  => null,
            'created_at'   => $user->created_at ? $user->created_at->toDateTimeString() : null,
            'updated_at'   => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
        ];

        // Resolve OPD if kd_opd is set
        if (!empty($user->kd_opd)) {
            $opdModel = new \App\Models\OpdModel();
            $opd = $opdModel->where('kode_opd', $user->kd_opd)->first();
            if ($opd) {
                $profile['opd_id']   = (int)$opd['id'];
                $profile['nama_opd'] = $opd['nama_opd'];
            }
        }

        // Resolve Bidang if kd_bidang is set
        if (!empty($user->kd_bidang)) {
            $bidangModel = new \App\Models\BidangModel();
            $bidang = $bidangModel->where('kode_bidang', $user->kd_bidang)->first();
            if ($bidang) {
                $profile['bidang_id']   = (int)$bidang['id'];
                $profile['nama_bidang'] = $bidang['nama_bidang'];
            }
        }

        return $profile;
    }
}
