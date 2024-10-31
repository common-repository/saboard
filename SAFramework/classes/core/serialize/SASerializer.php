<?php
if(!class_exists('SASerializer')){
	class SASerializer {
		public static function serialize($obj) {
			return is_array ( $obj ) || is_object ( $obj ) ? base64_encode ( serialize ( $obj ) ) : false;
		}
		
		public static function unserialize($obj) {
			$obj = base64_decode ( $obj );
			
			return is_serialized ( $obj ) ? unserialize ( $obj ) : false;
		}
	}
}