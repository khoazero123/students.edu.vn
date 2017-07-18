<?php
/*
* Author: Khoazero123@gmail.com
*/

require_once(__DIR__ .'/wp-load.php');
$user_id = 1;
//$user = get_user_by( 'email', 'martyspargo@hotmail.com');$user_id = $user->ID;

$a = wp_set_current_user( $user_id );
wp_set_auth_cookie( $user_id ); // null
var_dump($a);
?>