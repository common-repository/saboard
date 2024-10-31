<?php
if(!class_exists('SAAdminPageController')){
	define('SA_PAGE_TYPE_ADMIN','ADMIN');
	define('SA_PAGE_TYPE_MENU','MENU');
	define('SA_PAGE_MODE_NAME', 'pageMode');
	
	abstract class SAAdminPageController extends SAAbstractController{
		var $pageType = SA_PAGE_TYPE_ADMIN;
		var $pageName;
		var $pageMode;
		var $pageImage;
		
		var $slugLink;
		var $slugPage;
		
		var $menus;
		var $subMenus;
		var $option;
		
		var $rootMav;
		
		var $action_footer;
		var $action_header;
		var $action_setUp;
		
		public function initAdminPage(){
			$this->rootMav = new SAModelAndView();
			$this->rootMav->setViewPath(dirname(__FILE__).'/views/');
			$this->rootMav->addObject('controller', $this);
			
			$keys = array_keys($this->menus);
			$this->pageMode = SARequest::getParameter(SA_PAGE_MODE_NAME,$keys[0]);
	
			$this->slugPage = '?page='.$this->slug;
			$this->slugLink = '?page='.$this->slug.'&pageMode=';
			
			$this->option = new SAOption($this->slug);
			$this->rootMav->addObject('option', $this->option);
			
			$this->action_header = $this->slug.'_view_header';
			$this->action_footer = $this->slug.'_view_footer';
			$this->action_setUp  = $this->slug.'_setUp';
		}
		
		public function redirect($pageMode){
			SAResponse::sendRedirect($this->slugLink.$pageMode);
		}
		
		public function saveMessage($message){
			SARequest::setSessionMessage($this->slug, $message);
		}
		
		public function setPageName($pageName){
			$this->pageName = $pageName;
			
			return $this;
		}
		
		public function setPageImage($pageImage){
			$this->pageImage = $pageImage;
		}
		
		public function setMenus($menus){
			$this->menus = $menus;
			
			return $this;
		}
		
		public function setSubMenus($subMenus){
			$this->subMenus = $subMenus;
			
			return $this;
		}
		
		public function setPageType($pageType){
			$this->pageType = $pageType;
			
			return $this;
		}
		
		public function makeMenu(){ 
			if(empty($this->menus)){
				echo '메뉴를 추가해주세요';
				return false;
			}
			
			$keys = array_keys($this->menus);
			
			$currentMenu = SARequest::getParameter(SA_PAGE_MODE_NAME,$keys[0]);
					
			foreach($this->menus as $menu=>$menuNm){
				$isCurrentMenu = preg_match('/'.$currentMenu.'/', $menu);
				?>
				<a href="?page=<?=$this->slug ?>&<?=SA_PAGE_MODE_NAME ?>=<?=$menu ?>" 
				   class="nav-tab <?php $isCurrentMenu ? _e('nav-tab-active') : '' ?>">
				   <?=$menuNm ?>
				</a>
			<?php }
		}
	
		public function setUp(){
			$this->initAdminPage();
// 			add_action('init', array(&$this,'initAdminPage'));

			do_action($this->action_setUp);
			
			add_action($this->action_header, array(&$this,'view_header'));
			add_action($this->action_footer, array(&$this,'view_footer'));
			
			if ( current_user_can('activate_plugins') ) {
				if($this->pageType == SA_PAGE_TYPE_ADMIN){
					add_action('admin_menu', array(&$this,'add_theme_page'));
				}
				
				if($this->pageType == SA_PAGE_TYPE_MENU){
					add_action('admin_menu', array(&$this,'add_menu_page'));
					add_action('admin_menu', array(&$this,'add_sub_menu_page'));
				}
			}
		}
		
		public function add_sub_menu_page() {
			if(!empty($this->subMenus)){
				$num = 0;
				
				foreach($this->subMenus as $subMenu){
					//add_submenu_page($this->slug, $subMenu['title'], $subMenu['title'], 'manage_options', $this->slug.$num,array(&$this,'outView'));
					
					$num++;
				}	
			}
		}
		
		public function add_menu_page(){
			add_menu_page($this->pageName, $this->pageName, 'read', $this->slug, array(&$this,'outView'),$this->pageImage);
		}
		
		public function add_theme_page(){
			if(empty($this->slug) || empty($this->pageName)){
				die('slug or pageName empty');	
			}
			
			add_theme_page($this->pageName, $this->pageName, 'read', $this->slug ,array(&$this,'outView'));
		}
		
		public function view_header(){
			$this->rootMav->getView('header.php');
		}
		
		public function view_footer(){
			$this->rootMav->getView('footer.php');
		}
		
		public function outView(){
			do_action($this->action_header);
			
			$this->view();
			
			do_action($this->action_footer);
		}
		
		public abstract function view();
	}
}