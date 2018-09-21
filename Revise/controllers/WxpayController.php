<?php

namespace Controllers;
use Yansongda\Pay\Pay;
use Endroid\QrCode\QrCode;

class WxpayController {

    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://cea3e421.ngrok.io/wxpay/notify',
    ];

    public function index(){

        view('wxpay.index');
    }

    public function pay(){

        $money = $_POST['money'];
        $order = [
            'out_trade_no' => time(),
            'total_fee' => $money*100, // **单位：分**
            'body' => 'test body - 测试',
            // 'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Pay::wechat($this->config)->scan($order);

        // echo $pay->return_code , '<hr>';
        // echo $pay->return_msg , '<hr>';
        // echo $pay->appid , '<hr>';
        // echo $pay->result_code , '<hr>';
        // echo $pay->code_url , '<hr>';

        
        // $code = "/uploads/waterImage.jpg";

        $code = $pay->code_url;

        view('wxpay.pay',['code'=>$code]);
    }

    public function qrCode(){

        $code = $_GET['code'];
        $qrCode = new QrCode($code);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            Log::debug('Wechat notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }
}