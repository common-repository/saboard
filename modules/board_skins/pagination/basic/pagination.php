<div class="sa-pagination sa-pagination-centered">
	<ul>
		<?php if( $pagination->hasPrevBlock() ) { ?>
			<li><a href="#" data-page="<?=$pagination->prev_block ?>" class="prev_block">&lt;</a></li>
		<?php }?>
			
		<?php for($i = $pagination->start_page; $i <= $pagination->end_page; $i++){?>
			<li class="<?php if($pagination->now_page==$i){echo "on active";}?>"><a href="#" data-page="<?=$i ?>"><?=$i ?></a></li>	
		<?php }?>
			
		<?php if( $pagination->hasNextBlock()){?>
			<li class="#"><a href="#" data-page="<?=$pagination->next_block ?>" class="next_block">&gt;</a></li>
		<?php }?>
	</ul>
</div>