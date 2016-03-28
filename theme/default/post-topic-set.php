<?php include('header.php'); ?>
<div class="container topicSet">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col">
			<ol class="breadcrumb mb-10">
				<li>
					<a href="<?php echo $redis->get('site_url'); ?>?m=post&a=topic&id=<?php echo $id; ?>">
						话题首页
					</a>
				</li>
				<li class="active">
					管理
				</li>
			</ol>
			<div class="panel panel-default">
				<div class="panel-heading">
					<ul class="nav nav-pills nav-justified mb-0">
						<li role="presentation" <?php if($step==1 || $step=='') echo 'class="active"'; ?>>
							<a href="<?php echo $redis->get('site_url'); ?>/?m=post&a=topicset&id=<?php echo $id; ?>&step=1">
								基本信息
							</a>
						</li>
						<li role="presentation" <?php if($step==2 || $step==5) echo 'class="active"'; ?>>
							<a href="<?php echo $redis->get('site_url'); ?>/?m=post&a=topicset&id=<?php echo $id; ?>&step=2">
								权限管理 <?php echo maoo_topic_contribute_count($id); ?>
							</a>
						</li>
						<li role="presentation" <?php if($step==3) echo 'class="active"'; ?>>
							<a href="<?php echo $redis->get('site_url'); ?>/?m=post&a=topicset&id=<?php echo $id; ?>&step=3">
								黑名单
							</a>
						</li>
						<li role="presentation" <?php if($step==4) echo 'class="active"'; ?>>
							<a href="<?php echo $redis->get('site_url'); ?>/?m=post&a=topicset&id=<?php echo $id; ?>&step=4">
								数据统计
							</a>
						</li>
					</ul>
				</div>
				<div class="panel-body">
					<?php include('post-topic-set-step-'.$step.'.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>