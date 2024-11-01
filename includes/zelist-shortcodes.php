<?php

add_shortcode('directory_submit', 'zelist_directory_submit');
function zelist_directory_submit($attrs) {
	$defaults = array();

	$args = shortcode_atts($defaults, $attrs, 'zelist');
	$args = apply_filters('zelist_directory_submit_shortcode_args', $args, $attrs);
	zelist_submit_form($args);
}

add_shortcode ('directory', 'zelist_directory');
function zelist_directory($attrs) {
	$defaults = array(
		'taxonomy' 		=> 'listing_category',
		'show_count' 	=> true,
		'title_li' 		=> false,
		'hide_empty' 	=> true,
		'hide_title_if_empty' => '%%NO%%',
		'depth' 		=> 1,
		'before'		=> '<ul id="directory">',
		'after'			=> '</ul>',
		'selected'		=> '',
		);
	$args = shortcode_atts($defaults, $attrs, 'zelist');
	$args = apply_filters('zelist_directory_shortcode_args', $args, $attrs);

	if(current_user_can('activate_plugins')) {
		//$categories = get_categories($args);		plouf($categories, " categories");
	}
	echo $args['before'];
	wp_list_categories($args);
	echo $args['after'];
}

add_shortcode ('directory_recently_published', 'zelist_directory_recently_published');
function zelist_directory_recently_published($attrs) {
	global $exclude_ids;
	if(!is_array($exclude_ids))
		$exclude_ids = array();

	$defaults = array(
		'post_type' => 'listing',
		'post_parent' => 0,
		'count' => 8,
		'order' => 'DESC',
		'orderby' => 'post_date',
		'post__not_in' => $exclude_ids
		);
	$args = shortcode_atts($defaults, $attrs, 'zelist');
	$args['posts_per_page'] = $args['count'];
	unset($args['count']);

	global $exclude_ids;
	$args['post__not_in'] = $exclude_ids;

	$args = apply_filters('zelist_directory_recently_published_shortcode_args', $args, $attrs);
	$query = new WP_Query( $args );
		//The Query
	//echo " file = " . get_template_directory() . '/loop-listing.php';

	$possible_files = array(
		get_theme_file_path() . '/loop-listing.php',
		get_theme_file_path() . '/template-parts/loop-listing.php',
		get_theme_file_path() . '/listing-items/loop-listing.php',
		get_template_directory() . '/loop-listing.php',
		ZELIST_PATH .'/templates/loop-listing.php'
	);
	//print_r($possible_files);

	$template_file = false;
	foreach($possible_files as $file) {
		if(file_exists($file)) {
			$template_file = $file;
			break;
		}
	}

	//plouf($possible_files , " TEMPLATE = $template_file");

	//echo " file = $template_file";
	//The Loop
	if ( $query->have_posts() ) :
?>
	<div id="zelist_directory_recently_published" class="listings_list">
	<header><h3><?php _e('Recently published', 'zelist') ;?></h3></header>

	<?php
	$item_number = 0;
	echo apply_filters('zelist_directory_recently_published_before_content', '<div class="content">');
	do_action('zelist_directory_recently_published');
	while ( $query->have_posts() ) :
		do_action('zelist_directory_recently_published-item-' . $item_number);
	 	$item_number++;
		$exclude_ids[] = get_the_ID();
		$query->the_post();
		echo "<!-- template $template_file -->";
		include($template_file);
	endwhile; else:

	echo apply_filters('zelist_directory_recently_published_after_content', '</div>');
	?>
	</div>
	<?php
		endif;

	//Reset Query
	wp_reset_postdata();
}

add_shortcode ('directory_last_updates', 'zelist_directory_last_updates');
function zelist_directory_last_updates($attrs) {
	global $exclude_ids;
	if(!is_array($exclude_ids))
		$exclude_ids = array();
	// wp_get_recent_posts
		$defaults = array(
		'post_type' 	=> 'listing',
		'post_parent' 	=> 0,
		'count' 		=> 8,
		'order' 		=> 'DESC',
		'orderby' 		=> 'post_modified',
		'post__not_in' 	=> $exclude_ids
		);

	$args = shortcode_atts($defaults, $attrs, 'zelist');
	$args['posts_per_page'] = $args['count'];
	unset($args['count']);
	$args = apply_filters('zelist_directory_last_updates_shortcode_args', $args, $attrs);

	//plouf($args);

	$query = new WP_Query( $args );
	$possible_files = array(
		get_theme_file_path() . '/loop-listing.php',
		get_template_directory() . '/loop-listing.php',
		ZELIST_PATH .'/templates/loop-listing.php'
	);
	$template_file = false;
	foreach($possible_files as $file) {
		if(file_exists($file)) {
			$template_file = $file;
			break;
		}
	}
	if ( $query->have_posts() ) :
	?>

	<div id="zelist_directory_last_updates" class="listings_list">
	<header><h3><?php _e('Recently updated', 'zelist') ;?></h3></header>
	<?php
	echo apply_filters( 'zelist_directory_last_updates_before_content', '<div class="content">');
	do_action('zelist_directory_last_updates_before_content');
	$item_number = 0;
	 while ( $query->have_posts() ) : $query->the_post();
	 	do_action('zelist_directory_last_updates-item-' . $item_number);
	 	$item_number++;
		$exclude_ids[] = get_the_ID();
		include($template_file);
	endwhile; else:
	echo apply_filters( 'zelist_directory_last_updates_after_content', '</div>');
	?>
	</div>
	<?php

	endif;
	//Reset Query
	wp_reset_postdata();
}

