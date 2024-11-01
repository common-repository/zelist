<?php
/**
 * Plugin Name: zeList directory for WordPress
 * Description: List things
 * Version: 3.0.6
 * Plugin Slug: zelist
 * Author: Fluenx
 * Author URI: http://www.fluenx.com/
 * Text Domain: zelist
 * Domain Path: /languages
 */

defined('ZELIST_NAME') 		or define( 'ZELIST_NAME', 	plugin_basename( __FILE__ ) ); // plugin name as known by WP.
defined('ZELIST_SLUG') 		or define( 'ZELIST_SLUG', 	'zelist' );// plugin slug (should match above meta: Text Domain).
defined('ZELIST_DIR') 		or define( 'ZELIST_DIR', 	dirname( __FILE__ ) ); // our directory.
defined('ZELIST_PATH') 		or define( 'ZELIST_PATH', 	realpath(__DIR__) ); // our directory.
defined('ZELIST_URL') 		or define( 'ZELIST_URL', 	plugins_url( '', __FILE__ ) );
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
defined('ZELIST_VERSION')	or define( 'ZELIST_VERSION', $plugin_data['Version']);

$wp_upload_dir = wp_upload_dir();
defined('ZELIST_FILES')		or define( 'ZELIST_FILES', 	$wp_upload_dir['basedir'] . '/zelist');
defined('ZELIST_FILES_URL')	or define( 'ZELIST_FILES_URL', $wp_upload_dir['baseurl'] . '/zelist');
defined('ZELIST_LOG')		or define( 'ZELIST_LOG', 		ZELIST_FILES . '/logs');
if(!is_dir(ZELIST_FILES)) {
	mkdir(ZELIST_FILES);
}
if(!is_dir(ZELIST_LOG)) {
	mkdir(ZELIST_LOG);
}

try {
	require_once( trailingslashit(ZELIST_PATH) . 'zelist-hooks.php' );
	require_once( trailingslashit(ZELIST_PATH) . 'zelist-ajax.php' );
	//require_once( trailingslashit(ZELIST_PATH) . 'zelist-configuration.class.php' );

	require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-plugin-install.php' );
	require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-cpt.php' );

	require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-children-listing.php' );
	require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-functions.php' );
	require_once( trailingslashit(ZELIST_PATH) . 'zelist-configuration.class.php' );

	if(is_admin()) {
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-upgrade.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-functions.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-lists.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-uncategorized.php' );

		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-hooks.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-ajax.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'admin/zelist-admin-meta-box.php' );

		require_once( trailingslashit(ZELIST_PATH) . 'settings/wp-improved-settings-api.class.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'settings/wp-improved-settings.class.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'settings/wp-improved-settings-zelist.class.php' );
	}
	else {
		require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-front.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-shortcodes.php' );
		require_once( trailingslashit(ZELIST_PATH) . 'includes/zelist-front-submit.php' );
	}
} catch (Exception $e) {
	if(current_user_can( 'manage_options')) {
			print_r($e);
			die('probleme');
	}
}

// charge les options par défaut à l'activation
register_activation_hook( __FILE__, 'zelist_activate_plugin' );
function zelist_activate_plugin() {
	zelist_install_plugin();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'zelist_deactivate_plugin' );
function zelist_deactivate_plugin() {
	flush_rewrite_rules();
}

/*
	Teste si le plugin est installé et configuré
	Répond (bool) true pour plugin installé et configuré
	Répond (bool) false pour plugin installé mais valeurs non configurées
*/
function zelist_is_plugin_fully_configured() {
return true;
}

if(!function_exists('plouf')) {
	function plouf($e, $txt = '') {
		if($txt != '') echo "<br />\n$txt";
		echo '<pre>';
		print_r($e);
		echo '</pre>';
	}
}

add_action('plugins_loaded', 'zelist_load_textdomain');
function zelist_load_textdomain() {
	load_plugin_textdomain( 'zelist', false, basename( dirname( __FILE__ ) ) . '/languages' );
} 

function is_directory() {
//	echo " is idr ? ";
//	foreach(array('listing_category', 'listing_tag', 'listing', 'page_id') as $var) echo "\n VAR $var ? = " . get_query_var( $var);
	if(get_query_var('listing_category') || get_query_var('listing_tag') || get_query_var('listing'))
		return true;
	elseif(get_query_var('page_id') === intval(zeListConfiguration::getDirectoryPage()	))
		return true;
	else
		return false;
}

function empty_function_for_text_strings() {
		$test = __('Updated %1$s ago', 'zelist');
		$test = __('Published %1$s ago', 'zelist');
		$text = __('and <a href="%1$s">zeList directory plugin</a>', 'zelist');
		$text = __('Organisation: %1$s', 'zelist');
	}

add_action('init', 'zelist_init_admin');
function zelist_init_admin() {
 if(!class_exists('ACF')) {
 return;
 }

	if(!is_admin()) {
		return;
	}

	if(get_option('zelist_plugin_installed') === false) {
 zelist_install_plugin();
 }
 global $WP_Improved_Settings;

 if(!$WP_Improved_Settings) {
 	$WP_Improved_Settings = new WP_Improved_Settings_zeList();
 }
}

