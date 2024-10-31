<?php
/**
 * @author oternet
 * 
 * @uses SATheme
 * 
 * class SAThemeHeler extends SATheme {
 *	 public function wp_head_callback() {}
 *	
 *	 public function admin_head_callback() {}
 *	
 *	 public function admin_preview_callback() {}
 * }
 *	
 * $themeHelper = new SAThemeHeler ( array (
 *	 'post-formats' => array ('aside','gallery'),
 *	 'post-thumbnails' => array ('size' => array (300,400)),
 *	 'automatic-feed-links' => true,
 *	 'custom-background' => array ( 'default-color' => 'e6e6e6' )
 * ));
 * 
 */
if(!class_exists('SATheme')){
	abstract class SATheme {
		protected $sideBars = array();
		protected $languages = array();
		protected $themeSupprots = array();
		
		public function addSidebar($sidebar=array()){
			array_push($this->sideBars, $sidebar);
		}
		
		public function __construct() {
			add_action ( 'after_setup_theme'  , array (&$this,'after_setup') );
			add_action ( 'after_setup_theme'  , array (&$this,'load_theme_textdomain') );
			add_action ( 'after_setup_theme'  , array (&$this,'load_theme_support') );
			
			add_action ( 'after_switch_theme' , array (&$this,'activation_hook'));
			add_action ( 'widgets_init' 	  , array (&$this,'registerSidebar'));
		}
		
		public abstract function activation_hook();
	
		public abstract function after_setup_theme();
		
		public function after_setup(){
			$this->after_setup_theme();
		}
	
		public function add_theme_support($name,$value=''){
			$this->themeSupprots[$name] = $value;
		}
		
		public function load_theme_support(){
			foreach ($this->themeSupprots as $key=>$value){
				if(empty($value)){
					add_theme_support($key);
				}else{
					add_theme_support($key,$value);
				}
			}
		}
		
		public function init() {
			
		}
			
		public function registerPostThumbnails(array $args = array ()){
			$default = array ('type' => array (),'size' => 	array (50,50));
			
			$args = sa_parse_args($args,$default);
			
			if (! empty ( $args['type'] )) {
				add_theme_support ( 'post-thumbnails', $args['type'] );
			} else {
				add_theme_support ( 'post-thumbnails' );
			}
			
			set_post_thumbnail_size ( $args['size'] [0] , $args['size'] [1] );
		}
		
		public function registerNavigationMenu(array $args=array()){
			foreach ($args as $key=>$value){
				register_nav_menu( $key, $value );
			}
		}
	
		public function registerCustomBackground(array $args=array()){
			global $wp_version;
			
			$default = array ('default-color' => 'fff' );
			
			$args = sa_parse_args($args,$default);
			
			if (version_compare ( $wp_version, '3.4', '>=' ))
				add_theme_support ( 'custom-background', $args );
			else
				add_custom_background ( $args );
		}
		
		public function add_theme_textdomain($domain,$path){
			$this->languages[$domain] = $path;	
		}
		
		public function load_theme_textdomain(){
			foreach($this->languages as $key=>$value){
				load_theme_textdomain ( $key , $value);
			}
		}
		
		public function excerpt_filter($str){
			sa_excerpt_filter($str);
		}
		
		public function registerSidebar(){
			foreach($this->sideBars as $sideBar){
				register_sidebar( $sideBar);
			}
		}
	}
}