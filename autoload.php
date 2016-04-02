<?php
error_reporting(0);
//定义根目录
define('ROOT_PATH',dirname(__FILE__));
session_save_path(ROOT_PATH.'/upload/session');
session_id(SID);
session_start();
header("Content-Type: text/html; charset=UTF-8");

define('DB_TYPE','mysql'); //这里设置你的数据库类型，可选 mysql  redis  mdb
//数据库连接
if(DB_TYPE=='redis') :
	try {
		$redis = new Redis();
		$redis->connect("127.0.0.1", 6379);
        $redis->auth('redis数据库密码');
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
		$redis->connect('数据库地址','用户名','密码','数据库名');  //在这里修改mysql数据库配置信息
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
	}
	catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
	}
endif;
