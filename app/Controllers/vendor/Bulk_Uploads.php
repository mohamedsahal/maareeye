<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Categories_model;
use App\Models\Customers_model;
use App\Models\Delivery_boys_model;
use App\Models\Orders_items_model;
use App\Models\Products_model;
use App\Models\Orders_model;
use App\Models\Orders_services_model;
use App\Models\Products_variants_model;
use App\Models\Services_model;
use App\Models\Suppliers_model;
use App\Models\Tax_model;
use App\Models\Vendor_purchase_transactions_model;
use App\Models\Vendors_model;
use App\Models\WarehouseProductStockModel;
use DateTime;
use Tests\Support\Models\UserModel;
use App\Models\Purchases_model;
use App\Models\Purchases_items_model;

//$id = $this->ionAuth->getUserId();
class Bulk_Uploads extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }

    private function isValidTaxString($taxString)
    {
        // Define the regex pattern for validation
        $pattern = '/^\["\d+"(,"[\d]+")*\]$/';

        // Use preg_match to check if the tax string matches the pattern
        return preg_match($pattern, $taxString) === 1;
    }

    // public function import_products()
    // {

    //     if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
    //         return redirect()->to('login');
    //     } else {
    //         $status = subscription();
    //         if ($status == 'active') {
    //             $vendor_id = $_SESSION['user_id'];
    //             if ($this->ionAuth->isTeamMember()) {
    //                 $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
    //             } else {
    //                 $vendor_id = $_SESSION['user_id'];
    //             }
    //             $subscription = check_subscription($vendor_id);
    //             if ($subscription) {

    //                 $db = \Config\Database::connect();
    //                 $user_packages = $db->table('users_packages up')->select('up.*')->where(['user_id' => $vendor_id])->get()->getResultArray();

    //                 $allowed_products = 0;
    //                 $can_create = true;

    //                 $products_model = new Products_model();
    //                 $no_of_products = $products_model->where('vendor_id', $vendor_id)->countAllResults();

    //                 if (!empty($user_packages)) {

    //                     foreach ($user_packages as $p) {
    //                         $status = subscription_status($p['id']);
    //                         if ($status == 'active') {
    //                             $allowed_products = $p["no_of_products"];
    //                             break;
    //                         }
    //                     }
    //                 }
    //                 if ($status == 'active' && $allowed_products != '-1' && $no_of_products >= $allowed_products) {
    //                     $can_create = false;
    //                 }
    //                 if ($can_create) {
    //                     if (isset($_POST) && !empty($_POST)) {
    //                         $this->validation->setRules([
    //                             'business_id' => 'required',
    //                         ]);

    //                         if (empty($_FILES['file']['name'])) {
    //                             $this->validation->setRules([
    //                                 'file' => 'required',
    //                             ]);
    //                         }
    //                         if (!$this->validation->withRequest($this->request)->run()) {

    //                             $errors = $this->validation->getErrors();
    //                             $response['error'] = true;
    //                             foreach ($errors as $e) {
    //                                 $response['message'] = $e;
    //                                 $response['csrf_token'] = csrf_token();
    //                                 $response['csrf_hash'] = csrf_hash();
    //                                 $response['data'] = [];
    //                             }
    //                             return $this->response->setJSON($response);
    //                         } else {

    //                             $oration_type = $this->request->getVar('type');

    //                             if (empty($oration_type)) {
    //                                 $response['error'] = true;
    //                                 $response['csrf_token'] = csrf_token();
    //                                 $response['csrf_hash'] = csrf_hash();
    //                                 $response['message'] = 'Select Type  !';
    //                                 return $this->response->setJSON($response);
    //                             }

    //                             $file = $this->request->getFile('file');
    //                             // $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
    //                             $allowed_mime_type_arr = array(
    //                                 'text/x-comma-separated-values',
    //                                 'text/comma-separated-values',
    //                                 'application/x-csv',
    //                                 'text/x-csv',
    //                                 'text/csv',
    //                                 'application/csv',
    //                                 'application/vnd.ms-excel', // For older Excel files (.xls)
    //                                 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
    //                                 'text/plain' // Allow .csv files that are identified as text/plain
    //                             );
    //                             $type = $this->request->getVar('type');
    //                             $mime = $file->getMimeType();
    //                             if (!in_array($mime, $allowed_mime_type_arr)) {
    //                                 $response['error'] = true;
    //                                 $response['csrf_token'] = csrf_token();
    //                                 $response['csrf_hash'] = csrf_hash();
    //                                 $response['message'] = 'Invalid file format!';
    //                                 return $this->response->setJSON($response);
    //                             }

    //                             if ($file->isValid() && !$file->hasMoved()) {
    //                                 $filePath = WRITEPATH . 'uploads/' . $file->getName();
    //                                 $file->move(WRITEPATH . 'uploads/');

    //                                 if ($oration_type == "upload") {
    //                                     if (($handle = fopen($filePath, 'r')) !== false) {
    //                                         $productModel = new Products_model();
    //                                         $productVariantModel = new Products_variants_model();

    //                                         fgetcsv($handle); // Skip header

    //                                         $products = [];
    //                                         $rowCount = 1;
    //                                         $business_id = $_SESSION['business_id'];
    //                                         $vendor_id = $_SESSION['user_id'];
    //                                         if ($this->ionAuth->isTeamMember()) {
    //                                             $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
    //                                         } else {
    //                                             $vendor_id = $_SESSION['user_id'];
    //                                         }

    //                                         while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    //                                             $productName = $data[4];
    //                                             if (!isset($products[$productName])) {
    //                                                 // Insert product
    //                                                 $productData = [
    //                                                     'category_id' => $data[0],
    //                                                     'business_id' => $business_id,
    //                                                     'vendor_id' => $vendor_id,
    //                                                     'tax_ids' => $data[3],
    //                                                     'name' => $productName,
    //                                                     'description' => $data[5],
    //                                                     'qty_alert' => $data[6],
    //                                                     'image' => $data[7],
    //                                                     'type' => $data[8],
    //                                                     'stock_management' => $data[9],
    //                                                     'stock' => $data[10],
    //                                                     'unit_id' => $data[11],
    //                                                     'is_tax_included' => $data[12],
    //                                                     'status' => $data[13],
    //                                                 ];

    //                                                 if (!$productData['is_tax_included'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['is_tax_included'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Is tax included Id is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 } else {
    //                                                     if (empty($productData['tax_ids'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'tax_ids is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     } {
    //                                                         $taxString = $productData['tax_ids'];
    //                                                         if (!$this->isValidTaxString($taxString)) {
    //                                                             // The tax string is invalid;

    //                                                             $response['error'] = true;
    //                                                             $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
    //                                                             $response['csrf_token'] = csrf_token();
    //                                                             $response['csrf_hash'] = csrf_hash();
    //                                                             return $this->response->setJSON($response);
    //                                                         }
    //                                                     }
    //                                                 }

    //                                                 if ($productData['is_tax_included'] == 1) {
    //                                                     $productData['tax_ids'] = json_encode([]);
    //                                                 }

    //                                                 if (empty($productData['category_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Category id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['business_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Business id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['vendor_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Vendor id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (!$productData['stock_management'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['stock_management'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Stock Management is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if ($productData['stock_management'] == 2) {
    //                                                     $productData['type'] = "variable";
    //                                                     $productData['stock'] = 0;
    //                                                     $productData['unit_id'] = 0;
    //                                                     $productData['qty_alert'] == "";
    //                                                 }

    //                                                 if (!$productData['stock'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['stock'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Stock is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if (!$productData['unit_id'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['unit_id'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Unit Id is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if (!$productData['qty_alert'] == "") { // if the value is empty string it will ignore the validation.
    //                                                     if (empty($productData['qty_alert'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Qty Alert is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }


    //                                                 if (empty($productData['name'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Name is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['description'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Description is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }

    //                                                 if (empty($productData['image'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Image is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['type'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Type is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }

    //                                                 if (empty($productData['status'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Status is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }


    //                                                 $productId = $productModel->insert($productData);

    //                                                 // Store product ID to use for variants
    //                                                 $products[$productName] = $productId;
    //                                             } else {
    //                                                 $productId = $products[$productName];
    //                                             }

    //                                             // Insert variant
    //                                             if ($productData['stock_management'] == 2) {
    //                                                 $variantData = [
    //                                                     'product_id' => $productId,
    //                                                     'variant_name' => $data[14],
    //                                                     'sale_price' => $data[15],
    //                                                     'purchase_price' => $data[16],
    //                                                     'stock' => $data[17],
    //                                                     'qty_alert' => $data[18],
    //                                                     'unit_id' => $data[19],
    //                                                     'status' => $data[20],
    //                                                 ];
    //                                                 $productVariantModel->insert($variantData);
    //                                             }
    //                                         }

    //                                         fclose($handle);
    //                                         $response['error'] = false;
    //                                         $response['csrf_token'] = csrf_token();
    //                                         $response['csrf_hash'] = csrf_hash();
    //                                         $response['message'] = 'Products Uploaded successfully!';
    //                                         return $this->response->setJSON($response);
    //                                     } else {
    //                                         $response['error'] = true;
    //                                         $response['csrf_token'] = csrf_token();
    //                                         $response['csrf_hash'] = csrf_hash();
    //                                         $response['message'] = 'Failed to open the file.';
    //                                         return $this->response->setJSON($response);
    //                                     }
    //                                 } else {

    //                                     if (($handle = fopen($filePath, 'r')) !== false) {
    //                                         $productModel = new Products_model();
    //                                         $productVariantModel = new Products_variants_model();

    //                                         fgetcsv($handle); // Skip header

    //                                         $products = [];
    //                                         $rowCount = 1;
    //                                         $business_id = $_SESSION['business_id'];
    //                                         $vendor_id = $_SESSION['user_id'];
    //                                         if ($this->ionAuth->isTeamMember()) {
    //                                             $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
    //                                         } else {
    //                                             $vendor_id = $_SESSION['user_id'];
    //                                         }

    //                                         while (($data = fgetcsv($handle, 1000, ',')) !== false) {

    //                                             $productName = $data[5];
    //                                             if (!isset($products[$productName])) {
    //                                                 // Insert product

    //                                                 $productData = [
    //                                                     'id' => $data[0],
    //                                                     'category_id' => $data[1],
    //                                                     'business_id' => $business_id,
    //                                                     'vendor_id' => $vendor_id,
    //                                                     'tax_ids' => $data[4],
    //                                                     'name' => $productName,
    //                                                     'description' => $data[6],
    //                                                     'qty_alert' => $data[7],
    //                                                     'image' => $data[8],
    //                                                     'type' => $data[9],
    //                                                     'stock_management' => $data[10],
    //                                                     'stock' => $data[11],
    //                                                     'unit_id' => $data[12],
    //                                                     'is_tax_included' => $data[13],
    //                                                     'status' => $data[14],
    //                                                 ];

    //                                                 /*
    //                                                     expected data;
    //                                                     data
    //                                                     (
    //                                                         data[0] => id (product id)
    //                                                         data[1] => category_id
    //                                                         data[2] => business_id
    //                                                         data[3] => vendor_id
    //                                                         data[4] => tax_id
    //                                                         data[5] => name
    //                                                         data[6] => description
    //                                                         data[7] => qty_alert
    //                                                         data[8] => image
    //                                                         data[9] => type
    //                                                         data[10] => stock_management
    //                                                         data[11] => stock
    //                                                         data[12] => unit_id
    //                                                         data[13] => is_tax_included
    //                                                         data[14] => status
    //                                                         data[15] => variant_id
    //                                                         data[16] => variant_name
    //                                                         data[17] => sale_price
    //                                                         data[18] => purchase_price
    //                                                         data[19] => variant_stock
    //                                                         data[20] => variant_qty_alert
    //                                                         data[21] => variant_unit_id
    //                                                         data[22] => variant_status
    //                                                     )
    //                                                     $productData
    //                                                     (
    //                                                         $productData[id] => id (product id)
    //                                                         $productData[category_id] => category_id
    //                                                         $productData[business_id] => 3
    //                                                         $productData[vendor_id] => 1
    //                                                         $productData[tax_id] => tax_id
    //                                                         $productData[name] => tax_id
    //                                                         $productData[description] => description
    //                                                         $productData[qty_alert] => qty_alert
    //                                                         $productData[image] => image
    //                                                         $productData[type] => type
    //                                                         $productData[stock_management] => stock_management
    //                                                         $productData[stock] => stock
    //                                                         $productData[unit_id] => unit_id
    //                                                         $productData[is_tax_included] => is_tax_included
    //                                                         $productData[status] => status
    //                                                     )
    //                                                 */
    //                                                 if (!$productData['is_tax_included'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['is_tax_included'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Is tax included Id is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 } else {
    //                                                     if (empty($productData['tax_ids'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'tax_ids is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     } {
    //                                                         $taxString = $productData['tax_ids'];
    //                                                         if (!$this->isValidTaxString($taxString)) {
    //                                                             // The tax string is invalid;

    //                                                             $response['error'] = true;
    //                                                             $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
    //                                                             $response['csrf_token'] = csrf_token();
    //                                                             $response['csrf_hash'] = csrf_hash();
    //                                                             return $this->response->setJSON($response);
    //                                                         }
    //                                                     }
    //                                                 }

    //                                                 if ($productData['is_tax_included'] == 1) {
    //                                                     $productData['tax_ids'] = json_encode([]);
    //                                                 }


    //                                                 if (empty($productData['category_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Category id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['business_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Business id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['vendor_id'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Vendor id is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (!$productData['stock_management'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['stock_management'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Stock Management is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if ($productData['stock_management'] == 0) {
    //                                                     $productData['stock'] = 0;
    //                                                     $productData['unit_id'] = 0;
    //                                                     $productData['tax_id'] = 0;
    //                                                 }

    //                                                 if ($productData['stock_management'] == 2) {
    //                                                     $productData['type'] = "variable";
    //                                                     $productData['stock'] = 0;
    //                                                     $productData['unit_id'] = 0;
    //                                                     $productData['qty_alert'] == "";
    //                                                 }

    //                                                 if (!$productData['stock'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['stock'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Stock is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if (!$productData['unit_id'] == 0) { // if the value is zero it will ignore the validation.
    //                                                     if (empty($productData['unit_id'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Unit Id is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if (!$productData['qty_alert'] == "") { // if the value is empty string it will ignore the validation.
    //                                                     if (empty($productData['qty_alert'])) {
    //                                                         $response['error'] = true;
    //                                                         $response['message'] = 'Qty Alert is empty at row ' . $rowCount;
    //                                                         $response['csrf_token'] = csrf_token();
    //                                                         $response['csrf_hash'] = csrf_hash();
    //                                                         return $this->response->setJSON($response);
    //                                                     }
    //                                                 }

    //                                                 if (empty($productData['name'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Name is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }
    //                                                 if (empty($productData['description'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Description is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }

    //                                                 if (empty($productData['image'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Image is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 } else {
    //                                                     $productData['image'] = "public/uploads/products/" . $productData['image'];
    //                                                 }
    //                                                 if (empty($productData['type'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Type is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }

    //                                                 if (empty($productData['status'])) {
    //                                                     $response['error'] = true;
    //                                                     $response['message'] = 'Status is empty at row ' . $rowCount;
    //                                                     $response['csrf_token'] = csrf_token();
    //                                                     $response['csrf_hash'] = csrf_hash();
    //                                                     return $this->response->setJSON($response);
    //                                                 }


    //                                                 $productModel->update($productData['id'], $productData);
    //                                                 $productId = $productData['id'];

    //                                                 // Store product ID to use for variants
    //                                                 $products[$productName] = $productId;
    //                                             } else {
    //                                                 $productId = $products[$productName];
    //                                             }

    //                                             // Insert variant
    //                                             if ($productData['stock_management'] == 2) {
    //                                                 $variantId = $data[15];
    //                                                 $variantData = [
    //                                                     'variant_name' => $data[16],
    //                                                     'sale_price' => $data[17],
    //                                                     'purchase_price' => $data[18],
    //                                                     'stock' => $data[19],
    //                                                     'qty_alert' => $data[20],
    //                                                     'unit_id' => $data[21],
    //                                                     'status' => $data[22],
    //                                                 ];

    //                                                 $productVariantModel->update($variantId, $variantData);
    //                                             }

    //                                             $rowCount++;
    //                                         }

    //                                         fclose($handle);
    //                                         $response['error'] = false;
    //                                         $response['csrf_token'] = csrf_token();
    //                                         $response['csrf_hash'] = csrf_hash();
    //                                         $response['message'] = 'Products updated successfully!';
    //                                         return $this->response->setJSON($response);
    //                                     } else {
    //                                         $response['error'] = true;
    //                                         $response['csrf_token'] = csrf_token();
    //                                         $response['csrf_hash'] = csrf_hash();
    //                                         $response['message'] = 'Failed to open the file.';
    //                                         return $this->response->setJSON($response);
    //                                     }
    //                                 }
    //                             } else {
    //                                 $response['error'] = true;
    //                                 $response['csrf_token'] = csrf_token();
    //                                 $response['csrf_hash'] = csrf_hash();
    //                                 $response['message'] = 'Failed to upload the file.';
    //                                 return $this->response->setJSON($response);
    //                             }
    //                         }
    //                     }
    //                 } else {
    //                     $response = [
    //                         'error' => true,
    //                         'message' => ['You have Exceeded limit of adding products'],
    //                         'data' => []
    //                     ];
    //                     $response['csrf_token'] = csrf_token();
    //                     $response['csrf_hash'] = csrf_hash();
    //                     return $this->response->setJSON($response);
    //                 }
    //             } else {
    //                 return false;
    //             }
    //         }
    //         if ($status == 'upcoming') {
    //             $response = [
    //                 'error' => true,
    //                 'message' => ['Your subscription has not started yet!'],
    //             ];
    //             $response['csrf_token'] = csrf_token();
    //             $response['csrf_hash'] = csrf_hash();
    //             return $this->response->setJSON($response);
    //         }
    //         if ($status == 'expired') {
    //             $response = [
    //                 'error' => true,
    //                 'message' => ['Please Buy Subscription to proceed ahead!'],
    //             ];
    //             $response['csrf_token'] = csrf_token();
    //             $response['csrf_hash'] = csrf_hash();
    //             return $this->response->setJSON($response);
    //         }
    //     }
    // }
    public function import_products()
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                $vendor_id = $_SESSION['user_id'];
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                } else {
                    $vendor_id = $_SESSION['user_id'];
                }
                $subscription = check_subscription($vendor_id);
                if ($subscription) {

                    $db = \Config\Database::connect();
                    $user_packages = $db->table('users_packages up')->select('up.*')->where(['user_id' => $vendor_id])->get()->getResultArray();

                    $allowed_products = 0;
                    $can_create = true;

                    $products_model = new Products_model();
                    $no_of_products = $products_model->where('vendor_id', $vendor_id)->countAllResults();

                    if (!empty($user_packages)) {

                        foreach ($user_packages as $p) {
                            $status = subscription_status($p['id']);
                            if ($status == 'active') {
                                $allowed_products = $p["no_of_products"];
                                break;
                            }
                        }
                    }
                    if ($status == 'active' && $allowed_products != '-1' && $no_of_products >= $allowed_products) {
                        $can_create = false;
                    }
                    if ($can_create) {
                        if (isset($_POST) && !empty($_POST)) {
                            $this->validation->setRules([
                                'business_id' => 'required',
                            ]);

                            if (empty($_FILES['file']['name'])) {
                                $this->validation->setRules([
                                    'file' => 'required',
                                ]);
                            }
                            if (!$this->validation->withRequest($this->request)->run()) {

                                $errors = $this->validation->getErrors();
                                $response['error'] = true;
                                foreach ($errors as $e) {
                                    $response['message'] = $e;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['data'] = [];
                                }
                                return $this->response->setJSON($response);
                            } else {
                                // Process uploaded file and handle supplier data
                                $file = $this->request->getFile('file');
                                $allowed_mime_type_arr = array(
                                    'text/x-comma-separated-values',
                                    'text/comma-separated-values',
                                    'application/x-csv',
                                    'text/x-csv',
                                    'text/csv',
                                    'application/csv',
                                    'application/vnd.ms-excel', // For older Excel files (.xls)
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                                    'text/plain' // Allow .csv files that are identified as text/plain
                                );
                                $mime = $file->getMimeType();

                                // Validate MIME type
                                if (!in_array($mime, $allowed_mime_type_arr)) {
                                    $response['error'] = true;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['message'] = 'Invalid file format!';
                                    return $this->response->setJSON($response);
                                }

                                $type = $_POST['type'];
                                $file_path = $_FILES['file']['tmp_name'];

                                // Convert CSV to JSON
                                $json_data = csvToJsonProduct($file_path, $type);
                                if (!$json_data) {
                                    $this->response['error'] = true;
                                    $this->response['message'] = 'Error converting CSV to JSON!';
                                    print_r(json_encode($this->response));
                                    return false;
                                }

                                if ($this->ionAuth->isTeamMember()) {
                                    $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                                } else {
                                    $vendor_id = $_SESSION['user_id'];
                                }
                                $products_model = new Products_model();
                                $products_variants_model = new products_variants_model();
                                $warehouse_product_stock_model = new WarehouseProductStockModel();

                                if ($type == 'upload') {

                                    $required_fields = [
                                        'name',
                                        'category_id',
                                        'business_id',
                                        'description',
                                        'image',
                                        'product_type',
                                        'stock_management',
                                        'variants',
                                    ];
                                    for ($i = 0; $i < count($json_data); $i++) {

                                        $row = $json_data[$i];
                                        $missing_fields = [];

                                        // Check for missing required fields
                                        foreach ($required_fields as $field) {
                                            if (!isset($row[$field]) || empty($row[$field])) {
                                                $missing_fields[] = $field;
                                            }
                                        }

                                        if (isset($row['product_type']) && !empty($row['product_type']) && $row['product_type'] == "simple") {

                                            if (!isset($row['variants'][0]['variant_name']) || empty($row['variants'][0]['variant_name'])) {
                                                $missing_fields[] = 'variant_name';
                                            }
                                            if (!isset($row['variants'][0]['sale_price']) || empty($row['variants'][0]['sale_price'])) {
                                                $missing_fields[] = 'sale_price';
                                            }
                                            if (!isset($row['variants'][0]['purchase_price']) || empty($row['variants'][0]['purchase_price'])) {
                                                $missing_fields[] = 'purchase_price';
                                            }
                                            // if (!isset($row['variants'][0]['unit_id']) || empty($row['variants'][0]['unit_id'])) {
                                            //     $missing_fields[] = 'unit_id';
                                            // }
                                            // if (!isset($row['variants'][0]['stock']) || empty($row['variants'][0]['stock'])) {
                                            //     $missing_fields[] = 'stock';
                                            // }
                                            // if (!isset($row['variants'][0]['qty_alert']) || empty($row['variants'][0]['qty_alert'])) {
                                            //     $missing_fields[] = 'qty_alert';
                                            // }
                                        } else {
                                            for ($k = 0; $k < count($row['variants']); $k++) {

                                                if (!isset($row['variants'][$k]['variant_name']) || empty($row['variants'][$k]['variant_name'])) {
                                                    $missing_fields[] = 'variant_name';
                                                }
                                                if (!isset($row['variants'][$k]['sale_price']) || empty($row['variants'][$k]['sale_price'])) {
                                                    $missing_fields[] = 'sale_price';
                                                }
                                                if (!isset($row['variants'][$k]['purchase_price']) || empty($row['variants'][$k]['purchase_price'])) {
                                                    $missing_fields[] = 'purchase_price';
                                                }
                                            }
                                        }
                                        if (!empty($missing_fields)) {
                                            $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                            continue;
                                        }
                                    }
                                    // If there are errors, return them
                                    if (!empty($errors)) {
                                        $response['error'] = true;
                                        $response['message'] = $errors;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }

                                    for ($i = 0; $i < count($json_data); $i++) {

                                        $product_stock = 0;
                                        $product_qty_alert = 0;
                                        $product_unit_id = 0;

                                        if (isset($json_data[$i]['stock_management']) && !empty($json_data[$i]['stock_management']) && $json_data[$i]['stock_management'] == 1) {
                                            $product_stock = $json_data[$i]['product_stock'];
                                            $product_qty_alert = $json_data[$i]['product_qty_alert'];
                                            $product_unit_id = $json_data[$i]['product_unit_id'];
                                        }

                                        $products = array(
                                            'vendor_id' => $vendor_id,
                                            'business_id' => $json_data[$i]['business_id'],
                                            'category_id' => $json_data[$i]['category_id'],
                                            'tax_ids' => $json_data[$i]['tax_ids'],
                                            'name' => $json_data[$i]['name'],
                                            'description' => $json_data[$i]['description'],
                                            'image' => $json_data[$i]['image'],
                                            'type' => $json_data[$i]['product_type'],
                                            'stock_management' => $json_data[$i]['stock_management'],
                                            'stock' => $product_stock,
                                            'qty_alert' => $product_qty_alert,
                                            'unit_id' => $product_unit_id,
                                            'brand_id' => $json_data[$i]['brand_id'],
                                            'is_tax_included' => $json_data[$i]['is_tax_included'],
                                            'status' => $json_data[$i]['status'],
                                        );
                                        $products_model->insert($products);

                                        $product_id = $products_model->getInsertID();

                                        for ($j = 0; $j < count($json_data[$i]['variants']); $j++) {

                                            if (isset($json_data[$i]['stock_management']) && !empty($json_data[$i]['stock_management']) && $json_data[$i]['stock_management'] == 1) {

                                                $variants['stock'] = '0';
                                                $variants['qty_alert'] = '0';
                                                $variants['unit_id'] = '0';
                                            } else {
                                                $variants['qty_alert'] = isset($json_data[$i]['variants'][$j]['qty_alert']) && !empty($json_data[$i]['variants'][$j]['qty_alert']) ? $json_data[$i]['variants'][$j]['qty_alert'] : '0';
                                                $variants['stock'] = $json_data[$i]['variants'][$j]['stock'];
                                                $variants['unit_id'] = $json_data[$i]['variants'][$j]['unit_id'];
                                            }

                                            $variants['product_id'] = $product_id;
                                            $variants['variant_name'] = $json_data[$i]['variants'][$j]['variant_name'];
                                            $variants['sale_price'] = $json_data[$i]['variants'][$j]['sale_price'];
                                            $variants['purchase_price'] = $json_data[$i]['variants'][$j]['purchase_price'];
                                            $variants['barcode'] = isset($json_data[$i]['variants'][$j]['barcode']) && !empty($json_data[$i]['variants'][$j]['barcode']) ? $json_data[$i]['variants'][$j]['barcode'] : null;

                                            $products_variants_model->insert($variants);
                                            $products_variants_id = $products_variants_model->getInsertID();

                                            if (isset($json_data[$i]['variants'][$j]['warehouses']) && !empty($json_data[$i]['variants'][$j]['warehouses'])) {

                                                // Loop through each warehouse related to this variant

                                                for ($k = 0; $k < count($json_data[$i]['variants'][$j]['warehouses']); $k++) {


                                                    // Prepare the data for storage
                                                    $data = [
                                                        'warehouse_id' => $json_data[$i]['variants'][$j]['warehouses'][$k]['warehouse_id'],
                                                        'product_variant_id' => $products_variants_id,  // Correct variant ID
                                                        'stock' => $json_data[$i]['variants'][$j]['warehouses'][$k]['stock'],
                                                        'qty_alert' => $json_data[$i]['variants'][$j]['warehouses'][$k]['qty_alert'],
                                                        'vendor_id' => $vendor_id,
                                                        'business_id' => $json_data[$i]['business_id'],

                                                    ];

                                                    $warehouse_product_stock_model->insert($data);
                                                }
                                            }
                                        }

                                    }

                                    $response['error'] = false;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['message'] = 'Products uploaded successfully!';

                                    return $this->response->setJSON($response);
                                } else {
                                    $required_fields = [
                                        'product_id',
                                        'name',
                                        'category_id',
                                        'business_id',
                                        'description',
                                        'image',
                                        'product_type',
                                        'stock_management',
                                        'variants',
                                    ];
                                    for ($i = 0; $i < count($json_data); $i++) {

                                        $row = $json_data[$i];
                                        $missing_fields = [];

                                        // Check for missing required fields
                                        foreach ($required_fields as $field) {
                                            if (!isset($row[$field]) || empty($row[$field])) {
                                                $missing_fields[] = $field;
                                            }
                                        }

                                        if (isset($row['product_type']) && !empty($row['product_type']) && $row['product_type'] == "simple") {

                                            if (!isset($row['variants'][0]['variant_id']) || empty($row['variants'][0]['variant_id'])) {
                                                $missing_fields[] = 'variant_id';
                                            }
                                            if (!isset($row['variants'][0]['variant_name']) || empty($row['variants'][0]['variant_name'])) {
                                                $missing_fields[] = 'variant_name';
                                            }
                                            if (!isset($row['variants'][0]['sale_price']) || empty($row['variants'][0]['sale_price'])) {
                                                $missing_fields[] = 'sale_price';
                                            }
                                            if (!isset($row['variants'][0]['purchase_price']) || empty($row['variants'][0]['purchase_price'])) {
                                                $missing_fields[] = 'purchase_price';
                                            }
                                            // if (!isset($row['variants'][0]['unit_id']) || empty($row['variants'][0]['unit_id'])) {
                                            //     $missing_fields[] = 'unit_id';
                                            // }
                                            // if (!isset($row['variants'][0]['stock']) || empty($row['variants'][0]['stock'])) {
                                            //     $missing_fields[] = 'stock';
                                            // }
                                            // if (!isset($row['variants'][0]['qty_alert']) || empty($row['variants'][0]['qty_alert'])) {
                                            //     $missing_fields[] = 'qty_alert';
                                            // }
                                        } else {
                                            for ($k = 0; $k < count($row['variants']); $k++) {

                                                if (!isset($row['variants'][$k]['variant_id']) || empty($row['variants'][$k]['variant_id'])) {
                                                    $missing_fields[] = 'variant_id';
                                                }
                                                if (!isset($row['variants'][$k]['variant_name']) || empty($row['variants'][$k]['variant_name'])) {
                                                    $missing_fields[] = 'variant_name';
                                                }
                                                if (!isset($row['variants'][$k]['sale_price']) || empty($row['variants'][$k]['sale_price'])) {
                                                    $missing_fields[] = 'sale_price';
                                                }
                                                if (!isset($row['variants'][$k]['purchase_price']) || empty($row['variants'][$k]['purchase_price'])) {
                                                    $missing_fields[] = 'purchase_price';
                                                }
                                            }
                                        }
                                        if (!empty($missing_fields)) {
                                            $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                            continue;
                                        }
                                    }
                                    // If there are errors, return them
                                    if (!empty($errors)) {
                                        $response['error'] = true;
                                        $response['message'] = $errors;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }

                                    for ($i = 0; $i < count($json_data); $i++) {

                                        $product_stock = 0;
                                        $product_qty_alert = 0;
                                        $product_unit_id = 0;

                                        if (isset($json_data[$i]['stock_management']) && !empty($json_data[$i]['stock_management']) && $json_data[$i]['stock_management'] == 1) {
                                            $product_stock = $json_data[$i]['product_stock'];
                                            $product_qty_alert = $json_data[$i]['product_qty_alert'];
                                            $product_unit_id = $json_data[$i]['product_unit_id'];
                                        }

                                        $products = array(
                                            'vendor_id' => $vendor_id,
                                            'business_id' => $json_data[$i]['business_id'],
                                            'category_id' => $json_data[$i]['category_id'],
                                            'tax_ids' => $json_data[$i]['tax_ids'],
                                            'name' => $json_data[$i]['name'],
                                            'description' => $json_data[$i]['description'],
                                            'image' => $json_data[$i]['image'],
                                            'type' => $json_data[$i]['product_type'],
                                            'stock_management' => $json_data[$i]['stock_management'],
                                            'stock' => $product_stock,
                                            'qty_alert' => $product_qty_alert,
                                            'unit_id' => $product_unit_id,
                                            'brand_id' => $json_data[$i]['brand_id'],
                                            'is_tax_included' => $json_data[$i]['is_tax_included'],
                                            'status' => $json_data[$i]['status'],
                                        );
                                        $products_model->update($json_data[$i]['product_id'], $products);

                                        $product_id = $json_data[$i]['product_id'];

                                        for ($j = 0; $j < count($json_data[$i]['variants']); $j++) {

                                            if (isset($json_data[$i]['stock_management']) && !empty($json_data[$i]['stock_management']) && $json_data[$i]['stock_management'] == 1) {

                                                $variants['stock'] = '0';
                                                $variants['qty_alert'] = '0';
                                                $variants['unit_id'] = '0';
                                            } else {
                                                $variants['qty_alert'] = isset($json_data[$i]['variants'][$j]['qty_alert']) && !empty($json_data[$i]['variants'][$j]['qty_alert']) ? $json_data[$i]['variants'][$j]['qty_alert'] : '0';
                                                $variants['stock'] = $json_data[$i]['variants'][$j]['stock'];
                                                $variants['unit_id'] = $json_data[$i]['variants'][$j]['unit_id'];
                                            }

                                            $variants['product_id'] = $product_id;
                                            $variants['variant_name'] = $json_data[$i]['variants'][$j]['variant_name'];
                                            $variants['sale_price'] = $json_data[$i]['variants'][$j]['sale_price'];
                                            $variants['purchase_price'] = $json_data[$i]['variants'][$j]['purchase_price'];
                                            $variants['barcode'] = isset($json_data[$i]['variants'][$j]['barcode']) && !empty($json_data[$i]['variants'][$j]['barcode']) ? $json_data[$i]['variants'][$j]['barcode'] : null;

                                            $products_variants_model->update($json_data[$i]['variants'][$j]['variant_id'], $variants);

                                            $products_variants_id = $json_data[$i]['variants'][$j]['variant_id'];

                                            if (isset($json_data[$i]['variants'][$j]['warehouses']) && !empty($json_data[$i]['variants'][$j]['warehouses'])) {

                                                // Loop through each warehouse related to this variant

                                                for ($k = 0; $k < count($json_data[$i]['variants'][$j]['warehouses']); $k++) {


                                                    // Prepare the data for storage
                                                    $data = [
                                                        'warehouse_id' => $json_data[$i]['variants'][$j]['warehouses'][$k]['warehouse_id'],
                                                        'product_variant_id' => $products_variants_id,  // Correct variant ID
                                                        'stock' => $json_data[$i]['variants'][$j]['warehouses'][$k]['stock'],
                                                        'qty_alert' => $json_data[$i]['variants'][$j]['warehouses'][$k]['qty_alert'],
                                                        'vendor_id' => $vendor_id,
                                                        'business_id' => $json_data[$i]['business_id'],

                                                    ];

                                                    update_details($data, ['warehouse_id' => $json_data[$i]['variants'][$j]['warehouses'][$k]['warehouse_id'], 'product_variant_id' => $products_variants_id], 'warehouse_product_stock');

                                                    // $warehouse_product_stock_model->save($data);
                                                }
                                            }
                                        }

                                    }

                                    $response['error'] = false;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['message'] = 'Products updated successfully!';

                                    return $this->response->setJSON($response);
                                }

                            }
                        }
                    } else {
                        $response = [
                            'error' => true,
                            'message' => ['You have Exceeded limit of adding products'],
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                } else {
                    return false;
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
    public function import_categories()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    $category_model = new Categories_model();
                    $this->validation->setRules([
                        'type' => 'required',
                    ]);
                    if (empty($_FILES['file']['name'])) {
                        $this->validation->setRules([
                            'file' => 'required',
                        ]);
                    }
                    if (!$this->validation->withRequest($this->request)->run()) {

                        $errors = $this->validation->getErrors();
                        $response['error'] = true;
                        foreach ($errors as $e) {
                            $response['message'] = $e;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['data'] = [];
                        }
                        return $this->response->setJSON($response);
                    } else {
                        // Process uploaded file and handle supplier data
                        $file = $this->request->getFile('file');
                        $allowed_mime_type_arr = array(
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'application/x-csv',
                            'text/x-csv',
                            'text/csv',
                            'application/csv',
                            'application/vnd.ms-excel', // For older Excel files (.xls)
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                            'text/plain' // Allow .csv files that are identified as text/plain
                        );
                        $mime = $file->getMimeType();

                        // Validate MIME type
                        if (!in_array($mime, $allowed_mime_type_arr)) {
                            $response['error'] = true;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Invalid file format!';
                            return $this->response->setJSON($response);
                        }

                        $type = $_POST['type'];
                        $file_path = $_FILES['file']['tmp_name'];

                        // Convert CSV to JSON
                        $json_data = csvToJson($file_path, $type);
                        if (!$json_data) {
                            $this->response['error'] = true;
                            $this->response['message'] = 'Error converting CSV to JSON!';
                            print_r(json_encode($this->response));
                            return false;
                        }

                        if ($this->ionAuth->isTeamMember()) {
                            $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                        } else {
                            $vendor_id = $_SESSION['user_id'];
                        }
                        if ($type == 'upload') {

                            $required_fields = [
                                'name',
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if (!isset($row[$field]) || empty($row[$field])) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }

                            for ($i = 0; $i < count($json_data); $i++) {

                                $data['parent_id'] = isset($json_data[$i]['parent_id']) && !empty($json_data[$i]['parent_id']) ? $json_data[$i]['parent_id'] : 0;
                                $data['vendor_id'] = $vendor_id;
                                $data['name'] = $json_data[$i]['name'];
                                $data['status'] = isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : 0;

                                $category_model->insert(row: (object) $data);
                            }

                            $response['error'] = false;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Categories uploaded successfully!';

                            return $this->response->setJSON($response);
                        } else {
                            $required_fields = [
                                'id',
                                'name',
                                'status',
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];

                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            for ($i = 0; $i < count($json_data); $i++) {
                                $category_id = $json_data[$i]['id'];
                                $category = fetch_details('categories', ['id' => $category_id]);
                                $data['parent_id'] = isset($json_data[$i]['parent_id']) && !empty($json_data[$i]['parent_id']) ? $json_data[$i]['parent_id'] : $category[0]['parent_id'];
                                $data['vendor_id'] = $vendor_id;
                                $data['name'] = isset($json_data[$i]['name']) && !empty($json_data[$i]['name']) ? $json_data[$i]['name'] : $category[0]['name'];
                                $data['status'] = isset($json_data[$i]['status']) ? $json_data[$i]['status'] : $category[0]['status'];
                                $data['id'] = $category_id;


                                $category_model->update($category_id, $data);
                            }


                            $response['error'] = false;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Categories updated successfully!';
                            return $this->response->setJSON($response);
                        }
                    }
                } else {
                    return redirect()->to('vendor/categories');
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    public function import_stock()
    {
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {

                if (isset($_POST) && !empty($_POST)) {
                    $this->validation->setRules([
                        'business_id' => 'required',
                    ]);
                    if (empty($_FILES['file']['name'])) {
                        $this->validation->setRules([
                            'file' => 'required',
                        ]);
                    }
                    if (!$this->validation->withRequest($this->request)->run()) {
                        $errors = $this->validation->getErrors();
                        $response['error'] = true;
                        foreach ($errors as $e) {
                            $response['message'] = $e;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['data'] = [];
                        }
                        return $this->response->setJSON($response);
                    } else {
                        // Process uploaded file and handle supplier data
                        $file = $this->request->getFile('file');
                        $allowed_mime_type_arr = array(
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'application/x-csv',
                            'text/x-csv',
                            'text/csv',
                            'application/csv',
                            'application/vnd.ms-excel', // For older Excel files (.xls)
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                            'text/plain' // Allow .csv files that are identified as text/plain
                        );
                        $mime = $file->getMimeType();

                        // Validate MIME type
                        if (!in_array($mime, $allowed_mime_type_arr)) {
                            $response['error'] = true;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Invalid file format!';
                            return $this->response->setJSON($response);
                        }

                        $type = $_POST['type'];
                        $file_path = $_FILES['file']['tmp_name'];

                        // Convert CSV to JSON
                        $json_data = csvToJson($file_path, $type);
                        if (!$json_data) {
                            $this->response['error'] = true;
                            $this->response['message'] = 'Error converting CSV to JSON!';
                            print_r(json_encode($this->response));
                            return false;
                        }

                        if ($type == 'update') {

                            $required_fields = [
                                'variant_id',
                                'stock_management',
                                'warehouse_id',
                                'current_stock',
                                'quantity',
                                'type',
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if (!isset($row[$field]) || empty($row[$field])) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);

                            }

                            $warehouse_product_stock = new WarehouseProductStockModel();
                            for ($i = 0; $i < count($json_data); $i++) {
                                // produc is post is a product id   
                                $stock = 0;
                                $variant_id = $json_data[$i]['variant_id']; //if stock manaement is 1 then this is product id OR if stock management is 2 then this is product variant id
                                $quantity = $json_data[$i]['quantity'];
                                $warehouse_id = $json_data[$i]['warehouse_id'];

                                $product_id = fetch_details('products_variants', ['id' => $variant_id], 'product_id')[0]['product_id'];


                                // check if the selected products_variants are in selected warehouse or not.

                                // $warehouse_product_list = $warehouse_product_stock->where([
                                //     'warehouse_id' => $warehouse_id,
                                //     'product_variant_id' => $variant_id
                                // ])->get()->getResultArray();

                                // if (empty($warehouse_product_list)) {
                                //     $response = [
                                //         'error' => true,
                                //         'message' => ["Product is not available in selected warehouse !"],
                                //     ];
                                //     $response['csrf_token'] = csrf_token();
                                //     $response['csrf_hash'] = csrf_hash();
                                //     return $this->response->setJSON($response);
                                // }

                                if ($json_data[$i]['type'] == 'add') {
                                    $stock = floatval($json_data[$i]['current_stock']) + floatval($quantity);
                                    updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $variant_id, warehouse_stock: $quantity, type: 1);
                                }
                                if ($json_data[$i]['type'] == 'subtract') {
                                    $current_stock = floatval($json_data[$i]['current_stock']);
                                    $current_quantity = floatval($quantity);
                                    if ($current_stock < $current_quantity) {
                                        $response = [
                                            'error' => true,
                                            'message' => ['name' => "Quantity must be less than Current Stock fo Subtraction  !"],

                                        ];
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    $stock = floatval($json_data[$i]['current_stock']) - floatval($quantity);
                                    updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $variant_id, warehouse_stock: $quantity, type: 0);
                                }
                                if ($json_data[$i]['stock_management'] == '1') {
                                    update_details(['stock' => (string) $stock], ['id' => $product_id], 'products');
                                }
                                if ($json_data[$i]['stock_management'] == '2') {
                                    update_details(['stock' => (string) $stock], ['id' => $variant_id], 'products_variants');
                                }
                            }
                        }

                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Stock updated successfully!';
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->to('vendor/products/manage_stock');
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    public function import_orders()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            $status = subscription();

            if ($status == 'active') {

                if (isset($_POST) && !empty($_POST)) {
                    $vendors_model = new Vendors_model();
                    $order_model = new Orders_model();
                    $order_items_model = new Orders_items_model();
                    $order_service_model = new Orders_services_model();

                    $this->validation->setRules([
                        'type' => 'required',
                        // Add more validation rules as needed for order data
                    ]);

                    if (empty($_FILES['file']['name'])) {
                        $this->validation->setRules([
                            'file' => 'required',
                        ]);
                    }

                    if (!$this->validation->withRequest($this->request)->run()) {
                        $errors = $this->validation->getErrors();
                        $response['error'] = true;
                        foreach ($errors as $e) {
                            $response['message'][] = $e;
                        }
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                        return $this->response->setJSON($response);
                    } else {
                        // Process uploaded file and handle orders
                        $file = $this->request->getFile('file');
                        $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'text/plain');
                        $mime = $file->getMimeType();

                        if (!in_array($mime, $allowed_mime_type_arr)) {
                            $response['error'] = true;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Invalid file format!';
                            return $this->response->setJSON($response);
                        }

                        $csv = $_FILES['file']['tmp_name'];
                        $handle = fopen($csv, "r");
                        $response['message'] = '';
                        $type = $_POST['type'];
                        $header = fgetcsv($handle, 10000, ",");

                        $vendor_id = $_SESSION['user_id'];
                        if ($this->ionAuth->isTeamMember()) {
                            $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                        } else {
                            $vendor_id = $_SESSION['user_id'];
                        }

                        if ($type == 'upload') {
                            $rowCount = 1;
                            $lastOrderNo = null;
                            $Tax_model = new Tax_model();
                            // Inside the while loop where orders are processed                   
                            while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {

                                $orderNo = $row[2];

                                // Fetch the logged-in user's data
                                $user_data = $this->ionAuth->user()->row();

                                // Assuming Vendors_model has a method to fetch vendor data by user ID
                                $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                                // Extract the vendor_id from the fetched data
                                if ($vendor_data) {
                                    $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                                }



                                /* expected data from file
                                    Array
                                    (
                                        $row[0] => vendor_id
                                        $row[1] => customer_id
                                        $row[2] => order_no
                                        $row[3] => business_id
                                        $row[4] => created_by
                                        $row[5] => total
                                        $row[6] => delivery_charges
                                        $row[7] => discount
                                        $row[8] => final_total
                                        $row[9] => payment_status
                                        $row[10] => amount_paid
                                        $row[11] => order_type
                                        $row[12] => message
                                        $row[13] => payment_method
                                        $row[14] => transaction_id
                                        $row[15] => created_at
                                        $row[16] => updated_at
                                        $row[17] => product_id
                                        $row[18] => product_variant_id
                                        $row[19] => product_name
                                        $row[20] => quantity
                                        $row[21] => price
                                        $row[22] => tax_name
                                        $row[23] => tax_percentage
                                        $row[24] => is_tax_included
                                        $row[25] => tax_details
                                        $row[26] => sub_total
                                        $row[27] => status
                                        $row[28] => delivery_boy
                                        $row[29] => service_id
                                        $row[30] => service_name
                                        $row[31] => price
                                        $row[32] => quantity
                                        $row[33] => unit_name
                                        $row[34] => unit_id
                                        $row[35] => sub_total
                                        $row[36] => tax_name
                                        $row[37] => tax_percentage
                                        $row[38] => is_tax_included
                                        $row[39] => tax_details
                                        $row[40] => is_recursive
                                        $row[41] => recurring_days
                                        $row[42] => starts_on
                                        $row[43] => ends_on
                                        $row[44] => delivery_boy
                                        $row[45] => status
                                    )
                                */

                                // Insert orders into database
                                $order_data = [
                                    'vendor_id' => $vendor_id,
                                    'customer_id' => $row[1],
                                    'order_no' => $row[2],
                                    'business_id' => $row[3],
                                    'created_by' => $row[4],
                                    'total' => $row[5],
                                    'delivery_charges' => $row[6],
                                    'discount' => $row[7],
                                    'final_total' => $row[8],
                                    'payment_status' => $row[9],
                                    'amount_paid' => $row[10],
                                    'order_type' => $row[11],
                                    'message' => $row[12],
                                    'payment_method' => $row[13],
                                ];

                                if (empty($order_data['order_no'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Order no is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($order_data['customer_id'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Customer id is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($order_data['business_id'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Business id is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($order_data['created_by'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Created By is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($order_data['total'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Total  is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if ($order_data['delivery_charges'] != 0) {
                                    if (empty($order_data['delivery_charges'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Delivery Charges  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }
                                if ($order_data['discount'] != 0) {
                                    if (empty($order_data['discount'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Discount  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                } else {
                                    if ($order_data['total'] < $order_data['discount']) {

                                        $response['error'] = true;
                                        $response['message'] = 'Discount cannot be greater  Total at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }

                                if (empty($order_data['final_total'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Final Total  is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                } else {
                                    if ($order_data['final_total'] < $order_data['discount']) {

                                        $response['error'] = true;
                                        $response['message'] = 'Discount cannot be greater Final total at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }

                                if (empty($order_data['payment_status'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Payment Status is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }

                                if ($order_data['amount_paid'] != 0) {
                                    if (empty($order_data['amount_paid'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Amount paid is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }

                                if (empty($order_data['order_type'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Order type is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                } else {
                                    if (trim($order_data['order_type']) != 'product' && trim($order_data['order_type']) != 'service') {
                                        $response['error'] = true;
                                        $response['message'] = 'Invalid value in Order type at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }

                                if ($order_data['amount_paid'] != 0) {
                                    if (!empty($order_data['amount_paid'])) {
                                        if (empty($order_data['payment_method'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Payment Method is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                    }
                                }


                                try {
                                    $existingOrder = $order_model->where('order_no', $orderNo)->first();
                                    if (!$existingOrder) {

                                        $order_model->insert($order_data);
                                        $orderId = $order_model->getInsertID();
                                    } else {
                                        $orderId = $existingOrder['id'];
                                    }

                                    $lastOrderNo = $orderNo; // Track the last inserted order_no

                                    // Check order type
                                    if (trim($order_data['order_type']) === 'service') {
                                        $delivery_boy = isset($row[44]) ? $row[44] : null;
                                        // Insert order service
                                        $order_service_data = [
                                            'order_id' => $orderId,
                                            'service_id' => $row[29],
                                            'service_name' => $row[30],
                                            'price' => $row[31],
                                            'quantity' => $row[32],
                                            'unit_name' => $row[33],
                                            'unit_id' => $row[34],
                                            'sub_total' => $row[35],
                                            'tax_name' => $row[36],
                                            'tax_percentage' => $row[37],
                                            'is_tax_included' => $row[38],
                                            'is_recursive' => $row[40],
                                            'recurring_days' => $row[41],
                                            'tax_details' => $row[39],
                                            'starts_on' => $row[42],
                                            'ends_on' => $row[43],
                                            'delivery_boy' => $delivery_boy,
                                            'status' => $row[45]
                                            // Add more fields to $order_service_data array as needed
                                        ];

                                        if ($order_service_data['is_tax_included'] == 0) {
                                            if (empty($order_service_data['tax_details'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'tax_details is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            } {
                                                $taxString = $order_service_data['tax_details'];
                                                if (!$this->isValidTaxString($taxString)) {
                                                    // The tax string is invalid;

                                                    $response['error'] = true;
                                                    $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                    $response['csrf_token'] = csrf_token();
                                                    $response['csrf_hash'] = csrf_hash();
                                                    return $this->response->setJSON($response);
                                                } else {
                                                    $tax_ids = json_decode($order_service_data['tax_details']);
                                                    $tax_details = [];
                                                    foreach ($tax_ids as $tax_id) {
                                                        $tax = $Tax_model->find($tax_id);
                                                        if (!empty($tax)) {
                                                            $tax_details[] = [
                                                                'tax_id' => $tax_id,
                                                                'name' => $tax['name'],
                                                                'percentage' => $tax['percentage']
                                                            ];
                                                        } else {
                                                            $response['error'] = true;
                                                            $response['message'] = 'There is no such tax with id ' . $tax_id . ' at row ' . $rowCount;
                                                            $response['csrf_token'] = csrf_token();
                                                            $response['csrf_hash'] = csrf_hash();
                                                            return $this->response->setJSON($response);
                                                        }
                                                    }
                                                    $tax_details = empty($tax_details) ? "[]" : json_encode($tax_details);

                                                    $order_service_data['tax_details'] = $tax_details;
                                                }
                                            }
                                        }



                                        if (empty($order_service_data['service_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Service id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['service_name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Service Name is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['price'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Price id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['quantity'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Quantity id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['unit_name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Unit name  is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['unit_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Unit id  is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['sub_total'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Sub total is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if ($order_service_data['is_tax_included'] == 0) {
                                            if (empty($order_service_data['tax_name'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Tax name is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                            if (empty($order_service_data['tax_percentage'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Tax percentage is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }
                                        if (empty($order_service_data['starts_on'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Starts on is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['ends_on'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Ends on is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['status'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Status is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        $order_service_model->insert($order_service_data);
                                    } elseif (trim($order_data['order_type']) === 'product') {
                                        $delivery_boy = isset($row[28]) ? $row[28] : null;
                                        // Insert order item
                                        $order_item_data = [
                                            'order_id' => $orderId,
                                            'product_id' => $row[17],
                                            'product_variant_id' => $row[18],
                                            'product_name' => $row[19],
                                            'quantity' => $row[20],
                                            'price' => $row[21],
                                            'tax_name' => $row[22],
                                            'tax_percentage' => $row[23],
                                            'is_tax_included' => $row[24],
                                            'tax_details' => $row[25],
                                            'sub_total' => $row[26],
                                            'status' => $row[27],
                                            'delivery_boy' => $delivery_boy
                                            // Add more fields to $order_item_data array as needed
                                        ];



                                        if ($order_item_data['is_tax_included'] == 0) {
                                            if (empty($order_item_data['tax_details'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'tax_details is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            } {
                                                $taxString = $order_item_data['tax_details'];
                                                if (!$this->isValidTaxString($taxString)) {
                                                    // The tax string is invalid;

                                                    $response['error'] = true;
                                                    $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                    $response['csrf_token'] = csrf_token();
                                                    $response['csrf_hash'] = csrf_hash();
                                                    return $this->response->setJSON($response);
                                                } else {
                                                    $tax_ids = json_decode($order_item_data['tax_details']);
                                                    $tax_details = [];
                                                    foreach ($tax_ids as $tax_id) {
                                                        $tax = $Tax_model->find($tax_id);
                                                        if (!empty($tax)) {
                                                            $tax_details[] = [
                                                                'tax_id' => $tax_id,
                                                                'name' => $tax['name'],
                                                                'percentage' => $tax['percentage']
                                                            ];
                                                        } else {
                                                            $response['error'] = true;
                                                            $response['message'] = 'There is no such tax with id ' . $tax_id . ' at row ' . $rowCount;
                                                            $response['csrf_token'] = csrf_token();
                                                            $response['csrf_hash'] = csrf_hash();
                                                            return $this->response->setJSON($response);
                                                        }
                                                    }
                                                    $tax_details = empty($tax_details) ? "[]" : json_encode($tax_details);

                                                    $order_item_data['tax_details'] = $tax_details;
                                                }
                                            }
                                        }

                                        if (empty($order_item_data['product_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Product id  is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_item_data['product_variant_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Product variant id  is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_item_data['product_name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Product name is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_item_data['quantity'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Quantity is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_item_data['price'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Price is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_item_data['sub_total'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Sub total is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        $order_items_model->insert($order_item_data);
                                    } else {
                                        // Handle other order types or invalid types
                                    }
                                } catch (\Exception $e) {
                                    // Log or display the error message
                                    $response['error'] = true;
                                    $response['message'] = 'Error: ' . $e->getMessage();
                                    return $this->response->setJSON($response);
                                }
                                $rowCount++;
                            }


                            fclose($handle);
                            $response['error'] = false;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Orders uploaded successfully!';

                            return $this->response->setJSON($response);
                        } else {
                            // Update operation
                            // Similar logic as upload, but updating existing orders
                        }
                    }
                } else {
                    // Redirect to order page if no POST data is present
                    return redirect()->to('vendor/orders');
                }
            }

            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }

            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    public function import_purchase()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                } else {
                    $vendor_id = $_SESSION['user_id'];
                }
                $suppliers_model = new Suppliers_model();
                $subscription = check_subscription($vendor_id);
                if ($subscription) {
                    if (isset($_POST) && !empty($_POST)) {


                        $this->validation->setRules([
                            'type' => 'required',
                        ]);

                        if (empty($_FILES['file']['name'])) {
                            $this->validation->setRules([
                                'file' => 'required',
                            ]);
                        }

                        if (!$this->validation->withRequest($this->request)->run()) {
                            $errors = $this->validation->getErrors();
                            $response['error'] = true;
                            foreach ($errors as $e) {
                                $response['message'][] = $e;
                            }
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['data'] = [];
                            return $this->response->setJSON($response);
                        } else {
                            // Process uploaded file and handle purchase data
                            $file = $this->request->getFile('file');
                            $allowed_mime_type_arr = array(
                                'text/x-comma-separated-values',
                                'text/comma-separated-values',
                                'application/x-csv',
                                'text/x-csv',
                                'text/csv',
                                'application/csv',
                                'application/vnd.ms-excel', // For older Excel files (.xls)
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                                'text/plain' // Allow .csv files that are identified as text/plain
                            );
                            $mime = $file->getMimeType();

                            // Validate MIME type
                            if (!in_array($mime, $allowed_mime_type_arr)) {
                                $response['error'] = true;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Invalid file format!';
                                return $this->response->setJSON($response);
                            }

                            $type = $_POST['type'];
                            $file_path = $_FILES['file']['tmp_name'];

                            // Convert CSV to JSON
                            $json_data = csvToJson($file_path, $type);
                            if (!$json_data) {
                                $this->response['error'] = true;
                                $this->response['message'] = 'Error converting CSV to JSON!';
                                print_r(json_encode($this->response));
                                return false;
                            }

                            // Instantiate Purchases model
                            $purchases_model = new Purchases_model();
                            $purchase_items_model = new Purchases_items_model();
                            $warehouse_product_stock_model = new WarehouseProductStockModel();
                            $vendor_transaction_model = new Vendor_purchase_transactions_model();

                            if ($type === 'upload') {

                                $required_fields = [
                                    'purchase_date',
                                    'supplier_id',
                                    'warehouse_id',
                                    'product_variant_id',
                                    'purchases_item_quantity',
                                    'purchases_item_price',
                                    'payment_status',
                                    'payment_method',
                                    'total_of_deal',
                                    'amount_paid',
                                    'order_type',
                                    'order_no',
                                    'business_id',
                                    'status'
                                ];
                                $format = 'Y-m-d';
                                for ($i = 0; $i < count($json_data); $i++) {

                                    $row = $json_data[$i];
                                    $missing_fields = [];

                                    // Check for missing required fields
                                    foreach ($required_fields as $field) {
                                        if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                            $missing_fields[] = $field;
                                        }
                                    }

                                    if (!empty($missing_fields)) {
                                        $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                        continue;
                                    }

                                    //check if tax is in correct formate or not
                                    $taxString = $json_data[$i]['tax_ids'];
                                    if (!$this->isValidTaxString($taxString)) {
                                        // The tax string is invalid;
                                        $errors[] = 'tax_ids is not in correct format at rocord ' . ($i + 1);
                                        continue;
                                    }

                                    //check if supplier is exist in system or not
                                    $supplier = fetch_details('suppliers', ['user_id' => $json_data[$i]['supplier_id']]);
                                    if (empty($supplier)) {
                                        $errors[] = 'Supplier not found at at rocord ' . ($i + 1);
                                        continue;
                                    }

                                    //check if payment status is valid or not
                                    $payment_status = ['fully_paid', 'partially_paid', 'unpaid', 'cancelled'];
                                    if (!in_array($json_data[$i]['payment_status'], $payment_status)) {
                                        $errors[] = "Payment status at record " . ($i + 1) . " is invalid. Use one of: fully_paid, partially_paid, unpaid, or cancelled.";
                                        continue;
                                    }

                                    //check if order type is valid or not
                                    $order_type = ['order', 'return'];
                                    if (!in_array($json_data[$i]['order_type'], $order_type)) {
                                        $errors[] = "Order Type at record " . ($i + 1) . " is invalid. Use one of: order or return.";
                                        continue;
                                    }

                                    //check if order total is valid or not
                                    if (empty($json_data[$i]['total_of_deal']) || $json_data[$i]['total_of_deal'] < 0) {
                                        $errors[] = "Invalid Order Total at record " . ($i + 1);
                                        continue;
                                    }

                                    //check if order amount paid is valid or not
                                    if (empty($json_data[$i]['amount_paid']) || $json_data[$i]['amount_paid'] < 0) {
                                        $errors[] = "Invalid Amount Paid at record " . ($i + 1);
                                        continue;
                                    }
                                }
                                // If there are errors, return them
                                if (!empty($errors)) {
                                    $response['error'] = true;
                                    $response['message'] = $errors;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);

                                }
                                for ($i = 0; $i < count($json_data); $i++) {

                                    $originalDate = $json_data[$i]['purchase_date'];
                                    $dateObject = new \DateTime($originalDate);
                                    $convertedDate = $dateObject->format('Y-m-d'); // Result: '2025-01-23'

                                    $purchase_data = [
                                        'business_id' => $json_data[$i]['business_id'],
                                        'vendor_id' => $vendor_id,
                                        'supplier_id' => $json_data[$i]['supplier_id'],
                                        'warehouse_id' => $json_data[$i]['warehouse_id'],
                                        'order_no' => $json_data[$i]['order_no'],
                                        'purchase_date' => $convertedDate,
                                        'tax_ids' => $json_data[$i]['tax_ids'],
                                        'status' => isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : 0,
                                        'delivery_charges' => isset($json_data[$i]['delivery_charges']) && !empty($json_data[$i]['delivery_charges']) ? $json_data[$i]['delivery_charges'] : 0,
                                        'order_type' => $json_data[$i]['order_type'],
                                        'total' => $json_data[$i]['total_of_deal'],
                                        'payment_method' => $json_data[$i]['payment_method'],
                                        'payment_status' => $json_data[$i]['payment_status'],
                                        'amount_paid' => $json_data[$i]['amount_paid'],
                                        'message' => $json_data[$i]['message'],
                                        'discount' => isset($json_data[$i]['order_discount']) && !empty($json_data[$i]['order_discount']) ? $json_data[$i]['order_discount'] : 0,
                                    ];

                                    $purchases_model->insert($purchase_data);
                                    $purchaseId = $purchases_model->getInsertID();

                                    for ($j = 0; $j < count($json_data[$i]['product_variant_id']); $j++) {


                                        $purchase_item_data = (object) [
                                            'purchase_id' => $purchaseId,
                                            'product_variant_id' => $json_data[$i]['product_variant_id'][$j],
                                            'quantity' => $json_data[$i]['purchases_item_quantity'][$j],
                                            'price' => $json_data[$i]['purchases_item_price'][$j],
                                            'discount' => isset($json_data[$i]['discount_on_item'][$j]) && !empty($json_data[$i]['discount_on_item'][$j]) ? $json_data[$i]['discount_on_item'][$j] : 0,
                                            'status' => isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : 0,
                                        ];

                                        $purchase_items_model->insert($purchase_item_data);

                                        // Additional processing if needed

                                        if ($json_data[$i]['order_type'] == "order") {
                                            update_stock(product_variant_ids: $json_data[$i]['product_variant_id'][$j], qtns: $json_data[$i]['purchases_item_quantity'][$j], type: 'plus');
                                            if (is_exist(['product_variant_id' => (int) $json_data[$i]['product_variant_id'][$j], 'warehouse_id' => (int) $json_data[$i]['warehouse_id']], ' warehouse_product_stock')) {
                                                updateWarehouseStocks(warehouse_id: $json_data[$i]['warehouse_id'], product_variant_id: $json_data[$i]['product_variant_id'][$j], warehouse_stock: $json_data[$i]['purchases_item_quantity'][$j], type: 1);
                                            } else {
                                                $warehouse_data = [
                                                    'warehouse_id' => $json_data[$i]['warehouse_id'],
                                                    'product_variant_id' => $json_data[$i]['product_variant_id'][$j],  // Correct variant ID
                                                    'stock' => $json_data[$i]['purchases_item_quantity'][$j],
                                                    'qty_alert' => 0,
                                                    'vendor_id' => $vendor_id,
                                                    'business_id' => $json_data[$i]['business_id'],
                                                ];
                                                $warehouse_product_stock_model->save($warehouse_data);
                                            }
                                        } elseif ($json_data[$i]['order_type'] == "return") {
                                            update_stock(product_variant_ids: $json_data[$i]['product_variant_id'][$j], qtns: $json_data[$i]['purchases_item_quantity'][$j]);
                                            updateWarehouseStocks(warehouse_id: $json_data[$i]['warehouse_id'], product_variant_id: $json_data[$i]['product_variant_id'][$j], warehouse_stock: $json_data[$i]['purchases_item_quantity'][$j], type: 0);
                                        }
                                    }

                                    $transaction = array(
                                        'order_id' => $purchaseId,
                                        'supplier_id' => $json_data[$i]['supplier_id'],
                                        'vendor_id' => $vendor_id,
                                        'business_id' => $json_data[$i]['business_id'],
                                        'transaction_type' => "debit",
                                        'order_type' => $json_data[$i]['order_type'],
                                        'created_by' => $vendor_id,
                                        'payment_type' => $json_data[$i]['payment_method'],
                                        'amount' => $json_data[$i]['amount_paid'],
                                        'message' => ''
                                    );

                                    $vendor_transaction_model->save($transaction);
                                }

                                $response['error'] = false;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Purchases uploaded successfully!';
                                return $this->response->setJSON($response);
                            } else {
                                $response['error'] = true;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Invalid type, Please select valid type';
                                return $this->response->setJSON($response);
                            }
                        }
                    } else {
                        return redirect()->to('vendor/purchases');
                    }
                } else {
                    return false;
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    // public function import_suppliers()
    // {


    //     //name , mobile , email , opening balance , creadit period , creadit limit , billing address, shipping address, tax name, tax no , stauts

    //     // Check if the user is logged in and is an admin
    //     if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
    //         return redirect()->to('login');
    //     } else {
    //         // Check if form data is submitted
    //         if (isset($_POST) && !empty($_POST)) {
    //             // Set validation rules for form fields
    //             // Check if file is uploaded

    //             $this->validation->setRules([
    //                 'type' => 'required',
    //             ]);

    //             if (empty($_FILES['file']['name'])) {
    //                 $this->validation->setRules([
    //                     'file' => 'required',
    //                 ]);
    //             }


    //             // Run validation
    //             if (!$this->validation->withRequest($this->request)->run()) {
    //                 // If validation fails, return error response
    //                 $errors = $this->validation->getErrors();
    //                 $response['error'] = true;
    //                 foreach ($errors as $e) {
    //                     $response['message'][] = $e;
    //                 }
    //                 $response['csrf_token'] = csrf_token();
    //                 $response['csrf_hash'] = csrf_hash();
    //                 $response['data'] = [];
    //                 return $this->response->setJSON($response);
    //             } else {



    //                 // Process uploaded file and handle supplier data
    //                 $file = $this->request->getFile('file');
    //                 $allowed_mime_type_arr = array(
    //                     'text/x-comma-separated-values',
    //                     'text/comma-separated-values',
    //                     'application/x-csv',
    //                     'text/x-csv',
    //                     'text/csv',
    //                     'application/csv',
    //                     'application/vnd.ms-excel', // For older Excel files (.xls)
    //                     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
    //                     'text/plain' // Allow .csv files that are identified as text/plain
    //                 );
    //                 $mime = $file->getMimeType();

    //                 // Validate MIME type
    //                 if (!in_array($mime, $allowed_mime_type_arr)) {
    //                     $response['error'] = true;
    //                     $response['csrf_token'] = csrf_token();
    //                     $response['csrf_hash'] = csrf_hash();
    //                     $response['message'] = 'Invalid file format!';
    //                     return $this->response->setJSON($response);
    //                 }

    //                 $csv = $_FILES['file']['tmp_name'];
    //                 $handle = fopen($csv, "r");
    //                 $response['message'] = '';
    //                 $type = $_POST['type'];
    //                 $header = fgetcsv($handle, 10000, ",");

    //                 // Get the vendor ID


    //                 // Instantiate Suppliers model
    //                 $suppliers_model = new Suppliers_model();
    //                 $ionAuthModel = new \IonAuth\Libraries\IonAuth();

    //                 if ($type == 'upload') {
    //                     $vendor_id = $_SESSION['user_id'];
    //                     if ($this->ionAuth->isTeamMember()) {
    //                         $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
    //                     } else {
    //                         $vendor_id = $_SESSION['user_id'];
    //                     }

    //                     $row_number = 1;
    //                     // Inside the while loop where supplier data is processed
    //                     while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
    //                         $row_number++;
    //                         try {

    //                             // Check if 'id' field exists in the CSV file

    //                             $id_index = array_search('id', $header);


    //                             if ($id_index !== false) {

    //                                 // if id column is mentioned in file 
    //                                 /*
    //                                 $row[0] => id
    //                                 $row[1] => name
    //                                 $row[2] => mobile
    //                                 $row[3] => email
    //                                 $row[4] => opening_balance
    //                                 $row[5] => creadit_period
    //                                 $row[6] => creadit_limit
    //                                 $row[7] => billing_address
    //                                 $row[8] => shipping_address
    //                                 $row[9] => tax_name
    //                                 $row[10] => tax_no
    //                                 $row[11] => stauts
    //                                 $row[12] => created_at
    //                                 $row[13] => updated_at
    //                                 */
    //                                 $supplier_id = $row[$id_index];
    //                                 $user_id = $suppliers_model->find($supplier_id);
    //                                 $user_id = $user_id['user_id'];

    //                                 $userData = [
    //                                     "first_name" => $row[1],
    //                                     "mobile" => $row[2],
    //                                     "email" => $row[3],
    //                                 ];

    //                                 $db = \Config\Database::connect();
    //                                 $builder = $db->table('users');
    //                                 $builder->where('id', $user_id)->update($userData);

    //                                 $supplier_data = (object) [
    //                                     'vendor_id' => $vendor_id,
    //                                     'user_id' => $user_id,
    //                                     'balance' => $row[4],
    //                                     'credit_period' => $row[5],
    //                                     'credit_limit' => $row[6],
    //                                     'billing_address' => $row[7],
    //                                     'shipping_address' => $row[8],
    //                                     'tax_name' => $row[9],
    //                                     'tax_num' => $row[10],
    //                                     'status' => $row[11],
    //                                     'created_at' => $row[12],
    //                                     'updated_at' => $row[13],
    //                                 ];


    //                                 // Check if supplier with this ID exists in the database
    //                                 $existing_supplier = $suppliers_model->find($supplier_id);
    //                                 if ($existing_supplier) {
    //                                     // Update existing supplier
    //                                     try {
    //                                         $suppliers_model->update($supplier_id, $supplier_data);
    //                                         // Additional processing if needed
    //                                     } catch (\Exception $e) {
    //                                         // Log or display the error message
    //                                         $response['error'] = true;
    //                                         $response['message'] = 'Error: ' . $e->getMessage();
    //                                         return $this->response->setJSON($response);
    //                                     }

    //                                     continue; // Move to the next iteration, skipping insertion
    //                                 }
    //                             }



    //                             /*
    //                                     $row[0] => name
    //                                     $row[1] => mobile
    //                                     $row[2] => email
    //                                     $row[3] => opening_balance
    //                                     $row[4] => creadit_period
    //                                     $row[5] => creadit_limit
    //                                     $row[6] => billing_address
    //                                     $row[7] => shipping_address
    //                                     $row[8] => tax_name
    //                                     $row[9] => tax_no
    //                                     $row[10] => stauts
    //                                 */



    //                             $tables = $this->configIonAuth->tables;
    //                             $identityColumn = $this->configIonAuth->identity;

    //                             $email = strtolower(trim($row[2]));
    //                             $identity = ($identityColumn === 'email') ? $email : trim($row[1]);
    //                             $group_id_arry = fetch_details("groups", ['name' => 'suppliers'], "id");
    //                             $group_id = [$group_id_arry[0]['id']];
    //                             $additionalData = [
    //                                 'first_name' => trim($row[0]),
    //                                 'phone' => trim($row[1]),
    //                             ];


    //                             try {
    //                                 $id = $this->ionAuth->register($identity, '12345678', $email, $additionalData, $group_id);
    //                                 if (!$id) {
    //                                     $errors = $this->ionAuth->errors();
    //                                     $response['error'] = true;
    //                                     $response['message'] = 'Registration failed: ' . ($errors);
    //                                     return $this->response->setJSON($response);
    //                                 }
    //                             } catch (\Exception $e) {
    //                                 $response['error'] = true;
    //                                 $response['message'] = 'Error: ' . $e->getMessage();
    //                                 return $this->response->setJSON($response);
    //                             }

    //                             // Insert new supplier data into the database
    //                             $supplier_data = [
    //                                 'vendor_id' => $vendor_id,
    //                                 'user_id' => $id,
    //                                 'balance' => $row[3],
    //                                 'billing_address' => $row[6],
    //                                 'shipping_address' => $row[7],
    //                                 'credit_period' => $row[4],
    //                                 'credit_limit' => $row[5],
    //                                 'tax_name' => $row[8],
    //                                 'tax_num' => $row[9],
    //                                 'status' => $row[10],
    //                                 'created_at' => $row[11],
    //                                 'updated_at' => $row[12],
    //                                 // Add more fields as needed
    //                             ];


    //                             try {
    //                                 $suppliers_model->insert($supplier_data);
    //                                 // Additional processing if needed
    //                             } catch (\Exception $e) {
    //                                 // Log or display the error message
    //                                 $response['error'] = true;
    //                                 $response['message'] = 'Error: ' . $e->getMessage();
    //                                 return $this->response->setJSON($response);
    //                             }
    //                         } catch (\Exception $e) {
    //                             // Log or display the error message, including the row number
    //                             $response['error'] = true;
    //                             $response['message'] = 'Error on row ' . $row_number . ': ' . $e->getMessage();
    //                             return $this->response->setJSON($response);
    //                         }
    //                     }

    //                     fclose($handle);
    //                     $response['error'] = false;
    //                     $response['csrf_token'] = csrf_token();
    //                     $response['csrf_hash'] = csrf_hash();
    //                     $response['message'] = 'Suppliers data uploaded successfully!';
    //                     return $this->response->setJSON($response);
    //                 } else {
    //                 }
    //             }
    //         } else {
    //             return redirect()->to('admin/suppliers');
    //         }
    //     }
    // }

    public function import_suppliers()
    {
        //name , mobile , email , opening balance , creadit period , creadit limit , billing address, shipping address, tax name, tax no , stauts

        // Check if the user is logged in and is an admin
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                // Check if file is uploaded

                $this->validation->setRules([
                    'type' => 'required',
                ]);

                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {

                    // Process uploaded file and handle supplier data
                    $file = $this->request->getFile('file');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();

                    // Validate MIME type
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $type = $_POST['type'];
                    $file_path = $_FILES['file']['tmp_name'];

                    // Convert CSV to JSON
                    $json_data = csvToJson($file_path, $type);
                    if (!$json_data) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Error converting CSV to JSON!';
                        print_r(json_encode($this->response));
                        return false;
                    }

                    // Instantiate Suppliers model
                    $suppliers_model = new Suppliers_model();

                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                    } else {
                        $vendor_id = $_SESSION['user_id'];
                    }
                    if ($type == 'upload') {

                        $required_fields = [
                            'name',
                            'mobile',
                            'email',
                            'status'
                        ];
                        for ($i = 0; $i < count($json_data); $i++) {

                            $row = $json_data[$i];
                            $missing_fields = [];

                            // Check for missing required fields
                            foreach ($required_fields as $field) {
                                if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                    $missing_fields[] = $field;
                                }
                            }

                            if (!empty($missing_fields)) {
                                $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                continue;
                            }
                        }

                        // If there are errors, return them
                        if (!empty($errors)) {
                            $response['error'] = true;
                            $response['message'] = $errors;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);

                        }
                        for ($i = 0; $i < count($json_data); $i++) {
                            $identityColumn = $this->configIonAuth->identity;

                            $email = strtolower(trim($json_data[$i]['email']));
                            $identity = ($identityColumn === 'email') ? $email : trim($json_data[$i]['mobile']);
                            $group_id_arry = fetch_details("groups", ['name' => 'suppliers'], "id");
                            $group_id = [$group_id_arry[0]['id']];
                            $additionalData = [
                                'first_name' => trim($json_data[$i]['name']),
                                'phone' => trim($json_data[$i]['mobile']),
                            ];

                            try {
                                // $this->ionAuth->set_error_delimiters('', ''); // Remove default <ul><li> etc.
                                $id = $this->ionAuth->register($identity, '12345678', $email, $additionalData, $group_id);
                                if (!$id) {
                                    $errors = $this->ionAuth->errors(); // This is now plain text
                                    $response['error'] = true;

                                    // Customize your message here
                                    if (strpos($errors, 'Identity Already Used') !== false) {
                                        $response['message'] = 'Record ' . ($i + 1) . ' has a mobile number or email that is already registered.';
                                    } elseif (strpos($errors, 'Unable to Create Account') !== false) {
                                        $response['message'] = 'There was a problem creating the account. Please try again.';
                                    } else {
                                        $response['message'] = 'Registration failed: ' . $errors;
                                    }

                                    return $this->response->setJSON($response);
                                }
                            } catch (\Exception $e) {
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }

                            // Insert new supplier data into the database
                            $supplier_data = [
                                'vendor_id' => $vendor_id,
                                'user_id' => $id,
                                'balance' => $json_data[$i]['opening_balance'],
                                'billing_address' => $json_data[$i]['billing_address'],
                                'shipping_address' => $json_data[$i]['shipping_address'],
                                'credit_period' => $json_data[$i]['creadit_period'],
                                'credit_limit' => $json_data[$i]['creadit_limit'],
                                'tax_name' => $json_data[$i]['tax_name'],
                                'tax_num' => $json_data[$i]['tax_no'],
                                'status' => $json_data[$i]['stauts'],
                            ];


                            try {
                                $suppliers_model->insert($supplier_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }


                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Suppliers data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        //code for update supplier

                        $required_fields = [
                            'id',
                            'status'
                        ];
                        for ($i = 0; $i < count($json_data); $i++) {

                            $row = $json_data[$i];
                            $missing_fields = [];

                            // Check for missing required fields
                            foreach ($required_fields as $field) {
                                if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                    $missing_fields[] = $field;
                                }
                            }

                            if (!empty($missing_fields)) {
                                $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                continue;
                            }
                        }

                        // If there are errors, return them
                        if (!empty($errors)) {
                            $response['error'] = true;
                            $response['message'] = $errors;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);

                        }
                        for ($i = 0; $i < count($json_data); $i++) {

                            $user_id = $json_data[$i]['id'];

                            $supplier = fetch_details('suppliers', ['user_id' => $user_id]);
                            $supplier_id = $supplier[0]['id'];

                            $user = fetch_details('users', ['id' => $user_id], 'mobile,email,first_name');

                            $userData = [
                                "first_name" => isset($json_data[$i]['name']) && !empty($json_data[$i]['name']) ? $json_data[$i]['name'] : $user[0]['first_name'],
                                "mobile" => isset($json_data[$i]['mobile']) && !empty($json_data[$i]['mobile']) ? $json_data[$i]['mobile'] : $user[0]['mobile'],
                                "email" => isset($json_data[$i]['email']) && !empty($json_data[$i]['email']) ? $json_data[$i]['email'] : $user[0]['email'],
                            ];

                            $db = \Config\Database::connect();
                            $builder = $db->table('users');
                            $builder->where('id', $user_id)->update($userData);

                            $supplier_data = (object) [
                                'vendor_id' => $vendor_id,
                                'user_id' => $user_id,
                                'balance' => isset($json_data[$i]['opening_balance']) && !empty($json_data[$i]['opening_balance']) ? $json_data[$i]['opening_balance'] : $supplier[0]['balance'],
                                'credit_period' => isset($json_data[$i]['creadit_period']) && !empty($json_data[$i]['creadit_period']) ? $json_data[$i]['creadit_period'] : $supplier[0]['credit_period'],
                                'credit_limit' => isset($json_data[$i]['creadit_limit']) && !empty($json_data[$i]['creadit_limit']) ? $json_data[$i]['creadit_limit'] : $supplier[0]['credit_limit'],
                                'billing_address' => isset($json_data[$i]['billing_address']) && !empty($json_data[$i]['billing_address']) ? $json_data[$i]['billing_address'] : $supplier[0]['billing_address'],
                                'shipping_address' => isset($json_data[$i]['shipping_address']) && !empty($json_data[$i]['shipping_address']) ? $json_data[$i]['shipping_address'] : $supplier[0]['shipping_address'],
                                'tax_name' => isset($json_data[$i]['tax_name']) && !empty($json_data[$i]['tax_name']) ? $json_data[$i]['tax_name'] : $supplier[0]['tax_name'],
                                'tax_num' => isset($json_data[$i]['tax_no']) && !empty($json_data[$i]['tax_no']) ? $json_data[$i]['tax_no'] : $supplier[0]['tax_num'],
                                'status' => isset($json_data[$i]['stauts']) ? $json_data[$i]['stauts'] : $supplier[0]['status'],
                            ];

                            // Check if supplier with this ID exists in the database
                            $existing_supplier = $suppliers_model->find($supplier_id);
                            if ($existing_supplier) {
                                // Update existing supplier
                                try {
                                    $suppliers_model->update($supplier_id, $supplier_data);
                                    // Additional processing if needed
                                } catch (\Exception $e) {
                                    // Log or display the error message
                                    $response['error'] = true;
                                    $response['message'] = 'Error: ' . $e->getMessage();
                                    return $this->response->setJSON($response);
                                }

                                continue; // Move to the next iteration, skipping insertion
                            }
                        }

                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Suppliers data updated successfully!';
                        return $this->response->setJSON($response);
                    }
                }
            } else {
                return redirect()->to('admin/suppliers');
            }
        }
    }

    public function import_delivery_boys()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                // Check if form data is submitted
                if (isset($_POST) && !empty($_POST)) {

                    $this->validation->setRules([
                        'type' => 'required',
                    ]);

                    // Validate uploaded file
                    if (empty($_FILES['file']['name'])) {
                        $this->validation->setRules([
                            'file' => 'required',
                        ]);
                    }

                    // Run validation
                    if (!$this->validation->withRequest($this->request)->run()) {
                        // If validation fails, return error response
                        $errors = $this->validation->getErrors();
                        $response['error'] = true;
                        foreach ($errors as $e) {
                            $response['message'][] = $e;
                        }
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                        return $this->response->setJSON($response);
                    } else {
                        // Process uploaded file and handle supplier data
                        $file = $this->request->getFile('file');
                        $allowed_mime_type_arr = array(
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'application/x-csv',
                            'text/x-csv',
                            'text/csv',
                            'application/csv',
                            'application/vnd.ms-excel', // For older Excel files (.xls)
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                            'text/plain' // Allow .csv files that are identified as text/plain
                        );
                        $mime = $file->getMimeType();

                        // Validate MIME type
                        if (!in_array($mime, $allowed_mime_type_arr)) {
                            $response['error'] = true;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Invalid file format!';
                            return $this->response->setJSON($response);
                        }

                        $type = $_POST['type'];
                        $file_path = $_FILES['file']['tmp_name'];

                        // Convert CSV to JSON
                        $json_data = csvToJson($file_path, $type);
                        if (!$json_data) {
                            $this->response['error'] = true;
                            $this->response['message'] = 'Error converting CSV to JSON!';
                            print_r(json_encode($this->response));
                            return false;
                        }

                        $delivery_boy_model = new Delivery_boys_model();

                        $vendor_id = $_SESSION['user_id'];

                        $id = 0;
                        if ($type == 'upload') {

                            $required_fields = [
                                'name',
                                'email',
                                'phone',
                                'password',
                                'business_id',
                                'permissions',
                                'status'
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);

                            }
                            for ($i = 0; $i < count($json_data); $i++) {

                                $userData = [
                                    "email" => trim($json_data[$i]['email']),
                                    "first_name" => trim($json_data[$i]['name']),
                                    "mobile" => trim($json_data[$i]['phone']),
                                    "password" => $json_data[$i]['password']
                                ];
                                $identityColumn = $this->configIonAuth->identity;

                                $email = strtolower($userData["email"]);
                                $identity = ($identityColumn === 'email') ? $email : $userData["mobile"];
                                $password = trim($json_data[$i]['password']);
                                $group_id_arry = fetch_details("groups", ['name' => 'delivery_boys'], "id");
                                $group_id = [$group_id_arry[0]['id']];

                                $additionalData = [
                                    'first_name' => $userData["first_name"],
                                    'phone' => $userData["mobile"],
                                ];

                                $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);

                                if (!$id) {
                                    $errors = $this->ionAuth->errors(); // This is now plain text
                                    $response['error'] = true;

                                    // Customize your message here
                                    if (strpos($errors, 'Identity Already Used') !== false) {
                                        $response['message'] = 'Record ' . ($i + 1) . ' has a mobile number or email that is already registered.';
                                    } elseif (strpos($errors, 'Unable to Create Account') !== false) {
                                        $response['message'] = 'There was a problem creating the account. Please try again.';
                                    } else {
                                        $response['message'] = 'Registration failed: ' . $errors;
                                    }

                                    return $this->response->setJSON($response);
                                }
                                // Insert new delivery boy data into the database

                                $delivery_boy_data = (object) [
                                    'vendor_id' => $vendor_id,
                                    'user_id' => $id,
                                    'business_id' => $json_data[$i]['business_id'],
                                    'permissions' => $json_data[$i]['permissions'],
                                    'status' => isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : 0,

                                ];

                                $delivery_boy_id = $delivery_boy_model->insert($delivery_boy_data);
                            }

                            $response['error'] = false;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Delivery boys data uploaded successfully!';
                            return $this->response->setJSON($response);
                        } else {
                            // Update operation for delivery boy data
                            $required_fields = [
                                'id',
                                'name',
                                'email',
                                'phone',
                                'password',
                                'business_id',
                                'permissions',
                                'status'
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);

                            }
                            for ($i = 0; $i < count($json_data); $i++) {
                                $delivery_boy_id = $json_data[$i]['id'];

                                // getting user data 
                                $delivery_boy_from_db = $delivery_boy_model->find($delivery_boy_id);

                                // checking if delivery boy exists or not
                                if (empty($delivery_boy_from_db)) {

                                    $response['error'] = true;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['message'] = "Delivery boy does not exists with id $delivery_boy_id at record " . ($i + 1) . " !";
                                    return $this->response->setJSON($response);
                                }

                                $user_id = $delivery_boy_from_db['user_id'];

                                $user = fetch_details('users', ['id' => $user_id], 'first_name,email,mobile');

                                $userData = [
                                    "email" => isset($json_data[$i]['email']) && !empty($json_data[$i]['email']) ? $json_data[$i]['email'] : $user[0]['email'],
                                    "first_name" => isset($json_data[$i]['name']) && !empty($json_data[$i]['name']) ? $json_data[$i]['name'] : $user[0]['first_name'],
                                    "mobile" => isset($json_data[$i]['phone']) && !empty($json_data[$i]['phone']) ? $json_data[$i]['phone'] : $user[0]['mobile'],
                                ];

                                $db = \Config\Database::connect();
                                $builder = $db->table('users');
                                $builder->where('id', $user_id)->update($userData);


                                $delivery_boy_data = (object) [
                                    'vendor_id' => $vendor_id,
                                    'user_id' => $user_id,
                                    'business_id' => isset($json_data[$i]['business_id']) && !empty($json_data[$i]['business_id']) ? $json_data[$i]['business_id'] : $delivery_boy_from_db['business_id'],
                                    'permissions' => isset($json_data[$i]['permissions']) && !empty($json_data[$i]['permissions']) ? $json_data[$i]['permissions'] : $delivery_boy_from_db['permissions'],
                                    'status' => isset($json_data[$i]['status']) ? $json_data[$i]['status'] : $delivery_boy_from_db['status'],
                                ];

                                $delivery_boy_model->update($delivery_boy_id, $delivery_boy_data);

                                $response['error'] = false;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Delivery boys data updated successfully!';
                                return $this->response->setJSON($response);
                            }
                        }
                    }
                } else {
                    return redirect()->to('vendor/delivery_boys');
                }
            } else {

                if ($status == 'upcoming') {
                    $response = [
                        'error' => true,
                        'message' => ['Your subscription has not started yet!'],
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
                if ($status == 'expired') {
                    $response = [
                        'error' => true,
                        'message' => ['Please Buy Subscription to proceed ahead!'],
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
        }
    }
    public function import_customers()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                // Check if form data is submitted
                if (isset($_POST) && !empty($_POST)) {

                    $this->validation->setRules([
                        'type' => 'required',
                    ]);

                    // Validate uploaded file
                    if (empty($_FILES['file']['name'])) {
                        $this->validation->setRules([
                            'file' => 'required',
                        ]);
                    }

                    // Run validation
                    if (!$this->validation->withRequest($this->request)->run()) {
                        // If validation fails, return error response
                        $errors = $this->validation->getErrors();
                        $response['error'] = true;
                        foreach ($errors as $e) {
                            $response['message'][] = $e;
                        }
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                        return $this->response->setJSON($response);
                    } else {
                        // Process uploaded file and handle supplier data
                        $file = $this->request->getFile('file');
                        $allowed_mime_type_arr = array(
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'application/x-csv',
                            'text/x-csv',
                            'text/csv',
                            'application/csv',
                            'application/vnd.ms-excel', // For older Excel files (.xls)
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                            'text/plain' // Allow .csv files that are identified as text/plain
                        );
                        $mime = $file->getMimeType();

                        // Validate MIME type
                        if (!in_array($mime, $allowed_mime_type_arr)) {
                            $response['error'] = true;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Invalid file format!';
                            return $this->response->setJSON($response);
                        }

                        $type = $_POST['type'];
                        $file_path = $_FILES['file']['tmp_name'];

                        // Convert CSV to JSON
                        $json_data = csvToJson($file_path, $type);
                        if (!$json_data) {
                            $this->response['error'] = true;
                            $this->response['message'] = 'Error converting CSV to JSON!';
                            print_r(json_encode($this->response));
                            return false;
                        }

                        $customers_model = new Customers_model();

                        $vendor_id = $_SESSION['user_id'];

                        $id = 0;
                        if ($type == 'upload') {

                            $required_fields = [
                                'name',
                                'email',
                                'phone',
                                'password',
                                'business_id',
                                'address',
                                'status'
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);

                            }
                            for ($i = 0; $i < count($json_data); $i++) {

                                $userData = [
                                    "email" => trim($json_data[$i]['email']),
                                    "first_name" => trim($json_data[$i]['name']),
                                    "mobile" => trim($json_data[$i]['phone']),
                                    "password" => $json_data[$i]['password'],
                                    "address" => $json_data[$i]['address']
                                ];
                                $identityColumn = $this->configIonAuth->identity;

                                $email = strtolower($userData["email"]);
                                $identity = ($identityColumn === 'email') ? $email : $userData["mobile"];
                                $password = trim($json_data[$i]['password']);
                                $group_id_arry = fetch_details("groups", ['name' => 'delivery_boys'], "id");
                                $group_id = [$group_id_arry[0]['id']];

                                $additionalData = [
                                    'first_name' => $userData["first_name"],
                                    'phone' => $userData["mobile"],
                                    'address' => $userData["address"],
                                ];

                                $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);

                                if (!$id) {
                                    $errors = $this->ionAuth->errors(); // This is now plain text
                                    $response['error'] = true;

                                    // Customize your message here
                                    if (strpos($errors, 'Identity Already Used') !== false) {
                                        $response['message'] = 'Record ' . ($i + 1) . ' has a mobile number or email that is already registered.';
                                    } elseif (strpos($errors, 'Unable to Create Account') !== false) {
                                        $response['message'] = 'There was a problem creating the account. Please try again.';
                                    } else {
                                        $response['message'] = 'Registration failed: ' . $errors;
                                    }

                                    return $this->response->setJSON($response);
                                }
                                // Insert new delivery boy data into the database

                                $customer_data = (object) [
                                    'vendor_id' => $vendor_id,
                                    'user_id' => $id,
                                    'business_id' => $json_data[$i]['business_id'],
                                    'balance' => 0,
                                    'status' => isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : 0,
                                    'created_by' => $vendor_id,
                                ];

                                $customers_model->save($customer_data);
                            }

                            $response['error'] = false;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $response['message'] = 'Customers data uploaded successfully!';
                            return $this->response->setJSON($response);
                        } else {
                            // Update operation for delivery boy data
                            $required_fields = [
                                'id',
                                'name',
                                'email',
                                'phone',
                                'password',
                                'business_id',
                                'address',
                                'status'
                            ];
                            for ($i = 0; $i < count($json_data); $i++) {

                                $row = $json_data[$i];
                                $missing_fields = [];

                                // Check for missing required fields
                                foreach ($required_fields as $field) {
                                    if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                        $missing_fields[] = $field;
                                    }
                                }

                                if (!empty($missing_fields)) {
                                    $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                    continue;
                                }
                            }
                            // If there are errors, return them
                            if (!empty($errors)) {
                                $response['error'] = true;
                                $response['message'] = $errors;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);

                            }
                            for ($i = 0; $i < count($json_data); $i++) {
                                $user_id = $json_data[$i]['id'];
                                $customer = fetch_details('customers', ['user_id' => $user_id]);
                                $user = fetch_details('users', ['id' => $user_id], 'first_name,email,mobile,address');


                                // checking if delivery boy exists or not
                                if (empty($customer)) {

                                    $response['error'] = true;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    $response['message'] = "Customer does not exists with id  $user_id at record " . ($i + 1) . " !";
                                    return $this->response->setJSON($response);
                                }

                                $userData = [
                                    "email" => isset($json_data[$i]['email']) && !empty($json_data[$i]['email']) ? $json_data[$i]['email'] : $user[0]['email'],
                                    "first_name" => isset($json_data[$i]['name']) && !empty($json_data[$i]['name']) ? $json_data[$i]['name'] : $user[0]['first_name'],
                                    "mobile" => isset($json_data[$i]['phone']) && !empty($json_data[$i]['phone']) ? $json_data[$i]['phone'] : $user[0]['mobile'],
                                    "address" => isset($json_data[$i]['address']) && !empty($json_data[$i]['address']) ? $json_data[$i]['address'] : $user[0]['address'],
                                ];

                                $db = \Config\Database::connect();
                                $builder = $db->table('users');
                                $builder->where('id', $user_id)->update($userData);


                                $customer_data = (object) [
                                    'vendor_id' => $vendor_id,
                                    'user_id' => $user_id,
                                    'business_id' => isset($json_data[$i]['business_id']) && !empty($json_data[$i]['business_id']) ? $json_data[$i]['business_id'] : $customer[0]['business_id'],
                                    'balance' => 0,
                                    'status' => isset($json_data[$i]['status']) ? $json_data[$i]['status'] : $customer[0]['status'],
                                    'created_by' => $vendor_id,
                                ];

                                $customers_model->update($customer[0]['id'], $customer_data);

                                $response['error'] = false;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Customer data updated successfully!';
                                return $this->response->setJSON($response);
                            }
                        }
                    }
                } else {
                    return redirect()->to('vendor/delivery_boys');
                }
            } else {

                if ($status == 'upcoming') {
                    $response = [
                        'error' => true,
                        'message' => ['Your subscription has not started yet!'],
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
                if ($status == 'expired') {
                    $response = [
                        'error' => true,
                        'message' => ['Please Buy Subscription to proceed ahead!'],
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
        }
    }
    public function import_service()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }
        $status = subscription();
        if ($status == 'active') {

            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                $this->validation->setRules([
                    'type' => 'required',
                ]);

                // Validate uploaded file
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle supplier data
                    $file = $this->request->getFile('file');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();

                    // Validate MIME type
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $type = $_POST['type'];
                    $file_path = $_FILES['file']['tmp_name'];

                    // Convert CSV to JSON
                    $json_data = csvToJson($file_path, $type);

                    if (!$json_data) {
                        $this->response['error'] = true;
                        $this->response['message'] = 'Error converting CSV to JSON!';
                        print_r(json_encode($this->response));
                        return false;
                    }

                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                    } else {
                        $vendor_id = $_SESSION['user_id'];
                    }

                    // Instantiate Service model
                    $service_model = new Services_model();

                    if ($type == 'upload') {
                        $required_fields = [
                            'name',
                            'description',
                            'price',
                            'cost_price',
                            'business_id',
                            'status',
                            'image',
                        ];

                        for ($i = 0; $i < count($json_data); $i++) {

                            $row = $json_data[$i];
                            $missing_fields = [];

                            if ($json_data[$i]['is_recursive'] == 1) {

                                if (empty($json_data[$i]['recurring_price']) || $json_data[$i]['recurring_price'] < 0) {
                                    $errors[] = "Invalid Recurring Price Price at record " . ($i + 1);
                                    continue;
                                }
                            }

                            // Check for missing required fields
                            foreach ($required_fields as $field) {
                                if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                    $missing_fields[] = $field;
                                }
                            }

                            if (!empty($missing_fields)) {
                                $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                continue;
                            }

                            //check if price is valid or not
                            if (empty($json_data[$i]['price']) || $json_data[$i]['price'] < 0) {
                                $errors[] = "Invalid Price at record " . ($i + 1);
                                continue;
                            }

                            //check if cost_price is valid or not
                            if (empty($json_data[$i]['cost_price']) || $json_data[$i]['cost_price'] < 0) {
                                $errors[] = "Invalid Cost Price at record " . ($i + 1);
                                continue;
                            }


                        }
                        // If there are errors, return them
                        if (!empty($errors)) {
                            $response['error'] = true;
                            $response['message'] = $errors;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }

                        for ($i = 0; $i < count($json_data); $i++) {

                            $service = array(
                                'vendor_id' => $vendor_id,
                                'business_id' => $json_data[$i]['business_id'],
                                'name' => $json_data[$i]['name'],
                                'description' => $json_data[$i]['description'],
                                'price' => $json_data[$i]['price'],
                                'cost_price' => $json_data[$i]['cost_price'],
                                'tax_ids' => isset($json_data[$i]['tax_ids']) && !empty($json_data[$i]['tax_ids']) ? $json_data[$i]['tax_ids'] : '[]',
                                'unit_id' => $json_data[$i]['unit_id'],
                                'is_tax_included' => isset($json_data[$i]['is_tax_included']) && !empty($json_data[$i]['is_tax_included']) ? $json_data[$i]['is_tax_included'] : '0',
                                'is_recursive' => isset($json_data[$i]['is_recursive']) && !empty($json_data[$i]['is_recursive']) ? $json_data[$i]['is_recursive'] : '0',
                                'recurring_days' => isset($json_data[$i]['recurring_days']) && !empty($json_data[$i]['recurring_days']) ? $json_data[$i]['recurring_days'] : '0',
                                'recurring_price' => isset($json_data[$i]['recurring_price']) && !empty($json_data[$i]['recurring_price']) ? $json_data[$i]['recurring_price'] : '0',
                                'image' => $json_data[$i]['image'],
                                'status' => $json_data[$i]['status'],
                            );

                            $service_model->insert($service);
                        }

                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Services uploaded successfully!';

                        return $this->response->setJSON($response);
                    } else {
                        $required_fields = [
                            'name',
                            'description',
                            'price',
                            'cost_price',
                            'business_id',
                            'status',
                            'image',
                        ];

                        for ($i = 0; $i < count($json_data); $i++) {

                            $row = $json_data[$i];
                            $missing_fields = [];

                            if ($json_data[$i]['is_recursive'] == 1) {

                                if (empty($json_data[$i]['recurring_price']) || $json_data[$i]['recurring_price'] < 0) {
                                    $errors[] = "Invalid Recurring Price Price at record " . ($i + 1);
                                    continue;
                                }
                            }

                            // Check for missing required fields
                            foreach ($required_fields as $field) {
                                if ((!isset($row[$field]) || empty($row[$field])) && $row[$field] != 0) {
                                    $missing_fields[] = $field;
                                }
                            }

                            if (!empty($missing_fields)) {
                                $errors[] = "Record " . ($i + 1) . " is missing the following fields: " . implode(', ', $missing_fields);
                                continue;
                            }

                            //check if price is valid or not
                            if (empty($json_data[$i]['price']) || $json_data[$i]['price'] < 0) {
                                $errors[] = "Invalid Price at record " . ($i + 1);
                                continue;
                            }

                            //check if cost_price is valid or not
                            if (empty($json_data[$i]['cost_price']) || $json_data[$i]['cost_price'] < 0) {
                                $errors[] = "Invalid Cost Price at record " . ($i + 1);
                                continue;
                            }


                        }
                        // If there are errors, return them
                        if (!empty($errors)) {
                            $response['error'] = true;
                            $response['message'] = $errors;
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }

                        for ($i = 0; $i < count($json_data); $i++) {

                            $service_data = fetch_details('services', ['id' => $json_data[$i]['id']]);

                            $service = array(
                                'vendor_id' => $vendor_id,
                                'business_id' => isset($json_data[$i]['business_id']) && !empty($json_data[$i]['business_id']) ? $json_data[$i]['business_id'] : $service_data[0]['business_id'],
                                'name' => isset($json_data[$i]['name']) && !empty($json_data[$i]['name']) ? $json_data[$i]['name'] : $service_data[0]['name'],
                                'description' => isset($json_data[$i]['description']) && !empty($json_data[$i]['description']) ? $json_data[$i]['description'] : $service_data[0]['description'],
                                'price' => isset($json_data[$i]['price']) && !empty($json_data[$i]['price']) ? $json_data[$i]['price'] : $service_data[0]['price'],
                                'cost_price' => isset($json_data[$i]['cost_price']) && !empty($json_data[$i]['cost_price']) ? $json_data[$i]['cost_price'] : $service_data[0]['cost_price'],
                                'tax_ids' => isset($json_data[$i]['tax_ids']) && !empty($json_data[$i]['tax_ids']) ? $json_data[$i]['tax_ids'] : '[]',
                                'unit_id' => isset($json_data[$i]['unit_id']) && !empty($json_data[$i]['unit_id']) ? $json_data[$i]['unit_id'] : $service_data[0]['unit_id'],
                                'is_tax_included' => isset($json_data[$i]['is_tax_included']) && !empty($json_data[$i]['is_tax_included']) ? $json_data[$i]['is_tax_included'] : '0',
                                'is_recursive' => isset($json_data[$i]['is_recursive']) && !empty($json_data[$i]['is_recursive']) ? $json_data[$i]['is_recursive'] : '0',
                                'recurring_days' => isset($json_data[$i]['recurring_days']) && !empty($json_data[$i]['recurring_days']) ? $json_data[$i]['recurring_days'] : '0',
                                'recurring_price' => isset($json_data[$i]['recurring_price']) && !empty($json_data[$i]['recurring_price']) ? $json_data[$i]['recurring_price'] : '0',
                                'image' => isset($json_data[$i]['image']) && !empty($json_data[$i]['image']) ? $json_data[$i]['image'] : $service_data[0]['image'],
                                'status' => isset($json_data[$i]['status']) && !empty($json_data[$i]['status']) ? $json_data[$i]['status'] : $service_data[0]['statusk'],
                            );

                            $service_model->update($json_data[$i]['id'], $service);
                        }

                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Services updated successfully!';

                        return $this->response->setJSON($response);
                    }



                }
            } else {
                return redirect()->to('admin/services');
            }
        } else {

            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
}
