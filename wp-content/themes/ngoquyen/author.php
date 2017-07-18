<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content">
		<?php if (have_posts()) : ?>
			<?php $post = $posts[0]; ?>
			<?php
				if(isset($_GET['author_name'])) :
				$curauth = get_userdatabylogin($author_name);
				else :
				$curauth = get_userdata(intval($author));
				endif;
			?>
			
			<div class="content-block">
				<div class="box-title">
					<div class="blue">
						<h3><?php _e('About','vn-news'); ?> <?php echo $curauth->nickname; ?></h3>
					</div>
				</div>
				<div class="author-info">
					<span class="author-avatar"><?php echo get_avatar($curauth->user_email,64,get_bloginfo('template_directory') . '/images/avatar.gif'); ?></span>
					<p><strong><?php echo $curauth->nickname; ?></strong> - <?php printf(__('who has written %1$s posts on ',"vn-news"), get_the_author_posts()); ?> <strong><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></strong>.</p>
					<p><?php echo $curauth->description; ?></p>
					<span class="author-email"><?php echo '<a href="mailto:'. $curauth->user_email .'">'.__("Email This Author","vn-news").'</a>'; ?></span> | 
					<span class="feed"><a href="<?php echo get_author_posts_url( $author, ""); ?>feed/" title="<?php printf(__('Subscribe to %1$s','vn-news'), $curauth->nickname); ?>"><?php echo $curauth->nickname; ?>'s RSS</a></span>
				</div>
			</div>
			
			<div class="content-block">
				<div class="box-title">
					<div class="blue">
						<h3><?php printf(__('Post by %1$s ','vn-news'), $curauth->nickname); ?></h3>
					</div>
				</div>
				<?php while (have_posts()) : the_post(); ?>
					<div class="post-content clearfix" id="post-<?php the_ID(); ?>" style="border-bottom:1px solid #ccc;">
						<h3><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h3>
						<div class="post-info">
							<span class="date"><?php the_time(__('F jS, Y','vn-news')) ?></span> | 
							<?php if(function_exists('the_views')) { ?>
							<span class="views"><?php the_views(); ?></span> | 
							<?php } ?>
							<span class="comments"><a href="<?php the_permalink(); ?>#respond" title="<?php _e('Leave a comment','vn-news'); ?>" ><?php comments_number(__('0 Comments &#187;','vn-news'), __('1 Comment &#187;','vn-news'), __('% Comments &#187;','vn-news')); ?></a></span>
							<?php edit_post_link(__('Edit','vn-news'), '[', ']'); ?>
						</div>
						<?php if ( get_option( 'show_thumb' ) && get_post_meta($post->ID, 'Image', true) ) { $img_url = get_post_meta($post->ID, 'Image', true); ?>
						<div class="post-thumbnail">
							<?php if ( get_option( 'thumb_resizer' ) ) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo str_replace(get_bloginfo('wpurl').'/','',$img_url); ?>&amp;w=<?php echo get_option( 'thumb_width' ); ?>&amp;h=<?php echo get_option( 'thumb_height' ); ?>&amp;zc=1&amp;q=100" alt="<?php the_title_attribute(); ?>" /></a>
							<?php } elseif ((strpos($img_url,'wp-content') == 0) && file_exists($img_url)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php echo get_bloginfo('wpurl') . '/' . $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
							<?php } else { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php echo $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
							<?php } ?>
						</div>
						<?php } ?>
						<div class="content clearfix"><?php the_excerpt(); ?></div>
					</div>
				<?php endwhile; ?>
				<?php page_navi(); ?>
			</div>
		<?php else : ?>
			<div class="content-block">
				<div class="box-title">
					<div class="blue">
						<h3><?php _e('404 Page Not Found','vn-news'); ?></h3>
					</div>
				</div>
				<div class="post-content clearfix">
					<p><?php _e("Sorry, but you are looking for something that isn't here.","vn-news"); ?></p>
					<?php _e('You could try going <a href="javascript:history.back()">&laquo; Back</a> or maybe you can find what your looking for below:','vn-news'); ?>
					<?php include (TEMPLATEPATH . "/searchform.php"); ?>
				</div>
			</div>
		<?php endif; ?>
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

