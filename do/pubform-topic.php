<?php
require 'functions.php';
if(maoo_user_id()>0) :
	$user_id = maoo_user_id();
	$_POST['page']['title'] = maoo_remove_html($_POST['page']['title'],'all');
	$_POST['page']['fmimg'] = maoo_remove_html($_POST['page']['fmimg'],'all');
	$_POST['page']['content'] = maoo_str_replace_base64(maoo_remove_html($_POST['page']['content'],'all'));
	if($_POST['id']>0) : //编辑
		$id = $_POST['id'];
		if($redis->hget('topic:'.$id,'author')==$user_id) :
			if($_POST['step']==2) :
				if($_POST['permission']>0) :
					$redis->hset('topic:'.$id,'permission',$_POST['permission']);
					if($redis->smembers('con_topic_post_id:'.$id)) :
						//投稿处理
						if($_POST['permission']==4) :
							//全部退回
						elseif($_POST['permission']==2) :
							//选择性退回
						elseif($_POST['permission']!=3) :
							//全部通过
						endif;
					endif;
					$url = $redis->get('site_url').'?m=post&a=topicset&step=2&id='.$id.'&done=修改权限成功';
				else :
					$url = $redis->get('site_url').'?m=post&a=topicset&step=2&id='.$id.'&done=参数错误';
				endif;
			elseif($_POST['step']==3) :
				//黑名单功能预留
			else :
				if($_POST['page']['fmimg'] && $_POST['page']['title']) :
					if($redis->hget('topic:'.$id,'term')!=$_POST['page']['term'] && $_POST['page']['term']>0) :
						$redis->srem('term_topic_id:'.$redis->hget('topic:'.$id,'term'),$id);
						$redis->sadd('term_topic_id:'.$_POST['page']['term'],$id);
					endif;
					$redis->hmset('topic:'.$id,$_POST['page']);
					$url = $redis->get('site_url').'?m=post&a=topicset&step=1&id='.$id.'&done=编辑成功';
				else :
					$url = $redis->get('site_url').'?m=post&a=publishtopic&id='.$id.'&done=必须设置话题名称和封面';
				endif;
			endif;
		else :
			$url = $redis->get('site_url').'?m=post&a=topic&id='.$id.'&done=你没有权限编辑此话题';
		endif;
	else : //新建
		if($redis->hget('user:'.$user_id,'user_level')==10) :
			$pubcan = 1;
		else :
			if($redis->get('topic_permission')!=2) :
				if($redis->get('topic_number')>0) :
					if($redis->scard('user_topic_id:'.$user_id)<$redis->get('topic_number')) :
						$pubcan = 1;
					else :
						$error = '您最多只能建立'.$redis->get('topic_number').'个话题';
					endif;
				else :
					$pubcan = 1;
				endif;
			else :
				$error = '只有管理员可以建立话题';
			endif;
		endif;
		if($pubcan==1) :
			if($_POST['page']['fmimg'] && $_POST['page']['title']) :
				$id = $redis->incr('topic_id_incr');
				$_POST['page']['date'] = strtotime("now");
				$_POST['page']['author'] = $user_id;
				if($_POST['page']['term']>0) :
					$redis->sadd('term_topic_id:'.$_POST['page']['term'],$id);
				endif;
				$redis->sadd('topic_id',$id);
				$redis->zadd('topic_rank_list',0,$id);
				$redis->sadd('user_topic_id:'.$_POST['page']['author'],$id);
				$redis->hmset('topic:'.$id,$_POST['page']);
				$url = $redis->get('site_url').'?m=user&a=topic&done=新建话题成功';
			else :
				$url = $redis->get('site_url').'?m=post&a=publishtopic&done=必须设置话题名称和封面';
			endif;
		else :
			$url = $redis->get('site_url').'?m=user&a=topic&done='.$error;
		endif;
	endif;
else :
	$url = $redis->get('site_url').'?done=请先登录';
endif;
//Header("Location:$url");
?>
<!DOCTYPE html><html lang="zh-CN"><meta http-equiv="refresh" content="0;url=<?php echo $url; ?>"><head><meta charset="utf-8"><title>Mao10CMS</title></head><body></body></html>
