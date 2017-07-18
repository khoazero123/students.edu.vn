<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
	<div>
		<input type="text" value="<?php _e('Nhập từ khóa...','vn-news'); ?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" name="s" id="s" class="searchfield" />
		<input type="submit" id="s-submit" value="<?php _e('Tìm kiếm','vn-news'); ?>" />
	</div>
</form>
