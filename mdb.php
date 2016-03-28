<?php
function maoo_mdb_patch($type) {
	return ROOT_PATH."/db/".$type."/";
};
function maoo_mdb_write($file,$content) {
    $fp = fopen($file, 'cb');
    flock($fp, LOCK_EX | LOCK_NB);
    fwrite($fp, $content);
    flock($fp, LOCK_UN);
    fclose($fp);
};
function maoo_serialize($obj) {
   return base64_encode(gzcompress(serialize($obj)));
};
//反序列化
function maoo_unserialize($txt) {
   return unserialize(gzuncompress(base64_decode($txt)));
};
class Mao10Mdb {

	public function close() {
		//不支持
	}

	public function multi() {
		//不支持
	}

	public function exec() {
		//不支持
	}

    public function exists($key) {
	    $types = array('string','set','zset','hash','list');
	    $exists = false;
	    foreach($types as $type) :
	    	$db_name = maoo_mdb_patch($type).$key.".php";
	    	if(file_exists($db_name)):
	    		$exists = true;
	    	endif;
	    endforeach;
	    return $exists;
    }

    public function keys($key) {
    	//暂未支持此项功能
    }
    public function del($key) {
    	$types = array('string','set','zset','hash','list');
    	foreach($types as $type) :
    		$db_name = maoo_mdb_patch($type).$key.".php";
    		unlink($db_name);
    	endforeach;
    	return true;
    }

    public function incr($key) {
        $key = str_replace(":",".",$key);
	    $db_name = maoo_mdb_patch('string').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db_data_new = $db+1;
		$db_data = '<?php die("forbidden"); ?>'.$db_data_new;
		maoo_mdb_write($db_name,$db_data);
		return $db_data_new;
    }

	public function set($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('string').$key.".php";
		$db_data = '<?php die("forbidden"); ?>'.$val;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function get($key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('string').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		return $db;
	}

	public function sadd($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		if(in_array($val, $db)) :
			return false;
		else :
			array_push($db, $val);
			$db_data = maoo_serialize($db);
			$db_data = '<?php die("forbidden"); ?>'.$db_data;
			maoo_mdb_write($db_name,$db_data);
			return true;
		endif;
	}

	public function scard($key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		return count($db);
	}

	public function sismember($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		if(in_array($val, $db)) :
			return true;
		else :
			return false;
		endif;
	}

	public function smembers($key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		return $db;
	}

    public function srem($key,$val) {
        $key = str_replace(":",".",$key);
    	$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		foreach($db as $key=>$value) :
			if($value==$val) :
				unset($db[$key]);
			endif;
		endforeach;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
    }

	public function zscore($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		$score = false;
		foreach($db as $key=>$value) :
			if($value['value']==$val) :
				$score = $db[$key]['score'];
			endif;
		endforeach;
		return $score;
	}

	public function zcard($key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		return count($db);
	}

	public function zrange($key,$n1,$n2) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		foreach ($db as $key=>$val) :
			$score[$key] = $val['score'];
			$value[$key] = $val['value'];
		endforeach;
		array_multisort($score,SORT_NUMERIC,SORT_ASC,$value,SORT_NUMERIC,SORT_DESC,$db);
		foreach ($db as $key=>$val) :
			$db[$key] = $val['value'];
		endforeach;
		if($n2>0) :
			$db = array_slice($db,$n1,$n2);
		else :
			$db = array_slice($db,$n1);
		endif;
		return $db;
	}

	public function zrevrange($key,$n1,$n2) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		foreach ($db as $key=>$val) :
			$score[$key] = $val['score'];
			$value[$key] = $val['value'];
		endforeach;
		array_multisort($score,SORT_NUMERIC,SORT_DESC,$value,SORT_NUMERIC,SORT_DESC,$db);
		foreach ($db as $key=>$val) :
			$db[$key] = $val['value'];
		endforeach;
		if($n2>0) :
			$db = array_slice($db,$n1,$n2);
		else :
			$db = array_slice($db,$n1);
		endif;
		return $db;
	}

	public function zadd($key,$score,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		$addnew = true;
		foreach($db as $key=>$value) :
			if($value['value']==$val) :
				$db[$key]['score'] = $score;
				$addnew = false;
			endif;
		endforeach;
		if($addnew) :
			$val_array = array('score'=>$score,'value'=>$val);
			array_push($db, $val_array);
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function zincrby($key,$score,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		$addnew = true;
		foreach($db as $key=>$value) :
			if($value['value']==$val) :
				$db[$key]['score'] = $score+$value['score'];
				$addnew = false;
			endif;
		endforeach;
		if($addnew) :
			$val_array = array('score'=>$score,'value'=>$val);
			array_push($db, $val_array);
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function zrem($key,$val) {
        $key = str_replace(":",".",$key);
    	$db_name = maoo_mdb_patch('zset').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		foreach($db as $key=>$value) :
			if($value['value']==$val) :
				unset($db[$key]);
			endif;
		endforeach;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
    }

	public function hdel($key,$hash_key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('hash').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		if($db!='') :
			$db = maoo_unserialize($db);
			unset($db->$hash_key);
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function hget($key,$hash_key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('hash').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		if($db!='') :
			$db = maoo_unserialize($db);
			return $db->$hash_key;
		else :
			return false;
		endif;
	}

	public function hset($key,$hash_key,$hash_val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('hash').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		if($db!='') :
			$db = maoo_unserialize($db);
		endif;
		$db->$hash_key = $hash_val;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function hmset($key,$val_array) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('hash').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		if($db!='') :
			$db = maoo_unserialize($db);
		endif;
		foreach($val_array as $hash_key=>$hash_val) :
			$db->$hash_key = $hash_val;
		endforeach;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function llen($key) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('list').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		return count($db);
	}

	public function lpush($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('list').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(is_array($db)) :
			array_unshift($db,$val);
		else :
			$db[0] = $val;
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function rpush($key,$val) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('list').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(is_array($db)) :
			array_push($db,$val);
		else :
			$db[0] = $val;
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function ltrim($key,$n1,$n2) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('list').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if($n2>0) :
			$db = array_slice($db,$n1,$n2);
		else :
			$db = array_slice($db,$n1);
		endif;
		$db_data = maoo_serialize($db);
		$db_data = '<?php die("forbidden"); ?>'.$db_data;
		maoo_mdb_write($db_name,$db_data);
		return true;
	}

	public function lrange($key,$n1,$n2) {
        $key = str_replace(":",".",$key);
		$db_name = maoo_mdb_patch('list').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if($n2>0) :
			$db = array_slice($db,$n1,$n2);
		else :
			$db = array_slice($db,$n1);
		endif;
		return $db;
	}

	public function sort($key,$array=array('sort'=>'desc')) {
        $key = str_replace(":",".",$key);
		//目前sort只对set类型数据有效
		$db_name = maoo_mdb_patch('set').$key.".php";
		$db = file($db_name);
		$db = str_replace('<?php die("forbidden"); ?>','',$db[0]);
		$db = maoo_unserialize($db);
		if(!is_array($db)) :
			$db = array();
		endif;
		if($array['limit']) :
			if($array['limit'][1]>0) :
				$db = array_slice($db,$array['limit'][0],$array['limit'][1]);
			else :
				$db = array_slice($db,$array['limit'][0]);
			endif;
		endif;
		if($array['sort']=='asc') :
			array_multisort($db,SORT_NUMERIC,SORT_ASC);
		else :
			array_multisort($db,SORT_NUMERIC,SORT_DESC);
		endif;
		return $db;
	}

	public function expire() {
		//此函数暂时实现不了 数据库结构的悲剧
	}
}
