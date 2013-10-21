<?php
/*
Plugin Name: Fancy Gallery (radykal)
Plugin URI: http://codecanyon.net/item/fancy-gallery-wordpress-plugin/400535
Description: Create your own galleries with unlimited of albums. You can upload different types of media. Use the generator for external usage of a gallery.
Version: 2.1.1
Author: Rafael Dery
Author URI: http://codecanyon.net/user/radykal
*/


if (!defined('FG_PLUGIN_DIR'))
              define( 'FG_PLUGIN_DIR', dirname(__FILE__) );

if(!class_exists('FancyGallery')) {
	class FancyGallery {
		
		private $wpdb;
		private $version = '2.1.0';
		private $version_field_name = 'fancygallery_version';
		private $options_field_name = 'fg_gallery_options';
		private $gallery_table_name;
		private $album_table_name;
		private $images_table_name;
		private $default_options;
		private $maximum_filesize;
		private $content_dir;
		
		const CAPABILITY = "edit_fancy_galleries";
		const DEMO = false;
		
	    public function __construct() {
			
			global $wpdb;
			
			$this->maximum_filesize = 1024 * 1000;
            
			//set table option names
			$this->wpdb = $wpdb;
			$this->gallery_table_name = $wpdb->prefix . "fg_gallery";
			$this->album_table_name = $wpdb->prefix . "fg_album"; 
			$this->images_table_name = $wpdb->prefix . "fg_media";
			
			$this->default_options = array('background_color' => '#F5F5F5',
			                               'title_color' => '#383634',
										   'thumbnail_width' => 140,
										   'thumbnail_height' => 79,
										   'thumbnail_opacity' => '0.6',
										   'thumbnails_per_page' => 6,
										   'thumbnail_zc' => 1,
										   'title_height' => 20,
										   'border_thickness' => 3,
										   'row_offset' => 15,
										   'column_offset' => 15,
										   'shadow_offset' => 0,
										   'shadow_image' => plugins_url('/images/fancygallery/shadows/shadow1.png', __FILE__),
										   'hover_image' => '',
										   'hover_image_effect' => 'fade',
										   'nav_position' => 'bottom',
										   'text_fade_direction' => 'normal',
										   'dropdown_theme' => 'white',
										   'dropdown' => 1,
										   'divider' => 1,
										   'show_title' => 0,
										   'slide_title' => 1,
										   'inverse_hover_effect' => 0,
										   'second_thumbnail' => 0, //V2.0
										   'timthumb_Url' => plugins_url('/admin/timthumb.php', __FILE__), //V2.0
										   'timthumb_parameters' => '&zc=1&f=2', //V2.0
										   'all_medias_selector' => '', //V2.0
										   'album_selection' => 'dropdown', //V2.0
										   'navigation' => 'arrows', //V2.0
										   'nav_style' => 'white', //V2.0
										   'nav_alignment' => 'left', //V2.0
										   'nav_previous_text' => '<', //V2.0
										   'nav_next_text' => '>', //V2.0
										   'nav_back_text' => '&crarr;', //V2.0
										   'thumbnail_transition' => 'fade', //V2.0
										   'lightbox' => 'prettyphoto', //V2.0
										   'prettyphoto_theme' => 'pp_default',
										   'prettyphoto_overlay' => 1,
										   'prettyphoto_image_resize' => 1,
										   'prettyphoto_deeplinking' => 0, 
										   'prettyphoto_slideshow' => 0,
										   'prettyphoto_social_tools' => 1, //V2.0
										   'fancybox_width' => 800, //V2.0
										   'fancybox_height' => 600, //V2.0
										   'fancybox_autoplay' => 0, //V2.0
										   'fancybox_arrows' => 1, //V2.0
										   'fancybox_loop' => 1, //V2.0
										   'fancybox_title_position' => 'inside', //V2.0
										   'fancybox_buttons_position' => 'none', //V2.0
										   'fancybox_thumbs_position' => 'none', //V2.0
										   'columns' => 5, //V2.1.0
										   'media_label' => 'Media', //V2.1.1
										   'show_only_first_thumbnail' => 0 //V2.1.1
									    );
			
			if(is_multisite()) {
				global $blog_id;
				if (isset($blog_id) && $blog_id > 1) {
					$this->content_dir = WP_CONTENT_DIR . "/blogs.dir/" .$blog_id . "/fancygallery";
				}
				else {
					$this->content_dir = WP_CONTENT_DIR . "/fancygallery";
				}
			}
			else {
				$this->content_dir = WP_CONTENT_DIR . "/fancygallery";
			}
			
			require_once(FG_PLUGIN_DIR . '/widgets.php');
			
			//include gallery_shortcode.php if exists to replace the default gallery shortcode
			if(file_exists(FG_PLUGIN_DIR . '/gallery_shortcode.php'))
				require_once(FG_PLUGIN_DIR . '/gallery_shortcode.php');
			
			
			//activation and actions
            register_activation_hook(  __FILE__, array( &$this, 'activate_plugin' ) );
			//Uncomment this line to delete all database tables when deactivating the plugin
            //register_deactivation_hook( __FILE__, array( &$this,'deactive_plugin' ) );
			
			//action hooks
			add_action( 'init', array( &$this,'init_plugin' ) );
			add_action( 'admin_init', array( &$this,'init_admin' ) );
			add_action( 'plugins_loaded', array( &$this,'check_version' ) );
			add_action( 'wp_enqueue_scripts',array( &$this,'enqueue_scripts_styles' ) );
			add_action( 'admin_menu', array( &$this,'add_menu_pages' ) );
			add_action( 'widgets_init', create_function( '', 'return register_widget("FancyGalleryListWidget");' ) );
			add_action( 'widgets_init', create_function( '', 'return register_widget("FancyGalleryAlbumWidget");' ) );
			add_action( 'widgets_init', create_function( '', 'return register_widget("FancyGalleryMediaWidget");' ) );
					
			//filter hooks
            add_filter('widget_text', 'do_shortcode');
			
			//shortcodes
	        add_shortcode('fancygallery', array($this,'add_fancyGallery'));

		}
		
		public function init_plugin() {
				
			load_plugin_textdomain('radykal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
			
		}
		
		public function init_admin() {
			
			// get value for safe mode
			if ( (gettype( ini_get('safe_mode') ) == 'string') ) {
				if ( ini_get('safe_mode') == 'off' ) define('SAFE_MODE', FALSE);
				else define( 'SAFE_MODE', ini_get('safe_mode') );
			} else
			define( 'SAFE_MODE', ini_get('safe_mode') );
			
			//action hooks for the admin
			add_action( 'wp_ajax_newgallery', array( &$this, 'new_gallery' ) );
			add_action( 'wp_ajax_editgallery', array( &$this, 'edit_gallery' ) );
			add_action( 'wp_ajax_deletegallery', array( &$this, 'delete_gallery' ) );
			add_action( 'wp_ajax_newalbum', array( &$this, 'new_album' ) );
			add_action( 'wp_ajax_editalbum', array( &$this, 'edit_album' ) );
			add_action( 'wp_ajax_deletealbum', array( &$this, 'delete_album' ) );
			add_action( 'wp_ajax_updatealbums', array( &$this, 'update_albums' ) );
			add_action( 'wp_ajax_loadfiles', array( &$this, 'load_files' ) );
			add_action( 'wp_ajax_uploadfile', array( &$this, 'upload_file' ) );
			add_action( 'wp_ajax_savefile', array( &$this, 'save_file' ) );
			add_action( 'wp_ajax_deletefiles', array( &$this, 'delete_files' ) );
			add_action( 'wp_ajax_updatefiles', array( &$this, 'update_files' ) );
			
			wp_register_script('ajax-utils', plugins_url('/admin/js/ajax-utils.js', __FILE__));
			
			$role = get_role( 'administrator' );
			$role->add_cap( FancyGallery::CAPABILITY ); 
			
		}
		
		public function check_version() {
			
			if( get_option($this->version_field_name) != $this->version)
			    $this->upgrade();
		}
		
		public function activate_plugin($networkwide) {
		  
		   if(version_compare(PHP_VERSION, '5.2.0', '<')) { 
			  deactivate_plugins(plugin_basename(__FILE__)); // Deactivate plugin
			  wp_die("Sorry, but you can't run this plugin, it requires PHP 5.2 or higher."); 
			  return; 
			}
			
			global $wpdb;
			
			if ( is_multisite() ) {
	    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	                $current_blog = $wpdb->blogid;
	    			// Get all blog ids
	    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
	    			foreach ($blogids as $blog_id) {
	    				switch_to_blog($blog_id);
	    				$this->install();
	    			}
	    			switch_to_blog($current_blog);
	    			return;
	    		}	
	    	}
						
			$this->install();
			
		}
		
		public function deactive_plugin($networkwide) {
		
			global $wpdb;
 
		    if (is_multisite()) {
		        if ($networkwide) {
		            $old_blog = $wpdb->blogid;
		            // Get all blog ids
		            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
		            foreach ($blogids as $blog_id) {
		                switch_to_blog($blog_id);
		                $this->deinstall();
		            }
		            switch_to_blog($old_blog);
		            return;
		        }  
		    }
		    
		    $this->deinstall();
		    
		}
		
		public function add_menu_pages() {
			
			//create own admin menu with subs
			add_menu_page( 'Fancy Gallery', 'Fancy Gallery',  FancyGallery::CAPABILITY, 'fancy-gallery', array($this, 'manage_galleries_admin_page'), plugins_url('/admin/images/menu_icon.png', __FILE__) );
			$manage_galleries = add_submenu_page( 'fancy-gallery', __('Manage galleries'), __('Manage galleries'),  FancyGallery::CAPABILITY, 'fancy-gallery', array($this, 'manage_galleries_admin_page') );
			$options = add_submenu_page( 'fancy-gallery', __('Options'), __('Options'),  FancyGallery::CAPABILITY, 'fancy-gallery-options', array($this, 'options_admin_page') );
			
			//include styles and  scripts for the admin
			add_action('admin_print_styles-' . $manage_galleries, array( &$this,'manage_galleries_styles'));
			add_action('admin_print_scripts-' . $manage_galleries, array( &$this,'manage_galleries_scripts'));
			add_action('admin_print_styles-' . $options, array( &$this,'options_styles'));
			add_action('admin_print_scripts-' . $options, array( &$this,'options_scripts'));
			
		}
		
		public function manage_galleries_styles() {
			
			wp_enqueue_style('fg-admin', plugins_url('/admin/css/admin.css', __FILE__), array('thickbox'), $this->version);
				
		}
		
		public function manage_galleries_scripts() {
			
			$nonce = wp_create_nonce( 'fg-upload-nonce' );
			wp_enqueue_script('jquery-ui-widget-upload', plugins_url('/admin/js/vendor/jquery.ui.widget.js', __FILE__), array('jquery', 
																															  'jquery-ui-core', 
																															  'jquery-ui-sortable', 
																															  'wp-ajax-response', 
																															  'ajax-utils', 
																															  'media-upload',
																															  'thickbox')
			);
			wp_enqueue_script('jquery-iframe-transport', plugins_url('/admin/js/jquery.iframe-transport.js', __FILE__) );
			wp_enqueue_script('jquery-file-upload', plugins_url('/admin/js/jquery.fileupload.js', __FILE__));			
			wp_enqueue_script('fg-manage-galleries', plugins_url('/admin/js/manage-galleries.js', __FILE__) );
			wp_enqueue_script('jquery-tiptip', plugins_url('/admin/js/jquery.tipTip.minified.js', __FILE__) );
			
			if(is_multisite()) {
				global $blog_id;
				if (isset($blog_id) && $blog_id > 1) {
					$blog_details = get_blog_details(1);
					$content_url = $blog_details->siteurl . "/wp-content/blogs.dir/" .$blog_id . "/fancygallery/";
				}
				else {
					$content_url = content_url() . "/fancygallery/";
				}
			}
			else {
				$content_url = content_url() . "/fancygallery/";
			}
			wp_localize_script('fg-manage-galleries', 'options', array( 'uploadNonce' => $nonce, 'Ajax_Url' => admin_url( 'admin-ajax.php'), 'adminUrl' => plugins_url("admin", __FILE__), 'contentUrl' => $content_url ));
			
		}
		
		public function options_styles() {
			
			wp_enqueue_style('fg-colorpicker', plugins_url('/admin/css/spectrum.css', __FILE__));
			wp_enqueue_style('fg-admin', plugins_url('/admin/css/admin.css', __FILE__), array('thickbox'), $this->version);
				
		}
		
		public function options_scripts() {
			
			wp_enqueue_script('fg-colorpicker', plugins_url('/admin/js/spectrum.js', __FILE__));	
			wp_enqueue_script('fg-options', plugins_url('/admin/js/options.js', __FILE__), array('jquery', 'media-upload','thickbox'), $this->version);
						
		}
		
		public function manage_galleries_admin_page() {
			require_once(FG_PLUGIN_DIR . '/admin/manage-galleries.php');
				
		}
		
		public function options_admin_page() {
											
			//save options for gallery
			if(isset($_POST['fg_opts_save']) || isset($_POST['fg_generate_code']) || !empty($_POST['overwrite_gallery'])){
				
				//overwrite options with another one
				if( !empty($_POST['overwrite_gallery']) ) {
					$_POST['selected_gallery'] = $_POST['overwrite_gallery'];
				}

				$new_opts;
				
				foreach($this->default_options as $key => $value) {
					$new_opts[$key] = $_POST[$key] === null ? 0 : $_POST[$key];
				}
				
				if( !isset($_POST['fg_generate_code']) ) {
					$this->wpdb->update( $this->gallery_table_name, array('options' => serialize($new_opts) ), array('ID'=> $_POST['selected_gallery']), array('%s') );
				}								 
				$options = $new_opts;
				
			}
			//reset options
			else if($_POST['fg_opts_reset']) {
			
				$this->wpdb->update( $this->gallery_table_name, array('options' => serialize($this->default_options) ), array('ID'=> $_POST['selected_gallery']), array('%s') );
				$options = $this->default_options;
				
			}
			//dropdown changed
			else if(isset($_POST['selected_gallery'])) {
				$requested_gallery_opts = $this->wpdb->get_results('SELECT options FROM '.$this->gallery_table_name.' WHERE ID="'.$_POST['selected_gallery'].'" LIMIT 1');
			    $options = unserialize($requested_gallery_opts[0]->options);
			}
			else {
				$first_gallery_opts = $this->wpdb->get_results('SELECT options FROM '.$this->gallery_table_name.' ORDER BY title LIMIT 1');
			    $options = unserialize($first_gallery_opts[0]->options);
			    
			}
            
			//check if options is an array
			if(!is_array($options)) {
				$options = $this->default_options;	
			}
			
			
			$options = $this->check_options_availability($options);
			
			//reset options
			if($_POST['fg_generate_code']) {
				require_once(FG_PLUGIN_DIR . '/admin/generate-code.php');
			}
			else {
				require_once(FG_PLUGIN_DIR . '/admin/options.php');	
			}
							
			
				
		}
		
		public function add_fancyGallery($atts) {

			$selectAlbum = '';
			if( isset( $_GET['album']) )
			    $selectAlbum = $_GET['album'];

			extract( shortcode_atts( array(
				'id' => '',
				'album' => ''
			), $atts ) );
			
			
			$options = $this->wpdb->get_results("SELECT options FROM {$this->gallery_table_name} WHERE ID='$id'");
			$options = unserialize($options[0]->options);
			
			//check if options is an array, fallback
			if(!is_array($options)) {
				$options = $this->default_options;	
			}
			
			//get albums of the gallery corresponding to the ID
			if(empty($album)) {
				$selector = 'fancygallery-'.$id;
				$albums = $this->wpdb->get_results("SELECT * FROM {$this->album_table_name} WHERE gallery_id='$id' ORDER BY sort ASC");
			}
			else {
				$selector = 'fancygallery-'.$id.'-'.$album;
				$albums = $this->wpdb->get_results("SELECT * FROM {$this->album_table_name} WHERE gallery_id='$id' && ID='$album' ORDER BY sort ASC");
			}
			

			//check if gallery exists and has albums
			if(sizeof($albums) == 0)
				return sprintf(__('<p class="fg-error"><strong>Fancy Gallery Error:</strong><br />No gallery with the ID %s was found or you have not created albums for it yet.</p>', 'radykal'), $id);
			
			//html output
			ob_start();
			?><div id="<?php echo $selector; ?>" class="fg-panel">
				<?php foreach($albums as $album) : ?><div title="<?php echo stripslashes($album->title); ?>"><?php
				 
					$album_files = $this->wpdb->get_results("SELECT * FROM {$this->images_table_name} WHERE album_id='$album->ID' ORDER BY sort ASC");
					foreach($album_files as $album_file):
													
					echo $this->get_media_link($album_file->file, $album_file->thumbnail, $options['thumbnail_width'], $options['thumbnail_height'], $options['thumbnail_zc'], $album_file->title, $album_file->description);
				
				endforeach; ?></div><?php endforeach; ?></div>
			<?php
			//js output
			
			$options = $this->check_options_availability($options);
			
			echo $this->get_js_code($options, $selector, $selectAlbum);
						
			$output_gallery = ob_get_contents();
			
			ob_end_clean();
						
			return $output_gallery;
			
		}
		
		//includes scripts and styles in the frontend
		public function enqueue_scripts_styles() {
		
			wp_enqueue_style('prettyphoto', plugins_url('/prettyphoto/css/prettyPhoto.css', __FILE__));
			wp_enqueue_style('fancybox', plugins_url('/fancybox/jquery.fancybox.css', __FILE__));
			wp_enqueue_style('fancybox-buttons', plugins_url('/fancybox/helpers/jquery.fancybox-buttons.css', __FILE__));
			wp_enqueue_style('fancybox-thumbs', plugins_url('/fancybox/helpers/jquery.fancybox-thumbs.css', __FILE__));
			wp_enqueue_style('radykal-fancy-gallery', plugins_url('/css/jquery.fancygallery.css', __FILE__), array(), $this->version);
		
			wp_register_script('prettyphoto', plugins_url('/prettyphoto/jquery.prettyPhoto.js', __FILE__));
			wp_register_script('fancybox', plugins_url('/fancybox/jquery.fancybox.pack.js', __FILE__));
			wp_register_script('fancybox-media', plugins_url('/fancybox/helpers/jquery.fancybox-media.js', __FILE__));
			wp_register_script('fancybox-buttons', plugins_url('/fancybox/helpers/jquery.fancybox-buttons.js', __FILE__));
			wp_register_script('fancybox-thumbs', plugins_url('/fancybox/helpers/jquery.fancybox-thumbs.js', __FILE__));
			wp_enqueue_script('radykal-fancy-gallery', plugins_url('/js/jquery.fancygallery.min.js', __FILE__), array('jquery', 'prettyphoto', 'fancybox', 'fancybox-media', 'fancybox-buttons','fancybox-thumbs'), $this->version);

		}
		
		public function new_gallery() {
			
			//Get post data
		    if ( !isset( $_POST['title'] ) ) 
			    exit;
				
			$title = trim($_POST['title']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			//check if main gallery folder exists
			if(!file_exists($this->content_dir)) {
				//try to create main gallery folder
				if( !wp_mkdir_p($this->content_dir) ) {
				     $errors->add( 'makeroot-error', __(WP_CONTENT_DIR .': Could not create the fancygallery folder! Please check the permission. Set the CHMOD to 755 or 777 for the wp-content folder.', 'radykal'));
					 $this->ajax_error_handler($errors, $error_response);
					 exit;
				}
				
				//check if main gallery folder is writable
				if( !@is_writable($this->content_dir) ) {
					$errors->add( 'gallery-permission-error', sprintf(__('<strong>%s</strong> '.' is not writable! Please check the permission. Set the CHMOD to 755 or 777.', 'radykal')), $this->content_dir);
					$this->ajax_error_handler($errors, $error_response);
					exit;
				}
			}
			
			//insert gallery in DB
			$inserted = $this->wpdb->insert( 
				$this->gallery_table_name, 
				array( 'title' => $title, 'options' => serialize($this->default_options)),
				array( '%s', '%s') 
			);

			if( $inserted ) {
				
				$id = $this->wpdb->insert_id;
				
				$gallery_directory = $this->content_dir . '/' . $id;
				if (wp_mkdir_p($gallery_directory)) {
					$success_response->add(array( 'what' => 'object', 'data' => __('Gallery successfully created!', 'radykal'), 'supplemental' => array('gallery_html' => $this->get_gallery_list_item($id, $title)) ) );
					$success_response->send();
					exit;
				}
				else {
					$errors->add( 'new-gallery-error', __('A new gallery directory could not be created. Please try again!', 'radykal'));
					$this->ajax_error_handler($errors, $error_response);
					exit;
				}
				
			}
			else {
				$errors->add( 'new-gallery-error', __('Database error: A new gallery could not be created!', 'radykal'));
				$this->ajax_error_handler($errors, $error_response);
				exit;
			}
			
		}
		
		public function edit_gallery() {
			
		    if ( !isset($_POST['id']) || !isset($_POST['newTitle']) ) 
			    exit;
			
			$id = trim($_POST['id']);
		    $newTitle = trim($_POST['newTitle']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			if( $this->wpdb->update( $this->gallery_table_name, array('title' => $newTitle ), array('ID' => $id )) ) {
				$success_response->add( array( 'what' => 'object', 'data' => __('Gallery title successfully changed!', 'radykal'), 'supplemental' => array('title' => $newTitle) ) );
				$success_response->send();
			}
			else {
				$errors->add( 'edit-album-error', __('Could not rename gallery, please try again!', 'radykal') );
				$this->ajax_error_handler($errors, $error_response);
			}
			
			exit;
			
		}
		
		public function delete_gallery() {
			
		    if ( !isset( $_POST['id'] ) ) 
			    exit;
		    
			$id = trim($_POST['id']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			try {
			
				$this->wpdb->query("DELETE FROM {$this->album_table_name} WHERE gallery_id ='$id'");
				$this->wpdb->query("DELETE FROM {$this->gallery_table_name} WHERE ID ='$id'");
				$this->delete_directory($this->content_dir . '/' . $id);
				
				$success_response->add( array( 'what' => 'object', 'data' => __('Gallery successfully deleted!', 'radykal') ) );
				$success_response->send();
				
				exit;
				
			}
			catch(Exception $e) {
				$errors->add( 'delete-gallery-error', __('Could not delete gallery!', 'radykal') );		
			}
			
			$this->ajax_error_handler($errors, $error_response);
			exit;
			
		}
		
		public function new_album() {
			
		    if ( !isset($_POST['title']) || !isset($_POST['gallery']) || !isset($_POST['sortId']) ) 
			    exit;
		    
			$title = trim($_POST['title']);
			$sort_id = trim($_POST['sortId']);
			$gallery_id = trim($_POST['gallery']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			$inserted = $this->wpdb->insert(
				$this->album_table_name,
				array( 'gallery_id' => $gallery_id, 'title' => $title, 'sort' => $sort_id ),
				array( '%d', '%s', '%d')
			);
			
			if( $inserted ) {
			
				$id = $this->wpdb->insert_id;
				$album_directory = $this->content_dir . '/' . $gallery_id . '/'. $id;
				
				if (wp_mkdir_p($album_directory)) {					
					$success_response->add(array( 'what' => 'object', 'data' => __('Album successfully created!', 'radykal'), 'supplemental' => array('album_html' => $this->get_album_list_item($id, $title)) ) );
					$success_response->send();
					exit;
				}
				else
				  $errors->add( 'new-album-error', __('An new album directory could not be created. Please try again!', 'radykal') );
			}
			else {
				$errors->add( 'new-album-error', __('Database error: A new album could not be created!', 'radykal'));
			}
			
			$this->ajax_error_handler($errors, $error_response);
			
			exit;
			
		}
		
		public function edit_album() {
			
			//Get post data
		    if ( !isset($_POST['id']) || !isset($_POST['newTitle']) ) 
			    exit;
			
			$id = trim($_POST['id']);
		    $newTitle = trim($_POST['newTitle']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			if( $this->wpdb->update($this->album_table_name, array('title' => $newTitle ), array('ID' => $id)) ) {
				$success_response->add( array( 'what' => 'object', 'data' => __('Album title successfully changed!', 'radykal'), 'supplemental' => array('title' => $newTitle) ) );
				$success_response->send();
				exit;	
			}
			else {
				$errors->add( 'edit-album-error', __('Could not rename album directory!', 'radykal') );
			}
			
			$this->ajax_error_handler($errors, $error_response);
			
			exit;
		}
		
		public function delete_album() {
			
			//Get post data
		    if ( !isset($_POST['id']) || !isset($_POST['galleryId']) ) 
			    exit;
		    
			$id = trim($_POST['id']);
			$gallery_id = trim($_POST['galleryId']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			try {
				$this->wpdb->query("DELETE FROM {$this->album_table_name} WHERE ID ='$id' ");
			    $this->delete_directory($this->content_dir . '/' . $gallery_id . '/' . $id);
				
				$success_response->add( array( 'what' => 'object', 'data' => __('Album successfully deleted!', 'radykal') ) );
				$success_response->send();
				
				exit;
			}
			catch(Exception $e) {
				$errors->add( 'delete-album-error', __('Could not delete album directory!', 'radykal') );		
			}
			
			$this->ajax_error_handler($errors, $error_response);
			exit;   
		}
		
		public function update_albums() {
			
			//Get post data
		    if ( !isset($_POST['albums']) || !isset($_POST['oldGallery']) || !isset($_POST['newGallery']) || !isset($_POST['albumId']) ) 
			    exit;
				
			$albums = $_POST['albums'];
			$oldGalleryId = trim($_POST['oldGallery']);
			$newGalleryId = trim($_POST['newGallery']);
			
			$error_response = $success_response = new WP_Ajax_Response();
			$errors = new WP_Error();
			
			try {
				
				if($oldGalleryId == $newGalleryId) {
					
					//update sort index					
					for($i = 0; $i < sizeof($albums); $i++) {
						
						if(isset($albums[$i])) {
							 $this->wpdb->update(
							 	$this->album_table_name,
							 	array('sort' => $i),
							 	array(
							 		'gallery_id' => $oldGalleryId,
							 		'ID' =>  $albums[$i]
							 	),
							 	array('%d'),
							 	array('%d', '%d')
							 );
						}
						
					}
					
				}
				else {
				
					$album_id = trim($_POST['albumId']);
					
					//update sort index
					for($i = 0; $i < sizeof($albums); $i++) {
						
						if(isset($albums[$i])) {
							 $this->wpdb->update(
							 	$this->album_table_name,
							 	array(
							 		'gallery_id' => $newGalleryId,
							 		'sort' => $i
							 	),
							 	array(
							 		'gallery_id' => $oldGalleryId,
							 		'ID' =>  $albums[$i]
							 	),
							 	array('%d'),
							 	array('%d', '%d')
							 );
						}
						
					}
					
					//move album directory into new gallery folder
					$album_directory_old = $this->content_dir . '/' . $oldGalleryId . '/' . $album_id;
					rename($album_directory_old, $this->content_dir . '/' . $newGalleryId . '/' . $album_id);
					
					//update file pathes
					$this->wpdb->query("UPDATE {$this->images_table_name} SET file = REPLACE(file, '$oldGalleryId', '$newGalleryId') WHERE album_id='$album_id'");
					$this->wpdb->query("UPDATE {$this->images_table_name} SET thumbnail = REPLACE(thumbnail, '$oldGalleryId', '$newGalleryId') WHERE album_id='$album_id'");
					
				}
				
				$success_response->add( array( 'what' => 'object', 'data' => __('Album successfully updated!', 'radykal') ) );
				$success_response->send();
				
				exit;
			}
			catch(Exception $e) {
				$errors->add( 'update-album-error', __('Album could not be updated. Please try again!', 'radykal') );
			}
			
			$this->ajax_error_handler($errors, $error_response);
			
			exit;
				
		}
		
		public function load_files() {
			
		    if ( !isset($_POST['albumId']) ) 
			    exit;
		    
			$album_id = trim($_POST['albumId']);
			$results = $this->wpdb->get_results("SELECT * FROM {$this->images_table_name} WHERE album_id='$album_id' ORDER BY sort DESC");
			
			$response = json_encode( $results );
 
			// response output
			header( "Content-Type: application/json" );
			echo $response;
			
			exit;						
		}
		
		public function upload_file() {
			
			if ( !isset($_POST['albumDir']) ) 
				exit;
				
			$album_dir = trim($_POST['albumDir']);
		
			check_ajax_referer( 'fg-upload-nonce', 'security' );
			
			header( "Content-Type: application/json" );
		
			foreach ($_FILES as $fieldName => $file) {
				
				$filename = $file['name'][0];
				//check if its an image
				if(!getimagesize($file['tmp_name'][0])) {
					echo json_encode(array('error' => 1, 'message' => __('File is not an image', 'radykal'), 'filename' => $filename));
					die;
				}
				
				//check for php errors
				if($file['error'][0] !== UPLOAD_ERR_OK) {
					echo json_encode(array('error' => 1, 'message' => __(file_upload_error_message($file['error'][0]), 'radykal'), 'filename' => $filename));
					die;	
				}
				
				//check for maximum upload size
				if($file['size'][0] > $this->maximum_filesize) {
					echo json_encode(array('error' => 1, 'message' => __('Uploaded image is too big', 'radykal'), 'filename' => $filename));
					die;	
				}
				
			    $myFileExt = sanitize_file_name($filename);
				$myTempFile = $this->content_dir.'/'.$album_dir.$myFileExt;
				if( @move_uploaded_file($file['tmp_name'][0], $myTempFile) ) {
					echo json_encode(array('error' => 0, 'filename' => preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename), 'realFile' => $myFileExt));
				}
				else {
					echo json_encode(array('error' => 1, 'message' => __('PHP Issue - move_uploaed_file failed', 'radykal'), 'filename' => $filename));
				}
			}
			
			die;
			
		}
		
		public function save_file() {
			
			//Get post data
		    if ( !isset($_POST['album']) || !isset($_POST['file']) || !isset($_POST['thumbnail']) || !isset($_POST['sortId']) ) 
			    exit;
			
			$album_id = trim($_POST['album']);
			$file = trim($_POST['file']);
			$thumbnail = trim($_POST['thumbnail']);
			$sort_id = trim($_POST['sortId']);
						
			$this->wpdb->insert( 
				$this->images_table_name, 
				array( 'album_id' => $album_id, 'file' => $file, 'thumbnail' => $thumbnail, 'title' => $title, 'description' => '', 'sort' => $sort_id ),
				array( '%d', '%s', '%s', '%s', '%s', '%d')
			);
			
			exit;
			
		}
		
		public function delete_files() {
			
			//Get post data
		    if ( !isset($_POST['files']) ) 
			    exit;
			
		    $files = $_POST['files'];		     
			 
		    foreach($files as $file) {
			   $file_url = trim($file['value']);
			   $album_dir = strrchr(dirname($file_url), "/");
			   $gallery_dir = strrchr(dirname(dirname($file_url)), "/");
			   
			   $this->wpdb->query("DELETE FROM {$this->images_table_name} WHERE file='$file_url' ");

			   $file_path = $this->content_dir . $gallery_dir . $album_dir . '/' . basename($file_url);
			   if(file_exists($file_path))
				   @unlink($file_path);
		    }
		    exit;
		}
		
		public function update_files() {
			
			//Get post data
		    if ( !isset($_POST['files']) || !isset($_POST['thumbnails']) || !isset($_POST['albumId']) ) 
			    exit;
			
			$album_id = trim($_POST['albumId']);
			$files = $_POST['files'];
			$thumbnails = $_POST['thumbnails'];
		    $descriptions = $_POST['descriptions'];
			$titles = $_POST['titles'];
			
			$this->wpdb->query("DELETE FROM {$this->images_table_name} WHERE album_id='$album_id'");	
			 
			for($i = 0; $i < sizeof($files); $i++) {
				if(isset($files[$i])) {
				
					$this->wpdb->insert(
						$this->images_table_name, 
						array(
							'album_id' => $album_id, 
							'file' => trim($files[$i]['value']), 
							'thumbnail' => trim($thumbnails[$i]['value']), 
							'title' => $titles[$i]['value'], 
							'description' => $descriptions[$i]['value'], 
							'sort' => $i
						),
						array( '%d', '%s', '%s', '%s', '%s','%d' )
					);
				}
			}
			
			exit;
			
		}
		
		private function install() {
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			global $wpdb;
			global $charset_collate;
						  
			$this->gallery_table_name = $wpdb->prefix . "fg_gallery";
			$this->album_table_name = $wpdb->prefix . "fg_album"; 
			$this->images_table_name = $wpdb->prefix . "fg_media";
						
			//create tables in the db
			$gallery_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			              title TEXT COLLATE utf8_general_ci NOT NULL,
			              options TEXT COLLATE utf8_general_ci NOT NULL,
						  PRIMARY KEY (ID)";
			
			$album_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			              gallery_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			              title TEXT COLLATE utf8_general_ci NOT NULL,
						  sort INT COLLATE utf8_general_ci NOT NULL,
						  PRIMARY KEY (ID)";
						  
			$images_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			              album_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
						  file VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
						  thumbnail VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
						  title TEXT COLLATE utf8_general_ci NULL,
						  description TEXT COLLATE utf8_general_ci NULL,
						  sort INT COLLATE utf8_general_ci NOT NULL,
						  PRIMARY KEY (ID)";
						
			$sql = "CREATE TABLE {$this->gallery_table_name} ($gallery_sql) $charset_collate;";
			$sql .= "CREATE TABLE {$this->album_table_name} ($album_sql) $charset_collate;";
			$sql .= "CREATE TABLE {$this->images_table_name} ($images_sql) $charset_collate;";

			dbDelta($sql);
			
			$wpdb->query("ALTER TABLE {$this->album_table_name} ADD CONSTRAINT fk_fg_gallery_id FOREIGN KEY (gallery_id) references {$this->gallery_table_name}(ID) on DELETE CASCADE;");
			$wpdb->query("ALTER TABLE {$this->images_table_name} ADD CONSTRAINT fk_fg_album_id FOREIGN KEY (album_id) references {$this->album_table_name}(ID) on DELETE CASCADE;");
			
			add_option($this->version_field_name, $this->version);
			
			$cache_dir = FG_PLUGIN_DIR . '/admin/cache/';
			
			//create cache for timthumb and set chmod to 755
			if(!file_exists($cache_dir)) {
				wp_mkdir_p($cache_dir);
			}
			chmod($cache_dir, 0755);
			
	   }
	   
	   private function deinstall() {
	   		
	   		global $wpdb;
	   		
	   		$this->gallery_table_name = $wpdb->prefix . "fg_gallery";
			$this->album_table_name = $wpdb->prefix . "fg_album"; 
			$this->images_table_name = $wpdb->prefix . "fg_media";
			
			//delete everything
			delete_option($this->version_field_name);
			$wpdb->query("SET FOREIGN_KEY_CHECKS=0;");
			$wpdb->query("DROP TABLE {$this->images_table_name}");
			$wpdb->query("DROP TABLE {$this->album_table_name}");
			$wpdb->query("DROP TABLE {$this->gallery_table_name}");
			$wpdb->query("SET FOREIGN_KEY_CHECKS=1;");
			if(file_exists($this->content_dir))
			    $this->delete_directory($this->content_dir);
				
		   
	   }
	   
	   private function upgrade() {
	   
	   		global $wpdb;
	   		
	   		//upgrade to V2.1.1
			if( get_option($this->version_field_name) == '2.1.0') {
				//update version in db
		   		update_option($this->version_field_name, '2.1.1');
			}
	   		
	   		//upgrade to 2.1.0
	   		if( get_option($this->version_field_name) == '2.0.0' ) {
	   			
	   			//rename album folders and update album path
	   			$albums = $wpdb->get_results("SELECT id, slug, gallery FROM {$this->album_table_name}");
				foreach ( $albums as $album ) {
					$wpdb->query("UPDATE {$this->images_table_name} SET thumbnail = REPLACE(thumbnail, '{$album->slug}', '{$album->id}') WHERE album='{$album->slug}'");
					$wpdb->query("UPDATE {$this->images_table_name} SET file = REPLACE(file, '{$album->slug}', '{$album->id}') WHERE album='{$album->slug}'");
					$old_album_directory = $this->content_dir . '/' . $album->gallery . '/' . $album->slug;
					
					if( file_exists($old_album_directory) ) {					
						rename($old_album_directory, $this->content_dir . '/' . $album->gallery . '/' . $album->id);
					}						
				}
				
				//rename gallery folders update gallery path
	   			$galleries = $wpdb->get_results("SELECT id, slug FROM {$this->gallery_table_name}");
				foreach ( $galleries as $gallery ) {
					$wpdb->query("UPDATE {$this->images_table_name} SET thumbnail = REPLACE(thumbnail, '{$gallery->slug}', '{$gallery->id}') WHERE gallery='{$gallery->slug}'");
					$wpdb->query("UPDATE {$this->images_table_name} SET file = REPLACE(file, '{$gallery->slug}', '{$gallery->id}') WHERE gallery='{$gallery->slug}'");
					$old_gallery_directory = $this->content_dir . '/' . $gallery->slug;
					
					if( file_exists($old_gallery_directory) ) {					
						rename($old_gallery_directory, $this->content_dir . '/' . $gallery->id);
					}						
				}
	   			
	   			//change structure for gallery table
	   			$wpdb->query("ALTER TABLE {$this->gallery_table_name} CHANGE id ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
				$wpdb->query("ALTER TABLE {$this->gallery_table_name} ADD CONSTRAINT PRIMARY KEY(ID);");
				
				//change structure for album table
				$wpdb->query("ALTER TABLE {$this->album_table_name} CHANGE id ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
				$wpdb->query("ALTER TABLE {$this->album_table_name} ADD gallery_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';");
				$wpdb->query("ALTER TABLE {$this->album_table_name} ADD CONSTRAINT PRIMARY KEY(ID);");
				$wpdb->query("ALTER TABLE {$this->album_table_name} ADD CONSTRAINT fk_fg_gallery_id FOREIGN KEY (gallery_id) references {$this->gallery_table_name}(ID) on DELETE CASCADE;");
				
				//update rows in album table
				$galleries = $wpdb->get_results("SELECT ID, slug FROM {$this->gallery_table_name}");
				foreach ( $galleries as $gallery ) {
					$wpdb->update(
						$this->album_table_name,
						array('gallery_id' => $gallery->ID),
						array('gallery' => $gallery->slug),
						array('%d'),
						array('%s')
					);
				}
				
				//change structure for media table
				$wpdb->query("ALTER TABLE {$this->images_table_name} CHANGE id ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
				$wpdb->query("ALTER TABLE {$this->images_table_name} ADD album_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';");
				$wpdb->query("ALTER TABLE {$this->images_table_name} ADD CONSTRAINT PRIMARY KEY(ID);");
				$wpdb->query("ALTER TABLE {$this->images_table_name} ADD CONSTRAINT fk_fg_album_id FOREIGN KEY (album_id) references {$this->album_table_name}(ID) on DELETE CASCADE;");
				
				//update rows in media table
				$albums = $wpdb->get_results("SELECT ID, slug, gallery FROM {$this->album_table_name}");
				foreach ( $albums as $album ) {
					$wpdb->update(
						$this->images_table_name,
						array('album_id' => $album->ID),
						array('gallery' => $album->gallery, 'album' => $album->slug),
						array('%d'),
						array('%s', '%s')
					);
				}			
				
				//drop old columns
				$wpdb->query("ALTER TABLE {$this->gallery_table_name} DROP slug;");
				$wpdb->query("ALTER TABLE {$this->album_table_name} DROP slug, DROP gallery;");
				$wpdb->query("ALTER TABLE {$this->images_table_name} DROP gallery, DROP album;");
		   		
		   		update_option($this->version_field_name, '2.1.0');
		   		$this->upgrade();
	   		}
			
			//upgrade to V2.0.0
			if( get_option($this->version_field_name) == '1.2.5') {
				//update version in db
		   		update_option($this->version_field_name, '2.0.0');
		   		$this->upgrade();
			}
			
			//upgrade to V1.2.5
			if( get_option($this->version_field_name) == '1.2.4') {
				//update version in db
		   		update_option($this->version_field_name, '1.2.5');
		   		$this->upgrade();
			}
			
			//upgrade to V1.2.4
			if( get_option($this->version_field_name) == '1.2.3') {
				//update version in db
		   		update_option($this->version_field_name, '1.2.4');
		   		$this->upgrade();
			}
			
			if( get_option($this->version_field_name) < '1.2.3') {
				//UPGRADE TO 1.2.3
			
		   		//add new column to gallery table
		   		$this->wpdb->query( 'ALTER TABLE '.$this->gallery_table_name.' ADD COLUMN options TEXT' );
		   		
		   		//get all gallery slugs
		   		$gallery_slugs = $this->wpdb->get_results('SELECT slug FROM '.$this->gallery_table_name.'');
		   		//get options
		   		$options = get_option($this->options_field_name);
		   		
		   		//move options from options table to gallery table
		   		foreach($gallery_slugs as $gallery_slug) {
		   			$gallery_opts = empty($options[$gallery_slug->slug]) ? $this->default_options : $options[$gallery_slug->slug];
		   			$this->wpdb->update( $this->gallery_table_name, array('options' => serialize($gallery_opts) ), array('slug'=> $gallery_slug->slug), array('%s') );
		   		}
		   		
		   		//delete options from option tabale
		   		delete_option($this->options_field_name);
		   		
		   		//update version in db
		   		update_option($this->version_field_name, '1.2.3');
				
				$this->upgrade();
			}	   		      
		     		      
	   }
		       
	   //deletes a directory and is content 
	   private function delete_directory($dir) {
			$iterator = new RecursiveDirectoryIterator($dir);
			foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
			if ($file->isDir()) {
				@rmdir($file->getPathname());
			 } 
			 else {
			    @unlink($file->getPathname());
			 }
		   }
		   @rmdir($dir);
	   }
	   
	   //returns an error to the ajax handler
	   private function ajax_error_handler($errors, $error_response) {
		   if ( count ( $errors->get_error_codes() ) > 0 ) {
				$error_response->add( array( 'what' => 'errors', 'id' => $errors ) );
				$error_response->send();
				
				exit;
			}
	   }
	   
	   private function get_gallery_list_item($id, $title) {
	   	   return '<li><div id="' . $id . '" class="gallery-item clearfix"><span class="fg-title">' . stripslashes($title) . '</span><span class="fg-meta-bar"><a href="#" title="' . __('Add new album', 'radykal') . '" class="fg-add-album"></a><a href="#" title="' . __('Edit gallery title', 'radykal') . '" class="fg-edit fg-edit-gallery"></a><a href="#" title="' . __('Show Shortcode', 'radykal') . '" class="fg-show-shortcode"></a><a href="#" title="' . __('Delete gallery', 'radykal') . '" class="fg-delete fg-delete-gallery"></a></span></div><ul class="sub-menu">';
	   }
	   
	   private function get_album_list_item($id, $title) {
		   return '<li><div id="' . $id . '" class="clearfix"><span class="dragger"></span><span class="fg-title">' . stripslashes($title) . '</span><span class="fg-meta-bar"><a href="#" title="' . __('Edit album title', 'radykal') . '" class="fg-edit fg-edit-album"></a><a href="#" title="' . __('Show Shortcode', 'radykal') . '" class="fg-show-shortcode fg-album-shortcode"></a><a href="#" title="' . __('Delete album', 'radykal') . '" class="fg-delete"></a></span></div></li>';
	   }
	   
	   private function file_upload_error_message($error_code) {
		    switch ($error_code) {
		        case UPLOAD_ERR_INI_SIZE:
		            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		        case UPLOAD_ERR_FORM_SIZE:
		            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
		        case UPLOAD_ERR_PARTIAL:
		            return 'The uploaded file was only partially uploaded';
		        case UPLOAD_ERR_NO_FILE:
		            return 'No file was uploaded';
		        case UPLOAD_ERR_NO_TMP_DIR:
		            return 'Missing a temporary folder';
		        case UPLOAD_ERR_CANT_WRITE:
		            return 'Failed to write file to disk';
		        case UPLOAD_ERR_EXTENSION:
		            return 'File upload stopped by extension';
		        default:
		            return 'Unknown upload error';
		    }
		}
		
		public function check_options_availability($options) {
			foreach($this->default_options as $key => $value) {
				$options[$key] = $options[$key] === null ? $value : $options[$key];
			}
			return $options;
		}
		
		public function get_media_link($file, $thumbnail, $tn_width, $tn_height, $tn_zc, $title, $description) {
		
			global $blog_id;
		
			//check if multisite and path contains files string to get real file path
			if(is_multisite() && strpos($thumbnail, '/files/')) {
				$imageParts = explode('/files/', $thumbnail);
				$blog_details = get_blog_details(1);
				$thumbnail = $blog_details->siteurl . "/wp-content/blogs.dir/" .$blog_id . "/files/" . $imageParts[1];
			}
		
			return '<a href="'.$file.'" ><img src="'.plugins_url('/admin/timthumb.php', __FILE__).'?src='.urlencode($thumbnail).'&w='.$tn_width.'&h='.$tn_height.'&zc='.$tn_zc.'&q=100" title="'.strip_tags(stripslashes($title)).'" /><span>'.htmlspecialchars(str_replace('"', "'", stripslashes($description))).'</span></a>';
			
		}
		
		public function get_js_code($options, $selector, $selectAlbum) {
			
			if($options['lightbox'] == 'prettyphoto') {
				$lightbox_options = "theme: '" . $options['prettyphoto_theme'] . "', allow_resize: " . $options['prettyphoto_image_resize'] . " , overlay_gallery: " . $options['prettyphoto_overlay'] . ", autoplay_slideshow: " . $options['prettyphoto_slideshow'] . ", deeplinking: " . $options['prettyphoto_deeplinking'] . " ";
				if( !$options['prettyphoto_social_tools'] ) { $lightbox_options .= ", social_tools: ''"; }
			}
			else {
				$lightbox_options = "helpers: { media: {}, buttons: {position: '" . $options['fancybox_buttons_position'] . "'}, title: {type: '" . $options['fancybox_title_position'] . "'},thumbs: {position: '" . $options['fancybox_thumbs_position'] . "'} }, arrows: " . $options['fancybox_arrows'] . ", width: " . $options['fancybox_width'] . ", height: " . $options['fancybox_height'] . ", autoPlay: " . $options['fancybox_autoplay'] . ", loop: ". $options['fancybox_loop'] . " ";
			}
			
			//check for older themes - uniform
			if($options['dropdown_theme'] == 'default' || $options['dropdown_theme'] == 'aristo' || $options['dropdown_theme'] == 'agent')
			    $options['dropdown_theme'] = 'white';
			
			$js_code = "<script type='text/javascript'>
jQuery(document).ready(function(){
  jQuery('#".$selector."').fancygallery({
	  backgroundColor: '" . $options['background_color'] . "', 
	  titleColor: '" . $options['title_color'] . "', 
	  thumbWidth: " . $options['thumbnail_width'] . ", 
	  thumbHeight: " . $options['thumbnail_height'] . ",
	  thumbOpacity: " . $options['thumbnail_opacity'] . ", 
	  imagesPerPage: " . $options['thumbnails_per_page'] . ", 
	  titleHeight: " . $options['title_height'] . ", 
	  borderThickness: " . $options['border_thickness'] . ",
	  rowOffset: " . $options['row_offset'] . ", 
	  columnOffset: " . $options['column_offset'] . ", 
	  shadowOffset: " . $options['shadow_offset'] . ", 
	  textFadeDirection: '" . $options['text_fade_direction'] . "', 
	  shadowImage: '" . $options['shadow_image'] . "',
	  hoverImage: '" . $options['hover_image']. "',
	  hoverImageEffect: '" . $options['hover_image_effect'] . "',
	  navPosition: '" . $options['nav_position'] . "', 
	  selectAlbum: '" . $selectAlbum . "', 
	  dropdown: " . $options['dropdown'] . ", 
	  divider: " . $options['divider'] . ", 
	  showTitle: " . $options['show_title'] .", 
	  slideTitle: " . $options['slide_title'] . ",
	  inverseHoverEffect: ". $options['inverse_hover_effect'] .",
	  secondThumbnail: " . $options['second_thumbnail'] . ",
	  timthumbUrl: '".plugins_url('/admin/timthumb.php', __FILE__)."',  
	  timthumbParameters: '" . $options['timthumb_parameters'] . "', 
	  allMediasSelector: '" . $options['all_medias_selector'] . "', 
	  albumSelection: '" . $options['album_selection'] . "',
	  navigation: '" . $options['navigation'] . "', 
	  navStyle: '" . $options['nav_style'] . "', 
	  navAlignment: '" . $options['nav_alignment'] . "', 
	  navPreviousText: '" . $options['nav_previous_text'] . "',
	  navNextText: '" . $options['nav_next_text'] . "', 
	  navBackText: '" . $options['nav_back_text'] . "', 
	  thumbnailTransition: '" . $options['thumbnail_transition'] . "', 
	  lightbox: '" . $options['lightbox'] . "', 
	  boxOptions: { ". $lightbox_options ." },
	  columns: " . $options['columns'] . ",
	  dropdownTheme: '". $options['dropdown_theme'] ."',
	  showOnlyFirstThumbnail: " . $options['show_only_first_thumbnail'] . ",
	  mediaText: '" . $options['media_label'] . "'
  });
});
</script>";
			
			return $js_code;

		}
	}
}

//init Fancy Gallery
if(class_exists('FancyGallery')) {
	$fg = new FancyGallery();
}

?>