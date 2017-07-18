<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content">
	<?php if (have_posts()) : ?>
		<?php $post = $posts[0]; ?>
		
		<div class="content-block">
			<div class="box-title">
				<div class="blue"><h3>
			 	  <?php /* If this is a tag archive */ if( is_tag() ) { ?>
					<?php _e('Tag archive for','vn-news'); ?> &#8216;<?php single_tag_title(); ?>&#8217;
			 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
					<?php _e('Dayily Archive for','vn-news'); ?> <?php the_time('F jS, Y'); ?>
			 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
					<?php _e('Monthly Archive for','vn-news'); ?> <?php the_time('F, Y'); ?>
			 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
					<?php _e('Yearly Archive for','vn-news'); ?> <?php the_time('Y'); ?>
			 	  <?php } ?></h3>
				</div>
			</div>
		
			<?php while (have_posts()) : the_post(); ?>
			<div class="post-content clearfix" id="post-<?php the_ID(); ?>" style="border-bottom:1px solid #ccc;">
				<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h3>
				
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
