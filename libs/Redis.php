<?php

namespace Libs;

class Redis {

    private static $redis = null;
    private function __clone(){}
    private function __construct(){}

    public static function getInstance(){

        if(self::$redis == null){

            $data = config('redis');

            self::$redis = new \Predis\Client([
                'scheme' => $data['scheme'],
                'host'   => $data['host'],
                'port'   => $data['port'],
            ]);
        }

        return self::$redis;
    }
}