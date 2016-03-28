<?php
require 'functions.php';
if($_GET['id']>0 && maoo_user_id()) : 
	$id = $_GET['id'];
	$user_id = maoo_user_id();
	$count = $redis->hget('topic:'.$id,'sub_count')-1;
	if($count<1) {
		$count = 0;	
	};
	$redis->multi();
	$redis->zrem('user_sub_topic_id:'.$user_id,$id);
	$redis->srem('topic:sub_user:'.$id,$user_id);
	$redis->hset('topic:'.$id,'sub_count',$count);
	$redis->exec();
endif;
