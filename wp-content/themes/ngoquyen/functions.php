<?php 
//Đăng ký muốn dùng Sidebar (Widget)
//Đăng ký muốn dùng menu
add_theme_support('menus');
//Thêm vị trí cho menu
register_nav_menus(array(
	'top' => 'Top Menu',
));
?>
<?php
add_editor_style('editor-style.css');
# Get image attachment (sizes: thumbnail, medium, full)
function get_thumb($postid=0, $size='full') {
	if ($postid<1) 
	$postid = get_the_ID();
	$thumb = get_post_meta($postid, "Image", TRUE); // Declare the custom field for the image
	if ($thumb != null or $thumb != '') {
		echo $thumb; 
	}
	elseif ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => '1',
		'post_mime_type' => 'image', )))
		foreach($images as $image) {
			$thumbnail=wp_get_attachment_image_src($image->ID, $size);
			?>
<?php echo $thumbnail[0]; ?>
<?php
		}
	else {
		echo get_bloginfo ('stylesheet_directory');
		echo '/images/image-pending.gif';
	}
}

# Automatically display/resize thumbnails
function show_thumb($width, $height) {
?>
 
<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'thumb_width' ); ?>" height="<?php echo get_option( 'thumb_height' ); ?>" src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php get_thumb($post->ID, 'Image'); ?>&amp;w=<?php echo get_option( 'thumb_width' ); ?>&amp;h=<?php echo get_option( 'thumb_height' ); ?>&amp;zc=1&amp;q=100" alt="<?php the_title_attribute(); ?>" /></a>


<?php

}
function show_featured_thumb($featured_thumb_width, $featured_thumb_height) {
	?>
	<a href="<?php the_permalink(); ?>" rel="bookmark"><img width="<?php echo get_option( 'featured_thumb_width' ); ?>" height="<?php echo get_option( 'featured_thumb_height' ); ?>" src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php get_thumb($post->ID, 'Image'); ?>&amp;w=<?php echo get_option( 'featured_thumb_width' ); ?>&amp;h=<?php echo get_option( 'featured_thumb_height' ); ?>&amp;zc=1&amp;q=100" alt="<?php the_title_attribute(); ?>" /></a>
	<?php 
}

#Since 1.2.3
function theme_init() {
	//$lang = get_option("site_lang");
	$lang = get_option("site_lang");
	if (isset($lang) && $lang) {
		load_textdomain('vn-news', get_template_directory() . '/languages/' . $lang);
	}
}
add_action ('init', 'theme_init');

$ThemeName = "VN-News"; // do not modify this line

#Register Sidebar			
if ( function_exists('register_sidebar') ) {
    register_sidebar(
		array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
        'name' => 'Sidebar 1',
		'id' => 'sidebar-1',
        )
   	);
   	register_sidebar(
   		array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
		'name' => 'Sidebar 2',
		'id' => 'sidebar-2',
		)
	);
}

#Input
function bm_input( $var, $type, $description = "", $value = "", $selected="", $onchange="",$width="60%" ) {
 	echo "\n";
	switch( $type ) {
	    case "text":
	 		echo "<input name=\"$var\" id=\"$var\" type=\"$type\" style=\"width: $width\" class=\"code\" value=\"$value\" onchange=\"$onchange\"/>";
	 		echo "<p style=\"font-size:11px; color:#000; margin:0;\">$description</p>";
			break;
		case "submit":
	 		echo "<p class=\"submit\"><input name=\"$var\" type=\"$type\" value=\"$value\" /></p>";
			break;
		case "option":
			if( $selected == $value ) { $extra = "selected=\"true\""; }
			echo "<option value=\"$value\" $extra >$description</option>";
		    break;
  		case "radio":
			if( $selected == $value ) { $extra = "checked=\"true\""; }
  			echo "<label><input name=\"$var\" id=\"$var\" type=\"$type\" value=\"$value\" $extra /> $description</label><br/>";
  			break;
		case "checkbox":
			if( $selected == $value ) { $extra = "checked=\"true\""; }
  			echo "<label><input name=\"$var\" id=\"$var\" type=\"$type\" value=\"$value\" $extra /> $description</label><br/>";
  			break;
		case "textarea":
		    echo "<textarea name=\"$var\" id=\"$var\" style=\"width: $width; height: 10em;\" class=\"code\">" . stripslashes($value) . "</textarea>";
			echo "<p style=\"font-size:11px; color:#000; margin:0;\">$description</p>";
		    break;
	}

}
#Select
function bm_select( $var, $arrValues, $selected, $label, $description ) {
	if( $label != "" ) {
		echo "<label for=\"$var\">$label</label>";
	}
	echo "<select name=\"$var\" id=\"$var\">\n";
	forEach( $arrValues as $arr ) {
		$extra = "";	
		if( $selected == $arr[ 0 ] ) { $extra = " selected=\"true\""; }
		echo "<option value=\"" . $arr[ 0 ] . "\"$extra>" . $arr[ 1 ] . "</option>\n";
	}
	echo "</select>";
	echo "<p style=\"font-size:11px; color:#000; margin:0;\">$description</p>";
}

function bm_multiSelect( $var, $arrValues, $arrSelected, $label, $description ) {
	if( $label != "" ) {
		echo "<label for=\"$var\">$label</label>";
	}
	echo "<select multiple=\"true\" size=\"7\" name=\"$var\" id=\"$var\" style=\"height:150px;\">\n";
	forEach( $arrValues as $arr ) {
		$extra = "";	
		if( in_array( $arr[ 0 ], $arrSelected ) ) { $extra = " selected=\"true\""; }
		echo "<option value=\"" . $arr[ 0 ] . "\"$extra>" . $arr[ 1 ] . "</option>\n";
	}
	echo "</select>";
	echo "<p style=\"font-size:11px; color:#000; margin:0;\">$description</p>";
}

