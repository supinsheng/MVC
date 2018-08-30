<?php

    namespace controllers;
    use Models\Blog;
    use PDO;

    class BlogController {

        public function index(){

            $pdo = new PDO("mysql:host=localhost;dbname=mvc","root","123");
            $pdo->exec("set names utf8");

            $where = 1;

            $value = [];

            if(isset($_GET['keyword']) && $_GET['keyword']){

                $where .= " and (title LIKE ? OR content LIKE ?)";
                $value[] = '%'.$_GET['keyword'].'%';
                $value[] = '%'.$_GET['keyword'].'%';
            }

            if(isset($_GET['start_date']) && $_GET['start_date']){

                $where .= " AND created_at >= ?";
                $value[] = $_GET['start_date'];
            }

            if(isset($_GET['end_date']) && $_GET['end_date']){

                $where .= " and created_at <=?";
                $value[] = $_GET['end_date'];
            }

            $odby = 'created_at';
            $odway = 'desc';

            if(isset($_GET['odby']) && $_GET['odby'] == 'display')
            {
                $odby = 'display';
            }

            if(isset($_GET['odway']) && $_GET['odway'] == 'asc')
            {
                $odway = 'asc';
            }

            if(isset($_GET['odway']) && $_GET['odway'] == 'desc')
            {
                $odway = 'desc';
            }

            $perpage = 3;
            $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;

            $offset = ($page-1) * $perpage;

            $stmt = $pdo->prepare("select count(*) from blog where $where");

            $stmt->execute($value);

            $count = $stmt->fetch(PDO::FETCH_COLUMN);

            $pageCount = ceil( $count / $perpage );

            $btns = '';
            for($i=1; $i<=$pageCount; $i++)
            {
                // 先获取之前的参数
                $params = getUrlParams(['page']);

                $class = $page==$i ? 'active' : '';
                $btns .= "<a class='$class' href='?{$params}page=$i'> $i </a>";
            }
        

            $stmt = $pdo->prepare("select * from blog where $where ORDER BY $odby $odway LIMIT $offset,$perpage");
            $stmt->execute($value);

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            view("blog.index",['data'=>$data,'btns'=>$btns]);
        }

        public function content_to_html(){

            $pdo = new PDO("mysql:host=localhost;dbname=mvc","root","123");
            $pdo->exec("set NAMES utf8");

            $stmt = $pdo->query("select * from blog limit 10");
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_start();
            foreach($blogs as $v){

                view("blog.content",["blog"=>$v]);

                $str = ob_get_contents();
               
                file_put_contents(ROOT.'public/contents/'.$v['id'].'.html', $str);

                // 清空缓冲区
                ob_clean();    
            }
        }
    }