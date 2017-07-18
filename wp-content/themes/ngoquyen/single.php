<?php get_header(); ?>
<div id="contentleft">
	<div id="main-content" style="width:100%;">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<div class="content-block">
					<div class="box-title">
						<div class="blue">
							<h3><a href="<?php bloginfo('url'); ?>"><?php _e('Home','vn-news'); ?></a> &raquo; <?php the_category(', ') ?></h3>
						</div>
					</div>
					<div class="post-content clearfix" id="post-<?php the_ID(); ?>">
						<div class="post-info">
							<table>
							  <tr>
								<td><span class="date">
									<?php the_time(__('d-m-Y','vn-news')) ?> <?php the_time(__('H:i','vn-news')) ?> - </span>
									<?php edit_post_link(__('Chỉnh sửa','vn-news'), '[', ']'); ?>
									<?php if(function_exists('the_views')) { the_views(); } ?>
								</td>
								<td>
									<?php if(function_exists('fontResizer_place')) { fontResizer_place(); } ?>
									<div class="sendlink"><?php if(function_exists('wp_email')) { email_link(); } ?></div>
								</td>
							  </tr>
							</table>
							
						</div>
						<p class="title-post"><?php the_title_attribute(); ?></p>
						
						<div class="entry clearfix">
							<div class="content clearfix"><?php the_content(); ?></div>
							<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:','vn-news').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
							<?php if(function_exists('st_related_posts')) : ?>
							<!--
							<div id="related_posts">
							
								<h2 style="margin-top:10px;color:red;"><?php _e('Các tin khác:','vn-news'); ?></h2>
								
								
								<ul>
								  <?php do_action(
									'related_posts_by_category',
									array(
										'orderby' => 'RAND',
										'order' => 'DESC',
										'limit' => 7,
										'echo' => true,
										'before' => '<li>',
										'inside' => '',
										'outside' => '',
										'after' => '</li>',
										'rel' => 'nofollow',
										'type' => 'post',
										'message' => 'Không có tin liên quan'
									  )
									) ?>
								</ul>
							
							</div>
							-->
							<?php endif; ?>
						</div>
						<?php the_tags('<div class="tags" style="font-size:12px;padding-bottom:0px;margin-top:10px;">'.__('Tags:','vn-news').' ',' &middot; ','</div>'); ?>
						<?php 	$permalink 	 = urlencode(get_permalink($post->ID));
								$title 		 = urlencode($post->post_title);
								$title 		 = str_replace('+','%20',$title);
						?>
						
						<?php if(function_exists('the_ratings')) { echo '<div style="float:right;margin-top:10px;">'; the_ratings(); echo '</div>'; } ?>
					</div>
					<div style="border-top:1px solid #ccc;padding:0px;margin:0px;"></div>
					
				</div>
				<?php comments_template(); ?>
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
		
		<?php if (get_option("show_subscribe")) include (TEMPLATEPATH . '/subscribe-form.php'); ?>
		<ul>
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
				<?php include (TEMPLATEPATH . '/sidebar-1.php'); ?>
			<?php endif; ?>
		</ul>
	</div>
</div>

<?php get_footer(); ?>