function bm_th( $title ) {
   	echo "<tr valign=\"top\">";
	echo "<th width=\"33%\" scope=\"row\">$title</th>";
	echo "<td>";
}

function bm_cth() {
	echo "</td>";
	echo "</tr>";
}

#Add Vn-News options page
function bm_addThemePage() {
	global $ThemeName;
	if ( $_GET['page'] == basename(__FILE__) ) {
	    // save settings
		if ( 'save' == $_REQUEST['action'] ) {
			check_admin_referer( 'save-theme-properties' );

			update_option( 'feedburner_URL', $_REQUEST[ 'feedburner_URL' ] );
			update_option( 'feedburner_Comments', $_REQUEST[ 'feedburner_Comments' ] );
			update_option( 'feedburner_ID', $_REQUEST[ 'feedburner_ID' ] );
			update_option( 'google_analytics', $_REQUEST[ 'google_analytics' ] );
			
			update_option( 'home_cat', $_REQUEST[ 'home_cat' ] );	
			update_option( 'home_cat_count', $_REQUEST[ 'home_cat_count' ] );
			
			update_option( 'show_feature', $_REQUEST[ 'show_feature' ] );
			update_option( 'feature_cat', $_REQUEST[ 'feature_cat' ] );
			update_option( 'feature_count', $_REQUEST[ 'feature_count' ] );
			
			update_option( 'sidebar_1_pos', $_REQUEST[ 'sidebar_1_pos' ] );
			
			update_option( 'site_logo', $_REQUEST[ 'site_logo' ] );
			update_option( 'site_lang', $_REQUEST[ 'site_lang' ] );
			
			update_option( 'header_insert', stripslashes($_REQUEST[ 'header_insert' ]) );
			update_option( 'footer_insert', stripslashes($_REQUEST[ 'footer_insert' ]) );
			
			update_option( 'show_subscribe', $_REQUEST[ 'show_subscribe' ] );
			
			update_option( 'show_thumb', $_REQUEST[ 'show_thumb' ] );
			update_option( 'thumb_width', $_REQUEST[ 'thumb_width' ] );
			update_option( 'thumb_height', $_REQUEST[ 'thumb_height' ] );
			update_option( 'thumb_resizer', $_REQUEST[ 'thumb_resizer' ] );

			update_option( 'show_featured_thumb', $_REQUEST[ 'show_featured_thumb' ] );
			update_option( 'featured_thumb_width', $_REQUEST[ 'featured_thumb_width' ] );
			update_option( 'featured_thumb_height', $_REQUEST[ 'featured_thumb_height' ] );
			
			// goto theme edit page
			header("Location: themes.php?page=functions.php&saved=true");
			die;

  		// reset settings
		} else if( 'reset' == $_REQUEST['action'] ) {

			delete_option( 'feedburner_URL' );
			delete_option( 'feedburner_Comments' );
			delete_option( 'feedburner_ID' );
			delete_option( 'google_analytics' );
			
			delete_option( 'home_cat' );
			delete_option( 'home_cat_count' );
			
			delete_option( 'show_feature' );
			delete_option( 'feature_cat' );
			delete_option( 'feature_count' );
			
			delete_option( 'sidebar_1_pos' );
			
			delete_option( 'site_logo' );
			delete_option( 'site_lang' );
			
			delete_option( 'header_insert' );
			delete_option( 'footer_insert' );
			
			delete_option( 'show_subscribe' );
			
			delete_option( 'show_thumb' );
			delete_option( 'thumb_width' );
			delete_option( 'thumb_height' );
			delete_option( 'thumb_resizer' );

			delete_option( 'show_featured_thumb' );
			delete_option( 'featured_thumb_width' );
			delete_option( 'featured_thumb_height' );
			
			// goto theme edit page
			header("Location: themes.php?page=functions.php&reset=true");
			die;
		}
	}
	add_theme_page( $ThemeName . ' Theme Options', $ThemeName . ' Options', 'edit_themes', basename(__FILE__), 'bm_themePage' );
}

