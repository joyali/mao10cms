<?php include('header.php'); ?>
<div class="container">
	<div class="text-center mb-20">
		<h1><?php echo maoo_term_title($id); ?></h1>
	</div>
	<hr>
	<ul class="list-inline term-list">
		<li><a href="<?php echo maoo_url('post','topic'); ?>">全部</a></li>
		<?php foreach($redis->zrange('term:post',0,-1) as $term_title) : $term_id = $redis->zscore('term:post',$term_title); ?>
		<li <?php if($term_id==$id) : ?>class="active"<?php endif; ?>><a href="<?php echo maoo_url('post','term',array('id'=>$term_id)); ?>"><?php echo $term_title; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<hr>
	<div class="topic-list row">
		<?php 
			$count = $redis->scard('term_topic_id:'.$id);
			$page_now = $_GET['page'];
			$page_size = $redis->get('page_size');
			if(empty($page_now) || $page_now<1) :
				$page_now = 1;
			else :
				$page_now = $_GET['page'];
			endif;
			$offset = ($page_now-1)*$page_size;
			$db = $redis->sort('term_topic_id:'.$id,array('sort'=>'desc','limit' =>array($offset,$page_size)));
		?>
		<?php foreach($db as $page_id) : ?>
		<div class="topic-<?php echo $page_id; ?> topic col-xs-4 col">
			<div class="topic-pr pr mb-20">
				<div class="topic-img" style="background-image: url(<?php echo maoo_fmimg($page_id,'topic'); ?>);"></div>
				<div class="topic-bg"></div>
				<a class="topic-txt" href="<?php echo maoo_url('post','topic',array('id'=>$page_id)); ?>">
					<h2 class="title">
						<?php echo $redis->hget('topic:'.$page_id,'title'); ?>
					</h2>
					<?php echo maoo_sub_count($page_id); ?>人订阅
				</a>
				<?php echo maoo_sub_btn($page_id); ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
    <?php echo maoo_pagenavi($count,$page_now); ?>
</div>
<?php echo maoo_sub_js(); ?>
<?php include('footer.php'); ?>