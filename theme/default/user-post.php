<?php include('header.php'); ?>
<?php include_once('user-head.php'); ?>
<div class="container user-center">
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2 col">
			<?php include_once('user-nav-1.php'); ?>
			<div class="post-list">
				<?php foreach($db as $page_id) : ?>
				<div class="post-<?php echo $id; ?> post mb-20">
					<a class="pull-left img-div" href="<?php echo $redis->get('site_url'); ?>?m=post&a=single&id=<?php echo $page_id; ?>">
						<img src="<?php echo maoo_fmimg($page_id); ?>">
					</a>
					<div class="post-right">
						<h2 class="title">
							<a href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
								<?php echo $redis->hget('post:'.$page_id,'title'); ?>
							</a>
							<?php if($redis->hget('post:'.$page_id,'permission')==3) : ?>
							<small>投稿审核中</small>
							<?php endif; ?>
						</h2>
						<div class="author mb-10">
							<i class="glyphicon glyphicon-time"></i> <?php echo date('Y/m/d',$redis->hget('post:'.$page_id,'date')); ?>
						</div>
						<div class="entry mb-10">
							<?php echo maoo_cut_str(strip_tags($redis->hget('post:'.$page_id,'content')),70); ?>
						</div>
						<ul class="list-inline mb-0">
							<?php if($redis->hget('post:'.$page_id,'term')>0) : ?>
							<li><i class="glyphicon glyphicon-paperclip"></i> <a href="<?php echo maoo_url('post','term',array('id'=>$redis->hget('post:'.$page_id,'term'))); ?>"><?php echo maoo_term_title($redis->hget('post:'.$page_id,'term')); ?></a></li>
							<?php endif; ?>
							<li><i class="glyphicon glyphicon-heart"></i> <?php echo maoo_like_count($page_id); ?></li>
							<li><i class="glyphicon glyphicon-eye-open"></i> <?php echo maoo_get_views($page_id); ?></li>
							<?php if(maoo_user_id()==$user_id) : ?>
							<li><a href="<?php echo $redis->get('site_url'); ?>?m=post&a=edit&id=<?php echo $page_id; ?>">编辑</a></li>
							<li><a href="<?php echo $redis->get('site_url'); ?>/do/delete.php?id=<?php echo $page_id; ?>&type=post">删除</a></li>
							<?php endif; ?>
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
<?php include('footer.php'); ?>