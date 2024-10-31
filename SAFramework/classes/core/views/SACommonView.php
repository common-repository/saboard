<?php
if(!class_exists('SACommonView')){
	class SACommonView  {
		private $fileDirPath = '';
		
		public function setFileDirPath($fileDirPath){
			$this->fileDirPath = $fileDirPath;
		}
		
		public function getFileDirPath(){
			return $this->fileDirPath;
		}
		
		/**
		 *
		 * @see SAViewResolver::view()
		 */
		private function getViewContent($file,$params=array()){
			$fileName = $this->fileDirPath . $file;
			
			try {
				$view = sa_file_read ( $fileName );
	
				if ($view) {
					foreach ( $_REQUEST as $key1 => $val1 ) {
						${$key1} = $val1;
					}
			
					if (is_array ( $params ) && count ( $params )) {
						foreach ( $params as $key => $val ) {
							${$key} = $val;
						}
					}
					
					ob_start ();
					eval ( "?>{$view}<?" );
					$res = ob_get_contents ();
					ob_end_clean ();
	
					return $res;
				} else {
					die ( $fileName . " : BAD REQUEST." );
				}
			} catch ( \Exception $e ) {
				echo $e->getMessage();
			}	
		}
		
		public function getView($file,$params=array()){
			return $this->getViewContent($file , $params);
		}
		
		public function view($file,$params = array()) {
			echo $this->getViewContent($file , $params);
		}
		
		private static $instance;
			
		public static function getInstance() {
			if (! isset ( self::$instance )) {
				self::$instance = new SACommonView();
			}
			
			return self::$instance;
		}
	}
}