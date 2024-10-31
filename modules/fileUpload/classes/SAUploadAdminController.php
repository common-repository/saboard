<?php
class SAUploadAdminController extends SAAdminPageController{
	public function init(){
		$this->setMenus(array('파일목록'));
		$this->setPageName('SAUpload');
		$this->setPageType(SA_PAGE_TYPE_MENU);
	}
	
	public function view(){
		$mav = new SAModelAndView();
		$mav->setViewPath(dirname(__FILE__).'/../views/');
		
		$uploadFileList = get_option('sa_file_upload_list',array());
		
		$mav->addObject('slugPage', $this->slugPage);
		
		switch ($this->pageMode){
			case 'file_list' : 
				$mav->addObject( 'uploadFileList', $uploadFileList );
				$mav->getView('file_list.php');
			break;
			
			case 'action_file_delete' :
				$fileName = SARequest::getParameter('fileName');
			
				for($i=0;$i<count($uploadFileList);$i++){
					$uploadFiles = $uploadFileList[$i]['files'];
						
					for($x=0;$x<count($uploadFiles);$x++){
						$file = $uploadFiles[$x];
			
						if(is_array($fileName)){
							foreach($fileName as $f){
								if($file->getUploadName() == $f){
									$file->deleteFile();
			
									unset($uploadFileList[$i]['files'][$x]);
								}
							}
								
							update_option('sa_file_upload_list', $uploadFileList);
						}else{
							if($file->getUploadName() == $fileName){
								$file->deleteFile();
									
								unset($uploadFileList[$i]['files'][$x]);
									
								update_option('sa_file_upload_list', $uploadFileList);
									
								break;
							}
						}
					}
				}
			
				$this->redirect('file_list');
				
				break;
		}
	}
	
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SAUploadAdminController();
		}
	
		return self::$instance;
	}
}