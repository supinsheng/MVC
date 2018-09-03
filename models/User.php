<?php

    namespace Models;
    use PDO;

    class User {

        public $pdo;

        public function __construct(){

            $this->pdo = new PDO("mysql:host=localhost;dbname=mvc","root","123");
            $this->pdo->exec("SET NAMES utf8");
        }

        public function add($email,$password){

            $stmt = $this->pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
            return $stmt->execute([$email,$password]);
        }
    }