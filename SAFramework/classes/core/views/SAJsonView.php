<?php
if(!class_exists('SAJsonView')){
	class SAJsonView {
		public function view($params = array(),$file="") {
			header ( "Content-Type:application/json;charset=utf-8" );
				
			$res = ob_get_clean ();
			ob_start ();
			
			echo json_encode ( $params );
			
			$res = ob_get_clean ();
			
			echo $res;
			
			exit ();
		}
		
		public function getView($params = array(),$file=""){
			$res = ob_get_clean ();
			ob_start ();
			
			echo json_encode ( $params );
			
			$res = ob_get_clean ();
			
			return $res;
		}
		
		private static $instance;
		
		public static function getInstance() {
			if (! isset ( self::$instance )) {
				self::$instance = new SAJsonView();
			}
		
			return self::$instance;
		}
	}
}