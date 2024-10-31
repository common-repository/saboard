<?php
class SABoardShortCodeViewList extends SABoardShortCodeView {
	public function getView($output) {
		if(!$this->user->getRole('read')){
			return "목록을 읽을 권한이 없습니다.";
		}
		
		$pagination = new SAPagination(array('page_per_record'=>$this->boardTable->table['board_table_list_cnt']));
		
		$this->addRequestParam('start_record', $pagination->start_record);
		$this->addRequestParam('page_per_record', $pagination->page_per_record);
		
		$this->addRequestParam(SARequest::getParameter('searchDiv'), SARequest::getParameter('searchValue'));
		
		$tot = $this->boardService -> getAllBoardCount($_REQUEST);
		$pagination->setTotal_record($tot);
		
		$num = $tot - (($pagination->now_page-1) * $pagination->page_per_record);
		
		$this->addRequestParam('num', $num);
		
		$this->addViewParam('board_list', $this->boardService->getAllBoardList($_REQUEST));
		$this->addViewParam('pagination', $pagination);
		
		if($this->boardTable->table['board_table_search_useyn'] == 'Y'){
			$output .= SACommonView::getInstance()->getView($this->path['boardSearchPath'].'/search.php',$this->viewParam);
		}
		
		$output .= SACommonView::getInstance()->getView($this->viewPath.'/board_list.php',$this->viewParam);
		
		$output .= SACommonView::getInstance()->getView($this->path['boardPaginationPath'].'/pagination.php',array('pagination'=>$pagination));
		
		return $output;
	}
}