var disableLinks = false;

//-----------Document ready----------
jQuery(document).ready(function($) {

	$ = jQuery.noConflict();
	
	var notification = $('#fg-notification'),
		galleriesAccordion = $('#galleries-accordion'),
		galleries = $('#galleries-accordion > li > div'),
   		accordion_body = $('#galleries-accordion li > .sub-menu')
   		currentGallery = galleries.first().attr('id')
   		currentAlbum = $('#galleries-accordion > li:first').children('.sub-menu:first').find('li > div').attr('id'),
   		loadingIndex = 0,
   		filesLength = 0;
   	
   	currentGallery = currentGallery === undefined ? null : currentGallery;
   	currentAlbum = currentAlbum === undefined ? null : currentAlbum;   	
   	  		
   	// Open the first tab on load
    galleries.first().addClass('active').next().slideDown('normal');
   		
   	$('#mediaList').sortable();
   	
   	$(".fg-tooltip").tipTip().click(function() {
	   	return false;
   	});
   	
   	//init sortable for image list
	accordion_body.sortable({
		connectWith: '.sub-menu', 
		placeholder: 'fg-placeholder',
		start: function() { 
			accordion_body.css({'min-height': 2}).filter($('.gallery-item:not(.active)').next('.sub-menu')).slideDown(0).sortable('refresh');
		},
		stop: function() { 
			accordion_body.css({'min-height': 0}).filter($('.gallery-item:not(.active)').next('.sub-menu')).slideUp(100);
		},
		update: function(evt, ui) {
		
			if(ui.sender == null) {
				
				var targetList = ui.item.parent(),
					albumId = ui.item.children('div').attr('id'),
					targetGallery = targetList.prev('.gallery-item').attr('id'),
					albums = [];
				
				targetList.children('li').children('div').each(function() {
					albums.push($(this).attr('id'));
				});
				
				toggleAjaxLoading();

				$.ajax({ url: options.Ajax_Url, data: { action: 'updatealbums', oldGallery: currentGallery, newGallery: targetGallery,  albums: albums, albumId: albumId }, type: 'post', success: function(data) {
				
					var res = wpAjax.parseAjaxResponse(data, 'ajax-response');
					var msg = getResponseMessage(res);
					
					if(!res.errors) {
						
						if(currentAlbum == albumId && currentGallery != targetList.prev('.gallery-item').attr('id')) {
							$('#mediaList').empty();
							targetList.prev('.gallery-item').click();
						}
						
						showSuccessBox(msg);
					}
					else {
						alert(msg);
						
					}
					toggleAjaxLoading();			
				  }
				});
			}			
		}
	}).disableSelection();
	
	//start upload process
	$('#upload-images-button').click(function() {
	
		if(checkIds()) {
			alert(checkIds());
			return false;
		}
		
		$('#image-upload-form').fileupload('option',{formData: {action: 'uploadfile', security: options.uploadNonce, albumDir: currentGallery + '/' + currentAlbum + '/'}});
		
		$('#image-upload-form input').click();
		return false;
	});
	
	//get alert box
	var alertBox = $('#fg-alert > a').click(function() {
		$(this).parent().slideUp(200).children('ul').empty();
		return false;
	}).parent();
   		 
    
    //opens the window to select the images
    $('#image-upload-form').fileupload({
		url: options.Ajax_Url,
		sequentialUploads: true,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
		change: function(e, data) {
			toggleAjaxLoading();
			loadingIndex = filesLength = 0;
		},
		add: function(e, data) {
			data.submit()
			.success(function(result, textStatus, jqXHR) {
				var filename = result.filename;
				if(textStatus == "success" && result.error == 0) {
					var file = options.contentUrl+currentGallery+"/"+currentAlbum+"/"+result.realFile;
					$.ajax({ 
						url: options.Ajax_Url, 
						data: { 
							action: 'savefile', 
							album: currentAlbum, 
							file: file, 
							thumbnail: file, 
							sortId: $('#mediaList li').size()
				 
						}, 
						type: 'post', 
						success: function(data) {
							
							addListItem('append', file, file, $('#titles-from-filenames').is(':checked') ? filename : '', '');
							
						}
					});
				}else {
					var errorMessage = "Upload failed";
					if(result.message) {
						errorMessage = result.message;	
					}
					
					if(alertBox.is(':hidden')) { alertBox.slideDown(300); }
					alertBox.children('ul').append('<li><strong>'+filename+': </strong>'+errorMessage+'</li>');
				}
				
				++loadingIndex;
				if(loadingIndex == filesLength) {
					//all images stored
					toggleAjaxLoading();
					notification.text('Upload completed!');
				}
			})
			.error(function (jqXHR, textStatus, errorThrown) {
		        if (errorThrown === 'abort') {
		            //console.log('File Upload has been canceled');
		        }
	        })
		},
		send: function(e, data) {
			filesLength = data.originalFiles.length;
			notification.html('Loading: '+data.originalFiles[loadingIndex].name + ' - <strong>' + (loadingIndex+1) + '/' + filesLength + '</strong>');
		}
    });
    
    //upload another media
	$('#upload-media-button').click(function(evt) {
	
		if(checkIds()) {
			alert(checkIds());
			return false;
		}
		
		var thumbnail,
			media;
				
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		
		window.send_to_editor = function(html) {
		    thumbnail = $('img',html).attr('src');
		    tb_remove();
		    if(thumbnail) {
			    media = prompt('Please enter the URL of the media. This could be an URL to a YouTube, Quicktime, Vimeo video etc. . \n Please check out the documentation to get more infos about the supported media for the 2 lightboxes.', '')
			    if(media != null && media != '') {
			    	toggleAjaxLoading();
			    	
			    	$.ajax({ 
			    		url: options.Ajax_Url, 
			    		data: { action: 'savefile', gallery: currentGallery, album: currentAlbum, file: media, thumbnail: thumbnail, sortId: $('#mediaList li').size() }, 
			    		type: 'post', 
			    		success: function(data) {
							addListItem('append', media, thumbnail, '', '');
							toggleAjaxLoading();
							showSuccessBox('Media successfully uploaded!');
					  }
					});
				    
			    }
			    else if(media == '') {
				    alert('Please enter a valid media URL!');
			    }
		    }
		};
		
		return false;

	});	
 
    //show albums of a gallery
    galleriesAccordion.delegate('li > div', 'click', function(event) {
	    
	    var $this = $(this);
	    
        // Show and hide the tabs on click
        if (!$this.hasClass('active') && $this.hasClass('gallery-item')){
            accordion_body.slideUp('normal');
            $this.next().stop(true,true).slideToggle('normal');
            galleries.removeClass('active');
            $this.addClass('active');
            currentGallery = $this.attr('id');
            if($this.next('.sub-menu').children('li').size() > 0) {
	            $this.next('.sub-menu').children('li:first').children('div').click();
            }
            else {
            	$('#mediaList').empty();
            	notification.text('The selected gallery has no albums yet!');
	            currentAlbum = null;
            }
        }
        
        return false;

    });
    
    //show media of an album
    galleriesAccordion.delegate('li > ul > li > div', 'click', function(event) {

	    var $this = $(this);
	    currentAlbum = $this.attr('id');
		loadAlbum();
        
        return false;

    });
    
    //add a new gallery to the gallery list
	$('#fg-add-gallery').click(function() {

		var titleInput =  $('input[name=gallery_title]');
		
		if(titleInput.val() != "") {
            toggleAjaxLoading();
			$.ajax({ url: options.Ajax_Url, data: { action: 'newgallery', title: titleInput.val() }, type: 'post', success: function(data) {

				var res = wpAjax.parseAjaxResponse(data, 'ajax-response'),
					supplemental = getSupplemental(res)
					msg = getResponseMessage(res);
                
				if(!res.errors) {
					$('#galleries-accordion').append(supplemental.gallery_html + '</ul></li>').children('li:last').children('div').click();
					titleInput.val('');
					galleries = $('#galleries-accordion > li > div');
					accordion_body = $('#galleries-accordion li > .sub-menu');
					accordion_body.sortable();
					currentAlbum = null;
					showSuccessBox(msg);
				}
				else {
					alert(msg);  
				}
				
				toggleAjaxLoading();
			  }
			});
		}
		else {
			alert("Please enter a gallery title!");
		}
		
		return false;
	});
    
    //edit titles
    galleriesAccordion.delegate('.fg-add-album', 'click', function() {
    	
    	var $this = $(this),
    		$albumList = $this.parent().parent().next('.sub-menu'),
    		title =  prompt('Please enter a title for the album:', '');
    	
		if(title != "" && title != null) {
		
			$this.parent().parent().click();
		
            toggleAjaxLoading();
            
			$.ajax({ url: options.Ajax_Url, data: { action: 'newalbum', gallery: currentGallery, title: title, sortId: $albumList.children('li').size() }, type: 'post', success: function(data) {
				
				var res = wpAjax.parseAjaxResponse(data, 'ajax-response'),
					supplemental = getSupplemental(res)
					msg = getResponseMessage(res);
				
				if(!res.errors) {
					$albumList.append(supplemental.album_html);
					accordion_body = $('#galleries-accordion li > .sub-menu');
					accordion_body.sortable();
					$this.parent().parent().click().next('.sub-menu').children('li:last').children('div').click();
					showSuccessBox(msg);
				}
				else {
					alert(msg);  
				}
				
				toggleAjaxLoading();
			  }
			});
		}
		else if(title == "") {
			alert("Not a valid title! Please enter a correct title for the album!");
			$this.click();
		}
		
		return false;
    	
    });
    
    //edit titles
    galleriesAccordion.delegate('.fg-edit', 'click', function() {
    
    	var $this = $(this),
    		item = $this.parent().parent(),
    		oldTitle = item.children('.fg-title').text(),
    		newTitle = prompt($this.hasClass('fg-edit-gallery') ? 'Please enter a new title for the gallery:' : 'Please enter a new title for the album:', oldTitle);
    		
    	if(newTitle != null && newTitle != '' && newTitle != oldTitle) {
	    	toggleAjaxLoading();
	    	
	    	//edit gallery title
	    	if($this.hasClass('fg-edit-gallery')) {
		    	$.ajax({ url: options.Ajax_Url, data: { action: 'editgallery', id: item.attr('id'), newTitle: newTitle }, type: 'post', success: function(data) {
		    	
					var res = wpAjax.parseAjaxResponse(data, 'ajax-response'),
						supplemental = getSupplemental(res),
						msg = getResponseMessage(res);
					
					if(!res.errors) {
						$this.parent().parent().children('.fg-title').text(supplemental.title);
						showSuccessBox(msg);
					}
					else {
						alert(msg);
					}	
					
					toggleAjaxLoading();
				  }
				});
	    	}
	    	//edit album title
	    	else {
		    	$.ajax({ url: options.Ajax_Url, data: { action: 'editalbum', id: item.attr('id'), newTitle: newTitle }, type: 'post', success: function(data) {
		    	
					var res = wpAjax.parseAjaxResponse(data, 'ajax-response'),
						supplemental = getSupplemental(res),
						msg = getResponseMessage(res);
					
					if(!res.errors) {
						$this.parent().parent().children('.fg-title').text(supplemental.title);
						showSuccessBox(msg);
					}
					else {
						alert(msg);
					}	
					
					toggleAjaxLoading();
				  }
				});
	    	}
			
    	}
    	else if(newTitle == '') {
	    	if($this.hasClass('fg-edit-gallery')) { alert('Please enter a correct title for your gallery!'); }
	    	else { alert('Please enter a correct title for your album!'); }
	    	$this.click();
    	}
    	
	    return false;
    });
    
    //trash gallery/album
    galleriesAccordion.delegate('.fg-delete', 'click', function() {
    	
    	var $this = $(this),
    		item = $this.parent().parent(),
    		confirmMsg = $this.hasClass('fg-delete-gallery') ? 'Are you sure you want to trash gallery: '+item.children('.fg-title').text()+'?' :  'Are you sure you want to trash album: '+item.children('.fg-title').text()+'?',
    		r = confirm(confirmMsg);
    		
		if(r) {
			toggleAjaxLoading();
			if($this.hasClass('fg-delete-gallery')) {
				
				$.ajax({ url: options.Ajax_Url, data: { action: 'deletegallery', id: item.attr('id') }, type: 'post', success: function(data) {
					var res = wpAjax.parseAjaxResponse(data, 'ajax-response');
					var msg = getResponseMessage(res);

					if(!res.errors) {
						if(item.attr('id') == currentGallery) {
							$('#mediaList').empty();
							currentGallery = null; 
						}
						item.parent().remove();
						showSuccessBox(msg);
					}
					else {
						alert(msg);
					}
					
					toggleAjaxLoading();
				  }
				});
			}
			else {
				$.ajax({ url: options.Ajax_Url, data: { action: 'deletealbum', galleryId: currentGallery, id: item.attr('id') }, type: 'post', success: function(data) {
					var res = wpAjax.parseAjaxResponse(data, 'ajax-response');
					var msg = getResponseMessage(res);
					
					if(!res.errors) {
						if(item.attr('id') == currentAlbum) {
							$('#mediaList').empty();
							currentAlbum = null; 
						}
						item.parent().remove();
						showSuccessBox(msg);
					}
					else {
						alert(msg);
					}
					
					toggleAjaxLoading();
				  }
				});
			}
			
		}
		
    	return false;
    });
    
    //show shortcode
    galleriesAccordion.delegate('.fg-show-shortcode', 'click', function() {
	    var $this = $(this),
	    	id = $this.parent().parent().attr('id'),
	    	shortcode = '[fancygallery id="'+ id +'"]';

	    if($this.hasClass('fg-album-shortcode')) {
	    	var galleryId = $this.parent().parent().parent().parent().prev('.gallery-item').attr('id');
		    shortcode = '[fancygallery id="'+ galleryId +'" album="'+ id +'"]'
	    }
	    $('#shortcode-output').val(shortcode).select();
	    return false;
    });

	//selects all list items
	$("#select-all, #deselect-all").click(function(evt) {
		$("#mediaList input:checkbox").each(function() {
		  this.checked = evt.currentTarget.id == 'select-all' ? 'checked' : '';
		});
		
		return false;
	});
	
	
	//delete media from the media list
	$('#delete-files').click(function(evt){
		
		if($("input:checked").length == 0) {
			alert("No media selected!");
			return false;
		}
		
		r = confirm("You are going to delete the selected media! Sure?");
		if(!r) {
			return false;
		}
		
		toggleAjaxLoading();
		
		var files = $("input:checked").serializeArray();
		$.ajax({ url: options.Ajax_Url, data: { action: 'deletefiles', files: files, gallery: currentGallery, album: currentAlbum }, type: 'post', success: function(data) {
			$("input:checked").parent().remove();
			toggleAjaxLoading();
			showSuccessBox('Media successfully deleted!');
		  }
		});
		
		return false;
	});
	
	//update all media
	$('#update-media').click(function() {
				
		toggleAjaxLoading();
		
		var files = $('input[name*=files]').serializeArray();
		var thumbnails = $('input[name*=thumbnails]').serializeArray();
		var titles = $('input[name*=titles]').serializeArray();
		var descriptions = $('textarea[name*=descriptions]').serializeArray();
						
		$.ajax({ url: options.Ajax_Url, data: { action: 'updatefiles', albumId: currentAlbum, files: files, thumbnails: thumbnails, titles: titles, descriptions: descriptions }, type: 'post', success: function(data) {
			
			toggleAjaxLoading();
			showSuccessBox('Media successfully updated!');
			
		  }
		});
		
		return false;
	});
	
	//load the first album
	loadAlbum();
	
	//load all media of an album
    function loadAlbum() {
    	
    	toggleAjaxLoading();
    	
	    $('#mediaList').empty();
	    
	    if(currentAlbum == undefined || currentAlbum == null) {
	    	toggleAjaxLoading();
		    return false;
	    }
	    
	    accordion_body.find('div').removeClass('active');
	    galleries.filter('[id="'+currentGallery+'"]').next('.sub-menu').find('[id="'+currentAlbum+'"]').addClass('active');
	    
	    $.ajax({ url: options.Ajax_Url, data: { action: 'loadfiles', albumId: currentAlbum }, type: 'post', success: function(data) {
			
			if(data.length > 0) {
				notification.text('');
				$.each(data, function() {
					addListItem('prepend', this.file, this.thumbnail, this.title, this.description);
				});
			}
			else {
				 notification.text('The selected album has no media files yet!');
			}
			
			toggleAjaxLoading();
			showSuccessBox('Album successfully loaded!');
			
		  }
		});
		
    };

	//adds a new list item to the media list
	function addListItem(where, file, thumbnail, title, description) {
		
		var listItem = '<li><input type="checkbox" name="selected" value="'+file+'" /><input type="hidden" name="files[]" value="'+file+'" /><input type="hidden" name="thumbnails[]" value="'+thumbnail+'" /><span class="description">Title</span><br /><input type="text" name="titles[]" value="'+stripslashes(title)+'" style="width:100%;" /><img src="'+thumbnail+'" width="120" height="105" /><span class="description" style="float:left;">Description</span><textarea rows="4" name="descriptions[]" style="width:55%;">'+stripslashes(description)+'</textarea></li>';
		
		if(where == 'append') {
			$('#mediaList').append(listItem);
		}
		else {
			$('#mediaList').prepend(listItem);		
		}
	};
	
	//strip slashes from text
	function stripslashes(str) {
		str=str.replace(/\\'/g,'\'');
		str=str.replace(/\\"/g,'"');
		str=str.replace(/\\0/g,'\0');
		str=str.replace(/\\\\/g,'\\');
		return str;
	};
	
	//check if an album and or gallery is set
	function checkIds() {
	    if(currentGallery == undefined || currentGallery == null) {
	    	return 'No gallery is selected!';
	    }
	    else if(currentAlbum == undefined || currentAlbum == null) {
		    return 'No album is selected!';
	    }
	    return false;
    };
	
	//disable all links
	if($(document).find('.fg-disable-links').length) {
		disableLinks = true;
		$('.fg-disable-links').find('.button-secondary, .button-primary, .upload-button, .fg-button').unbind().fadeTo(0, 0.5);
		galleriesAccordion.undelegate('.fg-add-album', 'click');
		galleriesAccordion.undelegate('.fg-delete', 'click');
		galleriesAccordion.undelegate('.fg-edit', 'click');
		$('.fg-disable-links').find('input:submit').attr('disabled', 'disabled');
		accordion_body.sortable('disable');
	};
});