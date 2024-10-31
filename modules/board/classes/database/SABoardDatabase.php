<?php
class SABoardDatabase extends SAWpBackUp{
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SABoardDatabase();
		}
	
		return self::$instance;
	}
	
	public function __construct(){
		$this->setBackupPath(dirname(__FILE__).'/backup');
		$this->setTables(array('sa_board','sa_board_tables','sa_board_file','sa_board_reply'));
		
		sa_init_option('sa_board_db',new SAFile('',''));
	}
	
	public function createTables(){
		$option = get_option('sa_board_db');
		
		$f = new SAFile('', dirname(__FILE__).'/saboard.sql');
		
		$check =    !SADbHelper::isExistsTable('sa_board') 
				 || !SADbHelper::isExistsTable('sa_board_file')
				 || !SADbHelper::isExistsTable('sa_board_reply')
				 || !SADbHelper::isExistsTable('sa_board_tables')
				 || !SADbHelper::isExistsTable('sa_board_group');;
		
		if($check){
			$this->fileExecQuery(dirname(__FILE__).'/saboard.sql', true,'sa_board');
		}
		
		$this->alertTable();
	}
	
	public function alertTable(){
		//그룹추가
		$this->addColumn('sa_board_tables', 'board_group_index', 'VARCHAR', 5);
		
		//이메일필수여부
		$this->addColumn('sa_board_tables', 'board_table_user_email_useyn', 'VARCHAR', 5);
		
		//연락처필수여부
		$this->addColumn('sa_board_tables', 'board_table_user_phone_useyn', 'VARCHAR', 5);
		
		//메일추가
		$this->addColumn('sa_board' 	  , 'board_user_email' , 'VARCHAR', 30);
		
		//연락처추가
		$this->addColumn('sa_board' 	  , 'board_user_phone' , 'VARCHAR', 30);
	}
}