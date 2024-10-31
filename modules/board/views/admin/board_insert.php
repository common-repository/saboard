<?php $slug = $model->models['slugPage']; ?>
<?php $action = $model->models['action'];?>
<?php $board_option = $model->models['board_option'];?>

<?php $form = new SAFormHtml(); ?>
<?=$form->form(array('action'=>$slug.'&pageMode=action_board_'.$action)) ?>
	<table class="wp-list-table widefat fixed">
		<colgroup>
			<col style="width:30%;">
		</colgroup>
		<thead>
			<tr>
				<th colspan="2">
					<strong>게시판을 
						<?php if($action == 'update') {?>
							수정
						<?php }else{?>
							생성
						<?php }?>
					하세요
					</strong>
				</th>
			</tr>
		</thead>
		<tbody>
		
			<tr>
				<th>게시판 그룹</th>
				<td>
					<select name="board_group_index" id="board_group_index">
						<?php foreach($model->models['boardGroupListDomain'] as $boardGroup){?>
							<option value="<?=$boardGroup['board_group_index'] ?>" 
								<?php if($board_group_index == $boardGroup['board_group_index']) {?>
									selected="selected"
								<?php }?>>
								<?=$boardGroup['board_group_nm'] ?>
							</option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<th>게시판 아이디</th>
				<td>
					<?php if($action == 'update') {?>
						<input type="text" value="<?=$board_table_id ?>" disabled="disabled" style="background:#dedede;"/>
						<?=$form->hidden('board_table_id') ?>
					<?php } else {?>	
						<?=$form->input('board_table_id') ?>
					<?php }?>
				</td>
			</tr>
			<tr>
				<th>게시판 명</th>
				<td><?=$form->input('board_table_nm') ?></td>
			</tr>
			<tr>
				<th>게시판 설명</th>
				<td><?=$form->input('board_table_desc') ?></td>			
			</tr>
			<tr>
				<th>게시판 노출 필드</th>
				<td style="overflow: visible;">
					<select name="board_table_show_columns[]" id="board_table_show_columns" multiple class="chosen-select chosen" >
						<?php foreach($board_fields as $key=>$value){ ?>
							<?php $sel = '';
								if(empty($board_table_show_columns)) {
									$sel = 'selected="selected"';
								}else{
									foreach($board_table_show_columns as $column){
										if($column == $key){
											$sel = 'selected="selected"';
										}
									}
								}
							?>
							<option value="<?=$key ?>" <?=$sel ?>><?=$value ?></option>
						<?php }?>
					</select>
				</td>			
			</tr>
			
			<tr>
				<th>게시물 쓰기 추가 필드</th>
				<td style="overflow: visible;">
					<select name="board_table_user_option[]" id="board_table_user_option" multiple class="chosen-select chosen" >
						<option value="board_table_user_email_useyn" <?php if($board_table_user_email_useyn == 'Y'){?>selected="selected"<?php }?>>
							이메일
						</option>
						<option value="board_table_user_phone_useyn" <?php if($board_table_user_phone_useyn == 'Y'){?>selected="selected"<?php }?>>
							전화번호
						</option>
					</select>
				</td> 
			</tr>
			
			<tr>
				<th>게시판에 새로운 글 등록시 메일 전송 여부</th>
				<td>
					<?=$form->select('board_table_insert_mail_yn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_insert_mail_yn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			
			<tr>
				<th>게시판에 새로운 댓글 등록시 메일 전송 여부</th>
				<td>
					<?=$form->select('board_table_reply_insert_mail_yn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_reply_insert_mail_yn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
						
			<tr>
				<th>게시판 리스트 제목 글자수</th>
				<td>
					<?=$form->input('board_table_title_cut') ?>
				</td>
			</tr>
			<tr>
				<th>게시판 스킨</th>
				<td>
					<?=$form->select('board_table_theme') ?>
						<?=$form->option($model->models['skins']['board'],$board_table_theme,true) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>페이징 스킨</th>
				<td>
					<?=$form->select('board_table_theme_pagination') ?>
						<?=$form->option($model->models['skins']['pagination'],$board_table_theme_pagination,true) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>댓글 스킨</th>
				<td>
					<?=$form->select('board_table_theme_reply') ?>
						<?=$form->option($model->models['skins']['reply'],$board_table_theme_reply,true) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>검색 스킨</th>
				<td>
					<?=$form->select('board_table_theme_search') ?>
						<?=$form->option($model->models['skins']['search'],$board_table_theme_search,true) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>읽기권한</th>
				<td>
					<select name="board_table_read_role">
						<?= SABoardAdminService::getInstance()->getBoard_dropdown_roles($board_table_read_role) ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>쓰기권한</th>
				<td>
					<select name="board_table_write_role">
						<?= SABoardAdminService::getInstance()->getBoard_dropdown_roles($board_table_write_role) ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>댓글 사용유무</th>
				<td>
					<?=$form->select('board_table_reply_useyn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_reply_useyn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>덧글 사용유무</th>
				<td>
					<?=$form->select('board_table_comment_useyn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_comment_useyn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>비밀글 사용유무</th>
				<td>
					<?=$form->select('board_table_secret_useyn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_secret_useyn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>검색 사용유무</th>
				<td>
					<?=$form->select('board_table_search_useyn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_search_useyn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>SEO 사용유무</th>
				<td>
					<?=$form->select('board_table_seo_useyn') ?>
						<?=$form->option(array('Y'=>'사용','N'=>'미사용'),$board_table_seo_useyn) ?>
					<?=$form->_select() ?>
				</td>
			</tr>
			<tr>
				<th>페이지당 뿌려질 목록수</th>
				<td>
					<?=$form->input('board_table_list_cnt') ?>
				</td>
			</tr>
			<tr>
				<th>업로드 가능한 파일 개수</th>
				<td>
					<?=$form->input('board_table_file_cnt') ?>
				</td>
			</tr>
			<tr>
				<th>업로드 가능한 최대 파일 사이즈</th>
				<td>
					<?=$form->input('board_table_file_max_size') ?> <span>bytes</span>
				</td>
			</tr>
			<tr>
				<th>게시물 기본 양식</th>
				<td>
					<?php wp_editor($board_table_default_content, 'board_table_default_content');?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<a href="<?=$slug ?>&manager=board_list" class="button-primary" style="float: right;">목록</a>
					<input type="submit" class="button-primary" value="<?php if($action == 'update'){?>수정<?php }?><?php if($action == 'insert'){?>등록<?php }?>" />
				</td>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$('.chosen').chosen({
					width:"95%"
				});
			});
		})(jQuery);				
	</script>
	<?=$form->hidden('board_table_index') ?>
<?=$form->_form() ?>