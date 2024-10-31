<?php
if(!class_exists('SACategory')){ 
	class SACategory{
		static $a;
		
		public static function countQuery( $query ) {
			if(is_category() && empty($query->query['post_type'])){
				global $cat;
				
				$query->set( 'category__in' , array( $cat) );
				$query->set( 'posts_per_page' , self::$a );
				
			}
			return $query;
		}
		
		public static function setCategoryShowcount($showCount){
			self::$a = $showCount;
			
			add_filter( 'pre_get_posts' , array(__CLASS__,'countQuery') );
		}
		
	}
}