<?php
class SAScriptLoader {
	var $handleJsList  = array();
	var $handleCssList = array();
	
	var $pluginUrl;
	var $resPath;	
		
	public function __construct(){
		
	}

	public function registerScripts(){
		global $saManager;
		
		$this->handleList = sa_get_wp_script_lists();
		$this->pluginUrl  = $saManager->getSaPluginUrl().'/scriptLoader';
		$this->resPath    = $this->pluginUrl.'/resources';
		
		$this->add_script('jquery-fancybox' , '/jquery-plugins/jquery.fancybox/jquery.fancybox.pack.js'
										    , '/jquery-plugins/jquery.fancybox/jquery.fancybox.css');
		
		$this->add_script('jquery-watermark' ,'/jquery-plugins/jquery.watermark.min.js' );
		
		$this->add_script('jquery-cycle'  	 ,'/jquery-plugins/jquery.cycle.all.min.js' );
		
		$this->add_script('jquery-als' 		 ,'/jquery-plugins/jquery.als-1.1.min.js' );
		
		$this->add_script('jquery-messi'  	 ,'/jquery-plugins/jquery.messi.min.js' 
											 ,'/jquery-plugins/jquery.messi.min.css' );
		
		$this->add_script('jquery-powertip'  ,'/jquery-plugins/jquery.powertip.min.js'
									 		 ,'/jquery-plugins/jquery.powertip.min.css' );
		
		$this->add_script('jquery-selectboxIt' ,'/jquery-plugins/jquery.selectBoxIt.min.js'
									 		   ,'/jquery-plugins/jquery.selectBoxIt.css' );

		$this->add_script('jquery-validation' , '/jquery-plugins/jquery.validation/jquery.validate.min.js' 
											  , '/jquery-plugins/jquery.validation/additional-methods.min.js');
		
		$this->add_script('jquery-percentloader' ,'/jquery-plugins/jquery.percentloader/jquery.percentageloader-0.1.min.js'
												 ,'/jquery-plugins/jquery.percentloader/jquery.percentageloader-0.1.css');
		
		$this->add_script('jquery-validation-add' , '/jquery-plugins/jquery.validation/additional-methods.min.js');

		$this->add_script('jquery-chosen' 		 ,'/jquery-plugins/jquery.chosen/chosen.jquery.min.js'
										 		 ,'/jquery-plugins/jquery.chosen/chosen.min.css');
		
		$this->add_script('iscroll' 		 ,'/iscroll/iscroll.min.js' );
	}
	
	public function add_script($name){
		$args = func_get_args();

		for($i=1;$i<count($args);$i++){
			if(preg_match('/\.css/', $args[$i])){
				$this->handleCssList[$name] = array('name'=>$name,'url'=>$this->resPath.$args[$i]);
				wp_register_style  ($name, $this->resPath.$args[$i]);
			}else if(preg_match('/\.js/', $args[$i])){
				$this->handleJsList[$name] = array('name'=>$name,'url'=>$this->resPath.$args[$i]); 
				wp_register_script ($name, $this->resPath.$args[$i]);
			}		
		}
	}
	
	public function is_exists_handle($handleName){
		foreach($this->handleList as $handle){
			if($handleName == $handle){
				return true;
			}
		}
		
		return false;		
	}
	
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SAScriptLoader();
		}
	
		return self::$instance;
	}
}