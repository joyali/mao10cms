<?php include('header.php'); ?>
<div class="container admin">
	<div class="row">
		<div class="col-sm-3 col user-center-side">
			<?php include('side.php'); ?>
		</div>
		<div class="col-sm-9 col admin-body">
			<?php 
				$count = $redis->zcard('consult_group');
				$page_now = $_GET['page'];
				$page_size = $redis->get('page_size');
				if(empty($page_now) || $page_now<1) :
					$page_now = 1;
				else :
					$page_now = $_GET['page'];
				endif;
				$offset = ($page_now-1)*$page_size;
				$consults = $redis->zrevrange('consult_group',$offset,$offset+$page_size-1);
			?>
			<div class="form-group mb-0">
				<label>
					全部咨询
				</label>
			</div>
			<div class="panel-group consult-group" id="consult-accordion" role="tablist" aria-multiselectable="true">
				<?php foreach($consults as $consult) : $num++; ?>
				<?php $db = $redis->sort('consult_id:'.$consult,array('sort'=>'desc','limit'=>array(0,30))); $pro_id = $redis->hget('consult:'.$db[0],'pro_id'); ?>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $num; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $num; ?>">
								<?php echo maoo_format_date($redis->zscore('consult_group',$consult)); ?>关于“<?php echo $redis->hget('pro:'.$pro_id,'title'); ?>”的咨询
							</a>
							<a class="pull-right" target="_blank" href="<?php echo maoo_url('pro','single',array('id'=>$pro_id)); ?>">商品链接</a>
						</h4>
					</div>
					<div id="collapse-<?php echo $num; ?>" class="panel-collapse collapse" role="tabpanel">
						<div class="panel-body">
							<ul class="media-list">
										<?php foreach($db as $page_id) : ?>
										<li class="media">
											<div class="<?php if($redis->hget('consult:'.$page_id,'type')==1) : echo 'media-left'; else : echo 'media-right'; endif; ?>">
												<div class="img-div">
													<img class="media-object" src="<?php echo maoo_user_avatar($redis->hget('consult:'.$page_id,'user_id')); ?>">
												</div>
											</div>
											<div class="media-body">
												<h4 class="media-heading">
													<?php echo maoo_user_display_name($redis->hget('consult:'.$page_id,'user_id')); ?> ：
												</h4>
												<?php echo $redis->hget('consult:'.$page_id,'content'); ?>
												<?php if($redis->hget('consult:'.$page_id,'type')==1) : ?>
												<div class="clearfix mt-10"></div>
												<a class="btn btn-default btn-sm btn-consult" href="#" data-toggle="modal" data-target="#consultModal" data-pro="<?php echo $redis->hget('consult:'.$page_id,'pro_id'); ?>" data-user="<?php echo $redis->hget('consult:'.$page_id,'buyer_id'); ?>">回复</a>
												<?php endif; ?>
											</div>
										</li>
										<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php echo maoo_pagenavi($count,$page,10); ?>
			<div class="modal fade" id="consultModal" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">
									&times;
								</span>
							</button>
							<h4 class="modal-title">
								回复咨询
							</h4>
						</div>
						<form method="post" action="<?php echo $redis->get('site_url'); ?>/do/consult.php">
						<input id="consult_user_id" type="hidden" name="page[buyer_id]" value="">
						<input id="consult_pro_id" type="hidden" name="page[pro_id]" value="">
						<div class="modal-body">
							<div class="form-group">
								<textarea class="form-control" rows="3" name="page[content]"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">
								取消
							</button>
							<button type="submit" class="btn btn-warning">
								发送
							</button>
						</div>
						</form>
					</div>
				</div>
			</div>
			<script>
				$('.btn-consult').hover(function(){
					var user = $(this).attr('data-user');
					$('#consult_user_id').val(user);
					var pro = $(this).attr('data-pro');
					$('#consult_pro_id').val(pro);
				});
			</script>
			<?php if($_GET['showconsult']==1) : ?>
			<script>
				$('#consultModal').modal({
					show: true
				});
				$("#consult-in").scrollTop(99999);
				$('#consultModal textarea').focus();
			</script>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php include('footer.php'); ?>