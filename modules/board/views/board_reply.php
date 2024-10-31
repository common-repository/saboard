<?php include_once $shortCodeView->path['boardThemePath'].'/board_reply.php';?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#boardReplyDomain').saboard({
				board_reply   	   : $('.bd_reply'),
				board_reply_open   : $('.bd_reply_open'),
				board_reply_delete : $('.bd_reply_delete')
			});

			$('#boardReplyDomain').validate({
				errorPlacement: function(error, element) {
					error.appendTo(element.siblings(".error"));
				},rules : {
					board_reply_user_nm 	: {required : true},
					board_reply_password 	: {required : true},
					board_reply_title 		: {required : true},
					board_reply_content 	: {required : true}
				}
			});
			
			$('#boardReplyPasswordDomain').saboard({
				board_reply_delete_open : $('.bd_reply_delete_open'),
				board_reply_pw_check    : $('.bd_reply_pw_check'),
				board_reply_modify_open : $('.bd_reply_modify_open')
			});
		});	
	})(jQuery);
</script>