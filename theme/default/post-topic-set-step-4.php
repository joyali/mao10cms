<div class="row">
	<div class="col-xs-6 col">
		<div class="panel panel-default">
			<div class="panel-heading">
				发文最多的用户
			</div>
			<?php $db_1 = $redis->zrevrange('topic_post_count_to_user:'.$id,0,9); if($db_1) : ?>
			<div class="list-group">
				<?php foreach($db_1 as $user_id) : ?>
				<a class="list-group-item" href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>">
					<?php echo maoo_user_display_name($user_id); ?> (<?php echo $redis->zscore('topic_post_count_to_user:'.$id,$user_id); ?>)
				</a>
				<?php endforeach; ?>
			</div>
			<?php else : ?>
			<div class="list-group">
				<div class="list-group-item">暂无任何数据</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-xs-6 col">
		<div class="panel panel-default">
			<div class="panel-heading">
				收获赞最多的用户
			</div>
			<?php $db_2 = $redis->zrevrange('topic_like_count_to_user:'.$id,0,9); if($db_2) : ?>
			<div class="list-group">
				<?php foreach($db_2 as $user_id) : ?>
				<a class="list-group-item" href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>">
					<?php echo maoo_user_display_name($user_id); ?> (<?php echo $redis->zscore('topic_like_count_to_user:'.$id,$user_id); ?>)
				</a>
				<?php endforeach; ?>
			</div>
			<?php else : ?>
			<div class="list-group">
				<div class="list-group-item">暂无任何数据</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>