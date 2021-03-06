<?php

    namespace controllers;
    use PDO;

    class MockController {

        public function user(){

            try{

                $pdo = new PDO("mysql:host=localhost;dbname=mvc","root","123");
                $pdo->exec("set names UTF8");  

            }catch(PDOException $e){
                die("数据库连接失败".$e->getMessage());
            } 

            for($i=0;$i<10;$i++){

                $email = rand(50000,99999999999).'@126.com';
                $password = md5('123');
                $pdo->exec("INSERT INTO users (email,password) VALUES('$email','$password')");
            }
        }

        public function blog(){

            try{

                $pdo = new PDO("mysql:host=localhost;dbname=mvc","root","123");
                $pdo->exec("set names UTF8");  

            }catch(PDOException $e){
                die("数据库连接失败".$e->getMessage());
            } 

            for($i=0;$i<110;$i++){

                $title = $this->getChar(rand(10,50));
                $content = $this->getChar(rand(100,600));
                $display = rand(0,10000);
                $date = rand(1233333399,1535592288);
                $date = date('Y-m-d H:i:s',$date);
                $user_id = rand(1,11);

                $num = $pdo->exec("INSERT INTO blog(title,content,display,created_at,user_id) VALUES('$title','$content','$display','$date',$user_id)");
            }
        }  

        private function getChar($num)  // $num为生成汉字的数量
        {
            $b = '';
            for ($i=0; $i<$num; $i++) {
                // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
                $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
                // 转码
                $b .= iconv('GB2312', 'UTF-8', $a);
            }
            return $b;
        }
    }