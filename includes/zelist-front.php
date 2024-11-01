<?php

function zelist_listing_category_label() {
	$label = zeListConfiguration::listingCategoryLabel();
	if( !$label ) {
		$label = __('Listing category','zelist');
	}
	return $label;
}
function zelist_listing_tag_label() {
	$label = zeListConfiguration::listingTagLabel();
	if( !$label ) {
		$label = __('Listing Tag','zelist');
	}
	return $label;
}

function zelist_get_listing_url( $post_id ) {
	$url = false;
	if( $url_field = zeListConfiguration::getURLField() ) {
		$url = get_field( $url_field, $post_id);
	}
	return $url;
}

function zelist_categories_breadcrumb($add_style = true) {
//global $wp_query;plouf($wp_query);

	$string = '
	<ul id="listing_category_breadcrumb">
 <li><a href="' . get_permalink(zeListConfiguration::getDirectoryPage()) .'">'. get_the_title(zeListConfiguration::getDirectoryPage()) .'</a></li>';
	$term = get_term_by("slug", get_query_var("term"), get_query_var("taxonomy") );
 $tmpTerm = $term;
 $tmpCrumbs = array();
 while ($tmpTerm->parent > 0){
 $tmpTerm = get_term($tmpTerm->parent, get_query_var("taxonomy"));
 $crumb = '<li><a href="' . get_term_link($tmpTerm, get_query_var('taxonomy')) . '">' . $tmpTerm->name . '</a></li>';
 array_push($tmpCrumbs, $crumb);
 }
 $string .= implode(' ', array_reverse($tmpCrumbs));
 $string .= '<li class="current"><a href="' . get_term_link($tmpTerm, get_query_var('taxonomy')) . '">' . $term->name . '</a></li>';
 $string .= '
 </ul>';

	$style = zelist_add_style_for_breadcrumbs();
 	echo "<!--test style ? $add_style / style = " . strlen($style) . "--> ";
 if($add_style) {
 	$string = $string . $style;
 }
 return $string;
}

function zelist_add_style_for_breadcrumbs() {
	$style = '
	<style type="text/css">
	#listing_category_breadcrumb {
	}
	#listing_category_breadcrumb li {
		list-style: none;
		display: inline-block;
		margin-right: .5rem;
	}
	#listing_category_breadcrumb li:after {
		content: " > ";
	}
	#listing_category_breadcrumb li:last-child:after {
		content: "";
	}

	</style>
	';

	echo "<!-- style = " . strlen($style);

	$style = apply_filters('zelist_add_style_for_breadcrumbs', $style);
	echo " / style = " . strlen($style) ." -->";
	return $style;
}

function zelist_listing_meta($args = array()) {
	$defaults = array(
		'before'	=> '<ul class="entry-meta">',
		'after'		=> '</ul>',
		'before_el'	=> '<li class="%s">',
		'after_el'	=> "</li>\n",
		'default_format'	=> __('<span class="label">%s: </span>%s', 'zelist'),
		'separator'	=> ', ',
	);
	$args = wp_parse_args($args, $defaults);
	$args = apply_filters('zelist_listing_meta_format', $args);

	global $post;

	$fields = zeListConfiguration::fieldsToShowOnFront();

	$fields = apply_filters('zelist_listing_meta_fields', $fields);

	if(!count($fields)) {
		return;
	}

	$maybe_twitter = zeListConfiguration::GetTwitterFieldName();
	$maybe_facebook = zeListConfiguration::getFaceBookFieldName();
	echo "\n" . $args['before'];

	foreach($fields as $field) {
		if($field == 'listing_category') {
			//plouf($args);
			printf($args['before_el'], 'listing_category');
			$label = zelist_listing_category_label();
			the_terms($post->ID, 'listing_category', $label , $args['separator']);
			echo $args['after_el'];
		}
		elseif($field == 'listing_tags'){
			printf($args['before_el'], 'listing_tags');
			$label = zelist_listing_tag_label();
			the_terms($post->ID, 'listing_tags', $label , $args['separator']);
			echo $args['after_el'];
		}
		elseif($field == 'thumbnail') {
			if($thumbnail = get_the_post_thumbnail()) {
				printf($args['before_el'], 'thumbnail');
				echo $thumbnail;
				echo $args['after_el'];
			}
		}
		elseif($maybe_twitter && $field == $maybe_twitter) {
			$acf_field_key = substr($field, 4);
			$acf_field = get_field_object($acf_field_key);
			$value = get_field($acf_field_key);
			if(!strlen($value)) {
				continue;
			}
			$parsed_url = parse_url($value);
			if(!isset($parsed_url['host'])) {
				$full_url = 'https://twitter.com/' . $value;
			}
			else {
				$full_url = $value;
			}
			printf($args['before_el'], $acf_field['name']);
			printf(
					__('%s: <a href="%s" target="_blank">%s</a>', 'zelist'),
					$acf_field['label'],
					$full_url,
					$value
				);
			echo $args['after_el'];
		}
		elseif($maybe_facebook && $field == $maybe_facebook) {
			$acf_field_key = substr($field, 4);
			$acf_field = get_field_object($acf_field_key);
			$value = get_field($acf_field_key);
			if(!strlen($value)) {
				continue;
			}
			$parsed_url = parse_url($value);
			if(!isset($parsed_url['host'])) {
				$full_url = 'https://www.facebook.com/' . $value;
			}
			else {
				$full_url = $value;
			}
			printf($args['before_el'], $acf_field['name']);
			printf(
					__('%s: <a href="%s" target="_blank">%s</a>', 'zelist'),
					$acf_field['label'],
					$full_url,
					$value
				);
			echo $args['after_el'];
		}
		elseif(substr($field, 0, 4) == 'acf:') {
			//echo " field $field / maybetwwiter $maybe_twitter / fb $maybe_facebook";
			$acf_field_key = substr($field, 4);
			$value = get_field($acf_field_key);

			if(
				is_null($value)
				||
				(is_string($value) && !strlen($value))
				||
				(is_array($value) && !count($value))
				) {
				continue;
			}
			$acf_field = get_field_object($acf_field_key);
			//plouf($acf_field, "\n KEY $acf_field_key = '$value' " . strlen($value));

			printf($args['before_el'], $acf_field['name']);

			if(is_array($value)) {
				$value = implode($args['separator'], $value);
			}
			if($acf_field['type'] == 'url') {
				printf(
					__('%s: <a href="%s" target="_blank">%s</a>', 'zelist'),
					$acf_field['label'],
					$value,
					$value
				);
			}
			else {
				printf(
					$args['default_format'],
					$acf_field['label'],
					$value
				);
			}
			//the_field($acf_field_key);
			echo $args['after_el'];
		}
	}

	echo "\n" . $args['after'];
}