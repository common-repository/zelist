<?php

add_action( 'init', 'zelist_register_cpt', 1);
function zelist_register_cpt() {
	zelist_register_taxonomy_listing_category();
	zelist_register_taxonomy_listing_tag();
	zelist_register_post_type_listing();
	zelist_register_listing_statuses();

	add_action('post_submitbox_start', 'zelist_add_post_statuses');
}

function zelist_register_taxonomy_listing_category() {
	$args = array(
		'labels' => array(
			'name'			=> __('Listing Categories', 'zelist'),
			'singular_name' => __('Listing category', 'zelist'),
			'all_items' 	=> __('All categories', 'zelist'),
			),
		'hierarchical' => true,
		'update_count_callback' => 'zelist_update_listing_category_count_callback',
		'query_var' => 'listing_category',
		'show_ui' => true,
		);

	if(zeListConfiguration::getSlugListingCategory()) {
		$args['rewrite'] = array(
			'slug'	=> zeListConfiguration::getSlugListingCategory(),
			'with_front' => false
		);
	}
	else {
		$args['rewrite'] = false;
	}

	$args = apply_filters('zelist_register_taxonomy_listing_category',		$args);

	//$args['rewrite'] = true;
	register_taxonomy(
		'listing_category',
		'listing',
		$args
	);
}
add_action( 'template_redirect', 'zelist_theme_filter_404', 0 );
function zelist_theme_filter_404() {
 global $wp_query;
	if(!$wp_query->is_404)
		return;

	return;
 global $wp_rewrite; plouf($wp_rewrite); plouf($wp_query);
}

function zelist_register_taxonomy_listing_tag() {
	$args = array(
		'label' => __('Listing Tags', 'zelist'),
		'update_count_callback' => 'zelist_update_listing_tag_count_callback',
		'query_var' => 'listing_tag',
		'show_ui' => true,
		);

	if(zeListConfiguration::getSlugListingTag()) {
		$args['rewrite'] = array(
			'slug'	=> zeListConfiguration::getSlugListingTag(),
			'with_front'	=> false
		);

		//global $wp_rewrite;		$wp_rewrite->add_rewrite_tag("%" . zeListConfiguration::getSlugListingTag() ."%", '([^/]+)', "listing=");
	}
	else {
		$args['rewrite'] = false;
	}

	$args = apply_filters( 'zelist_register_taxonomy_listing_tag', $args);

	register_taxonomy(
		'listing_tag',
		'listing',
		$args
	);
}

function zelist_register_post_type_listing() {
	$args = array(
		'labels' => array(
			'name' 			=> __( 'Listings', 'zelist'),
			'singular_name' => __( 'Listing', 'zelist'),
			'add_new_item'	=> __('Add new listing', 'zelist'),
			'edit_item'		=> __('Edit listing', 'zelist'),
			'new_item' 		=> __('New listing', 'zelist'),
			'view_item'		=> __('View listing', 'zelist'),
			'view_items'	=> __('View listings', 'zelist'),
			'search_items'	=> __('Search listings', 'zelist'),
			'not_found'		=> __('No listing found', 'zelist'),
			'not_found_in_trash'	=> __('No listing found in trash', 'zelist'),
			'all_items'				=> __('All listings', 'zelist'),
			'archives'				=> __('Listing archives', 'zelist'),

/*
'attributes' - Label for the attributes meta box. Default is 'Post Attributes' / 'Page Attributes'.
'insert_into_item' - String for the media frame button. Default is Insert into post/Insert into page.
'uploaded_to_this_item' - String for the media frame filter. Default is Uploaded to this post/Uploaded to this page.
*/

		),
		'public' 			=> true,
		'capability_type' 	=> 'post',
		'map_meta_cap' 		=> true,
		//'has_archive'		=> true,
		'hierarchical' 		=> true,
		'taxonomies' 		=> array('listing_category', 'listing_tag'),
//		'query_var' 		=> false,
		'exclude_from_search'	=> false,
		'can_export' 		=> true,
		'delete_with_user' 	=> false,
		'supports' 			=> array( 'title', 'publicize', 'editor', 'thumbnail', 'page-attributes', 'excerpt', 'author', 'custom-fields', 'revisions' ),
	);

	if(zeListConfiguration::getSlugListing()) {
		$args['rewrite'] = array(
			'slug' 	=> zeListConfiguration::getSlugListing(),
			//'with_front'	=> false
		);
	}
	else {
		$args['rewrite'] = false;
	}

	$args = apply_filters('zelist_register_post_type_listing', $args);
	register_post_type( 'listing', $args );
	//flush_rewrite_rules();
}

