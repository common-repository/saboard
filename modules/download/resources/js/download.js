(function($) {

	$.clickDownCntUpdate = function($element, opts) {
		$element.each(function(index, item) {
			var fileName = $(this).attr('data-fileName');
			
			
			$(this).click(function(e){
				e.preventDefault();
				
				$.ajax({
					'url' : SA_GLOBAL.AJAX_URL,
					'data'  : {
						'action' : 'ajaxSaDownCnt',
						'fileName' : fileName,	
					},
					success : function(response){
						console.log(response);
					}
				});
			});
		});
	}

	$.fn.clickDownCntUpdate = function(opts) {
		return $.clickDownCntUpdate($(this), opts);
	}
	
	$(document).ready(function(){
		$('.clickDownCntUpdate').clickDownCntUpdate();
	});

})(jQuery);