<?php
require 'functions.php';
if(maoo_user_id()>0) :
	$user_id = maoo_user_id();
    if($redis->hget('user:'.maoo_user_id(),'user_level')!=10) :
	foreach($_POST['page'] as $page_key=>$page_val) :
        $legal_keys = array('title','content','content2','tags','fmimg','topic','coins');
        if(!in_array($page_key,$legal_keys)) :
            unset($_POST['page'][$page_key]);
        endif;
	endforeach;
    endif;
	$_POST['page']['title'] = maoo_remove_html($_POST['page']['title'],'all');
	$_POST['page']['tags'] = maoo_remove_html($_POST['page']['tags'],'all');
	if($redis->hget('user:'.maoo_user_id(),'user_level')>7) :
        $_POST['page']['content'] = maoo_str_replace_base64($_POST['page']['content']);
	       $_POST['page']['content2'] = maoo_str_replace_base64($_POST['page']['content2']);
    else :
	   $_POST['page']['content'] = maoo_str_replace_base64(maoo_remove_html($_POST['page']['content']));
	   $_POST['page']['content2'] = maoo_str_replace_base64(maoo_remove_html($_POST['page']['content2']));
    endif;
	if($_POST['page']['content2']) :
		if($_POST['page']['coins']>0) :
			//nothing happened
		else :
			$_POST['page']['coins'] = 3;
		endif;
	else :
		unset($_POST['page']['coins']);
	endif;
	$fmimg = $_POST['page']['fmimg'];
	if($_POST['draft']==1) :
		$redis->multi();
		$redis->hset('user_draft_post:'.$user_id,'title',$_POST['page']['title']);
		$redis->hset('user_draft_post:'.$user_id,'content',$_POST['page']['content']);
		$redis->hset('user_draft_post:'.$user_id,'content2',$_POST['page']['content2']);
		$redis->hset('user_draft_post:'.$user_id,'tags',$_POST['page']['tags']);
		$redis->exec();
		$url = $redis->get('site_url').'?m=post&a=publish&done=保存草稿成功';
	elseif($_POST['draft']==2) :
		$redis->multi();
		$redis->hset('user_draft_post:'.$user_id,'title',$_POST['title']);
		$redis->hset('user_draft_post:'.$user_id,'content',$_POST['content']);
		$redis->hset('user_draft_post:'.$user_id,'content2',$_POST['page']['content2']);
		$redis->hset('user_draft_post:'.$user_id,'tags',$_POST['tags']);
		$redis->exec();
	else :
		if($_POST['page']['title'] && $_POST['page']['content'] && $_POST['page']['topic']>0) :
			$_POST['page']['fmimg'] = maoo_remove_html($fmimg,'all');
			if($_POST['id']>0) : //编辑
				$id = $_POST['id'];
				$topic_id = $redis->hget('post:'.$id,'topic');
				if($redis->hget('post:'.$id,'permission')==3) : //投稿扔在审核中
					if($redis->hget('topic:'.$topic_id,'author')==$user_id) : //话题发起者审核文章
						$author = $redis->hget('post:'.$id,'author');
                        if($_POST['page']['rank']=='') :
						  $_POST['page']['rank'] = $redis->hget('user:'.$author,'rank1');
                        endif;
						$_POST['page']['permission'] = 31;
						$redis->hmset('post:'.$id,$_POST['page']);
						if($_POST['page']['topic']) :
							$redis->sadd('topic_post_id:'.$_POST['page']['topic'],$id);
						endif;
						$tags = explode(' ',$_POST['page']['tags']);
						foreach($tags as $tag) :
							if($tag) :
								$redis->sadd('tag_post_id:'.$tag,$id);
							endif;
						endforeach;
						$redis->sadd('post_id',$id);
						$redis->srem('con_topic_post_id:'.$topic_id,$id);
						$redis->sadd('user_post_id:'.$author,$id);
						$redis->zadd('rank_list',$_POST['page']['rank'],$id);
						//统计发文最多的作者
						if($redis->zscore('topic_post_count_to_user:'.$_POST['page']['topic'],$author)>0) :
							$redis->zincrby('topic_post_count_to_user:'.$_POST['page']['topic'],1,$author) ;
						else :
							$redis->zadd('topic_post_count_to_user:'.$_POST['page']['topic'],1,$author);
						endif;
						$url = $redis->get('site_url').'?m=post&a=single&done=审核已通过&id='.$id;
					elseif($redis->hget('post:'.$id,'author')==$user_id) : //投稿者重新编辑文章
						$redis->hmset('post:'.$id,$_POST['page']);
						if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==3 && $redis->hget('topic:'.$_POST['page']['topic'],'author')!=$user_id) : //如果编辑的文章，此时提交的话题需审核投稿，并且当前操作者并非此话题的发起人
							if($topic_id!=$_POST['page']['topic']) : //如果提交的话题与之前投稿话题不同
								$redis->srem('con_topic_post_id:'.$topic_id,$id);
								$redis->sadd('con_topic_post_id:'.$_POST['page']['topic'],$id);
								$redis->del('user_draft_post:'.$user_id); //删除草稿
								$url = $redis->get('site_url').'?m=user&a=index&done=投稿成功';
							else :
								$url = $redis->get('site_url').'?m=user&a=index&done=编辑稿件成功';
							endif;
						else :
							$redis->hdel('post:'.$id,'permission');
							if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==2) :
								if($redis->sismember('topic_partner:'.$_POST['page']['topic'],$user_id) || $redis->hget('topic:'.$_POST['page']['topic'],'author')==$user_id) :
									$pubcan = 1;
								endif;
							elseif($redis->hget('topic:'.$_POST['page']['topic'],'permission')==4) :
								if($redis->hget('topic:'.$_POST['page']['topic'],'author')==$user_id) :
									$pubcan = 1;
								endif;
							else :
								$pubcan = 1;
							endif;
							if($pubcan==1) :
                                if($_POST['page']['rank']=='') :
								    $_POST['page']['rank'] = $redis->hget('post:'.$id,'rank');
                                endif;
								//topic rank核算
								$topic_rank_now = $redis->hget('topic:'.$_POST['page']['topic'],'rank');
								$topic_post_count = $redis->scard('topic_post_id:'.$_POST['page']['topic']);
								$topic_rank_new = round(($topic_rank_now*$topic_post_count+$_POST['page']['rank'])/($topic_post_count+1),0);
								$redis->hset('topic:'.$_POST['page']['topic'],'rank',$topic_rank_new);
								$redis->zrem('topic_rank_list',$_POST['page']['topic']);
								$redis->zadd('topic_rank_list',$topic_rank_new,$_POST['page']['topic']);
								//将文章加入topic列表
								$redis->srem('con_topic_post_id:'.$topic_id,$id);
								$redis->sadd('topic_post_id:'.$_POST['page']['topic'],$id);
								//标签
								$tags = explode(' ',$_POST['page']['tags']);
								foreach($tags as $tag) :
									if($tag) :
										$redis->sadd('tag_post_id:'.$tag,$id);
									endif;
								endforeach;
								$redis->sadd('post_id',$id);
								$redis->zadd('rank_list',$_POST['page']['rank'],$id);
								$redis->del('user_draft_post:'.$user_id); //删除草稿
								$url = $redis->get('site_url').'?m=post&a=single&done=发布成功&id='.$id;
							else :
								$url = $redis->get('site_url').'?m=post&a=edit&done=您没有权限在此话题中发布文章&id='.$id;
							endif;
						endif;
					else :
						$url = $redis->get('site_url').'?done=您没有权限编辑此文章';
					endif;
				//投稿仍在审核中 - 结束
				elseif($redis->hget('post:'.$id,'permission')==31) : //投稿已通过
					if($redis->hget('topic:'.$topic_id,'author')==$user_id) : //话题发起者编辑已投稿文章

						if($redis->hget('post:'.$id,'topic')!=$_POST['page']['topic']) :
							$redis->srem('topic_post_id:'.$redis->hget('post:'.$id,'topic'),$id);
							$redis->sadd('topic_post_id:'.$_POST['page']['topic'],$id);
						endif;
						if($redis->hget('post:'.$id,'tags')!=$_POST['page']['tags']) :
							$tags1 = explode(' ',$redis->hget('post:'.$id,'tags'));
							foreach($tags1 as $tag) :
								if($tag) :
									$redis->srem('tag_post_id:'.$tag,$id);
								endif;
							endforeach;
							$tags = explode(' ',$_POST['page']['tags']);
							foreach($tags as $tag) :
								if($tag) :
									$redis->sadd('tag_post_id:'.$tag,$id);
								endif;
							endforeach;
						endif;
						$redis->hmset('post:'.$id,$_POST['page']);
						$redis->del('user_draft_post:'.$user_id); //删除草稿
						$url = $redis->get('site_url').'?m=post&a=single&done=编辑完成&id='.$id;
					else :
						$url = $redis->get('site_url').'?done=您没有权限编辑此文章'; //此处将来需允许投稿者提交修正版本
					endif;
				else :
					if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==3 && $redis->hget('topic:'.$_POST['page']['topic'],'author')!=maoo_user_id()) : //提交的话题需审核投稿
						if($redis->hget('post:'.$id,'tags')) :
							$tags1 = explode(' ',$redis->hget('post:'.$id,'tags'));
							foreach($tags1 as $tag) :
								if($tag) :
									$redis->srem('tag_post_id:'.$tag,$id);
								endif;
							endforeach;
						endif;
						$redis->srem('topic_post_id:'.$topic_id,$id);
						$redis->srem('post_id',$id);
						$redis->zrem('rank_list',$id);
						$_POST['page']['permission'] = 3;
						$redis->sadd('con_topic_post_id:'.$_POST['page']['topic'],$id);
						$redis->hmset('post:'.$id,$_POST['page']);
						$redis->del('user_draft_post:'.$user_id); //删除草稿
						$url = $redis->get('site_url').'?m=user&a=index&done=投稿成功';
					else :
						if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==2) :
							if($redis->sismember('topic_partner:'.$_POST['page']['topic'],$user_id) || $redis->hget('topic:'.$_POST['page']['topic'],'author')==$user_id) :
								$pubcan = 1;
							endif;
							$vvv = 1;
						elseif($redis->hget('topic:'.$_POST['page']['topic'],'permission')==4) :
							if($redis->hget('topic:'.$_POST['page']['topic'],'author')==$user_id) :
								$pubcan = 1;
							endif;
							$vvv = 2;
						else :
							$pubcan = 1;
							$vvv = 3;
						endif;
						if($pubcan==1) :
                            if($_POST['page']['rank']=='') :
							$_POST['page']['rank'] = $redis->hget('post:'.$id,'rank');
                            endif;
							if($topic_id!=$_POST['page']['topic']) :
								//topic rank核算 - old
								$topic_rank_now_old = $redis->hget('topic:'.$topic_id,'rank');
								$topic_post_count_old = $redis->scard('topic_post_id:'.$topic_id);
								$topic_rank_new_old = round(($topic_rank_now_old*$topic_post_count_old-$_POST['page']['rank'])/($topic_post_count-1),0);
								$redis->hset('topic:'.$topic_id,'rank',$topic_rank_new_old);
								$redis->zrem('topic_rank_list',$topic_id);
								$redis->zadd('topic_rank_list',$topic_rank_new_old,$topic_id);
								//topic rank核算 - new
								$topic_rank_now = $redis->hget('topic:'.$_POST['page']['topic'],'rank');
								$topic_post_count = $redis->scard('topic_post_id:'.$_POST['page']['topic']);
								$topic_rank_new = round(($topic_rank_now*$topic_post_count+$_POST['page']['rank'])/($topic_post_count+1),0);
								$redis->hset('topic:'.$_POST['page']['topic'],'rank',$topic_rank_new);
								$redis->zrem('topic_rank_list',$_POST['page']['topic']);
								$redis->zadd('topic_rank_list',$topic_rank_new,$_POST['page']['topic']);
								//移除、加入
								$redis->srem('topic_post_id:'.$topic_id,$id);
								$redis->sadd('topic_post_id:'.$_POST['page']['topic'],$id);
								//统计发文最多的作者
								if($redis->zscore('topic_post_count_to_user:'.$topic_id,$author)>1) :
									$redis->zincrby('topic_post_count_to_user:'.$topic_id,-1,$author) ;
								else :
									$redis->zrem('topic_post_count_to_user:'.$topic_id,$author);
								endif;
								if($redis->zscore('topic_post_count_to_user:'.$_POST['page']['topic'],$author)>0) :
									$redis->zincrby('topic_post_count_to_user:'.$_POST['page']['topic'],1,$author) ;
								else :
									$redis->zadd('topic_post_count_to_user:'.$_POST['page']['topic'],1,$author);
								endif;
								//统计用户为话题收获的赞数量
								if(maoo_like_count($id)>0) :
									$like_count = maoo_like_count($id);
									$redis->zadd('topic_like_count_to_user:'.$topic_id,-$like_count,$author);
								endif;
							endif;
							//更新文章
							if($redis->hget('post:'.$id,'tags')!=$_POST['page']['tags']) :
								$tags1 = explode(' ',$redis->hget('post:'.$id,'tags'));
								foreach($tags1 as $tag) :
									if($tag) :
										$redis->srem('tag_post_id:'.$tag,$id);
									endif;
								endforeach;
								$tags = explode(' ',$_POST['page']['tags']);
								foreach($tags as $tag) :
									if($tag) :
										$redis->sadd('tag_post_id:'.$tag,$id);
									endif;
								endforeach;
							endif;
                            //topic rank核算 - old
				            $topic_rank_now_old = $redis->hget('topic:'.$topic_id,'rank');
				            $topic_post_count_old = $redis->scard('topic_post_id:'.$topic_id);
				            $topic_rank_new_old = round(($topic_rank_now_old*$topic_post_count_old-$_POST['page']['rank'])/($topic_post_count-1),0);
				            $redis->hset('topic:'.$topic_id,'rank',$topic_rank_new_old);
                            $redis->zadd('topic_rank_list',$topic_rank_new_old,$topic_id);
                            $redis->zadd('rank_list',$_POST['page']['rank'],$id);
							$redis->hmset('post:'.$id,$_POST['page']);
							$redis->del('user_draft_post:'.$user_id); //删除草稿
							$url = $redis->get('site_url').'?m=post&a=single&done=编辑成功&id='.$id;
						else :
							$url = $redis->get('site_url').'?m=post&a=edit&done=您没有权限在此话题中发布文章'.$vvv.'&id='.$id;
						endif;
					endif;
				endif;
			else : //新建
				$id = $redis->incr('post_id_incr');
				$_POST['page']['date'] = strtotime("now");
				$_POST['page']['author'] = $user_id;
                if($_POST['page']['rank']=='') :
				    $_POST['page']['rank'] = $redis->hget('user:'.$_POST['page']['author'],'rank1');
                endif;
                if($redis->hget('user:'.maoo_user_id(),'user_level')>7) :
				    $user_pubcan = $redis->hget('user:'.$user_id,'user_pubbbs_date')+10;
                else :
                    $user_pubcan = $redis->hget('user:'.$user_id,'user_pubbbs_date')+120;
                endif;
				if($user_pubcan<$_POST['page']['date']) :
					if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==3 && $redis->hget('topic:'.$_POST['page']['topic'],'author')!=$_POST['page']['author']) :
						$_POST['page']['permission'] = 3;
						$redis->sadd('user_post_id:'.$_POST['page']['author'],$id);
						$redis->sadd('con_topic_post_id:'.$_POST['page']['topic'],$id);
						$redis->hmset('post:'.$id,$_POST['page']);
						$redis->del('user_draft_post:'.$user_id); //删除草稿
						$redis->hset('user:'.$user_id,'user_pub_date',$_POST['page']['date']);
						$url = $redis->get('site_url').'?m=user&a=index&done=投稿成功';
					else :
						if($redis->hget('topic:'.$_POST['page']['topic'],'permission')==2) :
							if($redis->sismember('topic_partner:'.$_POST['page']['topic'],$_POST['page']['author']) || $redis->hget('topic:'.$_POST['page']['topic'],'author')==$_POST['page']['author']) :
								$pubcan = 1;
							endif;
						elseif($redis->hget('topic:'.$_POST['page']['topic'],'permission')==4) :
							if($redis->hget('topic:'.$_POST['page']['topic'],'author')==$_POST['page']['author']) :
								$pubcan = 1;
							endif;
						else :
							$pubcan = 1;
						endif;
						if($pubcan==1) :
							//topic rank核算
							$topic_rank_now = $redis->hget('topic:'.$_POST['page']['topic'],'rank');
							$topic_post_count = $redis->scard('topic_post_id:'.$_POST['page']['topic']);
							$topic_rank_new = round(($topic_rank_now*$topic_post_count+$_POST['page']['rank'])/($topic_post_count+1),0);
							$redis->hset('topic:'.$_POST['page']['topic'],'rank',$topic_rank_new);
							$redis->zrem('topic_rank_list',$_POST['page']['topic']);
							$redis->zadd('topic_rank_list',$topic_rank_new,$_POST['page']['topic']);
							//将文章加入topic列表
							$redis->sadd('topic_post_id:'.$_POST['page']['topic'],$id);
							//统计发文最多的作者
							if($redis->zscore('topic_post_count_to_user:'.$_POST['page']['topic'],$user_id)>0) :
								$redis->zincrby('topic_post_count_to_user:'.$_POST['page']['topic'],1,$user_id) ;
							else :
								$redis->zadd('topic_post_count_to_user:'.$_POST['page']['topic'],1,$user_id);
							endif;
							//标签
							$tags = explode(' ',$_POST['page']['tags']);
							foreach($tags as $tag) :
								if($tag) :
									$redis->sadd('tag_post_id:'.$tag,$id);
								endif;
							endforeach;
							$redis->sadd('post_id',$id);
							$redis->sadd('user_post_id:'.$_POST['page']['author'],$id);
							$redis->zadd('rank_list',$_POST['page']['rank'],$id);
							$redis->hmset('post:'.$id,$_POST['page']);
							$redis->del('user_draft_post:'.$user_id); //删除草稿
							$redis->hset('user:'.$user_id,'user_pub_date',$_POST['page']['date']);
							//消息
							$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$user_id)).'">'.maoo_user_display_name($user_id).'</a> 发表了文章 <a href="'.maoo_url('post','single',array('id'=>$id)).'">'.$redis->hget('post:'.$id,'title').'</a></h4>'.maoo_cut_str(strip_tags($_POST['page']['content']),30);
							$db = $redis->zrevrange('user_fans:'.$user_id,0,999);
							foreach($db as $rank_user_id) :
								maoo_add_message($user_id,$rank_user_id,$text);
							endforeach;
							$text = '<h4 class="title"><a href="'.maoo_url('user','index',array('id'=>$user_id)).'">'.maoo_user_display_name($user_id).'</a> 在您订阅的话题 <a href="'.maoo_url('post','topic',array('id'=>$_POST['page']['topic'])).'">'.$redis->hget('topic:'.$_POST['page']['topic'],'title').'</a> 中发表了文章 <a href="'.maoo_url('post','single',array('id'=>$id)).'">'.$redis->hget('post:'.$id,'title').'</a></h4>'.maoo_cut_str(strip_tags($_POST['page']['content']),30);
							$url = $redis->get('site_url').'?m=post&a=single&done=发布成功&id='.$id;
						else :
							$url = $redis->get('site_url').'?m=post&a=publish&done=您没有权限在此话题中发布文章';
						endif;
					endif;
				else :
					$url = $redis->get('site_url').'?m=post&a=publish&done=发布文章间隔不得小于2分钟';
				endif;
			endif;
		else :
			if($_POST['id']>0) :
				$url = $redis->get('site_url').'?m=post&a=edit&done=必须设置标题、内容以及所属话题&id='.$_POST['id'];
			else :
				$url = $redis->get('site_url').'?m=post&a=publish&done=必须设置标题、内容以及所属话题';
			endif;
		endif;
	endif;
else :
	$url = $redis->get('site_url').'?done=请先登录';
endif;

//Header("Location:$url");
?>
<!DOCTYPE html><html lang="zh-CN"><meta http-equiv="refresh" content="0;url=<?php echo $url; ?>"><head><meta charset="utf-8"><title>Mao10CMS</title></head><body></body></html>
