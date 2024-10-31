<?php $slug = $model->models['slugPage']; ?>

<form action="<?=$slug ?>&manager=action_file_delete" method="post">
	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="action">
				<option value="-1" selected="selected">일괄 작업</option>
				<option value="delete" class="hide-if-no-js">삭제</option>
			</select>
			<input type="submit" name="" id="doaction" class="button action" value="적용">
		</div>
	</div>
	
	<table class="wp-list-table widefat fixed">
		<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style="">
				<label class="screen-reader-text" for="cb-select-all-1">전체선택</label>
				<input id="cb-select-all-1" type="checkbox">
			</th>
			<th>파일명</th>
			<th>업로드파일명</th>
			<th>크기</th>
			<th>아이피</th>
			<th>사용유무</th>
			<th>관리</th>
		</tr>
		</thead>
		
		<tbody>
		<?php foreach($model->models['uploadFileList'] as $uploadFile) {?>
			<?php foreach($uploadFile['files'] as $file){?>
			<?php $attach_image = SABoardService::getInstance()->getBoardDomainAttachmentImage(array('attach_name'=>$file->getUploadName()));?>
			<tr>
				<th scope="row" class="check-column">
					<label class="screen-reader-text">선택</label>
					<input type="checkbox" name="fileName[]" value="<?=$file->getUploadName() ?>">
					<div class="locked-indicator"></div>
				</th>
				<td><?=$file->getName() ?></td>
				<td><?=$file->getUploadName() ?></td>
				<td><?=$file->getSize() ?> <span>bytes</span></td>
				<td><?=$file->uploaderIp ?></td>
				<td>
					<?php if(empty($attach_image)){?>
						사용안됨
					<?php } else{ ?>
						게시물번호 : <?=$attach_image['board_index'] ?> 
					<?php }?>
				</td>
				<td>
					<a href="<?=$slug ?>&pageMode=action_file_delete&fileName=<?=$file->getUploadName() ?>" class="button button-primary">파일삭제</a>
				</td>
			</tr>
			<?php }?>
		<?php }?>
		</tbody>
	</table>
</form>