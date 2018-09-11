<?php

    namespace Models;
    use PDO;

    class Base {

        public static $pdo = null;

        public function __construct(){

            if(self::$pdo == null){

                $config = config('db');

                self::$pdo = new PDO("mysql:host=".$config['host'].";dbname=".$config['dbname']."",$config['user'],$config['pass']);
                self::$pdo->exec("set names ".$config['charset']."");
            }
        }

        public function startTrans(){

            self::$pdo->exec('start transaction');
        }

        public function commit(){

            self::$pdo->exec('commit');
        }

        public function rollback(){

            self::$pdo->exec('rollback');
        }
    }