<?php
// 반복해서 출력
// reply_step4 에서 닫아주면 된다.
?>
<ul class="board_reply">
	<?php if(empty($boardReplyListDomain)) {?>
		<li class="not-exists-reply">댓글이 존재하지 않습니다.</li>
	<?php }?>
