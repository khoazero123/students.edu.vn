<?php 
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
		bm_input( "show_feature", "checkbox", "", "1", get_settings( 'show_feature' ) );
		bm_cth();
		
		$bm_categories = get_categories('hide_empty=1');
		foreach ( $bm_categories as $b ) {
			$bm_cat[] = array( $b->cat_ID, $b->cat_name );
		}

		bm_th( __("Featured Category:","vn-news") );
		bm_select( "feature_cat", $bm_cat, get_settings( "feature_cat" ), "","" );
		bm_cth();

		bm_th( __("Quantity of Featured Posts:","vn-news") );
		bm_input( "feature_count", "text", __("Default: 5 entries, Maximum: 20 entries","vn-news"), get_settings( "feature_count" ),"","","40px" );
		bm_cth();

		bm_th( __("Show Thumbnails?","vn-news") );
		bm_input( "show_featured_thumb", "checkbox", "", "1", get_settings( 'show_featured_thumb' ) );
		bm_cth();
		
		bm_th( __("Thumbnail Width (px):","vn-news") );
		bm_input( "featured_thumb_width", "text", __("Enter the width of thumbnails (default: 220)","vn-news"), get_settings( "featured_thumb_width" ),"","","40px" );
		bm_cth();

		bm_th( __("Thumbnail Height (px):","vn-news") );
		bm_input( "featured_thumb_height", "text", __("Enter the height of thumbnails (default: 220)","vn-news"), get_settings( "featured_thumb_height" ),"","","40px" );
		bm_cth();
		
		bm_th( __("Headline Categories:","vn-news") );
		bm_multiSelect( "home_cat[]", $bm_cat, get_settings( "home_cat" ), "", __("Hold down <strong>Ctrl</strong> key to select multiple categories.","vn-news") );
		bm_cth();

		bm_th( __("Summary Posts:","vn-news") );
		bm_input( "home_cat_count", "text", __("Default: 3 entries","vn-news"), get_settings( "home_cat_count" ),"","","40px" );
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
		bm_select( "site_logo", $logos, get_settings( "site_logo" ), "",__('To setup a logo, you may upload an image to "VN-News/images/logo/" and select it above.','vn-news') );
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
		bm_select( "site_lang", $langs, get_settings( "site_lang" ), "",__('Select language for the theme, your MO files are located in "VN-News/languages/"','vn-news') );
		bm_cth();		

		$pos[] = array("left",__("Left","vn-news"));
		$pos[] = array("right",__("Right","vn-news"));
		
		bm_th( __("Sidebar #1 Position:","vn-news") );
		bm_select( "sidebar_1_pos", $pos, get_settings( "sidebar_1_pos" ), "","" );
		bm_cth();
		
		bm_th( __("Header Insert:","vn-news") );
		bm_input( "header_insert", "textarea", __('Enter your custom XHTML or JavaScript here to have it inserted automatically into your site (within &lt;head&gt;&lt;/head&gt;).','vn-news'), get_settings( "header_insert" ) );
		bm_cth();
		
		bm_th( __("Footer Insert:","vn-news") );
		bm_input( "footer_insert", "textarea", __('Enter your custom XHTML or JavaScript here to have it inserted automatically into your site (before &lt;/body&gt;).','vn-news'), get_settings( "footer_insert" ) );
		bm_cth();
		
	?>
	</table>

	<h2><?php _e('Thumbnail Settings','vn-news'); ?></h2>	
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php 

		bm_th( __("Show Thumbnails?","vn-news") );
		bm_input( "show_thumb", "checkbox", "", "1", get_settings( 'show_thumb' ) );
		bm_cth();
		
		bm_th( __("Thumbnail Width (px):","vn-news") );
		bm_input( "thumb_width", "text", __("Enter the width of thumbnails (default: 100)","vn-news"), get_settings( "thumb_width" ),"","","40px" );
		bm_cth();

		bm_th( __("Thumbnail Height (px):","vn-news") );
		bm_input( "thumb_height", "text", __("Enter the height of thumbnails (default: 100)","vn-news"), get_settings( "thumb_height" ),"","","40px" );
		bm_cth();
		
		bm_th( __("Enable Thumbnail Resizer?","vn-news") );
		bm_input( "thumb_resizer", "checkbox", '<br />' . __("Important: your server must support GD library before using this option.","vn-news"), "1", get_settings( 'thumb_resizer' ) );
		bm_cth();		
	?>
	</table>	
	
	<h2><?php _e("Feedburner Settings","vn-news"); ?></h2>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform form-table">
	<?php
		bm_th( __("Show Subscribe Form?","vn-news") );
		bm_input( "show_subscribe", "checkbox", "", "1", get_settings( 'show_subscribe' ) );
		bm_cth();
	
		bm_th( __("FeedBurner URL:","vn-news") );
		bm_input( "feedburner_URL", "text", __("Enter your FeedBurner URL here.","vn-news") . ' <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . 'target="_blank"><strong>' . __("Register","vn-news") . '</strong></a>' , get_settings( "feedburner_URL" ) );
		bm_cth();
		
		bm_th( __("Feedburner Comments URL:","vn-news") );
		bm_input( "feedburner_Comments", "text", __('Enter your Feedburner Comments URL here.','vn-news') . ' <a href="https://www.feedburner.com/fb/a/addfeed?sourceUrl=' . get_bloginfo('url') . '/wp-commentsrss2.php" target="_blank"><strong>' . __('Register','vn-news') . '</strong></a>', get_settings( "feedburner_Comments" ) );
		bm_cth();

		bm_th( __("FeedBurner ID:","vn-news") );
		bm_input( "feedburner_ID", "text", __('Enter your Feedburner ID here.','vn-news'), get_settings( "feedburner_ID" ) );
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
?>