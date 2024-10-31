<?php 
	//반복문으로 돌아간다.
?>

<div class="reply_wrap">
	<?php if($boardReplyDomain['board_reply_use_yn'] == 'Y') { ?>
	<div class="reply_header">
		<h6>
			<span class="reply_tlt"><?=$boardReplyDomain["board_reply_title"] ?></span>
			<span class="reply_user"><?=$boardReplyDomain['board_reply_user_nm'] ?> 작성</span>
		</h6>
	</div>
	<div class="reply_content">
		<p><?=$boardReplyDomain["board_reply_content"] ?></p>
		<div class="btns">
			<a href="#" class="bd_reply_open" data-board_reply_depth="<?=$boardReplyDomain['board_reply_depth'] ?>"
											  data-board_reply_order="<?=$boardReplyDomain['board_reply_order'] ?>"
											  data-board_reply_index="<?=$boardReplyDomain['board_reply_index'] ?>"
											  data-board_reply_grp="<?=$boardReplyDomain['board_reply_grp'] ?>" >
			댓글작성</a>
			
			<a href="#" class="bd_reply_modify_open" data-board_reply_index="<?=$boardReplyDomain['board_reply_index'] ?>" 
													<?php if($boardReplyDomain['write_me']) {?>data-write_me="true"<?php }?>>댓글수정</a>
			
			<a href="#" class="bd_reply_delete_open" data-board_reply_index="<?=$boardReplyDomain['board_reply_index'] ?>"
													<?php if($boardReplyDomain['write_me']) {?>data-write_me="true"<?php }?>>댓글삭제</a>
		</div>
	</div>
	<?php } else {?>
		<h6>작성자에 의해 삭제된 댓글입니다.</h6>		
	<?php }?>
</div>
