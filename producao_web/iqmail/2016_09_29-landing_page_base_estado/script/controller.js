$(function(){
	
	// console.log("oi");
	
	// OVERLAY DO VIDEO IQMAIL
	$("#video_play-iqmail").click(function(){	
    	$("#video_iqmail").fadeIn();
    	$('.youtube_video-iqmail').each(function(){
 			this.contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*')
		});
		$("body").css("overflow-x", "hidden");
		$("body").css("overflow-y", "hidden");
	});
	
	var hasVideo = 1;
	
	$("#video_iqmail").click(function(){
    	$("#video_iqmail").fadeOut();
    	$('.youtube_video-iqmail').each(function(){
 			this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
		});
		$("body").css("overflow-x", "auto");
		$("body").css("overflow-y", "auto");
	});
	
	$(document).ready(function(){
		$(document).bind('keydown', function(e) { 
			if (e.which == 27) {
				$("#video_iqmail").fadeOut();
				$('.youtube_video-iqmail').each(function(){
					this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
				});
				$("body").css("overflow-x", "auto");
				$("body").css("overflow-y", "auto");
			}
		}); 
	});
	
	
	
	
	// OVERLAY DO VIDEO OFERTADINAMICA
	$("#video_play-ofertadinamica").click(function(){	
    	$("#video_ofertadinamica").fadeIn();
    	$('.youtube_video-ofertadinamica').each(function(){
 			this.contentWindow.postMessage('{"event":"command","func":"' + 'playVideo' + '","args":""}', '*')
		});
		$("body").css("overflow-x", "hidden");
		$("body").css("overflow-y", "hidden");
	});
	
	var hasVideo = 1;
	
	$("#video_ofertadinamica").click(function(){
    	$("#video_ofertadinamica").fadeOut();
    	$('.youtube_video-ofertadinamica').each(function(){
 			this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
		});
		$("body").css("overflow-x", "auto");
		$("body").css("overflow-y", "auto");
	});
	
	$(document).ready(function(){
		$(document).bind('keydown', function(e) { 
			if (e.which == 27) {
				$("#video_ofertadinamica").fadeOut();
				$('.youtube_video-ofertadinamica').each(function(){
					this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
				});
				$("body").css("overflow-x", "auto");
				$("body").css("overflow-y", "auto");
			}
		}); 
	});
	
	

	// SMOOTH SCROLL
	$(function() {
	  $('a[href*="#"]:not([href="#"])').click(function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
		  var target = $(this.hash);
		  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		  if (target.length) {
			$('html, body').animate({
			  scrollTop: target.offset().top
			}, 1000);
			return false;
		  }
		}
	  });
	});

		
});//JQUERY()
