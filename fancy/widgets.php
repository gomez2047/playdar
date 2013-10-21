<?php

/*
+ This widget creates an album list of a gallery. The gallery needs to be added to a published post or page first!
*
*/
class FancyGalleryListWidget extends WP_Widget {
	
	function FancyGalleryListWidget() {
		
		// widget actual processes
		$widget_opts = array (  
            'classname' => '',  
            'description' => __('Creates a list of all albums from a gallery. The links refers to the page where the gallery is included and will open the selected album.', 'radykal') 
        ); 
		
		parent::WP_Widget('fancy-gallery-list-widget', __('Fancy Gallery - List albums of gallery', 'radykal'), $widget_opts);
		
	}
	
	function widget($args, $instance) {
		
		// outputs the content of the widget
		extract($args);
        $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $current_gallery = $instance['gallery'];
        
		global $wpdb;
		
		echo $before_widget;
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
	
		$albumTableName = $wpdb->prefix . "fg_album";
		$albums = $wpdb->get_results("SELECT * FROM $albumTableName WHERE gallery_id='$current_gallery' ORDER BY sort ASC");
		
		$postid = $wpdb->get_var( 'SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[fancygallery id=\"'.$current_gallery.'\"%" AND post_status = "publish"' );
				
		//check if gallery has been included in a published post or page
		if( empty($postid) ) {
			echo __('<p>There is no published post that has this gallery included! Please add the gallery in a published post or page first!</p>', 'radykal');
			return;
		}
		
		//create album menu
		echo "<ul>";
		foreach($albums as $album) {
			echo "<li><a href='" . get_permalink($postid) . "?album=".urlencode($album->title)."'>" .  $album->title . "</a></li>";
		}
		echo "</ul>";
		
		
		echo $after_widget;
		
	}

	function form($instance) {
			
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		$current_gallery = esc_attr($instance['gallery']);
		
		//select all galleries
		global $wpdb;
		$galleryTableName = $wpdb->prefix . "fg_gallery";
		$galleries = $wpdb->get_results("SELECT ID, title FROM $galleryTableName");
		?>
		<!-- Widget Title -->
		<p><?php _e('Title:', 'radykal'); ?><br />
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" /></p>
		
		<p><?php _e('Select a gallery:', 'radykal'); ?></p>
		<select name="<?php echo $this->get_field_name('gallery'); ?>" value="<?php echo $current_gallery; ?>">
		<?php foreach($galleries as $gallery): ?>	
		<option value="<?php echo $gallery->ID; ?>" <?php echo selected( $gallery->ID, $current_gallery ); ?>><?php echo $gallery->title; ?></option>
		<?php endforeach; ?>
		</select>
		
		<?php		
	}

	function update($new_instance, $old_instance) {
		
		// processes widget options to be saved
		$instance = $old_instance;
	    $instance['title'] = strip_tags($new_instance['title']);
	    $instance['gallery'] = strip_tags($new_instance['gallery']);
        return $instance;
		
	}

}



/*
* This widget displays the media of an album.
*
*/
class FancyGalleryAlbumWidget extends WP_Widget {
	
	function FancyGalleryAlbumWidget() {
		
		// widget actual processes
		$widget_opts = array (  
            'classname' => '',  
            'description' => __('Displays the media of an album. You should create an own gallery for this album and adjust the options for the gallery.', 'radykal') 
        ); 
		
		parent::WP_Widget('fancy-gallery-album-widget', __('Fancy Gallery - Album Display', 'radykal'), $widget_opts);
		
	}
	