function bm_themePage() {
	global $ThemeName;
	if ( $_REQUEST[ 'saved' ] ) echo '<div id="message" class="updated fade"><p><strong>' . __("Settings Saved","vn-news") . '</strong></p></div>';
	if ( $_REQUEST[ 'reset' ] ) echo '<div id="message" class="updated fade"><p><strong>' . __("Settings Reset","vn-news") . '</strong></p></div>';
	?>
	<div class="wrap">
	<form method="post">
	<?php if ( function_exists('wp_nonce_field') ) { wp_nonce_field( 'save-theme-properties' ); } ?>
	<br />Visit the <a href="http://hieudt.info" target="_blank">theme's homepage</a> for more details. If you have any question or suggest new feature for this theme, contact me at <a href="http://hieudt.info/contact" target="_blank">here</a>
	
	<h2><?php _e('HomePage Settings','vn-news'); ?></h2>	
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php 

		bm_th( __("Show Featured Post?","vn-news") );
		bm_input( "show_feature", "checkbox", "", "1", get_option( 'show_feature' ) );
		bm_cth();
		
		$bm_categories = get_categories('hide_empty=1');
		foreach ( $bm_categories as $b ) {
			$bm_cat[] = array( $b->cat_ID, $b->cat_name );
		}

		bm_th( __("Featured Category:","vn-news") );
		bm_select( "feature_cat", $bm_cat, get_option( "feature_cat" ), "","" );
		bm_cth();

		bm_th( __("Quantity of Featured Posts:","vn-news") );
		bm_input( "feature_count", "text", __("Default: 5 entries, Maximum: 20 entries","vn-news"), get_option( "feature_count" ),"","","40px" );
		bm_cth();

		bm_th( __("Show Thumbnails?","vn-news") );
		bm_input( "show_featured_thumb", "checkbox", "", "1", get_option( 'show_featured_thumb' ) );
		bm_cth();
		
		bm_th( __("Thumbnail Width (px):","vn-news") );
		bm_input( "featured_thumb_width", "text", __("Enter the width of thumbnails (default: 220)","vn-news"), get_option( "featured_thumb_width" ),"","","40px" );
		bm_cth();

		bm_th( __("Thumbnail Height (px):","vn-news") );
		bm_input( "featured_thumb_height", "text", __("Enter the height of thumbnails (default: 220)","vn-news"), get_option( "featured_thumb_height" ),"","","40px" );
		bm_cth();
		
		bm_th( __("Headline Categories:","vn-news") );
		bm_multiSelect( "home_cat[]", $bm_cat, get_option( "home_cat" ), "", __("Hold down <strong>Ctrl</strong> key to select multiple categories.","vn-news") );
		bm_cth();

		bm_th( __("Summary Posts:","vn-news") );
		bm_input( "home_cat_count", "text", __("Default: 3 entries","vn-news"), get_option( "home_cat_count" ),"","","40px" );
		bm_cth();
		
	?>
	</table>	
<?php
#Display Setting GUI
?>
	<h2><?php _e("Blog Settings","vn-news"); ?></h2>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php

		$logo_dir = opendir( TEMPLATEPATH . "/images/logo/" );
		$logos[] = array( "0", __("None (default)","vn-news") );
	    while (false !== ( $logo_folder = readdir( $logo_dir ) ) ) {
	    	if( $logo_folder != "." && $logo_folder != ".." ) {
	    		$cp_logoName = $logo_folder;
	    		$logos[] = array( $logo_folder, $cp_logoName );
    		}
		}
		closedir( $logo_dir );
		
		bm_th( __("Site Logo:","vn-news") );
		bm_select( "site_logo", $logos, get_option( "site_logo" ), "",__('To setup a logo, you may upload an image to "VN-News/images/logo/" and select it above.','vn-news') );
		bm_cth();
		
		$lang_dir = opendir( TEMPLATEPATH . "/languages/" );
		$langs[] = array( "0", __("English (default)","vn-news") );
	    while (false !== ( $lang_folder = readdir( $lang_dir ) ) ) {
	    	if( $lang_folder != "." && $lang_folder != ".." && eregi("\.mo",$lang_folder)) {
	    		$cp_langName = $lang_folder;
	    		$langs[] = array( $lang_folder, $cp_langName );
    		}
		}
		closedir( $lang_dir );
		
		bm_th( __("Language:","vn-news") );
		bm_select( "site_lang", $langs, get_option( "site_lang" ), "",__('Select language for the theme, your MO files are located in "VN-News/languages/"','vn-news') );
		bm_cth();		

		$pos[] = array("left",__("Left","vn-news"));
		$pos[] = array("right",__("Right","vn-news"));
		
		bm_th( __("Sidebar #1 Position:","vn-news") );
		bm_select( "sidebar_1_pos", $pos, get_option( "sidebar_1_pos" ), "","" );
		bm_cth();
		
		bm_th( __("Header Insert:","vn-news") );
		bm_input( "header_insert", "textarea", __('Enter your custom XHTML or JavaScript here to have it inserted automatically into your site (within &lt;head&gt;&lt;/head&gt;).','vn-news'), get_option( "header_insert" ) );
		bm_cth();
		
		bm_th( __("Footer Insert:","vn-news") );
		bm_input( "footer_insert", "textarea", __('Enter your custom XHTML or JavaScript here to have it inserted automatically into your site (before &lt;/body&gt;).','vn-news'), get_option( "footer_insert" ) );
		bm_cth();
		
	?>
	</table>

	<h2><?php _e('Thumbnail Settings','vn-news'); ?></h2>	
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php 

		bm_th( __("Show Thumbnails?","vn-news") );
		bm_input( "show_thumb", "checkbox", "", "1", get_option( 'show_thumb' ) );
		bm_cth();
		
		bm_th( __("Thumbnail Width (px):","vn-news") );
		bm_input( "thumb_width", "text", __("Enter the width of thumbnails (default: 100)","vn-news"), get_option( "thumb_width" ),"","","40px" );
		bm_cth();

		bm_th( __("Thumbnail Height (px):","vn-news") );
		bm_input( "thumb_height", "text", __("Enter the height of thumbnails (default: 100)","vn-news"), get_option( "thumb_height" ),"","","40px" );
		bm_cth();
		
		bm_th( __("Enable Thumbnail Resizer?","vn-news") );
		bm_input( "thumb_resizer", "checkbox", '<br />' . __("Important: your server must support GD library before using this option.","vn-news"), "1", get_option( 'thumb_resizer' ) );
		bm_cth();		
	?>
	</table>	
	
	<h2><?php _e("Feedburner Settings","vn-news"); ?></h2>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php
		bm_th( __("Show Subscribe Form?","vn-news") );
		bm_input( "show_subscribe", "checkbox", "", "1", get_option( 'show_subscribe' ) );
		bm_cth();
	
		bm_th( __("FeedBurner URL:","vn-news") );
		bm_input( "feedburner_URL", "text", __("Enter your FeedBurner URL here.","vn-news") . ' <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . 'target="_blank"><strong>' . __("Register","vn-news") . '</strong></a>' , get_option( "feedburner_URL" ) );
		bm_cth();
		
		bm_th( __("Feedburner Comments URL:","vn-news") );
		bm_input( "feedburner_Comments", "text", __('Enter your Feedburner Comments URL here.','vn-news') . ' <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . '/wp-commentsrss2.php" target="_blank"><strong>' . __('Register','vn-news') . '</strong></a>', get_option( "feedburner_Comments" ) );
		bm_cth();

		bm_th( __("FeedBurner ID:","vn-news") );
		bm_input( "feedburner_ID", "text", __('Enter your Feedburner ID here.','vn-news'), get_option( "feedburner_ID" ) );
		bm_cth();
	?>
	</table>
	
	<input type="hidden" name="action" value="save" />
	
	<?php bm_input( "save", "submit", "", __("Save Settings","vn-news") ); ?>
	
	</form>
	
	</div>
	<?php
}
add_action( 'admin_menu', 'bm_addThemePage' );

function the_content_limit2($max_char, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '', $printout = 1) {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = strip_tags($content);

   if (strlen($_GET['p']) > 0) {
      echo "<p>";
      echo $content;
      echo "</p>";
   }
   else if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
        $content = substr($content, 0, $espacio);
        $content = $content;
		if ($printout==1) {
        echo "<p>";
        echo $content;
        echo "...";
        echo "&nbsp;<a href='";
        the_permalink();
        echo "'>".$more_link_text."</a>";
        echo "</p>";
		} else {return $content; }
   }
   else {
      echo "<p>";
      echo $content;
      echo "</p>";
   }
}

#Recent Comments
function recent_comments_2($number_comments = 5, $excerpt_length = 5) {
    global $wpdb;
    $request = "SELECT ID, comment_ID, comment_content, comment_author, comment_author_url, comment_date, comment_author_email, comment_type, post_title FROM $wpdb->comments LEFT JOIN $wpdb->posts ON $wpdb->posts.ID=$wpdb->comments.comment_post_ID WHERE post_status IN ('publish','static') ";
	$request .= "AND post_password ='' ";
	$request .= "AND comment_approved = '1' AND comment_type = '' ORDER BY comment_ID DESC LIMIT $number_comments";
	
	$comments = $wpdb->get_results($request);
	
    $output = '';
	
	if ($comments) {
		foreach ($comments as $comment) {
			$comment_author = stripslashes($comment->comment_author);
			if ($comment_author == "")
				$comment_author = "anonymous"; 
			$comment_content = strip_tags($comment->comment_content);
			$comment_content = stripslashes($comment_content);
			$words=split(" ",$comment_content); 
			$comment_excerpt = join(" ",array_slice($words,0,$excerpt_length));
			$permalink = get_permalink($comment->ID)."#comment-".$comment->comment_ID;
			$post_title = stripslashes($comment->post_title);
			$url = $comment->comment_author_url;
			$date = mysql2date('d-m-Y', $comment->comment_date);
			
			if ( empty( $url ) || 'http://' == $url )
				$output .= '<li class="clearfix">'.get_avatar($comment,50).'<div style="margin-bottom:5px;"><span class="user-comments"><strong>'.$comment_author.'</strong></span> | '.$date.'</div><a href="'.$permalink.'">'.$comment_excerpt.'</a></li>';
			else
				$output .= '<li class="clearfix">'.get_avatar($comment,50).'<div style="margin-bottom:5px;"><span class="user-comments"><a href="'.$url.'" rel="external nofollow" title="'.$url.'"><strong>'.$comment_author.'</strong></a></span> | '.$date.'</div><a href="'.$permalink.'">'.$comment_excerpt.'</a></li>';
		}
		$output = convert_smilies($output);
	} else {
		$output .= $before . "None found" . $after;
	}
    echo $output;
}

function the_title2($before = '', $after = '', $echo = true, $length = false) {

	$title = get_the_title();

	if ( $length && is_numeric($length) ) {
    	$title = substr( $title, 0, $length );
	}

	if ( strlen($title)> 0 ) {

		$title = apply_filters('the_title2', $before . $title . $after, $before, $after);
             
		if ( $echo ) {
			if (strlen(get_the_title()) > strlen($title))
				echo $title.'...';
			else
				echo $title;
		}
		else {
			return $title;
		}
	}
}

function feed_redirect2() {

	global $wp, $feed, $withcomments;
	
	//$newURL1 = trim( get_option( "feedburner_URL" ) );
	//$newURL2 = trim( get_option( "feedburner_Comments" ) );
	$newURL1 = trim( get_option( "feedburner_URL" ) );
	$newURL2 = trim( get_option( "feedburner_Comments" ) );
	
	if( is_feed() ) {

		if ( $feed != 'comments-rss2' 
				&& !is_single() 
				&& $wp->query_vars[ 'category_name' ] == ''
				&& !is_author() 
				&& ( $withcomments != 1 )
				&& $newURL1 != '' ) {
		
			if ( function_exists( 'status_header' ) ) { status_header( 302 ); }
			header( "Location:" . $newURL1 );
			header( "HTTP/1.1 302 Temporary Redirect" );
			exit();
			
		} elseif ( ( $feed == 'comments-rss2' || $withcomments == 1 ) && $newURL2 != '' ) {
	
			if ( function_exists( 'status_header' ) ) { status_header( 302 ); }
			header( "Location:" . $newURL2 );
			header( "HTTP/1.1 302 Temporary Redirect" );
			exit();
			
		}
	
	}

}

function feed_check_url2() {

	switch ( basename( $_SERVER[ 'PHP_SELF' ] ) ) {
		case 'wp-rss.php':
		case 'wp-rss2.php':
		case 'wp-atom.php':
		case 'wp-rdf.php':
		
			$newURL = trim( get_option( "feedburner_URL" ) );
			
			if ( $newURL != '' ) {
				if ( function_exists('status_header') ) { status_header( 302 ); }
				header( "Location:" . $newURL );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
			}
			
			break;
			
		case 'wp-commentsrss2.php':
		
			$newURL = trim( get_option( "feedburner_Comments" ) );
			
			if ( $newURL != '' ) {
				if ( function_exists('status_header') ) { status_header( 302 ); }
				header( "Location:" . $newURL );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
			}
			
			break;
	}
}

if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
	add_action('template_redirect', 'feed_redirect2');
	add_action('init','feed_check_url2');
}

