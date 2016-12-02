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
function knoppys_shortcode() {

	$ajax_nonce = wp_create_nonce( "my-special-string" );
	?>

	<!-- Simple form to post some data -->
	<label>Number of posts to retrieve.</label><br>
	<input type="text" name="noofposts" id="noofposts">
	<input type="hidden" name="nonce" id="nonce" value="<?php echo $ajax_nonce; ?>">
	<button type="button" class="button" id="fetch">Fetch</button>

	<!-- An empty div for adding data returned. -->
	<div id="result"></div>

<?php }

add_shortcode( 'knoppys', 'knoppys_shortcode' );

add_action( 'wp_ajax_my_action', 'my_action_function' );
function my_action_function() {
	check_ajax_referer( 'my-special-string', 'security' );
	echo sanitize_text_field( $_POST['my_string'] );
	wp_die();
}

/***************************
 * Carry out the function with the data just sent from the javascript
 ****************************/

function knoppy_ajax_implement_ajax_getposts() {

	/**
	 * Check to make sure the request is from this site
	 * This will automatically die the script if it fails
	 *
	 * @see https://codex.wordpress.org/Function_Reference/check_ajax_referer
	 */
	check_ajax_referer( 'my-special-string', 'security' );

	// Force a limit so you don't crash MySQL
	$limit     = 5;
	$noofposts = ( isset( $_POST['noofposts'] ) && is_integer( (int) $_POST['noofposts'] ) ) ? (int) $_POST['noofposts'] : false;

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

		// We have posts send out array in jSON
		if ( sizeof( $data ) ) {
			echo json_encode( $data );
		} else {
			// No posts send a nice responce
			$data[0]['title']   = 'Sorry!';
			$data[0]['excerpt'] = 'No posts were found.';
			echo json_encode( $data );
		}
	} else {
		// Something was not right
		die();
	}

	die();
}

add_action( 'wp_ajax_getposts', 'knoppy_ajax_implement_ajax_getposts' );
add_action( 'wp_ajax_nopriv_getposts', 'knoppy_ajax_implement_ajax_getposts' );


/**
 * Adds the WordPress Ajax Library to the frontend.
 *
 * The problem with your method in the js is that its a fixed location
 * If someone uses your plugin and relocates WP directories your plugin breaks
 * Not a problem with this method
 */
function knoppy_ajax_add_ajax_library() {

	$html = '<script type="text/javascript">';
	$html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
	$html .= '</script>';

	echo $html;

}

add_action( 'wp_head', 'knoppy_ajax_add_ajax_library' );