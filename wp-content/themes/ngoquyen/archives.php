<?php
/*
Template Name: Archives Page
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
			if (function_exists('srg_clean_archives')) {
				echo '<div class="post-content clearfix"><h1>';
				the_title_attribute();
				echo '</h1>';
				srg_clean_archives();
				echo '</div>';
			} else {
			?>			
			<div class="archive-left">
				<h4><?php _e('Categories','vn-news'); ?></h4>
				<ul class="categories">
					<?php wp_list_cats('sort_column=name&show_count=1&hierarchical=1'); ?>
				</ul>
				<br />
				
				<h4><?php _e('Browse By Month','vn-news'); ?></h4>
				<ul class="archives">
					<?php wp_get_archives('type=monthly&show_post_count=1'); ?>
				</ul>
				<br />

				<h4><?php _e('Browse By Year','vn-news'); ?></h4>
				<ul class="archives">
					<?php wp_get_archives('type=yearly&show_post_count=1'); ?>
				</ul>				
			</div>

			<div class="archive-right">
				<h4><?php _e('Popular Tags','vn-news'); ?></h4>
				<div style="line-height:25px;text-align:justify;" class="tagcloud">
					<?php wp_tag_cloud('smallest=9&largest=20'); ?>
				</div>
				<div style="border-top:1px dotted #ccc;margin:10px 0;"></div>

				<?php if(function_exists('get_most_viewed')) { ?>
				<h4><?php _e('Most Viewed Posts','vn-news'); ?></h4>
				<ul>
					<?php get_most_viewed('post',10); ?>
				</ul>
				<div style="border-top:1px dotted #ccc;margin:10px 0;"></div>
				<?php } ?>

				<?php if(function_exists('get_highest_rated')) { ?>
				<h4><?php _e('Highest Rated Posts','vn-news'); ?></h4>
				<ul>
					<?php get_highest_rated('post',10); ?>
				</ul>
				<div style="border-top:1px dotted #ccc;margin:10px 0;"></div>
				<?php } ?>
				
				<h4><?php _e('Most Recent Posts','vn-news'); ?></h4>
				<ul>
					<?php get_archives('postbypost', '10', 'custom', '<li>', '</li>'); ?>
				</ul>
			</div>
			
			<div class="clear"></div>
			<?php } ?>
			
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
