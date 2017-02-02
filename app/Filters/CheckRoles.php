<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;


class CheckRoles implements FilterInterface
{
    private $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
    }
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
 
        if (! $this->ionAuth->loggedIn() || ! $this->ionAuth->isVendor()) {
           
            $session->setFlashdata("message", "You don't have permission to access this page.");
            $session->setFlashdata("type", "error");
            if ($this->ionAuth->isAdmin()) {
                return redirect()->to(base_url('admin/home'));
            }
            return redirect()->to(base_url('vendor/home'));
        }

        
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action required after the request.
    }
}
