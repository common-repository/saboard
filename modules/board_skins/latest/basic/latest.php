<div class="saboard_latest saboard_latest_<?=$boardTable['board_table_id']?>">
	<h4><?=$boardTable['board_table_nm'] ?></h4>
	<ul>
		<?php foreach($boardDomainList as $boardDomain) {?>
			<li><a href="#"><?=$boardDomain['board_title'] ?></a></li>
		<?php }?>
		<?php if(empty($boardDomainList)) {?>
			<li> 등록된 게시물이 존재하지 않습니다.</li>
		<?php }?>
	</ul>
</div>