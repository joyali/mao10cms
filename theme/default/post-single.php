<?php include('header.php'); ?>
<div class="container">
	<div class="row single-post">
		<div class="col-md-9 col">
			<div class="panel panel-default panel-single-post">
				<div class="panel-heading">
					<h1 class="title mt-0 mb-20"><?php echo $redis->hget('post:'.$id,'title'); ?></h1>
					<div class="post-info">
						<ul class="list-inline mb-0">
							<li><a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a></li>
							<li class="hidden-xs hidden-sm">•</li>
							<li class="hidden-xs hidden-sm"><?php echo date('Y/m/d',$redis->hget('post:'.$id,'date')); ?></li>
							<li>•</li>
							<li><?php echo maoo_get_views($id); ?>人阅读</li>
							<?php if($redis->hget('post:'.$id,'topic')>0) : ?>
							<li>•</li>
							<li><a href="<?php echo maoo_url('post','topic',array('id'=>$redis->hget('post:'.$id,'topic'))); ?>"><?php echo $redis->hget('topic:'.$redis->hget('post:'.$id,'topic'),'title'); ?></a></li>
							<?php endif; ?>
							<?php if($redis->hget('post:'.$id,'tags')) : ?>
							<li>•</li>
							<?php $tags = explode(' ',$redis->hget('post:'.$id,'tags')); foreach($tags as $tag) : if($tag) : ?>
							<li><a href="<?php echo maoo_url('post','tag',array('tag'=>$tag)); ?>"><?php echo $tag; ?></a></li>
							<?php endif; endforeach; endif; ?>
						</ul>
					</div>
					<div class="bg"></div>
					<div class="bgimg blur" style="background-image:url(<?php echo $redis->hget('post:'.$id,'fmimg'); ?>);"></div>
				</div>
				<div class="panel-body">
                    <?php echo maoo_ad('post4'); ?>
					<div class="entry">
						<?php echo $redis->hget('post:'.$id,'content'); ?>
					</div>
					<?php if($redis->hget('post:'.$id,'content2')) : ?>
						<?php if(maoo_user_id()==$author || $redis->hget('user:'.maoo_user_id(),'user_level')==10 || $redis->sismember('post:'.$id.':paycoins',maoo_user_id())) : ?>
							<div class="entry mt-40 mb-40" id="post-entry-content2">
								<div class="well mb-0">
									<h3 class="title text-center mt-0 mb-20">
										<i class="fa fa-lock"></i> 隐藏内容
									</h3>
									<?php echo $redis->hget('post:'.$id,'content2'); ?>
								</div>
							</div>
						<?php else : ?>
							<div class="entry mt-40 mb-40" id="post-entry-content2">
								<div class="well mb-0">
									<h3 class="title text-center mt-0 mb-20">
										<i class="fa fa-lock"></i> 隐藏内容
									</h3>
									<div class="text-center">
										<?php if(maoo_user_id()) : ?>
											<p>您必须支付<span class="coins"><?php echo $redis->hget('post:'.$id,'coins'); ?></span>积分才可以查看这部分隐藏内容</p>
											<?php $user_coins = maoo_user_coins(maoo_user_id()); if($user_coins>=$redis->hget('post:'.$id,'coins')) : ?>
												<a class="btn btn-danger" href="<?php echo $redis->get('site_url'); ?>/do/coins-to-post.php?pid=<?php echo $id; ?>">
													<i class="fa fa-unlock"></i> 立即支付
												</a>
											<?php else : ?>
												您的积分不足，请先<a href="<?php echo maoo_url('user','coins'); ?>#ucash1">购买积分</a>
											<?php endif; ?>
										<?php else : ?>
											请在<a href="<?php echo $redis->get('site_url'); ?>?m=user&a=login<?php if($_GET['a']=='logout') : echo '&noreferer=yes'; endif; ?>">登录</a>或<a href="<?php echo $redis->get('site_url'); ?>?m=user&a=register<?php if($_GET['a']=='logout') : echo '&noreferer=yes'; endif; ?>">注册</a>后查看隐藏内容
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php echo maoo_like_btn($id); ?>
					<div class="media post-author hidden-xs hidden-sm" id="post-author-box">
						<div class="media-left">
							<a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>">
								<img class="media-object" src="<?php echo maoo_user_avatar($author); ?>" alt="<?php echo maoo_user_display_name($author); ?>">
							</a>
						</div>
						<div class="media-body">
							<h4 class="media-heading">
								<a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a>
								<?php echo maoo_guanzhu_btn($author); ?>
								<?php if($author!=maoo_user_id() && maoo_user_id()>0) : ?>
								<div class="pull-right btn-group btn-dashang">
									<button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="glyphicon glyphicon-piggy-bank"></i> 打赏
									</button>
									<ul class="dropdown-menu">
										<li><a href="javascript:;">您现有积分：<?php echo maoo_user_coins(maoo_user_id()); ?></a></li>
										<?php $coins_array = array(1,3,5,10,20); foreach($coins_array as $coins) : ?>
								    <li><a href="<?php echo $redis->get('site_url'); ?>/do/coins-to-user.php?pid=<?php echo $id; ?>&coins=<?php echo $coins; ?>">打赏 <?php echo $coins; ?> 积分</a></li>
										<?php endforeach; ?>
								  </ul>
								</div>
								<?php endif; ?>
							</h4>
							<?php echo $redis->hget('user:'.$author,'description'); ?>
						</div>
						<div class="clearfix"></div>
						<?php
							$db = $redis->lrange('coins:user:'.$author,0,50);
							if($db) :
							$coins_db = array();
							foreach($db as $coins_message) : $coins_message = unserialize($coins_message); if($coins_message->des=='被打赏') :
								array_push($coins_db,$coins_message);
							endif;endforeach;
							$coins_db = array_slice($coins_db,0,12);
							if($coins_db) :
						?>
						<div class="clearfix mb-30"></div>
						<div class="row post-author-coins">
                            <h5 class="mt-0 mb-10">这些用户打赏了TA...</h5>
							<?php foreach($coins_db as $coins_message) : ?>
							<div class="col-xs-12 col">
								<div class="thumbnail">
								  <a class="img-div" href="<?php echo maoo_url('user','index',array('id'=>$coins_message->user_id)); ?>">
										<img src="<?php echo maoo_user_avatar($coins_message->user_id); ?>" />
									</a>
								  <div class="caption text-center">
								    +<?php echo $coins_message->coins; ?>
								  </div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; endif; ?>
					</div>
					<?php if($redis->hget('user:'.maoo_user_id(),'user_level')==10 || $redis->hget('user:'.maoo_user_id(),'user_level')==8 || maoo_user_id()==$author) : ?>
					<div class="text-center mt-30">
						<a class="btn btn-default" href="<?php echo $redis->get('site_url'); ?>?m=post&a=edit&id=<?php echo $id; ?>">编辑</a> <a class="btn btn-default" href="<?php echo $redis->get('site_url'); ?>/do/delete.php?id=<?php echo $id; ?>&type=post">删除</a>
					</div>
					<?php endif; ?>
				</div>
			</div>
            <?php echo maoo_ad('post5'); ?>
			<div id="react-post-comment-box"></div>
				<script type="text/jsx" src="<?php echo $redis->get('site_url'); ?>/theme/default/react/comment.js"></script>
				<script type="text/jsx">
				React.render(
					<BbsCommentBox url="<?php echo $redis->get('site_url'); ?>" type="post" uid="<?php echo maoo_user_id(); ?>" ulevel="<?php echo $redis->hget('user:'.maoo_user_id(),'user_level'); ?>" postId="<?php echo $id; ?>" pollInterval={10000} />,
					document.getElementById('react-post-comment-box')
				);
				</script>
		</div>
        <div class="col-md-3 col">
            <div class="home-side-box side-author">
                <a class="avatar" href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>">
					<img src="<?php echo maoo_user_avatar($author); ?>" alt="<?php echo maoo_user_display_name($author); ?>">
				</a>
                <div class="clearfix mb-10"></div>
				<a class="name wto" href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a>
                <div class="clearfix mb-20"></div>
                <ul class="list-inline mb-10">
                    <li><a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>">文章 <?php echo $redis->scard('user_post_id:'.$author); ?></a></li>
                    <li><a href="<?php echo maoo_url('user','topic',array('id'=>$author)); ?>">话题 <?php echo $redis->scard('user_topic_id:'.$author); ?></a></li>
                    <li><a href="<?php echo maoo_url('user','comment',array('id'=>$author)); ?>">评论 <?php echo $redis->scard('user_comment_id:'.$author); ?></a></li>
                </ul>
                <div id="share-box">
                    <div class="bdsharebuttonbox">
                        <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
                        <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
                        <a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
                        <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
                        <a href="#" class="bds_douban" data-cmd="douban" title="分享到豆瓣网"></a>
                        <a href="#" class="bds_meilishuo" data-cmd="meilishuo" title="分享到美丽说"></a>
                    </div>
                    <script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"<?php echo $redis->hget('post:'.$id,'title'); ?>","bdMini":"2","bdMiniList":false,"bdPic":"<?php echo $redis->hget('post:'.$id,'fmimg'); ?>","bdStyle":"1","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
                </div>
            </div>
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
</div>
<?php
	echo maoo_like_js();
	echo maoo_guanzhu_js();
?>
<?php include('footer.php'); ?>
