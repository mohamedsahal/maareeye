<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;

use App\Models\Tax_model;


class Products extends BaseController
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


    public function get_products()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $product_id = ($this->request->getGet('id') != '') ? $this->request->getGet('id') : '';

        $settings = get_settings('general', true);
        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '$';
        $data = $_GET;
        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|numeric',
        ];
        if ($this->request->getGet('category_id')) {
            $rules['category_id'] = 'numeric';
        }
        if ($this->request->getGet('limit')) {
            $rules['limit'] = 'numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if ($this->request->getGet('offset')) {
            $rules['offset'] = 'numeric|greater_than_equal_to[0]';
        }
        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $business_id = $data['business_id'];
            $category_id = (!empty($data['category_id'])) ? $data['category_id'] : "";
            $brand_id = (!empty($data['brand_id'])) ? $data['brand_id'] : "";
            $warehouse_id = (!empty($data['warehouse_id'])) ? $data['warehouse_id'] : "";
            $limit = (!empty($data['limit'])) ? $data['limit'] : 10;
            $offset = (!empty($data['offset'])) ? $data['offset'] : 0;
            $sort = (!empty($data['sort'])) ? $data['sort'] : 'id';
            $order = (!empty($data['order'])) ? $data['order'] : 'DESC';
            $search = (!empty($data['search'])) ? $data['search'] : '';
            $products = fetch_products($business_id, $category_id, $brand_id, $search, $limit, $offset, $sort, $order, '', [], ['product_id' => $product_id], $warehouse_id);
            $final_product_list = array();
            $final_vars = [];
            $temp_arr = $products['products'];

            $variants_array = array();
            $tax_model = new Tax_model();
            if (isset($temp_arr) && !empty($temp_arr)) {
                foreach ($temp_arr as $val) {
                    if (file_exists($val['image'])) {
                        $image = base_url($val['image']);
                    } else {
                        $image = base_url('public/backend/assets/img/no-image.jpg');
                    }
                    $val['image'] = $image;
                    $variants = count($val['variants']);
                    for ($i = 0; $i < $variants; $i++) {
                        $val['variants'][$i]['image'] = $val['image'];
                        $val['variants'][$i]['name'] = $val['name'];
                        $val['variants'][$i]['category'] = category_name($val['category_id']);
                    }
                    $tax_ids = json_decode($val['tax_ids']);
                    // Note percentage and percentages are different ;
                    $percentage = 1;
                    $percentages = [];

                    // checking if the tax_ids is array or int
                    if (gettype($tax_ids) != "array") {
                        if ($tax_ids != 0) {
                            $taxes = fetch_details("tax", ['id' => $tax_ids]);
                            $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                        }
                    } else {
                        // if tax_ids is array then get get percentage 
                        foreach ($tax_ids as $tax) {
                            $taxes = fetch_details("tax", ['id' => $tax]);
                            $per = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                            $percentages[] = $per;
                        }
                    }

                    $is_tax_inlcuded = $val['is_tax_included'];
                    if ($is_tax_inlcuded != "1") {
                        for ($i = 0; $i < $variants; $i++) {
                            $sale_price = $val['variants'][$i]['sale_price'];
                            $taxable_amount_price = 0;
                            if (!empty($percentages)) {

                                foreach ($percentages as $prec) {

                                    $taxable_amount_price += floatval($sale_price) * (floatval($prec) / 100);
                                }
                            } else {
                                $taxable_amount_price = floatval($sale_price) * (floatval($percentage) / 100);
                            }

                            $price = floatval($sale_price) + $taxable_amount_price;
                            $val['variants'][$i]['sale_price'] = $price;

                            $purchase_price = $val['variants'][$i]['purchase_price'];
                            $taxable_amount = 0;
                            if (!empty($percentages)) {
                                foreach ($percentages as $prec) {
                                    $taxable_amount += floatval($purchase_price) * (floatval($prec) / 100);
                                }
                            } else {
                                $taxable_amount = floatval($purchase_price) * (floatval($percentage) / 100);
                            }

                            $purchase = floatval($purchase_price) + $taxable_amount;
                            $val['variants'][$i]['purchase_price'] = $purchase;
                        }
                    } else {
                        $val['variants'] = $val['variants'];
                    }
                    $final_product_list[] = $val;
                }
            }

            // getting only varients array for the select2 search. 
            $variants_array = array_column($final_product_list, 'variants');
            $count = count($variants_array);
            for ($i = 0; $i < $count; $i++) {
                foreach ($variants_array[$i] as $row) {
                    array_push($final_vars, $row);
                }
            }
            $response['variants'] = $final_vars;
            $response['error'] = (!empty($products['products'])) ? false : true;
            $response['message'] = (!empty($products['products'])) ? "Products fetched successfully" : "No products found!";
            $response['total'] = $products['total'];
            $response['data'] = $final_product_list;
            $response['currency'] = $currency;
            return $this->response->setJSON($response);
        }
    }

    public function get_services()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $data = $_GET;

        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|trim|numeric',
        ];
        if ($this->request->getGet('limit')) {
            $rules['limit'] = 'trim|numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if ($this->request->getGet('offset')) {
            $rules['offset'] = 'trim|numeric|greater_than_equal_to[0]';
        }

        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $business_id = $data['business_id'];
            $limit = (!empty($data['limit'])) ? $data['limit'] : 10;
            $offset = (!empty($data['offset'])) ? $data['offset'] : 0;
            $sort = (!empty($data['sort'])) ? $data['sort'] : 'id';
            $order = (!empty($data['order'])) ? $data['order'] : 'DESC';
            $search = (!empty($data['search'])) ? $data['search'] : '';
            $services = fetch_services($business_id, $search, $limit, $offset, $sort, $order);
            $final_product_list = array();
            $temp_arr = $services['services'];
            if (isset($temp_arr) && !empty($temp_arr)) {
                foreach ($temp_arr as $val) {


                    if (file_exists($val['image'])) {
                        $image = base_url($val['image']);
                    } else {
                        $image = base_url('public/backend/assets/img/no-image.jpg');
                    }
                    $val['image'] = $image;
                    $tax_ids = json_decode($val['tax_ids'], true);

                    // Note percentage and percentages are different ;
                    $percentage = 1;
                    $percentages = [];

                    // checking if the tax_ids is array or int
                    if (gettype($tax_ids) != "array") {
                        if ($tax_ids != 0) {
                            $taxes = fetch_details("tax", ['id' => $tax_ids]);
                            $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                        }
                    } else {
                        // if tax_ids is array then get get percentage;
                        foreach ($tax_ids as $tax) {
                            $taxes = fetch_details("tax", ['id' => $tax]);
                            $per = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                            $percentages[] = $per;
                        }
                    }

                    $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "0";
                    $is_tax_inlcuded = $val['is_tax_included'];
                    $is_tax_inlcuded = $val['is_tax_included'];
                    if ($is_tax_inlcuded != "1") {

                        $sale_price = $val['price'];
                        $taxable_amount_price = 0;
                        if (!empty($percentages)) {

                            foreach ($percentages as $prec) {

                                $taxable_amount_price += floatval($sale_price) * (floatval($prec) / 100);
                            }
                        } else {
                            $taxable_amount_price = floatval($sale_price) * (floatval($percentage) / 100);
                        }

                        $price = floatval($sale_price) + $taxable_amount_price;
                        $val['price'] = $price;
                    } else {
                        $val;
                    }
                    $final_product_list[] = $val;
                }
            }

            $response['error'] = (!empty($services['services'])) ? false : true;
            $response['message'] = (!empty($services['services'])) ? "Services fetched successfully" : "No service found!";
            $response['total'] = $services['total'];
            $response['data'] = $final_product_list;
            return $this->response->setJSON($response);
        }
    }

}
