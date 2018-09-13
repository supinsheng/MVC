<?php

    namespace Models;
    use PDO;

    class Blog extends Base {

        public function getNew()
        {
            $stmt = self::$pdo->query('SELECT * FROM blog ORDER BY id DESC LIMIT 20');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 添加静态页
        public function makeHtml($id){

            $blog = $this->find($id);

            ob_start();

            view("blog.content",['blog'=>$blog]);

            $content = ob_get_clean();

            file_put_contents(ROOT."public/contents/".$id.".html",$content);
        }

        // 删除静态页
        public function delHtml($id){

            @unlink(ROOT."public/contents/".$id.".html");
        }

        public function search(){

            $where = 'user_id='.$_SESSION['id'];

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

            $stmt = self::$pdo->prepare("select count(*) from blog where $where");

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
        

            $stmt = self::$pdo->prepare("select * from blog where $where ORDER BY $odby $odway LIMIT $offset,$perpage");
            $stmt->execute($value);

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['btns'=>$btns,'data'=>$data];
        }

        public function content2html(){

            $stmt = self::$pdo->query("select * from blog limit 10");
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

        public function index2html(){

            $stmt = self::$pdo->query("SELECT * FROM blog ORDER BY id DESC LIMIT 20 ");
            $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            ob_start();

            view("index.index",['blogs'=>$blogs]);

            $str = ob_get_contents();

            file_put_contents(ROOT."public/index.html",$str);
            ob_clean();
        }

        public function getDisplay($id){

            $redis = \Libs\Redis::getInstance();

            $key = "blog-{$id}";
            
            if($redis->hexists("blog_displays",$key)){

                $newNum = $redis->hincrby("blog_displays",$key,1);
                return $newNum;
            }else {
                
                $stmt = self::$pdo->prepare("SELECT display FROM blog WHERE id = ?");
                $stmt->execute([$id]);
                $display = $stmt->fetch(PDO::FETCH_COLUMN);

                $display++;
                $redis->hset("blog_displays",$key,$display);
                return $display;
            }
        }

        public function displayToDb(){

            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            $data = $redis->hgetall("blog_displays");

            foreach($data as $k=>$v){

                $id = str_replace("blog-","",$k);

                $stmt = self::$pdo->prepare("UPDATE blog SET display={$v} WHERE id= ? ");
                $stmt->execute([$id]);
            }
        }

        public function add($title,$content){

            $stmt = self::$pdo->prepare("INSERT INTO blog(title,content,user_id,created_at) VALUES(?,?,?,now())");

            $ret = $stmt->execute([
                $title,
                $content,
                $_SESSION['id']
            ]);

            if(!$ret){

                echo "失败！";
                $error = $stmt->errorInfo();

                echo "<pre>";
                var_dump($error);
                exit;
            }

            return self::$pdo->lastInsertId();
        }

        public function delete($id)
        {
            // 只能删除自己的日志
            $stmt = self::$pdo->prepare('DELETE FROM blog WHERE id = ? AND user_id=?');
            $stmt->execute([
                $id,
                $_SESSION['id'],
            ]);
        }

        public function find($id){

            $stmt = self::$pdo->prepare("SELECT * FROM blog WHERE id = ?");

            $stmt->execute([$id]);

            $blog = $stmt->fetch(PDO::FETCH_ASSOC);

            return $blog;
        }

        public function update($title,$content,$id){

            $stmt = self::$pdo->prepare("UPDATE blog SET title=?,content=? WHERE id=?");

            $ret = $stmt->execute([$title,$content,$id]);
        }
    }
