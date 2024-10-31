<?php 
$slug = $model->models['slugPage'];
$boardGroup = $model->models['boardGroup']; 
$action = empty($boardGroup) ? 'add' : 'update'
?> 

<?php $form = new SAFormHtml(); ?>
<?=$form->form(array('action'=>$slug.'&pageMode=action_board_group_'.$action)) ?>
	
	<table class="wp-list-table widefat fixed">
		<colgroup>
			<col style="width:15%;">
		</colgroup>
		<tbody>
			<tr>
				<th>그룹 ID</th>
				<td><?=$form->input('board_group_id',array('value'=>$boardGroup['board_group_id'])) ?></td>
			</tr>
			<tr>
				<th>그룹 제목</th>
				<td><?=$form->input('board_group_nm',array('value'=>$boardGroup['board_group_nm'])) ?></td>
			</tr>
		</tbody>
	</table>
	
	<div class="btns">
		<button class="button button-primary">등록</button>
		<a href="<?=$slug?>&pageMode=board_group" class="button button-primary">뒤로</a>
	</div>
	
	<?php if($action === 'update') {?>
		<?=$form->hidden('board_group_index',array('value'=>$boardGroup['board_group_index'])) ?>
	<?php }?>	
<?= $form->_form()?>