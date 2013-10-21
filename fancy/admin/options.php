<div class="wrap <?php if( !current_user_can('manage_options') && FancyGallery::DEMO ){ echo " fg-disable-links"; } ?>" id="fg-options-admin">
  <div class="icon32" id="icon-options-general"><br/></div>
  <h2><?php _e('Options', 'radykal'); ?></h2>
  
  <div id="fg-content" class="clearfix">
  
	  <div id="accordion-wrapper">
		<h3><?php _e('Galleries', 'radykal'); ?></h3>
		
		<ul id="galleries-accordion">
		<?php 
		$galleries = $this->wpdb->get_results("SELECT * FROM {$this->gallery_table_name} ORDER BY title");
		foreach($galleries as $gallery) {
			
			echo '<li><div id="'.$gallery->ID.'" class="clearfix"><span class="fg-title">'.$gallery->title.'</span><span class="fg-meta-bar"><a href="#" class="fg-paste-options" title="'. __('Paste current showing options to this gallery', 'radykal').'"></a></span></div></li>';
		}
		?>
		</ul>
	  </div>
	  
	  <!-- Right content starts -->
	  <div id="fg-right-col">
	  	<form action="" method="post" id="fg-options-form">
	  		<input type="hidden" value="<?php echo isset($_POST['selected_gallery']) ? $_POST['selected_gallery'] : $galleries[0]->ID; ?>" name="selected_gallery" />
	  		<input type="hidden" value="" name="overwrite_gallery" />
	  		<?php require_once( dirname(__FILE__)."/options-form.php" ); ?>
		  
	  		<input type="submit" name="fg_opts_save" value="<?php _e('Save Changes', 'radykal'); ?>" title="<?php _e('Save Options Changes', 'radykal'); ?>" class="fg-button fg-primary-button"  <?php disabled(!current_user_can('manage_options') && FancyGallery::DEMO,  true); ?> />
	  		<input type="submit" name="fg_opts_reset" value="<?php _e('Reset Options', 'radykal'); ?>" title="<?php _e('Reset Options for this gallery', 'radykal'); ?>" class="fg-button fg-secondary-button" <?php disabled(!current_user_can('manage_options') && FancyGallery::DEMO,  true); ?>  />
	  		<input type="submit" name="fg_generate_code" value="<?php _e('Generate Code', 'radykal'); ?>" title="<?php _e('Generate code for external usage', 'radykal'); ?>" class="fg-button fg-secondary-button" />
	  		
		</form>
	  </div>
	  <!-- Right content ends -->
	  
  </div>
</div>