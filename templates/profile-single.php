<?php 

// remove the loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

//* Add custom body class to the head
add_filter( 'body_class', 'tc_profiles_add_body_class' );
function tc_profiles_add_body_class( $classes ) {

   $classes[] = 'tc-profile-class';
   return $classes;
   
}

// force full width layout
add_filter( 'genesis_pre_get_option_site_layout', 'child_do_template_layout' );

function child_do_template_layout( $opt ) {
    $opt = 'full-width-content'; // You can change this to any Genesis layout
    return $opt;
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

	?>
		<div class="profile-header">
			<div class="profile-image-section">
				<?php $position = get_post_meta( get_the_ID(), 'tc_position', true ); ?>
				<header class="entry-header">
					<h1 class="entry-title" itemprop="headline"><a href="<?php echo the_permalink(); ?>" rel="bookmark"><?php _e( the_title(), 'tc-profiles' ); ?></a></h1> 
					<h4><?php _e( $position, 'tc-profiles' ); ?></h4>
				</header>
			</div>
		</div>
		<div class="profile-image">
			<?php echo $post_thumbnail = has_post_thumbnail( get_the_ID() ) ? get_the_post_thumbnail( get_the_ID(), 'attorney_profile', array( 'class' => 'alignnone' ) ) : ''; ?>
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

				<article class="post-<?php echo get_the_ID(); ?> tc_profiles type-tc_profiles status-publish has-post-thumbnail profile_category-attorneys entry" itemscope itemtype="http://schema.org/CreativeWork">
					<div class="entry-content" itemprop="text">
						<?php echo $post_thumbnail = has_post_thumbnail( get_the_ID() ) ? get_the_post_thumbnail( get_the_ID(), array( 167, 250 ), array( 'class' => 'alignleft profile-image-small' ) ) : ''; ?>
						<?php _e( the_content(), 'tc-profiles' ); ?>
						<p class="entry-meta"><a class="post-edit-link" href="http://smithlaw.dev/wp-admin/post.php?post=30&amp;action=edit">(Edit)</a></p>
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