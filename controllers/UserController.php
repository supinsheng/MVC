<?php

    namespace controllers;
    use Models\User;
    use Models\Order;
    use Intervention\Image\ImageManagerStatic as Image;

    class UserController {

        public function setActiveUsers(){

            $user = new User;
            $user->computeActiveUsers();
        }

        public function setAvatar(){

            $upload = \Libs\Uploader::make();
            $path = $upload->upload('avatar','avatar');

            $image = Image::make(ROOT.'public/uploads/'.$path);
            $image->crop((int)$_POST['w'],(int)$_POST['h'],(int)$_POST['x'],(int)$_POST['y']);
            $image->save(ROOT . 'public/uploads/'.$path);

            $user = new User;
            $user->setAvatar('/uploads/'.$path);

            if($_SESSION['avatar'] != "/images/fave.jpg"){

                @unlink( ROOT . 'public/' . $_SESSION['avatar'] );
            }

            $_SESSION['avatar'] = '/uploads/'.$path;

            message('设置成功！',2,'/blog/index');
        }

        public function avatar(){

            view('users.avatar');
        }

        public function orderStatus(){

            $sn = $_GET['sn'];

            $order = new Order;

            $try = 5;

            do{ 
                $status = $order->status($sn);

                if($status == 0){

                    sleep(1);
                    $try--;
                }else {
                    break;
                }
            }while($try>0);

            echo $status;
        }

        public function money(){

            $user = new User;

            echo $user->getMoney();
        }

        public function orders(){

            $order = new Order;
            $data = $order->search();

            view("users.order",$data);
        }

        public function docharge(){

            $money = $_POST['money'];

            $order = new Order;
            $order->create($money);

            message("充值订单已生成，请完成支付！",2,'/user/orders');
        }

        public function charge(){

            view("users.charge");
        }

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