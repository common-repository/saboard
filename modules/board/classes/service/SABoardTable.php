<?php
class SABoardTable {
	var $boardId;
	var $table;
	
	public function __construct($boardId){
		$this->boardId = $boardId;
		$this->table = SABoardAdminService::getInstance()->getBoardTableDomain(array('board_table_id'=>$this->boardId));
		
		$this->init();

		$this->boardIdExistsCheck();
		$this->boardTableExistsCheck();
	}
	
	public function init(){
		
	}
	
	public function is_show_column($column){
		foreach($this->table['board_table_show_columns'] as $col){
			if($column == $col){
				return true;
			}
		}
	}
	
	public function boardTableExistsCheck(){
		if(empty($this->table)){
			die("게시판이 존재하지않습니다.");
		}
	}
	
	public function boardIdExistsCheck(){
		if(empty($this->boardId)){
			die("잘못된 접근입니다.");
		}
	}
}