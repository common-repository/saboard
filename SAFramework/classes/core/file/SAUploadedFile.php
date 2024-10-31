<?php
if(!class_exists('SAUploadedFile')){
	define('ERROR_FILE_SIZE_BIG',99);
	define('ERROR_FILE_SIZE_BIG_KR','파일용량이 너무 큽니다.');
	
	define('ERROR_FILE_EXTENSION',100);
	define('ERROR_FILE_EXTENSION_KR','허용할수 없는 확장자입니다.');
	
	class SAUploadedFile {
		var $name;
		var $uploadName;
		var $uploaderIp;
	
		var $realName;
	
		var $size;
	
		var $tempName;
		var $mimetype;
		var $error;
	
		var $maxFileSize;
		var $extension;
	
		public function __construct($name = '', $tempName = '', $size = '', $mimetype = '', $error = '') {
			$this->name = $name;
			$this->tempName = $tempName;
			$this->size = $size;
			$this->mimetype = $mimetype;
			$this->error = $error;
	
			if(count($this->extension) <= 0 || !is_array($this->extension)){
				$this->setExtension(array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'zip', 'hwp', 'ppt', 'xls', 'doc', 'txt', 'pdf','psd','ai'));
			}
	
			$this->uploaderIp = SARequest::getRemoteIp();
		}
	
		public function getTempName() {
			return $this->tempName;
		}
	
		public function setName($name) {
			$this->name = $name;
		}
	
		public function getUploadName() {
			return $this->uploadName;
		}
	
		public function setUploadName($uploadName) {
			$this->uploadName = $uploadName;
		}
	
		public function setSize($size) {
			$this->size = $size;
		}
	
		public function setTempName($tempName) {
			$this->tempName = $tempName;
		}
	
		public function setMimetype($mimetype) {
			$this->mimetype = $mimetype;
		}
	
		public function setError($error) {
			$this->error = $error;
		}
	
		public function getName() {
			return $this->name;
		}
	
		public function getTemporaryName() {
			return $this->tempName;
		}
	
		public function getSize() {
			return $this->size;
		}
	
		public function getMimetype() {
			return $this->mimetype;
		}
	
		public function getError() {
			return $this->error;
		}
	
		public function isEmpty() {
			return ! ($this->error === UPLOAD_ERR_OK && $this->size > 0);
		}
	
		public function getMaxFileSize() {
			return $this->maxFileSize;
		}
	
		public function setMaxFileSize($maxFileSize) {
			$this->maxFileSize = $maxFileSize;
		}
	
		public function getExtension() {
			return $this->extension;
		}
	
		public function setExtension($extension) {
			$this->extension = $extension;
		}
	
		public function getRealName() {
			return $this->realName;
		}
	
		public function setRealName($realName) {
			$this->realName = $realName;
		}
	
		public function transferTo($filename,$extension=array()) {
			if(!$this->isEmpty()){
				$errorNo = null;
				$errorMsg = null;
					
				$errorFlag = false;
					
				if(!$this->isAbleExtension()){
					$errorNo = ERROR_FILE_EXTENSION;
					$errorMsg = ERROR_FILE_EXTENSION_KR;
					$errorFlag = true;
				}
					
				if(!$this->isAbleFileSize()){
					$errorNo = ERROR_FILE_SIZE_BIG;
					$errorMsg = ERROR_FILE_SIZE_BIG_KR;
						
					$errorFlag = true;
				}
					
				if(!$errorFlag){
					$path = explode ( "/", $filename );
						
					$p = '';
						
					for($i = 0; $i < count ( $path ) - 1; $i ++) {
						$p .= '/' . $path [$i];
	
						if (! is_dir ( $p )) {
							mkdir ( $p, 0777 );
						}
					}
						
					if (! $this->isEmpty () && is_uploaded_file ( $this->tempName )) {
						if (@copy ( $this->tempName, $filename )) {
							return array(
									'result' => true,
									'name' => $this->tempName
							);
						}
	
						if (@move_uploaded_file ( $this->tempName, $filename )) {
							return array(
									'result' => true,
									'name' => $this->tempName
							);
						}
					}
				}else{
					return array(
							'result' => false,
							'errorNo' => $errorNo,
							'errorMsg' => $errorMsg,
							'error' => $this->error
					);
				}
			}
	
			return array('result' => true);
		}
	
		public function deleteFile(){
			if(is_file($this->getRealName())){
				unlink($this->getRealName());
			}else{
			}
		}
	
		public function isAbleExtension(){
			$Tmp = explode('.', $this->name);
			$extension = $Tmp[count($Tmp)-1];
			$temp = strtolower($extension);
	
			foreach($this->extension as $ext){
				if(preg_match('/'.$ext.'/', $temp)){
					return true;
				}
			}
	
			return false;
		}
	
		public function isAbleFileSize(){
			if($this->size >= $this->maxFileSize){
				return false;
			}
	
			return true;
		}
	}	
}