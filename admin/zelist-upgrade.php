<?php

function zelist_upgrade() {
	global $wpdb;

	$sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'zelist_link'";
	$zelist_links = $wpdb->get_results($sql);

	echo '<p>';
	printf(__('Links found in old format: %d', 'zelist'), count($zelist_links));

	$sql = "UPDATE $wpdb->posts SET post_type = 'listing' WHERE post_type = 'zelist_link'";
	$updated = $wpdb->query($sql);
	echo '<br />';
	printf(__('Links updated: %d', 'zelist'), $updated);

	$sql = "UPDATE $wpdb->term_taxonomy SET taxonomy = 'listing_tag' WHERE taxonomy = 'link_tag'";
	$updated = $wpdb->query($sql);
	echo '<br />';
	printf(__('Tags updated: %d', 'zelist'), $updated);

	$sql = "UPDATE $wpdb->term_taxonomy SET taxonomy = 'listing_category' WHERE taxonomy = 'zelist_category'";
	$updated = $wpdb->query($sql);
	echo '<br />';
	printf(__('Categories updated: %d', 'zelist'), $updated);
	echo '</p>';
}