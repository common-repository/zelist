<?php
	add_action('manage_listing_posts_columns', 'zelist_add_custom_columns_to_listings_list');
function zelist_add_custom_columns_to_listings_list( $posts_columns ) {
	 $extra_columns = zeListConfiguration::fieldsToShowInAdminList();
	 $columns_to_add = array();

	 //$columns_to_add['listing_status'] = __('Listing status', 'zelist');

	 if($extra_columns) foreach($extra_columns as $extra_column) {
		 if($extra_column == 'listing_category') {
			$columns_to_add['listing_category'] = __('Category', 'zelist');
		 }
		 elseif($extra_column == 'listing_tag') {
			$columns_to_add['listing_tag'] = __('Listing Tags', 'zelist');
		 }
		 elseif($extra_column == 'thumbnail') {
				$columns_to_add['thumbnail'] = __('Thumbnail');
		 }
		 elseif(substr($extra_column, 0, 4) == 'acf:') {
			 $acf_field_key = substr($extra_column, 4);
			 $acf_field = acf_get_field($acf_field_key);
			 //plouf($acf_field, " KEY = $acf_field_key");
			 $columns_to_add[$extra_column] = $acf_field['label'];
		 }
	 }

	//plouf($columns_to_add);

	foreach($posts_columns as $key => $posts_column) {
		if($key == 'author') {
			foreach($columns_to_add as $add_key => $add_label) {
			   $new_posts_columns[$add_key] = $add_label;
			}
	// $new_posts_columns['featured'] = __('Thumbnail', 'zelist');
		}

		$new_posts_columns[$key] = $posts_column;
	}
	$new_posts_columns['updated'] = __('Updated', 'zelist');
	unset($new_posts_columns['date']);

	return $new_posts_columns;
}

add_action('manage_pages_custom_column', 'show_zelist_columns', 10, 2);
function show_zelist_columns( $column_id, $post_id ) {
	global $typenow;

	if ($typenow=='listing') {
	   switch ($column_id) {
		   case 'listing_category':
				$terms = get_the_terms($post_id, 'listing_category');
				$categories = array();
				if (is_array($terms)) {
					foreach($terms as $key => $term) {
						$edit_link = get_term_link($term, 'listing_category');
						$categories[$key] = '<a href="'.$edit_link.'">' . $term->name . '</a>';
					}
					//echo implode("<br/>", $businesses);
					echo implode(' | ', $categories);
				}
			break;

		   case 'thumbnail' :
 			  echo get_the_post_thumbnail($post_id, 'thumbnail');
			break;

		   case 'listing_tag' :
					$terms = get_the_terms($post_id, 'listing_tag');
				$tags = array();
				if (is_array($terms)) {
					foreach($terms as $key => $term) {
						$edit_link = get_term_link($term, 'listing_category');
						$tags[$key] = '<a href="'.$edit_link.'">' . $term->name . '</a>';
					}
					//echo implode("<br/>", $businesses);
					echo implode(', ', $tags);
				}

			break;
	/*
	case 'listing_status' :
 		$metas = array();

 		$post = get_post($post_id);

	//echo " stautrs = " . get_post_status();

	//plouf(get_post_status_object(get_post_status()), " object");
	$post_status_object = get_post_status_object(get_post_status());
	echo $post_status_object->label;

 		foreach(array('link_http_last_check', 'link_http_response_code', 'link_new_location') as $key) {
 			$metas[$key] = get_post_meta($post_id, $key, 1);
 			if(is_array(	$metas[$key] ))
 				$metas[$key] = $metas[$key][count($metas[$key]) -1];
 		}

 		$days_since_last_check = (time() - strtotime($metas['link_http_last_check'])) / (24*3600);
	echo '<br />';

	///$link_url = zelist_acf_get_field($post_id, 'site_web');
	$link_url = get_post_meta($post_id, 'site_web', true);
	if($link_url && !empty($link_url)) {
	printf(__('URL: <a href="%1$s" target="_blank" rel="noopener">%1$s</a>', 'zelist'), $link_url);
	}

 		//echo "<br />TIME " . time() . " - " . strtotime($metas['link_http_last_check']) . " = $days_since_last_check";
 		if($metas['link_http_response_code'] == '200' && $days_since_last_check	< 10) {
 			_e('OK', 'zelist');
	}
 		elseif($metas['link_http_response_code'] == '200') {
 			_e('OK (needs a new check)', 'zelist');
	}
 		elseif(in_array($metas['link_http_response_code'], array('301', '302'))) {
	echo '<b>New location</b>';
	}
 			//printf(__('New location: %1$s', 'zelist'), $metas['link_new_location']);
 		else {
	if(isset($metas['link_http_response_code']) && !empty($metas['link_http_response_code'])) {
	printf(__('Code: %s', 'zelist'), $metas['link_http_response_code']);
	}
	}

	foreach(array('region', 'organisation', 'public') as $key) {
	$meta_value = get_post_meta($post_id, $key, true);
	if($meta_value != null && $meta_value != 'null') {
	if(is_array($meta_value))
	echo "<br />$key: <b>" . implode(', ', $meta_value) .'</b>';
	else
	echo "<br />$key : <b>" . $meta_value .'</b>';
	}

 		//plouf($metas);
	}

	break;*/
		   case 'updated' :
				$post = get_post($post_id);
			//echo "<br />ID $post_id "; plouf($post);

				$post_status = get_post_status_object( get_post_status( $post_id) );
				printf(__('Status: %s', 'zelist'), $post_status->label);
				echo '<br />';
				printf(
					__('Published on: %1$s', 'zelist'),
					get_the_date(get_option('date_format'), $post_id)
				);
				echo '<br />';
				printf(
					__('Updated on: %1$s', 'zelist'),
					get_the_modified_date(get_option('date_format'), $post_id)
				);

			break;
			default:
			 	if(substr($column_id, 0, 4) == 'acf:') {
			 		$acf_field_key = substr($column_id, 4);
			 		$value = get_field($acf_field_key, $post_id);
			 		$acf_field = acf_get_field($acf_field_key);

    			 	if($acf_field['type'] === 'url') {
    			 		printf(
    			 			__('<a href="%s" target="_blank" rel="noopener">%s</a>', 'zelist'),
    			 			$value,
    			 			$value
    			 		);
    			 	}
    			 	elseif($acf_field['type'] === 'true_false') {
    			 		if($value == '1') {
    			 			echo '<span class="dashicons dashicons-star-filled"></span>';
    			 		}
    			 	}
    			 	else {
    			 		echo $value;
    				// . " type = " . $acf_field['type'];
    			 	}
			 	}
			 	else {
			 		//echo " colu = $column_id";
			 	}
			break;
	}
	}
}

add_filter( 'manage_edit-listing_sortable_columns', 'zelist_admin_sortable_column_updated' );
function zelist_admin_sortable_column_updated( $columns ) {
	$columns['updated'] = 'post_modified';
	return $columns;
}