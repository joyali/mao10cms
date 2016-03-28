<?php

require dirname(__FILE__) . '/../../../do/functions.php';
$event = json_decode(file_get_contents('php://input'));

// 对异步通知做处理
if (!isset($event->type)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
    exit("fail");
}
switch ($event->type) {
    case "charge.succeeded":
        $order_no = $event->data->object->order_no;
        $total_fee = $event->data->object->amount/100;
        $cash_id = $redis->zscore('cash:orderno',$order_no);
        $redis->hset('cash:'.$cash_id,'status',2);
        $user_id = $redis->hget('cash:'.$cash_id,'user_id');
		$redis->hset('user:'.$user_id,'cash',maoo_user_cash($user_id)+$total_fee);
		$redis->sadd('cash_id',$cash_id);
        
        //print_r($event->data->object);
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        break;
    case "refund.succeeded":
        // 开发者在此处加入对退款异步通知的处理代码
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
        break;
    default:
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
        break;
}