<?php

namespace controllers;
use Yansongda\Pay\Pay;
use Models\Order;
use Models\User;

class AlipayController {

    // 账号：rhacsc7797@sandbox.com

    public $config = [
        'app_id' => '2016091600527385',
        // 通知地址
        'notify_url' => 'http://0e452974.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://localhost:5533/alipay/return',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArO337et0TRnUUEZ7rZsQ1jakw0V7FMKghVUSlgJ/QxIizihWWgBXET5phSfXjBWT8Ok0anbtHLcKmq37h4A/MfiMMOSezvQvhoOegz3rNbJBjR5M10C/7YT7UmEsgTOB0yyjT18oR+cJJ0AWxvvmRP04CaMPsbsSEwNyHlDb+GyCmQTDGmRBNYafH/LNkph28Ud/dR0RwpWEW2FtmggkA3A7nWiy7U4ImQy9e6IA4n9qeGjvqLuLJ34qkEB5cbYx16f53Y/cduZr/vMgjZVj/ztvzpuJKvx5aU9Bq9mHpavVOnaIyIVFamhRJF8ZHK6nn2zRKawiRsp+LpCse69E4wIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEpAIBAAKCAQEAv+racZU+nIsKfywFYE9s4ZiXJWdSzOxKZHSZR3hTJ7/0nj+BxqpxAgqSxBEXzeYyXCFVx3JDGcxuS2z7MfZJp0hQZA3poV9wwmm1dkpL/xN0AaB/H7ERFhOVDwUtaAHyc1A64l/h2seLKL6nMtVEgRLylG1hvaEo1mP5NeLtX4kIYjONcPBAb4CazVlTx22fYFJHfAbG5ft291VRIg0rNqaNF+YTMmc6Z1w1jN6isAaYmoSMum8Gawcak9+BCowPc8b7jYE9B2cPDBtN6cexSpwyUmIJfLYwyix8XyDGC8ZttGJ6ZaMhBKIQXacCGRyHp/SXFoswVpcc2JMrVfVVFwIDAQABAoIBABHf9eiOVf6OlLOzjeyieSmp5Kn/LZ8YgvVIRt55PoV9Q3NZxuSSC2R9R54rWWQ8BITANhUVd1p3x+4OgHbu0X1bJuGqyg/Vq9LvtY8G7H0deriMEksJWuYLfN6hRNFjHQnQdyuOcqLF4xgwabI6Wt8KvL1GUswurncJaLS0Jhe3qlemWO+yed/B+hYwMhot/h7+UP1kI0YMw7skPdn0uZYrS7Xq/vCjXKqRePJ/9tPeSS9K63iev3iNRT3R9K72GGspUvap64zJEBtEVUjPhEOCfd/axfCzt3VSjX8jPi5eYRgs7WGEp+qaTTfparsXw/825WyPTIjQz1I1HSpCMoECgYEA5YO2yHEHfp9FeoUf+eNFwU8DbJ3OnoepR89sFEQqJYVHcLteMLDsQedfSN3si60qTb+JMsH17xB90u4FMqtR+0XSgnJ1CfqL//GwKkuWSQVi8VPgpwn15cmvqCpXURQRs51EBAGwYnmM/vObpWavm0jf8+fiiHB8fKBw3xjEXYkCgYEA1hBzWbdLVVj17p5bOercsumH5McwabPgXnfD9CKPVGsLJwwPkNKUP2GIsB+kMbEdgaV2XtrauRpA5o26qZgl+UkPDr8/GvJ96lmlKRsjxhajjhCYoLVzHf+7c9d7ZMuP160tqcoyGNQh00ApcqbpxipIcrkHj7ycdyjZhEv7FZ8CgYAvmn8Z2d+9EpndjKkSMmJwcsv6Bk0psmeY/lujZHP9bkRgDGy/2qJWFQA9Y9JBMjx4/cYeIf65hAkk67tmRARwAo5kAgtmc3IANwfb7euQ364i8cvBuZ4n+AoX7hhIN8poH7FQx2znL+DfrHVW/BJmOdpBg5IHzJT1YG2oyEFKSQKBgQCiiPEcwMjJb/ekC8cWrdaPXjQZqIiA6dxFvkgh+Y+8uF5KcqTrAIkhqsvfdtb0CBhwVRMQqLoEbAO1Sw3dMbI7mpZ9Swb+Tfr/UrvN/1ZlVNEw0DcjZ0KWF5PcPFPrz4hTaaHPVsA9C+z2+rO94zCDj51cxlMQd1SSQYkPI6Xs3wKBgQDUQTQP2G6iVzdnEuaM/3RN1njN+xeCOjsXWsRgCDFxX7Q/9kMYLHm4C7+Rz26xxnOd8PeGmW1eZE3Ht6QIgxxeQbab/Iwgw6KppY08n+TwUrkKynjxJFjFMan35nexHVkwHs1gyln5KvaBvU+fwZiCYolmIupeHEZIN+mqmxd5JA==',
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];

    // 发起支付
    public function pay()
    {

        $sn = $_POST['sn'];

        $order = new Order;

        $data = $order->findSn($sn);

        if($data['status'] == 0){

            $alipay = Pay::alipay($this->config)->web([
                'out_trade_no' => (int)$sn,      // 本地订单ID
                'total_amount' => (int)$data['money'],   // 支付金额    // 支付金额
                'subject' => '智聊系统用户充值 ：'.$data['money'].'元',   // 支付标题
            ]);
    
            $alipay->send();
        }else {

            die('订单状态不允许支付~');
        }
    }

    // 支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        echo '<h1>支付成功！</h1> <hr>';
        var_dump( $data->all() );
    }

    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；

            if($data->trade_status == "TRADE_SUCCESS" || $data->trade_status == "TRADE_FINISHED"){

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
            // echo '订单ID：'.$data->out_trade_no ."\r\n";
            // echo '支付总金额：'.$data->total_amount ."\r\n";
            // echo '支付状态：'.$data->trade_status ."\r\n";
            // echo '商户ID：'.$data->seller_id ."\r\n";
            // echo 'app_id：'.$data->app_id ."\r\n";
        } catch (\Exception $e) {
            echo '失败：';
            var_dump($e->getMessage()) ;
        }
        // 返回响应
        return $alipay->success()->send();
    }

    public function refund(){

        // 生成唯一退款订单号
        $refundNo = md5( rand(1,99999) . microtime() );
        
        try{

            // 退款
            $ret = Pay::alipay($this->config)->refund([
                'out_trade_no' => '258921699892785152',    // 之前的订单流水号
                'refund_amount' => 100.00,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 退款订单号
            ]);

            if($ret->code == 10000)
            {
                echo '退款成功！';
            }else{
                echo '退款失败，错误信息'.$ret->sub_msg;
                echo '错误编号'.$ret->sub_code;
            }
        }catch(\Exception $e) {

            var_dump( $e->getMessage() );
        }
    }
}