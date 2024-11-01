<?php
if( !class_exists( 'WP_Improved_Settings\WP_Improved_Settings' ) ) {
	require( dirname( __FILE__ ) . '/wp-improved-settings.class.php' );
}

class WP_Improved_Settings_zeList extends WP_Improved_Settings\WP_Improved_Settings {
	public $plugin_id = 'zelist';

	public $option_page = 'zelist_settings';

	public $menu_order = 51;

	public $parent_menu = 'edit.php?post_type=listing';
	public $defaultSettingsTab = 'parametres';

	public $extendedActions = array(
		'old_import'	=> 'importFromOlderVersion',
	);

	/*Dashboard: 'index.php'
Posts: 'edit.php'
Media: 'upload.php'
Pages: 'edit.php?post_type=page'
Comments: 'edit-comments.php'
Custom Post Types: 'edit.php?post_type=your_post_type'
Appearance: 'themes.php'
Plugins: 'plugins.php'
Users: 'users.php'
Tools: 'tools.php'
Settings: 'options-general.php'
Network Settings: 'settings.php'
*/

	function importFromOlderVersion() {
		zelist_upgrade();
	}

	static function getPageTitle() {
		return __('zeList settings', 'zelist');
	}

	static function getMenuTitle() {
		return __('Settings', 'zelist');
	}

	function on_save() {
		update_option('zelist_plugin_installed', 1);

		if($_REQUEST['tab'] == 'urls') {
			flush_rewrite_rules();
		}
	}

