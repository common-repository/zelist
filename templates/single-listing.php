<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="entry-content">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				include(ZELIST_PATH . '/templates/template-parts/listing/content-listing.php');

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

				the_post_navigation( array(
					'prev_text' =>
						'<span class="screen-reader-text">'
						. __( 'Previous listing', 'zelist')
						. '</span><span aria-hidden="true" class="nav-subtitle">'
						. __( 'Previous listing', 'zelist')
						. '</span>'
						.' <span class="nav-title">%title</span>',
					'next_text' =>
						'<span class="screen-reader-text">'
						. __( 'Next listing', 'zelist')
						. '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next listing', 'zelist') . '</span> <span class="nav-title">%title</span>',
				) );

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
