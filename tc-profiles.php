<?php
/**
 *
 * @link              http://trinitycodes.com
 * @since             1.0.0
 * @package           Trinity Codes Profiles
 *
 * @wordpress-plugin
 * Plugin Name:       Trinity Codes Profiles
 * Plugin URI:        http://trinitycodes.com/
 * Description:       Creates and displays a custom profiles section in the website.  This section is used for displaying employees.
 * Version:           1.0.0
 * Author:            Trinity Codes
 * Author URI:        http://trinitycodes.com/about-trinity-codes-business-websites/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tc-profiles
 * Domain Path:       /languages
 */

require_once dirname(__FILE__) . '/lib/customizer.php';
require_once dirname(__FILE__) . '/lib/tc_model.php';

class TC_Profiles {

	private $tc_model;

	/**
	 * Initialize the plugin and components
	 */
	function __construct() {

		add_filter( 'template_redirect', array( $this, 'front_controller' ) );
		add_filter( 'excerpt_length', array( $this, 'tc_custom_excerpt_length' ), 999 );
		add_filter( 'excerpt_more', array( $this, 'tc_excerpt_more' ) );
		add_action( 'init', array( $this, 'tc_initialize' ) );

		/* Output custom css */
		add_action( 'wp_enqueue_scripts', array( $this, 'tc_profile_css' ) );

		$this->tc_model = new TC_Model();

	}

	/**
	 * Controls the inclusion of templates
	 * @return [type] [description]
	 */
	public function front_controller() {

		if( is_post_type_archive( 'tc_profiles' ) ) {
			// Load the archive template
			global $profile_object;

			// get the profiles
			$args = array(
					'orderby' => 'title DESC'
				);
			$profile_object = new WP_Query( $args );

			include dirname(__FILE__) . '/templates/profile-archive.php';
			exit;

		}

		if( is_singular( 'tc_profiles' ) ) {

			// load the single profile template
			include dirname(__FILE__) . '/templates/profile-single.php';
			exit;

		}

	}

	/**
	 * Change the excerpt length to 20 words.
	 * @param  [type] $length [description]
	 * @return [type]         [description]
	 */
	public function tc_custom_excerpt_length( $length ) {
		return 20;
	}

	/**
	 * Set the more link at the end of each profile.
	 * @param  [type] $more [description]
	 * @return [type]       [description]
	 */
	public function tc_excerpt_more( $more ) {
		return '<div><a class="button" href="' . get_the_permalink( get_the_ID() ) . '"> Read More...</a></div>';
	}

	/**
	 * Run initialization of plugin
	 * @return none
	 */
	public function tc_initialize() {

		// register scripts and styles
		wp_register_style( 'tc-profiles-style', plugins_url( 'css/style.css', __FILE__ ) );

		// Run the model actions in TC_Model
		$this->tc_model->tc_run_model_actions();

	}

	/**
	 * Create and print out custom css for background images and customizer.
	 * @return none
	 */
	public function tc_profile_css() {

		$handle  = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';

		$opts = apply_filters( 'profile_images', array( '1' ) );

		$settings = array();

		foreach( $opts as $opt ){
			$settings[$opt]['image'] = preg_replace( '/^https?:/', '', get_option( $opt .'-tc-profile-image', sprintf( '%s/images/bg-%s.jpg', plugins_url( '', __FILE__ ), $opt ) ) );
		}

		$css = '';

		foreach ( $settings as $section => $value ) {

			$background = $value['image'] ? sprintf( 'background-image: url(%s);', $value['image'] ) : '';
			$image = $value['image'] ? sprintf( '<img src="%s" alt="Profile Header Background Image" />', $value['image'] ) : '';

			// for member bulletin page template
			if( is_singular( 'tc_profiles' ) || is_post_type_archive( 'tc_profiles' ) ) {

				$css .= ( ! empty( $section ) && ! empty( $background ) ) ? sprintf( '.profile-header { %s }', $background ) : '';

			}

		}

		if( $css ){
			wp_add_inline_style( $handle, $css );
		}

	}
}
$profiles = new TC_Profiles();