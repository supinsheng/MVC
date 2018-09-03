<?php

    namespace controllers;
    use Models\User;

    class UserController {

        public function register(){

            view("users.add");
        }

        public function store(){

            $email = $_POST['email'];
            $password = md5($_POST['password']);

            $user = new User;
            $ret = $user->add($email,$password);

            if(!$ret){

                echo "注册失败！";
            }

            // $mail = new \Libs\Mail;

            // $content = "恭喜您 ，注册成功！";
            $name = explode('@', $email);
            $from = [$email, $name[0]];

            // $mail->send('注册成功',$content, $from);
            $message = [
                'content'=>"点击以下链接进行激活：<br> <a href=''>点击激活</a>。",
                'title'=>'注册成功！',
                'from'=>$from
            ];

            $message = json_encode($message);

            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            $redis->lpush("email",$message);

            echo 'ok';
        }
    }