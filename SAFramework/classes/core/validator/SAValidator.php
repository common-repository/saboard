<?php
if(!class_exists('SAValidator')){
	class SAValidator {
		var $validateItem;
		var $errorList = array ();
		
		public function __construct($validateItem) {
			$this->validateItem = $validateItem;
		}
		
		public function setValidateItem($item) {
			$this->validateItem = $item;
		}
		
		public function validate($option = array()) {
			if (! is_array ( $this->validateItem )) {
				return false;
			}
			
			$check = '';
			
			foreach ( $option as $a => $b ) {
				foreach ( $b as $key => $value ) {
					if (is_numeric ( $key )) {
						$check = $value;
					} else {
						$check = $key;
					}
					
					switch ($check) {
						case 'isNotEmpty' :
							$this->isNotEmpty ( $a );
							break;
						
						case 'isNumber' :
							$this->isNumber ( $a );
							break;
						
						case 'isEnglish' :
							$this->isEnglish ( $a );
							break;
						
						// key=>value
						case 'maxLength' :
							$this->maxLength ( $a, $value );
							break;
						
						case 'minLength' :
							$this->minLength ( $a, $value );
							break;
					}
				}
			}
			
			$this->bindResult ();
		}
		
		public function maxLength($name, $value) {
			$error = '입력할수 있는 최대길이는 ' . $value . ' 입니다.';
			
			if (strlen ( sa_get_array_value($this->validateItem,$name) ) > $value) {
				$this->pushError($name, $error);
			}
		}
		
		public function minLength($name, $value) {
			$error = '최소 ' . $value . '글자 이상 입력하셔야 합니다.';
			
			if (strlen ( $this->validateItem [$name] ) < $value) {
				$this->pushError ( $name, $error );
			}
		}
		
		public function isEmail($name){
			$error = '이메일 형식에 맞지 않습니다. ';
				
			if (! eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $this->validateItem [$name] ) ) {
				$this->pushError ( $name, $error );
			}
			
			return ;
		}
		
		public function isEnglish($name, $error = '영문만 입력할수 있습니다.') {
			$this->pattern ( $name, '/[^A-Za-z0-9]/i', $error );
		}
		
		public function isNumber($name, $error = '숫자만 입력할수 있습니다.') {
			if (! is_numeric ( $this->validateItem [$name] )) {
				$this->pushError ( $name, $error );
			}
		}
		
		public function isNotEmpty($name, $error = '필수입력입니다.') {
			if (empty ( $this->validateItem [$name] )) {
				$this->pushError ( $name, $error );
			}
		}
		
		public function pattern($name, $pattern, $error = '형식이 맞지 않습니다.') {
			if (preg_match ( $pattern, $this->validateItem [$name] )) {
				$this->pushError ( $name, $error );
			}
		}
		
		public function pushError($name, $error,$error_code='') {
			array_push ( $this->errorList, array (
					'name' => $name,
					'error' => $error,
					'error_code'=>$error_code
			) );
		}
		
		public function getErrorList() {
			return $this->errorList;
		}
		
		public function bindResult() {
			foreach ( $this->errorList as $validate ) {
				SARequest::setErrorMessage ( $validate ['name'], $validate ['error'] );
			}
		}
		
		public function bindResultToModel($model) {
			foreach ( $this->errorList as $validate ) {
				
				$model[$validate['name']] = $validate['error'];
				
				//SARequest::setErrorMessage ( $validate ['name'], $validate ['error'] );
			}
		}
				
		public function hasErrors() {
			return count ( $this->errorList ) > 0;
		}
		
	}
}