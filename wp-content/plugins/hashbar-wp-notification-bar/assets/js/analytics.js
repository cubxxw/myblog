(function ($) {
	"use strict";

	if(hashbar_analytical.enable_analytics == ''){
		return;
	}

	$(window).on('load',function(){
		notification_bars();
	});

	function notification_bars(){
		var $notificationBars = $('.hthb-notification');
		$notificationBars.each(function(){
			var data 	 = {};
			data.post_id = $(this).attr('id').split('-')[1];
			data.clicked = false;
			data.view 	 = true;

			if($(this).hasClass('hthb-state--open')){
				analytics(data);
			}

			$(this).find('.hthb-open-toggle').on('click',function(){
				data.clicked = false;
				data.view 	 = true;
				analytics(data);
			});

			$(this).find('.ht_btn').on('click',function(){
				data.clicked = true;
				data.view 	 = false;
				analytics(data);
			});

			$(this).find('.ht-promo-button').on('click',function(){
				data.clicked = true;
				data.view 	 = false;
				analytics(data);
			});
		});
	}

	function analytics(data){
		$.ajax({
			type: "POST",
			url: hashbar_analytical.ajaxurl,
			data: {
				action: "hashbar_analytics",
				nonce: data.nonce,
				id: data.post_id,
				clicked: data.clicked,
				view: data.view,
				nonce: hashbar_analytical.nonce_key,
			},
			success: function (response) {
				//response
			},
		});
	}

})(jQuery);