<?php $form = new SAFormHtml(); ?>
<?php $args = array(''=>'전체검색','board_title'=>'제목','board_content'=>'내용','board_user_nm'=>'작성자'); ?>

<?= $form->form(array('id'=>'boardSearchDomain','class'=>'wp-core-ui')) ?>
	
	<?= $form->select('searchDiv') ?>
		<?= $form->option($args,SARequest::getParameter('searchDiv')) ?>
	<?= $form->_select(); ?>
	
	<div class="input-append">
		<?= $form->input('searchValue',array('class'=>'span2')) ?>
	    <button class="btn" type="button">검색</button>
	</div>

<?= $form->_form();?>