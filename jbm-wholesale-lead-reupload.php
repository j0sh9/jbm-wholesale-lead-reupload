<?php
/*
Plugin Name: _Wholesale License Upload
Description: Wholesale License Upload
Version: 1.0
*/


add_action( 'show_user_profile' , 'wwlc_add_file_upload_to_admin_page' );
add_action( 'edit_user_profile' , 'wwlc_add_file_upload_to_admin_page' );

function wwlc_add_file_upload_to_admin_page() {
	global $user_id;
	if ( !empty(get_user_meta($user_id, 'wwlc_cf_tier', true)) ) {
		if ( isset($_GET['license_upload_type']) ) {
			$msg_type = $_GET['license_upload_type'];
			?>
			<div class="notice notice-<?php echo $_GET['license_upload_type']; ?> is-dismissible">
				<p><?php echo urldecode($_GET['license_upload_msg']); ?></p>
			</div>
			<?php
		}
	?>
	<script>
		jQuery(document).ready( function() {
			jQuery('label[for=wwlc_cf_license]').parent().next().append('<h4>Upload New License File&nbsp;&nbsp;&nbsp;<form enctype="multipart/form-data" action="/?update_ws_license=1&user_id=<?=$user_id?>&return_url=<?=urlencode(urldecode($_SERVER['REQUEST_URI']))?>" method="POST"><label for="new_ws_license" class="button" >Choose File</label><input type="file" name="new_ws_license" id="new_ws_license" style="display: none;" onChange="new_ws_license_select();" required />&nbsp;&nbsp;&nbsp;<input type="submit" value="upload" name="new_ws_license_upload" class="button button-primary" /></form></h4><p id="WSuploadFilename" style="color:#0073aa;"></p>');
		});
		function new_ws_license_select() {
			var fullPath = document.getElementById('new_ws_license').value;
			if (fullPath) {
				var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
				var filename = fullPath.substring(startIndex);
				if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
					filename = filename.substring(1);
				}
				document.getElementById('WSuploadFilename').innerHTML = filename;
			}
		}
	</script>
	<?php
	}
}


// Save Custom Fields On Admin User Edit Page.
add_action( 'template_redirect' , 'wwlc_add_file_save_from_admin_page' );

function wwlc_add_file_save_from_admin_page() {
	
	if ( isset($_GET['update_ws_license']) && $_GET['update_ws_license'] == 1 ) {
		include_once(plugin_dir_path( __FILE__ ).'savefile.php');
		die();
	}

}