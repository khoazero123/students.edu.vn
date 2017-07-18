<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	
	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie  ?>
			<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','vn-news'); ?></p>
		<?php
			return;
		}
	}
?>

<?php if ($comments) {

		$numPingBacks = 0;
		$numComments  = 0;

		foreach ($comments as $comment) {
			if (get_comment_type() != "comment") { $numPingBacks++; }
			else { $numComments++; }
		}
	} else $numComments = 0;
?>
	
<div class="commentlist" id="respond">
	<div style="overflow:hidden;">
	
	<div class="tabber">
		<div class="tabbertab">
			<?php if ($numComments != 0) { ?>
			
			<h3><?php _e('Bình Luận','vn-news'); ?> (<?php echo $numComments; ?>)</h3>
			<ul class="pop">
			
			<?php foreach ($comments as $comment) : ?>
				<?php if (get_comment_type()=="comment") : ?>
				<?php $isByAuthor = false; if($comment->comment_author_email == get_the_author_email()) $isByAuthor = true;	?>
				
				<li class="<?php if($isByAuthor) echo 'comment-alt'; else echo 'comment'; ?>" id="comment-<?php comment_ID() ?>">
					<?php echo get_avatar($comment->comment_author_email,50); ?>
				
					<span style="font-size:16px;font-weight:bold;margin:0;color:#333;"><?php comment_author_link() ?> <?php if($isByAuthor ) { echo __('(author)','vn-news');} ?></span> 
					
					<?php if ($comment->comment_approved == '0') : ?>
					<em>(Bình luận của bạn sẽ được kiểm duyệt trước khi đăng)</em>
					<?php endif; ?>
					<br />
					
					<small><a href="#comment-<?php comment_ID() ?>"><?php printf( __('%1$s vào lúc %2$s', 'vn-news'), get_comment_date(__('F jS, Y', 'vn-news')), get_comment_time(__('H:i', 'vn-news')) ); ?></a> <?php edit_comment_link(__('[Edit]','vn-news'),'&nbsp;&nbsp;',''); ?></small>
					
					<?php if (function_exists("CID_init")) { echo '<br />'; CID_print_comment_flag(); echo ' '; CID_print_comment_browser(); } ?>
					
					<div style="margin:10px 0;"><?php comment_text() ?></div>
					
					<div style="clear:both;"></div>
				</li>
				<?php endif; ?>
			<?php endforeach; ?>
			
			</ul>
			
			<?php } else { ?>
			
			<h3><?php _e('Bình Luận','vn-news'); ?> (0)</h3>
			<div style="margin:10px 0 10px 10px;"><?php _e('Không có bình luận nào được đăng trong bài viết này.','vn-news'); ?></div>
			<?php } ?>
			
			<div style="clear:both;"></div>
		</div>
		
		
	</div>
	</div>

	<?php if ('open' == $post->comment_status) : ?>
	<div style="margin:20px 10px 10px 10px;">
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
		<div class="error" style="font-weight:bold;"><?php printf(__('Bạn phải <a href="%s">đăng nhập</a> để gửi bình luận.', 'vn-news'), wp_login_url(get_permalink())); ?></div>
	<?php else : ?>
		<h1 style="margin-bottom:5px;"><?php _e('Gửi bình luận','vn-news'); ?></h1>
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		<?php if ( $user_ID ) : ?>
			<?php _e('Đăng nhập bởi', 'vn-news'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><strong><?php echo $user_identity; ?></strong></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Thoát khỏi tài khoản', 'vn-news'); ?>"><?php _e('Đăng xuất &raquo;', 'vn-news'); ?></a>
		<?php else : ?>
			<label class="form-l"><?php _e('Họ và tên', 'vn-news'); ?> <?php if ($req) _e('(Bắt buộc)', 'vn-news'); ?></label>
			<input class="your-name" type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="42" tabindex="1" />
			<label class="form-l"><?php _e('Địa chỉ E-Mail', 'vn-news');?> <?php if ($req) _e('(Bắt buộc)', 'vn-news'); ?> <span style="color:#666666; font-weight:normal;"><?php _e('Sẽ được hiển thị','vn-news'); ?></span></label> 
			<input class="your-email" type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="42" tabindex="2" />
			<label class="form-l" style="color:#006600;"><?php _e('Địa chỉ trang web', 'vn-news'); ?></label>
			<input class="your-site" type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="42" tabindex="3" />
			<?php 
			/****** Math Comment Spam Protection Plugin ******/
			if ( function_exists('math_comment_spam_protection') ) { 
				$mcsp_info = math_comment_spam_protection(); ?>
			<label class="form-l" for="mcspvalue"><strong><?php echo $mcsp_info['operand1'] . ' + ' . $mcsp_info['operand2'] . ' = ? ' . __('(required)','vn-news'); ?></strong></label>			
			<input class="spam-field" type="text" name="mcspvalue" id="mcspvalue" value="" size="42" tabindex="4" />
			<input type="hidden" name="mcspinfo" value="<?php echo $mcsp_info['result']; ?>" />
			<?php } // if function_exists... ?>			
		<?php endif; ?>
		<textarea class="your-message" name="comment" id="comment" cols="50%" rows="6" tabindex="5"></textarea>
		<?php do_action('comment_form', $post->ID); ?>
		<input class="comment-box-submit" name="submit" type="submit" id="submit" tabindex="6" value="<?php _e('Gửi bình luận', 'vn-news'); ?>" />
		<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
		<div style="padding:5px 0;"></div>
		</form>
	<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