function zelist_register_listing_statuses() {
	$args = apply_filters('zelist_register_post_status_dead', array(
			'label' => _x( 'Dead', 'zelist'),
			'label_count' => _n_noop( 'Dead <span class="count">(%s)</span>', 'Dead <span class="count">(%s)</span>' ),
			'public' => false,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
		) );
	register_post_status( 'dead', $args);

	$args = apply_filters('zelist_register_post_status_deny', array(
			'label' => _x( 'Denied', 'zelist'),
			'label_count' => _n_noop( 'Denied <span class="count">(%s)</span>', 'Denied <span class="count">(%s)</span>' ),
			'public' => false,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
		));
	register_post_status( 'deny', $args );
}

// allow future/pending listings to be parent. So cute.
add_filter('page_attributes_dropdown_pages_args', 'zelist_sub_listing_page_attributes_dropdown_pages_args');
add_filter('quick_edit_dropdown_pages_args', 'zelist_sub_listing_page_attributes_dropdown_pages_args');
function zelist_sub_listing_page_attributes_dropdown_pages_args($args, $post = false) {
	if(!get_post_type() == 'listing')
		return $args;

	$args['post_type'] = 'listing';
	$args['post_status'] = 'draft, pending, future, publish';

	return $args;
}
/*
add_filter('generate_rewrite_rules', 'taxonomy_slug_rewrite');
function taxonomy_slug_rewrite($wp_rewrite) {
 $rules = array();
 // get all custom taxonomies
 $taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
 // get all custom post types
 $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

 foreach ($post_types as $post_type) {
 foreach ($taxonomies as $taxonomy) {
 // go through all post types which this taxonomy is assigned to
 foreach ($taxonomy->object_type as $object_type) {
 // check if taxonomy is registered for this custom type
 if ($object_type == $post_type->rewrite['slug']) {
 // get category objects
 $terms = get_categories(array('type' => $object_type, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0));

 // make rules
 foreach ($terms as $term) {
 $rules[$object_type . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
 }
 }
 }
 }
 }
 // merge with global rules
 $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
*/

function zelist_update_listing_tag_count_callback($terms, $taxonomy) {
 global $wpdb;
 foreach ( (array) $terms as $term) {
 do_action( 'edit_term_taxonomy', $term, $taxonomy );

 // Do stuff to get your count
 $count = $wpdb->get_var("SELECT COUNT(*) as count FROM $wpdb->term_relationships WHERE term_taxonomy_id = $term");

 $wpdb->update( $wpdb->term_taxonomy, array( 'count' => $count ), array( 'term_taxonomy_id' => $term ) );
 do_action( 'edited_term_taxonomy', $term, $taxonomy );
 }
}

function zelist_update_listing_category_count_callback($terms, $taxonomy) {
 global $wpdb;
 foreach ( (array) $terms as $term) {
 do_action( 'edit_term_taxonomy', $term, $taxonomy );

 // Do stuff to get your count
 $count = $wpdb->get_var("SELECT COUNT(*) as count FROM $wpdb->term_relationships WHERE term_taxonomy_id = $term");
 $wpdb->update( $wpdb->term_taxonomy, array( 'count' => $count ), array( 'term_taxonomy_id' => $term ) );
 do_action( 'edited_term_taxonomy', $term, $taxonomy );
 }
}
