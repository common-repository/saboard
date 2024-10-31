(function($) {
	$.sa_file_upload = function($element,opts) {
		var defaultOptions = {
			beforeSend : function(){},
			uploadProgress : function(event, position, total, percentComplete) {},
			successListener : function(response){},
			completeListener : function(response){},
			registerListener : function(){}
		};
		
		defaultOptions = $.extend(defaultOptions, opts);
		
		var options = {
			beforeSend : function() {
				$('#fileForm_progress').show('fast');
				defaultOptions.beforeSend.apply(this);
			},
			uploadProgress : function(event, position, total, percentComplete) {
				defaultOptions.uploadProgress.apply(this,[event, position, total, percentComplete]);
			},
			success : function(response) {
				if(response == null){
					return false;
				}
				
				var result = response.result;
				
				defaultOptions.successListener.apply(this,[response]);
				
				if (!result.result) {
					alert(result.errorMsg);

					return false;
				}

				var $viewBox = $('#fileForm_prev_image_view_box');
				var uploadfiles = response.files;
				var uploadUrl = response.uploadUrl;

				$.each(uploadfiles, function(i, item) {
					var fileName = item.uploadName;

					var $li = $('<li>', {

					}).appendTo($viewBox);

					var $a = $('<a>', {
						'href' : uploadUrl + '/' + fileName
					}).click(function(e) {
						e.preventDefault();
					}).appendTo($li);

					$('<img>', {
						'src' : uploadUrl + '/' + fileName
					}).css('max-width','100%').appendTo($a);

					$('<span>', {
						text : '삭제',
						click : function() {
							$li.hide('slow', function() {
								$.post(SA_GLOBAL.AJAX_URL, {
									'action' : 'ajaxSaFileDelete',
									'fileName' : fileName
								}, function(response) {
									$li.remove();
									$.fancybox.update();
								});
							});
						}
					}).appendTo($li);
					
					$.fancybox.update();
				});
			},
			complete : function(response) {
				$('#fileForm_progress').hide('fast');
				
				defaultOptions.completeListener.apply(this,[response]);
			},
			error : function(response) {
				alert('에러가 발생했습니다.');
			},
			dataType : 'json'
		};
		
		$element.attr('action', SA_GLOBAL.AJAX_URL);
		$element.ajaxForm(options);
	};
	
	$.fn.sa_file_upload = function(options){
		return this.each(function(){
			$.sa_file_upload($(this),options);
		});
	};
})(jQuery);