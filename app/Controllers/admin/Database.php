<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class Database extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            if (isset($_SESSION['business_id'])) {
                if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                    return redirect()->to("admin/businesses");
                }
            }
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
            $data['page'] = VIEWS . "backup";
            $data['title'] = "Database Backup - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function backup()
    {
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $rows = [];
            $limit = isset($_GET['limit']) && !empty($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $offset = isset($_GET['offset']) && !empty($_GET['offset']) ? (int) $_GET['offset'] : 0;
            $i = 0;
            $j = $offset + 1; // for correct numbering based on offset

            $path = FCPATH . 'public/database_backup/';
            $maps = get_dir_file_info($path);

            // Sort files by date (descending)
            uasort($maps, function ($a, $b) {
                return $b['date'] <=> $a['date'];
            });

            // Total number of files before pagination
            $total_files = count($maps);

            // Paginate the array (slice it)
            $paginated_maps = array_slice($maps, $offset, $limit);

            foreach ($paginated_maps as $files) {
                $action = "<button id='" . $files['name'] . "' onclick='download_backup(this)' class='btn btn-info btn-sm m-1' title='Download'><i class='bi bi-download'></i></button> ";

                $action .= "<button id='" . $files['name'] . "' data-bs-toggle='modal' data-bs-target='#mail_DBbackup' onClick='mail_backup(this)' class='btn btn-warning btn-sm m-1' title='Mail'><i class='bi bi-envelope'></i></button> ";

                $action .= "<button id='" . $files['name'] . "' class='btn btn-danger btn-sm m-1' onClick='delete_backup(this)' title='Delete'><i class='bi bi-trash'></i></button>";

                $rows[$i] = [
                    'no_of_files' => $j,
                    'file' => $files['name'],
                    'date' => date("d-m-Y h:i:s", $files['date']),
                    'server_path' => $files['server_path'],
                    'relative_path' => $files['relative_path'],
                    'action' => $action,
                ];
                $i++;
                $j++;
            }

            $array['rows'] = $rows;
            $array['total'] = $total_files;
        }

        echo json_encode($array);
    }

    public function backup_database()
    {
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $db = \Config\Database::connect();
            $database = $db->database;
            $host = $db->hostname;
            $password = $db->password;
            $user = $db->username;

            $backup = backup_tables($host, $user, $password, $database);

            if ($backup) {
                return $this->response->setJSON([
                    "error" => false,
                    "message" => "Database Backup Create Successfully!",
                    "data" => [],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    "error" => true,
                    "message" => "Database Backup NOt Created!",
                    "data" => [],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            }



        }
    }
    public function mail_database()
    {
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $setting = get_settings('email', true);
            $email_id = $_POST['email'];
            $message = $_POST['message'];
            $company_title = get_settings('general', true);
            $path = FCPATH . 'public/database_backup/' . $_POST['file_name'] . '';

            $email_config = array(
                'charset' => 'iso-8859-1',
                'mailType' => 'html'
            );
            $subject = "Database Backup";
            $email = \Config\Services::email();
            $email->initialize($email_config);
            $email->setFrom($setting['email'], $company_title['title']);
            $email->setTo(trim($email_id));
            $email->setSubject($subject);
            $email->setMessage($message);
            $email->attach($path);
            if ($email->send()) {
                return $this->response->setJSON([
                    "error" => false,
                    "message" => "Email sent!",
                    "data" => [],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    "error" => true,
                    "message" => "Something went wrong Please try again after some time.",
                    "data" => [
                        'console' => "console.log(" . $email->printDebugger() . ");"
                    ],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            }
        }
    }
    public function delete()
    {
        $path = FCPATH . 'public/database_backup/' . $_POST['file_name'] . '';

        if (unlink($path)) {
            return $this->response->setJSON([
                "error" => false,
                "message" => "Backup Deleted Successfully!",
                "data" => [],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                "error" => true,
                "message" => "Something went wrong Please try again after some time.",
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        }
        return redirect()->to('/admin/database');
    }
}
