<?php  
header('Access-Control-Allow-Origin: *');
require '../functions.php';
if($_POST['name'] && $_POST['pass']) :
	$user_name = $_POST['name'];
	$user_pass = sha1($_POST['pass']);
	$id = $redis->zscore('user_id_name',$user_name);
	if($id>0) :
		$user_pass_true = $redis->hget('user:'.$id,'user_pass');
		if($user_pass==$user_pass_true) :
			$date['user_login_date'] = strtotime("now");
			$redis->hmset('user:'.$id,$date);
			$_SESSION['user_name'] = $user_name;
			$_SESSION['user_pass'] = $user_pass;
			$user_level = $redis->hget('user:'.$id,'user_level');
			echo '{"logindone":true,"id":'.$id.',"error":"登录成功"}';
		else :
			echo '{"logincan":false,"error":"用户名与密码不符"}';
		endif;
	else :
		echo '{"logincan":false,"error":"用户名不存在"}';
	endif;
elseif($_POST['name']) :
	$user_name = $_POST['name'];
	$id = $redis->zscore('user_id_name',$user_name);
	if($id>0) :
		echo '{"logincan":true,"error":"用户名验证通过"}';
	else :
		echo '{"logincan":false,"error":"用户名不存在"}';
	endif;
else :
	echo '{"logincan":false,"error":"用户名和密码必须填写"}';
endif;
?>