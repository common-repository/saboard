<?php
class SABoardAdminController extends SAAdminPageController {
	private $currentVersion = 0.1;
	
	private $boardAdminService;
	private $boardDatabase;
	
	public function setDatabase(){
		SABoardDatabase::getInstance()->createTables();
	}
	
	public function init(){
		$this->setDatabase();
		
		add_option('saboard_version',$this->currentVersion);
		
		$this->setPageName('SABoard');
		$this->setPageType(SA_PAGE_TYPE_MENU);
		
		$this->setMenus(array(
			'board_list'=>'게시판목록',
			'board_group'=>'게시판그룹생성/수정',
			'board_insert'=>'게시판생성/수정',
			'board_backup'=>'백업'	
		));
		
		add_action('sa_board_admin_view', array(&$this,'initBoardAdmin'));
		add_action($this->slug.'_view_header', array(&$this,'updateView'));
	}
	
	public function updateView(){
		
	}
	
	public function initBoardAdmin(){
		global $saManager;
		
		wp_enqueue_script('jquery-chosen');
		wp_enqueue_style ('jquery-chosen');
		
		wp_enqueue_script('saframework');
		wp_enqueue_style ('saframework');
		
		wp_enqueue_script('saboard_admin',$saManager->getSaPluginUrl() . '/board/resources/board_admin.js');
		wp_enqueue_style ('saboard_admin',$saManager->getSaPluginUrl() . '/board/resources/board_admin.css');
		
		$this->boardAdminService = SABoardAdminService::getInstance();
		$this->boardDatabase     = SABoardDatabase::getInstance();
		$this->boardDatabase->createTables();
	}
	
