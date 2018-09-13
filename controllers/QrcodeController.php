<?php

namespace controllers;
use Endroid\QrCode\QrCode;

class QrcodeController {

    public function qrcode(){

        $code = $_GET['code'];

        $qrCode = new QrCode($code);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }
}