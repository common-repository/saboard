<?php
class SAUploader {
	var $file;
	
	var $uploadFile = array();
	var $extension = array();
	
	public function __construct($name='') {
		if(!empty($name)){
			$this->setFile($name);
		}
		
		$this->setAllowType();
	}
	
	public function setFile($name){
		$this->file = $_FILES [$name];
		$this->setUploadFile();
	}
	
	public function getUploadFile() {
		return $this->uploadFile;
	}
	
	public function setUploadFile() {
		if ($this->isMultiple ()) {
			
			for($i = 0; $i < count ( $this->file ['name'] ); $i ++) {
				$f = new SAUploadedFile ();
				
				foreach ( $this->file as $a => $b ) {
					$value = $b [$i];
					
					if ($a == 'name') {
						$f->setName ( $value );
					} else if ($a == 'type') {
						$f->setMimetype ( $value );
					} else if ($a == 'tmp_name') {
						$f->setTempName ( $value );
					} else if ($a == 'error') {
						$f->setError ( $value );
					} else if ($a == 'size') {
						$f->setSize ( $value );
					}
				}
				
				array_push ( $this->uploadFile, $f );
			}
		} else {
			$f = new SAUploadedFile ();
			
			foreach ( $this->file as $a => $b ) {
				
				if ($a == 'name') {
					$f->setName ( $b );
				} else if ($a == 'type') {
					$f->setMimetype ( $b );
				} else if ($a == 'tmp_name') {
					$f->setTempName ( $b );
				} else if ($a == 'error') {
					$f->setError ( $b );
				} else if ($a == 'size') {
					$f->setSize ( $b );
				}
			}
			
			array_push ( $this->uploadFile, $f );
		}
	}
	
	public function copyAll($directory,$filenameAppend=''){
		$result = null;
		$fileNames = array();
		
		$uploadUrl = wp_upload_dir(); 
		$uploadUrl = $uploadUrl['baseurl'];
		
		foreach($this->uploadFile as $file){
			$fileName = $directory.'/'.$filenameAppend.$file->getName();
			
			$result = $file->transferTo($fileName);
			
			if($result['result'] == false){
				foreach($this->uploadFile as $file){
					if(is_file($fileName)){
						$file->deleteFile();
					}
				}
				
				break;
			}else{
				$file->setUploadName($filenameAppend.$file->getName());
				$file->setRealName($fileName);
			}
		}

		return array(
			'result'=> $result,
			'files' => $this->uploadFile
		);
	}
	
	public function copy($directory,$index,$filenameAppend=''){
		$file = $this->getFile($index);
		return $file->transferTo($directory.'/'.$filenameAppend.$file->getName());
	}
	
	public function hasUseAbleFile(){
		foreach($this->uploadFile as $file){
			if(!$file->error){
				return true;
			}
		}	
	}
	
	public function setFileExtensions($extension){
		foreach($this->uploadFile as $file){
			$file->setExtension($extension);
		}
	}
	
	public function setMaxFileSize($size){
		foreach($this->uploadFile as $file){
			$file->setMaxFileSize($size);	
		}
	}
	
	public function setAllowType($type='all'){
		$types = array();
		
		switch ($type){
			case 'image':
				$types = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
			break;
			
			case 'document':
				$types = array('hwp', 'ppt', 'xls', 'doc', 'txt', 'pdf');
			break;
			
			case 'all' :
				$types = sa_get_mimetypes_extentions();
			break;
		}
		
		$this->setFileExtensions($types);
	}
	
	public function getFile($index){
		$fileList = $this->getUploadFile();
		
		return $fileList[$index];
	}
	
	public function isMultiple() {
		return is_array ( $this->file ['name'] );
	}
}