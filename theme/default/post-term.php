<?php include('header.php'); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col">
			<div class="text-center mb-20">
                <h1><?php echo maoo_term_title($id); ?></h1>
            </div>
			<hr>
            <?php echo maoo_ad('post3'); ?>
			<div class="topic-post-list post-list">
				<?php foreach($db as $page_id) : ?>
				<div class="post-<?php echo $page_id; ?> post mb-20">
					<a class="pull-left img-div" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
						<img src="<?php echo maoo_fmimg($page_id); ?>">
					</a>
					<div class="post-right">
						<h2 class="title">
							<a href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
								<?php echo $redis->hget('post:'.$page_id,'title'); ?>
							</a>
						</h2>
						<?php $author = $redis->hget('post:'.$page_id,'author'); ?>
						<div class="author mb-10">
							<a class="avatar" href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><img src="<?php echo maoo_user_avatar($author); ?>" alt="<?php echo maoo_user_display_name($author); ?>"></a> <a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a><span class="dian">•</span><span><?php echo date('Y/m/d',$redis->hget('post:'.$page_id,'date')); ?></span>
							<div class="clearfix"></div>
						</div>
						<div class="entry mb-10">
							<?php echo maoo_cut_str(strip_tags($redis->hget('post:'.$page_id,'content')),70); ?>
						</div>
						<ul class="list-inline mb-0">
							<?php if($redis->hget('post:'.$page_id,'term')>0) : ?>
							<li><i class="glyphicon glyphicon-tag"></i> <a href="<?php echo maoo_url('post','term',array('id'=>$redis->hget('post:'.$page_id,'term'))); ?>"><?php echo maoo_term_title($redis->hget('post:'.$page_id,'term')); ?></a></li>
							<?php endif; ?>
							<li><i class="glyphicon glyphicon-heart"></i> <?php echo maoo_like_count($page_id); ?></li>
							<li><i class="glyphicon glyphicon-eye-open"></i> <?php echo maoo_get_views($page_id); ?></li>
						</ul>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php endforeach; ?>
				<?php echo maoo_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
</div>
<?php echo maoo_sub_js(); ?>
<?php include('footer.php'); ?>
