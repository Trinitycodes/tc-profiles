<?php 

// remove the loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

// force full width layout
add_filter( 'genesis_pre_get_option_site_layout', 'child_do_template_layout' );

function child_do_template_layout( $opt ) {
    $opt = 'full-width-content'; // You can change this to any Genesis layout
    return $opt;
}

//* Add custom body class to the head
add_filter( 'body_class', 'tc_profiles_add_body_class' );
function tc_profiles_add_body_class( $classes ) {

   $classes[] = 'tc-profile-class';
   return $classes;
   
}

add_action( 'genesis_meta', 'tc_profiles_do_setup' );
function tc_profiles_do_setup() {

	add_action( 'genesis_after_header', 'tc_profile_header' );

	//* Enqueue scripts
	add_action( 'wp_enqueue_scripts', 'trailhead_enqueue_trailhead_script' );
	function trailhead_enqueue_trailhead_script() {

		wp_enqueue_style( 'tc-profiles-style' );

	}

}

function tc_profile_header() {

	$handle  = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';

		$opts = apply_filters( 'profile_images', array( 'archive' ) );

		$settings = array();

		foreach( $opts as $opt ){
			$settings[$opt]['image'] = preg_replace( '/^https?:/', '', get_option( $opt .'-tc-profile-image', sprintf( '%s/images/bg-%s.jpg', plugins_url( '..', __FILE__ ), $opt ) ) );
		}

		foreach ( $settings as $section => $value ) {

			$image = $value['image'] ? sprintf( '<img src="%s" class="archive-header-image" width="500" alt="' . _e( get_option( 'blog_name' ), 'tc-profiles' ) . '" />', $value['image'] ) : '';

		}

	?>
		<div class="profile-header">
			<div class="profile-image-section">
				<header class="entry-header">
					<h1 class="entry-title" itemprop="headline"><a href="<?php echo the_permalink(); ?>" rel="bookmark"><?php _e( 'Attorney Profiles', 'tc-profiles' ); ?></a></h1> 
					<h4><?php _e( 'Meet Our Team of Professionals', 'tc-profiles' ); ?></h4>
				</header>
			</div>
		</div>
		<div class="profile-image">
			<?php echo $image; ?>
		</div>
	<?php

}

// Display the after email registration form
add_action( 'genesis_loop', 'tc_do_profile_loop' );

function tc_do_profile_loop() {

	global $profile_object;
	$counter = 2;

	?>

		<div class="wr" style="height: 100%;">

			<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>

				<?php if( $counter == 2 ): ?>
					<?php $first = 'first'; ?>
					<?php $counter = 0; ?>
				<?php else: ?>
					<?php $first = ''; ?>
				<?php endif; ?>
				<article class="one-half <?php echo $first; ?> post-<?php echo get_the_ID(); ?> profile type-profile status-publish has-post-thumbnail profilecategory-attorneys entry" itemscope itemtype="http://schema.org/CreativeWork">
					<header class="entry-header">
						<h1 class="entry-title" itemprop="headline"><a href="<?php echo the_permalink(); ?>" rel="bookmark"><?php _e( the_title(), 'tc-profiles' ); ?></a></h1> 
						<p class="entry-meta"><a class="post-edit-link" href="http://smithlaw.dev/wp-admin/post.php?post=30&amp;action=edit">(Edit)</a></p>
					</header>
					<div class="entry-content" itemprop="text">
						<?php
							$post_thumbnail = has_post_thumbnail() ? the_post_thumbnail( array( '167', '250' ), array( 'class' => 'alignleft' ) ) : ''; 
						?>
						<a href="<?php echo the_permalink(); ?>" aria-hidden="true"><?php echo $post_thumbnail; ?></a>
						<?php _e( the_excerpt(), 'tc-profiles' ); ?>
					</div>
				</article>
				<?php $counter++; ?>
			<?php endwhile; else: ?>
				There are no Profiles to display.
			<?php endif; ?>

		</div>
	<?php

}

genesis();
?>