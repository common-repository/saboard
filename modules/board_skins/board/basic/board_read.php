<?php $boardReadDomain = $shortCodeView->viewParam['boardReadDomain']; ?>
<div class="saboard_read_wrapper">
	<header>
		<h3><span class="icon32 icon32-1"></span><?=$boardReadDomain['board_title' ] ?></h3>
		<p>
			<span><?=date("Y/m/d", strtotime($boardReadDomain['board_reg_date'])) ?></span>
			<span>조회수 <?=$boardReadDomain['board_read_cnt'] ?></span>
			<span>
				작성자 <?=$boardReadDomain['board_user_nm'] ?>
				
				<?php if(!empty($boardReadDomain['board_user_email'])){ ?>
					(<?=$boardReadDomain['board_user_email'] ?>)
				<?php }?>
			</span>
		</p>
	</header>
	
	<section>
		<?=$boardReadDomain['board_content' ]?>
	</section>
	
	<footer>	
	
	<?php if(!empty($boardFileListDomain)){ ?>
		<div class="footer_files">
			<h4>첨부파일</h4>
			<ul>
			<?php foreach($boardFileListDomain as $boardFileDomain) {?>
				<li>
					<a href="#" class="board_filedown" data-fileName="<?=$boardFileDomain['board_file_name']?>">
						<?=sa_get_str_last_name($boardFileDomain['board_file_name'],'/');?>
					</a>
				</li>
			<?php }?>
			</ul>
		</div>
	<?php }?>
	
		<div class="footer_navigation">
			<ul>
				<li class="footer_navigation_prev">
					<?php if(empty($boardNavDomain['boardPrevDomain'])) { ?>
						이전 게시물이 존재하지 않습니다. 
					<?php } else {?>
					<a href="#" class="bd_read" data-index="<?=$boardNavDomain['boardPrevDomain']['board_index'] ?>">
						이전 : <?=$boardNavDomain['boardPrevDomain']['board_title'] ?>
					</a>
					<?php }?>
				</li>
				<li class="footer_navigation_next">
					<?php if(empty($boardNavDomain['boardNextDomain'])) { ?>
						다음 게시물이 존재하지 않습니다. 
					<?php } else {?>
					<a href="#" class="bd_read" data-index="<?=$boardNavDomain['boardNextDomain']['board_index'] ?>">
						다음 : <?=$boardNavDomain['boardNextDomain']['board_title'] ?>
					</a>
					<?php }?>
				</li>
			</ul>
		</div>
		
	</footer>	
	
	<div class="btns">
		<?php if($shortCodeView->user->getRole('write') && $shortCodeView->boardTable->table['board_table_comment_useyn'] == 'Y') {?>
			<a href="#" class="bd_comment" data-index="<?=$boardReadDomain['board_index'] ?>">덧글달기</a>
		<?php }?>
		
		<?php if($shortCodeView->user->getRole('modify')) {?>
			<a href="#" class="bd_modify" data-index="<?=$boardReadDomain['board_index'] ?>">수정</a>
		<?php }?>
		
		<?php if($shortCodeView->user->getRole('delete')) {?>
			<a href="#" class="bd_delete" data-index="<?=$boardReadDomain['board_index'] ?>">삭제</a>
		<?php }?>
		
		<a href="#" class="bd_list">목록으로</a>
	</div>
</div>