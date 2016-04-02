<?php include('header.php'); ?>
<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col">
			<div class="topic-head" style="background-image: url(<?php echo $redis->hget('topic:'.$id,'fmimg'); ?>);">
				<?php echo $redis->hget('topic:'.$id,'title'); ?>
				<div class="topic-rank help">
					<span class="t1">话题排名：<?php echo maoo_topic_rank($id); ?></span>
					<span class="t2">文章被赞越多，话题排名越高，删除文章会降低排名</span>
				</div>
				<div class="topic-head-bg"></div>
				<div class="btn-box">
                    <?php if($redis->hget('user:'.maoo_user_id(),'user_level')>7) : ?>
					<a class="btn btn-default" href="<?php echo $redis->get('site_url'); ?>/do/delete.php?id=<?php echo $id; ?>&type=topic">删除</a>
					<?php endif; ?>
					<?php if(maoo_user_id()==$redis->hget('topic:'.$id,'author')) : ?>
					<a class="btn btn-default" href="<?php echo maoo_url('post','topicset',array('id'=>$id)); ?>">管理 <?php echo maoo_topic_contribute_count($id); ?></a>
					<?php endif; ?>
					<?php echo maoo_sub_btn($id); ?>
					<?php if(maoo_user_id()) : ?>
					<?php if($redis->hget('topic:'.$id,'permission')==3 && $redis->hget('topic:'.$id,'author')!=maoo_user_id()) : ?>
					<a class="btn btn-default" href="<?php echo maoo_url('post','publish',array('topic_id'=>$id)); ?>">
						投稿
					</a>
					<?php else : ?>
					<?php
						$pubcan = 0;
						if($redis->hget('topic:'.$id,'permission')==2) :
							if($redis->sismember('topic_partner:'.$id,$_POST['page']['author']) || $redis->hget('topic:'.$id,'author')==maoo_user_id()) :
								$pubcan = 1;
							endif;
						elseif($redis->hget('topic:'.$id,'permission')==4) :
							if($redis->hget('topic:'.$id,'author')==maoo_user_id()) :
								$pubcan = 1;
							endif;
						else :
							$pubcan = 1;
						endif;
						if($pubcan==1) :
					?>
					<a class="btn btn-default" href="<?php echo maoo_url('post','publish',array('topic_id'=>$id)); ?>">
						发布文章
					</a>
					<?php endif; endif; ?>
					<?php else : ?>
					<a class="btn btn-default" href="<?php echo maoo_url('post','publish',array('topic_id'=>$id)); ?>">
						<?php if($redis->hget('topic:'.$id,'permission')==3) : ?>投稿<?php else : ?>发布文章<?php endif; ?>
					</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="topic-content mb-20 hidden-xs hidden-sm">
				<?php echo maoo_magic_out($redis->hget('topic:'.$id,'content')); ?>
			</div>
            <?php echo maoo_ad('post3'); ?>
			<hr>
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
		</div>
	</div>
</div>
<?php echo maoo_sub_js(); ?>
<?php include('footer.php'); ?>
