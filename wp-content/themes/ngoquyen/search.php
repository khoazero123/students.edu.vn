<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content">
	<?php if (have_posts()) : ?>
		<?php $post = $posts[0]; ?>
		
		<div class="content-block">
			<div class="box-title">
				<div class="blue">
					<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php _e('Kết quả tìm kiếm cho','vn-news'); ?> '<?php echo $s; ?>'</h3>
				</div>
			</div>
		
			<?php while (have_posts()) : the_post(); ?>
				<div class="post-content clearfix" id="post-<?php the_ID(); ?>" style="border-bottom:1px solid #ccc;">
					<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h3>
					
					<?php if ( get_settings( 'show_thumb' ) && get_post_meta($post->ID, 'Image', true) ) { $img_url = get_post_meta($post->ID, 'Image', true); ?>
					<div class="post-thumbnail">
						<?php if ( get_settings( 'thumb_resizer' ) ) { ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_settings( 'thumb_width' ); ?>" height="<?php echo get_settings( 'thumb_height' ); ?>" src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo str_replace(get_bloginfo('wpurl').'/','',$img_url); ?>&amp;w=<?php echo get_settings( 'thumb_width' ); ?>&amp;h=<?php echo get_settings( 'thumb_height' ); ?>&amp;zc=1&amp;q=100" alt="<?php the_title_attribute(); ?>" /></a>
						<?php } elseif ((strpos($img_url,'wp-content') == 0) && file_exists($img_url)) { ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_settings( 'thumb_width' ); ?>" height="<?php echo get_settings( 'thumb_height' ); ?>" src="<?php echo get_bloginfo('wpurl') . '/' . $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
						<?php } else { ?>
						<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_settings( 'thumb_width' ); ?>" height="<?php echo get_settings( 'thumb_height' ); ?>" src="<?php echo $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
						<?php } ?>
					</div>
					<?php } ?>			
					<div class="content clearfix"><?php the_excerpt(); ?></div>
					<?php the_tags('<div class="tags" style="font-size:12px;padding-bottom:0px;margin-top:20px;">Tags: ',' &middot; ','</div>'); ?>
				</div>
			<?php endwhile; ?>
			<?php page_navi(); ?>
		</div>
	<?php else : ?>
		<div class="content-block">
			<div class="box-title">
				<div class="blue">
					<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php _e('Kết quả tìm kiếm: Không tìm thấy!','vn-news'); ?></h3>
				</div>
			</div>
			<div class="post-content clearfix">
				<p style="margin-bottom:10px;"><?php _e("Xin lỗi! Không có kết quả cho từ khóa này.",'vn-news'); ?></p>
				<?php include (TEMPLATEPATH . "/searchform.php"); ?>
			</div>
		</div>
	<?php endif; ?>

	</div>
</div>

<div id="contentright">
	<div id="sidebar1">
		
		<?php if (get_settings("show_subscribe")) include (TEMPLATEPATH . '/subscribe-form.php'); ?>
		<ul>
		<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
			<?php include (TEMPLATEPATH . '/sidebar-1.php'); ?>
		<?php endif; ?>
		</ul>
	</div>
</div>

<?php get_footer(); ?>
