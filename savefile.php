<?php
function upload_new_license_file() {
	
	if(isset($_POST["new_ws_license_upload"])) :
		if ( ! function_exists( 'wp_handle_upload' ) )
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$user_id = $_GET['user_id'];

		$uploads_dir = wp_upload_dir();

		$wholesale_dir = array('dir' => ABSPATH . 'wp-content/uploads/wholesale-customers', 'url' => get_site_url().'/wp-content/uploads/wholesale-customers');

		$target_dir = $wholesale_dir['dir']."/".$user_id."/";
	
		define( "JBM_TARGET_DIR", $target_dir);

		if ( ! file_exists( $target_dir ) ) {
			wp_mkdir_p( $target_dir );
		}
		
		$new_file = $_FILES["new_ws_license"];
	
		$target_file = $target_dir . basename($new_file["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$new_file["name"] = 'ws-license-' . $user_id . '-' . time() . '.' . $imageFileType;

		$relative_file_path = $wholesale_dir['url']."/".$user_id."/" . 'ws-license-' . $user_id . '-' . time() . '.' . $imageFileType;

		// Check file size
		if ($new_file["size"] > 25000000) {
			$upload_new_license_file_msg['msg'] = "Your file must be smaller than 25MB";
			$upload_new_license_file_msg['type'] = 'error';
			return $upload_new_license_file_msg;
		}
		// Allow certain file formats
		if ( ! in_array( $new_file[ 'type' ] , get_allowed_mime_types() ) ) {
			$upload_new_license_file_msg['msg'] = "File type not allowed.";
			$upload_new_license_file_msg['type'] = 'error';
			return $upload_new_license_file_msg;
		}
			
		$upload_overrides = array(
			'test_form' 	=> false, 	// Turn off to avoid 'Invalid form submission.'
			'test_type' 	=> false 	// Bypass mime type check so we can avoid doing upload_mimes filter.
		);
		function wwlc_upload_target_dir($upload) {
			$user_id = $_GET['user_id'];
			$upload['subdir'] = '/wholesale-customers/' . $user_id;
			$upload['path'] = $upload['basedir'] . $upload['subdir'];
			$upload['url']  = $upload['baseurl'] . $upload['subdir'];
			
			return $upload;
		}
		// Set temp upload directory for wwlc file upload
		add_filter( 'upload_dir' , 'wwlc_upload_target_dir' );
		// Perform file upload
		$file = wp_handle_upload( $new_file , $upload_overrides );

		// Remove filter that sets temp upload directory
		remove_filter( 'upload_dir' , 'wwlc_upload_target_dir' );

		if ( $file && ! isset( $file[ 'error' ] ) ) {
			$upload_new_license_file_msg['msg'] = basename( $new_file["name"]). " has been uploaded";
			$upload_new_license_file_msg['type'] = 'success';
			update_user_meta( $user_id, 'wwlc_cf_license', $relative_file_path );	
		} else {
			$upload_new_license_file_msg['msg'] = "Sorry, there was an error uploading your image. Please try again.";
			$upload_new_license_file_msg['type'] = 'error';
		}
		return $upload_new_license_file_msg;
	else :
		return false;
	endif;
	
}
$upload_new_license_file_msg = upload_new_license_file();
if ( $upload_new_license_file_msg ) {
	$msg = urlencode($upload_new_license_file_msg['msg']);
	$msg_type = $upload_new_license_file_msg['type'];
	$return_url = $_GET['return_url'];	
	$url = $return_url.'&license_upload_type='.$msg_type.'&license_upload_msg='.$msg;
	exit( wp_redirect( home_url( $url ) ) );
}
?>