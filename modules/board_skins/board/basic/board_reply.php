<?php $shortCodeView -> print_reply (); ?>

<?php $form = new SAFormHtml(); ?>
<?= $form->form(array('id'=>'boardReplyDomain','style'=>'display:none;','class'=>'wp-core-ui')) ?>
	<div class="board_reply_wrapper">
		<dl>
			<dt><?=$form->label('board_reply_user_nm', '작성자') ?></dt>
			<dd><?= $form->input( 'board_reply_user_nm' , array('value'=>sa_get_current_user_nm()) ) ?></dd>
		</dl>
		<?php if(!is_user_logged_in()) {?>
		<dl>
			<dt><?=$form->label('board_reply_password', '비밀번호') ?></dt>
			<dd><?= $form->input('board_reply_password',array('type'=>'password')) ?></dd>
		</dl>
		<?php }?>
		<dl>
			<dt><?=$form->label('board_reply_title', '제목') ?></dt>
			<dd><?= $form->input('board_reply_title') ?></dd>
		</dl>
		<dl>
			<dt><?=$form->label('board_reply_content', '내용') ?></dt>
			<dd><?= $form->textArea('board_reply_content','',array('rows'=>6)) ?></dd>
		</dl>
		
		<div class="btns">
			<a href="#" class="bd_reply button">댓글전송</a>
		</div>
	</div>
	
	<?= $form->hidden('board_reply_depth') ?>
	<?= $form->hidden('board_reply_order') ?>
	<?= $form->hidden('board_reply_index') ?>
	<?= $form->hidden('board_reply_grp') ?>
	<?= $form->hidden('board_reply_mode') ?>
		
	<?php wp_nonce_field('nonce_sa_board','nonce_sa_board'); ?>
<?= $form->_form()?>

<?php $form = new SAFormHtml(); ?>
<?= $form->form(array('id'=>'boardReplyPasswordDomain','style'=>'display:none;','class'=>'wp-core-ui')) ?>
	<div class="board_reply_wrapper">
		<dl>
			<dt><?=$form->label('board_reply_password', '비밀번호') ?></dt>
			<dd><?= $form->input('board_reply_password',array('type'=>'password')) ?></dd>
		</dl>
		<div class="btns">
			<a href="#" class="bd_reply_pw_check button">수정</a>
		</div>
	</div>
	
	<?= $form->hidden('board_pw_mode') ?>
	<?= $form->hidden('board_reply_index') ?>
	
	<?php wp_nonce_field('nonce_sa_board','nonce_sa_board'); ?>
<?= $form->_form()?>