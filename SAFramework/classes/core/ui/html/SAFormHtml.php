<?php
if(!class_exists('SAFormHtml')){
	class SAFormHtml {
		private $model;
		
		public function getModel() {
			return $this->model;
		}
		
		public function setModel($model) {
			$this->model = $model;
		}
		
		public function __construct($model = array()) {
			$this->model = sa_parse_args ( $model , $_REQUEST );
		}
		
		public function form($html_options = array()) {
			if (! array_key_exists ( 'method', $html_options )) {
				$html_options ['method'] = 'post';
			}
			
			if (array_key_exists ( 'multipart', $html_options )) {
				$html_options ['enctype'] = 'multipart/form-data';
				unset ( $html_options ['multipart'] );
			}
			
			return SAHtml::html ( 'form', $html_options, true );
		}
		
		public function _form() {
			return SAHtml::_html('form');
		}
		
		public function radio($name,$value,$options=array()){
			$options['value'] = $value;
			
			$v = $this->getValueFromModel($name);
			
			if($v == $value){
				$options['checked'] = 'checked';
				
			}
						
			return $this->input($name,$options,'radio');
		}
		
		public function password($name, $options = array()){
			return $this->input($name,$options,'password');
		}
		
		public function input($name, $options = array(), $type = '') {
			global $_SESSION;
			
			$type = empty ( $type ) ? 'text' : $type;
			$value = $this->getValueFromModel ( $name );
			
			$default = array (
					'type' => $type,
					'name' => $name,
					'id' => $name,
					'value' => $value 
			);
			
			$options = sa_parse_args ( $options, $default );
			
			$result = SAHtml::html ( 'input', $options ).'<span class="error">'. sa_get_array_value($_SESSION,'error_'.$name).'</span>';
			
			unset($_SESSION['error_'.$name]);
			
			return $result;
		}
		
		public function label($name,$text,$options = array()){
			$default = array('data-target' => $name,'class'=>$name);
			$options = sa_parse_args($options,$default);
			
			return SAHtml::content_html('label',$text,$options);
		}
		
		public function checkbox($name, $options = array()) {
			return $this->input ( $name, $options, 'checkbox' );
		}
		
		public function hidden($name, $options = array()) {
			return $this->input ( $name, $options, 'hidden' );
		}
		
		public function submit($name, $options = array()) {
			return $this->input ( $name, $options, 'submit' );
		}
		
		public function select($name, $options = array()){
			if (isset ( $options ['multiple'] ) && $options ['multiple'] && substr ( $name, - 2 ) !== '[]') {
				$name .= '[]';
			}
			
			return SAHtml::html ( 'select', array ('name' => $name,'id' => $name  ) ,true);
		}
		
		public function _select(){
			return SAHtml::_html('select');
		}
		
		public function textArea($name, $content='',$options = array()){
			$default = array('name' => $name,'id'=>$name);
			$options = sa_parse_args($options,$default);
			
			$output = SAHtml::content_html('textarea' , $content , $options).'<span class="error">'. sa_get_array_value($_SESSION,'error_'.$name).'</span>';
			
			unset($_SESSION['error_'.$name]);
			
			return $output;
		}
		
		public function option( $items = array() ,$selected ,$nameeq=false){
			return $this->options_for_select ( $items, $selected ,$nameeq);
		}
		
		private function options_for_select($options = array(), $selected = '',$nameeq=false, $html_options = array()) {
			if (is_array ( $selected )) {
				$valid = array_values ( $selected );
				$valid = array_map ( 'strval', $valid );
			}
		
			$html = '';
		
			foreach ( $options as $key => $value ) {
				$option_options = array ('value' => $key );
				
				if($nameeq){
					$option_options = array('value'=>$value);
				}
				
				if (isset ( $selected ) && (is_array ( $selected ) && in_array ( strval ( $key ), $valid, true )) || (strval ( $key ) == strval ( $selected ))) {
					$option_options ['selected'] = 'selected';
				}
				
				if($selected == $value){
					$option_options ['selected'] = 'selected';
				}
		
				$html .= SAHtml::content_html ( 'option', $value, $option_options ) . "\n";
			}
		
			return $html;
		}
		
		public function option_each($items=array() , $html_options = array() , $selected ){
			return $this -> options_for_select_each ( $items, $selected , $html_options);
		}
		
		private function options_for_select_each($options = array(), $selected = '', $html_options = array()) {
			if (is_array ( $selected )) {
				$valid = array_values ( $selected );
				$valid = array_map ( 'strval', $valid );
			}
			
			$html = '';
					
			foreach ( $options as $array ) {
				$options_option = array ();
				
				foreach ( $array as $key => $value ) {
					$options_option = sa_parse_args( $options_option , $html_options );
					
					if($html_options['itemLabel'] == $key || $html_options['itemValue'] == $key){
						if (isset ( $selected ) && (is_array ( $selected ) && in_array ( strval ( $value ), $valid, true )) || (strval ( $value ) == strval ( $selected ))) {
							$options_option ['selected'] = 'selected';
						}
						
						if($html_options['itemLabel'] == $key){
							$content = $value;
						}
						
						if($html_options['itemValue'] == $key){
							$options_option['value'] = $value;
						}
					}
				}
				
				unset($options_option['itemLabel']);
				unset($options_option['itemValue']);
						
				$html .= SAHtml::content_html ( 'option', $content , $options_option ) . "\n";
			}
			
			return $html;
		}
		
		private function getValueFromModel($name) {
			return isset ( $this->model [$name] ) ? $this->model [$name] : '';
		}
		
		private function _convert_options($options = array()) {
			foreach ( array ('disabled','readonly','multiple' ) as $attribute ) {
				$options = $this->_boolean_attribute ( $options, $attribute );
			}
		
			return $options;
		}
		
		private function _boolean_attribute($options, $attribute) {
			if (array_key_exists ( $attribute, $options )) {
				if ($options [$attribute]) {
					$options [$attribute] = $attribute;
				} else {
					unset ( $options [$attribute] );
				}
			}
		
			return $options;
		}
	}
}