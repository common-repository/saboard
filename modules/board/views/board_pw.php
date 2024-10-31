<?php include_once $shortCodeView->path['boardThemePath'].'/board_pw.php';?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#boardDomain').saboard({
				board_list    : $('.bd_list'),
				board_write   : $('.bd_write'),
				board_modify  : $('.bd_modify'),
				board_pw_check : $('.bd_pw_check')
			});

			$('#boardDomain').validate({
				errorPlacement: function(error, element) {
					error.appendTo(element.siblings(".error"));
				},rules : {
					board_password 		 : {required : true}
				}
			});
		});
	})(jQuery);
</script>