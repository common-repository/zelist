<?php

add_action('acf/save_post', 'zelist_action_submit_form');
if(!function_exists('zelist_action_submit_form')) {
	function zelist_action_submit_form( $post_id ) {
		if( get_post_type($post_id) !== 'listing' ) {
			return;
		}
		if( is_admin() ) {
			return;
		}

		// vars
		$post = get_post( $post_id );

		// add listing category
		$listing_category = intval($_POST['listing_category']);
		wp_set_post_terms($post_id, array($listing_category), 'listing_category');

		// add listing tags
		$input_tags = explode(', ', $_POST['listing_tags']);
		$listing_tags = array();
		if($input_tags) foreach($input_tags as $input_tag) {
			$input_tag = sanitize_text_field($input_tag);
			$listing_tag = get_term_by( 'name', $input_tag, 'listing_tag');
			if(!$listing_tag) {
				$listing_tag = wp_insert_term($input_tag, 'listing_tag');
			}

			if(is_array($listing_tag)) {
				$listing_tags[] = $listing_tag['term_taxonomy_id'];
			}
			elseif($listing_tag) {
				$listing_tags[] = $listing_tag->term_taxonomy_id;
			}
		}
		wp_set_post_terms($post_id, $listing_tags, 'listing_tag');

		// copy ACF image to featured image
		if(isset($_POST['_acfuploader'])) {
			$acf_field_key = $_POST['_acfuploader'];
			if(isset($_POST['acf'][$acf_field_key])) {
				$attachment_id = $_POST['acf'][$acf_field_key];
				add_post_meta($post_id, '_thumbnail_id', $attachment_id);
				delete_field($acf_field_key, $post_id);
			}
		}

		//plouf($_POST, "POST");	plouf($_GET, 'GET');	plouf($post, 'post');	die();
	}
}

if(!function_exists('zelist_submit_form')) {
	function zelist_submit_form($args) {
		if(isset($_POST['_acf_screen']) && $_POST['_acf_screen'] == 'acf_form') {
			_e('Your listing has been submitted and is pending validation by an editor', 'zelist');
			return;
		}

		acf_form_head();

		$fields = zeListConfiguration::fieldsToAskOnFrontEndSubmit();
		$acf_fields = array();
		$before_bits = array();
		$after_bits = array();

		foreach($fields as $field) {
			if($field == 'listing_category') {
				$before_bits[] = '
				<div class="acf-field acf-field-text acf-field--post-category" data-name="_post_category" data-type="select" data-key="_post_category" data-required="1" style="padding: 0;">
					<div class="acf-label"><label for="acf-_post_category">' . __('Listing category', 'zelist') . '<span class="acf-required">*</span></label></div>
					<div class="acf-input">
						<div class="acf-input-wrap">' . wp_dropdown_categories(array(
					'taxonomy'		=> 'listing_category',
					'hierarchical'	=> 1,
					'name'			=> 'listing_category',
					'echo'			=> 0,
					'show_option_none'	=> '-'
				)) .'
						</div>
					</div>
				</div>';
			}
			elseif($field == 'listing_tag') {
				$before_bits[] = '
			<div class="acf-field acf-field-text acf-field--post-tags" data-name="_post_tags" data-type="text" data-key="_post_tags" data-required="0" style="padding: 0;">
					<div class="acf-label"><label for="acf-_post_tags">' . __('Listing Tags', 'zelist') . '</label></div>
					<div class="acf-input">
						<div class="acf-input-wrap"><input type="text" id="acf-_post_tags" name="listing_tags"></div>			</div>
					</div>
			</div>
			';
			}
			elseif($field == 'thumbnail') {
			}
			elseif(substr($field, 0, 4) == 'acf:') {
				$acf_field_key = substr($field, 4);
				$acf_fields[] = $acf_field_key;
			}
		}

		$before_bits = apply_filters('zelist_submit_form_before_bits', $before_bits);
		$after_bits = apply_filters( 'zelist_submit_form_after_bits', $after_bits);

		$acf_form_args = array(
			'post_id'				=> 'new_post',
			'post_title'			=> true,
			'post_content'			=> true,
			'fields'				=> $acf_fields,
			'uploader'				=> 'basic',
			'html_before_fields'	=> implode("\n", $before_bits),
			'html_after_fields'		=> implode("\n", $after_bits),
			'new_post'				=> array(
				'post_type'		=> 'listing',
				'post_status'	=> 'pending'
			),
			'submit_value'			=> __('Submit listing', 'zelist'),

		);
		$acf_form_args = apply_filters('zelist_submit_form_acf_form_args', $acf_form_args);
		acf_form($acf_form_args);
	}
}