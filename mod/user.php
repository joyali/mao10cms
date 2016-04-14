<?php
class Maoo {
	public function index(){
		global $redis;
		if($_GET['id']>0) {
			$user_id = $_GET['id'];
			if($redis->hget('user:'.$user_id,'del')!=1) {
				$maoo_title = $redis->hget('user:'.$user_id,'title').'发布的文章 - '.$redis->get('site_name');
                $count = $redis->scard('user_post_id:'.$user_id);
				$page_now = $_GET['page'];
				$page_size = $redis->get('page_size');
				if(empty($page_now) || $page_now<1) :
					$page_now = 1;
				else :
					$page_now = $_GET['page'];
				endif;
				$offset = ($page_now-1)*$page_size;
				$db = $redis->sort('user_post_id:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
				include ROOT_PATH.'/theme/'.maoo_theme().'/user-home.php';
			} else {
				$error = '该用户已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			};
		} elseif(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我发布的 - '.$redis->get('site_name');
            $count = $redis->scard('user_post_id:'.$user_id);
				$page_now = $_GET['page'];
				$page_size = $redis->get('page_size');
				if(empty($page_now) || $page_now<1) :
					$page_now = 1;
				else :
					$page_now = $_GET['page'];
				endif;
				$offset = ($page_now-1)*$page_size;
				$db = $redis->sort('user_post_id:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-home.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function login(){
		global $redis;
		if(maoo_user_id()) {
			$url = maoo_url('user','index');
			Header("Location:$url");
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function register(){
		global $redis;
		if(maoo_user_id()) {
			$url = maoo_url('user','index');
			Header("Location:$url");
		} else {
			$maoo_title = '用户注册 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/register.php';
		}
	}
	public function lostpass(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我发布的 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-home.php';
		} else {
			$maoo_title = '找回密码 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/lostpass.php';
		}
	}
	public function logout(){
		global $redis;
		$_SESSION['user_pass'] = null;
		$maoo_title = '用户登录 - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
	}
	public function like(){
		global $redis;
		if($_GET['id']>0) {
			$user_id = $_GET['id'];
			if($redis->hget('user:'.$user_id,'del')!=1) {
				$maoo_title = $redis->hget('user:'.$user_id,'title').'喜欢的文章 - '.$redis->get('site_name');
                $count = $redis->scard('user_like:'.$user_id);
					$page_now = $_GET['page'];
					$page_size = $redis->get('page_size');
					if(empty($page_now) || $page_now<1) :
						$page_now = 1;
					else :
						$page_now = $_GET['page'];
					endif;
					$offset = ($page_now-1)*$page_size;
					$db = $redis->sort('user_like:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
				include ROOT_PATH.'/theme/'.maoo_theme().'/user-like.php';
			} else {
				$error = '该用户已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			}
		} elseif(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我喜欢的 - '.$redis->get('site_name');
            $count = $redis->scard('user_like:'.$user_id);
					$page_now = $_GET['page'];
					$page_size = $redis->get('page_size');
					if(empty($page_now) || $page_now<1) :
						$page_now = 1;
					else :
						$page_now = $_GET['page'];
					endif;
					$offset = ($page_now-1)*$page_size;
					$db = $redis->sort('user_like:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-like.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/login.php';
		}
	}
	public function guanzhu(){
		global $redis;
		if($_GET['id']>0) {
			$user_id = $_GET['id'];
			if($redis->hget('user:'.$user_id,'del')!=1) {
				$maoo_title = $redis->hget('user:'.$user_id,'title').'关注的用户 - '.$redis->get('site_name');
                $count = $redis->zcard('user_guanzhu:'.$user_id);
						$page_now = $_GET['page'];
						$page_size = $redis->get('page_size');
						if(empty($page_now) || $page_now<1) :
							$page_now = 1;
						else :
							$page_now = $_GET['page'];
						endif;
						$offset = ($page_now-1)*$page_size;
						$db = $redis->zrevrange('user_guanzhu:'.$user_id,$offset,$offset+$page_size-1);
				include ROOT_PATH.'/theme/'.maoo_theme().'/user-guanzhu.php';
			} else {
				$error = '该用户已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			}
		} elseif(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我关注的 - '.$redis->get('site_name');
            $count = $redis->zcard('user_guanzhu:'.$user_id);
						$page_now = $_GET['page'];
						$page_size = $redis->get('page_size');
						if(empty($page_now) || $page_now<1) :
							$page_now = 1;
						else :
							$page_now = $_GET['page'];
						endif;
						$offset = ($page_now-1)*$page_size;
						$db = $redis->zrevrange('user_guanzhu:'.$user_id,$offset,$offset+$page_size-1);
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-guanzhu.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/login.php';
		}
	}
	public function topic(){
		global $redis;
		if($_GET['id']>0) {
			$user_id = $_GET['id'];
			if($redis->hget('user:'.$user_id,'del')!=1) {
				$maoo_title = $redis->hget('user:'.$user_id,'title').'发起的话题 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/user-topic.php';
			} else {
				$error = '该用户已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			}
		} elseif(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的话题 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-topic.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function subtopic(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我订阅的话题 - '.$redis->get('site_name');
            $count = $redis->zcard('user_sub_topic_id:'.$user_id);
                            $page_now = $_GET['page'];
                            $page_size = $redis->get('page_size');
                            if(empty($page_now) || $page_now<1) :
                                $page_now = 1;
                            else :
                                $page_now = $_GET['page'];
                            endif;
                            $offset = ($page_now-1)*$page_size;
                            $db = $redis->zrevrange('user_sub_topic_id:'.$user_id,$offset,$offset+$page_size-1);
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-sub-topic.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function set(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的资料 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-set.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function pass(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '修改密码 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-pass.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function comment(){
		global $redis;
		if($_GET['id']>0) {
			$user_id = $_GET['id'];
			if($redis->hget('user:'.$user_id,'del')!=1) {
				$maoo_title = $redis->hget('user:'.$user_id,'title').'发表的评论 - '.$redis->get('site_name');
                $count = $redis->scard('user_comment_id:'.$user_id);
					$page_now = $_GET['page'];
					$page_size = $redis->get('page_size');
					if(empty($page_now) || $page_now<1) :
						$page_now = 1;
					else :
						$page_now = $_GET['page'];
					endif;
					$offset = ($page_now-1)*$page_size;
					$db = $redis->sort('user_comment_id:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
				include ROOT_PATH.'/theme/'.maoo_theme().'/user-comment.php';
			} else {
				$error = '该用户已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			}
		} elseif(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的评论 - '.$redis->get('site_name');
            $count = $redis->scard('user_comment_id:'.$user_id);
					$page_now = $_GET['page'];
					$page_size = $redis->get('page_size');
					if(empty($page_now) || $page_now<1) :
						$page_now = 1;
					else :
						$page_now = $_GET['page'];
					endif;
					$offset = ($page_now-1)*$page_size;
					$db = $redis->sort('user_comment_id:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-comment.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function order(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的订单 - '.$redis->get('site_name');
            if($_GET['type']==2 || $_GET['type']==3 || $_GET['type']==4 || $_GET['type']==5) :
                $dbkey = 'cart:user:'.$_GET['type'].':'.maoo_user_id();
            else :
                $dbkey = 'cart:user:'.maoo_user_id();
            endif;
            $count = $redis->scard($dbkey);
            $page_now = $_GET['page'];
            $page_size = $redis->get('page_size');
            if(empty($page_now) || $page_now<1) :
                $page_now = 1;
            else :
                $page_now = $_GET['page'];
            endif;
            $offset = ($page_now-1)*$page_size;
            $db = $redis->sort($dbkey,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-order.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function deal(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的项目 - '.$redis->get('site_name');
            $page_now = $_GET['page'];
                        $page_size = $redis->get('page_size');
                        if(empty($page_now) || $page_now<1) :
                            $page_now = 1;
                        else :
                            $page_now = $_GET['page'];
                        endif;
                        $offset = ($page_now-1)*$page_size;
                        $count = $redis->scard('user_deal_id:'.$user_id);
                        $db = $redis->sort('user_deal_id:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-deal.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function reward(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的支持记录 - '.$redis->get('site_name');
            $page_size = $redis->get('page_size');
                        if(empty($page_now) || $page_now<1) :
                            $page_now = 1;
                        else :
                            $page_now = $_GET['page'];
                        endif;
                        $offset = ($page_now-1)*$page_size;
                        $count = $redis->scard('user:reward:'.$user_id);
                        $db = $redis->sort('user:reward:'.$user_id,array('sort'=>'desc','limit'=>array($offset,$page_size)));
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-reward.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function cash(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的账单 - '.$redis->get('site_name');
            $db = $redis->sort('cash:user_id:'.$user_id,array('sort'=>'desc','limit'=>array(0,10))); 
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-cash.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function recharge(){
		global $redis;
		if(maoo_user_id()) {
            if($_POST['cash']>0) :
                $user_id = maoo_user_id();
                $cash = $_POST['cash'];
                $maoo_title = '账户充值 - '.$redis->get('site_name');
                include ROOT_PATH.'/theme/'.maoo_theme().'/user-recharge.php';
            else :
                $error = '充值金额必须大于0';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
            endif;
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function coins(){
		global $redis;
		if(maoo_user_id()) {
			$user_id = maoo_user_id();
			$maoo_title = '我的积分 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/user-coins.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
}
