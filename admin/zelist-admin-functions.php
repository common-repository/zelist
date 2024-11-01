<?php

function zelist_add_post_statuses() {
 if(get_post_type() != 'listing')
 return;
?>

<style type="text/css">
	#deny-action, #markasdead-action {
		 float: left;
 line-height: 28px;
	}

</style>

	<div id="deny-action">
	<input name="deny" type="submit" class="button button-secondary" id="deny" value="<?php esc_attr_e( 'Deny', 'zelist') ?>" />
	</div>
	<div id="markasdead-action">
	<input name="markasdead" type="submit" class="button button-secondary" id="markasdead" value="<?php esc_attr_e( 'Mark as dead', 'zelist') ?>" />
	</div>
<script type="text/javascript">

 // <![CDATA[
    jQuery(document).ready(function($){
 	$('#post_status').append('<option value="deny"><?php _e('Denied', 'zelist'); ?></option>')
 	$('#post_status').append('<option value="dead"><?php _e('Dead', 'zelist'); ?></option>')
 	 //$(".misc-pub-section label").append('test 1');

 	var current_status = $('#original_post_status').val();

 	if(current_status == 'dead') {
 		$('#post-status-display').html('<?php _e('Dead', 'zelist'); ?>');
 		$('#post_status option[value="dead"]').prop('selected', true);
 		$('#save-post').show();
 	}
 	if(current_status == 'deny') {
 		$('#post-status-display').html('<?php _e('Denied', 'zelist'); ?>');
 		$('#post_status option[value="deny"]').prop('selected', true);
 		$('#save-post').show();
 	}

 	$('#deny').click(function() {
 		$('#post_status option:selected').removeAttr("selected");
 		$('#post_status option[value="deny"]').prop('selected', true);
 		$('#hidden_post_status').val('deny');
 		$('#post-status-display').html('<?php _e('Deny', 'zelist'); ?>');
 		$('#save-post').show();
 		$('#save-post').show().val( postL10n.saveDraft );
 		$('#publish').val( postL10n.update );
 		console.log(' draft ' + postL10n.saveDraft);
 		return false;
	 	});

 	$('#markasdead').click(function() {
 		$('#post_status option:selected').removeAttr("selected");
 		$('#post_status option[value="dead"]').prop('selected', true);
 		$('#hidden_post_status').val('dead');
 		$('#post-status-display').html('<?php _e('Dead', 'zelist'); ?>');
 		$('#save-post').show();
 		$('#save-post').show().val( postL10n.saveDraft );
 		$('#publish').val( postL10n.update );
 		console.log(' draft ' + postL10n.saveDraft);
 		return false;
	 	});
 });
 // ]]>
 </script>
<?php
}

