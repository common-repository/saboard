<?php include_once $shortCodeView->path['boardThemePath'].'/board_read.php';?>

<?php $form = new SAFormHtml(); ?>
<?= $form->form(array('id'=>'boardDomain'))?>
	
	<?php if(!empty($shortCodeView->page_id)){?>
		<?=$form->hidden('page_id')?>
	<?php }?>
	
	<?=$form->hidden('now_page')?>
	<?=$form->hidden('board_id')?>
	<?=$form->hidden('board_index')?>
	<?=$form->hidden('board_mode')?>
	<?=$form->hidden('board_action',array('value'=>''))?>
	
	<?php wp_nonce_field('nonce_sa_board','nonce_sa_board'); ?>
<?= $form->_form() ?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#boardDomain').saboard({
				board_list    : $('.bd_list'),
				board_modify  : $('.bd_modify'),
				board_delete  : $('.bd_delete'),
				board_read    : $('.bd_read'),
				board_write   : $('.bd_write'),
				board_comment : $('.bd_comment')
			});	
		});	
	})(jQuery);
</script>