	function maybe_print_notices() {
		$fully_configured = zelist_is_plugin_fully_configured();
		//plouf($fully_configured);

		if($fully_configured !== true) {
			$class = 'notice notice-error';

			$messages = '';
			foreach($fully_configured as $error) {
				$messages .= '<li>' . $error->get_error_message() . '</li>';
			}
			$message = sprintf(
				__('zeList plugin is not fully configured yet : <ul>%s</ul>', 'zelist'),
				$messages
			);

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), ( $message ) );
		}
	}

	function getSettingsStructure() {
		$settings = array(
			'parametres'	=> array(
				'title' 	=> __('Settings', 'zelist'),
				'sections'	=> array()
			),
			'acf'	=> array(
				'title' 	=> __('ACF', 'zelist'),
				'sections'	=> array()
			),
			'integration' 	=> array(
				'title' 	=> __('Integration', 'zelist'),
				'sections'	=> array()
			),
			'urls'	=> array(
				'title' 	=> __('URL', 'zelist'),
				'sections'	=> array()
			),
			'maintenance'	=> array(
				'title' 	=> __('Maintenance', 'zelist'),
				'sections'	=> array()
			),
		);

		/// PARAMETRES

		$settings['parametres']['sections']['pages'] = array(
			'title' 	=> __('Pages', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'directory_page',
				'title'		=> __( 'Directory main page' , 'zelist'),
				 'type'		=> 'raw',
				 'raw_html' 	=> wp_dropdown_pages(array(
				 	'echo' => false,
				 	'name'	=> 'zelist_directory_page',
				 	'show_option_none'	=> '-',
				 	'selected' => zeListConfiguration::getDirectoryPage()
				 )),
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),

			)
		);

		$sort_order_options = array(
				 	'post_modified' 	=> __('Modification date','zelist'),
				 	'post_title'		=> __('Listing title','zelist'),
				 	'ID'				=> __('ID','zelist'),
				 );
		$sort_order_options = apply_filters( 'zelist_sort_order_options', $sort_order_options);

		$settings['parametres']['sections']['settings'] = array(
			'title' 	=> __('General settings', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'default_order',
				'title'		=> __( 'Default sort' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> $sort_order_options,
				 'default'	=> 'post_modified',
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),
				array(
				 'id'		=> 'default_orderby',
				'title'		=> __( 'Default sort order' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> array(
				 	'DESC'		=> __('Descending','zelist'),
				 	'ASC'		=> __('Ascending','zelist'),
				 ),
				 'default'	=> 'post_modified',
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),

			)
		);
		// ACF

		$eligible_groups = array();
		$eligible_groups[-1] = '-';
		$groups = acf_get_field_groups(array('post_type' => 'listing'));
 if($groups) foreach($groups as $group) {
			$eligible_groups[$group['key']] = $group['title'];
		}
		$simpler_fields = array();

		$settings['acf']['sections']['acf'] = array(
			'title' 	=> __('Advanced Custom Fields', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'main_acf_group',
				'title'		=> __( 'ACF group used for listings' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> $eligible_groups,
				 'description'	=> __('Choose the relevant ACF group (amongst those used for "listing" posts type)', 'zelist'),
				 ),
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
			)
		);

		$eligible_fields = array(
			'Native'	=> array(
				'listing_category'	=> __('Listing category', 'zelist'),
				'listing_tag'		=> __('Listing Tags', 'zelist'),
				'thumbnail'			=> __('Thumbnail')
			)

		);
		$css = 'width: 20em; height: ' . (count($eligible_fields['Native']) + 4) .'em;';
		$acf_group = zeListConfiguration::getACFGroup();
		if($acf_group) {
			if($acf_group) {
				$acf_fields = acf_get_fields($acf_group);
			}
			else {
				$acf_fields = array();
			}

			
			if($acf_fields) foreach($acf_fields as $acf_field) {
				$simpler_fields[$acf_field['key']] = $acf_field['label'];
				$eligible_fields['ACF']['acf:'. $acf_field['key']] = $acf_field['label'];
			}

			$css = 'width: 20em; height: ' . (count($eligible_fields['Native']) + count($eligible_fields['ACF']) + 10) .'em;';
		}

		$settings['acf']['sections']['admin_columns'] = array(
			'title' 	=> __('Admin columns', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'show_admin_columns',
				'title'		=> __( 'Show on admin lists' , 'zelist'),
				 'type'		=> 'multiselect',
				 'options'	=> $eligible_fields,
				 'value'		=> zeListConfiguration::fieldsToShowInAdminList(),
				 'css'		=> $css,
				 ),
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
			)
		);

		$settings['acf']['sections']['show_fields_on_front'] = array(
			'title' 	=> __('Show on front end', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'show_fields_on_front',
				'title'		=> __( 'Show on front end' , 'zelist'),
				 'type'		=> 'multiselect',
				 'options'	=> $eligible_fields,
				 'value'		=> zeListConfiguration::fieldsToShowOnFront(),
				 'css'		=> $css,
				 ),
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
			)
		);

		$settings['acf']['sections']['ask_fields_on_frontsubmit'] = array(
			'title' 	=> __('Front end submit', 'zelist'),
			'fields'	=> array(
				array(
				 'id'		=> 'ask_fields_on_frontsubmit',
				'title'		=> __( 'Show on front end form' , 'zelist'),
				 'type'		=> 'multiselect',
				 'options'	=> $eligible_fields,
				 'value'		=> zeListConfiguration::fieldsToAskOnFrontEndSubmit(),
				 'css'		=> $css,
				 ),
				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
			)
		);

		$settings['acf']['sections']['custom_fields'] = array(
			'title'		=> __('Website fields', 'zelist'),
			'fields' 	=> array(
				array(
					'id'			=> 'url_field',
				'title'			=> __( 'URL Field', 'wpdeepl' ),
					'type'			=> 'select',
					'options'		=> array_merge(
						array(
							'0' => __('None', 'zelist')
						),
						$simpler_fields
					),
					'description'	=> '',
				),

				array(
					'id'			=> 'rss_field',
				'title'			=> __( 'RSS Field', 'wpdeepl' ),
					'type'			=> 'select',
					'options'		=> array_merge(
						array(
							'0' => __('None', 'zelist')
						),
						$simpler_fields
					),
					'description'	=> '',
				),

			)
		);

		// INTEGRATION
		$settings['integration']['sections']['labels'] = array(
			'title' 	=> __('Labels', 'zelist'),
			'fields'	=> array(
				array(
					'id'			=> 'listing_category_label',
					'title'			=> __( 'Listing category' , 'zelist'),
				 	'type'			=> 'text',
				 	'placeholder'	=> __( 'Listing category' , 'zelist'),
				 	'description'	=> __('Can be used on the front site with : <code>&lt;?php echo zelist_listing_category_label(); ?&gt;</code>'),
				),
				array(
					'id'		=> 'listing_tag_label',
					'title'		=> __( 'Listing Tag' , 'zelist'),
				 	'type'		=> 'text',
				 	'placeholder'	=> __( 'Listing Tag' , 'zelist'),
				 	'description'	=> __('Can be used on the front site with : <code>&lt;?php echo zelist_listing_tag_label(); ?&gt;</code>'),
				),
			)
		);

		$settings['integration']['sections']['liens'] = array(
			'title' 	=> __('Links', 'zelist'),
			'fields'	=> array()
		);

		$options =array('0'	=> __('None', 'zelist'));

		if(isset($eligible_fields['ACF'])) {
			$options = array_merge($options, $eligible_fields['ACF']);
		}

		/*foreach($eligible_fields['ACF'] as $key => $value) {
			$acf_field_key = substr($key, 4);
			$acf_field = get_field_object($acf_field_key);
			plouf($acf_field, " $key = $value ($acf_field_key)");
		}*/

		if(isset($eligible_fields['ACF'])) {
			$settings['integration']['sections']['liens']['fields'][] = array(
				 'id'		=> 'featured_field',
				'title'		=> __( 'Featured field' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> $options

				);
			$settings['integration']['sections']['liens']['fields'][] = array(
				 'id'		=> 'url_field',
				'title'		=> __( 'Website URL field name' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> $options

				);
			$settings['integration']['sections']['liens']['fields'][] = array(
				 'id'		=> 'url_field',
				'title'		=> __( 'Website URL field name' , 'zelist'),
				 'type'		=> 'select',
				 'options'	=> $options

				);
			$settings['integration']['sections']['liens']['fields'][] = array(
				 'id'		=> 'facebook_field',
				'title'		=> __( 'Facebook field name' , 'zelist'),
				 'type'		=> 'select',
				 'description'	=> __('Facebook URL will be prepended if needed', 'zelist'),
				 'options'	=> $options

				);
			$settings['integration']['sections']['liens']['fields'][] = array(
				 'id'		=> 'twitter_field',
				'title'		=> __( 'Twitter field name' , 'zelist'),
				 'type'		=> 'select',
				 'description'	=> __('Twitter URL will be prepended if needed', 'zelist'),
				 'options'	=> $options

				);
		}
		$settings['integration']['sections']['liens']['fields'][] = array(
		 'id'		=> 'permalink_on_archive',
			'title'		=> __( 'Where should the link point on archive pages' , 'zelist'),
		 'type'		=> 'radio',
		 'values'	=> array(
		 	'internal'	=> __('To the listing single page', 'zelist'),
		 	'external'	=> __('To the external URL', 'zelist'),
		 ),
		 'default'	=> 'internal'
		 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
		);

		// URLS

		$settings['urls']['sections']['urls'] = array(
			'title' 	=> __('Slugs', 'zelist')
		);

		 if ( get_option('permalink_structure') ) {
		 	$settings['urls']['sections']['urls']['fields'] = array(

				array(
				 'id'		=> 'listing_category_slug',
				'title'		=> __( 'Prefix for categories' , 'zelist'),
				 'type'		=> 'text',
				 'class'		=> 'required',
				 'placeholder'	=> __('directory/', 'zelist'),
				 'value'		=> zeListConfiguration::getSlugListingCategory()

				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),
				array(
				 'id'		=> 'listing_tag_slug',
				'title'		=> __( 'Prefix for listing tags' , 'zelist'),
				 'type'		=> 'text',
				 'class'		=> 'required',
				 'placeholder'	=> __('directory/tag/', 'zelist'),
				 'value'		=> zeListConfiguration::getSlugListingTag()

				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),
				array(
				 'id'		=> 'listing_slug',
				'title'		=> __( 'Prefix for single listing' , 'zelist'),
				 'type'		=> 'text',
				 'class'		=> 'required',
				 'placeholder'	=> __('directory/listing/', 'zelist'),
				 'value'		=> zeListConfiguration::getSlugListing()

				 //'description'		=> __( 'Format des étiquettes PDF' , 'zelist'),
				),
			);
		}

		else {
			$settings['urls']['sections']['urls']['description'] = sprintf(
				__('Please activate <a href="%s">Pretty Permalinks</a> to manage Links URLs options', 'zelist'),
				admin_url('/wp-admin/options-permalink.php')
			);

			$settings['urls']['sections']['urls']['fields'] = array();
		}

		// Maintenance

		$settings['maintenance']['sections']['maintenance'] = array(
			'title' 	=> __('Slugs', 'zelist'),
			'fields'	=> array(),
		);
		$settings['maintenance']['footer'] = array();
		$settings['maintenance']['footer'][] = '<input type="submit" name="old_import" id="import-old" class="button button-primary" value="' . __('Import from older version', 'zelist') . '">';

		return apply_filters('zelist_admin_configuration', $settings);
	}
}