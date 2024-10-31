<?php

if (! function_exists ( 'sa_file_read' )) {
	
	/**
	 * 파일을 읽는다.
	 * 
	 * @param string $file        	
	 * @return boolean | string
	 */
	function sa_file_read($file) {
		if (! file_exists ( $file )) {
			return false;
		}
		
		if (function_exists ( 'file_get_contents' )) {
			$c = file_get_contents ( $file );
			
			return $c;
		}
		
		if (! $fp = @fopen ( $file, FOPEN_READ )) {
			return false;
		}
		
		flock ( $fp, LOCK_SH );
		
		$data = '';
		if (filesize ( $file ) > 0) {
			$data = & fread ( $fp, filesize ( $file ) );
		}
		
		flock ( $fp, LOCK_UN );
		fclose ( $fp );
		
		return $data;
	}
}

if (! function_exists ( 'sa_file_delete' )) {
	
	/**
	 * 파일을 삭제한다.
	 * 
	 * @param string $path        	
	 * @param string $del_dir        	
	 * @return boolean
	 */
	function sa_file_delete($path, $del_dir = FALSE, $level = 0) {
		$path = rtrim ( $path, DIRECTORY_SEPARATOR );
		
		if (! $current_dir = @opendir ( $path )) {
			return FALSE;
		}
		
		while ( FALSE !== ($filename = @readdir ( $current_dir )) ) {
			if ($filename != "." and $filename != "..") {
				if (is_dir ( $path . DIRECTORY_SEPARATOR . $filename )) {
					if (substr ( $filename, 0, 1 ) != '.') {
						delete_files ( $path . DIRECTORY_SEPARATOR . $filename, $del_dir, $level + 1 );
					}
				} else {
					unlink ( $path . DIRECTORY_SEPARATOR . $filename );
				}
			}
		}
		
		@closedir ( $current_dir );
		
		if ($del_dir == TRUE and $level > 0) {
			return @rmdir ( $path );
		}
		
		return TRUE;
	}
}

