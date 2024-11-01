<?php
// add a link to filter uncategorized listings

add_filter('views_edit-listing', 'zelist_admin_subsubsub_no_category');
function zelist_admin_subsubsub_no_category($views) {
	global $wp_query;

	$linksWithoutCategory = new WP_Query(array(
	 'post_type' 	=> 'listing',
	 'tax_query' 	=> array(
	 	array(
	 		'taxonomy' => 'listing_category',
	 		'operator' => 'NOT EXISTS'
	 	)
	 )
	));

 $class = ($wp_query->query_vars['cat'] == '-1') ? ' class="current"' : '';
	$views['unlinked'] =
		sprintf(
			'<a href="%s" %s>',
			admin_url('edit.php?post_type=listing&cat=-1'),
			$class
		)
		. __('Uncategorized', 'zelist')
		. sprintf(' <span class="count">(%d)</span></a>', $linksWithoutCategory->found_posts)
	;
 	return $views;
}

add_filter('parse_query', 'zelist_admin_subsubsub_filter_no_category');
function zelist_admin_subsubsub_filter_no_category($query) {
	if(!is_admin()) {
		return $query;
	}

	if(!isset($query->query_vars['cat']) || $query->query_vars['cat'] != '-1') {
		return $query;
	}

	$tax_query = array(
		 array(
			 'taxonomy' => 'listing_category',
			 'operator' => 'NOT EXISTS'
		 )
	 );
	$query->set('tax_query', $tax_query);

	//$query->query_vars['cat'] = null;

//	plouf($query);

	return $query;
}
