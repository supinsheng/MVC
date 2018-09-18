<?php

namespace controllers;

class CommentController {

    public function comment_list(){

        $id = $_GET['id']; 

        $model = new \Models\Comment;

        $data = $model->getComments($id);

        echo json_encode([
            'status_code'=>200,
            'data'=>$data
        ]);
    }

    public function comments(){

        if(!isset($_SESSION['id'])){

            echo json_encode([
                'status_code'=>'401',
                'message'=>'必须先登录!'
            ]);
            exit;
        }

        // 接收原始数据,json数据只能这样接收
        $data = file_get_contents('php://input');
        // 转成数组
        $_POST = json_decode($data,true);

        $content = e($_POST['content']);
        $blog_id = $_POST['blog_id'];

        $model = new \Models\Comment;
        $model->add($content,$blog_id);

        echo json_encode([
            'status_code'=>'200',
            'message'=>'发表成功!',
            'data'=>[
                'content'=>$content,
                'avatar'=>$_SESSION['avatar'],
                'email'=>$_SESSION['email'],
                'created_at'=>date('Y-m-d H:i:s')
            ]
        ]);
    }
}