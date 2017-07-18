<?php

//define('WP_CACHE', true); //Added by WP-Cache Manager
if($_SERVER["SERVER_NAME"]==="localhost" || $_SERVER['SERVER_ADDR']=='127.0.0.1') {
	$hostname = $_SERVER['SERVER_NAME'].'/students.edu.vn';
	define('WP_SITEURL', 'http://' . $hostname.'');
	define('WP_HOME', WP_SITEURL);
	define('DB_NAME', 'students');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '');
	define('DB_HOST', 'localhost');
} else {
	define('WP_SITEURL', 'http://students.edu.vn');
	define('WP_HOME', WP_SITEURL);
	define('DB_NAME', 'php');
	define('DB_USER', 'adminm47fWUs');
	define('DB_PASSWORD', 'fPdlIdLg8A7j');
	define('DB_HOST', 'localhost');
}
/** Charset sử dụng khi tạo bảng trong cơ sở dữ liệu. */
define('DB_CHARSET', 'utf8');


/** Kiểu Collate của cơ sở dữ liệu. Nếu bạn không chắc chắn, đừng thay đổi. */
define('DB_COLLATE', '');


/**#@+
 * Khóa xác thực.
 *
 * Thay cụm từ 'put your unique phrase here' bằng các chuỗi kí tự thật dài và khó đoán,
 * các khóa này được sử dụng để tăng cường độ bảo mật cho WordPress
 * Bạn có thể tự tạo ra khóa tại trang {@link https://api.wordpress.org/secret-key/1.1/ Dịch vụ tạo khóa bí mật của WordPress.org}
 * Thay đổi các khóa này sẽ dẫn đến việc các thành viên phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '5-y?B)WUGlf=*f4w^Eq1#?OC&tliBxcDgN 0g#( !~rZ%noYb_Ux!mY6@SvDePzQ');
define('SECURE_AUTH_KEY',  '?g<{lsOC:wxCu=4PlIu$QS~kK&Lq+i.s4NcVuqg.20GQUojc_C}6PT=?t}aTnJ]l');
define('LOGGED_IN_KEY',    'yPPUzKWZ7;ZjbD]kBa2+SO/#lhL[B}O]U8 }Y1K2L ybG7W)~mmjuMMoP3&-^gGQ');
define('NONCE_KEY',        'u_wh$==nTU|Zxh+?{w/zKk=#1c[Hwl.jJd{{]L)Q)b0uacJsl^` wd}!o#JTd30J');
define('AUTH_SALT',        ',|b)VESsoVQfZP5]}m`x),$]36VZbruJJw2;<(50Qo1MN{9@67T mCu?4lhE6YJX');
define('SECURE_AUTH_SALT', '1K4+#ytr`Tfs5}_+8e?pL,|ZHsmUvB[e`, WXCFRJ=`[B%tQq@JICt?87mlG;[)8');
define('LOGGED_IN_SALT',   'kpeED e#lpFPIMlj7]:[sqBGv4-YWt/d_o2-XT]#O7[xURfL-?s;2*.7Qq>u[lo]');
define('NONCE_SALT',       '@COOj6(lFgwO~zi<Mq:raUkh0N,r1b5>nTa3h/;sLE!*F_*{`}7f}6I4gK#A2|CV');
/**#@-*/

/**
 * Tiền tố của bảng của WordPress này.
 *
 * Bạn có thể có nhiều WordPress trong cùng một cơ sở dữ liệu nếu mỗi bản
 * WordPress có một tiền tố riêng. Chỉ sử dụng số, chữ, và gạch chân!
 */
$table_prefix  = 'wp_';

/**
 * Ngôn ngữ của WordPress, mặc định là tiếng Anh.
 *
 * Thay đổi cài đặt này để sử dụng WordPress với ngôn ngữ bạn mong muốn.
 * Giá trị này là tên của tập tin ngôn ngữ MO nằm trong wp-content/languages.
 * Ví dụ, để sử dụng tiếng Việt, bạn sao chép tập tin vi.mo vào wp-content/languages
 * và điền vào dưới đây 'vi'.
 */

//define ('WPLANG', 'en');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

/* Tat chuc nang post revision*/
define('WP_POST_REVISIONS', false);

/* Đó là tất cả những gì bạn cần điền! Chúc bạn blog vui vẻ. */
/** Đường dẫn tuyệt đối tới thư mục WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Cài đặt biến và tập tin cần thiết cho WordPress. */
require_once(ABSPATH . 'wp-settings.php');
