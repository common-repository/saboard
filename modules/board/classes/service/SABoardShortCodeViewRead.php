<?php
class SABoardShortCodeViewRead extends SABoardShortCodeView {
	public function getView($output) {
		$boardDomain = $this->boardService->getBoardDomain($_REQUEST);
		
		if(empty($boardDomain)){
			$this->redirect_alert($this->listUrl,'게시물이 삭제되었거나 잘못된 접근입니다.');			
		}
		
		$this->user->setBoardDomain($boardDomain);
		
		if(!$this->user->getRole('read')){
			return "게시물을 읽을 권한이 없습니다.";
		}
		
		if($boardDomain['board_secret'] == 'Y' && !is_super_admin()){
			if(!$this->user->is_eq_password()){
				$pw = SARequest::getParameter('board_password');
				
				if(!empty($pw)){
					SARequest::setErrorMessage('board_password', '패스워드를 확인하세요.');
				}
				
				$_REQUEST['board_title'] 		= $boardDomain['board_title'];
				$_REQUEST['board_action'] 		= 'pw_check';
				
				$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_pw.php',$this->viewParam);
				
				return $output;
			}
		}
			
		$boardDomain['board_content'] = apply_filters( 'the_content', $boardDomain['board_content'] );
		
		$this->boardService->updateBoardReadCnt($_REQUEST);
		
		$this->addViewParam('boardReplyListDomain', $this->boardService->getBoardReplyList($_REQUEST));
		$this->addViewParam('boardReadDomain', $boardDomain);
		$this->addViewParam('boardNavDomain', $this->boardService->getNavBoardDomain($_REQUEST));
		$this->addViewParam('boardExpansionDomain', sa_unserialize($boardDomain['board_expansion']));
		$this->addViewParam('boardFileListDomain', $this->boardService->getBoardFileList($_REQUEST));
		$this->addViewParam('boardReplyMaxGrp', $this->boardService->getBoardReplyMaxGrp());
			
		$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_read.php',$this->viewParam);
		
		if($this->boardTable->table['board_table_reply_useyn'] == 'Y'){
			$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_reply.php',$this->viewParam);
		}
		
		return $output;
	}
	
	public function sort_seq($a, $b) {
		return $a['board_reply_index'] - $b['board_reply_index'];
	}
	
	public function board_reply_to_tree($array, $parent=0) {
		$ret = array();
	
		for($i=0; $i < count($array); $i++) {
			if ($array[$i]['board_reply_parent'] == $parent) {
				$a = $array[$i];
				array_splice($array,$i--,1);
					
				$a['item'] = $this->board_reply_to_tree($array, $a['board_reply_index']);
				if(empty($a['item'])){ unset($a['item']); }
				$ret[] = $a;
					
				continue;
			}
		}
	
		usort($ret, array(&$this,'sort_seq'));
		
		return $ret;
	}
	
	public function board_reply_array_tree(){
		$boardReplyList = $this->boardService->getBoardReplyList($_REQUEST);
		$result = $this->board_reply_to_tree($boardReplyList);
		return $result;
	}
	
	public function print_reply($dept=0 , $array = array()){
		if(empty($array)){
			$array = $this->board_reply_array_tree();
		}
		
		$replyUrl = $this->path['boardReplyUrl'];
		$replyTemplatePath = $this->path['boardReplyPath'];
	
		if(!isset($maxDept) || !isset($currentMaxDept)){
			$maxDept = $this->boardService->getBoardReplyTotalCount($_REQUEST);
			$currentMaxDept = 0;
		}
	
		if($dept == 0){
			$currentMaxDept = count($array)-1;
				
			do_action('sa_board_reply_head');
				
			echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step1.php' , $this->viewParam);
		}
		
		$this->addViewParam('boardReplyListDomain', $array);
		
		echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step2.php' , $this->viewParam);
			
		foreach($array as $boardReplyDomain){
			$boardReplyDomain['write_me'] = false;
				
			if($boardReplyDomain['board_reply_user_id'] == sa_get_current_user_login_id() && empty($boardReplyDomain['board_reply_password'])){
				$boardReplyDomain['write_me'] = true;
			}
			
			$boardReplyDomain['board_reply_content'] = apply_filters('the_content', $boardReplyDomain['board_reply_content']);
			
			$this->addViewParam('boardReplyDomain', $boardReplyDomain);
			
			echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step3_header.php' , $this->viewParam);
				
			echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step3.php' , $this->viewParam);
				
			if(!empty($boardReplyDomain["item"])){
				$this->print_reply(1,$boardReplyDomain["item"]);
			}
				
			echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step3_footer.php' , $this->viewParam);
		}
	
		echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step4.php' , $this->viewParam);
	
		if($maxDept-1 == $currentMaxDept || empty($array)){
			do_action('sa_board_reply_footer');
				
			echo SACommonView::getInstance()->getView( $replyTemplatePath.'/reply_step5.php' , $this->viewParam);
		}

		return $array;
	}
}