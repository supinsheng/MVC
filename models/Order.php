<?php

namespace Models;
use PDO;

class Order extends Base {

    public function status($sn){

        $stmt = self::$pdo->prepare("SELECT status FROM orders WHERE sn=?");
        $stmt->execute([$sn]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    // 下订单
    public function create($money) {

        $flake = new \Libs\Snowflake(1023);

        $stmt = self::$pdo->prepare("INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)");

        $stmt->execute([$_SESSION['id'],$money,$flake->nextId()]);
    }

    public function search(){

        $where = 'user_id='.$_SESSION['id'];

        $odby = 'created_at';
        $odway = 'desc';

        $perpage = 3;
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;

        $offset = ($page-1) * $perpage;

        $stmt = self::$pdo->prepare("select count(*) from orders where $where");

        $stmt->execute();

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
    

        $stmt = self::$pdo->prepare("select * from orders where $where ORDER BY $odby $odway LIMIT $offset,$perpage");
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['btns'=>$btns,'data'=>$data];
    }

    public function findSn($sn){

        $stmt = self::$pdo->prepare("SELECT * FROM orders where sn = ?");

        $stmt->execute([$sn]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setPaid($sn){

        $stmt = self::$pdo->prepare("UPDATE orders SET status=1,pay_time=now() WHERE sn=?");
        return $stmt->execute([$sn]);
    }
}