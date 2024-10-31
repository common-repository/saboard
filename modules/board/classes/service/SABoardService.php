<?php
class SABoardService {
	private static $instance;
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new SABoardService();
		}
	
		return self::$instance;
	}
	
	private $fpBatis;
	
	public function __construct() {
		$map = dirname(__FILE__).'/../sqlMapConfig.xml';
		
		$this->fpBatis = new FPBatis ( $map );
	}
	
	public function insertBoardReply($obj){
		$board_reply_index = SARequest::getParameter('board_reply_index');
		
		$obj['board_reply_user_id'] = sa_getCurrentUser()->get('user_login');
		$obj['board_reply_user_ip'] = SARequest::getRemoteIp();
		
		if(!empty($board_reply_index)){
			$brd = $this->getBoardReplyDomain($_REQUEST);
			
			$obj['board_reply_depth']  = $brd['board_reply_depth']+1;
			$obj['board_reply_order']  = $brd['board_reply_order']+1;
			$obj['board_reply_parent'] = $brd['board_reply_index'];
		}
		
		$boardDomain = $this->getBoardDomain($_REQUEST);
		$boardReplyListDomain = $this->getBoardReplyList($_REQUEST);
		 
		return $this->fpBatis->doInsert('saboard.insertBoardReply', $obj);
	}
	
	public function updateUseYnBoardReplyDomain($obj){
		return $this->fpBatis->doUpdate('saboard.updateUseYnBoardReplyDomain', $obj);
	}
	
	public function updateBoardReply($obj){
		return $this->fpBatis->doUpdate('saboard.updateBoardReply', $obj);
	}
	
	public function getBoardReplyList($obj){
		return $this->fpBatis->doSelect('saboard.getBoardReplyList', $obj);
	}
	
	public function getBoardReplyDomain($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardReplyDomain', $obj);
	}
	
	public function getBoardReplyMaxGrp(){
		return $this->fpBatis->queryForObject('saboard.getBoardReplyMaxGrp');
	}
	
	public function getBoardReplyMaxGrpByBoardIndex($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardReplyMaxGrpByBoardIndex',$obj);
	}
	
	public function getBoardReplyTotalCount($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardReplyTotalCount',$obj);
	}
	
	public function deleteBoardReplyDomain($obj){
		return $this->fpBatis->doDelete('saboard.deleteBoardReplyDomain',$obj);
	}
	
	public function getBoardReplyPasswordCheck($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardReplyPasswordCheck',$obj);
	}
	
	public function getBoardMaxGrp($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardMaxGrp',$obj);
	}
	
	public function getBoardMaxOrder(){
		return $this->fpBatis->queryForObject('saboard.getBoardMaxOrder');
	}
	
	public function insertBoard($obj){
		$attach_images = sa_get_html_in_img_src($obj['board_content']);
		$attach_images = implode(',', $attach_images[1]);
		
		$obj['board_reg_ip']  = SARequest::getRemoteIp();
		$obj['board_user_id'] = sa_get_current_user_login_id();
		$obj = $this->cleanXss($obj);
		
		$obj['board_attach_image']= $attach_images;
		
		return $this->fpBatis->doInsert('saboard.insertBoard', $obj);
	}
	
	public function updateBoard($obj){
		if(empty($obj['board_content'])) return false;
		if(empty($obj['board_title'])) return false;
	
		$obj['board_reg_ip'] = SARequest::getRemoteIp();
		$obj['board_user_id'] = sa_get_current_user_login_id();
		$obj = $this->cleanXss($obj);
		
		return $this->fpBatis->doUpdate('saboard.updateBoard', $obj);
	}
	
	public function updateBoardOrder($obj){
		return $this->fpBatis->doUpdate('saboard.updateBoardOrder', $obj);
	}
	
	public function deleteBoard($obj){
		$files = $this->getBoardFileList($obj);

		foreach($files as $file){
			$path = wp_upload_dir();
			
			$f = $path['basedir'].$file['board_file_name'];
			
			if(is_file($f)){
				unlink($f);
			}
		}
		
		return $this->fpBatis->doDelete('saboard.deleteBoard', $obj);
	}
	
	public function getAllBoardCount($obj){
		return $this->fpBatis->queryForObject('saboard.getAllBoardCount', $obj);
	}
	
	public function getAllBoardList($obj){
		return $this->fpBatis->doSelect('saboard.getAllBoardList', $obj);
	}
	
	public function updateBoardReadCnt($obj){
		if(!empty($obj['board_id'])){
			if(!@in_array($obj['board_id'].$obj['board_index'], $_SESSION['updatedBoardReadIndex'])){
				$_SESSION['updatedBoardReadIndex'][] = $obj['board_id'].$obj['board_index'];
				
				return $this->fpBatis->doUpdate('saboard.updateBoardReadCnt', $obj);
			}	
		}
	}
	
	public function getBoardDomain($obj){
		$return = $this->fpBatis->queryForObject('saboard.getBoardDomain', $obj);
		return $return;
	}
	
	public function getBoardDomainAttachmentImage($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardDomainAttachmentImage', $obj);
	}
	
	public function getBoardDomainByRnum($obj){
		return $this->fpBatis->queryForObject('saboard.getBoardDomainByRnum', $obj);
	}
	
	
	public function getNavBoardDomain($obj){
		$selectDomain = $this->getBoardDomainByRnum(array( 'board_index' => $obj['board_index'],'board_id'=>$obj['board_id'] ) );
		
		$boardPrevDomain = $this->getBoardDomainByRnum( array( 'rnum' => $selectDomain['rnum'] + 1 ,'board_id'=>$obj['board_id']) );
		$boardNextDomain = $this->getBoardDomainByRnum( array( 'rnum' => $selectDomain['rnum'] - 1 ,'board_id'=>$obj['board_id']) );
		
		return array( 'boardPrevDomain'=>$boardPrevDomain 
					, 'boardNextDomain'=>$boardNextDomain );
	}
	
	public function insertBoardFile($obj){
		return $this->fpBatis->doInsert('saboard.insertBoardFile', $obj);
	}
	
	public function getBoardFileList($obj){
		return $this->fpBatis->doSelect('saboard.getBoardFileList', $obj);
	}
	
	public function updateBoardFile($obj){
		return $this->fpBatis->doUpdate('saboard.updateBoardFile', $obj);
	}
	
	public function getBoardFileDomain($obj){
		return $this->fpBatis->doSelect('saboard.getBoardFileDomain', $obj);
	}
	
	public function deleteBoardFile($obj){
		return $this->fpBatis->doDelete('saboard.deleteBoardFile', $obj);
	}
	
	//<iframe src="http://www.naver.com" />
	public function cleanXss($obj){
		//관리자는 스크립트를 사용할수있다.
		//if(!current_user_can('administrator')){
			foreach($obj as $key=>$value){
				$obj[$key] = SAXssFilter::clean($value);
			}
		//}
				
		return $obj;
	}
	
}