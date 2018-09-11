<?php

    define("ROOT",dirname(__FILE__)."/../");
    require(ROOT.'vendor/autoload.php');


    

    // 设置 SESSION 保存
    ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
    ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=1');  // 设置 redis 服务器的地址、端口、使用的数据库
    ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期
    session_start();

    // $cURL = curl_init();
    // $url  = 'http://www.51-n.com/';
    // curl_setopt_array(
    //         $cURL,
    //         array(
    //                 CURLOPT_CAINFO => 'c:/wamp64/bin/php/php7.1.16/extras/ssl/cacert.pem',
    //                 CURLOPT_URL => $url,        
    //                 CURLOPT_REFERER => $url,
    //                 CURLOPT_AUTOREFERER => true,
    //                 CURLOPT_RETURNTRANSFER => true,
    //                 CURLOPT_SSL_VERIFYPEER => false,
    //                 CURLOPT_SSL_VERIFYHOST => false,
    //                 CURLOPT_CONNECTTIMEOUT => 1,
    //                 CURLOPT_TIMEOUT => 30,
    //                 CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36'
    //         )
    // );

    // 如果用户以 POST 方式访问网站时，需要验证令牌
    // if($_SERVER['REQUEST_METHOD'] == 'POST')
    // {
    //     if(!isset($_POST['_token']))
    //         die('违法操作！');

    //     if($_POST['_token'] != $_SESSION['token'])
    //         die('违法操作！');
    // }

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

    function e($content){

        return htmlspecialchars($content);
    }

    function hpe($content)
    {
        // 一直保存在内存中（直到脚本执行结束）
        static $purifier = null;
        // 只有第一次调用时才会创建新的对象
        if($purifier === null)
        {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'utf-8');
            $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
            $config->set('Cache.SerializerPath', ROOT.'cache');
            $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
            $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
            $config->set('AutoFormat.AutoParagraph', TRUE);
            $config->set('AutoFormat.RemoveEmpty', TRUE);
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier->purify($content);
    }

    function csrf(){

        if(!isset($_SESSION['token'])){

            $token = md5( rand(1,99999).microtime() );
            $_SESSION['token'] = $token;
        }

        return $_SESSION['token'];
    }

    function csrf_field(){

        $csrf = isset($_SESSION['token']) ? $_SESSION['token'] : csrf();
        echo "<input type='hidden' name='_token' value='{$csrf}'>";
    }