<?php
/*
Plugin Name:       Knoppys Plugin Boilerplate
Plugin URI:        https://github.com/knoppys/
Description:       Demo plugin for using Ajax in WordPress Posts, Pages, Plugins etc. 
Version:           1
Author:            Knoppys Digital Limited
License:           GNU General Public License v2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/knoppys/
GitHub Branch:     master
*/

define( 'PLUGIN_VERSION', '1' );
define( 'PLUGIN__MINIMUM_WP_VERSION', '1.0' );
define( 'PLUGIN__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/***************************
 *Load Native & Custom wordpress functionality plugin files.
 ****************************/

/***************************
 * Load Scripts
 ****************************/
function knoppy_ajax_plugin_scripts() {
	wp_enqueue_script( 'knoppy-ajax-core', plugin_dir_url( __FILE__ ) . 'core.js', array( 'jquery' ), '1.0.0', true );
	wp_localize_script( 'knoppy-ajax-core', 'siteUrlobject', array( 'siteUrl' => get_bloginfo( 'url' ) ) );
}

add_action( 'wp_enqueue_scripts', 'knoppy_ajax_plugin_scripts' );

/***************************
 * Create the shortcode we'll use for the demo
 ****************************/
function knoppys_shortcode() { ?>

	<!-- Simple form to post some data -->
	<label>Number of posts to retrieve.</label><br>
	<input type="text" name="noofposts" id="noofposts">
	<button type="button" class="button" id="fetch">Fetch</button>

	<!-- An empty div for adding data returned. -->
	<div id="result"></div>

<?php }

add_shortcode( 'knoppys', 'knoppys_shortcode' );

/***************************
 * Carry out the function with the data just sent from the javascript
 ****************************/

function knoppy_ajax_implement_ajax_getposts() {

	// Force a limit so you don't crash MySQL
	$limit     = 5;
	$noofposts = ( isset( $_POST['noofposts'] ) && is_integer( $_POST['noofposts'] ) ) ? $_POST['noofposts'] : false;

	if ( $noofposts && $noofposts <= $limit ) {

		// WP_Query arguments
		$args = array(
			'post_type'      => array( 'post' ),
			'posts_per_page' => ( $noofposts ),
		);

		// The Query
		$query = new WP_Query( $args );
		$data  = array();

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id                     = get_the_ID();
				$data[ $post_id ]['title']   = get_the_title();
				$data[ $post_id ]['excerpt'] = get_the_excerpt();
			}
		}

		wp_reset_postdata();

		if ( sizeof( $data ) ) {
			echo json_encode( $data );
		} else {
			echo 'No posts found';
		}
	}

	die();
}

add_action( 'wp_ajax_getposts', 'knoppy_ajax_implement_ajax_getposts' );
add_action( 'wp_ajax_nopriv_getposts', 'knoppy_ajax_implement_ajax_getposts' );

