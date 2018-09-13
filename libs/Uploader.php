<?php

namespace Libs;

class Uploader {

    // 不允许被 new 来生成对象
    private function __construct(){}
    
    // 不允许被克隆
    private function __clone(){}
    
    // 保存唯一的对象 (只有静态的属性属于这个类是唯一的)
    private static $_obj = null;

    public static function make(){

        if(self::$_obj == null){

            self::$_obj = new self;
        }

        return self::$_obj;
    }

    private $_root = ROOT . 'public/uploads/';
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];  // 允许上传的扩展名
    private $_maxSize = 1024*1024*1.8; // 最大允许上传的尺寸 1.8M
    private $_file;
    private $_subDir;

    public function upload($name,$subdir){

        $this->_file = $_FILES[$name];
        $this->_subDir = $subdir;

        if(!$this->_checkType()){
            die('图片类型不正确！');
        }

        if(!$this->_checkSize()){
            die('图片最大为1.8M！');
        }

        // 创建目录
        $dir = $this->_makeDir();

        // 生成唯一的名字
        $name = $this->_makeName();

        // 移动图片
        move_uploaded_file($this->_file['tmp_name'],$this->_root.$dir.$name);

        // 返回二级目录的路径
        return $dir.$name;
    }

    // 创建目录
    private function _makeDir(){

        $dir = $this->_subDir.'/'.date('Ymd');

        if(!is_dir($this->_root.$dir)){

            mkdir($this->_root . $dir,0777,true);
        }

        return $dir.'/';
    }

    // 生成唯一的名字
    private function _makeName(){

        $name = md5(time() . rand(1,9999));
        $ext = strrchr($this->_file['name'],'.');
        return $name . $ext;
    }

    private function _checkType()
    {
        
        return in_array($this->_file['type'], $this->_ext);
    }

    private function _checkSize()
    {
        return $this->_file['size'] < $this->_maxSize;
    }
}