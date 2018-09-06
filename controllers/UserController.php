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

            $code = md5( rand(1,99999) );

            $key = "temp_user:{$code}";

            $redis = \Libs\Redis::getInstance();

            $value = json_encode([
                'email'=>$email,
                'password'=>$password
            ]);

            $redis->setex($key,300,$value);

            // $mail = new \Libs\Mail;

            // $content = "恭喜您 ，注册成功！";
            $name = explode('@', $email);
            $from = [$email, $name[0]];

            // $mail->send('注册成功',$content, $from);
            $message = [
                'content'=>"点击以下链接进行激活：<br> <a href='http://localhost:5533/user/active_user?code={$code}'>
                http://localhost:5533/user/active_user?code={$code}</a>。
                <p>如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",
                'title'=>'注册成功！',
                'from'=>$from
            ];

            $message = json_encode($message);

            $redis->lpush("email",$message);

            echo 'ok';
        }

        public function active_user(){

            $code = $_GET['code'];

            $key = "temp_user:".$code;

            $redis = \Libs\Redis::getInstance();

            $data = $redis->get($key);

            if($data){

                $redis->del($key);
                $data = json_decode($data,true);

                $user = new User;

                $user->add($data['email'],$data['password']);

                header("Location:/user/login");
            }else {

                die("激活码无效！");
            }
        }

        public function login(){

            view("users.login");
        }

        public function dologin(){

            $email = $_POST['email'];
            $password = md5($_POST['password']);

            $user = new User;

            if($user->login($email,$password)){

                message('登录成功！', 2, '/blog/index');
            }else {
                message('账号或者密码错误', 1, '/user/login');
            }
        }

        public function logout(){

            unset($_SESSION['id']);
            unset($_SESSION['email']);

            die("退出成功！");
        }
    }