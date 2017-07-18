<?php
/*
Template Name: Sitemap
*/
?>

<?php get_header(); ?>

<div id="contentleft">
	<div id="main-content" style="width:100%;">
		<div class="content-block">
			<div class="box-title">
				<div class="blue">
					<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php the_title_attribute(); ?></h3>
				</div>
			</div>
			<?php
			if (function_exists('ddsg_create_sitemap'))
				echo ddsg_create_sitemap();
			else
				echo '<div class="post-content clearfix"><p class="error">Plugin required: <strong>Dagon Design Sitemap Generator</strong> - <a href="http://www.dagondesign.com/articles/sitemap-generator-plugin-for-wordpress/" target="_blank"><strong>Install now !</strong></a></p></div>';
			?>
		</div>
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