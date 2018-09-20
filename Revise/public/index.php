<?php

define("ROOT",dirname(__FILE__)."/../");  

function autoload($class){

    $path = str_replace('\\','/',$class);
    
    require(ROOT.$path.".php");
}

spl_autoload_register("autoload");

if(isset($_SERVER['PATH_INFO'])){

    $data = explode('/',$_SERVER['PATH_INFO']);
    
    $controller = $data[1]."Controller";
    $active = $data[2];
}else {

    $controller = "IndexController";
    $active = "index";
}

$fullController = "Controllers\\".$controller;

$c = new $fullController;
$c->$active();

function view($viewFileName,$data=[]){

    extract($data);

    $path = str_replace('.','/',$viewFileName).'.html';

    require(ROOT.'views/'.$path);
}

function back(){

    header("Location:".$$_SERVER['HTTP_REFERER']);
}