	public function view(){
		do_action('sa_board_admin_view');
		
		$mav = new SAModelAndView();
		$mav->setViewPath(dirname(__FILE__).'/../../views/admin');
		
		$mav->addObject('slugPage', $this->slugPage);
		$mav->addObject('skins', $this->boardAdminService->getSkins());
		
		$board_id = SARequest::getParameter('board_table_id');
		
		$board_option = new SABoardTableOption($board_id);

		$mav->addObject('board_option', $board_option);
		
		switch ($this->pageMode){
			case 'board_list' :
				$boardTableList = $this->boardAdminService->getBoardTableList($_REQUEST);
				$mav->addObject('boardTableList', $boardTableList);
				
				$mav->getView('board_list.php');
			break;
			case 'board_insert' :
				$boardGroupListDomain = $this->boardAdminService->getBoardGroupList();
				
				if(empty($boardGroupListDomain)){
					$this->saveMessage('그룹을 먼저 생성해주세요.');
					$this->redirect('board_list');
				}
				
				$boardTableDomain = $this->boardAdminService->getBoardTableDomain($_REQUEST);
				
				$mav->addObject('boardTableDomain', $boardTableDomain);
				$mav->addObject('boardGroupListDomain', $boardGroupListDomain);
				
				$board_fields = array('board_title'=>'제목','board_user_nm'=>'작성자','board_read_cnt'=>'조회수','board_reg_date'=>'작성일');
				
				if(!empty($boardTableDomain)){
					if(empty($boardTableDomain['board_table_list_cnt'])){
						$boardTableDomain['board_table_list_cnt'] = 15;
					}
					
					$boardTableDomain['board_fields'] = $board_fields;

					$_REQUEST = array_merge($_REQUEST,$boardTableDomain);
					$_REQUEST = array_merge($_REQUEST,$board_option->getOption());
					
					$mav->addObject('action', 'update');
				}else{
					$_REQUEST['board_table_list_cnt'] 		= 15;
					$_REQUEST['board_table_file_cnt'] 		= 1;
					$_REQUEST['board_table_file_max_size'] 	= 10485760;
					$_REQUEST['board_table_title_cut'] 		= 75;
					$_REQUEST['board_table_default_content']   = '';
					$_REQUEST['board_table_show_columns']   = null;
					$_REQUEST['board_table_theme']   = null;
					$_REQUEST['board_table_theme_pagination']   = null;
					$_REQUEST['board_table_theme_reply']   = null;
					$_REQUEST['board_table_reply_useyn']   = null;
					$_REQUEST['board_table_comment_useyn']   = null;
					$_REQUEST['board_table_secret_useyn']   = null;
					$_REQUEST['board_table_search_useyn']   = null;
					$_REQUEST['board_table_seo_useyn']   = null;
					$_REQUEST['board_table_theme_search']   = null;
					$_REQUEST['board_table_read_role']   = null;
					$_REQUEST['board_table_write_role']   = null;
					$_REQUEST['board_group_index']   = null;
					
					$_REQUEST['board_table_user_email_useyn']   = false;
					$_REQUEST['board_table_user_phone_useyn']   = false;
										
					$_REQUEST['board_fields'] = $board_fields;
					
					$mav->addObject('action', 'insert');
				}
				
				$mav->addAllObject($_REQUEST);
				
				$mav->getView('board_insert.php');
			break;
			case 'board_backup' :
				$backupList =  array_reverse($this->boardDatabase->getBackUpFiles());
			
				$pagination = new SAPagination(array('page_per_record'=>5));
				$pagination->setTotal_record(count($backupList));
				
				$mav->addObject('pagination', $pagination);
				$mav->addObject('backupList', $backupList);
			
				$mav->getView('board_backup.php');
			break;
			
			case 'board_group' :
				$groupList = $this->boardAdminService->getBoardGroupList();
				
				$mav->addObject('groupList', $groupList);
				
				$mav->getView('board_group.php');
			break;
			
			case 'board_group_add' : 
				$boardGroup = $this->boardAdminService->getBoardGroupByIndex($_REQUEST);				
				
				$mav->addObject('boardGroup', $boardGroup);
				
				$mav->getView('board_group_add.php');
			break;
			
			case 'action_board_group_add' :
				if(SADbHelper::isExistsValue('sa_board_group', 'board_group_id', $_REQUEST['board_group_id'])){
					$this->saveMessage('이미 등록되어 있는 그룹입니다.');
					$this->redirect('board_group_add');
					wp_die();
				}
				
				$validator = new SAValidator($_REQUEST);
				
				$validator->validate(array(
						'board_group_id' => array('isNotEmpty','isEnglish'),
						'board_group_nm' => array('isNotEmpty')
				));
				
				if($validator->hasErrors()){
					$this->redirect('board_group_add');
				}else{
					$this->boardAdminService->insertBoardGroup($_REQUEST);
					$this->redirect('board_group');
				}
			break; 
			case 'action_board_group_update' :
				$validator = new SAValidator($_REQUEST);
			
				$validator->validate(array(
						'board_group_id' => array('isNotEmpty','isEnglish'),
						'board_group_nm' => array('isNotEmpty'),
						'board_group_index' => array('isNotEmpty')
				));
			
				if($validator->hasErrors()){
					$this->redirect('board_group_add');
				}else{
					$this->boardAdminService->updateBoardGroup($_REQUEST);
					$this->redirect('board_group');
				}
			break;
			case 'action_board_group_delete' :
				$validator = new SAValidator($_REQUEST);
				
				$validator->validate(array(
						'board_group_index' => array('isNotEmpty'),
				));
				
				if($validator->hasErrors()){
				}else{
					$this->boardAdminService->deleteBoardGroup($_REQUEST);
				}
				
				$this->redirect('board_group');
			break;
			case 'action_board_insert' :
				$boardTable = $this->boardAdminService->getBoardTableDomain($_REQUEST);
				
				if(!empty($boardTable)){
					$this->saveMessage('중복되는 게시판이 존재합니다.');
					$this->redirect('board_insert');
					die();
				}
				
				$validator = new SAValidator($_REQUEST);
				
				$validator->validate(array(
					'board_table_id' => array('isNotEmpty','isEnglish'),
					'board_table_nm' => array('isNotEmpty'),
					'board_table_list_cnt' => array('isNumber'),
					'board_table_file_cnt' => array('isNumber')
				));
				
				if($validator->hasErrors()){
					$this->redirect('board_insert');
				}else{
					$_REQUEST['board_table_show_columns'] = sa_serialize(SARequest::getParameter('board_table_show_columns',array()));
					
					$this->boardAdminService->insertBoardTable($_REQUEST);
					
					$board_option->requestUpdate();
					
					$this->saveMessage('게시판이 생성되었습니다 페이지에 추가해보세요.');
					$this->redirect('board_list');
				}
				
			break;
			
			case 'action_board_update' :
				$user_options = $_REQUEST['board_table_user_option'];
				
				foreach($user_options as $option){
					$_REQUEST[$option] = 'Y';	
				}
				
				$_REQUEST['board_table_show_columns'] = sa_serialize(SARequest::getParameter('board_table_show_columns',array()));
				
				$this->boardAdminService->updateBoardTable($_REQUEST);
				
				$board_table_id = SARequest::getParameter('board_table_id');
				
				$board_option->requestUpdate();
				
				$this->saveMessage('수정되었습니다.');
				$this->redirect('board_insert&board_table_id='.$board_table_id);
			break;
			case 'action_board_delete' :
				$this->boardAdminService->deleteBoardTable($_REQUEST);
				
				$board_option->clearOption();
				
				$this->saveMessage('삭제되었습니다.');
				$this->redirect('board_list');
				
				break;
					
			case 'action_backup' :
				$this->boardDatabase->backUp();
			
				$this->saveMessage('백업되었습니다.');
				$this->redirect('board_backup');
			break;
					
			case 'action_backup_restore' :
				$fileName = SARequest::getParameter('fileName');
				$this->boardDatabase->restore($fileName);
			
				$this->saveMessage('복원되었습니다.');
				$this->redirect('board_backup');
			break;
					
			case 'action_backup_delete' :
				$fileName = SARequest::getParameter('fileName');
				
				if(is_array($fileName)){
					foreach($fileName as $file){
						$this->boardDatabase->deleteBackupFile($file);
					}
				}else{
					$this->boardDatabase->deleteBackupFile($fileName);
				}
			
				$this->saveMessage('파일이 삭제되었습니다..');
				$this->redirect('board_backup');
			break;
		}
	}
}