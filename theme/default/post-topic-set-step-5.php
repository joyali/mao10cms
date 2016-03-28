			<div class="panel panel-default">
				<div class="panel-heading">
					邀请用户
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
				<?php if($users) : ?>
				<div class="search-user-list">
					<ul class="media-list mb-0">
						<?php foreach($users as $user_id) : ?>
						<li class="media">
							<div class="media-left">
								<a href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>">
									<img class="media-object" src="<?php echo maoo_user_avatar($user_id); ?>" alt="<?php echo maoo_user_display_name($user_id); ?>">
								</a>
							</div>
							<div class="media-body">
								<h4 class="media-heading">
									<a href="<?php echo $redis->get('site_url'); ?>/?m=user&a=index&id=<?php echo $user_id; ?>"><?php echo maoo_user_display_name($user_id); ?></a>
									<a class="pull-right btn btn-sm btn-default" href="<?php echo $redis->get('site_url'); ?>/do/partner.php?id=<?php echo $user_id; ?>&topic_id=<?php echo $id; ?>">邀请</a>
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
						没有搜索到任何与”<?php echo $_POST['user']; ?>“有关的用户！
					</li>
				</ul>
				<?php endif; ?>
			</div>
