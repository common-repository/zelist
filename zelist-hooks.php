<?php

// add category nicenames in body and post class
add_filter( 'post_class', 'zelist_featured_class' );
function zelist_featured_class( $classes ) {
	global $post;
	$is_featured = get_post_meta($post->ID, 'featured', 1);
	if($is_featured && $is_featured == 1) {
		$classes[] = 'featured_listing';
	}
	return $classes;
}

//add_filter( 'single_template', 'zelist_get_custom_post_type_template' );
function zelist_get_custom_post_type_template($single_template) {
 global $post;

 if ($post->post_type == 'listing') {
 $single_template = ZELIST_PATH . '/templates/single-listing.php';
 }
 return $single_template;
}

add_action( 'pre_get_posts', 'zelist_action_pre_get_posts');
function zelist_action_pre_get_posts($query) {
	if(is_admin()) return;

	if(isset($query->query_vars['listing_category']) && strlen($query->query_vars['listing_category']) > 0) {
		//$query->query_vars['post_parent'] = 0;
		//$query->query['post_parent'] = 0;
		//$query->set( 'orderby', array('menu_order' => 'ASC', 'post_title' => 'ASC' ));
	}

	if($query->is_main_query() && get_query_var( 'post_type') === 'listing') {
	//	$query->is_page = false;
		//$query->is_single = true;
		if( zeListConfiguration::getDefaultSortOrder() ) {
			$query->set( 'orderby', zeListConfiguration::getDefaultSortOrder() );
			$query->set( 'order', zeListConfiguration::getDefaultSortOrderBy());
			
		}
		else {
			$query->set( 'orderby', 'post_modified');
			$query->set( 'order', 'DESC' );
		}
	 	
	}

	if(
		$query->is_tax &&
		($query->get('listing_category') || $query->get('listing_tag'))
	) {
		$query->set('post_type', 'listing');
	}

	// Returns featured listing on top of query
	$featured_field_meta_key = zeListConfiguration::getFeaturedFieldMetaKey();
	if($featured_field_meta_key && $query->is_tax && $query->get('listing_category') ) {
		$query->set( 'meta_query', array(
			'relation'	=> 'OR',
			 array(
				'key' => $featured_field_meta_key,
				'value'	=> '1'
				),
				array(
				'key' => $featured_field_meta_key,
				'value'	=> '0'
				),
				array(
				'key' => $featured_field_meta_key,
				'value'	=> 'NOT EXISTS'
				)
			 ) 
		);
		 //sort by a meta value
		//$query->set( 'orderby', 'meta_value, post_modified' );
		//$query->set( 'order', 'DESC' );

		$orderby = array();
		$orderby['meta_value'] = 'DESC';
		if( zeListConfiguration::getDefaultSortOrder() ) {
			$orderby[zeListConfiguration::getDefaultSortOrder()] = zeListConfiguration::getDefaultSortOrderBy();
		}
		else {
			$orderby['post_modified'] = 'DESC';
		}
		$query->set( 'orderby', $orderby );


	}

}
