<?php
/**
 * Plugin Name:  WordPress Related Posts Thumbnails
 * Plugin URI:   https://wpbrigade.com/wordpress/plugins/related-posts/?utm_source=related-posts-lite&utm_medium=plugin-uri&utm_campaign=pro-upgrade-rp
 * Description:  Showing related posts thumbnails under the posts.
 * Version:      1.6.0
 * Author:       WPBrigade
 * Author URI:   https://WPBrigade.com/?utm_source=related-posts-lite&utm_medium=author-link&utm_campaign=pro-upgrade-rp
 */

/*
   Copyright 2010 to 2017

	This product was first developed by Maria I Shaldybina and later on maintained by Adnan (WPBrigade)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/
class RelatedPostsThumbnails {
	/* Default values. PHP 4 compatible */
	var $single_only       = '1';
	var $auto              = '1';
	var $top_text          = '<h3>Related posts:</h3>';
	var $number            = 3;
	var $relation          = 'categories';
	var $poststhname       = 'thumbnail';
	var $background        = '#FFFFFF';
	var $hoverbackground   = '#EEEEEF';
	var $border_color      = '#DDDDDD';
	var $font_color        = '#333333';
	var $font_family       = 'Arial';
	var $font_size         = '12';
	var $text_length       = '100';
	var $excerpt_length    = '0';
	var $custom_field      = '';
	var $custom_height     = '100';
	var $custom_width      = '100';
	var $text_block_height = '75';
	var $thsource          = 'post-thumbnails';
	var $categories_all    = '1';
	var $devmode           = '0';
	var $output_style      = 'div';
	var $post_types        = array( 'post' );
	var $custom_taxonomies = array();

	protected $wp_kses_rp_args = array(

						'h1' => array(),
						'h2' => array(),
						'h3' => array(),
						'h4' => array(),
						'h5' => array(),
						'h6' => array(),
						'strong' => array(),
					);

	function __construct() {
		// initialization
		load_plugin_textdomain( 'related-posts-thumbnails', false, basename( dirname( __FILE__ ) ) . '/locale' );
		$this->default_image = esc_url( plugins_url( 'img/default.png', __FILE__ ) );

		// Compatibility for old default image path.
		if ( $this->is_old_default_img() )
			update_option( 'relpoststh_default_image', $this->default_image );

		if ( get_option( 'relpoststh_auto', $this->auto ) ) {
			add_filter( 'the_content', array( $this, 'auto_show' ) );
		}

		add_action( 'admin_menu',  array( $this, 'admin_menu' ) );
		add_shortcode( 'related-posts-thumbnails' , array( $this, 'get_html' ) );

		$this->wp_version = get_bloginfo( 'version' );

		add_action( 'wp_ajax_relpost_subscriber', array( $this, 'relpost_subscriber' ) );
	}

	function relpost_subscriber() {
		$email = sanitize_email( wp_unslash( $_POST['subscriber_mail'] ) );
		$display_name = sanitize_text_field( wp_unslash( $_POST['name'] ) );


		$data = array(
			 'email_address' => $email,
			 "status_if_new" => "pending",
			 // 'status'        => 'pending', // "subscribed","unsubscribed","cleaned","pending"
			 'merge_fields'  => array(
				 'FNAME'       => $display_name
			 ),
			 'interests' => array(
				 '5f0e7578b3' => true, // add interest ? loginPress => d91a783713 || Related Posts Thumbnails => 1c85722a4b || Newsletter Subscribers => 3eefb18f62
			 )
		 	);


		$apiKey = '7f32f907a2e6ceeaf8a97fc00b962b2b-us15';
		$listId = '30d28e0fc3';

		$memberId = md5( strtolower( $data['email_address'] ) );
		$dataCenter = substr( $apiKey , strpos( $apiKey,'-' ) + 1 );


		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId .'/members/' . $memberId;

		// 2d13665c08ff83f22de7cc7da216db12
		$json = json_encode( $data );

		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $apiKey );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)' );

		$result = curl_exec( $ch );

		if( $request_type == 'GET' ){
			$result_array = json_decode ( $result , true );
			$status       = $result_array['status'];
			return $status;
		}
		// var_dump($result);
		// var_dump( curl_getinfo( $ch ) );

		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		curl_close( $ch );
		if ( 200 == $httpCode ) {
			echo "Thank you!";
		} else {
			echo "oops! Please try again later.";
		}
		wp_die();
	}

	/**
	 * [is_old_default_img Check the compatibility for old default image path.]
	 * @return boolean Return true if path is old.
	 */
	function is_old_default_img() {

		if ( get_option( 'relpoststh_default_image') !== $this->default_image ) {

			$chunks = explode( '/', get_option( 'relpoststh_default_image') );
			if ( in_array('related-posts-thumbnails', $chunks) ) {
				return true;
			}
		}

	}

	function auto_show( $content ) {
		// Automatically displaying related posts under post body
		return $content . $this->get_html( true );
	}

	function get_html( $show_top = false ) {
		// Getting related posts HTML
		if ( $this->is_relpoststh_show() ) {
			return $this->get_thumbnails( $show_top ); }
		return '';
	}

	function get_thumbnails( $show_top = false ) {
		// Retrieve Related Posts HTML for output
		$output              = '';
		$debug               = 'Developer mode initialisation; Version: 1.2.9;';
		$time                = microtime( true );
		$posts_number        = get_option( 'relpoststh_number', $this->number );

		if ( $posts_number <= 0 ) { // return nothing if this parameter was set to <= 0
			return $this->finish_process( $output, $debug . 'Posts number is 0;', $time );
		}

		$id                  = get_the_ID();
		$relation            = get_option( 'relpoststh_relation', $this->relation );
		$poststhname         = get_option( 'relpoststh_poststhname', $this->poststhname );
		$text_length         = get_option( 'relpoststh_textlength', $this->text_length );
		$excerpt_length      = get_option( 'relpoststh_excerptlength', $this->excerpt_length );
		$thsource            = get_option( 'relpoststh_thsource', $this->thsource );
		$categories_show_all = get_option( 'relpoststh_show_categoriesall', get_option( 'relpoststh_categoriesall', $this->categories_all ) );
		$onlywiththumbs      = ( current_theme_supports( 'post-thumbnails' ) && $thsource == 'post-thumbnails' ) ? get_option( 'relpoststh_onlywiththumbs', false ) : false;
		$post_type           = get_post_type();

		global $wpdb;

		/* Get taxonomy terms */
		$debug .= "Relation: $relation; All categories: $categories_show_all;";
		$use_filter = ( $categories_show_all != '1' || $relation != 'no' );

		if ( $use_filter ) {
			$query_objects = "SELECT distinct object_id FROM $wpdb->term_relationships WHERE 1=1 ";

			if ( $relation != 'no' ) { /* Get object terms */
				if ( $relation == 'categories' ) {
					$taxonomy = array( 'category' ); } elseif ( $relation == 'tags' ) {
					$taxonomy = array( 'post_tag' ); } elseif ( $relation == 'custom' ) {
						$taxonomy = get_option( 'relpoststh_custom_taxonomies', $this->custom_taxonomies );
					} else {
						$taxonomy = array( 'category', 'post_tag' );
					}
					$object_terms = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'ids' ) );
					if ( empty( $object_terms ) || ! is_array( $object_terms ) ) { // no terms to get taxonomy
						return $this->finish_process( $output, $debug . 'No taxonomy terms to get posts;', $time );
					}

					$query = "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id in ('". implode( "', '", $object_terms ) . "')";
					$object_taxonomy = $wpdb->get_results( $query );
					$object_taxonomy_a = array();
					if ( count( $object_taxonomy ) > 0 ) {
						foreach ( $object_taxonomy as $item ) {
							$object_taxonomy_a[] = $item->term_taxonomy_id;
						}
					}
					$query_objects .= " AND term_taxonomy_id IN ('". implode( "', '", $object_taxonomy_a ) . "') ";
			}

			if ( $categories_show_all != '1' ) { /* Get filter terms */
				$select_terms = get_option( 'relpoststh_show_categories',
				get_option( 'relpoststh_categories' ) );
				if ( empty( $select_terms ) || ! is_array( $select_terms ) ) { // if no categories were specified intentionally return nothing
					return $this->finish_process( $output, $debug . 'No categories were selected;', $time );
				}

				$query = "SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id in ('". implode( "', '", $select_terms ) . "')";
				$taxonomy = $wpdb->get_results( $query );
				$filter_taxonomy_a = array();
				if ( count( $taxonomy ) > 0 ) {
					foreach ( $taxonomy as $item ) {
						$filter_taxonomy_a[] = $item->term_taxonomy_id; }
				}
				if ( $relation != 'no' ) {
					$query_objects .= " AND object_id IN (SELECT distinct object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ('". implode( "', '", $filter_taxonomy_a ) . "') )";
				} else {
					$query_objects .= " AND term_taxonomy_id IN ('". implode( "', '", $filter_taxonomy_a ) . "')";
				}
			}

			$relationships = $wpdb->get_results( $query_objects );
			$related_objects = array();
			if ( count( $relationships ) > 0 ) {
				foreach ( $relationships as $item ) {
					$related_objects[] = $item->object_id;
				}
			}
		}

		$query = "SELECT distinct ID FROM $wpdb->posts ";
		$where = " WHERE post_type = '" . $post_type . "' AND post_status = 'publish' AND ID<>" . $id; // not the current post
		$startdate = get_option( 'relpoststh_startdate' );
		if ( ! empty( $startdate ) && preg_match( '/^\d\d\d\d-\d\d-\d\d$/', $startdate ) ) { // If startdate was set
			$debug .= "Startdate: $startdate;";
			$where .= " AND post_date >= '" . $startdate . "'";
		}
		if ( $use_filter ) {
			$where .= " AND ID IN ('". implode( "', '", $related_objects ) . "')";
		}
		$join = '';
		if ( $onlywiththumbs ) {
			$debug .= 'Only with thumbnails;';
			$join = " INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)";
			$where .= " AND $wpdb->postmeta.meta_key = '_thumbnail_id'";
		}

		$order = ' ORDER BY rand() LIMIT ' . $posts_number;
		$random_posts = $wpdb->get_results( $query . $join . $where . $order );

		/* Get posts by their IDs */
		if ( ! is_array( $random_posts ) || count( $random_posts ) < 1 ) {
			return $this->finish_process( $output, $debug . 'No posts matching relationships criteria;', $time );
		}

		$posts_in = array();
		foreach ( $random_posts as $random_post ) {
			$posts_in[] = $random_post->ID;
		}
		$query = "SELECT ID, post_content, post_excerpt, post_title FROM $wpdb->posts WHERE ID IN ('". implode( "', '", $posts_in ) . "')";
		$posts = $wpdb->get_results( $query );
		if ( ! ( is_array( $posts ) && count( $posts ) > 0 ) ) { // no posts
			$debug .= 'No posts found;';
			return $this->finish_process( $output, $debug, $time );
		} else {
			$debug .= 'Found ' . count( $posts ) . ' posts;';
		}

		/* Calculating sizes */
		if ( $thsource == 'custom-field' ) {
			$debug .= 'Custom sizes;';
			$width = get_option( 'relpoststh_customwidth', $this->custom_width );
			$height = get_option( 'relpoststh_customheight', $this->custom_height );
		} else { // post-thumbnails source
			if ( $poststhname == 'thumbnail' || $poststhname == 'medium' || $poststhname == 'large' ) { // get thumbnail size for basic sizes
				$debug .= 'Basic sizes;';
				$width = get_option( "{$poststhname}_size_w" );
				$height = get_option( "{$poststhname}_size_h" );
			} elseif ( current_theme_supports( 'post-thumbnails' ) ) { // get sizes for theme supported thumbnails
				global $_wp_additional_image_sizes;
				if ( isset( $_wp_additional_image_sizes[ $poststhname ] ) ) {
					$debug .= 'Additional sizes;';
					$width = $_wp_additional_image_sizes[ $poststhname ]['width'];
					$height = $_wp_additional_image_sizes[ $poststhname ]['height'];
				} else { 					$debug .= 'No additional sizes;'; }
			}
		}
		// displaying square if one size is not cropping
		if ( $height == 9999 ) {
			$height = $width;
		}
		if ( $width == 9999 ) {
			$width = $height;
		}
		// theme is not supporting but settings were not changed
		if ( empty( $width ) ) {
			$debug .= 'Using default width;';
			$width = get_option( 'thumbnail_size_w' );
		}
		if ( empty( $height ) ) {
			$debug .= 'Using default height;';
			$height = get_option( 'thumbnail_size_h' );
		}
		$debug .= 'Got sizes '.$width.'x'.$height.';';
		// rendering related posts HTML
		if ( $show_top ) {
			$output .= stripslashes( get_option( 'relpoststh_top_text', $this->top_text ) );
		}
		$relpoststh_output_style = get_option( 'relpoststh_output_style', $this->output_style );
		$relpoststh_cleanhtml = get_option( 'relpoststh_cleanhtml', 0 );
		$text_height = get_option( 'relpoststh_textblockheight', $this->text_block_height );

		if ( $relpoststh_output_style == 'list' ) {
			$output .= '<ul id="related_posts_thumbnails"';
			if ( ! $relpoststh_cleanhtml ) {
				$output .= ' style="list-style-type:none; list-style-position: inside; padding: 0; margin:0"';
			}
			$output .= '>';
		} else {
			$output .= '<div style="clear: both"></div><div style="border: 0pt none ; margin: 0pt; padding: 0pt;">';
		}

		foreach ( $posts as $post ) {
			$image = '';
			$url = '';
			if ( $thsource == 'custom-field' ) {
				$debug .= 'Using custom field;';
				$url = $basic_url = get_post_meta( $post->ID, get_option( 'relpoststh_customfield', $this->custom_field ), true );
				if ( strpos( $url, '/wp-content' ) !== false ) {
					$url = substr( $url, strpos( $url, '/wp-content' ) );
				}
				$theme_resize_url = get_option( 'relpoststh_theme_resize_url', '' );
				if ( ! empty( $theme_resize_url ) ) {
					$url = $theme_resize_url . '?src=' . $url . '&w=' . $width . '&h=' . $height . '&zc=1&q=90';
				}
			} else {
				$from_post_body = true;
				if ( current_theme_supports( 'post-thumbnails' ) ) { // using built in Wordpress feature
					$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
					$debug .= 'Post-thumbnails enabled in theme;';
					if ( ! ( empty( $post_thumbnail_id ) || $post_thumbnail_id === false ) ) { // post has thumbnail
						$debug .= 'Post has thumbnail '.$post_thumbnail_id.';';
						$debug .= 'Postthname: '.$poststhname.';';
						$image = wp_get_attachment_image_src( $post_thumbnail_id, $poststhname );
						$url = $image[0];
						$from_post_body = false;
					} else {
						$debug .= 'Post has no thumbnail;';
					}
				}
				if ( $from_post_body ) { // Theme does not support post-thumbnails, or post does not have assigned thumbnail
					$debug .= 'Getting image from post body;';
					$wud = wp_upload_dir();
					preg_match_all( '|<img.*?src=[\'"](' . $wud['baseurl'] . '.*?)[\'"].*?>|i', $post->post_content, $matches ); // searching for the first uploaded image in text

					//var_dump($matches);

					if ( isset( $matches ) and isset( $matches[1][0] ) ) {
						$image = $matches[1][0];
					} else {
						$debug .= 'No image was found;';
					}

					if ( strlen( trim( $image ) ) > 0 ) {
						$image_sizes = @getimagesize( $image );
						if ( $image_sizes === false ) {
							$debug .= 'Unable to determine parsed image size';
						}
						if ( $image_sizes !== false && isset( $image_sizes[0] ) && $image_sizes[0] == $width ) { // if this image is the same size as we need
							$debug .= 'Image used is the required size;';
							$url = $image;
						} else { // if not, search for resized thumbnail according to Wordpress thumbnails naming function
							$debug .= 'Changing image according to Wordpress standards;';
							$url = preg_replace( '/(-[0-9]+x[0-9]+)?(\.[^\.]*)$/', '-' . $width . 'x' . $height . '$2', $image );
						}
					} else {
						$debug .= 'Found wrong formatted image: '.$image.';';
					}
				}
				$basic_url = $url;
			}

			if ( strpos( $url, '/' ) === 0 ) {
				$debug .= 'Relative url: ' . $url . ';';
				$url = $basic_url = get_bloginfo( 'url' ) . $url;
			}

			$debug .= 'Image URL: '.$url.';';
			if ( empty( $basic_url ) ) { // parsed URL is empty or no file if can check
				$debug .= 'Image is empty or no file. Using default image;';
				$url = get_option( 'relpoststh_default_image', $this->default_image );
			}

			$title = $this->process_text_cut( $post->post_title, $text_length );
			$post_excerpt = ( empty( $post->post_excerpt ) ) ? $post->post_content : $post->post_excerpt;
			$excerpt = $this->process_text_cut( $post_excerpt, $excerpt_length );

			if ( ! empty( $title ) && ! empty( $excerpt ) ) {
				$title = '<b>' . $title . '</b>';
				$excerpt = '<br/>' . $excerpt;
			}

			$fontface = '';

			$debug .= 'Using title with size ' . $text_length . '. Using excerpt with size ' . $excerpt_length . ';';
			if ( $relpoststh_output_style == 'list' ) {
				$link = get_permalink( $post->ID );
				$fontface = str_replace( '"', "'", stripslashes( get_option( 'relpoststh_fontfamily', $this->font_family ) ) );
				$output .= '<li ';
				if ( ! $relpoststh_cleanhtml ) {
					$output .= ' style="float: left; padding: 0; margin:0; padding: 5px; display: block; border-right: 1px solid ' . get_option( 'relpoststh_bordercolor', $this->border_color ) . '; background-color: ' . get_option( 'relpoststh_background', $this->background ) . '" onmouseout="this.style.backgroundColor=\'' . get_option( 'relpoststh_background', $this->background ) . '\'" onmouseover="this.style.backgroundColor=\'' . get_option( 'relpoststh_hoverbackground', $this->hoverbackground ) . '\'"';
				}
				$output .= '>';
				$output .= '<a href="' . $link . '" ><img alt="' . $title . '" src="' . $url . '" width="' . $width . '" height="' . $height . '" ';
				if ( ! $relpoststh_cleanhtml ) {
					$output .= 'style="padding: 0px; margin: 0px; border: 0pt none;"';
				}
				$output .= '/></a>';
				if ( $text_height != '0' ) {
					$output .= '<a href="' . $link . '"';
					if ( ! $relpoststh_cleanhtml ) {
						$output .= ' style="display: block; width: ' . $width . 'px; overflow: hidden;height: ' . $text_height . 'px; font-family: ' . $fontface . '; font-style: normal; font-variant: normal; font-weight: normal; font-size: ' . get_option( 'relpoststh_fontsize', $this->font_size ) . 'px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; color: ' . get_option( 'relpoststh_fontcolor', $this->font_color ) . ';text-decoration: none;"'; }
					$output .= '><span>' . $title . $excerpt . '</span></a></li>';
				}
			} else {
				$output .= '<a onmouseout="this.style.backgroundColor=\'' . get_option( 'relpoststh_background', $this->background ) . '\'" onmouseover="this.style.backgroundColor=\'' . get_option( 'relpoststh_hoverbackground', $this->hoverbackground ) . '\'" style="background-color: ' . get_option( 'relpoststh_background', $this->background ) . '; border-right: 1px solid ' . get_option( 'relpoststh_bordercolor', $this->border_color ) . '; border-bottom: medium none; margin: 0pt; padding: 6px; display: block; float: left; text-decoration: none; text-align: left; cursor: pointer;" href="' . get_permalink( $post->ID ) . '">';
				$output .= '<div style="border: 0pt none ; margin: 0pt; padding: 0pt; width: ' . $width . 'px; height: ' . ( $height + $text_height ) . 'px;">';
				$output .= '<div style="border: 0pt none ; margin: 0pt; padding: 0pt; background: transparent url(' . $url . ') no-repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; width: ' . $width . 'px; height: ' . $height . 'px;"></div>';
				$output .= '<div style="border: 0pt none; margin: 3px 0pt 0pt; padding: 0pt; font-family: ' . $fontface . '; font-style: normal; font-variant: normal; font-weight: normal; font-size: ' . get_option( 'relpoststh_fontsize', $this->font_size ) . 'px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; color: ' . get_option( 'relpoststh_fontcolor', $this->font_color ) . ';">' . $title . $excerpt . '</div>';
				$output .= '</div>';
				$output .= '</a>';
			}
		} // end foreach
		if ( $relpoststh_output_style == 'list' ) {
			$output .= '</ul>';
		} else {
			$output .= '</div>';
		}
		$output .= '<div style="clear: both"></div>';
		return $this->finish_process( $output, $debug, $time );
	}

	function finish_process( $output, $debug, $time ) {
		$devmode = get_option( 'relpoststh_devmode', $this->devmode );
		if ( $devmode ) {
			$time = microtime( true ) - $time;
			$debug .= "Plugin execution time: $time sec;";
			$output .= '<!-- '.$debug.' -->';
		}
		return $output;
	}

	function process_text_cut( $text, $length ) {
		if ( $length == 0 ) {
			return '';
		} else {
			$text = htmlspecialchars( strip_tags( strip_shortcodes( $text ) ) );
			if ( function_exists( 'mb_strlen' ) ) {
				return ( ( mb_strlen( $text ) > $length ) ? mb_substr( $text, 0, $length ) . '...' : $text );
			} else {
				return ( ( strlen( $text ) > $length ) ? substr( $text, 0, $length ) . '...' : $text );
			}
		}
	}

	function is_relpoststh_show() {
		// Checking display options
		if ( ! is_single() && get_option( 'relpoststh_single_only', $this->single_only ) ) { // single only
			return false;
		}
		/* Check post type */
		$post_types = get_option( 'relpoststh_post_types', $this->post_types );
		$post_type = get_post_type();
		if ( ! in_array( $post_type, $post_types ) ) {
			return false;
		}
		/* Check categories */
		$id = get_the_ID();
		$categories_all = get_option( 'relpoststh_categoriesall', $this->categories_all );
		if ( $categories_all != '1' ) { // only specific categories were selected
			$post_categories = wp_get_object_terms( $id, array( 'category' ), array( 'fields' => 'ids' ) );
			$relpoststh_categories = get_option( 'relpoststh_categories' );
			if ( ! is_array( $relpoststh_categories ) || ! is_array( $post_categories ) ) { // no categories were selcted or post doesn't belong to any
				return false;
			}
			$common_categories = array_intersect( $relpoststh_categories, $post_categories );
			if ( empty( $common_categories ) ) { // post doesn't belong to specified categories
				return false;
			}
		}
		return true;
	}

	function admin_menu() {
		$page = add_menu_page( __( 'Related Posts Thumbnails', 'related-posts-thumbnails' ), __( 'Related Posts', 'related-posts-thumbnails' ), 'administrator', 'related-posts-thumbnails', array( $this, 'admin_interface' ) );
	}

	function admin_interface() {
		// Admin interface
		if ( isset( $_POST['action'] ) && ($_POST['action'] == 'update') ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'No access', 'related-posts-thumbnails' ) );
			}
			check_admin_referer( 'related-posts-thumbnails' );
			$validation = true;
			if ( ! empty( $_POST['relpoststh_year'] ) || ! empty( $_POST['relpoststh_month'] ) || ! empty( $_POST['relpoststh_year'] ) ) { // check date
				$set_date = sprintf( '%04d-%02d-%02d', $_POST['relpoststh_year'], $_POST['relpoststh_month'], $_POST['relpoststh_day'] );
				if ( checkdate( intval( $_POST['relpoststh_month'] ), intval( $_POST['relpoststh_day'] ), intval( $_POST['relpoststh_year'] ) ) === false ) {
					$validation = false;
					$error = __( 'Wrong date', 'related-posts-thumbnails' ) . ': ' . sprintf( '%d/%d/%d', $_POST['relpoststh_month'], $_POST['relpoststh_day'], $_POST['relpoststh_year'] );
				}
			} else {
				$set_date = '';
			}
			if ( $validation ) {

				if( isset( $_POST['relpoststh_single_only'] ) ) {
					update_option( 'relpoststh_single_only', sanitize_text_field( wp_unslash( $_POST['relpoststh_single_only'] ) ) );
				} else {
					update_option( 'relpoststh_single_only', '0' );
				}

				if( isset( $_POST['relpoststh_post_types'] ) ) {
					update_option( 'relpoststh_post_types', array_map( 'sanitize_text_field', wp_unslash( $_POST['relpoststh_post_types'] ) ) );
				} else {
					update_option( 'relpoststh_post_types', array() );
				}

				if( isset( $_POST['onlywiththumbs'] ) ) {
					update_option( 'relpoststh_onlywiththumbs', sanitize_text_field( wp_unslash( $_POST['onlywiththumbs'] ) ) );
				} else {
					update_option( 'relpoststh_onlywiththumbs', '0' );
				}

				if( isset( $_POST['relpoststh_output_style'] ) ) {
					update_option( 'relpoststh_output_style', sanitize_text_field( wp_unslash( $_POST['relpoststh_output_style'] ) ) );
				}

				if( isset( $_POST['relpoststh_cleanhtml'] ) ) {
					update_option( 'relpoststh_cleanhtml', sanitize_text_field( wp_unslash( $_POST['relpoststh_cleanhtml'] ) ) );
				} else {
					update_option( 'relpoststh_cleanhtml', '0' );
				}

				if( isset( $_POST['relpoststh_auto'] ) ) {
					update_option( 'relpoststh_auto', sanitize_text_field( wp_unslash( $_POST['relpoststh_auto'] ) ) );
				} else {
					update_option( 'relpoststh_auto', '0' );
				}

				if( isset( $_POST['relpoststh_top_text'] ) ) {
					update_option( 'relpoststh_top_text', wp_kses( $_POST['relpoststh_top_text'], $this->wp_kses_rp_args ) );
				}


				if( isset( $_POST['relpoststh_number'] ) ) {
					update_option( 'relpoststh_number', absint( $_POST['relpoststh_number'] ) );
				}

				if( isset( $_POST['relpoststh_relation'] ) ) {
					update_option( 'relpoststh_relation', sanitize_text_field( wp_unslash( $_POST['relpoststh_relation'] ) ) );
				}

				if( isset( $_POST['relpoststh_default_image'] ) ) {
					update_option( 'relpoststh_default_image', sanitize_text_field( wp_unslash( $_POST['relpoststh_default_image'] ) ) );
				}

				if( isset( $_POST['relpoststh_poststhname'] ) ) {
					update_option( 'relpoststh_poststhname', sanitize_text_field( wp_unslash( $_POST['relpoststh_poststhname'] ) ) );
				}

				if( isset( $_POST['relpoststh_background'] ) ) {
					update_option( 'relpoststh_background', sanitize_text_field( wp_unslash( $_POST['relpoststh_background'] ) ) );
				}

				if( isset( $_POST['relpoststh_hoverbackground'] ) ) {
					update_option( 'relpoststh_hoverbackground', sanitize_text_field( wp_unslash( $_POST['relpoststh_hoverbackground'] ) ) );
				}


				if( isset( $_POST['relpoststh_bordercolor'] ) ) {
					update_option( 'relpoststh_bordercolor', sanitize_text_field( wp_unslash( $_POST['relpoststh_bordercolor'] ) ) );
				}

				if( isset( $_POST['relpoststh_fontcolor'] ) ) {
					update_option( 'relpoststh_fontcolor', sanitize_text_field( wp_unslash( $_POST['relpoststh_fontcolor'] ) ) );
				}

				if( isset( $_POST['relpoststh_fontsize'] ) ) {
					update_option( 'relpoststh_fontsize', sanitize_text_field( wp_unslash( $_POST['relpoststh_fontsize'] ) ) );
				}

				if( isset( $_POST['relpoststh_fontfamily'] ) ) {
					update_option( 'relpoststh_fontfamily', sanitize_text_field( wp_unslash( $_POST['relpoststh_fontfamily'] ) ) );
				}

				if( isset( $_POST['relpoststh_textlength'] ) ) {
					update_option( 'relpoststh_textlength', sanitize_text_field( wp_unslash( $_POST['relpoststh_textlength'] ) ) );
				}

				if( isset( $_POST['relpoststh_excerptlength'] ) ) {
					update_option( 'relpoststh_excerptlength', sanitize_text_field( wp_unslash( $_POST['relpoststh_excerptlength'] ) ) );
				}

				if( isset( $_POST['relpoststh_thsource'] ) ) {
					update_option( 'relpoststh_thsource', sanitize_text_field( wp_unslash( $_POST['relpoststh_thsource'] ) ) );
				}

				if( isset( $_POST['relpoststh_customfield'] ) ) {
					update_option( 'relpoststh_customfield', sanitize_text_field( wp_unslash( $_POST['relpoststh_customfield'] ) ) );
				}

				if( isset( $_POST['relpoststh_theme_resize_url'] ) ) {
					update_option( 'relpoststh_theme_resize_url', sanitize_text_field( wp_unslash( $_POST['relpoststh_theme_resize_url'] ) ) );
				}

				if( isset( $_POST['relpoststh_customwidth'] ) ) {
					update_option( 'relpoststh_customwidth', sanitize_text_field( wp_unslash( $_POST['relpoststh_customwidth'] ) ) );
				}

				if( isset( $_POST['relpoststh_customheight'] ) ) {
					update_option( 'relpoststh_customheight', sanitize_text_field( wp_unslash( $_POST['relpoststh_customheight'] ) ) );
				}

				if( isset( $_POST['relpoststh_textblockheight'] ) ) {
					update_option( 'relpoststh_textblockheight', sanitize_text_field( wp_unslash( $_POST['relpoststh_textblockheight'] ) ) );
				}

				if( isset( $_POST['relpoststh_customwidth'] ) ) {
					update_option( 'relpoststh_customwidth', sanitize_text_field( wp_unslash( $_POST['relpoststh_customwidth'] ) ) );
				}

				if( isset( $_POST['relpoststh_categories'] ) ) {
					update_option( 'relpoststh_categories', array_map( 'sanitize_text_field', wp_unslash( $_POST['relpoststh_categories'] ) ) );
				} else {
					update_option( 'relpoststh_categories', array() );
				}

				if( isset( $_POST['relpoststh_categoriesall'] ) ) {
					update_option( 'relpoststh_categoriesall', sanitize_text_field( wp_unslash( $_POST['relpoststh_categoriesall'] ) ) );
				} else {
					update_option( 'relpoststh_categoriesall', '' );
				}

				if( isset( $_POST['relpoststh_show_categoriesall'] ) ) {
					update_option( 'relpoststh_show_categoriesall', sanitize_text_field( wp_unslash( $_POST['relpoststh_show_categoriesall'] ) ) );
				} else {
					update_option( 'relpoststh_show_categoriesall', '' );
				}

				if( isset( $_POST['relpoststh_show_categories'] ) ) {
					update_option( 'relpoststh_show_categories', array_map( 'sanitize_text_field', wp_unslash( $_POST['relpoststh_show_categories'] ) ) );
				} else {
					update_option( 'relpoststh_show_categories', array() );
				}

				if( isset( $_POST['relpoststh_devmode'] ) ) {
					update_option( 'relpoststh_devmode', sanitize_text_field( wp_unslash( $_POST['relpoststh_devmode'] ) ) );
				} else {
					update_option( 'relpoststh_devmode', '0' );
				}

				update_option( 'relpoststh_startdate', $set_date );

				if( isset( $_POST['relpoststh_custom_taxonomies'] ) ) {
					update_option( 'relpoststh_custom_taxonomies', array_map( 'sanitize_text_field', wp_unslash( $_POST['relpoststh_custom_taxonomies'] ) ) );
				} else {
					update_option( 'relpoststh_custom_taxonomies', array() );
				}

				echo "<div class='updated fade'><p>" . __( 'Settings updated', 'related-posts-thumbnails' ) .'</p></div>';
			} else {
				echo "<div class='error fade'><p>" . __( 'Settings update failed', 'related-posts-thumbnails' ) . '. '. $error . '</p></div>';
			}
		}
		$available_sizes = array( 'thumbnail' => 'thumbnail', 'medium' => 'medium' );
		if ( current_theme_supports( 'post-thumbnails' ) ) {
			global $_wp_additional_image_sizes;
			if ( is_array( $_wp_additional_image_sizes ) ) {
				$available_sizes = array_merge( $available_sizes, $_wp_additional_image_sizes );
			}
		}
		$relpoststh_single_only        = get_option( 'relpoststh_single_only', $this->single_only );
		$relpoststh_auto               = get_option( 'relpoststh_auto', $this->auto );
		$relpoststh_cleanhtml          = get_option( 'relpoststh_cleanhtml', 0 );
		$relpoststh_relation           = get_option( 'relpoststh_relation', $this->relation );
		$relpoststh_thsource           = get_option( 'relpoststh_thsource', $this->thsource );
		$relpoststh_devmode            = get_option( 'relpoststh_devmode', $this->devmode );
		$relpoststh_categoriesall      = get_option( 'relpoststh_categoriesall', $this->categories_all );
		$relpoststh_categories         = get_option( 'relpoststh_categories' );
		$relpoststh_show_categories    = get_option( 'relpoststh_show_categories', get_option( 'relpoststh_categories' ) );
		$relpoststh_show_categoriesall = get_option( 'relpoststh_show_categoriesall', $relpoststh_categoriesall );
		$onlywiththumbs                = get_option( 'relpoststh_onlywiththumbs', false );
		$relpoststh_startdate          = explode( '-', get_option( 'relpoststh_startdate' ) );
		$relpoststh_output_style       = get_option( 'relpoststh_output_style', $this->output_style );
		$thsources                     = array( 'post-thumbnails' => __( 'Post thumbnails', 'related_posts_thumbnails' ), 'custom-field' => __( 'Custom field', 'related_posts_thumbnails' ) );
		$categories                    = get_categories();
		if ( $this->wp_version >= 3 ) {
			$post_types = get_post_types( array( 'public' => 1 ) );
		} else {
			$post_types = get_post_types();
		}
		$relpoststh_post_types = get_option( 'relpoststh_post_types', $this->post_types );
		$output_styles = array( 'div' => __( 'Blocks', 'related-posts-thumbnails' ), 'list' => __( 'List', 'related-posts-thumbnails' ) );
		$relation_options = array( 'categories' => __( 'Categories', 'related-posts-thumbnails' ), 'tags' => __( 'Tags', 'related-posts-thumbnails' ), 'both' => __( 'Categories and Tags', 'related-posts-thumbnails' ), 'no' => __( 'Random', 'related-posts-thumbnails' ), 'custom' => __( 'Custom', 'related-posts-thumbnails' ) );
		if ( $this->wp_version >= 3 ) {
			$custom_taxonomies = get_taxonomies( array( 'public' => 1 ) );
			$relpoststh_custom_taxonomies = get_option( 'relpoststh_custom_taxonomies', $this->custom_taxonomies );
			if ( ! is_array( $relpoststh_custom_taxonomies ) ) {
				$relpoststh_custom_taxonomies = array();
			}
		} else {
			$relation_options['custom'] .= ' '. __( '(This option is available for WP v3+ only)', 'related_posts_thumbnails' );
		}
		?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {

					$('#wpbr_rpt_relation_options').on('click', function(){

						$('.nav-tab').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');

						$('#content_general_options').hide();
						$('#content_style_options').hide();
						$('#content_thumbnail_options').hide();
						$('#content_relation_options').show();

					});

					$('#wpbr_rpt_general_options').on('click', function(){

						$('.nav-tab').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');

						$('#content_general_options').show();
						$('#content_style_options').hide();
						$('#content_thumbnail_options').hide();
						$('#content_relation_options').hide();

					});


					$('#wpbr_rpt_style_options').on('click', function(){

						$('.nav-tab').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');

						$('#content_general_options').hide();
						$('#content_style_options').show();
						$('#content_thumbnail_options').hide();
						$('#content_relation_options').hide();

					});


					$('#wpbr_rpt_thumbnails_source').on('click', function(){

						$('.nav-tab').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active');

						$('#content_general_options').hide();
						$('#content_style_options').hide();
						$('#content_thumbnail_options').show();
						$('#content_relation_options').hide();

					});




					$(".select_all").click(function(){
						if (this.checked) {
							$(this).parent().find("div.select_specific").hide();
						}
						else {
							$(this).parent().find("div.select_specific").show();
						}
					});
					$('#relpoststh_thsource').change(function(){
						if (this.value == 'post-thumbnails') {
							$('#relpoststh-post-thumbnails').show();
							$('#relpoststh-custom-field').hide();
						}
						else {
							$('#relpoststh-post-thumbnails').hide();
							$('#relpoststh-custom-field').show();
						}
					});
					$('#relpoststh_output_style').change(function(){
						if (this.value == 'list') {
							$('#relpoststh_cleanhtml').show();
						}
						else {
							$('#relpoststh_cleanhtml').hide();
						}
					});
					$("input[name='relpoststh_relation']").change(function(){
						if ($("input[name='relpoststh_relation']:checked").val() == 'custom') {
							$('#custom_taxonomies').show();
						}
						else {
							$('#custom_taxonomies').hide();
						}
					});

					// Ajax for subsriber
					$('#rpt_subscribe_btn').on('click', function(event) {
						event.preventDefault();

						var subscriber_mail = $('#rpt_subscribe_mail').val();
						var name = $('#rpt_subscribe_name').val();
						if (!subscriber_mail) {
							$('.rpt_subscribe_warning').html('Please Enter Email');
							return;
						}

						$.ajax({
							url: ajaxurl,
							type: 'POST',
							data: {
								subscriber_mail : subscriber_mail,
								action : 'relpost_subscriber',
								name : name
							},
							beforeSend : function() {
								$('.rpt_subscribe_loader').show();
								$('#rpt_subscribe_btn').attr('disabled', 'disabled');
							}
						})
						.done(function(res) {
							$('.rpt_return_message').html(res);
							$('.rpt_subscribe_loader').hide();
						});

					});
				});
			</script>
			<style>

			#rpt_subscribe_btn{
				display: block;
    			margin: 20px auto 0;
			}

				#relpoststh-settings .nav-tab-wrapper li a {
				  float: none;
				  margin:0;
				  padding: 7px 20px;
				  display: block;
				  height: auto;
				  background: none;
				}

				#relpoststh-settings .nav-tab-wrapper li{
				  margin-bottom: 0;
				  margin-left: -1px;
				}
				#relpoststh-settings::after{
				  display: table;
				  content: "";
				  clear: both;
				}
				#relpoststh-settings{
				  display: table;
				  width: calc(100% - 2px);
				  border:1px solid #ccc;
				  margin: 0;
				}

				.relpoststh .wpbr-tabsWrapper{
				  width: calc(100% - 265px);
				  float: left;
				}
				#relpoststh-settings .nav-tab-wrapper{
				  display: table-cell;
				  vertical-align: top;
				  width: 202px;
				}
				.relpoststh .form-table th:first-child{
				   width: 110px;
				}
				.relpoststh #relpoststh-settings .metabox-holder{
				  display: table-cell;
				  padding: 10px;
				  background: #fff;
				}
				#relpoststh-settings .nav-tab-wrapper li:first-child{
				  margin-top: -1px;
				}
				#relpoststh-settings .nav-tab-wrapper li a.nav-tab-active{
				  background: #fff;
				}
				#relpoststh-settings .nav-tab-wrapper li a.nav-tab-active{
				  border-right: transparent;
				}
				#relpoststh-settings .nav-tab-wrapper li:last-child a{
				  border-bottom: 1px solid #ccc;
				}
				.relpoststh #the-list .approve{
				  display: block;
				}
				.relpoststh .postbox{
				  padding: 10px;
				}
				.relpoststh .wpbr-sidebar{
				  width: 255px;
				  float: right;
				  min-width: inherit;
				  box-sizing: border-box;
				}
				.relpoststh .wpbr-sidebar .postbox{
				  min-width: inherit;
				  width: auto;
				}
				.relpoststh .wpbr-sidebar ul li a{
				  width: 100%;
				  display: block;
				}
				.relpoststh .wpbr-sidebar .wp-core-ui .button{
				  position: relative;
				}
				.relpoststh .wpbr-sidebar .dashicons{
				  position: absolute;
				  right: 10px;
				  margin-top: 3px;
				}
				.relpoststh .postbox ul li a{
				   position: relative;
				}

				.relpoststh .wpbr-button-container{
				  padding: 10px 0;
				  overflow: hidden;
				}
				.relpoststh .wpbr-social-links a{
				  text-decoration: none;
				}
				.relpoststh .wpbr-button-container{
				  padding: 10px;
				  overflow: hidden;
				  border:1px solid #ccc;

				}
				.relpoststh .wpbr-button-container.top{
				  border-bottom: 0;
				}
				.relpoststh .wpbr-button-container.bottom{
				  border-top: 0;
				}
				.relpoststh .wpbrmedia-settings-submit{
				  float:right;
				}
				.relpoststh .wpbr-sidebar h2{
				  margin: 0;
				  padding: 10px;
				  border-bottom: 1px solid #ccc;
				}
				.relpoststh .wpbr-sidebar ul li .twitter .dashicons {
				    color: #45b0e3;
				}
				.relpoststh .wpbr-sidebar ul li .facebook .dashicons {
				    color: #3b5998;
				}
				.relpoststh .wpbr-sidebar ul li .wordpress .dashicons {
				    color: #21759b;
				}
				.relpoststh .wpbr-sidebar ul li .rss .dashicons {
				    color: #FF6600;
				}
				#relpoststh-settings p.submit{
				  display: none;
				}
				.relpoststh #wpbr_custom_css .form-table th{
				   width: 0;
				}
				.relpoststh #wpbr_custom_css .form-table td{
				   width: 100%;
				}
				.relpoststh .wpbr-wrap:after{
				   content: '';
				   display: table;
				   clear: both;
				}

				.relpoststh .metabox-holder{
				   padding-top: 0 !important;
				   padding-right: 15px !important;
				}

				.relpoststh .setting-notification{
				   position: absolute;
				   width: auto;
				   padding: 16px 10px;
				   left: 0;
				   top: 0;
				   background-color: #fcf8e3;
				   color: #c09853;
				   height: 100%;
				   box-sizing: border-box;
				   display: none;
				}
				#relpoststh-settings .postbox{
					border: 0px solid #e5e5e5;
				    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.0);
				    box-shadow: 0 1px 1px rgba(0,0,0,.0);
				}
				#relpoststh-settings .nav-tab-wrapper{
					padding-top: 0;
				    border-bottom: 0px solid #ccc;
				    border-right: 1px solid #ccc;
				}
				#relpoststh-settings .nav-tab-wrapper li a.nav-tab-active{
					margin: 0 -1px 0 0;
					outline: none;
					box-shadow: none;
				}
				#relpoststh-settings .nav-tab-wrapper .nav-tab{
					border-right: 0;
				}
				.plugins_lists li{
					padding-bottom: 12px;
					line-height: 1.4;
				}
			</style>
			<div class="wrap relpoststh">
				<div class="icon32" id="icon-options-general"><br></div>
				<h2><?php _e( 'Related Posts Thumbnails Settings', 'related-posts-thumbnails' ); ?></h2>

				<form action="?page=related-posts-thumbnails" method="POST" style="clear:both;">
					<input type="hidden" name="action" value="update" />
					<?php wp_nonce_field( 'related-posts-thumbnails' ); ?>

				<div class="wpbr-wrap"><div class="wpbr-tabsWrapper">
					<div class="wpbr-button-container top">
							<div class="setting-notification">
								<?php echo __( 'Settings have changed, you should save them!' , 'related-posts-thumbnails' ); ?>
							</div>
	                	<input type="submit" name="Submit" class="wpbrmedia-settings-submit button button-primary button-big" value="<?php esc_html_e( 'Save Settings','related-posts-thumbnails' );?>" id="wpbr_save_setting_top">
	                </div>

					<div id="relpoststh-settings" class="">
						<ul class="nav-tab-wrapper">
							<li> <a href="#nogo" class="nav-tab nav-tab-active" id="wpbr_rpt_general_options">General Display Options</a> </li>
							<li> <a href="#nogo" class="nav-tab" id="wpbr_rpt_thumbnails_source">Thumbnails source</a> </li>
							<li> <a href="#nogo" class="nav-tab" id="wpbr_rpt_style_options">Style options:</a> </li>
							<li> <a href="#nogo" class="nav-tab" id="wpbr_rpt_relation_options">Relation Builder Options</a> </li>
						</ul>

						<div class="metabox-holder">
							<div class="postbox" style="padding: 20px" id="content_general_options">
								<h2><?php _e( 'General Display Options', 'related-posts-thumbnails' ); ?>:</h2>

								<table class="form-table">
									<tr valign="top">
										<th scope="row"><?php _e( 'Automatically append to the post content', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="checkbox" name="relpoststh_auto" id="relpoststh_auto" value="1" <?php if ( $relpoststh_auto ) { echo 'checked="checked"'; } ?>/>
											<label for="relpoststh_auto"><?php _e( 'Or use <b>&lt;?php get_related_posts_thumbnails(); ?&gt;</b> in the Loop', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Developer mode', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="checkbox" name="relpoststh_devmode" id="relpoststh_devmode" value="1" <?php if ( $relpoststh_devmode ) { echo 'checked="checked"'; } ?>/>
											<label for="relpoststh_devmode"><?php _e( 'This will add debugging information in HTML source', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Page type', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="checkbox" name="relpoststh_single_only" id="relpoststh_single_only" value="1" <?php if ( $relpoststh_single_only ) { echo 'checked="checked"'; } ?>/>
											<label for="relpoststh_single_only"><?php _e( 'Show on single posts only', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Post types', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<?php if ( is_array( $post_types ) && count( $post_types ) ) :  ?>
											<?php foreach ( $post_types as $post_type ) : ?>
											<input type="checkbox" name="relpoststh_post_types[]" id="pt_<?php echo $post_type; ?>" value="<?php echo $post_type; ?>" <?php if ( in_array( $post_type, $relpoststh_post_types ) ) { echo 'checked="checked"'; } ?>/>
											<label for="pt_<?php echo $post_type; ?>"><?php echo $post_type; ?></label>
											<?php endforeach; ?>
											<?php endif; ?>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Categories on which related thumbnails will appear', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<?php $this->display_categories_list( $relpoststh_categoriesall, $categories, $relpoststh_categories, 'relpoststh_categoriesall', 'relpoststh_categories' ); ?>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Categories that will appear in related thumbnails', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<?php $this->display_categories_list( $relpoststh_show_categoriesall, $categories, $relpoststh_show_categories, 'relpoststh_show_categoriesall', 'relpoststh_show_categories' ); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Include only posts after', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<?php _e( 'Year', 'related-posts-thumbnails' ); ?>: <input type="text" name="relpoststh_year" size="4" value="<?php if ( isset( $relpoststh_startdate[0] ) ) { echo $relpoststh_startdate[0]; } ?>"> <?php _e( 'Month', 'related-posts-thumbnails' ); ?>: <input type="text" name="relpoststh_month" size="2" value="<?php if ( isset( $relpoststh_startdate[1] ) ) { echo $relpoststh_startdate[1]; } ?>"> <?php _e( 'Day', 'related-posts-thumbnails' ); ?>: <input type="text" name="relpoststh_day" size="2" value="<?php if ( isset( $relpoststh_startdate[2] ) ) { echo $relpoststh_startdate[2]; } ?>"> <label for="relpoststh_excerptlength"><?php _e( 'Leave empty for all posts dates', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Top text', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_top_text" value="<?php echo stripslashes( htmlspecialchars( get_option( 'relpoststh_top_text', $this->top_text ) ) ); ?>" size="50"/>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Number of similar posts to display', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_number" value="<?php echo get_option( 'relpoststh_number', $this->number ); ?>" size="2"/>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Default image URL', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_default_image" value="<?php echo get_option( 'relpoststh_default_image', $this->default_image );?>" size="50"/>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Thumbnails source', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<select name="relpoststh_thsource"  id="relpoststh_thsource">
												<?php foreach ( $thsources as $name => $title ) : ?>
												<option value="<?php echo $name; ?>" <?php if ( $relpoststh_thsource == $name ) { echo 'selected'; } ?>><?php echo $title; ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
								</table>
							</div>

							<div class="postbox" style="padding: 20px; display:none;" id="content_thumbnail_options">
								<div style="padding: 20px" id="relpoststh-post-thumbnails" <?php if ( $relpoststh_thsource != 'post-thumbnails' ) : ?> style="display:none" <?php endif; ?>>
									<h2><?php _e( 'Thumbnails source', 'related-posts-thumbnails' ); ?>:</h2>
									<table class="form-table">
										<tr valign="top">
											<th scope="row"><?php _e( 'Post-thumbnails name', 'related-posts-thumbnails' ); ?>:</th>
											<td>
												<select name="relpoststh_poststhname">
													<?php foreach ( $available_sizes as $size_name => $size ) : ?>
													<option <?php if ( $size_name == get_option( 'relpoststh_poststhname', $this->poststhname ) ) { echo 'selected'; } ?>><?php echo $size_name; ?></option>
													<?php endforeach; ?>
												</select>
												<?php if ( ! current_theme_supports( 'post-thumbnails' ) ) : ?>
												(<?php _e( 'Your theme has to support post-thumbnails to have more choices', 'related-posts-thumbnails' ); ?>)
												<?php endif; ?>
											</td>
										</tr>
										<?php if ( current_theme_supports( 'post-thumbnails' ) ) :  ?>
										<tr>
											<th scope="row"><?php _e( 'Show posts only with thumbnails', 'related-posts-thumbnails' ); ?>:</th>
											<td>
												<input type="checkbox" name="onlywiththumbs" id="onlywiththumbs" value="1" <?php if ( $onlywiththumbs ) { echo 'checked="checked"'; } ?>/>
												<label for="onlywiththumbs"><?php _e( 'Only posts with assigned Featured Image', 'related-posts-thumbnails' ); ?></label><br />
											</td>
										</tr>
										<?php endif; ?>
									</table>
								</div>
								<div style="padding: 20px" id="relpoststh-custom-field" <?php if ( $relpoststh_thsource != 'custom-field' ) : ?> style="display:none" <?php endif; ?>>
									<h2><?php _e( 'Thumbnails source', 'related-posts-thumbnails' ); ?>:</h2>
									<table class="form-table">
										<tr valign="top">
											<th scope="row"><?php _e( 'Custom field name', 'related-posts-thumbnails' ); ?>:</th>
											<td>
												<input type="text" name="relpoststh_customfield" value="<?php echo get_option( 'relpoststh_customfield', $this->custom_field );?>" size="50"/>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php _e( 'Size', 'related-posts-thumbnails' ); ?>:</th>
											<td>
												<?php _e( 'Width', 'related-posts-thumbnails' ); ?>: <input type="text" name="relpoststh_customwidth" value="<?php echo get_option( 'relpoststh_customwidth', $this->custom_width );?>" size="3"/>px x
												<?php _e( 'Height', 'related-posts-thumbnails' ); ?>: <input type="text" name="relpoststh_customheight" value="<?php echo get_option( 'relpoststh_customheight', $this->custom_height );?>" size="3"/>px
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php _e( 'Theme resize url', 'related-posts-thumbnails' ); ?>:</th>
											<td>
												<input type="text" name="relpoststh_theme_resize_url" value="<?php echo get_option( 'relpoststh_theme_resize_url', '' );?>" size="50"/>
												(<?php _e( 'If your theme resizes images, enter URL to its resizing PHP file', 'related-posts-thumbnails' ); ?>)
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="postbox" style="padding: 20px; display:none;" id="content_style_options">
								<h2><?php _e( 'Style options', 'related-posts-thumbnails' ); ?>:</h2>
								<table class="form-table">
									<tr>
										<th scope="row"><?php _e( 'Output style', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<select name="relpoststh_output_style"  id="relpoststh_output_style">
												<?php foreach ( $output_styles as $name => $title ) : ?>
												<option value="<?php echo $name; ?>" <?php if ( $relpoststh_output_style == $name ) { echo 'selected'; } ?>><?php echo $title; ?></option>
												<?php endforeach; ?>
											</select>
											<span id="relpoststh_cleanhtml" style="display: <?php if ( $relpoststh_output_style == 'list' ) { echo 'inline';
												} else { echo 'none'; }?>;"><?php _e( 'Turn off plugin styles', 'related-posts-thumbnails' ); ?> <input type="checkbox" name="relpoststh_cleanhtml" <?php if ( $relpoststh_cleanhtml ) { echo 'checked="checked"'; } ?> /></span>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Background color', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_background" value="<?php echo get_option( 'relpoststh_background', $this->background ); ?>" size="7"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Background color on mouse over', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_hoverbackground" value="<?php echo get_option( 'relpoststh_hoverbackground', $this->hoverbackground ); ?>" size="7"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Border color', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_bordercolor" value="<?php echo get_option( 'relpoststh_bordercolor', $this->border_color )?>" size="7"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Font color', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_fontcolor" value="<?php echo get_option( 'relpoststh_fontcolor', $this->font_color ); ?>" size="7"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Font family', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_fontfamily" value="<?php echo stripslashes( htmlspecialchars( get_option( 'relpoststh_fontfamily', $this->font_family ) ) ); ?>" size="50"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Font size', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_fontsize" value="<?php echo get_option( 'relpoststh_fontsize', $this->font_size )?>" size="7"/>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Text maximum length', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_textlength" value="<?php echo get_option( 'relpoststh_textlength', $this->text_length )?>" size="7"/>
											<label for="relpoststh_textlength"><?php _e( 'Set 0 for no title', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Excerpt maximum length', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_excerptlength" value="<?php echo get_option( 'relpoststh_excerptlength', $this->excerpt_length )?>" size="7"/>
											<label for="relpoststh_excerptlength"><?php _e( 'Set 0 for no excerpt', 'related-posts-thumbnails' ); ?></label><br />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( 'Text block height', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<input type="text" name="relpoststh_textblockheight" value="<?php echo get_option( 'relpoststh_textblockheight', $this->text_block_height )?>" size="7"/> px
										</td>
									</tr>
								</table>
							</div>
							<div class="postbox" style="padding: 20px; display:none;" id="content_relation_options">
								<h2><?php _e( 'Relation Builder Options', 'related-posts-thumbnails' ); ?>:</h2>
								<table class="form-table">
									<tr valign="top">
										<th scope="row"><?php _e( 'Relation based on', 'related-posts-thumbnails' ); ?>:</th>
										<td>
											<?php if ( is_array( $relation_options ) && count( $relation_options ) ) : ?>
											<?php foreach ( $relation_options as $ro_key => $ro_name ) : ?>
											<input type="radio" name="relpoststh_relation" id="relpoststh_relation_<?php echo $ro_key; ?>" value="<?php echo $ro_key; ?>" <?php if ( $relpoststh_relation == $ro_key ) { echo 'checked="checked"'; } ?>/>
											<label for="relpoststh_relation_<?php echo $ro_key; ?>"><?php echo $ro_name; ?></label><br />
											<?php endforeach; ?>
											<?php endif; ?>
											<div id="custom_taxonomies" style="display: <?php if ( $relpoststh_relation == 'custom' ) { echo 'inline';
								} else { echo 'none'; }?>;">
																<?php if ( is_array( $custom_taxonomies ) && count( $custom_taxonomies ) ) : ?>
																<?php foreach ( $custom_taxonomies as $custom_taxonomy ) : ?>
																<input type="checkbox" name="relpoststh_custom_taxonomies[]" id="ct_<?php echo $custom_taxonomy; ?>" value="<?php echo $custom_taxonomy; ?>" <?php if ( in_array( $custom_taxonomy, $relpoststh_custom_taxonomies ) ) { echo 'checked="checked"'; } ?>/>
																<label for="ct_<?php echo $custom_taxonomy; ?>"><?php echo $custom_taxonomy; ?></label>
																<?php endforeach; ?>
																<?php endif; ?>
															</div>
														</td>
													</tr>
												</table>
							</div>
							<!-- <input name="Submit" value="<?php _e( 'Save Changes', 'related-posts-thumbnails' ); ?>" type="submit" class="button-primary"> -->
						</div>
					</div>

					<div class="wpbr-button-container bottom">
		                  <div class="wpbr-social-links alignleft">
		                  	<a href="https://profiles.wordpress.org/hiddenpearls/" class="wordpress" target="_blank"><span class="dashicons dashicons-wordpress"></span></a>
		                  </div>
		                  <input type="submit" name="Submit" class="wpbrmedia-settings-submit button button-primary button-big" value="<?php esc_html_e( 'Save Settings','related-posts-thumbnails' ); ?>" id="wpbr_save_setting_bottom">
		                  </div>
					</div>

				    <div class="metabox-holder wpbr-sidebar">
		              <div class="sidebar postbox">
						 <h2><?php esc_html_e( 'Spread the Word' , 'related-posts-thumbnails' )?></h2>
			            <ul>
							<li>
								<a href="http://twitter.com/share?text=This is Best Related Post Thumbnails Plugin for WordPress&url=https://wordpress.org/support/view/plugin-reviews/related-posts-thumbnails" data-count="none"  class="button twitter" target="_blank" title="Post to Twitter Now"><?php esc_html_e( 'Share on Twitter' , 'related-posts-thumbnails' )?><span class="dashicons dashicons-twitter"></span></a>
							</li>

							<li>
								<a href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/support/view/plugin-reviews/related-posts-thumbnails" class="button facebook" target="_blank" title="Share with your facebook friends about this awesome plugin."><?php esc_html_e( 'Share on Facebook' , 'related-posts-thumbnails' )?><span class="dashicons dashicons-facebook"></span>
								</a>
							</li>

							<li>
								<a href="https://wordpress.org/support/view/plugin-reviews/related-posts-thumbnails?filter=5" class="button wordpress" target="_blank" title="Rate on Wordpress.org"><?php esc_html_e( 'Rate on Wordpress.org' , 'related-posts-thumbnails' )?><span class="dashicons dashicons-wordpress"></span>
								</a>
							</li>
						</ul>
		            </div>
								<form>

								<div class="sidebar postbox">

									<h2><?php esc_html_e( 'Subscribe Newsletter' , 'related-posts-thumbnails' )?></h2>
										<ul>
											<li>
												<label for="">Email</label>
												<input type="email" name="subscriber_mail" value="<?php echo get_option( 'admin_email' ) ?>" id="rpt_subscribe_mail">
												<p class='rpt_subscribe_warning'></p>
											</li>
											<li>
												<label for="">Name</label>
												<input type="text" name="subscriber_name" id="rpt_subscribe_name" value="<?php echo wp_get_current_user()->display_name ?>" id="rpt_subscribe_mail">
											</li>
											<li>
												<input type="submit" value="Subscribe Now" class="button button-primary button-big"  id='rpt_subscribe_btn' />
												<img src="<?php echo admin_url( 'images/spinner.gif' ) ?>" class='rpt_subscribe_loader' style="display:none" />
											</li>
											<li>
												<p class='rpt_return_message'></p>
											</li>
										</ul>
								</div>

								<div class="sidebar postbox">
									<h2><?php esc_html_e( 'Recommended Plugins' , 'related-posts-thumbnails' )?></h2>
									<!-- <p>Following are the plugins highly recommend by Team WPBrigade.</p> -->
									<ul class="plugins_lists">
										<li>
											<a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=related-posts-lite&utm_medium=sidebar&utm_campaign=pro-upgrade" data-count="none"  target="_blank" title="Post to Twitter Now"><?php esc_html_e( 'LoginPress - Login Customizer' , 'related-posts-thumbnails' )?></a>  
										</li>

										<li>
											<a href="https://analytify.io/ref/73/?utm_source=related-posts-lite&utm_medium=sidebar&utm_campaign=pro-upgrade"  target="_blank" title="Share with your facebook friends about this awesome plugin."><?php esc_html_e( 'Google Analytics by Analytify' , 'related-posts-thumbnails' )?></span>
											</a>
										</li>

										<li>
											<a href="http://wpbrigade.com/recommend/maintenance-mode"  target="_blank" title="Under Construction & Maintenance mode"><?php esc_html_e( 'Under Construction & Maintenance mode' , 'related-posts-thumbnails' )?></span>
											</a>
										</li>
									</ul>
								</div>
					</div>

				</div>
				</form>
			<p style="margin-top: 40px;"><small><?php _e( 'If you experience some problems with this plugin please let me know about it on <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/related-posts/">Plugin\'s homepage</a>. If you think this plugin is awesome please vote on <a target="_blank" href="https://wordpress.org/plugins/related-posts-thumbnails/">Wordpress plugin page</a>. Thanks!', 'related-posts-thumbnails' ); ?></small></p>
			<?php
	}

	function display_categories_list( $categoriesall, $categories, $selected_categories, $all_name, $specific_name ) {
	?>
		<input id="<?php echo $all_name; ?>" class="select_all" type="checkbox" name="<?php echo $all_name; ?>" value="1" <?php if ( $categoriesall == '1' ) { echo 'checked="checked"'; } ?>/>
		<label for="<?php echo $all_name; ?>"><?php _e( 'All', 'related-posts-thumbnails' ); ?></label>
		<div class="select_specific" <?php if ( $categoriesall == '1' ) : ?> style="display:none" <?php endif; ?>>
			<?php foreach ( $categories as $category ) : ?>
			<input type="checkbox" name="<?php echo $specific_name; ?>[]" id="<?php echo $specific_name; ?>_<?php echo $category->category_nicename; ?>" value="<?php echo $category->cat_ID; ?>" <?php if ( in_array( $category->cat_ID, (array) $selected_categories ) ) { echo 'checked="checked"'; } ?>/>
			<label for="<?php echo $specific_name; ?>_<?php echo $category->category_nicename; ?>"><?php echo $category->cat_name; ?></label><br />
			<?php endforeach; ?>
		</div>
	<?php
	}
}

add_action( 'init', 'related_posts_thumbnails' );

function related_posts_thumbnails() {
	global $related_posts_thumbnails;
	$related_posts_thumbnails = new RelatedPostsThumbnails();
}

function get_related_posts_thumbnails() {
	global $related_posts_thumbnails;
	echo $related_posts_thumbnails->get_html();
}

/**
 * Related Posts Widget, will be displayed on post page
 */
class RelatedPostsThumbnailsWidget extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = 'Related Posts Thumbnails' );
	}

	function widget( $args, $instance ) {
		if ( is_single() && ! is_page() ) { // display on post page only
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title; }
			get_related_posts_thumbnails();
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<?php
	}
} // class RelatedPostsThumbnailsWidget

add_action( 'widgets_init', create_function( '', 'return register_widget("RelatedPostsThumbnailsWidget");' ) );
?>
