<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content">
	<?php if (have_posts()) : ?>
		
		<div class="content-block">
		<?php $post = $posts[0]; ?>
		<?php
			$object = $wp_query->get_queried_object();

			// Get parents of current category
			$parent_id  = $object->category_parent;
			$cat_breadcrumbs = '';
			while ($parent_id) {
				$category   = get_category($parent_id);
				$cat_breadcrumbs = '<a href="' . get_category_link($category->cat_ID) . '">' . $category->cat_name . '</a> &raquo; ' . $cat_breadcrumbs;
				$parent_id  = $category->category_parent;
			}

			$result = $cat_breadcrumbs . $object->cat_name;
		?>
			<div class="box-title">
				<div class="blue">
					<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php echo $result; ?></h3>
				</div>
			</div>
		
			<?php while (have_posts()) : the_post(); ?>
				<div class="post-content clearfix" id="post-<?php the_ID(); ?>" style="border-bottom:1px solid #ccc;">
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
				</div>
			<?php endwhile; ?>
			<?php page_navi(); ?>
		</div>
	<?php else : ?>
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
