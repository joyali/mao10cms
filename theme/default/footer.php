<footer>
	<div class="container">
		©2014-2016 <a href="http://www.mao10.com/">Mao10CMS V6</a> 内容型网络商城建站系统
	</div>
</footer>
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form method="get" action="<?php echo $redis->get('site_url'); ?>">
				    <div class="form-group">
				        <input type="text" class="form-control" name="s" placeholder="搜索文章、话题、用户">
				    </div>
				</form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#searchModal').on('show.bs.modal', function (e) {
        $('#header').removeClass('navshow');
        $('#nav-show-bg').css('display','none');
    });
</script>
<?php if($redis->get('promod')!=1) : ?>
<div class="pro-side-fixed hidden-xs hidden-sm">
	<?php if($_GET['m']=='pro' && $_GET['a']=='single') : ?>
	<?php if($redis->hget('user:'.maoo_user_id(),'user_level')==10) : ?>
	<a href="<?php echo $redis->get('site_url'); ?>?m=admin&a=editpro&id=<?php echo $id; ?>">
		<i class="glyphicon glyphicon-edit"></i>
		<div class="clearfix"></div>
		编辑商品
	</a>
	<?php endif; ?>
	<?php if(maoo_user_id()) : ?>
		<?php if($redis->hget('user:'.maoo_user_id(),'user_level')!=10) : ?>
		<a class="last" href="#" data-toggle="modal" data-target="#consultModal">
			<i class="glyphicon glyphicon-envelope"></i>
			<div class="clearfix"></div>
			咨询店家
		</a>
		<?php endif; ?>
	<?php else : ?>
	<a class="last" href="<?php echo $redis->get('site_url'); ?>?m=user&a=login<?php if($_GET['a']=='logout') : echo '&noreferer=yes'; endif; ?>">
		<i class="glyphicon glyphicon-envelope"></i>
		<div class="clearfix"></div>
		咨询店家
	</a>
	<?php endif; ?>
	<?php endif; ?>
	<a href="#toptg" class="goto">
		<i class="glyphicon glyphicon-circle-arrow-up"></i>
		<div class="clearfix"></div>
		返回顶部
	</a>
</div>
<?php endif; ?>
<?php include('cart.php'); ?>
<!--[if lte IE 8]>
<div class="browser-msg text-center">
	<p class="txt">
		为了获得更好的浏览体验，建议使用以下浏览器：
	</p>
	<ul class="browsers">
		<li>
			<a href="http://www.google.cn/intl/zh-cn/chrome/browser/desktop/index.html"
			target="_blank">
				<img class="icon" src="<?php echo $redis->get('site_url'); ?>/public/img/chrome.png" width="50" height="50">
				<span class="name">
					Chrome
				</span>
			</a>
		</li>
		<li>
			<a href="http://www.firefox.com.cn" target="_blank">
				<img class="icon" src="<?php echo $redis->get('site_url'); ?>/public/img/firefox.png" width="50" height="50">
				<span class="name">
					Firefox
				</span>
			</a>
		</li>
		<li>
			<a href="http://www.apple.com/cn/safari/" target="_blank">
				<img class="icon" src="<?php echo $redis->get('site_url'); ?>/public/img/safari.png" width="50" height="50">
				<span class="name">
					Safari
				</span>
			</a>
		</li>
		<li>
			<a href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" target="_blank">
				<img class="icon" src="<?php echo $redis->get('site_url'); ?>/public/img/ie.png" width="50" height="50">
				<span class="name">
					IE9及更高版
				</span>
			</a>
		</li>
	</ul>
</div>
<![endif]-->
<script src="<?php echo $redis->get('site_url'); ?>/public/js/cat.js"></script>
<?php if($_GET['done']) : ?>
<div class="done-message animated">
	<?php echo $_GET['done']; ?>
	<i class="glyphicon glyphicon-remove"></i>
</div>
<script>
	 $('.done-message i').click(function(){
		 $('.done-message').animate({top:"-61px"});
	 });
	 setTimeout("$('.done-message').animate({top:'-61px'})",3000);
</script>
<?php endif; ?>
<?php echo maoo_guanzhu_js(); ?>
<?php echo $redis->get('statistical_code'); ?>
</body>
</html>
