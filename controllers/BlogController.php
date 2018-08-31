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

            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            $id = (int)$_GET['id'];
            $key = "blog-{$id}";
            
            if($redis->hexists("blog_displays",$key)){

                $newNum = $redis->hincrby("blog_displays",$key,1);
                echo $newNum;
            }else {
                
                $blog = new Blog;

                $display = $blog->getDisplay($id);

                $display++;
                $redis->hset("blog_displays",$key,$display);
                echo $display;
            }

            // $redis->set("library","predis");

            // $retavl = $redis->get("library");
            // echo $retavl;
            // $redis->setex("str",10,"bax");

            // $redis->setnx("foo",12);
            // $redis->setnx("foo",34);

            // $redis->del("foo");
            // echo '<br>'.$redis->type("library");
        }
    }