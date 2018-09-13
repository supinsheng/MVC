<?php

namespace controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestController {

    // 生成 excel 并下载
    public function makeExcel(){

        // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1','标题');
        $sheet->setCellValue('B1','内容');
        $sheet->setCellValue('C1','发表时间');
        $sheet->setCellValue('D1','浏览量');

        $blog = new \Models\Blog;
        $data = $blog->getNew();

        $i=2;
        foreach($data as $v){

            $sheet->setCellValue('A'.$i,$v['title']);
            $sheet->setCellValue('B'.$i,$v['content']);
            $sheet->setCellValue('C'.$i,$v['created_at']);
            $sheet->setCellValue('D'.$i,$v['display']);

            $i++;
        }

        $date = date('Ymd');

        // 生成 excel 文件
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT . 'excel/'.$date.'.xlsx');

        // 调用 header 函数设置协议头，告诉浏览器开始下载文件// 下载文件路径
        $file = ROOT . 'excel/'.$date.'.xlsx';
        // 下载时文件名
        $fileName = '最新的20条日志-'.$date.'.xlsx';

        // 告诉浏览器这是一个二进程文件流    
        Header ( "Content-Type: application/octet-stream" ); 
        // 请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        // 告诉浏览器文件尺寸    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        // 开始下载，下载时的文件名
        Header ( "Content-Disposition: attachment; filename=" . $fileName );

        // 读取服务器上的一个文件并以文件流的形式输出给浏览器
        readfile($file);
    }

    // 导出 Excel 文件
    public function testExc(){

        $blog = new \Models\Blog;
        $data = $blog->getNew();

        // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();
        
        // 设置第1行内容
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '内容');
        $sheet->setCellValue('C1', '发表时间');
        $sheet->setCellValue('D1', '浏览量');

        $i=2;
        foreach($data as $v){

            $sheet->setCellValue('A'.$i, $v['title']);
            $sheet->setCellValue('B'.$i, $v['content']);
            $sheet->setCellValue('C'.$i, $v['created_at']);
            $sheet->setCellValue('D'.$i, $v['display']);

            $i++;
        }

        // 生成 Excel 文件
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT . 'hello-world.xlsx');
    }

    public function uploadbig(){

        $count = $_POST['count'];
        $i = $_POST['i'];
        $size = $_POST['size'];
        $name = 'big_'.$_POST['img_name'];
        $img = $_FILES['img'];

        move_uploaded_file( $img['tmp_name'] , ROOT.'public/tmp/'.$i);

        $redis = \Libs\Redis::getInstance();

        $uploadedCount = $redis->incr($name);
       
        if($uploadedCount == $count){

            $fp = fopen(ROOT."public/uploads/big/".$name.".png",'a');

            for($i=0;$i<$count;$i++){

                fwrite($fp,file_get_contents(ROOT.'public/tmp/'.$i));

                unlink(ROOT.'public/tmp/'.$i);
            }
            // 关闭文件
            fclose($fp);
            // 从 redis 中删除这个文件对应的编号这个变量
            $redis->del($name);
        }else {
            var_dump($count);
            exit;
        }
    }

    public function douploadm(){
        
        $root = ROOT . "public/uploads/";
        $date = date("Ymd");

        if(!is_dir($root.$date)){

            mkdir($root.$date,0777);
        }

        foreach($_FILES['images']['name'] as $k=>$v){

            $ext = strrchr($v,'.');

            $name = md5(time().rand(1,9999));

            $fullName = $root.$date.'/'.$name.$ext;

            move_uploaded_file($_FILES['images']['tmp_name'][$k],$fullName);

            echo $fullName."<hr>";
        }
    }

    public function doupload(){

        $upload = \Libs\Uploader::make();

        // 参数一、表单中文件名
        // 参数二、保存到二级目录名
        $path = $upload->upload('image', 'avatar');
    }

    public function testLog(){

        $log = new \Libs\Log('email');

        $log->log('日志内容~');
    }

    public function testPHP(){

        phpinfo();
    }
}