	function widget($args, $instance) {
	
		if(class_exists('FancyGallery')) {
			$fg = new FancyGallery();
		}
		
		// outputs the content of the widget
		extract($args);
        $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $current_album = $instance['album'];
        $gallery_options = $instance['gallery_options'];
        
		global $wpdb;
		
		echo $before_widget;
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		
		$mediaTableName = $wpdb->prefix . "fg_media";
		$album_files = $wpdb->get_results("SELECT * FROM $mediaTableName WHERE album_id='$current_album' ORDER BY sort ASC");
		
		$gallery_table_name = $wpdb->prefix . 'fg_gallery';
		$options = $wpdb->get_results("SELECT options FROM $gallery_table_name WHERE ID='$gallery_options'");
		if($options) {
		
			$options = unserialize($options[0]->options);
		
			//html output
			$output = '<div id="fancygallery-' . $current_album .'" class="fg-panel">';			
	
			$output .= '<div title="' . stripslashes($current_album ) . '">';
			
			foreach($album_files as $album_file) {
				$output .= '<a href="'. $album_file->file .'" ><img src="' . plugins_url('/admin/timthumb.php', __FILE__) . '?src='. urlencode($album_file->thumbnail) . '&w='.$options['thumbnail_width'].'&h='.$options['thumbnail_height'].'&zc='.$options['thumbnail_zc'].'&q=100" title="'.stripslashes($album_file->title).'" /><span>'.strip_tags($album_file->description).'</span></a>';
			}
				
			$output .= "</div></div>";
			
			$options = $fg->check_options_availability($options);
			$output .= $fg->get_js_code($options, 'fancygallery-' . $current_album, '');
			
		}
		else {
			$output = __('<p>Please choice options for this gallery in the widget admin!</p>', 'radykal');
		}
		
		
		echo $output;

		echo $after_widget;
		
	}

	function form($instance) {
			
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		$current_album = esc_attr($instance['album']);
		$gallery_options = esc_attr($instance['gallery_options']);
		
		//select all galleries
		global $wpdb;
		$albumTableName = $wpdb->prefix . "fg_album";
		$albums = $wpdb->get_results("SELECT ID, title FROM $albumTableName ORDER BY id ASC");
		$galleryTableName = $wpdb->prefix . "fg_gallery";
		$galleries = $wpdb->get_results("SELECT ID, title FROM $galleryTableName ORDER BY title ASC");
		?>
		
		<!-- Widget Title -->
		<p><?php _e('Title:', 'radykal'); ?><br />
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" />
		</p>
		
		<!-- Album selection -->
		<p><?php _e('Select the album, you would like to display:', 'radykal'); ?><br />
		<select name="<?php echo $this->get_field_name('album'); ?>" value="<?php echo $current_album; ?>">
		<?php foreach($albums as $album) :?>
		<option value="<?php echo $album->ID; ?>" <?php selected( $album->ID, $current_album ); ?> ><?php echo $album->title; ?></option>
		<?php endforeach; ?>
		</select>
		</p>
		
		<!-- Gallery selection -->
		<p><?php _e('Take options from gallery:', 'radykal'); ?><br />
		<select name="<?php echo $this->get_field_name('gallery_options'); ?>" value="<?php echo $gallery_options; ?>">
		<?php foreach($galleries as $gallery) :?>
		<option value="<?php echo $gallery->ID; ?>" <?php selected( $gallery->ID, $gallery_options ); ?> ><?php echo $gallery->title; ?></option>
		<?php endforeach; ?>
		</select>
		</p>
		
		<?php
				
	}

	function update($new_instance, $old_instance) {
		
		// processes widget options to be saved
		$instance = $old_instance;
	    $instance['title'] = strip_tags($new_instance['title']);
	    $instance['album'] = strip_tags($new_instance['album']);
	    $instance['gallery_options'] = strip_tags($new_instance['gallery_options']);
        return $instance;
		
	}

}


/*
* This widget displays latest or random media.
*
*/
class FancyGalleryMediaWidget extends WP_Widget {
	
	function FancyGalleryMediaWidget() {
		
		// widget actual processes
		$widget_opts = array (  
            'classname' => '',  
            'description' => __('Displays latest or random media.', 'radykal') 
        ); 
		
		parent::WP_Widget('fancy-gallery-media-widget', __('Fancy Gallery - Media Display', 'radykal'), $widget_opts);
		
	}
	
