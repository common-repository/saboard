<?php
class SABoardShortCode {
	private $boardService;
	private $boardAdminService;
	private $boardAction;
	private $boardSeo;
	var $option;
		
	public function __construct(){
		$this->boardService = SABoardService::getInstance();
		$this->boardAdminService = SABoardAdminService::getInstance();
		$this->boardAction = SABoardAction::getInstance();
		$this->boardSeo    = SABoardSeo::getInstance();
	}
	
	public function init(){
		add_shortcode ( 'sa_board'   , array( &$this ,'sa_board' ) );
		add_shortcode ( 'sa_board_latest'   , array( &$this ,'sa_board_latest' ) );
		
		add_action ( 'wp_head' , array(&$this->boardSeo,'init'));
		
		add_action ( 'wp_enqueue_scripts', array(&$this,'registerScripts'));
		
		add_action ( 'sa_board_header' , array(&$this,'board_textdomain'));
		add_action ( 'sa_board_header' , array(&$this->boardAction,'init'));
		
	}
	
	public function registerScripts(){
		global $saManager;
		
		wp_register_script('saboard', $saManager->getSaPluginUrl() . '/board/resources/board.js');
		wp_register_style ('saboard', $saManager->getSaPluginUrl() . '/board/resources/board.css');
		
		wp_enqueue_script ('jquery-form');
		wp_enqueue_script ('jquery-validation');
		wp_enqueue_script ('jquery-fancybox');
		wp_enqueue_style  ('jquery-fancybox');
		
		wp_enqueue_script ('saboard');
		wp_enqueue_style  ('saboard');
	}
	
	public function board_textdomain(){
		global $saManager;
	
		$languages = $saManager->getSaPluginPath().'/board/languages';
		
		load_theme_textdomain ( 'sa_board' , $languages );
	}

	public function sa_board_latest($atts,$content=null){
		global $saManager;
		
		$viewPath = $saManager->getSaPluginPath().'/board/views/service';
		
		extract ( shortcode_atts ( array ( 'boardid'=>'' ,'boardskin'=>'basic','list_cnt'=>5), $atts ) );
		
		$boardTable      = $this->boardAdminService->getBoardTableDomain(array('board_table_id'=>$boardid)); 
		$boardDomainList = $this->boardService->getAllBoardList( array('board_id'=>$boardid,'start_record'=>0,'page_per_record'=>$list_cnt) );
		
		wp_enqueue_script ('board-latest' , $shortCodeView->path['boardLatestUrl'].'/'.$boardskin.'/board_latest.js');
		wp_enqueue_style  ('board-latest' , $shortCodeView->path['boardLatestUrl'].'/'.$boardskin.'/board_latest.css');
		
		$output = '';
		$output .= SACommonView::getInstance()->getView($viewPath.'/skins/latest/'.$boardskin.'/latest.php',array('boardDomainList'=>$boardDomainList,'boardTable'=>$boardTable));
		
		return do_shortcode($content).$output;
	}
	
	public function sa_board($atts,$content=null){
		global $saManager;
		
		extract ( shortcode_atts ( array ( 'boardid'=>'' ), $atts ) );
		
		do_action('sa_board_header');
				
		$shortCodeView = new SABoardShortCodeViewList($boardid);
		
		$this->option = new SABoardTableOption($boardid);
		$shortCodeView->addViewParam('board_option', $this->option);
		
		wp_register_script('board-theme', $shortCodeView->path['boardThemeUrl'].'/board_script.js');
		wp_register_style ('board-theme', $shortCodeView->path['boardThemeUrl'].'/board_style.css');
		
		wp_register_script('board-pagination', $shortCodeView->path['boardPaginationUrl'].'/board_pagination.js');
		wp_register_style ('board-pagination', $shortCodeView->path['boardPaginationUrl'].'/board_pagination.css');
		
		wp_register_script ('board-search',$shortCodeView->path['boardSearchUrl'].'/search.js');
		wp_register_style  ('board-search',$shortCodeView->path['boardSearchUrl'].'/search.css');
		
		wp_register_script ('board-reply' , $shortCodeView->path['boardReplyUrl'].'/board_reply_script.js');
		wp_register_style  ('board-reply' , $shortCodeView->path['boardReplyUrl'].'/board_reply_style.css');
		
		wp_enqueue_style ('board-theme');
		wp_enqueue_script('board-theme');
		
		wp_enqueue_style ('board-pagination');
		wp_enqueue_script('board-pagination');
		
		wp_enqueue_style ('board-search');
		wp_enqueue_script('board-search');
		
		wp_enqueue_style ('board-reply');
		wp_enqueue_script('board-reply');
		
		$output = SACommonView::getInstance()->getView($shortCodeView->viewPath.'/include/board_header.php',$shortCodeView->viewParam);
		
		switch ($shortCodeView->board_mode){
			case 'board_list' :
				$shortCodeView = new SABoardShortCodeViewList($boardid);
			break;
			
			case 'board_edit' :
				$shortCodeView = new SABoardShortCodeViewEdit($boardid);
			break;
						
			case 'board_read' :
				$shortCodeView = new SABoardShortCodeViewRead($boardid);
			break;
			
			case 'board_delete' :
				$shortCodeView = new SABoardShortCodeViewDelete($boardid);
			break;
		}
		
		$output = $shortCodeView->getView($output);
		
		$output .= SACommonView::getInstance()->getView($shortCodeView->viewPath.'/include/board_footer.php',$shortCodeView->viewParam);
		
		do_action('sa_board_footer');
		
		return do_shortcode($content).apply_filters('sa_board_output', $output);
	}
}