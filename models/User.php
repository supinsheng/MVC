<?php

    namespace Models;
    use PDO;

    class User extends Base {

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