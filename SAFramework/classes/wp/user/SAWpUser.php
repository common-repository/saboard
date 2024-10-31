<?php
if(!class_exists('SAWpUser')){
	class SAWpUser {
		var $user;
		
		public function __construct(){
			$this->user = $this->getCurrentUser();
		}
		
		public function getLoginId(){
			return $this->user->get('user_login');
		}
		
		public function getCurrentUser(){
			global $current_user;
			
			get_currentuserinfo();
			
			return $current_user;
		}
		
		public function getCurrentUserData(){
			return get_userdata($this->user->ID);
		}
		
		public function getCurrentUserMeta(){
			return get_user_meta($this->user->ID);
		}
		
		public function isSuperAdmin(){
			return !function_exists('is_super_admin') || !function_exists('is_multisite') || !is_multisite() || is_super_admin();
		}
		
		public function is_login(){
			return is_user_logged_in();
		}
		
		private static $instance;
		
		public static function getInstance(){
			if(self::$instance == null){
				self::$instance = new SAWpUser();
			}
		
			return self::$instance;
		}
	}
}