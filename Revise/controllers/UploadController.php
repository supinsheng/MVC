<?php

namespace Controllers;

class UploadController {

    public function index(){

        view('upload.index');
    }

    public function save(){

        $image = $_FILES['image'];

        $ext = strrchr($image['name'],'.');
        
        $array = ['.jpg','.jpeg','.bmp','.gif','.png','.JPG','.JPEG','.BMP','.GIF','.PNG'];

        if(in_array($ext,$array)){

            $dir = date('Ymd');

            if(!is_dir(ROOT."public/uploads/".$dir)){

                mkdir(ROOT."public/uploads/".$dir,0777,true);
            }

            $dir = ROOT."public/uploads/".$dir;

            $name = time().$ext;

            if(move_uploaded_file($image['tmp_name'],$dir."/".$name)){

                echo "图片上传成功！";
            }
        }else {
            die("图片格式不正确！");
        }
    }

    public function saveBig(){

        $count = $_POST['count'];
        $i = $_POST['i'];
        $name = $_POST['img_name'];
        $size = $_POST['size'];
        $img = $_FILES['img'];

        move_uploaded_file($img['tmp_name'],ROOT.'public/tmp/'.$i);

        if($i+1 == $count){

            $fp = fopen(ROOT."public/uploads/big/".$name.".png",'a');

            for($i=0;$i<$count;$i++){

                fwrite($fp,file_get_contents(ROOT.'public/tmp/'.$i));
                @unlink(ROOT.'public/tmp/'.$i);
            }

            fclose($fp);
        }
    }
}