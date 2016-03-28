<form method="post" role="form" action="<?php echo $redis->get('site_url'); ?>/do/pubform-topic.php">
	<div class="radio">
		<label>
			<input type="radio" name="permission" value="1" <?php if($redis->hget('topic:'.$id,'permission')!=2 || $redis->hget('topic:'.$id,'permission')!=3 || $redis->hget('topic:'.$id,'permission')!=4) echo 'checked'; ?>>
			公开 - 所有人可自由发布文章
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="permission" value="2" <?php if($redis->hget('topic:'.$id,'permission')==2) echo 'checked'; ?>>
			仅邀请 - 仅自己和受邀请者可发布文章
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="permission" value="3" <?php if($redis->hget('topic:'.$id,'permission')==3) echo 'checked'; ?>>
			接受投稿 - 所有人可投稿（需审核）
		</label>
	</div>
	<div class="radio">
		<label>
			<input type="radio" name="permission" value="4" <?php if($redis->hget('topic:'.$id,'permission')==4) echo 'checked'; ?>>
			私密 - 仅自己可发布文章
		</label>
	</div>
	<button type="submit" class="btn btn-default">
		保存
	</button>
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<input type="hidden" name="step" value="2">
</form>
<?php if($redis->hget('topic:'.$id,'permission')==2) : ?>
<div class="panel panel-default mt-20">
	<!-- Default panel contents -->
	<div class="panel-heading">
		邀请列表
	</div>
	<div class="panel-body">
		<form method="post" action="<?php echo $redis->get('site_url'); ?>/do/search-user.php" id="search-user">
			<div class="input-group">
				<input type="text" name="user" class="form-control" placeholder="请输入被邀请的用户名">
				<span class="input-group-addon cp">
					查询用户
				</span>
			</div>
			<input type="hidden" name="id" value="<?php echo $id; ?>">
		</form>
		<script>
			$('#search-user .input-group-addon').click(function(){
				$('form#search-user').submit();
			});
		</script>
	</div>
	<?php if($partners) : ?>
	<div class="search-user-list">
					<ul class="media-list mb-0">
						<?php foreach($partners as $user_id) : ?>
						<li class="media">
							<div class="media-left">
								<a href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>">
									<img class="media-object" src="<?php echo maoo_user_avatar($user_id); ?>" alt="<?php echo maoo_user_display_name($user_id); ?>">
								</a>
							</div>
							<div class="media-body">
								<h4 class="media-heading">
									<a href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>"><?php echo maoo_user_display_name($user_id); ?></a>
									<a class="pull-right btn btn-sm btn-default" href="<?php echo $redis->get('site_url'); ?>/do/partner-del.php?id=<?php echo $user_id; ?>&topic_id=<?php echo $id; ?>">取消邀请</a>
								</h4>
								<?php echo $redis->hget('user:'.$user_id,'description'); ?>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
	<?php else : ?>
	<ul class="list-group">
		<li class="list-group-item">
			还没有邀请任何用户！
		</li>
	</ul>
	<?php endif; ?>
</div>
<?php elseif($redis->hget('topic:'.$id,'permission')==3) : ?>
<div class="panel panel-default mt-20">
	<!-- Default panel contents -->
	<div class="panel-heading">
		投稿列表
	</div>
	<?php if($contribute) : ?>
	<div class="list-group">
		<?php foreach($contribute as $page_id) : ?>
		<a class="list-group-item" href="<?php echo $redis->get('site_url'); ?>?m=post&a=edit&id=<?php echo $page_id; ?>">
			<?php echo $redis->hget('post:'.$page_id,'title'); ?>
		</a>
		<?php endforeach; ?>
	</div>
	<?php else : ?>
	<ul class="list-group">
		<li class="list-group-item">
			还没有收到任何投稿！
		</li>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?>