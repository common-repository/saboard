<?php
if(!class_exists('SAModel')){
	class SAModel {
		var $models;
		
		public function __construct(){
			$this->models = array();
		}
		
		public function addObject($key,$object){
			$this->models[$key] = $object;
		}
		
		public function get($key){
			if(array_key_exists($key, $this->models)){
				return $this->models[$key];
			}
			
			return sprintf('KEY : %s NOT EXISTS <br/>',$key);
		}
		
		public function getAllObject(){
			return $this->models;
		}
		
		public function toArray(){
			return $this->models;
		}
	}
}