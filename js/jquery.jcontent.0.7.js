/*
* jContent 0.1 - jQuery content slider plugin
* jQuery.fn.jContent(options);
* http://jcontent.fahon.org
*/

(function($){
	$.fn.jContent = function(options){
		
		var jhndl = this;
		var options = $.extend({
			speed: 500,
			orientation: 'horizontal', //horizontal, vertical,
			auto: false,
			direction: 'next',
			pause: 5000,
			circle: false,
			easing: '', //easeOutCirc, easeOutBounce,
			xml: '',
			pause_on_hover: false,
			width: 655,
			height: 170,
			videos: false
		},options);

		this.init = function(hndl){
				
			//init
			hndl.div = $(hndl);			
			hndl.slides_div = $(hndl).children("div.slides").css("overflow", "hidden").css("position", "relative").css("width", options.width).css("height", options.height).css("float", "left");	
			hndl.currentSlide = 0;
			hndl.lastDirection = '';
			hndl.isMouseHover = false;
			hndl.intervalId;
			
			//init classes
			hndl.div.addClass("jContent");
			if(options.orientation == 'horizontal'){
				hndl.div.addClass("jc-horizontal");
			}
			else{
				hndl.div.addClass("jc-vertical");
			}
			
			if(options.auto){
				hndl.div.addClass("jc-auto");
			}
			
			if(options.circle){
				hndl.div.addClass("jc-circle");
			}
			
			//init slides
			hndl.slides = hndl.slides_div.children("div").css("width", options.width).css("height", options.height).hide();
			hndl.count = hndl.slides.length;
			
			//init arrows
			hndl.left_arr = hndl.div.children("a").eq(0);
			hndl.right_arr = hndl.div.children("a").eq(1);			
			
			//create panel
			hndl.slides_div.append("<div class='panel'></div>");
			hndl.panel = hndl.slides_div.children("div.panel").css("position", "absolute");
			
			//orientation
			switch(options.orientation){
				case 'horizontal':{	
					hndl.slides.css("float", "left");
					hndl.panel.css("width", 3*options.width).css("height", options.height);
					break;
				}
				case 'vertical':{
					hndl.panel.css("height", 3*options.height).css("width", options.width);
					break;
				}
			}			
			
			hndl.getNextIndex = function(){
				if(hndl.currentSlide >= hndl.count - 1){
					return 0;
				}
				else{
					return hndl.currentSlide + 1;
				}
			};
			
			hndl.getPrevIndex = function(){
				if(hndl.currentSlide <= 0){
					return hndl.count - 1;
				}
				else{
					return hndl.currentSlide - 1;
				}			
			};
			
			hndl.initItemsPosition = function(direction){
			
				if(hndl.panel.children().length == 0){
					//alert(hndl.slides.eq(hndl.getPrevIndex()).outerHtml());
					hndl.slides.eq(hndl.getPrevIndex()).show().appendTo(hndl.panel);
					hndl.slides.eq(hndl.currentSlide).show().appendTo(hndl.panel);
					hndl.slides.eq(hndl.getNextIndex()).show().appendTo(hndl.panel);
				}
				else{
					if(direction == 'prev'){
						
						//add prev as first item
						hndl.slides.eq(hndl.getPrevIndex()).show().prependTo(hndl.panel);
						
						//remove the last one
						hndl.panel.children().eq(3).remove();
						//hndl.panel.children().eq(0).replaceWith(hndl.slides.eq(hndl.getPrevIndex()).clone().show());
						
						if(options.videos){
							//replace the last (third) item
							hndl.panel.children().eq(2).replaceWith(hndl.slides.eq(hndl.getNextIndex()).show());
						}
					}
					else{
						//add next to the last
						hndl.slides.eq(hndl.getNextIndex()).show().appendTo(hndl.panel);
						
						//remove the first
						hndl.panel.children().eq(0).remove();
						
						if(options.videos){
							//replace the last (third) item
							hndl.panel.children().eq(0).replaceWith(hndl.slides.eq(hndl.getPrevIndex()).show());
						}
					}
					
					
				}
				
				
			
				switch(options.orientation){
					case 'horizontal':{	
						hndl.panel = hndl.slides_div.children("div.panel").css("left", -1*options.width);
						break;
					}
					case 'vertical':{
						hndl.panel = hndl.slides_div.children("div.panel").css("top", -1*options.height);
						break;
					}
				}
				
			};

			hndl.isLastItem = function(){
				return hndl.currentSlide >= hndl.count - 1;
			};
			
			hndl.isFirstItem = function(){
				return hndl.currentSlide <= 0;
			};
			
			hndl.updateArrows = function(){
				if(!options.auto && !options.circle){
					if(hndl.isLastItem()){
						hndl.left_arr.removeClass("hide").removeClass("show").addClass("show");
						hndl.right_arr.removeClass("hide").removeClass("show").addClass("hide");
					}
					else{
						if(hndl.isFirstItem()){
							hndl.left_arr.removeClass("hide").removeClass("show").addClass("hide");
							hndl.right_arr.removeClass("hide").removeClass("show").addClass("show");
						}
						else{
							hndl.left_arr.removeClass("hide").removeClass("show").addClass("show");
							hndl.right_arr.removeClass("hide").removeClass("show").addClass("show");					
						}
					}
				}
			};
			
			hndl.getAnimateArgs = function(direction){
				var args;
				
				switch(options.orientation){
					case 'horizontal':{	
						if(direction == 'prev'){
							args = {left: 0};
						}
						else{//next
							args = {left:-2*options.width};
						}						
						break;
					}
					case 'vertical':{
						if(direction == 'prev'){
							args = {top: 0};
						}
						else{//next
							args = {top:-2*options.height};
						}	
						break;
					}
				}
				
				return args;				
			};
			
			
			hndl.performAimate = function(direction){
				
				hndl.panel.animate(hndl.getAnimateArgs(direction), options.speed, options.easing, function(){	
					
					if(direction == 'prev'){
						hndl.currentSlide = hndl.getPrevIndex();
					}
					else{
						hndl.currentSlide = hndl.getNextIndex();
					}
					
					hndl.initItemsPosition(direction);	
					hndl.updateArrows();
					
					if(options.auto == true){	

						hndl.go(direction);
					}
				});	

			};
			
			hndl.animation = function(direction){
			
				hndl.lastDirection = direction;	
				
				if(options.auto){//auto
				
					setTimeout(function(){//delay
						
						//pause on hover
						if(options.pause_on_hover && hndl.isMouseHover){
						
							hndl.intervalId = setInterval(function(){
									if(!hndl.isMouseHover){
										clearInterval(hndl.intervalId);	
										hndl.performAimate(direction);
									}
								}, 250);					
						}
						else{
							hndl.performAimate(direction);
						}				
					
					}, options.pause);
				}
				else{//click
					hndl.performAimate(direction);
				}
				
			};
			
			hndl.go = function(direction){									
				if(direction == 'prev'){
					if(!hndl.isFirstItem() || options.auto || options.circle){
						hndl.animation('prev');
					}
				}
				else{//next
					if(!hndl.isLastItem() || options.auto || options.circle){
						hndl.animation('next');
					}				
				}
								
			};
			
			hndl.left_arr.click(function(){	
				hndl.go('prev');
				return false;
			});
			
			hndl.right_arr.click(function()
			{
				hndl.go('next');
				return false;
			});
			
			hndl.div.mouseenter(function(){
				if(options.pause_on_hover){					
					hndl.isMouseHover = true;
				}
			});
			
			hndl.div.mouseleave(function(e){
				if(options.pause_on_hover){						
					hndl.isMouseHover = false;						
				}	
			});
			
			//start
			hndl.updateArrows();
			hndl.initItemsPosition('');
			
			//autostart
			if(options.auto){				
				hndl.go(options.direction);
			}			
		};
		
		return this.each(function() {			
			var hndl = this;
			
			//ajax
			if(options.xml != '')
			{
				$.ajax({url: options.xml,
						type: "GET",
						cache: false, 
						success: function(XmlDoc){
							var slides = $("slide", XmlDoc);
							
							//build data
							$(hndl).html("<a title='' href='#' class='prev'></a><div class='slides'></div><a title='' href='#' class='next'></a>");
							slides.each(function(){	
								$(hndl).find("div.slides").append($(this).text());
							});
							
							//init
							jhndl.init(hndl);
						},			
						dataType: "xml"});				
			}else{
				jhndl.init(hndl);
			}

		});	
	};
})(jQuery);
