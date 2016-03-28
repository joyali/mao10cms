<?php
/**
 * Ping++ Server SDK
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写, 并非一定要使用该代码。
 * 该代码仅供学习和研究 Ping++ SDK 使用，只是提供一个参考。
 */
require dirname(__FILE__) . '/../../../../do/functions.php';
require dirname(__FILE__) . '/../init.php';

// api_key、app_id 请从 [Dashboard](https://dashboard.pingxx.com) 获取
$api_key = $redis->hget('payset','pingxx_key');
$app_id = $redis->hget('payset','pingxx_id');

// 此处为 Content-Type 是 application/json 时获取 POST 参数的示例
$input_data = json_decode(file_get_contents('php://input'), true);
if (empty($input_data['channel']) || empty($input_data['amount'])) {
    echo 'channel or amount is empty';
    exit();
}
$channel = strtolower($input_data['channel']);
$amount = $input_data['amount'];
$orderNo = substr(md5(time()), 0, 12);

/**
 * $extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array()。
 * 以下 channel 仅为部分示例，未列出的 channel 请查看文档 https://pingxx.com/document/api#api-c-new
 */
$extra = array();
switch ($channel) {
    case 'alipay_wap':
        $extra = array(
            'success_url' => $redis->get('site_url').'/do/pay-return.php',
            'cancel_url' => $redis->get('site_url')
        );
        break;
    case 'alipay_pc_direct':
        $extra = array(
            'success_url' => $redis->get('site_url').'/do/pay-return.php'
        );
        break;
    case 'bfb_wap':
        $extra = array(
            'result_url' => 'http://www.mao10.com',
            'bfb_login' => true
        );
        break;
    case 'upacp_pc':
        $extra = array(
            'result_url' => $redis->get('site_url').'/do/pay-return.php'
        );
        break;
    case 'upacp_wap':
        $extra = array(
            'result_url' => $redis->get('site_url').'/do/pay-return.php'
        );
        break;
    case 'wx_pub':
        $extra = array(
            'open_id' => 'openidxxxxxxxxxxxx'
        );
        break;
    case 'wx_pub_qr':
        $extra = array(
            'product_id' => 'Productid'
        );
        break;
    case 'yeepay_wap':
        $extra = array(
            'product_category' => '1',
            'identity_id'=> 'your identity_id',
            'identity_type' => 1,
            'terminal_type' => 1,
            'terminal_id'=>'your terminal_id',
            'user_ua'=>'your user_ua',
            'result_url'=>$redis->get('site_url').'/do/pay-return.php'
        );
        break;
    case 'jdpay_wap':
        $extra = array(
            'success_url' => $redis->get('site_url').'/do/pay-return.php',
            'fail_url'=> $redis->get('site_url'),
            'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf'
        );
        break;
}

if($amount>0) :
        //必填
				$id = $redis->incr('cash:id_incr');
				$redis->sadd('cash:user_id:'.maoo_user_id(),$id);
				$cash['out_trade_no'] = $orderNo;
				$cash['user_id'] = maoo_user_id();
				$cash['status'] = 1;
				$cash['total'] = $amount/100;
				$cash['des'] = '充值';
				$cash['date'] = strtotime("now");
                $redis->zadd('cash:orderno',$id,$orderNo);
				$redis->hmset('cash:'.$id,$cash);
endif;
// 设置 API Key
\Pingpp\Pingpp::setApiKey($api_key);
try {
    $ch = \Pingpp\Charge::create(
        array(
            'subject'   => '账户充值',
            'body'      => '账户充值',
            'amount'    => $amount,
            'order_no'  => $orderNo,
            'currency'  => 'cny',
            'extra'     => $extra,
            'channel'   => $channel,
            'client_ip' => $_SERVER['REMOTE_ADDR'],
            'app'       => array('id' => $app_id)
        )
    );
    echo $ch;
} catch (\Pingpp\Error\Base $e) {
    header('Status: ' . $e->getHttpStatus());
    // 捕获报错信息
    echo $e->getHttpBody();
}
