<?php
if(!class_exists('SALoader')){
	class SALoader {
		const PHP_EXTENSION = '\.php';
	
		static $classPaths = array();
	
		static function addClassPath($classPath){
			self::$classPaths[] = $classPath;
		}
	
		static function import($className) {
			self::load($className);
		}

		static function load($className){
			if(empty(self::$classPaths)){
				self::addClassPath(dirname(__FILE__));
				self::addClassPath(dirname(__FILE__).'/../../modules');
			}
	
			foreach(self::$classPaths as $path){
				$files = self::findDirFile($path);
				
				foreach($files as $file){
					$name = mb_split('/', $file);
					$name = $name[count($name)-1];
					$name = preg_replace('/'.self::PHP_EXTENSION.'/', '', $name);
					
					if($className === $name){
						
						require_once $file;
						
						break;
					}
				}
			}
		}
	
		static function findDirFile($root = '.'){
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
	
			return $files['files'];
		}
	}
}

spl_autoload_register(array('SALoader','load'));