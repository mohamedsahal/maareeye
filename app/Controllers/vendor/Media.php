<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Media_model;


class Media extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    protected $validationListTemplate = 'list';
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->select()->where(['user_id' => session('user_id')])->get();
                if (empty($allbusiness)) {
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
                } else {
                    session()->setFlashdata('message', 'Please select a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
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
            $data['page'] = VIEWS . 'media_table';
            $data['title'] = "Media - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['business_id'] = session('business_id');
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function media_table()
    {
        $media_model = new Media_model();
        $user_id = $_SESSION['user_id'];

        // Determine actual vendor_id
        $vendor_id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        // Fetch supplier data with total
        $media_data = $media_model->get_media($vendor_id);
        $total = $media_data['total'] ?? 0;
        $media_data = $media_data['data'] ?? [];


        $rows = [];
        foreach ($media_data as $media) {

            $action = '';

            $action = '<a href="javascript:void(0);" class="delete-media action-btn btn btn-danger btn-xs ml-1 mr-1 mb-1" title="Delete" data-id="' . $media['id'] . '" ><i class="fa fa-trash"></i></a>';
            $action .= '<a href="javascript:void(0);" class="copy-to-clipboard btn btn-primary btn-xs action-btn ml-1 mr-1 mb-1" title="Copy to clipboard" ><i class="fa fa-clipboard"></i></a>';
            $action .= "<a href='javascript:void(0);' class='btn btn-info btn-xs mr-1 mb-1 ml-1 action-btn copy-relative-path' data-path=" . $media['sub_directory'] . $media['name'] . " title='Copy image path for csv file'><i class='fa fa-file-alt'></i></a>";

            if (file_exists($media['sub_directory'] . $media['name'])) {
                $image = base_url($media['sub_directory'] . $media['name']);
            } else {
                $image = base_url('public/backend/assets/img/no-image.jpg');
            }

            $media['image'] = '<div class = "image-box-100"><span class="path d-none">' . base_url() . $media['sub_directory'] . $media['name'] . '</span><span class="relative-path d-none">' . $media['sub_directory'] . $media['name'] . '</span><a class="align-items-center d-flex icon-box justify-content-center" href=" ' . $image . '" data-lightbox="image-1"> 
             <img src=" ' . $image . ' "" class="image-100 image-box-100 img-fluid" /> 
            </a></div>';

            $rows[] = [
                'id' => $media['id'] ?? '',
                'vendor_id' => $media['vendor_id'] ?? '',
                'name' => $media['name'] ?? '',
                'image' => $media['image'],
                'extension' => $media['extension'] ?? '',
                'sub_directory' => $media['sub_directory'] ?? '',
                'size' => $media['size'] ?? '',
                'action' => $action,

            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $rows
        ]);
    }
    public function upload()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ['Modifications are not allowed in demo mode.'],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ]);
        }

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $status = subscription();
        if ($status != 'active') {
            $msg = $status == 'upcoming' ? 'Your subscription has not started yet!' : 'Please Buy Subscription to proceed ahead!';
            return $this->response->setJSON([
                'error' => true,
                'message' => [$msg],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
        $media_model = new Media_model();
        $session_user_id = $_SESSION['user_id'];
        $vendor_id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($session_user_id) : $session_user_id;

        if (!check_subscription($vendor_id)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ['No active subscription found.'],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $media_ids = [];
        $other_images_new_name = [];
        $other_image_info_error = '';

        $sub_directory = 'public/uploads/media/';
        $target_path = './public/uploads/media/';

        if (!is_dir($target_path)) {
            mkdir($target_path, 0777, true);
        }

        $files = $this->request->getFiles();

        if (isset($files['documents'])) {
            foreach ($files['documents'] as $i => $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Move this part BEFORE move()
                    $extension = $file->getClientExtension(); // uses $_FILES[] info
                    $type = $file->getMimeType();             // reads from temp file
                    $size = $file->getSize();
                    $title = pathinfo($file->getName(), PATHINFO_FILENAME);

                    $newName = $file->getRandomName();
                    $file->move($target_path, $newName); // After calling getMimeType()

                    $media_data = [
                        'vendor_id' => $vendor_id,
                        'title' => $title,
                        'name' => $newName,
                        'extension' => $extension,
                        'type' => $type,
                        'sub_directory' => $sub_directory,
                        'size' => $size
                    ];

                    $media_id = $media_model->insert($media_data);
                    $media_ids[] = $media_id;
                    $other_images_new_name[] = $newName;
                } else {
                    $other_image_info_error .= ' ' . $file->getErrorString();
                }
            }

        }

        return $this->response->setJSON([
            'error' => empty($other_image_info_error) ? false : true,
            'message' => empty($other_image_info_error) ? ['Files uploaded successfully.'] : [$other_image_info_error],
            'media_ids' => $media_ids,
            'file_names' => $other_images_new_name,
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }
    public function delete($id)
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ['Modifications are not allowed in demo mode.'],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ]);
        }

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $status = subscription();
        if ($status != 'active') {
            $msg = $status == 'upcoming' ? 'Your subscription has not started yet!' : 'Please Buy Subscription to proceed ahead!';
            return $this->response->setJSON([
                'error' => true,
                'message' => [$msg],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }
        $media_model = new Media_model();
        $session_user_id = $_SESSION['user_id'];
        $vendor_id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($session_user_id) : $session_user_id;

        if (!check_subscription($vendor_id)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ['No active subscription found.'],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $media = $media_model->where(['id' => $id])->findAll();

        if (file_exists($media[0]['sub_directory'] . $media[0]['name'])) {
            unlink($media[0]['sub_directory'] . $media[0]['name']);
        }

        $status = $media_model->where("id", $id)->delete();

        return $this->response->setJSON([
            'error' => $status ? false : true,
            'message' => $status ? 'Files deleted successfully.' : 'Files Not deleted successfully',
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }


}
