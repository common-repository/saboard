<?php

if (! function_exists ( 'sa_img' )) {
	/**
	 *
	 * @uses sa_img(array('src','title'));
	 * @param array $args        	
	 */
	function sa_img($link, $title, $id = '', $css_class = '', $longdesc = '') {
		$output = "<img src=\"{$link}\" title=\"{$title}\" ";
		
		if (! empty ( $id )) {
			$output .= "id=\"${id}\"";
		}
		
		if (! empty ( $css_class )) {
			$output .= "class=\"{$css_class}\"";
		}
		
		if (! empty ( $longdesc )) {
			$output .= "longdesc=\"${longdesc}\"";
		}
		
		$output .= "/>";
		
		return $output;
	}
}

if (! function_exists ( 'sa_tag_options' )) {
	function sa_tag_options($options = array()) {
		$html = '';
		foreach ( $options as $key => $value ) {
			$html .= ' ' . $key . '="' . $value . '"';
		}
		
		return $html;
	}
}

if (! function_exists ( 'sa_html' )) {
	function sa_html($name, $options = array(), $open = false) {
		if (! $name) {
			return '';
		}
		
		return '<' . $name . sa_tag_options ( $options ) . (($open) ? '>' : ' />');
	}
}

if (! function_exists ( '_sa_html' )) {
	function _sa_html($name,$open = false){
		if($open){
			
		}else{
			return '</'.$name.'>';
		}
	}
}


if (! function_exists ( 'sa_form_tag' )) {
	function sa_form_tag($url = '', $html_options = array()) {
		if (! array_key_exists ( 'method', $html_options )) {
			$html_options ['method'] = 'post';
		}
		
		if (array_key_exists ( 'multipart', $html_options )) {
			$html_options ['enctype'] = 'multipart/form-data';
			unset ( $html_options ['multipart'] );
		}
		
		// $html_options ['action'] = system_url ( $url );
		
		return sa_html ( 'form', $html_options, true );
	}
}

if (! function_exists ( '_sa_form_tag' )) {
	function _sa_form_tag() {
		return '</form>';
	}
}

