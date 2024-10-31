<?php
class SAUploadAjaxController {
	private static $instance;

	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SAUploadAjaxController();
		}

		return self::$instance;
	}

	public function __construct(){
		if(get_option('sa_file_upload_list') === false){
			add_option('sa_file_upload_list',array());
		}
	}
	
	public function init(){
		add_action('wp_ajax_ajaxSaFileUplad', array(&$this,'saFileUplad') );
		add_action('wp_ajax_nopriv_ajaxSaFileUplad', array(&$this,'saFileUplad') );
		
		add_action('wp_ajax_ajaxSaFileDelete', array(&$this,'saFileDelete') );
		add_action('wp_ajax_nopriv_ajaxSaFileDelete', array(&$this,'saFileDelete') );
	}

	public function saFileUplad(){
		check_ajax_referer( 'safile_upload_ajax_nonce');

		$type = SARequest::getParameter('type');
		$mode = SARequest::getParameter('mode');

		$uploader = new SAUploader('safile');
		$uploader->setAllowType($type);
		$uploader->setMaxFileSize(10485760);
		
		if($uploader->hasUseAbleFile()){
			$dir = wp_upload_dir();
			$dir = $dir['basedir'].'/sa_upload_files';
			$uploadUrl = sa_wp_upload_url().'/sa_upload_files';
			
			$r = $uploader->copyAll($dir,time().'_');
			
			do_action('sa_file_upload_action',$r);
			
			if($type == 'image'){
					
			}
			
			$userUploadFile = SARequest::getAttribute('userUploadFile');
			
			if(empty($userUploadFile)) $userUploadFile = array();
			
			array_push($userUploadFile, $uploader->getUploadFile());
			
			SARequest::setAttribute('userUploadFile', $userUploadFile);
			
			if(is_array($r)){
				if($r['result']['result']) {
					$r['uploadUrl'] = $uploadUrl;
						
					$sa_file_upload_list = get_option('sa_file_upload_list',array());
			
					array_push($sa_file_upload_list, $r);
			
					update_option('sa_file_upload_list',$sa_file_upload_list);
			
					do_action('sa_file_upload_success_action' , $r);
				}
			}
			
			SAJsonView::getInstance()->view($r);
		}		
		
		die();
	}

	public function saFileDelete(){
		$userUploadFile = SARequest::getAttribute('userUploadFile');
		$fileName = SARequest::getParameter('fileName');
		
		foreach($userUploadFile as $files){
			foreach($files as $file){
				if($fileName == $file->getUploadName()){
					$file->deleteFile();
				}	
			}
		}
		
		die();
	}
}