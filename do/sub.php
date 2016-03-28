<?php
require 'functions.php';
if($_GET['id']>0 && maoo_user_id()) : 
	$id = $_GET['id'];
	$user_id = maoo_user_id();
	if($redis->hget('topic:'.$id,'author')!=$user_id) :
		$count = $redis->hget('topic:'.$id,'sub_count')+1;
		$redis->multi();
		$redis->zadd('user_sub_topic_id:'.$user_id,strtotime("now"),$id);
		$redis->sadd('topic:sub_user:'.$id,$user_id);
		$redis->hset('topic:'.$id,'sub_count',$count);
		$redis->exec();
	endif;
endif;
