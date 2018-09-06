<?php

    define("ROOT",dirname(__FILE__)."/../");
    require(ROOT.'vendor/autoload.php');

    // 设置 SESSION 保存
    ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
    ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=1');  // 设置 redis 服务器的地址、端口、使用的数据库
    ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期
    session_start();

    function autoload($class){

        $path = str_replace('\\','/',$class);
        require(ROOT.$path.'.php');
    }

    spl_autoload_register('autoload');

    if(php_sapi_name() == "cli"){

        $controller = ucfirst($argv[1])."Controller";
        $action = $argv[2];
    }else {

        if(isset($_SERVER['PATH_INFO'])){

            $pathInfo = explode("/",$_SERVER['PATH_INFO']);
            $controller = ucfirst($pathInfo[1]).'Controller';
            $action = $pathInfo[2];
        }else {
            $controller = "IndexController";
            $action = "index";
        }
    }

    $fullController = 'controllers\\'.$controller;
    
    $C = new $fullController;
    $C->$action();

    function view($viewFileName,$data=[]){

        // 解压数组成变量
        extract($data);

        $path = str_replace('.','/',$viewFileName).'.html';
        require(ROOT . 'views/' . $path);
    }

    function getUrlParams($except = []){

        foreach($except as $v){

            unset($_GET[$v]);
        }

        $str = '';
        foreach($_GET as $k=>$v){

            $str .= "$k=$v&";
        }
        return $str;
    }

    function config($name){

        static $config = null;

        if($config === null){

            $config = require_once(ROOT.'config.php');
        }

        return $config[$name];
    }

    function redirect($route){

        header("Location:".$route);
        exit;
    }

    function back(){

        header("Location:".$_SERVER['HTTP_REFERER']);
    }

    function message($message,$type,$url,$seconds=5){

        if($type==1){

            view('common.success',[
                'message'=>$message,
                'url'=>$url,
                'seconds'=>$seconds
            ]);
        }else if($type == 2){

            // 把消息保存到 SESSION
            $_SESSION['_MESS_'] = $message;
            // 跳转到下一个页面
            redirect($url);
        }
    }