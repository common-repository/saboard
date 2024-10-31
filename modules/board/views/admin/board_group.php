<?php $slug = $model->models['slugPage']; ?>
<?php $groupList = $model->models['groupList']; ?>

	<table class="wp-list-table widefat fixed">
		<colgroup>
			<col style="width:15%;">
			<col>
			<col style="width:15%;">
		</colgroup>
		<thead>
			<tr>
				<th>그룹 ID</th>
				<th colspan="2">그룹 제목</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($groupList as $group) {?>
				<tr>
					<td><?=$group['board_group_id'] ?></td>
					<td><?=$group['board_group_nm'] ?></td>
					<td>
						<a href="<?=$slug ?>&pageMode=board_group_add&board_group_index=<?=$group['board_group_index'] ?>" 
						   class="button button-primary" 
						   >수정</a>
						<a href="<?=$slug ?>&pageMode=action_board_group_delete&board_group_index=<?=$group['board_group_index'] ?>" 
						   class="button button-primary user-confirm">삭제</a>
					</td>
				</tr>
			<?php }?>
		</tbody>
	</table>
	
	<div class="btns">
		<a href="<?=$slug ?>&pageMode=board_group_add" class="button button-primary">그룹 추가</a>
	</div>
