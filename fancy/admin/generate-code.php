<div class="wrap">
  <h2><?php _e('Your HTML Code', 'radykal'); ?></h2>
  <?php
    $gallery = trim($_POST['selected_gallery']);
		
    //get albums of the gallery corresponding to the ID
	$albums = $this->wpdb->get_results("SELECT * FROM {$this->album_table_name} WHERE gallery_id='$gallery' ORDER BY sort ASC");
	
  ?>
  <h3 style="margin-bottom: 0px;">Copy this code and paste in your head tag:</h3>
  <textarea cols="170" rows="20">
<?php 
echo htmlentities("<!-- Style Sheets -->");
echo "\n";
if($options['lightbox'] == 'prettyphoto') {
	echo htmlentities("<link rel='stylesheet' href='" . plugins_url('/radykal-fancy-gallery/prettyphoto/css/prettyPhoto.css') . "' />");
	echo "\n";
}
else {
	echo htmlentities("<link rel='stylesheet' href='" . plugins_url('/radykal-fancy-gallery/fancybox/jquery.fancybox.css') . "' />");
	echo "\n";
	if($options['fancybox_buttons_position'] != 'none') {
		echo htmlentities("<link rel='stylesheet' href='" . plugins_url('/radykal-fancy-gallery/fancybox/helpers/jquery.fancybox-buttons.css') . "' />");
		echo "\n";
	}
	if($options['fancybox_thumbs_position'] != 'none') {
		echo htmlentities("<link rel='stylesheet' href='" . plugins_url('/radykal-fancy-gallery/fancybox/helpers/jquery.fancybox-thumbs.css') . "' />");
		echo "\n";
	}
}

echo htmlentities("<link rel='stylesheet' href='" . plugins_url('/radykal-fancy-gallery/css/jquery.fancygallery.css') . "' />");
echo "\n";

echo htmlentities("<!-- Javascript Files -->");
echo "\n";
echo htmlentities("<script src='http://code.jquery.com/jquery-latest.min.js' type='text/javascript'></script>");
echo "\n";
echo htmlentities("<script src='". plugins_url('/radykal-fancy-gallery/js/jquery.fancygallery.min.js') ."' type='text/javascript'></script>");
echo "\n";
if($options['lightbox'] == 'prettyphoto') {
	echo htmlentities("<script src='". plugins_url('/radykal-fancy-gallery/prettyphoto/jquery.prettyPhoto.js') ."' type='text/javascript'></script>");
	echo "\n";
}
else {
	echo htmlentities("<script src='". plugins_url('/radykal-fancy-gallery/fancybox/jquery.fancybox.pack.js') ."' type='text/javascript'></script>");
	echo "\n";
	echo htmlentities("<script src='". plugins_url('/radykal-fancy-gallery/fancybox/helpers/jquery.fancybox-media.js') ."' type='text/javascript'></script>");
	echo "\n";
	if($options['fancybox_buttons_position'] != 'none') {
		echo htmlentities("<script src='" . plugins_url('/radykal-fancy-gallery/fancybox/helpers/jquery.fancybox-buttons.js') . "' type='text/javascript'></script>");
		echo "\n";
	}
	if($options['fancybox_thumbs_position'] != 'none') {
		echo htmlentities("<script src='" . plugins_url('/radykal-fancy-gallery/fancybox/helpers/jquery.fancybox-thumbs.js') . "' type='text/javascript'></script>");
		echo "\n";
	}
}

echo "\n";

echo htmlentities("<!-- Calling the plugin -->");
echo "\n";	
  
echo $this->get_js_code($options, 'fancygallery-' . $gallery, '');
  ?>
  </textarea>
  <h3 style="margin-bottom: 0px;"><?php _e('Copy this code and paste it in your body tag:', 'radykal'); ?></h3>
  <textarea cols="170" rows="30">
<?php
echo htmlentities("<div id='fancygallery-$gallery'>\n");
		
foreach($albums as $album) {
	echo htmlentities("  <div id='{$album->ID}' title='{$album->title}'>\n");
	
	$album_files = $this->wpdb->get_results("SELECT * FROM {$this->images_table_name} WHERE album_id='{$album->ID}' ORDER BY sort ASC");
	foreach($album_files as $album_file) {
		$thumbnail_path =  $album_file->thumbnail;
				
		//check if multisite and path contains files string to get real file path
		if(is_multisite() && strpos($thumbnail_path, '/files/')) {
			$imageParts = explode('/files/', $thumbnail_path);
			$blog_details = get_blog_details(1);
			$thumbnail_path = $blog_details->siteurl . "/wp-content/blogs.dir/" .$blog_id . "/files/" . $imageParts[1];
		}
		$thumbnail_src = plugins_url('/timthumb.php', __FILE__).'?src='.urlencode($thumbnail_path).'&w='.$options['thumbnail_width'].'&h='.$options['thumbnail_height'].'&zc='.$options['thumbnail_zc'].'&q=100';
		echo htmlentities('    <a href="' . $album_file->file . '" >
      <img src="' . $thumbnail_src . '" title="' . strip_tags(stripslashes($album_file->title)) . '" />
      <span>' . htmlspecialchars(stripslashes($album_file->description)) . '</span>
    </a>');
    echo "\n";
	}
		
	echo htmlentities("  </div>\n");
}
echo htmlentities("</div>");
  ?>
  </textarea>
</div>