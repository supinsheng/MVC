<?php

namespace Controllers;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class AlipayController {

    protected $config = [
        'app_id' => '2016091600527385',
        'notify_url' => 'http://localhost:5533/alipay/notify',
        'return_url' => 'http://localhost:5533/alipay/return',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArO337et0TRnUUEZ7rZsQ1jakw0V7FMKghVUSlgJ/QxIizihWWgBXET5phSfXjBWT8Ok0anbtHLcKmq37h4A/MfiMMOSezvQvhoOegz3rNbJBjR5M10C/7YT7UmEsgTOB0yyjT18oR+cJJ0AWxvvmRP04CaMPsbsSEwNyHlDb+GyCmQTDGmRBNYafH/LNkph28Ud/dR0RwpWEW2FtmggkA3A7nWiy7U4ImQy9e6IA4n9qeGjvqLuLJ34qkEB5cbYx16f53Y/cduZr/vMgjZVj/ztvzpuJKvx5aU9Bq9mHpavVOnaIyIVFamhRJF8ZHK6nn2zRKawiRsp+LpCse69E4wIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEpAIBAAKCAQEAv+racZU+nIsKfywFYE9s4ZiXJWdSzOxKZHSZR3hTJ7/0nj+BxqpxAgqSxBEXzeYyXCFVx3JDGcxuS2z7MfZJp0hQZA3poV9wwmm1dkpL/xN0AaB/H7ERFhOVDwUtaAHyc1A64l/h2seLKL6nMtVEgRLylG1hvaEo1mP5NeLtX4kIYjONcPBAb4CazVlTx22fYFJHfAbG5ft291VRIg0rNqaNF+YTMmc6Z1w1jN6isAaYmoSMum8Gawcak9+BCowPc8b7jYE9B2cPDBtN6cexSpwyUmIJfLYwyix8XyDGC8ZttGJ6ZaMhBKIQXacCGRyHp/SXFoswVpcc2JMrVfVVFwIDAQABAoIBABHf9eiOVf6OlLOzjeyieSmp5Kn/LZ8YgvVIRt55PoV9Q3NZxuSSC2R9R54rWWQ8BITANhUVd1p3x+4OgHbu0X1bJuGqyg/Vq9LvtY8G7H0deriMEksJWuYLfN6hRNFjHQnQdyuOcqLF4xgwabI6Wt8KvL1GUswurncJaLS0Jhe3qlemWO+yed/B+hYwMhot/h7+UP1kI0YMw7skPdn0uZYrS7Xq/vCjXKqRePJ/9tPeSS9K63iev3iNRT3R9K72GGspUvap64zJEBtEVUjPhEOCfd/axfCzt3VSjX8jPi5eYRgs7WGEp+qaTTfparsXw/825WyPTIjQz1I1HSpCMoECgYEA5YO2yHEHfp9FeoUf+eNFwU8DbJ3OnoepR89sFEQqJYVHcLteMLDsQedfSN3si60qTb+JMsH17xB90u4FMqtR+0XSgnJ1CfqL//GwKkuWSQVi8VPgpwn15cmvqCpXURQRs51EBAGwYnmM/vObpWavm0jf8+fiiHB8fKBw3xjEXYkCgYEA1hBzWbdLVVj17p5bOercsumH5McwabPgXnfD9CKPVGsLJwwPkNKUP2GIsB+kMbEdgaV2XtrauRpA5o26qZgl+UkPDr8/GvJ96lmlKRsjxhajjhCYoLVzHf+7c9d7ZMuP160tqcoyGNQh00ApcqbpxipIcrkHj7ycdyjZhEv7FZ8CgYAvmn8Z2d+9EpndjKkSMmJwcsv6Bk0psmeY/lujZHP9bkRgDGy/2qJWFQA9Y9JBMjx4/cYeIf65hAkk67tmRARwAo5kAgtmc3IANwfb7euQ364i8cvBuZ4n+AoX7hhIN8poH7FQx2znL+DfrHVW/BJmOdpBg5IHzJT1YG2oyEFKSQKBgQCiiPEcwMjJb/ekC8cWrdaPXjQZqIiA6dxFvkgh+Y+8uF5KcqTrAIkhqsvfdtb0CBhwVRMQqLoEbAO1Sw3dMbI7mpZ9Swb+Tfr/UrvN/1ZlVNEw0DcjZ0KWF5PcPFPrz4hTaaHPVsA9C+z2+rO94zCDj51cxlMQd1SSQYkPI6Xs3wKBgQDUQTQP2G6iVzdnEuaM/3RN1njN+xeCOjsXWsRgCDFxX7Q/9kMYLHm4C7+Rz26xxnOd8PeGmW1eZE3Ht6QIgxxeQbab/Iwgw6KppY08n+TwUrkKynjxJFjFMan35nexHVkwHs1gyln5KvaBvU+fwZiCYolmIupeHEZIN+mqmxd5JA==',
    
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];

    public function index(){
        view('alipay.index');
    }

    public function pay(){

        $money = $_POST['money'];

        $order = [
            'out_trade_no' => time(),
            'total_amount' => $money,
            'subject' => 'test subject - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        echo $data->out_trade_no."<br>";
        echo $data->trade_no."<br>";
        echo $data->total_amount."<br>";

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }
}