<?php
class SABoardShortCodeViewEdit extends SABoardShortCodeView {
	public function getView($output) {
		$boardDomain  = $this->boardService->getBoardDomain($_REQUEST);
		
		$this->user->setBoardDomain($boardDomain);
		
		if(!$this->user->getRole('write')){
			return "글을 작성할 권한이 없습니다.";
		}
		
		$is_comment = SARequest::getParameter('is_comment');
		
		wp_enqueue_media();
		
		$boardFileListDomain = $this->boardService->getBoardFileList($_REQUEST);
		
		$password = SARequest::getParameter('board_password','');
		
		if(empty($boardDomain)){
			$_REQUEST['board_action']  = 'insert';
			$_REQUEST['board_content'] = $this->boardTable->table['board_table_default_content'];
			$_REQUEST['board_grp']     = $this->boardService->getBoardMaxGrp($_REQUEST)+1;
			$_REQUEST['board_order']   = $this->boardService->getBoardMaxOrder()+1;
			
			$_REQUEST['boardFileListDomain'] = array();
				
			if(is_user_logged_in()){
				$meta = sa_get_current_user_meta();
		
				$_REQUEST['board_user_nm'] = $meta['nickname'][0];
			}
				
			$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_edit.php',$this->viewParam);
		}else if($is_comment){
			$_REQUEST['board_depth']  = $boardDomain['board_depth']+1;
			$_REQUEST['board_order']  = $boardDomain['board_order']+1;
			$_REQUEST['board_parent'] = $boardDomain['board_index'];
			$_REQUEST['board_grp']    = $boardDomain['board_grp'];
			$_REQUEST['board_secret'] = $boardDomain['board_secret'];
			$_REQUEST['board_content'] = '';
			$_REQUEST['board_action'] = 'insert';
			$_REQUEST['boardFileListDomain'] = array();
		
			$board_tlt = '';
		
			$_REQUEST['board_title'] = $board_tlt.' ';
				
			if(is_user_logged_in()){
				$_REQUEST['board_user_nm'] = $this->user->userNm;
			}
				
			$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_edit.php',$this->viewParam);
		}else{
			$_REQUEST['board_title'] 		= $boardDomain['board_title'];
				
			if($boardDomain['board_user_id'] == sa_get_current_user_login_id() && empty($boardDomain['board_password'])){
		
				$_REQUEST['board_user_nm'] 		= $boardDomain['board_user_nm'];
				$_REQUEST['board_content'] 		= $boardDomain['board_content'];
				$_REQUEST['board_grp']    		= $boardDomain['board_grp'];
				$_REQUEST['board_order'] 		= $boardDomain['board_order'];
				$_REQUEST['board_parent'] 		= $boardDomain['board_parent'];
				$_REQUEST['board_secret'] 		= $boardDomain['board_secret'];
				$_REQUEST['board_user_email'] 	= $boardDomain['board_user_email'];
				$_REQUEST['board_user_phone'] 	= $boardDomain['board_user_phone'];
				
				$_REQUEST['board_action'] 		= 'update';
				$_REQUEST['boardFileListDomain'] = $boardFileListDomain;
		
				$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_edit.php',$this->viewParam);
			}else{
				$_REQUEST['board_action'] 		= 'modify';
				
				if($this->user->is_pw_pass($password)){
					$_REQUEST['board_user_nm'] 		= $boardDomain['board_user_nm'];
					$_REQUEST['board_content'] 		= $boardDomain['board_content'];
					$_REQUEST['board_grp']    		= $boardDomain['board_grp'];
					$_REQUEST['board_order'] 		= $boardDomain['board_order'];
					$_REQUEST['board_parent'] 		= $boardDomain['board_parent'];
					$_REQUEST['board_secret'] 		= $boardDomain['board_secret'];
					$_REQUEST['board_action'] 		= 'update';
					$_REQUEST['boardFileListDomain'] = $boardFileListDomain;
					
					$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_edit.php',$this->viewParam);
				}else{
					if(!empty($password)){
						SARequest::setErrorMessage('board_password', '패스워드를 확인하세요.');
					}
					
					$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_pw.php',$this->viewParam);
				}
			}
		}
				
		return $output;
	}
}