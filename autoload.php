<?php
error_reporting(0);
//定义根目录
define('ROOT_PATH',dirname(__FILE__));
session_save_path(ROOT_PATH.'/upload/session');
session_id(SID);
session_start();
header("Content-Type: text/html; charset=UTF-8");

define('DB_TYPE','mdb');
//数据库连接
if(DB_TYPE=='redis') :
	try {
		$redis = new Redis();
		$redis->connect("127.0.0.1", 6379);
		$redis->select(0);
	}
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
elseif(DB_TYPE=='mysql') :
	/*
		开发时需注意:
		- mysql操作类中，大部分”增改删“操作只返回true和false
		- sort只对set表有效
		- 各类型表中，name未经过重名限制，请人工避免name重复
	*/
	require __DIR__.'/mysql.php';
	try {
		$redis = new Mao10Mysql;
		$redis->connect('127.0.0.1','root','','nera');
		$redis->setprefix('maoo_');
        $redis->flush();
	}
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
elseif(DB_TYPE=='mdb') :
    require __DIR__.'/mdb.php';
	try {
		$redis = new Mao10Mdb;
        if($redis->get('site_url')=='') :
            $site_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];
            $site_url = preg_replace("/\/[a-z0-9]+\.php.*/is", "", $site_url);
            $redis->set('site_ur',$site_url);
        endif;
	}
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
endif;
