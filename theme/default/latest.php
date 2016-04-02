<?php include('header.php'); ?>
<div class="container" id="latest">
    <?php echo maoo_ad('post1'); ?>
    <div class="row post-list">
		<div class="col-sm-9 col">
		<h4 class="title mt-0 mb-10 hidden-xs hidden-sm">最新文章</h4>
		<?php foreach($db as $page_id) : ?>
		<div class="post-<?php echo $page_id; ?> post mb-20">
					<a class="pull-left img-div" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
						<img src="<?php echo maoo_fmimg($page_id); ?>">
					</a>
					<div class="post-right">
						<h2 class="title">
							<a class="wto" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
								<?php echo $redis->hget('post:'.$page_id,'title'); ?>
							</a>
						</h2>
						<?php $author = $redis->hget('post:'.$page_id,'author'); ?>
						<div class="author mb-10">
							<a class="avatar" href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><img src="<?php echo maoo_user_avatar($author); ?>" alt="<?php echo maoo_user_display_name($author); ?>"></a> <a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a><span class="dian">•</span><span><?php echo date('Y/m/d',$redis->hget('post:'.$page_id,'date')); ?></span>
							<div class="clearfix"></div>
						</div>
						<div class="entry mb-10">
							<?php echo maoo_cut_str(strip_tags($redis->hget('post:'.$page_id,'content')),33); ?>
						</div>
						<ul class="list-inline mb-0">
							<?php if($redis->hget('post:'.$page_id,'topic')>0) : ?>
							<li><i class="glyphicon glyphicon-tag"></i> <a href="<?php echo maoo_url('post','topic',array('id'=>$redis->hget('post:'.$page_id,'topic'))); ?>"><?php echo $redis->hget('topic:'.$redis->hget('post:'.$page_id,'topic'),'title'); ?></a></li>
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
		<div class="col-sm-3 col hidden-xs hidden-sm">
			<div class="home-side-box side-latest-post">
				<h4 class="title mt-0 mb-10">
					热门文章
				</h4>
				<ul class="media-list">
					<?php $db = $redis->zrevrange('rank_list',0,9); ?>
					<?php foreach($db as $page_id) : ?>
					<li class="media">
						<div class="media-left">
							<a class="wto" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
								<img class="media-object" src="<?php echo maoo_fmimg($page_id); ?>" alt="<?php echo $redis->hget('post:'.$page_id,'title'); ?>">
							</a>
						</div>
						<div class="media-body">
							<h4 class="media-heading">
								<a href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>"><?php echo $redis->hget('post:'.$page_id,'title'); ?></a>
							</h4>
							<div class="excerpt">
								<?php echo maoo_cut_str(strip_tags($redis->hget('post:'.$page_id,'content')),21); ?>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="home-side-box side-topic-list">
				<h4 class="title mt-0 mb-20">
					热门话题
					<a class="pull-right" href="<?php echo maoo_url('post','topic'); ?>">更多</a>
				</h4>
				<div class="side-topic-box">
					<?php $db = $redis->sort('topic_id',array('sort'=>'desc','limit' =>array(0,10))); ?>
					<?php foreach($db as $topic_id) : ?>
					<a class="side-topic" href="<?php echo maoo_url('post','topic',array('id'=>$topic_id)); ?>"><?php echo $redis->hget('topic:'.$topic_id,'title'); ?></a>
					<?php endforeach; ?>
					<div class="clearfix"></div>
				</div>
			</div>
            <?php if($redis->get('promod')!=1) : ?>
			<div class="home-side-box side-pro-list">
				<h4 class="title mt-0 mb-10">
					会员专购
					<a class="pull-right" href="<?php echo maoo_url('pro'); ?>">更多</a>
				</h4>
				<?php
					$db = $redis->zrevrange('pro_id',0,4);
				?>
				<ul class="media-list">
					<?php foreach($db as $page_id) : $cover_images = unserialize($redis->hget('pro:'.$page_id,'cover_image')); ?>
					<li class="media">
						<a class="media-left img-div" href="<?php echo maoo_url('pro','single',array('id'=>$page_id)); ?>">
							<img class="media-object" src="<?php echo $cover_images[1]; ?>">
						</a>
						<div class="media-body">
							<h4 class="media-heading">
								<a href="<?php echo maoo_url('pro','single',array('id'=>$page_id)); ?>"><?php echo $redis->hget('pro:'.$page_id,'title'); ?></a>
							</h4>
							<div class="price"><?php echo maoo_pro_min_price($page_id); ?>元</div>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
            <?php endif; ?>
            <?php echo maoo_ad('post2'); ?>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>