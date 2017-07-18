<?php get_header(); ?>

<div id="contentleft">
	<div id="contentslider">
	<?php include (ABSPATH . '/wp-content/plugins/front-slider/front-slider.php'); ?>
	</div>
	<?php if (get_option( 'show_feature' )) { ?>
	<div id="my-glider">
		<div class="scroller">
			<div class="content">
				<?php $display_categories = get_option("feature_cat"); ?>
				<?php $post_cat = get_option('feature_count'); if (($post_cat==0) || ($post_cat>20)) { $post_cat=5; } ?>
				<?php $result = '';  $i=0; ?>
				<?php query_posts('showposts='.$post_cat.'&cat='.$display_categories); ?>
				<?php while (have_posts()) : the_post(); ?>
					<div class="section" id="section-<?php the_ID(); ?>">
						<?php if ( get_option( 'show_featured_thumb' ) || get_post_meta($post->ID, 'Image', true) ) { $img_url = get_post_meta($post->ID, 'Image', true); ?>
						<div class="featured-post-thumbnail">
							<?php if ( get_option( 'thumb_resizer' ) ) { ?>
							<?php show_featured_thumb('featured_thumb_width', 'featured_thumb_height'); ?>
							<?php } elseif ((strpos($img_url,'wp-content') == 0) && file_exists($img_url)) { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'featured_thumb_width' ); ?>" height="<?php echo get_option( 'featured_thumb_height' ); ?>" src="<?php echo get_bloginfo('wpurl') . '/' . $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
							<?php } else { ?>
							<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'featured_thumb_width' ); ?>" height="<?php echo get_option( 'featured_thumb_height' ); ?>" src="<?php echo $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
							<?php } ?>
						</div>
						<?php } ?>					
						<div class="feature-entry">
							<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h2>
							
							<div class="content"><?php the_excerpt(); ?></div>
							<?php $i++; ?>
							<?php $result .= '<li><a href="#section-' .$post->ID. '">'.$i.'</a></li>'."\n"; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<div class="controls">
			<ul>
				<?php echo $result; ?>
			</ul>
			<div class="clearfix"></div>
		</div>
	</div>

	<script type="text/javascript" charset="utf-8">
		var my_glider = new Glider('my-glider', {duration:0.5, autoGlide:true, frequency:6});
	</script>

	<div style="clear:both;"></div>
	<?php } ?>
	
	<div id="main-content">
		<?php $display_categories = get_option("home_cat"); ?>
		<?php if (is_array($display_categories)) { ?>
			<?php $post_cat = get_option('home_cat_count'); if ($post_cat==0) { $post_cat=3; } ?>

			<?php foreach ($display_categories as $category) { ?>
				<?php query_posts("showposts=1&cat=$category");	?>
				<?php $cat_details = get_category($category); ?>
				<?php
					while (have_posts()) : the_post(); 
				?>
					<div class="content-block">
						<div class="box-title">
							<div class="b-post">
								<h3><a href="<?php echo get_category_link( $cat_details->cat_ID ); ?>"><?php echo $cat_details->cat_name ?> &raquo;</a></h3>
							</div>
						</div>
						<div class="post-content clearfix">
							<h3><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title_attribute(); ?></a></h3>
							
							<?php if ( get_option( 'show_thumb' ) || get_post_meta($post->ID, 'Image', true) ) { $img_url = get_post_meta($post->ID, 'Image', true); ?>
							<div class="post-thumbnail">
								<?php if ( get_option( 'thumb_resizer' ) ) { ?>
								<?php show_thumb('thumb_width', 'thumb_height'); ?>
								<?php } elseif ((strpos($img_url,'wp-content') == 0) && file_exists($img_url)) { ?>
								<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php echo get_bloginfo('wpurl') . '/' . $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
								<?php } else { ?>
								<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php echo $img_url; ?>" alt="<?php the_title_attribute(); ?>" /></a>	
								<?php } ?>
							</div>
							<?php } ?>
							<div class="content clearfix"><?php the_excerpt(); ?></div>
							<?php $recent = new WP_Query('cat='.$category.'&showposts='.$post_cat.'&offset=1'); ?> 
							<?php if ($recent->have_posts()) : ?>
								<div style="clear:both;"></div>
								<div class="post-summary">
									<span style="color:#C10000;font-weight:bold;font-size: 92%;letter-spacing: 1px;text-transform: uppercase;"><?php printf( __('Các tin khác:','vn-news'), $cat_details->cat_name ); ?></span>
									<ul>
									<?php while($recent->have_posts()) : $recent->the_post();?>
										<li><a href="<?php the_permalink() ?>" title="Permanent Link to <?php the_title(); ?>" rel="bookmark"><?php the_title() ?></a></li>
									<?php endwhile; ?>
									</ul>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php } ?>
		<?php } else { ?>
        
        
			<div class="content-block">
				<div class="box-title">
					<div class="blue">
						<h3><?php _e('Home Page Error!','vn-news'); ?></h3>
					</div>
				</div>
				<div class="post-content clearfix">
					<p><?php _e('You have not selected which Categories will be shown on Home Page.','vn-news'); ?></p>
					<?php _e('Please select them in WP-Admin -> Appearance -> VN-News Options.','vn-news'); ?>
				</div>
			</div>
		<?php } ?>
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