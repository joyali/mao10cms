<?php include('header.php'); ?>
<style>
	body {padding-bottom: 60px;}
	footer {display: none; }
</style>
<div class="container">
	<div class="row single-post">
		<div class="col-sm-10 col-sm-offset-1 col">
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
            <div class="panel panel-default single-recommend-post">
                <div class="panel-heading">
                    <i class="glyphicon glyphicon-bookmark"></i> 推荐阅读
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?php 
                            if($redis->scard('topic_post_id:'.$redis->hget('post:'.$id,'topic'))>0) :
                            $topic_post_db1 = $redis->sort('topic_post_id:'.$redis->hget('post:'.$id,'topic'),array('sort'=>'desc','limit' =>array(0,3)));
                            $topic_post_db2 = $redis->sort('post_id',array('sort'=>'desc','limit' =>array(0,10)));
                            $topic_post_db = array_merge($topic_post_db1,$topic_post_db2);
                            $topic_post_db3 = array_unique($topic_post_db);
                            $topic_post_db3 = array_slice($topic_post_db3,0,3);
                            foreach($topic_post_db3 as $page_id) :
                        ?>
                        <div class="col-md-4 col">
                            <div class="thumbnail">
                                <a class="img-div" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
                                    <img src="<?php echo maoo_fmimg($page_id); ?>">
                                </a>
                                <div class="caption">
                                    <a class="wto" href="<?php echo maoo_url('post','single',array('id'=>$page_id)); ?>">
                                        <?php echo $redis->hget('post:'.$page_id,'title'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
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
	</div>
</div>
<div class="post-footer">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-6 col left">
				<a class="avatar" href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>">
					<img src="<?php echo maoo_user_avatar($author); ?>" alt="<?php echo maoo_user_display_name($author); ?>">
				</a>
				<a href="<?php echo maoo_url('user','index',array('id'=>$author)); ?>"><?php echo maoo_user_display_name($author); ?></a>
				<span class="ml-5 mr-5">发表于</span>
				<?php echo date('Y/m/d',$redis->hget('post:'.$id,'date')); ?>
			</div>
			<div class="col-sm-6 col right text-right hidden-xs hidden-sm">
				<ul class="list-inline mb-0">
					<li>
						<?php echo maoo_like_btn($id); ?>
					</li>
					<li>
						<a class="goto" href="#react-post-comment-box">
							<i class="glyphicon glyphicon-comment"></i> <?php echo $redis->scard('post_comment_id:'.$id); ?>
						</a>
					</li>
					<li>
						<span id="share" class="pr">
							<i class="glyphicon glyphicon-share"></i> 分享
							<div id="share-box">
								<div class="bdsharebuttonbox">
									<a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
									<a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
									<a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
									<a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
								</div>
								<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"<?php echo $redis->hget('post:'.$id,'title'); ?>","bdMini":"2","bdMiniList":false,"bdPic":"<?php echo $redis->hget('post:'.$id,'fmimg'); ?>","bdStyle":"1","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
							</div>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
	echo maoo_like_js();
	echo maoo_guanzhu_js();
?>
<?php include('footer.php'); ?>
