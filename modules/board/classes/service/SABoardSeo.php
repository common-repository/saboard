<?php
class SABoardSeo extends SASeo {
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SABoardSeo();
		}
	
		return self::$instance;
	}
	
	public function init() {
		$board_id = SARequest::getParameter('board_id');
		
		if(!empty($board_id)){
			$boardTable = new SABoardTable ($board_id);
			
			if($boardTable->table['board_table_seo_useyn'] != 'Y'){
				return false;	
			}
			
			$user 		= new SABoardUser  ($boardTable);
			$boardDomain = SABoardService::getInstance()->getBoardDomain($_REQUEST);
			
			$user->setBoardDomain($boardDomain);
			
			if($user->getReadRole()){
				$this->setDescription($boardDomain['board_content']);
				$this->setAuthor($boardDomain['board_user_nm']);
				$this->setAuthor_date($boardDomain['board_reg_date']);
				$this->setOg_title($boardDomain['board_title']);
				$this->setOg_content($boardDomain['board_content']);
				$this->setPublisher('web-readymade');
				$this->setLink(get_home_url().'?board_id='.$board_id.'&board_mode=board_read&board_index='.$boardDomain['board_index']);
				$this->setCopyright('by web-readymade team');
				$this->setClassification('SABoard');
				$this->output();
			}
		}
	}
}