add_action('admin_menu', 'add_custom_box');
add_action('save_post', 'save_custom_box');

function add_custom_box() {
    add_meta_box( 'vn_news_id', 'Thumbnail', 'show_custom_box', 'post', 'normal' );
    add_meta_box( 'vn_news_id', 'Thumbnail', 'show_custom_box', 'page', 'normal' );
}
   
function show_custom_box() {
	global $post;
	echo '<input type="hidden" name="vnnews_noncename" id="vnnews_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />'."\n";
	echo '<p><label for="vnn_thumbnail">Enter your thumbnail url here:</label><br />';
	echo '<input type="text" id="vnn_thumbnail" name="vnn_thumbnail" value="'.get_post_meta($post->ID, 'Image', true).'" style="width:90%;" /></p>'."\n";
}

function save_custom_box( $post_id ) {
	if ( !wp_verify_nonce( $_POST['vnnews_noncename'], plugin_basename(__FILE__) )) {
		return $post_id;
	}

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ))
			return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
	}
	
	global $wpdb;

	$meta_key = "Image";
	
	$meta_value = stripslashes($_POST['vnn_thumbnail']);

	if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) ) ) {
		return add_post_meta($post_id, $meta_key, $meta_value, true);
	}

	$meta_value = maybe_serialize($meta_value);

	$data  = compact( 'meta_value' );
	$where = compact( 'meta_key', 'post_id' );

	$wpdb->update( $wpdb->postmeta, $data, $where );
	wp_cache_delete($post_id, 'post_meta');
	
	return $meta_value;
}

