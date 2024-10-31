<?php $form = new SAFormHtml(); ?>
<?php $args = array(''=>'전체검색','board_title'=>'제목','board_content'=>'내용','board_user_nm'=>'작성자'); ?>

<?= $form->form(array('id'=>'boardSearchDomain','class'=>'wp-core-ui')) ?>
	
	<?= $form->select('searchDiv') ?>
		<?= $form->option($args,SARequest::getParameter('searchDiv')) ?>
	<?= $form->_select(); ?>
	
	<?= $form->input('searchValue') ?>
	
	<input type="submit" value="검색" class="button" />
	
<?= $form->_form();?>