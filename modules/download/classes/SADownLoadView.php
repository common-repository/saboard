<?php
class SADownLoadView{
	var $fileName;
	var $upload_dir;
	
	public function __construct(){
		$this->fileName = SARequest::getParameter('fileName');
		
		$upload_dir = wp_upload_dir();
		$this->upload_dir = $upload_dir['basedir'];
	}
	
	public function init(){
		$this->view($this->upload_dir.$this->fileName);
		
		add_action('wp_enqueue_scripts', array(&$this,'addResource'));
		
		add_action('wp_ajax_ajaxSaDownCnt', array(&$this,'ajaxSaDownCnt') );
		add_action('wp_ajax_nopriv_ajaxSaDownCnt', array(&$this,'ajaxSaDownCnt') );
	}
	
	public function addResource(){
		global $saManager;
		
		wp_enqueue_script('download',$saManager->getSaPluginUrl().'/download/resources/js/download.js');
	}
	
	public function ajaxSaDownCnt(){
		$option = new SADownLoadOption('sa_down_option');
		
		$fileOption = $option->getOption($this->fileName,0);
		
		$o = $option->updateOption($this->fileName, $fileOption+1);
		
		echo SAJsonView::getInstance()->getView($o);
		
		die();
	}
	
	function view($file, $params = array()) {
		if(!isset($_GET['downloadView'])){
			return;
		}
		
		if($_GET['downloadView'] != 'load'){
			return;
		}
		
		if (! is_file ( $file )) {
			wp_die ( '<h3>404 File not found!...'.$file.'</h3><p><a href="javascript:history.back()">뒤로</a></p>' );
		}
	
		if(preg_match('/\.\.\//', $file)){
			wp_die('<h3>Is not able to use the file name.</h3><p><a href="javascript:history.back()">뒤로</a></p>');
		}
		
		$f = new SAFile('', $file);
		
		do_action('safile_download',$f);
	
		$len = filesize ( $file );
		$filename = basename ( $file );
		$alias = ! empty ( $alias ) ? $alias : $filename;
		$file_extension = strtolower ( substr ( strrchr ( $filename, "." ), 1 ) );
		
		switch ($file_extension) {
			case "pdf" :
				$ctype = "application/pdf";
				break;
			case "exe" :
				$ctype = "application/octet-stream";
				break;
			case "zip" :
				$ctype = "application/zip";
				break;
			case "doc" :
				$ctype = "application/msword";
				break;
			case "xls" :
				$ctype = "application/vnd.ms-excel";
				break;
			case "ppt" :
				$ctype = "application/vnd.ms-powerpoint";
				break;
			case "gif" :
				$ctype = "image/gif";
				break;
			case "png" :
				$ctype = "image/png";
				break;
			case "jpeg" :
			case "jpg" :
				$ctype = "image/jpg";
				break;
			case "mp3" :
				$ctype = "audio/mpeg";
				break;
			case "wav" :
				$ctype = "audio/x-wav";
				break;
			case "mpeg" :
			case "mpg" :
			case "mpe" :
				$ctype = "video/mpeg";
				break;
			case "mov" :
				$ctype = "video/quicktime";
				break;
			case "avi" :
				$ctype = "video/x-msvideo";
				break;
			case "php" :
			case "htm" :
			case "html" :
			default :
				$ctype = "application/force-download";
		}
	
		header ( "Pragma: public" );
		header ( "Expires: 0" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Cache-Control: public" );
		header ( "Content-Description: File Transfer" );
		header ( "Content-Type: $ctype" );
		header ( "Content-Disposition: attachment; filename=" . $alias . ";" );
		header ( "Content-Transfer-Encoding: binary" );
		header ( "Content-Length: " . $len );
	
		@readfile ( $file );
		
		die();
	}
}