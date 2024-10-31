<div class="sa-pagination">
	<?php if( $pagination->hasPrevBlock() ) { ?>
		<a href="#" data-page="<?=$pagination->prev_block ?>" class="prev_block">이전</a>
	<?php }?>
			
	<?php for($i = $pagination->start_page; $i <= $pagination->end_page; $i++){?>
		<a href="#" class="<?php if($pagination->now_page==$i){echo "on";}?> <?=$paginationName ?>" data-page="<?=$i ?>"><?=$i ?></a>	
	<?php }?>
			
	<?php if( $pagination->hasNextBlock()){?>
		<a href="#" data-page="<?=$pagination->next_block ?>" class="next_block">다음</a>
	<?php }?>
</div>