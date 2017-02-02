<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class CheckPermissions implements FilterInterface
{
    protected $ionAuth;
    public function __construct()
    {
        helper('function_helper'); // Load the helper file
        $this->ionAuth = new \App\Libraries\IonAuth();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $response = service('response');

        // Check if $arguments is not null
        if ($arguments !== null) {
            $module = null;
            $action = null;

            foreach ($arguments as $argument) {
                list($key, $value) = explode('=', $argument);

                if ($key === 'module') {
                    $module = $value;
                } elseif ($key === 'action') {
                    $action = $value;
                }
            }

            if (! $this->ionAuth->isAdmin() && ! $this->ionAuth->isVendor()) {
                if ($this->isTeamMemberActive()) {

                    //   Check if the user has permission for the specified module and action
                    if ($module !== null && $action !== null) {
                        if (!$this->userHasPermission($module, $action)) {
                            return $this->handleNoPermission($request, $response, $session);
                        }
                    }
                } else {
                    // If team member is not active, deny access
                    return $this->handleNoPermission($request, $response, $session);
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Code after the request
    }

    private function isTeamMemberActive()
    {
        $user_id = $this->ionAuth->getUserId();
        $team_member = fetch_details('team_members', ['user_id' => $user_id]);

        if (!empty($team_member)) {
            return $team_member[0]['status'] == 1;
        }

        return false;
    }

    private function userHasPermission($moduleName, $action)
    {
        $session = Services::session();
        $maareeyeConfig = new \Config\Maareeye();
        $all_permissions = $maareeyeConfig->permissions;

        $userPermissions = $this->get_user_permissions($session->get('user_id'));


        if (!empty($userPermissions)) {
            $userPermissions = json_decode($userPermissions[0]['permissions'], true);

            if (
                isset($userPermissions["'$moduleName'"]) &&
                in_array($action, $all_permissions[$moduleName]) &&
                in_array("'$action'", $userPermissions["'$moduleName'"])
            ) {
                return true;
            }
        }

        return false;
    }

    // Get user permissions from the database
    private function get_user_permissions($id)
    {
        return fetch_details('team_members', ['user_id' => $id]);
    }

    private function handleNoPermission($request, $response, $session)
    {
        // If the request is an AJAX request, return a JSON response
        if ($request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            $jsonResponse = [
                'error' => true,
                'message' => ['You do not have permission to access this page.'],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ];

            return $response->setJSON($jsonResponse);
        }

        // For non-AJAX requests, set flash data and redirect
        $session->setFlashdata("message", "You do not have permission to access this page.");
        $session->setFlashdata("type", "error");


        return redirect()->to(base_url('vendor/home'));
    }
}