#Page Navigation
function page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 5;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	
	echo $before.'<center><div class="page_navi">'."\n";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = "First";
		echo '<a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a>';
	}
	previous_posts_link('&laquo;');
	for($i = $start_page; $i  <= $end_page; $i++) {						
		if($i == $paged) {
			echo '<span class="current">'.$i.'</span>';
		} else {
			echo '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
		}
	}
	next_posts_link('&raquo;');
	if ($end_page < $max_page) {
		$last_page_text = "Last";
		echo '<a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a>';
	}
	echo '</div></center>'.$after."\n";
}

function get_excerpt_content($max_char = 55,$more_text = '', $printout = 1,$content = '') {
	if ($content == '')
		$content = get_the_content('');
		
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	$content = strip_tags($content);
	
	$words = explode(' ', $content, $max_char + 1);
	
	if (count($words) > $max_char) {
		array_pop($words);
		$content = implode(' ', $words);
		$content = $content . '...';
	}
	
	if ($more_text != '') $content = $content.' <a class="continuebox" href="'.get_permalink().'" title="Permanent Link to '.get_the_title().'">'.$more_text.'</a>';
	
	if ($printout==1)
		echo $content;
	else
		return $content;
}

function widget_recent_comments_2_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	function widget_recent_comments_2($args) {
		extract($args);
		$options = get_option('widget_recent_comments_2');
		$title = htmlspecialchars($options['title']);
		
		echo '<li id="recent-comments-2" class="widget">'."\n";
		echo '<h2>'.$title.'</h2>'."\n";
		echo '<ul>'."\n";
		recent_comments_2($options['number_comments'], $options['excerpt_length']);
		echo '</ul>'."\n";
		echo '</li>'."\n";
	}

	function widget_recent_comments_2_options() {
		$options = get_option('widget_recent_comments_2');
		if (!is_array($options)) {
			$options = array('title' =>'Recent Comments', 'number_comments' => 5, 'excerpt_length' => 15);
		}
		if ($_POST['recent_comments_2-submit']) {
			$options['title'] = strip_tags(addslashes($_POST['recent_comments_2-title']));
			$options['number_comments'] = intval($_POST['recent_comments_2-number_comments']);
			$options['excerpt_length'] = intval($_POST['recent_comments_2-excerpt_length']);
			update_option('widget_recent_comments_2', $options);
		}
		echo '<p style="text-align: left;"><label for="recent_comments_2-title">';
		echo "Title";
		echo ': </label><input type="text" id="recent_comments_2-title" name="recent_comments_2-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="recent_comments_2-number_comments">';
		echo "Number of Comments to show";
		echo ':&nbsp; </label><input type="text" id="recent_comments_2-number_comments" name="recent_comments_2-number_comments" value="'.intval($options['number_comments']).'" size="1" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="recent_comments_2-excerpt_length">';
		echo "Excerpt length for each Comment";
		echo ': </label><input type="text" id="recent_comments_2-excerpt_length" name="recent_comments_2-excerpt_length" value="'.intval($options['excerpt_length']).'" size="1" />'."\n";
		echo '</p>'."\n";
		echo '<input type="hidden" id="recent_comments_2-submit" name="recent_comments_2-submit" value="1" />'."\n";
	}
	// Register Widgets
	//register_sidebar_widget('Recent Comments 2', 'widget_recent_comments_2');
	//register_widget_control('Recent Comments 2', 'widget_recent_comments_2_options');
	wp_register_sidebar_widget(sanitize_title('Recent Comments 2'),'Recent Comments 2', 'widget_recent_comments_2');
	wp_register_widget_control(sanitize_title('Recent Comments 2'),'Recent Comments 2', 'widget_recent_comments_2_options');
}
widget_recent_comments_2_init();

