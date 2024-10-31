<?php 
class SABoardAjaxController{
	private $boardService;
	
	public function __construct(){
		$this->boardService = SABoardService::getInstance();
	}
	
	public function init(){
		add_action( 'wp_ajax_board_reply_password_check', array( &$this,'wp_ajax_board_reply_password_check' ) );
		add_action( 'wp_ajax_nopriv_board_reply_password_check', array( &$this,'wp_ajax_board_reply_password_check' ) );
	}
	
	public function wp_ajax_board_reply_password_check(){
		sa_nonce_check('nonce_sa_board');
		
		$param = array('board_reply_index'=>$_REQUEST['board_reply_index']);
		$boardReplyDomain = $this->boardService->getBoardReplyDomain($_REQUEST);
			
		if(empty($boardReplyDomain['board_reply_user_id'])){
			if(empty($_REQUEST['board_reply_password'])){
				SAJsonView::getInstance()->view(array('result'=>false));
				die();
			}
			
			$param['board_reply_password'] = $_REQUEST['board_reply_password'];
		}else{
			$param['board_reply_user_id']  = sa_get_current_user_login_id();
		}
		
		$check = $this->boardService->getBoardReplyPasswordCheck($param);
		
		if($check > 0){
			SAJsonView::getInstance()->view(array('result'=>true,'boardReplyDomain'=>$boardReplyDomain));
			
			die();
		}else{
			SAJsonView::getInstance()->view(array('result'=>false));
			
			die();
		}
	}
}