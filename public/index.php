<?php
    
    define("ROOT",dirname(__FILE__)."/../");
    require(ROOT.'vendor/autoload.php');

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