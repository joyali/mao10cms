<?php include('header.php'); ?>
<?php include_once('user-head.php'); ?>
<div class="container user-center">
	<div class="row">
		<div class="col-lg-8 col-lg-offset-2 col">
			<?php include_once('user-nav-1.php'); ?>
			<div class="topic-list row">
				<?php if($user_id==maoo_user_id()) : ?>
				<div class="col-xs-12 col mb-20">
					<h4 class="title mt-0 mb-0 pull-left">我发起的话题</h4>
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
				</div>
				<?php endif; ?>
				<?php foreach($redis->smembers('user_topic_id:'.$user_id) as $topic_id) : ?>
				<div class="topic-<?php echo $topic_id; ?> topic col-md-6 col">
					<div class="topic-pr pr mb-20">
						<div class="topic-img" style="background-image: url(<?php echo maoo_fmimg($topic_id,'topic'); ?>);"></div>
						<div class="topic-bg"></div>
						<a class="topic-txt" href="<?php echo maoo_url('post','topic',array('id'=>$topic_id)); ?>">
							<h2 class="title">
								<?php echo $redis->hget('topic:'.$topic_id,'title'); ?>
							</h2>
							<?php echo maoo_sub_count($topic_id); ?>人订阅
						</a>
						<?php echo maoo_sub_btn($topic_id); ?>
					</div>
				</div>
				<?php endforeach; ?>
				<?php if($user_id==maoo_user_id()) : ?>
				<div class="col-xs-12 col mt-30 mb-20">
					<h4 class="title mt-0 mb-0 pull-left">邀请我参与的话题</h4>
					<a class="btn btn-primary pull-right" href="#">拒绝任何邀请</a>
					<div class="clearfix"></div>
				</div>
				<?php endif; ?>
				<?php foreach($redis->sort('topic_partner_user:'.$user_id,array('sort'=>'desc')) as $topic_id) : ?>
				<div class="topic-<?php echo $topic_id; ?> topic col-md-6 col">
					<div class="topic-pr pr mb-20">
						<div class="topic-img" style="background-image: url(<?php echo maoo_fmimg($topic_id,'topic'); ?>);"></div>
						<div class="topic-bg"></div>
						<a class="topic-txt" href="<?php echo maoo_url('post','topic',array('id'=>$topic_id)); ?>">
							<h2 class="title">
								<?php echo $redis->hget('topic:'.$topic_id,'title'); ?>
							</h2>
							<?php echo maoo_sub_count($topic_id); ?>人订阅
						</a>
						<?php echo maoo_sub_btn($topic_id); ?>
						<a class="btn btn-default btn-partner" href="<?php echo $redis->get('site_url'); ?>/do/partner-decline.php?topic_id=<?php echo $topic_id; ?>">拒绝邀请</a>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
<?php echo maoo_sub_js(); ?>
<?php include('footer.php'); ?>