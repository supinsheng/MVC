<?php
    
    define("ROOT",dirname(__FILE__)."/../");

    function autoload($class){

        $path = str_replace('\\','/',$class);
        require(ROOT.$path.'.php');
    }

    spl_autoload_register('autoload');

    if($_SERVER['PATH_INFO']){

        $pathInfo = explode("/",$_SERVER['PATH_INFO']);
        $controller = ucfirst($pathInfo[1]).'Controller';
        $action = $pathInfo[2];
    }else {
        $controller = "IndexController";
        $action = "index";
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