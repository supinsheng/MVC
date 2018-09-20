<?php

    namespace Models;
    use PDO;

    class User extends Base {

        public function getActiveUsers(){

            $redis = \Libs\Redis::getInstance();

            $data = $redis->get('active_users');

            return json_decode($data,true);
        }

        public function computeActiveUsers(){

            $stmt = self::$pdo->query('SELECT user_id,count(*)*5 fz FROM blog WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
            $data1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
            $stmt = self::$pdo->query('SELECT user_id,count(*)*5 fz FROM comments WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
            $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            $stmt = self::$pdo->query('SELECT user_id,count(*)*5 fz FROM blog_agrees WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) GROUP BY user_id');
            $data3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $arr = [];

            foreach($data1 as $v){

                $arr[$v['user_id']] = $v['fz'];
            }
            
            foreach($data2 as $v){

                if(isset($arr[$v['user_id']])) $arr[$v['user_id']] += $v['fz'];
                else $arr[$v['user_id']] = $v['fz'];
            }

            foreach($data3 as $v){

                if(isset($arr[$v['user_id']])) $arr[$v['user_id']] += $v['fz'];
                else $arr[$v['user_id']] = $v['fz'];
            }

            arsort($arr);

            $data = array_slice($arr,0,20,true);

            $userIds = array_keys($data);
            
            $userIds = implode(',',$userIds);

            $sql = "SELECT id,email,avatar FROM users WHERE id IN($userIds)";

            $stmt = self::$pdo->query($sql);
            $data = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $redis = \Libs\Redis::getInstance();
            $redis->set('active_users',json_encode($data));

        }

        public function getAll(){

            $stmt = self::$pdo->query("SELECT * FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function setAvatar($path){

            $stmt = self::$pdo->prepare('UPDATE users SET avatar=? WHERE id=?');
            $stmt->execute([
                $path,
                $_SESSION['id']
            ]);
        }

        public function getMoney(){

            $stmt = self::$pdo->prepare("SELECT money FROM users WHERE id=?");

            $stmt->execute([$_SESSION['id']]);

            $money = $stmt->fetch(PDO::FETCH_COLUMN);

            $_SESSION['money'] = $money;

            return $money;
        }

        public function addMoney($money,$userId){

            $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");

            return $stmt->execute([$money,$userId]);
        }

        public function add($email,$password){

            $stmt = self::$pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
            return $stmt->execute([$email,$password]);
        }

        public function login($email,$password){

            $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->execute([$email,$password]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user){
                return FALSE;
            }else {

                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['money'] = $user['money'];
                $_SESSION['avatar'] = $user['avatar'] ? $user['avatar'] : '/images/fave.jpg';

                return TRUE;
            }
        }
    }