
//-----------Document ready----------

jQuery(document).ready(function($) {

	$ = jQuery.noConflict();
		
	$('textarea').focus(function() {
		$(this).select();
	});
	
	$("table tr:nth-child(odd)").addClass("odd-row");
	/* For cell text alignment */
	$("table td:first-child, table th:first-child").addClass("first");
	/* For removing the last border */
	$("table td:last-child, table th:last-child").addClass("last");
	
	//add active class to current gallery item in accordion
	$('#galleries-accordion > li > div[id="'+$('input[name="selected_gallery"]').val()+'"]').addClass('active');
	
	//show active lightbox options
	$('table#lightbox-options tbody.active').show();
	
	//submit form with selected gallery
	$('#galleries-accordion > li > div, .fg-paste-options').click(function() {
		var $this = $(this);
		if($this.is('a')) {
			if( $this.parent().parent().attr('id') != $('input[name="selected_gallery"]').val() && $(document).find('.fg-disable-links').size() == 0 ) {
				$('input[name="overwrite_gallery"]').val($this.parent().parent().attr('id'));
				$('#fg-options-form').submit();
			}
		}
		else {
			if( $this.attr('id') != $('input[name="selected_gallery"]').val() ) {
				$('input[name="selected_gallery"]').val($this.attr('id'));
				$('#fg-options-form').submit();
			}
		}
		
		return false;
	});
	
	//show selected lightbox options
	$('input[name="lightbox"]').change(function() {
		if( !$('#lightbox-options tbody').is(':animated')) {
			$('#lightbox-options tbody.active').hide(200).removeClass('active');
			$('#'+$(this).val()+'-options').delay(500).show(500).addClass('active');
		}
	});
	
	
	//set colorpickers
	$('.colorpicker').spectrum({
		color: $(this).val(),
		preferredFormat: "hex",
		showInput: true,
		chooseText: "Change Color",
		change: function(color) {
			$(this).val(color.toHexString());
		}
	});
	
	//set shadow image
	var currentImgUploader;	
    $('.image-upload').click(function(evt) {
    	currentImgUploader = $(this);
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');	
		 window.send_to_editor = function(html) {
			imagePath = $('img',html).attr('src');
			currentImgUploader.parent().children('input').val(imagePath);	
			tb_remove();
			_checkUploadInputs();
		}
		return false;
	});
	
	$('.remove-image').click(function(evt) {
		$(this).parent().children('input').val('');
		_checkUploadInputs();
		return false;
	});
	
	_checkUploadInputs();
	
	function _checkUploadInputs() {
		var uploadChecks = $('.upload-check');
		uploadChecks.each(function() {
			var $this = $(this);
			if($this.next('input').val().length > 0) {
				$this.removeClass('upload-check-fail').addClass('upload-check-success');
			}
			else {
				$this.removeClass('upload-check-success').addClass('upload-check-fail');
			}
		});
		
	}
	
	//disable all links
	if($(document).find('.fg-disable-links').length) {
		$('.fg-paste-options').die('click');
	};
	
});