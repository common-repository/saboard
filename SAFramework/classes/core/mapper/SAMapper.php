<?php
if(!class_exists('SAMapper')){
	class SAMapper {
		public static function array_to_object($array,$object){
			if (! is_object ( $object )) {
				echo 'Argument is not object';
				return false;
			}
			
			$reflection = new ReflectionClass ( $object );
			
			$properties = $reflection->getProperties ();
			
			foreach ( $properties as $property ) {
				$property->setAccessible ( true );
			
				$name = $property->getName ();
				$value = $property->getValue ( $object );
			
				foreach ( $array as $key => $value ) {
					if ($name == $key) {
						$property->setValue ( $object, $value );
					}
				}
			}
			
			return $object;
		}
		
		public static function requestMapping($object,$requestMethod='request'){
			switch ($requestMethod){
				case 'request' :
					return self::array_to_object($_REQUEST, $object);
					break;
				case 'post' : 
					return self::array_to_object($_POST, $object);
					break;
				case 'get'  :
					return self::array_to_object($_GET, $object);
					break;
			}
			
			return false;
		}
	}
}