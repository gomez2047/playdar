var ajaxRunning = false;

function toggleAjaxLoading() {
	if(ajaxRunning) {
		ajaxRunning = false;
		jQuery('#ajaxOverlay').remove();
		jQuery('#ajaxLoader').remove();
	}
	else {
		ajaxRunning = true;
		var windowWidth = jQuery(window).width();
		var windowHeight = jQuery(window).height();
		jQuery('body').append("<div id='ajaxOverlay' style='width:"+windowWidth+"px; height:"+windowHeight+"px;'></div>");
		jQuery('body').append("<div id='ajaxLoader' style='left:"+ (windowWidth * 0.5 - 63)+"px; top:"+ (windowHeight * 0.5 - 11) +"px;'></div>");
		jQuery('#ajaxOverlay').fadeTo(0, 0.5);
	}
};

function showSuccessBox(txt) {
	if(!jQuery('.success-box').size()) {
		jQuery('body').prepend("<div class='success-box' style='display: none;'><div style='margin: 10px 0px 0px 70px;'><h3 style='color: #258815; margin:0px;'>Success</h3><p style='margin: 2px;'></p></div></div>");
	}

	jQuery('.success-box').css({left: jQuery(window).width() * 0.5 - 200}).hide().stop(true, true).find('p').text(txt).parent().parent().fadeIn(300).delay(2500).fadeOut(300);
};

function showErrorBox(txt) {
	if(!jQuery('.error-box').size()) {
		jQuery('body').prepend("<div class='error-box' style='display: none;'><div style='margin: 10px 0px 0px 70px;'><h3 style='color: #871414; margin:0px;'>Error</h3><p style='margin: 2px;'></p></div></div>");
	}

	jQuery('.error-box').css({left: jQuery(window).width() * 0.5 - 200}).hide().stop(true, true).find('p').text(txt).parent().parent().fadeIn(300).delay(4000).fadeOut(300);
}

function getResponseMessage(res) {	
	var msg = '';
	if(res.errors) {
		jQuery.each(res.responses, function() {
			jQuery.each(this.errors, function() {
				msg += this.message + '\n';
			});
		});
	}
	else {
		if(res.responses) {
			msg = res.responses[0].data;
		}
		else {
			msg = "Success!";	
		}
	}
	return msg;
};

function getSupplemental(res) {	
	var supplemental = {};
	if(!res.errors) {
		if(res.responses) {
			supplemental = res.responses[0].supplemental;
		}
	}
	return supplemental;
};