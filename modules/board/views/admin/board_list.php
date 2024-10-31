<?php $form = new SAFormHtml(); ?>
<?=$form->form(array('action'=>$model->models['slugPage'].'&manager=action_board_insert')) ?>
	<table class="wp-list-table widefat fixed">
		<thead>
			<tr>
				<th>게시판 그룹</th>
				<th>게시판 아이디</th>
				<th>게시판 명</th>
				<th>게시판 설명</th>
				<th>숏코드</th>
				<th></th>			
			</tr>
		</thead>
		<tbody>
			<?php foreach($model->get('boardTableList') as $board_table) {?>
				<tr>
					<td><?=$board_table['board_group_id'] ?></td>
					<td>
						<a href="<?=$model->get('slugPage') ?>&pageMode=board_insert&board_table_id=<?=$board_table['board_table_id'] ?>">
							<?=$board_table['board_table_id'] ?>
						</a>
					</td>
					<td>
						<?=$board_table['board_table_nm'] ?>
					</td>
					<td>
						<?=$board_table['board_table_desc'] ?>
					</td>
					<td>
						[sa_board boardid="<?=$board_table['board_table_id'] ?>"]
					</td>
					<td>
						<a href="<?=$model->get('slugPage') ?>&pageMode=board_insert&board_table_id=<?=$board_table['board_table_id'] ?>" class="button button-primary">수정</a>
						<a href="<?=$model->get('slugPage') ?>&pageMode=action_board_delete&board_table_index=<?=$board_table['board_table_index'] ?>" 
						   class="button button-primary user-confirm"
						   data-message="데이터를 삭제하시기전에 백업한 데이터가 있는지 확인하시기 바랍니다. 계속하시겠습니까?">삭제</a>
					</td>
				</tr>
			<?php }?>
		</tbody>
	</table>
<?=$form->_form() ?>

<div class="btns">
	<a href="<?=$model->models['slugPage'] ?>&pageMode=board_insert" class="button button-primary">게시판 생성</a>
</div>