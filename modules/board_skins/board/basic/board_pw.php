<?php $form = new SAFormHtml(); ?>

<?= $form->form(array('id'=>'boardDomain','action'=>'','class'=>'sa_board_password_check'))?>
	<dl>
		<dt><?=$board_title ?></dt>
		<dd>비밀번호</dd>
		<dd><?=$form->input('board_password',array('type'=>'password')) ?></dd>
		<dd>
			<div class="btns text-left">
				<?php if($board_action == 'update' || $board_action == 'modify'){?> <a href="#" class="bd_modify" data-index="<?=$board_index ?>">수정</a><?php }?>
				
				<?php if($board_action == 'delete'){?> <a href="#" class="bd_modify" data-index="<?=$board_index ?>">삭제</a><?php }?>
				
				<?php if($board_action == 'pw_check'){?><a href="#" class="bd_pw_check" data-index="<?=$board_index ?>">확인</a><?php }?>
				
				<a href="#" class="bd_list">목록</a>
			</div>
		</dd>
	</dl>
	
	<?=$form->hidden('board_action')?>
	<?=$form->hidden('board_index')?>
	<?=$form->hidden('board_id')?>
	<?=$form->hidden('now_page')?>
	<?=$form->hidden('board_pw_mode')?>
	
	<?php if(!empty($shortCodeView->page_id)){?>
		<?=$form->hidden('page_id')?>
	<?php }?>
	<?php wp_nonce_field('nonce_sa_board','nonce_sa_board'); ?>
<?= $form->_form() ?>