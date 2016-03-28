<?php include('header.php'); ?>
<div class="container">
	<div class="text-center mb-20 hidden-xs hidden-sm">
		<h1>话题广场</h1>
	</div>
	<hr class="hidden-xs hidden-sm">
	<ul class="list-inline term-list pull-left mb-0">
		<li class="active"><a href="<?php echo maoo_url('post','topic'); ?>">全部</a></li>
		<?php foreach($redis->zrange('term:post',0,-1) as $term_title) : $term_id = $redis->zscore('term:post',$term_title); ?>
		<li><a href="<?php echo maoo_url('post','term',array('id'=>$term_id)); ?>"><?php echo $term_title; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php 
					if($redis->hget('user:'.$user_id,'user_level')==10) :
						$pubcan = 1;
					else :
						if($redis->get('topic_permission')!=2) :
							if($redis->get('topic_number')>0) :
								if($redis->scard('user_topic_id:'.$user_id)<$redis->get('topic_number')) :
									$pubcan = 1;
								endif;
							else : 
								$pubcan = 1;
							endif;
						endif;
					endif;
					if($pubcan==1) :
				?>
				<a class="btn btn-primary pull-right hidden-xs hidden-sm" href="<?php echo maoo_url('post','publishtopic'); ?>">发起话题</a>
				<?php endif; ?>
	<div class="clearfix"></div>
	<hr>
	<div class="topic-list row">
		<?php 
			$count = $redis->zcard('topic_rank_list');
			$page_now = $_GET['page'];
			$page_size = $redis->get('page_size');
			if(empty($page_now) || $page_now<1) :
				$page_now = 1;
			else :
				$page_now = $_GET['page'];
			endif;
			$offset = ($page_now-1)*$page_size;
			$db = $redis->zrevrange('topic_rank_list',$offset,$offset+$page_size-1);
		?>
		<?php foreach($db as $page_id) : ?>
		<div class="topic-<?php echo $page_id; ?> topic col-md-4 col">
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