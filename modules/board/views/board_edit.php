<?php include_once $shortCodeView->path['boardThemePath'].'/board_edit.php';?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#boardDomain').saboard({
				board_list    : $('.bd_list'),
				board_write   : $('.bd_write')
			});

			$('#boardDomain').validate({
				errorPlacement: function(error, element) {
					error.appendTo(element.siblings(".error"));
				},rules : {
					board_user_nm 		 : { required : true },
					board_title 		 : { required : true },
					board_password 		 : { required : true },
					board_password_check : { required : true },
					board_user_email 	 : { required : true, email:true },
					board_user_phone 	 : { required : true, number:true }
				}
			});
		});
		
		$(window).load(function(){
			$("#fileForm").sa_file_upload({
				completeListener : function(){
					var output = '';

					$('#fileForm_prev_image_view_box li a').each(function(){
						output += $(this).outerHTML();
					});

					$('#fileForm_prev_image_view_box').empty();

					if($('#board_content').css('display') == 'block'){
						var o = $('#board_content').val();
						$('#board_content').val(o + output);
					}else{
						tinyMCE.activeEditor.execCommand('mceInsertContent', false, output);
					}
					
					$.fancybox.close();
				}
			});
		});
	})(jQuery);
</script>