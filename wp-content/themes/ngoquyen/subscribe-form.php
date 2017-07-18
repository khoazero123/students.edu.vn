<div id="subscribe">
	<h2><?php _e('Enter your email address:','vn-news'); ?></h2>
	<form action="http://www.feedburner.com/fb/a/emailverify" method="post" target="popupwindow" onsubmit="window.open('http://www.feedburner.com/fb/a/emailverifySubmit?feedId=<?php echo get_settings("feedburner_ID"); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true" id="subscribeform">
		<div>
			<input type="text" value="" name="email" class="subscribeinput" />
			<input type="hidden" value="http://feeds.feedburner.com/~e?ffid=<?php echo get_settings("feedburner_ID"); ?>" name="url"/><input type="hidden" value="VN News" name="title"/><input type="hidden" name="loc" value="en_US"/>
			<input type="image" src="<?php bloginfo('template_directory'); ?>/images/subscribesubmit.png" class="subscribesubmit"/>
		</div>
	</form>
</div>	
<div class="clear"></div>