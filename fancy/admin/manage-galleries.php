<div class="wrap <?php if( !current_user_can('manage_options') && FancyGallery::DEMO ){ echo " fg-disable-links"; } ?>">

  <div class="icon32" id="icon-upload"><br/></div>
  <h2><?php _e('Manage galleries', 'radykal'); ?></h2>
  
  <div id="fg-content" class="clearfix">
  
	  <div id="accordion-wrapper">
	  
		<h3><?php _e('Galleries', 'radykal'); ?></h3>
		
		<input type="text" name="gallery_title" class="widefat" id="gallery-title" />
		<a href="#" id="fg-add-gallery" class="fg-button fg-primary-button"><?php _e('Add Gallery', 'radykal'); ?></a>
		
		<ul id="galleries-accordion">
		<?php 
		$galleries = $this->wpdb->get_results("SELECT * FROM {$this->gallery_table_name} ORDER BY title");
		foreach($galleries as $gallery) {
			
			echo $this->get_gallery_list_item($gallery->ID, $gallery->title);
			
			 $album_results = $this->wpdb->get_results("SELECT * FROM {$this->album_table_name} WHERE gallery_id='{$gallery->ID}' ORDER BY sort ASC");
			 foreach($album_results as $album_result) {
				 echo $this->get_album_list_item($album_result->ID, $album_result->title);
			 }
			
			echo "</ul></li>";
		}
		?>
		</ul>
		
		<label>Shortcode:</label>
		<input type="text" id="shortcode-output" value="" class="widefat" readonly="readyonly" />
		<br /><br /><br /><br />
		<a href="http://radykal.de/codecanyon/docs/fg_wp/" target="_blank"><?php _e('Get instructions and hints in the documentation!', 'radykal'); ?></a>
		
	  </div>
	  
	  <div id="fg-right-col">
	  	<div id="media-buttons">
	  	
	  		<div style="float: left;">
	  			<div id="image-upload-form">
		  			<form>
		  				<a href="#" title="" id="upload-images-button" class="upload-button"><?php _e('Upload Images', 'radykal'); ?><span title='<?php _e('Multiple image selection is only supported in following browsers: <strong>Firefox 3.6+, Safari 5+, Google Chrome and Opera 11+.</strong><br /> Use CTRL or SHIFT for selecting multiple images. Maximum upload size for every image is 1MB.', 'radykal'); ?>' class="fg-tooltip"></span></a>
				    	<input type="file" name="files[]" multiple>
				    </form>
	  			</div>
	  			
	  			<a href="#" title="" id="upload-media-button" class="upload-button"><?php _e('Upload other media', 'radykal'); ?><span title='<?php _e('You can only upload one media after another. This could be a Video(Quicktime, Youtube, Vimeo or Flash), an external site and more. <strong>Check out the documentation to get an overview which media types are supported in the different lightboxes!</strong>', 'radykal'); ?>' class="fg-tooltip"></span></a>
	  			<div class="clear" style="margin-top: 40px;">
	  				<input type="checkbox" id="titles-from-filenames" />
	  				<label><strong><?php _e('Get titles from filenames', 'radykal'); ?></strong></label>
	  			</div>
	  			
	  		</div>
	  		
	  		<div style="float: right;">
	  			<a href="#" id="update-media" class="fg-button fg-primary-button"><?php _e('Save Changes', 'radykal'); ?></a>
		  		<a href="#" id="select-all" class="fg-button fg-secondary-button" title="<?php _e('Select all media', 'radykal'); ?>" ><?php _e('Select all', 'radykal'); ?></a>
			  	<a href="#" id="deselect-all" class="fg-button fg-secondary-button" title="<?php _e('Deselect all media', 'radykal'); ?>" ><?php _e('Deselect all', 'radykal'); ?></a>
			  	<a href="#" id="delete-files" class="fg-button fg-secondary-button" title="<?php _e('Delete selected media', 'radykal'); ?>"><?php _e('Delete', 'radykal'); ?></a>
	  		</div>
	  			  		
	  	</div>
	  	
	  	<div id="fg-notification"></div>
	  	
	  	<div id="fg-alert">
		  	<ul></ul>
		  	<a href="#">&times;</a>
	  	</div>
	  		  	
		<ol id="mediaList" class="clearfix"></ol>
		
	  </div>
	  
  </div>
     
</div>