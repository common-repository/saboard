<?php
class PageController {
	const ACTION_SETUP 				 = 'action_setup';
	
	const ACTION_ENQUEUE_SCRIPTS 	 = 'action_enqueue_scripts';
	
	const ACTION_ENQUEUE_SCRIPTS_PRE = 'action_enqueue_scripts_pre';
	
	const ACTION_INIT_OPTION 		 = 'action_init_option';
	
	const ACTION_INIT_MAV 		 	 = 'action_init_mav';
	
	var $option;
	var $slug;
	var $mav;
	
	var $slugLink;
	var $pageMode;
		
	var $sub_menu_cnt = 0;
	
	var $admin_css  = array();
	var $admin_js  	= array();
	
	var $plugin_css = array();
	var $plugin_js  = array();
	
	var $css_path  	= '';
	var $js_path  	= '';
	var $image_path = '';
	
	var $file;
	
	var $wpdb;
	var $user;
	
	var $pageId;
	var $pageLink;
	
	var $pages;
	
	public function setUp(){
		$this->initVariables();
		
		$this->action_init_option();
		
		$this->action_init_mav();
		
		$this->action_setup();
				
		$this->action_enqueue_scripts_pre();
		
		$this->action_enqueue_scripts();
	}
	
	public function initVariables(){
		global $wpdb;
		
		$rc = new ReflectionClass(get_class($this));
		$this->file = $rc->getFileName();
		
		$this->pageMode = SARequest::getParameter('pageMode');
		
		$this->slug   = get_class($this);
		$this->slugLink = '?page='.$this->slug.'&pageMode=';
		
		$this->pageId 	= SARequest::getParameter('page_id');
		$this->pageLink = empty($this->pageId) ? '' : '?page_id='.$this->pageId;
		
		$this->wpdb = $wpdb;
		$this->user = $this->getUser();
	}
	
	public function action_setup(){
		do_action(self::ACTION_SETUP);
	}
	
	public function getUser(){
		$user = new WP_User(get_current_user_id());
		
		if(!is_object($user)){
			return false;
		}
		
		$userMeta = get_user_meta( $user->ID);
		
		if(!is_array($userMeta)){
			return false;
		}
		
		$meta = array_map( function($a) { return $a[0]; }, $userMeta );
		
		$user = $user->to_array();
		
		if(is_array($user) && is_array($meta)){
			return array_merge($user,$meta);
		}
	}
	
	public function action_init_option(){
		$this->option = new SAOption($this->slug);
		
		/**
		 * here is add custom add_option
		 */
		do_action(self::ACTION_INIT_OPTION);
	}
	
	public function action_init_mav(){
		$this->mav    = new SAModelAndView();
		$this->mav->setViewPath(dirname($this->file).'/../views/');
		
		$this->mav->addObject('option', $this->option);

		$this->mav->addObject('slug', $this->slug);
		$this->mav->addObject('slugLink', $this->slugLink);
		
		/**
		 * here is custom add object
		 * this is global
		 */
		do_action(self::ACTION_INIT_MAV);
	}
	
	public function action_enqueue_scripts_pre(){
		$this->css_path 	= str_replace('classes', 'resources', plugin_dir_url($this->file)).'css/';
		$this->js_path  	= str_replace('classes', 'resources', plugin_dir_url($this->file)).'js/';
		$this->image_path 	= str_replace('classes', 'resources', plugin_dir_url($this->file)).'images/';
		
		/**
		 * here is similarity filter
		 * css_path,js_path,image_path change to here
		 */
		do_action(self::ACTION_ENQUEUE_SCRIPTS_PRE);
		
		$this->mav->addObject('css_path', $this->css_path);
		$this->mav->addObject('image_path', $this->image_path);
		$this->mav->addObject('js_path', $this->js_path);
	}
	
	public function action_enqueue_scripts(){
		do_action(self::ACTION_ENQUEUE_SCRIPTS);
		
		if(!empty($this->admin_css) || !empty($this->admin_js) ) {
			add_action('admin_enqueue_scripts', array(&$this, 'load_admin_scripts'));
		}
		
		if(!empty($this->plugin_css) || !empty($this->plugin_js) ) {
			add_action('wp_enqueue_scripts', array(&$this, 'load_plugin_scripts'));
		}
	}
	
	public function add_admin_css($css){
		$this->admin_css[] = $css;
	}
	
	public function add_admin_js($js){
		$this->admin_js[] = $js;
	}
	
	public function add_plugin_css($css){
		$this->plugin_css[] = $css;
	}
	
	public function add_plugin_js($js){
		$this->plugin_js[] = $js;
	}
	
	function load_admin_scripts() {
		foreach($this->admin_css as $css) {
			wp_enqueue_style($css, $this->css_path.$css);
		}
		foreach($this->admin_js as $js) {
			wp_enqueue_script($js, $this->js_path.$js);
		}
	}
	
	function load_plugin_scripts() {
		foreach($this->plugin_css as $css) {
			wp_enqueue_style( $css , $this->css_path.$css.'.css');
		}
		foreach($this->plugin_js as $js) {
			wp_enqueue_script($js, $this->js_path.$js.'.js');
		}
	}
	
	public function setDefaultPage($pageName){
		if(empty($this->pageMode)){
			$this->pageMode = $pageName;
		}
	}
	
	public function add_menu_page($pageName,$view,$pageImage=null){
		add_menu_page($pageName, $pageName, 'read', $this->slug , $view , $pageImage);
	}
	
	public function add_submenu_page($pageName,$view){
		$menu_slug = $this->slug.$this->sub_menu_cnt;
		
		add_submenu_page($this->slug, $pageName, $pageName, 'manage_options', $menu_slug ,$view);
		
		$this->sub_menu_cnt++;
	}
	
	public function add_theme_page($pageName,$view){
		add_theme_page($pageName, $pageName, 'read', $this->slug ,$view);
	}
	
	public function redirect($pageMode){
		SAResponse::sendRedirect($this->slugLink.$pageMode);
	}
	
	public function redirectReferer(){
		if ( wp_get_referer() ){
			SAResponse::sendRedirect(wp_get_referer());
		} else {
			SAResponse::sendRedirect(get_home_url());
		}
	}
	
	public function getHeader(){
		$output  = '<div id="'.$this->slug.'" class="wrap">';
		$output .= '<h2 class="nav-tab-wrapper">';
			$output .= '<div class="nav-tabs">';
			
			$tlt = get_admin_page_title();
			$pages = $this->pages[$tlt];
			
			if(empty($this->pageMode)){
				$keys = array_keys($pages);
				$this->pageMode = $keys[0];
			}
			
			if(is_array($pages)){
				$cnt = '';
				
				foreach ($pages as $mode => $pageName){
					$cnt = is_numeric($cnt) ? ($cnt-1) : $cnt;
					$link = '?page='.$this->slug.$cnt.'&pageMode='; 					
					
					$tab_class = '';
					
					if($this->pageMode == $mode){
						$tab_class = 'nav-tab-active';
					}
					
					$output .= '<a href="'.$link.$mode.'"class="nav-tab '.$tab_class.'">';
						$output .= $pageName;
					$output .= '</a>';
					
					$cnt += 1;
				}	
			}
				
			$output .= '</div>'; 
		$output .= '</h2>';
		
		$output .= '<br/>';
		
		echo $output;
	}
	
	public function getFooter(){
		echo '</div>';
	}
	
	public function set_page($name,$param = array()){
		$this->pages[$name] = $param;
	}
}