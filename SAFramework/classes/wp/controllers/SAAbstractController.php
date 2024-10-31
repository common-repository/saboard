<?php
if(!class_exists('SAAbstractController')){
	abstract class SAAbstractController {
		var $slug;
	
		public function __construct(){
			//$this->action_init();
			$this->initEtc();
			$this->init();
			
// 			add_action('init', array(&$this,'init'));
// 			add_action('init', array(&$this,'initEtc'));
		}
		
		public function initEtc(){
			$this->slug = get_class($this);
		}
		
		public abstract function init();
	}
}