if ( ! function_exists('delete_files'))
{
	function delete_files($path, $del_dir = FALSE, $level = 0)
	{
		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{
					if (substr($filename, 0, 1) != '.')
					{
						delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}
		@closedir($current_dir);

		if ($del_dir == TRUE AND $level > 0)
		{
			return @rmdir($path);
		}

		return TRUE;
	}
}

if (! function_exists ( 'sa_file_write' )) {
	/**
	 * 파일을 쓴다.
	 * 
	 * @param string $path        	
	 * @param string $data        	
	 * @param string $mode        	
	 * @return boolean
	 */
	function sa_file_write($path, $data, $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE) {
		if (! $fp = @fopen ( $path, $mode )) {
			return FALSE;
		}
		
		flock ( $fp, LOCK_EX );
		fwrite ( $fp, $data );
		flock ( $fp, LOCK_UN );
		fclose ( $fp );
		
		return TRUE;
	}
}

if (! function_exists ( 'sa_file_getDirectoryNames' )) {
	
	/**
	 * 디렉토리를 읽는다.
	 * 
	 * @param string $source_dir        	
	 * @param string $directory_depth        	
	 * @param string $hidden        	
	 * @return multitype:NULL string |boolean
	 */
	function sa_file_getDirectoryNames($source_dir, $directory_depth = 0, $hidden = FALSE) {
		if ($fp = @opendir ( $source_dir )) {
			$filedata = array ();
			$new_depth = $directory_depth - 1;
			$source_dir = rtrim ( $source_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
			
			while ( FALSE !== ($file = readdir ( $fp )) ) {
				if (! trim ( $file, '.' ) or ($hidden == FALSE && $file [0] == '.')) {
					continue;
				}
				
				if (($directory_depth < 1 or $new_depth > 0) && @is_dir ( $source_dir . $file )) {
					$filedata [$file] = sa_file_getDirectoryMap ( $source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden );
				} else {
					$filedata [] = $file;
				}
			}
			closedir ( $fp );
			return $filedata;
		}
		
		return FALSE;
	}
}

if (! function_exists ( 'sa_file_getDirectoryMap' )) {
	function sa_file_getDirectoryMap($source_dir, $directory_depth = 0, $hidden = FALSE) {
		if ($fp = @opendir ( $source_dir )) {
			$filedata = array ();
			$new_depth = $directory_depth - 1;
			$source_dir = rtrim ( $source_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
			
			while ( FALSE !== ($file = readdir ( $fp )) ) {
				if (! trim ( $file, '.' ) or ($hidden == FALSE && $file [0] == '.')) {
					continue;
				}
				
				if (($directory_depth < 1 or $new_depth > 0) && @is_dir ( $source_dir . $file )) {
					$filedata [$file] = sa_file_getDirectoryMap ( $source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden );
				} else {
					$filedata [] = $file;
				}
			}
			
			closedir ( $fp );
			return $filedata;
		}
		
		return FALSE;
	}
}

if (! function_exists ( 'sa_file_getFileNames' )) {
	
	/**
	 * 파일명들을 얻는다.
	 * 
	 * @param string $source_dir        	
	 * @param string $include_path        	
	 * @param string $_recursion        	
	 * @return multitype:string |boolean
	 */
	function sa_file_getFileNames($source_dir, $include_path = FALSE, $_recursion = FALSE) {
		static $_filedata = array ();
		
		if ($fp = @opendir ( $source_dir )) {
			if ($_recursion === FALSE) {
				$_filedata = array ();
				$source_dir = rtrim ( realpath ( $source_dir ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
			}
			
			while ( FALSE !== ($file = readdir ( $fp )) ) {
				if (@is_dir ( $source_dir . $file ) && strncmp ( $file, '.', 1 ) !== 0) {
					sa_file_getFileNames ( $source_dir . $file . DIRECTORY_SEPARATOR, $include_path, TRUE );
				} elseif (strncmp ( $file, '.', 1 ) !== 0) {
					$_filedata [] = ($include_path == TRUE) ? $source_dir . $file : $file;
				}
			}
			return $_filedata;
		} else {
			return FALSE;
		}
	}
}

if (! function_exists ( 'sa_file_copy' )) {
	function sa_file_copy($file, $newFile) {
		if(file_exists($file)){
			ob_start ();
			require_once ($file);
			$result = ob_get_clean ();
			
			file_put_contents ( $newFile, $result, LOCK_EX );
		}
	}
}

if (! function_exists ( 'sa_rm' )) {
	function sa_rm($fileglob){
		if (is_string($fileglob)) {
			if (is_file($fileglob)) {
				return unlink($fileglob);
			} else if (is_dir($fileglob)) {
				$ok = sa_rm("$fileglob/*");
				if (! $ok) {
					return false;
				}
				return rmdir($fileglob);
			} else {
				$matching = glob($fileglob);
				if ($matching === false) {
					trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
					return false;
				}
				$rcs = array_map('rm', $matching);
				if (in_array(false, $rcs)) {
					return false;
				}
			}
		} else if (is_array($fileglob)) {
			$rcs = array_map('rm', $fileglob);
			if (in_array(false, $rcs)) {
				return false;
			}
		} else {
			trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
			return false;
		}
	
		return true;
	}
}

if(!function_exists('sa_file_read_all')){
	function sa_file_read_all($root = '.'){
		$files  = array('files'=>array(), 'dirs'=>array());
		$directories  = array();
		$last_letter  = $root[strlen($root)-1];
		$root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;
	
		$directories[]  = $root;
	
		while (sizeof($directories)) {
			$dir  = array_pop($directories);
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					$file  = $dir.$file;
					if (is_dir($file)) {
						$directory_path = $file.DIRECTORY_SEPARATOR;
						array_push($directories, $directory_path);
						$files['dirs'][]  = $directory_path;
					} elseif (is_file($file)) {
						$files['files'][]  = $file;
					}
				}
				closedir($handle);
			}
		}
	
		return $files;
	}	
}
