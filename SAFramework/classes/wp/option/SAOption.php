<?php
class SAOption {
	const PRE_FIX = 'sa_option_';
	
	private $option;
	private $name;
	
	public function __construct($name){
		$this->name = self::PRE_FIX.$name;
		
		add_option($name,array());
	}

	public function debug(){
		$array = $this->toArray();
		
		foreach($array as $name=>$value){
			echo sprintf('%s : %s <br/>' , $name , $value);
		}
	}
	
	public function getAllOption(){
		return get_option($this->name);
	}
	
	public function get($name){
		return $this->getOption($name);
	}
	
	public function getOption($name){
		$option = $this->getAllOption();

		if(array_key_exists($name, $option)){
			return $option[$name];
		}else{
		}
	}
	
	public function addOption($name,$value=''){
		$option = $this->getAllOption();
		
		if(!array_key_exists($name, $option)){
			$option[$name] = $value;
			
			update_option($this->name, $option);
		}
	}
	
	public function updateOption($name,$value){
		$option = $this->getAllOption();
		
		if(array_key_exists($name, $option)){
			$option[$name] = $value;
			
			update_option($this->name, $option);
		}else{
		}
	}
	
	public function deleteOption($name){
		$option = $this->getAllOption();
		
		if(array_key_exists($name, $option)){
			unset($option[$name]);
			
			update_option($this->name, $option);
		}else{
		}
	}
	
	public function remove(){
		delete_option($this->name);
	}
	
	public function requestUpdate($emptySkip = true){
		$option = $this->getAllOption();
	
		foreach($option as $key => $value){
			$v = SARequest::getParameter($key);
			
			if($emptySkip && empty($v)){
				
			}else{
				$this->updateOption($key, $v);
			}
		}
	}
	
	public function toArray(){
		return $this->getAllOption();
	}
}