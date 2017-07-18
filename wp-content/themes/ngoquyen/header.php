<?php
/** 
* New header by max2max
* You can read the document at Code24h.com
* Since 1.4	
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php
	/*
	 * New title styles. Better for SEO. LOL
	 */
	global $page, $paged;

	wp_title( '&mdash;', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " &mdash; $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'vn-news' ), max( $paged, $page ) );

	?></title>
 
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />

<?php if (is_home()) { 
// Just add Glidder if is home
?>

<?php } ?>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/scripts/tabber.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory');?>/scripts/ie6_script_other.js?<?php echo time(); ?>"></script>
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if (get_option("sidebar_1_pos") == "right") { ?>
<style type="text/css">
#main-content {float:left;}
#sidebar2 {float:right;}
</style>
<?php } ?>

<?php 
/* We add some JavaScript to pages with the comment form
 * to support sites with threaded comments (when in use).
*/
		if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
// Do not delete wp_head() if you want your blog working 
wp_head(); ?>

<?php if(get_option("header_insert")) echo stripslashes(get_option("header_insert")); ?>

</head>
<body>

<div id="wrapper" class="clearfix">
	<div id="header" class="clearfix">
		<div class="sitehead-left">
			<!--<object width="960" height="300">
				<param name="movie" value="<?php bloginfo('stylesheet_directory'); ?>/images/banner.swf">
				<param name="wmode" value="transparent">
				<embed src="<?php bloginfo('stylesheet_directory'); ?>/images/banner.swf" width="960" height="300" wmode="transparent" />
			</object> -->
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/bn-42913.jpg" width="960" height="137" border="0">
			<!--- <h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
			<h2 id="description"><?php bloginfo('description'); ?></h2> -->
		
		</div> <!--sitehead-left-->

		<!-- <div id="page-nav" class="clearfix">
			<ul>
				<li class="<?php if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_option('home'); ?>"><?php _e('Home','vn-news'); ?></a></li>
				<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
			</ul>
		</div> -->
	</div> <!--Sitehead-right-->
	<div id="menungang">
	<?php
		wp_nav_menu(array(
			'theme_location' => 'top',
			'container' => '',
			'menu_id' => '',
			'menu_class' => 'dropdown'
			));
	?>
	</div>
	<div id="page" class="clearfix">