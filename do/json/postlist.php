<?php  
header('Access-Control-Allow-Origin: *');
require '../functions.php';
?>
<?php 
			$count = $redis->scard('post_id');
			$page_now = $_GET['page'];
			$page_size = 10;
			$max_page = ($count-$count%$page_size)/$page_size+1;
			if(empty($page_now) || $page_now<1) :
				$page_now = 1;
			elseif($page_now>$max_page) :
				$page_now = $max_page;
			else :
				$page_now = $_GET['page'];
			endif;
			$offset = ($page_now-1)*$page_size;
			$db = $redis->sort('post_id',array('sort'=>'desc','limit'=>array($offset,$page_size)));

$posts = array();
foreach($db as $page_id) :
	$author = $redis->hget('post:'.$page_id,'author');
	$post->id = $page_id;
	$post->title = $redis->hget('post:'.$page_id,'title');
	$post->fmimg = maoo_fmimg($page_id);
	$post->time = maoo_format_date($redis->hget('post:'.$page_id,'date'));
	$post->views = maoo_get_views($page_id);
	$post->userName = maoo_user_display_name($author);
	array_push($posts,$post);
	unset($post);
endforeach;
$json->count = $count;
$json->posts = $posts;
echo json_encode($json);
?>