function recent_posts_2($limit = 10, $excerpt_length = 17, $display = true, $show_thumb = true) {
    global $wpdb, $post;
	
	if ( !$results = wp_cache_get( 'recent_posts_2', 'widget' ) ) {
		$results = $wpdb->get_results("SELECT ID, post_title, post_content FROM $wpdb->posts WHERE post_date < '".current_time('mysql')."' AND post_type = 'post' AND post_status = 'publish' AND post_password = '' ORDER  BY post_date DESC LIMIT $limit");
		wp_cache_add( 'recent_posts_2', $results, 'widget' );
	}
	
	if($results) {
		$output = '';
		foreach ($results as $post) {
			$post_url = get_permalink($post->ID);
			$post_title = get_the_title();
			$post_excerpt = get_excerpt_content($excerpt_length,'',0,$post->post_content);
			$post_thumb = get_post_meta($post->ID, 'Image', true);
			$attachments = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 1) );
		if ($post_thumb == null or $post_thumb == '')
		{
			 foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$image = $img[0];
			$post_thumb = $image;
			}
		}
			$output .= '<li class="clearfix">'."\n\t";
			if ($show_thumb && $post_thumb ) {
				if ( get_option( 'thumb_resizer' ) ) {
					$output .=  '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('template_url').'/scripts/timthumb.php?src='.str_replace(get_bloginfo('wpurl').'/','',$post_thumb).'&amp;w=50&amp;h=50&amp;zc=1&amp;q=100" alt="" /></a>';
					
				} elseif ((strpos($post_thumb,'wp-content') == 0) && file_exists($post_thumb)) {
					$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('wpurl') . '/' . $post_thumb.'" alt="" /></a>';
				} else {
					$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.$post_thumb.'" alt="" /></a>';
				}
			}
			$output .= '<a style="font-weight:bold;" href="'.$post_url.'" title="'.$post_title.'" rel="bookmark">'.$post_title.'</a><br /><span style="display:block;overflow:hidden;font-size:8pt;">'.$post_excerpt."</span>\n";
			$output .= "</li>\n";
		}
	} else {
		$output = '<li>None found.</li>';
	}
	if($display) {
		echo $output;
	} else {
		return $output;
	}
}

function widget_recent_posts_2_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	function widget_recent_posts_2($args) {
		extract($args);
		$options = get_option('widget_recent_posts_2');
		$title = htmlspecialchars($options['title']);
		
		echo '<li id="recent-posts-2" class="widget">'."\n";
		echo '<h2>'.$title.'</h2>'."\n";
		echo '<ul>'."\n";
		recent_posts_2($options['number_posts'], $options['excerpt_length'], true, $options['show_thumb']);
		echo '</ul>'."\n";
		echo '</li>'."\n";
	}

	function widget_recent_posts_2_options() {
		$options = get_option('widget_recent_posts_2');
		if (!is_array($options)) {
			$options = array('title' =>'Recent Posts', 'number_posts' => 5, 'excerpt_length' => 17, 'show_thumb' => true);
		}
		if ($_POST['recent_posts_2-submit']) {
			$options['title'] = strip_tags(addslashes($_POST['recent_posts_2-title']));
			$options['number_posts'] = intval($_POST['recent_posts_2-number_posts']);
			$options['excerpt_length'] = intval($_POST['recent_posts_2-excerpt_length']);
			$options['show_thumb'] = intval($_POST['recent_posts_2-show_thumb']);
			update_option('widget_recent_posts_2', $options);
		}
		echo '<p style="text-align: left;"><label for="recent_posts_2-title">';
		echo "Title";
		echo ': </label><input type="text" id="recent_posts_2-title" name="recent_posts_2-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="recent_posts_2-number_posts">';
		echo "Number of Posts to show";
		echo ':&nbsp; </label><input type="text" id="recent_posts_2-number_posts" name="recent_posts_2-number_posts" value="'.intval($options['number_posts']).'" size="1" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="recent_posts_2-excerpt_length">';
		echo "Excerpt length for each Post";
		echo ': </label><input type="text" id="recent_posts_2-excerpt_length" name="recent_posts_2-excerpt_length" value="'.intval($options['excerpt_length']).'" size="1" />'."\n";
		echo '</p>'."\n";
		echo '<p style="text-align: left;"><label for="recent_posts_2-show_thumb">';
		echo "Show thumbnails?";
		if ($options['show_thumb'] == 1)
			echo ': </label><input type="checkbox" id="recent_posts_2-show_thumb" name="recent_posts_2-show_thumb" value="1" checked="true" />'."\n";
		else
			echo ': </label><input type="checkbox" id="recent_posts_2-show_thumb" name="recent_posts_2-show_thumb" value="1" />'."\n";
		echo '</p>'."\n";
		
		echo '<input type="hidden" id="recent_posts_2-submit" name="recent_posts_2-submit" value="1" />'."\n";
	}
	
	//register_sidebar_widget('Recent Posts 2', 'widget_recent_posts_2');
	//register_widget_control('Recent Posts 2', 'widget_recent_posts_2_options');
	wp_register_sidebar_widget(sanitize_title('Recent Posts 2'),'Recent Posts 2', 'widget_recent_posts_2');
	wp_register_widget_control(sanitize_title('Recent Posts 2'),'Recent Posts 2', 'widget_recent_posts_2_options');
}
widget_recent_posts_2_init();

