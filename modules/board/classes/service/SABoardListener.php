<?php
class SABoardListener {
	public function init() {
		add_action('saboard_insert', array(&$this,'boardInsert'));
		
		add_action('saboard_update', array(&$this,'boardUpdate'));
		
		add_action('saboard_reply_insert', array(&$this,'boardReplyInsert'));
		
		add_action('safile_download', array(&$this,'boardFileDownload'));
	}
	
	public function __construct(){
		$this->init();
	}
	
	public function makePageUrl($param){
		
		$siteUrl = home_url();
		$siteUrl .= '?board_mode=board_read&board_id='.$param['board_id'].'&board_index='.$param['board_index'].'&page_id='.$param['page_id'];

		return $siteUrl;
	}
	
	//글을 작성했을떄
	public function boardInsert($param) {
		$boardTableOption = new SABoardTableOption($param['board_id']);
		
		if($boardTableOption->getOption('board_table_insert_mail_yn') == 'Y'){
			$headers [] = 'Content-type : text/html';
			
			$subject = $param ['board_user_nm'] . '님이 새로운 글을 등록하셨습니다.';
			
			$currentUrl = $this->makePageUrl($param);
			
			$content = '<p><a href="' . $currentUrl . '" target="_blank">바로가기</a></p> <br/><br/>';
			
			$content .= stripcslashes ( $param ['board_content'] );
			$content = apply_filters ( 'the_content', $content );
			
			wp_mail ( get_option ( 'admin_email' ), $subject, $content, $headers );
		}
	}
	
	//글수정
	public function boardUpdate($param){
		
	}

	//댓글작성
	public function boardReplyInsert($param){
		$boardTableOption = new SABoardTableOption($param['board_id']);
		
		if($boardTableOption->getOption('board_table_reply_insert_mail_yn') == 'Y'){
			$headers [] = 'Content-type : text/html';
	
			$subject = $param ['board_reply_user_nm'] . '님이 댓글을 등록하셨습니다.';
			
			$currentUrl = $this->makePageUrl($param);
			
			$content = '<p><a href="' . $currentUrl . '" target="_blank">바로가기</a></p> <br/><br/>';
			
			$content .= stripcslashes ( $param ['board_reply_content'] );
			$content = apply_filters ( 'the_content', $content );
			
			wp_mail ( get_option ( 'admin_email' ), $subject, $content, $headers );
		}
	}
	
	//댓글 수정	
	public function boardReplyUpdate($param){
	
	}
	
	//파일 다운로드 할때
	public function boardFileDownload($param){
		
	}
}