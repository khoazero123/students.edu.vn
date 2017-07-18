<div class="clear"></div>
</div>
<div id="footer" class="clearfix">
	<div class="footer-content">
		<span style="float:left;"><a href="<?php echo get_option('home'); ?>"><strong><?php bloginfo('name'); ?></strong></a> </span><br>
		<span style="float:left;"><strong><?php _e('Địa chỉ:','vn-news'); ?></strong> <?php _e('Trường THCS xã Đồng Văn, huyện Thanh Chương, tỉnh Nghệ An','vn-news'); ?></span><br>
		<span style="float:left;"><strong><?php _e('Điện thoại:','vn-news'); ?></strong> <?php _e('0383933230','vn-news'); ?></span><br>
		<span style="float:left;"><strong><?php _e('Email:','vn-news'); ?></strong> lienhe@students.edu.vn</span>
		<span style="float:right;">
			<?php wp_register('',' &middot; '); ?> <?php wp_loginout(); ?> &middot;
			<a href="http://validator.w3.org/check?uri=referer" target="_bank">XHTML</a>
		</span>
		<div class="clear"></div>
	</div>
</div>
</div>

<?php wp_footer(); ?>

<?php if(get_option("footer_insert")) echo stripslashes(get_option("footer_insert")); ?>

</body>
</html>
