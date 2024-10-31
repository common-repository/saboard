<?php
class SABoardShortCodeViewDelete extends SABoardShortCodeView {
	public function getView($output) {
		$boardDomain = $this->boardService->getBoardDomain ( $_REQUEST );
		
		$this->user->setBoardDomain($boardDomain);
		
		if(!$this->user->getRole('delete')){
			return "삭제할 권한이 없습니다.";
		}
		
		if (! empty ( $boardDomain )) {
			if($this->user->getRole('delete_action')){
				$this->boardService->deleteBoard($_REQUEST);
				$this->redirect_alert($this->listUrl);
			}else{
				$password = SARequest::getParameter('board_password','');
				$password = sa_convert_str_to_mysql_password($password);
				$password_check = !empty($password) && $password == $boardDomain['board_password'];
					
				if (!$password_check) {
					$this->addRequestParam('board_action', 'delete');
					$this->addRequestParam('board_title', $boardDomain ['board_title']);
					
					$output .= SACommonView::getInstance ()->getView ( $this->viewPath . '/board_pw.php', $this->viewParam );
				}	
			}
		}
		
		return $output;
	}
}