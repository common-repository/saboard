<?php
if (! class_exists ( 'SAManager' )) {
	/**
	 * abstract manager
	 *
	 * @author oternet
	 */
	abstract class SAManager {
		const CURRENT_SAFRAMEWORK_VERSION = 0.1;
		
		protected $paths = array();
		protected $__file;
		protected $sadirpath;
		protected $sadirurl;
		
		public function getVersion(){
			return self::CURRENT_SAFRAMEWORK_VERSION;
		}
		
		public function __construct($__file) {
			add_action( 'init' , array(&$this,'session_start'));
			add_action( 'init' , 'sa_head_set');
			add_action( 'init' , array(&$this,'registerScripts'));
			add_action( 'admin_init' , array(&$this,'registerScripts'));
			
			remove_action('wp_head', 'wp_generator');
			
			$this->paths = array ( 'includes' => array ( 'SAFramework/functions/loader.php' ) , 'classes'  => array ( ) );
			
			$this->__file = $__file;
			$this->setPhpSetting();
		}
		
		public function init(){}
		
		public function setPhpSetting(){
			ini_set("memory_limit" , '128M');
			ini_set('max_execution_time', 300);
		}
		
		public function registerScripts(){
			wp_register_script ('saframework', $this->getSaDirUrl().'/SAFramework/asset/saframework/js/common.js');
			wp_register_style  ('saframework', $this->getSaDirUrl().'/SAFramework/asset/saframework/css/style.css');
			
			wp_enqueue_script  ('saframework');
			wp_enqueue_style   ('saframework');
		}
		
		public function session_start(){
			if( !session_id()){
				session_start();
			}
		}
		
		public function getSaPluginUrl(){
			return $this->getSaDirUrl().'/modules';			
		}
		
		public function getSaPluginPath(){
			return $this->getSaDirPath().'/modules';
		}
		
		public function setSaDirPath($dirpath){
			$this->sadirpath = $dirpath;
		}
		
		public function setSaDirUrl($dirurl){
			$this->sadirurl = $dirurl;
		}
				
		public abstract function getSaDirPath();
		public abstract function getSaDirUrl();
	}
}

if (!class_exists ( 'SAPluginManager' )) {
	
	/**
	 * use plugin
	 * 
	 * @author oternet
	 */
	class SAPluginManager extends SAManager {
		public function getSaDirPath() {
			return plugin_dir_path ( $this->__file );
		}
		
		public function getSaDirUrl() {
			return plugin_dir_url ( $this->__file );
		}
	}
}

if (!class_exists ( 'SAThemeManager' )) {
	/**
	 * use theme
	 * 
	 * @author oternet
	 */
	class SAThemeManager extends SAManager {
		public function getSaDirPath() {
			if(empty($this->sadirpath)){
				return get_template_directory () . '/';
			}else{
				return $this->sadirpath . '/';
			}
		}
		
		public function getSaDirUrl() {
			if(empty($this->sadirurl)){
				return get_template_directory_uri () . '/';
			}else{
				return $this->sadirurl . '/';
			}
		}
	}
}