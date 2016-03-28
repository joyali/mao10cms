<?php
require 'functions.php';
if(maoo_user_id()) :
	$redis->hset('user:'.maoo_user_id(),'add_name',$_POST['page']['add_name']);
    $redis->hset('user:'.maoo_user_id(),'add_province',$_POST['province']);
    $redis->hset('user:'.maoo_user_id(),'add_city',$_POST['city']);
    $redis->hset('user:'.maoo_user_id(),'add_area',$_POST['area']);
    $redis->hset('user:'.maoo_user_id(),'add_address',$_POST['page']['add_address']);
    $redis->hset('user:'.maoo_user_id(),'add_phone',$_POST['page']['add_phone']);
	$url = $redis->get('site_url').'?m=user&a=order&done=默认收货地址设置成功';
else :
	$url = $redis->get('site_url').'?m=user&a=login&done=请先登陆';
endif;
//Header("Location:$url");
?>
<!DOCTYPE html><html lang="zh-CN"><meta http-equiv="refresh" content="0;url=<?php echo $url; ?>"><head><meta charset="utf-8"><title>Mao10CMS</title></head><body></body></html>
