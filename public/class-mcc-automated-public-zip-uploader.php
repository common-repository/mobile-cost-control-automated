<?php





class Mcc_Automated_Public_Zip_Uploader {



	protected $folder = '';



	public function __construct( $folder ) {

		$this->folder = $folder;

	}



	/**

	 * Get folder name where to upload

	 *

	 * @param $post_id

	 *

	 * @return string

	 */

	public function get_folder_name( $filename ) {

		return sanitize_title( $filename );

	}



	/**

	 * Get target path for the parent folder where all files are uploaded

	 *

	 * @return string

	 */

	public function get_target_path() {

		$upload_directory = wp_get_upload_dir();

		$upload_baseurl   = $upload_directory['basedir'];

		return trailingslashit( $upload_baseurl ) . $this->folder;

	}





	





	/**

	 * Get path

	 *

	 * @param $post_id

	 *

	 * @return string

	 */

	public function get_folder_path( $folder ) {

		return trailingslashit( $this->get_target_path() ) . $folder;

	}



	/**

	 * Check if there is an error

	 *

	 * @param $error

	 *

	 * @return bool|WP_Error

	 */

	public function check_error($error) {

		//print_r($error);

		// exit;

		$file_errors = array(

			0 => __( "There is no error, the file uploaded with success", 'your_textdomain' ),

			1 => __( "The uploaded file exceeds the upload_max_files in server settings", 'your_textdomain' ),

			2 => __( "The uploaded file exceeds the MAX_FILE_SIZE from html form", 'your_textdomain' ),

			3 => __( "The uploaded file uploaded only partially", 'your_textdomain' ),

			4 => __( "No file was uploaded", 'your_textdomain' ),

			6 => __( "Missing a temporary folder", 'your_textdomain' ),

			7 => __( "Failed to write file to disk", 'your_textdomain' ),

			8 => __( "A PHP extension stoped file to upload", 'your_textdomain' ),

		);



		if ( $error > 0 ) {

			return new \WP_Error( 'file-error', $file_errors[$error] );

		}



		return true;

	}

  

	/**

	 * Upload File

	 *

	 * @param $file

	 *

	 * @return bool|string|true|WP_Error

	 */

	public function upload( $file ) {

		/** @var $wp_filesystem \WP_Filesystem_Direct */

		global $wp_filesystem;

    

		if ( ! function_exists( 'WP_Filesystem' ) ) {

			include_once 'wp-admin/includes/file.php';

		}

    

		WP_Filesystem();



		// print_r($file);

		// exit;



		$file_error = $file["file"]["error"];



		//$file_error = $file_error[0];

		// Check for Errors

		if ( is_wp_error( $this->check_error( $file_error ) ) ) {

			return $this->check_error( $file_error );

		}

    

		$file_name       = $file["file"]["name"];

		// print_r($file_name);

		// exit;

		$file_name_arr   = explode( '.', $file_name );

		$extension       = array_pop( $file_name_arr );

		$filename        = implode( '.', $file_name_arr ); // File Name




		if ( 'zip' !== $extension ) {

			return new WP_Error( 'no-zip', 'This does not seem to be a ZIP file' );

		}



		$temp_name  = $file["file"]["tmp_name"];

		$file_size  = $file["file"]["size"];




		// Get folder path
		//if need the file to unzip inside folder having same name as file
		// $upload_path = $this->get_folder_path( $this->get_folder_name( $filename ) );

    	// Uploading ZIP file
		$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => 'wp_unique_filename');
		$movefile = wp_handle_upload( $file['file'], $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {

			// Unzip the file to the upload path
			$unzip_result = unzip_file( $movefile['file'], basename($movefile['file'], '.zip') );

			if ( is_wp_error( $unzip_result ) ) {
				return $unzip_result;
			} 
			else {

				//unzip it again.
				$unzip_result = unzip_file( $movefile['file'], basename($movefile['file'], '.zip') );

			}

			$result['zip_file_url'] = $movefile['url'];

			$result['zip_file'] = $movefile['file'];
			
			return  $result;

		} else {

			return new \WP_Error( 'not-uploaded', __( 'Could not upload file', 'your_textdomain' ) );

		}

	}





}