<?php
class Maoo {
	public function index(){
		global $redis;
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		$maoo_title = '商品'.$maoo_title_page.' - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/pro-index.php';
	}
	public function single(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
			if($redis->hget('pro:'.$id,'del')==1) {
				$error = '该商品已被删除';
				$maoo_title = '错误404 - '.$redis->get('site_name');
				include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
			}  else {
				$term_id = $redis->hget('pro:'.$id,'term');
				$maoo_title = $redis->hget('pro:'.$id,'title').' - '.$redis->get('site_name');
				maoo_set_views($id,'pro');
				include ROOT_PATH.'/theme/'.maoo_theme().'/pro-single.php';
			}
		} else {
			$error = '您访问的页面没有找到';
			$maoo_title = '错误404 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/404.php';
		}
	}
	public function term(){
		global $redis;
		if($_GET['id']>0) {
			$id = $_GET['id'];
		};
		if($_GET['page']>1) :
			$maoo_title_page = ' - 第'.$_GET['page'].'页';
		endif;
		$maoo_title = maoo_term_title($id,'pro').$maoo_title_page.' - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/pro-term.php';
	}
	public function checkout(){
		global $redis;
		if(maoo_user_id()) {
			$maoo_title = '支付订单 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/checkout.php';
		} else {
			$maoo_title = '用户登录 - '.$redis->get('site_name');
			include ROOT_PATH.'/theme/'.maoo_theme().'/login.php';
		}
	}
	public function imgrank(){
		global $redis;
		$maoo_title = '买家晒单 - '.$redis->get('site_name');
		include ROOT_PATH.'/theme/'.maoo_theme().'/pro-imgrank.php';
	}
}
