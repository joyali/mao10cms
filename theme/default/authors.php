<?php include('header.php'); ?>
<div class="container">
	<div class="row author-list">
		<div class="col-lg-8 col-lg-offset-2 col">
			<div class="panel panel-default panel-author-list">
				<div class="panel-heading text-center">
					<i class="glyphicon glyphicon-th-list"></i> 推荐作者
				</div>
				<div class="panel-body">
					<ul class="media-list">
						<?php foreach($db as $rank_user_id) : ?>
						<li class="media">
							<div class="media-left">
								<a href="<?php echo maoo_url('user','index',array('id'=>$rank_user_id)); ?>">
									<img class="media-object" src="<?php echo maoo_user_avatar($rank_user_id); ?>" alt="<?php echo maoo_user_display_name($rank_user_id); ?>">
								</a>
							</div>
							<div class="media-body">
								<h4 class="media-heading">
									<a href="<?php echo maoo_url('user','index',array('id'=>$rank_user_id)); ?>"><?php echo maoo_user_display_name($rank_user_id); ?></a>
									<?php echo maoo_guanzhu_btn($rank_user_id,'pull-right'); ?>
								</h4>
								<?php echo $redis->hget('user:'.$rank_user_id,'description'); ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php echo maoo_pagenavi($count,$page_now); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>