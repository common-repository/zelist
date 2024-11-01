<?php

function zelist_link_children($args = '') {
	global $post;

	$defaults = array();
	$args = wp_parse_args($args, $defaults);

	$children = get_children('post_status=publish&post_parent=' . $post->ID);
	if($children) {
		echo '
		<ul class="link_children">';

		foreach($children as $child) {
			$link_url = get_post_meta($child->ID, 'link_url', true);
			echo '
			<li>' . $child->post_title . ' : <a href="' . $link_url .'" target="_blank">' . $link_url .'</a></li>';
		}
		echo '
		</ul>';
	}
}

/*
// Prevent children links to appear on category pages
add_action( 'pre_get_posts', 'zelist_filter_pre_get_posts' );
function zelist_filter_pre_get_posts($query) {
	//if(current_user_can('edit_posts')) 		plouf($query);

	if(is_admin()) return;

	if(isset($query->query_vars['zelist_category']) && strlen($query->query_vars['zelist_category']) > 0) {
		$query->query_vars['post_parent'] = 0;
		$query->query['post_parent'] = 0;
		$query->set( 'orderby', array('menu_order' => 'ASC', 'post_title' => 'ASC' ));
	}

	if($query->is_main_query() && get_query_var( 'post_type') === 'zelist_link') {
		$query->is_page = false;
		$query->is_single = true;
	}

	//if(current_user_can( 'manage_posts' )) print_r($query);

	//if(!isset($query->query_vars['']))
 return $query;
}

*/