<table>
	<colgroup>
		<col style="width: 7%;">
		<col style="width: 60%;">
		<col style="width: 13%;">
		<col style="width: 10%;">
		<col style="width: 10%;">
	</colgroup>
	<thead>
	<tr>
		<th class="board_row_num"><?= _e('no','sa_board') ?></th>
		<?php if($shortCodeView->boardTable->is_show_column('board_title')){?>
			<th class="board_title"><?= __('title','sa_board') ?></th>
		<?php }?>
		<?php if($shortCodeView->boardTable->is_show_column('board_user_nm')){?>
			<th class="board_reg_user"><?= __('user','sa_board') ?></th>
		<?php }?>
		<?php if($shortCodeView->boardTable->is_show_column('board_reg_date')){?>
			<th class="board_reg_date"><?= __('date','sa_board') ?></th>
		<?php }?>
		<?php if($shortCodeView->boardTable->is_show_column('board_read_cnt')){?>
			<th class="board_read_cnt"><?= __('view','sa_board') ?></th>
		<?php }?>
	</tr>
	</thead>
	<tbody>
		<?php foreach($board_list as $board) {?>
			<tr> 
				<td class="board_row_num"><?=$num-- ?></td>
				
				<?php if($shortCodeView->boardTable->is_show_column('board_title')){?>
					<td class="board_title bd_read text-left" data-index="<?=$board['board_index'] ?>">
						 <?php for($i=0;$i<$board['board_depth'];$i++) { ?>[RE]<?php }?>
						 
						 <?= sa_str_cut($board['board_title'] , $shortCodeView->boardTable->table['board_table_title_cut']) ?>

						 <?php if($board['board_secret'] == 'Y') {?> (비밀글)<?php }?>
						
						<?php if($board['board_reply_cnt'] > 0) {?>
							<span class="reply_cnt">(<?=$board['board_reply_cnt'] ?>)</span>
						<?php }?>
					</td>
				<?php }?>
				
				<?php if($shortCodeView->boardTable->is_show_column('board_user_nm')){?>
					<td class="board_reg_user"><?=$board['board_user_nm'] ?></td>
				<?php }?>
				
				<?php if($shortCodeView->boardTable->is_show_column('board_reg_date')){?>
					<td class="board_reg_date"><?=date("Y/m/d", strtotime($board['board_reg_date'])) ?></td>
				<?php }?>
				
				<?php if($shortCodeView->boardTable->is_show_column('board_read_cnt')){?>
					<td class="board_read_cnt"><?=$board['board_read_cnt'] ?></td>
				<?php }?>
				
			</tr>
		<?php } ?>
		<?php if(empty($board_list)) {?>	
			<tr>
				<td colspan="5">게시물이 존재하지 않습니다.</td>
			</tr>
		<?php }?>
	</tbody>
</table>

<?php if($shortCodeView->user->getRole('write')) {?>
	<div class="btns">
		<a href="#" class="bd_write"><?=__('write','sa_board') ?></a>
	</div>
<?php }?>