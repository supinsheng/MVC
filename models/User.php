<?php

    namespace Models;
    use PDO;

    class User extends Base {

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

                return TRUE;
            }
        }
    }