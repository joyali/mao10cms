<?php
require 'functions.php';
if(maoo_user_id()>0) :
	$user_id = maoo_user_id();
	if($_POST['text'] && $_POST['pid']>0) :
		$pid = $_POST['pid'];
        $content = str_replace("\n","<br />",maoo_remove_html($_POST['text'],'all'));
        //@ start
        $content = str_replace('@',' @',$content);
        $content_array = explode(' ',$content);
        foreach($content_array as $val) :
            $content_s = strstr($val, '@');
            $to_user = substr($content_s, 1);
            if($to_user) :
                if($redis->zscore('user_id_name',$to_user)>0) :
                    $idx = $redis->zscore('user_id_name',$to_user);
                elseif($redis->zscore('user_id_display_name',$to_user)>0) :
                    $idx = $redis->zscore('user_id_display_name',$to_user);
                endif;
                if($idx) :
				    $content_s2 .= '<a href="'.maoo_url('user','index',array('id'=>$idx)).'">'.$content_s.'</a> ';
                    $text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$user_id)).'">'.maoo_user_display_name($user_id).'</a> 在文章 <a href="'.maoo_url('post','single',array('id'=>$pid)).'">'.$redis->hget('post:'.$pid,'title').'</a> 中@了你：</h4>'.$content;
                    maoo_add_message($user_id,$idx,$text);
                else :
                    $content_s2 .= $val.' ';
                endif;
            else :
                $content_s2 .= $val.' ';
            endif;
        endforeach;
        //@ end
		$comment['content'] = $content_s2;
		$comment['type'] = 'post';
		$comment['post'] = $pid;
		if($_POST['parent']>0) :
			$comment['parent'] = $_POST['parent'];
		endif;
		$comment['author'] = $user_id;
		$comment['date'] = strtotime("now");
		$id = $redis->incr('comment_id_incr');
		$redis->hmset('comment:'.$id,$comment);
		if($_POST['parent']>0) :
			$redis->sadd('comment_child_id:'.$comment['parent'],$id);
			$author = $redis->hget('comment:'.$comment['parent'],'author');
			$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$user_id)).'">'.maoo_user_display_name($user_id).'</a> 在文章 <a href="'.maoo_url('post','single',array('id'=>$pid)).'">'.$redis->hget('post:'.$pid,'title').'</a> 中回复了你：</h4>'.$comment['content'];
			maoo_add_message($user_id,$author,$text);
		else :
			$redis->sadd('post_comment_id:'.$pid,$id);
			$author = $redis->hget('post:'.$pid,'author');
			$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$user_id)).'">'.maoo_user_display_name($user_id).'</a> 评论了你的文章 <a href="'.maoo_url('post','single',array('id'=>$pid)).'">'.$redis->hget('post:'.$pid,'title').'</a> ：</h4>'.$comment['content'];
			maoo_add_message($user_id,$author,$text);
		endif;
		$redis->sadd('comment_id',$id);
		$redis->sadd('user_comment_id:'.$user_id,$id);
		echo maoo_comment_json($pid,'post');
	endif;
endif;
if($_POST['del']>0 && $_POST['pid']>0 && $redis->hget('user:'.maoo_user_id(),'user_level')>0) :
	$pid = $_POST['pid'];
	$comment = $_POST['del'];
	$user_id = maoo_user_id();
	$author = $redis->hget('comment:'.$comment,'author');
	$delete = false;
	if($redis->hget('user:'.$user_id,'user_level')>7) :
		$delete = true;
	elseif($author==$user_id) :
		$delete = true;
	endif;
	if($delete) :
		$db_child = $redis->sort('comment_child_id:'.$comment,array('sort'=>'desc','limit'=>array(0,100)));
		foreach($db_child as $comment_child) :
			$redis->srem('comment_id',$comment_child);
			$redis->srem('user_comment_id:'.$user_id,$comment_child);
			$redis->del('comment:'.$comment_child);
		endforeach;
		$redis->srem('comment_id',$comment);
		$redis->srem('user_comment_id:'.$user_id,$comment);
		if($redis->hget('comment:'.$comment,'parent')>0) :
			$comment_parent = $redis->hget('comment:'.$comment,'parent');
			$redis->srem('comment_child_id:'.$comment_parent,$comment);
		else :
			$redis->srem('post_comment_id:'.$pid,$comment);
			$redis->del('comment_child_id:'.$comment);
		endif;
		$redis->del('comment:'.$comment);
	endif;
	echo maoo_comment_json($pid,'post');
endif;
if($_GET['pid']>0) :
		$pid = $_GET['pid'];
		echo maoo_comment_json($pid,'post');
endif;
?>
