<?php 
abstract class SABoardShortCodeView{
	var $path;
	var $url;
	var $listUrl;
	
	var $viewPath;
	var $viewUrl;
	
	var $skinPath;
	var $skinUrl;
	
	var $boardId;
	var $boardTable;
	
	var $boardAdminService;
	var $boardService;
	
	var $board_index;
	var $board_mode;
	var $page_id;
	var $page_id_link;
	
	var $viewParam;
	
	var $detect;
	var $user;
	
	public function __construct($boardId){
		$this->boardId = $boardId;
		
		$this->init();
		$this->initParamaters();
		$this->initEtc();
		
		$this->construct();
	}
	
	public function init(){
		global $saManager;
		
		$this->viewPath = $saManager -> getSaPluginPath().'/board/views';
		$this->viewUrl  = $saManager -> getSaPluginUrl().'/board/views';
		
		$this->skinPath = $saManager -> getSaPluginPath().'/board_skins';
		$this->skinUrl  = $saManager -> getSaPluginUrl().'/board_skins';
		
		$this->boardAdminService 	= SABoardAdminService::getInstance();
		$this->boardService 		= SABoardService::getInstance();
		
		$this->boardTable 	= new SABoardTable ($this->boardId);
		$this->user 		= new SABoardUser  ($this->boardTable);
		
		do_action('sa_board_shortcode_view_init');
	}
	
	public function initParamaters(){
		$this->board_index  = SARequest::getParameter('board_index');
		$this->board_mode   = SARequest::getParameter('board_mode','board_list');
		
		$this->page_id 	    = get_the_ID();
		$this->page_id_link = empty($this->page_id) ? '' : '&page_id='.$this->page_id;
		
		$this->viewParam    = array('shortCodeView'=>&$this);
	}
	
	public function initEtc(){
		$this->path = array();
		
		$this->path['boardThemePath']      = $this->skinPath . '/board/'.$this->boardTable->table['board_table_theme'];
		$this->path['boardThemeUrl']       = $this->skinUrl  . '/board/'.$this->boardTable->table['board_table_theme'];
		
		$this->path['boardPaginationPath'] = $this->skinPath . '/pagination/'.$this->boardTable->table['board_table_theme_pagination'];
		$this->path['boardPaginationUrl']  = $this->skinUrl  . '/pagination/'.$this->boardTable->table['board_table_theme_pagination'];
		
		$this->path['boardSearchPath']     = $this->skinPath . '/search/'.$this->boardTable->table['board_table_theme_search'];
		$this->path['boardSearchUrl'] 	   = $this->skinUrl  . '/search/'.$this->boardTable->table['board_table_theme_search'];
		
		$this->path['boardLatestPath']     = $this->skinPath . '/latest/';
		$this->path['boardLatestUrl'] 	   = $this->skinUrl  . '/latest/';
		
		$this->path['boardReplyPath']     = $this->skinPath . '/reply/'.$this->boardTable->table['board_table_theme_reply'];
		$this->path['boardReplyUrl'] 	   = $this->skinUrl  . '/reply/'.$this->boardTable->table['board_table_theme_reply'];
		
		$this->detect = new Mobile_Detect();
		
		$this->listUrl = '?board_mode=board_list&board_id='.$this->boardId.$this->page_id_link;
		
		$this->addViewParam    ('detect', $this->detect);
		$this->addRequestParam ('board_id', $this->boardId);
		$this->addRequestParam ('now_page', SARequest::getParameter('now_page',1));
		$this->addRequestParam ('boardReplyMaxGrp', '');
	}
	
	public function redirect_alert($location,$message=''){
		if (!empty($message)){
			sa_alert($message);
		}
		
		SAResponse::sendRedirect($location);
		die();
	}
	
	public function addViewParam($key,$object){
		$this->viewParam[$key] = $object;
	}
	
	public function addRequestParam($key,$object){
		$_REQUEST[$key] = $object;
	}
	
	public abstract function getView($output);
	public function construct(){}
}