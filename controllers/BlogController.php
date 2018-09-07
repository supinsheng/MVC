<?php

    namespace controllers;
    use Models\Blog;

    class BlogController {

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