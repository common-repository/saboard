<?php $form = new SAFormHtml(); ?>

<?= $form->form(array('enctype'=>'multipart/form-data','id'=>'boardDomain'))?>
	<table>
		<tr>
			<th><?=$form->label('board_user_nm','작성자') ?></th>
			<td>
				<?=$form->input('board_user_nm')?>
				
			</td>
		</tr>
		
		<?php if($shortCodeView->boardTable->table['board_table_user_email_useyn'] == 'Y'){ ?>
		<tr>
			<th><?=$form->label('board_user_email', '이메일') ?></th>
			<td>
				<?=$form->input('board_user_email') ?>
			</td>
		</tr>
		<?php }?>
		
		<?php if($shortCodeView->boardTable->table['board_table_user_phone_useyn'] == 'Y'){ ?>
		<tr>
			<th><?=$form->label('board_user_phone', '전화번호') ?></th>
			<td>
				<?=$form->input('board_user_phone') ?>
			</td>
		</tr>
		<?php }?>
		
		<tr>
			<th><?=$form->label('board_title','제목') ?></th>
			<td><?=$form->input('board_title')?></td>
		</tr>
		
		<?php if(!is_user_logged_in()) { ?>
			<tr>
				<th><?=$form->label('board_password','비밀번호') ?></th>
				<td><?=$form->input('board_password',array('type'=>'password'))?></td>
			</tr>
			<tr>
				<th><?=$form->label('board_password_check','비밀번호체크') ?></th>
				<td><?=$form->input('board_password_check',array('type'=>'password'))?></td>
			</tr>
		<?php }?>
		
		<?php if($shortCodeView->boardTable->table['board_table_secret_useyn'] == 'Y') { ?>
			<tr>
				<th>비밀글 사용</th>
				<td>
					<?=$form->select('board_secret') ?>
						<?=$form->option(array('N'=>'미사용','Y'=>'사용'),$board_secret) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
		<?php }?>
		
		<tr>
			<th colspan="2"><?=$form->label('board_content','내용') ?></th>
		</tr>
		<tr>
			<td colspan="2">
				<?php $option = array('media_buttons'=>false); 
					  if($detect->isMobile()){
						$option['quicktags'] =  false;
						$option['tinymce'] =  false;
						$option['textarea_rows'] = 5;
					  }else{
						echo do_shortcode('[sa_file_upload_button]');
					  } 
				?>

				<?php sa_wp_editor($board_content, 'board_content',$option);?>
			</td>
		</tr>
		<tr>
			<th><?=$form->label('board_files[]','파일') ?></th>
			<td>
				<ul class="board_file_list">
					<?php for($i=0; $i< $shortCodeView->boardTable->table['board_table_file_cnt']; $i++) { ?>
						<li>
							<?= $form->input('board_files[]',array('type'=>'file')) ?>
							<?php 
								foreach($boardFileListDomain as $boardFileDomain){
									if($boardFileDomain['board_file_seq'] == $i){
										echo $boardFileDomain['board_file_name'];	
									}
								}
							?>
						</li>
					<?php } ?>
				</ul>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<div class="btns">
					<a href="#" class="bd_write">전송</a>
					<a href="#" class="bd_list">목록</a>
				</div>
			</td>
		</tr>	
	</table>	
	
	<?=$form->hidden('board_index')?>
	<?=$form->hidden('board_action')?>
	<?=$form->hidden('board_id')?>
	<?=$form->hidden('now_page')?>
	<?=$form->hidden('board_depth')?>
	<?=$form->hidden('board_parent')?>
	<?=$form->hidden('board_grp')?>
	<?=$form->hidden('board_order')?>

	<?php if(!empty($shortCodeView->page_id)){?>
		<?=$form->hidden('page_id')?>
	<?php }?>
	
	<?php wp_nonce_field('nonce_sa_board','nonce_sa_board'); ?>
<?= $form->_form()?>