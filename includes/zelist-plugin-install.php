<?php
function zelist_install_plugin() {
	if(!class_exists('ACF')) {
		die(__('Please install and activate Advanced Custom Fields first', 'zelist'));
	}

	$default_options = array(
		'zelist_listing_category_slug' 	=> __('directory/', 'zelist'),
		'zelist_listing_tag_slug'		=> __('directory/tag/', 'zelist'),
		'zelist_listing_slug'			=> __('directory/link/', 'zelist'),
		'zelist_listing_category_label'	=> __('Listing category','zelist'),
		'zelist_listing_tag_label'		=> __('Listing Tag','zelist'),
		'zelist_default_sort'			=> 'post_modified',


	);
	foreach($default_options as $option_key => $option_value) {
		if(!get_option($option_key)) {
			update_option($option_key, $option_value);
		}
	}

	// not installed, just defaulted
	update_option('zelist_plugin_installed', 0);
}