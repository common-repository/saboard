<?php
if(!class_exists('SAPostsPagination')){
	class SAPostsPagination {
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
			global $paged,$cat,$wp_query;
			
			$category = get_category($cat);
			$default = array('page_per_record' => get_query_var('posts_per_page'),'block_per_page' => 5);
			
			$args = sa_parse_args($args,$default);
		
			$this->now_page 	   = $paged;
			if (!$this->now_page || $this->now_page < 0) $this->now_page = 1;
			
			$this->page_per_record = $args['page_per_record']; //노출되는 줄의 수
			$this->block_per_page  = $args['block_per_page'];  //한 블럭당 표시될수 있는 페이징 번호의수
		
			$this->now_block = ceil ( $this->now_page / $this->block_per_page );
			$this->prev_block = ceil ( (($this->now_block - 1) * $this->block_per_page) - ($this->block_per_page - 1) );
			$this->next_block = ceil ( (($this->now_block + 1) * $this->block_per_page) - ($this->block_per_page - 1) );
			$this->start_page = (($this->now_block - 1) * $this->block_per_page) + 1;
			$this->start_record = (($this->now_page - 1) * $this->page_per_record);
			
			$this->total_record = $category->count;
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
			<div class="pagination">
				<ul>
					<?php if( $this->hasPrevBlock() ) { ?>
						<li><a href="<?=get_pagenum_link() ?>&paged=<?=$this->prev_block ?>">이전</a></li>
					<?php }?>
					<?php for($i = $this->start_page; $i <= $this->end_page; $i++){?>
						<li <?php if($this->now_page == $i){echo 'class="active"';}?>><a href="<?=get_pagenum_link() ?>&paged=<?=$i ?>"><?=$i ?></a></li>	
					<?php }?>
					<?php if( $this->hasNextBlock()){?>
						<li><a href="<?=get_pagenum_link() ?>&paged=<?=$this->next_block ?>">다음</a></li>
					<?php }?>
				</ul>
			</div>
		<?php }
		
	}
}