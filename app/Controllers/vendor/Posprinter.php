<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Posprinter extends BaseController
{
    public $ionAuth ;
    public $validation;
    public $configIonAuth;
    public $session;
    public function index()
    {
        // return view('welcome_message');
        $connector = new NetworkPrintConnector("150.129.54.153", 9100);
        $printer = new Printer($connector);
        try {
            $printer->text("print invoice here");
            $printer->cut();
        } finally {
            $printer->close();
        }
        echo "code is working";
    }
}
