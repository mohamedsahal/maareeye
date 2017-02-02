<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Libraries\Stripe;

class Webhooks extends BaseController
{
    private $stripe;
    public function __construct()
    {
        $this->stripe = new Stripe;
    }

    public function stripe()
    {

        $credentials = $this->stripe->get_credentials();
        $request_body = file_get_contents('php://input');
        $event = json_decode($request_body, FALSE);
        log_message('error', 'Stripe Webhook test --> ');
        if (!empty($event->data->object->payment_intent)) {
            $txn_id = (isset($event->data->object->payment_intent)) ? $event->data->object->payment_intent : "";
            if (!empty($txn_id)) {
            }
            $amount = $event->data->object->amount;
            $currency = $event->data->object->currency;
            $balance_transaction = $event->data->object->balance_transaction;
        } else {
            $order_id = 0;
            $amount = 0;
            $currency = (isset($event->data->object->currency)) ? $event->data->object->currency : "";
            $balance_transaction = 0;
        }

        /* Wallet refill has unique format for order ID - wallet-refill-user-{user_id}-{system_time}-{3 random_number}  */


        $http_stripe_signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : "";
        $result = $this->stripe->construct_event($request_body, $http_stripe_signature, $credentials['webhook_key']);

        $amount = $event->data->object->metadata->amount;
        $user_id = $event->data->object->metadata->user_id;
        $plan_id = $event->data->object->metadata->plan_id;
        $tenure_id = $event->data->object->metadata->tenure;
        $db = \Config\Database::connect();

        $tenure = $db->table('packages_tenures')->where(['id' => $tenure_id, 'package_id' => $plan_id])->get()->getResultArray()[0];
        $amount = $tenure['price'];
        $tenure_name = $tenure['tenure'];
        $id = $user_id;

        if ($result == "Matched") {
            if ($event->type == 'charge.succeeded') {
                $insert_id = add_transaction($txn_id, $amount, "Stripe", $id, "success");
                if ($sub_id = add_subscription($id, $plan_id, $tenure['months'], $txn_id, $amount, $tenure_name)) {
                    $response['error'] = false;
                    $response['message'] = "Order Placed Successfully";

                    $response['plan'] = $plan_id;
                    update_details(
                        [

                            'transaction_id' => $insert_id,
                        ],
                        [
                            'id' => $sub_id,

                        ],
                        'users_packages'
                    );
                    update_details(
                        [
                            'subscription_id' => $sub_id,
                        ],
                        [
                            'id' => $insert_id,

                        ],
                        'transactions'
                    );
                    return $this->response->setJSON($response);
                }
                $response['error'] = true;
                $response['message'] = "something went wrong";


                return $this->response->setJSON($response);


                $response['error'] = false;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction successfully done";
                echo json_encode($response);
                return false;
            } elseif ($event->type == 'charge.failed') {
                $insert_id = add_transaction($txn_id, $amount, "Stripe", $id, "failed");
            } elseif ($event->type == 'charge.pending') {
                $insert_id = add_transaction($txn_id, $amount, "Stripe", $id, "pending");

                return false;
            } elseif ($event->type == 'charge.expired') {
                $insert_id = add_transaction($txn_id, $amount, "Stripe", $id, "failed");


                return false;
            } elseif ($event->type == 'charge.refunded') {
                $insert_id = add_transaction($txn_id, $amount, "Stripe", $id, "refunded");

                return false;
            } else {
                $response['error'] = true;
                $response['transaction_status'] = $event->type;
                $response['message'] = "Transaction could not be detected.";

                echo json_encode($response);
                return false;
            }
        } else {
            log_message('error', 'Stripe Webhook | Invalid Server Signature  --> ' );
            return false;
        }
    }
}
