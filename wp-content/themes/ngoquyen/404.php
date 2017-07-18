<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content">
		<div class="content-block">
			<div class="box-title">
				<div class="blue">
					<h3><?php _e('404 Lỗi không tìm thấy trang','vn-news'); ?></h3>
				</div>
			</div>
			<div class="post-content clearfix">
				<p><?php _e("Xin lỗi, không tìm thấy dữ liệu trong trang này.","vn-news"); ?></p>
				<?php _e('Bạn thử <a href="javascript:history.back()">&laquo; quay lại</a> hay có thể tìm kiếm ở ô bên dưới:','vn-news'); ?>
				<?php include (TEMPLATEPATH . "/searchform.php"); ?>
			</div>
		</div>
	</div>
</div>

<div id="contentright">
	<div id="sidebar1">
		
		<?php if (get_option("show_subscribe")) include (TEMPLATEPATH . '/subscribe-form.php'); ?>
		<ul>
		<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
			<?php include (TEMPLATEPATH . '/sidebar-1.php'); ?>
		<?php endif; ?>
		</ul>
	</div>
</div>

<?php get_footer(); ?>
