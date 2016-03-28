<?php include('header.php'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
			<form method="post" role="form" action="<?php echo $redis->get('site_url'); ?>/do/pubform-topic.php">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="publish-step-1">
							<div class="form-group">
								<label>
									标题
								</label>
								<input type="text" name="page[title]" class="form-control" value="<?php echo $redis->hget('topic:'.$id,'title'); ?>">
							</div>
							<div class="form-group">
								<label>
									分类
								</label>
								<div class="clearfix"></div>
								<?php foreach($redis->zrange('term:post',0,-1) as $title) : ?>
								<label class="radio-inline">
									<input type="radio" name="page[term]" value="<?php echo $redis->zscore('term:post',$title); ?>" <?php if($redis->hget('topic:'.$id,'term')==$redis->zscore('term:post',$title)) : ?>checked<?php endif; ?>> <?php echo $title; ?>
								</label>
								<?php endforeach; ?>
							</div>
							<div class="form-group">
								<label>
									封面图片
								</label>
								<div class="clearfix"></div>
								<?php
									if($redis->hget('topic:'.$id,'fmimg')) :
										$fmimg_full = $redis->hget('topic:'.$id,'fmimg');
										$fmimg_full2 = $redis->hget('topic:'.$id,'fmimg');
									else :
										$fmimg_full = $redis->get('site_url').'/public/img/upload.jpg';
									endif;
								?>
								<img id="default-img1" class="mb-10 pull-left mr-20" src="<?php echo $fmimg_full; ?>" width="300">
								<div class="pub-imgadd pull-left">
									<button type="button" class="btn btn-default btn-lg">上传图片</button>
									<input type="file" class="picfile" onchange="readFile(this,1)" />
								</div>
								<div class="clearfix"></div>
								<textarea name="page[fmimg]" rows="1" class="form-control" id="pub-input1"><?php echo $fmimg_full2; ?></textarea>
								<script>
								function readFile(obj,id){
											$('#default-img'+id).attr('src','<?php echo $redis->get('site_url'); ?>/public/img/loading.gif');
											var file = obj.files[0];
											//判断类型是不是图片
											if(!/image\/\w+/.test(file.type)){
															alert("请确保文件为图像类型");
															return false;
											}

											data = new FormData();
    									data.append("file", file);
											$.ajax({
													data: data,
													type: "POST",
													url: "<?php echo $redis->get('site_url'); ?>/do/imgupload-sm.php",
													cache: false,
													contentType: false,
        									processData: false,
													success: function(url) {
														$('#default-img'+id).attr('src',url);
														$('#pub-input'+id).html(url);
													},
													error : function(data) {
														alert('上传失败');
														$('#default-img'+id).attr('src','<?php echo $redis->get('site_url'); ?>/public/img/upload.jpg');
													}
											});
							}
								</script>
							</div>
							<div class="form-group">
								<label>
									介绍
								</label>
								<textarea name="page[content]" rows="5" class="form-control"><?php echo $redis->hget('topic:'.$id,'content'); ?></textarea>
							</div>
							<button type="submit" class="btn btn-block btn-default">
								提交
							</button>
						</div>
					</div>
					<?php if($id) : ?>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<?php endif; ?>
				</form>
			</div>
		</div>
	</div>
<?php include('footer.php'); ?>
