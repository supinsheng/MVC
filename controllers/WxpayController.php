<?php
namespace controllers;

use Yansongda\Pay\Pay;
use Models\Order;
use Models\User;

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
        $sn = $_POST['sn'];

        $order = new Order;

        $data = $order->findSn($sn);

        if($data['status'] == 0){

            $pay = Pay::wechat($this->config)->scan([
                'out_trade_no' => $sn,
                'total_fee' => $data['money'] * 100, // **单位：分**
                'body' => '智聊系统用户充值 ：'.($data['money'] * 100).'元',
                // 'openid' => 'onkVf1FjWS5SBIixxxxxxx',
            ]);
            
            $code = $pay->code_url;

            view("users.wxpay",['code'=>$code,'sn'=>$sn]);

        }else {
            die('订单状态不允许支付~');
        }

        

        // echo $pay->return_code , '<hr>';
        // echo $pay->return_msg , '<hr>';
        // echo $pay->appid , '<hr>';
        // echo $pay->result_code , '<hr>';
        // echo $pay->code_url , '<hr>';
        
    }

    public function notify()
    {
        $log = new \Libs\Log('wxpay');

        $log->log('接收到微信的消息！');
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            $log->log('验证成功，接收的数据是：'. file_get_contents('php://input'));
            // 判断是否支付成功
            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                $order = new Order;

                $orderInfo = $order->findSn($data->out_trade_no);

                if($orderInfo && $orderInfo['status'] == 0){

                    $order->startTrans();

                    $ret1 = $order->setPaid($data->out_trade_no);

                    $user = new User;

                    $ret2 = $user->addMoney($orderInfo['money'], $orderInfo['user_id']);

                    if($ret1 && $re2){

                        $order->commit();
                    }else {
                        $order->rollback();
                    }
                }
            }

        } catch (Exception $e) {
            $log->log('验证失败！'.$e->getMessage());
            var_dump( $e->getMessage() );
        }
        // 发送响应
        $pay->success()->send();
    }
}