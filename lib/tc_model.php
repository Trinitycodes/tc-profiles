<?php

class TC_Model {

	/**
	 * Initialize everything you need in the __construct method
	 */
	public function __construct() {

		add_action( 'tc_execute_meta_boxes', array( $this, 'tc_setup_meta_boxes' ) );
		add_action( 'tc_execute_post_type_creator', array( $this, 'tc_create_post_types' ) );
		add_action( 'tc_execute_taxonomy_creator', array( $this, 'tc_create_taxonomies' ) );

	}

	/**
	 * This method is called from the the root class and executes any actions
	 * in the TC_Model Class
	 * @return none
	 */
	public function tc_run_model_actions() {

		do_action( 'tc_execute_meta_boxes' );
		do_action( 'tc_execute_post_type_creator' );
		do_action( 'tc_execute_taxonomy_creator' );

	}

	/**
	 * Create the tc-profiles post type
	 * @return none
	 */
	public function tc_create_post_types() {

		 /**
		   * Register a profiles post type, with REST API support
		   *
		   */
		    $labels = array(
		        'name'               => _x( 'Profiles', 'tc_profiles', 'tc-profiles' ),
		        'singular_name'      => _x( 'Profile', 'tc_profiles', 'tc-profiles' ),
		        'menu_name'          => _x( 'Profiles', 'admin menu', 'tc-profiles' ),
		        'name_admin_bar'     => _x( 'Profile', 'add new on admin bar', 'tc-profiles' ),
		        'add_new'            => _x( 'Add New', 'Profile', 'tc-profiles' ),
		        'add_new_item'       => __( 'Add New Profile', 'tc-profiles' ),
		        'new_item'           => __( 'New Profile', 'tc-profiles' ),
		        'edit_item'          => __( 'Edit Profile', 'tc-profiles' ),
		        'view_item'          => __( 'View Profile', 'tc-profiles' ),
		        'all_items'          => __( 'All Profiles', 'tc-profiles' ),
		        'search_items'       => __( 'Search Profiles', 'tc-profiles' ),
		        'parent_item_colon'  => __( 'Parent Profiles:', 'tc-profiles' ),
		        'not_found'          => __( 'No profiles found.', 'tc-profiles' ),
		        'not_found_in_trash' => __( 'No profiles found in Trash.', 'tc-profiles' )
		    );

		    $args = array(
		        'labels'             => $labels,
		        'description'        => __( 'Profiles of employees or team members.', 'tc-profiles' ),
		        'public'             => true,
		        'publicly_queryable' => true,
		        'show_ui'            => true,
		        'show_in_menu'       => true,
		        'query_var'          => true,
		        'rewrite'            => array( 'slug' => 'profiles' ),
		        'capability_type'    => 'post',
		        'has_archive'        => true,
		        'menu_icon'          => 'dashicons-businessman',
		        'hierarchical'       => false,
		        'menu_position'      => null,
		        'show_in_rest'       => true,
		        'rest_base'          => 'profiles-api',
		        'rest_controller_class' => 'WP_REST_Posts_Controller',
		        'supports'           => array( 'title', 'editor', 'thumbnail' )
		    );

		    register_post_type( 'tc_profiles', $args );

	}

	/**
	 * Create any taxonomies needed for this plugin
	 * @return none
	 */
	public function tc_create_taxonomies() {

	/**
	   * Register a profile-categories post type, with REST API support
	   *
	   * Based on example at: https://codex.wordpress.org/Function_Reference/register_taxonomy
	   */

	    $labels = array(
	        'name'              => _x( 'Profile Categories', 'profile-categories' ),
	        'singular_name'     => _x( 'Profile Category', 'profile-category' ),
	        'search_items'      => __( 'Search Profile Categories' ),
	        'all_items'         => __( 'All Profile Categories' ),
	        'parent_item'       => __( 'Parent Profile Category' ),
	        'parent_item_colon' => __( 'Parent Profile Category:' ),
	        'edit_item'         => __( 'Edit Profile Category' ),
	        'update_item'       => __( 'Update Profile Category' ),
	        'add_new_item'      => __( 'Add New Profile Category' ),
	        'new_item_name'     => __( 'New Profile Category Name' ),
	        'menu_name'         => __( 'Profile Category' ),
	    );

	    $args = array(
	        'hierarchical'      => true,
	        'labels'            => $labels,
	        'show_ui'           => true,
	        'show_admin_column' => true,
	        'query_var'         => true,
	        'rewrite'           => array( 'slug' => 'profile-categories' ),
	        'show_in_rest'       => true,
	        'rest_base'          => 'profile-category',
	        'rest_controller_class' => 'WP_REST_Terms_Controller',
	    );

	    register_taxonomy( 'profile-categories', array( 'tc_profiles' ), $args );

	}

