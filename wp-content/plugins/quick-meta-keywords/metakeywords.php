<?php
/*
Plugin Name: Quick META Keywords 
Author: Quick Online Tips  
Author URI: http://www.quickonlinetips.com/
Version: 1.0
Description: Automatically adds a META keywords tags between your wordpress html HEAD tags. The categories are used as keywords.
Plugin URI: http://www.quickonlinetips.com/archives/quick-meta-keywords-wordpress-plugin/
*/

 
function quickkeywords() {
	
  if (is_single())
     	{
  echo '<meta name="keywords" content="';
      }
  foreach((get_the_category()) as $cat)
  if (is_single())
      {
  echo  $cat->cat_name . ', '; 
   	}
  if (is_single())
     	{
  echo '" />';
      }
     
}
add_action('wp_head', 'quickkeywords');
?>