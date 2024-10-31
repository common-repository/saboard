<?php 
if(!function_exists('sa_get_str_last_name')){
	function sa_get_str_last_name($str,$split){
		$str_array = explode($split, $str);
	
		return $str_array[count($str_array)-1];
	}
}
?>