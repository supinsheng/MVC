<?php
namespace controllers;

use Yansongda\Pay\Pay;
use Endroid\QrCode\QrCode;

class WxpayController
{
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://0a3de674.ngrok.io/wxpay/notify',
    ];

    public function pay()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **单位：分**
            'body' => 'test body',
            // 'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Pay::wechat($this->config)->scan($order);

        echo $pay->return_code , '<hr>';
        echo $pay->return_msg , '<hr>';
        echo $pay->appid , '<hr>';
        echo $pay->result_code , '<hr>';
        echo $pay->code_url , '<hr>';
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            // 判断是否支付成功
            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                echo '共支付了：'.$data->total_fee.'分';
                echo '订单ID：'.$data->out_trade_no;
            }

        } catch (Exception $e) {
            var_dump( $e->getMessage() );
        }
        // 发送响应
        $pay->success()->send();
    }

    public function qrcode()
    {
        $qrCode = new QrCode('weixin://wxpay/bizpayurl?pr=jQmnlGZ');
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }
}