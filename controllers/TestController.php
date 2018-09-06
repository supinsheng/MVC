<?php

namespace controllers;

class TestController {

    public function testLog(){

        $log = new \Libs\Log('email');

        $log->log('日志内容~');
    }

    public function testPHP(){

        phpinfo();
    }
}