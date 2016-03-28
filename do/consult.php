<?php  
require 'functions.php';
if(maoo_user_id()) :
	if($_POST['page']['content']!='' && $_POST['page']['pro_id']>0) :
		$_POST['page']['date'] = strtotime("now");
		$id = $redis->incr('consult_id_incr');
		if($redis->hget('user:'.maoo_user_id(),'user_level')==10) :
			$_POST['page']['type'] = 2;
		else :
			$_POST['page']['type'] = 1;
			$_POST['page']['buyer_id'] = maoo_user_id();
		endif;
		$_POST['page']['user_id'] = maoo_user_id();
		$redis->sadd('consult_id:'.$_POST['page']['pro_id'].':'.$_POST['page']['buyer_id'],$id);
		$redis->hmset('consult:'.$id,$_POST['page']);
		if($_POST['page']['type']==1) :
			$redis->zadd('consult_group',$_POST['page']['date'],$_POST['page']['pro_id'].':'.$_POST['page']['buyer_id']);
			$url = $redis->get('site_url').'?m=pro&a=single&showconsult=1&id='.$_POST['page']['pro_id'];
		else :
			$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>maoo_user_id())).'">'.maoo_user_display_name(maoo_user_id()).'</a> 在商品 <a href="'.maoo_url('post','single',array('id'=>$pid)).'">'.$redis->hget('post:'.$pid,'title').'</a> 中回复了你的咨询：</h4>'.$_POST['page']['content'];
			maoo_add_message(maoo_user_id(),$_POST['page']['buyer_id'],$text);
			$url = $redis->get('site_url').'?m=admin&a=consult&showconsult=1&done=回复成功';
		endif;
	else :
		$url = $redis->get('site_url').'?m=pro&a=single&showconsult=1&done=内容不可为空&id='.$_POST['page']['pro_id'];
	endif;
else :
	$url = $redis->get('site_url').'?m=user&a=login&done=请先登录';
endif;
//Header("Location:$url");
?>
<!DOCTYPE html><html lang="zh-CN"><meta http-equiv="refresh" content="0;url=<?php echo $url; ?>"><head><meta charset="utf-8"><title>Mao10CMS</title></head><body></body></html>