/**
* Since 1.2.3
*/
if (function_exists('the_views')) { 
	function get_most_viewed_2($limit = 5, $display = true, $show_thumb = true) {
		global $wpdb, $post;
		$where = '';
		$output = '';
		$where = "post_type = 'post'";
		$most_viewed = $wpdb->get_results("SELECT DISTINCT $wpdb->posts.*, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID WHERE post_date < '".current_time('mysql')."' AND $where AND post_status = 'publish' AND meta_key = 'views' AND post_password = '' ORDER  BY views DESC LIMIT $limit");
		if($most_viewed) {
			foreach ($most_viewed as $post) {
				$post_views = number_format_i18n(intval($post->views));
				$post_url = get_permalink($post->ID);
				$post_title = get_the_title();
				//$post_excerpt = get_excerpt_content(10,'',0,$post->post_content);
				$post_thumb = get_post_meta($post->ID, 'Image', true);
				
				$attachments = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 1) );
		if ($post_thumb == null or $post_thumb == '')
		{
			 foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$image = $img[0];
			$post_thumb = $image;
			}
		}
				$output .= '<li class="clearfix">'."\n\t";
				if ($show_thumb && $post_thumb ) {
					if ( get_option( 'thumb_resizer' ) ) {
						$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('template_url').'/scripts/timthumb.php?src='.str_replace(get_bloginfo('wpurl').'/','',$post_thumb).'&amp;w=50&amp;h=50&amp;zc=1&amp;q=100" alt="" /></a>';
					} elseif ((strpos($post_thumb,'wp-content') == 0) && file_exists($post_thumb)) {
						$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('wpurl') . '/' . $post_thumb.'" alt="" /></a>';
					} else {
						$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.$post_thumb.'" alt="" /></a>';
					}
				}
				$output .= '<a style="font-weight:bold;" href="'.$post_url.'" title="'.$post_title.'" rel="bookmark">'.$post_title.'</a> (' . $post_views . ' ' . __('views','vn-news') . ")\n";
				$output .= "</li>\n";
			}			
		} else {
			$output = '<li>'.__('N/A', 'vn-news').'</li>'."\n";
		}
		if($display) {
			echo $output;
		} else {
			return $output;
		}
	}

	function widget_views_init_2() {
		function widget_views_most_viewed_2($args) {
			extract($args);
			$options = get_option('widget_views_most_viewed_2');
			$title = htmlspecialchars(stripslashes($options['title']));		

			echo '<li id="most-viewed-2" class="widget">'."\n";
			echo '<h2>'.$title.'</h2>'."\n";
			echo '<ul>'."\n";
			get_most_viewed_2($options['limit'], true, $options['show_thumb']);
			echo '</ul>'."\n";
			echo '</li>'."\n";
		}

		function widget_views_most_viewed_2_options() {
			$options = get_option('widget_views_most_viewed_2');
			if (!is_array($options)) {
				$options = array('title' => 'Most Viewed 2', 'limit' => 5, 'show_thumb' => true);
			}
			if ($_POST['most_viewed_2-submit']) {
				$options['title'] = strip_tags($_POST['most_viewed_2-title']);
				$options['show_thumb'] = intval($_POST['most_viewed_2-show_thumb']);
				$options['limit'] = intval($_POST['most_viewed_2-limit']);
				update_option('widget_views_most_viewed_2', $options);
			}
			echo '<p style="text-align: left;"><label for="most_viewed_2-title">Title: </label><input type="text" id="most_viewed_2-title" name="most_viewed_2-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
			echo '<p style="text-align: left;"><label for="most_viewed_2-limit">Limit: </label><input type="text" id="most_viewed_2-limit" name="most_viewed_2-limit" value="'.intval($options['limit']).'" size="3" /></p>'."\n";
			echo '<p style="text-align: left;"><label for="most_viewed_2-show_thumb">';
			echo "Show thumbnails?";
			if ($options['show_thumb'] == 1)
				echo ': </label><input type="checkbox" id="most_viewed_2-show_thumb" name="most_viewed_2-show_thumb" value="1" checked="true" />'."\n";
			else
				echo ': </label><input type="checkbox" id="most_viewed_2-show_thumb" name="most_viewed_2-show_thumb" value="1" />'."\n";
			echo '</p>'."\n";
			
			echo '<input type="hidden" id="most_viewed_2-submit" name="most_viewed_2-submit" value="1" />'."\n";
		}
		// Register Widgets
		//register_sidebar_widget(array('Most Viewed 2', 'vn-news'), 'widget_views_most_viewed_2');
		//register_widget_control(array('Most Viewed 2', 'vn-news'), 'widget_views_most_viewed_2_options', 400, 200);
		wp_register_sidebar_widget(sanitize_title('Most Viewed 2'),'Most Viewed 2', 'widget_views_most_viewed_2');
		wp_register_widget_control(sanitize_title('Most Viewed 2'),'Most Viewed 2', 'widget_views_most_viewed_2_options',['width'=>400, 'height'=>200]);
	}
	widget_views_init_2();
}

