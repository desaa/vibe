<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VerificationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');
        
        if ($auth->loggedIn()) {
            $user = $auth->user();
            
            // Jika user belum aktif (belum memverifikasi email)
            if (!$user->active) {
                // Kecuali untuk halaman verifikasi itu sendiri dan logout
                $currentRoute = $request->getUri()->getPath();
                
                // Use str_contains for robust matching regardless of path format
                $excludedPaths = ['auth/a/show', 'auth/a/verify', 'logout'];
                foreach ($excludedPaths as $excluded) {
                    if (str_contains($currentRoute, $excluded)) {
                        return;
                    }
                }
                
                return redirect()->to(base_url('auth/a/show'))
                                 ->with('error', 'Silakan masukkan kode verifikasi yang telah dikirim ke email Anda.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
