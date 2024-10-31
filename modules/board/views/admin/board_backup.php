<?php $slug = $model->models['slugPage']; ?>
<?php $backupList = $model->models['backupList']; ?>
<?php $pagination = $model->models['pagination']; ?>

<div class="backup">
	<div class="manage-menus">
		<span>현재 저장된 데이터 백업 : </span><a href="<?=$slug ?>&pageMode=action_backup" class="button" class="db_backup">백업</a> <strong>(워드프레스 정보는 백업되지 않습니다.)</strong>
	</div>
	
	<?php $form = new SAFormHtml(); ?>
	<?=$form->form(array('action'=>$slug.'&pageMode=action_backup_delete')) ?>
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="action">
					<option value="-1" selected="selected">일괄 작업</option>
					<option value="delete" class="hide-if-no-js">삭제</option>
				</select>
				<input type="submit" name="" id="doaction" class="button action" value="적용">
			</div>
		</div>
		<table class="wp-list-table widefat fixed backup-table">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
						<label class="screen-reader-text" for="cb-select-all-1">전체선택</label>
						<input id="cb-select-all-1" type="checkbox">
					</th>
					<th colspan="3">백업목록</th>
					<th>저장된파일수 : <?=count($backupList) ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th></th>
					<th>파일명</th>
					<th>백업일</th>
					<th>사이즈</th>
					<th>관리</th>
				</tr>
				<?php $end = $pagination->start_record + $pagination->page_per_record; ?>
				<?php for($i=$pagination->start_record;$i<$end; $i++){
						if($i > count($backupList)-1){
							break;
						} 
						$backup = $backupList[$i];  ?>
					<tr>
						<th scope="row" class="check-column">
							<label class="screen-reader-text">선택</label>
							<input type="checkbox" name="fileName[]" value="<?=$backup->fileLastName ?>">
							<div class="locked-indicator"></div>
						</th>
						<td><?=$backup->fileLastName ?></td>
						<td>
							<?= date('Y/m/d h:i',$backup->time) ?>
						</td>
						<td>
							<?= $backup->fileSize?> <span>bytes</span>
						</td>
						<td>
							<a href="<?=$slug ?>&pageMode=action_backup_restore&fileName=<?=$backup->fileLastName ?>" 
							   class="button button-primary user-confirm"
							   data-message="복원하시겠습니까? 미리 현재 데이터를 백업하시기 바랍니다.">복원</a>
							<a href="<?=$slug ?>&pageMode=action_backup_delete&fileName=<?=$backup->fileLastName ?>" 
							   class="button button-primary user-confirm"
							   data-message="삭제된 파일은 복구할수 없습니다. 계속 진행하시겠습니까?" >삭제</a>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
		
		<div class="sa-pagination">
			<?php if( $pagination->hasPrevBlock() ) { ?>
				<a href="#" data-page="<?=$pagination->prev_block ?>" class="prev_block">이전</a>
			<?php }?>
					
			<?php for($i = $pagination->start_page; $i <= $pagination->end_page; $i++){?>
				<a href="#" class="<?php if($pagination->now_page==$i){echo "on";}?> " data-page="<?=$i ?>"><?=$i ?></a>	
			<?php }?>
					
			<?php if( $pagination->hasNextBlock()){?>
				<a href="#" data-page="<?=$pagination->next_block ?>" class="next_block">다음</a>
			<?php }?>
		</div>
	<?=$form->_form() ?>
</div>