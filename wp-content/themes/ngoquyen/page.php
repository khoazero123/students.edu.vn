<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content" style="width:100%;">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<div class="content-block">
					<div class="box-title">
						<div class="blue">
							<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php the_title_attribute(); ?></h3>
						</div>
					</div>
					<div class="post-content clearfix" id="post-<?php the_ID(); ?>">
					
						<!-- <h1><?php the_title_attribute(); ?></h1> Title PAGE-->
						<div class="content clearfix"><?php the_content(); ?></div>
						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>
				</div>
                				

			<?php endwhile; else: ?>
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
		
		<?php if (get_settings("show_subscribe")) include (TEMPLATEPATH . '/subscribe-form.php'); ?>
		<ul>
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
				<?php include (TEMPLATEPATH . '/sidebar-1.php'); ?>
			<?php endif; ?>
		</ul>
	</div>
</div>

<?php get_footer(); ?>