	function widget($args, $instance) {
	
		if(class_exists('FancyGallery')) {
			$fg = new FancyGallery();
		}
		
		// outputs the content of the widget
		extract($args);
        $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $gallery_options = $instance['gallery_options'];
        $number = isset($instance['number']) ? $instance['number'] : 5;
        $selection = isset( $instance['selection'] ) ? $instance['selection'] : 'latest';
        
		global $wpdb;
		
		echo $before_widget;
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		
		$mediaTableName = $wpdb->prefix . "fg_media";
		if($selection == 'latest') {
			$album_files = $wpdb->get_results("SELECT * FROM $mediaTableName ORDER BY ID DESC LIMIT $number");
		}
		else if($selection == 'random') {
			$album_files = $wpdb->get_results("SELECT * FROM $mediaTableName ORDER BY ID");
			shuffle($album_files);
		}
		
		$gallery_table_name = $wpdb->prefix . 'fg_gallery';
		$options = $wpdb->get_results("SELECT options FROM $gallery_table_name WHERE ID='$gallery_options'");
		$options = unserialize($options[0]->options);
		
		//html output
		$output = '';
		$output .= '<div id="fancygallery-media-widget-'.$selection.'" class="fg-panel">';			

		$output .= '<div title="Media Widget">';
		
		for($i=0; $i < $number; ++$i)  {
			$album_file = $album_files[$i];
			if($album_file) {
				$output .= '<a href="'. $album_file->file .'" ><img src="' . plugins_url('/admin/timthumb.php', __FILE__) . '?src='. urlencode($album_file->thumbnail) . '&w='.$options['thumbnail_width'].'&h='.$options['thumbnail_height'].'&zc='.$options['thumbnail_zc'].'&q=100" title="'.stripslashes($album_file->title).'" /><span>'.strip_tags($album_file->description).'</span></a>';
			}
		}
			
		$output .= "</div></div>";
		
		$options = $fg->check_options_availability($options);
		$output .= $fg->get_js_code($options, 'fancygallery-media-widget-'.$selection.'', '');
		
		echo $output;

		echo $after_widget;
		
	}

	function form($instance) {
			
		// outputs the options form on admin
		$title = esc_attr($instance['title']);
		$gallery_options = esc_attr($instance['gallery_options']);
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$selection = isset( $instance['selection'] ) ? $instance['selection'] : 'latest';
		
		//select all galleries
		global $wpdb;
		$galleryTableName = $wpdb->prefix . "fg_gallery";
		$galleries = $wpdb->get_results("SELECT ID, title FROM $galleryTableName ORDER BY title ASC");
		?>
		
		<!-- Widget Title -->
		<p><?php _e('Title:'); ?><br />
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" />
		</p>
		
		<!-- Gallery selection -->
		<p><?php _e('Take options from gallery:', 'radykal'); ?><br />
		<select name="<?php echo $this->get_field_name('gallery_options'); ?>" value="<?php echo $gallery_options; ?>">
		<?php foreach($galleries as $gallery) :?>
		<option value="<?php echo $gallery->ID; ?>" <?php selected( $gallery->ID, $gallery_options ); ?> ><?php echo $gallery->title; ?></option>
		<?php endforeach; ?>
		</select>
		</p>
		
		<!-- Media Amount -->
		<p><?php _e('Number of media to show:', 'radykal'); ?><br />
		<input type="text" size="5" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" />
		</p>
		
		<!-- Media Amount -->
		<p>
		<input type="radio" name="<?php echo $this->get_field_name('selection'); ?>" value="latest" <?php checked($selection, 'latest') ?>> <?php _e('Latest', 'radykal'); ?><br>
		<input type="radio" name="<?php echo $this->get_field_name('selection'); ?>" value="random" <?php checked($selection, 'random') ?>> <?php _e('Random', 'radykal'); ?>
		</p>
		
		<?php
				
	}

	function update($new_instance, $old_instance) {
		
		// processes widget options to be saved
		$instance = $old_instance;
	    $instance['title'] = strip_tags($new_instance['title']);
	    $instance['gallery_options'] = strip_tags($new_instance['gallery_options']);
	    $instance['number'] = strip_tags($new_instance['number']);
	    $instance['selection'] = strip_tags($new_instance['selection']);
        return $instance;
		
	}

}



?>