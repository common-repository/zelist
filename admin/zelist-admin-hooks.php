<?php

add_action( 'edit_form_after_title', 'zelist_action_edit_form_after_title');
function zelist_action_edit_form_after_title() {
	if( $url_field = zeListConfiguration::getURLField() ) {
		global $post;
		$url = get_field( $url_field, $post->ID);
		//var_dump($url_field);		var_dump($url);

		echo '
		<div id="url-box" style="margin-top: 5px; padding: 0 10px;">
			<strong>' . __('URL:', 'zelist') . '</strong>
				<span id="sample-permalink"><a href="' . $url .'" rel="noopener" target="_blank">' . $url . '</a></span>‎
		</div>';
	}
}

