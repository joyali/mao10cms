<?php include('header.php'); ?>
<div class="container">
	<?php if(maoo_user_id() && maoo_dayu() && $redis->hget('user:'.maoo_user_id(),'phone')=='') : ?>
	<div class="bindPhoneTips mb-20 text-center">
		您还没有绑定手机，请立刻前往用户中心<a href="<?php echo maoo_url('user','set'); ?>">绑定手机</a>，即可使用手机号码快捷登录，并获得10积分奖励。
	</div>
	<?php endif; ?>
	<?php if($redis->get('slider_img:1')) : ?>
	<div id="carousel-home" class="carousel slide" data-ride="carousel">
		<?php if($redis->get('slider_img:2')) : ?>
		<ol class="carousel-indicators">
			<li data-target="#carousel-home" data-slide-to="0" class="active"></li>
			<li data-target="#carousel-home" data-slide-to="1"></li>
			<?php if($redis->get('slider_img:3')) : ?>
			<li data-target="#carousel-home" data-slide-to="2"></li>
			<?php endif; ?>
		</ol>
		<?php endif; ?>
		<div class="carousel-inner" role="listbox">
			<a class="item active" href="<?php echo $redis->get('slider_link:1'); ?>" style="background-image: url(<?php echo $redis->get('slider_img:1'); ?>);"></a>
			<?php if($redis->get('slider_img:2')) : ?>
			<a class="item" href="<?php echo $redis->get('slider_link:2'); ?>" style="background-image: url(<?php echo $redis->get('slider_img:2'); ?>);"></a>
			<?php endif; ?>
			<?php if($redis->get('slider_img:3')) : ?>
			<a class="item" href="<?php echo $redis->get('slider_link:3'); ?>" style="background-image: url(<?php echo $redis->get('slider_img:3'); ?>);"></a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
    <?php echo maoo_ad('home1'); ?>
	<div class="row post-list">
		<div class="col-sm-9 col">
		<h4 class="title mt-0 mb-10 hidden-xs hidden-sm">热门文章</h4>
		<?php
			$count = $redis->zcard('rank_list');
			$page_now = $_GET['page'];
			$page_size = $redis->get('page_size');
			if(empty($page_now) || $page_now<1) :
				$page_now = 1;
			else :
				$page_now = $_GET['page'];
			endif;
			$offset = ($page_now-1)*$page_size;
			$db = $redis->zrevrange('rank_list',$offset,$offset+$page_size-1);
		?>
		<?php foreach($db as $page_id) : $numad++; ?>
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
        <?php if($numad==2) : ?><?php echo maoo_ad('home2'); ?><?php endif; ?>
		<?php endforeach; ?>
		<?php echo maoo_pagenavi($count,$page_now); ?>
		</div>
		<div class="col-sm-3 col hidden-xs hidden-sm">
            <?php echo maoo_ad('home3'); ?>
			<div class="home-side-box side-latest-post">
				<h4 class="title mt-0 mb-10">
					最新文章
					<a class="pull-right" href="<?php echo maoo_url('post','latest'); ?>">更多</a>
				</h4>
				<ul class="media-list">
					<?php $db = $redis->sort('post_id',array('sort'=>'desc','limit'=>array(0,5))); ?>
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
		</div>
	</div>
	<!--hr class="mb-30" />
	<h4 class="title mt-0 mb-20">推荐商品</h4>
	<div class="row shop-allpro-list mb-30">
			<?php
				$db = $redis->zrevrange('pro_id',0,7);
			?>
			<?php foreach($db as $page_id) : $cover_images = unserialize($redis->hget('pro:'.$page_id,'cover_image')); ?>
			<div class="col-xs-3 col">
				<div class="thumbnail">
								<a class="img-div" href="<?php echo maoo_url('pro','single',array('id'=>$page_id)); ?>"><img src="<?php echo $cover_images[1]; ?>" alt="<?php echo $redis->hget('pro:'.$page_id,'title'); ?>"></a>
								<div class="caption">
									<h4 class="title">
										<a class="wto" href="<?php echo maoo_url('pro','single',array('id'=>$page_id)); ?>"><?php echo $redis->hget('pro:'.$page_id,'title'); ?></a>
									</h4>
									<div class="price"><?php echo maoo_pro_min_price($page_id); ?>元</div>
								</div>
							</div>
			</div>
			<?php endforeach; ?>
	</div-->
</div>
<div class="home-bottom hidden-xs hidden-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-lg-9">
				<div class="home-side-box side-comment-list">
					<h4 class="title mt-0 mb-10">
						最新评论
					</h4>
					<?php $db = $redis->sort('comment_id',array('sort'=>'desc','limit'=>array(0,4))); ?>
					<ul class="media-list">
						<?php foreach($db as $comment_id) : ?>
                        <?php 
                            $comment_post_id = $redis->hget('comment:'.$comment_id,'post');
                            $comment_type = $redis->hget('comment:'.$comment_id,'type');
                        ?>
						<li class="media">
							<div class="media-left comment-post">
								<a class="img-div" href="<?php if($comment_type) : echo maoo_url($comment_type,'single',array('id'=>$comment_post_id)); else : echo 'javascript:;'; endif; ?>">
									<img class="media-object" src="<?php echo maoo_fmimg($comment_post_id); ?>">
								</a>
							</div>
							<?php $comment_user_id = $redis->hget('comment:'.$comment_id,'author'); ?>
							<div class="media-left comment-user">
								<a class="img-div" href="<?php echo maoo_url('user','index',array('id'=>$comment_user_id)); ?>">
									<img class="media-object" src="<?php echo maoo_user_avatar($comment_user_id); ?>" alt="<?php echo maoo_user_display_name($comment_user_id); ?>">
								</a>
							</div>
							<div class="media-body">
								<h4 class="media-heading mb-10">
									<a href="<?php echo maoo_url('user','index',array('id'=>$comment_user_id)); ?>"><?php echo maoo_user_display_name($comment_user_id); ?></a> :
								</h4>
								<div class="content mb-10">
									<?php echo $redis->hget('comment:'.$comment_id,'content'); ?>
								</div>
								<div class="time">
									<i class="glyphicon glyphicon-time"></i> <?php echo maoo_format_date($redis->hget('comment:'.$comment_id,'date')); ?>
								</div>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div class="hidden-xs hidden-sm col-md-4 col-lg-3">
				<div class="home-side-box side-user-rank-list">
					<h4 class="title mt-0 mb-10">
						推荐作者
						<a class="pull-right" href="<?php echo maoo_url('index','authors'); ?>">更多</a>
					</h4>
					<?php
						$db = $redis->zrevrange('user_rank_list',0,4);
					?>
					<ul class="media-list">
						<?php foreach($db as $rank_user_id) : ?>
						<li class="media">
							<div class="media-left">
								<a href="<?php echo maoo_url('user','index',array('id'=>$rank_user_id)); ?>">
									<img class="media-object" src="<?php echo maoo_user_avatar($rank_user_id); ?>" alt="<?php echo maoo_user_display_name($rank_user_id); ?>">
								</a>
							</div>
							<div class="media-body">
								<h4 class="media-heading mb-10">
									<a href="<?php echo maoo_url('user','index',array('id'=>$rank_user_id)); ?>"><?php echo maoo_user_display_name($rank_user_id); ?></a>
									<?php echo maoo_guanzhu_btn($rank_user_id,'pull-right'); ?>
								</h4>
								<?php echo $redis->hget('user:'.$rank_user_id,'description'); ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="link-box">
    <div class="container">
        <div class="link-box-in">
            <?php echo maoo_link(); ?>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
