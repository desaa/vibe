<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PengajuanBidangModel;
use App\Models\PengajuanBidangItemModel;
use App\Models\PengajuanOpdModel;
use App\Models\HistoriPengajuanModel;

class TestWorkflow extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:workflow';
    protected $description = 'Runs the asset recommendation system end-to-end workflow test.';

    private function mockRequest(array $postData = [], string $path = '')
    {
        $_POST = $postData;
        $_REQUEST = $postData;
        
        $config = config('App');
        $uri = new \CodeIgniter\HTTP\SiteURI($config, $path);
        
        // IncomingRequest reads $_POST at construct time
        $request = new \CodeIgniter\HTTP\IncomingRequest($config, $uri, null, new \CodeIgniter\HTTP\UserAgent());
        
        \Config\Services::injectMock('request', $request);
        \Config\Services::injectMock('validation', null);
    }

    public function run(array $params)
    {
        // Inject a mock CLI Session to bypass native session errors in CLI mode
        $config = config('Session');
        $driver = new \CodeIgniter\Session\Handlers\ArrayHandler($config, '127.0.0.1');
        $mockSession = new class($driver, $config) extends \CodeIgniter\Session\Session {
            public function regenerate(bool $destroy = false)
            {
                // Do nothing to bypass session_regenerate_id() CLI error
            }
            public function start()
            {
                if (!isset($_SESSION)) {
                    $_SESSION = [];
                }
                return $this;
            }
        };
        \Config\Services::injectMock('session', $mockSession);

        // Bootstrap initial empty request
        $this->mockRequest();

        CLI::write("==================================================", "yellow");
        CLI::write("  STARTING END-TO-END WORKFLOW INTEGRATION TEST  ", "yellow");
        CLI::write("==================================================", "yellow");

        $db = \Config\Database::connect();
        $session = \Config\Services::session();
        $usersProvider = auth()->getProvider();

        // ----------------------------------------------------
        // 0. RESET TEST DATA
        // ----------------------------------------------------
        CLI::write("\n[Step 0] Resetting transactional tables...", "blue");
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('pengajuan_bidang_item')->truncate();
        $db->table('pengajuan_bidang')->truncate();
        $db->table('pengajuan_opd')->truncate();
        $db->table('histori_pengajuan')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');
        CLI::write("Tables reset successfully.", "green");

        // Fetch users
        $bidangUser = $usersProvider->findByCredentials(['email' => 'adminbidang.yankes@vibe.id']);
        $opdUser = $usersProvider->findByCredentials(['email' => 'adminopd.dinkes@vibe.id']);
        $kadinUser = $usersProvider->findByCredentials(['email' => 'kadin@vibe.id']);

        if (!$bidangUser || !$opdUser || !$kadinUser) {
            CLI::error("Test users not found! Please run InitialSeeder first: php spark db:seed InitialSeeder");
            return;
        }

        // ----------------------------------------------------
        // 1. ADMIN BIDANG CREATES DRAFT & SUBMITS
        // ----------------------------------------------------
        CLI::write("\n[Step 1] Logging in as Admin Bidang (Yankes DINKES)...", "blue");
        auth()->login($bidangUser);
        CLI::write("Logged in as " . auth()->user()->username, "green");

        // Mock POST data for create/store
        $postStore = [
            'tahun_anggaran' => 2026,
            'items' => [
                [
                    'nama_aset' => 'Laptop Core i7',
                    'spesifikasi' => '16GB RAM, 512GB SSD',
                    'jumlah' => 5,
                    'satuan' => 'Unit',
                    'estimasi_harga' => 15000000,
                    'kegunaan' => 'Pelayanan admisi Puskesmas',
                ],
                [
                    'nama_aset' => 'Printer Laser',
                    'spesifikasi' => 'A4, Monochrome, Duplex',
                    'jumlah' => 2,
                    'satuan' => 'Unit',
                    'estimasi_harga' => 3500000,
                    'kegunaan' => 'Cetak resep dan rekam medis',
                ]
            ]
        ];

        CLI::write("Creating Usulan Pengadaan...", "blue");
        $this->mockRequest($postStore, 'bidang/pengajuan/store');
        
        $bidangController = new \App\Controllers\Bidang\PengajuanController();
        $bidangController->initController(service('request'), service('response'), service('logger'));
        
        // Execute store
        $bidangController->store();

        $pengajuanBidangModel = new PengajuanBidangModel();
        $usulan = $pengajuanBidangModel->first();

        if (!$usulan) {
            CLI::error("FAIL: Usulan Bidang not created.");
            return;
        }

        CLI::write("SUCCESS: Usulan created with ID " . $usulan['id'] . " and status " . $usulan['status'], "green");
        if ($usulan['status'] !== 'DRAFT') {
            CLI::error("FAIL: Initial status should be DRAFT.");
            return;
        }

        // Submitting usulan
        CLI::write("Submitting Usulan to Admin OPD...", "blue");
        $this->mockRequest([], 'bidang/pengajuan/kirim/' . $usulan['id']);
        $bidangController->initController(service('request'), service('response'), service('logger'));
        $bidangController->kirim($usulan['id']);

        $usulan = $pengajuanBidangModel->find($usulan['id']);
        CLI::write("SUCCESS: Usulan status changed to: " . $usulan['status'], "green");
        if ($usulan['status'] !== 'DIAJUKAN') {
            CLI::error("FAIL: Submitted usulan status should be DIAJUKAN.");
            return;
        }

        // ----------------------------------------------------
        // 2. ADMIN OPD VERIFIKASI (VALIDATION & APPROVAL)
        // ----------------------------------------------------
        CLI::write("\n[Step 2] Logging in as Admin OPD (Dinas Kesehatan)...", "blue");
        auth()->logout();
        auth()->login($opdUser);
        CLI::write("Logged in as " . auth()->user()->username, "green");

        $verifikasiController = new \App\Controllers\Opd\VerifikasiController();

        // Test validation: Tolak/Kembalikan without catatan
        CLI::write("Testing OPD validation (Reject without Catatan should fail)...", "blue");
        $this->mockRequest([
            'action' => 'tolak',
            'catatan' => ''
        ], 'opd/verifikasi/aksi/' . $usulan['id']);
        
        $verifikasiController->initController(service('request'), service('response'), service('logger'));
        $session->remove('error');
        $verifikasiController->aksi($usulan['id']);
        if ($session->getFlashdata('error')) {
            CLI::write("PASS: Rejecting without notes failed correctly with message: " . $session->getFlashdata('error'), "green");
        } else {
            CLI::error("FAIL: Rejecting without notes was allowed.");
            return;
        }

        // Setuju usulan
        CLI::write("Approving Bidang Usulan as Admin OPD...", "blue");
        $this->mockRequest([
            'action' => 'setuju',
            'catatan' => ''
        ], 'opd/verifikasi/aksi/' . $usulan['id']);
        
        $verifikasiController->initController(service('request'), service('response'), service('logger'));
        $verifikasiController->aksi($usulan['id']);

        $usulan = $pengajuanBidangModel->find($usulan['id']);
        CLI::write("SUCCESS: Usulan status changed to: " . $usulan['status'], "green");
        if ($usulan['status'] !== 'DISETUJUI_OPD') {
            CLI::error("FAIL: Approved usulan status should be DISETUJUI_OPD.");
            return;
        }

        // ----------------------------------------------------
        // 3. ADMIN OPD KONSOLIDASI
        // ----------------------------------------------------
        CLI::write("\n[Step 3] Consolidating approved usulans into OPD letter...", "blue");
        $konsolidasiController = new \App\Controllers\Opd\KonsolidasiController();

        $this->mockRequest([
            'nomor_surat_opd' => 'SRT-DINKES-2026-001',
            'tahun_anggaran' => 2026,
            'submissions' => [$usulan['id']]
        ], 'opd/konsolidasi/store');
        
        $konsolidasiController->initController(service('request'), service('response'), service('logger'));
        $konsolidasiController->store();

        $pengajuanOpdModel = new PengajuanOpdModel();
        $konsolidasi = $pengajuanOpdModel->first();

        if (!$konsolidasi) {
            CLI::error("FAIL: OPD Consolidation not created.");
            return;
        }

        CLI::write("SUCCESS: Consolidation created with ID " . $konsolidasi['id'] . " and status " . $konsolidasi['status'], "green");
        if ($konsolidasi['status'] !== 'DIAJUKAN') {
            CLI::error("FAIL: Consolidated letter status should be DIAJUKAN.");
            return;
        }

        // Check if underlying Bidang request status changed to DIPROSES_KOMINFO
        $usulan = $pengajuanBidangModel->find($usulan['id']);
        CLI::write("SUCCESS: Underlying usulan status changed to: " . $usulan['status'], "green");
        if ($usulan['status'] !== 'DIPROSES_KOMINFO') {
            CLI::error("FAIL: Underlying usulan status should be DIPROSES_KOMINFO.");
            return;
        }

        // ----------------------------------------------------
        // 4. KEPALA DISKOMINFO PERSETUJUAN (VALIDATION & APPROVAL)
        // ----------------------------------------------------
        CLI::write("\n[Step 4] Logging in as Kepala DISKOMINFO...", "blue");
        auth()->logout();
        auth()->login($kadinUser);
        CLI::write("Logged in as " . auth()->user()->username, "green");

        $persetujuanController = new \App\Controllers\Kominfo\PersetujuanController();

        // Test validation: Approve without recommendation details
        CLI::write("Testing Kominfo validation (Approve without No/Tanggal Rekomendasi should fail)...", "blue");
        $this->mockRequest([
            'action' => 'setuju',
            'nomor_rekomendasi' => '',
            'tanggal_rekomendasi' => ''
        ], 'kominfo/persetujuan/aksi/' . $konsolidasi['id']);
        
        $persetujuanController->initController(service('request'), service('response'), service('logger'));
        $session->remove('errors');
        $persetujuanController->aksi($konsolidasi['id']);
        if ($session->getFlashdata('errors')) {
            CLI::write("PASS: Approving without details failed correctly with validation errors.", "green");
        } else {
            CLI::error("FAIL: Approving without details was allowed.");
            return;
        }

        // Test validation: Tolak/Kembalikan without notes
        CLI::write("Testing Kominfo validation (Reject without Catatan should fail)...", "blue");
        $this->mockRequest([
            'action' => 'tolak',
            'catatan' => ''
        ], 'kominfo/persetujuan/aksi/' . $konsolidasi['id']);
        
        $persetujuanController->initController(service('request'), service('response'), service('logger'));
        $session->remove('error');
        $persetujuanController->aksi($konsolidasi['id']);
        if ($session->getFlashdata('error')) {
            CLI::write("PASS: Rejecting without notes failed correctly with message: " . $session->getFlashdata('error'), "green");
        } else {
            CLI::error("FAIL: Rejecting without notes was allowed.");
            return;
        }

        // Approve consolidation
        CLI::write("Approving OPD Consolidation as Kepala DISKOMINFO...", "blue");
        $this->mockRequest([
            'action' => 'setuju',
            'nomor_rekomendasi' => 'REK-DISKOMINFO-2026-001',
            'tanggal_rekomendasi' => '2026-05-21'
        ], 'kominfo/persetujuan/aksi/' . $konsolidasi['id']);
        
        $persetujuanController->initController(service('request'), service('response'), service('logger'));
        $persetujuanController->aksi($konsolidasi['id']);

        $konsolidasi = $pengajuanOpdModel->find($konsolidasi['id']);
        CLI::write("SUCCESS: Consolidation status changed to: " . $konsolidasi['status'], "green");
        if ($konsolidasi['status'] !== 'DISETUJUI') {
            CLI::error("FAIL: Approved consolidation status should be DISETUJUI.");
            return;
        }

        // Verify underlying Bidang request status changed to DISETUJUI_KOMINFO
        $usulan = $pengajuanBidangModel->find($usulan['id']);
        CLI::write("SUCCESS: Underlying usulan status changed to: " . $usulan['status'], "green");
        if ($usulan['status'] !== 'DISETUJUI_KOMINFO') {
            CLI::error("FAIL: Underlying usulan status should be DISETUJUI_KOMINFO.");
            return;
        }

        // ----------------------------------------------------
        // 5. PRINT LAYOUT RENDERING
        // ----------------------------------------------------
        CLI::write("\n[Step 5] Logging in back as Admin OPD to test Cetak...", "blue");
        auth()->logout();
        auth()->login($opdUser);
        CLI::write("Logged in as " . auth()->user()->username, "green");

        CLI::write("Rendering recommendation letter print layout...", "blue");
        $this->mockRequest([], 'opd/rekomendasi/cetak/' . $konsolidasi['id']);
        $konsolidasiController->initController(service('request'), service('response'), service('logger'));
        $res = $konsolidasiController->cetak($konsolidasi['id']);
        
        if (is_object($res)) {
            CLI::write("Type of result: " . get_class($res), "yellow");
            if (method_exists($res, 'getBody')) {
                CLI::write("Response body preview: " . substr($res->getBody(), 0, 500), "yellow");
            }
            if (method_exists($res, 'getHeaders')) {
                CLI::write("Headers: " . json_encode($res->headers()), "yellow");
            }
        } else {
            CLI::write("Type of result: string", "yellow");
            CLI::write("Content preview: " . substr((string)$res, 0, 500), "yellow");
        }

        $resStr = is_object($res) && method_exists($res, 'getBody') ? $res->getBody() : (string)$res;

        if (strpos($resStr, 'REK-DISKOMINFO-2026-001') !== false) {
            CLI::write("SUCCESS: Print layout rendered correctly and contains recommendation number.", "green");
        } else {
            CLI::error("FAIL: Print layout did not render the required recommendation fields. Raw result: " . substr($resStr, 0, 300));
            return;
        }

        // ----------------------------------------------------
        // 6. AUDIT TRAILS & HISTORI CHECK
        // ----------------------------------------------------
        CLI::write("\n[Step 6] Checking Audit Trails (histori_pengajuan)...", "blue");
        $historiModel = new HistoriPengajuanModel();
        
        $bidangHistories = $historiModel->where('pengajuan_type', 'bidang')->where('reference_id', $usulan['id'])->orderBy('created_at', 'ASC')->findAll();
        $opdHistories = $historiModel->where('pengajuan_type', 'opd')->where('reference_id', $konsolidasi['id'])->orderBy('created_at', 'ASC')->findAll();

        CLI::write("Bidang Audit Log Count: " . count($bidangHistories), "green");
        CLI::write("OPD Audit Log Count: " . count($opdHistories), "green");

        if (count($bidangHistories) < 4 || count($opdHistories) < 2) {
            CLI::error("FAIL: Insufficient audit trail entries recorded.");
            return;
        }

        foreach ($bidangHistories as $h) {
            CLI::write(" - Bidang Log: [" . $h['status_awal'] . " -> " . $h['status_akhir'] . "] : " . $h['catatan'], "green");
        }
        foreach ($opdHistories as $h) {
            CLI::write(" - OPD Log: [" . $h['status_awal'] . " -> " . $h['status_akhir'] . "] : " . $h['catatan'], "green");
        }

        // ----------------------------------------------------
        // 7. PUBLIC REGISTRATION, VERIFICATION FILTER, AND ADMIN REGISTER KADIN
        // ----------------------------------------------------
        CLI::write("\n[Step 7] Testing Public Self-Registration, Verification Filter & Admin Kadin Registration...", "blue");

        // 7.1. Public Self-Registration Test
        CLI::write("Simulating public self-registration for Admin Bidang...", "blue");
        $uniqId = time() . rand(10, 99);
        $regUsername = 'bidangtest' . $uniqId;
        $regEmail = 'bidangtest' . $uniqId . '@vibe.id';

        $postRegister = [
            'username'     => $regUsername,
            'email'        => $regEmail,
            'password'     => 'Password123!',
            'nama_lengkap' => 'Admin Bidang Test',
            'nip'          => '1234567890',
            'role'         => 'admin_bidang',
            'kd_opd'       => 'DINKES',
            'kd_bidang'    => 'YANKES',
        ];

        // Logout current user first to allow registration
        auth()->logout();

        $this->mockRequest($postRegister, 'register');
        $registerController = new \App\Controllers\Auth\RegisterController();
        $registerController->initController(service('request'), service('response'), service('logger'));

        $session->remove('errors');
        $session->remove('message');

        try {
            $registerController->registerAction();
        } catch (\Exception $e) {
            // startUpAction may fail sending email in CLI context, but user should still be created
            CLI::write("Note: Registration action threw exception (expected in CLI): " . $e->getMessage(), "yellow");
        }

        // Check if user is registered
        $newUser = $usersProvider->findByCredentials(['email' => $regEmail]);
        if (!$newUser) {
            CLI::error("FAIL: Self-Registration failed to insert user into database.");
            CLI::error("Session Errors: " . print_r($session->get('errors'), true));
            CLI::error("Validation Errors: " . print_r(service('validation')->getErrors(), true));
            return;
        }

        CLI::write("SUCCESS: User registered: " . $newUser->username, "green");
        if ((int)$newUser->active !== 0) {
            CLI::error("FAIL: Newly registered user active status should be 0, got: " . $newUser->active);
            return;
        }
        if (!$newUser->inGroup('admin_bidang')) {
            CLI::error("FAIL: User should have group 'admin_bidang'.");
            return;
        }

        // 7.2. Verification Filter Interception Test
        CLI::write("Testing Verification Filter interception on unactivated user...", "blue");
        
        // Clear the pending auth state left by startLogin/startUpAction in registerAction
        auth()->logout();
        
        // Delete email activation action identity so login() can proceed
        // (VerificationFilter checks active=0, not the action identity)
        $db->table('auth_identities')
           ->where('user_id', $newUser->id)
           ->where('type', 'email_activate')
           ->delete();

        // Login the new user directly (simulates them coming back and logging in with active=0)
        // Since actions['login'] = null, this will fully log them in (STATE_LOGGED_IN) despite active=0
        // Note: Session::login() returns void, not a Result object
        auth('session')->getAuthenticator()->login($newUser);

        // Simulated Request to Dashboard
        $mockRequestDashboard = new \CodeIgniter\HTTP\IncomingRequest(config('App'), new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard'), null, new \CodeIgniter\HTTP\UserAgent());
        $filter = new \App\Filters\VerificationFilter();
        
        $filterResult = $filter->before($mockRequestDashboard);
        if ($filterResult instanceof \CodeIgniter\HTTP\RedirectResponse) {
            CLI::write("PASS: Unactivated user intercepted and redirected successfully.", "green");
        } else {
            CLI::error("FAIL: Verification Filter failed to intercept unactivated user on dashboard route.");
            return;
        }

        // Simulated Request to allowed route (auth/a/show)
        $mockRequestAllowed = new \CodeIgniter\HTTP\IncomingRequest(config('App'), new \CodeIgniter\HTTP\SiteURI(config('App'), 'auth/a/show'), null, new \CodeIgniter\HTTP\UserAgent());
        $filterResultAllowed = $filter->before($mockRequestAllowed);
        if ($filterResultAllowed === null) {
            CLI::write("PASS: Unactivated user allowed to access auth/a/show without redirection loop.", "green");
        } else {
            CLI::error("FAIL: Verification Filter intercepted allowed route 'auth/a/show'.");
            return;
        }

        // 7.3. Super Admin registers Kepala DISKOMINFO Test
        CLI::write("Logging in as Super Admin to test Kepala DISKOMINFO registration...", "blue");
        auth()->logout();
        $superadminUser = $usersProvider->findByCredentials(['email' => 'superadmin@vibe.id']);
        auth()->login($superadminUser);
        CLI::write("Logged in as " . auth()->user()->username, "green");

        $kadinUsername = 'kadintest' . $uniqId;
        $kadinEmail = 'kadintest' . $uniqId . '@vibe.id';

        $postKadin = [
            'username'     => $kadinUsername,
            'email'        => $kadinEmail,
            'password'     => 'Password123!',
            'nama_lengkap' => 'Kepala DISKOMINFO Test',
            'nip'          => '9876543210',
        ];

        $this->mockRequest($postKadin, 'admin/register-kadin');
        $userController = new \App\Controllers\Admin\UserController();
        $userController->initController(service('request'), service('response'), service('logger'));

        try {
            $userController->processRegisterKadin();
        } catch (\Exception $e) {
            CLI::write("Note: Kadin registration threw exception (expected in CLI): " . $e->getMessage(), "yellow");
        }

        // Check if Kepala DISKOMINFO user is registered
        $newKadin = $usersProvider->findByCredentials(['email' => $kadinEmail]);
        if (!$newKadin) {
            CLI::error("FAIL: Super Admin failed to register Kepala DISKOMINFO.");
            return;
        }

        CLI::write("SUCCESS: Registered new Kepala DISKOMINFO: " . $newKadin->username, "green");
        if ((int)$newKadin->active !== 0) {
            CLI::error("FAIL: Kadin user should be inactive initially, got: " . $newKadin->active);
            return;
        }
        if ($newKadin->kd_opd !== 'DISKOMINFO') {
            CLI::error("FAIL: Kadin user must be automatically bound to 'DISKOMINFO' OPD.");
            return;
        }
        if (!$newKadin->inGroup('kepala_diskominfo')) {
            CLI::error("FAIL: Kadin user should have group 'kepala_diskominfo'.");
            return;
        }

        CLI::write("\n==================================================", "yellow");
        CLI::write("  ALL INTEGRATION TESTS PASSED SUCCESSFULLY!       ", "yellow");
        CLI::write("==================================================", "yellow");
    }
}

