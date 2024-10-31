<?php
if (! class_exists ( 'SAFileReader' )) {
	class SAFileReader {
		public static function getDirectoryNames($source_dir, $directory_depth = 0, $hidden = FALSE) {
			if ($fp = @opendir ( $source_dir )) {
				$filedata = array ();
				$new_depth = $directory_depth - 1;
				$source_dir = rtrim ( $source_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
					
				while ( FALSE !== ($file = readdir ( $fp )) ) {
					if (! trim ( $file, '.' ) or ($hidden == FALSE && $file [0] == '.')) {
						continue;
					}
		
					if (($directory_depth < 1 or $new_depth > 0) && @is_dir ( $source_dir . $file )) {
						$filedata [$file] = self::getDirectoryMap ( $source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden );
					} else {
						$filedata [] = $file;
					}
				}
				closedir ( $fp );
				return $filedata;
			}
		
			return FALSE;
		}
		
		public static function getDirectoryMap($source_dir, $directory_depth = 0, $hidden = FALSE) {
			if ($fp = @opendir ( $source_dir )) {
				$filedata = array ();
				$new_depth = $directory_depth - 1;
				$source_dir = rtrim ( $source_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
					
				while ( FALSE !== ($file = readdir ( $fp )) ) {
					if (! trim ( $file, '.' ) or ($hidden == FALSE && $file [0] == '.')) {
						continue;
					}
		
					if (($directory_depth < 1 or $new_depth > 0) && @is_dir ( $source_dir . $file )) {
						$filedata [$file] = self::getDirectoryMap ( $source_dir . $file . DIRECTORY_SEPARATOR, $new_depth, $hidden );
					} else {
						$filedata [] = $file;
					}
				}
					
				closedir ( $fp );
				return $filedata;
			}
		
			return FALSE;
		}
		
		public static function getFileNames($source_dir, $include_path = FALSE, $_recursion = FALSE) {
			static $_filedata = array ();
		
			if ($fp = @opendir ( $source_dir )) {
				if ($_recursion === FALSE) {
					$_filedata = array ();
					$source_dir = rtrim ( realpath ( $source_dir ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
				}
					
				while ( FALSE !== ($file = readdir ( $fp )) ) {
					if (@is_dir ( $source_dir . $file ) && strncmp ( $file, '.', 1 ) !== 0) {
						self::getFileNames ( $source_dir . $file . DIRECTORY_SEPARATOR, $include_path, TRUE );
					} elseif (strncmp ( $file, '.', 1 ) !== 0) {
						$_filedata [] = ($include_path == TRUE) ? $source_dir . $file : $file;
					}
				}
				return $_filedata;
			} else {
				return FALSE;
			}
		}
		
		public static function readAllFiles($root = '.'){
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
}