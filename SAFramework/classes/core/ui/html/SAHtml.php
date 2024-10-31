<?php
if(!class_exists('SAHtml')){
	class SAHtml {
		public static function html($name, $options = array(), $open = false) {
			if (! $name) {
				return '';
			}
			
			return '<' . $name . self::html_options ( $options ) . (($open) ? '>' : ' />');
		}
		
		public static function _html($name) {
			return '</' . $name . '>';
		}
		
		public static function html_options($options = array()) {
			$html = '';
			foreach ( $options as $key => $value ) {
				$html .= ' ' . $key . '="' . $value . '"';
			}
			
			return $html;
		}
		
		public static function content_html($name, $content = '', $options = array()) {
			if (! $name) {
				return '';
			}
			
			return '<' . $name . self::html_options ( $options ) . '>' . $content . '</' . $name . '>';
		}
	}
}