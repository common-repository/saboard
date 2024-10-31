<?php
/**
 * @param now_page 
 * 		현재페이지
 * @uses 
 * 		setTotal_record 메소드로 합계를 세팅한후 사용한다.
 * @author oks
 *
 */
if(!class_exists('SAPagination')){
	class SAPagination {
		var $now_page;
		var $page_per_record;
		var $block_per_page;
		var $now_block;
		var $prev_block;
		var $next_block;
		var $start_page;
		var $start_record;
		var $total_record;
		var $total_page;
		var $total_block;
		var $end_page;
		
		public function __construct($args = array()) {
			$default = array('page_per_record' => 10,'block_per_page' => 5);
			
			$args = sa_parse_args($args,$default);
			
			$now_page = SARequest::getParameter('now_page');
	
			if (!$now_page || $now_page < 0) $now_page = 1;
			
			$this->now_page 	   = $now_page;
			$this->page_per_record = $args['page_per_record']; //노출되는 줄의 수
			$this->block_per_page  = $args['block_per_page'];  //한 블럭당 표시될수 있는 페이징 번호의수
			
			$this->now_block = ceil ( $now_page / $this->block_per_page );
			$this->prev_block = ceil ( (($this->now_block - 1) * $this->block_per_page) - ($this->block_per_page - 1) );
			$this->next_block = ceil ( (($this->now_block + 1) * $this->block_per_page) - ($this->block_per_page - 1) );
			$this->start_page = (($this->now_block - 1) * $this->block_per_page) + 1;
			$this->start_record = (($this->now_page - 1) * $this->page_per_record);
		}
		
		/**
		 *  게시물의 총 게수를 지정한다.
		 * @param unknown_type $total_record
		 */
		public function setTotal_record($total_record) {
			$this->total_record = $total_record;
			$this->total_page = ceil ( $this->total_record / $this->page_per_record );
			$this->total_block = ceil ( $this->total_page / $this->block_per_page );
			$this->end_page = (($this->start_page + $this->block_per_page) <= $this->total_page) ? ($this->start_page + $this->block_per_page) : $this->total_page;
		}
		
		public function hasNextBlock(){
			return $this->now_block < $this->total_block;
		}
		
		public function hasPrevBlock(){
			return $this->now_block > 1;
		}
		
		/**
		 * 일반적인 페이징 처리 
		 */
		public function makePagination(){?>
			<div class="paging">
				<?php if( $this->hasPrevBlock() ) { ?><a href="?now_page=<?=$this->prev_block ?>">이전</a><?php }?>
					<?php for($i = $this->start_page; $i <= $this->end_page; $i++){?>
						<a href="?now_page=<?=$i ?>" <?php if($this->now_page == $i){echo 'class="on"';}?> data-page=<?=$i ?>><?=$i ?></a>	
					<?php }?>
				<?php if( $this->hasNextBlock()){?><a href="?now_page=<?=$this->next_block ?>">다음</a><?php }?>
			</div>
		<?php }
		
		/**
		 * 별도의 다른 파라미터를 넘기기 위해 사용한다.
		 * @example
		 *	<script type="text/javascript"> function goPage($page) {}</script>
		 */
		public function makeScriptPagination($scriptName){?>
			<div class="pagination">
				<?php if( $this->hasPrevBlock() ) { ?><a href="Javascript:<?=$scriptName ?>(<?= $this->prev_block ?>)">이전</a><?php }?>
					<?php for($i = $this->start_page; $i <= $this->end_page; $i++){?>
						<a href="Javascript:<?=$scriptName ?>(<?=$i ?>)" class="<?php if($this->now_page==$i){echo "on";}?>" data-page=<?=$i ?>><?=$i ?></a>	
					<?php }?>
				<?php if( $this->hasNextBlock()){?><a href="Javascript:<?=$scriptName ?>(<?=$this->next_block ?>)">다음</a><?php }?>
			</div>
		<?php }
		
		public function makeBasicPagination($paginationName){?>
			<div class="pagination">
				<?php if( $this->hasPrevBlock() ) { ?>
					<a href="#" data-prev_block="<?=$this->prev_block ?>" class="prev_block">이전</a>
				<?php }?>
				
				<?php for($i = $this->start_page; $i <= $this->end_page; $i++){?>
					<a href="#" class="<?php if($this->now_page==$i){echo "on";}?> <?=$paginationName ?>" data-page="<?=$i ?>"><?=$i ?></a>	
				<?php }?>
				
				<?php if( $this->hasNextBlock()){?>
					<a href="#" data-next_block="<?=$this->next_block ?>" class="next_block">다음</a>
				<?php }?>
			</div>
		<?php }
		
		public function makeBasicListPagination($paginationName){?>
			<div class="pagination">
				<ul>
				<?php if( $this->hasPrevBlock() ) { ?>
					<li class="prev_block"><a href="#" data-prev_block="<?=$this->prev_block ?>">이전</a></li>
				<?php }?>
				
				<?php for($i = $this->start_page; $i <= $this->end_page; $i++){?>
					<li><a href="#" class="<?php if($this->now_page==$i){echo "on";}?> <?=$paginationName ?>" data-page="<?=$i ?>"><?=$i ?></a></li>	
				<?php }?>
				
				<?php if( $this->hasNextBlock()){?>
					<li class="next_block"><a href="#" data-next_block="<?=$this->next_block ?>">다음</a></li>
				<?php }?>
				</ul>
			</div>	
		<?php }
	}
}