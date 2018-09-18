<?php

    namespace controllers;
    use Models\Blog;

    class BlogController {

        // 点赞
        public function agreements(){

            $id = $_GET['id'];
            // 判断登陆
            if(!isset($_SESSION['id'])){

                echo json_encode([
                    'status_code' => '403',
                    'message' => '必须先登录'
                ]);
                exit;
            }

            // 点赞
            $model = new \Models\Blog;
            $ret = $model->agree($id);

            if($ret){

                echo json_encode([
                    'status_code' => '200',
                ]);
                exit;
            }else{
                
                echo json_encode([
                    'status_code' => '403',
                    'message' => '已经点赞过了'
                ]);
                exit;
            }
        }

        // 获取点赞的用户
        public function agreements_list(){

            $id = $_GET['id'];
            $model = new \Models\Blog;
            $data = $model->agreeList($id);

            echo json_encode([
                'status_code'=>200,
                'data'=>$data
            ]);
        }

        public function contents(){

            $id = $_GET['id'];

            $model = new Blog;
            $blog = $model->find($id);

            view('blog.content',['blog'=>$blog]);
        }

        public function index(){

            $blog = new Blog;
            $dataArr = $blog->search();
            
            view("blog.index",$dataArr);
        }

        public function content_to_html(){

            $blog = new Blog;

            $blog->content2html();
        }

        public function index2html(){

            $blog = new Blog;
            $blog->index2html();
        }

        public function update_display(){
            
            $id = (int)$_GET['id'];

            $blog = new Blog;
            $display = $blog->getDisplay($id);

            echo json_encode([
                'display'=>$display,
                'email'=>isset($_SESSION['email']) ? $_SESSION['email'] : ''
            ]);

            // $redis->set("library","predis");

            // $retavl = $redis->get("library");
            // echo $retavl;
            // $redis->setex("str",10,"bax");

            // $redis->setnx("foo",12);
            // $redis->setnx("foo",34);

            // $redis->del("foo");
            // echo '<br>'.$redis->type("library");
        }

        public function displayToDb(){

            $blog = new Blog;
            $blog->displayToDb();
        }

        public function create(){

            view("blog.create");
        }

        public function store(){

            $title = $_POST['title'];
            $content = $_POST['content'];

            $blog = new Blog;

            $id = $blog->add($title,$content);

            $blog->makeHtml($id);

            message("发表成功",2,"/blog/index");
        }

        public function delete()
        {
            $id = $_POST['id'];

            $blog = new Blog;
            $blog->delete($id);

            $blog->delHtml($id);

            message('删除成功',2,'/blog/index');

        }

        public function edit(){

            $id = $_POST['id'];

            $blog = new Blog;

            $data = $blog->find($id);

            view("blog.edit",['data'=>$data]);
        }

        public function update(){

            $title = $_POST['title'];
            $content = $_POST['content'];
            $id = $_POST['id'];

            $blog = new Blog;

            $blog->update($title,$content,$id);

            $blog->makeHtml($id);

            message('修改成功！', 1, '/blog/index');
        }
    }