function get_random_posts($limit = 10, $excerpt_length = 17, $display = true, $show_thumb = true) {
    global $wpdb, $post;
	
	if ( !$results = wp_cache_get( 'widget_random_posts', 'widget' ) ) {
		$results = $wpdb->get_results("SELECT ID, post_title, post_content FROM $wpdb->posts WHERE 1=1 AND post_type = 'post' AND post_status = 'publish' AND post_password = '' ORDER  BY RAND() LIMIT $limit");
		wp_cache_add( 'widget_random_posts', $results, 'widget' );
	}
	
	if($results) {
		$output = '';
		foreach ($results as $post) {
			$post_url = get_permalink($post->ID);
			$post_title = get_the_title();
			$post_excerpt = get_excerpt_content($excerpt_length,'',0,$post->post_content);
			$post_thumb = get_post_meta($post->ID, 'Image', true);
			$attachments = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 1) );
		if ($post_thumb == null or $post_thumb == '')
		{
			 foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$image = $img[0];
			$post_thumb = $image;
			}
		}
			$output .= '<li class="clearfix">'."\n\t";
			if ($show_thumb && $post_thumb ) {
				if ( get_option( 'thumb_resizer' ) ) {
					$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('template_url').'/scripts/timthumb.php?src='.str_replace(get_bloginfo('wpurl').'/','',$post_thumb).'&amp;w=50&amp;h=50&amp;zc=1&amp;q=100" alt="" /></a>';
				} elseif ((strpos($post_thumb,'wp-content') == 0) && file_exists($post_thumb)) {
					$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.get_bloginfo('wpurl') . '/' . $post_thumb.'" alt="" /></a>';
				} else {
					$output .= '<a href="'.$post_url.'" rel="bookmark"><img class="thumb" src="'.$post_thumb.'" alt="" /></a>';
				}
			}
			$output .= '<a style="font-weight:bold;" href="'.$post_url.'" title="'.$post_title.'" rel="bookmark">'.$post_title.'</a><br /><span style="display:block;overflow:hidden;font-size:8pt;">'.$post_excerpt."</span>\n";
			$output .= "</li>\n";
		}
	} else {
		$output = '<li>Oop! You get an error.</li>';
	}
	if($display) {
		echo $output;
	} else {
		return $output;
	}
}

function widget_random_posts_init() {

	function widget_random_posts($args) {
		extract($args);
		$options = get_option('widget_random_posts');
		$title = htmlspecialchars($options['title']);
		
		echo '<li id="random-posts" class="widget">'."\n";
		echo '<h2>'.$title.'</h2>'."\n";
		echo '<ul>'."\n";
		get_random_posts($options['number_posts'], $options['excerpt_length'], true, $options['show_thumb']);
		echo '</ul>'."\n";
		echo '</li>'."\n";
	}

	function widget_random_posts_options() {
		$options = get_option('widget_random_posts');
		if (!is_array($options)) {
			$options = array('title' =>'Random Posts', 'number_posts' => 5, 'excerpt_length' => 17, 'show_thumb' => true);
		}
		if ($_POST['random_posts-submit']) {
			$options['title'] = strip_tags(addslashes($_POST['random_posts-title']));
			$options['number_posts'] = intval($_POST['random_posts-number_posts']);
			$options['excerpt_length'] = intval($_POST['random_posts-excerpt_length']);
			$options['show_thumb'] = intval($_POST['random_posts-show_thumb']);
			update_option('widget_random_posts', $options);
		}
		echo '<p style="text-align: left;"><label for="random_posts-title">';
		echo "Title";
		echo ': </label><input type="text" id="random_posts-title" name="random_posts-title" value="'.htmlspecialchars(stripslashes($options['title'])).'" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="random_posts-number_posts">';
		echo "Number of Posts to show";
		echo ':&nbsp; </label><input type="text" id="random_posts-number_posts" name="random_posts-number_posts" value="'.intval($options['number_posts']).'" size="1" /></p>'."\n";
		echo '<p style="text-align: left;"><label for="random_posts-excerpt_length">';
		echo "Excerpt length for each Post";
		echo ': </label><input type="text" id="random_posts-excerpt_length" name="random_posts-excerpt_length" value="'.intval($options['excerpt_length']).'" size="1" />'."\n";
		echo '</p>'."\n";
		echo '<p style="text-align: left;"><label for="random_posts-show_thumb">';
		echo "Show thumbnails?";
		if ($options['show_thumb'] == 1)
			echo ': </label><input type="checkbox" id="random_posts-show_thumb" name="random_posts-show_thumb" value="1" checked="true" />'."\n";
		else
			echo ': </label><input type="checkbox" id="random_posts-show_thumb" name="random_posts-show_thumb" value="1" />'."\n";
		echo '</p>'."\n";
		
		echo '<input type="hidden" id="random_posts-submit" name="random_posts-submit" value="1" />'."\n";
	}
	
	//register_sidebar_widget('Random Posts', 'widget_random_posts');
	//register_widget_control('Random Posts', 'widget_random_posts_options');
	wp_register_sidebar_widget(sanitize_title('Random Posts'),'Random Posts', 'widget_random_posts');
	wp_register_widget_control(sanitize_title('Random Posts'),'Random Posts', 'widget_random_posts_options');
}
widget_random_posts_init();
?>