	/**
	 * Setup up hooks that only run the add metaboxes 
	 * when a post is loaded in the admin site
	 * @return none
	 */
	public function tc_setup_meta_boxes() {

		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php', array( 'TC_Model', 'tc_add_meta_box_hooks' ) );
		add_action( 'load-post-new.php', array( 'TC_Model', 'tc_add_meta_box_hooks' ) );

	}

	/**
	 * Add the meta box creation methods to the add_meta_boxes
	 * hook
	 * @return none
	 */
	public function tc_add_meta_box_hooks() {

		add_action( 'add_meta_boxes', array( 'TC_Model', 'tc_add_meta_boxes' ) );
		add_action( 'save_post', array( 'TC_Model', 'tc_save_phone_number_meta' ), 10, 2 );
		add_action( 'save_post', array( 'TC_Model', 'tc_save_position_meta' ), 10, 2 );

	}

	/**
	 * This method is the method that actually adds
	 * the metaboxes to the post create and post edit
	 * screens.
	 * @return none
	 */
	public function tc_add_meta_boxes() {

		add_meta_box(
		    'tc-position',      // Unique ID
		    esc_html__( 'Position', 'tc-profiles' ),    // Title
		    'TC_Model::tc_position_meta_box',   // Callback function
		    'tc_profiles',         // Admin page (or post type)
		    'normal',         // Context
		    'default'         // Priority
		  );

		add_meta_box(
		    'tc-phone-number',      // Unique ID
		    esc_html__( 'Phone Number', 'tc-profiles' ),    // Title
		    'TC_Model::tc_phone_number_meta_box',   // Callback function
		    'tc_profiles',         // Admin page (or post type)
		    'normal',         // Context
		    'default'         // Priority
		  );

	}

	/**
	 * Display the position meta box in tc_profiles edit page.
	 * @return none
	 */
	public function tc_position_meta_box( $object, $box ) { 
		?>
		 	<?php wp_nonce_field( basename( __FILE__ ), 'tc_position_nonce' ); ?>

		 	<p>
				<label for="tc-position"><?php _e( "The position of this employee at your company.  Example: Owner / Attorney.", 'tc-profiles' ); ?></label>
		    	<br />
		    	<input class="widefat" type="text" name="tc-position" id="tc-position" value="<?php echo esc_attr( get_post_meta( $object->ID, 'tc_position', true ) ); ?>" size="30" />
		  	</p>

		<?php
	}

	/**
	 * Display the phone number meta box in tc_profiles edit page.
	 * @return none
	 */
	public function tc_phone_number_meta_box( $object, $box ) { 
		?>
		 	<?php wp_nonce_field( basename( __FILE__ ), 'tc_phone_number_nonce' ); ?>
		 	
		 	<p>
				<label for="tc-phone-number"><?php _e( "The phone number for contacting this employee directly (This number will be shown to the public).", 'tc-profiles' ); ?></label>
		    	<br />
		    	<input class="widefat" type="text" name="tc-phone-number" id="tc-phone-number" value="<?php echo esc_attr( get_post_meta( $object->ID, 'tc_phone_number', true ) ); ?>" size="30" />
		  	</p>

		<?php
	}

	public function tc_save_position_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['tc_position_nonce'] ) || !wp_verify_nonce( $_POST['tc_position_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value = ( isset( $_POST['tc-position'] ) ? sanitize_text_field( $_POST['tc-position'] ) : '' );

		/* Get the meta key. */
		$meta_key = 'tc_position';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

	}

	public function tc_save_phone_number_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['tc_phone_number_nonce'] ) || !wp_verify_nonce( $_POST['tc_phone_number_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value = ( isset( $_POST['tc-phone-number'] ) ? sanitize_text_field( $_POST['tc-phone-number'] ) : '' );

		/* Get the meta key. */
		$meta_key = 'tc_phone_number';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

	}

}