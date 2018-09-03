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
            echo $blog->getDisplay($id);

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
    }