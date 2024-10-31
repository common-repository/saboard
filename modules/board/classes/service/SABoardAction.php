<?php
class SABoardAction {
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SABoardAction();
		}
	
		return self::$instance;
	}
	
	private $boardService;
	private $boardAdminService;
	
	public function __construct(){
		$this->boardService = SABoardService::getInstance();
		$this->boardAdminService = SABoardAdminService::getInstance();
	}
	
	public function updateFilter($param){
		//wp_mail(get_option('admin_email'), '새로운 글이 등록되었습니다.', $param);
	}
	
	public function init(){
		global $saManager;

		$board_action 	= SARequest::getParameter('board_action');
		$board_id   	= SARequest::getParameter('board_id');
		
		$page_id 	    = SARequest::getParameter('page_id');
		$page_id_link   = empty($page_id) ? '' : '&page_id='.$page_id;
		
		$board_index 	= SARequest::getParameter('board_index');
		
		if(empty($board_action)){
			return false;
		}
		
		$boardTable = new SABoardTable($board_id);
		$user       = new SABoardUser ($boardTable);
		
		$max_file_size  = $boardTable->table['board_table_file_max_size'];
		
		$boardDomain = $this->boardService->getBoardDomain($_REQUEST);
		$user->setBoardDomain($boardDomain);
		
		$validateArgs = array('board_user_nm'=>array('isNotEmpty','maxLength'=>30),
							  'board_title'=>array('isNotEmpty'),
							  'board_content'=>array('isNotEmpty'),
							  'board_id'=>array('isNotEmpty') );
		
		switch ($board_action){
			case 'insert':
				if(!$user->getRole('write')){
					wp_die("잘못된 접근입니다.");
				}
				
				sa_nonce_check('nonce_sa_board');

				$validator = new SAValidator($_REQUEST);
				$validator->validate($validateArgs);
				
				if($validator->hasErrors()){
					SAResponse::sendRedirect('?board_mode=board_edit&board_id='.$board_id.$page_id_link);
					return false;
				}
				
				$expansion = array();
				
				foreach($_REQUEST as $key=>$value){
					if(preg_match('/expansion/', $key)){
						$expansion[$key] = $value;
					}
				}
				
				$expansion = sa_serialize($expansion);
				$_REQUEST['board_expansion'] = $expansion;
				
				$orderDomain = $this->boardService->getBoardDomain(array('board_index'=>$_REQUEST['board_parent']));
				
				if(!empty($orderDomain)){
					$this->boardService->updateBoardOrder($orderDomain);
				}
				
				$insertResult = $this->boardService->insertBoard($_REQUEST);
				
				do_action('saboard_insert',wp_parse_args($insertResult['result'],$_REQUEST));
				
				if($insertResult){
					$uploader = new SAUploader('board_files');
					$uploader->setMaxFileSize($max_file_size);
					
					if($uploader->hasUseAbleFile()){
						$upload_dir = wp_upload_dir();
						$r = $uploader->copyAll($upload_dir['basedir'].'/'.$boardTable->table['board_table_id']);
						
						$index = $insertResult['id'];
						
						if($r['result']['result']){
							$fileList = $uploader->getUploadFile();
							
							for($i=0;$i<count($fileList);$i++){
								$file = $fileList[$i];
								
								if(!$file->isEmpty()){
									$fileParam = array(  'board_file_id'=>$index
														,'board_file_name'=> '/'.$boardTable->table['board_table_id'].'/'.$file->getName()
														,'board_file_size'=>$file->getSize()
														,'board_file_oriname'=>$file->getName() 
														,'board_file_seq'=>$i);
										
									$this->boardService->insertBoardFile($fileParam);
								}
							}
							
						}else{
							$this->boardService->deleteBoard($insertResult['result']);
							
							sa_save_alert($r['result']['errorMsg']);
							return false;
						}
					}
					
					$sec = sa_get_array_value($_REQUEST,'board_secret');
					
					if(empty($sec) || $sec == 'N'){
						SAResponse::sendRedirect('?board_mode=board_read&board_id='.$board_id.$page_id_link.'&board_index='.$insertResult['id']);
					}else{
						//SAResponse::sendRedirect('?board_mode=board_list&board_id='.$board_id.$page_id_link);
					}
				}
			break;
			
			case 'update':
				if(!$user->getRole('modify')){
					wp_die("잘못된 접근입니다.");
				}
				
				sa_nonce_check('nonce_sa_board');
				
				$validator = new SAValidator($_REQUEST);
				$validator->validate($validateArgs);
				
				if($validator->hasErrors()){
					SAResponse::sendRedirect('?board_mode=board_edit&board_index='.$_REQUEST['board_index'].'&board_id='.$board_id.$page_id_link);
					
					return false;
				}
				
				$updateResult = $this->boardService->updateBoard($_REQUEST);
				
				do_action('saboard_update',wp_parse_args($updateResult,$_REQUEST));
				
				$uploader = new SAUploader('board_files');
				$uploader->setMaxFileSize($max_file_size);
				
				if($uploader->hasUseAbleFile()){
					$fileList = $this->boardService->getBoardFileList($_REQUEST);
					
					$upload_dir = wp_upload_dir();
					
					for($i=0; $i<$boardTable->table['board_table_file_cnt']; $i++) {
						$file = $uploader->getFile($i);

						$r = $uploader->copy($upload_dir['basedir'].'/'.$boardTable->table['board_table_id'], $i);
						
						if($file->error != 4){
							$r = $file->transferTo($upload_dir['basedir'].'/'.$boardTable->table['board_table_id'].'/'.$file->getName());
							
							$fileParam = array(  'board_file_id'=>$board_index
												,'board_file_name'=> '/'.$boardTable->table['board_table_id'].'/'.$file->getName()
												,'board_file_size'=>$file->getSize()
												,'board_file_oriname'=>$file->getName() 
												,'board_file_seq'=>$i);
							
							$boardFileDomain = $this->boardService->getBoardFileDomain($fileParam);
							
							if(empty($boardFileDomain)){
								$this->boardService->insertBoardFile($fileParam);								
							}else{
								$this->boardService->updateBoardFile($fileParam);
							}
						}
					}
					
					$sec = sa_get_array_value($_REQUEST,'board_secret');
				}
				
				if(empty($sec) || $sec == 'N'){
					SAResponse::sendRedirect('?board_mode=board_read&board_id='.$board_id.$page_id_link.'&board_index='.$_REQUEST['board_index']);
				}else{
					SAResponse::sendRedirect('?board_mode=board_list&board_id='.$board_id.$page_id_link);
				}
				
			break;
			case 'modify' :
				return false;
				break;
			case 'delete':
				$boardPassword = SARequest::getParameter('board_password');
				
				if(empty($boardPassword)){
					return false;
				}
				
				sa_nonce_check('nonce_sa_board');
				
				if($user->is_eq_password()){
					$this->boardService->deleteBoard($_REQUEST);
						
					SAResponse::sendRedirect('?board_mode=board_list&board_id='.$board_id.$page_id_link);
				}else{
					if(!empty($boardPassword)){
						SARequest::setErrorMessage('board_password', '패스워드를 확인하세요.');
					}
					
					SAResponse::sendRedirect('?board_mode=board_delete&board_index='.$board_index.$page_id_link);
				}
			break;
			
			case 'board_reply_insert':
				if(!$user->getRole('write')){
					wp_die("잘못된 접근입니다.");
				}
								
				sa_nonce_check('nonce_sa_board');
				
				$validator = new SAValidator($_REQUEST);
				$validator->validate(array(
					'board_reply_user_nm'=>array('isNotEmpty'),
					'board_reply_title'=>array('isNotEmpty'),
					'board_reply_content'=>array('isNotEmpty')
				));
				
				if($validator->hasErrors()){
					SAResponse::sendRedirect('?board_mode=board_read&board_index='.$_REQUEST['board_index'].'&board_id='.$board_id.$page_id_link);
					return false;
				}
				
				$result = $this->boardService->insertBoardReply($_REQUEST);
				
				do_action('saboard_reply_insert',wp_parse_args($result['result'],$_REQUEST));
				
				SAResponse::sendRedirect('?board_mode=board_read&board_index='.$board_index.$page_id_link);
			break;
			case 'board_reply_modify':
				if(!$user->getRole('write')){
					wp_die("잘못된 접근입니다.");
				}
				
				$validator = new SAValidator($_REQUEST);
				$validator->validate(array(
						'board_reply_user_nm'=>array('isNotEmpty'),
						'board_reply_title'=>array('isNotEmpty'),
						'board_reply_content'=>array('isNotEmpty')
				));
				
				if($validator->hasErrors()){
					SAResponse::sendRedirect('?board_mode=board_read&board_index='.$_REQUEST['board_index'].'&board_id='.$board_id.$page_id_link);
					return false;
				}
				
				sa_nonce_check('nonce_sa_board');
							
				$result = $this->boardService->updateBoardReply($_REQUEST);
			
				do_action('saboard_reply_update',wp_parse_args($result,$_REQUEST));
				
				SAResponse::sendRedirect('?board_mode=board_read&board_index='.$board_index.$page_id_link);
			break;
			case 'board_reply_delete' :
				if(!$user->getRole('write')){
					wp_die("잘못된 접근입니다.");
				}
				
				sa_nonce_check('nonce_sa_board');

				$this->boardService->deleteBoardReplyDomain($_REQUEST);
				
				SAResponse::sendRedirect('?board_mode=board_read&board_index='.$board_index.$page_id_link);
			break;
			case 'board_reply_useyn' :
				if(!$user->getRole('write')){
					wp_die("잘못된 접근입니다.");
				}
				
				sa_nonce_check('nonce_sa_board');
				
				$this->boardService->updateUseYnBoardReplyDomain($_REQUEST);
				
				SAResponse::sendRedirect('?board_mode=board_read&board_index='.$board_index.$page_id_link);
			break;
			default :
				wp_die('잘못된 접근입니다.');
			break;
		}
	}
}