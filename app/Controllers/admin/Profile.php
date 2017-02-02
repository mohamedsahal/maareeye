<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Vendors_model;
use CodeIgniter\CLI\Console;

class Profile extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $model = new Vendors_model();
            $id = $_SESSION['user_id'];
            $data['profile'] = $model->edit_profile($id);
            $data['page'] = FORMS . 'manage-profile';
            $data['title'] = "My Profile - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function update()
    {
        // Check if modification is allowed (Demo Mode)
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];

            return $this->response->setJSON($response);
        }

        // Check if the user is logged in and is an admin
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            // Validate incoming request parameters
            $this->validation->setRules([
                'first_name' => 'required',
                'last_name' => 'required',
                'identity' => 'required',
            ]);

            if (!$this->validation->withRequest($this->request)->run()) {
                // If validation fails, return error response
                $errors = $this->validation->getErrors();
                return $this->response->setJSON([
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'error' => true,
                    'message' => $errors,
                    "data" => $_POST
                ]);
            }

            // Get user ID
            $id = $this->ionAuth->getUserId();

            // Prepare data array with updated user details
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'mobile' => $_POST['identity'],
            ];

            // Check if a new password is provided
            if (isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['password_confirm']) && !empty($_POST['password_confirm'])) {
                // Hash the new password
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                // Add hashed password to the data array
                $data['password'] = $hashedPassword;
            }

            // Update user details in the database
            $status = update_details(
                $data,
                ['id' => $id],
                'users'
            );



            if ($status) {
                // User details updated successfully
                return $this->response->setJSON([
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'error' => false,
                    'message' => "User updated successfully",
                    "data" => $_POST
                ]);
            } else {
                // Something went wrong while updating user details
                return $this->response->setJSON([
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'error' => true,
                    'message' => "Something went wrong...",
                    "data" => []
                ]);
            }
        } else {
            // Unauthorized access
            return $this->response->setJSON([
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
                'error' => true,
                'message' => "Unauthorized",
                "data" => []
            ]);
        }
    }

}
