<?php

class zeListConfiguration {
	static function getURLField() {
		return apply_filters( __METHOD__, get_option( 'zelist_url_field' ) );
	}

	static function getRSSField() {
		return apply_filters( __METHOD__, get_option( 'zelist_rss_field' ) );
	}

	static function getDirectoryPage() {
		return apply_filters( __METHOD__, get_option('zelist_directory_page'));
	}

	static function getSlugListingCategory() {
		return apply_filters( __METHOD__, get_option('zelist_listing_category_slug'));
	}

	static function getSlugListingTag() {
		return apply_filters( __METHOD__, get_option('zelist_listing_tag_slug'));
	}

	static function getSlugListing() {
		return apply_filters( __METHOD__, get_option('zelist_listing_slug'));
	}

	static function getACFGroup() {
		return apply_filters( __METHOD__, get_option('zelist_main_acf_group'));
	}

	static function fieldsToShowInAdminList() {
		return apply_filters( __METHOD__, get_option('zelist_show_admin_columns'));
	}

	static function fieldsToShowOnFront() {
		return apply_filters( __METHOD__, get_option('zelist_show_fields_on_front'));
	}

	static function fieldsToAskOnFrontEndSubmit() {
		return apply_filters( __METHOD__, get_option('zelist_ask_fields_on_frontsubmit'));
	}

	static function listingCategoryLabel() {
		return apply_filters( __METHOD__, get_option('zelist_listing_category_label'));
	}
	static function listingCategoryTag() {
		return apply_filters( __METHOD__, get_option('zelist_listing_tag_label'));
	}

	static function getURLFieldName() {
		return apply_filters( __METHOD__, get_option('zelist_url_field'));
	}

	static function getFaceBookFieldName() {
		return apply_filters( __METHOD__, get_option('zelist_facebook_field'));
	}

	static function GetTwitterFieldName() {
		return apply_filters( __METHOD__, get_option('zelist_twitter_field'));
	}

	static function getFeaturedFieldName() {
		return apply_filters( __METHOD__, get_option('zelist_featured_field'));
	}

	static function getDefaultSortOrder() {
		return apply_filters( __METHOD__, get_option('zelist_default_order'));		
	}
	static function getDefaultSortOrderBy() {
		return apply_filters( __METHOD__, get_option('zelist_default_orderby'));		
	}

	static function getFeaturedFieldMetaKey() {
		// probably my ugliest hack to date, thanks ACF
		// direct way (get_field) cause infinite loop and fatal error
		if(!function_exists('get_field')) {
			return false;
		}

		$field = zeListConfiguration::getFeaturedFieldName();
		$acf_field_key = substr($field, 4);
		global $wpdb;
		$sql = "SELECT post_excerpt FROM $wpdb->posts WHERE post_type = 'acf-field' AND post_name = %s";
		$sql = $wpdb->prepare($sql, $acf_field_key);

		$meta_key = $wpdb->get_var($sql);
		return $meta_key;
	}

	static function whichLinkShouldWeDisplayOnArchive() {
		return apply_filters( __METHOD__, get_option('zelist_permalink_on_archive'));
	}

}

