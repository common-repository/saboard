<?php
if (! class_exists ( 'SAFile' )) {
	class SAFile {
		var $dirName;
		var $fileName;
		var $fileLastName;
		var $fileSize;
		var $time;
		var $contents;
		
		public function __construct($dirName,$fileName) {
			$this->dirName = $dirName;
			$this->fileName = $dirName.'/'.$fileName;
			
			$this->fileLastName = $fileName;
			
			if ($this->is_exists_file()) {
				$this->fileSize = filesize ( $this->fileName );
				$this->time = filemtime ( $this->fileName );
				$this->contents = $this->read ();
			}
		}
		
		public function is_exists_file(){
			return is_file($this->fileName);
		}
		
		public function delete() {
			if (is_string ( $this->fileName )) {
				if (is_file ( $this->fileName )) {
					return unlink ( $this->fileName );
				} else if (is_dir ( $this->fileName )) {
					$ok = sa_rm ( "$this->fileName/*" );
					if (! $ok) {
						return false;
					}
					return rmdir ( $this->fileName );
				} else {
					$matching = glob ( $this->fileName );
					if ($matching === false) {
						trigger_error ( sprintf ( 'No files match supplied glob %s', $this->fileName ), E_USER_WARNING );
						return false;
					}
					$rcs = array_map ( 'sa_rm', $matching );
					if (in_array ( false, $rcs )) {
						return false;
					}
				}
			} else if (is_array ( $this->fileName )) {
				$rcs = array_map ( 'rm', $this->fileName );
				if (in_array ( false, $rcs )) {
					return false;
				}
			} else {
				trigger_error ( 'Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR );
				return false;
			}
			
			return true;
		}
		
		public function read() {
			if(!$this->is_exists_file()){
				return false;
			}
			
			if (function_exists ( 'file_get_contents' )) {
				$c = file_get_contents ( $this->fileName );
				return $c;
			}
			
			if (! $fp = @fopen ( $this->fileName, FOPEN_READ )) {
				return false;
			}
			
			flock ( $fp, LOCK_SH );
			
			$data = '';
			if (filesize ( $this->fileName ) > 0) {
				$data = & fread ( $fp, filesize ( $this->fileName ) );
			}
			
			flock ( $fp, LOCK_UN );
			fclose ( $fp );
			
			return $data;
		}
		
		public function write( $data, $mode = 'w') {
			if (! $fp = @fopen ( $this->fileName, $mode )) {
				return FALSE;
			}
			
			flock ( $fp, LOCK_EX );
			fwrite ( $fp, $data );
			flock ( $fp, LOCK_UN );
			fclose ( $fp );
			
			return true;
		}
		
		public function copy($newFile) {
			if(!$this->is_exists_file()){
				return false;
			}
	
			copy($this->fileName, $newFile);
		}
	}
}