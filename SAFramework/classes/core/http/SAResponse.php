<?php
if(!class_exists('SAResponse')){
	class SAResponse {
		/**
		 * 리다이렉트 시킨다.
		 * @param string $location        	
		 * @param 200,302,404,500...httpstate $status        	
		 */
		public static function sendRedirect($location, $status = 302) {
			if (headers_sent ()) {
				$html  = '<script type="text/javascript">';
				$html .= '	window.location = "' . $location . '"';
				$html .= '</script>';
				
				echo $html;
			} else {
				if(function_exists('wp_redirect')){
					wp_redirect($location,$status);
				}else{
					header("Location: $location", true, $status);
				}
			}
			
			die ();
		}
		
		public static function out($content){
			echo $content;
		}
	}
}