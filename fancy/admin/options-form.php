<div class="options-container">
  <table cellspacing="0">
  	<thead>
	  	<tr>
	  		<th width="170"><?php _e('Option', 'radykal'); ?></th>
	  		<th style="width: 200px;"><?php _e('Value', 'radykal'); ?></th>
	  		<th><?php _e('Explanation', 'radykal'); ?></th>
	  	</tr>
  	</thead>
  	<tbody>
	    <tr>
	      <td><?php _e('Background Color', 'radykal'); ?></td>
	      <td>
	         <input type="text" name="background_color" class="colorpicker" value="<?php echo $options['background_color']; ?>" />
	      </td>
	      <td><?php _e('The color for the background of the thumbnails.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Title Color', 'radykal'); ?></td>
	      <td>
	        <input type="text" name="title_color" class="colorpicker" value="<?php echo $options['title_color']; ?>" />
	      </td>
	      <td><?php _e('The color for the titles, that appears under the thumbnail.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail Width', 'radykal'); ?></td>
	      <td><input type="text" name="thumbnail_width" size="3" maxlength="5" value="<?php echo $options['thumbnail_width']; ?>" /></td>
	      <td><?php _e('The max. width for every thumbnail.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail Height', 'radykal'); ?></td>
	      <td><input type="text" name="thumbnail_height" size="3" maxlength="5" value="<?php echo $options['thumbnail_height']; ?>" /></td>
	      <td><?php _e('The max. height for every thumbnail.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail Opacity', 'radykal'); ?></td>
	      <td><input type="text" name="thumbnail_opacity" size="3" maxlength="3" value="<?php echo $options['thumbnail_opacity']; ?>" /></td>
	      <td><?php _e('A value between 0-1, e.g. 0.4', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnails per Page', 'radykal'); ?></td>
	      <td><input type="text" name="thumbnails_per_page" size="3" maxlength="5" value="<?php echo $options['thumbnails_per_page']; ?>" /></td>
	      <td><?php _e('Set value to 0 to show all images of album at once.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Title Height', 'radykal'); ?></td>
	      <td><input type="text" name="title_height" size="3" maxlength="3" value="<?php echo $options['title_height']; ?>" /></td>
	      <td><?php _e('the height of the layer for the title.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Border thickness', 'radykal'); ?></td>
	      <td><input type="text" name="border_thickness" size="3" maxlength="2" value="<?php echo $options['border_thickness']; ?>" /></td>
	      <td><?php _e('the thickness of the border around each thumbnail container.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Row Offset', 'radykal'); ?></td>
	      <td><input type="text" name="row_offset" size="3" maxlength="3" value="<?php echo $options['row_offset']; ?>" /></td>
	      <td><?php _e('the offset between each thumbnail row.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Column Offset', 'radykal'); ?></td>
	      <td><input type="text" name="column_offset" size="3" maxlength="3" value="<?php echo $options['column_offset']; ?>" /></td>
	      <td><?php _e('the offset between each thumbnail column.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Shadow Offset', 'radykal'); ?></td>
	      <td><input type="text" name="shadow_offset" size="3" maxlength="3" value="<?php echo $options['shadow_offset']; ?>" /></td>
	      <td><?php _e('You can use negative values as well.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Shadow Image', 'radykal'); ?></td>
	      <td class="clearfix">
	      	<a href="#" id="uploadShadowImage" class="fg-button fg-primary-button image-upload"><?php _e('Upload', 'radykal'); ?></a>
	      	<a href="#" class="fg-button fg-secondary-button remove-image"><?php _e('Remove', 'radykal'); ?></a>
	      	<span class="upload-check"></span>
	      	<input type="hidden" name="shadow_image" value="<?php echo $options['shadow_image']; ?>" />
	      </td>
	      <td>
	      	<?php _e('Upload a shadow png that appears under the thumbnail box.', 'radykal'); ?>
	      </td>
	    </tr>
	    <tr>
	      <td><?php _e('Hover Image', 'radykal'); ?></td>
	      <td class="clearfix">
	      	<a href="#" id="uploadHoverImage" class="fg-button fg-primary-button image-upload"><?php _e('Upload', 'radykal'); ?></a>
	      	<a href="#" class="fg-button fg-secondary-button remove-image"><?php _e('Remove', 'radykal'); ?></a>
	      	<span class="upload-check"></span>
	      	<input type="hidden" name="hover_image" value="<?php echo $options['hover_image']; ?>" />
	      </td>
	      <td>
		      <?php _e('Upload a hover icon png that appears over the thumbnail when moving the mouse over it.', 'radykal'); ?>
	      </td>
	    </tr>
	    <tr>
	      <td><?php _e('Hover Image Effect', 'radykal'); ?></td>
	      <td>
	        <select name="hover_image_effect">
	          <option value="fade" <?php selected($options['hover_image_effect'], "fade"); ?> ><?php _e('Fade', 'radykal'); ?></option>
	          <option value="l2r"<?php selected($options['hover_image_effect'], "l2r"); ?> ><?php _e('Left to Right', 'radykal'); ?></option>
	          <option value="r2l"<?php selected($options['hover_image_effect'], "r2l"); ?> ><?php _e('Right to Left', 'radykal'); ?></option>
	          <option value="t2b" <?php selected($options['hover_image_effect'], "t2b"); ?> ><?php _e('Top to Bottom', 'radykal'); ?></option>
	          <option value="b2t" <?php selected($options['hover_image_effect'], "b2t"); ?> ><?php _e('Bottom to Top', 'radykal'); ?></option>
	        </select>
	      </td>
	      <td><?php _e('Select an effect for the hover image.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail Scale Mode', 'radykal'); ?></td>
	      <td>
	      <?php
			if($_GET['page'] == 'fancy-gallery-generator') {
				echo '<select name="thumbnail_scalemode">
					  <option value="stretch" '.  selected($options['thumbnail_scalemode'], "stretch", false) .'>'.__('Stretch').'</option>
					  <option value="prop" '.selected($options['thumbnail_scalemode'], "prop", false) .'>'.__('Proportional').'</option>
					  <option value="crop" '. selected($options['thumbnail_scalemode'], "crop", false) .'>'.__('Crop').'</option>
					</select>';
			}
			else {
				echo '<select name="thumbnail_zc">
					  <option value="0" '. selected($options['thumbnail_zc'], 0, false) .'>'.__('No Cropping').'</option>
					  <option value="1" '. selected($options['thumbnail_zc'], 1, false) .'>'.__('Best Fit (recommend)').'</option>
					  <option value="2" '. selected($options['thumbnail_zc'], 2, false) .'>'.__('Proportional (gaps)').'</option>
					  <option value="3" '. selected($options['thumbnail_zc'], 3, false) .'>'.__('Proportional (no gaps)').'</option>
					</select>';
			}
		  ?>
	      </td>
	      <td><?php _e('Select a scale mode how the thumbnails should be scaled.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Text Fade Direction', 'radykal'); ?></td>
	      <td>
	        <select name="text_fade_direction">
	          <option value="normal" <?php selected($options['text_fade_direction'], "normal"); ?>><?php _e('Normal', 'radykal'); ?></option>
	          <option value="reverse" <?php selected($options['text_fade_direction'], "reverse"); ?>><?php _e('Reverse', 'radykal'); ?></option>
	          <option value="random" <?php selected($options['text_fade_direction'], "random"); ?>><?php _e('Random', 'radykal'); ?></option>
	        </select>
	      </td>
	      <td><?php _e('Select the direction for the letters fading of the title.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Dropdown theme', 'radykal'); ?></td>
	      <td>
	        <select name="dropdown_theme">
	          <option value="white" <?php selected($options['dropdown_theme'], "white"); ?>><?php _e('White', 'radykal'); ?></option>
	          <option value="black" <?php selected($options['dropdown_theme'], "black"); ?>><?php _e('Black', 'radykal'); ?></option>
	          <option value="blue" <?php selected($options['dropdown_theme'], "blue"); ?>><?php _e('Blue', 'radykal'); ?></option>
	        </select>
	      </td>
	      <td><?php _e('Select a theme for the dropdown menu.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Show Dropdown', 'radykal'); ?></td>
	      <td><input type="checkbox" name="dropdown" value="1" <?php checked($options['dropdown'], 1) ?> /></td>
	      <td><?php _e('Hide or show the dropdown menu.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Show Divider', 'radykal'); ?></td>
	      <td><input type="checkbox" name="divider" value="1" <?php checked($options['divider'], 1) ?> /></td>
	      <td><?php _e('Hide or show the line between thumbnails and controllers.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Show Title', 'radykal'); ?></td>
	      <td><input type="checkbox" name="show_title" value="1" <?php checked($options['show_title'], 1) ?> /></td>
	      <td><?php _e('Show or hide the title.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Slide Title', 'radykal'); ?></td>
	      <td><input type="checkbox" name="slide_title" value="1" <?php checked($options['slide_title'], 1) ?> /></td>
	      <td><?php _e('Enable or disable the slide of the Title.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Inverse Hover Effect', 'radykal'); ?></td>
	      <td><input type="checkbox" name="inverse_hover_effect" value="1" <?php checked($options['inverse_hover_effect'], 1) ?> /></td>
	      <td><?php _e('All images are clear and by hover the thumbnail will get the thumbnail opacity.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Second Thumbnail', 'radykal'); ?></td>
	      <td><input type="checkbox" name="second_thumbnail" value="1" <?php checked($options['second_thumbnail'], 1) ?> /></td>
	      <td><?php _e('Creates a second thumbnail over the origin thumbnail, that can be manipulated with the Timthumb Parameters.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Timthumb Parameters for the second thumbnail', 'radykal'); ?></td>
	      <td><input type="text" name="timthumb_parameters" class="widefat" value="<?php echo $options['timthumb_parameters']; ?>" /></td>
	      <td><?php _e('The timthumb parameters to manipulate the second thumbnails, <a href="http://www.binarymoon.co.uk/2010/08/timthumb-image-filters/" target="_blank">learn more about the timthumb image filters</a>. By default it turns all second thumbnails into shades of grey.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('All media selector', 'radykal'); ?></td>
	      <td><input type="text" name="all_medias_selector" class="widefat" value="<?php echo $options['all_medias_selector']; ?>" /></td>
	      <td><?php _e('Set the text for the button to show all media of a gallery. Leave it empty, when you do not need this button.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Album Selection', 'radykal'); ?></td>
	      <td>
	        <select name="album_selection">
	          <option value="dropdown" <?php selected($options['album_selection'], "dropdown"); ?>>Dropdown</option>
	          <option value="thumbnails" <?php selected($options['album_selection'], "thumbnails"); ?>>Thumbnails</option>
	          <option value="menu" <?php selected($options['album_selection'], "menu"); ?>>Menu</option>
	        </select>
	      </td>
	      <td><?php _e('The selection type for the albums.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation', 'radykal'); ?></td>
	      <td>
	        <select name="navigation">
	          <option value="arrows" <?php selected($options['navigation'], "arrows"); ?>>Arrows</option>
	          <option value="pagination" <?php selected($options['navigation'], "pagination"); ?>>Pagination</option>
	          <option value="dots" <?php selected($options['navigation'], "dots"); ?>>Dots</option>
	        </select>
	      </td>
	      <td><?php _e('The navigation type.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation Position', 'radykal'); ?></td>
	      <td>
	        <select name="nav_position">
	          <option value="top" <?php selected($options['nav_position'], "top"); ?>><?php _e('Top', 'radykal'); ?></option>
	          <option value="bottom" <?php selected($options['nav_position'], "bottom"); ?>><?php _e('Bottom', 'radykal'); ?></option>
	        </select>
	      </td>
	      <td><?php _e('Select the position of the navigation.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation Style', 'radykal'); ?></td>
	      <td><input type="text" name="nav_style" class="widefat" value="<?php echo $options['nav_style']; ?>" /></td>
	      <td><?php _e('The color scheme of the navigation. Choice between "white", "black" or a custom CSS class.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation Alignment', 'radykal'); ?></td>
	      <td>
	        <select name="nav_alignment">
	          <option value="left" <?php selected($options['nav_alignment'], "left"); ?>>Left</option>
	          <option value="center" <?php selected($options['nav_alignment'], "center"); ?>>Center</option>
	          <option value="right" <?php selected($options['nav_alignment'], "right"); ?>>Right</option>
	        </select>
	      </td>
	      <td><?php _e('The navigation alignment.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation Previous Button Text', 'radykal'); ?></td>
	      <td><input type="text" name="nav_previous_text" class="widefat" value="<?php echo esc_textarea($options['nav_previous_text']); ?>" /></td>
	      <td><?php _e('The text for the previous button of the naviagtion.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Navigation Next Button Text', 'radykal'); ?></td>
	      <td><input type="text" name="nav_next_text" class="widefat" value="<?php echo esc_textarea($options['nav_next_text']); ?>" /></td>
	      <td><?php _e('The text for the next button of the naviagtion.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('"Back to albums" Button Text', 'radykal'); ?></td>
	      <td><input type="text" name="nav_back_text" class="widefat" value="<?php echo esc_textarea($options['nav_back_text']); ?>" /></td>
	      <td><?php _e('The text for the "back to album overview" button of the naviagtion.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail transition', 'radykal'); ?></td>
	      <td>
	        <select name="thumbnail_transition">
	          <option value="fade" <?php selected($options['thumbnail_transition'], "fade"); ?>>Fade</option>
	          <option value="none" <?php selected($options['thumbnail_transition'], "none"); ?>>None</option>
	        </select>
	      </td>
	      <td><?php _e('The type of the thumbnail transition.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Lightbox', 'radykal'); ?></td>
	      <td>
		      <input type="radio" name="lightbox" value="prettyphoto" <?php checked($options['lightbox'], 'prettyphoto') ?> /> PrettyPhoto<br />
		      <input type="radio" name="lightbox" value="fancybox" <?php checked($options['lightbox'], 'fancybox') ?> /> Fancybox
	      </td>
	      <td><?php _e('The type of lightbox. You can choice between <a href="http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/" target="_blank">prettyphoto</a> and <a href="http://www.fancyapps.com/fancybox/" target="_blank">fancybox</a>. Please note, when using fancybox and you have a commercial website, you need to buy a <a href="http://www.fancyapps.com/fancybox/#license" target="_blank">license for FancyBox</a>.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Number of columns', 'radykal'); ?></td>
	      <td><input type="text" name="columns" size="3" value="<?php echo esc_textarea($options['columns']); ?>" /></td>
	      <td><?php _e('The number of columns.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Media label', 'radykal'); ?></td>
	      <td><input type="text" name="media_label" class="widefat" value="<?php echo esc_textarea($options['media_label']); ?>" /></td>
	      <td><?php _e('The media label when using Thumbnails as album selection.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Show only first thumbnail of an album', 'radykal'); ?></td>
	      <td><input type="checkbox" name="show_only_first_thumbnail" value="1" <?php checked($options['show_only_first_thumbnail'], 1) ?> /></td>
	      <td><?php _e('This will only show the first thumbnail of an album. Use this if you want to show a whole gallery in the lightbox from just one thumbnail.', 'radykal'); ?></td>
	    </tr>
  	</tbody>
  </table>
  
  <br /><br />
  
  <h3>Options for the lightbox</h3>
  <table cellspacing="0" id="lightbox-options">
  	<thead>
	  	<tr>
	  		<th width="170"><?php _e('Option', 'radykal'); ?></th>
	  		<th style="width: 200px;"><?php _e('Value', 'radykal'); ?></th>
	  		<th><?php _e('Explanation', 'radykal'); ?></th>
	  	</tr>
  	</thead>
  	<tbody id="prettyphoto-options" class="<?php echo $options['lightbox'] == 'prettyphoto' ? 'active' : ''; ?>">
	    <tr>
	      <td><?php _e('Theme', 'radykal'); ?></td>
	      <td>
	        <select name="prettyphoto_theme">
	          <option value="pp_default" <?php selected($options['prettyphoto_theme'], "pp_default"); ?>>Default</option>
	          <option value="light_rounded"<?php selected($options['prettyphoto_theme'], "light_rounded"); ?>>Light rounded</option>
	          <option value="dark_rounded"<?php selected($options['prettyphoto_theme'], "dark_rounded"); ?>>Dark rounded</option>
	          <option value="light_square" <?php selected($options['prettyphoto_theme'], "light_square"); ?>>Light square</option>
	          <option value="dark_square" <?php selected($options['prettyphoto_theme'], "dark_square"); ?>>Dark square</option>
	          <option value="facebook" <?php selected($options['prettyphoto_theme'], "facebook"); ?>>Facebook</option>
	        </select>
	      </td>
	      <td><?php _e('Select a theme for the prettyphoto modal box.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Enable overlay gallery', 'radykal'); ?></td>
	      <td><input type="checkbox" name="prettyphoto_overlay" value="1" <?php checked($options['prettyphoto_overlay'], 1) ?> /></td>
	      <td><?php _e('Hide or show the overlay gallery.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Allow image resizing', 'radykal'); ?></td>
	      <td><input type="checkbox" name="prettyphoto_image_resize" value="1" <?php checked($options['prettyphoto_image_resize'], 1); ?> /></td>
	      <td><?php _e('Allow to resize the image so it fits in the browser window.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Autoplay slideshow', 'radykal'); ?></td>
	      <td><input type="checkbox" name="prettyphoto_slideshow" value="1" <?php checked($options['prettyphoto_slideshow'], 1); ?> /></td>
	      <td><?php _e('Enable or disable the autoplay for the slideshow.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Deeplinking', 'radykal'); ?></td>
	      <td><input type="checkbox" name="prettyphoto_deeplinking" value="1" <?php checked($options['prettyphoto_deeplinking'], 1); ?> /></td>
	      <td><?php _e('Enable or disable the deeplinking for every media.<strong>This is required for the social buttons!</strong>', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Social Buttons', 'radykal'); ?></td>
	      <td><input type="checkbox" name="prettyphoto_social_tools" value="1" <?php checked($options['prettyphoto_social_tools'], 1); ?> /></td>
	      <td><?php _e('Enable or disable the social buttons.', 'radykal'); ?></td>
	    </tr>
  	</tbody>
  	<tbody id="fancybox-options" class="<?php echo $options['lightbox'] == 'fancybox' ? 'active' : ''; ?>">
	    <tr>
	      <td><?php _e('Width', 'radykal'); ?></td>
	      <td><input type="text" name="fancybox_width" size="3" maxlength="5" value="<?php echo $options['fancybox_width']; ?>" /></td>
	      <td><?php _e('Default width for "iframe" and "swf" content.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Height', 'radykal'); ?></td>
	      <td><input type="text" name="fancybox_height" size="3" maxlength="5" value="<?php echo $options['fancybox_height']; ?>" /></td>
	      <td><?php _e('Default height for "iframe" and "swf" content.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Autoplay', 'radykal'); ?></td>
	      <td><input type="checkbox" name="fancybox_autoplay" value="1" <?php checked($options['fancybox_autoplay'], 1); ?> /></td>
	      <td><?php _e('If checked, slideshow will start after opening the first gallery item.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Arrows', 'radykal'); ?></td>
	      <td><input type="checkbox" name="fancybox_arrows" value="1" <?php checked($options['fancybox_arrows'], 1); ?> /></td>
	      <td><?php _e('If checked, navigation arrows will be displayed.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Loop', 'radykal'); ?></td>
	      <td><input type="checkbox" name="fancybox_loop" value="1" <?php checked($options['fancybox_loop'], 1); ?> /></td>
	      <td><?php _e('If checked, enables cyclic navigation. This means, if you click "next" after you reach the last element, first element will be displayed (and vice versa).', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Title Position', 'radykal'); ?></td>
	      <td>
	        <select name="fancybox_title_position">
	          <option value="float" <?php selected($options['fancybox_title_position'], "float"); ?>>Float</option>
	          <option value="inside"<?php selected($options['fancybox_title_position'], "inside"); ?>>Inside</option>
	          <option value="outside"<?php selected($options['fancybox_title_position'], "outside"); ?>>Outside</option>
	          <option value="over" <?php selected($options['fancybox_title_position'], "over"); ?>>Over</option>
	        </select>
	      </td>
	      <td><?php _e('The position of the title.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Button Helpers Position', 'radykal'); ?></td>
	      <td>
	        <select name="fancybox_buttons_position">
	          <option value="none" <?php selected($options['fancybox_buttons_position'], "none"); ?>>None</option>
	          <option value="top"<?php selected($options['fancybox_buttons_position'], "top"); ?>>Top</option>
	          <option value="bottom"<?php selected($options['fancybox_buttons_position'], "bottom"); ?>>Bottom</option>
	        </select>
	      </td>
	      <td><?php _e('The position of the button helpers.', 'radykal'); ?></td>
	    </tr>
	    <tr>
	      <td><?php _e('Thumbnail Helpers Position', 'radykal'); ?></td>
	      <td>
	        <select name="fancybox_thumbs_position">
	          <option value="none" <?php selected($options['fancybox_thumbs_position'], "none"); ?>>None</option>
	          <option value="top"<?php selected($options['fancybox_thumbs_position'], "top"); ?>>Top</option>
	          <option value="bottom"<?php selected($options['fancybox_thumbs_position'], "bottom"); ?>>Bottom</option>
	        </select>
	      </td>
	      <td><?php _e('The position of the thumbnails helpers.', 'radykal'); ?></td>
	    </tr>
  	</tbody>
  </table>
  
</div>