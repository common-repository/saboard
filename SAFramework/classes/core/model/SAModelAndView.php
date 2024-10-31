<?php
if(!class_exists('SAModelAndView')){
	class SAModelAndView {
		private $viewPath;
		var $model;
	
		public function __construct(){
			$this->model = new SAModel();
		}
	
		public function addObject($name,$object){
			$this->model->addObject($name, $object);
		}
	
		public function addAllObject($obejct = array()){
			foreach($obejct as $key=>$value){
				$this->model->addObject($key, $value);
			}
		}
	
		public function getModel(){
			return $this->model;
		}
	
		public function setViewPath($viewPath) {
			$this->viewPath = $viewPath;
		}
		
		public function getView($viewFile,$type='common',$echo=true){
			if(empty($this->viewPath)){
				die('view path empty');
			}
	
			$view = $this->viewPath.'/'.$viewFile;
	
			switch ($type){
				case 'common' :
					if(!$echo){
						return SACommonView::getInstance()->getView($view,array('model'=>$this->model));
					}
	
					return SACommonView::getInstance()->view($view,array('model'=>$this->model));
	
				break;
				case 'ajax' :
					return SAJsonView::getInstance()->view($view,array('result'=>$this->model));
				break;
				case 'redirect':
					SAResponse::sendRedirect($viewFile);
					die();
				break;
			}
			
			return $this->view;
		}
	}
}